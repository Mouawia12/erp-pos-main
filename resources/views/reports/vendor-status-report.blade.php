<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.vendor_status_report') ?? 'تقارير الموردين حسب الحالة' }}</title>
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
        <h2>{{ __('main.vendor_status_report') ?? 'تقارير الموردين حسب الحالة' }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ $period ?? '' }}</div>
            <div class="meta-cell">{{ __('main.status') ?? 'الحالة' }}:
                @if($status === 'active')
                    {{ __('main.status_active') ?? 'نشط' }}
                @elseif($status === 'inactive')
                    {{ __('main.status_inactive') ?? 'راكد' }}
                @else
                    {{ __('main.status_stopped') ?? 'موقوف' }}
                @endif
            </div>
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.invoice_no') ?? 'رقم الفاتورة' }}: {{ $invoiceNo ?: __('main.all') }}</div>
            <div class="meta-cell">{{ __('main.amount_range') ?? 'نطاق القيمة' }}:
                {{ $amountMin !== null && $amountMin !== '' ? $amountMin : '-' }} -
                {{ $amountMax !== null && $amountMax !== '' ? $amountMax : '-' }}
            </div>
        </div>
    </div>

    @php $companyMap = $companies->keyBy('id'); @endphp
    @if($status === 'active')
        <table>
            <thead>
                <tr>
                    <th>{{ __('main.supplier') }}</th>
                    <th>{{ __('main.invoice_no') }}</th>
                    <th>{{ __('main.date') }}</th>
                    <th>{{ __('main.total_balance') ?? 'القيمة' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $row)
                    @php $company = $companyMap->get($row->company_id); @endphp
                    <tr>
                        <td>{{ $company?->name ?? '-' }}</td>
                        <td>{{ $row->invoice_no }}</td>
                        <td>{{ $row->date }}</td>
                        <td>{{ number_format($row->net ?? $row->total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('main.supplier') }}</th>
                    <th>{{ __('main.status') }}</th>
                    <th>{{ __('main.invoice_no') }}</th>
                    <th>{{ __('main.date') }}</th>
                    <th>{{ __('main.total_balance') ?? 'القيمة' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    @php $last = $lastTransactions->get($company->id); @endphp
                    <tr>
                        <td>{{ $company->name }}</td>
                        <td>
                            @if($company->stop_sale)
                                {{ __('main.status_stopped') ?? 'موقوف' }}
                            @else
                                {{ __('main.status_inactive') ?? 'راكد' }}
                            @endif
                        </td>
                        <td>{{ $last?->invoice_no ?? '-' }}</td>
                        <td>{{ $last?->date ?? '-' }}</td>
                        <td>{{ number_format($last?->net ?? $last?->total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
