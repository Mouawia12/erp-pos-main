<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8"/>
    @php
        $typeLabel = __('main.invoice_type_tax');
        if($data->invoice_type == 'simplified_tax_invoice') $typeLabel = __('main.invoice_type_simplified');
        if($data->invoice_type == 'non_tax_invoice') $typeLabel = __('main.invoice_type_nontax');
        $isReturn = ($data->sale_id ?? 0) > 0;
        $titleAr = $isReturn ? 'إشعار خصم' : $typeLabel;
        $titleEn = $isReturn ? 'Credit Note' : (
            $data->invoice_type == 'simplified_tax_invoice' ? 'Simplified Tax Invoice' :
            ($data->invoice_type == 'non_tax_invoice' ? 'Non-tax Invoice' : 'Tax Invoice')
        );
        $promoDiscount = (float) $details->sum('discount_unit');
        $taxableAmount = (float) $data->total - (float) $data->discount - $promoDiscount;
        $vatTotal = (float) $data->tax + (float) $data->tax_excise;
        $amountDue = (float) $data->net - (float) $data->paid;
        $paymentMethodMap = [
            'cash' => ['نقدي', __('main.cash') ?? 'Cash'],
            'network' => ['شبكة', __('main.network') ?? __('main.visa') ?? 'Network'],
            'cash_network' => ['نقدي - شبكة', (__('main.cash') ?? 'Cash') . ' + ' . (__('main.network') ?? __('main.visa') ?? 'Network')],
            'credit' => ['أجل', __('main.credit') ?? 'Credit'],
        ];
        $paymentMethodKey = $data->payment_method ?? null;
        $paymentMethodLabels = $paymentMethodMap[$paymentMethodKey] ?? null;
        $paymentLabelAr = $paymentMethodLabels[0] ?? '-';
        $paymentLabelEn = $paymentMethodLabels[1] ?? '-';
        $customerDisplayName = !empty($vendor?->company) ? $vendor->company : ($vendor?->name ?? '-');
        $companyNameAr = $company?->name_ar ?? $data->branch_name ?? '';
        $companyNameEn = $company?->name_en ?? '';
        $companyAddress = $company?->address ?? $data->branch_address ?? '';
        $companyPhone = $company?->phone ?? $data->branch_phone ?? '';
        $companyPhone2 = $company?->phone2 ?? '';
        $companyEmail = $company?->email ?? $data->branch_email ?? '';
        $companyTaxNumber = $company?->taxNumber ?? $resolvedTaxNumber ?? '';
        $companyCrn = $company?->registrationNumber ?? $data->cr_number ?? '';
        $logoSrc = $logoDataUri ?? asset('assets/img/logo.png');
        $currency = '﷼';
        $issueDate = !empty($data->date) ? \Carbon\Carbon::parse($data->date)->format('Y-m-d') : '-';
        $supplyDate = $issueDate;
        $referenceNo = $data->sale_id ?? $data->id ?? '-';
        $representativeName = $data->representative_user_name ?? $data->representative_name ?? '-';
        $totalItems = (float) $details->sum('quantity');
        $totalDiscount = (float) $data->discount + $promoDiscount;
        $companyLogoFile = $company?->logo ?? $company?->image_url ?? null;
        $logoPath = !empty($companyLogoFile) ? asset('uploads/profiles/' . $companyLogoFile) : $logoSrc;
    @endphp
    <title>{{ $titleAr }} {{ $data->id }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { margin:0; font-family: "Tajawal","Cairo", Arial, sans-serif; color:#000; direction: rtl; text-align: right; }
        table { width:100%; border-collapse:collapse; }
        th,td { border:1px solid #999; padding:6px 8px; font-size:12px; vertical-align:middle; }
        .thin { border-color:#bbb; }
        .no-border { border:0 !important; }
        .section-line { border-top:2px solid #777; margin:8px 0; }
        .invoice-page { width:190mm; margin:0 auto; }
        .header-table { direction: ltr; }
        .header-table td { border:0; vertical-align:top; }
        .header-block { font-size:12px; line-height:1.4; }
        .header-block--en { text-align:left; direction:ltr; }
        .header-logo { text-align:center; }
        .header-logo img { max-height:70px; max-width:110px; }
        .title-table td { border:0; }
        .title-cell { text-align:center; }
        .title-ar { font-size:18px; font-weight:700; }
        .title-en { font-size:14px; font-weight:700; }
        .qr-cell { width:45mm; text-align:right; }
        .qr-cell img { width:40mm; height:40mm; }
        .meta-title-row td { border:0; vertical-align:top; }
        .meta-cell { width:60%; }
        .title-qr-cell { width:40%; }
        .title-qr-wrap { width:100%; table-layout:fixed; border-collapse:collapse; }
        .title-qr-wrap td { border:0; }
        .meta-wrapper { direction:ltr; }
        .meta-wrapper td { border:0; }
        .meta-table { width:100%; border-collapse:collapse; table-layout:fixed; direction:ltr; }
        .meta-table td { border:1px solid #999; }
        .meta-table .label-en { text-align:left; direction:ltr; }
        .meta-table .label-ar { text-align:right; direction:rtl; }
        .meta-table .value-cell { text-align:center; }
        .num { direction:ltr; text-align:left; unicode-bidi: embed; }
        .num-right { direction:ltr; text-align:right; unicode-bidi: embed; }
        .party-wrap { width:100%; table-layout:fixed; direction:ltr; }
        .party-wrap td { border:0; vertical-align:top; }
        .party-table th { background:#f7f7f7; font-weight:700; }
        .party-table .label { width:35%; white-space:nowrap; }
        .items-table { table-layout:fixed; direction:rtl; }
        .items-table th, .items-table td { font-size:11px; padding:5px 6px; }
        .items-table .item-desc { text-align:right; word-break:break-word; }
        .items-table .th-ar { display:block; font-size:11px; font-weight:700; }
        .items-table .th-en { display:block; font-size:10px; direction:ltr; }
        .totals-table { table-layout:fixed; direction:ltr; }
        .totals-table td { border:1px solid #999; }
        .totals-table .label-en { text-align:left; direction:ltr; }
        .totals-table .label-ar { text-align:right; direction:rtl; }
        .totals-table .strong { font-weight:700; border-top:2px solid #777; }
        .footer-table { table-layout:fixed; direction:ltr; }
        .footer-table td { border:0; vertical-align:top; }
        .notes-box { border:1px solid #999; min-height:50px; padding:6px; }
        .signature-line { border-bottom:1px dotted #333; height:18px; margin-top:6px; }
        .page-number { text-align:right; margin-top:6px; font-size:11px; }
        .trial-watermark { border:1px dashed #f39c12; padding:10px; margin-bottom:10px; text-align:center; font-size:12pt; color:#c0392b; }
        .action-bar {
            position: fixed;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            z-index: 50;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12);
        }
        .action-btn--back {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #111827;
        }
        .action-btn--pdf {
            background: #0ea5e9;
            color: #fff;
        }
        .action-btn--print {
            background: #111827;
            color: #fff;
        }
        @media print { .action-bar { display:none !important; } }
    </style>
</head>
<body>
<div class="invoice-page">
    @if($trialMode ?? false)
        <div class="trial-watermark">
            نسخة تجريبية - لا يمكن استخدام هذه الفاتورة لأغراض ضريبية رسمية
        </div>
    @endif

    <table class="header-table">
        <tr>
            <td class="header-block header-block--en" dir="ltr">
                @if(!empty($companyNameEn))
                    <div><strong>{{ $companyNameEn }}</strong></div>
                @endif
                @if(!empty($companyAddress))
                    <div>{{ $companyAddress }}</div>
                @endif
                @if(!empty($companyPhone))
                    <div>Phone <span class="num">{{ $companyPhone }}</span></div>
                @endif
                @if(!empty($companyPhone2))
                    <div>Phone <span class="num">{{ $companyPhone2 }}</span></div>
                @endif
                @if(!empty($companyTaxNumber))
                    <div>VAT No. <span class="num">{{ $companyTaxNumber }}</span></div>
                @endif
            </td>
            <td class="header-logo">
                @if(!empty($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo"/>
                @endif
            </td>
            <td class="header-block" dir="rtl">
                @if(!empty($companyNameAr))
                    <div><strong>{{ $companyNameAr }}</strong></div>
                @endif
                @if(!empty($companyAddress))
                    <div>{{ $companyAddress }}</div>
                @endif
                @if(!empty($companyPhone))
                    <div>جوال: <span class="num">{{ $companyPhone }}</span></div>
                @endif
                @if(!empty($companyPhone2))
                    <div>جوال: <span class="num">{{ $companyPhone2 }}</span></div>
                @endif
                @if(!empty($companyTaxNumber))
                    <div>الرقم الضريبي: <span class="num">{{ $companyTaxNumber }}</span></div>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-line"></div>

    <table class="meta-title-row" dir="rtl">
        <tr>
            <td class="title-qr-cell">
                <table class="title-qr-wrap" dir="rtl">
                    <tr>
                        <td class="title-cell">
                            <div class="title-ar">فاتورة ضريبية</div>
                            <div class="title-en">Tax invoice</div>
                        </td>
                        <td class="qr-cell">
                            @if(!empty($qrCodeImage))
                                <img src="{{ $qrCodeImage }}" alt="QR Code"/>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="meta-cell">
                <table class="meta-table">
                    <tr>
                        <td class="label-en">Invoice No</td>
                        <td class="value-cell"><span class="num-right">{{ $data->invoice_no }}</span></td>
                        <td class="label-ar">رقم الفاتورة</td>
                    </tr>
                    <tr>
                        <td class="label-en">Ref No</td>
                        <td class="value-cell"><span class="num-right">{{ $referenceNo }}</span></td>
                        <td class="label-ar">رقم المرجع</td>
                    </tr>
                    <tr>
                        <td class="label-en">Issue date</td>
                        <td class="value-cell"><span class="num-right">{{ $issueDate }}</span></td>
                        <td class="label-ar">تاريخ الإصدار</td>
                    </tr>
                    <tr>
                        <td class="label-en">Date of supply</td>
                        <td class="value-cell"><span class="num-right">{{ $supplyDate }}</span></td>
                        <td class="label-ar">تاريخ التوريد</td>
                    </tr>
                    <tr>
                        <td class="label-en">Payment</td>
                        <td class="value-cell">{{ $paymentLabelAr }}</td>
                        <td class="label-ar">الدفع</td>
                    </tr>
                    <tr>
                        <td class="label-en">Commissary</td>
                        <td class="value-cell">{{ $representativeName }}</td>
                        <td class="label-ar">المندوب</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-line"></div>

    <table class="party-wrap">
        <tr>
            <td style="width:50%;">
                <table class="party-table">
                    <tr>
                        <th colspan="2">البائع / Seller</th>
                    </tr>
                    <tr>
                        <td class="label">Name / الاسم</td>
                        <td>{{ $companyNameAr ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Address / العنوان</td>
                        <td>
                            <div>{{ $companyAddress ?: '-' }}</div>
                            <div>{{ $companyEmail }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">VAT Number / الرقم الضريبي</td>
                        <td class="num-right">{{ $companyTaxNumber ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">CRN / السجل التجاري</td>
                        <td class="num-right">{{ $companyCrn ?: '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table class="party-table">
                    <tr>
                        <th colspan="2">العميل / Customer</th>
                    </tr>
                    <tr>
                        <td class="label">Name / الاسم</td>
                        <td>{{ $customerDisplayName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Address / العنوان</td>
                        <td>
                            <div>{{ $vendor->address ?? '-' }}</div>
                            <div>{{ $vendor->city ?? '' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">VAT Number / الرقم الضريبي</td>
                        <td class="num-right">{{ $vendor->vat_no ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Customer Mobile / جوال العميل</td>
                        <td class="num-right">{{ $vendor->phone ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-line"></div>

    <table class="items-table">
        <colgroup>
            <col style="width:6%">
            <col style="width:26%">
            <col style="width:8%">
            <col style="width:10%">
            <col style="width:6%">
            <col style="width:8%">
            <col style="width:12%">
            <col style="width:7%">
            <col style="width:8%">
            <col style="width:9%">
        </colgroup>
        <thead>
            <tr>
                <th><span class="th-ar">رقم الصنف</span><span class="th-en">Item No</span></th>
                <th><span class="th-ar">تفاصيل السلع والخدمات</span><span class="th-en">Product or Service Details</span></th>
                <th><span class="th-ar">الوحدة</span><span class="th-en">Unit</span></th>
                <th><span class="th-ar">سعر الوحدة</span><span class="th-en">Unit Price</span></th>
                <th><span class="th-ar">الكمية</span><span class="th-en">Qty</span></th>
                <th><span class="th-ar">الخصم</span><span class="th-en">Discount</span></th>
                <th><span class="th-ar">المبلغ الخاضع للضريبة</span><span class="th-en">Taxable Amount</span></th>
                <th><span class="th-ar">نسبة الضريبة</span><span class="th-en">VAT Rate</span></th>
                <th><span class="th-ar">مبلغ الضريبة</span><span class="th-en">VAT Amount</span></th>
                <th><span class="th-ar">المجموع + الضريبة</span><span class="th-en">Subtotal + VAT</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $detail)
                @php
                    $qty = (float) $detail->quantity;
                    $discountUnit = (float) ($detail->discount_unit ?? 0);
                    $discountLine = $discountUnit * $qty;
                    $unitPrice = (float) $detail->price_unit + $discountUnit;
                    $taxable = ($unitPrice * $qty) - $discountLine;
                    $lineTax = (float) $detail->tax + (float) $detail->tax_excise;
                    $vatRate = $taxable > 0 ? ($lineTax / $taxable) * 100 : 0;
                    $lineTotal = $taxable + $lineTax;
                @endphp
                <tr>
                    <td class="num-right">{{ $loop->iteration }}</td>
                    <td class="item-desc">
                        {{ $detail->note ?: $detail->name }} @if(!empty($detail->code)) - {{ $detail->code }} @endif
                        @if(!empty($detail->variant_color) || !empty($detail->variant_size))
                            <div style="font-size:10px; color:#666;">
                                @if(!empty($detail->variant_color)) {{ $detail->variant_color }} @endif
                                @if(!empty($detail->variant_size)) - {{ $detail->variant_size }} @endif
                            </div>
                        @endif
                    </td>
                    <td>{{ $detail->unit_name ?? '-' }}</td>
                    <td class="num-right">{{ number_format($unitPrice, 2) }}</td>
                    <td class="num-right">{{ number_format($qty, 2) }}</td>
                    <td class="num-right">{{ number_format($discountLine, 2) }}</td>
                    <td class="num-right">{{ number_format($taxable, 2) }}</td>
                    <td class="num-right">{{ number_format($vatRate, 2) }}%</td>
                    <td class="num-right">{{ number_format($lineTax, 2) }}</td>
                    <td class="num-right">{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-line"></div>

    <table class="totals-table">
        <tr>
            <td class="label-en">Number Of Items</td>
            <td class="num-right">{{ number_format($totalItems, 3) }}</td>
            <td class="label-ar">عدد الأصناف</td>
        </tr>
        <tr>
            <td class="label-en">Total Amount</td>
            <td class="num-right">{{ $currency }} {{ number_format($data->net, 2) }}</td>
            <td class="label-ar">إجمالي المبلغ</td>
        </tr>
        <tr>
            <td class="label-en">Total (Excluding VAT)</td>
            <td class="num-right">{{ $currency }} {{ number_format($data->total, 2) }}</td>
            <td class="label-ar">الإجمالي (غير شامل الضريبة)</td>
        </tr>
        <tr>
            <td class="label-en">Discount</td>
            <td class="num-right">{{ $currency }} {{ number_format($totalDiscount, 2) }}</td>
            <td class="label-ar">مجموع الخصومات</td>
        </tr>
        <tr>
            <td class="label-en">Total Taxable Amount (Excluding VAT)</td>
            <td class="num-right">{{ $currency }} {{ number_format($taxableAmount, 2) }}</td>
            <td class="label-ar">الإجمالي الخاضع للضريبة (غير شامل الضريبة)</td>
        </tr>
        <tr>
            <td class="label-en">Total VAT</td>
            <td class="num-right">{{ $currency }} {{ number_format($vatTotal, 2) }}</td>
            <td class="label-ar">مجموع الضريبة</td>
        </tr>
        <tr>
            <td class="label-en strong">Total Amount Due</td>
            <td class="num-right strong">{{ $currency }} {{ number_format($data->net, 2) }}</td>
            <td class="label-ar strong">إجمالي المبلغ المستحق</td>
        </tr>
        <tr>
            <td class="label-en">Paid Amount</td>
            <td class="num-right">{{ $currency }} {{ number_format($data->paid, 2) }}</td>
            <td class="label-ar">المبلغ المدفوع</td>
        </tr>
        <tr>
            <td class="label-en">Remaining amount</td>
            <td class="num-right">{{ $currency }} {{ number_format($amountDue, 2) }}</td>
            <td class="label-ar">المتبقي على الفاتورة</td>
        </tr>
    </table>

    <div class="section-line"></div>

    <table class="footer-table">
        <tr>
            <td style="width:30%;">
                <div>Seller / البائع</div>
                <div>{{ $representativeName }}</div>
            </td>
            <td style="width:40%; text-align:center;">
                <div>استلمت البضاعة كاملة وسليمة</div>
                <div>Received By</div>
                <div class="signature-line"></div>
            </td>
            <td style="width:30%;">
                <div>ملاحظات / Notes</div>
                <div class="notes-box">{!! !empty($data->note) ? nl2br(e($data->note)) : '&nbsp;' !!}</div>
            </td>
        </tr>
    </table>

    <div class="page-number">1/1</div>
</div>

<div class="action-bar no-print" dir="rtl">
    <a class="action-btn action-btn--back" href="@if($data->pos){{route('pos')}}@else{{route('sales')}}@endif">العودة الى النظام</a>
    <a class="action-btn action-btn--pdf" href="{{ route('invoice.pdf', $data->id) }}">حفظ PDF</a>
    <button onclick="window.print();" class="action-btn action-btn--print">اضغط للطباعة</button>
</div>

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
