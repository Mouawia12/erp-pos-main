<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockCountController extends Controller
{
    public function index()
    {
        $counts = StockCount::with('items')->latest()->get();
        return view('admin.stock_counts.index', compact('counts'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::with('variants')->get();
        return view('admin.stock_counts.create', compact('warehouses','products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
        ]);

        $reference = 'SC-'.Str::padLeft(StockCount::max('id')+1, 5, '0');

        $count = StockCount::create([
            'reference' => $reference,
            'warehouse_id' => $request->warehouse_id,
            'branch_id' => $request->branch_id ?? null,
            'user_id' => Auth::id(),
            'subscriber_id' => Auth::user()->subscriber_id ?? null,
            'status' => 'draft',
            'note' => $request->note,
        ]);

        foreach($request->items as $item){
            $expected = $this->getExpectedQty($request->warehouse_id, $item['product_id'], $item['variant_id'] ?? null);
            StockCountItem::create([
                'stock_count_id' => $count->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_color' => $item['variant_color'] ?? null,
                'variant_size' => $item['variant_size'] ?? null,
                'variant_barcode' => $item['variant_barcode'] ?? null,
                'expected_qty' => $expected,
                'counted_qty' => $item['counted_qty'] ?? 0,
                'difference' => ($item['counted_qty'] ?? 0) - $expected,
            ]);
        }

        return redirect()->route('stock_counts.index')->with('success', __('main.created'));
    }

    public function approve(StockCount $stock_count)
    {
        $stock_count->load('items');
        foreach($stock_count->items as $item){
            $this->applyAdjustment($stock_count->warehouse_id, $item);
        }
        $stock_count->update(['status' => 'approved']);
        return back()->with('success', __('main.done'));
    }

    public function destroy(StockCount $stock_count)
    {
        $stock_count->items()->delete();
        $stock_count->delete();
        return back()->with('success', __('main.deleted'));
    }

    private function getExpectedQty($warehouseId, $productId, $variantId = null): float
    {
        $row = WarehouseProducts::where('warehouse_id',$warehouseId)
            ->where('product_id',$productId)
            ->first();
        return (float) ($row->quantity ?? 0);
    }

    private function applyAdjustment($warehouseId, StockCountItem $item): void
    {
        $delta = $item->difference;
        // Update warehouse product
        $row = WarehouseProducts::firstOrCreate(
            ['warehouse_id'=>$warehouseId, 'product_id'=>$item->product_id],
            ['quantity'=>0,'cost'=>0]
        );
        $row->update(['quantity' => $row->quantity + $delta]);

        // Update variant if exists
        if($item->variant_id){
            $variant = ProductVariant::find($item->variant_id);
            if($variant){
                $variant->update(['quantity' => $variant->quantity + $delta]);
            }
        }
    }
}
