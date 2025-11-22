<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Journal extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = ['baseon_text'];

}
