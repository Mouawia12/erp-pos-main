<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name','cr_number','tax_number','responsible_person','contact_email','contact_phone',
        'address','system_url','users_limit','subscription_start','subscription_end','status','notes','created_by',
        'login_email','login_password','login_password_plain','user_id'
    ];

    protected $casts = [
        'subscription_start' => 'date',
        'subscription_end' => 'date',
    ];

    public function renewals()
    {
        return $this->hasMany(SubscriberRenewal::class);
    }

    public function documents()
    {
        return $this->hasMany(SubscriberDocument::class);
    }
}
