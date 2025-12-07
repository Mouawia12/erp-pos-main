@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('اضافة صنف') 
    @if (session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                [ {{ __('main.add_product')}} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-body itemCaRD" id="paymentBody">
                        <form  method="POST" action="{{ route('storeProduct')}}" enctype="multipart/form-data">
                            @csrf
                
                            <div class="row" style="padding: 20px">  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Product_Type') }}<span class="text-danger">*</span> </label>
                                        @php $defType = old('type', $settings->default_product_type ?? '1'); @endphp
                                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                            <option value="1" @if($defType=='1') selected @endif>{{__('main.General')}}</option>
                                            <option value="2" @if($defType=='2') selected @endif>{{__('main.Collection')}}</option>
                                            <option value="3" @if($defType=='3') selected @endif>{{__('main.Service')}}</option>
                                        </select>
                                        @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.code') }}<span class="text-danger">*</span> </label>
                                        <input type="text"  id="code" name="code"
                                               class="form-control @error('code') is-invalid @enderror"
                                               placeholder="{{ __('main.code') }}" value="{{ old('code', $defaultCode ?? '') }}" required />
                                         @error('code')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                         @enderror
                                     </div>
                                 </div> 
                                <div class="col-md-3">
                                     <div class="form-group">
                                         <label>{{ __('main.barcode') }}</label>
                                         <input type="text"  id="barcode" name="barcode"
                                                class="form-control @error('barcode') is-invalid @enderror"
                                                placeholder="{{ __('main.barcode') }}" value="{{ old('barcode') }}" />
                                         @error('barcode')
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                         </span>
                                         @enderror
                                     </div>
                                 </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('main.product_name_ar') ?? __('main.name') }}<span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="{{ __('main.product_name_ar') ?? __('main.name') }}" value="{{ old('name') }}" required />
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('main.product_name_en') }}</label>
                                        <input type="text" id="name_en" name="name_en"
                                               class="form-control @error('name_en') is-invalid @enderror"
                                               placeholder="{{ __('main.product_name_en') }}" value="{{ old('name_en') }}" />
                                        @error('name_en')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('main.img') }}</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" accept="image/*"
                                                       oninput="pic.src=window.URL.createObjectURL(this.files[0])" id="img"
                                                       name="img" class="form-control"> 
                                                <label class="custom-file-label" for="img"
                                                       id="path">{{__('main.img_choose')}} 
                                                    @if(old('img_name'))
                                                        {{ old('img_name') }}
                                                    @else
                                                        {{__('main.optional')}}
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        <span style="font-size: 9pt ; color:gray;">{{ __('main.img_hint') }}</span>
                                        <div class="mt-2 text-center">
                                            <img id="pic" src="" style="max-width: 140px; max-height: 120px;" class="img-fluid rounded border">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.brand') }}<span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100 @error('brand') is-invalid @enderror" name="brand">
                                            @foreach($brands as $brand)
                                                <option value="{{$brand->id}}" @if(old('brand')==$brand->id) selected @endif>{{$brand->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('brand')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>   
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.categories') }}  <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100 @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                                            <option value="">{{ __('main.choose') }}</option>
                                            @foreach($categories as $cat) 
                                                <option value="{{$cat->id}}" data-excise="{{ $cat->tax_excise ?? 0 }}" @if(old('category_id')==$cat->id) selected @endif>{{$cat->name}}</option> 
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.subcategory') ?? 'التصنيف الفرعي' }}</label>
                                        <select class="form-control @error('subcategory_id') is-invalid @enderror" name="subcategory_id" id="subcategory_id" data-selected="{{ old('subcategory_id') }}">
                                            <option value="">{{ __('main.choose') }}</option>
                                        </select>
                                        @error('subcategory_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="mb-0">{{ __('main.units') }}<span class="text-danger">*</span></label>
                                            @can('اضافة ترميز')
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="openInlineUnitModal">
                                                {{ __('main.add_new') }}
                                            </button>
                                            @endcan
                                        </div>
                                        <select class="js-example-basic-single w-100 @error('unit') is-invalid @enderror"     name="unit" id="unit_base">
                                            @foreach($units as $unit) 
                                                <option value="{{$unit->id}}" @if(old('unit')==$unit->id) selected @endif>{{$unit->name}}</option> 
                                            @endforeach
                                        </select>
                                        @error('unit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-3">  
                                    <div class="form-group">
                                        <label>{{ __('main.Product_Tax') }}  <span class="text-danger">*</span></label>
                                        <select id="tax_rate" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" >
                                            @foreach($taxRages as $tax)
                                                <option value="{{$tax->id}}" @if(old('tax_rate')==$tax->id) selected @endif>{{$tax->rate}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden"  id="tax" name="tax"/>
                                        @error('tax_rate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.tax_excise') }}</label>
                                        <input type="number" step="0.01" id="tax_excise" name="tax_excise" class="form-control @error('tax_excise') is-invalid @enderror" value="{{ old('tax_excise') }}">
                                        @error('tax_excise')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">  
                                    <div class="form-group">
                                        <label>{{ __('main.Cost') }} <span class="text-danger">*</span>  </label>
                                        <input type="number"  id="cost" name="cost"
                                               class="form-control @error('cost') is-invalid @enderror" step="0.01"
                                               placeholder="{{ __('main.Cost') }}" value="{{ old('cost') }}" required  />
                                        @error('cost')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        <small id="exciseCostHelper" class="form-text text-info d-none"></small>
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Sale_Price') }} <span class="text-danger">*</span>  </label>
                                        <input type="number"  id="price" name="price"
                                               class="form-control @error('price') is-invalid @enderror" step="0.01"
                                               placeholder="{{ __('main.Sale_Price') }}" value="{{ old('price') }}" required  />
                                        @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-3">  
                                    <div class="form-group">
                                        <label>{{ __('main.profit_margin') }} %</label>
                                        <input type="number"  id="profit_margin" name="profit_margin"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.profit_margin') }}" value="{{ old('profit_margin') }}"  />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.price_includes_tax') }}</label>
                                        @php $priceIncludes = old('price_includes_tax', '0'); @endphp
                                        <select class="form-control" name="price_includes_tax">
                                            <option value="0" @if($priceIncludes==='0') selected @endif>{{ __('main.false_val') }}</option>
                                            <option value="1" @if($priceIncludes==='1') selected @endif>{{ __('main.true_val') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{ __('main.price_level') }} (1-6)</label>
                                        <div class="row">
                                            @for($i=1;$i<=6;$i++)
                                                <div class="col-md-2 mb-2">
                                                    <input type="number" step="0.01" class="form-control" name="price_level_{{$i}}" placeholder="Level {{$i}}" value="{{ old('price_level_'.$i) }}">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>متغيرات (لون/مقاس/باركود)</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantRowBtn">+ إضافة متغير</button>
                                        </div>
                                        <table class="table table-bordered mt-2" id="variantRowsTable">
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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Lista') }}  </label>
                                        <input type="number"  id="lista" name="lista"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Lista') }}" value="{{ old('lista') }}"  />
                                    </div>
                                </div> 

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Max_Order') }}</label>
                                        <input type="number"  id="max_order" name="max_order"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Max_Order') }}" value="{{ old('max_order') }}"  />
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Track_Quantity') }}</label>
                                        @php $trackQuantity = old('track_quantity', '1'); @endphp
                                        <select id="track_quantity" name="track_quantity"
                                               class="form-control" >
                                            <option value="1" @if($trackQuantity=='1') selected @endif>{{__('main.status1')}}</option>
                                            <option value="0" @if($trackQuantity=='0') selected @endif>{{__('main.status2')}}</option>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Alert_Quantity') }}</label>
                                        <input type="number"  id="alert_quantity" name="alert_quantity"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Alert_Quantity') }}" value="{{ old('alert_quantity') }}"  />
                                    </div>
                                </div> 
                            </div> 
                
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-labeled btn-primary"  >
                                        {{__('main.save_btn')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>الوحدات الإضافية</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="addUnitRowBtn">+ إضافة وحدة</button>
                                        </div>
                                        <table class="table table-bordered mt-2" id="unitRowsTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('main.units') }}</th>
                                                    <th>{{ __('main.price') }}</th>
                                                    <th>معامل التحويل</th>
                                                    <th>{{ __('main.barcode') ?? 'باركود' }}</th>
                                                    <th>{{ __('main.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> 
                            @can('اضافة ترميز')
                            <!-- Inline Unit Modal -->
                            <div class="modal fade" id="inlineUnitModal" tabindex="-1" aria-labelledby="inlineUnitModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="inlineUnitModalLabel">{{ __('main.units') }}</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger d-none" id="inlineUnitErrors"></div>
                                            <form id="inlineUnitForm" method="POST" action="{{ route('storeUnit') }}">
                                                @csrf
                                                <input type="hidden" name="id" value="0">
                                                <div class="form-group">
                                                    <label>{{ __('main.code') }}</label>
                                                    <input type="text" name="code" class="form-control" placeholder="{{ __('main.code') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('main.unit_name_ar') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name_ar" class="form-control" placeholder="{{ __('main.unit_name_ar') }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('main.unit_name_en') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name_en" class="form-control" placeholder="{{ __('main.unit_name_en') }}" required>
                                                </div>
                                                <div class="text-center mt-3">
                                                    <button type="submit" class="btn btn-primary">{{ __('main.save_btn') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
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
    $unitOptionsData = $units->map(function ($unit) {
        return [
            'id' => $unit->id,
            'name' => $unit->name,
        ];
    })->values();
@endphp
@section('js')
<script type="text/javascript">
    const subCategories = @json($subCategoryOptions);
    const subPlaceholder = "{{ __('main.choose') }}";
    let unitOptions = @json($unitOptionsData);

    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return '';
        }
        return text
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function regenerateUnitOptionsHtml(excludeId = null) {
        return unitOptions.map(function (unit) {
            if (excludeId && String(unit.id) === String(excludeId)) {
                return '';
            }
            return `<option value="${unit.id}">${escapeHtml(unit.name ?? '')}</option>`;
        }).join('');
    }

    function refreshUnitSelects(selectedId = null) {
        const $base = $('#unit_base');
        const currentBaseValue = selectedId ?? $base.val();
        const baseHtml = regenerateUnitOptionsHtml();
        $base.html(baseHtml);
        if (currentBaseValue) {
            $base.val(currentBaseValue);
        }

        unitOptionsHtml = regenerateUnitOptionsHtml($base.val());

        $('#unitRowsTable select').each(function () {
            const previousValue = $(this).val();
            $(this).html(unitOptionsHtml);
            if (previousValue && String(previousValue) !== String($base.val())) {
                $(this).val(previousValue);
            }
        });
    }

    let unitOptionsHtml = '';
    $(document).ready(function () {
        const subSelect = document.getElementById('subcategory_id');
        const parentSelect = document.getElementById('category_id');
        let exciseTouched = false;

        refreshUnitSelects();
        unitOptionsHtml = regenerateUnitOptionsHtml($('#unit_base').val());
        $('#unit_base').on('change', function(){
            refreshUnitSelects($(this).val());
        });

        function fillSubCategories(parentId) {
            if (!subSelect) return;
            const selected = subSelect.dataset.selected || '';
            subSelect.innerHTML = `<option value="">${subPlaceholder}</option>`;
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

        const exciseInput = document.getElementById('tax_excise');
        if (exciseInput) {
            exciseInput.addEventListener('input', function () {
                exciseTouched = true;
            });
        }

        // إضافة وحدة جديدة من نفس الشاشة
        if ($('#inlineUnitModal').length) {
            $('#openInlineUnitModal').on('click', function () {
                const $form = $('#inlineUnitForm');
                $('#inlineUnitErrors').addClass('d-none').empty();
                if ($form.length) {
                    $form.trigger('reset');
                    $form.find('input[name="id"]').val(0);
                }
                $('#inlineUnitModal').modal('show');
            });

            $('#inlineUnitModal').on('hidden.bs.modal', function () {
                $('#inlineUnitErrors').addClass('d-none').empty();
                const $form = $('#inlineUnitForm');
                if ($form.length) {
                    $form.trigger('reset');
                }
            });

            $('#inlineUnitForm').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                const $submit = $form.find('button[type="submit"]');
                const $errors = $('#inlineUnitErrors');
                $errors.addClass('d-none').empty();
                $submit.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'Accept': 'application/json',
                    },
                    success: function (response) {
                        if (response && response.unit) {
                            const newUnit = {
                                id: response.unit.id,
                                name: response.unit.name,
                            };
                            const existingIndex = unitOptions.findIndex(function (unit) {
                                return Number(unit.id) === Number(newUnit.id);
                            });
                            if (existingIndex === -1) {
                                unitOptions.push(newUnit);
                            } else {
                                unitOptions[existingIndex] = newUnit;
                            }
                            refreshUnitSelects(newUnit.id);
                        }
                        $('#inlineUnitModal').modal('hide');
                    },
                    error: function (xhr) {
                        let messages = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            messages += '<ul class="mb-0">';
                            Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                                xhr.responseJSON.errors[key].forEach(function (msg) {
                                    messages += `<li>${escapeHtml(msg)}</li>`;
                                });
                            });
                            messages += '</ul>';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            messages = `<p class="mb-0">${escapeHtml(xhr.responseJSON.message)}</p>`;
                        } else {
                            messages = '<p class="mb-0">حدث خطأ غير متوقع</p>';
                        }
                        $errors.html(messages).removeClass('d-none');
                    },
                    complete: function () {
                        $submit.prop('disabled', false);
                    }
                });
            });
        }

        // وحدات متعددة
        let unitRowIndex = 0;
        function addUnitRow(unitId, price, factor, barcode, canDelete=true){
            const optionsHtml = unitOptionsHtml || regenerateUnitOptionsHtml($('#unit_base').val());
            const row = `<tr data-index="${unitRowIndex}">
                <td><select name="product_units[${unitRowIndex}][unit]" class="form-control">${optionsHtml}</select></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][price]" class="form-control" value="${price ?? ''}"></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][conversion_factor]" class="form-control" value="${factor ?? 1}"></td>
                <td><input type="text" name="product_units[${unitRowIndex}][barcode]" class="form-control" value="${barcode ?? ''}"></td>
                <td class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary generateBarcode">{{ __('main.generate') ?? 'توليد' }}</button>
                    ${canDelete ? '<button type="button" class="btn btn-sm btn-danger removeUnitRow">-</button>' : ''}
                </td>
            </tr>`;
            $('#unitRowsTable tbody').append(row);
            const $lastRow = $('#unitRowsTable tbody tr').last();
            if(unitId){ $lastRow.find('select').val(unitId); }
            unitRowIndex++;
        }
        $('#addUnitRowBtn').on('click', function(){
            addUnitRow('', '', 1, '', true);
        });
        $(document).on('click','.removeUnitRow', function(){
            $(this).closest('tr').remove();
        });
        $(document).on('click','.generateBarcode', function(){
            const code = '9' + Math.floor(100000000000 + Math.random() * 900000000000).toString().slice(0,12);
            $(this).closest('tr').find('input[name*="[barcode]"]').val(code);
        });

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

        document.title = "{{ __('main.add_product')}}";
        
        $('#tax_rate').change(function (){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        });
        if($('#tax_rate').prop('selectedIndex') == 0){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        }

        $('#addVariantRowBtn').on('click', function(){
            addVariantRow();
        });
    }); 

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
            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();">حذف</button></td>
        `;
        tbody.appendChild(row);
    }
</script>
@endsection 
