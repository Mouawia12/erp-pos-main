<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('main.salon_services_report') ?? 'تقرير خدمات المشغل' }}</title>
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
        <h2>{{ __('main.salon_services_report') ?? 'تقرير خدمات المشغل' }}</h2>
        <div>{{ $companyInfo->name_ar ?? $companyInfo->name_en ?? '' }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.from_date') }}: {{ $dateFrom }}</div>
            <div class="meta-cell">{{ __('main.to_date') }}: {{ $dateTo }}</div>
            <div class="meta-cell">{{ __('main.salon_department') ?? 'قسم المشغل' }}:
                {{ $departments->firstWhere('id', $departmentSelected)?->name ?? __('main.all') }}
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-cell">{{ __('main.date') }}: {{ $generatedAt }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.name') }}</th>
                <th>{{ __('main.salon_department') ?? 'قسم المشغل' }}</th>
                <th>{{ __('main.quantity') }}</th>
                <th>{{ __('main.total') }}</th>
                <th>{{ __('main.tax_total') }}</th>
                <th>{{ __('main.net') ?? 'الصافي' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $idx => $row)
                @php $net = ($row->total ?? 0) + ($row->tax ?? 0) + ($row->tax_excise ?? 0); @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->department_name }}</td>
                    <td>{{ $row->quantity }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                    <td>{{ number_format(($row->tax ?? 0) + ($row->tax_excise ?? 0), 2) }}</td>
                    <td>{{ number_format($net, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">{{ __('main.no_data') }}</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">{{ __('main.total') }}</td>
                <td>{{ number_format($totals['quantity'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['total'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['net'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
