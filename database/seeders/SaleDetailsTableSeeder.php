<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $sale = DB::table('sales')->orderBy('id')->first();
        $product = DB::table('products')->orderBy('id')->first();
        $warehouseId = DB::table('warehouses')->value('id');
        $subId = DB::table('subscribers')->value('id');

        if(!$sale || !$product){
            return;
        }

        DB::table('sale_details')->updateOrInsert(
            ['sale_id' => $sale->id, 'product_id' => $product->id],
            [
                'sale_id' => $sale->id,
                'product_code' => $product->code,
                'product_id' => $product->id,
                'quantity' => 2,
                'price_unit' => 250,
                'discount' => 0,
                'price_with_tax' => 250,
                'warehouse_id' => $warehouseId,
                'unit_id' => $product->unit,
                'tax' => 0,
                'tax_excise' => 0,
                'total' => 500,
                'lista' => 0,
                'profit' => 0,
                'subscriber_id' => $subId,
            ]
        );
    }
}
