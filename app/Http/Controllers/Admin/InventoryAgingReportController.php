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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAgingReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $branchSelected = $request->branch_id ?? 0;
        if (! empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $lastPurchaseSub = DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->select('pd.product_id', 'pd.warehouse_id', DB::raw('MAX(p.date) as last_purchase_date'))
            ->when(Auth::user()->subscriber_id ?? null, fn($q, $v) => $q->where('p.subscriber_id', $v))
            ->groupBy('pd.product_id', 'pd.warehouse_id');

        $query = WarehouseProducts::query()
            ->join('products', 'products.id', '=', 'warehouse_products.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->leftJoin('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->leftJoinSub($lastPurchaseSub, 'lp', function ($join) {
                $join->on('warehouse_products.product_id', '=', 'lp.product_id')
                    ->on('warehouse_products.warehouse_id', '=', 'lp.warehouse_id');
            })
            ->select(
                'products.id',
                'products.code',
                'products.name',
                'products.category_id',
                'products.brand',
                'products.cost as product_cost',
                'warehouse_products.quantity',
                'warehouse_products.cost as warehouse_cost',
                'warehouses.name as warehouse_name',
                'branches.branch_name',
                'warehouses.branch_id',
                'lp.last_purchase_date'
            )
            ->where('warehouse_products.quantity', '>', 0);

        if ($branchSelected > 0) {
            $query->where('warehouses.branch_id', $branchSelected);
        }

        if ($request->warehouse_id) {
            $query->where('warehouse_products.warehouse_id', $request->warehouse_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('products.brand', $request->brand_id);
        }

        $agingTotals = [
            'current' => 0,
            '30' => 0,
            '60' => 0,
            '90' => 0,
            'over' => 0,
        ];

        $data = $query->orderBy('products.name')->get()->map(function ($row) use (&$agingTotals) {
            $cost = $row->warehouse_cost ?? $row->product_cost ?? 0;
            $row->unit_cost = (float) $cost;
            $row->value = (float) $row->quantity * $row->unit_cost;

            if ($row->last_purchase_date) {
                $days = Carbon::parse($row->last_purchase_date)->diffInDays(now());
            } else {
                $days = null;
            }

            $row->days_since = $days;
            $row->aging_bucket = 'N/A';

            if ($days !== null) {
                if ($days <= 30) {
                    $row->aging_bucket = '0-30';
                    $agingTotals['current'] += $row->value;
                } elseif ($days <= 60) {
                    $row->aging_bucket = '31-60';
                    $agingTotals['30'] += $row->value;
                } elseif ($days <= 90) {
                    $row->aging_bucket = '61-90';
                    $agingTotals['60'] += $row->value;
                } elseif ($days <= 120) {
                    $row->aging_bucket = '91-120';
                    $agingTotals['90'] += $row->value;
                } else {
                    $row->aging_bucket = '120+';
                    $agingTotals['over'] += $row->value;
                }
            }

            return $row;
        });

        $payload = [
            'data' => $data,
            'agingTotals' => $agingTotals,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchSelected ? Branch::find($branchSelected) : null,
            'warehouse' => $request->warehouse_id ? Warehouse::find($request->warehouse_id) : null,
            'category' => $request->category_id ? Category::find($request->category_id) : null,
            'brand' => $request->brand_id ? Brand::find($request->brand_id) : null,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/inventory-aging-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
