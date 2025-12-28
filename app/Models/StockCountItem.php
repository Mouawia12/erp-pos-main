<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCountItem extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'stock_count_id',
        'product_id',
        'variant_id',
        'variant_color',
        'variant_size',
        'variant_barcode',
        'batch_no',
        'production_date',
        'expiry_date',
        'expected_qty',
        'counted_qty',
        'difference',
    ];
}
