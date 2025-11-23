<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'variant_id',
        'variant_color',
        'variant_size',
        'variant_barcode',
        'quantity',
        'price_unit',
        'tax',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
