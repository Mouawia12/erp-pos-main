<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Journal extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'branch_id',
        'date',
        'basedon_no',
        'basedon_id',
        'baseon_text',
        'total_credit',
        'total_debit',
        'notes',
    ];

}
