@extends('admin.layouts.master')
@section('content')
@can('التقارير المخزون')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="col-lg-12 margin-tb">
                    <h4 class="alert alert-primary text-center">
                        {{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.pos_end_of_day') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.from_date') ?? 'من تاريخ' }}</label>
                                <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.to_date') ?? 'إلى تاريخ' }}</label>
                                <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.branch') ?? 'الفرع' }}</label>
                                @if(empty(Auth::user()->branch_id))
                                    <select name="branch_id" class="form-control">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" @if(($filters['branch_id'] ?? 0) == $branch->id) selected @endif>{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly value="{{ Auth::user()->branch->branch_name }}" />
                                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.warehouse') }}</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @if(($filters['warehouse_id'] ?? 0) == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.cashier') ?? 'الكاشير' }}</label>
                                <select name="user_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($cashiers as $cashier)
                                        <option value="{{ $cashier->id }}" @if(($filters['user_id'] ?? 0) == $cashier->id) selected @endif>{{ $cashier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.shift') ?? 'الشفت' }}</label>
                                <select name="shift_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" @if(($filters['shift_id'] ?? 0) == $shift->id) selected @endif>
                                            #{{ $shift->id }} ({{ $shift->opened_at }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary" style="width:150px; margin: 20px auto;">
                                {{ __('main.report') ?? 'تقرير' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <h5 class="mb-0">{{ __('main.summary') ?? 'الملخص' }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('main.type') ?? 'النوع' }}</th>
                                <th>{{ __('main.total') }}</th>
                                <th>{{ __('main.tax') }}</th>
                                <th>{{ __('main.net') ?? 'الصافي' }}</th>
                                <th>{{ __('main.profit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ __('main.sales') ?? 'المبيعات' }}</td>
                                <td>{{ number_format($summary['sales']['total'] ?? 0, 2) }}</td>
                                <td>{{ number_format(($summary['sales']['tax'] ?? 0) + ($summary['sales']['tax_excise'] ?? 0), 2) }}</td>
                                <td>{{ number_format($summary['sales']['net'] ?? 0, 2) }}</td>
                                <td>{{ number_format($summary['sales']['profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('main.returns') ?? 'المرتجعات' }}</td>
                                <td>{{ number_format($summary['returns']['total'] ?? 0, 2) }}</td>
                                <td>{{ number_format(($summary['returns']['tax'] ?? 0) + ($summary['returns']['tax_excise'] ?? 0), 2) }}</td>
                                <td>{{ number_format($summary['returns']['net'] ?? 0, 2) }}</td>
                                <td>{{ number_format($summary['returns']['profit'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('main.quantity') }}</th>
                                <td colspan="4">{{ number_format($summary['quantity'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('main.cash') ?? 'نقدي' }}</th>
                                <th>{{ __('main.bank') ?? 'تحويل/شبكة' }}</th>
                                <th>{{ __('main.cards') ?? 'بطاقات' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($summary['payments']['cash'] ?? 0, 2) }}</td>
                                <td>{{ number_format($summary['payments']['bank'] ?? 0, 2) }}</td>
                                <td>{{ number_format($summary['payments']['card'] ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
