<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpensesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subId = DB::table('subscribers')->value('id');
        $branchId = DB::table('branches')->value('id');
        $userId = DB::table('users')->value('id');

        $expenses = [
            [
                'branch_id' => $branchId,
                'from_account' => 0,
                'to_account' => 0,
                'client' => 'مصروف كهرباء',
                'amount' => 250,
                'tax_amount' => 0,
                'notes' => 'فاتورة كهرباء شهرية',
                'date' => Carbon::today()->subDays(2),
                'docNumber' => 'EXP-001',
                'payment_type' => 0,
                'user_id' => $userId,
                'subscriber_id' => $subId,
            ],
            [
                'branch_id' => $branchId,
                'from_account' => 0,
                'to_account' => 0,
                'client' => 'مصروف صيانة',
                'amount' => 150,
                'tax_amount' => 0,
                'notes' => 'صيانة أجهزة',
                'date' => Carbon::today()->subDay(),
                'docNumber' => 'EXP-002',
                'payment_type' => 0,
                'user_id' => $userId,
                'subscriber_id' => $subId,
            ],
        ];

        foreach ($expenses as $exp) {
            DB::table('expenses')->updateOrInsert(
                ['docNumber' => $exp['docNumber']],
                $exp
            );
        }
    }
}
