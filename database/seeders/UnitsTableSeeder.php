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

        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(['code' => $unit['code']], $unit);
        }
    }
}
