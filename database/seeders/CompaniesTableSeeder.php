<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subs = Subscriber::all();
        foreach ($subs as $sub) {
            Company::updateOrCreate(
                ['email' => 'client-'.$sub->id.'@example.com'],
                [
                    'group_id' => 3,
                    'group_name' => 'عميل',
                    'customer_group_id' => 1,
                    'customer_group_name' => 'افتراضي',
                    'name' => 'عميل ' . $sub->company_name,
                    'company' => $sub->company_name,
                    'vat_no' => $sub->tax_number,
                    'address' => $sub->address,
                    'city' => 'مدينة',
                    'state' => 'منطقة',
                    'postal_code' => '00000',
                    'country' => 'السعودية',
                    'email' => 'client-'.$sub->id.'@example.com',
                    'phone' => '05123' . $sub->id,
                    'status' => 1,
                    'cr_number' => $sub->cr_number,
                    'tax_number' => $sub->tax_number,
                    'subscriber_id' => $sub->id,
                ]
            );

            Company::updateOrCreate(
                ['email' => 'supplier-'.$sub->id.'@example.com'],
                [
                    'group_id' => 4,
                    'group_name' => 'مورد',
                    'customer_group_id' => 1,
                    'customer_group_name' => 'افتراضي',
                    'name' => 'مورد ' . $sub->company_name,
                    'company' => $sub->company_name,
                    'vat_no' => $sub->tax_number,
                    'address' => $sub->address,
                    'city' => 'مدينة',
                    'state' => 'منطقة',
                    'postal_code' => '00000',
                    'country' => 'السعودية',
                    'email' => 'supplier-'.$sub->id.'@example.com',
                    'phone' => '05987' . $sub->id,
                    'status' => 1,
                    'cr_number' => $sub->cr_number,
                    'tax_number' => $sub->tax_number,
                    'subscriber_id' => $sub->id,
                ]
            );
        }
    }
}
