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
    input.form-control.text-center.iNewQuantity {
        min-width: 100px;
    }

</style> 
@can('اضافة جرد')  
                    
    <form method="POST" action="#"
          enctype="multipart/form-data" id="pos_sales_form">
        @csrf
        @method('POST')
        <input type="hidden" name="user_id" value="{{Auth::user()->id}}"/>
        <input type="hidden" name="inventory_id" id="inventory_id" value="{{$inventorys->id}}"/>
        <div class="row">
            <div class="card shadow mb-4 col-12">
                <div class="card-header py-3">
                    <div class="row">
                       <div class="col-12"> 
                            <h4  class="alert alert-primary text-center">
                               محضر جرد جديد  &nbsp&nbsp&nbsp&nbsp
                               <a class="btn btn-primary" href="{{ route('inventory.report',$inventorys->id) }}" target="_blank" role="button"><i class="fa fa-print"></i></a>
                               <form method="POST" action="{{ route('admin.inventory.match') }}" class="d-inline" onsubmit="return confirm('{{ __('main.confirm_inventory_match') }}');">
                                   @csrf
                                   <input type="hidden" name="inventory_id" value="{{$inventorys->id}}">
                                   <button type="submit" class="btn btn-success" @if($inventorys->is_matched) disabled @endif>
                                       {{ __('main.match_inventory') }}
                                   </button>
                               </form>
                            </h4> 
                            
                        </div> 
                    </div> 
                </div>
                <div class="card-body">  
                    <div class="document_type1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('main.warehouse') }}<span class="text-danger">*</span></label>
                                    <select class="js-example-basic-single w-100"
                                        name="warehouse_id" id="warehouse_id" required>
                                        <option value="0" selected>حدد الاختيار</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse -> id}}">{{ $warehouse -> name}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="col-md-12" id="sticker"> 
                                <div class="form-group">
                                    <div class="input-group wide-tip">
                                        <div class="input-group-addon">
                                            <i class="fa fa-3x fa-barcode addIcon"></i>
                                        </div> 
                                        <input type="text" name="add_item" id="add_item" value="" class="form-control input-lg ui-autocomplete-input" placeholder="{{__('main.add_item_hint')}}" autocomplete="off">
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
                                                <tbody id="tbody"></tbody>
                                                <tfoot></tfoot>
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
<audio id="mysoundclip1" preload="auto">
    <source src="{{URL::asset('assets/sound/beep/beep-timber.mp3')}}"></source>
</audio>
<audio id="mysoundclip2" preload="auto">
    <source src="{{URL::asset('assets/sound/beep/beep-07.mp3')}}"></source>
</audio>
@endcan 
@endsection 
@section('js')

<script type="text/javascript">

    var suggestionItems = {};
    var sItems = {};
    var count = 1; 

    $(document).ready(function () { 

        document.title = "جرد المخزون";

        $('#add_item').on('input', function (e) {
            searchProduct($('#add_item').val());
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
        $('#products_suggestions').empty();
        suggestionItems = {};
        if (count == 1) {
            sItems = {};
        }

        if (sItems[item.id]) {
            alert('هذا الصنف موجود ضمن محضر الجرد');
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

            const inventory_id = $('#inventory_id').val(); 
            const warehouse_id = $('#warehouse_id').val();  
            const warehouse_name = $('#warehouse_id option:selected').text();

            $.post("{{route('admin.inventory.add')}}", {
                id: item.id, 
                unit: item.unit, 
                quantity: item.qty, 
                inventory_id: inventory_id,
                warehouse_id :warehouse_id,
                "_token": "{{ csrf_token() }}"
            });

            $('#warehouse_id').empty();
            $('#warehouse_id').append('<option value="'+warehouse_id+'">'+ warehouse_name + '</option>');

        }
        
            var newTr = $('<tr data-item-id="' + item.id + '">');
            tr_html ='<td>' + count + '</span> </td>'; 
            tr_html +='<td><input type="hidden" name="item_id[]" value="' + item.id + '"><span>' + item.name + ' [ ' + (item.code) +  ' ] ' +'</span> </td>';
            tr_html +='<td><input type="hidden" name="unit[]" value="' + item.unit + '"> <span>' + item.units.name + '</span> </td>';
            tr_html +='<td><input type="text" readonly="readonly" class="form-control text-center batch-input" name="batch_no[]" id="batch_no[' + item.id + ']" value="" ></td>';
            tr_html +='<td><input type="date" readonly="readonly" class="form-control text-center batch-input" name="production_date[]" id="production_date[' + item.id + ']" value="" ></td>';
            tr_html +='<td><input type="date" readonly="readonly" class="form-control text-center batch-input" name="expiry_date[]" id="expiry_date[' + item.id + ']" value="" ></td>';
            tr_html +='<td><input type="text" readonly="readonly" class="form-control text-center iNewQuantity" name="quantity[]" value="' + item.qty + '" ></td>';
            tr_html +='<th><input type="text" readonly="readonly"  class="form-control text-center iNewQuantity2" name="new_quantity[]" id="new_quantity[' + item.id + ']" value="" ></th>'; 
            tr_html +='<td><input type="checkbox" name="item[]" class="cb_items" value="' + item.id + '"/> تعديل</td>';
            tr_html +='<td class="text-center"><button type="button" class="btn btn-primary btn-update-inventory"><span name="msg[' + item.id + ']" id="msg[' + item.id + ']"></span>حفظ</button></td>';
 
            newTr.html(tr_html);
            newTr.appendTo('#Table');  
            count++;  
            document.getElementById('add_item').value = '';
            $('#add_item').focus();
    }
 
    $(document).on('click','.btn-update-inventory',function () {
        var row = $(this).closest('tr'); 
        const item_id = row[0].cells[1].firstChild.value;
        const unit = row[0].cells[2].firstChild.value; 
        const quantity =  row[0].cells[6].firstChild.value;
        const batch_no = row[0].cells[3].firstChild.value;
        const production_date = row[0].cells[4].firstChild.value;
        const expiry_date = row[0].cells[5].firstChild.value;
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

 
    function is_numeric(mixed_var) {
        var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
        return (
            (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -1)) &&
            mixed_var !== '' &&
            !isNaN(mixed_var)
        );
    }
 
</script> 
@endsection 
 

