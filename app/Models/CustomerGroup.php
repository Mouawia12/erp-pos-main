<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'name',
        'discount_percentage',
        'sell_with_cost',
        'enable_discount'

    ];
    public function clients()
    {
        return $this->hasMany(Company::class , 'customer_group_id');
    }
}
