<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryDetails extends Model
{
    protected $table = "inventory_details";
    protected $fillable = [
        'inventory_id', 'date', 'unit', 'item_id','quantity'
        , 'new_quantity', 'state', 'user_id'
    ];

    public function units(){
        return $this -> belongsTo(\App\Models\Unit::class ,'unit' );
    }
 
    public function item(){
        return $this -> belongsTo(\App\Models\Product::class ,'item_id' );
    }

    public function inventory(){
        return $this -> belongsTo(\App\Models\Inventory::class ,'inventory_id' );
    }
}
