<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosReservation extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'reservation_time',
        'guests',
        'status',
        'pos_section_id',
        'branch_id',
        'subscriber_id',
        'sale_id',
        'session_location',
        'session_type',
        'notes',
        'created_by',
    ];
}
