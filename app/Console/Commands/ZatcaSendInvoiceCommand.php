<?php

namespace App\Console\Commands;

use App\Models\Sales;
use App\Services\ZatcaIntegration\ZatcaDocumentService;
use App\Services\ZatcaIntegration\ZatcaInvoiceService;
use Illuminate\Console\Command;

class ZatcaSendInvoiceCommand extends Command
{
    protected $signature = 'zatca:send
        {invoice : Sales ID or invoice number}
        {--by-number : Treat the invoice argument as the invoice_no column}';

    protected $description = 'Send a specific sales invoice to ZATCA.';

    public function handle(ZatcaInvoiceService $invoiceService, ZatcaDocumentService $documentService): int
    {
        if (! config('zatca.enabled')) {
            $this->error('ZATCA integration is disabled. Set ZATCA_ENABLED=true first.');
            return self::FAILURE;
        }

        $identifier = $this->argument('invoice');
        $sale = $this->option('by-number')
            ? Sales::where('invoice_no', $identifier)->first()
            : Sales::find($identifier);

        if (! $sale) {
            $this->error('Could not find a sales invoice matching "'.$identifier.'".');
            return self::FAILURE;
        }

        try {
            $document = $documentService->initDocumentForSale($sale);
            $invoiceService->sendForSale($sale, $document);
        } catch (\Throwable $exception) {
            $this->error('Failed to send invoice: '.$exception->getMessage());
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Invoice %s (ID %d) sent successfully. UUID: %s',
            $sale->invoice_no,
            $sale->id,
            $document->uuid
        ));

        return self::SUCCESS;
    }
}
