<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $purchase = DB::table('purchases')->orderBy('id')->first();
        $product = DB::table('products')->orderBy('id','desc')->first();
        $warehouseId = DB::table('warehouses')->value('id');
        $subId = DB::table('subscribers')->value('id');

        if(!$purchase || !$product){
            return;
        }

        DB::table('purchase_details')->updateOrInsert(
            ['purchase_id' => $purchase->id, 'product_id' => $product->id],
            [
                'purchase_id' => $purchase->id,
                'product_code' => $product->code,
                'product_id' => $product->id,
                'quantity' => 3,
                'cost_without_tax' => 100,
                'cost_with_tax' => 100,
                'warehouse_id' => $warehouseId,
                'unit_id' => $product->unit,
                'tax' => 0,
                'total' => 300,
                'net' => 300,
                'returned_qnt' => 0,
                'subscriber_id' => $subId,
            ]
        );
    }
}
