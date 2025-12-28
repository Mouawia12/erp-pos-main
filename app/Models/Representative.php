<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use App\Models\Company;
use App\Models\RepresentativeDocument;
use App\Models\Warehouse;
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
      'warehouse_id',
      'price_level_id',
      'profit_margin',
      'discount_percent',
      'subscriber_id'
    ];

    public function clients(){
       return $this -> hasMany(Company::class , 'representative_id_');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function documents()
    {
        return $this->hasMany(RepresentativeDocument::class);
    }
}
