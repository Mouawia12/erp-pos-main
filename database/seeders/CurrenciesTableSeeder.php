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

        $subscribers = DB::table('subscribers')->pluck('id');
        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            foreach ($currencies as $currency) {
                DB::table('currencies')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriberId,
                        'code' => $currency['code'],
                    ],
                    array_merge($currency, ['subscriber_id' => $subscriberId])
                );
            }
        }
    }
}
