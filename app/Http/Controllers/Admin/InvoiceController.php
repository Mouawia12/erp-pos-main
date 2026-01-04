<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\Company;
use App\Models\CompanyInfo;
use App\Models\Payment;
use App\Models\PosSettings;
use App\Models\Sales;
use App\Models\Subscriber;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Salla\ZATCA\GenerateQrCode as ZatcaQr;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;

class InvoiceController extends Controller
{
    public function print(Sales $invoice)
    {
        $payload = $this->getPrintPayload((int) $invoice->id);

        if (! $payload) {
            abort(404);
        }

        $payload['logoDataUri'] = $this->resolveLogoDataUri($payload['company'] ?? null);
        $payload['fontDataUri'] = $this->resolveFontDataUri();

        return view('invoices.print', $payload)->render();
    }

    public function pdf(Sales $invoice)
    {
        $payload = $this->getPrintPayload((int) $invoice->id);

        if (! $payload) {
            abort(404);
        }

        $payload['isPdf'] = true;
        $payload['logoDataUri'] = $this->resolveLogoDataUri($payload['company'] ?? null);
        $payload['fontDataUri'] = $this->resolveFontDataUri();

        $html = view('invoices.print', $payload)->render();
        $storageDir = storage_path('app/invoices');
        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        $invoiceRef = $payload['data']->invoice_no ?? $payload['data']->id;
        $safeRef = preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $invoiceRef);
        $path = $storageDir . '/invoice-' . $safeRef . '.pdf';

        $shot = Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->setOption('args', [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-gpu',
                '--no-zygote',
                '--single-process',
                '--disable-dev-shm-usage',
            ])
            ->setOption('timeout', 120000)
            ->setOption('protocolTimeout', 120000);

        $chromePath = env('BROWSERSHOT_CHROME_PATH');
        if ($chromePath) {
            $shot->setChromePath($chromePath);
        }

        $shot->save($path);

        return response()->download($path);
    }

    private function getPrintPayload(int $id): ?array
    {
        $data = DB::table('sales')
            ->join('warehouses', 'sales.warehouse_id', '=', 'warehouses.id')
            ->join('companies', 'sales.customer_id', '=', 'companies.id')
            ->join('branches', 'sales.branch_id', '=', 'branches.id')
            ->leftJoin('pos_sections', 'sales.pos_section_id', '=', 'pos_sections.id')
            ->leftJoin('pos_shifts', 'sales.pos_shift_id', '=', 'pos_shifts.id')
            ->select(
                'sales.*',
                'warehouses.name as warehouse_name',
                'companies.name as customer_name',
                'branches.branch_name',
                'branches.branch_phone',
                'branches.branch_address',
                'branches.cr_number',
                'branches.tax_number as branch_tax_number',
                'branches.manager_name as branch_manager',
                'branches.contact_email as branch_email',
                'pos_sections.name as pos_section_name',
                'pos_sections.type as pos_section_type',
                'pos_shifts.opened_at as shift_opened_at'
            )
            ->where('sales.id', '=', $id)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('sales.subscriber_id', $sub);
            })
            ->first();

        if (! $data) {
            return null;
        }

        $details = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('sale_details.*', 'products.code', 'products.name', 'products.tax as taxRate', 'products.tax_excise as taxExciseRate')
            ->where('sale_details.sale_id', '=', $id)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('sale_details.subscriber_id', $sub);
            })
            ->get();

        $payments = Payment::with('user')
            ->where('sale_id', $id)
            ->where('sale_id', '<>', null)
            ->get();

        $vendor = Company::find($data->customer_id);
        $cashier = Cashier::first();
        $company = CompanyInfo::first();
        $settings = SystemSettings::where('subscriber_id', $data->subscriber_id)->first() ?? SystemSettings::first();
        $posSettings = PosSettings::query()
            ->when($data->subscriber_id, fn ($q) => $q->where('subscriber_id', $data->subscriber_id))
            ->first();
        $subscriber = $data->subscriber_id ? Subscriber::find($data->subscriber_id) : null;
        $trialMode = $subscriber?->isTrialActive() ?? false;
        $resolvedTaxNumber = $this->resolveTaxNumber($data, $subscriber, $company, $settings);
        $qrCodeImage = $this->buildInvoiceQr($data, $company, $resolvedTaxNumber, $trialMode);

        return compact('data', 'details', 'vendor', 'cashier', 'payments', 'company', 'settings', 'posSettings', 'subscriber', 'trialMode', 'resolvedTaxNumber', 'qrCodeImage');
    }

    private function resolveTaxNumber($sale, ?Subscriber $subscriber, ?CompanyInfo $company, ?SystemSettings $settings): ?string
    {
        return $sale->branch_tax_number
            ?? optional($subscriber)->tax_number
            ?? optional($settings)->tax_number
            ?? optional($company)->taxNumber;
    }

    private function buildInvoiceQr($sale, ?CompanyInfo $company, ?string $taxNumber, bool $trialMode): ?string
    {
        try {
            if ($trialMode) {
                return ZatcaQr::fromArray([
                    new Seller('TRIAL VERSION'),
                    new TaxNumber('000000000000000'),
                    new InvoiceDate(now()->toIso8601String()),
                    new InvoiceTotalAmount(0),
                    new InvoiceTaxAmount(0),
                ])->render();
            }

            $sellerName = $sale->branch_name
                ?? optional($company)->name_ar
                ?? optional($company)->name_en
                ?? 'Company';

            $taxValue = $taxNumber ?: '000000000000000';

            return ZatcaQr::fromArray([
                new Seller($sellerName),
                new TaxNumber($taxValue),
                new InvoiceDate($sale->date),
                new InvoiceTotalAmount($sale->net),
                new InvoiceTaxAmount($sale->tax),
            ])->render();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveLogoDataUri(?CompanyInfo $company): ?string
    {
        $logoPath = null;
        if ($company && !empty($company->logo)) {
            $logoPath = public_path('uploads/profiles/' . $company->logo);
        }
        if (! $logoPath || ! file_exists($logoPath)) {
            $logoPath = public_path('assets/img/logo.png');
        }
        if (! file_exists($logoPath)) {
            return null;
        }

        $ext = pathinfo($logoPath, PATHINFO_EXTENSION) ?: 'png';
        $mime = $ext === 'jpg' ? 'image/jpeg' : 'image/' . $ext;
        $data = base64_encode(file_get_contents($logoPath));

        return 'data:' . $mime . ';base64,' . $data;
    }

    private function resolveFontDataUri(): ?string
    {
        $fontPath = public_path('fonts/Almarai.ttf');
        if (! file_exists($fontPath)) {
            return null;
        }

        $data = base64_encode(file_get_contents($fontPath));

        return 'data:font/ttf;base64,' . $data;
    }
}
