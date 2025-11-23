<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Representative;
use App\Models\Subscriber;
use App\Models\SystemSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertService
{
    public function sync(): void
    {
        $this->syncLowStockAlerts();
        $this->syncExpiryAlerts();
        $this->syncRepresentativeDocumentAlerts();
        $this->syncSubscriberExpiryAlerts();
    }

    protected function syncLowStockAlerts(): void
    {
        Alert::where('type', 'low_stock')->update(['resolved_at' => now()]);

        $branchId = optional(Auth::user())->branch_id;
        $subscriberId = optional(Auth::user())->subscriber_id;

        $rows = DB::table('warehouse_products')
            ->join('products', 'products.id', '=', 'warehouse_products.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->leftJoin('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->select(
                'products.id as product_id',
                'products.code',
                'products.name',
                'products.alert_quantity',
                'warehouse_products.quantity',
                'warehouse_products.warehouse_id',
                'warehouses.name as warehouse_name',
                'warehouses.branch_id',
                'branches.branch_name'
            )
            ->where('products.alert_quantity', '>', 0)
            ->whereColumn('warehouse_products.quantity', '<=', 'products.alert_quantity')
            ->when($branchId, fn($q, $v) => $q->where('warehouses.branch_id', $v))
            ->when($subscriberId, function ($q, $v) {
                $q->where(function ($query) use ($v) {
                    $query->where('warehouse_products.subscriber_id', $v)
                        ->orWhere('products.subscriber_id', $v);
                });
            })
            ->get();

        foreach ($rows as $row) {
            Alert::updateOrCreate(
                [
                    'type' => 'low_stock',
                    'related_id' => $row->product_id,
                    'warehouse_id' => $row->warehouse_id,
                ],
                [
                    'title' => __('main.alert_low_stock_title', ['item' => $row->name]),
                    'message' => __('main.alert_low_stock_body', [
                        'item' => $row->name,
                        'warehouse' => $row->warehouse_name,
                    ]),
                    'severity' => 'warning',
                    'branch_id' => $row->branch_id,
                    'meta' => [
                        'product_code' => $row->code,
                        'warehouse_name' => $row->warehouse_name,
                        'branch_name' => $row->branch_name,
                        'quantity' => $row->quantity,
                        'alert_quantity' => $row->alert_quantity,
                    ],
                    'resolved_at' => null,
                ]
            );
        }
    }

    protected function syncExpiryAlerts(): void
    {
        Alert::where('type', 'near_expiry')->update(['resolved_at' => now()]);

        $settings = SystemSettings::first();
        $branchId = optional(Auth::user())->branch_id;
        $subscriberId = optional(Auth::user())->subscriber_id;
        $days = $settings?->item_expired ?? 30;
        $toDate = Carbon::today()->addDays($days)->toDateString();

        $rows = DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->join('products as pr', 'pr.id', '=', 'pd.product_id')
            ->join('warehouses as w', 'w.id', '=', 'pd.warehouse_id')
            ->leftJoin('branches as b', 'b.id', '=', 'p.branch_id')
            ->select(
                'pd.id as purchase_detail_id',
                'pd.batch_no',
                'pd.expiry_date',
                'pd.quantity',
                'pd.warehouse_id',
                'pr.id as product_id',
                'pr.code',
                'pr.name',
                'w.name as warehouse_name',
                'b.branch_name',
                'p.branch_id',
                DB::raw('DATEDIFF(pd.expiry_date, CURDATE()) as days_to_expiry')
            )
            ->whereNotNull('pd.expiry_date')
            ->whereDate('pd.expiry_date', '<=', $toDate)
            ->when($branchId, fn($q, $v) => $q->where('p.branch_id', $v))
            ->when($subscriberId, fn($q, $v) => $q->where('pd.subscriber_id', $v))
            ->orderBy('pd.expiry_date')
            ->get();

        foreach ($rows as $row) {
            $severity = $row->days_to_expiry <= 0 ? 'danger' : 'warning';
            Alert::updateOrCreate(
                [
                    'type' => 'near_expiry',
                    'related_id' => $row->product_id,
                    'warehouse_id' => $row->warehouse_id,
                    'due_date' => $row->expiry_date,
                ],
                [
                    'title' => __('main.alert_near_expiry_title', ['item' => $row->name]),
                    'message' => __('main.alert_near_expiry_body', [
                        'item' => $row->name,
                        'expiry' => $row->expiry_date,
                    ]),
                    'severity' => $severity,
                    'branch_id' => $row->branch_id,
                    'meta' => [
                        'batch_no' => $row->batch_no,
                        'warehouse_name' => $row->warehouse_name,
                        'branch_name' => $row->branch_name,
                        'quantity' => $row->quantity,
                        'days_to_expiry' => $row->days_to_expiry,
                    ],
                    'resolved_at' => null,
                ]
            );
        }
    }

    protected function syncRepresentativeDocumentAlerts(): void
    {
        Alert::where('type', 'representative_document')->update(['resolved_at' => now()]);

        $threshold = 30;
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays($threshold);

        $reps = Representative::query()
            ->whereNotNull('document_expiry_date')
            ->whereDate('document_expiry_date', '<=', $limitDate)
            ->get();

        foreach ($reps as $rep) {
            $days = $today->diffInDays(Carbon::parse($rep->document_expiry_date), false);
            $severity = $days < 0 ? 'danger' : 'warning';

            Alert::updateOrCreate(
                [
                    'type' => 'representative_document',
                    'related_id' => $rep->id,
                ],
                [
                    'title' => __('main.alert_rep_document_title', ['rep' => $rep->name]),
                    'message' => __('main.alert_rep_document_body', [
                        'rep' => $rep->name,
                        'expiry' => $rep->document_expiry_date,
                    ]),
                    'severity' => $severity,
                    'meta' => [
                        'document_name' => $rep->document_name,
                        'document_number' => $rep->document_number,
                        'days_to_expiry' => $days,
                    ],
                    'due_date' => $rep->document_expiry_date,
                    'resolved_at' => null,
                ]
            );
        }
    }

    protected function syncSubscriberExpiryAlerts(): void
    {
        Alert::where('type', 'subscriber_near_expiry')->update(['resolved_at' => now()]);

        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(30);

        $subs = Subscriber::query()
            ->whereNotNull('subscription_end')
            ->whereDate('subscription_end', '<=', $limitDate)
            ->get();

        foreach ($subs as $sub) {
            $days = $today->diffInDays(Carbon::parse($sub->subscription_end), false);
            $severity = $days < 0 ? 'danger' : 'warning';

            Alert::updateOrCreate(
                [
                    'type' => 'subscriber_near_expiry',
                    'related_id' => $sub->id,
                ],
                [
                    'title' => __('main.alert_subscriber_expiry_title', ['sub' => $sub->company_name]),
                    'message' => __('main.alert_subscriber_expiry_body', [
                        'sub' => $sub->company_name,
                        'expiry' => optional($sub->subscription_end)->format('Y-m-d'),
                    ]),
                    'severity' => $severity,
                    'branch_id' => null,
                    'meta' => [
                        'company_name' => $sub->company_name,
                        'contact_email' => $sub->contact_email,
                        'contact_phone' => $sub->contact_phone,
                        'days_to_expiry' => $days,
                    ],
                    'due_date' => $sub->subscription_end,
                    'resolved_at' => null,
                ]
            );
        }
    }
}
