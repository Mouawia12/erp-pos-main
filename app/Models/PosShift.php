<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'user_id',
        'branch_id',
        'warehouse_id',
        'subscriber_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'status',
        'notes',
    ];
}
