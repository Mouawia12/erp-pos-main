<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountsTree;
use App\Models\Subscriber;

class AccountsTreeTableSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'الأصول', 'type' => 0, 'parent_id' => 0, 'parent_code' => '0', 'level' => 1, 'list' => 1, 'department' => 1, 'side' => 1],
            ['code' => '2000', 'name' => 'الخصوم', 'type' => 0, 'parent_id' => 0, 'parent_code' => '0', 'level' => 1, 'list' => 1, 'department' => 2, 'side' => 2],
            ['code' => '4000', 'name' => 'الإيرادات', 'type' => 0, 'parent_id' => 0, 'parent_code' => '0', 'level' => 1, 'list' => 1, 'department' => 3, 'side' => 2],
            ['code' => '5000', 'name' => 'المصروفات', 'type' => 0, 'parent_id' => 0, 'parent_code' => '0', 'level' => 1, 'list' => 1, 'department' => 4, 'side' => 1],
        ];

        $subscriberIds = Subscriber::pluck('id');
        if ($subscriberIds->isEmpty()) {
            $subscriberIds = collect([null]);
        }

        foreach ($subscriberIds as $subscriberId) {
            foreach ($accounts as $acc) {
                $acc['subscriber_id'] = $subscriberId;
                AccountsTree::updateOrCreate(
                    [
                        'code' => $acc['code'],
                        'subscriber_id' => $subscriberId,
                    ],
                    $acc
                );
            }
        }
    }
}
