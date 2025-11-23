<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Company;
class Representative extends Model
{
    use HasFactory, BelongsToSubscriber;
    protected $fillable = [
      'code',
      'name',
      'user_name',
      'password',
      'notes',
      'active',
      'document_name',
      'document_number',
      'document_expiry_date',
      'subscriber_id'
    ];

    public function clients(){
       return $this -> hasMany(Company::class , 'representative_id_');
    }
}
