<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['code' => 'BR-001', 'name' => 'عام', 'user_id' => 1, 'status' => 1],
            ['code' => 'BR-002', 'name' => 'الكترونيات', 'user_id' => 1, 'status' => 1],
            ['code' => 'BR-003', 'name' => 'أطعمة', 'user_id' => 1, 'status' => 1],
        ];

        foreach ($brands as $index => $brand) {
            DB::table('brands')->updateOrInsert(
                ['id' => $index + 1],
                $brand
            );
        }
    }
}
