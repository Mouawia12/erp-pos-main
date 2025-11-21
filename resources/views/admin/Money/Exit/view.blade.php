<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-info no-print" id="btnPrint">Print</button>
                <label>{{__('main.money_exit_preview')}}</label>
            </div>
            <div class="modal-body" id="smallBody">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="date" name="date"
                                       class="form-control" required readonly
                                       placeholder="{{ __('main.date') }}"  value="{{ \Carbon\Carbon::parse($bill -> date) -> format('d-m-Y') }}" />

                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.bill_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="doc_number" name="doc_number"
                                       class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly value="{{$bill -> doc_number}}"/>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.payment_type') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                @if($bill -> type == 0)
                                    <input required type="text"   id="doc_number" name="doc_number"
                                           class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly value="{{__('main.payment_type0')}}"/>
                                @elseif($bill -> type == 1)
                                    <input required type="text"   id="doc_number" name="doc_number"
                                           class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly value="{{__('main.payment_type1')}}"/>
                                @else
                                    <input required type="text"   id="doc_number" name="doc_number"
                                           class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly value="{{__('main.payment_type2')}}"/>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.supplier') .'/'. __('main.client')}} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" class="form-control" readonly value="{{$bill -> vendor_name}}">

                            </div>
                        </div> 
                    </div> 
                    <div class="row payment_type1">
                        @if($bill -> type > 0)
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.price_gram') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="price_gram" name="price_gram"
                                       class="form-control"  placeholder="{{ __('main.balance_gold') }}"  readonly value="{{$bill -> price_gram}}"/>
                            </div>
                        </div>
                        @endif

                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.based_on') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" class="form-control" readonly value="{{$bill -> invoice_number}}">
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.paid_money') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="amount" name="amount" min="0" step="any"
                                       class="form-control"  placeholder="{{ __('main.paid_money') }}"  readonly value="{{$bill -> amount}}"/>
                            </div>
                        </div>

                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.payment_method') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                @if($bill -> payment_method == 0)
                                <input type="text" class="form-control" readonly value="{{__('main.cash')}} ">
                                @else
                                    <input type="text" class="form-control" readonly value="{{__('main.visa')}} ">
                                @endif
                            </div>
                        </div>  
                    </div>
                @if($bill -> type > 0)
                    <div class="row payment_type1"> 
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.paid_weight21') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="paid_weight21" name="paid_weight21"  step="any"
                                       class="form-control"  placeholder="0" readonly  value="{{ $bill -> price_gram > 0 ? number_format($bill -> amount / $bill -> price_gram,2) : 0}}" />

                            </div>
                        </div>
                    </div>
                @endif
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea name="notes" id="notes" rows="3" placeholder="{{ __('main.notes') }}" class="form-control-lg" style="width: 100%" readonly disabled>{{$bill -> notes}}</textarea>
                            </div>
                        </div>
                    </div> 
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();

        });

    });

    function printPage(){ 
        var css = '@page { size: portrait; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

        const originalHTML = document.body.innerHTML;
        document.body.innerHTML = document.getElementById('paymentsModal').innerHTML;
        document.querySelectorAll('.not-print')
            .forEach(img => img.remove())
        window.print();

    }
</script>
