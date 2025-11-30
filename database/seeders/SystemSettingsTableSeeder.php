<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')
            ->select('id', 'company_name', 'contact_email', 'contact_phone')
            ->get();

        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) [
                'id' => null,
                'company_name' => 'Demo Company',
                'contact_email' => 'info@example.com',
                'contact_phone' => '0500000000',
            ]]);
        }

        foreach ($subscribers as $subscriber) {
            $subscriberId = $subscriber->id;
            $currencyId = DB::table('currencies')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('currencies')->value('id') ?? 0;
            $groupId = DB::table('customer_groups')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('customer_groups')->value('id') ?? 0;
            $branchId = DB::table('branches')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('branches')->value('id') ?? 0;
            $cashierId = DB::table('cashiers')
                ->where('subscriber_id', $subscriberId)
                ->value('id') ?? DB::table('cashiers')->value('id') ?? 0;

            $branchCount = DB::table('branches')
                ->where('subscriber_id', $subscriberId)
                ->count();

            DB::table('system_settings')->updateOrInsert(
                ['subscriber_id' => $subscriberId],
                [
                    'company_name' => $subscriber->company_name ?? 'Demo Company',
                    'email' => $subscriber->contact_email ?? 'info@example.com',
                    'contact_phone' => $subscriber->contact_phone ?? '0500000000',
                    'currency_id' => $currencyId,
                    'client_group_id' => $groupId,
                    'branch_id' => $branchId,
                    'cashier_id' => $cashierId,
                    'sales_prefix' => sprintf('SWSI-%02d', $subscriberId ?? 0),
                    'sales_return_prefix' => sprintf('SWSR-%02d', $subscriberId ?? 0),
                    'payment_prefix' => sprintf('PAY-%02d', $subscriberId ?? 0),
                    'purchase_prefix' => sprintf('PCH-%02d', $subscriberId ?? 0),
                    'purchase_return_prefix' => sprintf('PRN-%02d', $subscriberId ?? 0),
                    'expenses_prefix' => sprintf('EXP-%02d', $subscriberId ?? 0),
                    'quotation_prefix' => sprintf('QTN-%02d', $subscriberId ?? 0),
                    'update_qnt_prefix' => sprintf('UQT-%02d', $subscriberId ?? 0),
                    'store_prefix' => sprintf('STR-%02d', $subscriberId ?? 0),
                    'transaction_prefix' => sprintf('TRX-%02d', $subscriberId ?? 0),
                    'valid_to' => now()->addYear()->format('d/m/Y'),
                    'sell_without_stock' => 1,
                    'default_product_type' => 1,
                    'default_invoice_type' => 'simplified_tax_invoice',
                    'single_device_login' => 0,
                    'per_user_sequence' => 0,
                    'max_branches' => max(3, $branchCount),
                    'subscriber_id' => $subscriberId,
                ]
            );
        }
    }
}
