<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\CostCenter;
use App\Models\Representative;
use App\Models\Warehouse;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuotationsReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $hasRepresentative = Schema::hasColumn('quotations', 'representative_id');
        $hasCostCenter = Schema::hasColumn('quotations', 'cost_center_id');

        $query = DB::table('quotations as q')
            ->leftJoin('companies as c', 'c.id', '=', 'q.customer_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'q.warehouse_id')
            ->leftJoin('branches as b', 'b.id', '=', 'q.branch_id');

        if ($hasRepresentative) {
            $query->leftJoin('representatives as r', 'r.id', '=', 'q.representative_id');
        }
        if ($hasCostCenter) {
            $query->leftJoin('cost_centers as cc', 'cc.id', '=', 'q.cost_center_id');
        }

        $selectColumns = [
            'q.*',
            'c.name as customer_name_display',
            'w.name as warehouse_name',
            'b.branch_name',
        ];
        $selectColumns[] = $hasRepresentative
            ? 'r.user_name as representative_name'
            : DB::raw('NULL as representative_name');
        $selectColumns[] = $hasCostCenter
            ? 'cc.name as cost_center_name'
            : DB::raw('NULL as cost_center_name');

        $query->select($selectColumns);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('quotations', 'subscriber_id')) {
                $query->where('q.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        $branchId = $this->resolveBranchId($request);
        if ($branchId) {
            $query->where('q.branch_id', $branchId);
        } elseif ($request->filled('branch_id') && (int) $request->branch_id > 0) {
            $query->where('q.branch_id', (int) $request->branch_id);
        }

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query
            ->when($request->quotation_no, fn($q, $v) => $q->where('q.quotation_no', 'like', '%' . $v . '%'))
            ->when($request->customer_id, fn($q, $v) => $q->where('q.customer_id', $v))
            ->when($request->representative_id && $hasRepresentative, fn($q, $v) => $q->where('q.representative_id', $v))
            ->when($request->warehouse_id, fn($q, $v) => $q->where('q.warehouse_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('q.status', $v))
            ->when($request->cost_center_id && $hasCostCenter, fn($q, $v) => $q->where('q.cost_center_id', $v))
            ->when($dateFrom, fn($q) => $q->whereDate('q.date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('q.date', '<=', $dateTo));

        $quotations = $query->orderByDesc('q.date')->get();

        $summary = [
            'total' => $quotations->sum('total'),
            'discount' => $quotations->sum('discount'),
            'tax' => $quotations->sum('tax'),
            'net' => $quotations->sum('net'),
        ];

        $payload = [
            'quotations' => $quotations,
            'summary' => $summary,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : ($request->filled('branch_id') ? Branch::find((int) $request->branch_id) : null),
            'warehouse' => $request->filled('warehouse_id') ? Warehouse::find((int) $request->warehouse_id) : null,
            'customer' => $request->filled('customer_id') ? Company::find((int) $request->customer_id) : null,
            'representative' => $request->filled('representative_id') ? Representative::find((int) $request->representative_id) : null,
            'costCenter' => $request->filled('cost_center_id') ? CostCenter::find((int) $request->cost_center_id) : null,
            'status' => $request->status ?: null,
            'quotationNo' => $request->quotation_no ?: null,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/quotations-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }

    private function resolveBranchId(Request $request): ?int
    {
        if (! empty(Auth::user()->branch_id)) {
            return (int) Auth::user()->branch_id;
        }

        if ($request->filled('branch_id') && (int) $request->branch_id > 0) {
            return (int) $request->branch_id;
        }

        return null;
    }
}
