<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = ['employer_id','date','amount','advance_amount','remain','user_id'];

    public function Employer(){
        return $this->belongsTo(Employer::class);
    }

    public function Months(){
        return $this->hasMany(AdvancePaymentMonth::class);
    }
    public function User(){
        return $this->belongsTo(User::class);
    }
}
