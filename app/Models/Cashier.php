<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'company', //
        'name', //
        'address', //
        'commercial_register',//
        'license',//
        'tax_number',//
        'email',//
        'bill_holder1',//
        'bill_holder2',//
        'phone',//
    ];
}
