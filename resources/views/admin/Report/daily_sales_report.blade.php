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
                            {{__('main.daily_sales_report')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div> 
                <div class="card-body">
                    <form id="daily-sales-report-form">
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
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                               <input type="date"  id="bill_date" name="bill_date"
                                      class="form-control"
                               />
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>{{ __('main.customer') }}</label>
                               <select class="js-example-basic-single w-100"
                                       name="customer_id" id="customer_id">
                                   <option  value="0" selected>{{__('main.all')}}</option>
                                   @foreach ($customers as $customer)
                                       <option value="{{$customer -> id}}"> {{ $customer -> name}}</option>
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
                               <label>{{ __('main.vehicle_plate') }}</label>
                               <input type="text"  id="vehicle_plate" name="vehicle_plate"
                                      class="form-control" list="dailyVehicleOptions"
                                      placeholder="{{ __('main.vehicle_plate') }}"/>
                               <datalist id="dailyVehicleOptions"></datalist>
                           </div>
                       </div> 
                    </div> 
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px;
                                    margin: 30px auto;">{{__('main.report')}}</button>
                            <span id="daily-sales-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </div>  
                    </form>
                </div>
            </div>
        </div> 
    </div> 

<div class="modal fade" id="dailySalesReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('main.daily_sales_report') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="daily-sales-pdf-viewer"
                        src=""
                        style="width:100%; height:80vh; border:none;"></iframe>
                <div id="daily-sales-pdf-error" class="alert alert-danger mt-3 d-none"></div>
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
        const customerVehiclesCache = {};
        const vehicleInput = $('#vehicle_plate');
        const vehicleOptionsList = $('#dailyVehicleOptions');
        const customerSelect = $('#customer_id');

        function renderVehicleOptions(vehicles){
            if(!vehicleOptionsList.length){
                return;
            }
            vehicleOptionsList.empty();
            (vehicles || []).forEach(function(vehicle){
                if(!vehicle || !vehicle.vehicle_plate){
                    return;
                }
                var label = vehicle.vehicle_plate;
                if(vehicle.vehicle_odometer !== undefined && vehicle.vehicle_odometer !== null && vehicle.vehicle_odometer !== ''){
                    label += ' ('+vehicle.vehicle_odometer+')';
                }
                var option = $('<option>')
                    .attr('value', vehicle.vehicle_plate)
                    .attr('data-odometer', vehicle.vehicle_odometer ?? '');
                option.text(label);
                vehicleOptionsList.append(option);
            });
        }

        function fetchCustomerVehicles(customerId){
            if(!vehicleOptionsList.length){
                return;
            }
            if(!customerId || Number(customerId) === 0){
                renderVehicleOptions([]);
                return;
            }
            if(customerVehiclesCache[customerId]){
                renderVehicleOptions(customerVehiclesCache[customerId]);
                return;
            }
            var url = "{{ route('customers.vehicles', ['customer' => ':id']) }}";
            url = url.replace(':id', customerId);
            $.get(url)
                .done(function(response){
                    customerVehiclesCache[customerId] = response || [];
                    renderVehicleOptions(customerVehiclesCache[customerId]);
                })
                .fail(function(){
                    renderVehicleOptions([]);
                });
        }

        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null); 
        document.getElementById('bill_date').valueAsDate = new Date();
        $('#excute').click(async function (){
            const date = document.getElementById('bill_date').value ;
            const warehouse = document.getElementById('warehouse_id').value ;
            const branch_id = document.getElementById('branch_id').value ;
            const customer_id = document.getElementById('customer_id').value || 0;
            const cost_center_id = document.getElementById('cost_center_id').value || 0;
            const vehicle_plate = vehicleInput.val() ? vehicleInput.val().trim() : 'empty';
            await fetchDailySalesPdf({
                bill_date: date,
                warehouse_id: warehouse,
                branch_id: branch_id,
                customer_id: customer_id,
                vehicle_plate: vehicle_plate,
                cost_center_id: cost_center_id,
            });
        });

        if(customerSelect.length){
            customerSelect.on('change', function(){
                fetchCustomerVehicles($(this).val());
            });
            fetchCustomerVehicles(customerSelect.val());
        }

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

        document.title = "{{__('main.daily_sales_report')}}";
    });

    function showDailySalesPdfError(message) {
        const errorBox = document.getElementById('daily-sales-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearDailySalesPdfError() {
        const errorBox = document.getElementById('daily-sales-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setDailySalesPdfLoading(isLoading) {
        const spinner = document.getElementById('daily-sales-pdf-spinner');
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

    async function fetchDailySalesPdf(params) {
        clearDailySalesPdfError();
        setDailySalesPdfLoading(true);
        try {
            const query = new URLSearchParams(params);
            const response = await fetch("{{ route('daily_sales_report_pdf') }}?" + query.toString(), {
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
            if (window.dailySalesReportBlobUrl) {
                URL.revokeObjectURL(window.dailySalesReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.dailySalesReportBlobUrl = blobUrl;

            const viewer = document.getElementById('daily-sales-pdf-viewer');
            viewer.src = blobUrl;
            $('#dailySalesReportModal').modal('show');
        } catch (error) {
            showDailySalesPdfError(error.message);
        } finally {
            setDailySalesPdfLoading(false);
        }
    }

    $('#dailySalesReportModal').on('hidden.bs.modal', function () {
        if (window.dailySalesReportBlobUrl) {
            URL.revokeObjectURL(window.dailySalesReportBlobUrl);
            window.dailySalesReportBlobUrl = null;
        }
        const viewer = document.getElementById('daily-sales-pdf-viewer');
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
