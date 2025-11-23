<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class WarehouseTransfer extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'reason',
        'reject_reason',
        'branch_id',
        'user_id',
        'approved_by',
        'subscriber_id',
    ];

    public function items()
    {
        return $this->hasMany(WarehouseTransferItem::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
