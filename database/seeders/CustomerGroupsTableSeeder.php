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
                'name' => 'تجزئة',
                'discount_percentage' => 0,
                'sell_with_cost' => false,
                'enable_discount' => false,
            ],
            [
                'name' => 'جملة',
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

        $subscribers = DB::table('subscribers')->pluck('id');
        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            foreach ($groups as $group) {
                DB::table('customer_groups')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriberId,
                        'name' => $group['name'],
                    ],
                    array_merge($group, ['subscriber_id' => $subscriberId])
                );
            }
        }
    }
}
