@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-6">
            <h4>{{ __('main.promotions') ?? 'العروض الترويجية' }}</h4>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('promotions.create') }}" class="btn btn-primary">{{ __('main.add_new') }}</a>
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
                    <th>{{ __('main.name') }}</th>
                    <th>{{ __('main.representatives') }}</th>
                    <th>{{ __('main.date') }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($promotions as $promotion)
                    <tr>
                        <td>{{ $promotion->id }}</td>
                        <td>{{ $promotion->name }}</td>
                        <td>{{ $promotion->representative?->user_name ?? __('main.all') }}</td>
                        <td>{{ optional($promotion->start_date)->format('Y-m-d') }} - {{ optional($promotion->end_date)->format('Y-m-d') }}</td>
                        <td><span class="badge badge-{{ $promotion->status === 'active' ? 'success' : 'secondary' }}">{{ $promotion->status }}</span></td>
                        <td class="text-nowrap">
                            <a href="{{ route('promotions.show',$promotion) }}" class="btn btn-sm btn-info">{{ __('main.preview') }}</a>
                            <a href="{{ route('promotions.edit',$promotion) }}" class="btn btn-sm btn-warning">{{ __('main.edit') }}</a>
                            <form action="{{ route('promotions.destroy',$promotion) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف العرض؟');">
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
