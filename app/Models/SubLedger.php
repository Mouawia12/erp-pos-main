<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubLedger extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'company_id',
        'control_account_id',
        'type',
        'branch_id',
        'subscriber_id',
    ];
}
