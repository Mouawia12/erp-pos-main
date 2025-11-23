@extends('admin.layouts.master')
@section('title') [ {{ __('main.alerts_center') }} ] @endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="alert alert-primary w-100 text-center mb-0">{{ __('main.alerts_center') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('alerts.index') }}" class="form-inline">
                            <div class="form-group mr-2 mb-2">
                                <label for="type" class="mr-2">{{ __('main.alert_type') }}</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">{{ __('main.all') }}</option>
                                    <option value="low_stock" @if($filterType==='low_stock') selected @endif>{{ __('main.alert_type_low_stock') }}</option>
                                    <option value="near_expiry" @if($filterType==='near_expiry') selected @endif>{{ __('main.alert_type_near_expiry') }}</option>
                                    <option value="representative_document" @if($filterType==='representative_document') selected @endif>{{ __('main.alert_type_rep_document') }}</option>
                                </select>
                            </div>
                            <div class="form-group mr-2 mb-2">
                                <label for="status" class="mr-2">{{ __('main.status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('main.all') }}</option>
                                    <option value="unread" @if($filterStatus==='unread') selected @endif>{{ __('main.alert_status_new') }}</option>
                                    <option value="open" @if($filterStatus==='open') selected @endif>{{ __('main.alert_status_open') }}</option>
                                    <option value="resolved" @if($filterStatus==='resolved') selected @endif>{{ __('main.alert_status_resolved') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">{{ __('main.search') }}</button>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <form method="POST" action="{{ route('alerts.refresh') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fa fa-rotate"></i> {{ __('main.alerts_refresh_now') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('main.type') }}</th>
                            <th>{{ __('main.message') }}</th>
                            <th>{{ __('main.branch') }}</th>
                            <th>{{ __('main.warehouse') }}</th>
                            <th>{{ __('main.date') }}</th>
                            <th>{{ __('main.status') }}</th>
                            <th>{{ __('main.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($alerts as $alert)
                            @php
                                $rowClass = $alert->severity === 'danger' ? 'table-danger' : ($alert->severity === 'warning' ? 'table-warning' : '');
                                $statusLabel = $alert->resolved_at ? __('main.alert_status_resolved')
                                    : ($alert->read_at ? __('main.alert_status_read') : __('main.alert_status_new'));
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $alert->id }}</td>
                                <td>
                                    <span class="badge badge-{{ $alert->severity === 'danger' ? 'danger' : 'warning' }}">
                                        {{ $alert->type === 'low_stock' ? __('main.alert_type_low_stock') : ($alert->type === 'near_expiry' ? __('main.alert_type_near_expiry') : __('main.alert_type_rep_document')) }}
                                    </span>
                                </td>
                                <td class="text-left">
                                    <strong>{{ $alert->title }}</strong><br>
                                    <small>{{ $alert->message }}</small>
                                    @if(!empty($alert->meta['product_code']))
                                        <div class="text-muted">{{ __('main.product_code') }}: {{ $alert->meta['product_code'] }}</div>
                                    @endif
                                    @if(!empty($alert->meta['document_number']))
                                        <div class="text-muted">{{ __('main.rep_document_number') }}: {{ $alert->meta['document_number'] }}</div>
                                    @endif
                                    @if(isset($alert->meta['days_to_expiry']))
                                        <div class="text-muted">{{ __('main.days_to_expiry') }}: {{ $alert->meta['days_to_expiry'] }}</div>
                                    @endif
                                </td>
                                <td>{{ $alert->meta['branch_name'] ?? '-' }}</td>
                                <td>{{ $alert->meta['warehouse_name'] ?? '-' }}</td>
                                <td>
                                    @if($alert->due_date)
                                        {{ $alert->due_date->format('Y-m-d') }}
                                    @else
                                        {{ $alert->created_at->format('Y-m-d') }}
                                    @endif
                                </td>
                                <td>{{ $statusLabel }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <form method="POST" action="{{ route('alerts.read',$alert) }}" class="mr-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" @if($alert->read_at) disabled @endif>
                                                {{ __('main.mark_read') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('alerts.resolve',$alert) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" @if($alert->resolved_at) disabled @endif>
                                                {{ __('main.mark_resolved') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('main.no_alerts_found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
