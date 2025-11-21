@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 text-center">
                    <div class="col-lg-12 margin-tb ">
                        <h4  class="alert alert-primary text-center"> 
                          [ {{__('اضافة قيد يدوي')}} ]
                        </h4>
                    </div> 
                </div>  
            </div> 
                <div class="card-body px-0 pt-0 pb-2">
                   <div class="card shadow mb-4">  
                   <div class="card-body">
                    <form   method="POST" action="{{ route('store_manual') }}"
                            enctype="multipart/form-data" >
                        @csrf

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>رقم القيد <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                    <input id="bill_number" readonly class="form-control" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="datetime-local"  id="date" name="date"
                                       class="form-control"/> 
                            </div>  
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                    <textarea name="notes" id="notes" rows="3" class="form-control-lg" style="width: 100%"></textarea>
                                </div>
                            </div>
                        </div> 
                        <div class="row"> 
                                <div class="col-md-12" id="sticker">
                                    <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                        <div class="form-group" style="margin-bottom:0;">
                                            <div class="input-group wide-tip">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-3x fa-barcode addIcon"></i>
                                                </div>
                                                <input style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                                       type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input" id="add_item" placeholder="{{__('main.search_journal')}}" autocomplete="off">
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
                                        <h4  class="alert alert-info text-center">{{__('الحسابات')}} </h4>
                                    </div>   
                                    <div class="card-body px-0 pt-0 pb-2">
                                        <div class="table-responsive p-0"> 
                                            <table id="sTable" style="width:100%" class="table align-items-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">{{__('main.account_code')}}</th>
                                                        <th class="text-center">{{__('main.account_name')}}</th>
                                                        <th class="text-center">{{__('main.Debit')}} </th>
                                                        <th class="text-center">{{__('main.Credit')}}</th> 
                                                        <th style="max-width: 30px !important; text-align: center;" class="text-center">
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


                        <div class="row">
                            <div class="col-md-12 text-center">
                                <input type="submit" class="btn btn-primary" id="primary" tabindex="-1"
                                       style="width: 150px; 
                                       margin: 30px auto;" value="{{__('main.save_btn')}}"></input>

                            </div>
                        </div> 
                    </form> 
                </div> 
            </div>
            <!-- /.container-fluid -->
            <input id="local" value="{{Config::get('app.locale')}}" hidden>
        </div>
        <!-- End of Main Content --> 
    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper --> 

@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script> 

<script type="text/javascript"> 
    function getDocNo(){

        let bill_number = document.getElementById('bill_number');
        $.ajax({
            type:'get',
            url:'{{route('manual_number')}}',
            dataType: 'json',

            success:function(response){
                console.log(response);

                if(response){
                    bill_number.value = response ;
                } else {
                    bill_number.value = '' ;
                }
            }
        });
    }


    var suggestionItems = {};
    var sItems = {};
    var count = 1;

    $(document).ready(function() {
        var now = new Date();
        local = document.getElementById('local').value ;
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        now.setMilliseconds(null);
        now.setSeconds(null);

        document.getElementById('date').value = now.toISOString().slice(0, -1);
        getDocNo();
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
 
        $(document).on('click', '.close-create', function (event) {
            $('#paymentsModal').modal("hide");
            
        });
    });


    function getBillNo(){

        {{--let bill_number = document.getElementById('bill_number');--}}
        {{--$.ajax({--}}
        {{--    type:'get',--}}
        {{--    url:'{{route('get_sale_no')}}',--}}
        {{--    dataType: 'json',--}}

        {{--    success:function(response){--}}
        {{--        console.log(response);--}}

        {{--        if(response){--}}
        {{--            bill_number.value = response ;--}}
        {{--        } else {--}}
        {{--            bill_number.value = '' ;--}}
        {{--        }--}}
        {{--    }--}}
        {{--});--}}
    }
    function searchProduct(code){
        var url = '{{route('getAccounts',":id")}}';
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


        var time = new Date();
        sItems[time] = item;
        sItems[time].date_item_id = time;
        sItems[time].credit = 0;
        sItems[time].debit = 0;

        count++;
        loadItems();
        document.getElementById('add_item').value = '' ;
    }

    var old_row_qty=0;
    var old_row_price = 0;
    var old_row_w_price = 0;

    $(document)
        .on('focus','.iCredit',function () {
            old_row_qty = $(this).val();
        })
        .on('change','.iCredit',function () {
            var row = $(this).closest('tr');
            if(!is_numeric($(this).val()) || parseFloat($(this).val()) < 0){
                $(this).val(old_row_qty);
                alert('wrong value');
                return;
            }

            var newQty = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');

            sItems[item_id].credit= newQty;
            sItems[item_id].debit= 0;
            loadItems();

        });


    $(document)
        .on('focus','.iDebit',function () {
            old_row_price = $(this).val();
        })
        .on('change','.iDebit',function () {
            var row = $(this).closest('tr');
            if(!is_numeric($(this).val()) || parseFloat($(this).val()) < 0){
                $(this).val(old_row_price);
                alert('wrong value');
                return;
            }

            var newQty = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');


            sItems[item_id].credit= 0;
            sItems[item_id].debit= newQty;
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
        var totalCredit= 0,totalDebit = 0;
        $.each(sItems,function (i,item) {


            totalCredit += item.credit;
            totalDebit += item.debit;
            var newTr = $('<tr data-item-id="'+item.date_item_id+'">');
            var tr_html ='<td class="text-center"><input type="hidden" name="account_id[]" value="'+item.id+'"> <span>'+item.code+'</span> </td>';
            tr_html +=   '<td class="text-center">'+item.name+'</td>';
            tr_html +=   '<td class="text-center"><input type="text" class="form-control iDebit" name="debit[]" value="'+item.debit.toFixed(2)+'"></td>';
            tr_html +=   '<td class="text-center"><input type="text" class="form-control iCredit" name="credit[]" value="'+item.credit.toFixed(2)+'"></td>';
            tr_html += `<td class="text-center">      <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+item.id+' ">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-trash"></i></span></button> </td>`;

            newTr.html(tr_html);
            newTr.appendTo('#sTable');
        });


        if(totalCredit !== totalDebit){
            $(':input[type="submit"]').prop('disabled', true);
        }else{
            $(':input[type="submit"]').prop('disabled', false);
        }


    }
 
</script>
 
 





