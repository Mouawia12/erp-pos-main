<?php

namespace App\Services\ZatcaIntegration;

use App\Models\Sales;
use App\Models\ZatcaDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZatcaDocumentService
{
    public function initDocumentForSale(Sales $sale): ZatcaDocument
    {
        $existing = $sale->zatcaDocuments()->latest()->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($sale) {
            $subscriberId = $sale->subscriber_id;
            $branchId = $sale->branch_id;

            $latest = ZatcaDocument::query()
                ->when($subscriberId, fn ($query) => $query->where('subscriber_id', $subscriberId))
                ->when(
                    $branchId,
                    fn ($query) => $query->where('branch_id', $branchId),
                    fn ($query) => $query->whereNull('branch_id')
                )
                ->lockForUpdate()
                ->orderByDesc('icv')
                ->first();

            $icv = ($latest?->icv ?? 0) + 1;
            $previousHash = $latest?->hash ?: base64_encode(hash('sha256', '0', true));

            return ZatcaDocument::create([
                'subscriber_id' => $subscriberId,
                'branch_id' => $branchId,
                'sale_id' => $sale->id,
                'icv' => $icv,
                'uuid' => (string) Str::uuid(),
                'invoice_number' => $sale->invoice_no,
                'invoice_type' => $sale->invoice_type ?? 'simplified_tax_invoice',
                'previous_hash' => $previousHash,
                'sent_to_zatca' => false,
            ]);
        });
    }

    public function storeSignedPayload(ZatcaDocument $document, array $draft): ZatcaDocument
    {
        $document->fill([
            'hash' => $draft['hash'] ?? $document->hash,
            'xml' => $draft['invoice'] ?? $document->xml,
            'qr_value' => $draft['qr_value'] ?? $document->qr_value,
            'error_message' => null,
            'signing_time' => isset($draft['signing_time'])
                ? Carbon::parse($draft['signing_time'])
                : $document->signing_time,
        ])->save();

        return $document->fresh();
    }

    public function markSuccess(ZatcaDocument $document, array $payload): ZatcaDocument
    {
        $document->fill([
            'hash' => $payload['hash'] ?? $document->hash,
            'xml' => $payload['xml'] ?? $document->xml,
            'response' => isset($payload['response'])
                ? json_encode($payload['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : $document->response,
            'qr_value' => $payload['qr_value'] ?? $document->qr_value,
            'sent_to_zatca' => true,
            'sent_to_zatca_status' => data_get($payload, 'response.validationResults.status')
                ?? data_get($payload, 'response.status')
                ?? 'accepted',
            'signing_time' => isset($payload['signing_time'])
                ? Carbon::parse($payload['signing_time'])
                : $document->signing_time,
            'submitted_at' => now(),
            'error_message' => null,
        ])->save();

        return $document->fresh();
    }

    public function markFailed(ZatcaDocument $document, string $message, mixed $response = null): ZatcaDocument
    {
        $document->fill([
            'sent_to_zatca' => false,
            'sent_to_zatca_status' => 'failed',
            'error_message' => $message,
            'response' => $response
                ? json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : $document->response,
            'submitted_at' => now(),
        ])->save();

        return $document->fresh();
    }
}
