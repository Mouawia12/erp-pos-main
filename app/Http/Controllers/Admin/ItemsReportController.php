<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanyInfo;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $categoryId = (int) $request->input('category_id', 0);
        $brandId = (int) $request->input('brand_id', 0);
        $warehouseId = (int) $request->input('warehouse_id', 0);
        $branchId = $this->resolveBranchId($request);
        $type = (int) $request->input('type', 0);

        $data = $this->buildItemsData($categoryId, $brandId, $warehouseId, $branchId, $type);

        $isBranches = $branchId ? 1 : 0;
        if (! empty(Auth::user()->branch_id)) {
            $isBranches = 1;
        }

        $totals = [
            'cost' => $data->sum('cost'),
            'price' => $data->sum('price'),
        ];

        $payload = [
            'data' => $data,
            'type' => $type,
            'isbranches' => $isBranches,
            'totals' => $totals,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'warehouse' => $warehouseId ? Warehouse::find($warehouseId) : null,
            'category' => $categoryId ? Category::find($categoryId) : null,
            'brand' => $brandId ? Brand::find($brandId) : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/items-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function buildItemsData(int $categoryId, int $brandId, int $warehouseId, ?int $branchId, int $type)
    {
        if ($branchId) {
            $query = Product::with('units')
                ->join('warehouse_products', 'warehouse_products.product_id', '=', 'products.id')
                ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
                ->join('branches', 'branches.id', '=', 'warehouses.branch_id')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->select(
                    'products.*',
                    'warehouse_products.quantity as qty',
                    'warehouse_products.warehouse_id as warehouseId',
                    'warehouses.branch_id as branchId',
                    'warehouses.name as warehouse_name',
                    'branches.branch_name',
                    'categories.name as categories_name'
                );

            if ($categoryId > 0) {
                $query->where('products.category_id', $categoryId);
            }
            if ($brandId > 0) {
                $query->where('products.brand', $brandId);
            }
            if ($warehouseId > 0) {
                $query->where('warehouse_products.warehouse_id', $warehouseId);
            }
            $query->where('branches.id', $branchId);

            if ($type == 1) {
                $query->whereColumn('warehouse_products.quantity', '<=', 'products.alert_quantity');
            } elseif ($type == 2) {
                $query->where('warehouse_products.quantity', '<=', 0);
            }

            return $query->orderBy('products.name')->get();
        }

        $query = Product::with('units')
            ->join('warehouse_products', 'warehouse_products.product_id', '=', 'products.id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->join('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->select('products.*', \DB::raw('SUM(warehouse_products.quantity) qty'))
            ->groupBy('warehouse_products.product_id');

        if ($categoryId > 0) {
            $query->where('products.category_id', $categoryId);
        }
        if ($brandId > 0) {
            $query->where('products.brand', $brandId);
        }
        if ($warehouseId > 0) {
            $query->where('warehouse_products.warehouse_id', $warehouseId);
        }

        if ($type == 1) {
            $query->whereRaw('products.alert_quantity > warehouse_products.quantity');
        } elseif ($type == 2) {
            $query->where('warehouse_products.quantity', '<=', 0);
        }

        return $query->orderBy('products.name')->get();
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
