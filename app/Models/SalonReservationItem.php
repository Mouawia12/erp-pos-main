<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalonReservationItem extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'salon_reservation_id',
        'product_id',
        'variant_id',
        'unit_id',
        'unit_factor',
        'quantity',
        'note',
        'subscriber_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(SalonReservation::class, 'salon_reservation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
