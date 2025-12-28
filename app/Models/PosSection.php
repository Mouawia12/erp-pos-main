<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSection extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'name',
        'type',
        'branch_id',
        'subscriber_id',
        'is_active',
    ];
}
