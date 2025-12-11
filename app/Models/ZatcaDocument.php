<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Sales;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZatcaDocument extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'subscriber_id',
        'branch_id',
        'sale_id',
        'icv',
        'uuid',
        'invoice_number',
        'invoice_type',
        'previous_hash',
        'hash',
        'xml',
        'response',
        'error_message',
        'sent_to_zatca',
        'sent_to_zatca_status',
        'signing_time',
        'submitted_at',
        'qr_value',
    ];

    protected $casts = [
        'sent_to_zatca' => 'boolean',
        'signing_time' => 'datetime',
        'submitted_at' => 'datetime',
        'response' => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getPihAttribute(): string
    {
        return $this->previous_hash ?: base64_encode(hash('sha256', '0', true));
    }

    public function scopePending($query)
    {
        return $query->where('sent_to_zatca', false);
    }
}
