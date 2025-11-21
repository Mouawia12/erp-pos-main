<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
    protected $table = "sales";
    protected $fillable = [
        'date','invoice_no','customer_id', 'customer_name', 'customer_phone','biller_id',
        'warehouse_id','note','total','discount','tax','tax_excise','net','paid',
        'sale_status','payment_status','pos','lista','profit','sale_id',
        'additional_service', 'branch_id','user_id', 'status'
    ];

    public function branch(){
        return $this -> belongsTo(Branch::class , 'branch_id');
    }

    public function warehouse(){
        return $this -> belongsTo(Warehouse::class , 'warehouse_id');
    }

    public function customer(){
        return $this -> belongsTo(Company::class , 'customer_id');
    }
}
