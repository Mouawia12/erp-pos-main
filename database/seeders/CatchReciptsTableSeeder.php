<?php

namespace Database\Seeders;

use App\Models\AccountSetting;
use App\Models\Company;
use App\Models\Subscriber;
use App\Models\CatchRecipt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CatchReciptsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = Subscriber::all();
        if ($subscribers->isEmpty()) {
            $subscribers = collect([(object) ['id' => null]]);
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

            $customer = Company::query()
                ->where('group_id', 3)
                ->when($subscriber->id !== null, fn($q) => $q->where('subscriber_id', $subscriber->id))
                ->first();

            if (!$customer || !$settings?->safe_account) {
                continue;
            }

            $rows = [
                [
                    'docNumber' => sprintf('CR-%02d-%03d', $subscriber->id ?? 0, 1),
                    'amount' => 200,
                    'notes' => 'سند قبض تجريبي',
                    'payment_type' => 0,
                    'days_ago' => 1,
                ],
                [
                    'docNumber' => sprintf('CR-%02d-%03d', $subscriber->id ?? 0, 2),
                    'amount' => 350,
                    'notes' => 'سند قبض تجريبي (تحويل)',
                    'payment_type' => 1,
                    'days_ago' => 0,
                ],
            ];

            foreach ($rows as $row) {
                $fromAccount = $row['payment_type'] == 1
                    ? ($settings->bank_account ?: $settings->safe_account)
                    : $settings->safe_account;

                CatchRecipt::updateOrCreate(
                    [
                        'subscriber_id' => $subscriber->id,
                        'docNumber' => $row['docNumber'],
                    ],
                    [
                        'branch_id' => $branchId,
                        'docNumber' => $row['docNumber'],
                        'date' => Carbon::today()->subDays($row['days_ago']),
                        'from_account' => $fromAccount,
                        'to_account' => $customer->account_id,
                        'client' => $customer->name ?? $customer->company,
                        'amount' => $row['amount'],
                        'notes' => $row['notes'],
                        'payment_type' => $row['payment_type'],
                        'user_id' => $userId,
                        'subscriber_id' => $subscriber->id,
                    ]
                );
            }
        }
    }
}
