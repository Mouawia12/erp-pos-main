<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')->pluck('id');

        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            $purchases = DB::table('purchases')
                ->where('subscriber_id', $subscriberId)
                ->orderBy('id')
                ->take(2)
                ->get();
            $product = DB::table('products')
                ->where('subscriber_id', $subscriberId)
                ->orderBy('id', 'desc')
                ->first() ?? DB::table('products')->orderBy('id')->first();
            $warehouseId = DB::table('warehouses')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('warehouses')->value('id');

            if (! $product || $purchases->isEmpty() || ! $warehouseId) {
                continue;
            }

            foreach ($purchases as $purchase) {
                $quantity = 3;
                $cost = round($purchase->net / max($quantity, 1), 2);

                DB::table('purchase_details')->updateOrInsert(
                    [
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'purchase_id' => $purchase->id,
                        'product_code' => $product->code,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'cost_without_tax' => $cost,
                        'cost_with_tax' => $cost,
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $product->unit,
                        'tax' => 0,
                        'total' => $cost * $quantity,
                        'net' => $cost * $quantity,
                        'returned_qnt' => 0,
                        'subscriber_id' => $subscriberId,
                    ]
                );
            }
        }
    }
}
