<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.daily_sales_report') }}</title>
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
        .meta-row { display: table-row; }
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
        <h2>{{ __('main.daily_sales_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
        <div>[ {{ $period_ar ?? '' }} ]</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.customer') }}: {{ $customer?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.cost_center') }}: {{ $costCenter?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.vehicle_plate') }}: {{ $vehiclePlate ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.bill_number') }}</th>
                <th>{{ __('main.date') }}</th>
                <th>{{ __('main.branche') }}</th>
                <th>{{ __('main.warehouse') }}</th>
                <th>{{ __('main.customer') }}</th>
                <th>{{ __('main.vehicle_plate') }}</th>
                <th>{{ __('main.vehicle_odometer') }}</th>
                <th>{{ __('main.amount') }}</th>
                <th>{{ __('main.tax') }}</th>
                <th>{{ __('main.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $detail)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $detail->invoice_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $detail->branch?->branch_name ?? '-' }}</td>
                    <td>{{ $detail->warehouse?->name ?? '-' }}</td>
                    <td>{{ optional($detail->customer)->name ?? '-' }}</td>
                    <td>{{ $detail->vehicle_plate ?? '-' }}</td>
                    <td>{{ $detail->vehicle_odometer ?? '-' }}</td>
                    <td>{{ $detail->total }}</td>
                    <td>{{ ($detail->tax ?? 0) + ($detail->tax_excise ?? 0) }}</td>
                    <td>{{ $detail->net }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">{{ __('main.total') }}</td>
                <td>{{ number_format($totals['total'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['net'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
