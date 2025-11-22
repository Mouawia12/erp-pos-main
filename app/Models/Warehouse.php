<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Warehouse extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'tax_number',
        'commercial_registration',
        'serial_prefix',
        'branch_id',
        'user_id',
        'status',
        'subscriber_id'
    ];

    public function branch(){
        return $this->belongsTo('\App\Models\Branch','branch_id','id');
    }
}
