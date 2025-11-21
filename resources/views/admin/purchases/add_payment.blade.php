
<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
style="width: 100%;">
    <div class="modal-dialog modal-sm" role="document" style="min-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label>{{__('main.add_payment')}}</label>
            </div>
            <div class="modal-body" id="smallBody">
                <form id="purchasePaymentForm" method="POST" action="{{ route('store_purchases_payments',$id) }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.bill_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="date"  id="date" name="date" readonly
                                       class="form-control" required
                                       placeholder="{{ __('main.bill_date') }}"  />

                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.net_after_discount') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number" step="0.01"  id="amount" name="amount"
                                       class="form-control text-center" value="{{$remain}}" readonly
                                       placeholder="{{ __('main.amount') }}"  />

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.cash') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number" step="0.01"  id="cash" name="cash"
                                       class="form-control" value="0" min="0"
                                       placeholder="{{ __('main.amount') }}"  />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-0">{{__('main.visa')}}</label>
                                <button type="button" class="btn btn-sm btn-secondary" id="addCardRowPurchase">
                                    + {{__('main.add_payment')}}
                                </button>
                            </div>
                            <div id="cardRowsPurchase">
                                <div class="form-row card-row mb-1">
                                    <div class="col-7">
                                        <input type="text" class="form-control" name="card_bank[]" placeholder="{{__('main.method.payment')}}">
                                    </div>
                                    <div class="col-5">
                                        <input type="number" class="form-control card_amount" name="card_amount[]" min="0" step="any" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="button" id="purchase_payment_btn" class="btn btn-labeled btn-primary"  >
                                {{__('main.save_btn')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        document.getElementById('date').valueAsDate = new Date();

        function sumCards(){
            var total = 0;
            $('.card_amount').each(function(){
                var v = parseFloat($(this).val());
                if(!isNaN(v)){
                    total += v;
                }
            });
            return total;
        }

        $('#addCardRowPurchase').on('click', function(){
            var row = `<div class="form-row card-row mb-1">
                            <div class="col-7">
                                <input type="text" class="form-control" name="card_bank[]" placeholder="{{__('main.method.payment')}}">
                            </div>
                            <div class="col-5">
                                <input type="number" class="form-control card_amount" name="card_amount[]" min="0" step="any" value="0">
                            </div>
                        </div>`;
            $('#cardRowsPurchase').append(row);
        });

        $('#purchase_payment_btn').on('click', function (){
            var total = parseFloat($('#amount').val()) || 0;
            var cash = parseFloat($('#cash').val()) || 0;
            var cards = sumCards();

            if(Number(total.toFixed(2)) === Number((cash + cards).toFixed(2))){
                $('#purchasePaymentForm').submit();
            }else{
                alert('لابد ان يكون مجموع المبالغ مساويا لاجمالى الفاتورة');
            }
        });
    })
</script>
