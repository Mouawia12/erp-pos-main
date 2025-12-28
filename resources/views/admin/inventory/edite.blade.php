@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
<!-- row opened -->
<style>
 
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
  color: #ffffff;
  background-color: #E5B80B;
  border-color: #E5B80B;
}
 
select option {
        font-size: 15px !important;
    }
    .select2-container{
        width:100% !important;
    }
    span.select2-selection.select2-selection--single{
        padding:2px;
    }

</style> 
 
@can('تعديل جرد')                      
    <form method="POST" action="#"
          enctype="multipart/form-data" id="pos_sales_form">
        @csrf
        @method('POST')
        <input type="hidden" name="user_id" value="{{Auth::user()->id}}"/> 
        <input type="hidden" name="warehouse_id" id="warehouse_id" value="{{$inventory->warehouse_id}}"/>
        <div class="row">
            <div class="card shadow mb-4 col-12">
                <div class="card-header py-3">
                    <div class="row">
                       <div class="col-12"> 
                            <h4 class="alert alert-primary text-center">
                               استكمال محضر جرد   &nbsp&nbsp&nbsp&nbsp
                               <a class="btn btn-primary" href="{{ route('inventory.report',$inventory->id) }}" target="_blank" role="button"><i class="fa fa-print"></i></a>
                               <form method="POST" action="{{ route('admin.inventory.match') }}" class="d-inline" onsubmit="return confirm('{{ __('main.confirm_inventory_match') }}');">
                                   @csrf
                                   <input type="hidden" name="inventory_id" value="{{$inventory->id}}">
                                   <button type="submit" class="btn btn-success" @if($inventory->is_matched) disabled @endif>
                                       {{ __('main.match_inventory') }}
                                   </button>
                               </form>
                            </h4>  
                        </div> 
                    </div> 
                </div>
                <div class="card-body">  
                    <div class="row">  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('main.bill_number') }} <span class="text-danger">*</span> </label>
                                <input type="number" name="inventory_id" id="inventory_id"  class="form-control"  value="{{$inventory->id}}" readonly/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                <input type="date"  id="bill_date" name="bill_date"
                                       class="form-control"  value="{{$inventory->date}}"
                                       readonly/>
                            </div>
                        </div> 
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label>{{ __('main.branche') }} <span class="text-danger">*</span> </label>
                                <input type="text" name="branch" id="branch" value="{{$inventory->branch->branch_name}}"  class="form-control"  readonly/>
                            </div>
                        </div>
                        <div class="col-md-3" >
                            <div class="form-group">
                                <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                <input type="text" name="warehouse" id="warehouse" value="{{$inventory->warehouse->name}}"  class="form-control"  readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="document_type1">
                        <div class="row"> 
                            <div class="col-md-12" id="sticker"> 
                                <div class="form-group" style="border: 1px solid #eee;padding: 1%;border-radius: 10px; background: #fbfbfb;width: 100%;">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon"
                                             style="padding-left: 10px; padding-right: 10px;">
                                            <i class="fa fa-2x fa-barcode addIcon"></i>
                                        </div>
                                        <input
                                            style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                            type="text" name="add_item" value=""
                                            class="form-control input-lg ui-autocomplete-input"
                                            id="add_item"
                                            placeholder="{{__('main.add_item_hint')}}"
                                            autocomplete="off">

                                    </div> 
                                    <ul class="suggestions" id="products_suggestions"
                                        style="display: block">
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                   <div class="card-header pb-0">
                                        <h4   class="alert alert-info text-center">
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> 
                                            {{__('main.items')}} 
                                        </h4>
                                    </div>
                                    <div class="card-body px-0 pt-0 pb-2">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home"
                                                        type="button" role="tab" aria-controls="home"
                                                        aria-selected="true">المجرود
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                                                        role="tab" aria-controls="profile" aria-selected="false">الغير مجرود
                                                </button>
                                            </li>
                        
                                        </ul> 
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                                <div class="table-responsive p-0">
                                                    <table class="display w-100 table-bordered" id="Table" 
                                                       style="text-align: center;">  
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th class="text-center">{{__('main.item')}}</th>
                                                                <th class="text-center">{{__('main.unit')}}</th>
                                                                <th class="text-center">{{__('main.batch_no')}}</th>
                                                                <th class="text-center">{{__('main.production_date')}}</th>
                                                                <th class="text-center">{{__('main.expiry_date')}}</th>
                                                                <th class="text-center">{{__('main.balance_book')}}</th> 
                                                                <th class="text-center">{{__('main.balance_now')}}</th>
                                                                <th></th>
                                                                <th></th>  
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbody"> 
                                                            @foreach($inventory_details as $inventory_detaile)
                                                                <tr data-item-id="{{$inventory_detaile->item->id}}">
                                                                    <td class="text-center">{{$loop -> index + 1}}</td> 
                                                                    <td class="text-center"><input type="hidden" name="item_id[]" value="{{$inventory_detaile->item->id}}"><span>{{$inventory_detaile->item->name .' - '. $inventory_detaile->item->code}}</span> </td> 
                                                                    <td class="text-center"><input type="hidden" readonly="readonly" name="unit[]" value="{{$inventory_detaile->unit}}"> <span>{{$inventory_detaile->units->name}}</span> </td> 
                                                                    <td class="text-center"><input type="text" readonly="readonly" class="form-control text-center batch-input" name="batch_no[]" id="batch_no[{{$inventory_detaile->item->id}}]" value="{{$inventory_detaile->batch_no}}"></td>
                                                                    <td class="text-center"><input type="date" readonly="readonly" class="form-control text-center batch-input" name="production_date[]" id="production_date[{{$inventory_detaile->item->id}}]" value="{{$inventory_detaile->production_date}}"></td>
                                                                    <td class="text-center"><input type="date" readonly="readonly" class="form-control text-center batch-input" name="expiry_date[]" id="expiry_date[{{$inventory_detaile->item->id}}]" value="{{$inventory_detaile->expiry_date}}"></td>
                                                                    <td class="text-center"><input type="text" readonly="readonly" class="form-control text-center iNewQuantity" name="quantity[]" value="{{$inventory_detaile->quantity}}" ></td> 
                                                                    <td class="text-center"><input type="text" readonly="readonly"  class="form-control text-center iNewQuantity2" name="new_quantity[]" id="new_quantity[{{$inventory_detaile->item->id}}]" value="{{$inventory_detaile->new_quantity}}"></td> 
                                                                    <td class="text-center"><input type="checkbox" name="item[]" class="cb_items" value="{{$inventory_detaile->item->id}}"/> تعديل</td> 
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-primary btn-update-inventory">
                                                                            <span name="msg[{{$inventory_detaile->item->id}}]" id="msg[{{$inventory_detaile->item->id}}]"></span>حفظ
                                                                        </button>
                                                                        <span class="ss" id="ss"></span>
                                                                    </td> 
                                                                </tr> 
                                                            @endforeach
                                                        </tbody> 
                                                    </table> 
                                                </div>

                                            </div>
                                            <div class="tab-pane fade show " id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                                <div class="table-responsive p-0">
                                                    <table id="example1" 
                                                           class="display w-100  table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">#</th>
                                                                <th class="text-center">{{__('main.item')}}</th>
                                                                <th class="text-center">{{__('main.unit')}}</th>
                                                                <th class="text-center">{{__('main.quantity')}}</th>  
                                                                <th>غير مجرود
                                                                    <button type="button" class="btn btn-primary btn-show-inventory-none">
                                                                        <i class="fa fa-refresh"></i>
                                                                    </button>
                                                                </th>  
                                                            </tr>
                                                        </thead>
                                                        <tbody id="Ktbody"></tbody> 
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
        </div> 
    </form>  
@endcan 
@endsection 
@section('js') 
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script type="text/javascript">
    var suggestionItems = {};
    var sItems = {}; 
    var count = 1;

    $(document).ready(function () { 

        document.title = "جرد المخزون";

        $('#add_item').on('input', function (e) {
            searchProduct($('#add_item').val());
        });

        $(document).on('click', '.cancel-modal', function (event) {
            $('#deleteModal').modal("hide");
            $('#ItemMaterialModalDialog').modal("hide");
            id = 0;
        });

        $(document).on('click', '.deleteBtn', function (event) {
            var row = $(this).parent().parent().index();
            var row1 = $(this).closest('tr');
            var item_id = row1.attr('data-item-id');
            delete sItems[item_id];
            loadItems();
        });

        $(document).on('click', '.btn-show-inventory-none', function (event){
            $('#Ktbody').empty();
            StateItemToTable(); 
        });
        
        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id'); 
            if(suggestionItems[item_id].id > 0){
                addItemToTable(suggestionItems[item_id]);  
            }
        });
 
        $(document).on('click', ".cb_items", function () {
            const item_id = $(this).val();
            document.getElementById('new_quantity['+ item_id +']').readOnly = true; 
            document.getElementById('batch_no['+ item_id +']').readOnly = true;
            document.getElementById('production_date['+ item_id +']').readOnly = true;
            document.getElementById('expiry_date['+ item_id +']').readOnly = true;
            if ($(this).is(':checked')) {  
                document.getElementById('new_quantity['+ item_id +']').readOnly = false; 
                document.getElementById('batch_no['+ item_id +']').readOnly = false;
                document.getElementById('production_date['+ item_id +']').readOnly = false;
                document.getElementById('expiry_date['+ item_id +']').readOnly = false;
            } 
        });
 
    });
 
    function is_numeric(mixed_var) {
        var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        return (
            (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -1)) &&
            mixed_var !== '' &&
            !isNaN(mixed_var)
        );
    }

    function searchProduct(code){ 
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
        $.each(response, function (i, item) {
            if (item.type == 1) {
                if (item.status == 1) {
                    suggestionItems[item.id] = item; 
                    $data += '<li class="select_product" data-item-id="' + item.id + '">' + item.name + '--' + item.code + '</li>';
                } 
            }
        });
        document.getElementById('products_suggestions').innerHTML = $data;
    }

 
    function openDialog() {
        let href = $(this).attr('data-attr');
        $.ajax({
            url: href,
            beforeSend: function () {
                $('#loader').show();
            },
            // return the result
            success: function (result) {
                $('#deleteModal').modal("show");
            },
            complete: function () {
                $('#loader').hide();
            },
            error: function (jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + href + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        });
    }

    function addItemToTable(item) {
        suggestionItems = {};
        if(count == 1) {
            $( "#Table tbody tr ").each( function( index ) {
              var row = $(this).closest('tr'); 
              count +=1;
            });
        } 

        $('#products_suggestions').empty();
        suggestionItems = {};
        if (count == 1) {
            sItems = {};
        }

        if (sItems[item.id]) {
            alert('This Item Entry has Already been made');
            return;
        } else {
            var price = item.price;
            var taxType = item.tax_method;
            var taxRate = item.tax_rate == 1 ? 0 : 15;
            var itemTax = 0;
            var priceWithoutTax = 0;
            var priceWithTax = 0;
            var itemQnt = item.qty;

            if (taxType == 1) {
                //included
                priceWithTax = price;
                priceWithoutTax = (price / (1 + (taxRate / 100)));
                itemTax = priceWithTax - priceWithoutTax;
            } else {
                //excluded
                itemTax = price * (taxRate / 100);
                priceWithoutTax = price;
                priceWithTax = price + itemTax;
            }

            sItems[item.id] = item;
            console.log(sItems);

            const inventory_id = document.getElementById('inventory_id').value; 
            const warehouse_id = document.getElementById('warehouse_id').value; 
           
            $.post("{{route('admin.inventory.add')}}", {
                    id: item.id, 
                    unit: item.unit, 
                    quantity: item.qty, 
                    inventory_id: inventory_id,
                    warehouse_id :warehouse_id,
                    "_token": "{{ csrf_token() }}"
                });

        }
       
        var newTr = $('<tr data-item-id="' + item.id + '">');
        var td_html ='<td>' + count + '</span> </td>'; 
        td_html +='<td><input type="hidden" name="item_id[]" value="' + item.id + '"><span>' + item.name + ' [ ' + (item.code) +  ' ] ' +'</span> </td>';
        td_html +='<td><input type="hidden" name="unit[]" value="' + item.unit + '"> <span>' + item.units.name + '</span> </td>';
        td_html +='<td><input type="text" readonly="readonly" class="form-control text-center batch-input" name="batch_no[]" id="batch_no[' + item.id + ']" value="" ></td>';
        td_html +='<td><input type="date" readonly="readonly" class="form-control text-center batch-input" name="production_date[]" id="production_date[' + item.id + ']" value="" ></td>';
        td_html +='<td><input type="date" readonly="readonly" class="form-control text-center batch-input" name="expiry_date[]" id="expiry_date[' + item.id + ']" value="" ></td>';
        td_html +='<td><input type="text" readonly="readonly" class="form-control text-center iNewQuantity" name="quantity[]" value="' + item.qty + '" ></td>'; 
        td_html +='<th><input type="text" readonly="readonly"  class="form-control text-center iNewQuantity2" name="new_quantity[]" id="new_quantity[' + item.id + ']" value="" ></th>'; 
        td_html +='<td><input type="checkbox" name="item[]" class="cb_items" value="' + item.id + '"/> تعديل</td>';
        td_html +='<td class="text-center"><button type="button" class="btn btn-primary btn-update-inventory"><span name="msg[' + item.id + ']" id="msg[' + item.id + ']"></span>حفظ</button></td>';
 
        newTr.html(td_html);
        newTr.appendTo('#Table');  
        count++;  
        document.getElementById('add_item').value = '';
        $('#add_item').focus();
    }


    //start
    function StateItemToTable() { 
        const inventory_id = document.getElementById('inventory_id').value; 
        var url = '{{route('inventory.state',":id")}}'; 
            url = url.replace(":id",inventory_id); 
       
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) {
                        var count =1;
                        for (let i = 0; i < response.length; i++){
                            var newTr = $('<tr>');
                            var td_html = '<td class="text-center">' + count + '</span> </td>';  
                            td_html += '<td class="text-center"><span>' + response[i].name + ' [ ' + (response[i].code) +  ' ] ' +'</span> </td>';
                            td_html += '<td class="text-center"><span>' + response[i].units.name + '</span> </td>';
                            td_html += '<td class="text-center">' + response[i].quantity + '</td>';
                            td_html += '<td class="text-center"></td></tr>';
                            newTr.html(td_html);
                            newTr.appendTo('#example1'); 
                
                            count++;  
                             
                        }
                    }else{
                        alert('no');
                    }
                }
            }); 
    } 
    //end 

    $(document).on('click','.btn-update-inventory',function () {
        var row = $(this).closest('tr'); 
        const item_id = row[0].cells[1].firstChild.value;
        const unit = row[0].cells[2].firstChild.value; 
        const batch_no = row[0].cells[3].firstChild.value;
        const production_date = row[0].cells[4].firstChild.value;
        const expiry_date = row[0].cells[5].firstChild.value;
        const quantity =  row[0].cells[6].firstChild.value;
        const new_quantity =  row[0].cells[7].firstChild.value;
        const inventory_id = document.getElementById('inventory_id').value;
        if( new_quantity !== '' ){
            $.post("{{route('admin.inventory.update')}}", {
                    id: item_id, 
                    unit: unit, 
                    quantity: quantity,
                    new_quantity: new_quantity,
                    batch_no: batch_no,
                    production_date: production_date,
                    expiry_date: expiry_date,
                    inventory_id: inventory_id,
                    "_token": "{{ csrf_token() }}"
                }, function (data) {
                    document.getElementById('msg['+ item_id +']').innerHTML = '<i class="fa fa-check"></i>';
            }); 
        }

    });

    $(document).on('click', '.deleteBtn2', function (event) {
        var row = $(this).parent().parent().index();
        console.log(row);
        var table = document.getElementById('tbody2');
        table.deleteRow(row); 
    });

    function calcTotals(){
        var count = 0 ; 
        $( "#Table tbody tr ").each( function( index ) {
          var row = $(this).closest('tr'); 
          count +=1;
        });
    }  
</script> 

@endsection 
 


