<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseMovement extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $table = 'warehouse_movements';
    protected $fillable = [
        'warehouse_id', 'product_id', 'debit', 'credit'
        , 'invoice_type', 'invoice_id', 'invoice_no', 'user_id'
    ];
}
