<?php

namespace App\Jobs;

use App\Models\Sales;
use App\Services\ZatcaIntegration\ZatcaDocumentService;
use App\Services\ZatcaIntegration\ZatcaInvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendZatcaInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $saleId)
    {
        $this->afterCommit = true;
        $this->onQueue('default');
    }

    public function handle(ZatcaInvoiceService $invoiceService, ZatcaDocumentService $documentService): void
    {
        if (! config('zatca.enabled')) {
            return;
        }

        $sale = Sales::with(['customer', 'branch.zatcaSetting', 'details'])->find($this->saleId);
        if (! $sale) {
            Log::warning('zatca_invoice_missing_sale', ['sale_id' => $this->saleId]);
            return;
        }

        $document = $documentService->initDocumentForSale($sale);

        try {
            $invoiceService->sendForSale($sale, $document);
        } catch (\Throwable $exception) {
            Log::error('zatca_invoice_job_failed', [
                'sale_id' => $this->saleId,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }
}
