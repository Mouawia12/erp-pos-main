<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'product_id',
        'sku',
        'color',
        'size',
        'barcode',
        'price',
        'quantity',
    ];
}
