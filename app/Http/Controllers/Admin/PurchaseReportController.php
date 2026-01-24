<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\CostCenter;
use App\Models\Purchase;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $fdate = $request->input('from_date');
        $tdate = $request->input('to_date');
        $warehouseId = (int) $request->input('warehouse_id', 0);
        $billNo = $request->input('bill_no', 'empty');
        $vendorId = (int) $request->input('vendor_id', 0);
        $branchId = $this->resolveBranchId($request);
        $costCenterId = (int) $request->input('cost_center_id', 0);

        $query = Purchase::query()
            ->with(['branch', 'warehouse', 'customer'])
            ->where('returned_bill_id', 0);

        if ($fdate && $fdate !== '0') {
            $query->whereDate('date', '>=', Carbon::parse($fdate)->format('Y-m-d'));
        }
        if ($tdate && $tdate !== '0') {
            $query->whereDate('date', '<=', Carbon::parse($tdate)->format('Y-m-d'));
        }
        if ($warehouseId > 0) {
            $query->where('warehouse_id', $warehouseId);
        }
        if (! empty($billNo) && $billNo !== 'empty') {
            $query->where('invoice_no', $billNo);
        }
        if ($vendorId > 0) {
            $query->where('customer_id', $vendorId);
        }
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($costCenterId > 0) {
            $query->where('cost_center_id', $costCenterId);
        }

        $rows = $query->get();

        $totals = [
            'total' => $rows->sum('net'),
            'paid' => $rows->sum('paid'),
            'remain' => $rows->sum(function ($row) {
                return (float) ($row->net ?? 0) - (float) ($row->paid ?? 0);
            }),
        ];

        $periodAr = 'الفترة :';
        if ($fdate && $fdate !== '0') {
            $periodAr .= $fdate;
        } else {
            $periodAr .= 'من البداية';
        }

        if ($tdate && $tdate !== '0') {
            $periodAr .= ' -- ' . Carbon::parse($tdate)->format('Y-m-d');
        } else {
            $periodAr .= ' -- حتى اليوم';
        }

        $payload = [
            'rows' => $rows,
            'totals' => $totals,
            'periodAr' => $periodAr,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'warehouse' => $warehouseId ? Warehouse::find($warehouseId) : null,
            'vendor' => $vendorId ? Company::find($vendorId) : null,
            'costCenter' => $costCenterId ? CostCenter::find($costCenterId) : null,
            'billNo' => $billNo && $billNo !== 'empty' ? $billNo : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/purchase-report', $payload)
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
