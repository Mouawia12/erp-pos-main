<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name','cr_number','tax_number','responsible_person','contact_email','contact_phone',
        'address','system_url','users_limit','subscription_start','subscription_end','status','notes','created_by',
        'login_email','login_password','login_password_plain','user_id','is_trial','trial_starts_at','trial_ends_at'
    ];

    protected $casts = [
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'trial_starts_at' => 'date',
        'trial_ends_at' => 'date',
        'is_trial' => 'boolean',
    ];

    public function renewals()
    {
        return $this->hasMany(SubscriberRenewal::class);
    }

    public function documents()
    {
        return $this->hasMany(SubscriberDocument::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function refreshLifecycleStatus(): void
    {
        $endDate = $this->subscription_end ?? $this->trial_ends_at;
        $today = Carbon::today();
        $newStatus = 'active';

        if ($endDate) {
            $end = Carbon::parse($endDate)->startOfDay();
            if ($end->lt($today)) {
                $newStatus = 'expired';
            } elseif ($end->diffInDays($today, false) <= 30) {
                $newStatus = 'near_expiry';
            }
        }

        if ($this->status !== $newStatus) {
            $this->forceFill(['status' => $newStatus])->saveQuietly();
        }
    }

    public function isTrialActive(): bool
    {
        if (! $this->is_trial) {
            return false;
        }

        if (! $this->trial_ends_at) {
            return true;
        }

        return ! Carbon::today()->gt(Carbon::parse($this->trial_ends_at)->startOfDay());
    }

    public function hasExpired(): bool
    {
        $this->refreshLifecycleStatus();
        return $this->status === 'expired';
    }

    public function remainingDays(): ?int
    {
        $endDate = $this->subscription_end ?? $this->trial_ends_at;
        if (! $endDate) {
            return null;
        }

        return Carbon::today()->diffInDays(Carbon::parse($endDate)->startOfDay(), false);
    }
}
