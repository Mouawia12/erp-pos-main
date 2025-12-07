<!DOCTYPE html>
<html>
<head>
    <title>
    @php
        $typeLabel = __('main.invoice_type_simplified');
        if($data->invoice_type == 'tax_invoice') $typeLabel = __('main.invoice_type_tax');
        if($data->invoice_type == 'non_tax_invoice') $typeLabel = __('main.invoice_type_nontax');
        $serviceLabels = [
            'dine_in' => __('main.service_mode_dine_in'),
            'takeaway' => __('main.service_mode_takeaway'),
            'delivery' => __('main.service_mode_delivery'),
        ];
        $serviceLabel = $serviceLabels[$data->service_mode ?? 'dine_in'] ?? __('main.service_mode_dine_in');
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

        table thead tr, table tbody tr {
            border-bottom: 1px solid #aaa;
        }

        table {
            text-align: center;
            width: 100% !important;
            margin-top: 10px !important;
        }
        .trial-watermark {
            border: 1px dashed #f39c12;
            padding: 6px;
            margin: 6px 0;
            text-align: center;
            font-size: 14px;
            color: #c0392b !important;
        }
        .tax-empty {
            display: inline-block;
            min-width: 120px;
            border-bottom: 1px solid #000;
            height: 18px;
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
            border-bottom: 1px solid #aaa;
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
    <div class="pos_details  justify-content-center text-center"> 
        <div class="text-center">
            @if($trialMode ?? false)
                <div class="trial-watermark">
                    نسخة تجريبية - لن يتم طباعة بيانات ضريبية حقيقية
                </div>
            @endif
            <h2 class="text-center mt-1" style="font-weight: bold;">
                {{$typeLabel}}
                <br>
                <small style="font-size:12px;">
                    @if($data->invoice_type == 'tax_invoice')
                        {{__('main.invoice_type_tax')}}
                    @elseif($data->invoice_type == 'non_tax_invoice')
                        {{__('main.invoice_type_nontax')}}
                    @else
                        {{__('main.invoice_type_simplified')}}
                    @endif
                </small>
            </h2>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                رقم الفاتورة :
                <span dir="ltr">
                {{$data->invoice_no}}
                </span>
            </h6> 
            <h3 class="text-center" style="font-weight: bold;">
                {{$company->name_ar}}
            </h3> 
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{$data->branch_name}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{$data->branch_address}}
            </h6> 
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{ __('main.service_mode') }} : {{$serviceLabel}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{ __('main.session_location') }} : {{$data->session_location ?? '-'}}
            </h6>
            @if(!empty($data->session_type))
                <h6 class="text-center mt-1" style="font-weight: bold;">
                    {{ __('main.session_type') }} : {{$data->session_type}}
                </h6>
            @endif
            @if(!empty($data->vehicle_plate) || !empty($data->vehicle_odometer))
                <h6 class="text-center mt-1" style="font-weight: bold;">
                    {{ __('main.vehicle_plate') }} : {{$data->vehicle_plate ?? '-'}}
                </h6>
                <h6 class="text-center mt-1" style="font-weight: bold;">
                    {{ __('main.vehicle_odometer') }} : {{$data->vehicle_odometer ?? '-'}}
                </h6>
            @endif
            @if(!empty($data->branch_phone))
            <h6 class="text-center mt-1" style="font-weight: bold;">
                هاتف الفرع / Branch Phone : {{$data->branch_phone}}
            </h6> 
            @endif
            @if(!empty($data->cr_number))
            <h6 class="text-center mt-1" style="font-weight: bold;">
                السجل التجاري / CR : {{$data->cr_number}}
            </h6>
            @endif
            <h6 class="text-center mt-1" style="font-weight: bold;">
                الرقم الضريبي / VAT :
                @if(!empty($resolvedTaxNumber))
                    {{$resolvedTaxNumber}}
                @else
                    <span class="tax-empty"></span>
                @endif
            </h6>
            <div class="clearfix"></div> 
            <h6 class="text-center mt-1" style="font-weight: bold;">
                التاريخ / Date :
                <span dir="ltr"> 
                    {{\Carbon\Carbon::parse($data->created_at)->format('Y-m-d H:i') }} 
                </span>
            </h6> 
            @if(empty($resolvedTaxNumber) && !empty($company->taxNumber))
                <h6 class="text-center mt-1" style="font-weight: bold;">
                    الرقم الضريبى / VAT : {{$company->taxNumber}}
                </h6>
            @endif 
            @if(!empty($company->registrationNumber))
            <h6 class="text-center mt-1" style="font-weight: bold;">
                السجل التجاري / CR : {{$company->registrationNumber}}
            </h6>
            @endif
        </div>
        <div class="mt-2" style="text-align:right; direction:rtl; border:1px dashed #aaa; padding:6px;">
            <strong>العميل / Customer:</strong> {{ optional($vendor)->name }}<br>
            @if(!empty(optional($vendor)->phone))
            <strong>جوال / Phone:</strong> {{ optional($vendor)->phone }}<br>
            @endif
            @if(!empty(optional($vendor)->address))
            <strong>العنوان / Address:</strong> {{ optional($vendor)->address }}
            @endif
            <br>
            <strong>{{__('main.tax_mode')}}:</strong>
            {{ $data->tax_mode === 'exclusive' ? __('main.tax_mode_exclusive') : __('main.tax_mode_inclusive') }}
        </div>
        @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)))
            <div class="mt-2" style="text-align:right; direction:rtl; border:1px dotted #aaa; padding:6px;">
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
        <div class="above-table w-25 text-center mt-3  justify-content-center" style="margin: 10px auto!important;">
            <table class="table-bordered text-center" style="width: 100% ; direction: rtl">
                <thead>
                    <tr> 
                        <th class="text-center">{{__('المنتج')}}<br>Product</th> 
                        <th class="text-center">{{__('main.quantity')}}<br>Qty</th>  
                        <th class="text-center">{{__('main.Amount')}}<br>Amount</th>  
                        <th class="text-center">{{__('main.discount')}}<br>Disc</th>  
    
                    </tr>
                </thead>
                <tbody>
                    @php
                       $qty = 0;
                    @endphp
                    @foreach($details as $detail)
                        <tr> 
                            <td>
                                {{ $detail->note ?: $detail->name }}
                                @if(!empty($detail->variant_color) || !empty($detail->variant_size))
                                    <div style="font-size: 11px; color:#555;">
                                        @if(!empty($detail->variant_color)) {{$detail->variant_color}} @endif
                                        @if(!empty($detail->variant_size)) - {{$detail->variant_size}} @endif
                                    </div>
                                @endif
                            </td> 
                            <td>{{$detail ->quantity }}</td>  
                            <td>{{$detail ->total }}</td> 
                            <td>{{ number_format($detail->discount_unit ?? 0,2) }}</td> 
                        </tr>
                    @php
                       $qty = $qty + $detail ->quantity;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot> 
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                         {{__('main.total_without_tax')}} (Sub Total)
                        </th>
                        <th colspan="1" class="alert alert text-center">
                          {{$data->total}}
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                         {{__('main.discount')}}  (Discount)
                        </th>
                        <th colspan="1" class="alert alert text-center">
                           {{$data->discount}} -
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                          {{__('main.promotions') ?? 'العروض الترويجية'}} (Promo)
                        </th>
                        <th colspan="1" class="alert alert text-center">
                           {{ number_format($details->sum('discount_unit'),2) }} -
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                            {{__('main.vat_tax')}} (VAT)
                        </th>
                        <th colspan="1" class="alert alert text-center">
                           {{$data->tax}} 
                        </th>
                    </tr>
                    @if($data->tax_excise>0)
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                            {{__('main.tax_excise')}} (Tax Excise) 
                        </th>  
                        <th colspan="1" class="alert alert text-center">
                            {{$data->tax_excise}}
                        </th>
          
                    </tr>
                    @endif
                    <tr>
                        <th colspan="2" class="alert alert text-center">
                            {{__('الاجمالي')}}  (ريال)
                        </th>
                        <th colspan="1" class="alert alert text-center">
                            {{$data->net}}
                        </th>
                    </tr>

                </tfoot>
            </table>   
            @if(!empty($data->note))
                <div class="mt-2 text-right" style="direction:rtl;">
                    <strong>ملاحظات / Notes:</strong>
                    <div>{{$data->note}}</div>
                </div>
            @endif
            @if(!empty(optional($vendor)->invoice_footer) || !empty($company->faild_ar))
                <div class="mt-2 text-right" style="direction:rtl;">
                    <strong>الشروط / Terms:</strong>
                    <div>{{optional($vendor)->invoice_footer ?? $company->faild_ar}}</div>
                </div>
            @endif
            @if(!empty($qrCodeImage))
                <div class="visible-print text-center mt-1">
                    <img src="{{$qrCodeImage}}" style="width: 150px; height: 150px;" alt="QR Code"/>
                </div>
            @endif
            <hr style="border-top: 1px solid #000;">
            <div class="row" style="direction:rtl">
                <div class="col-12 text-right">
                    <span> اسم البائع</span> <br>
                    <span>{{auth() -> user() -> name}}</span>
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
