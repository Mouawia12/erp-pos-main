<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = "inventorys";
    protected $fillable = [
        'date', 'branch_id', 'warehouse_id','state', 'user_id',
    ];

    public function detalis(){
        return $this -> belongsTo(\App\Models\InventoryDetails::class ,'inventory_id' );
    }

    public function warehouse(){
        return $this -> belongsTo(Warehouse::class , 'warehouse_id');
    }
    
    public function branch(){
        return $this->belongsTo('\App\Models\Branch','branch_id','id');
    }
}
