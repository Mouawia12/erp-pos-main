<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehousesTableSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        foreach ($branches as $branch) {
            Warehouse::updateOrCreate(
                [
                    'subscriber_id' => $branch->subscriber_id,
                    'name' => 'مخزن ' . $branch->branch_name,
                ],
                [
                    'code' => 'WH-' . $branch->id,
                    'name' => 'مخزن ' . $branch->branch_name,
                    'phone' => $branch->branch_phone ?? '050000000',
                    'email' => $branch->contact_email,
                    'address' => $branch->branch_address ?? 'غير محدد',
                    'branch_id' => $branch->id,
                    'user_id' => 1,
                    'status' => 1,
                    'subscriber_id' => $branch->subscriber_id,
                ]
            );
        }
    }
}
