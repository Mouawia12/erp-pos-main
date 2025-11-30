<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDoc extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = ['notes','date'];
}
