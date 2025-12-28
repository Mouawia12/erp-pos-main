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
        $counts = StockCount::with(['items', 'warehouse'])->latest()->get();
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
            'is_opening' => 'nullable|boolean',
            'items.*.batch_no' => 'nullable|string|max:191',
            'items.*.production_date' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        $isOpening = $request->boolean('is_opening');
        $reference = 'SC-'.Str::padLeft(StockCount::max('id')+1, 5, '0');

        $count = StockCount::create([
            'reference' => $reference,
            'warehouse_id' => $request->warehouse_id,
            'branch_id' => $request->branch_id ?? null,
            'user_id' => Auth::id(),
            'subscriber_id' => Auth::user()->subscriber_id ?? null,
            'status' => $isOpening ? 'approved' : 'draft',
            'is_opening' => $isOpening,
            'note' => $request->note,
        ]);

        foreach($request->items as $item){
            $expected = $isOpening
                ? 0
                : $this->getExpectedQty($request->warehouse_id, $item['product_id'], $item['variant_id'] ?? null);
            $countItem = StockCountItem::create([
                'stock_count_id' => $count->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_color' => $item['variant_color'] ?? null,
                'variant_size' => $item['variant_size'] ?? null,
                'variant_barcode' => $item['variant_barcode'] ?? null,
                'batch_no' => $item['batch_no'] ?? null,
                'production_date' => $item['production_date'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'expected_qty' => $expected,
                'counted_qty' => $item['counted_qty'] ?? 0,
                'difference' => ($item['counted_qty'] ?? 0) - $expected,
            ]);

            if ($isOpening) {
                $this->applyAdjustment($request->warehouse_id, $countItem, true);
            }
        }

        return redirect()->route('stock_counts.index')->with('success', __('main.created'));
    }

    public function approve(StockCount $stock_count)
    {
        $stock_count->load('items');
        foreach($stock_count->items as $item){
            $this->applyAdjustment($stock_count->warehouse_id, $item, (bool) $stock_count->is_opening);
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

    private function applyAdjustment($warehouseId, StockCountItem $item, bool $isOpening = false): void
    {
        $row = WarehouseProducts::firstOrCreate(
            ['warehouse_id'=>$warehouseId, 'product_id'=>$item->product_id],
            ['quantity'=>0,'cost'=>0]
        );
        $previousQty = (float) $row->quantity;
        if ($isOpening) {
            $row->update(['quantity' => $item->counted_qty]);
            $delta = (float) $item->counted_qty - $previousQty;
        } else {
            $delta = $item->difference;
            $row->update(['quantity' => $row->quantity + $delta]);
        }

        if ($delta != 0) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->update(['quantity' => $product->quantity + $delta]);
            }
        }

        // Update variant if exists
        if($item->variant_id){
            $variant = ProductVariant::find($item->variant_id);
            if($variant){
                if ($isOpening) {
                    $variant->update(['quantity' => $item->counted_qty]);
                } else {
                    $variant->update(['quantity' => $variant->quantity + $delta]);
                }
            }
        }
    }
}
