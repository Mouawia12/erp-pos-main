<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles; 

class Admin extends Authenticatable 
{
    use HasRoles;

    protected $table = 'admins';

    protected $fillable = [
        'name', 'email', 'password', 'branch_id','role_name', 'status', 'remember_token', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'password', 'remember_token' 
    ];
    public function branch(){
        return $this->belongsTo('\App\Models\Branch','branch_id','id');
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    

}
