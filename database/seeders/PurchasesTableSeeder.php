<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchasesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subId = DB::table('subscribers')->value('id');
        $branchId = DB::table('branches')->value('id');
        $warehouseId = DB::table('warehouses')->value('id');
        $supplierId = DB::table('companies')->where('group_id',4)->value('id');
        $userId = DB::table('users')->value('id');

        if(!$warehouseId || !$supplierId){
            return;
        }

        $today = Carbon::today();
        $rows = [
            ['inv' => 'PCH-000001', 'date' => $today, 'net' => 300],
            ['inv' => 'PCH-000002', 'date' => $today->copy()->subDay(), 'net' => 220],
        ];

        foreach ($rows as $row){
            DB::table('purchases')->updateOrInsert(
                ['invoice_no' => $row['inv']],
                [
                    'date' => $row['date'],
                    'invoice_no' => $row['inv'],
                    'customer_id' => $supplierId,
                    'biller_id' => $userId,
                    'warehouse_id' => $warehouseId,
                    'note' => 'عينة مشتريات',
                    'total' => $row['net'],
                    'discount' => 0,
                    'tax' => 0,
                    'net' => $row['net'],
                    'paid' => 0,
                    'purchase_status' => 'completed',
                    'payment_status' => 'not_paid',
                    'created_by' => $userId,
                    'returned_bill_id' => 0,
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'status' => 1,
                    'supplier_invoice_no' => null,
                    'supplier_invoice_copy' => null,
                    'cost_center' => null,
                    'tax_mode' => 'inclusive',
                    'subscriber_id' => $subId,
                ]
            );
        }
    }
}
