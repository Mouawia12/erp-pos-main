@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h4>{{ __('main.fiscal_years') ?? 'السنوات المالية' }}</h4>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">
            <strong>{{ __('main.add_new') }}</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fiscal_years.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('main.name') ?? 'الاسم' }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('main.start_date') ?? 'تاريخ البداية' }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('main.end_date') ?? 'تاريخ النهاية' }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-primary" type="submit">{{ __('main.save_btn') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.name') ?? 'الاسم' }}</th>
                    <th>{{ __('main.start_date') ?? 'تاريخ البداية' }}</th>
                    <th>{{ __('main.end_date') ?? 'تاريخ النهاية' }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($years as $year)
                    <tr>
                        <td>{{ $year->id }}</td>
                        <td>{{ $year->name ?? ('FY-' . $year->start_date) }}</td>
                        <td>{{ $year->start_date }}</td>
                        <td>{{ $year->end_date }}</td>
                        <td>
                            <span class="badge badge-{{ $year->is_closed ? 'danger' : 'success' }}">
                                {{ $year->is_closed ? __('main.fiscal_year_closed_label') : __('main.fiscal_year_open_label') }}
                            </span>
                        </td>
                        <td>
                            @if($year->is_closed)
                                <form method="POST" action="{{ route('fiscal_years.open', $year) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-warning" type="submit">{{ __('main.open') ?? 'فتح' }}</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('fiscal_years.close', $year) }}" class="d-inline" onsubmit="return confirm('{{ __('main.confirm_close_fiscal_year') ?? 'تأكيد إقفال السنة المالية؟' }}');">
                                    @csrf
                                    <button class="btn btn-sm btn-danger" type="submit">{{ __('main.close') ?? 'إقفال' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
