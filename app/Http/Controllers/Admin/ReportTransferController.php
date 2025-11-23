<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use Illuminate\Http\Request;

class ReportTransferController extends Controller
{
    public function index(Request $request)
    {
        $transfers = WarehouseTransfer::with(['fromWarehouse','toWarehouse'])
            ->when($request->status, fn($q,$v)=>$q->where('status',$v))
            ->when($request->from_warehouse_id, fn($q,$v)=>$q->where('from_warehouse_id',$v))
            ->when($request->to_warehouse_id, fn($q,$v)=>$q->where('to_warehouse_id',$v))
            ->latest()->get();

        $warehouses = Warehouse::all();
        return view('admin.transfers.report', compact('transfers','warehouses'));
    }
}
