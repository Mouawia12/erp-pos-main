<?php

namespace Database\Seeders;

use App\Models\AccountSetting;
use App\Models\AccountsTree;
use App\Models\Branch;
use App\Models\Subscriber;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class AccountSettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subs = Subscriber::all();
        if ($subs->isEmpty()) {
            $subs = collect([null]);
        }

        foreach ($subs as $sub) {
            $subscriberId = $sub?->id;
            $branches = Branch::query()
                ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->get();

            if ($branches->isEmpty()) {
                $branches = Branch::all();
            }

            foreach ($branches as $branch) {
                $findAccountId = function (string $code) use ($subscriberId) {
                    return AccountsTree::withoutGlobalScope('subscriber')
                        ->where('code', $code)
                        ->when($subscriberId !== null, fn($q) => $q->where('subscriber_id', $subscriberId))
                        ->value('id') ?? 0;
                };

                $warehouseId = Warehouse::query()
                    ->where('branch_id', $branch->id)
                    ->value('id') ?? 0;

                AccountSetting::updateOrCreate(
                    ['branch_id' => $branch->id],
                    [
                        'safe_account' => $findAccountId('110101'),
                        'bank_account' => $findAccountId('110201'),
                        'sales_account' => $findAccountId('4101'),
                        'purchase_account' => $findAccountId('5101'),
                        'return_sales_account' => $findAccountId('4102'),
                        'return_purchase_account' => $findAccountId('5102'),
                        'stock_account' => $findAccountId('120101'),
                        'sales_discount_account' => $findAccountId('4103'),
                        'purchase_discount_account' => $findAccountId('5103'),
                        'cost_account' => $findAccountId('5201'),
                        'reverse_profit_account' => $findAccountId('5202'),
                        'profit_account' => $findAccountId('3101'),
                        'sales_tax_account' => $findAccountId('2102'),
                        'purchase_tax_account' => $findAccountId('1301'),
                        'sales_tax_excise_account' => $findAccountId('2103'),
                        'warehouse_id' => $warehouseId,
                        'branch_id' => $branch->id,
                        'subscriber_id' => $subscriberId,
                    ]
                );
            }
        }
    }
}
