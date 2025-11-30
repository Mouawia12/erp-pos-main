<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')
            ->select('id', 'company_name')
            ->get();

        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null, 'company_name' => 'مشترك افتراضي']]);
        }

        foreach ($subscribers as $subscriber) {
            $branchId = DB::table('branches')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('branches')->value('id');
            $warehouseId = DB::table('warehouses')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('warehouses')->value('id');
            $customerId = DB::table('companies')
                ->where('group_id', 3)
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('companies')->where('group_id', 3)->value('id');
            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            if (! $warehouseId || ! $customerId || ! $userId) {
                continue;
            }

            $today = Carbon::today();
            $rows = [
                ['offset' => 0, 'net' => 500],
                ['offset' => 1, 'net' => 350],
                ['offset' => 2, 'net' => 420],
            ];

            foreach ($rows as $index => $row) {
                $date = $today->copy()->subDays($row['offset']);
                $invoice = sprintf(
                    'SWSI-%02d-%06d',
                    $subscriber->id ?? 0,
                    $index + 1
                );

                DB::table('sales')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'invoice_no' => $invoice,
                    ],
                    [
                        'date' => $date,
                        'invoice_no' => $invoice,
                        'invoice_type' => 'tax_invoice',
                        'sale_id' => 0,
                        'customer_id' => $customerId,
                        'biller_id' => $userId,
                        'warehouse_id' => $warehouseId,
                        'note' => 'عينة مبيعات ' . ($subscriber->company_name ?? 'مشترك'),
                        'total' => $row['net'],
                        'discount' => 0,
                        'tax' => 0,
                        'tax_excise' => 0,
                        'net' => $row['net'],
                        'paid' => 0,
                        'sale_status' => 'completed',
                        'payment_status' => 'not_paid',
                        'locked_at' => $date,
                        'created_by' => $userId,
                        'pos' => 0,
                        'lista' => 0,
                        'profit' => 0,
                        'additional_service' => 0,
                        'branch_id' => $branchId,
                        'user_id' => $userId,
                        'status' => 1,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
