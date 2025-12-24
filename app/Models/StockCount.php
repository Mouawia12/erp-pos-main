<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Traits\GuardsFiscalYear;

class StockCount extends Model
{
    use HasFactory, BelongsToSubscriber, GuardsFiscalYear;

    protected $fiscalDateField = 'created_at';

    protected $fillable = [
        'reference',
        'warehouse_id',
        'branch_id',
        'user_id',
        'subscriber_id',
        'status',
        'is_opening',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(StockCountItem::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
