@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.inventory') ?? 'الجرد' }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('stock_counts.create') }}" class="btn btn-primary">{{ __('main.add_new') }}</a>
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
                    <th>{{ __('main.reference') ?? 'المرجع' }}</th>
                    <th>{{ __('main.warehouses') }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($counts as $count)
                    <tr>
                        <td>{{ $count->id }}</td>
                        <td>{{ $count->reference }}</td>
                        <td>{{ optional($count->warehouse)->name ?? '' }}</td>
                        <td><span class="badge badge-{{ $count->status === 'approved' ? 'success' : 'warning' }}">{{ $count->status }}</span></td>
                        <td class="text-nowrap">
                            @if($count->status==='draft')
                                <form action="{{ route('stock_counts.approve',$count) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">{{ __('main.status1') ?? 'اعتماد' }}</button>
                                </form>
                            @endif
                            <form action="{{ route('stock_counts.destroy',$count) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف الجرد؟');">
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
