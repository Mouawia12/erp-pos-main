@extends('admin.layouts.master')
@section('title') [ {{ __('main.sale')}} ]@endsection   
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
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
    @can('اضافة مبيعات')   
    <div class="row row-sm">
        <div class="col-xl-12">
            <form method="POST" action="{{ route('store_sale') }}" id="salesform"
                           enctype="multipart/form-data" autocomplete="off">
            @csrf 
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
                                    @php $defaultType = $settings->first()->default_invoice_type ?? 'tax_invoice'; @endphp
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
                                <select class="js-example-basic-single w-100"
                                    name="customer_id" id="customer_id" required> 
                                    @foreach ($customers as $customer)
                                        <option value="{{$customer -> id}}" data-default-discount="{{$customer->default_discount ?? 0}}">
                                            {{ $customer -> name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                                                            <th class="text-center">{{__('main.quantity')}}</th>
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
                                                        <th colspan="6">
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
                                        <label>{{ __('نوع الخصم') }} </label> 
                                        <select class="js-example-basic-single w-100"
                                            name="discount_type" id="discount_type">  
                                            <option value="1">قيمة</option> 
                                            <option value="2">نسبة مئوية</option> 
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-md-6" >
                                    <div class="form-group">
                                        <label> {{__('main.discount')}} </label>
                                        <input type="number" step="any" class="form-control" id="discount" name="discount" placeholder="0">
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
            <div class="modal-body" id="smallBody">
                <img src="../../assets/img/warning.png" class="alertImage">
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
@endcan 
@endsection 
@section('js')
<script type="text/javascript">

    var suggestionItems = {};
    var sItems = {};
    var count = 1;
    var itemKey = 1;

    $(document).ready(function() {  

        $(document).on('click', '#payment_btn', function (){  
            const money = $('#money').val();
            let cash = $('#cash').val();
            let visa = $('#visa').val();
            if(Number(money) == ( Number(cash) + Number(visa) ) ){
                document.getElementById('salesform').submit(); 
            } else {
                alert('لابد ان يكون مجموع المبلغين مساويا لاجمالى الفاتورة');
            } 
        });

        $(document).on('change', '#cash', function () { 
            const money = $('#money').val();
            var visa = (Number(money) - Number(this.value)).toFixed(2);
            if(visa > 0 ){ 
                $('#visa').val(visa);
            }else{
                $('#visa').val(0);
            } 
        });
        
        $(document).on('keyup', '#cash', function () {
            const money = $('#money').val();
            var visa = (Number(money) - Number(this.value)).toFixed(2);
            if(visa > 0 ){ 
                $('#visa').val(visa);
            }else{
                $('#visa').val(0);
            } 
        });        

        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null);

        document.getElementById('bill_date').value = now.toISOString().slice(0, -1);

        getBillNo();
        $('#warehouse_id').change(function (){
            getBillNo();
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
            if(defaultDiscount > 0){
                $('#discount').val(defaultDiscount);
                NetAfterDiscount();
            }
        });

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
            var audio = $("#mysoundclip2")[0];
            audio.play();
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
                   //document.getElementById('salesform').submit(); 
                } else {
                    alert($('<div>{{trans('يجب تحديد تفاصيل واصناف الفاتورة')}}</div>').text());
                }
            } else {
                alert($('<div>{{trans('يجب تحديد العميل والمستودع')}}</div>').text());
            } 
        });

        
        getBillNo();
        getWarehouse();

        $('#branch_id').change(function (){
            getWarehouse();
        });

        function getBillNo(){

            const id = document.getElementById('branch_id').value;
            let invoice_no = document.getElementById('invoice_no');
            var url = '{{route('get.sale.no',":id")}}';
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
            document.getElementById('discount').value = 0; 
            document.getElementById('net_after_discount').value = 0;   
            suggestionItems = {};
            sItems = {};
            count = 1; 
        });

        document.getElementById('items_count').value = 0  ; 
        document.getElementById('first_total').value = 0; 
        document.getElementById('tax_total').value = 0;
        document.getElementById('discount_total').value = 0; 
        document.getElementById('discount').value = 0; 
        document.getElementById('net_sales').value = 0; 
        document.getElementById('net_after_discount').value = 0;   

        $(document).on('change', '#discount_type', function () {
            NetAfterDiscount(); 
        });

        $(document).on('change', '#discount', function () {
            NetAfterDiscount(); 
        });

        $(document).on('keyup', '#discount', function () {
            NetAfterDiscount(); 
        });

    });

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

    function addItemToTable(item){

        if(count == 1){
            sItems = {};
        }

        var isDuplicate = Object.values(sItems).some(function(existing){
            return existing.product_id === item.id;
        });

        var price = item.price;
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
        
        var key = item.id + '_' + itemKey;
        itemKey++;

        sItems[key] = item;
        sItems[key].product_id = item.id;
        sItems[key].price_with_tax = priceWithTax;
        sItems[key].price_withoute_tax = priceWithoutTax;
        sItems[key].item_tax = itemTax;
        sItems[key].tax_rate = taxRate;
        sItems[key].tax_excise = Excise; 
        sItems[key].qnt = 1;
        sItems[key].discount = 0;
        sItems[key].available_qty = item.qty ? Number(item.qty) : 0;
        sItems[key].cost = item.cost ? Number(item.cost) : 0;
        sItems[key].last_sale_price = item.last_sale_price ? Number(item.last_sale_price) : 0;
        sItems[key].selected_unit_id = defaultUnit;
        sItems[key].unit_factor = defaultFactor;
        sItems[key].units_options = item.units_options ?? [];

        if(isDuplicate){
            alert('{{ __('main.duplicate_item_warning') }}');
        }

        count++;
        loadItems(); 
        document.getElementById('add_item').value = '' ;
        $('#add_item').focus();
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

        console.log(newQty);
        console.log(item_id);
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

            var newPrice = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');

            var item_tax = sItems[item_id].item_tax;
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

        $(document).on('change','.selectUnit',function () {
            var row = $(this).closest('tr');
            var item_id = row.attr('data-item-id');
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
            row.find('.unitFactor').val(factor);
            loadItems();
            NetAfterDiscount();
        }); 

        $(document).on('change','.iDiscount',function () {
            var row = $(this).closest('tr'); 
       
            var newDiscount = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');   
            
            var price_withoute_tax = sItems[item_id].price_withoute_tax; 
            var priceWithTax = sItems[item_id].priceWithTax; 
            var item_tax = sItems[item_id].item_tax;
            var tax_rate = sItems[item_id].tax_rate;
            var tax_excise = sItems[item_id].tax_excise;
 
            price_withoute_tax = price_withoute_tax - newDiscount;
            priceWithTax = price_withoute_tax + (price_withoute_tax * (tax_rate/100));
            item_tax = (price_withoute_tax * (tax_rate/100)) + (price_withoute_tax * (tax_excise/100));

            sItems[item_id].discount = newDiscount;
            //sItems[item_id].price_withoute_tax = price_withoute_tax;
            sItems[item_id].price_with_tax= priceWithTax;
            sItems[item_id].item_tax= item_tax;
            loadItems();
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
            var tr_html ='<td><input type="hidden" name="product_id[]" value="'+(item.product_id ?? item.id)+'"> <span><strong>'+item.name + '</strong><br>' + (item.code)+'</span> </td>';
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

                tr_html +='<td>'+unitSelect+'</td>';
                tr_html +='<td><input type="number" class="form-control iQuantity" name="qnt[]" value="'+item.qnt+'"></td>';
                tr_html +='<td><input type="number" readonly="readonly" class="form-control iPrice" name="price_unit[]" value="'+(item.price_withoute_tax - item.discount).toFixed(2)+'"></td>';
                tr_html +='<td><input type="number" class="form-control iDiscount" name="discount_unit[]" value="'+(item.discount).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control iPriceWTax" name="price_with_tax[]" value="'+item.price_with_tax.toFixed(2)+'"></td>'; 
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="total[]" value="'+((item.price_withoute_tax - item.discount)*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="tax[]" value="'+(item.item_tax*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExcise" name="tax_excise[]" value="'+(((item.tax_excise/100)*(item.price_withoute_tax - item.discount))*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="net[]" value="'+(((item.price_withoute_tax - item.discount) + item.item_tax)*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxRate" name="tax_rate[]" value="'+item.tax_rate+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExciseRate" name="tax_excise_rate[]" value="'+item.tax_excise+'"></td>';
                tr_html +=`<td> <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+i+' ">
                                    <i class="fa fa-close"></i>
                                </button>
                            </td>`;

           
            newTr.html(tr_html);
            newTr.appendTo('#sTable');

            items_count_val += 1 ;  
            first_total_val += (item.price_withoute_tax - item.discount) * item.qnt;
            tax_total_val +=  Number(item.item_tax)  * Number(item.qnt)  ;
            discount_total_val += item.discount;
			net_sales_val += (((item.price_withoute_tax - item.discount) + item.item_tax)*item.qnt);


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
            $('#discount').attr({readOnly:true});
            $('#discount').val(0)    
        }else{
            $('#discount').attr({readOnly:false});    

            var discount = $('#discount').val();
            if(discount>0){
                NetAfterDiscount();
            } 
        }
    }

    function addPayments(remain) {  
        var route = '{{route('show_sales_payments',":remain")}}';
            route = route.replace(":remain",remain); 
        
        $.get( route, function(data){
            $(".show_modal").html(data);
            $('#paymentsModal').modal({backdrop: 'static', keyboard: false} ,'show');
        });
    }

    function NetAfterDiscount(){

        var net = $('#net_sales').val(); 
        var discount_type = $('#discount_type').val();  
        var discount = $('#discount').val(); 

        if(discount_type == 1){
            var net_after_discount = Number(net) - Number(discount);
        } else {
            var net_after_discount = Number(net) - Number(net * (discount/100));
        } 
 
        $("#net_after_discount").val(net_after_discount.toFixed(2)); 

    } 
</script>

@endsection 
