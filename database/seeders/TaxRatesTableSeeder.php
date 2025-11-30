<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')->pluck('id');
        if ($subscribers->isEmpty()) {
            $subscribers = collect([null]);
        }

        foreach ($subscribers as $subscriberId) {
            DB::table('tax_rates')->updateOrInsert(
                [
                    'subscriber_id' => $subscriberId,
                    'code' => 'VAT15',
                ],
                [
                    'name' => 'ضريبة القيمة المضافة 15%',
                    'rate' => 15,
                    'type' => 0,
                    'subscriber_id' => $subscriberId,
                ]
            );
        }
    }
}
