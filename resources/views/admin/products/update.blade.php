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
                                                                <div class="input-group">
                                                                    <input type="text" id="barcode" name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ $product->barcode }}">
                                                                    <button class="btn btn-outline-secondary generateMainBarcode" type="button">
                                                                        {{ __('main.generate') ?? 'توليد' }}
                                                                    </button>
                                                                </div>
                                                                @error('barcode')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.brand') }}</label>
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
                                                                <label>{{ __('main.categories') }}</label>
                                                                <select class="js-example-basic-single w-100 @error('category_id') is-invalid @enderror" name="category_node_id" id="category_node_id">
                                                                    <option value="">{{ __('main.choose') }}</option>
                                                                    @foreach($categoryTreeOptions as $option)
                                                                        <option value="{{ $option['id'] }}" data-excise="{{ $option['tax_excise'] ?? 0 }}" @if((string)$selectedCategoryNode === (string)$option['id']) selected @endif>{{ $option['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('category_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.salon_department') ?? 'قسم المشغل' }}</label>
                                                                @php $selectedSalonDepartment = old('salon_department_id', $product->salon_department_id); @endphp
                                                                <select class="js-example-basic-single w-100 @error('salon_department_id') is-invalid @enderror" name="salon_department_id">
                                                                    <option value="">{{ __('main.choose') }}</option>
                                                                    @foreach($salonDepartments as $department)
                                                                        <option value="{{ $department->id }}" @if((string)$selectedSalonDepartment === (string)$department->id) selected @endif>{{ $department->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('salon_department_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.units') }}</label>
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

                                                    <div class="row mt-1">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.enable_batch_tracking') }}</label>
                                                                <select id="track_batch" name="track_batch" class="form-control">
                                                                    <option value="1" @if($product->track_batch) selected @endif>{{ __('main.status1') }}</option>
                                                                    <option value="0" @if(!$product->track_batch) selected @endif>{{ __('main.status2') }}</option>
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
                                                                <label>{{ __('main.profit_type') ?? 'نوع احتساب الربح' }}</label>
                                                                @php
                                                                    $profitType = old('profit_type', $product->profit_type ?? ($product->profit_margin !== null ? 'percent' : 'percent'));
                                                                @endphp
                                                                <select class="form-control" id="profit_type" name="profit_type">
                                                                    <option value="percent" @if($profitType === 'percent') selected @endif>{{ __('main.profit_type_percent') ?? 'نسبة' }}</option>
                                                                    <option value="amount" @if($profitType === 'amount') selected @endif>{{ __('main.profit_type_amount') ?? 'مبلغ ثابت' }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('main.profit_value') ?? 'قيمة الربح' }}</label>
                                                                <input type="number" step="0.01" id="profit_amount" name="profit_amount" class="form-control" value="{{ old('profit_amount', $product->profit_amount ?? $product->profit_margin) }}">
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
                                                                <label>{{ __('main.Product_Tax') }}</label>
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
                                                                <label>{{ __('main.additional_taxes') ?? 'ضرائب إضافية' }}</label>
                                                                @php $selectedMultiTaxes = old('tax_rates_multi', $productTaxes ?? []); @endphp
                                                                <select class="js-example-basic-single w-100 @error('tax_rates_multi') is-invalid @enderror" name="tax_rates_multi[]" multiple>
                                                                    @foreach($taxRages as $tax)
                                                                        <option value="{{ $tax->id }}" @if(in_array($tax->id, $selectedMultiTaxes)) selected @endif>{{ $tax->name ?? $tax->rate }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('tax_rates_multi')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                            </div>
                                                        </div>
                                                        @if($exciseEnabled)
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>{{ __('main.tax_excise') }}</label>
                                                                    <input type="number" step="0.01" id="tax_excise" name="tax_excise" class="form-control @error('tax_excise') is-invalid @enderror" value="{{ $product->tax_excise }}">
                                                                    @error('tax_excise')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>{{ __('main.product_services') ?? 'خدمات الصنف' }}</label>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label class="small text-muted">{{ __('main.shipping_service') ?? 'خدمة الشحن' }}</label>
                                                                        @php $shippingType = old('shipping_service_type', $product->shipping_service_type ?? 'free'); @endphp
                                                                        <select class="form-control" name="shipping_service_type">
                                                                            <option value="paid" @if($shippingType==='paid') selected @endif>{{ __('main.service_paid') ?? 'برسوم' }}</option>
                                                                            <option value="included" @if($shippingType==='included') selected @endif>{{ __('main.service_included') ?? 'ضمن الفاتورة' }}</option>
                                                                            <option value="free" @if($shippingType==='free') selected @endif>{{ __('main.service_free') ?? 'مجانية' }}</option>
                                                                        </select>
                                                                        <input type="number" step="0.01" class="form-control mt-1" name="shipping_service_amount" placeholder="{{ __('main.service_fee') ?? 'قيمة الرسوم' }}" value="{{ old('shipping_service_amount', $product->shipping_service_amount ?? 0) }}">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="small text-muted">{{ __('main.delivery_service') ?? 'خدمة التوصيل' }}</label>
                                                                        @php $deliveryType = old('delivery_service_type', $product->delivery_service_type ?? 'free'); @endphp
                                                                        <select class="form-control" name="delivery_service_type">
                                                                            <option value="paid" @if($deliveryType==='paid') selected @endif>{{ __('main.service_paid') ?? 'برسوم' }}</option>
                                                                            <option value="included" @if($deliveryType==='included') selected @endif>{{ __('main.service_included') ?? 'ضمن الفاتورة' }}</option>
                                                                            <option value="free" @if($deliveryType==='free') selected @endif>{{ __('main.service_free') ?? 'مجانية' }}</option>
                                                                        </select>
                                                                        <input type="number" step="0.01" class="form-control mt-1" name="delivery_service_amount" placeholder="{{ __('main.service_fee') ?? 'قيمة الرسوم' }}" value="{{ old('delivery_service_amount', $product->delivery_service_amount ?? 0) }}">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="small text-muted">{{ __('main.installation_service') ?? 'خدمة التركيب' }}</label>
                                                                        @php $installationType = old('installation_service_type', $product->installation_service_type ?? 'free'); @endphp
                                                                        <select class="form-control" name="installation_service_type">
                                                                            <option value="paid" @if($installationType==='paid') selected @endif>{{ __('main.service_paid') ?? 'برسوم' }}</option>
                                                                            <option value="included" @if($installationType==='included') selected @endif>{{ __('main.service_included') ?? 'ضمن الفاتورة' }}</option>
                                                                            <option value="free" @if($installationType==='free') selected @endif>{{ __('main.service_free') ?? 'مجانية' }}</option>
                                                                        </select>
                                                                        <input type="number" step="0.01" class="form-control mt-1" name="installation_service_amount" placeholder="{{ __('main.service_fee') ?? 'قيمة الرسوم' }}" value="{{ old('installation_service_amount', $product->installation_service_amount ?? 0) }}">
                                                                    </div>
                                                                </div>
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

                                            <div class="card shadow-sm border mb-4">
                                                <div class="card-header bg-white border-bottom">
                                                    <strong>{{ __('main.warehouse_prices') ?? 'أسعار حسب المستودع' }}</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        @foreach($warehouses as $warehouse)
                                                            <div class="col-sm-6 col-md-4 col-lg-3">
                                                                <label class="small text-muted">{{ $warehouse->name }}</label>
                                                                <input type="number" step="0.01" class="form-control" name="warehouse_prices[{{ $warehouse->id }}]" value="{{ old('warehouse_prices.'.$warehouse->id, $warehousePrices[$warehouse->id] ?? $product->price) }}">
                                                            </div>
                                                        @endforeach
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
                                                        @error('product_units')
                                                        <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                                        @enderror
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
@endphp
@section('js')
<script type="text/javascript">
    const unitOptions = @json($units->map(function($unit){ return ['id'=>$unit->id,'name'=>$unit->name]; })->values());
    const barcodeGenerateUrl = "{{ route('products.generate_barcode') }}";

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
        const categorySelect = document.getElementById('category_node_id');
        let exciseTouched = false;

        function autoSetExcise(option) {
            if (!option || exciseTouched) return;
            const value = option.dataset.excise ?? null;
            const exciseInput = document.getElementById('tax_excise');
            if (value !== null && exciseInput) {
                exciseInput.value = value;
            }
        }
        if (categorySelect) {
            autoSetExcise(categorySelect.selectedOptions[0]);
            categorySelect.addEventListener('change', function () {
                exciseTouched = false;
                autoSetExcise(this.selectedOptions[0]);
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
                    <button type="button" class="btn btn-sm btn-outline-secondary generateUnitBarcode">{{ __('main.generate') ?? 'توليد' }}</button>
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
        function requestBarcode(fillTarget){
            $.get(barcodeGenerateUrl, function(response){
                if(response && response.barcode){
                    fillTarget(response.barcode);
                }
            }).fail(function(){
                alert("{{ __('main.error_occured') ?? 'حدث خطأ غير متوقع' }}");
            });
        }
        $(document).on('click','.generateUnitBarcode', function(){
            const $input = $(this).closest('tr').find('input[name*="[barcode]"]');
            requestBarcode(function(code){ $input.val(code); });
        });
        $(document).on('click','.generateVariantBarcode', function(){
            const $input = $(this).closest('tr').find('input[name*="[barcode]"]');
            requestBarcode(function(code){ $input.val(code); });
        });
        $(document).on('click','.generateMainBarcode', function(){
            const $input = $('#barcode');
            requestBarcode(function(code){ $input.val(code); });
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
                <td class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary generateVariantBarcode">{{ __('main.generate') ?? 'توليد' }}</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();">حذف</button>
                </td>`;
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
        const $priceInput = $('#price');
        const $profitType = $('#profit_type');
        const $profitAmount = $('#profit_amount');
        const $exciseInput = $('#tax_excise');
        function resetExciseHelper(){
            $('#exciseCostHelper').addClass('d-none').text('');
        }
        $costInput.data('inclusive-cost', $costInput.val());
        $costInput.on('input', function(){
            $(this).data('inclusive-cost', $(this).val());
            resetExciseHelper();
        });
        function syncPriceFromProfit(){
            const rawAmount = parseFloat($profitAmount.val());
            if (isNaN(rawAmount)) {
                return;
            }
            const costVal = parseFloat($costInput.val()) || 0;
            let nextPrice = costVal;
            if ($profitType.val() === 'percent') {
                nextPrice = costVal + (costVal * (rawAmount / 100));
            } else if ($profitType.val() === 'amount') {
                nextPrice = costVal + rawAmount;
            }
            $priceInput.val(nextPrice.toFixed(4));
        }
        $profitType.on('change', syncPriceFromProfit);
        $profitAmount.on('input', syncPriceFromProfit);
        $costInput.on('input', function(){
            if ($profitAmount.val() !== '') {
                syncPriceFromProfit();
            }
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
