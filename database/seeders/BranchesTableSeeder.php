<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    public function run(): void
    {
        $subs = Subscriber::all();
        foreach ($subs as $sub) {
            $templates = [
                [
                    'name' => 'الفرع الرئيسي - ' . $sub->company_name,
                    'phone_suffix' => '01',
                    'address' => $sub->address ?: 'عنوان رئيسي',
                ],
                [
                    'name' => 'نقطة بيع 1 - ' . $sub->company_name,
                    'phone_suffix' => '02',
                    'address' => ($sub->address ?: 'عنوان رئيسي') . ' - نقطة بيع',
                ],
            ];

            foreach ($templates as $idx => $template) {
                Branch::updateOrCreate(
                    [
                        'subscriber_id' => $sub->id,
                        'branch_name' => $template['name'],
                    ],
                    [
                        'branch_name' => $template['name'],
                        'branch_phone' => '0599' . str_pad($sub->id, 2, '0', STR_PAD_LEFT) . $template['phone_suffix'],
                        'branch_address' => $template['address'],
                        'cr_number' => $sub->cr_number,
                        'tax_number' => $sub->tax_number,
                        'manager_name' => $sub->responsible_person ?: $sub->company_name,
                        'contact_email' => $sub->contact_email ?: $sub->login_email,
                        'default_invoice_type' => 'simplified_tax_invoice',
                        'status' => 1,
                        'subscriber_id' => $sub->id,
                    ]
                );
            }
        }
    }
}
