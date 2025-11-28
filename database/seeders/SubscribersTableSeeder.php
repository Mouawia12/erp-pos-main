<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubscribersTableSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'company_name' => 'مشترك تجريبي',
                'cr_number' => 'CR-1001',
                'tax_number' => 'TAX-1001',
                'responsible_person' => 'Demo Admin',
                'contact_email' => 'admin@example.com',
                'contact_phone' => '050000001',
                'address' => 'الرياض',
                'system_url' => null,
                'users_limit' => 5,
                'subscription_start' => now()->subMonths(1),
                'subscription_end' => now()->addYear(),
                'status' => 'active',
                'login_email' => 'admin@example.com',
                'login_password' => Hash::make('password'),
                'login_password_plain' => 'password',
            ],
            [
                'company_name' => 'مشترك باء',
                'cr_number' => 'CR-2001',
                'tax_number' => 'TAX-2001',
                'responsible_person' => 'ليلى',
                'contact_email' => 'leila@example.com',
                'contact_phone' => '050000002',
                'address' => 'جدة',
                'system_url' => null,
                'users_limit' => 3,
                'subscription_start' => now()->subWeeks(2),
                'subscription_end' => now()->addMonths(6),
                'status' => 'active',
                'login_email' => 'leila@example.com',
                'login_password' => Hash::make('password123'),
                'login_password_plain' => 'password123',
            ],
            [
                'company_name' => 'مشترك جيم',
                'cr_number' => 'CR-3001',
                'tax_number' => 'TAX-3001',
                'responsible_person' => 'سالم',
                'contact_email' => 'salem@example.com',
                'contact_phone' => '050000003',
                'address' => 'الدمام',
                'system_url' => null,
                'users_limit' => 2,
                'subscription_start' => now()->subDays(10),
                'subscription_end' => now()->addMonths(3),
                'status' => 'active',
                'login_email' => 'salem@example.com',
                'login_password' => Hash::make('password123'),
                'login_password_plain' => 'password123',
            ],
        ];

        foreach ($samples as $sample) {
            Subscriber::updateOrCreate(
                ['login_email' => $sample['login_email']],
                $sample
            );
        }
    }
}
