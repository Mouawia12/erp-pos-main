<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxExcise extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $table = "tax_excise";

    protected $fillable = [
        'name', 'rate', 'status'
    ];
}
