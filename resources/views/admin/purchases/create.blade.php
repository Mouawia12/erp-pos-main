@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif 
    @can('اضافة مشتريات')   
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"  id="head-right"> 
                        <h4 class="alert alert-primary text-center">
                            [ {{ __('main.add_purchase') }} ]
                        </h4> 
                    </div> 
                    <div class="card-body"> 
                        <form id="formPurchase" method="POST" action="{{ route('store_purchase') }}"
                                enctype="multipart/form-data" >
                            @csrf 
                            @php
                                $invoiceTypeDefault = $defaultInvoiceType ?? optional($setting)->default_invoice_type ?? 'tax_invoice';
                            @endphp
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_number') }} <span  class="text-danger">*</span> </label>
                                        <input type="text"  id="invoice_no" name="invoice_no"
                                            class="form-control" placeholder="bill_number" readonly
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                        <input type="datetime-local" id="bill_date" name="bill_date"
                                               class="form-control" readonly/>   
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.invoice_type') }}</label>
                                        <select class="form-control" name="invoice_type" id="invoice_type">
                                            <option value="tax_invoice" @if($invoiceTypeDefault==='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                            <option value="simplified_tax_invoice" @if($invoiceTypeDefault==='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                            <option value="non_tax_invoice" @if($invoiceTypeDefault==='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100"
                                                name="warehouse_id" id="warehouse_id" required> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4" >
                                    <div class="form-group">
                                        <label>{{ __('main.supplier') }} <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100"
                                            name="customer_id" id="customer_id" required>
                                            <option  value="0" selected>{{ __('main.choose') }}</option>
                                            @foreach ($customers as $supplier)
                                                <option value="{{$supplier -> id}}"
                                                    data-representative="{{$supplier->representative_id_ ?? ''}}"
                                                    data-phone="{{$supplier->phone ?? ''}}"
                                                    data-address="{{$supplier->address ?? ''}}"
                                                    data-tax="{{$supplier->tax_number ?? ''}}"
                                                    data-name="{{$supplier->name}}">
                                                    {{ $supplier -> name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="mt-1 d-flex gap-2">
                                            <a href="{{ route('clients',4) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                {{ __('main.add_new') }} {{ __('main.supplier') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>  
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('main.supplier_name') }}</label>
                                        <input type="text" class="form-control" name="supplier_name" id="supplier_name" placeholder="{{ __('main.supplier_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.supplier_phone') }}</label>
                                        <input type="text" class="form-control" name="supplier_phone" id="supplier_phone" placeholder="{{ __('main.supplier_phone') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.supplier_tax_number') }}</label>
                                        <input type="text" class="form-control" id="supplier_tax_number" placeholder="{{ __('main.supplier_tax_number') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.representatives') }}</label>
                                        <select class="form-control" name="representative_id" id="representative_id">
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
                                            <option value="credit" selected>{{ __('main.credit') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.supplier_invoice_no') }}</label>
                                        <input type="text" id="supplier_invoice_no" name="supplier_invoice_no"
                                               class="form-control" placeholder="{{ __('main.supplier_invoice_no') }}"/>   
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.supplier_invoice_copy') }}</label>
                                        <input type="file" id="supplier_invoice_copy" name="supplier_invoice_copy"
                                               class="form-control" accept=".pdf,.jpg,.jpeg,.png"/>   
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.products') }}</label>
                                        <a href="{{ route('createProduct') }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                            {{ __('main.add_new') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                                        </div>
                                        <ul class="suggestions" id="products_suggestions" style="display: block">
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col-md-12"> 
                                    <div class="card mb-4">
                                        <div class="card-header pb-0">
                                            <h4 class="alert alert-info text-center">
                                                <i class="fa fa-cart-shopping"></i>
                                                {{__('main.items_invoice')}} 
                                            </h4>
                                        </div> 
                                        <div class="card-body px-0 pt-0 pb-2">
                                            <div class="table-responsive hoverable-table">
                                                <table class="display w-100 table-bordered" id="sTable" 
                                                       style="text-align: center;">  
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th class="col-md-3 text-center">{{__('main.item_name_code')}}</th>
                                                            <th class="text-center">{{__('main.unit')}}</th>
                                                            <th class="text-center">{{__('main.quantity')}}</th>
                                                            <th class="text-center">{{__('main.batch_no')}}</th>
                                                            <th class="text-center">{{__('main.production_date')}}</th>
                                                            <th class="text-center">{{__('main.expiry_date')}}</th>
                                                            <th class="text-center">{{__('main.price.unit')}}</th>
                                                            <th class="text-center">{{__('main.tax_rate')}}</th>
                                                            <th class="text-center">{{__('main.tax_total')}}</th>
                                                            <th class="text-center">{{__('main.total_without_tax')}}</th>
                                                            <th class="text-center">{{__('main.total_with_tax')}}</th>
                                                            <th class="text-center">{{__('main.item_note')}}</th>
                                                            <th  class="text-center"></th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody"></tbody>  
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="9">
                                                                {{__('main.sum')}}
                                                            </th>
                                                            <td class="text-center">
                                                                <strong id="tax-text">0</strong>   
                                                            </td> 
                                                            <td class="text-center">
                                                                <strong id="total-text">0</strong>   
                                                            </td> 
                                                            <td class="text-center">
                                                                <strong id="net-text">0</strong>   
                                                            </td>  
                                                            <td colspan="2"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table> 
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-primary btn-lg" id="primary" tabindex="-1"> 
                                       <i class="fa fa-save"></i> {{__('main.save_btn')}} 
                                    </button>  
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label>{{__('main.notes')}}</label>
                                        <textarea class="form-control" name="notes" rows="2" placeholder="{{__('main.notes')}}"></textarea>
                                    </div>
                                </div>
                            </div> 
                        </form> 
                    </div>
                </div>
            </div>
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
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close">
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

<div class="modal fade" id="batchModal" tabindex="-1" role="dialog" aria-labelledby="batchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="alertTitle mb-0">{{ __('main.batch_details') }}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2" id="batchItemName"></p>
                <div class="form-group">
                    <label>{{ __('main.batch_no') }}</label>
                    <input type="text" class="form-control" id="batch_no_input" placeholder="{{ __('main.batch_no') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('main.production_date') }}</label>
                    <input type="date" class="form-control" id="production_date_input">
                </div>
                <div class="form-group">
                    <label>{{ __('main.expiry_date') }}</label>
                    <input type="date" class="form-control" id="expiry_date_input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBatchModal">{{ __('main.cancel_btn') }}</button>
                <button type="button" class="btn btn-primary" id="saveBatchModal">{{ __('main.save_btn') }}</button>
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
                                <th>{{ __('main.price.unit') }}</th>
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
    var pendingDuplicateItem = null;
    var pendingVariantItem = null;
    var pendingVariantForceDuplicate = false;
    var pendingBatchItem = null;

    $(document).ready(function() { 
        
        document.title = "{{ __('main.add_purchase') }}";
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null); 
        document.getElementById('bill_date').value = now.toISOString().slice(0, -1);
        $('#representative_id').on('change', function(){
            const txt = $(this).find('option:selected').text().trim();
            if(txt && !$('#cost_center').val()){
                $('#cost_center').val(txt);
            }
        });
        $('#customer_id').on('change', function(){
            const selected = $(this).find(':selected');
            if(!selected.val() || selected.val() === '0'){
                $('#supplier_name').val('');
                $('#supplier_phone').val('');
                $('#supplier_tax_number').val('');
                return;
            }
            const repId = selected.data('representative') || '';
            if(repId){
                $('#representative_id').val(repId).trigger('change');
            }
            const name = selected.data('name') || selected.text().trim();
            if(name){
                $('#supplier_name').val(name);
            }
            const phone = selected.data('phone') || '';
            $('#supplier_phone').val(phone);
            const taxNumber = selected.data('tax') || '';
            $('#supplier_tax_number').val(taxNumber);
        });
        $('#customer_id').trigger('change');
         //document.getElementById('bill_date').valueAsDate = new Date();

        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val());
        });

        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val());
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

        $(document).on('input', '.itemNote', function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
            if(sItems[item_id]){
                sItems[item_id].note = $(this).val();
            }
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
            var audio = $("#mysoundclip1")[0];
            audio.play();
        });

        $(document).on('click', '#primary', function () {
            var rows = 0; 
            rows = ($('#sTable tbody tr').length);
            console.log(rows); 
            var net_after_discount = document.getElementById('total-text').value;
            var client = document.getElementById('customer_id').value;
            var warehouse_id = document.getElementById('warehouse_id').value;
            if(client > 0 && warehouse_id > 0){
                if(rows > 0){  
                    document.getElementById('formPurchase').submit(); 
                } else {
                    alert($('<div>{{__('main.invoice_details_required')}}</div>').text());
                }
            } else {
                alert($('<div>{{__('main.customer_warehouse_required')}}</div>').text());
            } 
        });

        getBillNo();
        getWarehouse();

        $('#branch_id').change(function (){
            getWarehouse();
        });

        $('#cancelDuplicateItem').on('click', function(){
            pendingDuplicateItem = null;
            $('#duplicateItemModal').modal('hide');
        });
        $('#confirmDuplicateItem').on('click', function(){
            if(pendingDuplicateItem){
                addItemToTable(pendingDuplicateItem, true);
                pendingDuplicateItem = null;
            }
            $('#duplicateItemModal').modal('hide');
        });

        $('#cancelVariantSelection').on('click', function(){
            pendingVariantItem = null;
            pendingVariantForceDuplicate = false;
            $('#variantModal').modal('hide');
        });
        $('#confirmVariantSelection').on('click', function(){
            if(!pendingVariantItem){
                return;
            }
            var chosen = $('input[name="variant_choice"]:checked').val();
            if(!chosen){
                alert('{{ __('main.choose_variant') ?? 'اختر المتغير' }}');
                return;
            }
            var variant = (pendingVariantItem.variants || []).find(function(v){
                return String(v.id) === String(chosen);
            });
            if(!variant){
                alert('{{ __('main.notfound') }}');
                return;
            }
            pendingVariantItem.selected_variant = variant;
            pendingVariantItem.variant_id = variant.id;
            pendingVariantItem.variant_color = variant.color;
            pendingVariantItem.variant_size = variant.size;
            pendingVariantItem.variant_barcode = variant.barcode;
            if(variant.price){
                pendingVariantItem.cost = variant.price;
            }
            $('#variantModal').modal('hide');
            addItemToTable(pendingVariantItem, pendingVariantForceDuplicate);
            pendingVariantItem = null;
            pendingVariantForceDuplicate = false;
        });

        $('#cancelBatchModal').on('click', function(){
            pendingBatchItem = null;
            $('#batchModal').modal('hide');
        });
        $('#saveBatchModal').on('click', function(){
            if(!pendingBatchItem){
                $('#batchModal').modal('hide');
                return;
            }
            var batchNo = $('#batch_no_input').val().trim();
            var productionDate = $('#production_date_input').val();
            var expiryDate = $('#expiry_date_input').val();
            if(!batchNo || !productionDate || !expiryDate){
                alert('{{ __('main.batch_required_message') ?? 'Please enter batch number, production and expiry dates.' }}');
                return;
            }
            pendingBatchItem.item.batch_no = batchNo;
            pendingBatchItem.item.production_date = productionDate;
            pendingBatchItem.item.expiry_date = expiryDate;
            $('#batchModal').modal('hide');
            addItemToTable(pendingBatchItem.item, pendingBatchItem.forceDuplicate, true);
            pendingBatchItem = null;
        });
       
        function getBillNo(){
            
            let id = document.getElementById('branch_id').value ;
            let invoice_no = document.getElementById('invoice_no');
            
            var url = '{{route('get.purchase.number',[2,":id"])}}';
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

        function getWarehouse(){

            var branch_id = $('#branch_id').val();
            var url = '{{route('get.warehouses.branches',":id")}}'; 
            url = url.replace(":id", branch_id);

            getBillNo();

            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('#warehouse_id').empty();
                        //$('#warehouse_id').append('<option value="0">{{ __('main.choose') }}</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                } 
            });
        }
         
    });


    function searchProduct(code){
        var url = '{{route('getProduct',":code")}}';
            url = url.replace(":code",code);

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
                }else{
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
        $data +='<li class="select_product" data-item-id="'+item.id+'">'+item.name+'</li>';
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

    function addItemToTable(item, forceDuplicate, skipBatchPrompt){
        forceDuplicate = forceDuplicate || false;
        skipBatchPrompt = skipBatchPrompt || false;
        if(count == 1){
            sItems = {};
        } 

        var isDuplicate = Object.values(sItems).some(function(existing){
            var existingVariantId = existing.variant_id ?? (existing.selected_variant ? existing.selected_variant.id : null);
            var targetVariantId = item.selected_variant ? item.selected_variant.id : (item.variant_id ?? null);
            return existing.product_id === item.id && String(existingVariantId ?? '') === String(targetVariantId ?? '');
        });
        if(item.track_batch){
            isDuplicate = false;
        }

        if(needsVariantSelection(item)){
            openVariantModal(item, forceDuplicate);
            return;
        }

        if(!skipBatchPrompt && item.track_batch){
            pendingBatchItem = {item: item, forceDuplicate: forceDuplicate};
            $('#batchItemName').text(item.name ? item.name : (item.code || ''));
            $('#batch_no_input').val(item.batch_no ?? '');
            $('#production_date_input').val(item.production_date ?? '');
            $('#expiry_date_input').val(item.expiry_date ?? '');
            $('#batchModal').modal({backdrop:'static', keyboard:false});
            return;
        }

    var price = item.cost;
    var taxType = item.tax_method;
    var taxRate = item.tax_rate == 1 ? 0 : 15;
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

    if(item.selected_variant && item.selected_variant.price){
        price = item.selected_variant.price;
        if(taxType == 1){
            priceWithTax = price;
            priceWithoutTax = (price / (1+(taxRate/100)));
            itemTax = priceWithTax - priceWithoutTax;
        }else{
            itemTax = price * (taxRate/100);
            priceWithoutTax = price;
            priceWithTax = price + itemTax;
        }
    }

    var key = (item.selected_variant ? 'v'+item.selected_variant.id : 'p'+item.id) + '_' + itemKey;
    itemKey++;

    sItems[key] = item;
    sItems[key].product_id = item.id;
    sItems[key].price_with_tax = priceWithTax;
    sItems[key].price_withoute_tax = priceWithoutTax;
    sItems[key].item_tax = itemTax;
    sItems[key].qnt = 1;
    sItems[key].available_qty = item.qty ? Number(item.qty) : 0;
    sItems[key].selected_unit_id = defaultUnit;
    sItems[key].unit_factor = defaultFactor;
    sItems[key].units_options = item.units_options ?? [];
    sItems[key].note = item.note ?? '';
    sItems[key].tax_rate_display = taxRate;
    sItems[key].variant_id = item.selected_variant ? item.selected_variant.id : (item.variant_id ?? null);
    sItems[key].variant_color = item.selected_variant ? item.selected_variant.color : (item.variant_color ?? null);
    sItems[key].variant_size = item.selected_variant ? item.selected_variant.size : (item.variant_size ?? null);
    sItems[key].variant_barcode = item.selected_variant ? item.selected_variant.barcode : (item.variant_barcode ?? null);
    sItems[key].track_batch = item.track_batch ? true : false;
    sItems[key].batch_no = item.batch_no ?? '';
    sItems[key].production_date = item.production_date ?? '';
    sItems[key].expiry_date = item.expiry_date ?? '';

    if(isDuplicate && !forceDuplicate){
        delete sItems[key];
        pendingDuplicateItem = item;
        $('#duplicateItemName').text(item.name ? item.name : (item.code || ''));
        $('#duplicateItemModal').modal({backdrop:'static', keyboard:false});
        try {
            var warnAudio = $("#mysoundclip2")[0];
            if(warnAudio){ warnAudio.play(); }
        } catch (e) {}
        return;
    }
    count++;
    loadItems();

    document.getElementById('add_item').value = '' ;
    $('#add_item').focus();
  }

    var old_row_qty=0;
    var old_row_price = 0;
    var old_row_w_price = 0;

    $(document)
    .on('focus','.iQuantity',function () {
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

        console.log(newQty);
        console.log(item_id);
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

            var newQty = parseFloat($(this).val()),
                item_id = row.attr('data-item-id'); 
            var item_tax =sItems[item_id].item_tax;
            var priceWithTax = newQty;
            
            if(item_tax > 0){
                priceWithTax = newQty * 1.15;
                item_tax = newQty * 0.15;
            }
            sItems[item_id].price_withoute_tax= newQty;
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

            var newQty = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');

            var item_tax =sItems[item_id].item_tax;
            var priceWithoutTax = newQty;
            if(item_tax > 0){
                priceWithoutTax = newQty / 1.15;
                item_tax = priceWithoutTax * 0.15;
            }
            sItems[item_id].price_withoute_tax= priceWithoutTax;
            sItems[item_id].price_with_tax= newQty;
            sItems[item_id].item_tax= item_tax;
            loadItems();
        });

    $(document).on('change','.selectUnit',function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        var selectedPrice = parseFloat($(this).find(':selected').data('price')) || sItems[item_id].price_withoute_tax;
        var factor = parseFloat($(this).find(':selected').data('factor')) || 1;

        var item_tax = 0;
        var priceWithTax = selectedPrice * 1.15;
        item_tax = selectedPrice * 0.15;
        sItems[item_id].price_withoute_tax= selectedPrice;
        sItems[item_id].price_with_tax= priceWithTax;
        sItems[item_id].item_tax= item_tax;
        sItems[item_id].selected_unit_id = $(this).val();
        sItems[item_id].unit_factor = factor;
          row.find('.unitFactor').val(factor);
        loadItems();
    });

    $(document).on('change', '.batchInput', function () {
        var item_id = $(this).closest('tr').attr('data-item-id');
        if(sItems[item_id]){
            sItems[item_id].batch_no = $(this).val();
        }
    });

    $(document).on('change', '.productionDateInput', function () {
        var item_id = $(this).closest('tr').attr('data-item-id');
        if(sItems[item_id]){
            sItems[item_id].production_date = $(this).val();
        }
    });

    $(document).on('change', '.expiryDateInput', function () {
        var item_id = $(this).closest('tr').attr('data-item-id');
        if(sItems[item_id]){
            sItems[item_id].expiry_date = $(this).val();
        }
    });

    function is_numeric(mixed_var) {
        var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        return (
            (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -1)) &&
            mixed_var !== '' &&
            !isNaN(mixed_var)
        );
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

        var items_count_val = 0 ; 
        var first_total_val = 0 ;  
        var tax_total_val =0 ;
        var net_val = 0 ; 

        $('#sTable tbody').empty();
        $.each(sItems,function (i,item) {
            var newTr = $('<tr data-item-id="'+i+'">');
            var nameCell = '<input type="hidden" name="product_id[]" value="'+(item.product_id ?? item.id)+'">';
            nameCell += '<input type="hidden" name="variant_id[]" value="'+escapeHtml(item.variant_id ?? '')+'">';
            nameCell += '<input type="hidden" name="variant_color[]" value="'+escapeHtml(item.variant_color ?? '')+'">';
            nameCell += '<input type="hidden" name="variant_size[]" value="'+escapeHtml(item.variant_size ?? '')+'">';
            nameCell += '<input type="hidden" name="variant_barcode[]" value="'+escapeHtml(item.variant_barcode ?? '')+'">';
            nameCell += '<div><strong>'+escapeHtml(item.name ?? '')+'</strong><br><small class="text-muted">'+escapeHtml(item.code ?? '')+'</small></div>';
            if(item.variant_color || item.variant_size){
                nameCell += '<div class="small text-info">'+escapeHtml(item.variant_color ?? '')+' '+escapeHtml(item.variant_size ?? '')+'</div>';
            }
            nameCell += '<div class="small text-muted">{{ __('main.available_qty') }}: '+Number(item.available_qty ?? 0)+'</div>';
            var tr_html ='<td>'+(items_count_val+1)+'</td>';
                tr_html +='<td>'+nameCell+'</td>';
                var unitSelect = '<select class="form-control selectUnit" name="unit_id[]">';
                if(item.units_options && item.units_options.length){
                    item.units_options.forEach(function(u){
                        var selected = u.unit_id == item.selected_unit_id ? 'selected' : '';
                        unitSelect += '<option value="'+u.unit_id+'" data-price="'+u.price+'" data-factor="'+(u.conversion_factor ?? 1)+'" '+selected+'">'+u.unit_name+'</option>';
                    });
                }
                unitSelect += '</select><input type="hidden" name="unit_factor[]" class="unitFactor" value="'+(item.unit_factor ?? 1)+'">';
                tr_html +='<td>'+unitSelect+'</td>';
                tr_html +='<td><input type="number" class="form-control iQuantity" name="qnt[]" value="'+item.qnt+'"></td>';
                tr_html +='<td><input type="text" class="form-control batchInput" name="batch_no[]" value="'+escapeHtml(item.batch_no ?? '')+'" placeholder="{{__('main.batch_no') ?? 'Batch'}}"></td>';
                tr_html +='<td><input type="date" class="form-control productionDateInput" name="production_date[]" value="'+(item.production_date ?? '')+'"></td>';
                tr_html +='<td><input type="date" class="form-control expiryDateInput" name="expiry_date[]" value="'+(item.expiry_date ?? '')+'"></td>';
                tr_html +='<td><input type="number" class="form-control iPrice" name="price_without_tax[]" value="'+Number(item.price_withoute_tax ?? 0).toFixed(2)+'"><input type="hidden" class="form-control iPriceWTax" name="price_with_tax[]" value="'+Number(item.price_with_tax ?? 0).toFixed(2)+'"></td>';
                var taxRateDisplay = Number(item.tax_rate_display ?? 0).toFixed(2) + '%';
                tr_html +='<td><input type="text" readonly class="form-control-plaintext text-center" value="'+taxRateDisplay+'"></td>';
                tr_html +='<td><input type="text" readonly class="form-control" name="tax[]" value="'+(Number(item.item_tax ?? 0)*Number(item.qnt ?? 0)).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly class="form-control" name="total[]" value="'+(Number(item.price_withoute_tax ?? 0)*Number(item.qnt ?? 0)).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly class="form-control" name="net[]" value="'+(Number(item.price_with_tax ?? 0)*Number(item.qnt ?? 0)).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" class="form-control itemNote" name="item_note[]" value="'+escapeHtml(item.note ?? '')+'" placeholder="'+escapeHtml(itemNotePlaceholder)+'"></td>';
                tr_html +=`<td><button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+i+' ">
                                    <i class="fa fa-close"></i>
                                </button>
                            </td>`;
            newTr.html(tr_html);
            newTr.appendTo('#sTable');

            items_count_val += 1 ;  
            first_total_val += (Number(item.price_withoute_tax ?? 0)) * Number(item.qnt ?? 0);
            tax_total_val +=  Number(item.item_tax ?? 0)  * Number(item.qnt ?? 0); 
			net_val += ((Number(item.price_withoute_tax ?? 0) + Number(item.item_tax ?? 0))*Number(item.qnt ?? 0));
        }); 

        document.getElementById('total-text').innerHTML = first_total_val.toFixed(2);
        document.getElementById('tax-text').innerHTML =  tax_total_val.toFixed(2);
        document.getElementById('net-text').innerHTML =  net_val.toFixed(2);
  }

    function needsVariantSelection(item){
        var variants = item.variants || [];
        if(!variants.length){
            return false;
        }
        var selectedId = item.selected_variant ? item.selected_variant.id : (item.variant_id ?? null);
        return !selectedId;
    }

    function openVariantModal(item, forceDuplicate){
        pendingVariantItem = item;
        pendingVariantForceDuplicate = !!forceDuplicate;
        var tbody = $('#variantOptionsBody');
        tbody.empty();
        (item.variants || []).forEach(function(variant){
            var row = `<tr>
                <td>${escapeHtml(variant.color ?? '')}</td>
                <td>${escapeHtml(variant.size ?? '')}</td>
                <td>${escapeHtml(variant.barcode ?? '')}</td>
                <td>${Number(variant.price ?? item.cost ?? 0).toFixed(2)}</td>
                <td>${Number(variant.quantity ?? 0)}</td>
                <td><input type="radio" name="variant_choice" value="${variant.id}"></td>
            </tr>`;
            tbody.append(row);
        });
        $('#variantModal').modal({backdrop:'static', keyboard:false});
    }
</script>
@endsection 
