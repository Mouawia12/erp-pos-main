<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Models\Sales;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CostCenter;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailySalesReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $date = $request->input('bill_date');
        $warehouse = (int) $request->input('warehouse_id', 0);
        $branchId = $this->resolveBranchId($request);
        $customerId = (int) $request->input('customer_id', 0);
        $costCenterId = (int) $request->input('cost_center_id', 0);
        $vehiclePlate = $request->input('vehicle_plate', 'empty');

        $data = Sales::where('sale_id', 0)->get();

        if ($date) {
            $data = $data->where('date', Carbon::parse($date)->format('Y-m-d'));
        }
        if ($warehouse > 0) {
            $data = $data->where('warehouse_id', $warehouse);
        }
        if ($branchId) {
            $data = $data->where('branch_id', $branchId);
        }
        if ($customerId > 0) {
            $data = $data->where('customer_id', $customerId);
        }
        if ($costCenterId > 0) {
            $data = $data->where('cost_center_id', $costCenterId);
        }
        if (! empty($vehiclePlate) && $vehiclePlate !== 'empty') {
            $data = $data->where('vehicle_plate', 'like', '%' . $vehiclePlate . '%');
        }

        $periodAr = 'الفترة :';
        if ($date) {
            $periodAr .= Carbon::parse($date)->format('Y-m-d');
        } else {
            $periodAr .= 'من البداية';
        }

        $totals = [
            'total' => $data->sum('total'),
            'tax' => $data->sum(function ($row) {
                return (float) ($row->tax ?? 0) + (float) ($row->tax_excise ?? 0);
            }),
            'net' => $data->sum('net'),
        ];

        $payload = [
            'data' => $data,
            'period_ar' => $periodAr,
            'totals' => $totals,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'warehouse' => $warehouse ? Warehouse::find($warehouse) : null,
            'customer' => $customerId ? Company::find($customerId) : null,
            'costCenter' => $costCenterId ? CostCenter::find($costCenterId) : null,
            'vehiclePlate' => $vehiclePlate && $vehiclePlate !== 'empty' ? $vehiclePlate : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/daily-sales-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape');

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
