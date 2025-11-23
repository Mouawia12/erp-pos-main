@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h4>{{ __('main.balance_sheet') ?? 'الميزانية' }}</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <tr>
                    <th>{{ __('main.assets') ?? 'الأصول' }}</th>
                    <td>{{ number_format($assets,2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('main.liabilities') ?? 'الالتزامات' }}</th>
                    <td>{{ number_format($liabilities,2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('main.equity') ?? 'حقوق الملكية' }}</th>
                    <td>{{ number_format($equity,2) }}</td>
                </tr>
                <tr>
                    <th>{{ __('main.balance') ?? 'الرصيد' }}</th>
                    <td>{{ number_format($assets - ($liabilities + $equity),2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
