<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchRecipt extends Model
{
    use HasFactory, BelongsToSubscriber;
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
        'user_id'
    ];
    
    public function branch(){
        return $this -> belongsTo(Branch::class, 'branch_id');
    }

}
