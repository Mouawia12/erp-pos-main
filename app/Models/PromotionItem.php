<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'product_id',
        'variant_id',
        'variant_color',
        'variant_size',
        'variant_barcode',
        'min_qty',
        'discount_value',
        'discount_type',
        'special_barcode',
        'max_qty',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
