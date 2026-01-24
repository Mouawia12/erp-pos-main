<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Models\SalonDepartment;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalonServicesReportController extends Controller
{
    public function pdf(Request $request)
    {
        $binary = config('snappy.pdf.binary');
        if (! $binary || ! is_file($binary) || ! is_executable($binary)) {
            return response()->json([
                'message' => 'wkhtmltopdf غير مثبت. يرجى ضبط WKHTMLTOPDF_BINARY.',
            ], 503);
        }

        $dateFrom = $request->date_from ?? now()->subDays(30)->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $departments = SalonDepartment::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $data = DB::table('sale_details as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->leftJoin('salon_departments as d', 'd.id', '=', 'p.salon_department_id')
            ->select(
                'p.id',
                'p.name',
                'd.name as department_name',
                DB::raw('SUM(sd.quantity) as quantity'),
                DB::raw('SUM(sd.total) as total'),
                DB::raw('SUM(sd.tax) as tax'),
                DB::raw('SUM(sd.tax_excise) as tax_excise')
            )
            ->whereNotNull('p.salon_department_id')
            ->whereBetween('s.date', [$dateFrom, $dateTo])
            ->when($request->department_id, fn($q,$v) => $q->where('p.salon_department_id', $v))
            ->when(Auth::user()->branch_id ?? null, fn($q,$v) => $q->where('s.branch_id', $v))
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('s.subscriber_id', $v))
            ->groupBy('p.id', 'p.name', 'd.name')
            ->orderBy('quantity', 'desc')
            ->get();

        $totals = [
            'quantity' => $data->sum('quantity'),
            'total' => $data->sum('total'),
            'tax' => $data->sum(fn($row) => (float) ($row->tax ?? 0) + (float) ($row->tax_excise ?? 0)),
            'net' => $data->sum(fn($row) => (float) ($row->total ?? 0) + (float) ($row->tax ?? 0) + (float) ($row->tax_excise ?? 0)),
        ];

        $payload = [
            'departments' => $departments,
            'data' => $data,
            'totals' => $totals,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'departmentSelected' => $request->department_id,
            'companyInfo' => CompanyInfo::first(),
            'generatedAt' => now()->format('Y-m-d H:i'),
        ];

        $pdf = SnappyPdf::loadView('reports/salon-services-report', $payload)
            ->setOption('encoding', 'utf-8')
            ->setOption('enable-local-file-access', true)
            ->setOption('allow', [public_path('fonts')])
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
