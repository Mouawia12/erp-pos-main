@extends('admin.layouts.master')
@section('title') [ {{ __('main.sale')}} ]@endsection   
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            <ul class="mb-0">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<style>  
.modal-content{
    border: unset !important;
} 
.modal-header {
    border-bottom: 0 !important;
}
span strong {font-size:12px;}
</style>  
@php $enableVehicleFeatures = $enableVehicleFeatures ?? false; @endphp
    @can('اضافة مبيعات')   
    @if(!empty($allowNegativeStock))
        <div class="alert alert-success small">
            {{ __('main.sell_without_stock_enabled') }}
        </div>
    @else
        <div class="alert alert-warning small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>{{ __('main.sell_without_stock_disabled') }}</span>
            <div class="d-flex flex-wrap gap-2">
                @if(Route::has('system_settings.enable_negative_stock'))
                    <button type="button" class="btn btn-sm btn-primary" id="enableNegativeStockBtn">
                        {{ __('main.enable_negative_stock_now') }}
                    </button>
                @endif
            </div>
        </div>
    @endif
    <div class="row row-sm">
        <div class="col-xl-12">
            <form method="POST" action="{{ route('store_sale') }}" id="salesform"
                           enctype="multipart/form-data" autocomplete="off">
            @csrf 
            <input type="hidden" name="tax_mode" value="inclusive">
            <div class="card shadow mb-4 col-xl-12"> 
                <div class="card-header"  id="head-right" > 
                    <div class="row"> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('main.invoice_no') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="invoice_no" name="invoice_no"
                                       class="form-control" placeholder="invoice_no" readonly/> 
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                <input type="datetime-local"  id="bill_date" name="bill_date"
                                       class="form-control" readonly/> 
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ __('main.invoice_type') }}</label>
                                <select class="form-control" name="invoice_type" id="invoice_type">
                                    @php $defaultType = $defaultInvoiceType ?? ($settings->default_invoice_type ?? 'simplified_tax_invoice'); @endphp
                                    <option value="tax_invoice" @if($defaultType=='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                    <option value="simplified_tax_invoice" @if($defaultType=='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                    <option value="non_tax_invoice" @if($defaultType=='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="d-block">{{ __('main.branche')}}<span class="text-danger">*</span> </label> 
                                @if(empty(Auth::user()->branch_id))
                                    <select name="branch_id" id="branch_id" class="js-example-basic-single w-100" required>  
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly
                                           value="{{Auth::user()->branch->branch_name}}"/>
                                    <input required class="form-control" type="hidden" id="branch_id"
                                           name="branch_id"
                                           value="{{Auth::user()->branch_id}}"/>
                                @endif 
                            </div>
                        </div>
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                    name="warehouse_id" id="warehouse_id" required>  
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label>{{ __('main.clients') }} <span class="text-danger">*</span> </label>
                                @php
                                    $defaultCustomerId = optional($walkInCustomer)->id ?? optional($customers->first())->id;
                                    $selectedCustomer = old('customer_id', $defaultCustomerId);
                                @endphp
                                <select class="js-example-basic-single w-100"
                                    name="customer_id" id="customer_id" required data-walk-in="{{ optional($walkInCustomer)->id }}">
                                    @foreach ($customers as $customer)
                                        <option value="{{$customer -> id}}"
                                                data-default-discount="{{$customer->default_discount ?? 0}}"
                                                data-representative="{{$customer->representative_id_ ?? ''}}"
                                                @if($selectedCustomer == $customer->id) selected @endif>
                                            {{ $customer -> name}}
                                            @if(!empty($walkInCustomer) && $walkInCustomer->id === $customer->id)
                                                ({{ __('main.walk_in_customer') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="walk_in_customer_id" value="{{ optional($walkInCustomer)->id }}">
                                <div class="mt-1 d-flex gap-2">
                                    <a href="{{ route('clients',3) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        {{ __('main.add_new') }} {{ __('main.clients') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @php $showWalkInFields = ($selectedCustomer == optional($walkInCustomer)->id); @endphp
                        <div class="col-md-3 {{ $showWalkInFields ? '' : 'd-none' }}" id="walk_in_fields">
                            <div class="form-group">
                                <label>{{ __('main.customer_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" id="walk_in_name"
                                       placeholder="{{__('main.customer_name')}}"
                                       value="{{ old('customer_name', optional($walkInCustomer)->name) }}">
                            </div>
                            <div class="form-group mt-2">
                                <label>{{ __('main.customer_phone') }}</label>
                                <input type="text" class="form-control" name="customer_phone" id="walk_in_phone"
                                       placeholder="{{__('main.customer_phone')}}"
                                       value="{{ old('customer_phone', optional($walkInCustomer)->phone) }}">
                                <small class="text-muted">{{ __('main.walk_in_customer_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('main.representatives') }}</label>
                                <select class="js-example-basic-single w-100"
                                    name="representative_id" id="representative_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($representatives as $rep)
                                        <option value="{{$rep->id}}">{{$rep->user_name}}</option>
                                    @endforeach
                                </select>
                                <div class="mt-1">
                                    <a href="{{ route('representatives') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        {{ __('main.add_new') }} {{ __('main.representatives') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('main.cost_center') }}</label>
                                <input type="text" class="form-control" name="cost_center" id="cost_center" placeholder="{{__('main.cost_center')}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('main.invoice_payment_method') ?? __('main.payment_method') }}</label>
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="cash">{{ __('main.cash') }}</option>
                                    <option value="credit">{{ __('main.credit') }}</option>
                                </select>
                            </div>
                        </div>
                        @if($enableVehicleFeatures)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.vehicle_plate') }}</label>
                                    <input type="text" id="vehicle_plate" name="vehicle_plate"
                                           class="form-control" list="vehiclePlateOptions"
                                           placeholder="{{ __('main.vehicle_plate') }}"
                                           value="{{ old('vehicle_plate') }}">
                                    <datalist id="vehiclePlateOptions"></datalist>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.vehicle_name') }}</label>
                                    <input type="text" id="vehicle_name" name="vehicle_name"
                                           class="form-control" placeholder="{{ __('main.vehicle_name') }}"
                                           value="{{ old('vehicle_name') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.vehicle_odometer') }}</label>
                                    <input type="number" step="1" id="vehicle_odometer" name="vehicle_odometer"
                                           class="form-control" placeholder="{{ __('main.vehicle_odometer') }}"
                                           value="{{ old('vehicle_odometer') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.vehicle_color') }}</label>
                                    <input type="text" id="vehicle_color" name="vehicle_color"
                                           class="form-control" placeholder="{{ __('main.vehicle_color') }}"
                                           value="{{ old('vehicle_color') }}">
                                </div>
                            </div>
                        @endif
                    </div> 
                </div>    
                <div class="row"> 
                    <div class="card shadow mb-4 col-9">
                        <div class="card-body">   
                                <div class="row"> 
                                    <div class="col-md-12" id="sticker">
                                        <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                            <div class="form-group">
                                                <div class="input-group wide-tip">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-3x fa-barcode addIcon"></i>
                                                    </div>
                                                    <input type="text" name="add_item" id="add_item" value="" class="form-control input-lg ui-autocomplete-input" placeholder="{{__('main.barcode.note')}}" autocomplete="off">
                                                </div> 
                                                <div class="mt-1">
                                                    <a href="{{ route('createProduct') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        {{ __('main.add_new') }} {{ __('main.products') ?? 'منتج' }}
                                                    </a>
                                                </div>
                                            </div>
                                            <ul class="suggestions" id="products_suggestions" style="display: block">
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>  
                                </div> 
                                <div class="row"> 
                                    <div class="card shadow mb-4 col-xl-12">
                                        <div class="card-header pb-0">
                                            <h4 class="alert alert-info text-center">
                                                <i class="fa fa-cart-shopping"></i>
                                                {{__('main.items_invoice')}} 
                                            </h4>
                                        </div>  
                                        <div class="card-body px-0 pt-0">
                                            <div class="table-responsive hoverable-table">
                                                <table class="display w-100 table-bordered" id="sTable" 
                                                       style="text-align: center;">  
                                                    <thead>
                                                        <tr>
                                                            <th class="col-md-3 text-center">{{__('main.item_name_code')}}</th>
                                                            <th class="text-center">{{__('main.available_qty')}}</th>
                                                            <th class="text-center">{{__('main.cost')}}</th>
                                                            <th class="text-center">{{__('main.last_sale_price')}}</th>
                                                            <th class="text-center">{{ __('main.unit') }}</th>
                                                            <th class="text-center">{{ __('main.quantity') }}</th>
                                                            <th class="text-center">{{__('main.price.unit')}}</th>
                                                            <th class="text-center">{{__('main.discount')}}</th> 
                                                            <th class="text-center">{{__('main.mount')}}</th>
                                                            <th class="text-center">{{__('main.tax')}}</th> 
                                                            <th class="text-center">{{__('main.total')}}</th> 
                                                            <th class="text-center"><i class="fa fa-trash"></i></th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody"></tbody>
                                                    <tfoot>
                                                        <th colspan="7">
                                                            {{__('main.sum')}}
                                                        </th>
                                                        <td class="text-center" colspan="1">
                                                            <strong id="discount-text">0</strong>   
                                                        </td>  
                                                        <td class="text-center" colspan="1">
                                                            <strong id="total-text">0</strong>   
                                                        </td> 
                                                        <td class="text-center" colspan="1">
                                                            <strong id="tax-text">0</strong>   
                                                        </td> 
                                                        <td class="text-center" colspan="1">
                                                            <strong id="net-text">0</strong>   
                                                        </td> 
                                                        <td class="text-center" colspan="1"></td>   
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div> 
                                    </div> 
                                </div> 
                                <div class="row">
                                    <div class="show_modal"> 
                                    </div>
                                </div>  
                            
                        </div> 
                    </div> 
                    <div class="card shadow mb-4 col-3"> 
                        <div class="card-body ">
                            <div class="row document_type1" style="align-items: center; margin-bottom: 10px;">
                                <div class="col-6">
                                    <label style="text-align: right;float: right;"> {{__('main.items_count')}} </label>
                                </div>
                                <div class="col-6">
                                    <input type="text" readonly class="form-control" id="items_count">
                                </div>
                            </div>  
                            <div class="row" style="align-items: center; margin-bottom: 10px;">
                                <div class="col-6">
                                    <label style="text-align: right;float: right;"> {{__('main.total_mount')}} </label>
                                </div>
                                <div class="col-6">
                                    <input type="text" readonly class="form-control" id="first_total">
                                </div>
                            </div> 
                            <div class="row" style="align-items: center; margin-bottom: 10px;">
                                <div class="col-6">
                                    <label style="text-align: right;float: right;"> {{__('main.total_tax')  }} </label>
                                </div>
                                <div class="col-6">
                                    <input type="text" readonly class="form-control" id="tax_total">
                                </div>
                            </div>  
                            <div class="row" hidden style="align-items: center; margin-bottom: 10px;">
                                <div class="col-6">
                                    <label style="text-align: right;float: right;"> {{__('main.discount_total')  }} </label>
                                </div>
                                <div class="col-6">
                                    <input type="text" readonly class="form-control" id="discount_total">
                                </div>
                            </div>  
                            <div class="row" style="align-items: center; margin-bottom: 10px;">
                                <div class="col-6">
                                    <label style="text-align: right;float: right;"> {{__('main.total.final')}} </label>
                                </div>
                                <div class="col-6">
                                    <input type="text" readonly class="form-control" id="net_sales" name="net_sales">
                                </div>
                            </div>
                            <hr class="sidebar-divider d-none d-md-block">
                            <div class="row" style="align-items: baseline; margin-bottom: 10px;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('main.discount_type') }} </label> 
                                        <select class="js-example-basic-single w-100"
                                            name="discount_type" id="discount_type">  
                                            <option value="1">{{ __('main.discount_amount') }}</option> 
                                            <option value="2">{{ __('main.discount_percent') }}</option> 
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-md-6" >
                                    <div class="form-group">
                                        <label> {{__('main.discount')}} </label>
                                        <input type="hidden" id="discount_amount" name="discount" value="0">
                                        <input type="number" step="any" class="form-control" id="discount_input" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label
                                            style="text-align: right;float: right;"> {{__('main.invoice.total')}} </label>
                                        <input type="text" readonly  class="form-control" id="net_after_discount" name="net_after_discount" placeholder="0">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label style="text-align: right;float: right;"> {{__('main.notes')}} </label>
                                        <textarea class="form-control" name="notes" rows="2" placeholder="{{__('main.notes')}}"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center" style="display: block; margin: auto;">
                                    <button type="button" class="btn btn-primary" id="SaveSales">
                                        <i class="fa fa-save"></i>
                                        {{__('main.save_btn')}}
                                    </button> 
                                </div> 
                            </div>  
                        </div>  
                    </div>  
                </div>  
            </div>
            </form> 
        </div>  
    </div>  

<audio id="mysoundclip1" preload="auto">
    <source src="{{URL::asset('assets/sound/beep/beep-timber.mp3')}}"></source>
</audio>
<audio id="mysoundclip2" preload="auto">
    <source src="{{URL::asset('assets/sound/beep/beep-07.mp3')}}"></source>
</audio>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                Alert!
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold; background: white;
                    height: 35px; width: 35px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="smallBody">
                <div class="mb-3">
                    <i class="fa fa-exclamation-triangle text-warning" style="font-size:48px;"></i>
                </div>
                <label class="alertTitle">{{__('main.notfound')}}</label>
                <br> 
                <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row text-center">
                    <div class="col-6 text-center" style="display: block;margin: auto">
                        <button type="button" class="btn btn-labeled btn-primary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;">
                                <i class="fa fa-check"></i>
                            </span>{{__('main.ok_btn')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="duplicateItemModal" tabindex="-1" role="dialog" aria-labelledby="duplicateItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="alertTitle mb-0">{{ __('main.alerts') }}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">{{ __('main.duplicate_item_warning') }}</p>
                <p class="text-muted" id="duplicateItemName"></p>
                <div class="d-flex justify-content-around mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelDuplicateItem">
                        {{ __('main.cancel_btn') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmDuplicateItem">
                        {{ __('main.add_new') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Route::has('system_settings.enable_negative_stock'))
<div class="modal fade" id="enableNegativeStockModal" tabindex="-1" role="dialog" aria-labelledby="enableNegativeStockLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enableNegativeStockLabel">{{ __('main.enable_negative_stock_confirm_title') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ __('main.enable_negative_stock_confirm_text') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('main.cancel_btn') }}</button>
                <button type="button" class="btn btn-primary" id="confirmEnableNegativeStock">{{ __('main.enable_negative_stock_now') }}</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="variantModal" tabindex="-1" role="dialog" aria-labelledby="variantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="alertTitle mb-0">{{ __('main.choose_variant') ?? 'اختر المتغير' }}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('main.color') ?? 'اللون' }}</th>
                                <th>{{ __('main.size') ?? 'المقاس' }}</th>
                                <th>{{ __('main.barcode') }}</th>
                                <th>{{ __('main.price') }}</th>
                                <th>{{ __('main.available_qty') }}</th>
                                <th>{{ __('main.choose') }}</th>
                            </tr>
                        </thead>
                        <tbody id="variantOptionsBody"></tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelVariantSelection">{{ __('main.cancel_btn') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmVariantSelection">{{ __('main.save_btn') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan 
@endsection 
@section('js')
<script type="text/javascript">

    var suggestionItems = {};
    var sItems = {};
    var count = 1;
    var itemKey = 1;
    const itemNotePlaceholder = @json(__('main.line_note_hint'));
    let pendingDuplicateItem = null;
    const allowNegativeStock = {{ !empty($allowNegativeStock) ? 'true' : 'false' }};
    const insufficientTemplate = "{{ __('main.insufficient_stock', ['item' => '__ITEM__']) }}";

    function setupNegativeStockQuickEnable(){
        @if(Route::has('system_settings.enable_negative_stock'))
        var enableBtn = document.getElementById('enableNegativeStockBtn');
        if(!enableBtn){
            return;
        }
        var modal = $('#enableNegativeStockModal');
        var confirmBtn = $('#confirmEnableNegativeStock');
        $(enableBtn).on('click', function(){
            modal.modal('show');
        });
        confirmBtn.off('click').on('click', function(){
            var $btn = $(this);
            $btn.prop('disabled', true).text('{{ __('main.enable_negative_stock_processing') }}');
            $.ajax({
                url: '{{ route('system_settings.enable_negative_stock') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(){
                    window.location.reload();
                },
                error: function(){
                    alert('{{ __('main.enable_negative_stock_error') }}');
                },
                complete: function(){
                    $btn.prop('disabled', false).text('{{ __('main.enable_negative_stock_now') }}');
                    modal.modal('hide');
                }
            });
        });
        modal.on('hidden.bs.modal', function(){
            confirmBtn.prop('disabled', false).text('{{ __('main.enable_negative_stock_now') }}');
        });
        @endif
    }

    function playSoundById(audioId){
        var audioElement = document.getElementById(audioId);
        if(!audioElement){
            return;
        }
        try{
            audioElement.currentTime = 0;
            var promise = audioElement.play();
            if(promise && typeof promise.catch === 'function'){
                promise.catch(function(){
                    try{
                        var clone = audioElement.cloneNode(true);
                        clone.currentTime = 0;
                        clone.play();
                    }catch(e){}
                });
            }
        }catch (e){}
    }

    function playSuccessSound(){
        playSoundById('mysoundclip1');
    }

    function playWarningSound(){
        playSoundById('mysoundclip2');
    }

    function setupPaymentModal(){
        const mismatchMsg = "{{ __('main.payments_must_match_total') }}";
        const totalLabel = "{{ __('main.total.final') }}";
        const paidLabel = "{{ __('main.paid') }}";

        const moneyInput = document.getElementById('money');
        const cashInput = document.getElementById('cash');
        const bankInput = document.getElementById('bank_amount');

        function clearErrorState(){
            const errorBox = document.getElementById('paymentError');
            if(errorBox){
                errorBox.classList.add('d-none');
                errorBox.textContent = '';
            }
            if(cashInput){
                cashInput.classList.remove('is-invalid');
            }
        }

        function showError(message){
            const errorBox = document.getElementById('paymentError');
            if(errorBox){
                errorBox.textContent = message;
                errorBox.classList.remove('d-none');
            }
            if(cashInput){
                cashInput.classList.add('is-invalid');
            }
        }

        function syncBankAmount(formatCashField){
            syncCashBoxInputs(formatCashField);
        }

        $(document).off('input','.payment-input').on('input','.payment-input',function (){
            clearErrorState();
            syncBankAmount(false);
        });

        if(cashInput){
            $(cashInput).off('blur.paymentFormat').on('blur.paymentFormat', function(){
                syncBankAmount(true);
            });
        }

        $(document).off('click','#payment_btn').on('click','#payment_btn',function (){
            const paymentBtn = this;
            const invoiceTotal = parseFloat(moneyInput?.value) || 0;
            const cashVal = parseFloat(cashInput?.value) || 0;
            const bankVal = parseFloat(bankInput?.value) || 0;
            const paid = Number((cashVal + bankVal).toFixed(2));
            const invoice = Number(invoiceTotal.toFixed(2));

            if(paid === invoice){
                clearErrorState();
                paymentBtn.disabled = true;
                paymentBtn.innerText = '... جاري الحفظ';
                $('#paymentsModal').modal('hide');
                document.getElementById('salesform').submit();
            }else{
                const differenceValue = invoice - paid;
                const diffLabel = differenceValue > 0 ? "{{ __('main.remain') }}" : "{{ __('main.extra_amount') ?? 'زيادة' }}";
                const formattedDiff = Math.abs(differenceValue).toFixed(2);
                const message = mismatchMsg + ' - ' + totalLabel + ': ' + invoice.toFixed(2) + ' | ' + paidLabel + ': ' + paid.toFixed(2) + ' (' + diffLabel + ': ' + formattedDiff + ')';
                showError(message);
            }
        });

        $('#paymentsModal').off('shown.bs.modal').on('shown.bs.modal', function (){
            const paymentBtn = document.getElementById('payment_btn');
            if(paymentBtn){
                paymentBtn.disabled = false;
                paymentBtn.innerText = "{{ __('main.save_btn') }}";
            }
            if(moneyInput && cashInput){
                cashInput.value = (moneyInput.value || 0);
            }
            clearErrorState();
            syncBankAmount(true);
        });
    }

    function syncCashBoxInputs(formatCashField){
        const moneyInput = document.getElementById('money');
        const cashInput = document.getElementById('cash');
        const bankInput = document.getElementById('bank_amount');

        if(!moneyInput || !cashInput || !bankInput){
            return;
        }

        const total = parseFloat(moneyInput.value) || 0;
        let cashVal = parseFloat(cashInput.value);
        if(isNaN(cashVal)){
            cashVal = 0;
        }
        cashVal = Math.min(Math.max(cashVal, 0), total);

        if(formatCashField){
            cashInput.value = cashVal.toFixed(2);
        }

        bankInput.value = (total - cashVal).toFixed(2);
    }

    let paymentMethodSelect;

    $(document).ready(function() {  

        setupNegativeStockQuickEnable();

        // ضمان وجود حقل رقم الفاتورة حتى لو لم يتم رسمه لأي سبب، لتجنب أخطاء JS توقف بقية المعالجات
        if(!document.getElementById('invoice_no')){
            var salesForm = document.getElementById('salesform') || document.body;
            var hiddenInvoice = document.createElement('input');
            hiddenInvoice.type = 'hidden';
            hiddenInvoice.id = 'invoice_no';
            hiddenInvoice.name = 'invoice_no';
            salesForm.appendChild(hiddenInvoice);
        }

        function resolveBranchSelect(){
            return document.getElementById('branch_id');
        }

        function resolveInvoiceInput(){
            return document.getElementById('invoice_no');
        }

        var warehouseSelect = $('#warehouse_id');
        var billInput = document.getElementById('bill_date');
        paymentMethodSelect = $('#payment_method');
        const vehiclePlateInput = $('#vehicle_plate');
        const vehicleOdometerInput = $('#vehicle_odometer');
        const vehicleNameInput = $('#vehicle_name');
        const vehicleColorInput = $('#vehicle_color');
        const vehicleOptionsList = $('#vehiclePlateOptions');
        const walkInCustomerId = Number($('#walk_in_customer_id').val() || 0);
        const $walkInFields = $('#walk_in_fields');
        const representativeSelect = $('#representative_id');
        const costCenterInput = $('#cost_center');
        const customerVehiclesCache = {};

        function setPaymentMethod(value){
            if(paymentMethodSelect && paymentMethodSelect.length){
                paymentMethodSelect.val(value).trigger('change');
            }
        }

        function hydrateVehicleOdometer(){
            if(!vehiclePlateInput.length || !vehicleOdometerInput.length){
                return;
            }
            var currentValue = (vehiclePlateInput.val() || '').trim();
            if(!currentValue){
                return;
            }
            var option = vehicleOptionsList.find('option').filter(function(){
                return this.value === currentValue;
            }).first();
            if(option.length){
                var stored = option.attr('data-odometer');
                if(stored !== undefined && stored !== null && stored !== ''){
                    vehicleOdometerInput.val(stored);
                }
                var storedName = option.attr('data-name');
                if(storedName){
                    vehicleNameInput.val(storedName);
                }
                var storedColor = option.attr('data-color');
                if(storedColor){
                    vehicleColorInput.val(storedColor);
                }
            }
        }

        function renderVehicleOptions(vehicles){
            if(!vehicleOptionsList.length){
                return;
            }
            vehicleOptionsList.empty();
            (vehicles || []).forEach(function(vehicle){
                if(!vehicle || !vehicle.vehicle_plate){
                    return;
                }
                var label = vehicle.vehicle_plate;
                var parts = [];
                if(vehicle.vehicle_odometer !== undefined && vehicle.vehicle_odometer !== null && vehicle.vehicle_odometer !== ''){
                    parts.push(vehicle.vehicle_odometer);
                }
                if(vehicle.vehicle_color){
                    parts.push(vehicle.vehicle_color);
                }
                if(parts.length){
                    label += ' ('+parts.join(' - ')+')';
                }
                var option = $('<option>')
                    .attr('value', vehicle.vehicle_plate)
                    .attr('data-odometer', vehicle.vehicle_odometer !== undefined && vehicle.vehicle_odometer !== null ? vehicle.vehicle_odometer : '')
                    .attr('data-name', vehicle.vehicle_name || '')
                    .attr('data-color', vehicle.vehicle_color || '');
                option.text(label);
                vehicleOptionsList.append(option);
            });
            hydrateVehicleOdometer();
        }

        function fetchCustomerVehicles(customerId){
            if(!vehicleOptionsList.length){
                return;
            }
            if(!customerId){
                renderVehicleOptions([]);
                return;
            }
            if(customerVehiclesCache[customerId]){
                renderVehicleOptions(customerVehiclesCache[customerId]);
                return;
            }
            var url = "{{ route('customers.vehicles', ['customer' => ':id']) }}";
            url = url.replace(':id', customerId);
            $.get(url)
                .done(function(response){
                    customerVehiclesCache[customerId] = response || [];
                    renderVehicleOptions(customerVehiclesCache[customerId]);
                })
                .fail(function(){
                    renderVehicleOptions([]);
                });
        }

        if(vehiclePlateInput.length){
            vehiclePlateInput.on('change blur', hydrateVehicleOdometer);
        }

        if(billInput){
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            now.setMilliseconds(null);
            now.setSeconds(null);
            billInput.value = now.toISOString().slice(0, -1);
        }


        function toggleWalkInFields(selectedId){
            const isWalkIn = walkInCustomerId && Number(selectedId) === Number(walkInCustomerId);
            if($walkInFields.length){
                $walkInFields.toggleClass('d-none', !isWalkIn);
            }
            if(isWalkIn){
                setPaymentMethod('cash');
            } else {
                setPaymentMethod('credit');
            }
        }

        if(representativeSelect.length){
            representativeSelect.on('change', function(){
                const selectedText = $(this).find('option:selected').text().trim();
                if(selectedText && costCenterInput && !costCenterInput.val()){
                    costCenterInput.val(selectedText);
                }
            });
        }

        if(resolveBranchSelect()){
            getWarehouse();
        }
        $('#warehouse_id').change(function (){
            // invoice number is generated on save server-side
        });

        //document.getElementById('bill_date').valueAsDate = new Date();
        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val()); 
        });

        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val()); 
        });

        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#paymentsModal').modal("hide");
            id = 0 ;
        });

        $('#customer_id').on('change', function(){
            var defaultDiscount = $(this).find(':selected').data('default-discount') || 0;
            var repId = $(this).find(':selected').data('representative') || '';
            if(defaultDiscount > 0){
                $('#discount_input').val(defaultDiscount);
                NetAfterDiscount();
            }
            if(representativeSelect.length){
                if(repId){
                    representativeSelect.val(repId).trigger('change');
                } else {
                    representativeSelect.val('').trigger('change');
                }
            }
            toggleWalkInFields($(this).val());
            @if($enableVehicleFeatures)
            fetchCustomerVehicles($(this).val());
            @endif
        });
        toggleWalkInFields($('#customer_id').val());
        @if($enableVehicleFeatures)
        fetchCustomerVehicles($('#customer_id').val());
        @endif

        $(document).on('click' , '.cancel-modal' , function(event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });

        $(document).on('click' , '.deleteBtn' , function (event) {
            var row = $(this).parent().parent().index(); 
            var row1 = $(this).closest('tr');
            var item_id = row1.attr('data-item-id');
            delete sItems[item_id];
            loadItems(); 
            playWarningSound();
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
            playSuccessSound();
        });

        $(document).on('click', '#SaveSales', function () {
            var rows =  0 ; 
            rows = ($('#sTable tbody tr').length);
            console.log(rows);

            var net_after_discount = document.getElementById('net_after_discount').value;
            var client = document.getElementById('customer_id').value ;
            var warehouse_id = document.getElementById('warehouse_id').value;

            if(client > 0 && warehouse_id > 0){
                if (rows > 0){ 
                    addPayments(net_after_discount);
                } else {
                    alert($('<div>{{__('main.invoice_details_required')}}</div>').text());
                }
            } else {
                alert($('<div>{{__('main.customer_warehouse_required')}}</div>').text());
            } 
        });

        
        $('#branch_id').change(function (){
            getWarehouse();
        });

        function getWarehouse(){
            var branchSelect = resolveBranchSelect();
            if(!branchSelect){
                return;
            }
            
            var branch_id = branchSelect.value;
            var url = '{{route('get.warehouses.branches',":id")}}'; 
            url = url.replace(":id", branch_id); 
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
            
                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('#warehouse_id').empty();
                        //$('#warehouse_id').append('<option value="0">حدد الاختيار ..</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                } 
            });
        }

        $(document).on('change', '#warehouse_id', function () { 
            $('#products_suggestions').empty();
            $('#sTable tbody').empty();
            //$('#items_count').empty(); 
            $('#total-text').empty(); 
            document.getElementById('items_count').value = 0  ; 
            document.getElementById('first_total').value = 0; 
            document.getElementById('tax_total').value = 0;
            document.getElementById('discount_total').value = 0; 
            document.getElementById('net_sales').value = 0; 
            document.getElementById('discount_input').value = 0; 
            document.getElementById('discount_amount').value = 0; 
            document.getElementById('net_after_discount').value = 0;   
            suggestionItems = {};
            sItems = {};
            count = 1; 
        });

        document.getElementById('items_count').value = 0  ; 
        document.getElementById('first_total').value = 0; 
        document.getElementById('tax_total').value = 0;
        document.getElementById('discount_total').value = 0; 
        document.getElementById('net_sales').value = 0; 
        document.getElementById('net_after_discount').value = 0;   

        $(document).on('change', '#discount_type', function () {
            NetAfterDiscount(); 
        });

        $(document).on('change', '#discount_input', function () {
            NetAfterDiscount(); 
        });

        $(document).on('keyup', '#discount_input', function () {
            NetAfterDiscount(); 
        });

        $('#confirmDuplicateItem').on('click', function (){
            if(pendingDuplicateItem){
                var item = pendingDuplicateItem;
                pendingDuplicateItem = null;
                $('#duplicateItemModal').modal('hide');
                addItemToTable(item, true);
            }
        });

        $('#cancelDuplicateItem').on('click', function (){
            pendingDuplicateItem = null;
            $('#duplicateItemModal').modal('hide');
        });

        $('#duplicateItemModal').on('hidden.bs.modal', function (){
            pendingDuplicateItem = null;
        });

        $('#confirmVariantSelection').on('click', function (){
            if(!pendingVariantProduct){
                return;
            }
            const selected = $('input[name="variant_choice"]:checked').val();
            if(!selected){
                alert('{{ __('main.choose_variant') ?? 'اختر المتغير' }}');
                return;
            }
            const variantObj = (pendingVariantProduct.variants || []).find(function(v){
                return String(v.id) === String(selected);
            });
            if(variantObj){
                pendingVariantProduct.selected_variant = variantObj;
                pendingVariantProduct.variant_color = variantObj.color;
                pendingVariantProduct.variant_size = variantObj.size;
                pendingVariantProduct.variant_barcode = variantObj.barcode;
            }
            const item = pendingVariantProduct;
            pendingVariantProduct = null;
            $('#variantModal').modal('hide');
            addItemToTable(item);
        });

        $('#cancelVariantSelection').on('click', function (){
            pendingVariantProduct = null;
            $('#variantModal').modal('hide');
        });

        $('#variantModal').on('hidden.bs.modal', function (){
            pendingVariantProduct = null;
            $('input[name="variant_choice"]').prop('checked', false);
        });

        NetAfterDiscount();

    });

    function formatInsufficientMessage(item){
        const name = item.name ?? item.code ?? '';
        return insufficientTemplate.replace('__ITEM__', name);
    }

    function isQuantityValid(item, qty){
        if(allowNegativeStock){
            return true;
        }
        const available = Number(item.available_qty ?? 0);
        const factor = Number(item.unit_factor ?? 1);
        const required = Number(qty ?? item.qnt ?? 0) * factor;
        return available >= required;
    }

    function searchProduct(code){
        //var url = '{{route('getProduct',":id")}}';
        var url = '{{route('get.product.warehouse',[":warehouse",":id"])}}';
        var warehouse_id = $('#warehouse_id').val();
        
        url = url.replace(":id",code);
        url = url.replace(":warehouse",warehouse_id);

        $.ajax({
            type:'get',
            url:url,
            dataType: 'json', 
            success:function(response){ 
                document.getElementById('products_suggestions').innerHTML = '';
                if(response){
                    if(response.length == 1){
                        //addItemToTable
                        addItemToTable(response[0]); 
                        playSuccessSound();
                    }else if(response.length > 1){ 
                        showSuggestions(response); 
                    } else if(response.id){
                        showSuggestions(response);
                    } else {
                        //showNotFoundAlert
                        openDialog();
                        document.getElementById('add_item').value = '' ;
                    }
                } else {
                    //showNotFoundAlert
                    openDialog();
                    document.getElementById('add_item').value = '' ;
                }
            }
        });
    }

  function showSuggestions(response) { 
        $data = '';
        $.each(response,function (i,item) {
            suggestionItems[item.id] = item;
            let label = item.name;
            if(item.selected_variant){
                label += ' | '+(item.selected_variant.color ?? '');
                label += ' '+(item.selected_variant.size ?? '');
            }
            $data +='<li class="select_product" data-item-id="'+item.id+'">'+label+'</li>';
        });
        document.getElementById('products_suggestions').innerHTML = $data;
    }

    function openDialog(){
      let href = $(this).attr('data-attr');
      $.ajax({
          url: href,
          beforeSend: function() {
              $('#loader').show();
          },
          // return the result
          success: function(result) {
              $('#deleteModal').modal("show");
          },
          complete: function() {
              $('#loader').hide();
          },
          error: function(jqXHR, testStatus, error) {
              console.log(error);
              alert("Page " + href + " cannot open. Error:" + error);
              $('#loader').hide();
          },
          timeout: 8000
      })
    }

    function needsVariantSelection(item){
        return Array.isArray(item.variants) && item.variants.length > 0 && !item.selected_variant;
    }

    function openVariantModal(item){
        pendingVariantProduct = item;
        const tbody = $('#variantOptionsBody');
        tbody.empty();
        (item.variants || []).forEach(function(variant){
            const row = `<tr>
                <td>${escapeHtml(variant.color ?? '')}</td>
                <td>${escapeHtml(variant.size ?? '')}</td>
                <td>${escapeHtml(variant.barcode ?? '')}</td>
                <td>${Number(variant.price ?? item.price ?? 0).toFixed(2)}</td>
                <td>${Number(variant.quantity ?? 0)}</td>
                <td><input type="radio" name="variant_choice" value="${variant.id}"></td>
            </tr>`;
            tbody.append(row);
        });
        $('#variantModal').modal({backdrop:'static',keyboard:false});
    }

    function addItemToTable(item, forceDuplicate){
        forceDuplicate = forceDuplicate || false;

        if($('#warehouse_id').val() == null){
            alert($('<div>{{__('main.customer_warehouse_required')}}</div>').text());
            return;
        }

        if(!item){
            return;
        }

        if(count == 1){
            sItems = {};
        }

        if(needsVariantSelection(item)){
            openVariantModal(item);
            return;
        }

        var targetVariantId = item.selected_variant ? item.selected_variant.id : null;
        var duplicateExists = Object.values(sItems).some(function(existing){
            var existingVariantId = existing.variant_id ?? (existing.selected_variant ? existing.selected_variant.id : null);
            return existing.product_id === item.id && String(existingVariantId ?? '') === String(targetVariantId ?? '');
        });
        if(duplicateExists && !forceDuplicate){
            pendingDuplicateItem = item;
            $('#duplicateItemName').text(item.name ? item.name : (item.code || ''));
            $('#duplicateItemModal').modal({backdrop: 'static', keyboard: false});
            playWarningSound();
            return;
        }

        var price = item.price;
        if(item.selected_variant && item.selected_variant.price){
            price = item.selected_variant.price;
        }
        if(item.promo_discount_unit){
            price = Math.max(price - Number(item.promo_discount_unit), 0);
        }
        var taxType = item.tax_method;
        var taxRate = item.tax;
        var itemTax = 0;
        var priceWithoutTax = 0;
        var priceWithTax = 0; 
        var itemQnt = 1;

        var defaultUnit = item.unit ?? (item.units_options && item.units_options[0] ? item.units_options[0].unit_id : null);
        var defaultFactor = 1;
        if(item.units_options && item.units_options.length){
            var firstUnit = item.units_options.find(function(u){ return u.unit_id == defaultUnit; }) || item.units_options[0];
            defaultFactor = firstUnit.conversion_factor ?? 1;
            if(firstUnit.price){
                price = firstUnit.price;
            }
        }

        if(taxType == 1){
            //included
            priceWithTax = price;
            priceWithoutTax = (price / (1+(taxRate/100))); 
            itemTax = priceWithTax - priceWithoutTax;
        }else{
            //excluded
            itemTax = price * (taxRate/100); 
            priceWithoutTax = price;
            priceWithTax = price + itemTax;
        }
        //update 19-04-2024
        var Excise = item.tax_excise;
        var taxExcise = 0;
        if(Excise > 0){    
            taxExcise = (priceWithoutTax * (Excise/100));
            itemTax = itemTax + taxExcise;
        }
        
        var uniqueKey = item.selected_variant ? ('v'+item.selected_variant.id) : ('p'+item.id);
        var key = uniqueKey + '_' + itemKey;
        itemKey++;

        sItems[key] = item;
        sItems[key].product_id = item.id;
        sItems[key].price_with_tax = priceWithTax;
        sItems[key].price_withoute_tax = priceWithoutTax;
        sItems[key].original_price = priceWithoutTax;
        sItems[key].item_tax = itemTax;
        sItems[key].tax_rate = taxRate;
        sItems[key].tax_excise = Excise; 
        sItems[key].qnt = 1;
        sItems[key].discount = 0;
        sItems[key].note = '';
        sItems[key].available_qty = item.qty ? Number(item.qty) : 0;
        sItems[key].cost = item.cost ? Number(item.cost) : 0;
        sItems[key].last_sale_price = item.last_sale_price ? Number(item.last_sale_price) : 0;
        sItems[key].selected_unit_id = defaultUnit;
        sItems[key].unit_factor = defaultFactor;
        sItems[key].units_options = item.units_options ?? [];
        sItems[key].variant_id = item.selected_variant ? item.selected_variant.id : (item.variant_id ?? null);
        sItems[key].variant_color = item.selected_variant ? item.selected_variant.color : (item.variant_color ?? null);
        sItems[key].variant_size = item.selected_variant ? item.selected_variant.size : (item.variant_size ?? null);
        sItems[key].variant_barcode = item.selected_variant ? item.selected_variant.barcode : (item.variant_barcode ?? null);
        sItems[key].promo_discount_unit = item.promo_discount_unit ?? 0;

        if(!isQuantityValid(sItems[key])){
            alert(formatInsufficientMessage(item));
            delete sItems[key];
            return;
        }

        count++;
        loadItems(); 
        document.getElementById('add_item').value = '' ;
        $('#add_item').focus();
        playSuccessSound();
    }

    var old_row_qty=0;
    var old_row_price = 0;
    var old_row_w_price = 0;

    $(document).on('focus','.iQuantity',function () {
        old_row_qty = $(this).val();
    })
    .on('change','.iQuantity',function () {
        var row = $(this).closest('tr');
        if(!is_numeric($(this).val()) || parseFloat($(this).val()) < 0){
            $(this).val(old_row_qty);
            alert('wrong value');
            return;
        }

        var newQty = parseFloat($(this).val()),
            item_id = row.attr('data-item-id');

        if(!isQuantityValid(sItems[item_id], newQty)){
            alert(formatInsufficientMessage(sItems[item_id]));
            $(this).val(old_row_qty);
            return;
        }

        sItems[item_id].qnt= newQty;
        loadItems();
        NetAfterDiscount();

    });


    $(document)
        .on('focus','.iPrice',function () {
            old_row_price = $(this).val();
        })
        .on('change','.iPrice',function () {
            var row = $(this).closest('tr');
            if(!is_numeric($(this).val()) || parseFloat($(this).val()) < 0){
                $(this).val(old_row_price);
                alert('wrong value');
                return;
            }

            var newPrice = parseFloat($(this).val()) || 0,
                item_id = row.attr('data-item-id');

            sItems[item_id].discount = 0;
            row.find('.iDiscount').val('0');
            sItems[item_id].original_price = newPrice;
            updateItemPricingValues(sItems[item_id], newPrice);
            loadItems();

        }); 

        $(document).on('change','.selectUnit',function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
            var previousUnit = sItems[item_id].selected_unit_id;
            var previousFactor = sItems[item_id].unit_factor;
            var selectedPrice = parseFloat($(this).find(':selected').data('price')) || sItems[item_id].original_price || 0;
            var factor = parseFloat($(this).find(':selected').data('factor')) || 1;

            sItems[item_id].original_price = selectedPrice;
            var effectivePrice = Math.max(selectedPrice - (sItems[item_id].discount ?? 0), 0);
            updateItemPricingValues(sItems[item_id], effectivePrice);
            sItems[item_id].selected_unit_id = $(this).val();
            sItems[item_id].unit_factor = factor;
            if(!isQuantityValid(sItems[item_id])){
                alert(formatInsufficientMessage(sItems[item_id]));
                sItems[item_id].selected_unit_id = previousUnit;
                sItems[item_id].unit_factor = previousFactor;
                $(this).val(previousUnit);
                return;
            }
            row.find('.unitFactor').val(factor);
            loadItems();
            NetAfterDiscount();
        }); 

        $(document).on('change','.iDiscount',function () {
            var row = $(this).closest('tr'); 
       
            var newDiscount = parseFloat($(this).val()) || 0,
                item_id = row.attr('data-item-id');   

            if(newDiscount < 0){
                newDiscount = 0;
                $(this).val(newDiscount.toFixed(2));
            }

            var maxDiscount = sItems[item_id].original_price ?? 0;
            if(newDiscount > maxDiscount){
                newDiscount = maxDiscount;
                $(this).val(maxDiscount.toFixed(2));
            }

            sItems[item_id].discount = newDiscount;
            var effectivePrice = Math.max((sItems[item_id].original_price ?? 0) - newDiscount, 0);
            updateItemPricingValues(sItems[item_id], effectivePrice);
            loadItems();
        }); 

        $(document).on('input','.itemNote',function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
            sItems[item_id].note = $(this).val();
        });

    function is_numeric(mixed_var) {
        var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        return (
            (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -1)) &&
            mixed_var !== '' &&
            !isNaN(mixed_var)
        );
    }

    function updateItemPricingValues(item, effectivePrice){
        effectivePrice = Math.max(effectivePrice || 0, 0);
        var tax_rate = parseFloat(item.tax_rate ?? 0) || 0;
        var tax_excise = parseFloat(item.tax_excise ?? 0) || 0;
        var vatAmount = effectivePrice * (tax_rate/100);
        var exciseAmount = effectivePrice * (tax_excise/100);
        item.price_withoute_tax = effectivePrice;
        item.item_tax = vatAmount + exciseAmount;
        item.price_with_tax = effectivePrice + item.item_tax;
    }

    function escapeHtml(value){
        if(value === null || value === undefined){
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

  function loadItems(){

        var total = 0 ;
        var qnt_val = 0 ; 
        var items_count_val = 0 ; 
        var first_total_val = 0 ;  
        var tax_total_val =0 ;
        var discount_total_val = 0;
        var net_sales_val = 0 ; 

        $('#sTable tbody').empty();
        $.each(sItems,function (i,item) {
            console.log(item);

            var newTr = $('<tr data-item-id="'+i+'">');
            var variantTag = '';
            if(item.variant_color || item.variant_size){
                variantTag = '<div class="small text-info">'+escapeHtml(item.variant_color ?? '')+' '+escapeHtml(item.variant_size ?? '')+'</div>';
            }
            var nameLabel = '<div><strong>'+escapeHtml(item.name ?? '')+'</strong><br><small class="text-muted">'+escapeHtml(item.code ?? '')+'</small>'+variantTag+'</div>';
            var noteInput = '<div class="mt-2"><input type="text" class="form-control form-control-sm itemNote" name="item_note[]" value="'+escapeHtml(item.note ?? '')+'" placeholder="'+escapeHtml(itemNotePlaceholder)+'"></div>';
            var tr_html ='<td>'
                +'<input type="hidden" name="product_id[]" value="'+(item.product_id ?? item.id)+'">'
                +'<input type="hidden" name="variant_id[]" value="'+escapeHtml(item.variant_id ?? '')+'">'
                +'<input type="hidden" name="variant_color[]" value="'+escapeHtml(item.variant_color ?? '')+'">'
                +'<input type="hidden" name="variant_size[]" value="'+escapeHtml(item.variant_size ?? '')+'">'
                +'<input type="hidden" name="variant_barcode[]" value="'+escapeHtml(item.variant_barcode ?? '')+'">'
                + nameLabel + noteInput + '</td>';
                tr_html +='<td><span class="badge badge-light">'+Number(item.available_qty ?? 0)+'</span></td>';
                tr_html +='<td><input type="text" readonly class="form-control" value="'+Number(item.cost ?? 0).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly class="form-control" value="'+Number(item.last_sale_price ?? 0).toFixed(2)+'"></td>';
                var unitSelect = '<select class="form-control selectUnit" name="unit_id[]">';
                if(item.units_options && item.units_options.length){
                    item.units_options.forEach(function(u){
                        var selected = u.unit_id == item.selected_unit_id ? 'selected' : '';
                        unitSelect += '<option value="'+u.unit_id+'" data-price="'+u.price+'" data-factor="'+(u.conversion_factor ?? 1)+'" '+selected+'>'+u.unit_name+'</option>';
                    });
                }
                unitSelect += '</select><input type="hidden" name="unit_factor[]" class="unitFactor" value="'+(item.unit_factor ?? 1)+'">';
                var qtyInput = '<div class="mt-2"><input type="number" step="0.01" min="0" class="form-control form-control-sm iQuantity" name="qnt[]" value="'+item.qnt+'"></div>';

                tr_html +='<td>'+unitSelect+'</td>';
                tr_html +='<td>'+qtyInput+'</td>';
                tr_html +='<td><input type="number" step="0.01" class="form-control iPrice" name="price_unit[]" value="'+Number(item.price_withoute_tax ?? 0).toFixed(2)+'"><input type="hidden" name="original_price[]" value="'+Number(item.original_price ?? 0).toFixed(2)+'"></td>';
                tr_html +='<td><input type="number" class="form-control iDiscount" name="discount_unit[]" value="'+Number(item.discount ?? 0).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control iPriceWTax" name="price_with_tax[]" value="'+Number(item.price_with_tax ?? 0).toFixed(2)+'"></td>'; 
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="total[]" value="'+((Number(item.price_withoute_tax ?? 0))*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="tax[]" value="'+(Number(item.item_tax ?? 0)*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExcise" name="tax_excise[]" value="'+(((Number(item.tax_excise ?? 0)/100)*(Number(item.price_withoute_tax ?? 0)))*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="net[]" value="'+(((Number(item.price_withoute_tax ?? 0)) + Number(item.item_tax ?? 0))*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxRate" name="tax_rate[]" value="'+item.tax_rate+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExciseRate" name="tax_excise_rate[]" value="'+item.tax_excise+'"></td>';
                tr_html +=`<td> <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+i+' ">
                                    <i class="fa fa-close"></i>
                                </button>
                            </td>`;

           
            newTr.html(tr_html);
            newTr.appendTo('#sTable');

            items_count_val += Number(item.qnt ?? 0);
            first_total_val += Number(item.price_withoute_tax ?? 0) * item.qnt;
            tax_total_val +=  Number(item.item_tax ?? 0)  * Number(item.qnt)  ;
            discount_total_val += Number(item.discount ?? 0) * Number(item.qnt ?? 0);
			net_sales_val += (((Number(item.price_withoute_tax ?? 0)) + Number(item.item_tax ?? 0))*item.qnt);


        });

        document.getElementById('discount-text').innerHTML = (discount_total_val).toFixed(2);
        document.getElementById('total-text').innerHTML = first_total_val.toFixed(2);
        document.getElementById('tax-text').innerHTML =  tax_total_val.toFixed(2);
        document.getElementById('net-text').innerHTML =  net_sales_val.toFixed(2);

        document.getElementById('items_count').value = items_count_val.toFixed(2) ;
        document.getElementById('first_total').value = first_total_val.toFixed(2); 
        document.getElementById('tax_total').value = tax_total_val.toFixed(2);
        document.getElementById('discount_total').value = discount_total_val.toFixed(2);
        document.getElementById('net_sales').value = net_sales_val.toFixed(2);
        
        $("#net_after_discount").val(net_sales_val.toFixed(2)); 

        let discount_total = $("#discount_total").val();

        if( discount_total > 0 ){ 
            $('#discount_input').attr({readOnly:true});
            $('#discount_input').val(0);
        }else{
            $('#discount_input').attr({readOnly:false});    
        }

        NetAfterDiscount();
    }

        function addPayments(remain) {  
            var route = '{{route('show_sales_payments',":remain")}}';
                route = route.replace(":remain",remain); 
            
            $.get( route, function(data){
                $(".show_modal").html(data);
                setupPaymentModal();
                $('#paymentsModal').modal({backdrop: 'static', keyboard: false} ,'show');
            });
        }

    function NetAfterDiscount(){

        var net = parseFloat($('#net_sales').val()) || 0; 
        var discount_type = $('#discount_type').val();  
        var discountInput = parseFloat($('#discount_input').val()) || 0; 
        var discountAmount = discount_type == 2 ? (net * (discountInput/100)) : discountInput;
        discountAmount = Math.min(discountAmount, net);
        var net_after_discount = net - discountAmount;
 
        $("#discount_amount").val(discountAmount.toFixed(2));
        $("#net_after_discount").val(net_after_discount.toFixed(2)); 

        if((paymentMethodSelect.val() || '').toLowerCase() === 'cash'){
            syncCashBoxInputs();
        }

    } 
</script>
<script src="{{ asset('js/offline-pos.js') }}"></script>

@endsection 
