<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanyInfo;
use App\Models\Purchase;
use App\Models\Representative;
use App\Models\Sales;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepresentativesReportController extends Controller
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
        $branchId = $this->resolveBranchId($request);
        $representativeId = (int) $request->input('representative_id', 0);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $salesQuery = Sales::query()
            ->where('sale_id', 0)
            ->when($subscriberId, fn($q, $v) => $q->where('subscriber_id', $v));

        $purchaseQuery = Purchase::query()
            ->where('returned_bill_id', 0)
            ->when($subscriberId, fn($q, $v) => $q->where('subscriber_id', $v));

        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
            $purchaseQuery->where('branch_id', $branchId);
        }

        if ($dateFrom) {
            $salesQuery->whereDate('date', '>=', $dateFrom);
            $purchaseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $salesQuery->whereDate('date', '<=', $dateTo);
            $purchaseQuery->whereDate('date', '<=', $dateTo);
        }

        if ($representativeId > 0) {
            $salesQuery->where('representative_id', $representativeId);
            $purchaseQuery->where('representative_id', $representativeId);
        }

        $sales = $salesQuery
            ->select('representative_id', DB::raw('COUNT(*) as invoices'), DB::raw('SUM(net) as net'), DB::raw('SUM(paid) as paid'))
            ->groupBy('representative_id')
            ->get()
            ->keyBy('representative_id');

        $purchases = $purchaseQuery
            ->select('representative_id', DB::raw('SUM(net) as net'))
            ->groupBy('representative_id')
            ->get()
            ->keyBy('representative_id');

        $representatives = Representative::all();

        $rows = $representatives->map(function ($rep) use ($sales, $purchases) {
            $salesRow = $sales->get($rep->id);
            $purchaseRow = $purchases->get($rep->id);
            $net = (float) ($salesRow->net ?? 0);
            $paid = (float) ($salesRow->paid ?? 0);
            return [
                'id' => $rep->id,
                'name' => $rep->user_name,
                'invoices' => (int) ($salesRow->invoices ?? 0),
                'sales_net' => $net,
                'sales_paid' => $paid,
                'sales_remain' => $net - $paid,
                'purchase_net' => (float) ($purchaseRow->net ?? 0),
            ];
        })->filter(function ($row) use ($representativeId) {
            if ($representativeId > 0) {
                return $row['id'] == $representativeId;
            }
            return true;
        })->values();

        $totals = [
            'invoices' => $rows->sum('invoices'),
            'sales_net' => $rows->sum('sales_net'),
            'sales_paid' => $rows->sum('sales_paid'),
            'sales_remain' => $rows->sum('sales_remain'),
            'purchase_net' => $rows->sum('purchase_net'),
        ];

        $payload = [
            'rows' => $rows,
            'totals' => $totals,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'representative' => $representativeId > 0 ? Representative::find($representativeId) : null,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/representatives-report', $payload)
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
