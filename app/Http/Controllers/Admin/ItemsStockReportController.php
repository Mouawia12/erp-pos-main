<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanyInfo;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsStockReportController extends Controller
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
        $warehouse = (int) $request->input('warehouse_id', 0);
        $branchId = $this->resolveBranchId($request);
        $itemId = (int) $request->input('item_id', 0);

        $data = [];

        $purchases = Purchase::join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->select(
                'purchases.warehouse_id as warehouse',
                'purchases.date as date',
                'purchase_details.product_id as item_id',
                'purchase_details.quantity as qnt',
                'purchases.branch_id',
                'products.code as product_code',
                'products.name as product_name'
            )
            ->where('purchases.returned_bill_id', '=', 0)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('purchases.subscriber_id', $sub);
            })
            ->get();

        if ($warehouse > 0) {
            $purchases = $purchases->where('warehouse', $warehouse);
        }
        if ($branchId) {
            $purchases = $purchases->where('branch_id', $branchId);
        }

        foreach ($purchases as $purchase) {
            $data[] = [
                'date' => $purchase->date,
                'item_id' => $purchase->item_id,
                'product_code' => $purchase->product_code,
                'product_name' => $purchase->product_name,
                'qnt' => $purchase->qnt,
                'warehouse' => $purchase->warehouse,
                'type' => 1,
            ];
        }

        $returnPurchase = Purchase::join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->select(
                'purchases.warehouse_id as warehouse',
                'purchases.date as date',
                'purchase_details.product_id as item_id',
                'purchase_details.quantity as qnt',
                'purchases.branch_id',
                'products.code as product_code',
                'products.name as product_name'
            )
            ->where('purchases.returned_bill_id', '<>', 0)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('purchases.subscriber_id', $sub);
            })
            ->get();

        if ($warehouse > 0) {
            $returnPurchase = $returnPurchase->where('warehouse', $warehouse);
        }
        if ($branchId) {
            $returnPurchase = $returnPurchase->where('branch_id', $branchId);
        }

        foreach ($returnPurchase as $item) {
            $data[] = [
                'date' => $item->date,
                'item_id' => $item->item_id,
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'qnt' => $item->qnt,
                'warehouse' => $item->warehouse,
                'type' => 2,
            ];
        }

        $sales = Sales::join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'sales.warehouse_id as warehouse',
                'sales.date as date',
                'sale_details.product_id as item_id',
                'sale_details.quantity as qnt',
                'sales.branch_id',
                'products.code as product_code',
                'products.name as product_name'
            )
            ->where('sales.sale_id', '=', 0)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('sales.subscriber_id', $sub);
            })
            ->get();

        if ($warehouse > 0) {
            $sales = $sales->where('warehouse', $warehouse);
        }
        if ($branchId) {
            $sales = $sales->where('branch_id', $branchId);
        }

        foreach ($sales as $item) {
            $data[] = [
                'date' => $item->date,
                'item_id' => $item->item_id,
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'qnt' => $item->qnt,
                'warehouse' => $item->warehouse,
                'type' => 3,
            ];
        }

        $salesReturn = Sales::join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select(
                'sales.warehouse_id as warehouse',
                'sales.date as date',
                'sale_details.product_id as item_id',
                'sale_details.quantity as qnt',
                'sales.branch_id',
                'products.code as product_code',
                'products.name as product_name'
            )
            ->where('sales.sale_id', '<>', 0)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('sales.subscriber_id', $sub);
            })
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('sale_details.subscriber_id', $sub);
            })
            ->get();

        if ($warehouse > 0) {
            $salesReturn = $salesReturn->where('warehouse', $warehouse);
        }
        if ($branchId) {
            $salesReturn = $salesReturn->where('branch_id', $branchId);
        }

        foreach ($salesReturn as $item) {
            $data[] = [
                'date' => $item->date,
                'item_id' => $item->item_id,
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'qnt' => $item->qnt,
                'warehouse' => $item->warehouse,
                'type' => 4,
            ];
        }

        $result = [];
        $group = [];

        foreach ($data as $element) {
            $group[$element['item_id']][] = $element;
        }

        foreach ($group as $element) {
            $qnt_update = 0;
            $qnt_purchase = 0;
            $qnt_purchase_return = 0;
            $qnt_sales = 0;
            $qnt_sales_return = 0;
            foreach ($element as $subElement) {
                if ($subElement['type'] == 0) {
                    $qnt_update += $subElement['qnt'];
                } elseif ($subElement['type'] == 1) {
                    $qnt_purchase += $subElement['qnt'];
                } elseif ($subElement['type'] == 2) {
                    $qnt_purchase_return += $subElement['qnt'];
                } elseif ($subElement['type'] == 3) {
                    $qnt_sales += $subElement['qnt'];
                } elseif ($subElement['type'] == 4) {
                    $qnt_sales_return += $subElement['qnt'];
                }
            }

            $result[] = [
                'date' => $subElement['date'],
                'item_id' => $subElement['item_id'],
                'product_code' => $subElement['product_code'],
                'product_name' => $subElement['product_name'],
                'qnt_update' => $qnt_update,
                'qnt_purchase' => $qnt_purchase,
                'qnt_purchase_return' => $qnt_purchase_return,
                'qnt_sales' => $qnt_sales,
                'qnt_sales_return' => $qnt_sales_return,
                'warehouse' => $subElement['warehouse'],
            ];
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

        $warehouseName = $warehouse ? (Warehouse::find($warehouse)?->name ?? __('main.all')) : __('main.all');
        $branchName = $branchId ? (Branch::find($branchId)?->branch_name ?? __('main.all')) : __('main.all');

        $payload = [
            'result' => $result,
            'fdate' => $fdate,
            'tdate' => $tdate,
            'warehouse' => $warehouse,
            'warehouse_name' => $warehouseName,
            'item_id' => $itemId,
            'period_ar' => $periodAr,
            'branch_name' => $branchName,
            'companyInfo' => CompanyInfo::first(),
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/items-stock-report', $payload)
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
