@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    @can('تعديل صنف')
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0" id="head-right">
                            <div class="col-lg-12 margin-tb">
                                <h4 class="alert alert-primary text-center">
                                    [ {{ __('main.products_list'). ' / '. __('main.update_product') }} ]
                                </h4>
                            </div>
                        </div>

                        <div class="modal-body" id="paymentBody">
                            <form method="POST" action="{{ route('updateProduct' , $product -> id) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="px-3 py-3">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            {{-- المعلومات الأساسية --}}
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom">
                                                    <strong>{{ __('main.basic_info') ?? 'المعلومات الأساسية' }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Product_Type') }} <span class="text-danger">*</span></label>
                                                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                                                    <option @if($product -> type == 1) selected @endif value="1">{{__('main.General')}}</option>
                                                                    <option @if($product -> type == 2) selected @endif value="2">{{__('main.Collection')}}</option>
                                                                    <option @if($product -> type == 3) selected @endif value="3">{{__('main.Service')}}</option>
                                                                </select>
                                                                @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.product_name_ar') ?? __('main.name') }} <span class="text-danger">*</span></label>
                                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $product->name }}">
                                                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.product_name_en') }}</label>
                                                                <input type="text" id="name_en" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ $product->name_en }}">
                                                                @error('name_en')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.code') }} <span class="text-danger">*</span></label>
                                                                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ $product->code }}">
                                                                @error('code')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.barcode') }}</label>
                                                                <input type="text" id="barcode" name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ $product->barcode }}">
                                                                @error('barcode')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.brand') }} <span class="text-danger">*</span></label>
                                                                <select class="js-example-basic-single w-100 @error('brand') is-invalid @enderror" name="brand">
                                                                    @foreach($brands as $brand)
                                                                        <option value="{{ $brand->id }}" @if($product->brand == $brand->id) selected @endif>{{ $brand->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('brand')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.categories') }} <span class="text-danger">*</span></label>
                                                                <select class="js-example-basic-single w-100 @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                                                    @foreach($categories as $cat)
                                                                        @if($cat -> isGold == 0)
                                                                            <option value="{{ $cat->id }}" @if($product->category_id == $cat->id) selected @endif>{{ $cat->name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                @error('category_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.subcategory') }}</label>
                                                                <select class="form-control @error('subcategory_id') is-invalid @enderror" name="subcategory_id" id="subcategory_id" data-selected="{{ $product->subcategory_id ?? '' }}">
                                                                    <option value="">{{ __('main.choose') }}</option>
                                                                </select>
                                                                @error('subcategory_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.units') }} <span class="text-danger">*</span></label>
                                                                <select class="js-example-basic-single w-100 @error('unit') is-invalid @enderror" name="unit">
                                                                    @foreach($units as $unit)
                                                                        <option value="{{ $unit->id }}" @if($product->unit == $unit->id) selected @endif>{{ $unit->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('unit')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Track_Quantity') }}</label>
                                                                <select id="track_quantity" name="track_quantity" class="form-control">
                                                                    <option value="1" @if($product->track_quantity == 1) selected @endif>{{ __('main.status1') }}</option>
                                                                    <option value="0" @if($product->track_quantity == 0) selected @endif>{{ __('main.status2') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Alert_Quantity') }}</label>
                                                                <input type="number" step="0.01" id="alert_quantity" name="alert_quantity" class="form-control" value="{{ $product->alert_quantity }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Max_Order') }}</label>
                                                                <input type="number" step="0.01" id="max_order" name="max_order" class="form-control" value="{{ $product->max_order }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Lista') }}</label>
                                                                <input type="number" step="0.01" id="lista" name="lista" class="form-control" value="{{ $product->lista }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.status') }}</label>
                                                                <select id="status" name="status" class="form-control">
                                                                    <option value="1" @if($product->status == 1) selected @endif>{{ __('main.status1') }}</option>
                                                                    <option value="0" @if($product->status == 0) selected @endif>{{ __('main.status2') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="featured" value="{{ $product->featured }}">
                                            <input type="hidden" name="city_tax" value="{{ $product->city_tax }}">
                                            <input type="hidden" name="quantity" value="{{ $product->quantity }}">

                                            {{-- التسعير والضرائب --}}
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom">
                                                    <strong>{{ __('main.pricing_taxes') ?? 'التسعير والضرائب' }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Cost') }} <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" id="cost" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ $product->cost }}">
                                                                @error('cost')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                                <small id="exciseCostHelper" class="form-text text-info d-none"></small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Sale_Price') }} <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ $product->price }}">
                                                                @error('price')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.profit_margin') }} %</label>
                                                                <input type="number" step="0.01" id="profit_margin" name="profit_margin" class="form-control" value="{{ $product->profit_margin }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.price_includes_tax') }}</label>
                                                                <select class="form-control" name="price_includes_tax">
                                                                    <option value="0" @if(!$product->price_includes_tax) selected @endif>{{ __('main.false_val') }}</option>
                                                                    <option value="1" @if($product->price_includes_tax) selected @endif>{{ __('main.true_val') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.Product_Tax') }} <span class="text-danger">*</span></label>
                                                                <select id="tax_rate" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror">
                                                                    @foreach($taxRages as $tax)
                                                                        <option value="{{ $tax->id }}" @if($product->tax_rate == $tax->id) selected @endif>{{ $tax->rate }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" id="tax" name="tax" value="{{ $product->tax }}">
                                                                @error('tax_rate')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.tax_excise') }}</label>
                                                                <input type="number" step="0.01" id="tax_excise" name="tax_excise" class="form-control @error('tax_excise') is-invalid @enderror" value="{{ $product->tax_excise }}">
                                                                @error('tax_excise')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- مستويات السعر --}}
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom">
                                                    <strong>{{ __('main.price_level') }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        @for($i=1;$i<=6;$i++)
                                                            @php $field = 'price_level_'.$i; @endphp
                                                            <div class="col-sm-6 col-md-4 col-lg-2">
                                                                <label class="small text-muted">{{ __('main.price') }} {{$i}}</label>
                                                                <input type="number" step="0.01" class="form-control" name="price_level_{{$i}}" value="{{ $product->$field ?? '' }}">
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- المتغيرات --}}
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                                    <strong>متغيرات المنتج</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantRowBtn">+ {{ __('main.add_new') }}</button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0" id="variantRowsTable">
                                                            <thead>
                                                            <tr>
                                                                <th>SKU</th>
                                                                <th>اللون</th>
                                                                <th>المقاس</th>
                                                                <th>باركود</th>
                                                                <th>{{ __('main.price') }}</th>
                                                                <th>{{ __('main.quantity') }}</th>
                                                                <th>{{ __('main.actions') }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- الوحدات الإضافية --}}
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                                    <strong>الوحدات الإضافية</strong>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addUnitRowBtn">+ {{ __('main.add_new') }}</button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0" id="unitRowsTable">
                                                            <thead>
                                                            <tr>
                                                                <th>{{ __('main.units') }}</th>
                                                                <th>{{ __('main.price') }}</th>
                                                                <th>معامل التحويل</th>
                                                                <th>{{ __('main.barcode') ?? 'باركود' }}</th>
                                                                <th>{{ __('main.actions') }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>{{-- end col-lg-8 --}}

                                        <div class="col-lg-4">
                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom">
                                                    <strong>{{ __('main.img') }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label>{{ __('main.img') }}</label>
                                                        <div class="custom-file mb-3">
                                                            <input type="file" class="custom-file-input" id="img" name="img" accept="image/png, image/jpeg">
                                                            <label class="custom-file-label" for="img" id="path">{{ __('main.img_choose') }}</label>
                                                        </div>
                                                        <span class="text-muted" style="font-size: 9pt;">{{ __('main.img_hint') }}</span>
                                                        <div class="mt-3 text-center">
                                                            @if($product->img)
                                                                <img src="{{ asset('uploads/items/images/'.$product->img) }}" class="img-thumbnail" id="profile-img-tag" width="200">
                                                            @else
                                                                <img src="{{ asset('assets/img/photo.png') }}" class="img-thumbnail" id="profile-img-tag" width="200">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center pb-4">
                                    <button type="submit" class="btn btn-labeled btn-primary">
                                        {{ __('main.save_btn') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@php
    $subCategoryOptions = $subCategories->mapWithKeys(function ($group, $parentId) {
        return [
            $parentId => $group->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'tax_excise' => $cat->tax_excise ?? 0,
                ];
            })->values()
        ];
    });
@endphp
@section('js')
<script type="text/javascript">
    const subCategories = @json($subCategoryOptions);
    const unitOptions = @json($units->map(function($unit){ return ['id'=>$unit->id,'name'=>$unit->name]; })->values());

    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return '';
        }
        return text.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function regenerateUnitOptionsHtml(excludeId = null){
        return unitOptions.map(function(unit){
            if(excludeId && String(unit.id) === String(excludeId)){
                return '';
            }
            return `<option value="${unit.id}">${escapeHtml(unit.name ?? '')}</option>`;
        }).join('');
    }

    let unitOptionsHtml = '';
    $(document).ready(function () {
        document.title = "{{ __('main.update_product')}}";
        const subSelect = document.getElementById('subcategory_id');
        const parentSelect = document.getElementById('category_id');
        let exciseTouched = false;

        function fillSubCategories(parentId) {
            if (!subSelect) return;
            const selected = subSelect.dataset.selected || '';
            subSelect.innerHTML = `<option value="">{{ __('main.choose') }}</option>`;
            (subCategories[parentId] || []).forEach(function (cat) {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                option.dataset.excise = cat.tax_excise ?? 0;
                if (selected && String(cat.id) === String(selected)) {
                    option.selected = true;
                }
                subSelect.appendChild(option);
            });
            if (!(subCategories[parentId] || []).some(cat => String(cat.id) === String(selected))) {
                subSelect.value = '';
            }
        }

        function autoSetExcise(option) {
            if (!option || exciseTouched) return;
            const value = option.dataset.excise ?? null;
            const exciseInput = document.getElementById('tax_excise');
            if (value !== null && exciseInput && !exciseInput.value) {
                exciseInput.value = value;
            }
        }

        if (parentSelect) {
            fillSubCategories(parentSelect.value);
            autoSetExcise(parentSelect.selectedOptions[0]);
            parentSelect.addEventListener('change', function () {
                exciseTouched = false;
                fillSubCategories(this.value);
                if (!subSelect.value) {
                    autoSetExcise(this.selectedOptions[0]);
                }
            });
        }

        if (subSelect) {
            subSelect.addEventListener('change', function () {
                subSelect.dataset.selected = this.value || '';
                if (this.value && this.selectedOptions[0]) {
                    exciseTouched = true;
                    const option = this.selectedOptions[0];
                    if (option.dataset.excise !== undefined && document.getElementById('tax_excise')) {
                        document.getElementById('tax_excise').value = option.dataset.excise;
                    }
                } else if (parentSelect) {
                    exciseTouched = false;
                    autoSetExcise(parentSelect.selectedOptions[0]);
                }
            });
        }

        const baseUnitSelect = $('select[name="unit"]');
        function refreshUnitOptionsHtml(){
            const excludeId = baseUnitSelect.val();
            unitOptionsHtml = regenerateUnitOptionsHtml(excludeId);
            $('#unitRowsTable select').each(function(){
                const previousValue = $(this).val();
                $(this).html(unitOptionsHtml);
                if(previousValue && String(previousValue) !== String(excludeId)){
                    $(this).val(previousValue);
                }
            });
        }
        refreshUnitOptionsHtml();
        baseUnitSelect.on('change', function(){
            refreshUnitOptionsHtml();
        });

        const exciseInput = document.getElementById('tax_excise');
        if (exciseInput) {
            exciseInput.addEventListener('input', function () {
                exciseTouched = true;
            });
        }

        let unitRowIndex = 0;
        function addUnitRow(unitId, price, factor, barcode, canDelete=true){
            const optionsHtml = unitOptionsHtml || regenerateUnitOptionsHtml(baseUnitSelect.val());
            const row = `<tr data-index="${unitRowIndex}">
                <td><select name="product_units[${unitRowIndex}][unit]" class="form-control">${optionsHtml}</select></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][price]" class="form-control" value="${price ?? ''}"></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][conversion_factor]" class="form-control" value="${factor ?? 1}"></td>
                <td><input type="text" name="product_units[${unitRowIndex}][barcode]" class="form-control" value="${barcode ?? ''}"></td>
                <td class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary generateBarcode">{{ __('main.generate') ?? 'توليد' }}</button>
                    ${canDelete ? '<button type="button" class="btn btn-sm btn-danger removeUnitRow">-</button>' : ''}
                </td>`;
            $('#unitRowsTable tbody').append(row);
            const $lastRow = $('#unitRowsTable tbody tr').last();
            if(unitId){ $lastRow.find('select').val(unitId); }
            unitRowIndex++;
        }

        const existingUnits = @json($productUnits ?? []);
        if(existingUnits.length){
            existingUnits.forEach((u,idx)=>{
                addUnitRow(u.unit_id, u.price, u.conversion_factor ?? 1, u.barcode ?? '', idx>0);
            });
        } else {
            addUnitRow({{$product->unit}}, {{$product->price}}, 1, '', false);
        }

        $('#addUnitRowBtn').on('click', function(){ addUnitRow('', '', 1, '', true); });
        $(document).on('click','.removeUnitRow', function(){ $(this).closest('tr').remove(); });
        $(document).on('click','.generateBarcode', function(){
            const code = '9' + Math.floor(100000000000 + Math.random() * 900000000000).toString().slice(0,12);
            $(this).closest('tr').find('input[name*="[barcode]"]').val(code);
        });
        $('#price').on('change', function(){ $('#unitRowsTable tbody tr').first().find('input[name*="[price]"]').val($(this).val()); });

        function addVariantRow(data = {}) {
            const tbody = document.querySelector('#variantRowsTable tbody');
            const row = document.createElement('tr');
            const index = tbody.children.length;
            row.innerHTML = `
                <td><input class="form-control" name="product_variants[${index}][sku]" value="${data.sku ?? ''}"></td>
                <td><input class="form-control" name="product_variants[${index}][color]" value="${data.color ?? ''}"></td>
                <td><input class="form-control" name="product_variants[${index}][size]" value="${data.size ?? ''}"></td>
                <td><input class="form-control" name="product_variants[${index}][barcode]" value="${data.barcode ?? ''}"></td>
                <td><input class="form-control" type="number" step="0.01" name="product_variants[${index}][price]" value="${data.price ?? ''}"></td>
                <td><input class="form-control" type="number" step="0.01" name="product_variants[${index}][quantity]" value="${data.quantity ?? ''}"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();">حذف</button></td>`;
            tbody.appendChild(row);
        }
        $('#addVariantRowBtn').on('click', function(){ addVariantRow(); });
        @if(!empty($productVariants))
            @foreach($productVariants as $variant)
                addVariantRow({ sku: "{{ $variant->sku }}", color: "{{ $variant->color }}", size: "{{ $variant->size }}", barcode: "{{ $variant->barcode }}", price: "{{ $variant->price }}", quantity: "{{ $variant->quantity }}" });
            @endforeach
        @endif

        $('#tax_rate').change(function (){
            $('#tax').val($('#tax_rate option:selected').text());
        }).trigger('change');

        const $costInput = $('#cost');
        const $exciseInput = $('#tax_excise');
        function resetExciseHelper(){
            $('#exciseCostHelper').addClass('d-none').text('');
        }
        $costInput.data('inclusive-cost', $costInput.val());
        $costInput.on('input', function(){
            $(this).data('inclusive-cost', $(this).val());
            resetExciseHelper();
        });
        function adjustCostForExcise(){
            const exciseVal = parseFloat($exciseInput.val());
            const inclusiveCost = parseFloat($costInput.data('inclusive-cost') || $costInput.val());
            if(!(inclusiveCost > 0) || !(exciseVal > 0)){
                resetExciseHelper();
                return;
            }
            const baseCost = inclusiveCost / (1 + (exciseVal/100));
            const exciseAmount = inclusiveCost - baseCost;
            $costInput.val(baseCost.toFixed(4));
            $('#exciseCostHelper').removeClass('d-none').text(
                "{{ __('main.excise_adjusted_hint') }}".replace(':amount', exciseAmount.toFixed(4))
            );
        }
        $exciseInput.on('change blur', function(){
            adjustCostForExcise();
        });
    });
</script>
@endsection
