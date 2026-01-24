<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Purchase;
use App\Models\Sales;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class VendorStatusReportPdfController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $status = $request->input('status', 'active');
        $fromDate = $this->normalizeDate($request->input('from_date'));
        $toDate = $this->normalizeDate($request->input('to_date'));
        $invoiceNo = $request->input('invoice_no');
        $amountMin = $request->input('amount_min');
        $amountMax = $request->input('amount_max');

        $transactions = $this->queryTransactions(4, $fromDate, $toDate, $invoiceNo, $amountMin, $amountMax);
        $companyIds = $transactions->pluck('company_id')->filter()->unique()->values();

        $companiesQuery = Company::query()->where('group_id', 4);
        if ($status === 'active') {
            $companiesQuery->whereIn('id', $companyIds);
        } elseif ($status === 'inactive') {
            $companiesQuery->whereNotIn('id', $companyIds)->where('stop_sale', 0);
        } else {
            $companiesQuery->where('stop_sale', 1);
        }

        $companies = $companiesQuery->orderBy('name')->get();
        $details = $transactions->whereIn('company_id', $companies->pluck('id'))->values();
        $lastTransactions = $this->lastTransactions(4, $companies->pluck('id')->all());

        $period = $this->periodLabel($fromDate, $toDate);

        $payload = [
            'status' => $status,
            'companies' => $companies,
            'details' => $details,
            'lastTransactions' => $lastTransactions,
            'period' => $period,
            'invoiceNo' => $invoiceNo,
            'amountMin' => $amountMin,
            'amountMax' => $amountMax,
            'companyInfo' => CompanyInfo::first(),
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/vendor-status-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function queryTransactions(
        int $groupId,
        ?string $fromDate,
        ?string $toDate,
        ?string $invoiceNo,
        ?string $amountMin,
        ?string $amountMax
    ) {
        $query = $groupId === 3
            ? Sales::query()->where('sale_id', 0)
            : Purchase::query()->where('returned_bill_id', 0);

        $subscriberId = Auth::user()->subscriber_id ?? null;
        if ($subscriberId && Schema::hasColumn($query->getModel()->getTable(), 'subscriber_id')) {
            $query->where('subscriber_id', $subscriberId);
        }

        if ($fromDate) {
            $query->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('date', '<=', $toDate);
        }
        if ($invoiceNo) {
            $query->where('invoice_no', 'like', '%' . $invoiceNo . '%');
        }
        if ($amountMin !== null && $amountMin !== '') {
            $query->where('net', '>=', (float) $amountMin);
        }
        if ($amountMax !== null && $amountMax !== '') {
            $query->where('net', '<=', (float) $amountMax);
        }

        $dateColumn = $query->getModel()->getTable() . '.date';
        return $query
            ->select('id', 'invoice_no', 'date', 'net', 'total', 'customer_id')
            ->orderByDesc($dateColumn)
            ->get()
            ->map(function ($row) {
                $row->company_id = $row->customer_id;
                return $row;
            });
    }

    private function lastTransactions(int $groupId, array $companyIds)
    {
        if (empty($companyIds)) {
            return collect();
        }

        $query = $groupId === 3
            ? Sales::query()->where('sale_id', 0)
            : Purchase::query()->where('returned_bill_id', 0);

        $subscriberId = Auth::user()->subscriber_id ?? null;
        if ($subscriberId && Schema::hasColumn($query->getModel()->getTable(), 'subscriber_id')) {
            $query->where('subscriber_id', $subscriberId);
        }

        $rows = $query
            ->whereIn('customer_id', $companyIds)
            ->orderByDesc('date')
            ->get(['customer_id', 'invoice_no', 'date', 'net', 'total']);

        return $rows->groupBy('customer_id')->map(function ($items) {
            return $items->first();
        });
    }

    private function normalizeDate(?string $value): ?string
    {
        if (! $value || $value === '0') {
            return null;
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function periodLabel(?string $fromDate, ?string $toDate): string
    {
        $label = 'الفترة :';
        $label .= $fromDate ?: 'من البداية';
        $label .= ' -- ';
        $label .= $toDate ?: 'حتى اليوم';
        return $label;
    }
}
