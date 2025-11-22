<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable 
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    protected $guard_name = 'admin-web';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'branch_id','subscriber_id','role_name', 'status', 'remember_token', 'created_at', 'updated_at',
        'phone_number','profile_pic'
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
