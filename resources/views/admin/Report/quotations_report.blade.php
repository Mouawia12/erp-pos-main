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
                            {{ __('main.quotations_report') }}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.quotations') }}" id="quotations-report-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('الفرع') }}</label>
                                    @if(empty(Auth::user()->branch_id))
                                        <select name="branch_id" id="branch_id" class="js-example-basic-single w-100">
                                            <option value="0">{{ __('main.all') }}</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch->id}}" @if(request('branch_id')==$branch->id) selected @endif>
                                                    {{$branch->branch_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input class="form-control" type="text" readonly value="{{Auth::user()->branch->branch_name}}"/>
                                        <input class="form-control" type="hidden" id="branch_id" name="branch_id"
                                               value="{{Auth::user()->branch_id}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.warehouse') }}</label>
                                    <select class="js-example-basic-single w-100" name="warehouse_id" id="warehouse_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" @if(request('warehouse_id')==$warehouse->id) selected @endif>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.customer') }}</label>
                                    <select class="js-example-basic-single w-100" name="customer_id" id="customer_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}" @if(request('customer_id')==$customer->id) selected @endif>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.representatives') }}</label>
                                    <select class="js-example-basic-single w-100" name="representative_id" id="representative_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach ($representatives as $rep)
                                            <option value="{{$rep->id}}" @if(request('representative_id')==$rep->id) selected @endif>
                                                {{ $rep->user_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.cost_center') }}</label>
                                    <select class="js-example-basic-single w-100" name="cost_center_id" id="cost_center_id">
                                        <option value="0">{{ __('main.all') }}</option>
                                        @foreach($costCenters as $center)
                                            <option value="{{$center->id}}" @if(request('cost_center_id')==$center->id) selected @endif>
                                                {{$center->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.status') }}</label>
                                    <select class="form-control" name="status">
                                        <option value="">{{ __('main.all') }}</option>
                                        <option value="draft" @if(request('status')==='draft') selected @endif>draft</option>
                                        <option value="converted" @if(request('status')==='converted') selected @endif>converted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('main.quotation_no') }}</label>
                                    <input type="text" class="form-control" name="quotation_no" value="{{ request('quotation_no') }}">
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
                                <button type="button" id="quotations-pdf-btn" class="btn btn-primary" style="width: 150px; margin: 30px auto;">
                                    {{ __('main.report') }}
                                </button>
                                <span id="quotations-pdf-spinner" class="pdf-spinner d-none" aria-hidden="true"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="quotationsReportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('main.quotations_report') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="quotations-pdf-viewer"
                            src=""
                            style="width:100%; height:80vh; border:none;"></iframe>
                    <div id="quotations-pdf-error" class="alert alert-danger mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
    @endcan
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#branch_id').change(function (){
            var url = '{{route('get.warehouses.branches',":id")}}';
            url = url.replace(":id", this.value);
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    if (response) {
                        $('#warehouse_id')
                            .empty();
                        $('#warehouse_id').append('<option value="0">{{ __('main.all') }}</option>');
                        for (let i = 0; i < response.length; i++){
                            $('#warehouse_id').append('<option value="'+response[i].id+'">'+response[i].name + '</option>');
                        }
                    }
                }
            });
        });

        document.title = "{{ __('main.quotations_report') }}";
    });

    function showQuotationsPdfError(message) {
        const errorBox = document.getElementById('quotations-pdf-error');
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
    }

    function clearQuotationsPdfError() {
        const errorBox = document.getElementById('quotations-pdf-error');
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function setQuotationsPdfLoading(isLoading) {
        const spinner = document.getElementById('quotations-pdf-spinner');
        const button = document.getElementById('quotations-pdf-btn');
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

    document.getElementById('quotations-pdf-btn').addEventListener('click', async () => {
        const form = document.getElementById('quotations-report-form');
        if (!form) {
            showQuotationsPdfError('تعذر العثور على نموذج الفلاتر.');
            return;
        }

        clearQuotationsPdfError();
        const params = new URLSearchParams(new FormData(form));
        setQuotationsPdfLoading(true);
        try {
            const response = await fetch("{{ route('reports.quotations_pdf') }}?" + params.toString(), {
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
            if (window.quotationsReportBlobUrl) {
                URL.revokeObjectURL(window.quotationsReportBlobUrl);
            }
            const blobUrl = URL.createObjectURL(blob);
            window.quotationsReportBlobUrl = blobUrl;

            const viewer = document.getElementById('quotations-pdf-viewer');
            viewer.src = blobUrl;
            $('#quotationsReportModal').modal('show');
        } catch (error) {
            showQuotationsPdfError(error.message);
        } finally {
            setQuotationsPdfLoading(false);
        }
    });

    $('#quotationsReportModal').on('hidden.bs.modal', function () {
        if (window.quotationsReportBlobUrl) {
            URL.revokeObjectURL(window.quotationsReportBlobUrl);
            window.quotationsReportBlobUrl = null;
        }
        const viewer = document.getElementById('quotations-pdf-viewer');
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
