<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $sale = DB::table('sales')->where('sale_id',0)->first();
        $purchase = DB::table('purchases')->where('returned_bill_id',0)->first();
        $userId = DB::table('users')->value('id');
        $subId = DB::table('subscribers')->value('id');

        if($sale){
            DB::table('payments')->updateOrInsert(
                ['based_on_bill_number' => $sale->invoice_no, 'paid_by' => 'cash'],
                [
                    'date' => Carbon::today(),
                    'doc_number' => 'PAY-001',
                    'branch_id' => $sale->branch_id ?? 1,
                    'purchase_id' => null,
                    'sale_id' => $sale->id,
                    'company_id' => $sale->customer_id,
                    'amount' => $sale->net > 0 ? min(100,$sale->net) : $sale->net,
                    'paid_by' => 'cash',
                    'remain' => $sale->net - min(100,$sale->net),
                    'based_on_bill_number' => $sale->invoice_no,
                    'user_id' => $userId,
                    'subscriber_id' => $subId,
                ]
            );
        }

        if($purchase){
            DB::table('payments')->updateOrInsert(
                ['based_on_bill_number' => $purchase->invoice_no, 'paid_by' => 'cash', 'purchase_id'=>$purchase->id],
                [
                    'date' => Carbon::today(),
                    'doc_number' => 'PAY-002',
                    'branch_id' => $purchase->branch_id ?? 1,
                    'purchase_id' => $purchase->id,
                    'sale_id' => null,
                    'company_id' => $purchase->customer_id,
                    'amount' => $purchase->net > 0 ? min(120,$purchase->net) : $purchase->net,
                    'paid_by' => 'cash',
                    'remain' => $purchase->net - min(120,$purchase->net),
                    'based_on_bill_number' => $purchase->invoice_no,
                    'user_id' => $userId,
                    'subscriber_id' => $subId,
                ]
            );
        }
    }
}
