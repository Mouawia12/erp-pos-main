<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $counter = 1;
        foreach ($warehouses as $warehouse) {
            for ($i = 1; $i <= 3; $i++) {
                Product::updateOrCreate(
                    ['code' => 'P-' . $counter],
                    [
                        'name' => 'صنف ' . $counter,
                        'unit' => 1,
                        'cost' => 10 * $i,
                        'price' => 15 * $i,
                        'price_level_1' => 15 * $i,
                        'price_level_2' => 16 * $i,
                        'price_level_3' => 17 * $i,
                        'price_level_4' => 18 * $i,
                        'price_level_5' => 19 * $i,
                        'price_level_6' => 20 * $i,
                        'lista' => 0,
                        'alert_quantity' => 5,
                        'category_id' => 1,
                        'subcategory_id' => 1,
                        'quantity' => 50,
                        'tax' => 15,
                        'tax_rate' => 15,
                        'tax_method' => 1,
                        'tax_excise' => 0,
                        'track_quantity' => 1,
                        'type' => 1,
                        'brand' => 0,
                        'slug' => 'product-' . $counter,
                        'featured' => 0,
                        'city_tax' => 0,
                        'max_order' => 0,
                        'img' => '',
                        'user_id' => 1,
                        'status' => 1,
                        'subscriber_id' => $warehouse->subscriber_id,
                    ]
                );
                $counter++;
            }
        }
    }
}
