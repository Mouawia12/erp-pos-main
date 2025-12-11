<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id)
 * @method static findOrFail($admin_id)
 */
class Branch extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $table = "branches";

    protected $fillable = [
        'branch_name','cr_number','tax_number','branch_phone','branch_address','manager_name','contact_email','default_invoice_type','status','subscriber_id'
    ];

    public function admin(){
        return $this->hasMany('\App\Models\Admin','branch_id','id');
    }
    public function zatcaSetting()
    {
        return $this->hasOne(BranchZatcaSetting::class);
    }
}
