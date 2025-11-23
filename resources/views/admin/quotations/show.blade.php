@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ $quotation->quotation_no }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('main.clients') }}:</strong> {{ optional($quotation->customer)->name ?? '-' }}</p>
            <p><strong>{{ __('main.date') }}:</strong> {{ optional($quotation->date)->format('Y-m-d') }}</p>
            <p><strong>{{ __('main.status') }}:</strong> {{ $quotation->status }}</p>

            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.item') }}</th>
                    <th>{{ __('main.quantity') }}</th>
                    <th>{{ __('main.price') }}</th>
                    <th>{{ __('main.tax') }}</th>
                    <th>{{ __('main.total') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($quotation->details as $detail)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>
                            {{ optional($detail->product)->name ?? '' }}
                            @if($detail->variant_color || $detail->variant_size)
                                <div class="text-muted" style="font-size: 11px;">
                                    {{ $detail->variant_color }} {{ $detail->variant_size }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price_unit,2) }}</td>
                        <td>{{ number_format($detail->tax,2) }}</td>
                        <td>{{ number_format($detail->total + $detail->tax,2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <p><strong>{{ __('main.total_without_tax') }}:</strong> {{ number_format($quotation->total,2) }}</p>
                <p><strong>{{ __('main.tax') }}:</strong> {{ number_format($quotation->tax,2) }}</p>
                <p><strong>{{ __('main.net_after_discount') ?? 'الصافي' }}:</strong> {{ number_format($quotation->net,2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
