@extends('admin.layouts.master')
@section('content')
@can('عرض تقارير')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="alert alert-primary text-center">{{ __('main.salon_services_report') ?? 'تقرير خدمات المشغل' }}</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.salon.services') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ __('main.from_date') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('main.to_date') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('main.salon_department') ?? 'قسم المشغل' }}</label>
                                <select name="department_id" class="form-control">
                                    <option value="">{{ __('main.all') }}</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @if($departmentSelected==$department->id) selected @endif>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">{{ __('main.search') }}</button>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.name') }}</th>
                                    <th>{{ __('main.salon_department') ?? 'قسم المشغل' }}</th>
                                    <th>{{ __('main.quantity') }}</th>
                                    <th>{{ __('main.total') }}</th>
                                    <th>{{ __('main.tax_total') }}</th>
                                    <th>{{ __('main.net') ?? 'الصافي' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $idx => $row)
                                    @php $net = ($row->total ?? 0) + ($row->tax ?? 0) + ($row->tax_excise ?? 0); @endphp
                                    <tr>
                                        <td>{{ $idx+1 }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->department_name }}</td>
                                        <td>{{ $row->quantity }}</td>
                                        <td>{{ number_format($row->total, 2) }}</td>
                                        <td>{{ number_format(($row->tax ?? 0) + ($row->tax_excise ?? 0), 2) }}</td>
                                        <td>{{ number_format($net, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7">{{ __('main.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
