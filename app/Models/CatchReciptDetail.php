<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchReciptDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'catch_recipt_id',
        'account_id',
        'amount',
        'notes',
    ];
}
