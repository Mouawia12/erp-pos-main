<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;

class Product extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
      'code',
      'name',
      'unit',
      'cost',
      'price',
      'lista',
      'alert_quantity',
      'category_id',
      'subcategory_id',
      'quantity',
      'tax',
      'tax_rate',
      'track_quantity',
      'tax_method',
      'tax_excise',
      'type',
      'brand',
      'slug',
      'featured', 
      'city_tax',
      'max_order',
      'img',
      'warehouse_id',
      'branch_id',
      'user_id',
      'status',
      'subscriber_id',
    ];

    public function units(){
      return $this -> belongsTo(Unit::class , 'unit');
    }
}
