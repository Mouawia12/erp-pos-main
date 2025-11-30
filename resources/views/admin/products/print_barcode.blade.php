@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    <style>
        .btn-default {
            color: #fff;
            background-color: #00b9ff;
            border-color: #00b9ff;
            font-size:14px;
        }
        
        .btn-default:hover {
            color: #fff;
            background-color: #0162e8;
            border-color: #0162e8;
        } 
    </style>

    @can('عرض صنف') 
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                [ {{ __('main.products_list'). ' / '. __('main.print_barcode')}} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
 
                    <div class="modal-body" id="paymentBody"> 
                        <form   method="POST" action="#" class="no-print">
                            @csrf 
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-md-12" id="sticker">
                                        <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                            <div class="form-group" style="margin-bottom:0;">
                                                <div class="input-group wide-tip">
                                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                        <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                                    <input style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                                           type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input"
                                                            id="add_item" placeholder="{{__('main.add_item_hint')}}" autocomplete="off">
            
                                                </div> 
                                            </div>
                                            <ul class="suggestions" id="products_suggestions" style="display: block">
            
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div> 
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-12"> 
                                    <div class="card mb-4"> 
                                        <div class="card-body px-0 pt-0 pb-2">
                                            <div class="table-responsive hoverable-table">
                                                <table class="display w-100 table-bordered" id="myTable" 
                                                       style="text-align: center;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">{{__('main.item')}}</th>
                                                            <th class="text-center">{{__('main.code')}}</th>
                                                            <th class=" text-center col-md-1">{{__('main.quantity')}} </th>
                                                            <th class="text-center" style="max-width: 30px !important; text-align: center;">
                                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                                            </th>
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

                            <div class="card mb-4"> 
                                <div class="card-body px-0 pt-0 pb-2">
                                    <div class="row" style="display: flex; justify-content: center">
                                        <div class="col-2" >
                                            <div class="form-group checkRow">
                                                <label>{{ __('main.company_name') }}</label>
                                                <input type="checkbox" id="company_name" name="company_name" value="0" 
                                                       class="form-check"/>
                                            </div>
                                        </div>
                                        <div class="col-2" >
                                            <div class="form-group checkRow">
                                                <label>{{ __('main.product_name') }}</label>
                                                <input type="checkbox" id="product_name" name="product_name" checked value="1"
                                                       class="form-check"/>
                                            </div>
                                        </div>
                                        <div class="col-2" >
                                            <div class="form-group checkRow">
                                                <label>{{ __('main.Sale_Price') }}</label>
                                                <input type="checkbox" id="sale_Price" name="sale_Price" checked value="1"
                                                       class="form-check"/>
                                            </div>
                                        </div>
                                        <div class="col-2" >
                                            <div class="form-group checkRow">
                                                <label>{{ __('main.code') }}</label>
                                                <input type="checkbox" id="include_code" name="include_code"  value="0"
                                                       class="form-check"/>
                                            </div>
                                        </div>
                                        <div class="col-2" >
                                            <div class="form-group checkRow">
                                                <label>{{ __('main.currencies') }}</label>
                                                <input type="checkbox" id="currencies" name="currencies" checked value="1"
                                                       class="form-check"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-4">
                                    <label><strong>الحجم والمقاس (Paper Size) *</strong></label>
                                    <select class="form-control" name="paper_size" required id="paper-size">
                                        <option value="0">Select paper size...</option>
                                        <option value="36">36 mm (1.4 inch)</option>
                                        <option value="24">24 mm (0.94 inch)</option>
                                        <option value="18">18 mm (0.7 inch)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row"> 
                                <div class="col-12" style="display: block; margin: 20px auto; text-align: center;">
                                    <hr>
                                    <button type="button" class="btn btn-labeled btn-primary"  id="submit-button">
                                        {{__('main.print_barcode')}}
                                    </button>
                                </div>
                            </div>
                        </form>
            
                        <div id="barcode-con" style="text-align: center">
                            @if(!empty($data))
                                <button type="button" onclick="window.print();return false;" class="btn btn-primary btn-block tip no-print" title="طباعة"><i class="icon fa fa-print"></i> طباعة</button>
                                        @foreach ($data as $index=>$item)
            
                                            @for ($r = 1; $r <= $item['quantity']; $r++)
                                                <div class="item style50" style="width:2in;height: 1in;border:0;">
                                                    <div class="div50" style="width:2in;height:1in;border: 1px dotted #CCC;padding-top:0.025in;">
                                                            @if ($item['site'])
                                                                <span class="barcode_site" style="font-family: arial;font-size: 11px;font-weight:bold;color: black;">{{$item['site']}}</span>
                                                            @endif
            
                                                            @if($item['name'])
                                                                <span class="barcode_name" style="display: block; font-family: arial;font-size: 11px;font-weight:bold;color: black;">{{$item['name']}}</span>
                                                            @endif
            
                                                            @if ($item['price'])
                                                                <span class="barcode_price"  style="font-family: arial;font-size: 11px;font-weight:bold;color: black;">السعر :
                                                                {{$item['price']}}
                                                                    @if($item['currency'])
                                                                        {{$item['currency']}}
                                                                    @endif
                                                                    @if($item['include_tax'])
                                                                        - شامل الضريبة
                                                                    @endif
                                                                </span>
                                                            @endif
                                                                <p style="font-family: 'Libre Barcode 39 !important';font-size: 30px;color: black;padding: 0px;margin: 0px;line-height: 1.2;">{{$item['barcode']}}</p>
                                                    </div>
                                                </div>
                                            @endfor
                                        @endforeach
                                    <button type="button" onclick="window.print();return false;" class="btn btn-primary btn-block tip no-print" title="طباعة"><i class="icon fa fa-print"></i>طباعة</button>
                            @else
                                <h3>{{__('main.no_product_selected')}}</h3>
                            @endif
                        </div> 
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <div id="print-barcode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 id="modal_header" class="modal-title">Barcode</h5>&nbsp;&nbsp;
                  <button id="print-btn" type="button" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</button>
                  <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div id="label-content">
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
    var row_id = 0;

    $(document).ready(function() {

        $("#submit-button").on("click", function(event) {

            paper_size = ($("#paper-size").val());

            if(paper_size != "0") {
                
                var company_name = "{{ optional($company)->name_ar }}";
                var currency_label = "{{ optional($company)->currency_label ?? 'ر.س' }}";
                var items = Object.values(sItems);

                if(items.length === 0){
                    alert('{{ __("main.no_product_selected") }}');
                    return;
                }

                var htmltext = '<table class="barcodelist" style="width:378px;" cellpadding="5px" cellspacing="10px">';
                var cellCounter = 0;

                items.forEach(function(item){
                    var qty = item.qty ? parseFloat(item.qty) : 1;
                    qty = isNaN(qty) || qty < 1 ? 1 : qty;

                    for(var i = 0; i < qty; i++){
                        if(cellCounter % 2 === 0){
                            htmltext += '<tr>';
                        }

                        if(paper_size == 36)
                            htmltext +='<td style="width:164px;height:88%;padding-top:7px;vertical-align:middle;text-align:center">';
                        else if(paper_size == 24)
                            htmltext +='<td style="width:164px;height:100%;font-size:12px;text-align:center">';
                        else
                            htmltext +='<td style="width:164px;height:100%;font-size:10px;text-align:center">';

                        if($('input[name="company_name"]').is(":checked"))
                            htmltext += (company_name || '') + '<br>';

                        if($('input[name="product_name"]').is(":checked"))
                            htmltext += item.name + '<br>';

                        var barcodeSrc = 'https://barcode.tec-it.com/barcode.ashx?data='+item.code+'&code=Code128';
                        if(paper_size == 18)
                            htmltext += '<img style="width: 22mm; height: 10mm;" src="'+barcodeSrc+'" alt="barcode" /><br>';
                        else
                            htmltext += '<img style="width: 25mm; height: 12mm;" src="'+barcodeSrc+'" alt="barcode" /><br>';

                        if($('input[name="include_code"]').is(":checked"))
                            htmltext += '<strong>'+item.code+'</strong><br>';

                        if($('input[name="sale_Price"]').is(":checked")) {
                            if($('input[name="currencies"]').is(":checked")) 
                                htmltext += 'السعر: '+item.price+' '+currency_label;
                            else
                                htmltext += 'السعر: '+item.price;
                        }
                        htmltext +='</td>';

                        if(cellCounter % 2 === 1){
                            htmltext +='</tr>';
                        }
                        cellCounter++;
                    }
                });

                if(cellCounter % 2 === 1){
                    htmltext +='<td></td></tr>';
                }

                htmltext += '</table>';
                $('#label-content').html(htmltext);
                $('#print-barcode').modal('show');
            }
            else
                alert('Please select paper size');
        });

        $("#print-btn").on("click", function() {
            var divToPrint=document.getElementById('print-barcode');
            var newWin=window.open('','Print-Window');
            newWin.document.open();
            newWin.document.write('<style type="text/css">@media print { #modal_header { display: none } #print-btn { display: none } #close-btn { display: none } } table.barcodelist { page-break-inside:auto } table.barcodelist tr { page-break-inside:avoid; page-break-after:auto }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
            newWin.document.close();
            setTimeout(function(){newWin.close();},10);
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
            // var table = document.getElementById('tbody');
            // table.deleteRow(row);
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        });

        $('#myTable').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true 
     
        }); 

    });

    function searchProduct(code){
        var url = '{{route('getProduct',":id")}}';
        url = url.replace(":id",code);
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

        let key = item.id;
        if(item.selected_variant && item.selected_variant.id){
            key = 'v'+item.selected_variant.id;
        }

        if(sItems[key])
            sItems[key].qty = sItems[key].qty +1;
        else{
            item.qty = item.quantity > 0 ? item.quantity : 1;
            sItems[key] = item; 
            sItems[key].id = key;
            sItems[key].name = item.name; 
            sItems[key].code = item.selected_variant ? (item.selected_variant.barcode ?? item.code) : item.code;
            sItems[key].variant_color = item.selected_variant ? item.selected_variant.color : null;
            sItems[key].variant_size = item.selected_variant ? item.selected_variant.size : null;
            sItems[key].price = item.selected_variant && item.selected_variant.price ? item.selected_variant.price : item.price; 
            sItems[key].promo_price = item.cost; 
            row_id = key;
        }

        count++;
        loadItems();

        document.getElementById('add_item').value = '' ;
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

            sItems[item_id].qty= newQty;
            var code = sItems[item_id].code
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

        $('#myTable tbody').empty();
        $.each(sItems,function (i,item) {
            console.log(item);

            var newTr = $('<tr data-item-id="'+item.id+'" data-price="'+item.price+'" data-promo-price="'+item.cost+'" data-currency="SR">');
            var tr_html ='<td class="text-center"><input type="hidden" name="product_id[]" value="'+item.code+'"> <span>'+item.name +'</span>';
            if(item.variant_color || item.variant_size){
                tr_html += '<div style="font-size: 11px; color:#555;">'+(item.variant_color ?? '')+' '+(item.variant_size ?? '')+'</div>';
            }
            tr_html += '</td>';
                tr_html +='<td class="product-code">'+(item.code)+'</td>';
                tr_html +='<td class="text-center"><input type="number" class="form-control qty" name="qty[]" value="'+item.qty.toFixed(2)+'"></td>';
                tr_html +=`<td class="text-center">
                                <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+item.id+' ">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>`;

            newTr.html(tr_html);
            newTr.appendTo('#myTable');
        });

    }
</script>
@endsection 
