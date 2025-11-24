<?php

namespace App\Services;

use App\Models\Sales;
use App\Models\SystemSettings;
use App\Models\UserDocumentCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate next document number.
     * Falls back to branch-wide counter when per_user_sequence is disabled.
     */
    public function next(string $docType, int $branchId, ?string $globalPrefix = null): string
    {
        $settings = SystemSettings::first();
        $prefix = $this->buildPrefix($settings, $globalPrefix, $branchId);

        if ($settings?->per_user_sequence) {
            $userId = Auth::id() ?? 0;
            return $this->nextPerUser($userId, $docType, $branchId, $prefix);
        }

        // branch-wide fallback keeps existing behavior
        $count = Sales::where('sale_id', 0)
            ->where('branch_id', $branchId)
            ->count();
        $next = $count + 1;

        return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    protected function nextPerUser(int $userId, string $docType, int $branchId, string $prefix): string
    {
        return DB::transaction(function () use ($userId, $docType, $branchId, $prefix) {
            $counter = UserDocumentCounter::lockForUpdate()->firstOrCreate(
                ['user_id' => $userId, 'doc_type' => $docType, 'branch_id' => $branchId],
                ['next_number' => 1, 'prefix' => $prefix]
            );

            $current = $counter->next_number;
            $counter->increment('next_number');

            return $prefix . str_pad($current, 6, '0', STR_PAD_LEFT);
        });
    }

    protected function buildPrefix(?SystemSettings $settings, ?string $globalPrefix, int $branchId): string
    {
        $prefix = $globalPrefix ?? '';

        if ($settings && $settings->sales_prefix) {
            $prefix = $settings->sales_prefix;
        }

        // keep existing dash-separated style: PREFIX-BRANCH-
        return trim($prefix) !== ''
            ? $prefix . '-' . $branchId . '-'
            : $branchId . '-';
    }
}
