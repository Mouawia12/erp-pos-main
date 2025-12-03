<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\SystemController;
use App\Models\Product;
use App\Models\ProductAssembly;
use App\Models\ProductAssemblyItem;
use App\Models\ProductRecipe;
use App\Models\ProductRecipeItem;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManufacturingController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $recipes = ProductRecipe::with(['product', 'items.component'])
            ->orderByDesc('id')
            ->get();
        $recentAssemblies = ProductAssembly::with(['product', 'warehouse'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.manufacturing.index', compact('products', 'warehouses', 'recipes', 'recentAssemblies'));
    }

    public function storeRecipe(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'nullable|exists:product_recipes,id',
            'product_id' => 'required|exists:products,id',
            'name' => 'nullable|string|max:255',
            'yield_quantity' => 'nullable|numeric|min:0.0001',
            'notes' => 'nullable|string|max:500',
            'component_product_id' => 'required|array|min:1',
            'component_product_id.*' => 'required|distinct|different:product_id|exists:products,id',
            'component_quantity' => 'required|array|min:1',
            'component_quantity.*' => 'required|numeric|min:0.0001',
        ]);

        $components = collect($validated['component_product_id'])
            ->map(function ($productId, $index) use ($validated) {
                $quantity = $validated['component_quantity'][$index] ?? null;
                if (! $quantity) {
                    return null;
                }

                return [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ];
            })
            ->filter();

        if ($components->isEmpty()) {
            throw ValidationException::withMessages([
                'component_product_id' => __('يجب تحديد مكون واحد على الأقل.'),
            ]);
        }

        $user = Auth::user();
        $subscriberId = $user->subscriber_id ?? null;

        DB::transaction(function () use ($request, $validated, $components, $subscriberId, $user) {
            if ($validated['recipe_id'] ?? false) {
                $recipe = ProductRecipe::query()
                    ->when($subscriberId, fn ($q, $sub) => $q->where('subscriber_id', $sub))
                    ->findOrFail($validated['recipe_id']);

                $recipe->update([
                    'product_id' => $validated['product_id'],
                    'name' => $validated['name'],
                    'yield_quantity' => $validated['yield_quantity'] ?? 1,
                    'notes' => $validated['notes'],
                ]);
            } else {
                $recipe = ProductRecipe::updateOrCreate(
                    [
                        'product_id' => $validated['product_id'],
                        'subscriber_id' => $subscriberId,
                    ],
                    [
                        'name' => $validated['name'],
                        'yield_quantity' => $validated['yield_quantity'] ?? 1,
                        'notes' => $validated['notes'],
                        'created_by' => $user?->id,
                    ]
                );
            }

            $recipe->items()->delete();

            foreach ($components as $component) {
                $recipe->items()->create([
                    'component_product_id' => $component['product_id'],
                    'quantity' => $component['quantity'],
                ]);
            }
        });

        return redirect()
            ->route('admin.manufacturing.index')
            ->with('success', __('تم حفظ تركيبة الصنف بنجاح.'));
    }

    public function assemble(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:product_recipes,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'build_quantity' => 'required|numeric|min:0.0001',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $subscriberId = $user->subscriber_id ?? null;

        $recipe = ProductRecipe::with(['items.component', 'product'])
            ->when($subscriberId, fn ($q, $sub) => $q->where('subscriber_id', $sub))
            ->findOrFail($validated['recipe_id']);

        if ($recipe->items->isEmpty()) {
            return redirect()
                ->route('admin.manufacturing.index')
                ->with('error', __('لا توجد مكونات معرفة لهذه التركيبة.'));
        }

        $yieldQuantity = max($recipe->yield_quantity ?? 1, 0.0001);
        $buildQuantity = $validated['build_quantity'];
        $warehouseId = $validated['warehouse_id'];

        $systemController = new SystemController();
        $allowNegative = $systemController->allowSellingWithoutStock();

        $componentMovements = [];
        $assemblyItemsData = [];

        foreach ($recipe->items as $item) {
            $requiredQty = ($item->quantity / $yieldQuantity) * $buildQuantity;

            if (! $allowNegative) {
                $available = WarehouseProducts::query()
                    ->where('warehouse_id', $warehouseId)
                    ->where('product_id', $item->component_product_id)
                    ->value('quantity') ?? 0;

                if ($available < $requiredQty) {
                    $name = $item->component->name ?? ('#' . $item->component_product_id);
                    throw ValidationException::withMessages([
                        'build_quantity' => __('المخزون غير كاف للمكون :name في المستودع المحدد.', ['name' => $name]),
                    ]);
                }
            }

            $component = new Product();
            $component->product_id = $item->component_product_id;
            $component->warehouse_id = $warehouseId;
            $component->quantity = $requiredQty;
            $componentMovements[] = $component;

            $assemblyItemsData[] = [
                'component_product_id' => $item->component_product_id,
                'quantity' => $requiredQty,
            ];
        }

        $finished = new Product();
        $finished->product_id = $recipe->product_id;
        $finished->warehouse_id = $warehouseId;
        $finished->quantity = $buildQuantity;

        DB::transaction(function () use (
            $systemController,
            $componentMovements,
            $finished,
            $validated,
            $recipe,
            $assemblyItemsData,
            $user,
            $subscriberId
        ) {
            $systemController->syncQnt($componentMovements, null, true);
            $systemController->syncQnt([$finished], null, false);

            $assembly = ProductAssembly::create([
                'recipe_id' => $recipe->id,
                'product_id' => $recipe->product_id,
                'quantity' => $validated['build_quantity'],
                'warehouse_id' => $validated['warehouse_id'],
                'notes' => $validated['notes'],
                'subscriber_id' => $subscriberId,
                'created_by' => $user?->id,
            ]);

            foreach ($assemblyItemsData as $itemData) {
                ProductAssemblyItem::create([
                    'assembly_id' => $assembly->id,
                    'component_product_id' => $itemData['component_product_id'],
                    'quantity' => $itemData['quantity'],
                    'subscriber_id' => $subscriberId,
                ]);
            }
        });

        return redirect()
            ->route('admin.manufacturing.index')
            ->with('success', __('تم تنفيذ عملية التجميع بنجاح.'));
    }
}
