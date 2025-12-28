@extends('admin.layouts.master')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('main.general_ledger') ?? 'الأستاذ العام' }}</h4>
            <form class="form-inline" method="GET" action="{{ route('reports.general_ledger') }}">
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.account') }}</label>
                    <select name="account_id" class="form-control">
                        <option value="">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" @if($accountId==$acc->id) selected @endif>{{ $acc->code }} - {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.start_date') ?? 'من' }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.end_date') ?? 'إلى' }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.branche') }}</label>
                    <select name="branch_id" class="form-control">
                        <option value="0">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($branches as $branch)
                            <option value="{{$branch->id}}" @if(($branchId ?? 0) == $branch->id) selected @endif>{{$branch->branch_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label class="mr-1">{{ __('main.cost_center') }}</label>
                    <select name="cost_center_id" class="form-control">
                        <option value="0">{{ __('main.all') ?? 'الكل' }}</option>
                        @foreach($costCenters as $center)
                            <option value="{{$center->id}}" @if(($costCenterId ?? 0) == $center->id) selected @endif>{{$center->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">{{ __('main.search') ?? 'عرض' }}</button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>{{ __('main.date') }}</th>
                    <th>{{ __('main.account') }}</th>
                    <th>{{ __('main.description') }}</th>
                    <th>{{ __('main.debit') }}</th>
                    <th>{{ __('main.credit') }}</th>
                </tr>
                </thead>
                <tbody>
                @php $sumDebit=0; $sumCredit=0; @endphp
                @forelse($movements as $mv)
                    @php $sumDebit += $mv->debit ?? 0; $sumCredit += $mv->credit ?? 0; @endphp
                    <tr>
                        <td>{{ optional($mv->date)->format('Y-m-d') }}</td>
                        <td>{{ optional($mv->account)->code ?? '' }} - {{ optional($mv->account)->name ?? '' }}</td>
                        <td>{{ $mv->description ?? '' }}</td>
                        <td>{{ number_format($mv->debit ?? 0,2) }}</td>
                        <td>{{ number_format($mv->credit ?? 0,2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">{{ __('main.no_results') ?? 'لا توجد بيانات' }}</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3">{{ __('main.total') }}</th>
                    <th>{{ number_format($sumDebit,2) }}</th>
                    <th>{{ number_format($sumCredit,2) }}</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
