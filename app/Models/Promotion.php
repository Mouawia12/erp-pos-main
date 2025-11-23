<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Promotion extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'branch_id',
        'subscriber_id',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PromotionItem::class);
    }
}
