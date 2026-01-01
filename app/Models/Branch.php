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

    protected $casts = [
        'national_address_proof_issue_date' => 'date',
        'national_address_proof_expiry_date' => 'date',
    ];

    protected $fillable = [
        'branch_name','cr_number','tax_number','branch_phone','branch_address',
        'national_address_short','national_address_building_no','national_address_street','national_address_district',
        'national_address_city','national_address_region','national_address_postal_code','national_address_additional_no',
        'national_address_unit_no','national_address_proof_no','national_address_proof_issue_date','national_address_proof_expiry_date',
        'national_address_country',
        'manager_name','contact_email','default_invoice_type','status','subscriber_id'
    ];

    public function admin(){
        return $this->hasMany('\App\Models\Admin','branch_id','id');
    }
    public function zatcaSetting()
    {
        return $this->hasOne(BranchZatcaSetting::class);
    }
}
