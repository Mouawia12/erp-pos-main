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
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_tax_number',
        'warehouse_id',
        'representative_id',
        'invoice_type',
        'payment_method',
        'cost_center',
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
