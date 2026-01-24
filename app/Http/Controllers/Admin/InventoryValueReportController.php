<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanyInfo;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryValueReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $siteBranchId = $this->resolveBranchId($request);
        $warehouseId = (int) $request->input('warehouse_id', 0);
        $categoryId = (int) $request->input('category_id', 0);
        $brandId = (int) $request->input('brand_id', 0);

        $branchSelected = $request->branch_id ?? 0;
        if (! empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $query = WarehouseProducts::query()
            ->join('products', 'products.id', '=', 'warehouse_products.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->leftJoin('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->select(
                'products.name',
                'products.code',
                'warehouse_products.quantity',
                'warehouse_products.cost',
                'warehouse_products.price',
                'warehouses.name as warehouse_name',
                'branches.branch_name'
            );

        if ($branchSelected > 0) {
            $query->where('warehouses.branch_id', $branchSelected);
        }
        if ($warehouseId > 0) {
            $query->where('warehouses.id', $warehouseId);
        }
        if ($categoryId > 0) {
            $query->where('products.category_id', $categoryId);
        }
        if ($brandId > 0) {
            $query->where('products.brand_id', $brandId);
        }

        $data = $query->orderBy('products.name')->get();

        $totalValue = $data->sum(function ($row) {
            $qty = (float) ($row->quantity ?? 0);
            $unitCost = (float) ($row->cost ?? 0);
            return $qty * $unitCost;
        });

        $payload = [
            'data' => $data,
            'totalValue' => $totalValue,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchSelected ? Branch::find($branchSelected) : null,
            'warehouse' => $warehouseId ? Warehouse::find($warehouseId) : null,
            'category' => $categoryId ? Category::find($categoryId) : null,
            'brand' => $brandId ? Brand::find($brandId) : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/inventory-value-report', $payload)
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
