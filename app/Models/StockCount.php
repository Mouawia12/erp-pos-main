<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class StockCount extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'reference',
        'warehouse_id',
        'branch_id',
        'user_id',
        'subscriber_id',
        'status',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(StockCountItem::class);
    }
}
