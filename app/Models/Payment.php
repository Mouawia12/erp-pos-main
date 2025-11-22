<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\User;

class Payment extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
        'date', 'doc_number','purchase_id','sale_id','company_id','amount','paid_by','remain' 
        , 'branch_id', 'based_on_bill_number','user_id','subscriber_id'
    ];

    public function user(){
        return $this -> belongsTo(User::class , 'user_id');
    }
}
