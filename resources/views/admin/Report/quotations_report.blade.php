@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    @can('التقارير المخزون')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4 class="alert alert-primary text-center">
                            {{ __('main.quotations_report') }}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.quotations') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('الفرع') }}</label>
                                    @if(empty(Auth::user()->branch_id))
                                        <select name="branch_id" id="branch_id" class="js-example-basic-single w-100">
                                            <option value="0">{{ __('main.all') }}</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" @if(request('branch_id')==$branch->id) selected @endif>
                                                    {{$branch->branch_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input class="form-control" type="text" readonly value="{{Auth::user()->branch->branch_name}}"/>
                                        <input class="form-control" type="hidden" id="branch_id" name="branch_id"
                                               value="{{Auth::user()->branch_id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.warehouse') }}</label>
                                    <select class="js-example-basic-single w-100" name="warehouse_id" id="warehouse_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" @if(request('warehouse_id')==$warehouse->id) selected @endif>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.customer') }}</label>
                                    <select class="js-example-basic-single w-100" name="customer_id" id="customer_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}" @if(request('customer_id')==$customer->id) selected @endif>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.representatives') }}</label>
                                    <select class="js-example-basic-single w-100" name="representative_id" id="representative_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($representatives as $rep)
                                            <option value="{{$rep->id}}" @if(request('representative_id')==$rep->id) selected @endif>
                                                {{ $rep->user_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.cost_center') }}</label>
                                    <select class="js-example-basic-single w-100" name="cost_center_id" id="cost_center_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($costCenters as $center)
                                            <option value="{{$center->id}}" @if(request('cost_center_id')==$center->id) selected @endif>
                                                {{$center->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.status') }}</label>
                                    <select class="form-control" name="status">
                                        <option value="">{{ __('main.all') }}</option>
                                        <option value="draft" @if(request('status')==='draft') selected @endif>draft</option>
                                        <option value="converted" @if(request('status')==='converted') selected @endif>converted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.quotation_no') }}</label>
                                    <input type="text" class="form-control" name="quotation_no" value="{{ request('quotation_no') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.from_date') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.to_date') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" style="width: 150px; margin: 30px auto;">
                                    {{ __('main.report') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.quotation_no') }}</th>
                                    <th>{{ __('main.date') }}</th>
                                    <th>{{ __('main.customer') }}</th>
                                    <th>{{ __('main.representatives') }}</th>
                                    <th>{{ __('الفرع') }}</th>
                                    <th>{{ __('main.warehouse') }}</th>
                                    <th>{{ __('main.cost_center') }}</th>
                                    <th>{{ __('main.total') }}</th>
                                    <th>{{ __('main.tax') }}</th>
                                    <th>{{ __('main.net') }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $row)
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->quotation_no }}</td>
                                        <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '' }}</td>
                                        <td>{{ $row->customer_name_display ?? $row->customer_name }}</td>
                                        <td>{{ $row->representative_name ?? '-' }}</td>
                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                        <td>{{ $row->warehouse_name ?? '-' }}</td>
                                        <td>{{ $row->cost_center_name ?? $row->cost_center ?? '-' }}</td>
                                        <td>{{ number_format($row->total ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->tax ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->net ?? 0, 2) }}</td>
                                        <td>{{ $row->status }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-info" href="{{ route('quotations.show', $row->id) }}">
                                                {{ __('main.preview') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-end">{{ __('main.total') }}</th>
                                    <th>{{ number_format($summary['total'] ?? 0, 2) }}</th>
                                    <th>{{ number_format($summary['tax'] ?? 0, 2) }}</th>
                                    <th>{{ number_format($summary['net'] ?? 0, 2) }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#branch_id').change(function (){
            var url = '{{route('get.warehouses.branches',":id")}}';
            url = url.replace(":id", this.value);
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    if (response) {
                        $('#warehouse_id')
                            .empty();
                        $('#warehouse_id').append('<option value="0">{{ __('main.all') }}</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                }
            });
        });

        document.title = "{{ __('main.quotations_report') }}";
    });
</script>
@endsection
