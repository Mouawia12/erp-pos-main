<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'invoice_no',
        'customer_id',
        'biller_id',
        'warehouse_id',
        'note',
        'total',
        'discount',
        'tax',
        'net',
        'paid',
        'purchase_status',
        'payment_status', 
        'returned_bill_id',
        'branch_id',
        'user_id',
        'status'
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
