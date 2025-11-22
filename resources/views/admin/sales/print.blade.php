<!DOCTYPE html>
<html>
<head>
    <title>
    @php
        $typeLabel = __('main.invoice_type_tax');
        if($data->invoice_type == 'simplified_tax_invoice') $typeLabel = __('main.invoice_type_simplified');
        if($data->invoice_type == 'non_tax_invoice') $typeLabel = __('main.invoice_type_nontax');
    @endphp
    {{$typeLabel}} {{$data->id}}
    </title>
    <meta charset="utf-8"/>
    <link href="{{asset('/assets/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <style type="text/css" media="screen">
        @font-face {
            font-family: 'Almarai';
            src: url("{{asset('fonts/Almarai.ttf')}}");
        } 
        * {
            color: #000 !important;
        }

        body, html {
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

        * {
            color: #000 !important;
        }

        body, html {
            color: #000;
            padding: 0px;
            margin: 0;
            font-family: 'Almarai' !important;
            font-size: 11px !important;
            font-weight: bold !important;
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
</head>
<body dir="rtl" style="background: #fff;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;" class="text-center">   
<header style="width: 95% ; display: block; margin: auto ; height: 3cm;" >

</header>            
    <div class="pos_details  justify-content-center text-center">  
        <div class="above-table w-50 text-right mt-3 justify-content-right" style="margin: 10px auto!important;">
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
                        <span dir="ltr">  
                            {{$data->branch_name}}
                        </span>
                    </h6> 
                    @if(!empty($data->cr_number))
                    <h6 class="text-right mt-1" style="font-weight: bold;">
                        السجل التجاري : {{$data->cr_number}}
                    </h6>
                    @endif
                    @if(!empty($data->branch_tax_number))
                    <h6 class="text-right mt-1" style="font-weight: bold;">
                        الرقم الضريبي : {{$data->branch_tax_number}}
                    </h6>
                    @endif
                    @if(!empty($data->branch_manager))
                    <h6 class="text-right mt-1" style="font-weight: bold;">
                        مدير الفرع : {{$data->branch_manager}}
                    </h6>
                    @endif
                    @if(!empty($data->branch_email))
                    <h6 class="text-right mt-1" style="font-weight: bold;">
                        بريد الفرع : {{$data->branch_email}}
                    </h6>
                    @endif
                </div>
                <div class="col-4 text-center">
                    <h4 class="text-center mt-1" style="font-weight: bold;">
                        <strong>
                        {{$typeLabel}}
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
            @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)))
                <div class="mt-2 text-right" style="direction:rtl;">
                    @if(!empty($data->note))
                        <strong>{{__('main.notes')}}:</strong>
                        <div>{{$data->note}}</div>
                    @endif
                    @if(!empty($settings) && !empty($settings->invoice_terms))
                        <strong>{{__('main.invoice_terms')}}:</strong>
                        <div>{{$settings->invoice_terms}}</div>
                    @endif
                </div>
            @endif
            <!--
            <h4 class="alert alert-secondary text-center"> 
                {{__('main.items')}} 
            </h4> 
            -->
            <table class="table-bordered text-center" style="width: 100% ; direction: rtl">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">{{__('main.item')}}<br>(Item) </th>
                        <th class="text-center">{{__('main.price.unit')}}<br>(U.Price)</th> 
                        <th class="text-center">{{__('main.quantity')}}<br>(Qty) </th>
                        <th class="text-center">{{__('main.amount')}}<br>(Amount)</th> 
                        <th colspan="2" class="text-center">{{__('main.tax')}}<br> (Vat)</th>
                        <th class="text-center">{{__('main.total_with_tax')}}<br>(Total With Vat)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                        <tr>
                            <td>{{$loop -> index+1}}</td>
                            <td>{{$detail ->name }} -- {{$detail ->code }}</td>
                            <td>{{$detail ->price_unit }}</td> 
                            <td>{{$detail ->quantity }}</td>
                            <td>{{$detail ->total }}</td>
                            <td> {{'%'. $detail ->taxRate + $detail ->taxExciseRate }}</td>
                            <td>{{$detail ->tax + $detail ->tax_excise}}</td> 
                            <td>{{$detail ->total + $detail->tax + $detail ->tax_excise}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-center">  
                            {{$data->total}}
                        </th> 
                        <th colspan="6" class="text-center">
                            {{__('main.total_without_tax')}} (Sub Total)
                        </th>

                    </tr>
                    <tr>
                        <th colspan="2" class="text-center">  
                           {{$data->discount}} -
                        </th>  
                        <th colspan="6" class="text-center">
                            {{__('main.discount')}}  (Discount)
                        </th> 
                    </tr>
                    <tr>
                        <th colspan="2" class="text-center">  
                            {{$data->tax}}
                        </th>  
                        <th colspan="6" class="text-center">
                            {{__('main.vat_tax')}} (VAT)
                        </th>

                    </tr> 
                    @if($data->tax_excise>0)
                    <tr>
                        <th colspan="2" class="text-center">  
                            {{$data->tax_excise}}
                        </th>  
                        <th colspan="6" class="text-center">
                            {{__('main.tax_excise')}} (Tax Excise)
                        </th>
          
                    </tr>
                    @endif
                    <tr>
                        <th colspan="2" class="text-center">  
                            {{$data->net}}
                        </th> 
                        <th colspan="6" class="text-center">  
                        {{__('main.total.due')}} (Total due)
                        </th> 
                
                    </tr>
                </tfoot>
            </table>   
            <br>
            <!--
            <h4 class="alert alert-secondary text-center"> 
               {{__('main.payments')}}
            </h4>  
                        
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">{{__('main.date')}}</th>
                        <th class="text-center">{{__('main.method.payment')}}</th>
                        <th class="text-center"> {{__('main.amount')}}</th>
                        <th class="text-center">{{__('main.user')}}</th> 
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
            -->
            <table>
                <tbody> 
                    <tr>
                        <td>{{$data->paid}}</td>
                        <td>{{__('main.paid')}}</td> 
                    </tr> 
                    <tr>
                        <td>{{$data->net - $data->paid}}</td>
                        <td>{{__('main.remain')}}</td> 
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

<a href="@if($data->pos){{route('pos')}}@else{{route('sales')}}@endif" class="no-print btn btn-md btn-danger"
   style="left:20px!important;">
    العودة الى النظام
</a> 

<button onclick="window.print();" class="no-print btn btn-md btn-success"
    style="left:150px!important;">
    <i class="fa fa-print text-white"></i> اضغط للطباعة 
</button>

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>

</body>
</html>
