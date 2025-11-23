<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Quotation extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'date',
        'quotation_no',
        'customer_id',
        'warehouse_id',
        'note',
        'total',
        'discount',
        'tax',
        'net',
        'status',
        'branch_id',
        'user_id',
        'subscriber_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Company::class, 'customer_id');
    }
}
