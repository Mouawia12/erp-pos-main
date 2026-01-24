<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.inventory_variance_report') }}</title>
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
        <h2>{{ __('main.inventory_variance_report') }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.inventory') }}: {{ $inventory ? ('#' . $inventory->id . ' - ' . ($inventory->date ? \Carbon\Carbon::parse($inventory->date)->format('Y-m-d') : '')) : __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.from_date') }}: {{ $dateFrom ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.to_date') }}: {{ $dateTo ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.difference') }}:
                @if($differenceType === 'shortage')
                    {{ __('main.shortage') }}
                @elseif($differenceType === 'excess')
                    {{ __('main.excess') }}
                @else
                    {{ __('main.all') }}
                @endif
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('main.item_name_code') }}</th>
                <th>{{ __('main.inventory') }}</th>
                <th>{{ __('main.warehouse') }}</th>
                <th>{{ __('main.branch') ?? 'الفرع' }}</th>
                <th>{{ __('main.quantity') }}</th>
                <th>{{ __('main.counted_quantity') }}</th>
                <th>{{ __('main.difference') }}</th>
                <th>{{ __('main.cost') }}</th>
                <th>{{ __('main.value') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>{{ $row->product_name }} <div class="small text-muted">{{ $row->product_code }}</div></td>
                    <td>#{{ $row->inventory_id }} - {{ $row->inventory_date ? \Carbon\Carbon::parse($row->inventory_date)->format('Y-m-d') : '' }}</td>
                    <td>{{ $row->warehouse_name ?? '-' }}</td>
                    <td>{{ $row->branch_name ?? '-' }}</td>
                    <td>{{ number_format($row->quantity ?? 0, 2) }}</td>
                    <td>{{ number_format($row->new_quantity ?? 0, 2) }}</td>
                    <td>{{ number_format($row->difference ?? 0, 2) }}</td>
                    <td>{{ number_format($row->product_cost ?? 0, 2) }}</td>
                    <td>{{ number_format($row->difference_value ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">{{ __('main.no_data') ?? 'لا يوجد بيانات' }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        <strong>{{ __('main.inventory_variance_report') }}</strong>
        <div>
            <span>{{ __('main.shortage') }}: {{ number_format($totals['shortage'] ?? 0, 2) }}</span>
            <span style="margin-inline-start: 12px;">{{ __('main.excess') }}: {{ number_format($totals['excess'] ?? 0, 2) }}</span>
        </div>
    </div>
</body>
</html>
