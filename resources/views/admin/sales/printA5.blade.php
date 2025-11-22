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
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h3>{{ $company->name_ar ?? $data->branch_name }}</h3>
            <div>{{ $data->branch_name }} - {{ $data->branch_address }}</div>
            <div>{{ __('main.invoice_type') }}: 
                @if($data->invoice_type == 'tax_invoice') {{ __('main.invoice_type_tax') }}
                @elseif($data->invoice_type == 'simplified_tax_invoice') {{ __('main.invoice_type_simplified') }}
                @else {{ __('main.invoice_type_nontax') }} @endif
            </div>
            <div>{{ __('main.invoice_no') }}: {{ $data->invoice_no }} | {{ __('main.bill_date') }}: {{ $data->date }}</div>
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
                    <th>{{ __('main.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $index => $detail)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $detail->product_code }}</td>
                        <td>{{ $detail->name }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price_unit,2) }}</td>
                        <td>{{ number_format($detail->tax + $detail->tax_excise,2) }}</td>
                        <td>{{ number_format($detail->total,2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>{{ __('main.total') }}: {{ number_format($data->total,2) }}</td>
                <td>{{ __('main.tax') }}: {{ number_format($data->tax + $data->tax_excise,2) }}</td>
            </tr>
            <tr>
                <td>{{ __('main.net_after_discount') }}: {{ number_format($data->net,2) }}</td>
                <td>{{ __('main.paid') }}: {{ number_format($data->paid,2) }}</td>
            </tr>
            <tr>
                <td colspan="2">{{ __('main.remain') }}: {{ number_format($data->net - $data->paid,2) }}</td>
            </tr>
        </table>

        @if(!empty($data->note) || (!empty($settings) && !empty($settings->invoice_terms)))
            <div class="footer">
                @if(!empty($data->note))
                    <div><strong>{{ __('main.notes') }}:</strong> {{ $data->note }}</div>
                @endif
                @if(!empty($settings) && !empty($settings->invoice_terms))
                    <div><strong>{{ __('main.invoice_terms') }}:</strong> {!! nl2br(e($settings->invoice_terms)) !!}</div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
