@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('main.sub_ledger_reconciliation') ?? 'مطابقة الأستاذ المساعد مع حساب التحكم' }}</h4>
            <form class="form-inline" method="GET" action="{{ route('reports.sub_ledger_reconciliation') }}">
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.start_date') ?? 'من' }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.end_date') ?? 'إلى' }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.branche') }}</label>
                    <select name="branch_id" class="form-control">
                        <option value="0">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($branches as $branch)
                            <option value="{{$branch->id}}" @if(($branchId ?? 0) == $branch->id) selected @endif>{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">{{ __('main.search') ?? 'عرض' }}</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.branche') ?? 'الفرع' }}</th>
                    <th>{{ __('main.account') ?? 'حساب التحكم' }}</th>
                    <th>{{ __('main.sub_ledger') ?? 'الأستاذ المساعد' }}</th>
                    <th>{{ __('main.debit') ?? 'مدين' }}</th>
                    <th>{{ __('main.credit') ?? 'دائن' }}</th>
                    <th>{{ __('main.balance') ?? 'الرصيد' }}</th>
                    <th>{{ __('main.control_account') ?? 'حساب التحكم' }}</th>
                    <th>{{ __('main.debit') ?? 'مدين' }}</th>
                    <th>{{ __('main.credit') ?? 'دائن' }}</th>
                    <th>{{ __('main.balance') ?? 'الرصيد' }}</th>
                    <th>{{ __('main.difference') ?? 'الفرق' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    @php
                        $diff = $row['difference'] ?? 0;
                        $rowClass = abs($diff) > 0.01 ? 'table-warning' : '';
                        $typeLabel = $row['control_type'] === 'suppliers' ? 'موردون' : 'عملاء';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $row['branch'] }}</td>
                        <td>{{ $row['account_code'] }} - {{ $row['account_name'] }}</td>
                        <td>{{ $typeLabel }}</td>
                        <td>{{ number_format($row['sub_debit'], 2) }}</td>
                        <td>{{ number_format($row['sub_credit'], 2) }}</td>
                        <td>{{ number_format($row['sub_balance'], 2) }}</td>
                        <td>{{ $row['account_code'] }}</td>
                        <td>{{ number_format($row['acc_debit'], 2) }}</td>
                        <td>{{ number_format($row['acc_credit'], 2) }}</td>
                        <td>{{ number_format($row['acc_balance'], 2) }}</td>
                        <td>{{ number_format($diff, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12">{{ __('main.no_data_found') ?? 'لا توجد بيانات' }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
