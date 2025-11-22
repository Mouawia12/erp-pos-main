<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class ProductUnit extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'product_id',
        'unit_id',
        'price',
        'conversion_factor',
        'barcode'
    ];
}
