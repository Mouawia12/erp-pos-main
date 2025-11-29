<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerGroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'عملاء التجزئة',
                'discount_percentage' => 0,
                'sell_with_cost' => false,
                'enable_discount' => false,
            ],
            [
                'name' => 'عملاء الجملة',
                'discount_percentage' => 5,
                'sell_with_cost' => false,
                'enable_discount' => true,
            ],
            [
                'name' => 'شركات',
                'discount_percentage' => 10,
                'sell_with_cost' => true,
                'enable_discount' => true,
            ],
        ];

        foreach ($groups as $index => $group) {
            DB::table('customer_groups')->updateOrInsert(
                ['id' => $index + 1],
                $group
            );
        }
    }
}
