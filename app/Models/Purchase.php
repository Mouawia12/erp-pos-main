<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Traits\GuardsFiscalYear;
use App\Models\CostCenter;

class Purchase extends Model
{
    use HasFactory, BelongsToSubscriber, GuardsFiscalYear;
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
        'status',
        'supplier_invoice_no',
        'supplier_invoice_copy',
        'cost_center',
        'cost_center_id',
        'representative_id',
        'supplier_name',
        'supplier_phone',
        'tax_mode',
        'invoice_type',
        'payment_method',
        'subscriber_id',
        'created_by'
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

    public function representative(){
        return $this->belongsTo(Representative::class,'representative_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}
