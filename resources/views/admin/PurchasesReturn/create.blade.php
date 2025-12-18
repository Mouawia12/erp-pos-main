@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('اضافة مردود مشتريات')   
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                [ {{ __('main.return_purchase') }} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div> 
                    <div class="card-body">
                        <form id="formPurchase"  method="POST" action="{{ route('return_purchase_store',$id) }}"
                                enctype="multipart/form-data" autocomplete="off"> 
                            @csrf
                            <input type="hidden" name="branch_id" id="branch_id" value="{{$purchase->branch_id}}">
                            <input type="hidden" name="qty" id="qty" value="0">
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_number') }} <span  class="text-danger">*</span> </label>
                                        <input type="text"  id="invoice_no" name="invoice_no"
                                               class="form-control" placeholder="bill_number" readonly/> 
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_date') }} <span  class="text-danger">*</span> </label>
                                        <input type="datetime-local"  id="bill_date" name="bill_date"
                                               class="form-control"/> 
                                    </div>
                                </div> 
                                <div class="col-2">
                                    <div class="form-group">
                                        <label>{{ __('فاتورة المشتريات') }}  <span  class="text-danger">*</span> </label>
                                        <input value="{{$purchase->invoice_no}}" id="invoice_purchase_no" name="invoice_purchase_no"
                                               type="text" class="form-control" placeholder="bill_number" readonly/> 
                                    </div>
                                </div>
                                <div class="col-3" >
                                    <div class="form-group">
                                        <label>{{ __('main.warehouse') }} <span  class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100" disabled
                                                name="warehouse_idd" id="warehouse_idd">
                                            <option  value="0" selected>Choose...</option>
                                            @foreach ($warehouses as $item)
                                                <option @if($item->id == $purchase->warehouse_id) selected @endif value="{{$item -> id}}"> {{ $item -> name}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" value="{{$purchase->warehouse_id}}"  name="warehouse_id" id="warehouse_id">
                                    </div>
                                </div> 
                                <div class="col-3" >
                                    <div class="form-group">
                                        <label>{{ __('main.supplier') }} <span  class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100"  disabled
                                                name="customer_idg" id="customer_idg">
                                            <option  value="0" selected>Choose...</option>
                                            @foreach ($customers as $item)
                                                <option @if($item->id == $purchase->customer_id) selected @endif value="{{$item -> id}}"> {{ $item -> name}}</option>

                                            @endforeach
                                        </select>
                                        <input type="hidden" value="{{$purchase->customer_id}}"  name="customer_id" id="customer_id">
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="control-group table-group"> 
                                        <div class="card-header pb-0">
                                            <h4 class="alert alert-info text-center">
                                                <i class="fa fa-cart-shopping"></i>
                                                {{__('main.items_invoice')}} 
                                            </h4>
                                        </div> 
                                        <div class="table-responsive hoverable-table">
                                            <table class="display w-100 text-nowrap table-bordered" id="sTable" 
                                                    style="text-align: center;">  
                                                <thead>
                                                    <tr>
                                                        <th class="col-md-3 text-center">{{__('main.item_name_code')}}</th>
                                                        <th class="col-md-1">{{__('main.unit')}}</th>
                                                        <th class="col-md-1">{{__('main.price_without_tax')}}</th>
                                                        <th class="col-md-1">{{__('main.price_with_tax')}}</th>
                                                        <th class="col-md-1">{{__('main.quantity')}}</th>
                                                        <th class="col-md-1">{{__('main.returned_qnt')}}</th>
                                                        <th class="col-md-1">{{__('main.total_without_tax')}}</th>
                                                        <th class="col-md-1">{{__('main.tax')}}</th>
                                                        <th class="col-md-1">{{__('main.total_with_tax')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody"></tbody> 
                                                <tfoot></tfoot>
                                            </table>
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
@endcan 
@endsection 
@section('js')
<script type="text/javascript">

    var suggestionItems = {};
    var sItems = {};
    var count = 1;

    $(document).ready(function() {

        document.title = "{{ __('main.return_purchase') }}";

        var allsItems = @json($purchaseItems);
        allsItems.forEach(function(item){
            sItems[item.product_id] = item;
        });

        loadItems(); 
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null); 
        document.getElementById('bill_date').value = now.toISOString().slice(0, -1);

        getBillNo();

        $(document).on('click', '#primary', function () {
            var rows = 0; 
            var qty = $('#qty').val();

            rows = ($('#sTable tbody tr').length);
            console.log(rows);    

            if(rows > 0 && qty>0){  
                document.getElementById('formPurchase').submit(); 
            } else {
                alert($('<div>{{trans('يجب تحديد كميات واصناف الفاتورة')}}</div>').text());
            } 
        });

    });

    function getBillNo(){ 
        let invoice_no = document.getElementById('invoice_no');
        $.ajax({
            type:'get',
            url:'{{route('get.return.purchase.number',[2,$purchase->branch_id])}}',
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

            if(newQty > sItems[item_id].quantity){
                $(this).val(old_row_qty);
                alert('wrong value');
                return;
            }

            sItems[item_id].returned_qnt = newQty;
            updateTotalReturned();
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
        $('#sTable tbody').empty();
        $.each(sItems,function (i,item) { 
            if(item.quantity > 0) { 
                if (!item.returned_qnt) {
                    item.returned_qnt = 0;
                } 

                if(!item.selected_unit_id){
                    item.selected_unit_id = item.unit_id;
                }
                if(!item.unit_factor){
                    item.unit_factor = item.unit_factor ? item.unit_factor : 1;
                }

                var unitSelect = '<select class="form-control selectUnit" name="unit_id[]">';
                if(item.units_options){
                    item.units_options.forEach(function(u){
                        var selected = u.unit_id == item.selected_unit_id ? 'selected' : '';
                        unitSelect += '<option value="'+u.unit_id+'" data-price="'+u.price+'" data-factor="'+(u.conversion_factor ?? 1)+'" '+selected+'">'+u.unit_name+'</option>';
                    });
                }else{
                    unitSelect += '<option value="'+item.unit_id+'">{{__("main.unit")}}</option>';
                }
                unitSelect += '</select><input type="hidden" name="unit_factor[]" class="unitFactor" value="'+(item.unit_factor ?? 1)+'">';

                var priceWithoutTax = parseFloat(item.price_withoute_tax ?? item.cost_without_tax ?? 0);
                var taxPerUnit = 0;
                if(item.tax && item.quantity){
                    taxPerUnit = parseFloat(item.tax) / parseFloat(item.quantity);
                }
                var priceWithTax = priceWithoutTax + taxPerUnit;

                var newTr = $('<tr data-item-id="' + item.product_id + '">');
                var tr_html = '<td><input type="hidden" name="product_id[]" value="' + item.product_id + '"> <span>' + item.product_name + '---' + (item.product_code) + '</span> </td>';
                    tr_html +='<td>'+unitSelect+'</td>';
                    tr_html +='<td><input type="text" class="form-control" readonly name="price_without_tax[]" value="' + priceWithoutTax.toFixed(2) + '"></td>';
                    tr_html +='<td><input type="text" class="form-control" readonly name="price_with_tax[]" value="' + priceWithTax.toFixed(2) + '"></td>';
                    tr_html +='<td><input readonly type="text" class="form-control" name="all_qnt[]" value="' + parseFloat(item.quantity) + '"></td>';
                    tr_html +='<td><input type="number" class="form-control iQuantity" min="0" max="' + parseFloat(item.quantity) + '" name="qnt[]" value="' + item.returned_qnt + '"></td>';
                    tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="total[]" value="' + (priceWithoutTax * parseFloat(item.returned_qnt)).toFixed(2) + '"></td>';
                    tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="tax[]" value="' + (taxPerUnit * parseFloat(item.returned_qnt)).toFixed(2) + '"></td>';
                    tr_html +='<td><input type="text" readonly="readonly" class="form-control" name="net[]" value="' + (priceWithTax * parseFloat(item.returned_qnt)).toFixed(2) + '"></td>';
                
                newTr.html(tr_html);
                newTr.appendTo('#sTable');
            }
        }); 
        $('#add_item').focus();
        updateTotalReturned();
    }

    function updateTotalReturned(){
        var totalReturned = 0;
        Object.values(sItems).forEach(function(item){
            totalReturned += parseFloat(item.returned_qnt || 0);
        });
        $('#qty').val(totalReturned);
    }

    $(document).on('change','.selectUnit',function () {
        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        var selectedPrice = parseFloat($(this).find(':selected').data('price')) || parseFloat(sItems[item_id].cost_without_tax) || 0;
        var factor = parseFloat($(this).find(':selected').data('factor')) || 1;

        sItems[item_id].price_withoute_tax = selectedPrice;
        sItems[item_id].price_with_tax = selectedPrice * 1.15;
        sItems[item_id].selected_unit_id = $(this).val();
        sItems[item_id].unit_factor = factor;
        row.find('.unitFactor').val(factor);
        loadItems();
    });
</script>
@endsection 
