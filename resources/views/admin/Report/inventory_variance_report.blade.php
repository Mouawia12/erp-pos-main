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
                            {{ __('main.inventory_variance_report') }}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.inventory_variance') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('main.inventory') }}</label>
                                    <select name="inventory_id" class="js-example-basic-single w-100">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($inventories as $inv)
                                            <option value="{{$inv->id}}" @if($inventorySelected==$inv->id) selected @endif>
                                                #{{$inv->id}} - {{ $inv->date ? \Carbon\Carbon::parse($inv->date)->format('Y-m-d') : '' }} - {{ $inv->warehouse?->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('الفرع') }}</label>
                                    @if(empty(Auth::user()->branch_id))
                                        <select name="branch_id" id="branch_id" class="js-example-basic-single w-100">
                                            <option value="0">{{ __('main.all') }}</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" @if($branchSelected==$branch->id) selected @endif>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('main.warehouse') }}</label>
                                    <select class="js-example-basic-single w-100" name="warehouse_id" id="warehouse_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" @if($warehouseSelected==$warehouse->id) selected @endif>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.difference') }}</label>
                                    <select name="difference_type" class="form-control">
                                        <option value="all" @if($differenceType==='all') selected @endif>{{ __('main.all') }}</option>
                                        <option value="shortage" @if($differenceType==='shortage') selected @endif>{{ __('main.shortage') }}</option>
                                        <option value="excess" @if($differenceType==='excess') selected @endif>{{ __('main.excess') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
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
                                    <th>{{ __('main.item_name_code') }}</th>
                                    <th>{{ __('main.inventory') }}</th>
                                    <th>{{ __('main.warehouse') }}</th>
                                    <th>{{ __('الفرع') }}</th>
                                    <th>{{ __('main.quantity') }}</th>
                                    <th>{{ __('main.counted_quantity') }}</th>
                                    <th>{{ __('main.difference') }}</th>
                                    <th>{{ __('main.cost') }}</th>
                                    <th>{{ __('main.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $row)
                                    <tr>
                                        <td>{{ $row->product_name }} <div class="small text-muted">{{ $row->product_code }}</div></td>
                                        <td>#{{ $row->inventory_id }} - {{ $row->inventory_date ? \Carbon\Carbon::parse($row->inventory_date)->format('Y-m-d') : '' }}</td>
                                        <td>{{ $row->warehouse_name ?? '-' }}</td>
                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                        <td>{{ number_format($row->quantity ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->new_quantity ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->difference ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->product_cost ?? 0, 2) }}</td>
                                        <td>{{ number_format($row->difference_value ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-light mt-3">
                        <strong>{{ __('main.inventory_variance_report') }}</strong>
                        <div class="d-flex flex-wrap gap-3">
                            <span>{{ __('main.shortage') }}: {{ number_format($totals['shortage'] ?? 0, 2) }}</span>
                            <span>{{ __('main.excess') }}: {{ number_format($totals['excess'] ?? 0, 2) }}</span>
                        </div>
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

        document.title = "{{ __('main.inventory_variance_report') }}";
    });
</script>
@endsection
