<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')->pluck('id');

        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            $sales = DB::table('sales')
                ->where('subscriber_id', $subscriberId)
                ->orderBy('id')
                ->take(2)
                ->get();
            $product = DB::table('products')
                ->where('subscriber_id', $subscriberId)
                ->orderBy('id')
                ->first() ?? DB::table('products')->orderBy('id')->first();
            $warehouseId = DB::table('warehouses')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('warehouses')->value('id');

            if (! $product || $sales->isEmpty() || ! $warehouseId) {
                continue;
            }

            foreach ($sales as $sale) {
                $quantity = 2;
                $price = round($sale->net / max($quantity, 1), 2);
                DB::table('sale_details')->updateOrInsert(
                    [
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'sale_id' => $sale->id,
                        'product_code' => $product->code,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price_unit' => $price,
                        'discount' => 0,
                        'price_with_tax' => $price,
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $product->unit,
                        'tax' => 0,
                        'tax_excise' => 0,
                        'total' => $price * $quantity,
                        'lista' => 0,
                        'profit' => 0,
                        'subscriber_id' => $subscriberId,
                    ]
                );
            }
        }
    }
}
