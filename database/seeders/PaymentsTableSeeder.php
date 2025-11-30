<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')
            ->select('id')
            ->get();

        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null]]);
        }

        foreach ($subscribers as $subscriber) {
            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            if (! $userId) {
                continue;
            }

            $sale = DB::table('sales')
                ->where('sale_id', 0)
                ->where('subscriber_id', $subscriber->id)
                ->orderBy('id')
                ->first();

            if ($sale) {
                $amount = $sale->net > 0 ? min(100, $sale->net) : $sale->net;
                $docNumber = sprintf('PAY-%02d-%03d', $subscriber->id ?? 0, 1);

                DB::table('payments')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'based_on_bill_number' => $sale->invoice_no,
                        'sale_id' => $sale->id,
                        'paid_by' => 'cash',
                    ],
                    [
                        'date' => Carbon::today(),
                        'doc_number' => $docNumber,
                        'branch_id' => $sale->branch_id ?? 1,
                        'purchase_id' => null,
                        'sale_id' => $sale->id,
                        'company_id' => $sale->customer_id,
                        'amount' => $amount,
                        'paid_by' => 'cash',
                        'remain' => $sale->net - $amount,
                        'based_on_bill_number' => $sale->invoice_no,
                        'user_id' => $userId,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }

            $purchase = DB::table('purchases')
                ->where('returned_bill_id', 0)
                ->where('subscriber_id', $subscriber->id)
                ->orderBy('id')
                ->first();

            if ($purchase) {
                $amount = $purchase->net > 0 ? min(120, $purchase->net) : $purchase->net;
                $docNumber = sprintf('PAY-%02d-%03d', $subscriber->id ?? 0, 2);

                DB::table('payments')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'based_on_bill_number' => $purchase->invoice_no,
                        'purchase_id' => $purchase->id,
                        'paid_by' => 'cash',
                    ],
                    [
                        'date' => Carbon::today(),
                        'doc_number' => $docNumber,
                        'branch_id' => $purchase->branch_id ?? 1,
                        'purchase_id' => $purchase->id,
                        'sale_id' => null,
                        'company_id' => $purchase->customer_id,
                        'amount' => $amount,
                        'paid_by' => 'cash',
                        'remain' => $purchase->net - $amount,
                        'based_on_bill_number' => $purchase->invoice_no,
                        'user_id' => $userId,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
