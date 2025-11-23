<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Models\WarehouseProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseTransferController extends Controller
{
    public function index()
    {
        $transfers = WarehouseTransfer::with(['fromWarehouse','toWarehouse'])
            ->latest()->get();
        return view('admin.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::with('variants')->get();
        return view('admin.transfers.create', compact('warehouses','products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id' => 'required|integer|different:from_warehouse_id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $transfer = WarehouseTransfer::create([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'status' => 'pending',
            'reason' => $request->reason,
            'branch_id' => $request->branch_id ?? null,
            'user_id' => Auth::id(),
            'subscriber_id' => Auth::user()->subscriber_id ?? null,
        ]);

        foreach($request->items as $item){
            WarehouseTransferItem::create([
                'warehouse_transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_color' => $item['variant_color'] ?? null,
                'variant_size' => $item['variant_size'] ?? null,
                'variant_barcode' => $item['variant_barcode'] ?? null,
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('transfers.index')->with('success', __('main.created'));
    }

    public function show(WarehouseTransfer $transfer)
    {
        $transfer->load('items');
        return view('admin.transfers.show', compact('transfer'));
    }

    public function approve(WarehouseTransfer $transfer)
    {
        $transfer->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);
        $this->applyStockMovement($transfer, false);
        return back()->with('success', __('main.done'));
    }

    public function reject(Request $request, WarehouseTransfer $transfer)
    {
        $request->validate(['reject_reason' => 'required|string|max:500']);
        $transfer->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'approved_by' => Auth::id(),
        ]);
        return back()->with('success', __('main.updated'));
    }

    public function markDamaged(WarehouseTransfer $transfer)
    {
        $transfer->update([
            'status' => 'damaged',
            'approved_by' => Auth::id(),
        ]);
        $this->applyStockMovement($transfer, true);
        return back()->with('success', __('main.updated'));
    }

    public function destroy(WarehouseTransfer $transfer)
    {
        $transfer->items()->delete();
        $transfer->delete();
        return back()->with('success', __('main.deleted'));
    }

    private function applyStockMovement(WarehouseTransfer $transfer, bool $toDamaged = false): void
    {
        $transfer->load('items');
        foreach ($transfer->items as $item) {
            // خصم من المستودع المصدر
            $this->updateWarehouseProduct($transfer->from_warehouse_id, $item->product_id, -$item->quantity);
            // إضافة للمستودع الهدف أو تجاهل (التالف)
            if(!$toDamaged){
                $this->updateWarehouseProduct($transfer->to_warehouse_id, $item->product_id, $item->quantity);
            }
            // تحديث مخزون المتغير إن وجد
            if(!empty($item->variant_id)){
                $variant = ProductVariant::find($item->variant_id);
                if($variant){
                    $variant->update(['quantity' => $variant->quantity + ($toDamaged ? 0 : $item->quantity) - $item->quantity]);
                }
            }
        }
    }

    private function updateWarehouseProduct($warehouseId, $productId, $delta)
    {
        $row = WarehouseProducts::firstOrCreate(
            ['warehouse_id'=>$warehouseId, 'product_id'=>$productId],
            ['quantity'=>0,'cost'=>0]
        );
        $row->update(['quantity' => $row->quantity + $delta]);
    }
}
