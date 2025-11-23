@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h4>{{ __('main.transfer_report') ?? 'تقرير التحويلات' }}</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <label>{{ __('main.status') }}</label>
                    <select name="status" class="form-control">
                        <option value="">{{ __('main.all') ?? 'الكل' }}</option>
                        <option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
                        <option value="approved" @if(request('status')=='approved') selected @endif>Approved</option>
                        <option value="rejected" @if(request('status')=='rejected') selected @endif>Rejected</option>
                        <option value="damaged" @if(request('status')=='damaged') selected @endif>Damaged</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>{{ __('main.from') ?? 'من مستودع' }}</label>
                    <select name="from_warehouse_id" class="form-control">
                        <option value="">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" @if(request('from_warehouse_id')==$w->id) selected @endif>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>{{ __('main.to') ?? 'إلى مستودع' }}</label>
                    <select name="to_warehouse_id" class="form-control">
                        <option value="">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" @if(request('to_warehouse_id')==$w->id) selected @endif>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">{{ __('main.search') ?? 'بحث' }}</button>
                </div>
            </form>

            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.from') ?? 'من' }}</th>
                    <th>{{ __('main.to') ?? 'إلى' }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->id }}</td>
                        <td>{{ optional($transfer->fromWarehouse)->name }}</td>
                        <td>{{ optional($transfer->toWarehouse)->name }}</td>
                        <td>{{ $transfer->status }}</td>
                        <td>{{ optional($transfer->created_at)->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
