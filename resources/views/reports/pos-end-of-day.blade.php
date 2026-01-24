<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}</title>
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
            margin-bottom: 18px;
        }
        .header h2 {
            margin: 0 0 6px;
            font-size: 18px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .meta-row {
            display: table-row;
        }
        .meta-cell {
            display: table-cell;
            padding: 4px 6px;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
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
        .section-title {
            margin: 10px 0 6px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('main.pos_end_of_day_report') ?? 'تقرير نهاية اليوم - نقاط البيع' }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.from_date') ?? 'من تاريخ' }}: {{ $filters['from'] ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.to_date') ?? 'إلى تاريخ' }}: {{ $filters['to'] ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.cashier') ?? 'الكاشير' }}: {{ $cashier?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.shift') ?? 'الشفت' }}: {{ $shift ? '#' . $shift->id . ' (' . $shift->opened_at . ')' : __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.date') ?? 'التاريخ' }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <div class="section-title">{{ __('main.summary') ?? 'الملخص' }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('main.type') ?? 'النوع' }}</th>
                <th>{{ __('main.total') }}</th>
                <th>{{ __('main.tax') }}</th>
                <th>{{ __('main.net') ?? 'الصافي' }}</th>
                <th>{{ __('main.profit') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ __('main.sales') ?? 'المبيعات' }}</td>
                <td>{{ number_format($summary['sales']['total'] ?? 0, 2) }}</td>
                <td>{{ number_format(($summary['sales']['tax'] ?? 0) + ($summary['sales']['tax_excise'] ?? 0), 2) }}</td>
                <td>{{ number_format($summary['sales']['net'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['sales']['profit'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>{{ __('main.returns') ?? 'المرتجعات' }}</td>
                <td>{{ number_format($summary['returns']['total'] ?? 0, 2) }}</td>
                <td>{{ number_format(($summary['returns']['tax'] ?? 0) + ($summary['returns']['tax_excise'] ?? 0), 2) }}</td>
                <td>{{ number_format($summary['returns']['net'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['returns']['profit'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>{{ __('main.quantity') }}</th>
                <td colspan="4">{{ number_format($summary['quantity'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>{{ __('main.cash') ?? 'نقدي' }}</th>
                <th>{{ __('main.bank') ?? 'تحويل/شبكة' }}</th>
                <th>{{ __('main.cards') ?? 'بطاقات' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($summary['payments']['cash'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['payments']['bank'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['payments']['card'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
