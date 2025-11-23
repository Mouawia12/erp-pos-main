@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.transfer_requests') ?? 'طلبات تحويل المخزون' }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('transfers.create') }}" class="btn btn-primary">{{ __('main.add_new') }}</a>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.from') ?? 'من مستودع' }}</th>
                    <th>{{ __('main.to') ?? 'إلى مستودع' }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->id }}</td>
                        <td>{{ optional($transfer->fromWarehouse)->name }}</td>
                        <td>{{ optional($transfer->toWarehouse)->name }}</td>
                        <td><span class="badge badge-{{ $transfer->status === 'approved' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'secondary') }}">{{ $transfer->status }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('transfers.show',$transfer) }}" class="btn btn-sm btn-info">{{ __('main.preview') }}</a>
                            @if($transfer->status==='pending')
                                <form action="{{ route('transfers.approve',$transfer) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">{{ __('main.status1') ?? 'موافقة' }}</button>
                                </form>
                                <form action="{{ route('transfers.reject',$transfer) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="reject_reason" value="{{ __('main.rejected') ?? 'مرفوض' }}">
                                    <button class="btn btn-sm btn-danger">{{ __('main.reject') ?? 'رفض' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
