<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxExcise extends Model
{
    use HasFactory;
    protected $table = "tax_excise";

    protected $fillable = [
        'name', 'rate', 'status'
    ];
}
