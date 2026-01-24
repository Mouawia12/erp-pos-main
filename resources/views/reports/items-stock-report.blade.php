<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.users_transactions_report') }}</title>
    <style>
        @font-face {
            font-family: 'Tajawal';
            src: url('{{ 'file://' . str_replace('\\', '/', public_path('fonts/Tajawal-Regular.ttf')) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Tajawal', 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            color: #1b1b1b;
            font-size: 12px;
            margin: 24px;
        }
        .header {
            text-align: center;
            margin-bottom: 14px;
        }
        .header h2 {
            margin: 0 0 6px;
            font-size: 18px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .meta-row { display: table-row; }
        .meta-cell {
            display: table-cell;
            padding: 4px 6px;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d7d7d7;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background: #f4f6f9;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('main.users_transactions_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branche') }}: {{ $branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ $period_ar ?? '' }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.product_code') }}</th>
                <th>{{ __('main.product_name') }}</th>
                <th>{{ __('main.qnt_purchase') }}</th>
                <th>{{ __('main.qnt_purchase_return') }}</th>
                <th>{{ __('main.qnt_sales') }}</th>
                <th>{{ __('main.qnt_sales_return') }}</th>
                <th>{{ __('main.qnt_net') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($result as $detail)
                @if(\Carbon\Carbon::parse($detail['date'])->gte(\Carbon\Carbon::parse($fdate)) &&
                    \Carbon\Carbon::parse($detail['date'])->lte(\Carbon\Carbon::parse($tdate)))
                    @if($detail['warehouse'] == $warehouse || $warehouse == 0)
                        @if($detail['item_id'] == $item_id || $item_id == 0)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $detail['product_code'] }}</td>
                                <td>{{ $detail['product_name'] }}</td>
                                <td>{{ $detail['qnt_purchase'] }}</td>
                                <td>{{ $detail['qnt_purchase_return'] }}</td>
                                <td>{{ $detail['qnt_sales'] }}</td>
                                <td>{{ $detail['qnt_sales_return'] }}</td>
                                <td>
                                    {{ $detail['qnt_update'] + $detail['qnt_purchase'] + $detail['qnt_purchase_return'] - $detail['qnt_sales'] - $detail['qnt_sales_return'] }}
                                </td>
                            </tr>
                        @endif
                    @endif
                @endif
            @empty
                <tr>
                    <td colspan="8">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
