@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="alert alert-primary text-center">{{ __('main.vendor_aging') ?? 'أعمار ديون الموردين/العملاء' }}</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>{{ __('main.vendor') ?? 'مورد' }}</th>
                    <th>{{ __('main.balance') ?? 'الرصيد' }}</th>
                    <th>0-30</th>
                    <th>31-60</th>
                    <th>61-90</th>
                    <th>91-120</th>
                    <th>>120</th>
                </tr>
                </thead>
                <tbody>
                @foreach($report as $row)
                    <tr>
                        <td>{{ $row['vendor'] }}</td>
                        <td>{{ number_format($row['balance'],2) }}</td>
                        <td>{{ number_format($row['aging']['current'],2) }}</td>
                        <td>{{ number_format($row['aging']['30'],2) }}</td>
                        <td>{{ number_format($row['aging']['60'],2) }}</td>
                        <td>{{ number_format($row['aging']['90'],2) }}</td>
                        <td>{{ number_format($row['aging']['over'],2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
