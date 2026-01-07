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
        $titleAr = $isReturn ? 'إشعار خصم' : $typeLabel;
        $titleEn = $isReturn ? 'Credit Note' : (
            $data->invoice_type == 'simplified_tax_invoice' ? 'Simplified Tax Invoice' :
            ($data->invoice_type == 'non_tax_invoice' ? 'Non-tax Invoice' : 'Tax Invoice')
        );
        $promoDiscount = (float) $details->sum('discount_unit');
        $taxableAmount = (float) $data->total - (float) $data->discount - $promoDiscount;
        $vatTotal = (float) $data->tax + (float) $data->tax_excise;
        $amountDue = (float) $data->net - (float) $data->paid;
        $logoPath = !empty($company?->logo) ? asset('uploads/profiles/' . $company->logo) : asset('assets/img/logo.png');
        $customerDisplayName = $vendor?->company ?: ($vendor?->name ?? '-');
        if (!empty($vendor?->company) && !empty($vendor?->name)) {
            $customerDisplayName = $vendor->company . ' - ' . $vendor->name;
        }
    @endphp
    {{$titleAr}} {{$data->id}}
    </title>
    <meta charset="utf-8"/>
    <link href="{{asset('/assets/css/bootstrap.min.css')}}" rel="stylesheet" media="screen"/>
    @if($isPdf ?? false)
        <link href="{{asset('/assets/css/invoice-print.css')}}" rel="stylesheet"/>
    @else
        <link href="{{asset('/assets/css/invoice-print.css')}}" rel="stylesheet" media="print"/>
    @endif
    <style type="text/css" media="{{ ($isPdf ?? false) ? 'all' : 'screen' }}">
        @font-face {
            font-family: 'Almarai';
            src: url("{{asset('fonts/Almarai.ttf')}}");
        } 
        :root {
            --border: #cfcfcf;
            --muted: #666;
        }
        * {
            color: #000 !important;
            box-sizing: border-box;
        }
        body, html {
            color: #000;
            font-family: 'Almarai' !important;
            font-size: 12px !important;
            font-weight: 600;
            margin: 0;
            padding: 12px 16px;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .invoice-page {
            max-width: 210mm;
            margin: 0 auto;
        }
        .header-table,
        .info-table,
        .items-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .header-block {
            font-size: 12px;
            line-height: 1.5;
        }
        .header-center {
            text-align: center;
        }
        .company-logo {
            max-height: 70px;
            max-width: 110px;
        }
        .title {
            text-align: center;
            margin: 10px 0 6px;
            font-size: 18px;
            font-weight: 800;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .divider {
            border-top: 1px solid var(--border);
            margin: 8px 0;
        }
        .info-table td,
        .info-table th,
        .items-table th,
        .items-table td,
        .summary-table td {
            border: 1px solid var(--border);
            padding: 6px 8px;
            text-align: center;
        }
        .items-table th {
            background: #f2f2f2;
            font-weight: 700;
        }
        .items-table th,
        .items-table td {
            padding: 4px 6px;
            font-size: 11px;
            line-height: 1.2;
        }
        .info-row {
            display: table;
            width: 100%;
        }
        .info-qr,
        .info-details {
            display: table-cell;
            vertical-align: top;
        }
        .info-qr {
            width: 140px;
            text-align: center;
            padding-right: 8px;
        }
        .info-qr img {
            width: 120px;
            height: 120px;
        }
        .text-muted {
            color: var(--muted) !important;
        }
        .text-ltr {
            direction: ltr;
            unicode-bidi: embed;
        }
        .trial-watermark {
            border: 1px dashed #f39c12;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
            color: #c0392b !important;
        }
        .tax-empty {
            display: inline-block;
            min-width: 120px;
            border-bottom: 1px solid #000;
            height: 16px;
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
    </style>
    <style type="text/css" media="print"></style>
</head>
<body dir="rtl" style="background: #fff;">
<div class="invoice-page">
    @if($trialMode ?? false)
        <div class="trial-watermark">
            نسخة تجريبية - لا يمكن استخدام هذه الفاتورة لأغراض ضريبية رسمية
        </div>
    @endif

    <table class="header-table">
        <tr>
            <td class="header-block" dir="ltr">
                <div><strong>{{ $company?->name_en ?? '' }}</strong></div>
                @if(!empty($company?->registrationNumber))
                    <div>CR No. <span class="text-ltr">{{ $company->registrationNumber }}</span></div>
                @endif
                <div>VAT No. <span class="text-ltr">{{ $resolvedTaxNumber ?? '' }}</span></div>
                @if(!empty($data->branch_phone))
                    <div>Phone <span class="text-ltr">{{ $data->branch_phone }}</span></div>
                @endif
                @if(!empty($data->branch_address))
                    <div>{{ $data->branch_address }}</div>
                @endif
            </td>
            <td class="header-center">
                <img src="{{ $logoPath }}" alt="Logo" class="company-logo"/>
                <div><strong>{{ $company?->name_ar ?? $data->branch_name }}</strong></div>
                @if(!empty($company?->faild_ar))
                    <div class="text-muted">{{ $company->faild_ar }}</div>
                @endif
            </td>
            <td class="header-block" dir="rtl">
                <div><strong>{{ $company?->name_ar ?? '' }}</strong></div>
                @if(!empty($data->cr_number))
                    <div>السجل التجاري: <span class="text-ltr">{{ $data->cr_number }}</span></div>
                @endif
                <div>الرقم الضريبي: <span class="text-ltr">{{ $resolvedTaxNumber ?? '' }}</span></div>
                @if(!empty($data->branch_phone))
                    <div>هاتف الفرع: <span class="text-ltr">{{ $data->branch_phone }}</span></div>
                @endif
                @if(!empty($data->branch_address))
                    <div>{{ $data->branch_address }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="divider"></div>
    <div class="title">{{ $titleAr }}</div>
    <div class="subtitle">{{ $titleEn }}</div>

    <div class="info-row">
        <div class="info-qr">
            @if(!empty($qrCodeImage))
                <img src="{{$qrCodeImage}}" alt="QR Code"/>
            @endif
        </div>
        <div class="info-details">
            <table class="info-table">
                <tr>
                    <th>العميل</th>
                    <td>{{ $customerDisplayName }}</td>
                    <th class="text-ltr">Customer</th>
                    <td class="text-ltr">{{ $customerDisplayName }}</td>
                </tr>
                <tr>
                    <th>الرقم الضريبي</th>
                    <td class="text-ltr">{{ $vendor->vat_no ?? '-' }}</td>
                    <th class="text-ltr">VAT No</th>
                    <td class="text-ltr">{{ $vendor->vat_no ?? '-' }}</td>
                </tr>
                <tr>
                    <th>العنوان</th>
                    <td>{{ $vendor->address ?? '-' }}</td>
                    <th class="text-ltr">Address</th>
                    <td class="text-ltr">{{ $vendor->address ?? '-' }}</td>
                </tr>
                <tr>
                    <th>المدينة</th>
                    <td>{{ $vendor->city ?? '-' }}</td>
                    <th class="text-ltr">City</th>
                    <td class="text-ltr">{{ $vendor->city ?? '-' }}</td>
                </tr>
                <tr>
                    <th>رقم الفاتورة</th>
                    <td class="text-ltr">{{ $data->invoice_no }}</td>
                    <th class="text-ltr">Invoice No</th>
                    <td class="text-ltr">{{ $data->invoice_no }}</td>
                </tr>
                <tr>
                    <th>تاريخ الفاتورة</th>
                    <td class="text-ltr">{{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}</td>
                    <th class="text-ltr">Invoice Date</th>
                    <td class="text-ltr">{{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>نوع الخدمة</th>
                    <td>{{ $serviceLabel }}</td>
                    <th class="text-ltr">Service</th>
                    <td class="text-ltr">{{ $serviceLabel }}</td>
                </tr>
                <tr>
                    <th>طريقة الضريبة</th>
                    <td>{{ $data->tax_mode === 'exclusive' ? __('main.tax_mode_exclusive') : __('main.tax_mode_inclusive') }}</td>
                    <th class="text-ltr">Tax Mode</th>
                    <td class="text-ltr">{{ $data->tax_mode === 'exclusive' ? __('main.tax_mode_exclusive') : __('main.tax_mode_inclusive') }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)))
        <div style="margin-top: 8px;">
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

    <div class="divider"></div>

    <table class="items-table" style="direction: rtl;">
        <thead>
            <tr>
                <th>المنتجات<br><span class="text-ltr">Products</span></th>
                <th>الكمية<br><span class="text-ltr">Quantity</span></th>
                <th>سعر الوحدة<br><span class="text-ltr">Unit price</span></th>
                <th>المبلغ الخاضع للضريبة<br><span class="text-ltr">Taxable Amount</span></th>
                <th>مبلغ الضريبة<br><span class="text-ltr">VAT Amount</span></th>
                <th>الإجمالي شامل الضريبة<br><span class="text-ltr">Subtotal (Inc. VAT)</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
                @php
                    $lineTax = (float) $detail->tax + (float) $detail->tax_excise;
                    $lineSubtotal = (float) $detail->total + $lineTax - (float) ($detail->discount_unit ?? 0);
                @endphp
                <tr>
                    <td>
                        {{ $detail->note ?: $detail->name }} @if(!empty($detail->code)) - {{ $detail->code }} @endif
                        @if(!empty($detail->variant_color) || !empty($detail->variant_size))
                            <div class="text-muted" style="font-size: 11px;">
                                @if(!empty($detail->variant_color)) {{ $detail->variant_color }} @endif
                                @if(!empty($detail->variant_size)) - {{ $detail->variant_size }} @endif
                            </div>
                        @endif
                    </td>
                    <td class="text-ltr">{{ $detail->quantity }}</td>
                    <td class="text-ltr">{{ number_format($detail->price_unit,2) }}</td>
                    <td class="text-ltr">{{ number_format($detail->total,2) }}</td>
                    <td class="text-ltr">{{ number_format($lineTax,2) }}</td>
                    <td class="text-ltr">{{ number_format($lineSubtotal,2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="summary-table">
        <tr>
            <td>الإجمالي غير شامل الضريبة</td>
            <td class="text-ltr">{{ number_format($data->total,2) }}</td>
            <td class="text-ltr">Total Excluding VAT</td>
        </tr>
        <tr>
            <td>الخصومات</td>
            <td class="text-ltr">{{ number_format($data->discount,2) }}</td>
            <td class="text-ltr">Discounts</td>
        </tr>
        <tr>
            <td>{{ __('main.promotions') ?? 'العروض الترويجية' }}</td>
            <td class="text-ltr">{{ number_format($promoDiscount,2) }}</td>
            <td class="text-ltr">Promo Discounts</td>
        </tr>
        <tr>
            <td>الإجمالي الخاضع للضريبة</td>
            <td class="text-ltr">{{ number_format($taxableAmount,2) }}</td>
            <td class="text-ltr">Total Taxable Amount Excluding VAT</td>
        </tr>
        <tr>
            <td>ضريبة القيمة المضافة</td>
            <td class="text-ltr">{{ number_format($vatTotal,2) }}</td>
            <td class="text-ltr">Total VAT</td>
        </tr>
        <tr>
            <td>إجمالي الفاتورة</td>
            <td class="text-ltr">{{ number_format($data->net,2) }}</td>
            <td class="text-ltr">Total Amount</td>
        </tr>
        <tr>
            <td>إجمالي المدفوع</td>
            <td class="text-ltr">{{ number_format($data->paid,2) }}</td>
            <td class="text-ltr">Total Paid Amount</td>
        </tr>
        <tr>
            <td>المبلغ المستحق</td>
            <td class="text-ltr">{{ number_format($amountDue,2) }}</td>
            <td class="text-ltr">Amount Due</td>
        </tr>
    </table>

    <div class="divider"></div>
    <table class="info-table">
        <tr>
            <td>رقم الفاتورة / Invoice No</td>
            <td class="text-ltr">{{ $data->invoice_no }}</td>
            <td>وقت الطباعة / Print Time</td>
            <td class="text-ltr">{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td>اسم البائع</td>
            <td>{{ auth()->user()->name ?? '-' }}</td>
            <td class="text-ltr">Salesperson</td>
            <td class="text-ltr">{{ auth()->user()->name ?? '-' }}</td>
        </tr>
    </table>
</div>

<a href="@if($data->pos){{route('pos')}}@else{{route('sales')}}@endif" class="no-print btn btn-md btn-danger"
   style="left:20px!important;">
    العودة الى النظام
</a> 

<a href="{{ route('invoice.pdf', $data->id) }}" class="no-print btn btn-md btn-primary"
    style="left:150px!important;">
    حفظ PDF
</a>

<button onclick="window.print();" class="no-print btn btn-md btn-success"
    style="left:260px!important;">
    <i class="fa fa-print text-white"></i> اضغط للطباعة 
</button>

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script>
    window.addEventListener('load', async () => {
        try {
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }
        } catch (e) {}
        setTimeout(() => window.print(), 300);
    });
</script>

</body>
</html>
