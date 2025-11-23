<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Models\PromotionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::withCount('items')->latest()->get();
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::with('variants')->get();
        return view('admin.promotions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.min_qty' => 'nullable|integer|min:1',
            'items.*.discount_value' => 'required|numeric',
            'items.*.discount_type' => 'required|in:percent,amount',
        ]);

        $promotion = Promotion::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 'active',
            'branch_id' => $request->branch_id,
            'subscriber_id' => Auth::user()->subscriber_id ?? null,
            'note' => $request->note,
        ]);

        $this->syncItems($promotion, $request->items);

        return redirect()->route('promotions.index')->with('success', __('main.created'));
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('items');
        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion)
    {
        $promotion->load('items');
        $products = Product::with('variants')->get();
        return view('admin.promotions.edit', compact('promotion','products'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.min_qty' => 'nullable|integer|min:1',
            'items.*.discount_value' => 'required|numeric',
            'items.*.discount_type' => 'required|in:percent,amount',
        ]);

        $promotion->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 'active',
            'branch_id' => $request->branch_id,
            'note' => $request->note,
        ]);

        $promotion->items()->delete();
        $this->syncItems($promotion, $request->items);

        return redirect()->route('promotions.index')->with('success', __('main.updated'));
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->items()->delete();
        $promotion->delete();
        return back()->with('success', __('main.deleted'));
    }

    private function syncItems(Promotion $promotion, array $items): void
    {
        foreach($items as $item){
            PromotionItem::create([
                'promotion_id' => $promotion->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_color' => $item['variant_color'] ?? null,
                'variant_size' => $item['variant_size'] ?? null,
                'variant_barcode' => $item['variant_barcode'] ?? null,
                'min_qty' => $item['min_qty'] ?? 1,
                'discount_value' => $item['discount_value'] ?? 0,
                'discount_type' => $item['discount_type'] ?? 'percent',
                'special_barcode' => $item['special_barcode'] ?? null,
                'max_qty' => $item['max_qty'] ?? null,
            ]);
        }
    }
}
