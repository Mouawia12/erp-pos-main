<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransferItem extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'warehouse_transfer_id',
        'product_id',
        'variant_id',
        'variant_color',
        'variant_size',
        'variant_barcode',
        'quantity',
    ];

    public function transfer()
    {
        return $this->belongsTo(WarehouseTransfer::class,'warehouse_transfer_id');
    }
}
