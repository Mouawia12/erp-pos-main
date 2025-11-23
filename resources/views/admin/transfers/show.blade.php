@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.transfer_requests') ?? 'طلب تحويل' }} #{{ $transfer->id }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('transfers.index') }}" class="btn btn-secondary">{{ __('main.back') ?? 'عودة' }}</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('main.from') ?? 'من' }}:</strong> {{ optional($transfer->fromWarehouse)->name }}</p>
            <p><strong>{{ __('main.to') ?? 'إلى' }}:</strong> {{ optional($transfer->toWarehouse)->name }}</p>
            <p><strong>{{ __('main.status') }}:</strong> {{ $transfer->status }}</p>
            @if($transfer->reject_reason)<p><strong>{{ __('main.reject') ?? 'رفض' }}:</strong> {{ $transfer->reject_reason }}</p>@endif
            @if($transfer->status === 'pending')
                <form action="{{ route('transfers.approve',$transfer) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm">{{ __('main.status1') ?? 'موافقة' }}</button>
                </form>
                <form action="{{ route('transfers.reject',$transfer) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="reject_reason" value="{{ __('main.reject') ?? 'رفض' }}">
                    <button class="btn btn-danger btn-sm">{{ __('main.reject') ?? 'رفض' }}</button>
                </form>
                <form action="{{ route('transfers.damaged',$transfer) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-warning btn-sm">{{ __('main.to') ?? 'تالف' }}</button>
                </form>
            @endif
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.item') }}</th>
                    <th>{{ __('main.quantity') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transfer->items as $item)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>
                            {{ optional($item->product)->name ?? '' }}
                            @if($item->variant_color || $item->variant_size)
                                <div class="text-muted" style="font-size: 11px;">{{ $item->variant_color }} {{ $item->variant_size }}</div>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
