<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.vendors_aging_report') }}</title>
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
        <h2>{{ __('main.vendors_aging_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.from_date') }}: {{ $dateFrom ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.to_date') }}: {{ $dateTo ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.supplier') }}: {{ $vendor?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.representative') }}: {{ $representative?->user_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.supplier') }}</th>
                <th>{{ __('main.representative') }}</th>
                <th>0-30</th>
                <th>31-60</th>
                <th>61-90</th>
                <th>91-120</th>
                <th>120+</th>
                <th>{{ __('main.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $row)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $row['company'] }}</td>
                    <td>{{ $row['representative_name'] ?? '-' }}</td>
                    <td>{{ number_format($row['aging']['current'] ?? 0, 2) }}</td>
                    <td>{{ number_format($row['aging']['30'] ?? 0, 2) }}</td>
                    <td>{{ number_format($row['aging']['60'] ?? 0, 2) }}</td>
                    <td>{{ number_format($row['aging']['90'] ?? 0, 2) }}</td>
                    <td>{{ number_format($row['aging']['over'] ?? 0, 2) }}</td>
                    <td>{{ number_format($row['balance'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
