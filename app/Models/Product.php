<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSettings;

class Product extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected static array $exciseEnabledCache = [];

    protected $fillable = [
      'code',
      'barcode',
      'name',
      'name_en',
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
      'salon_department_id',
      'quantity',
      'tax',
      'tax_rate',
      'track_quantity',
      'track_batch',
      'tax_method',
      'price_includes_tax',
      'profit_margin',
      'profit_type',
      'profit_amount',
      'tax_excise',
      'shipping_service_type',
      'shipping_service_amount',
      'delivery_service_type',
      'delivery_service_amount',
      'installation_service_type',
      'installation_service_amount',
      'type',
      'brand',
      'slug',
      'featured', 
      'city_tax',
      'max_order',
      'img',
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

    public function salonDepartment()
    {
        return $this->belongsTo(SalonDepartment::class, 'salon_department_id');
    }

    public function totalTaxRate(): float
    {
        $base = (float) ($this->tax ?? 0);
        $additional = $this->productTaxes->pluck('tax_rate_id')
            ->map(fn($id)=> (float) optional(\App\Models\TaxRates::find($id))->rate)
            ->sum();
        return $base + $additional;
    }

    public function getTaxExciseAttribute($value): float
    {
        if (!self::exciseEnabled()) {
            return 0.0;
        }

        return (float) ($value ?? 0);
    }

    private static function exciseEnabled(): bool
    {
        $user = Auth::user();
        $cacheKey = $user?->subscriber_id ?? 'global';

        if (array_key_exists($cacheKey, self::$exciseEnabledCache)) {
            return self::$exciseEnabledCache[$cacheKey];
        }

        $query = SystemSettings::query();
        if ($user?->subscriber_id) {
            $query->where('subscriber_id', $user->subscriber_id);
        }

        $settings = $query->first();
        $enabled = (bool) optional($settings)->is_tobacco;

        self::$exciseEnabledCache[$cacheKey] = $enabled;

        return $enabled;
    }
}
