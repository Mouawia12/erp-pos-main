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
                            <input type="submit" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px;
                                    margin: 30px auto;" value="{{__('main.report')}}">
                            </input> 
                        </div>
                    </div>  
                </div>
            </div>
        </div> 
    </div> 

<div class="show_modal">

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
        $('#excute').click(function (){
            const date = document.getElementById('bill_date').value ;
            const warehouse = document.getElementById('warehouse_id').value ;
            const branch_id = document.getElementById('branch_id').value ;
            const customer_id = document.getElementById('customer_id').value || 0;
            const vehicle_plate = vehicleInput.val() ? vehicleInput.val().trim() : 'empty';
            showReport(date , warehouse, branch_id, customer_id, vehicle_plate);
        });

        if(customerSelect.length){
            customerSelect.on('change', function(){
                fetchCustomerVehicles($(this).val());
            });
            fetchCustomerVehicles(customerSelect.val());
        }

        $(document).on('click', '.modal-close-btn', function (event) {
            $('#daily_sales_modal').modal("hide"); 
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

        document.title = "{{__('main.daily_sales_report')}}";
    });

    function showReport(date , warehouse, branch_id, customer_id, vehicle_plate) {

        var route = '{{route('daily.sales.report.search',[":date" , ":warehouse",":branch_id",":customer",":vehicle"] )}}';

        route = route.replace(":date", date);
        route = route.replace(":warehouse", warehouse ? warehouse : 0);
        route = route.replace(":branch_id", branch_id ? branch_id : 0);
        route = route.replace(":customer", customer_id ? customer_id : 0);
        var encodedPlate = vehicle_plate ? encodeURIComponent(vehicle_plate) : 'empty';
        route = route.replace(":vehicle", encodedPlate ? encodedPlate : 'empty');

        $.get(route, function( data ) {
            $(".show_modal" ).html( data );
            $('#daily_sales_modal').modal('show');
        });
    }

    $(document).on('click' , '.modal-close-btn' , function (event) {
        $('#daily_sales_modal').modal("hide");
        id = 0 ;
    }); 
</script>
@endsection 
