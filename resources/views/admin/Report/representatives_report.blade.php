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
                        <h4 class="alert alert-primary text-center">{{ __('main.representatives_report') }}</h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.representatives') }}">
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
                                        <input class="form-control" type="hidden" name="branch_id" value="{{Auth::user()->branch_id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.representative') }}</label>
                                    <select name="representative_id" class="js-example-basic-single w-100">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($representatives as $rep)
                                            <option value="{{$rep->id}}" @if($representativeSelected==$rep->id) selected @endif>
                                                {{$rep->user_name}}
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
                                    <th>{{ __('main.representative') }}</th>
                                    <th>{{ __('main.invoices_count') }}</th>
                                    <th>{{ __('main.sales_total') }}</th>
                                    <th>{{ __('main.sales_paid') }}</th>
                                    <th>{{ __('main.sales_remain') }}</th>
                                    <th>{{ __('main.purchases_total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['invoices'] }}</td>
                                        <td>{{ number_format($row['sales_net'], 2) }}</td>
                                        <td>{{ number_format($row['sales_paid'], 2) }}</td>
                                        <td>{{ number_format($row['sales_remain'], 2) }}</td>
                                        <td>{{ number_format($row['purchase_net'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">{{ __('main.no_data') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">{{ __('main.total') }}</th>
                                    <th>{{ $totals['invoices'] ?? 0 }}</th>
                                    <th>{{ number_format($totals['sales_net'] ?? 0, 2) }}</th>
                                    <th>{{ number_format($totals['sales_paid'] ?? 0, 2) }}</th>
                                    <th>{{ number_format($totals['sales_remain'] ?? 0, 2) }}</th>
                                    <th>{{ number_format($totals['purchase_net'] ?? 0, 2) }}</th>
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
        document.title = "{{ __('main.representatives_report') }}";
    });
</script>
@endsection
