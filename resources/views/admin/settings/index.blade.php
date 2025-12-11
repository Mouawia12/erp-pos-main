@extends('admin.layouts.master')
@section('css')
<style>
    .nav-tabs .nav-link.active {
        color: #fff !important;
        background: #0162e8 !important;
    }
</style>
@endsection 
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('تعديل الاعدادات')  
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0" id="head-right">
                        <div class="row">
                            <div class="col-lg-12 margin-tb">
                                <h4  class="alert alert-primary text-center">
                                [ {{ __('main.system_settings')}} ]
                                </h4>
                            </div> 
                        </div> 
                        <div class="clearfix"></div>
                    </div>

                    <div class="card-body text-right">
                        <form method="POST" action="{{ $setting ? route('updateSettings') : route('storeSettings') }}"
                              enctype="multipart/form-data" id="myform">
                            @csrf
                            @if($setting)
                                @method('PUT')
                            @endif
                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a class="nav-link active" aria-controls="tab_site_settings" href="#tab_site_settings" data-toggle="tab">{{__('main.site_settings')}}</a></li>
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_items_settings" href="#tab_items_settings" data-toggle="tab">{{__('main.items_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_sales_settings" href="#tab_sales_settings" data-toggle="tab">{{__('main.sales_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_prefix_settings" href="#tab_prefix_settings" data-toggle="tab">{{__('main.prefix_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_number_settings" href="#tab_number_settings" data-toggle="tab">{{__('main.number_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_barcode_settings" href="#tab_barcode_settings" data-toggle="tab">{{__('main.barcode_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_email_settings" href="#tab_email_settings" data-toggle="tab">{{__('main.email_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_points_settings" href="#tab_points_settings" data-toggle="tab"> {{__('main.points_settings')}}</a></li> 
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_tobacco_settings" href="#tab_tobacco_settings" data-toggle="tab">{{__('main.tobacco_settings')}}</a></li>  
                                <li class="nav-item"><a class="nav-link" aria-controls="tab_zatca_settings" href="#tab_zatca_settings" data-toggle="tab">{{ __('main.zatca_settings') }}</a></li>
                            </ul>
        
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_site_settings">
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2 class="alert alert-info text-center">{{__('main.site_settings')}} </h2>
                                        </div> 
                                        <div class="box-content text-right"> 
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.company') }} 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" id="company_name" name="company_name"
                                                               class="form-control"
                                                               placeholder="{{ __('main.company') }}" value="{{$setting?  $setting-> company_name : ''}}"/>
                                                        <input type="text" id="id" name="id"
                                                               class="form-control" value="{{$setting?  $setting-> id : 0}}"
                                                               placeholder="{{ __('main.code') }}" hidden=""/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.default_email') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="email" name="email"
                                                               class="form-control" value="{{$setting?  $setting-> email : ''}}"
                                                               placeholder="{{ __('main.default_email') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.default_currency') }} 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="js-example-basic-single w-100"
                                                                name="currency_id" id="currency_id">
                                                            <option @if(!$setting) selected @endif value="0">Choose...</option>
                                                            @foreach ($currencies as $item)
                                                                <option @if($setting?  $setting-> currency_id == $item -> id : false) selected @endif
                                                                    value="{{$item -> id}}"> {{ $item -> name}}
                                                                </option> 
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.default_client_group')}} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="js-example-basic-single w-100"
                                                                name="client_group_id" id="client_group_id">
                                                            <option @if(!$setting) selected @endif value="0">Choose...</option>
                                                            @foreach ($groups as $item)
                                                                <option @if($setting?  $setting-> client_group_id == $item -> id : false) selected @endif
                                                                    value="{{$item -> id}}" > 
                                                                    {{ $item -> name}}
                                                                </option> 
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.nom_of_days_to_edit_bill') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="nom_of_days_to_edit_bill"
                                                               name="nom_of_days_to_edit_bill"
                                                               class="form-control" value="{{$setting? $setting -> nom_of_days_to_edit_bill : '' }}"
                                                               placeholder="{{ __('main.nom_of_days_to_edit_bill') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.default_branch') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="js-example-basic-single w-100"
                                                                name="branch_id" id="branch_id">
                                                            <option @if(!$setting) selected @endif value="0">Choose...</option>
                                                            @foreach ($branches as $item)
                                                                <option @if($setting?  $setting-> branch_id == $item -> id : false) selected @endif
                                                                    value="{{$item -> id}}"> 
                                                                    {{ $item -> name}}
                                                                </option> 
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.default_cashier') }} 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="js-example-basic-single w-100"
                                                            name="cashier_id" id="cashier_id">
                                                            <option @if(!$setting) selected @endif value="0">Choose...</option>
                                                            @foreach ($cashiers as $item)
                                                                <option @if($setting?  $setting-> cashier_id == $item -> id : false) selected @endif
                                                                    value="{{$item -> id}}"> {{ $item -> name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                                <div class="tab-pane" id="tab_items_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.items_settings')}}
                                            </h2>
                                        </div>  
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.item_tax') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control" name="item_tax" id="item_tax">
                                                            <option  @if(!$setting ? false : $setting-> item_tax == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> item_tax == 1 : false) selected @endif value="1">
                                                                {{__('main.disable')}}
                                                            </option>
                                                            <option  @if($setting?  $setting-> item_tax == 2 : false) selected @endif value="2">
                                                                {{__('main.enable')}}
                                                            </option> 
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.item_expired') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control" name="item_expired" id="item_expired">
                                                            <option @if(!$setting ? false : $setting-> item_expired == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> item_expired == 1 : false) selected @endif  value="1">{{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> item_expired == 2 : false) selected @endif  value="2">{{__('main.enable')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.img_size') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <input type="number" id="img_width" name="img_width"
                                                                       class="form-control"
                                                                       placeholder="800" value="{{$setting? $setting -> img_width : ''}}"/>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="number" id="img_height" name="img_height"
                                                                       class="form-control"
                                                                       placeholder="800" value="{{$setting? $setting -> img_height : ''}}"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.barcode_break') }} 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="barcode_break" id="barcode_break">
                                                            <option @if(!$setting ? false : $setting-> barcode_break == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> barcode_break == 1 : false) selected @endif  value="1">{{__('main.barcode_break0')}}</option>
                                                            <option @if($setting?  $setting-> barcode_break == 2 : false) selected @endif  value="2">{{__('main.barcode_break1')}}</option>
                                                            <option @if($setting?  $setting-> barcode_break == 3 : false) selected @endif  value="3">{{__('main.barcode_break2')}}</option>
                                                            <option @if($setting?  $setting-> barcode_break == 4 : false) selected @endif  value="4">{{__('main.barcode_break3')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.small_img_size') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <input type="number" id="small_img_width" name="small_img_width"
                                                                       class="form-control"
                                                                       placeholder="150" value="{{$setting? $setting -> small_img_width : ''}}"/>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="number" id="small_img_height" name="small_img_height"
                                                                       class="form-control"
                                                                       placeholder="150" value="{{$setting? $setting -> small_img_height : ''}}"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>

								</div>
                                <div class="tab-pane" id="tab_sales_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2 class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.sales_settings')}}
                                            </h2>
                                        </div>  
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.sell_without_stock') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control" name="sell_without_stock" id="sell_without_stock">
                                                            <option @if(!$setting ? false : $setting-> sell_without_stock == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> sell_without_stock == 1 : false) selected @endif value="1">{{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> sell_without_stock == 2 : false) selected @endif value="2">{{__('main.enable')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.customize_refNumber') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control" name="customize_refNumber" id="customize_refNumber">
                                                            <option  @if(!$setting ? false : $setting-> customize_refNumber == 0) selected @endif value="0">Choose...</option>
                                                            <option  @if($setting?  $setting-> customize_refNumber == 1 : false) selected @endif  value="1">{{__('main.customize_refNumber0')}}</option>
                                                            <option  @if($setting?  $setting-> customize_refNumber == 2 : false) selected @endif  value="2">{{__('main.customize_refNumber1')}}</option>
                                                            <option  @if($setting?  $setting-> customize_refNumber == 3 : false) selected @endif  value="3">{{__('main.customize_refNumber2')}}</option>
                                                            <option  @if($setting?  $setting-> customize_refNumber == 4 : false) selected @endif  value="4">{{__('main.customize_refNumber3')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ __('main.item_serial') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control" name="item_serial" id="item_serial">
                                                            <option @if(!$setting ? false : $setting-> item_serial == 0 ) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> item_serial == 1 : false) selected @endif  value="1">{{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> item_serial == 2 : false) selected @endif  value="2">{{__('main.enable')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.adding_item_method') }} <span
                                                                class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="adding_item_method" id="adding_item_method">
                                                            <option @if(!$setting ? false : $setting-> adding_item_method == 0) selected @endif   value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> adding_item_method == 1 : false) selected @endif value="1">{{__('main.adding_item_method0')}}</option>
                                                            <option @if($setting?  $setting-> adding_item_method == 2 : false) selected @endif value="2">{{__('main.adding_item_method1')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
        
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.payment_method') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="payment_method" id="payment_method">
                                                            <option @if(!$setting ? false :  $setting-> payment_method == 0 ) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> payment_method == 1 : false) selected @endif value="1">{{__('main.payment_method0')}}</option>
                                                            <option @if($setting?  $setting-> payment_method == 2 : false) selected @endif value="2">{{__('main.payment_method1')}}</option>
                                                            <option @if($setting?  $setting-> payment_method == 3 : false) selected @endif value="3">{{__('main.payment_method2')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.single_device_login') }} </label>
                                                        <select class="form-control" name="single_device_login" id="single_device_login">
                                                            <option value="0" @if(!$setting ? true : $setting->single_device_login == 0) selected @endif>{{__('main.disable')}}</option>
                                                            <option value="1" @if($setting? $setting->single_device_login == 1 : false) selected @endif>{{__('main.enable')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.invoice_type') }}</label>
                                                        <select class="form-control" name="default_invoice_type" id="default_invoice_type">
                                                            <option value="tax_invoice" @if($setting? $setting->default_invoice_type == 'tax_invoice' : true) selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                                            <option value="simplified_tax_invoice" @if($setting? $setting->default_invoice_type == 'simplified_tax_invoice' : false) selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                                            <option value="non_tax_invoice" @if($setting? $setting->default_invoice_type == 'non_tax_invoice' : false) selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.default_product_type') ?? 'نوع الصنف الافتراضي' }}</label>
                                                        <select class="form-control" name="default_product_type" id="default_product_type">
                                                            @php $defProdType = $setting->default_product_type ?? '1'; @endphp
                                                            <option value="1" @if($defProdType=='1') selected @endif>{{ __('main.General') }}</option>
                                                            <option value="2" @if($defProdType=='2') selected @endif>{{ __('main.Collection') }}</option>
                                                            <option value="3" @if($defProdType=='3') selected @endif>{{ __('main.Service') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.tax_number') }}</label>
                                                        <input type="text" class="form-control" name="tax_number" id="tax_number" value="{{ $setting->tax_number ?? ''}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('main.invoice_terms') }}</label>
                                                        <div class="d-flex gap-2 mb-2">
                                                            <select class="form-control" id="invoice_terms_template_selector">
                                                                <option value="">{{ __('main.choose') }}</option>
                                                                @foreach(\App\Models\InvoiceTermTemplate::all() as $tpl)
                                                                    <option value="{{ $tpl->content }}">{{ $tpl->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <a href="{{ route('admin.invoice_terms.index') }}" class="btn btn-outline-secondary">
                                                                {{ __('main.manage') }}
                                                            </a>
                                                        </div>
                                                        <textarea name="invoice_terms" id="invoice_terms" class="form-control" rows="3" placeholder="{{__('main.invoice_terms')}}">{{ $setting->invoice_terms ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>

								</div>
                                <div class="tab-pane" id="tab_prefix_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.prefix_settings')}}
                                            </h2>
                                        </div> 
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.sales_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="sales_prefix" name="sales_prefix"
                                                               class="form-control" value="{{$setting? $setting -> sales_prefix : ''}}"
                                                               placeholder="{{ __('main.sales_prefix') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.sales_return_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="sales_return_prefix" name="sales_return_prefix"
                                                               class="form-control" value="{{$setting? $setting -> sales_return_prefix : ''}}"
                                                               placeholder="{{ __('main.sales_return_prefix') }}" />
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.payment_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="payment_prefix" name="payment_prefix"
                                                               class="form-control" value="{{$setting? $setting -> payment_prefix : ''}}"
                                                               placeholder="{{ __('main.payment_prefix') }}"/>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.purchase_payment_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="purchase_payment_prefix" name="purchase_payment_prefix"
                                                               class="form-control" value="{{$setting? $setting -> purchase_payment_prefix : ''}}"
                                                               placeholder="{{ __('main.purchase_payment_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.deliver_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="deliver_prefix" name="deliver_prefix"
                                                               class="form-control" value="{{$setting? $setting -> deliver_prefix : ''}}"
                                                               placeholder="{{ __('main.deliver_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.purchase_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="purchase_prefix" name="purchase_prefix"
                                                               class="form-control" value="{{$setting? $setting -> purchase_prefix : ''}}"
                                                               placeholder="{{ __('main.purchase_prefix') }}"/>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.purchase_return_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="purchase_return_prefix" name="purchase_return_prefix"
                                                               class="form-control" value="{{$setting? $setting -> purchase_return_prefix : ''}}"
                                                               placeholder="{{ __('main.purchase_return_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.transaction_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="transaction_prefix" name="transaction_prefix"
                                                               class="form-control" value="{{$setting? $setting -> transaction_prefix : ''}}"
                                                               placeholder="{{ __('main.transaction_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.expenses_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="expenses_prefix" name="expenses_prefix"
                                                               class="form-control" value="{{$setting? $setting -> expenses_prefix : ''}}"
                                                               placeholder="{{ __('main.expenses_prefix') }}"/>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.store_prefix') }}
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="store_prefix" name="store_prefix"
                                                               class="form-control" value="{{$setting? $setting -> store_prefix : ''}}"
                                                               placeholder="{{ __('main.store_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.quotation_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="quotation_prefix" name="quotation_prefix"
                                                               class="form-control" value="{{$setting? $setting -> quotation_prefix : ''}}"
                                                               placeholder="{{ __('main.quotation_prefix') }}"/>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.update_qnt_prefix') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="update_qnt_prefix" name="update_qnt_prefix"
                                                               class="form-control" value="{{$setting? $setting -> update_qnt_prefix : ''}}"
                                                               placeholder="{{ __('main.update_qnt_prefix') }}"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
								</div>
                                <div class="tab-pane" id="tab_number_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.number_settings')}}
                                            </h2>
                                        </div>  
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.fraction_number') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="fraction_number" id="fraction_number">
                                                            <option @if(!$setting ? false : $setting-> fraction_number == 0) selected @endif value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> fraction_number == 1 : false) selected @endif  value="1"> {{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> fraction_number == 2 : false) selected @endif  value="2"> 1</option>
                                                            <option @if($setting?  $setting-> fraction_number == 3 : false) selected @endif  value="3"> 2</option>
                                                            <option @if($setting?  $setting-> fraction_number == 4 : false) selected @endif  value="4"> 3</option>
                                                            <option @if($setting?  $setting-> fraction_number == 5 : false) selected @endif  value="5"> 4</option>
                                                            <option @if($setting?  $setting-> fraction_number == 6 : false) selected @endif  value="6"> 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.qnt_decimal_point') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="qnt_decimal_point" id="qnt_decimal_point">
                                                            <option @if(!$setting ? false : $setting-> qnt_decimal_point == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 1 : false) selected @endif  value="1"> {{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 2 : false) selected @endif  value="2"> 1</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 3 : false) selected @endif  value="3"> 2</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 4 : false) selected @endif  value="4"> 3</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 5 : false) selected @endif  value="5"> 4</option>
                                                            <option @if($setting?  $setting-> qnt_decimal_point == 6 : false) selected @endif  value="6"> 5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.decimal_type') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="decimal_type" id="decimal_type">
                                                            <option @if(!$setting ? false : $setting-> decimal_type == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> decimal_type == 1 : false) selected @endif value="1"> {{__('main.decimal_type0')}}</option>
                                                            <option @if($setting?  $setting-> decimal_type == 2 : false) selected @endif value="2"> {{__('main.decimal_type1')}}</option>
        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.thousand_type') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="thousand_type" id="thousand_type">
                                                            <option @if(!$setting ? false :  $setting-> thousand_type == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> thousand_type == 1 : false) selected @endif value="1">{{ __('main.decimal_type0') }} </option>
                                                            <option @if($setting?  $setting-> thousand_type == 2 : false) selected @endif value="2">{{ __('main.decimal_type1') }}</option>
                                                            <option @if($setting?  $setting-> thousand_type == 3 : false) selected @endif value="3">{{ __('main.decimal_type2') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.show_currency') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="show_currency" id="show_currency">
                                                            <option @if(!$setting ? false : $setting-> show_currency == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> show_currency == 1 : false) selected @endif value="1">{{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> show_currency == 2 : false) selected @endif value="2">{{__('main.enable')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.currency_label') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="currency_label" name="currency_label"
                                                               class="form-control" value="{{$setting? $setting -> currency_label : ''}}"
                                                               placeholder="{{ __('main.currency_label') }}"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.per_user_sequence') }}</label>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="per_user_sequence" name="per_user_sequence"
                                                                   value="1" @if($setting? $setting->per_user_sequence : false) checked @endif>
                                                            <label class="form-check-label" for="per_user_sequence">
                                                                {{ __('main.per_user_sequence_hint') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.a4_decimal_point') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="a4_decimal_point" id="a4_decimal_point">
                                                            <option @if(!$setting ? false : $setting-> a4_decimal_point == 0) selected @endif  value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 1 : false) selected @endif value="1"> {{__('main.disable')}}</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 2 : false) selected @endif value="2"> 1</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 3 : false) selected @endif value="3"> 2</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 4 : false) selected @endif value="4"> 3</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 5 : false) selected @endif value="5"> 4</option>
                                                            <option @if($setting?  $setting-> a4_decimal_point == 6 : false) selected @endif value="6"> 5</option>
                                                        </select>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div> 
                                    </div> 
								</div>
                                <div class="tab-pane" id="tab_barcode_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.barcode_settings')}}
                                            </h2>
                                        </div>  
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.barcode_type') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="barcode_type" id="barcode_type">
                                                            <option @if(!$setting ? false :$setting-> barcode_type == 0) selected @endif value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> barcode_type == 1 : false) selected @endif value="1"> {{__('main.barcode_type0')}}</option>
                                                            <option @if($setting?  $setting-> barcode_type == 2 : false) selected @endif value="2"> {{__('main.barcode_type1')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.barcode_length') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="barcode_length" name="barcode_length"
                                                               class="form-control" value="{{$setting? $setting-> barcode_length : '' }}"
                                                               placeholder="16"/>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.flag_character') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="text" id="flag_character" name="flag_character"
                                                               class="form-control" value="{{$setting? $setting-> flag_character : '' }}"
                                                               placeholder="{{ __('main.flag_character') }}"/>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.barcode_start') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="barcode_start" name="barcode_start"
                                                               class="form-control" value="{{$setting? $setting-> barcode_start : '' }}"
                                                               placeholder="0"/>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.code_length') }}
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="code_length" name="code_length"
                                                               class="form-control"  value="{{$setting? $setting-> code_length : '' }}"
                                                               placeholder="5"/>
                                                    </div>
                                                </div> 
                                            </div>
        
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.weight_start') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="weight_start" name="weight_start"
                                                               class="form-control" value="{{$setting? $setting-> weight_start : '' }}"
                                                               placeholder="0"/>
                                                    </div>
                                                </div>
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.weight_length') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="weight_length" name="weight_length"
                                                               class="form-control" value="{{$setting? $setting-> weight_length : '' }}"
                                                               placeholder="5"/>
                                                    </div>
                                                </div>
        
                                                <div class="col-4 ">
                                                    <div class="form-group">
                                                        <label>{{ __('main.weight_divider') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="weight_divider" name="weight_divider"
                                                               class="form-control" value="{{$setting? $setting-> weight_divider : '' }}"
                                                               placeholder="5"/>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div> 
                                    </div> 
								</div>
                                <div class="tab-pane" id="tab_email_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.email_settings')}}
                                            </h2>
                                        </div> 
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.email_protocol') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <select class="form-control"
                                                                name="email_protocol" id="email_protocol">
                                                            <option @if(!$setting ? false : $setting-> email_protocol == 0) selected @endif value="0">Choose...</option>
                                                            <option @if($setting?  $setting-> email_protocol == 1 : false) selected @endif value="1"> {{__('main.email_protocol0')}}</option>
                                                            <option @if($setting?  $setting-> email_protocol == 2 : false) selected @endif value="2"> {{__('main.email_protocol1')}}</option>
                                                            <option @if($setting?  $setting-> email_protocol == 3 : false) selected @endif value="3"> {{__('main.email_protocol2')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div id="smtp_config">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_host') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="text" id="email_host" name="email_host"
                                                                   class="form-control" value="{{$setting? $setting -> email_host : ''}}"
                                                                   placeholder="example.com"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 ">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_user') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="text" id="email_user" name="email_user"
                                                                   class="form-control" value="{{$setting? $setting -> email_user : ''}}"
                                                                   placeholder="{{__('main.email_user')}}}"/>
                                                        </div>
                                                    </div>
        
                                                    <div class="col-4 ">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_password') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="password" id="email_password" name="email_password"
                                                                   class="form-control" value="{{$setting? $setting -> email_password : ''}}"
                                                                   placeholder="{{__('main.email_password')}}"/>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_port') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="text" id="email_port" name="email_port"
                                                                   class="form-control" value="{{$setting? $setting -> email_port : ''}}"
                                                                   placeholder="465"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 ">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_encrypt') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <select class="form-control"
                                                                    name="email_encrypt" id="email_encrypt">
                                                                <option @if(!$setting ? false :$setting-> email_encrypt == 0) selected @endif value="0">Choose...</option>
                                                                <option @if($setting?  $setting-> email_encrypt == 1 : false) selected @endif value="1"> {{__('main.email_encrypt0')}}</option>
                                                                <option @if($setting?  $setting-> email_encrypt == 2 : false) selected @endif value="2"> {{__('main.email_encrypt1')}}</option>
                                                                <option @if($setting?  $setting-> email_encrypt == 3 : false) selected @endif value="3"> {{__('main.email_encrypt2')}}</option>
                                                            </select>
                                                        </div>
                                                    </div>
        
                                                </div>
                                            </div>
                                            <div id="tab_send_mail_config">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label>{{ __('main.email_path') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="text" id="email_path" name="email_path"
                                                                   class="form-control" value="{{$setting? $setting -> email_path : ''}}"
                                                                   placeholder="usr/ex/..."/>
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div> 
                                        </div> 
                                    </div> 
								</div>
                                <div class="tab-pane" id="tab_points_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.points_settings')}}
                                            </h2>
                                        </div>  
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.client_value') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="client_value" name="client_value"
                                                            class="form-control" value="{{$setting? $setting -> client_value : ''}}"
                                                            placeholder="1000"/>
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.client_points') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="client_points" name="client_points"
                                                               class="form-control" value="{{$setting? $setting -> client_points : ''}}"
                                                               placeholder="20"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.employee_value') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="employee_value" name="employee_value"
                                                               class="form-control" value="{{$setting? $setting -> employee_value : ''}}"
                                                               placeholder="1000"/>
                                                    </div>
                                                </div> 
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('main.employee_points') }} 
                                                            <span class="text-danger" >*</span>
                                                        </label>
                                                        <input type="number" id="employee_points" name="employee_points"
                                                               class="form-control" value="{{$setting? $setting -> employee_points : ''}}"
                                                               placeholder="20"/>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div> 
                                    </div> 
								</div>
                                <div class="tab-pane" id="tab_tobacco_settings"> 
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2  class="alert alert-info text-center">
                                                <i class="fa-fw fa fa-cog"></i>
                                                {{__('main.tobacco_settings')}}
                                            </h2>
                                        </div> 
                                        <div class="box-content text-right"> 
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div class="form-group">
                                                            <label>{{ __('main.is_tobacco') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="checkbox" id="is_tobacco" name="is_tobacco"
                                                                   class="form-check" style="width: 30px" value="{{$setting? $setting ->is_tobacco: 0 }}"
                                                                   @if($setting? $setting ->is_tobacco == 1 : false)checked @endif/>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 ">
                                                        <div class="form-group">
                                                            <label>{{ __('main.tobacco_tax') }} 
                                                                <span class="text-danger" >*</span>
                                                            </label>
                                                            <input type="number" id="tobacco_tax" name="tobacco_tax"
                                                                   class="form-control" value="{{$setting? $setting ->tobacco_tax: '' }}"
                                                                   placeholder="20"/>
                                                        </div>
                                                    </div> 
                                                </div>  
                                        </div> 
                                    </div>
								</div> 
                                <div class="tab-pane" id="tab_zatca_settings">
                                    <div class="box">
                                        <div class="card-header pb-0">
                                            <h2 class="alert alert-info text-center">{{ __('main.zatca_settings') }}</h2>
                                        </div>
                                        <div class="box-content text-right">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card card-body mb-4">
                                                        <h5 class="mb-2">{{ __('main.zatca_onboarding') }}</h5>
                                                        <p class="text-muted small mb-3">{{ __('main.zatca_onboarding_hint') }}</p>
                                                        @php
                                                            $currentEnv = old('env', $zatcaConfig['env'] ?? 'developer-portal');
                                                        @endphp
                                                        <div class="form-group">
                                                            <label>{{ __('main.zatca_onboarding_otp') }}</label>
                                                            <input type="text" name="otp" class="form-control" value="{{ old('otp') }}" required form="zatca_onboarding_form">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('main.zatca_branch') }}</label>
                                                            <select name="branch_id" class="form-control" form="zatca_onboarding_form" required>
                                                                <option value="">{{ __('main.zatca_select_branch') }}</option>
                                                                @foreach($zatcaBranches as $branchItem)
                                                                    <option value="{{ $branchItem->id }}" @if(old('branch_id') == $branchItem->id) selected @endif>
                                                                        {{ $branchItem->branch_name ?? __('main.branch').' #'.$branchItem->id }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.zatca_env') }}</label>
                                                                <select name="env" class="form-control" form="zatca_onboarding_form">
                                                                    @foreach(['developer-portal','simulation','core'] as $env)
                                                                        <option value="{{ $env }}" @if($currentEnv === $env) selected @endif>{{ ucfirst($env) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.zatca_invoice_type_code') }}</label>
                                                                <input type="text" name="invoice_type" class="form-control" value="{{ old('invoice_type', $zatcaConfig['onboarding_invoice_type'] ?? '1100') }}" form="zatca_onboarding_form">
                                                            </div>
                                                        </div>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.zatca_egs_serial') }}</label>
                                                                <input type="text" name="egs_serial" class="form-control" value="{{ old('egs_serial', $zatcaConfig['egs_serial_number'] ?? '') }}" form="zatca_onboarding_form">
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.zatca_business_category') }}</label>
                                                                <input type="text" name="business_category" class="form-control" value="{{ old('business_category', $zatcaConfig['business_category'] ?? '') }}" form="zatca_onboarding_form">
                                                            </div>
                                                        </div>
                                                        <div class="form-group form-check">
                                                            <input type="checkbox" name="simulate" value="1" class="form-check-input" id="zatca_simulate_check" form="zatca_onboarding_form" {{ old('simulate') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="zatca_simulate_check">{{ __('main.zatca_simulate_label') }}</label>
                                                            <small class="form-text text-muted">{{ __('main.zatca_simulate_hint') }}</small>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-block" form="zatca_onboarding_form">{{ __('main.zatca_send_onboarding') }}</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-body mb-4">
                                                        <h5 class="mb-2">{{ __('main.zatca_manual_send') }}</h5>
                                                        <p class="text-muted small mb-3">{{ __('main.zatca_manual_send_hint') }}</p>
                                                        <div class="form-row">
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.zatca_sale_id') }}</label>
                                                                <input type="number" name="sale_id" class="form-control" placeholder="#123" value="{{ old('sale_id') }}" form="zatca_manual_send_form">
                                                            </div>
                                                            <div class="form-group col-md-6">
                                                                <label>{{ __('main.invoice_no') }}</label>
                                                                <input type="text" name="invoice_no" class="form-control" placeholder="INV-0001" value="{{ old('invoice_no') }}" form="zatca_manual_send_form">
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-block" form="zatca_manual_send_form">{{ __('main.send_btn') }}</button>
                                                    </div>
                                                    <div class="card card-body">
                                                        <h5 class="mb-3">{{ __('main.zatca_recent_documents') }}</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped mb-0 text-left">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ __('main.invoice_no') }}</th>
                                                                        <th>{{ __('main.zatca_document_icv') }}</th>
                                                                        <th>{{ __('main.zatca_document_uuid') }}</th>
                                                                        <th>{{ __('main.zatca_status') }}</th>
                                                                        <th>{{ __('main.actions') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                @forelse($zatcaDocuments as $document)
                                                                    <tr>
                                                                        <td>{{ $document->invoice_number }}</td>
                                                                        <td>{{ $document->icv }}</td>
                                                                        <td class="text-monospace">{{ \Illuminate\Support\Str::limit($document->uuid, 12, '...') }}</td>
                                                                        <td>
                                                                            @php
                                                                                $statusLabel = $document->sent_to_zatca_status ?? ($document->sent_to_zatca ? __('main.sent_successfully') : __('main.pending'));
                                                                                $badgeClass = $document->sent_to_zatca ? 'badge-success' : 'badge-warning';
                                                                                if($document->error_message){
                                                                                    $badgeClass = 'badge-danger';
                                                                                    $statusLabel = $document->error_message;
                                                                                }
                                                                            @endphp
                                                                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <button type="submit" class="btn btn-link p-0" form="zatca_resend_form" formaction="{{ route('zatca.documents.resend', $document) }}">{{ __('main.zatca_resend') }}</button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted">{{ __('main.zatca_not_available') }}</td>
                                                                    </tr>
                                                                @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="card card-body">
                                                        <h5 class="mb-3">{{ __('main.zatca_branch_states') }}</h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped mb-0 text-left">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ __('main.branch') }}</th>
                                                                        <th>{{ __('main.zatca_env') }}</th>
                                                                        <th>{{ __('main.zatca_certificate_status') }}</th>
                                                                        <th>{{ __('main.zatca_last_requested') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                @forelse($zatcaBranches as $branchItem)
                                                                    @php
                                                                        $setting = $branchItem->zatcaSetting;
                                                                        $hasCompliance = $setting && $setting->certificate && $setting->secret && $setting->private_key;
                                                                        $hasProduction = $setting && $setting->production_certificate && $setting->production_secret;
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $branchItem->branch_name ?? __('main.branch').' #'.$branchItem->id }}</td>
                                                                        <td>
                                                                            @if($setting)
                                                                                <span class="badge badge-info">{{ strtoupper($setting->zatca_stage) }}</span>
                                                                                @if($setting->is_simulation)
                                                                                    <span class="badge badge-secondary">{{ __('main.zatca_simulation_short') }}</span>
                                                                                @endif
                                                                            @else
                                                                                <span class="badge badge-light">{{ __('main.zatca_not_available') }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($hasCompliance)
                                                                                <span class="badge badge-success">{{ __('main.zatca_certificate_ready') }}</span>
                                                                            @else
                                                                                <span class="badge badge-warning">{{ __('main.zatca_certificate_missing') }}</span>
                                                                            @endif
                                                                            @if($hasProduction)
                                                                                <span class="badge badge-success">{{ __('main.zatca_production_ready') }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($setting && $setting->requested_at)
                                                                                {{ $setting->requested_at->format('Y-m-d H:i') }}
                                                                            @else
                                                                                {{ __('main.zatca_not_available') }}
                                                                            @endif
                                                                            @if($setting && $setting->csid)
                                                                                <div class="small text-monospace">{{ \Illuminate\Support\Str::limit($setting->csid, 18, '...') }}</div>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="4" class="text-center text-muted">{{ __('main.zatca_not_available') }}</td>
                                                                    </tr>
                                                                @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row text-center"> 
                                    <hr>
                                    <div class="col-lg-12 margin-tb">
                                        <button type="submit" class="btn btn-labeled btn-primary" id="createButton" form="myform">
                                            <span class="btn-label" style="margin-right: 10px;">
                                            <i class="fa fa-save"></i>
                                            </span>
                                            {{__('main.save_btn')}}
                                        </button>
                                    </div>
                                </div> 
                            </div> 
                        </form>
                        <form id="zatca_onboarding_form" method="POST" action="{{ route('zatca.onboard') }}" class="d-none">
                            @csrf
                        </form>
                        <form id="zatca_manual_send_form" method="POST" action="{{ route('zatca.manual_send') }}" class="d-none">
                            @csrf
                        </form>
                        <form id="zatca_resend_form" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div> 
    </div>  
<!--   Core JS Files   --> 
<!--   Create Modal   --> 
<!--   Delte Modal   -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancel-modal" data-bs-dismiss="modal" aria-label="Close"
                        style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-check"></i>
                            </span>
                            {{__('main.confirm_btn')}}
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-close"></i>
                            </span>
                            {{__('main.cancel_btn')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan 
@endsection 
@section('js')
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader(); 
            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#image_url").change(function () {
        readURL(this);
    });
</script>

<script type="text/javascript">

    $(document).ready(function () {
        $('#invoice_terms_template_selector').on('change', function(){
            const val = $(this).val();
            if(val){
                $('#invoice_terms').val(val);
            }
        });
        // $(':input','#myform')
        //     .not(':button, :submit, :reset, :hidden')
        //     .val('')
        //     .prop('checked', false)
        //     .prop('selected', false);
        // $('select').prop('selectedIndex' , 0);
        // $("#smtp_config").slideUp();
        // $("#send_mail_config").slideUp();
        setTimeout(() =>{
            console.log($("#email_protocol").value);
            const val = $("#email_protocol").value ;
            if(val == 0 ||val == "" || val == undefined){
                $("#smtp_config").slideUp();
                $("#send_mail_config").slideUp();
            } else if(val  == 1){
                $("#smtp_config").slideUp();
                $("#send_mail_config").slideDown();
            } else if(val  == 2){
                $("#smtp_config").slideDown();
                $("#send_mail_config").slideUp();
            }
        } , 1000);

        $("#email_protocol").change(function (){
            console.log(this.value);
            if(this.value  == 0 || this.value  == ""){
                $("#smtp_config").slideUp();
                $("#send_mail_config").slideUp();
            } else if(this.value  == 1){
                $("#smtp_config").slideUp();
                $("#send_mail_config").slideDown();
            } else if(this.value  == 2){
                $("#smtp_config").slideDown();
                $("#send_mail_config").slideUp();
            }
        }); 

        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });
    });

</script>
@endsection 
