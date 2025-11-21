<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-info no-print" id="btnPrint">Print</button>
                <label>{{__('main.money_entry_create')}}</label>
            </div>
            <div class="modal-body" id="smallBody">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="datetime-local"  id="date" name="date" readonly
                                       class="form-control" required
                                       placeholder="{{ __('main.date') }}"  value="{{ $bill -> date}}" disabled/>

                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.bill_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="text"   id="doc_number" name="doc_number"
                                       class="form-control"  placeholder="{{ __('main.bill_no') }}"  readonly disabled value="{{$bill -> doc_number}}"/>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.client') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select name="client_id" class="form-control" id="client_id" disabled>
                                    @foreach($clients as $client)
                                    <option value="{{$client -> id}}" @if($client -> id == $bill -> client_id)  selected @endif >{{$client -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.based_on') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                @if($bill -> based_on == 0)
                                    <input value="{{_('main.account_deposit')}} " class="form-control" disabled>
                                @else
                                    <input value="{{$bill -> invoice_number}}" class="form-control" disabled>
                                @endif
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.payment_method') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" class="form-control" readonly value="{{$bill -> payment_method == 0 ? __('main.cash') : __('main.visa')}}">

                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.paid_money') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input required type="number"   id="amount" name="amount" min="0" step="any" disabled
                                       class="form-control"  placeholder="{{ __('main.paid_money') }}"
                                value="{{$bill -> amount }}"/>

                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea name="notes" id="notes" rows="3" disabled placeholder="{{ __('main.notes') }}" class="form-control-lg" style="width: 100%">{{$bill -> notes}}</textarea>
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
