<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalonDepartment extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'name',
        'description',
        'branch_id',
        'subscriber_id',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'salon_department_user')->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'salon_department_id');
    }
}
