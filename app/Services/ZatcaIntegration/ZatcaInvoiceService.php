<?php

namespace App\Services\ZatcaIntegration;

use App\Services\Zatca\Invoice\AdditionalDocumentReference;
use App\Services\Zatca\Invoice\AllowanceCharge;
use App\Services\Zatca\Invoice\Client;
use App\Services\Zatca\Invoice\Delivery;
use App\Services\Zatca\Invoice\InvoiceGenerator;
use App\Services\Zatca\Invoice\InvoiceLine;
use App\Services\Zatca\Invoice\LegalMonetaryTotal;
use App\Services\Zatca\Invoice\LineTaxCategory;
use App\Services\Zatca\Invoice\PaymentType;
use App\Services\Zatca\Invoice\PIH;
use App\Services\Zatca\Invoice\Supplier;
use App\Services\Zatca\Invoice\TaxesTotal;
use App\Services\Zatca\Invoice\TaxSubtotal;
use App\Models\BranchZatcaSetting;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SystemSettings;
use App\Models\ZatcaDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ZatcaInvoiceService
{
    public function __construct(private readonly ZatcaDocumentService $documentService)
    {
    }

    public function sendForSale(Sales $sale, ?ZatcaDocument $document = null): ZatcaDocument
    {
        if (! config('zatca.enabled')) {
            throw new RuntimeException('ZATCA integration is disabled.');
        }

        $sale->loadMissing(['customer', 'branch.zatcaSetting', 'details']);
        $document ??= $this->documentService->initDocumentForSale($sale);

        $setting = $this->resolveBranchSetting($sale);

        try {
            $generator = $this->buildInvoice($sale, $document, $setting);
            $useProduction = $this->shouldSubmitToProduction($setting);
            $certificate = $setting->getCertificateBundle($useProduction);
            if (! $certificate && $useProduction) {
                $useProduction = false;
                $certificate = $setting->getCertificateBundle(false);
            }
            if (! $certificate) {
                $branchName = $sale->branch?->branch_name ?? '#'.$sale->branch_id;
                throw new RuntimeException('ZATCA certificate credentials are missing for branch '.$branchName.'.');
            }

            $generator
                ->setCertificateEncoded($certificate['encoded'])
                ->setPrivateKeyEncoded($certificate['private_key'])
                ->setCertificateSecret($certificate['secret']);

            $draft = $generator->generateDocument();
            $this->documentService->storeSignedPayload($document, $draft);

            if ($this->isLocalSimulation($setting)) {
                $payload = array_merge($draft, [
                    'success' => true,
                    'response' => [
                        'simulation' => true,
                        'message' => 'Local simulation mode - document not submitted to ZATCA.',
                    ],
                ]);
            } else {
                $payload = $generator->sendDocument(
                    $draft['hash'],
                    (string) $document->uuid,
                    $draft['invoice'],
                    $useProduction
                );
                $payload = array_merge($draft, $payload);
            }
        } catch (\Throwable $exception) {
            Log::error('zatca_invoice_request_failed', [
                'sale_id' => $sale->id,
                'message' => $exception->getMessage(),
            ]);
            $this->documentService->markFailed($document, $exception->getMessage());
            throw $exception;
        }

        if (! ($payload['success'] ?? false)) {
            $message = data_get($payload, 'response.validationResults.status')
                ?? data_get($payload, 'response.message')
                ?? 'Invoice rejected by ZATCA.';
            $this->documentService->markFailed($document, $message, $payload['response'] ?? null);
            throw new RuntimeException($message);
        }

        return $this->documentService->markSuccess($document, $payload);
    }

    protected function resolveBranchSetting(Sales $sale): BranchZatcaSetting
    {
        $branch = $sale->branch;
        if (! $branch) {
            throw new RuntimeException('Sales invoice is missing the branch information required for ZATCA.');
        }

        $setting = $branch->zatcaSetting;
        if (! $setting) {
            throw new RuntimeException('Branch "'.($branch->branch_name ?? '#'.$branch->id).'" has not completed ZATCA onboarding yet.');
        }

        return $setting;
    }

    protected function shouldSubmitToProduction(?BranchZatcaSetting $setting): bool
    {
        if ($setting) {
            if ($setting->is_simulation) {
                return false;
            }

            return $setting->zatca_stage !== 'developer-portal';
        }

        $env = config('zatca.env', 'developer-portal');
        if ($env !== 'developer-portal') {
            return true;
        }

        return (bool) config('zatca.auto_submit_production', false);
    }

    protected function isLocalSimulation(?BranchZatcaSetting $setting = null): bool
    {
        if ($setting && $setting->is_simulation) {
            return true;
        }

        return (bool) config('zatca.local_simulation', false);
    }

    protected function buildInvoice(Sales $sale, ZatcaDocument $document, BranchZatcaSetting $setting): InvoiceGenerator
    {
        $config = config('zatca');
        $currency = $this->resolveCurrency($sale);
        $saleDate = Carbon::parse($sale->date ?? now());

        [$invoiceType, $documentType] = $this->resolveInvoiceType($sale->invoice_type);

        $generator = (new InvoiceGenerator())
            ->setZatcaEnv($setting->zatca_stage ?? $config['env'])
            ->setZatcaLang($config['language'] ?? 'en')
            ->setInvoiceNumber($sale->invoice_no)
            ->setInvoiceUuid($document->uuid)
            ->setInvoiceIssueDate($saleDate->format('Y-m-d'))
            ->setInvoiceIssueTime($saleDate->format('H:i:s'))
            ->setInvoiceType($invoiceType, $documentType)
            ->setInvoiceCurrencyCode($currency)
            ->setInvoiceTaxCurrencyCode($currency)
            ->setInvoiceAdditionalDocumentReference(
                (new AdditionalDocumentReference())->setInvoiceID((string) $document->icv)
            )
            ->setInvoicePIH((new PIH())->setPIH($document->pih))
            ->setInvoiceSupplier($this->buildSupplier($sale))
            ->setInvoiceDelivery((new Delivery())->setDeliveryDateTime($saleDate->format('Y-m-d')))
            ->setInvoicePaymentType(
                (new PaymentType())->setPaymentType($config['payment_means_code'] ?? '10')
            )
            ->setInvoiceLegalMonetaryTotal($this->buildMonetaryTotals($sale, $currency))
            ->setInvoiceTaxesTotal($this->buildTaxTotals($sale, $currency));

        if ($client = $this->buildClient($sale)) {
            $generator->setInvoiceClient($client);
        }

        $taxSubTotals = $this->buildTaxSubTotals($sale, $currency);
        $generator->setInvoiceTaxSubTotal(...$taxSubTotals);

        $allowanceCharges = $this->buildAllowanceCharge($sale, $currency);
        $generator->setInvoiceAllowanceCharges(...$allowanceCharges);

        $invoiceLines = $this->buildInvoiceLines($sale, $currency);
        $generator->setInvoiceLines(...$invoiceLines);

        return $generator;
    }

    protected function buildSupplier(Sales $sale): Supplier
    {
        $config = config('zatca');
        $supplier = $config['supplier'] ?? [];
        $settings = SystemSettings::query()
            ->when($sale->subscriber_id, fn ($q) => $q->where('subscriber_id', $sale->subscriber_id))
            ->first();

        $supplierName = $supplier['name']
            ?? $settings?->company_name
            ?? ($sale->branch?->branch_name ?? config('app.name'));

        $branch = $sale->branch;
        $supplierStreet = $supplier['street'] ?? $branch?->national_address_street ?? $branch?->branch_address ?? 'N/A';
        $supplierBuilding = $supplier['building_number'] ?? $branch?->national_address_building_no ?? '0000';
        $supplierPlot = $supplier['plot'] ?? $branch?->national_address_additional_no ?? '0000';
        $supplierSubdivision = $supplier['subdivision'] ?? $branch?->national_address_district ?? $branch?->branch_name ?? 'N/A';
        $supplierCity = $supplier['city'] ?? $branch?->national_address_city ?? $branch?->branch_name ?? 'Riyadh';
        $supplierPostal = $supplier['postal_code'] ?? $branch?->national_address_postal_code ?? '00000';
        $supplierCountry = $supplier['country'] ?? $branch?->national_address_country ?? 'SA';

        return (new Supplier())
            ->setCrn($supplier['cr_number'] ?? $sale->branch?->cr_number ?? 'N/A')
            ->setStreetName($supplierStreet)
            ->setBuildingNumber($supplierBuilding)
            ->setPlotIdentification($supplierPlot)
            ->setSubDivisionName($supplierSubdivision)
            ->setCityName($supplierCity)
            ->setPostalNumber($supplierPostal)
            ->setCountryName($supplierCountry)
            ->setVatNumber($supplier['vat_number'] ?? $settings?->tax_number ?? $config['vat_number'])
            ->setVatName($supplierName);
    }

    protected function buildClient(Sales $sale): ?Client
    {
        $customer = $sale->customer;
        if (! $customer) {
            return null;
        }

        $client = (new Client())
            ->setClientName($customer->name ?? ($sale->customer_name ?? 'Walk-in Customer'))
            ->setCountryName($customer->country ?? 'SA');

        $vat = $customer->tax_number ?? $customer->vat_no;
        if ($vat) {
            $client->setClientIdentification(config('zatca.client_identification', 'TIN'))
                ->setClientIdentificationValue($vat)
                ->setStreetName($customer->address ?? 'street')
                ->setBuildingNumber($customer->building_number ?? '0000')
                ->setPlotIdentification($customer->plot_identification ?? '0000')
                ->setSubDivisionName($customer->state ?? $customer->city ?? 'district')
                ->setCityName($customer->city ?? 'city')
                ->setPostalNumber($customer->postal_code ?? '00000');
        } else {
            $client->setClientIdentification('OTH');
        }

        return $client;
    }

    protected function buildMonetaryTotals(Sales $sale, string $currency): LegalMonetaryTotal
    {
        $lineExtension = $sale->details->sum(fn ($detail) => (float) $detail->total);
        $discount = (float) ($sale->discount ?? 0);
        $additional = (float) ($sale->additional_service ?? 0);
        $taxExclusive = max($lineExtension - $discount + $additional, 0);
        $taxInclusive = $taxExclusive + (float) $sale->tax + (float) $sale->tax_excise;

        return (new LegalMonetaryTotal())
            ->setTotalCurrency($currency)
            ->setLineExtensionAmount($lineExtension)
            ->setTaxExclusiveAmount($taxExclusive)
            ->setTaxInclusiveAmount($taxInclusive)
            ->setAllowanceTotalAmount($discount)
            ->setPrepaidAmount((float) ($sale->paid ?? 0))
            ->setPayableAmount((float) $sale->net);
    }

    protected function buildTaxTotals(Sales $sale, string $currency): TaxesTotal
    {
        return (new TaxesTotal())
            ->setTaxCurrencyCode($currency)
            ->setTaxTotal((float) $sale->tax + (float) $sale->tax_excise);
    }

    protected function buildTaxSubTotals(Sales $sale, string $currency): array
    {
        $taxable = max($sale->details->sum(fn ($detail) => (float) $detail->total) - (float) ($sale->discount ?? 0), 0);
        $taxAmount = (float) $sale->tax;
        $percentage = $taxable > 0 ? round(($taxAmount / $taxable) * 100, 2) : 0;

        return [
            (new TaxSubtotal())
                ->setTaxCurrencyCode($currency)
                ->setTaxableAmount($taxable)
                ->setTaxAmount($taxAmount)
                ->setTaxCategory('S')
                ->setTaxPercentage($percentage)
                ->getElement(),
        ];
    }

    protected function buildAllowanceCharge(Sales $sale, string $currency): array
    {
        $discount = (float) ($sale->discount ?? 0);
        if ($discount <= 0) {
            return [];
        }

        return [
            (new AllowanceCharge())
                ->setAllowanceChargeCurrency($currency)
                ->setAllowanceChargeIndex('1')
                ->setAllowanceChargeAmount($discount)
                ->setAllowanceChargeTaxCategory('S')
                ->setAllowanceChargeTaxPercentage($this->resolveVatPercentage($sale))
                ->getElement(),
        ];
    }

    protected function buildInvoiceLines(Sales $sale, string $currency): array
    {
        $details = $sale->details;
        if ($details->isEmpty()) {
            return [];
        }

        $productNames = [];
        $productIds = $details->pluck('product_id')->filter()->unique();
        if ($productIds->isNotEmpty()) {
            $productNames = Product::whereIn('id', $productIds)->pluck('name', 'id')->toArray();
        }

        $lines = [];
        $lineId = 1;
        foreach ($details as $detail) {
            $taxable = (float) $detail->total;
            $taxAmount = (float) $detail->tax + (float) $detail->tax_excise;
            $percentage = $taxable > 0 ? round(($taxAmount / $taxable) * 100, 2) : 0;
            $lineTaxCategory = (new LineTaxCategory())
                ->setTaxCategory('S')
                ->setTaxPercentage($percentage)
                ->getElement();
            $lineNet = $detail->price_with_tax ?? ($taxable + $taxAmount);

            $lines[] = (new InvoiceLine())
                ->setLineID((string) $lineId++)
                ->setLineName($productNames[$detail->product_id] ?? $detail->product_code ?? 'Item')
                ->setLineCurrency($currency)
                ->setLinePrice((float) $detail->price_unit)
                ->setLineQuantity((float) $detail->quantity)
                ->setLineSubTotal($taxable)
                ->setLineTaxTotal($taxAmount)
                ->setLineNetTotal($lineNet)
                ->setLineTaxCategories($lineTaxCategory)
                ->setLineDiscountReason(__('main.discount') ?? 'Discount')
                ->setLineDiscountAmount((float) ($detail->discount ?? 0))
                ->getElement();
        }

        return $lines;
    }

    protected function resolveVatPercentage(Sales $sale): float
    {
        $taxable = max($sale->details->sum(fn ($detail) => (float) $detail->total) - (float) ($sale->discount ?? 0), 0);
        $taxAmount = (float) $sale->tax;
        return $taxable > 0 ? round(($taxAmount / $taxable) * 100, 2) : 0.0;
    }

    protected function resolveInvoiceType(?string $type): array
    {
        $map = [
            'simplified_tax_invoice' => '0200000',
            'tax_invoice' => '0100000',
            'non_tax_invoice' => '0100000',
        ];

        $invoiceType = $map[$type] ?? config('zatca.default_invoice_type_code', '0200000');
        $documentType = config('zatca.default_document_type', '388');

        return [$invoiceType, $documentType];
    }

    protected function resolveCurrency(Sales $sale): string
    {
        $settings = SystemSettings::query()
            ->when($sale->subscriber_id, fn ($q) => $q->where('subscriber_id', $sale->subscriber_id))
            ->with('currency')
            ->first();

        return $settings?->currency?->code ?? 'SAR';
    }
}
