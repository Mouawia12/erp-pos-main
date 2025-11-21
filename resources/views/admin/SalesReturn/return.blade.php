@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('عرض مردود مبيعات')  
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            [ {{ __('main.add_return_sale') }} ]
                            </h4>
                        </div> 
                        <div class="clearfix"></div>
                    </div> 
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                       @can('اضافة مردود مبيعات')   
                        <a href="{{route('sales.return.create')}}" type="button" class="btn btn-labeled btn-info"> 
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div> 
                    <div class="card-body pt-0 pb-2">
                        <form   method="POST" action="{{ route('store_return',$id) }}"
                                enctype="multipart/form-data" >
                            @csrf

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_number') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="invoice_no" name="invoice_no"
                                               class="form-control" placeholder="bill_number" readonly
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                        <input type="datetime-local"  id="bill_date" name="bill_date"
                                               class="form-control"
                                              />
                                    </div>
                                </div>
                                <div class="col-md-4 " >
                                    <div class="form-group">
                                        <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100" readonly="readonly"
                                                name="warehouse_id" id="warehouse_id"> 
                                            @foreach ($warehouses as $warehouse)
                                                @if($warehouse->id == $sale->warehouse_id)
                                                <option value="{{$warehouse -> id}}">{{ $warehouse -> name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 " >
                                    <div class="form-group">
                                        <label>{{ __('main.clients') }} <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100"  readonly="readonly"
                                                name="customer_id" id="customer_id"> 
                                            @foreach ($customers as $customer)
                                                @if($customer->id == $sale->customer_id) 
                                                    <option value="{{$customer -> id}}"> {{ $customer -> name}}</option>
                                                @endif 
                                            @endforeach
                                        </select>
                                    </div>
                                </div> 
                            </div> 
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="control-group table-group"> 
                                        <div class="card-header pb-0">
                                            <h4  class="alert alert-info text-center">{{__('main.items')}} </h4>
                                        </div> 
                                        <div class="table-responsive hoverable-table">
                                            <table class="display w-100  text-nowrap table-bordered" id="sTable" 
                                                   style="text-align: center;">  
                                                <thead>
                                                    <tr>
                                                        <th class="col-md-3">{{__('main.item_name_code')}}</th>
                                                        <th class="col-md-1">{{__('main.price.unit')}}</th>
                                                        <th class="col-md-1">{{__('main.price_with_tax')}}</th>
                                                        <th class="col-md-1">{{__('main.quantity')}} </th>
                                                        <th class="col-md-1">{{__('main.returned_qnt')}} </th>
                                                        <th class="col-md-2">{{__('main.amount')}}</th>
                                                        <th class="col-md-2">{{__('main.tax')}}</th>
                                                        <th class="col-md-2">{{__('main.net')}}</th> 
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody"></tbody>  
                                                <tfoot></tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"><hr></div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ __('main.notes') }} <span class="text-danger">*</span> </label>
                                        <textarea name="notes" id="notes" rows="3" placeholder="{{ __('main.notes') }}" class="form-control" style="width: 100%"></textarea>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-12 text-center">
                                        <input type="submit" class="btn btn-primary" id="primary" tabindex="-1"
                                            value="{{__('main.save_btn')}}">
                                        </input> 
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
        var string = "{{$saleItems}}";

        var allsItems = JSON.parse(string.replace(/&quot;/g,'"'));
        $.each(allsItems,function (i,item) {
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
            console.log(row);
            var table = document.getElementById('tbody');
            table.deleteRow(row);
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        });

    });


  function getBillNo(){

      let invoice_no = document.getElementById('invoice_no');
      $.ajax({
            type:'get',
            url:'{{route('get.sale.return.no',$sale->branch_id)}}',
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
        $.ajax({
          type:'get',
          url:'getProduct' + '/' + code,
          dataType: 'json', 
          success:function(response){ 
              document.getElementById('products_suggestions').innerHTML = '';
              if(response){
                  if(response.length == 1){
                      //addItemToTable
                      addItemToTable(response[0]);
                  }else if(response.length > 1){
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
        }

        count++;
        loadItems(); 
        document.getElementById('add_item').value = '' ;
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


        sItems[item_id].rqnt= newQty;
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

                if (!item.rqnt) {
                    item.rqnt = 0;
                }
  
                console.log(item);
  
                var newTr = $('<tr data-item-id="' + item.product_id + '">');
                var tr_html = '<td><input type="hidden" name="product_id[]" value="' + item.product_id + '"> <span>' + item.product_name + '---' + (item.product_code) + '</span> </td>';
                tr_html += '<td><input type="text" class="form-control" readonly name="price_unit[]" value="' + parseFloat(item.price_unit).toFixed(2) + '"></td>';
                tr_html += '<td><input type="text" class="form-control" readonly name="price_with_tax[]" value="' + parseFloat(item.price_with_tax).toFixed(2) + '"></td>';
                tr_html += '<td><input type="text" readonly="readonly" class="form-control" name="all_qnt[]" value="' + parseFloat(item.quantity).toFixed(2) + '"></td>';
                tr_html += '<td><input type="text" class="form-control iQuantity" name="qnt[]" value="' + item.rqnt.toFixed(2) + '"></td>';
                tr_html += '<td><input type="text" readonly="readonly" class="form-control" name="total[]" value="' + (parseFloat(item.price_unit) * parseFloat(item.rqnt)).toFixed(2) + '"></td>';
                tr_html += '<td><input type="text" readonly="readonly" class="form-control" name="tax[]" value="' + (parseFloat(item.tax / item.quantity) * parseFloat(item.rqnt)).toFixed(2) + '"></td>';
                tr_html +='<td hidden><input type="hidden" class="form-control TaxExcise" name="tax_excise[]" value="'+((item.tax_excise / item.quantity) * parseFloat(item.rqnt)).toFixed(2)+'"></td>';
                tr_html += '<td><input type="text" readonly="readonly" class="form-control" name="net[]" value="' + (parseFloat(item.price_with_tax + (item.tax_excise / item.quantity)) * parseFloat(item.rqnt)).toFixed(2) + '"></td>';
  
                newTr.html(tr_html);
                newTr.appendTo('#sTable');
          }
      });

  }
</script>

@endsection 