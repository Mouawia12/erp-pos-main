<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAssembly extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'recipe_id',
        'product_id',
        'quantity',
        'warehouse_id',
        'notes',
        'subscriber_id',
        'created_by',
    ];

    public function recipe()
    {
        return $this->belongsTo(ProductRecipe::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(ProductAssemblyItem::class, 'assembly_id');
    }
}
