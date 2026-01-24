<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.vendors_movement_report') }}</title>
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
        <h2>{{ __('main.vendors_movement_report') }}</h2>
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

    @php
        $typeLabels = [
            'Sales' => 'فاتورة مبيعات',
            'Sale_Payment' => 'مدفوعات مبيعات',
            'Purchases' => 'فاتورة مشتريات',
            'Purchase_Payment' => 'مدفوعات مشتريات',
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.date') }}</th>
                <th>{{ __('main.supplier') }}</th>
                <th>{{ __('main.representative') }}</th>
                <th>{{ __('main.InvoiceType') }}</th>
                <th>{{ __('main.bill_number') }}</th>
                <th>{{ __('main.Debit') }}</th>
                <th>{{ __('main.Credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $row->date }}</td>
                    <td>{{ $row->company_name }}</td>
                    <td>{{ $row->representative_name ?? '-' }}</td>
                    <td>{{ $typeLabels[$row->invoice_type] ?? $row->invoice_type }}</td>
                    <td>{{ $row->invoice_no }}</td>
                    <td>{{ number_format($row->debit ?? 0, 2) }}</td>
                    <td>{{ number_format($row->credit ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">{{ __('main.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">{{ __('main.total_balance') }}</td>
                <td>{{ number_format($totalDebit ?? 0, 2) }}</td>
                <td>{{ number_format($totalCredit ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7">{{ __('main.balance') }}</td>
                <td>{{ number_format($balance ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
