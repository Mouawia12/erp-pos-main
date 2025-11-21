
<div class="modal fade" id="paymentsPosModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
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
                <form id="sales_payments_pos" method="POST" action="{{ route('store.sales.pos.payments',$id) }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" id="pos" name="pos" value="1"/> 
                                        
                    <div class="row">
                        <div class="col-6 "  style="display: block; margin: auto;">
                            <div class="form-group">
                                <label>{{ __('main.net_after_discount') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="money" name="money"
                                       class="form-control text-center" value="{{$remain}}" readonly/> 
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-6">
                            <div class="form-group"> 
                                <label>{{__('main.cash')}} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number" id="cash" name="cash" min="0" step="any"
                                       class="form-control" value="0" />
                            </div>
                        </div>
                        <div class="col-6" >
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-0">{{__('main.visa')}}</label>
                                <button type="button" class="btn btn-sm btn-secondary" id="addCardRowPos">
                                    + {{__('main.add_payment')}}
                                </button>
                            </div>
                            <div id="cardRowsPos">
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
                            <button type="button" class="btn btn-labeled btn-primary" id="payment_btn" >
                                {{__('main.save_btn')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function sumCards(){
            var total = 0;
            document.querySelectorAll('#cardRowsPos .card_amount').forEach(function(el){
                var v = parseFloat(el.value);
                if(!isNaN(v)){
                    total += v;
                }
            });
            return total;
        }

        var addBtn = document.getElementById('addCardRowPos');
        if(addBtn){
            addBtn.addEventListener('click', function (){
                var wrapper = document.getElementById('cardRowsPos');
                var row = document.createElement('div');
                row.className = 'form-row card-row mb-1';
                row.innerHTML = '<div class="col-7"><input type="text" class="form-control" name="card_bank[]" placeholder="{{__('main.method.payment')}}"></div><div class="col-5"><input type="number" class="form-control card_amount" name="card_amount[]" min="0" step="any" value="0"></div>';
                wrapper.appendChild(row);
            });
        }

        var paymentBtn = document.getElementById('payment_btn');
        if(paymentBtn){
            paymentBtn.addEventListener('click', function (){
                var money = parseFloat(document.getElementById('money').value) || 0;
                var cash = parseFloat(document.getElementById('cash').value) || 0;
                var cards = sumCards();
                if(Number(money.toFixed(2)) === Number((cash + cards).toFixed(2))){
                    var form = document.getElementById('salesform') || document.getElementById('form');
                    if(form){
                        form.submit();
                    }
                }else{
                    alert('لابد ان يكون مجموع المبالغ مساويا لاجمالى الفاتورة');
                }
            });
        }
    });
</script>
