<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('main.invoice_type') }} - A5</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 10mm;
        }
        body { font-family: Arial, sans-serif; direction: rtl; margin: 0; padding: 0; }
        .invoice { width: 100%; font-size: 12px; }
        .header, .footer { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: center; }
        .no-border td { border: none; }
        .totals td { text-align: left; }
        .trial-watermark {
            border: 1px dashed #f39c12;
            padding: 6px;
            margin-bottom: 6px;
            text-align: center;
            color: #c0392b;
        }
        .tax-empty {
            display: inline-block;
            min-width: 120px;
            border-bottom: 1px solid #000;
            height: 16px;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            @php
                $isReturn = ($data->sale_id ?? 0) > 0;
                $serviceLabels = [
                    'dine_in' => __('main.service_mode_dine_in'),
                    'takeaway' => __('main.service_mode_takeaway'),
                    'delivery' => __('main.service_mode_delivery'),
                ];
                $serviceLabel = $serviceLabels[$data->service_mode ?? 'dine_in'] ?? __('main.service_mode_dine_in');
            @endphp
            <h3>{{ $company->name_ar ?? $data->branch_name }}</h3>
            <div>{{ $data->branch_name }} - {{ $data->branch_address }}</div>
            @if($trialMode ?? false)
                <div class="trial-watermark">
                    نسخة تجريبية - البيانات الضريبية غير حقيقية
                </div>
            @endif
            <div>{{ __('main.invoice_type') }}: 
                @if($data->invoice_type == 'tax_invoice') {{ __('main.invoice_type_tax') }}
                @elseif($data->invoice_type == 'simplified_tax_invoice') {{ __('main.invoice_type_simplified') }}
                @else {{ __('main.invoice_type_nontax') }} @endif
                @if($isReturn) - {{ __('main.return_tag') ?? 'مردود' }} @endif
            </div>
            <div>{{ __('main.invoice_no') }}: {{ $data->invoice_no }} | {{ __('main.bill_date') }}: {{ $data->date }}</div>
            <div>{{ __('main.service_mode') }}: {{ $serviceLabel }}</div>
            <div>{{ __('main.session_location') }}: {{ $data->session_location ?? '-' }}</div>
            @if(!empty($data->session_type))
                <div>{{ __('main.session_type') }}: {{ $data->session_type }}</div>
            @endif
            <div>
                {{ __('main.tax_number') ?? 'الرقم الضريبي' }}:
                @if(!empty($resolvedTaxNumber))
                    {{ $resolvedTaxNumber }}
                @else
                    <span class="tax-empty"></span>
                @endif
            </div>
        </div>

        <table class="no-border">
            <tr>
                <td>{{ __('main.clients') }}: {{ $vendor->name ?? '' }}</td>
                <td>{{ __('main.branche') }}: {{ $data->branch_name }}</td>
            </tr>
            <tr>
                <td>{{ __('main.warehouse') }}: {{ $data->warehouse_name }}</td>
                <td>{{ __('main.payment_status') }}: {{ $data->payment_status }}</td>
            </tr>
            @if(!empty($data->vehicle_plate) || !empty($data->vehicle_odometer))
                <tr>
                    <td>{{ __('main.vehicle_plate') }}: {{ $data->vehicle_plate ?? '-' }}</td>
                    <td>{{ __('main.vehicle_odometer') }}: {{ $data->vehicle_odometer ?? '-' }}</td>
                </tr>
            @endif
        </table>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('main.product_code') }}</th>
                    <th>{{ __('main.name') }}</th>
                    <th>{{ __('main.quantity') }}</th>
                    <th>{{ __('main.price.unit') }}</th>
                    <th>{{ __('main.tax') }}</th>
                    <th>{{ __('main.discount') }}</th>
                    <th>{{ __('main.total_with_tax') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $index => $detail)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $detail->product_code }}</td>
                        <td>
                            {{ $detail->note ?: $detail->name }}
                            @if(!empty($detail->variant_color) || !empty($detail->variant_size))
                                <div style="font-size: 11px; color:#555;">
                                    @if(!empty($detail->variant_color)) {{ $detail->variant_color }} @endif
                                    @if(!empty($detail->variant_size)) - {{ $detail->variant_size }} @endif
                                </div>
                            @endif
                        </td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price_unit,2) }}</td>
                        <td>
                            {{ number_format($detail->tax + $detail->tax_excise,2) }}
                            <div style="font-size: 10px; color:#555;">
                                {{'%'. ($detail->taxRate + $detail->taxExciseRate) }}
                            </div>
                        </td>
                        <td>{{ number_format($detail->discount_unit ?? 0,2) }}</td>
                        <td>{{ number_format($detail->total + $detail->tax + $detail->tax_excise - ($detail->discount_unit ?? 0),2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>{{ __('main.total_without_tax') }}: {{ number_format($data->total,2) }}</td>
                <td>{{ __('main.tax') }}: {{ number_format($data->tax + $data->tax_excise,2) }}</td>
            </tr>
            <tr>
                <td>{{ __('main.promotions') ?? 'العروض الترويجية' }}: -{{ number_format($details->sum('discount_unit'),2) }}</td>
                <td>{{ __('main.net_after_discount') }}: {{ number_format($data->net,2) }}</td>
            </tr>
            <tr>
                <td>{{ __('main.paid') }}: {{ number_format($data->paid,2) }}</td>
                <td colspan="1">{{ __('main.remain') }}: {{ number_format($data->net - $data->paid,2) }}</td>
            </tr>
        </table>

        @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)) || !empty($qrCodeImage))
            <div class="footer">
                @if(!empty($data->note))
                    <div><strong>{{ __('main.notes') }}:</strong> {{ $data->note }}</div>
                @endif
                @if(!empty($settings) && !empty($settings->invoice_terms))
                    <div><strong>{{ __('main.invoice_terms') }}:</strong> {!! nl2br(e($settings->invoice_terms)) !!}</div>
                @endif
                @if(!empty($qrCodeImage))
                    <div style="margin-top:8px;">
                        <img src="{{ $qrCodeImage }}" alt="QR" style="width:100px;height:100px;">
                    </div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
