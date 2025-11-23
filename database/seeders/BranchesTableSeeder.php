<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subs = Subscriber::take(3)->get();
        foreach ($subs as $index => $sub) {
            Branch::updateOrCreate(
                ['branch_name' => 'فرع ' . ($index + 1) . ' - ' . $sub->company_name],
                [
                    'branch_name' => 'فرع ' . ($index + 1) . ' - ' . $sub->company_name,
                    'branch_phone' => '0599' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'branch_address' => 'عنوان ' . $sub->company_name,
                    'cr_number' => $sub->cr_number,
                    'tax_number' => $sub->tax_number,
                    'manager_name' => $sub->responsible_person,
                    'contact_email' => $sub->contact_email,
                    'default_invoice_type' => 'simplified_tax_invoice',
                    'status' => 1,
                    'subscriber_id' => $sub->id,
                ]
            );
        }
    }
}
