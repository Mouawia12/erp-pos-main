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
                            {{__('main.incoming_list_report')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div> 
                <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table  id="table" class="table align-items-center mb-0 border">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.code')}}</th>
                                        <th>{{__('main.name')}}</th>
                                        <th>{{__('main.Credit')}}</th>
                                        <th>{{__('main.Debit')}}</th>
                                        <th>{{__('main.balance')}}</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                @foreach($accounts as $index=>$unit)
                                    <tr>
                                        <td class="text-center">{{$index++}}</td>
                                        <td class="text-center">{{$unit->code}}</td>
                                        <td class="text-center">{{$unit->name}}</td>
                                        <td class="text-center">{{$unit->credit}}</td>
                                        <td class="text-center">{{$unit->debit}}</td>
                                        <td class="text-center">{{$unit->credit - $unit->debit}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
{{--                    <div class="card-body px-0 pt-0 pb-2">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>{{ __('main.from_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>--}}
{{--                                    <input type="checkbox" name="is_from_date" id="is_from_date"/>--}}
{{--                                    <input type="date"  id="from_date" name="from_date"--}}
{{--                                           class="form-control"--}}
{{--                                    />--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>{{ __('main.to_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>--}}
{{--                                    <input type="checkbox" name="is_to_date" id="is_to_date"/>--}}
{{--                                    <input type="date"  id="to_date" name="to_date"--}}
{{--                                           class="form-control"--}}
{{--                                    />--}}
{{--                                </div>--}}
{{--                            </div>--}}


{{--                        </div>--}}


{{--                        <div class="row">--}}
{{--                            <div class="col-md-12 text-center">--}}
{{--                                <input type="submit" class="btn btn-primary" id="excute" tabindex="-1"--}}
{{--                                       style="width: 150px;--}}
{{--margin: 30px auto;" value="{{__('main.excute_btn')}}"></input>--}}

{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
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
    $(document).ready(function() {
        $('table').dataTable();
    }); 
</script>
@endsection 