<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDocDetails extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = ['employer_id','hours','hour_value','reward','additional','advance_payment',
        'deductions','salary_doc_id'];
}
