<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.inventory_aging_report') }}</title>
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
            margin-bottom: 16px;
        }
        .header h2 {
            margin: 0 0 6px;
            font-size: 18px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-bottom: 14px;
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
        <h2>{{ __('main.inventory_aging_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.category') }}: {{ $category?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.brand') }}: {{ $brand?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('main.item_name_code') }}</th>
                <th>{{ __('main.warehouse') }}</th>
                <th>{{ __('main.branch') ?? 'الفرع' }}</th>
                <th>{{ __('main.quantity') }}</th>
                <th>{{ __('main.last_purchase_date') }}</th>
                <th>{{ __('main.days_since_last_purchase') }}</th>
                <th>{{ __('main.aging_bucket') }}</th>
                <th>{{ __('main.value') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>{{ $row->name }} <div class="small text-muted">{{ $row->code }}</div></td>
                    <td>{{ $row->warehouse_name ?? '-' }}</td>
                    <td>{{ $row->branch_name ?? '-' }}</td>
                    <td>{{ number_format($row->quantity ?? 0, 2) }}</td>
                    <td>{{ $row->last_purchase_date ? \Carbon\Carbon::parse($row->last_purchase_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $row->days_since !== null ? $row->days_since : '-' }}</td>
                    <td>{{ $row->aging_bucket }}</td>
                    <td>{{ number_format($row->value ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        <strong>{{ __('main.inventory_aging_report') }}</strong>
        <div>
            <span>0-30: {{ number_format($agingTotals['current'] ?? 0, 2) }}</span>
            <span style="margin-inline-start: 12px;">31-60: {{ number_format($agingTotals['30'] ?? 0, 2) }}</span>
            <span style="margin-inline-start: 12px;">61-90: {{ number_format($agingTotals['60'] ?? 0, 2) }}</span>
            <span style="margin-inline-start: 12px;">91-120: {{ number_format($agingTotals['90'] ?? 0, 2) }}</span>
            <span style="margin-inline-start: 12px;">120+: {{ number_format($agingTotals['over'] ?? 0, 2) }}</span>
        </div>
    </div>
</body>
</html>
