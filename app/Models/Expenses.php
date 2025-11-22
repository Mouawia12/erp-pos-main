<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Expenses extends Model
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
      'payment_type', // 0 cash 1 network
      'user_id',
      'subscriber_id'
    ];

    public function branch(){
      return $this -> belongsTo(Branch::class, 'branch_id');
    }
}
