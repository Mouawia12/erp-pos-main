<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class PurchaseDetails extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'purchase_id',
        'product_code',
        'product_id',
        'quantity',
        'cost_without_tax',
        'cost_with_tax',
        'warehouse_id',
        'unit_id',
        'batch_no',
        'production_date',
        'expiry_date',
        'unit_factor',
        'tax',
        'total',
        'net',
        'note',
        'returned_qnt','subscriber_id'
    ];
}
