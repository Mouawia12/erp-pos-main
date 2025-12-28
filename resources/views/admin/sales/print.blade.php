<!DOCTYPE html>
<html>
<head>
    <title>
    @php
        $typeLabel = __('main.invoice_type_tax');
        if($data->invoice_type == 'simplified_tax_invoice') $typeLabel = __('main.invoice_type_simplified');
        if($data->invoice_type == 'non_tax_invoice') $typeLabel = __('main.invoice_type_nontax');
        $isReturn = ($data->sale_id ?? 0) > 0;
        $returnTag = $isReturn ? (' - ' . (__('main.return_tag') ?? 'مردود')) : '';
        $serviceLabels = [
            'dine_in' => __('main.service_mode_dine_in'),
            'takeaway' => __('main.service_mode_takeaway'),
            'delivery' => __('main.service_mode_delivery'),
        ];
        $serviceLabel = $serviceLabels[$data->service_mode ?? 'dine_in'] ?? __('main.service_mode_dine_in');
    @endphp
    {{$typeLabel}}{{$returnTag}} {{$data->id}}
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
        .trial-watermark {
            border: 1px dashed #f39c12;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 16px;
            color: #c0392b !important;
        }

        .tax-empty {
            display: inline-block;
            min-width: 120px;
            border-bottom: 1px solid #000;
            height: 20px;
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
            @if($trialMode ?? false)
                <div class="trial-watermark">
                    نسخة تجريبية - لا يمكن استخدام هذه الفاتورة لأغراض ضريبية رسمية
                </div>
            @endif
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
                    <h6 class="text-right mt-1" style="font-weight: bold;">
                        الرقم الضريبي :
                        @if(!empty($resolvedTaxNumber))
                            {{$resolvedTaxNumber}}
                        @else
                            <span class="tax-empty"></span>
                        @endif
                    </h6>
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
                        {{$typeLabel}}@if($isReturn) - {{ __('main.return_tag') ?? 'مردود' }} @endif
                        </strong> 
                    </h4>
                </div>
                <div class="col-4 text-left">
                    @if(!empty($qrCodeImage))
                        <div class="visible-print text-left mt-1">
                            <img src="{{$qrCodeImage}}" style="width: 70px; height: 70px;" alt="QR Code"/>
                        </div>
                    @endif
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
                    <tr>
                        <td>{{ __('main.service_mode') }} : <strong>{{ $serviceLabel }}</strong></td>
                        <td>{{ __('main.session_location') }} :
                            <strong>{{ $data->session_location ?? '-' }}</strong>
                        </td>
                    </tr>
                    @if(!empty($data->session_type))
                        <tr>
                            <td colspan="2">{{ __('main.session_type') }} : <strong>{{ $data->session_type }}</strong></td>
                        </tr>
                    @endif
                    @if(!empty($data->vehicle_plate) || !empty($data->vehicle_odometer))
                        <tr>
                            <td>{{ __('main.vehicle_plate') }} :
                                <strong>{{ $data->vehicle_plate ?? '-' }}</strong>
                            </td>
                            <td>{{ __('main.vehicle_odometer') }} :
                                <strong>{{ $data->vehicle_odometer ?? '-' }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{__('main.tax_mode')}} : <strong>{{ $data->tax_mode === 'exclusive' ? __('main.tax_mode_exclusive') : __('main.tax_mode_inclusive') }}</strong></td>
                        <td></td>
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
                        <th class="text-center">{{__('main.tax')}}<br> (Vat)</th>
                        <th class="text-center">{{__('main.discount')}}<br>(Disc)</th>
                        <th class="text-center">{{__('main.total_with_tax')}}<br>(Total With Vat)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                        <tr>
                            <td>{{$loop -> index+1}}</td>
                            <td>
                                {{ $detail->note ?: $detail->name }} -- {{$detail ->code }}
                                @if(!empty($detail->variant_color) || !empty($detail->variant_size))
                                    <div style="font-size: 11px; color:#555;">
                                        @if(!empty($detail->variant_color)) {{$detail->variant_color}} @endif
                                        @if(!empty($detail->variant_size)) - {{$detail->variant_size}} @endif
                                    </div>
                                @endif
                            </td>
                            <td>{{$detail ->price_unit }}</td> 
                            <td>{{$detail ->quantity }}</td>
                            <td>{{$detail ->total }}</td>
                            <td>{{ number_format($detail->discount_unit ?? 0,2) }}</td>
                            <td>
                                {{ number_format($detail ->tax + $detail ->tax_excise,2) }}
                                <div style="font-size: 10px; color:#555;">
                                    {{'%'. ($detail ->taxRate + $detail ->taxExciseRate) }}
                                </div>
                            </td> 
                            <td>{{ number_format($detail ->total + $detail->tax + $detail ->tax_excise - ($detail->discount_unit ?? 0),2) }}</td>
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
                           {{ number_format($details->sum('discount_unit'),2) }} -
                        </th>  
                        <th colspan="6" class="text-center">
                            {{__('main.promotions') ?? 'العروض الترويجية'}} (Promo Disc)
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
