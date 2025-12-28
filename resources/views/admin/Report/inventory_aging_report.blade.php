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
                            {{ __('main.inventory_aging_report') }}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.inventory_aging') }}">
                        <div class="row">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                                    <label>{{ __('main.category') }}</label>
                                    <select class="js-example-basic-single w-100" name="category_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if($categorySelected==$category->id) selected @endif>
                                                {{$category->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.brand') }}</label>
                                    <select class="js-example-basic-single w-100" name="brand_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}" @if($brandSelected==$brand->id) selected @endif>
                                                {{$brand->name}}
                                            </option>
                                        @endforeach
                                    </select>
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
                                    <th>{{ __('main.item_name_code') }}</th>
                                    <th>{{ __('main.warehouse') }}</th>
                                    <th>{{ __('الفرع') }}</th>
                                    <th>{{ __('main.quantity') }}</th>
                                    <th>{{ __('main.last_purchase_date') }}</th>
                                    <th>{{ __('main.days_since_last_purchase') }}</th>
                                    <th>{{ __('main.aging_bucket') }}</th>
                                    <th>{{ __('main.value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $row)
                                    <tr>
                                        <td>{{ $row->name }} <div class="small text-muted">{{ $row->code }}</div></td>
                                        <td>{{ $row->warehouse_name ?? '-' }}</td>
                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                        <td>{{ number_format($row->quantity ?? 0, 2) }}</td>
                                        <td>{{ $row->last_purchase_date ? \Carbon\Carbon::parse($row->last_purchase_date)->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $row->days_since !== null ? $row->days_since : '-' }}</td>
                                        <td>{{ $row->aging_bucket }}</td>
                                        <td>{{ number_format($row->value ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-light">
                                <strong>{{ __('main.inventory_aging_report') }}</strong>
                                <div class="d-flex flex-wrap gap-3">
                                    <span>0-30: {{ number_format($agingTotals['current'] ?? 0, 2) }}</span>
                                    <span>31-60: {{ number_format($agingTotals['30'] ?? 0, 2) }}</span>
                                    <span>61-90: {{ number_format($agingTotals['60'] ?? 0, 2) }}</span>
                                    <span>91-120: {{ number_format($agingTotals['90'] ?? 0, 2) }}</span>
                                    <span>120+: {{ number_format($agingTotals['over'] ?? 0, 2) }}</span>
                                </div>
                            </div>
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

        document.title = "{{ __('main.inventory_aging_report') }}";
    });
</script>
@endsection
