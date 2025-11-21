@extends('admin.layouts.master')
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
                           enctype="multipart/form-data" >
            @csrf 
            <div class="card shadow mb-4 col-xl-12"> 
                <div class="card-header"  id="head-right" > 
                    <h4 class="alert alert-primary text-center">
                        [ {{ __('main.add_sale') }} ]
                    </h4> 
                </div>    
                <div class="row"> 
                    <div class="card shadow mb-4 col-9">
                        <div class="card-body">  
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('main.invoice_no') }} <span class="text-danger">*</span> </label>
                                            <input type="text"  id="invoice_no" name="invoice_no"
                                                   class="form-control" placeholder="invoice_no" readonly/> 
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                            <input type="datetime-local"  id="bill_date" name="bill_date"
                                                   class="form-control"/> 
                                        </div>
                                    </div> 
                                    <div class="col-md-3" >
                                        <div class="form-group">
                                            <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                            <select class="js-example-basic-single w-100"
                                                name="warehouse_id" id="warehouse_id" required> 
                                                @foreach ($warehouses as $item)
                                                    <option value="{{$item -> id}}" @if($item -> id == $settings -> branch_id) selected @endif> {{ $item -> name}}</option> 
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-md-3" >
                                        <div class="form-group">
                                            <label>{{ __('main.clients') }} <span class="text-danger">*</span> </label>
                                            <select class="js-example-basic-single w-100"
                                                name="customer_id" id="customer_id" required> 
                                                @foreach ($customers as $customer)
                                                    <option value="{{$customer -> id}}"> {{ $customer -> name}}</option>
                                                @endforeach
                                            </select>
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
                                                    {{__('main.items')}} 
                                                </h4>
                                            </div>  
                                            <div class="card-body px-0 pt-0 pb-2">
                                                <div class="table-responsive hoverable-table">
                                                    <table class="display w-100 table-bordered" id="sTable" 
                                                           style="text-align: center;">  
                                                        <thead>
                                                            <tr>
                                                                <th class="col-md-3 text-center">{{__('main.item_name_code')}}</th>
                                                                <th class="text-center">{{__('main.quantity')}}</th>
                                                                <th class="text-center">{{__('main.price.unit')}}</th>
                                                                <th class="text-center">{{__('main.discount')}}</th>
                                                                <th class="text-center" hidden>{{__('main.price_with_tax')}}</th> 
                                                                <th class="text-center">{{__('main.mount')}}</th>
                                                                <th class="text-center">{{__('main.tax')}}</th>
                                                                <th class="text-center" hidden>{{__('الضريبة الانتقائية')}}</th>
                                                                <th class="text-center">{{__('main.total')}}</th>
                                                                <th class="text-center" hidden>{{__('معدل الضريبة')}}</th>
                                                                <th class="text-center" hidden>{{__('معدل الضريبة الانتقائية')}}</th>
                                                                <th class="text-center">
                                                                    <i class="fa fa-trash-o"></i>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbody"></tbody>
                                                        <tfoot>
                                                            <th colspan="3">
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

                                                        </tfoot>
                                                    </table>
                                                </div>
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
                                    <input type="text" readonly class="form-control"  id="net_sales">
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

    $(document).ready(function() { 

        document.title = "{{ __('main.add_sale') }}";

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

        $(document).on('change', '#warehouse_id', function () {
            getBillNo();
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
            var net = document.getElementById('net_sales').value; 
            var discount = document.getElementById('discount').value;  
            if(this.value == 1){
                var net_after_discount = Number(net) - Number(discount);
            }else{
                var net_after_discount = Number(net) - Number(net * (discount/100));
            }
            document.getElementById('net_after_discount').value = net_after_discount.toFixed(2);
        });

        $(document).on('change', '#discount', function () {

            var net = document.getElementById('net_sales').value; 
            var discount_type = document.getElementById('discount_type').value;  

            if(discount_type == 1){
                var net_after_discount = Number(net) - Number(this.value);
            } else {
                var net_after_discount = Number(net) - Number(net * (this.value/100));
            } 

            document.getElementById('net_after_discount').value = net_after_discount.toFixed(2);

        });

        $(document).on('keyup', '#discount', function () {

            var net = document.getElementById('net_sales').value; 
            var discount_type = document.getElementById('discount_type').value;  

            if(discount_type == 1){
                var net_after_discount = Number(net) - Number(this.value);
            } else {
                var net_after_discount = Number(net) - Number(net * (this.value/100));
            } 

            document.getElementById('net_after_discount').value = net_after_discount.toFixed(2);

        });

    });


  function getBillNo(){
        const id = document.getElementById('warehouse_id').value ;
        let invoice_no = document.getElementById('invoice_no');
        var url = '{{route('get_sale_no',":id")}}';
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

        if(sItems[item.id]){
            sItems[item.id].qnt = sItems[item.id].qnt +1;
        }else{
            var price = item.price;
            var taxType = item.tax_method;
            var taxRate = item.tax;
            var itemTax = 0;
            var priceWithoutTax = 0;
            var priceWithTax = 0; 
            var itemQnt = 1;

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
            
            sItems[item.id] = item;
            sItems[item.id].price_with_tax = priceWithTax;
            sItems[item.id].price_withoute_tax = priceWithoutTax;
            sItems[item.id].item_tax = itemTax;
            sItems[item.id].tax_rate = taxRate;
            sItems[item.id].tax_excise = Excise; 
            sItems[item.id].qnt = 1;
            sItems[item.id].discount = 0;

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

            var newTr = $('<tr data-item-id="'+item.id+'">');
            var tr_html ='<td><input type="hidden" name="product_id[]" value="'+item.id+'"> <span><strong>'+item.name + '</strong><br>' + (item.code)+'</span> </td>';
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
                tr_html +=`<td> <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+item.id+' ">
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
        }else{
            $('#discount').attr({readOnly:false});    
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

</script>

@endsection 
