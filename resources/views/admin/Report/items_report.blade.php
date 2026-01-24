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
                        @if($type == 0)
                        {{__('main.items_report')}}
                        @elseif($type == 1)
                            {{__('main.under_limit_items_report')}}
                        @elseif($type == 2)
                            {{__('main.no_balance_items_report')}}
                        @endif
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div> 
                <div class="card-body">
                    <form id="items-report-form">
                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6" >
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
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.categories') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="category_id" id="category_id">
                                    <option  value="0" selected>{{__('main.all')}}</option>
                                    @foreach ($categories as $item)
                                        <option value="{{$item -> id}}"> {{ $item -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.brands') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="brand_id" id="brand_id">
                                    <option  value="0" selected>{{__('main.all')}}</option>
                                    @foreach ($brands as $item)
                                        <option value="{{$item -> id}}"> {{ $item -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <input type="hidden" value="{{$type}}" id="type" name="type">
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px;
                                   margin: 30px auto;">{{__('main.report')}}</button>
                            <span id="items-report-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </div> 
                    </form>
                    </div>
                </div>
            </div>
        </div> 
    </div> 

<div class="modal fade" id="itemsReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if($type == 0)
                        {{__('main.items_report')}}
                    @elseif($type == 1)
                        {{__('main.under_limit_items_report')}}
                    @else
                        {{__('main.no_balance_items_report')}}
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="items-report-pdf-viewer"
                        src=""
                        style="width:100%; height:80vh; border:none;"></iframe>
                <div id="items-report-pdf-error" class="alert alert-danger mt-3 d-none"></div>
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
        $('#excute').click(async function (){ 
            const category = document.getElementById('category_id').value;
            const brand = document.getElementById('brand_id').value; 
            const warehouse = document.getElementById('warehouse_id').value;
            const branch_id = document.getElementById('branch_id').value ;
            const type  = document.getElementById('type').value;
            
            await fetchItemsReportPdf({
                category_id: category,
                brand_id: brand,
                warehouse_id: warehouse,
                branch_id: branch_id,
                type: type,
            });
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
                        $('#warehouse_id')
                            .empty();
                        $('#warehouse_id').append('<option value="0">حدد الاختيار ..</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                } 
            });
        });

        document.title = "{{__('main.items_report')}}";
    });

    function showItemsReportPdfError(message) {
        const errorBox = document.getElementById('items-report-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearItemsReportPdfError() {
        const errorBox = document.getElementById('items-report-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setItemsReportPdfLoading(isLoading) {
        const spinner = document.getElementById('items-report-pdf-spinner');
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

    async function fetchItemsReportPdf(params) {
        clearItemsReportPdfError();
        setItemsReportPdfLoading(true);
        try {
            const query = new URLSearchParams(params);
            const response = await fetch("{{ route('items_report_pdf') }}?" + query.toString(), {
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
            if (window.itemsReportBlobUrl) {
                URL.revokeObjectURL(window.itemsReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.itemsReportBlobUrl = blobUrl;

            const viewer = document.getElementById('items-report-pdf-viewer');
            viewer.src = blobUrl;
            $('#itemsReportModal').modal('show');
        } catch (error) {
            showItemsReportPdfError(error.message);
        } finally {
            setItemsReportPdfLoading(false);
        }
    }

    $('#itemsReportModal').on('hidden.bs.modal', function () {
        if (window.itemsReportBlobUrl) {
            URL.revokeObjectURL(window.itemsReportBlobUrl);
            window.itemsReportBlobUrl = null;
        }
        const viewer = document.getElementById('items-report-pdf-viewer');
        if (viewer) {
            viewer.src = '';
        }
    });

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
