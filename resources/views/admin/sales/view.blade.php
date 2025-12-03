<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
            dir="rtl" style="background: #fff;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <style type="text/css" media="screen">
                    @font-face {
                        font-family: 'Almarai';
                        src: url("{{asset('fonts/Almarai.ttf')}}");
                    } 
                    * {
                        color: #000 !important;
                    }
            
                    #paymentsModal {
                        color: #000;
                        font-family: 'Almarai' !important;
                        font-size: 13px !important;
                        font-weight: bold;
                        margin: 0;
                        padding: 10px;
                        page-break-before: avoid;
                        page-break-after: avoid;
                        page-break-inside: avoid;
                    }
            
                    .no-print {
                        position: fixed;
                        bottom: 0;
                        color: #fff !important;
                        left: 30px;
                        height: 40px !important;
                        border-radius: 0;
                        padding-top: 10px;
                        z-index: 9999;
                    }
            
                    table thead tr, table tbody tr {
                        border-bottom: 1px solid #aaa;
                    }
            
                    table {
                        text-align: center;
                        width: 100% !important;
                        margin-top: 10px !important;
                    }
                </style>
                <style type="text/css" media="print">
                    .above-table {
                        width: 100% !important;
                    }
            
                    table {
                        text-align: center;
                        width: 100% !important;
                        margin-top: 10px !important;
                    }
            
                    table thead tr, table tbody tr {
                        //border-bottom: 1px solid #aaa;
                    }
            
                    * {
                        color: #000 !important;
                    }
            
                    #paymentsModal {
                        color: #000;
                        padding: 0px;
                        margin: 0;
                        font-family: 'Almarai' !important;
                       
                        page-break-before: avoid;
                        page-break-after: avoid;
                        page-break-inside: avoid;
                    }
            
                    .pos_details {
                        width: 100% !important;
                        page-break-before: avoid;
                        page-break-after: avoid;
                        page-break-inside: avoid;
                    }
            
                    .no-print {
                        display: none;
                    } 
                
                </style>
                <header style="width: 95% ; display: block; margin: auto ; height: 3cm;" >

                </header>   
            </div>
            <div class="modal-body" id="smallBody"> 
                <div class="above-table w-75 text-right mt-3 justify-content-right" style="margin: 10px auto!important;">
                    <div class="row" id="" style="direction:rtl">
                        <div class="col-4 text-right">
                            <h6 class="text-right mt-1" style="font-weight: bold;">
                                رقم الفاتورة :
                                <span dir="ltr">
                                   {{$data->invoice_no}}
                                </span>
                            </h6>
                            <h6 class="text-right mt-1" style="font-weight: bold;">
                                التاريخ :
                                <span dir="ltr"> 
                                     {{\Carbon\Carbon::parse($data->date) -> format('d- m -Y') }}
                                </span>
                            </h6>
                            <h6 class="text-right mt-1" style="font-weight: bold;">
                                الفرع :
                                <span dir="ltr"> 
                                    {{$data->branch_id}}
                                </span>
                            </h6> 
                        </div>
                        <div class="col-4 text-center">
                            <h4 class="text-center mt-1" style="font-weight: bold;">
                                <strong>
                                @if(empty($vendor->vat_no))
                                    فاتورة ضريبية مبسطة
                                @else
                                    فاتورة ضريبية  
                                @endif 
                                </strong> 
                            </h4>
                        </div>
                        <div class="col-4 text-left">
                            <div class="visible-print text-left mt-1">
                                <?php
                                use Salla\ZATCA\GenerateQrCode;
                                use Salla\ZATCA\Tags\InvoiceDate;
                                use Salla\ZATCA\Tags\InvoiceTaxAmount;
                                use Salla\ZATCA\Tags\InvoiceTotalAmount;
                                use Salla\ZATCA\Tags\Seller;
                                use Salla\ZATCA\Tags\TaxNumber;
                                $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
                                    new Seller($company->name_ar), // seller name
                                    new TaxNumber($company->taxNumber), // seller tax number
                                    new InvoiceDate($data->date), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                                    new InvoiceTotalAmount($data->net), // invoice total amount
                                    new InvoiceTaxAmount($data -> tax) // invoice tax amount
                                    // TODO :: Support others tags
                                ])->render();
                                ?>
                                <img src="{{$displayQRCodeAsBase64}}" style="width: 70px; height: 70px;" alt="QR Code"/>
                            </div>
                        </div>
                        <div class="clearfix"> </div> 
                        <hr>
                    </div>
                    <table class="table text-right">
                        <tbody> 
                            <tr>
                                <td>{{__('main.client')}} : <strong>{{$vendor->name}}</strong></td> 
                                <td>{{__('سجل ضريبي')}} : <strong>{{$vendor->vat_no}}</strong></td> 
                            </tr>  
                        </tbody>
                    </table>    
                    <h4 class="alert alert-secondary text-center"> 
                        {{__('main.items')}} 
                    </h4> 
                    <table class="table-bordered text-center" style="width: 100% ; direction: rtl">
                        <thead>
                        <tr>
                            <th class="col-md-3">{{__('main.item')}}<br>(Item) </th>
                            <th class="col-md-1">{{__('main.price.unit')}}<br>(U.Price)</th> 
                            <th class="col-md-1">{{__('main.quantity')}}<br>(Qty) </th>
                            <th class="col-md-1">{{__('main.amount')}}<br>(Amount)</th>
                            <th class="col-md-1">{{__('main.tax')}}<br> (Vat)</th>
                            <th class="col-md-2">{{__('main.total_without_tax')}}<br>(Total With Vat)</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{ $detail->note ?: $detail->name }} -- {{$detail ->code }}</td>
                                    <td>{{$detail ->price_unit }}</td> 
                                    <td>{{$detail ->quantity }}</td>
                                    <td>{{$detail ->total }}</td>
                                    <td>{{$detail ->tax }}</td>
                                    <td>{{$detail ->total + $detail->tax }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th colspan="3" class="alert alert">
                                {{__('main.sum')}}
                            </th>
                            <th>  
                                {{$data->total}}
                            </th> 
                            <th>  
                                {{$data->tax}} 
                            </th> 
                            <th>  
                                {{$data->net}}
                            </th> 
                        </tfoot>
                    </table>   
                    <br>
                    <h4 class="alert alert-secondary text-center"> 
                       {{__('main.payments')}}
                    </h4>  
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{__('main.date')}}</th>
                                <th>{{__('main.method.payment')}}</th>
                                <th> {{__('main.amount')}}</th>
                                <th>{{__('main.user')}}</th> 
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{$payment->date}}</td>
                                <td>{{$payment->paid_by}}</td>
                                <td>{{$payment->amount}}</td>
                                <td>{{$payment->user ?  $payment->user -> name : ''}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br>
                    <table style="width: 100% ; direction: rtl" class="table-bordered">
                        <tbody> 
                            <tr>
                                <td>{{__('main.paid')}}</td>
                                <td>{{$data->paid}}</td>
                            </tr> 
                            <tr>
                                <td>{{__('main.remain')}}</td>
                                <td>{{$data->net - $data->paid}}</td>
                            </tr>
                        </tbody>
                    </table> 
                    <hr>
                    <div class="row" style="direction:rtl">
                        <div class="col-6 text-center">
                            <span> اسم البائع</span> <br>
                            <span>{{auth() -> user() -> name}}</span>
                        </div>
                        <div class="col-6 text-center">
                            <span>  مدير الفرع</span> <br>
                            <span>........</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<a href="@if($data->pos){{route('pos')}}@else{{route('sales')}}@endif" class="no-print btn btn-md btn-danger"
   style="left:20px!important;">
    العودة الى النظام
</a> 

<button onclick="window.print();" class="no-print btn btn-md btn-success"
    style="left:150px!important;">
    <i class="fa fa-print text-white"></i> اضغط للطباعة 
</button>

<script>
    $(document).ready(function () {
        //window.print();
    });
</script>
