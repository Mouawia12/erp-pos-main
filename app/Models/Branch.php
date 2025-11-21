<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id)
 * @method static findOrFail($admin_id)
 */
class Branch extends Model
{
    protected $table = "branches";

    protected $fillable = [
        'branch_name','cr_number','tax_number','branch_phone','branch_address','manager_name','contact_email','status'
    ];

    public function admin(){
        return $this->hasMany('\App\Models\Admin','branch_id','id');
    }
 
}
