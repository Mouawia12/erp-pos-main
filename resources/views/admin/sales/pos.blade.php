@extends('admin.layouts.master')
@section('title') [ {{ __('main.pos')}} ]@endsection   
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
@can('اضافة مبيعات')  
 <style>
.pos-page {
    position: relative;
    width: 100%;
    min-height: 100vh;
    padding: 0 15px 60px;
}

.page.active,.pos-page .page {
    margin-left: 0;
    width: 100%;
}
.pos h4{ 
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 3%;
    margin: 0;
}
.input-group.wide-tip {
    border: 2px solid #ecf0fa;
    padding: 1%;
    border-radius: 10px;
    background: #ecf0fa;
}
.modal-content{
    border: unset !important;
} 
.modal-header {
    border-bottom: 0 !important;
}
section.pos-section {
    padding: 2px 0
}

.pos-page .table-fixed {
    margin-bottom: 0
}
#product-table_wrapper.dataTables_wrapper {
    margin-top: 0;
    padding: 0
}

table#product-table.dataTable {
    border-collapse: separate!important;
    margin: 15px 0!important
}

#product-table_paginate {
    float: right
}

#product-table tr:last-child td:hover {
    border-bottom: 1px solid #7c5cc4
}

#product-table_paginate .page-link {
    line-height: 1
}

#product-table td {
    border: none;
    border-right: 1px solid #e4e6fc;
    border-bottom: 1px solid #e4e6fc
}

.table-bordered td,.table-bordered th {
    border-color: #e4e6fc
}

#product-table tr td:first-child {
    border-left: 1px solid #e4e6fc
}

#product-table tr:first-child td {
    border-top: 1px solid #e4e6fc
}

#product-table td:hover {
    border: 1px solid #7c5cc4;
    color: #7c5cc4
}

#product-table tr:first-child td:hover {
    border-top: 1px solid #7c5cc4
}

#product-table tr td:first-child:hover {
    border-left: 1px solid #7c5cc4
}

#product-table td p {
    margin: 15px 0 0
}

#product-table td:hover p {
    color: #7c5cc4
}

input.form-control.pos-input,
select.form-control.pos-input,
textarea.form-control.pos-input {
    min-height: 38px;
    font-size: 14px;
}
.pos-categories .pos-cat-btn {
    border: none;
    border-radius: 20px;
    padding: 6px 14px;
    margin: 4px;
    cursor: pointer;
    background: #f0f4ff;
    color: #5a5c69;
}
.pos-categories {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}
.pos-categories .pos-cat-btn.active {
    background: #7c5cc4;
    color: #fff;
}
.pos-customer-lookup {
    position: relative;
}
.pos-customer-results {
    position: absolute;
    top: 100%;
    right: 0;
    left: 0;
    z-index: 20;
    background: #fff;
    border: 1px solid #e4e6fc;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(90, 92, 105, 0.15);
    max-height: 230px;
    overflow-y: auto;
}
.pos-customer-results .customer-lookup-item {
    font-size: 13px;
}
.product-img {
    margin-bottom: 0;
    padding: 15px 7px 0;
    text-align: center;
    text-transform: capitalize;
    width: 20%
}
.btn-default.minus,.btn-default.minus:focus,.btn-default.plus,.btn-default.plus:focus {
    background-color: #00b9ff;  
    border:1px solid #d6deff;
    color:#fff;
}
span.fa.fa-minus,span.fa.fa-plus {  font-weight:700;}
label.total {
    width: 100%;
    padding: 2%;
    margin: 0;
    text-align: center;
    font-size: 16px !important;
    background: #d6deff;
    color: #7c5cc4;
    font-weight: 700;
}
@if(isset($posMode) && $posMode === 'touch')
.pos-page.pos-touch .btn {
    min-height: 48px;
    font-size: 16px;
}
.pos-page.pos-touch input,
.pos-page.pos-touch select {
    min-height: 44px;
    font-size: 16px;
}
.pos-page.pos-touch .table td,
.pos-page.pos-touch .table th {
    font-size: 15px;
}
@endif
</style> 
<div class="pos-page {{ isset($posMode) && $posMode === 'touch' ? 'pos-touch' : 'pos-classic' }}"> 
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
    <div class="row row-lg">
        <section class="forms pos-section col-xl-12">
        
            <form id="form" method="POST" action="{{ route('store_sale') }}"
                enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" id="POS" name="POS" value="1"/> 
            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{ $defaultWarehouseId ?? optional($warehouses->first())->id }}">
            <input type="hidden" id="discount_amount" name="discount" value="0">
            <input type="hidden" id="net_sales" value="0">
            <input hidden type="datetime-local" id="bill_date" name="bill_date" /> 
            <input type="hidden" id="invoice_no" name="invoice_no" /> 

            <div class="card shadow mb-4 col-xl-12"> 
                <div class="row mt-1 mb-1 text-center justify-content-center align-content-center">  
                    <button type="button" class="btn btn-md btn-success m-1" id="payment" tabindex="-1">
                        <i class="fas fa-money-bill text-white"></i>
                        <strong> {{__('main.save_payment_btn')}} ( F9 ) </strong> 
                    </button>  
                    <button type="button" class="btn btn-md btn-info m-1" id="print" tabindex="-1" >
                        <i class="fa fa-print text-white"></i>
                        <strong> {{__('main.print_last')}} ( F2 )</strong> 
                    </button>  
                    <a href="{{route('pos')}}" class="btn btn-md btn-warning m-1" id="new" target="_blank">
                        <i class="fa fa-plus text-white"></i>
                        <strong> {{__('تعليق الحالية وفتح جديدة')}} </strong> 
                    </a>  
                    <button type="button" class="btn btn-md btn-danger m-1" id="cancel_entry" tabindex="-1">
                        <i class="fas fa-trash text-white"></i>
                        <strong>  {{__('main.cancel_btn')}} ( F3 )</strong> 
                    </button>  
                </div>  
                @if(!empty($defaultWarehouseId))
                <div class="text-center text-muted mb-2">
                    {{ __('main.warehouse') }} :
                    {{ optional($warehouses->firstWhere('id', $defaultWarehouseId))->name ?? '--' }}
                </div>
                @endif
                <div class="row"> 
                    <div class="card shadow mb-4 col-6">
                        <div class="card-body  px-0 pt-0 pb-2">
                            <div id="posCategories" class="pos-categories d-none mb-3"></div>
                            <div class="table-container"> 
                            </div> 
                        </div> 
                    </div>
                    <div class="card shadow mb-4 col-6">
                        <div class="card-body  px-0 pt-0 pb-2">
                            <br>
                            <div class="row">   
                                <div class="col-lg-12">
                                    <div class="form-group mb-2 pos-customer-lookup">
                                        <input type="text" class="form-control pos-input" id="customer_quick_lookup" autocomplete="off" placeholder="{{ __('main.pos_customer_quick_hint') }}">
                                        <div id="customer_lookup_results" class="pos-customer-results list-group d-none"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4" >  
                                    <div class="form-group">  
                                        <select id="customer_id" name="customer_id" class="js-example-basic-single w-100 pos-input" required>
                                            @foreach($vendors as $vendor)
                                                <option value="{{$vendor -> id}}"
                                                    data-default-discount="{{$vendor->default_discount ?? 0}}"
                                                    data-phone="{{$vendor->phone ?? ''}}"
                                                    data-address="{{$vendor->address ?? ''}}"
                                                    data-tax="{{$vendor->tax_number ?? ''}}"
                                                    data-name="{{$vendor->name}}">
                                                    {{$vendor -> name}} @if($vendor->phone) - {{$vendor->phone}} @endif @if($vendor->address) - {{$vendor->address}} @endif @if($vendor->tax_number) - {{$vendor->tax_number}} @endif
                                                </option>
                                            @endforeach
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-lg-4" > 
                                    <div class="form-group">   
                                        <input id="customer_name" name="customer_name" class="form-control pos-input" type="text" placeholder="{{__('main.customer_name')}}">  
                                    </div> 
                                </div>
                                <div class="col-lg-4">  
                                    <div class="form-group">   
                                        <input id="customer_phone" name="customer_phone" class="form-control pos-input" type="text" placeholder="{{__('main.customer_phone')}}">  
                                    </div>   
                                </div>
                                <div class="col-lg-4">  
                                    <div class="form-group">   
                                        <input id="customer_address" name="customer_address" class="form-control pos-input" type="text" placeholder="{{ __('main.address') }}">  
                                    </div>   
                                </div>
                                <div class="col-lg-4">  
                                    <div class="form-group">   
                                        <input id="customer_tax_number" name="customer_tax_number" class="form-control pos-input" type="text" placeholder="{{ __('main.tax_number') }}">  
                                    </div>   
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        @php
                                            $defaultType = $defaultInvoiceType ?? ($settings->default_invoice_type ?? 'simplified_tax_invoice');
                                            $defaultServiceMode = old('service_mode', 'dine_in');
                                        @endphp
                                        <label>{{ __('main.invoice_type') }}</label>
                                        <select class="form-control pos-input" name="invoice_type" id="invoice_type">
                                            <option value="simplified_tax_invoice" @if($defaultType=='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                            <option value="tax_invoice" @if($defaultType=='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                            <option value="non_tax_invoice" @if($defaultType=='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>{{ __('main.service_mode') }}</label>
                                        <select class="form-control pos-input" name="service_mode" id="pos_service_mode">
                                            <option value="dine_in" @if($defaultServiceMode === 'dine_in') selected @endif>{{ __('main.service_mode_dine_in') }}</option>
                                            <option value="takeaway" @if($defaultServiceMode === 'takeaway') selected @endif>{{ __('main.service_mode_takeaway') }}</option>
                                            <option value="delivery" @if($defaultServiceMode === 'delivery') selected @endif>{{ __('main.service_mode_delivery') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 pos-service-meta">
                                    <div class="form-group mb-0">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="toggleReservation">
                                            <label class="custom-control-label" for="toggleReservation">{{ __('main.enable_reservation') }}</label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="reservation_enabled" id="pos_reservation_enabled" value="0">
                                </div>
                                <div class="col-lg-12 d-none pos-service-meta" id="pos_reservation_fields">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('main.session_location') }}</label>
                                                <input type="text" class="form-control pos-input" name="session_location" id="pos_session_location" placeholder="{{ __('main.session_location') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('main.reservation_time') }}</label>
                                                <input type="datetime-local" class="form-control pos-input" id="reservation_time" name="reservation_time">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>{{ __('main.guests_count') }}</label>
                                                <input type="number" min="1" class="form-control pos-input" id="reservation_guests" name="reservation_guests" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="session_type" id="pos_session_type">
                            </div>  
                            <div class="row" > 
                                <div class="col-md-12" id="sticker">
                                    <div class="well well-sm">   
                                        <div class="form-group">
                                            <div class="input-group wide-tip" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-3x fa-barcode addIcon"></i>
                                                </div>
                                                <input type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input" id="add_item" placeholder="{{__('main.add_item_hint')}}" autocomplete="off">
                                            </div>
                                            <ul class="suggestions" id="products_suggestions" style="display: block">
                                            </ul>  
                                        </div>
                                    </div>
                                </div>  
                            </div>  
                            <div class="row"> 
                                <div class="col-xl-12"> 
                                    <div class="card mb-4"> 
                                        <div class="card-body px-0 pt-0 pb-2">
                                            <div class="table-responsive hoverable-table">
                                                <table class="display w-100 table-hover table-striped order-list text-center" id="sTable"> 
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center col-md-3">{{__('main.item_name_code')}}</th>
                                                            <th class="text-center">{{__('main.available_qty')}}</th>
                                                            <th class="text-center">{{__('main.cost')}}</th>
                                                            <th class="text-center">{{__('main.last_sale_price')}}</th>
                                                            <th class="text-center col-md-2">{{__('main.prices')}}</th>
                                                            <th class="text-center col-md-3">{{__('main.quantity')}} </th>
                                                            <th class="text-center">{{__('main.total')}}</th>
                                                            <th class="text-center">...</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody"></tbody>
                                                    <tfoot>
                                                        <th colspan="5" class="alert alert">
                                                            {{__('main.sum')}}
                                                        </th>
                                                        <th class="text-center"> 
                                                            <span id="items_count"></span>  
                                                        </th> 
                                                        <th class="text-center"> 
                                                            <span id="total_with_tax"></span> 
                                                        </th>  
                                                        <th></th>
                                                    </tfoot>
                                                </table> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body py-3">
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="total w-100 mb-0">{{__('main.total.final')}} <span id="totalBig"><strong>0.00</strong></span></label>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mb-1">{{ __('main.discount_type') }}</label>
                                                    <select class="form-control pos-input" name="discount_type" id="discount_type">
                                                        <option value="1">{{ __('main.discount_amount') }}</option>
                                                        <option value="2">{{ __('main.discount_percent') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mb-1">{{ __('main.discount') }}</label>
                                                    <input type="number" step="any" class="form-control pos-input" id="discount_input" value="0">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mb-1">{{ __('main.invoice.total') }}</label>
                                                    <input type="text" class="form-control pos-input" id="net_after_discount" readonly value="0">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="mb-1">{{ __('main.notes') }}</label>
                                                    <textarea class="form-control pos-input" name="notes" rows="2" placeholder="{{__('main.notes')}}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </div>  
                    </div>
                </div> 
            </div>
            </form> 
        
        </section> 
    </div> 
</div> 

<div class="show_modal">

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
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.notfound')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row text-center">
                    <div class="col-6 text-center" style="display: block;margin: auto">
                        <button type="button" class="btn btn-labeled btn-primary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-check"></i></span>
                            {{__('main.ok_btn')}}
                        </button>
                    </div>
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
@endcan 
@endsection 
@section('js')
<script type="text/javascript">
    var suggestionItems = {};
    var sItems = {};
    var count = 1;
    var itemKey = 1;
    var Bill = null ;
    var product_row_number = 3;
    var posProductsCatalog = [];
    var posActiveCategoryKey = 'all';
    var posCustomerLookupIndex = [];
    const allowNegativeStock = {{ !empty($allowNegativeStock) ? 'true' : 'false' }};
    const insufficientTemplate = "{{ __('main.insufficient_stock', ['item' => '__ITEM__']) }}";
    const posCategoryLabels = @json([
        'all' => __('main.pos_categories_all'),
        'uncategorized' => __('main.pos_categories_uncategorized'),
    ]);
    const posNoProductsText = @json(__('main.pos_no_products'));
    const posCustomerNoResultsText = @json(__('main.pos_customer_no_results'));
    const posProductImageBase = "{{ env('APP_URL') }}/uploads/items/images/";

    function togglePosServiceMeta(){
        const control = $('#pos_service_mode');
        if(!control.length){
            return;
        }
        const mode = control.val();
        const showFields = mode === 'dine_in';
        $('.pos-service-meta').toggleClass('d-none', !showFields);
        if(!showFields){
            $('#pos_session_location').val('');
            $('#pos_session_type').val('');
            $('#toggleReservation').prop('checked', false);
            handleReservationToggle();
        }
    }

    function handleReservationToggle(){
        const toggle = $('#toggleReservation');
        if(!toggle.length){
            return;
        }
        const enabled = toggle.is(':checked');
        $('#pos_reservation_fields').toggleClass('d-none', !enabled);
        $('#pos_reservation_enabled').val(enabled ? 1 : 0);
        $('#pos_session_type').val(enabled ? 'reservation' : '');
        if(!enabled){
            $('#reservation_time').val('');
            $('#reservation_guests').val('');
        }
    }

    $('#pos_service_mode').on('change', togglePosServiceMeta);
    togglePosServiceMeta();

    $('#payment').click(function (){
        submit();
    });

    $('#print').click(function (){
        printBill();
    });

    $('#cancel_entry').click(function (){
        cancelEntry();
    });

    $(document).keydown(function(event) {
        console.log(event.keyCode);
        if (event.keyCode == 120) {
            submit();
        }
        if (event.keyCode == 113) {
            printBill();
        }
        if (event.keyCode == 114) {
            cancelEntry();
        }
    });

    function submit() {
        var rows =  0 ; 
        rows = ($('#sTable tbody tr').length);
        console.log(rows); 

        var client = document.getElementById('customer_id').value ;
        var warehouse_id = document.getElementById('warehouse_id').value ;
        if(client > 0 && warehouse_id > 0) {
            if (rows > 0){
                document.getElementById('form').submit(); 
            } else {
                alert($('<div>{{trans('يجب تحديد تفاصيل واصناف الفاتورة')}}</div>').text());
            }
        } else {
            alert($('<div>{{trans('يجب تحديد العميل والمستودع')}}</div>').text());
        } 
    }
    
    function printBill(){  
        window.location = "{{ route('print_last_pos') }}"; 
    }

    function cancelEntry(){
        $('#sTable tbody').empty();
        document.getElementById('items').innerHTML = 0;
        document.getElementById('items_count').innerHTML = 0; 
        document.getElementById('total').innerHTML = 0;
        document.getElementById('total_with_tax').innerHTML = 0;
        document.getElementById('totalBig').innerHTML = 0;
        document.getElementById('net_sales').value = 0;
        document.getElementById('net_after_discount').value = 0;
        $('#discount_input').val(0);
        $('#discount_amount').val(0);
        updatePosDiscountSummary();
        sItems = {};
        count = 1;
        Bill = null ;
        suggestionItems = {};
    }

    $(document).ready(function() { 

        setupNegativeStockQuickEnable();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 

        buildCustomerLookupIndex();
        const customerLookupInput = $('#customer_quick_lookup');
        const customerLookupResults = $('#customer_lookup_results');
        if(customerLookupInput.length){
            customerLookupInput.on('input focus', function(){
                renderCustomerLookupResults($(this).val());
            });
        }
        $(document).on('click', '.customer-lookup-item', function(){
            const clientId = $(this).data('customer-id');
            if(clientId){
                $('#customer_id').val(clientId).trigger('change');
            }
            if(customerLookupInput.length){
                customerLookupInput.val($(this).data('label') || '');
            }
            customerLookupResults.addClass('d-none').empty();
        });
        $(document).on('click', function(e){
            if(!$(e.target).closest('.pos-customer-lookup').length){
                customerLookupResults.addClass('d-none');
            }
        });
        $(document).on('click', '#posCategories .pos-cat-btn', function(e){
            e.preventDefault();
            applyPosCategoryFilter($(this).data('category-key') || 'all');
        });
        handleReservationToggle();
        $('#toggleReservation').on('change', handleReservationToggle);

        getBillNo();
        getProductListImg();
        $('.open-toggle')[0].click();
  
        $(document).on('click', '#payment_btn', function (){  
            const money = $('#money').val();
            let cash = $('#cash').val();
            let visa = $('#visa').val();
            if(Number(money) == ( Number(cash) + Number(visa) ) ){
                document.getElementById('sales_payments_pos').submit(); 
            } else {
                alert('لابد ان يكون مجموع المبلغين مساويا لاجمالى الفاتورة');
            } 
        });

        $(document).on('change', '#cash', function () { 
            const money = $('#money').val();
            var visa = (Number(money) - Number(this.value)).toFixed(2);
            if(visa > 0 ){ 
                $('#visa').val(visa );
            }else{
                $('#visa').val(0);
            } 
        });
        
        $(document).on('keyup', '#cash', function () {
            const money = $('#money').val();
            var visa = (Number(money) - Number(this.value)).toFixed(2);
            if(visa > 0 ){ 
                $('#visa').val(visa );
            }else{
                $('#visa').val(0);
            } 
        }); 

        $('#warehouse_id').change(function (){ 
            getBillNo();
            $('#products_suggestions').empty();
            $('#sTable tbody').empty();
            $('#items_count').empty(); 
            $('#total_with_tax').empty();  
            $('#totalBig').empty();  
            $('#net_sales').val(0);
            $('#net_after_discount').val(0);
            $('#discount_input').val(0);
            $('#discount_amount').val(0);
            updatePosDiscountSummary();
            suggestionItems = {};
            sItems = {};
            count = 1; 
            product_row_number = 3;
            getProductListImg();
        });

        $.ajax({
            type: 'get',
            url: 'getLastSalesBill',
            dataType: 'json', 
            success: function (response) {
                console.log(response); 
                if (response) {

                    if (response.pos == 1 ) {
                        if (response.paid == 0) {
                            Bill = response;
                            addPayments(Bill.id);
                        } else {
                            Bill = null ;
                        }

                    } else {
                        Bill = null;
                    } 
                } else {
                    Bill = null;
                }
            }
        });

        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null); 
        document.getElementById('bill_date').value = now.toISOString().slice(0, -1);

        $('#customer_id').on('change', function(){
            const selected = $(this).find(':selected');
            const defaultDiscount = parseFloat(selected.data('default-discount')) || 0;
            const phone = selected.data('phone') || '';
            const address = selected.data('address') || '';
            const taxNumber = selected.data('tax') || '';
            const name = selected.data('name') || selected.text().trim();
            $('#customer_phone').val(phone);
            $('#customer_address').val(address);
            $('#customer_tax_number').val(taxNumber);
            if(!$('#customer_name').val()){
                $('#customer_name').val(name);
            }
            if(customerLookupInput && customerLookupInput.length){
                const summaryParts = [];
                if(name){
                    summaryParts.push(name);
                }
                if(phone){
                    summaryParts.push(phone);
                }
                customerLookupInput.val(summaryParts.join(' - '));
            }
            if(customerLookupResults && customerLookupResults.length){
                customerLookupResults.addClass('d-none');
            }
            if(defaultDiscount > 0){
                $('#discount_input').val(defaultDiscount);
            } else {
                $('#discount_input').val(0);
            }
            updatePosDiscountSummary();
        });
       
        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val());
        });

        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val());
        });

        $('#discount_type').on('change', function(){
            updatePosDiscountSummary();
        });

        $('#discount_input').on('keyup change', function(){
            updatePosDiscountSummary();
        });

        updatePosDiscountSummary();
        $('#customer_id').trigger('change');

        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#paymentsPosModal').modal("hide");
            id = 0 ;
        });

        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });

        $(document).on('click' , '.deleteBtn' , function (event) {
            var row = $(this).parent().parent().index(); 
            var row1 = $(this).closest('tr');
            var item_id = row1.attr('data-item-id');
            delete sItems[item_id];
            loadItems(); 
            var audio = $("#mysoundclip2")[0];
            audio.play();
        });

        $(document).on('click', '.select_product', function () {
        
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        }); 

        function getProductListImg(){

            $(".table-container").children().remove();
            const id = document.getElementById('warehouse_id').value ;
            var route = '{{ route('get_product_list_img',":id") }}';  
            route = route.replace(":id",id);
    
            $.get( route, function( data ) {
                populateProduct(data);
            });
        }


        $(document).on('click', '.sound-btn', function () { 
            var item_id =  $(this).data('id');     
            addItemToTable(suggestionItems[item_id]);  
            var audio = $("#mysoundclip1")[0];
            audio.play();
        });
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

    function addPayments(id) {  
        var route = '{{route('add_sales_payments',":id")}}';
            route = route.replace(":id",id); 
        
        $.get( route, function(data){
            $( ".show_modal" ).html( data );   
            $('#paymentsPosModal').modal({backdrop: 'static', keyboard: false} ,'show');
        });
    }

    function view_purchase(id) {
        var route = '{{route('preview_sales',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

    function getBillNo(){
        const id = document.getElementById('warehouse_id').value ;
        let invoice_no = document.getElementById('invoice_no');
      
       var url = '{{route('get.sale.pos.no',[2,":id"])}}';
            url = url.replace(":id",id);
        $.ajax({
            type:'get',
            url:url,
            dataType: 'json', 
            success:function(response){
                console.log(response); 
                if(response){
                    invoice_no.value = response ;
                } else {
                    invoice_no.value = '' ;
                }
            }
        });
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
                        var audio = $("#mysoundclip2")[0];
                        audio.play();
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

    function addItemToTable(item){
        if(count == 1){
            sItems = {};
        }

        var isDuplicate = Object.values(sItems).some(function(existing){
            return existing.product_id === item.id;
        });

        var price = item.price;
        if(item.selected_variant && item.selected_variant.price){
            price = item.selected_variant.price;
        }

        if(item.promo_discount_unit){
            price = Math.max(price - Number(item.promo_discount_unit), 0);
        }

        var taxType = item.tax_method;
        var taxRate = item.total_tax_rate ? Number(item.total_tax_rate) : (item.tax_rate == 1 ? 0 : 15);
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
            priceWithTax = price;
            priceWithoutTax = (price / (1+(taxRate/100)));
            itemTax = priceWithTax - priceWithoutTax;
        }else{
            itemTax = price * (taxRate/100);
            priceWithoutTax = price;
            priceWithTax = price + itemTax;
        } 
        var Excise = item.tax_excise;
        var taxExcise = 0;
        if(Excise > 0){    
            taxExcise = (priceWithoutTax * (Excise/100));
            itemTax = itemTax + taxExcise;
        }
        
        var key = (item.selected_variant ? ('v'+item.selected_variant.id) : ('p'+item.id)) + '_' + itemKey;
        itemKey++;

        sItems[key] = item;
        sItems[key].product_id = item.id;
        sItems[key].price_with_tax = priceWithTax;
        sItems[key].price_withoute_tax = priceWithoutTax;
        sItems[key].item_tax = itemTax;
        sItems[key].tax_rate = taxRate;
        sItems[key].tax_excise = Excise;
        sItems[key].qnt = 1;
        sItems[key].available_qty = item.qty ? Number(item.qty) : 0;
        sItems[key].cost = item.cost ? Number(item.cost) : 0;
        sItems[key].last_sale_price = item.last_sale_price ? Number(item.last_sale_price) : 0;
        sItems[key].selected_unit_id = defaultUnit;
        sItems[key].unit_factor = defaultFactor;
        sItems[key].units_options = item.units_options ?? [];
        sItems[key].variant_id = item.selected_variant ? item.selected_variant.id : null;
        sItems[key].variant_color = item.selected_variant ? item.selected_variant.color : null;
        sItems[key].variant_size = item.selected_variant ? item.selected_variant.size : null;
        sItems[key].variant_barcode = item.selected_variant ? item.selected_variant.barcode : null;
        sItems[key].promo_discount_unit = item.promo_discount_unit ?? 0;

        if(!isQuantityValid(sItems[key])){
            alert(formatInsufficientMessage(item));
            delete sItems[key];
            return;
        }

        if(isDuplicate){
            alert('{{ __('main.duplicate_item_warning') }}');
        }

        count++;
        loadItems(); 
        document.getElementById('add_item').value = '' ;
        $('#add_item').focus();
        var audio = $("#mysoundclip1")[0];
        if(audio && audio.play){
            try { audio.play(); } catch (e) {}
        }
    }

    var old_row_qty=0;
    var old_row_price = 0;
    var old_row_w_price = 0;

    $(document)
        .on('focus','.qty',function () {
            old_row_qty = $(this).val();
        })
        .on('change','.qty',function () {
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

            var newPrice = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');

            var item_tax =sItems[item_id].item_tax;
            var tax_rate = sItems[item_id].tax_rate;
            var tax_excise = sItems[item_id].tax_excise;
            var priceWithTax = newPrice;
            if(item_tax > 0){
                priceWithTax = newPrice + (newPrice * (tax_rate/100));
                item_tax = (newPrice * (tax_rate/100)) + (newPrice * (tax_excise/100));
            }
            sItems[item_id].price_withoute_tax= newPrice;
            sItems[item_id].price_with_tax= priceWithTax;
            sItems[item_id].item_tax= item_tax;
            loadItems();

        });

    $(document)
        .on('focus','.iPriceWTax',function () {
            old_row_w_price = $(this).val();
        })
        .on('change','.iPriceWTax',function () {
            var row = $(this).closest('tr');
            if(!is_numeric($(this).val()) || parseFloat($(this).val()) < 0){
                $(this).val(old_row_w_price);
                alert('wrong value');
                return;
            }

            var newPrice = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');

            var item_tax =sItems[item_id].item_tax;
            var tax_rate = sItems[item_id].tax_rate;
            var tax_excise = sItems[item_id].tax_excise;
            var priceWithoutTax = newPrice;
            if(item_tax > 0){ 
                priceWithoutTax = newPrice / (1 + (tax_rate/100)+(tax_excise/100));
                item_tax = (priceWithoutTax * (tax_rate/100)) + (priceWithoutTax * (tax_excise/100));
            }
            sItems[item_id].price_withoute_tax= priceWithoutTax;
            sItems[item_id].price_with_tax= newPrice;
            sItems[item_id].item_tax= item_tax;
            loadItems();

        });

        $(document).on('change','.selectUnit',function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
            var previousUnit = sItems[item_id].selected_unit_id;
            var previousFactor = sItems[item_id].unit_factor;
            var selectedPrice = parseFloat($(this).find(':selected').data('price')) || sItems[item_id].price_withoute_tax;
            var factor = parseFloat($(this).find(':selected').data('factor')) || 1;

            var item_tax = 0;
            var tax_rate = sItems[item_id].tax_rate;
            var tax_excise = sItems[item_id].tax_excise;
            var priceWithTax = selectedPrice;
            priceWithTax = selectedPrice + (selectedPrice * (tax_rate/100));
            item_tax = (selectedPrice * (tax_rate/100)) + (selectedPrice * (tax_excise/100));
            sItems[item_id].price_withoute_tax= selectedPrice;
            sItems[item_id].price_with_tax= priceWithTax;
            sItems[item_id].item_tax= item_tax;
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
        });

        $("#sTable").on('click', '.plus', function() {   
            rowindex = $(this).closest('tr').index(); 
            var qty = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val();
            
            if(!qty){
                qty = 1;
            }else{
                qty = parseFloat(qty) + 1;
            }
            
            var row = $(this).closest('tr');
            item_id = row.attr('data-item-id');  
           
            if(!isQuantityValid(sItems[item_id], qty)){
                alert(formatInsufficientMessage(sItems[item_id]));
                return;
            }

            sItems[item_id].qnt = qty;  
            loadItems();
            var audio = $("#mysoundclip1")[0];
            audio.play();
        });
            
        $("#sTable").on('click', '.minus', function() {
            rowindex = $(this).closest('tr').index();
            var qty = parseFloat($('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val()) - 1;
            if (qty > 0) {
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(qty);
            } else {
                qty = 1;
            } 

            var row = $(this).closest('tr');
            item_id = row.attr('data-item-id');  
           
            if(!isQuantityValid(sItems[item_id], qty)){
                alert(formatInsufficientMessage(sItems[item_id]));
                $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .qty').val(sItems[item_id].qnt);
                return;
            }

            sItems[item_id].qnt = qty;  
            loadItems();

            var audio = $("#mysoundclip1")[0];
            audio.play();
        });

    function is_numeric(mixed_var) {
        var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        return (
            (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -1)) &&
            mixed_var !== '' &&
            !isNaN(mixed_var)
        );
    }

    function loadItems(){

        var items = 0 ;
        var qnts = 0 ;
        var total = 0 ;
        var net = 0 ;

        $('#sTable tbody').empty();
        $.each(sItems,function (i,item) {

            items += 1 ;
            qnts += item.qnt ;
            var lineTotal = (item.price_withoute_tax*item.qnt);
            var lineTax = (item.item_tax*item.qnt);
            var lineDiscount = (item.promo_discount_unit ?? 0) * item.qnt;
            total += lineTotal ;
            net += (lineTotal + lineTax - lineDiscount);

            var newTr = $('<tr data-item-id="'+i+'">');
            var tr_html ='<td><input type="hidden" name="product_id[]" value="'+(item.product_id ?? item.id)+'"> <span><strong>'+item.name + '</strong><br>' + (item.code)+'</span> </td>';
                tr_html +='<td><span class="badge badge-light">'+Number(item.available_qty ?? 0)+'</span></td>';
                tr_html +='<td><input type="text" readonly class="form-control" value="'+Number(item.cost ?? 0).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly class="form-control" value="'+Number(item.last_sale_price ?? 0).toFixed(2)+'"></td>';
                var unitSelect = '<select class="form-control selectUnit" name="unit_id[]">';
                if(item.units_options && item.units_options.length){
                    item.units_options.forEach(function(u){
                        var selected = u.unit_id == item.selected_unit_id ? 'selected' : '';
                        unitSelect += '<option value="'+u.unit_id+'" data-price="'+u.price+'" data-factor="'+(u.conversion_factor ?? 1)+'" '+selected+'">'+u.unit_name+'</option>';
                    });
                }
                unitSelect += '</select><input type="hidden" name="unit_factor[]" class="unitFactor" value="'+(item.unit_factor ?? 1)+'">';
                tr_html +='<td>'+unitSelect+'</td>';
                tr_html +='<td hidden><input type="text" class="form-control iPrice" name="price_unit[]" value="'+item.price_withoute_tax.toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" class="form-control text-center iPriceWTax" name="price_with_tax[]" value="'+(item.price_withoute_tax + item.item_tax).toFixed(2)+'"></td>';
                //tr_html +='<td><input type="text" class="form-control iQuantity" name="qnt[]" value="'+item.qnt.toFixed(2)+'"></td>';
                tr_html +=`<td><div class="input-group">
	                            <span class="input-group-btn">
	                        	    <button type="button" class="btn btn-default minus">
	                        		    <span class="fa fa-minus"></span>
	                        		</button>
	                        	</span>
	                        	<input type="text" name="qnt[]" class="form-control qty numkey input-number text-center" step="any" value="`+item.qnt+`" required="">
	                        	<span class="input-group-btn">
	                        	    <button type="button" class="btn btn-default plus">
	                        		    <span class="fa fa-plus"></span>
	                        		</button>
	                        	</span>
	                        </div>
                        </td>`; 
                tr_html +='<td hidden><input type="text" readonly="readonly" class="form-control" name="total[]" value="'+(item.price_withoute_tax*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="text" readonly="readonly" class="form-control" name="tax[]" value="'+(item.item_tax*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExcise" name="tax_excise[]" value="'+(((item.tax_excise/100) * item.price_withoute_tax)*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control" name="discount_unit[]" value="'+(item.promo_discount_unit ?? 0)+'"></td>';
                tr_html +='<td><input type="text" readonly="readonly" class="form-control text-center" name="net[]" value="'+((item.price_withoute_tax + item.item_tax - (item.promo_discount_unit ?? 0))*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxRate" name="tax_rate[]" value="'+item.tax_rate+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExciseRate" name="tax_excise_rate[]" value="'+item.tax_excise+'"></td>';
                tr_html +='<td hidden><input type="hidden" name="variant_id[]" value="'+(item.variant_id ?? '')+'"></td>';
                tr_html +='<td hidden><input type="hidden" name="variant_color[]" value="'+(item.variant_color ?? '')+'"></td>';
                tr_html +='<td hidden><input type="hidden" name="variant_size[]" value="'+(item.variant_size ?? '')+'"></td>';
                tr_html +='<td hidden><input type="hidden" name="variant_barcode[]" value="'+(item.variant_barcode ?? '')+'"></td>';
                tr_html +=`<td class="text-center"> 
                                <button type="button" class="btn btn-danger deleteBtn btn-sm" value=" '+i+' ">
                                    <i class="fa fa-close"></i>
                                </button>
                            </td>`; 
                newTr.html(tr_html);
                newTr.appendTo('#sTable');
        });

        //document.getElementById('items').innerHTML = items ; 
        //document.getElementById('total').innerHTML = total ;
        document.getElementById('items_count').innerHTML = qnts ; 
        document.getElementById('total_with_tax').innerHTML = net.toFixed(2) ;
        document.getElementById('totalBig').innerHTML = net.toFixed(2) ;
        document.getElementById('net_sales').value = net.toFixed(2);
        updatePosDiscountSummary();

    }

    function updatePosDiscountSummary(){
        var net = parseFloat($('#net_sales').val()) || 0;
        var discountType = $('#discount_type').val();
        var discountValue = parseFloat($('#discount_input').val()) || 0;
        var discountAmount = discountType == '2' ? net * (discountValue / 100) : discountValue;
        discountAmount = Math.min(discountAmount, net);
        $('#discount_amount').val(discountAmount.toFixed(2));
        $('#net_after_discount').val((net - discountAmount).toFixed(2));
    }

    function buildCustomerLookupIndex(){
        posCustomerLookupIndex = [];
        $('#customer_id option').each(function(){
            const option = $(this);
            const id = option.val();
            if(!id){
                return;
            }
            const entry = {
                id: id,
                name: (option.data('name') || option.text() || '').trim(),
                phone: option.data('phone') || '',
                address: option.data('address') || '',
                tax: option.data('tax') || ''
            };
            entry.label = [entry.name, entry.phone, entry.address].filter(Boolean).join(' - ');
            entry.searchText = (entry.name + ' ' + entry.phone + ' ' + entry.address + ' ' + entry.tax).toLowerCase();
            posCustomerLookupIndex.push(entry);
        });
    }

    function renderCustomerLookupResults(query){
        const resultsContainer = $('#customer_lookup_results');
        if(!resultsContainer.length){
            return;
        }
        const normalized = (query || '').toString().trim().toLowerCase();
        if(!normalized){
            resultsContainer.addClass('d-none').empty();
            return;
        }
        const matches = posCustomerLookupIndex.filter(function(entry){
            return entry.searchText.indexOf(normalized) !== -1;
        }).slice(0,5);
        if(!matches.length){
            resultsContainer.removeClass('d-none').html('<div class="list-group-item disabled small">'+posCustomerNoResultsText+'</div>');
            return;
        }
        var html = '';
        matches.forEach(function(entry){
            html += '<button type="button" class="list-group-item list-group-item-action customer-lookup-item" data-customer-id="'+entry.id+'" data-label="'+entry.label+'">'+entry.label+'</button>';
        });
        resultsContainer.removeClass('d-none').html(html);
    }

    function getActiveCategoryProducts(){
        if(posActiveCategoryKey === 'all'){
            return posProductsCatalog;
        }
        return posProductsCatalog.filter(function(item){
            return item && item.pos_category_key === posActiveCategoryKey;
        });
    }

    function renderPosCategoryButtons(){
        const container = $('#posCategories');
        if(!container.length){
            return;
        }
        if(!posProductsCatalog.length){
            container.addClass('d-none').empty();
            return;
        }
        const categoriesMap = {};
        posProductsCatalog.forEach(function(item){
            if(!item){
                return;
            }
            categoriesMap[item.pos_category_key] = item.category_label || posCategoryLabels.uncategorized || '';
        });
        const sortedKeys = Object.keys(categoriesMap).sort(function(a,b){
            return categoriesMap[a].localeCompare(categoriesMap[b]);
        });
        var html = '';
        html += '<button type="button" class="pos-cat-btn'+(posActiveCategoryKey === 'all' ? ' active' : '')+'" data-category-key="all">'+(posCategoryLabels.all || 'All')+'</button>';
        sortedKeys.forEach(function(key){
            html += '<button type="button" class="pos-cat-btn'+(posActiveCategoryKey === key ? ' active' : '')+'" data-category-key="'+key+'">'+categoriesMap[key]+'</button>';
        });
        container.removeClass('d-none').html(html);
    }

    function renderPosProductGrid(){
        const container = $(".table-container");
        if(!container.length){
            return;
        }
        const products = getActiveCategoryProducts();
        if(!products.length){
            container.html('<div class="text-center text-muted py-4">'+posNoProductsText+'</div>');
            return;
        }
        var tableData = '<table id="product-table" class="table no-shadow product-list"> <thead class="d-none"> <tr><th></th></tr></thead> <tbody>';
        products.forEach(function(item, index){
            if(index % 5 === 0){
                tableData += '<tr>';
            }
            const imageName = item.img ? item.img : 'zummXD2dvAtI.png';
            tableData += '<td class="product-img sound-btn" data-id="'+item.id+'"><img src="'+posProductImageBase+imageName+'" width="100%" /><p>'+item.name+'</p><span>'+item.code+'</span></td>';
            if(index % 5 === 4){
                tableData += '</tr>';
            }
        });
        const remainder = products.length % 5;
        if(remainder){
            const emptyCells = 5 - remainder;
            for(let i=0; i<emptyCells; i++){
                tableData += '<td style="border:none;"></td>';
            }
            tableData += '</tr>';
        }
        tableData += '</tbody></table>';
        container.html(tableData);
        $('#product-table').DataTable({
            "order": [],
            'pageLength': product_row_number,
             'language': {
                'paginate': {
                    'previous': '<i class="fa fa-angle-right"></i>',
                    'next': '<i class="fa fa-angle-left"></i>'
                }
            },
            dom: 'tp'
        });
    }

    function applyPosCategoryFilter(key){
        posActiveCategoryKey = key || 'all';
        renderPosProductGrid();
        $('#posCategories .pos-cat-btn').removeClass('active').filter(function(){
            return ($(this).data('category-key') || 'all') === posActiveCategoryKey;
        }).addClass('active');
    }

    function populateProduct(data) {
        let parsedItems = [];
        if(data){
            try{
                parsedItems = JSON.parse(data.replace(/&quot;/g,'"'));
            }catch(e){
                parsedItems = [];
            }
        }
        suggestionItems = {};
        posProductsCatalog = [];
        (parsedItems || []).forEach(function(item){
            if(!item){
                return;
            }
            item.pos_category_key = item.category_id ? ('cat_'+item.category_id) : 'uncategorized';
            item.category_label = item.category_name || posCategoryLabels.uncategorized || '';
            suggestionItems[item.id] = item;
            posProductsCatalog.push(item);
        });
        posActiveCategoryKey = 'all';
        renderPosCategoryButtons();
        renderPosProductGrid();
    }
 
</script>
<script src="{{ asset('js/offline-pos.js') }}"></script>
<script type="text/javascript">
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
</script>
@endsection 
