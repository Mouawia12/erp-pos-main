@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('التقارير المخزون')   
    <!-- End Navbar -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                            {{__('main.imported_items_reports')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div>  
                <div class="card-body">
                    <form id="items-purchased-report-form">
                   <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label>{{ __('الفرع') }} <span class="text-danger">*</span> </label>
                                @if(empty(Auth::user()->branch_id))
                                    <select name="branch_id" id="branch_id" class="js-example-basic-single w-100" required>
                                        <option value="0">الكل</option>
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
                        <div class="col-md-4" >
                           <div class="form-group">
                               <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                               <select class="js-example-basic-single w-100"
                                       name="warehouse_id" id="warehouse_id">
                                   <option  value="0" selected>{{__('main.all')}}</option>
                                   @foreach ($warehouses as $warehouse)
                                       <option value="{{$warehouse -> id}}"> {{ $warehouse -> name}}</option>
                                   @endforeach
                               </select>
                           </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.supplier') }} <span class="text-danger">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="supplier_id" id="supplier_id">
                                    <option  value="0" selected>{{__('main.all')}}</option>
                                    @foreach ($vendors as $item)
                                        <option value="{{$item -> id}}"> {{ $item -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>   
                    <div class="row"> 
                        <div class="col-md-12">
                            <div class="col-md-12" id="sticker">
                                <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <div class="input-group wide-tip">
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                            <input style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                                   type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input" id="add_item" placeholder="{{__('main.add_item_hint')}}" autocomplete="off">
                                              <input type="hidden" id="item_id" name="item_id" value="0">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.from_date') }} <span class="text-danger">*</span> </label>
                                <input type="checkbox" name="is_from_date" id="is_from_date" required/>
                                <input type="date"  id="from_date" name="from_date"
                                       class="form-control" required/>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.to_date') }} <span class="text-danger">*</span> </label>
                                <input type="checkbox" name="is_to_date" id="is_to_date"/>
                                <input type="date"  id="to_date" name="to_date"
                                       class="form-control"
                                />
                            </div>
                        </div> 
                    </div>   
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px;
                                    margin: 30px auto;">{{__('main.report')}}</button>
                            <span id="items-purchased-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </div> 
                    </form>
                </div>
            </div>
        </div>
    </div> 
</div> 

<div class="modal fade" id="itemsPurchasedReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('main.imported_items_reports') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="items-purchased-pdf-viewer"
                        src=""
                        style="width:100%; height:80vh; border:none;"></iframe>
                <div id="items-purchased-pdf-error" class="alert alert-danger mt-3 d-none"></div>
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
        $('#is_from_date').prop('checked' , false);
        $('#from_date').attr('disabled' , true);
        $('#is_to_date').prop('checked' , false);
        $('#to_date').attr('disabled' , true); 

        $('#is_from_date').change(function (){
            $('#from_date').attr('disabled' , !this.checked);
        });

        $('#is_to_date').change(function (){
            $('#to_date').attr('disabled' , !this.checked);
        });

        document.getElementById('from_date').valueAsDate = new Date();
        document.getElementById('to_date').valueAsDate = new Date();

        $('#excute').click(async function (){
            var fromDate = '' ;
            var toDate = '' ;
            if (!$('#is_from_date').is(":checked"))
            {
                fromDate = '0';
            } else {
                fromDate =  document.getElementById('from_date').value.toString() ;
            }

            if ($('#is_to_date').is(":checked"))
            {
                toDate =  document.getElementById('to_date').value.toString() ;
            } else { 
                toDate = "{{\Carbon\Carbon::now()-> format('Y-m-d')}}";
            }

            const warehouse = document.getElementById('warehouse_id').value;
            const supplier = document.getElementById('supplier_id').value;
            const branch_id = document.getElementById('branch_id').value;

            var item ;
            var item_id ;

            if(document.getElementById('add_item').value ){
                item = document.getElementById('item_id').value ;
                item_id = item ?? 0 ;
            } else {
                item_id = 0 ;
            }

           await fetchItemsPurchasedPdf({
                from_date: fromDate,
                to_date: toDate,
                warehouse_id: warehouse,
                branch_id: branch_id,
                item_id: item_id,
                supplier_id: supplier,
           }); 
        });

        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val());
        });

        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val());
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            document.getElementById('item_id').value = item_id ;
            var item = suggestionItems[item_id] ;
            document.getElementById('add_item').value = item.name + '--' + item.code ;
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        });

        $('#branch_id').change(function (){
            var url = '{{route('get.warehouses.branches',":id")}}';
            url = url.replace(":id", this.value);
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('#warehouse_id').empty();
                        $('#warehouse_id').append('<option value="0">حدد الاختيار ..</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                } 
            });
        });

        document.title = "{{__('main.imported_items_reports')}}";
    });

    function showItemsPurchasedPdfError(message) {
        const errorBox = document.getElementById('items-purchased-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearItemsPurchasedPdfError() {
        const errorBox = document.getElementById('items-purchased-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setItemsPurchasedPdfLoading(isLoading) {
        const spinner = document.getElementById('items-purchased-pdf-spinner');
        const button = document.getElementById('excute');
        if (!spinner || !button) {
            return;
        }
        if (isLoading) {
            spinner.classList.remove('d-none');
            button.setAttribute('disabled', 'disabled');
        } else {
            spinner.classList.add('d-none');
            button.removeAttribute('disabled');
        }
    }

    async function fetchItemsPurchasedPdf(params) {
        clearItemsPurchasedPdfError();
        setItemsPurchasedPdfLoading(true);
        try {
            const query = new URLSearchParams(params);
            const response = await fetch("{{ route('items_purchased_report_pdf') }}?" + query.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const payload = await response.json();
                throw new Error(payload.message || 'تعذر إنشاء التقرير.');
            }
            if (!response.ok) {
                throw new Error('تعذر إنشاء التقرير.');
            }

            const blob = await response.blob();
            if (window.itemsPurchasedReportBlobUrl) {
                URL.revokeObjectURL(window.itemsPurchasedReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.itemsPurchasedReportBlobUrl = blobUrl;

            const viewer = document.getElementById('items-purchased-pdf-viewer');
            viewer.src = blobUrl;
            $('#itemsPurchasedReportModal').modal('show');
        } catch (error) {
            showItemsPurchasedPdfError(error.message);
        } finally {
            setItemsPurchasedPdfLoading(false);
        }
    }

    $('#itemsPurchasedReportModal').on('hidden.bs.modal', function () {
        if (window.itemsPurchasedReportBlobUrl) {
            URL.revokeObjectURL(window.itemsPurchasedReportBlobUrl);
            window.itemsPurchasedReportBlobUrl = null;
        }
        const viewer = document.getElementById('items-purchased-pdf-viewer');
        if (viewer) {
            viewer.src = '';
        }
    });

    function searchProduct(code){
        console.log(code);
        var url = '{{route('getProduct',":id")}}';
        url = url.replace(":id",code);
        $.ajax({
            type:'get',
            url:url,
            dataType: 'json', 
            success:function(response){
                console.log(response);
                document.getElementById('products_suggestions').innerHTML = '';
                suggestionItems = {};
                document.getElementById('item_id').value = 0 ;
                if(response){
                    if(response.length == 1){
                        //addItemToTable
                        //addItemToTable(response[0]);
                        showSuggestions(response);
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
</script>
<style>
    .pdf-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-inline-start: 8px;
        border: 2px solid #cfd4da;
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: pdf-spin 0.7s linear infinite;
        vertical-align: middle;
    }
    @keyframes pdf-spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection 
