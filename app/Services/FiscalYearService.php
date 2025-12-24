<?php

namespace App\Services;

use App\Models\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FiscalYearService
{
    public function assertDateOpen($date, ?int $subscriberId = null): void
    {
        $closedYear = $this->getClosedYearForDate($date, $subscriberId);
        if ($closedYear) {
            $message = __('main.fiscal_year_closed', [
                'start' => $closedYear->start_date,
                'end' => $closedYear->end_date,
            ]);
            throw ValidationException::withMessages(['date' => $message]);
        }
    }

    public function getClosedYearForDate($date, ?int $subscriberId = null): ?FiscalYear
    {
        if (! $date) {
            return null;
        }

        $parsedDate = Carbon::parse($date)->toDateString();
        $subscriberId = $subscriberId ?? (Auth::user()->subscriber_id ?? null);

        $query = FiscalYear::query()->where('is_closed', true);
        if ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        } else {
            $query->whereNull('subscriber_id');
        }

        return $query
            ->where('start_date', '<=', $parsedDate)
            ->where('end_date', '>=', $parsedDate)
            ->first();
    }
}
