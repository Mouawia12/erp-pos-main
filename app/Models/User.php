<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable 
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable, BelongsToSubscriber;
    protected $table = 'users';
    protected $guard_name = 'admin-web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'branch_id','subscriber_id','role_name', 'status','default_invoice_type', 'remember_token', 'created_at', 'updated_at',
        'phone_number','profile_pic','preferred_locale'
    ];

    protected $hidden = [
        'password', 'remember_token' 
    ];
    public function branch(){
        return $this->belongsTo('\App\Models\Branch','branch_id','id')
            ->withDefault(function ($branch) {
                $branch->branch_name = '';
                $branch->branch_name_ar = '';
            });
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
   
	 /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
 
}
