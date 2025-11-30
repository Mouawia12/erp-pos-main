<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpensesTableSeeder extends Seeder
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
            $userId = DB::table('users')
                ->where('subscriber_id', $subscriber->id)
                ->value('id') ?? DB::table('users')->value('id');

            if (! $branchId || ! $userId) {
                continue;
            }

            $expenses = [
                [
                    'client' => 'مصروف كهرباء ' . ($subscriber->company_name ?? ''),
                    'amount' => 250,
                    'notes' => 'فاتورة كهرباء شهرية',
                    'days_ago' => 2,
                ],
                [
                    'client' => 'مصروف صيانة ' . ($subscriber->company_name ?? ''),
                    'amount' => 150,
                    'notes' => 'صيانة أجهزة',
                    'days_ago' => 1,
                ],
            ];

            foreach ($expenses as $index => $exp) {
                $docNumber = sprintf('EXP-%02d-%03d', $subscriber->id ?? 0, $index + 1);
                DB::table('expenses')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'docNumber' => $docNumber,
                    ],
                    [
                        'branch_id' => $branchId,
                        'from_account' => 0,
                        'to_account' => 0,
                        'client' => $exp['client'],
                        'amount' => $exp['amount'],
                        'tax_amount' => 0,
                        'notes' => $exp['notes'],
                        'date' => Carbon::today()->subDays($exp['days_ago']),
                        'docNumber' => $docNumber,
                        'payment_type' => 0,
                        'user_id' => $userId,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
