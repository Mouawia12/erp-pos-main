<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\CostCenter;
use App\Models\Product;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesItemReportController extends Controller
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

        $fdate = $request->input('from_date');
        $tdate = $request->input('to_date');
        $warehouse = (int) $request->input('warehouse_id', 0);
        $branchId = $this->resolveBranchId($request);
        $itemId = (int) $request->input('item_id', 0);
        $supplierId = (int) $request->input('supplier_id', 0);
        $vehiclePlate = $request->input('vehicle_plate', 'empty');
        $costCenterId = (int) $request->input('cost_center_id', 0);

        $dataQuery = DB::table('sale_details')
            ->join('sales', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'products.id', 'sale_details.product_id')
            ->join('companies', 'sales.customer_id', '=', 'companies.id')
            ->join('warehouses', 'warehouses.id', 'sales.warehouse_id')
            ->join('branches', 'branches.id', 'sales.branch_id')
            ->select(
                'sale_details.*',
                'sales.date as bill_date',
                'sales.invoice_no as invoice_no',
                'sales.branch_id',
                'products.code as product_code',
                'products.name as product_name',
                'warehouses.name as warehouse_name',
                'branches.branch_name',
                'sales.warehouse_id',
                'sales.customer_id',
                'sales.date',
                'sales.vehicle_plate',
                'sales.vehicle_odometer',
                'companies.name as customer_name'
            )
            ->where('sales.sale_id', '=', 0)
            ->when($subscriberId, function ($q) use ($subscriberId) {
                $q->where('sale_details.subscriber_id', $subscriberId);
            });

        if ($fdate) {
            $dataQuery->whereDate('sales.date', '>=', Carbon::parse($fdate)->format('Y-m-d'));
        }
        if ($tdate) {
            $dataQuery->whereDate('sales.date', '<=', Carbon::parse($tdate)->format('Y-m-d'));
        }
        if ($warehouse > 0) {
            $dataQuery->where('sales.warehouse_id', $warehouse);
        }
        if ($branchId) {
            $dataQuery->where('sales.branch_id', $branchId);
        }
        if ($costCenterId > 0) {
            $dataQuery->where('sales.cost_center_id', $costCenterId);
        }
        if ($itemId > 0) {
            $dataQuery->where('sale_details.product_id', $itemId);
        }
        if ($supplierId > 0) {
            $dataQuery->where('sales.customer_id', $supplierId);
        }
        if (! empty($vehiclePlate) && $vehiclePlate !== 'empty') {
            $dataQuery->where('sales.vehicle_plate', 'like', '%' . $vehiclePlate . '%');
        }

        $rows = $dataQuery->get();

        $totals = [
            'total' => 0,
            'tax' => 0,
            'net' => 0,
        ];
        foreach ($rows as $row) {
            $lineTotal = (float) ($row->total ?? 0);
            $lineTax = (float) ($row->tax ?? 0) + (float) ($row->tax_excise ?? 0);
            $totals['total'] += $lineTotal;
            $totals['tax'] += $lineTax;
            $totals['net'] += $lineTotal + $lineTax;
        }

        $periodAr = 'الفترة :';
        if ($fdate) {
            $periodAr .= $fdate;
        } else {
            $periodAr .= 'من البداية';
        }

        if ($tdate) {
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
            'warehouse' => $warehouse ? Warehouse::find($warehouse) : null,
            'customer' => $supplierId ? Company::find($supplierId) : null,
            'item' => $itemId ? Product::find($itemId) : null,
            'costCenter' => $costCenterId ? CostCenter::find($costCenterId) : null,
            'vehiclePlate' => $vehiclePlate && $vehiclePlate !== 'empty' ? $vehiclePlate : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/sales-item-report', $payload)
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
