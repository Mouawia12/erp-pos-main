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
      'price_level_1',
      'price_level_2',
      'price_level_3',
      'price_level_4',
      'price_level_5',
      'price_level_6',
      'lista',
      'alert_quantity',
      'category_id',
      'subcategory_id',
      'quantity',
      'tax',
      'tax_rate',
      'track_quantity',
      'tax_method',
      'price_includes_tax',
      'profit_margin',
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

    public function productTaxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function totalTaxRate(): float
    {
        $base = (float) ($this->tax ?? 0);
        $additional = $this->productTaxes->pluck('tax_rate_id')
            ->map(fn($id)=> (float) optional(\App\Models\TaxRates::find($id))->rate)
            ->sum();
        return $base + $additional;
    }
}
