<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_count_id',
        'product_id',
        'variant_id',
        'variant_color',
        'variant_size',
        'variant_barcode',
        'expected_qty',
        'counted_qty',
        'difference',
    ];
}
