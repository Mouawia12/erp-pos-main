<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'PCS', 'name' => 'قطعة'],
            ['code' => 'BOX', 'name' => 'علبة'],
            ['code' => 'CTN', 'name' => 'كرتون'],
        ];

        $subscribers = DB::table('subscribers')->pluck('id');
        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            foreach ($units as $unit) {
                DB::table('units')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriberId,
                        'code' => $unit['code'],
                    ],
                    [
                        'name' => $unit['name'],
                        'subscriber_id' => $subscriberId,
                    ]
                );
            }
        }
    }
}
