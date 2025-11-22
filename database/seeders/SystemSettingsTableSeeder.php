<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['id' => 1],
            [
                'company_name' => 'Demo Company',
                'email' => 'info@example.com',
                'client_group_id' => 1,
                'branch_id' => 1,
                'cashier_id' => 1,
                'sales_prefix' => 'SWSI',
                'sales_return_prefix' => 'SWSR',
                'payment_prefix' => 'PAY',
                'purchase_prefix' => 'PCH',
                'purchase_return_prefix' => 'PRN',
                'expenses_prefix' => 'EXP',
                'valid_to' => now()->addYear()->format('d/m/Y'),
            ]
        );
    }
}
