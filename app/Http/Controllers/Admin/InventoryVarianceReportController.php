<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanyInfo;
use App\Models\Inventory;
use App\Models\InventoryDetails;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class InventoryVarianceReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $inventories = Inventory::query()
            ->with(['warehouse', 'branch'])
            ->orderByDesc('date')
            ->get();

        $selectedInventoryId = $request->has('inventory_id') ? (int) $request->inventory_id : null;
        if ($selectedInventoryId === null && $inventories->isNotEmpty()) {
            $selectedInventoryId = $inventories->first()->id;
        }

        $branchSelected = $request->branch_id ?? 0;
        if (! empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $query = InventoryDetails::query()
            ->join('inventorys as i', 'i.id', '=', 'inventory_details.inventory_id')
            ->join('products as p', 'p.id', '=', 'inventory_details.item_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'i.warehouse_id')
            ->leftJoin('branches as b', 'b.id', '=', 'i.branch_id')
            ->leftJoin('units as u', 'u.id', '=', 'inventory_details.unit')
            ->select(
                'inventory_details.*',
                'p.code as product_code',
                'p.name as product_name',
                'p.cost as product_cost',
                'w.name as warehouse_name',
                'b.branch_name',
                'i.date as inventory_date',
                'i.id as inventory_id',
                'u.name as unit_name',
                'i.branch_id',
                'i.warehouse_id'
            );

        if ($selectedInventoryId) {
            $query->where('inventory_details.inventory_id', $selectedInventoryId);
        }

        if ($branchSelected > 0) {
            $query->where('i.branch_id', $branchSelected);
        }

        if ($request->warehouse_id) {
            $query->where('i.warehouse_id', $request->warehouse_id);
        }

        if ($request->date_from) {
            $query->whereDate('i.date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('i.date', '<=', $request->date_to);
        }

        $differenceType = $request->difference_type ?? 'all';
        if ($differenceType === 'shortage') {
            $query->whereRaw('inventory_details.new_quantity < inventory_details.quantity');
        } elseif ($differenceType === 'excess') {
            $query->whereRaw('inventory_details.new_quantity > inventory_details.quantity');
        } else {
            $query->whereRaw('inventory_details.new_quantity <> inventory_details.quantity');
        }

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('inventorys', 'subscriber_id')) {
                $query->where('i.subscriber_id', Auth::user()->subscriber_id);
            }
            if (Schema::hasColumn('inventory_details', 'subscriber_id')) {
                $query->where('inventory_details.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        $data = $query->orderBy('inventory_details.id')->get()->map(function ($row) {
            $row->difference = (float) $row->new_quantity - (float) $row->quantity;
            $row->difference_value = $row->difference * ((float) ($row->product_cost ?? 0));
            return $row;
        });

        $totals = [
            'shortage' => $data->where('difference', '<', 0)->sum('difference_value'),
            'excess' => $data->where('difference', '>', 0)->sum('difference_value'),
        ];

        $payload = [
            'data' => $data,
            'totals' => $totals,
            'inventories' => $inventories,
            'inventory' => $selectedInventoryId ? $inventories->firstWhere('id', $selectedInventoryId) : null,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchSelected ? Branch::find($branchSelected) : null,
            'warehouse' => $request->warehouse_id ? Warehouse::find($request->warehouse_id) : null,
            'differenceType' => $differenceType,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/inventory-variance-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
