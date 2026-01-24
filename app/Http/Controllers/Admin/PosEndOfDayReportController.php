<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanyInfo;
use App\Models\Payment;
use App\Models\PosShift;
use App\Models\Sales;
use App\Models\User;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosEndOfDayReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $subscriberId = Auth::user()->subscriber_id ?? null;

        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'branch_id' => $this->resolveBranchId($request),
            'warehouse_id' => (int) $request->input('warehouse_id', 0),
            'user_id' => (int) $request->input('user_id', 0),
            'shift_id' => (int) $request->input('shift_id', 0),
        ];

        $salesQuery = Sales::query()
            ->where('pos', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId));

        if (! empty($filters['from'])) {
            $salesQuery->whereDate('date', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $salesQuery->whereDate('date', '<=', $filters['to']);
        }
        if (! empty($filters['branch_id'])) {
            $salesQuery->where('branch_id', $filters['branch_id']);
        }
        if ($filters['warehouse_id'] > 0) {
            $salesQuery->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['user_id'] > 0) {
            $salesQuery->where('user_id', $filters['user_id']);
        }
        if ($filters['shift_id'] > 0) {
            $salesQuery->where('pos_shift_id', $filters['shift_id']);
        }

        $salesTotals = (clone $salesQuery)->where('sale_id', 0)->selectRaw(
            'SUM(total) as total, SUM(tax) as tax, SUM(tax_excise) as tax_excise, SUM(net) as net, SUM(profit) as profit'
        )->first();

        $returnTotals = (clone $salesQuery)->where('sale_id', '>', 0)->selectRaw(
            'SUM(total) as total, SUM(tax) as tax, SUM(tax_excise) as tax_excise, SUM(net) as net, SUM(profit) as profit'
        )->first();

        $saleIds = (clone $salesQuery)->pluck('id');
        $quantityTotal = 0;
        if ($saleIds->isNotEmpty()) {
            $quantityTotal = DB::table('sale_details')
                ->when($subscriberId, fn($q) => $q->where('sale_details.subscriber_id', $subscriberId))
                ->whereIn('sale_id', $saleIds)
                ->selectRaw('SUM(quantity * COALESCE(unit_factor, 1)) as qty')
                ->value('qty');
        }

        $payments = Payment::query()
            ->whereIn('sale_id', $saleIds)
            ->selectRaw("SUM(CASE WHEN paid_by = 'cash' THEN amount ELSE 0 END) as cash_total")
            ->selectRaw("SUM(CASE WHEN paid_by = 'bank' THEN amount ELSE 0 END) as bank_total")
            ->selectRaw("SUM(CASE WHEN paid_by LIKE 'card:%' THEN amount ELSE 0 END) as card_total")
            ->first();

        $summary = [
            'sales' => [
                'total' => (float) ($salesTotals->total ?? 0),
                'tax' => (float) ($salesTotals->tax ?? 0),
                'tax_excise' => (float) ($salesTotals->tax_excise ?? 0),
                'net' => (float) ($salesTotals->net ?? 0),
                'profit' => (float) ($salesTotals->profit ?? 0),
            ],
            'returns' => [
                'total' => (float) ($returnTotals->total ?? 0),
                'tax' => (float) ($returnTotals->tax ?? 0),
                'tax_excise' => (float) ($returnTotals->tax_excise ?? 0),
                'net' => (float) ($returnTotals->net ?? 0),
                'profit' => (float) ($returnTotals->profit ?? 0),
            ],
            'quantity' => (float) ($quantityTotal ?? 0),
            'payments' => [
                'cash' => (float) ($payments->cash_total ?? 0),
                'bank' => (float) ($payments->bank_total ?? 0),
                'card' => (float) ($payments->card_total ?? 0),
            ],
        ];

        $payload = [
            'summary' => $summary,
            'filters' => $filters,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $filters['branch_id'] ? Branch::find($filters['branch_id']) : null,
            'warehouse' => $filters['warehouse_id'] ? Warehouse::find($filters['warehouse_id']) : null,
            'cashier' => $filters['user_id'] ? User::find($filters['user_id']) : null,
            'shift' => $filters['shift_id'] ? PosShift::find($filters['shift_id']) : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/pos-end-of-day', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function resolveBranchId(Request $request): ?int
    {
        if (! empty(Auth::user()->branch_id)) {
            return (int) Auth::user()->branch_id;
        }

        if ($request->filled('branch_id') && (int) $request->branch_id > 0) {
            return (int) $request->branch_id;
        }

        return null;
    }
}
