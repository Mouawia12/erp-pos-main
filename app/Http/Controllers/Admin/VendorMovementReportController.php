<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Representative;
use App\Models\VendorMovement;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class VendorMovementReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $groupId = 4;
        $subscriberId = Auth::user()->subscriber_id ?? null;

        $query = VendorMovement::query()
            ->join('companies', 'companies.id', '=', 'vendor_movements.vendor_id')
            ->leftJoin('representatives', 'representatives.id', '=', 'companies.representative_id_')
            ->select(
                'vendor_movements.*',
                'companies.name as company_name',
                'representatives.user_name as representative_name'
            )
            ->where('companies.group_id', $groupId);

        if ($subscriberId) {
            if (Schema::hasColumn('vendor_movements', 'subscriber_id')) {
                $query->where('vendor_movements.subscriber_id', $subscriberId);
            }
            if (Schema::hasColumn('companies', 'subscriber_id')) {
                $query->where('companies.subscriber_id', $subscriberId);
            }
        }

        $branchId = $this->resolveBranchId($request);
        if ($branchId) {
            $query->where('vendor_movements.branch_id', $branchId);
        }

        if ($request->filled('company_id') && (int) $request->company_id > 0) {
            $query->where('vendor_movements.vendor_id', (int) $request->company_id);
        }

        if ($request->filled('representative_id') && (int) $request->representative_id > 0) {
            $query->where('companies.representative_id_', (int) $request->representative_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('vendor_movements.date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('vendor_movements.date', '<=', $request->date_to);
        }

        $rows = $query->orderBy('vendor_movements.date')->get();

        $totalDebit = $rows->sum('debit');
        $totalCredit = $rows->sum('credit');
        $balance = $groupId === 3 ? ($totalDebit - $totalCredit) : ($totalCredit - $totalDebit);

        $payload = [
            'rows' => $rows,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $balance,
            'companyInfo' => CompanyInfo::first(),
            'branch' => $branchId ? Branch::find($branchId) : null,
            'vendor' => $request->filled('company_id') ? Company::find((int) $request->company_id) : null,
            'representative' => $request->filled('representative_id') ? Representative::find((int) $request->representative_id) : null,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/vendor-movement', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

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
