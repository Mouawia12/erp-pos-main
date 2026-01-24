@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    @can('التقارير المخزون')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4 class="alert alert-primary text-center">{{ __('main.vendors_movement_report') }}</h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.vendors_movement') }}" id="vendors-movement-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('الفرع') }}</label>
                                    @if(empty(Auth::user()->branch_id))
                                        <select name="branch_id" id="branch_id" class="js-example-basic-single w-100">
                                            <option value="0">{{ __('main.all') }}</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" @if($branchSelected==$branch->id) selected @endif>
                                                    {{$branch->branch_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input class="form-control" type="text" readonly value="{{Auth::user()->branch->branch_name}}"/>
                                        <input class="form-control" type="hidden" name="branch_id" value="{{Auth::user()->branch_id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.supplier') }}</label>
                                    <select name="company_id" class="js-example-basic-single w-100">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}" @if($companySelected==$company->id) selected @endif>
                                                {{$company->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.representative') }}</label>
                                    <select name="representative_id" class="js-example-basic-single w-100">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($representatives as $rep)
                                            <option value="{{$rep->id}}" @if($representativeSelected==$rep->id) selected @endif>
                                                {{$rep->user_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.from_date') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.to_date') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="button" id="vendors-movement-pdf-btn" class="btn btn-primary" style="width: 150px; margin: 30px auto;">
                                    {{ __('main.report') }}
                                </button>
                                <span id="vendors-movement-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vendorsMovementReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('main.vendors_movement_report') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="vendors-movement-pdf-viewer"
                            src=""
                            style="width:100%; height:80vh; border:none;"></iframe>
                    <div id="vendors-movement-pdf-error" class="alert alert-danger mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('js')
<script>
    $(document).ready(function() {
        document.title = "{{ __('main.vendors_movement_report') }}";
    });

    function showVendorsMovementPdfError(message) {
        const errorBox = document.getElementById('vendors-movement-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearVendorsMovementPdfError() {
        const errorBox = document.getElementById('vendors-movement-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setVendorsMovementPdfLoading(isLoading) {
        const spinner = document.getElementById('vendors-movement-pdf-spinner');
        const button = document.getElementById('vendors-movement-pdf-btn');
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

    document.getElementById('vendors-movement-pdf-btn').addEventListener('click', async () => {
        const form = document.getElementById('vendors-movement-form');
        if (!form) {
            showVendorsMovementPdfError('تعذر العثور على نموذج الفلاتر.');
            return;
        }

        clearVendorsMovementPdfError();
        const params = new URLSearchParams(new FormData(form));
        setVendorsMovementPdfLoading(true);
        try {
            const response = await fetch("{{ route('reports.vendors_movement_pdf') }}?" + params.toString(), {
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
            if (window.vendorsMovementReportBlobUrl) {
                URL.revokeObjectURL(window.vendorsMovementReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.vendorsMovementReportBlobUrl = blobUrl;

            const viewer = document.getElementById('vendors-movement-pdf-viewer');
            viewer.src = blobUrl;
            $('#vendorsMovementReportModal').modal('show');
        } catch (error) {
            showVendorsMovementPdfError(error.message);
        } finally {
            setVendorsMovementPdfLoading(false);
        }
    });

    $('#vendorsMovementReportModal').on('hidden.bs.modal', function () {
        if (window.vendorsMovementReportBlobUrl) {
            URL.revokeObjectURL(window.vendorsMovementReportBlobUrl);
            window.vendorsMovementReportBlobUrl = null;
        }
        const viewer = document.getElementById('vendors-movement-pdf-viewer');
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
