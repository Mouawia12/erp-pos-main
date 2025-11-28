<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use App\Models\User;
use App\Services\SubscriberProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'مدير النظام', 'guard_name' => 'admin-web']);

        $subscribers = Subscriber::all();
        foreach ($subscribers as $subscriber) {
            [$branch, $warehouse] = SubscriberProvisioner::ensureDefaults($subscriber);

            $user = User::updateOrCreate(
                ['email' => $subscriber->login_email],
                [
                    'name' => $subscriber->responsible_person ?: $subscriber->company_name,
                    'password' => $subscriber->login_password ?: Hash::make($subscriber->login_password_plain ?: 'password123'),
                    'branch_id' => $branch->id,
                    'subscriber_id' => $subscriber->id,
                    'role_name' => $role->name,
                    'status' => 1,
                    'phone_number' => $subscriber->contact_phone ?? '0000000000',
                    'profile_pic' => '',
                ]
            );

            $user->syncRoles([$role->name]);

            if (! $subscriber->user_id) {
                $subscriber->update(['user_id' => $user->id]);
            }

            if ($warehouse && (! $warehouse->user_id || $warehouse->user_id !== $user->id)) {
                $warehouse->update(['user_id' => $user->id]);
            }
        }
    }
}
