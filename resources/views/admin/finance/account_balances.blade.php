@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h4>{{ __('main.account_balance_report') ?? 'تقرير أرصدة الحسابات' }}</h4>
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
                    <th>{{ __('main.debit') }}</th>
                    <th>{{ __('main.credit') }}</th>
                    <th>{{ __('main.balance') ?? 'الرصيد' }}</th>
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
                        <td>{{ number_format($acc->balance,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3">{{ __('main.total') }}</th>
                    <th>{{ number_format($sumDebit,2) }}</th>
                    <th>{{ number_format($sumCredit,2) }}</th>
                    <th>{{ number_format($sumDebit-$sumCredit,2) }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
