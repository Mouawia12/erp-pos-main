<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8"/>
    @php
        $typeLabel = __('main.invoice_type_tax');
        if ($data->invoice_type == 'simplified_tax_invoice') $typeLabel = __('main.invoice_type_simplified');
        if ($data->invoice_type == 'non_tax_invoice') $typeLabel = __('main.invoice_type_nontax');
        $isReturn = ($data->sale_id ?? 0) > 0;
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
        $customerDisplayName = $vendor?->company ?: ($vendor?->name ?? '-');
        if (!empty($vendor?->company) && !empty($vendor?->name)) {
            $customerDisplayName = $vendor->company . ' - ' . $vendor->name;
        }
        $fontUrl = !empty($fontDataUri) ? $fontDataUri : asset('fonts/Almarai.ttf');
        $logoSrc = $logoDataUri ?? asset('assets/img/logo.png');
    @endphp
    <title>{{ $titleAr }} {{ $data->id }}</title>
    <link href="{{ asset('/assets/css/invoice-print.css') }}" rel="stylesheet"/>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        @font-face {
            font-family: 'Almarai';
            src: url("{{ $fontUrl }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        :root {
            --border: #cfcfcf;
            --muted: #666;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            color: #000;
            font-family: 'Almarai', Arial, sans-serif;
            font-size: 12pt;
        }
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .invoice {
            width: 190mm;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid var(--border);
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 11pt;
        }
        tr {
            page-break-inside: avoid;
        }
        .header-table td {
            border: none;
            vertical-align: top;
        }
        .header-block {
            font-size: 11pt;
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
            margin: 10px 0 4px;
            font-size: 18pt;
            font-weight: 700;
        }
        .subtitle {
            text-align: center;
            font-size: 11pt;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .divider {
            border-top: 1px solid var(--border);
            margin: 8px 0;
        }
        .info-table th,
        .items-table th,
        .summary-table th {
            background: #f2f2f2;
            font-weight: 700;
        }
        .items-table th,
        .items-table td {
            padding: 3px 4px;
            font-size: 10pt;
            line-height: 1.15;
        }
        .text-ltr {
            direction: ltr;
            unicode-bidi: embed;
        }
        .qr-cell {
            width: 35mm;
            text-align: center;
            vertical-align: top;
            padding: 6px 0;
        }
        .qr-cell img {
            width: 35mm;
            height: 35mm;
        }
        .trial-watermark {
            border: 1px dashed #f39c12;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 12pt;
            color: #c0392b;
        }
        .section-no-break {
            page-break-inside: avoid;
        }
        .controls {
            margin-top: 12px;
            text-align: center;
        }
        .controls a,
        .controls button {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 6px;
            border: 1px solid #333;
            background: #f7f7f7;
            color: #000;
            font-size: 11pt;
            text-decoration: none;
            cursor: pointer;
        }
        @media print {
            .controls {
                display: none !important;
            }
        }
        .alert {
            padding: 8px 12px;
            margin: 8px 0;
            border: 1px solid #e1b12c;
            background: #fff7d6;
            color: #6b4e00;
            font-size: 11pt;
            text-align: center;
        }
    </style>
</head>
<body dir="rtl">
<div class="invoice">
    @if(session('pdf_error'))
        <div class="alert">{{ session('pdf_error') }}</div>
    @endif
    @if($trialMode ?? false)
        <div class="trial-watermark">
            نسخة تجريبية - لا يمكن استخدام هذه الفاتورة لأغراض ضريبية رسمية
        </div>
    @endif

    <table class="header-table section-no-break">
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
                @if(!empty($logoSrc))
                    <img src="{{ $logoSrc }}" alt="Logo" class="company-logo"/>
                @endif
                <div><strong>{{ $company?->name_ar ?? $data->branch_name }}</strong></div>
                @if(!empty($company?->faild_ar))
                    <div style="color: var(--muted);">{{ $company->faild_ar }}</div>
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

    <table class="section-no-break">
        <tr>
            <td class="qr-cell">
                @if(!empty($qrCodeImage))
                    <img src="{{ $qrCodeImage }}" alt="QR Code"/>
                @endif
            </td>
            <td>
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
            </td>
        </tr>
    </table>

    @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)))
        <div style="margin-top: 8px;">
            @if(!empty($data->note))
                <strong>{{ __('main.notes') }}:</strong>
                <div>{{ $data->note }}</div>
            @endif
            @if(!empty($settings) && !empty($settings->invoice_terms))
                <strong>{{ __('main.invoice_terms') }}:</strong>
                <div>{{ $settings->invoice_terms }}</div>
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
                            <div style="color: var(--muted); font-size: 10pt;">
                                @if(!empty($detail->variant_color)) {{ $detail->variant_color }} @endif
                                @if(!empty($detail->variant_size)) - {{ $detail->variant_size }} @endif
                            </div>
                        @endif
                    </td>
                    <td class="text-ltr">{{ $detail->quantity }}</td>
                    <td class="text-ltr">{{ number_format($detail->price_unit,2) }}</td>
                    <td class="text-ltr">{{ number_format($detail->total,2) }}</td>
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
    <table class="info-table section-no-break">
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

    @if(!($isPdf ?? false))
        <div class="controls" dir="rtl">
            <a href="@if($data->pos){{ route('pos') }}@else{{ route('sales') }}@endif">العودة</a>
            <a href="{{ route('invoice.pdf', $data->id) }}">حفظ PDF</a>
            <button onclick="window.print()">طباعة</button>
        </div>
    @endif
</div>
</body>
</html>
