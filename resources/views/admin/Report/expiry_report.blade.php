@extends('admin.layouts.master')
@section('content')
@can('عرض تقارير')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="alert alert-primary text-center">{{ __('main.expiry_report') ?? 'تقرير الأصناف منتهية/قريبة الانتهاء' }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.expiry.search') }}">
                        @csrf
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
                                <label>{{ __('main.batch_no') ?? 'Batch' }}</label>
                                <input type="text" name="batch_no" class="form-control" value="{{ $batchSelected ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('main.branche') }}</label>
                                @if(empty(Auth::user()->branch_id))
                                    <select name="branch_id" class="form-control">
                                        <option value="">{{ __('main.all') }}</option>
                                        @foreach($branches as $b)
                                            <option value="{{$b->id}}" @if($branchSelected==$b->id) selected @endif>{{$b->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" value="{{Auth::user()->branch->branch_name}}" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>{{ __('main.warehouse') }}</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="">{{ __('main.all') }}</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{$w->id}}" @if($warehouseSelected==$w->id) selected @endif>{{$w->name}}</option>
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
                                    <th>{{ __('main.product_code') }}</th>
                                    <th>{{ __('main.name') }}</th>
                                    <th>{{ __('main.batch_no') ?? 'Batch' }}</th>
                                    <th>{{ __('main.expiry_date') ?? 'Expiry' }}</th>
                                    <th>{{ __('main.warehouse') }}</th>
                                    <th>{{ __('main.branche') }}</th>
                                    <th>{{ __('main.quantity') }}</th>
                                    <th>{{ __('main.days_to_expiry') ?? 'أيام للانتهاء' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $idx => $row)
                                    <tr @if($row->days_to_expiry <= 0) class="table-danger" @elseif($row->days_to_expiry <=7) class="table-warning" @endif>
                                        <td>{{ $idx+1 }}</td>
                                        <td>{{ $row->code }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->batch_no }}</td>
                                        <td>{{ $row->expiry_date }}</td>
                                        <td>{{ $row->warehouse_name }}</td>
                                        <td>{{ $row->branch_name }}</td>
                                        <td>{{ $row->quantity }}</td>
                                        <td>{{ $row->days_to_expiry }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9">{{ __('main.no_data') ?? 'لا بيانات' }}</td></tr>
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
