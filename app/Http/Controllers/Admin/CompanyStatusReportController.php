<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Purchase;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CompanyStatusReportController extends Controller
{
    public function clients()
    {
        return view('admin.Report.client_status_report');
    }

    public function clientsSearch(Request $request)
    {
        return $this->buildReport($request, 3);
    }

    public function vendors()
    {
        return view('admin.Report.vendor_status_report');
    }

    public function vendorsSearch(Request $request)
    {
        return $this->buildReport($request, 4);
    }

    private function buildReport(Request $request, int $groupId)
    {
        $status = $request->input('status', 'active');
        $fromDate = $this->normalizeDate($request->input('from_date'));
        $toDate = $this->normalizeDate($request->input('to_date'));
        $invoiceNo = $request->input('invoice_no');
        $amountMin = $request->input('amount_min');
        $amountMax = $request->input('amount_max');

        $transactions = $this->queryTransactions($groupId, $fromDate, $toDate, $invoiceNo, $amountMin, $amountMax);
        $companyIds = $transactions->pluck('company_id')->filter()->unique()->values();

        $companiesQuery = Company::query()->where('group_id', $groupId);
        if ($status === 'active') {
            $companiesQuery->whereIn('id', $companyIds);
        } elseif ($status === 'inactive') {
            $companiesQuery->whereNotIn('id', $companyIds)->where('stop_sale', 0);
        } else {
            $companiesQuery->where('stop_sale', 1);
        }

        $companies = $companiesQuery->orderBy('name')->get();
        $details = $transactions->whereIn('company_id', $companies->pluck('id'))->values();
        $lastTransactions = $this->lastTransactions($groupId, $companies->pluck('id')->all());

        $period = $this->periodLabel($fromDate, $toDate);

        $view = $groupId === 3
            ? 'admin.Report.client_status_modal'
            : 'admin.Report.vendor_status_modal';

        return view($view, compact(
            'status',
            'companies',
            'details',
            'lastTransactions',
            'period',
            'invoiceNo',
            'amountMin',
            'amountMax'
        ))->render();
    }

    private function queryTransactions(
        int $groupId,
        ?string $fromDate,
        ?string $toDate,
        ?string $invoiceNo,
        ?string $amountMin,
        ?string $amountMax
    ) {
        $query = $groupId === 3
            ? Sales::query()->where('sale_id', 0)
            : Purchase::query()->where('returned_bill_id', 0);

        $subscriberId = Auth::user()->subscriber_id ?? null;
        if ($subscriberId && Schema::hasColumn($query->getModel()->getTable(), 'subscriber_id')) {
            $query->where('subscriber_id', $subscriberId);
        }

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }
        if ($invoiceNo) {
            $query->where('invoice_no', 'like', '%' . $invoiceNo . '%');
        }
        if ($amountMin !== null && $amountMin !== '') {
            $query->where('net', '>=', (float) $amountMin);
        }
        if ($amountMax !== null && $amountMax !== '') {
            $query->where('net', '<=', (float) $amountMax);
        }

        $dateColumn = $query->getModel()->getTable() . '.date';
        return $query
            ->select('id', 'invoice_no', 'date', 'net', 'total', 'customer_id')
            ->orderByDesc($dateColumn)
            ->get()
            ->map(function ($row) {
                $row->company_id = $row->customer_id;
                return $row;
            });
    }

    private function lastTransactions(int $groupId, array $companyIds)
    {
        if (empty($companyIds)) {
            return collect();
        }

        $query = $groupId === 3
            ? Sales::query()->where('sale_id', 0)
            : Purchase::query()->where('returned_bill_id', 0);

        $subscriberId = Auth::user()->subscriber_id ?? null;
        if ($subscriberId && Schema::hasColumn($query->getModel()->getTable(), 'subscriber_id')) {
            $query->where('subscriber_id', $subscriberId);
        }

        $rows = $query
            ->whereIn('customer_id', $companyIds)
            ->orderByDesc('date')
            ->get(['customer_id', 'invoice_no', 'date', 'net', 'total']);

        return $rows->groupBy('customer_id')->map(function ($items) {
            return $items->first();
        });
    }

    private function normalizeDate(?string $value): ?string
    {
        if (! $value || $value === '0') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function periodLabel(?string $fromDate, ?string $toDate): string
    {
        $label = 'الفترة :';
        $label .= $fromDate ?: 'من البداية';
        $label .= ' -- ';
        $label .= $toDate ?: 'حتى اليوم';
        return $label;
    }
}
