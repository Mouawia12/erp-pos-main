<?php

namespace App\Models;

use App\Models\Traits\BelongsToSubscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAssemblyItem extends Model
{
    use HasFactory, BelongsToSubscriber;

    protected $fillable = [
        'assembly_id',
        'component_product_id',
        'quantity',
        'subscriber_id',
    ];

    public function assembly()
    {
        return $this->belongsTo(ProductAssembly::class, 'assembly_id');
    }

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
