<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tax_rates')->updateOrInsert(
            ['code' => 'VAT15'],
            [
                'name' => 'ضريبة القيمة المضافة 15%',
                'rate' => 15,
                'type' => 0,
            ]
        );
    }
}
