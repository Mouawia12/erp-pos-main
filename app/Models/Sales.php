<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Sales extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $table = "sales";
    protected $fillable = [
        'date','invoice_no','customer_id','representative_id', 'customer_name', 'customer_phone','biller_id',
        'warehouse_id','note','total','discount','tax','tax_excise','net','paid',
        'sale_status','payment_status','pos','lista','profit','sale_id','created_by',
        'additional_service', 'branch_id','user_id', 'status', 'invoice_type','service_mode','session_location','session_type','reservation_time','reservation_guests','cost_center','tax_mode',
        'vehicle_plate','vehicle_odometer',
        'locked_at','subscriber_id'
    ];

    protected static function booted()
    {
        static::creating(function (Sales $sale) {
            if (empty($sale->locked_at)) {
                $sale->locked_at = now();
            }
        });

        static::updating(function (Sales $sale) {
            if ($sale->locked_at) {
                $dirty = array_keys($sale->getDirty());
                $allowed = ['paid', 'payment_status', 'updated_at','service_mode','session_location','session_type','reservation_time','reservation_guests'];
                if (array_diff($dirty, $allowed)) {
                    abort(403, 'This sales invoice is locked and cannot be edited after posting.');
                }
            }
        });

        static::deleting(function () {
            abort(403, 'Sales invoices are locked and cannot be deleted after posting.');
        });
    }

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
}
