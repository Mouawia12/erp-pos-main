<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.quotations_report') }}</title>
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
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0 0 4px;
            font-size: 16px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .meta-row {
            display: table-row;
        }
        .meta-cell {
            display: table-cell;
            padding: 3px 6px;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d7d7d7;
            padding: 5px 6px;
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
        <h2>{{ __('main.quotations_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.customer') }}: {{ $customer?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.representatives') }}: {{ $representative?->user_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.cost_center') }}: {{ $costCenter?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.status') }}: {{ $status ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.quotation_no') }}: {{ $quotationNo ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.from_date') }}: {{ $dateFrom ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.to_date') }}: {{ $dateTo ?: __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.quotation_no') }}</th>
                <th>{{ __('main.date') }}</th>
                <th>{{ __('main.customer') }}</th>
                <th>{{ __('main.representatives') }}</th>
                <th>{{ __('main.branch') ?? 'الفرع' }}</th>
                <th>{{ __('main.warehouse') }}</th>
                <th>{{ __('main.cost_center') }}</th>
                <th>{{ __('main.total') }}</th>
                <th>{{ __('main.tax') }}</th>
                <th>{{ __('main.net') }}</th>
                <th>{{ __('main.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotations as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->quotation_no }}</td>
                    <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('Y-m-d') : '' }}</td>
                    <td>{{ $row->customer_name_display ?? $row->customer_name }}</td>
                    <td>{{ $row->representative_name ?? '-' }}</td>
                    <td>{{ $row->branch_name ?? '-' }}</td>
                    <td>{{ $row->warehouse_name ?? '-' }}</td>
                    <td>{{ $row->cost_center_name ?? $row->cost_center ?? '-' }}</td>
                    <td>{{ number_format($row->total ?? 0, 2) }}</td>
                    <td>{{ number_format($row->tax ?? 0, 2) }}</td>
                    <td>{{ number_format($row->net ?? 0, 2) }}</td>
                    <td>{{ $row->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">{{ __('main.total') }}</td>
                <td>{{ number_format($summary['total'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['tax'] ?? 0, 2) }}</td>
                <td>{{ number_format($summary['net'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
