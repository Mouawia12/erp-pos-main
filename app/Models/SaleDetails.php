<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class SaleDetails extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'sale_id','product_code','product_id','quantity','price_unit',
        'discount','price_with_tax','warehouse_id','unit_id','tax','tax_excise',
        'total','profit','lista','subscriber_id'
    ];
}
