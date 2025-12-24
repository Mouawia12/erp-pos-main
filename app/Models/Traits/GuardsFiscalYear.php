<?php

namespace App\Models\Traits;

use App\Services\FiscalYearService;
use Illuminate\Validation\ValidationException;

trait GuardsFiscalYear
{
    protected static function bootGuardsFiscalYear(): void
    {
        static::saving(function ($model) {
            $model->assertFiscalYearOpen();
        });

        static::deleting(function ($model) {
            $model->assertFiscalYearOpen();
        });
    }

    protected function assertFiscalYearOpen(): void
    {
        $dateField = property_exists($this, 'fiscalDateField') ? $this->fiscalDateField : 'date';
        $dateValue = $this->getAttribute($dateField);
        if (! $dateValue && $dateField === 'created_at') {
            $dateValue = now();
        }
        if (! $dateValue) {
            return;
        }

        $subscriberId = $this->getAttribute('subscriber_id');
        $service = app(FiscalYearService::class);
        try {
            $service->assertDateOpen($dateValue, $subscriberId);
        } catch (ValidationException $exception) {
            throw $exception;
        }
    }
}
