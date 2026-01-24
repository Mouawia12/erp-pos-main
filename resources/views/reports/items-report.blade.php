<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>
        @if($type == 0)
            {{ __('main.items_report') }}
        @elseif($type == 1)
            {{ __('main.under_limit_items_report') }}
        @else
            {{ __('main.no_balance_items_report') }}
        @endif
    </title>
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
        <h2>
            @if($type == 0)
                {{ __('main.items_report') }}
            @elseif($type == 1)
                {{ __('main.under_limit_items_report') }}
            @else
                {{ __('main.no_balance_items_report') }}
            @endif
        </h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.branch') ?? 'الفرع' }}: {{ $branch?->branch_name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.warehouse') }}: {{ $warehouse?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.categories') }}: {{ $category?->name ?? __('main.all') }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.brands') }}: {{ $brand?->name ?? __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.product_code') }}</th>
                <th>{{ __('main.product_name') }}</th>
                @if($isbranches == 1)
                    <th>{{ __('main.categories') }}</th>
                    <th>{{ __('main.branche') }}</th>
                    <th>{{ __('main.warehouse') }}</th>
                @endif
                <th>{{ __('main.unit') }}</th>
                <th>{{ __('main.quantity') }}</th>
                @if($type == 1)
                    <th>{{ __('main.alert_quantity') }}</th>
                @endif
                <th>{{ __('main.Cost') }}</th>
                <th>{{ __('main.price') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    @if($isbranches == 1)
                        <td>{{ $item->categories_name }}</td>
                        <td>{{ $item->branch_name }}</td>
                        <td>{{ $item->warehouse_name }}</td>
                    @endif
                    <td>{{ $item->units?->name ?? '-' }}</td>
                    <td>{{ $item->qty }}</td>
                    @if($type == 1)
                        <td>{{ $item->alert_quantity }}</td>
                    @endif
                    <td>{{ number_format($item->cost ?? 0, 2) }}</td>
                    <td>{{ number_format($item->price ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isbranches == 1 ? ($type == 1 ? 11 : 10) : ($type == 1 ? 8 : 7) }}">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                @if($isbranches == 1)
                    <td colspan="{{ $type == 1 ? 7 : 6 }}"></td>
                @else
                    <td colspan="{{ $type == 1 ? 4 : 3 }}"></td>
                @endif
                <td>{{ __('main.total') }}</td>
                @if($type == 1)
                    <td></td>
                @endif
                <td>{{ number_format($totals['cost'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['price'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
