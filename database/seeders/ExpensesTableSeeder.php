<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\AccountSetting;
use App\Models\AccountsTree;

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

            $settings = AccountSetting::query()
                ->where('branch_id', $branchId)
                ->first();

            $expenseAccountId = AccountsTree::withoutGlobalScope('subscriber')
                ->where('code', '5301')
                ->when($subscriber->id !== null, fn($q) => $q->where('subscriber_id', $subscriber->id))
                ->value('id') ?? 0;

            $fromAccount = $settings?->safe_account ?? 0;
            $toAccount = $expenseAccountId ?: ($settings?->cost_account ?? 0);

            $expenses = [
                [
                    'client' => 'مصروف كهرباء ' . ($subscriber->company_name ?? ''),
                    'amount' => 250,
                    'notes' => 'فاتورة كهرباء شهرية',
                    'days_ago' => 2,
                    'tax_amount' => 0,
                ],
                [
                    'client' => 'مصروف صيانة ' . ($subscriber->company_name ?? ''),
                    'amount' => 150,
                    'notes' => 'صيانة أجهزة',
                    'days_ago' => 1,
                    'tax_amount' => 0,
                ],
            ];

            foreach ($expenses as $index => $exp) {
                $docNumber = sprintf('EXP-%02d-%03d', $subscriber->id ?? 0, $index + 1);
                $payload = [
                    'branch_id' => $branchId,
                    'from_account' => $fromAccount,
                    'to_account' => $toAccount,
                    'client' => $exp['client'],
                    'amount' => $exp['amount'],
                    'notes' => $exp['notes'],
                    'date' => Carbon::today()->subDays($exp['days_ago']),
                    'docNumber' => $docNumber,
                    'payment_type' => 0,
                    'user_id' => $userId,
                    'subscriber_id' => $subscriber->id,
                ];

                if (Schema::hasColumn('expenses', 'tax_amount')) {
                    $payload['tax_amount'] = $exp['tax_amount'];
                }

                DB::table('expenses')->updateOrInsert(
                    [
                        'subscriber_id' => $subscriber->id,
                        'docNumber' => $docNumber,
                    ],
                    $payload
                );
            }
        }
    }
}
