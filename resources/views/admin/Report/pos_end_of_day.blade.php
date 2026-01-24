@extends('admin.layouts.master')
@section('content')
@can('التقارير المخزون')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="col-lg-12 margin-tb">
                    <h4 class="alert alert-primary text-center">
                        {{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}
                    </h4>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.pos_end_of_day') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.from_date') ?? 'من تاريخ' }}</label>
                                <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.to_date') ?? 'إلى تاريخ' }}</label>
                                <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.branch') ?? 'الفرع' }}</label>
                                @if(empty(Auth::user()->branch_id))
                                    <select name="branch_id" class="form-control">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" @if(($filters['branch_id'] ?? 0) == $branch->id) selected @endif>{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly value="{{ Auth::user()->branch->branch_name }}" />
                                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.warehouse') }}</label>
                                <select name="warehouse_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @if(($filters['warehouse_id'] ?? 0) == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.cashier') ?? 'الكاشير' }}</label>
                                <select name="user_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($cashiers as $cashier)
                                        <option value="{{ $cashier->id }}" @if(($filters['user_id'] ?? 0) == $cashier->id) selected @endif>{{ $cashier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.shift') ?? 'الشفت' }}</label>
                                <select name="shift_id" class="form-control">
                                    <option value="0">{{ __('main.all') }}</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" @if(($filters['shift_id'] ?? 0) == $shift->id) selected @endif>
                                            #{{ $shift->id }} ({{ $shift->opened_at }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" id="pos-end-of-day-pdf-btn" class="btn btn-primary" style="width:150px; margin: 20px auto;">
                                {{ __('main.report') ?? 'تقرير' }}
                            </button>
                            <span id="pos-end-of-day-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="posEndOfDayReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pos-end-of-day-pdf-viewer"
                        src=""
                        style="width:100%; height:80vh; border:none;"></iframe>
                <div id="pos-end-of-day-pdf-error" class="alert alert-danger mt-3 d-none"></div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@section('js')
<script>
    $(document).ready(function() {
        document.title = "{{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}";
    });

    function showPosEndOfDayPdfError(message) {
        const errorBox = document.getElementById('pos-end-of-day-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearPosEndOfDayPdfError() {
        const errorBox = document.getElementById('pos-end-of-day-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setPosEndOfDayPdfLoading(isLoading) {
        const spinner = document.getElementById('pos-end-of-day-pdf-spinner');
        const button = document.getElementById('pos-end-of-day-pdf-btn');
        if (!spinner || !button) {
            return;
        }
        if (isLoading) {
            spinner.classList.remove('d-none');
            button.setAttribute('disabled', 'disabled');
        } else {
            spinner.classList.add('d-none');
            button.removeAttribute('disabled');
        }
    }

    document.getElementById('pos-end-of-day-pdf-btn').addEventListener('click', async () => {
        const form = document.querySelector('form[action="{{ route('reports.pos_end_of_day') }}"]');
        if (!form) {
            showPosEndOfDayPdfError('تعذر العثور على نموذج الفلاتر.');
            return;
        }

        clearPosEndOfDayPdfError();
        const params = new URLSearchParams(new FormData(form));
        setPosEndOfDayPdfLoading(true);
        try {
            const response = await fetch("{{ route('reports.pos_end_of_day_pdf') }}?" + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const payload = await response.json();
                throw new Error(payload.message || 'تعذر إنشاء التقرير.');
            }
            if (!response.ok) {
                throw new Error('تعذر إنشاء التقرير.');
            }

            const blob = await response.blob();
            if (window.posEndOfDayBlobUrl) {
                URL.revokeObjectURL(window.posEndOfDayBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.posEndOfDayBlobUrl = blobUrl;

            const viewer = document.getElementById('pos-end-of-day-pdf-viewer');
            viewer.src = blobUrl;
            $('#posEndOfDayReportModal').modal('show');
        } catch (error) {
            showPosEndOfDayPdfError(error.message);
        } finally {
            setPosEndOfDayPdfLoading(false);
        }
    });

    $('#posEndOfDayReportModal').on('hidden.bs.modal', function () {
        if (window.posEndOfDayBlobUrl) {
            URL.revokeObjectURL(window.posEndOfDayBlobUrl);
            window.posEndOfDayBlobUrl = null;
        }
        const viewer = document.getElementById('pos-end-of-day-pdf-viewer');
        if (viewer) {
            viewer.src = '';
        }
    });
</script>
<style>
    .pdf-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-inline-start: 8px;
        border: 2px solid #cfd4da;
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: pdf-spin 0.7s linear infinite;
        vertical-align: middle;
    }
    @keyframes pdf-spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection
