<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetails extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'sale_id','product_code','product_id','note','quantity','price_unit',
        'discount','price_with_tax','warehouse_id','unit_id','unit_factor','tax','tax_excise',
        'total','profit','lista','subscriber_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
