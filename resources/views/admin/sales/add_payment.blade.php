@php
    $remain = $remain ?? 0;
@endphp

<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true" style="width: 100%;">
    <div class="modal-dialog modal-sm" role="document" style="min-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modal-close-btn" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label>{{ __('main.add_payment') }}</label>
            </div>
            <div class="modal-body" id="smallBody">
                <input type="hidden" id="pos" name="pos" value="0"/>
                <div class="row">
                    <div class="col-12" style="display: block; margin: auto;">
                        <div class="form-group text-center">
                            <label>{{ __('main.net_after_discount') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                            <input required type="text" id="money" name="money" class="form-control text-center" value="{{ $remain }}" readonly/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ __('main.cash') }} ({{ __('main.cash_box') ?? 'الصندوق' }}) <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                            <input required type="number" id="cash" name="cash" min="0" step="any" class="form-control payment-input" value="0"/>
                            <small class="text-muted d-block mt-1">{{ __('main.cash_box_hint') ?? 'اكتب 0 لتحويل المبلغ بالكامل إلى البنك.' }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>{{ __('main.bank_amount') ?? 'المبلغ للبنك' }}</label>
                            <input type="number" id="bank_amount" name="bank_amount" class="form-control" value="0" readonly>
                        </div>
                    </div>
                </div>
                <div id="paymentError" class="alert alert-danger mt-3 d-none"></div>

                <div class="row">
                    <div class="col-12" style="display: block; margin: 20px auto; text-align: center;">
                        <button type="button" class="btn btn-labeled btn-primary" id="payment_btn">
                            {{ __('main.save_btn') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
