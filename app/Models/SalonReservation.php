<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalonReservation extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'reservation_no',
        'customer_id',
        'salon_department_id',
        'assigned_user_id',
        'warehouse_id',
        'reservation_time',
        'location_text',
        'location_url',
        'status',
        'sale_id',
        'notes',
        'subscriber_id',
        'branch_id',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Company::class, 'customer_id');
    }

    public function department()
    {
        return $this->belongsTo(SalonDepartment::class, 'salon_department_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function items()
    {
        return $this->hasMany(SalonReservationItem::class, 'salon_reservation_id');
    }
}
