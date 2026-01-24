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
                            {{__('main.purchases_report')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div> 
                <div class="card-body"> 
                    <form id="purchase-report-form">
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
                                <label>{{ __('main.supplier') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="vendor_id" id="vendor_id">
                                    <option  value="0" selected>{{__('main.all')}}</option>
                                    @foreach ($vendors as $item)
                                        <option value="{{$item -> id}}"> {{ $item -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.cost_center') }}</label>
                                <select class="js-example-basic-single w-100" name="cost_center_id" id="cost_center_id">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($costCenters as $center)
                                        <option value="{{$center->id}}">{{$center->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                 <label>{{ __('main.bill_number') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                 <input type="text"  id="bill_no" name="bill_no"
                                        class="form-control"
                                 />
                             </div>
                         </div>
                    </div>  
                       <div class="row">
                           <div class="col-md-6">
                               <div class="form-group">
                                   <label>{{ __('main.from_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                   <input type="checkbox" name="is_from_date" id="is_from_date"/>
                                   <input type="date"  id="from_date" name="from_date"
                                          class="form-control"
                                   />
                               </div>
                           </div>
                           <div class="col-md-6">
                               <div class="form-group">
                                   <label>{{ __('main.to_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
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
                                <span id="purchase-report-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                            </div>
                        </div> 
                    </form>
                    </div>
                </div>
            </div>
        </div>  
    </div> 

<div class="modal fade" id="purchaseReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('main.purchases_report') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="purchase-report-pdf-viewer"
                        src=""
                        style="width:100%; height:80vh; border:none;"></iframe>
                <div id="purchase-report-pdf-error" class="alert alert-danger mt-3 d-none"></div>
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
            if (!$('#is_to_date').is(":checked"))
            {
                toDate = '0';
            } else {
                toDate =  document.getElementById('to_date').value.toString() ;
            }

            const warehouse = document.getElementById('warehouse_id').value;
            const bill = document.getElementById('bill_no').value;
            const vendor = document.getElementById('vendor_id').value;
            const branch_id = document.getElementById('branch_id').value ;
            const cost_center_id = document.getElementById('cost_center_id').value;

            var bill_no  ='empty';
            if(bill) bill_no = bill ;
            await fetchPurchaseReportPdf({
                from_date: fromDate,
                to_date: toDate,
                warehouse_id: warehouse,
                bill_no: bill_no,
                vendor_id: vendor,
                branch_id: branch_id,
                cost_center_id: cost_center_id,
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

        document.title = "{{__('main.purchases_report')}}";
    });

    function showPurchasePdfError(message) {
        const errorBox = document.getElementById('purchase-report-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearPurchasePdfError() {
        const errorBox = document.getElementById('purchase-report-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setPurchasePdfLoading(isLoading) {
        const spinner = document.getElementById('purchase-report-pdf-spinner');
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

    async function fetchPurchaseReportPdf(params) {
        clearPurchasePdfError();
        setPurchasePdfLoading(true);
        try {
            const query = new URLSearchParams(params);
            const response = await fetch("{{ route('purchase_report_pdf') }}?" + query.toString(), {
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
            if (window.purchaseReportBlobUrl) {
                URL.revokeObjectURL(window.purchaseReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.purchaseReportBlobUrl = blobUrl;

            const viewer = document.getElementById('purchase-report-pdf-viewer');
            viewer.src = blobUrl;
            $('#purchaseReportModal').modal('show');
        } catch (error) {
            showPurchasePdfError(error.message);
        } finally {
            setPurchasePdfLoading(false);
        }
    }

    $('#purchaseReportModal').on('hidden.bs.modal', function () {
        if (window.purchaseReportBlobUrl) {
            URL.revokeObjectURL(window.purchaseReportBlobUrl);
            window.purchaseReportBlobUrl = null;
        }
        const viewer = document.getElementById('purchase-report-pdf-viewer');
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
