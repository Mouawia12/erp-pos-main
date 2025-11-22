<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subId = DB::table('subscribers')->value('id');
        $branchId = DB::table('branches')->value('id');
        $warehouseId = DB::table('warehouses')->value('id');
        $customerId = DB::table('companies')->where('group_id',3)->value('id');
        $userId = DB::table('users')->value('id');

        if(!$warehouseId || !$customerId){
            return;
        }

        $today = Carbon::today();
        $rows = [
            ['inv' => 'SWSI-000001', 'date' => $today, 'net' => 500],
            ['inv' => 'SWSI-000002', 'date' => $today->copy()->subDay(), 'net' => 350],
            ['inv' => 'SWSI-000003', 'date' => $today->copy()->subDays(2), 'net' => 420],
        ];

        foreach ($rows as $row){
            DB::table('sales')->updateOrInsert(
                ['invoice_no' => $row['inv']],
                [
                    'date' => $row['date'],
                    'invoice_no' => $row['inv'],
                    'invoice_type' => 'tax_invoice',
                    'sale_id' => 0,
                    'customer_id' => $customerId,
                    'biller_id' => $userId,
                    'warehouse_id' => $warehouseId,
                    'note' => 'عينة مبيعات',
                    'total' => $row['net'],
                    'discount' => 0,
                    'tax' => 0,
                    'tax_excise' => 0,
                    'net' => $row['net'],
                    'paid' => 0,
                    'sale_status' => 'completed',
                    'payment_status' => 'not_paid',
                    'locked_at' => $row['date'],
                    'created_by' => $userId,
                    'pos' => 0,
                    'lista' => 0,
                    'profit' => 0,
                    'additional_service' => 0,
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'status' => 1,
                    'subscriber_id' => $subId,
                ]
            );
        }
    }
}
