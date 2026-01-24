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
                    <form method="GET" action="{{ route('reports.salon.services') }}" id="salon-services-form">
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
                            <button type="button" id="salon-services-pdf-btn" class="btn btn-primary">{{ __('main.search') }}</button>
                            <span id="salon-services-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </form>

                    <div class="modal fade" id="salonServicesReportModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('main.salon_services_report') ?? 'تقرير خدمات المشغل' }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <iframe id="salon-services-pdf-viewer"
                                            src=""
                                            style="width:100%; height:80vh; border:none;"></iframe>
                                    <div id="salon-services-pdf-error" class="alert alert-danger mt-3 d-none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@section('js')
<script>
    document.getElementById('salon-services-pdf-btn').addEventListener('click', async () => {
        const form = document.getElementById('salon-services-form');
        if (!form) {
            showSalonServicesPdfError('تعذر العثور على نموذج الفلاتر.');
            return;
        }

        clearSalonServicesPdfError();
        const params = new URLSearchParams(new FormData(form));
        setSalonServicesPdfLoading(true);
        try {
            const response = await fetch("{{ route('reports.salon_services_pdf') }}?" + params.toString(), {
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
            if (window.salonServicesReportBlobUrl) {
                URL.revokeObjectURL(window.salonServicesReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.salonServicesReportBlobUrl = blobUrl;

            const viewer = document.getElementById('salon-services-pdf-viewer');
            viewer.src = blobUrl;
            $('#salonServicesReportModal').modal('show');
        } catch (error) {
            showSalonServicesPdfError(error.message);
        } finally {
            setSalonServicesPdfLoading(false);
        }
    });

    function showSalonServicesPdfError(message) {
        const errorBox = document.getElementById('salon-services-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearSalonServicesPdfError() {
        const errorBox = document.getElementById('salon-services-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setSalonServicesPdfLoading(isLoading) {
        const spinner = document.getElementById('salon-services-pdf-spinner');
        const button = document.getElementById('salon-services-pdf-btn');
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

    $('#salonServicesReportModal').on('hidden.bs.modal', function () {
        if (window.salonServicesReportBlobUrl) {
            URL.revokeObjectURL(window.salonServicesReportBlobUrl);
            window.salonServicesReportBlobUrl = null;
        }
        const viewer = document.getElementById('salon-services-pdf-viewer');
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
