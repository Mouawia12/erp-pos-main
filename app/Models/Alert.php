<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
