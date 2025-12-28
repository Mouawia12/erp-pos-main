@extends('admin.layouts.master')
@section('content')
@can('عرض مبيعات')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.pos_shift') ?? 'الشفت' }}</h5>
                </div>
                <div class="card-body">
                    @if($currentShift)
                        <div class="alert alert-info">
                            {{ __('main.shift_open') ?? 'الشفت مفتوح' }} #{{ $currentShift->id }}
                        </div>
                        @if($summary)
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered text-center mb-0">
                                    <tr>
                                        <th>{{ __('main.total') }}</th>
                                        <td>{{ number_format($summary['net'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('main.tax') }}</th>
                                        <td>{{ number_format(($summary['tax'] ?? 0) + ($summary['tax_excise'] ?? 0), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('main.profit') }}</th>
                                        <td>{{ number_format($summary['profit'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('main.quantity') }}</th>
                                        <td>{{ number_format($summary['quantity'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('main.cash') ?? 'نقدي' }}</th>
                                        <td>{{ number_format($summary['cash_total'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('main.network') ?? 'شبكة' }}</th>
                                        <td>{{ number_format(($summary['bank_total'] ?? 0) + ($summary['card_total'] ?? 0), 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('pos.shifts.close', $currentShift) }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('main.closing_cash') ?? 'نقدية الإغلاق' }}</label>
                                <input type="number" step="any" name="closing_cash" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('main.notes') }}</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">{{ __('main.close_shift') ?? 'إغلاق الشفت' }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('pos.shifts.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('main.opening_cash') ?? 'نقدية البداية' }}</label>
                                <input type="number" step="any" name="opening_cash" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <label>{{ __('main.warehouse') }}</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('main.notes') }}</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('main.open_shift') ?? 'فتح شفت' }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ __('main.shift_history') ?? 'سجل الشفتات' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.open_time') ?? 'وقت الفتح' }}</th>
                                    <th>{{ __('main.close_time') ?? 'وقت الإغلاق' }}</th>
                                    <th>{{ __('main.status') }}</th>
                                    <th>{{ __('main.opening_cash') ?? 'نقدية البداية' }}</th>
                                    <th>{{ __('main.closing_cash') ?? 'نقدية الإغلاق' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                    <tr>
                                        <td>{{ $shift->id }}</td>
                                        <td>{{ $shift->opened_at }}</td>
                                        <td>{{ $shift->closed_at ?? '--' }}</td>
                                        <td>{{ $shift->status === 'open' ? __('main.open') ?? 'مفتوح' : __('main.closed') ?? 'مغلق' }}</td>
                                        <td>{{ number_format($shift->opening_cash ?? 0, 2) }}</td>
                                        <td>{{ $shift->closing_cash !== null ? number_format($shift->closing_cash, 2) : '--' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6">{{ __('main.no_data') }}</td></tr>
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
