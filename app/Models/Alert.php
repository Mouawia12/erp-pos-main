<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Alert extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'type',
        'title',
        'message',
        'severity',
        'related_id',
        'related_type',
        'branch_id',
        'warehouse_id',
        'subscriber_id',
        'due_date',
        'read_at',
        'resolved_at',
        'meta',
    ];

    protected $casts = [
        'due_date' => 'date',
        'read_at' => 'datetime',
        'resolved_at' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at')->whereNull('resolved_at');
    }

    public function scopeForUser($query, $user = null)
    {
        $user = $user ?: Auth::user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        $table = $query->getModel()->getTable();

        if ($user->hasRole('system_owner')) {
            $query->whereNull($table . '.subscriber_id');
        } elseif (! empty($user->subscriber_id)) {
            $query->where($table . '.subscriber_id', $user->subscriber_id);
        }

        if (! empty($user->branch_id)) {
            $query->where(function ($branchQuery) use ($table, $user) {
                $branchQuery->whereNull($table . '.branch_id')
                    ->orWhere($table . '.branch_id', $user->branch_id);
            });
        }

        return $query;
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function resolve(): void
    {
        $this->update([
            'resolved_at' => now(),
            'read_at' => $this->read_at ?? now(),
        ]);
    }
}
