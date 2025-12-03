<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'product_id',
        'name',
        'yield_quantity',
        'notes',
        'subscriber_id',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(ProductRecipeItem::class, 'recipe_id');
    }
}
