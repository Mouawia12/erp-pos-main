@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ __('تصنيع وتجميع الأصناف') }}</h4>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تعريف تركيبة صنف</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetRecipeForm">تفريغ النموذج</button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.manufacturing.recipes.store') }}" id="recipeForm">
                            @csrf
                            <input type="hidden" name="recipe_id" id="recipe_id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">الصنف النهائي<span class="text-danger">*</span></label>
                                    <select name="product_id" id="recipe_product_id" class="form-control" required>
                                        <option value="">{{ __('main.choose') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">إسم التركيبة</label>
                                    <input type="text" class="form-control" name="name" id="recipe_name" placeholder="مثال: تركيبة البرجر">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">كمية الناتج</label>
                                    <input type="number" step="0.0001" class="form-control" name="yield_quantity" id="recipe_yield" value="1">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">الملاحظات</label>
                                    <textarea class="form-control" name="notes" id="recipe_notes" rows="2"></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">مكونات التركيبة</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addComponentRow">إضافة مكون</button>
                            </div>
                            <div id="components-wrapper"></div>
                            <template id="component-row-template">
                                <div class="row g-2 align-items-end component-row mb-2">
                                    <div class="col-md-6">
                                        <select class="form-control component-product" name="component_product_id[]" required>
                                            <option value="">{{ __('main.choose') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.0001" class="form-control component-quantity" name="component_quantity[]" placeholder="{{ __('main.quantity') }}" required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-component">&times;</button>
                                    </div>
                                </div>
                            </template>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success">حفظ التركيبة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">تشغيل تصنيع/تجميع</h5>
                    </div>
                    <div class="card-body">
                        @if($recipes->isEmpty())
                            <p class="text-muted mb-0">لا توجد تراكيب معرفة بعد.</p>
                        @else
                            <form method="POST" action="{{ route('admin.manufacturing.assemble') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">التركيبة<span class="text-danger">*</span></label>
                                    <select class="form-control" name="recipe_id" required>
                                        <option value="">{{ __('main.choose') }}</option>
                                        @foreach($recipes as $recipe)
                                            <option value="{{ $recipe->id }}">
                                                {{ $recipe->product->name ?? '---' }} ({{ $recipe->name ?? 'افتراضي' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">المستودع<span class="text-danger">*</span></label>
                                    <select class="form-control" name="warehouse_id" required>
                                        <option value="">{{ __('main.choose') }}</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">الكمية المطلوبة<span class="text-danger">*</span></label>
                                    <input type="number" name="build_quantity" step="0.0001" min="0.0001" class="form-control" required value="1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('main.notes') }}</label>
                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">تشغيل عملية التجميع</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">التراكيب المعرفة</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>الصنف</th>
                                    <th>المكونات</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recipes as $recipe)
                                    <tr>
                                        <td>
                                            <strong>{{ $recipe->product->name ?? '---' }}</strong><br>
                                            <small>إنتاج: {{ number_format($recipe->yield_quantity, 4) }}</small>
                                        </td>
                                        <td>
                                            @foreach($recipe->items as $item)
                                                <div>- {{ $item->component->name ?? '---' }} ({{ number_format($item->quantity,4) }})</div>
                                            @endforeach
                                            @if($recipe->notes)
                                                <div class="text-muted small mt-1">{{ $recipe->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info" onclick="loadRecipe({{ $recipe->id }})">
                                                تعديل
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">لا توجد تراكيب بعد.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">أحدث عمليات التجميع</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الصنف</th>
                                    <th>الكمية</th>
                                    <th>المستودع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAssemblies as $assembly)
                                    <tr>
                                        <td>{{ $assembly->created_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $assembly->product->name ?? '---' }}</td>
                                        <td>{{ number_format($assembly->quantity,4) }}</td>
                                        <td>{{ $assembly->warehouse->name ?? '---' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد عمليات بعد.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    const componentsWrapper = document.getElementById('components-wrapper');
    const componentTemplate = document.getElementById('component-row-template');
    const recipesPayload = @json($recipes->mapWithKeys(function($recipe){
        return [$recipe->id => [
            'id' => $recipe->id,
            'product_id' => $recipe->product_id,
            'name' => $recipe->name,
            'yield_quantity' => $recipe->yield_quantity,
            'notes' => $recipe->notes,
            'items' => $recipe->items->map(fn($item) => [
                'component_product_id' => $item->component_product_id,
                'quantity' => $item->quantity,
            ])->toArray(),
        ]];
    })->toArray());

    function addComponentRow(productId = '', quantity = '') {
        const clone = componentTemplate.content.cloneNode(true);
        componentsWrapper.appendChild(clone);
        const row = componentsWrapper.lastElementChild;
        const select = row.querySelector('select');
        const qtyInput = row.querySelector('input');
        const removeBtn = row.querySelector('.remove-component');

        removeBtn.addEventListener('click', function(){
            row.remove();
        });

        if (productId) {
            select.value = productId;
        }
        if (quantity) {
            qtyInput.value = quantity;
        }
    }

    document.getElementById('addComponentRow').addEventListener('click', function(){
        addComponentRow();
    });

    document.getElementById('resetRecipeForm').addEventListener('click', function(){
        document.getElementById('recipeForm').reset();
        document.getElementById('recipe_id').value = '';
        componentsWrapper.innerHTML = '';
        addComponentRow();
    });

    function loadRecipe(id) {
        const recipe = recipesPayload[id];
        if (!recipe) return;
        document.getElementById('recipe_id').value = recipe.id;
        document.getElementById('recipe_product_id').value = recipe.product_id;
        document.getElementById('recipe_name').value = recipe.name || '';
        document.getElementById('recipe_yield').value = recipe.yield_quantity || 1;
        document.getElementById('recipe_notes').value = recipe.notes || '';
        componentsWrapper.innerHTML = '';
        if (recipe.items && recipe.items.length) {
            recipe.items.forEach(item => addComponentRow(item.component_product_id, item.quantity));
        } else {
            addComponentRow();
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    window.loadRecipe = loadRecipe;

    if (componentsWrapper.children.length === 0) {
        addComponentRow();
    }
</script>
@endsection
