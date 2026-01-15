@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.quotations') }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">{{ __('main.add_new') }}</a>
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
                    <th>{{ __('main.date') }}</th>
                    <th>{{ __('main.clients') }}</th>
                    <th>{{ __('main.total') }}</th>
                    <th>{{ __('main.tax') }}</th>
                    <th>{{ __('main.net') ?? 'الصافي' }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($quotations as $quotation)
                    <tr>
                        <td>{{ $quotation->quotation_no }}</td>
                        <td>{{ optional($quotation->date)->format('Y-m-d') }}</td>
                        <td>{{ optional($quotation->customer)->name ?? '-' }}</td>
                        <td>{{ number_format($quotation->total,2) }}</td>
                        <td>{{ number_format($quotation->tax,2) }}</td>
                        <td>{{ number_format($quotation->net,2) }}</td>
                        <td><span class="badge badge-{{ $quotation->status === 'converted' ? 'success' : 'secondary' }}">{{ $quotation->status }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('quotations.show',$quotation) }}" class="btn btn-sm btn-info">{{ __('main.preview') }}</a>
                            <a href="{{ route('quotations.edit',$quotation) }}" class="btn btn-sm btn-warning">{{ __('main.edit') }}</a>
                            <form action="{{ route('quotations.convert',$quotation) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" @if($quotation->status==='converted') disabled @endif>{{ __('main.save') ?? 'تحويل لفاتورة' }}</button>
                            </form>
                            <form action="{{ route('quotations.destroy',$quotation) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف العرض؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">{{ __('main.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
