<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.inventory_value_report') }}</title>
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
        tfoot td {
            background: #eef4ff;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('main.inventory_value_report') }}</h2>
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
                <th>{{ __('main.cost') }}</th>
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
                    <td>{{ number_format($row->cost ?? 0, 2) }}</td>
                    <td>{{ number_format(($row->quantity ?? 0) * ($row->cost ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">{{ __('main.total') }}</td>
                <td>{{ number_format($totalValue ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
