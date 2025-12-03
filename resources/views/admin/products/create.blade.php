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
                                <div class="col-md-6" >
                                    <div class="form-group">
                                        <label>{{ __('main.name') }}<span class="text-danger">*</span>  </label>
                                        <input type="text"  id="name" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="{{ __('main.name') }}" value="{{ old('name') }}" required />
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
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
                                        <label>{{ __('main.units') }}<span class="text-danger">*</span>  </label>
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
                                        <label>{{ __('main.additional_taxes') }}</label>
                                        @php $selectedTaxes = old('tax_rates_multi', []); @endphp
                                        <select class="js-example-basic-multiple w-100" name="tax_rates_multi[]" multiple>
                                            @foreach($taxRages as $tax)
                                                <option value="{{$tax->id}}" @if(in_array($tax->id,$selectedTaxes)) selected @endif>{{$tax->rate}}</option>
                                            @endforeach
                                        </select>
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
                                        <label>{{ __('main.Product_Tax_Type') }}<span class="text-danger">*</span>  </label>
                                        <select class="form-control @error('tax_method') is-invalid @enderror" name="tax_method">
                                            @foreach($taxTypes as $taxType)
                                                <option value="{{$taxType['id']}}" @if(old('tax_method')==$taxType['id']) selected @endif>{{$taxType['name']}}</option>
                                            @endforeach
                                        </select>
                                        @error('tax_method')
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
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{ __('main.additional_taxes') }}</label>
                                        <select class="js-example-basic-multiple w-100" name="tax_rates_multi[]" multiple>
                                            @foreach($taxRages as $tax)
                                                <option value="{{$tax->id}}" @if(in_array($tax->id,$selectedTaxes)) selected @endif>{{$tax->rate}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Slug') }}  <span class="text-danger">*</span> </label>
                                        <input type="text"  id="slug" name="slug"
                                               class="form-control @error('slug') is-invalid @enderror"
                                               placeholder="{{ __('main.Slug') }}" value="{{ old('slug', $defaultCode ?? '') }}" required />
                                        @error('slug')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('main.img') }}</label>
                                        <div class="row"> 
                                            <div class="col-6">
                                                <div class="custom-file"> 
                                                    <input accept="image/*" type="file"
                                                        oninput="pic.src=window.URL.createObjectURL(this.files[0])" id="img"
                                                        name="img" class="form-control"> 
                                                    <label class="custom-file-label" for="img"
                                                        id="path">{{__('main.img_choose')}} 
                                                    </label>
                                                </div>
                                                <br> 
                                                <span style="font-size: 9pt ; color:gray;">{{ __('main.img_hint') }}</span>
                                            </div>
                                            <div class="col-6 text-right">  
                                                <img id="pic" src=""
                                                    style="width: 100px; height:100px;"/>
                                            </div>
                                        </div>
                                        @error('printer')
                                           <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
    const subPlaceholder = "{{ __('main.choose') }}";
    $(document).ready(function () {
        const subSelect = document.getElementById('subcategory_id');
        const parentSelect = document.getElementById('category_id');
        let exciseTouched = false;

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

        // وحدات متعددة
        const unitOptionsHtml = `@foreach($units as $unit)<option value="{{$unit->id}}">{{$unit->name}}</option>@endforeach`;
        let unitRowIndex = 0;
        function addUnitRow(unitId, price, factor, barcode, canDelete=true){
            const row = `<tr data-index="${unitRowIndex}">
                <td><select name="product_units[${unitRowIndex}][unit]" class="form-control">${unitOptionsHtml}</select></td>
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
        addUnitRow($('#unit_base').val(), $('#price').val(), 1, '', false);
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
        $('#price').on('change', function(){
            const val = $(this).val();
            const firstRow = $('#unitRowsTable tbody tr').first();
            if(firstRow.length){
                firstRow.find('input[name*="[price]"]').val(val);
            }
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
