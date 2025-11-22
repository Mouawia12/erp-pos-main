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
    position: absolute;
    top: 8%;
    right: 7%;
    -webkit-transition: width .3s linear;
    transition: width .3s linear;
    width: 93%;
    min-height: 100vh;
    padding-bottom: 50px
}

.page.active,.pos-page .page {
    margin-left: 0;
    width: calc(100%)
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
</style> 
<div class="pos-page"> 
    <div class="row row-lg">
        <section class="forms pos-section col-xl-12">
        
            <form id="form" method="POST" action="{{ route('store_sale') }}"
                enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" id="POS" name="POS" value="1"/> 
            <input type="hidden" id="discount" name="discount"  value="0">
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
                <div class="row"> 
                    <div class="card shadow mb-4 col-6">
                        <div class="card-body  px-0 pt-0 pb-2">
                            <div class="table-container"> 
                            </div> 
                        </div> 
                    </div>
                    <div class="card shadow mb-4 col-6">
                        <div class="card-body  px-0 pt-0 pb-2">
                            <br>
                            <div class="row">   
                                <div class="col-lg-4" >  
                                   <div class="form-group">  
                                        <select class="js-example-basic-single w-100"
                                                name="warehouse_id" id="warehouse_id" required> 
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{$warehouse -> id}}"  
                                                        @if($warehouse -> id == $settings -> branch_id) selected @endif>
                                                    {{ $warehouse -> name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </div> 
                                </div> 
                                <div class="col-lg-4" >  
                                    <div class="form-group">  
                                        <select id="customer_id" name="customer_id" class="js-example-basic-single w-100" required>
                                            @foreach($vendors as $vendor)
                                                <option value="{{$vendor -> id}}">{{$vendor -> name}}</option>
                                            @endforeach
                                        </select> 
                                    </div>
                                </div>
                                <div class="col-lg-4" > 
                                    <div class="form-group">   
                                        <input id="customer_name" name="customer_name" class="form-control" type="text" placeholder="{{__('main.customer_name')}}">  
                                    </div> 
                                </div>
                                <div class="col-lg-4">  
                                    <div class="form-group">   
                                        <input id="customer_phone" name="customer_phone" class="form-control" type="text" placeholder="{{__('main.customer_phone')}}">  
                                    </div>   
                                </div>
                                <div class="col-lg-8">    
                                    <label class="total">{{__('main.total.final')}} <span id="totalBig"><strong>0.00</strong></span>  </label>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        @php $defaultType = $defaultInvoiceType ?? ($settings->default_invoice_type ?? 'simplified_tax_invoice'); @endphp
                                        <select class="form-control" name="invoice_type" id="invoice_type">
                                            <option value="simplified_tax_invoice" @if($defaultType=='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                            <option value="tax_invoice" @if($defaultType=='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                            <option value="non_tax_invoice" @if($defaultType=='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-2">
                                    <input type="text" class="form-control" name="cost_center" id="cost_center" placeholder="{{__('main.cost_center')}}">
                                </div>
                                <div class="col-lg-12 mb-2">
                                    <select class="form-control" name="representative_id" id="representative_id">
                                        <option value="">{{ __('main.representatives') }}</option>
                                        @foreach($representatives as $rep)
                                            <option value="{{$rep->id}}">{{$rep->user_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12 mb-2">
                                    <textarea class="form-control" name="notes" rows="2" placeholder="{{__('main.notes')}}"></textarea>
                                </div>
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
    var counter = 5; 

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
        sItems = {};
        count = 1;
        Bill = null ;
        suggestionItems = {};
    }

    $(document).ready(function() { 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 

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
            suggestionItems = {};
            sItems = {};
            count = 1; 
            product_row_number = 3;
            counter = 5;
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

        $('#representative_id').on('change', function(){
            const selectedText = $(this).find('option:selected').text().trim();
            if(selectedText && !$('#cost_center').val()){
                $('#cost_center').val(selectedText);
            }
        });
       
        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val());
        });

        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val());
        });

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
            total += item.price_withoute_tax*item.qnt ;
            net += ((item.price_withoute_tax + item.item_tax)*item.qnt);

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
                tr_html +='<td><input type="text" readonly="readonly" class="form-control text-center" name="net[]" value="'+((item.price_withoute_tax + item.item_tax)*item.qnt).toFixed(2)+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxRate" name="tax_rate[]" value="'+item.tax_rate+'"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExciseRate" name="tax_excise_rate[]" value="'+item.tax_excise+'"></td>';
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

    }

    function populateProduct(data) {
        var allsItems = JSON.parse(data.replace(/&quot;/g,'"'));
        var tableData = '<table id="product-table" class="table no-shadow product-list"> <thead class="d-none"> <tr> <th></th> <th></th> <th></th> <th></th> <th></th> </tr></thead> <tbody><tr>';

        if (data) {   
            $.each(allsItems,function (i,item) {  
                var product_info = item.code;
                suggestionItems[item.id] = item;
                if(item.img){
                    image = item.img;
                }else{
                    image = 'zummXD2dvAtI.png';
                } 
                
                if(counter / 5 == 0) {
                    tableData += '</tr><tr><td class="product-img sound-btn" data-id="'+item.id+'"><img  src="{{env('APP_URL')}}/uploads/items/images/'+image+'" width="100%" /><p>'+item.name+'</p><span>'+item.code+'</span></td>';
                }else if(counter / 5 > 0){
                    tableData += '<td class="product-img sound-btn" data-id="'+item.id+'"><img  src="{{env('APP_URL')}}/uploads/items/images/'+image+'" width="100%" /><p>'+item.name+'</p><span>'+item.code+'</span></td>';
                } 

                if(counter>0){
                    counter--; 
                }else{
                    counter +=4;
                }
                 
            });
             
            if(allsItems.length % 5){
                var number = 5 - (allsItems.length % 5);
                while(number > 0)
                {
                    tableData += '<td style="border:none;"></td>';
                    number--;
                }
            }
    
            tableData += '</tr></tbody></table>';
            $(".table-container").html(tableData);
            $('#product-table').DataTable( {
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
            $('table.product-list').hide();
            $('table.product-list').show(500);
         
        }
        else{
            tableData += '<td class="text-center">No data avaialable</td></tr></tbody></table>'
            $(".table-container").html(tableData);
        }
    }
    $('#product-table').DataTable( {
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
 
 
</script> 
@endsection 
