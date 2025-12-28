@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('main.trading_account') ?? 'حساب المتاجرة' }}</h4>
            <form class="form-inline" method="GET" action="{{ route('reports.trading_account') }}">
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.start_date') ?? 'من' }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.end_date') ?? 'إلى' }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>
                <button class="btn btn-primary">{{ __('main.search') ?? 'عرض' }}</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <tr>
                    <th>{{ __('main.revenue') ?? 'الإيرادات' }}</th>
                    <td>{{ number_format($revenue,2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('main.cost') ?? 'تكلفة المبيعات' }}</th>
                    <td>{{ number_format($cost,2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('main.gross_profit') ?? 'مجمل الربح' }}</th>
                    <td>{{ number_format($grossProfit,2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
