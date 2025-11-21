
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
                                       class="form-control" />
                            </div>
                        </div>
                        <div class="col-6" >
                            <div class="form-group">
                                <label>{{__('main.visa')}} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="visa" name="visa" min="0" step="any"
                                       class="form-control"  placeholder="0" readonly />
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
    $(document).ready(function() {
        document.getElementById('date').valueAsDate = new Date(); 
    })
</script>
