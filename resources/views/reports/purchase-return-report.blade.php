<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.purchases_return_report') }}</title>
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
        tfoot td {
            background: #eef4ff;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('main.purchases_return_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
        <div>[ {{ $periodAr }} ]</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.supplier') }}: {{ $vendor?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.cost_center') }}: {{ $costCenter?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.bill_number') }}: {{ $billNo ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.bill_number') }}</th>
                <th>{{ __('main.bill_date') }}</th>
                <th>{{ __('main.branche') }}</th>
                <th>{{ __('main.warehouse') }}</th>
                <th>{{ __('main.supplier_name') }}</th>
                <th>{{ __('main.total') }}</th>
                <th>{{ __('main.paid') }}</th>
                <th>{{ __('main.remain') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $detail)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $detail->invoice_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->created_at ?? $detail->date)->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $detail->branch?->branch_name ?? '-' }}</td>
                    <td>{{ $detail->warehouse?->name ?? '-' }}</td>
                    <td>{{ $detail->customer?->name ?? '-' }}</td>
                    <td>{{ number_format(((float) ($detail->net ?? 0) * -1), 2) }}</td>
                    <td>{{ number_format(((float) ($detail->paid ?? 0) * -1), 2) }}</td>
                    <td>{{ number_format(((float) ($detail->net ?? 0) * -1) - ((float) ($detail->paid ?? 0) * -1), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td>{{ __('main.total.final') }}</td>
                <td>{{ number_format($totals['total'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['paid'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['remain'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
