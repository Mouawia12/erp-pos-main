<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'code',
        'name', 
    ];
}
