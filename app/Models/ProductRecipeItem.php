<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipeItem extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'recipe_id',
        'component_product_id',
        'quantity',
        'unit_id',
        'subscriber_id',
    ];

    public function recipe()
    {
        return $this->belongsTo(ProductRecipe::class, 'recipe_id');
    }

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
