<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use App\Models\Traits\GuardsFiscalYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchRecipt extends Model
{
    use HasFactory, BelongsToSubscriber, GuardsFiscalYear;
    protected $fillable = [
        'id',
        'branch_id',
        'from_account',
        'to_account',
        'client',
        'amount',
        'notes',
        'date',
        'docNumber',
        'payment_type',
        'user_id',
        'subscriber_id'
    ];
    
    public function branch(){
        return $this -> belongsTo(Branch::class, 'branch_id');
    }

}
