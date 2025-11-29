<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'SAR', 'name' => 'الريال السعودي', 'symbol' => '﷼'],
            ['code' => 'USD', 'name' => 'الدولار الأمريكي', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'اليورو', 'symbol' => '€'],
        ];

        foreach ($currencies as $index => $currency) {
            DB::table('currencies')->updateOrInsert(
                ['id' => $index + 1],
                $currency
            );
        }
    }
}
