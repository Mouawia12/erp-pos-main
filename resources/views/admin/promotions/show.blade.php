@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ $promotion->name }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('main.date') }}:</strong> {{ optional($promotion->start_date)->format('Y-m-d') }} - {{ optional($promotion->end_date)->format('Y-m-d') }}</p>
            <p><strong>{{ __('main.status') }}:</strong> {{ $promotion->status }}</p>
            <p><strong>{{ __('main.representatives') }}:</strong> {{ $promotion->representative?->user_name ?? __('main.all') }}</p>
            <p><strong>{{ __('main.note') ?? 'ملاحظات' }}:</strong> {{ $promotion->note }}</p>

            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.item') }}</th>
                    <th>{{ __('main.quantity') }}</th>
                    <th>{{ __('main.discount') }}</th>
                    <th>{{ __('main.barcode') ?? 'باركود خاص' }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($promotion->items as $item)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>
                            {{ optional($item->product)->name ?? '' }}
                            @if($item->variant_color || $item->variant_size)
                                <div class="text-muted" style="font-size: 11px;">{{ $item->variant_color }} {{ $item->variant_size }}</div>
                            @endif
                        </td>
                        <td>{{ $item->min_qty }}</td>
                        <td>{{ $item->discount_type === 'percent' ? $item->discount_value.' %' : $item->discount_value }}</td>
                        <td>{{ $item->special_barcode ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
