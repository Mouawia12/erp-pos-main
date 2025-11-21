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
                        <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
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