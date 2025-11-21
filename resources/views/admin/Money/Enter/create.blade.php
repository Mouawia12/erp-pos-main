<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label>{{__('main.money_entry_create')}}</label>
            </div>
            <div class="modal-body" id="smallBody">
                <form   method="POST" action="{{ route('storeMoneyEnter') }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-3" >
                            <div class="form-group">
                                <label>{{ __('main.bill_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="doc_number" name="doc_number"
                                       class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly  />

                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="date"  id="date" name="date" readonly
                                       class="form-control" required
                                       placeholder="{{ __('main.date') }}"  />

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="d-block">
                                     الفرع <span style="color:red; font-size:20px; font-weight:bold;">*</span>
                                </label>
                                @if(empty(Auth::user()->branch_id))
                                    <select required  class="form-control select2" name="branch_id" id="branch_id">
                                        <option value="">حدد الاختيار</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly
                                           value="{{Auth::user()->branch->branch_name}}"/>
                                    <input required class="form-control" type="hidden" id="branch_id"
                                           name="branch_id"
                                           value="{{Auth::user()->branch_id}}"/>
                                @endif
                    
                            </div>
                        </div>
             
                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.client') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select name="client_id" class="form-control" id="client_id">.
                                    <option value="" selected>select..</option>
                                    @foreach($clients as $client)
                                    <option value="{{$client -> id}}" >{{$client -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.total_balance') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="balance" name="balance"
                                       class="form-control"  placeholder="{{ __('main.total_balance') }}"  readonly/>

                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.based_on') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select name="based_on" class="form-control" id="based_on">
                                    <option value="0" hidden>{{__('main.account_deposit')}}</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.document_balance') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="document_balance" name="document_balance" min="0" step="any"
                                       class="form-control"  placeholder="{{ __('main.document_balance') }}"  readonly/>

                                <input  type="hidden"   id="based_on_bill_number" name="based_on_bill_number" min="0" step="any"
                                       class="form-control"  placeholder="{{ __('main.document_balance') }}"  readonly/>


                            </div>
                        </div>



                    </div>
                    <div class="row">

                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.paid_money') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="amount" name="amount" min="0" step="any"
                                       class="form-control"  placeholder="{{ __('main.paid_money') }}"  />

                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.payment_method') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option value="0"> {{__('main.cash')}} </option>
                                    <option value="1"> {{__('main.visa')}} </option>

                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea name="notes" id="notes" rows="3" placeholder="{{ __('main.notes') }}" class="form-control-lg" style="width: 100%"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
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
        document.getElementById('client_id').value = '' ;
        $(document).on('change', '#branch_id', function () {
            getBillNo();  
           
        });
        function getBillNo() {
            let bill_number = document.getElementById('doc_number');  
            let branch_id = document.getElementById('branch_id').value;
    
            $.ajax({
                type: 'get',
                url: 'get-enter-mony-no/1/' + branch_id,
                dataType: 'json',
    
                success: function (response) {
                    console.log(response);
    
                    if (response) {
                        bill_number.value = response;
                    } else {
                        bill_number.value = '';
                    }
                }
            });
       
        }

        $('#client_id').change(function (){
            let branch_id = document.getElementById('branch_id').value;
            $.ajax({
                type: 'get',
                url: '/getCompany' + '/' + this.value,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) {
                        document.getElementById('balance').value = Number(response.credit_amount) - Number(response.deposit_amount) ;
                    }
                }
            });
            $.ajax({
                type: 'get',
                url: '/getClientExitWorks' + '/' + this.value + '/' + branch_id,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) {
                        $('#based_on')
                            .empty();
                        $('#based_on').append('<option value="0">{{__('main.account_deposit')}}</option>');
                        for (let i = 0; i < response.length; i++){
                            if(response.remain_money > 0){
                                $('#based_on').append('<option value="'+response[i].id+'">'+response[i].bill_number + '</option>');

                            }
                        }

                    }
                }
            });
        });

        $('#based_on').change(function(){

            const type = 4 ;
            $.ajax({
                type: 'get',
                url: '/getClientDocumentdata' + '/' + this.value + '/' + type,
                dataType: 'json',

                success: function (response) {
                    console.log(response);
                    if (response) { 
                        document.getElementById('document_balance').value = response.remain_money ;
                        document.getElementById('based_on_bill_number').value = response.bill_number ;
                    } else {
                        document.getElementById('document_balance').value =0 ;
                    }
                }
            });

        });
    })
</script>
