<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\TaxRates;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $counter = 1;

        foreach ($warehouses as $warehouse) {
            $subscriberId = $warehouse->subscriber_id;
            $unitId = Unit::where('subscriber_id', $subscriberId)->value('id') ?? Unit::value('id');
            $categoryId = Category::where('subscriber_id', $subscriberId)->value('id') ?? Category::value('id');
            $brandId = Brand::where('subscriber_id', $subscriberId)->value('id') ?? Brand::value('id') ?? 0;
            $taxRate = TaxRates::where('subscriber_id', $subscriberId)->first() ?? TaxRates::first();
            $userId = User::where('subscriber_id', $subscriberId)->value('id') ?? User::value('id') ?? 1;

            if (! $unitId || ! $categoryId || ! $taxRate) {
                continue;
            }

            for ($i = 1; $i <= 5; $i++) {
                $code = 'P-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
                $quantity = 60 + ($i * 5);
                $price = 15 * $i;
                $cost = 9 * $i;

                $product = Product::updateOrCreate(
                    [
                        'code' => $code,
                        'subscriber_id' => $subscriberId,
                    ],
                    [
                        'name' => 'صنف ' . $counter,
                        'barcode' => 'BR' . $subscriberId . str_pad($counter, 5, '0', STR_PAD_LEFT),
                        'unit' => $unitId,
                        'cost' => $cost,
                        'price' => $price,
                        'price_level_1' => $price,
                        'price_level_2' => $price + 1,
                        'price_level_3' => $price + 2,
                        'price_level_4' => $price + 3,
                        'price_level_5' => $price + 4,
                        'price_level_6' => $price + 5,
                        'lista' => 0,
                        'alert_quantity' => 5,
                        'category_id' => $categoryId,
                        'subcategory_id' => $categoryId,
                        'quantity' => $quantity,
                        'tax' => $taxRate->rate ?? 15,
                        'tax_rate' => $taxRate->id,
                        'tax_method' => 1,
                        'tax_excise' => 0,
                        'track_quantity' => 1,
                        'type' => 1,
                        'brand' => $brandId,
                        'slug' => 'product-' . $code,
                        'featured' => 0,
                        'city_tax' => 0,
                        'max_order' => 0,
                        'img' => '',
                        'user_id' => $userId,
                        'status' => 1,
                        'subscriber_id' => $subscriberId,
                    ]
                );

                ProductUnit::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'unit_id' => $unitId,
                    ],
                    [
                        'price' => $price,
                        'conversion_factor' => 1,
                        'barcode' => $product->barcode ?? $code,
                        'subscriber_id' => $subscriberId,
                    ]
                );

                WarehouseProducts::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'cost' => $cost,
                        'quantity' => $quantity,
                        'subscriber_id' => $subscriberId,
                    ]
                );

                $counter++;
            }
        }
    }
}
