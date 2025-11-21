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
        $('#excute').click(function (){ 
            const category = document.getElementById('category_id').value;
            const brand = document.getElementById('brand_id').value; 
            const warehouse = document.getElementById('warehouse_id').value;
            const branch_id = document.getElementById('branch_id').value ;
            const type  = document.getElementById('type').value;
            
            if(type == 0){
                showReport( category, brand, warehouse, branch_id);
            } else if(type == 1){
                showReport2( category, brand, warehouse, branch_id);
            } else if(type == 2){
                showReport3( category, brand, warehouse, branch_id);
            }  
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

        $(document).on('click', '.cancel-modal', function (event) {
            $('#items_modal').modal("hide"); 
        });

        document.title = "{{__('main.items_report')}}";
    });

    function showReport(category,brand,warehouse,branch_id) {
        var route = '{{route('items.report.search',[":category",":brand",":warehouse",":branch_id"] )}}';

        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        route = route.replace(":warehouse", warehouse ? warehouse : 0);
        route = route.replace(":branch_id", branch_id ? branch_id : 0);
        console.log(route);

        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');
        });
    }

    function showReport2(category, brand, warehouse, branch_id) {

        var route = '{{route('items.limit.report.search',[ ":category", ":brand",":warehouse",":branch_id"])}}';

        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        route = route.replace(":warehouse", warehouse ? warehouse : 0);
        route = route.replace(":branch_id", branch_id ? branch_id : 0);
        console.log(route);

        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');

        });
    }

    function showReport3(category , brand , warehouse, branch_id) {

        var route = '{{route('items.no.balance.report.search',[ ":category", ":brand",":warehouse",":branch_id"])}}';

        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        route = route.replace(":warehouse", warehouse ? warehouse : 0);
        route = route.replace(":branch_id", branch_id ? branch_id : 0);
        console.log(route);

        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');
        });
    }

</script>
@endsection 