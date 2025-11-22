<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'افتراضي',
                'code' => 'CAT-001',
                'slug' => 'default-category',
                'description' => 'فئة افتراضية للاختبار',
                'parent_id' => 0,
                'tax_excise' => 0,
                'branch_id' => 1,
                'user_id' => 1,
                'status' => 1,
            ]
        );
    }
}
