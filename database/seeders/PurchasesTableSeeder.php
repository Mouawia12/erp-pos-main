<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchasesTableSeeder extends Seeder
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
            $supplierId = DB::table('companies')
                ->where('group_id', 4)
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('companies')->where('group_id', 4)->value('id');
            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            if (! $warehouseId || ! $supplierId || ! $userId) {
                continue;
            }

            $today = Carbon::today();
            $rows = [
                ['offset' => 0, 'net' => 300],
                ['offset' => 1, 'net' => 220],
            ];

            foreach ($rows as $index => $row) {
                $date = $today->copy()->subDays($row['offset']);
                $invoice = sprintf(
                    'PCH-%02d-%06d',
                    $subscriber->id ?? 0,
                    $index + 1
                );

                DB::table('purchases')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'invoice_no' => $invoice,
                    ],
                    [
                        'date' => $date,
                        'invoice_no' => $invoice,
                        'customer_id' => $supplierId,
                        'biller_id' => $userId,
                        'warehouse_id' => $warehouseId,
                        'note' => 'عينة مشتريات ' . ($subscriber->company_name ?? 'مشترك'),
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
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
