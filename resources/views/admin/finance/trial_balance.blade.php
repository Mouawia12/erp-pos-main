@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('main.trial_balance') ?? 'ميزان المراجعة' }}</h4>
            <form class="form-inline" method="GET" action="{{ route('reports.trial_balance') }}">
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
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.code') }}</th>
                    <th>{{ __('main.name') }}</th>
                    <th>{{ __('main.debit') ?? 'مدين' }}</th>
                    <th>{{ __('main.credit') ?? 'دائن' }}</th>
                </tr>
                </thead>
                <tbody>
                @php $sumDebit=0; $sumCredit=0; @endphp
                @foreach($accounts as $acc)
                    @php $sumDebit += $acc->debit; $sumCredit += $acc->credit; @endphp
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $acc->code }}</td>
                        <td class="text-left">{{ $acc->name }}</td>
                        <td>{{ number_format($acc->debit,2) }}</td>
                        <td>{{ number_format($acc->credit,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3">{{ __('main.total') }}</th>
                    <th>{{ number_format($sumDebit,2) }}</th>
                    <th>{{ number_format($sumCredit,2) }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
