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
                        <h4 class="alert alert-primary text-center">
                            {{ __('main.client_status_report') ?? 'تقارير العملاء حسب الحالة' }}
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.status') ?? 'الحالة' }}</label>
                                <select class="form-control" id="status">
                                    <option value="active">{{ __('main.status_active') ?? 'نشط' }}</option>
                                    <option value="inactive">{{ __('main.status_inactive') ?? 'راكد' }}</option>
                                    <option value="stopped">{{ __('main.status_stopped') ?? 'موقوف' }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.invoice_no') ?? 'رقم الفاتورة' }}</label>
                                <input type="text" id="invoice_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.amount_min') ?? 'أقل قيمة' }}</label>
                                <input type="number" step="0.01" id="amount_min" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.amount_max') ?? 'أعلى قيمة' }}</label>
                                <input type="number" step="0.01" id="amount_max" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.from_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span></label>
                                <input type="checkbox" name="is_from_date" id="is_from_date"/>
                                <input type="date" id="from_date" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.to_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span></label>
                                <input type="checkbox" name="is_to_date" id="is_to_date"/>
                                <input type="date" id="to_date" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px; margin: 30px auto;">{{__('main.report')}}</button>
                            <span id="client-status-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clientStatusReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('main.client_status_report') ?? 'تقارير العملاء حسب الحالة' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="client-status-pdf-viewer"
                            src=""
                            style="width:100%; height:80vh; border:none;"></iframe>
                    <div id="client-status-pdf-error" class="alert alert-danger mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#is_from_date').prop('checked' , false);
        $('#from_date').attr('disabled' , true);
        $('#is_to_date').prop('checked' , false);
        $('#to_date').attr('disabled' , true);

        $('#is_from_date').change(function (){
            $('#from_date').attr('disabled' , !this.checked);
        });

        $('#is_to_date').change(function (){
            $('#to_date').attr('disabled' , !this.checked);
        });

        document.getElementById('from_date').valueAsDate = new Date();
        document.getElementById('to_date').valueAsDate = new Date();

        $('#excute').click(async function (){
            var fromDate = $('#is_from_date').is(":checked")
                ? document.getElementById('from_date').value.toString()
                : '0';
            var toDate = $('#is_to_date').is(":checked")
                ? document.getElementById('to_date').value.toString()
                : '0';

            await fetchClientStatusPdf({
                status: document.getElementById('status').value,
                from_date: fromDate,
                to_date: toDate,
                invoice_no: document.getElementById('invoice_no').value,
                amount_min: document.getElementById('amount_min').value,
                amount_max: document.getElementById('amount_max').value
            });
        });

        document.title = "{{ __('main.client_status_report') ?? 'تقارير العملاء حسب الحالة' }}";
    });

    function showClientStatusPdfError(message) {
        const errorBox = document.getElementById('client-status-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearClientStatusPdfError() {
        const errorBox = document.getElementById('client-status-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setClientStatusPdfLoading(isLoading) {
        const spinner = document.getElementById('client-status-pdf-spinner');
        const button = document.getElementById('excute');
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

    async function fetchClientStatusPdf(params) {
        clearClientStatusPdfError();
        setClientStatusPdfLoading(true);
        try {
            const query = new URLSearchParams(params);
            const response = await fetch("{{ route('reports.clients.status_pdf') }}?" + query.toString(), {
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
            if (window.clientStatusReportBlobUrl) {
                URL.revokeObjectURL(window.clientStatusReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.clientStatusReportBlobUrl = blobUrl;

            const viewer = document.getElementById('client-status-pdf-viewer');
            viewer.src = blobUrl;
            $('#clientStatusReportModal').modal('show');
        } catch (error) {
            showClientStatusPdfError(error.message);
        } finally {
            setClientStatusPdfLoading(false);
        }
    }

    $('#clientStatusReportModal').on('hidden.bs.modal', function () {
        if (window.clientStatusReportBlobUrl) {
            URL.revokeObjectURL(window.clientStatusReportBlobUrl);
            window.clientStatusReportBlobUrl = null;
        }
        const viewer = document.getElementById('client-status-pdf-viewer');
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
