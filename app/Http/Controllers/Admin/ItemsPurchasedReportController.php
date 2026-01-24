<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Purchase;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemsPurchasedReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $fdate = $request->input('from_date', '0');
        $tdate = $request->input('to_date', Carbon::now()->format('Y-m-d'));
        $warehouseId = (int) $request->input('warehouse_id', 0);
        $branchId = $this->resolveBranchId($request);
        $itemId = (int) $request->input('item_id', 0);
        $supplierId = (int) $request->input('supplier_id', 0);

        $data = DB::table('purchase_details')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->join('companies', 'purchases.customer_id', '=', 'companies.id')
            ->join('warehouses', 'warehouses.id', 'purchases.warehouse_id')
            ->join('branches', 'branches.id', 'purchases.branch_id')
            ->select(
                'purchases.*',
                'products.code as product_code',
                'products.name as product_name',
                'purchase_details.quantity',
                'purchase_details.cost_with_tax',
                'purchase_details.product_id',
                'companies.name as supplier_name',
                'warehouses.name as warehouse_name',
                'branches.branch_name'
            )
            ->get();

        if ($fdate && $fdate !== '0') {
            $data = $data->where('date', '>=', Carbon::parse($fdate)->format('Y-m-d'));
        }
        if ($tdate) {
            $data = $data->where('date', '<=', Carbon::parse($tdate)->format('Y-m-d'));
        }
        if ($warehouseId > 0) {
            $data = $data->where('warehouse_id', $warehouseId);
        }
        if ($branchId) {
            $data = $data->where('branch_id', $branchId);
        }
        if ($itemId > 0) {
            $data = $data->where('product_id', $itemId);
        }
        if ($supplierId > 0) {
            $data = $data->where('customer_id', $supplierId);
        }

        $periodAr = 'الفترة :';
        if ($fdate && $fdate !== '0') {
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
            'data' => $data,
            'period_ar' => $periodAr,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'warehouse' => $warehouseId ? Warehouse::find($warehouseId) : null,
            'supplier' => $supplierId ? Company::find($supplierId) : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/items-purchased-report', $payload)
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
