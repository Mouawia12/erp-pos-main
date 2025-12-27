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
                            <input type="submit" class="btn btn-primary" id="excute" tabindex="-1"
                                   style="width: 150px; margin: 30px auto;" value="{{__('main.report')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="show_modal"></div>
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

        $('#excute').click(function (){
            var fromDate = $('#is_from_date').is(":checked")
                ? document.getElementById('from_date').value.toString()
                : '0';
            var toDate = $('#is_to_date').is(":checked")
                ? document.getElementById('to_date').value.toString()
                : '0';

            showReport({
                status: document.getElementById('status').value,
                from_date: fromDate,
                to_date: toDate,
                invoice_no: document.getElementById('invoice_no').value,
                amount_min: document.getElementById('amount_min').value,
                amount_max: document.getElementById('amount_max').value
            });
        });

        $(document).on('click', '.cancel-modal', function () {
            $('#company_status_modal').modal("hide");
        });

        document.title = "{{ __('main.client_status_report') ?? 'تقارير العملاء حسب الحالة' }}";
    });

    function showReport(params) {
        $.get('{{ route('reports.clients.status.search') }}', params, function (html) {
            $('.show_modal').html(html);
            $('#company_status_modal').modal('show');
        });
    }
</script>
@endsection
