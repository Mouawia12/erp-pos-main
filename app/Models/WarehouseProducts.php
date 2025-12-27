<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class WarehouseProducts extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'cost',
        'price'
    ];
}
