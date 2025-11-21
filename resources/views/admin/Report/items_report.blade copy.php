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
                            {{__('main.items_report')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div> 
                <div class="card-body">
                        <div class="row">
                            <div class="col-6">
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
                            <div class="col-6" >
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
            const type  = document.getElementById('type').value;

            if(type == 0){
                showReport( category , brand);
            } else if(type == 1){
                showReport2( category , brand);
            } else if(type == 2){
                showReport3( category , brand);
            }  
        }); 

        $(document).on('click', '.cancel-modal', function (event) {
            $('#items_modal').modal("hide"); 
        });

        document.title = "{{__('main.items_report')}}";
    });

    function showReport(category , brand ) {
        var route = '{{route('items_report_search',[ ":category" , ":brand" ] )}}';
        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        console.log(route);
        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');
        });
    }

    function showReport2(category , brand ) {
        var route = '{{route('items_limit_report_search',[ ":category" , ":brand" ] )}}';
        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        console.log(route);
        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');

        });
    }

    function showReport3(category , brand ) {
        var route = '{{route('items_no_balance_report_search',[ ":category" , ":brand" ] )}}';
        route = route.replace(":category",category );
        route = route.replace(":brand",brand );
        console.log(route);
        $.get( route, function( data ) {
            $( ".show_modal" ).innerHTML = "" ;
            $( ".show_modal" ).html( data );
            $('#items_modal').modal('show');
        });
    }

</script>
@endsection 