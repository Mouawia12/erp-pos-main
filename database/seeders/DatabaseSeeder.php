<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'branch_id' => 1,
                'phone_number' => '0000000000',
                'profile_pic' => '',
                'role_name' => 'مدير النظام',
                'status' => 1,
            ]
        );

        $role = Role::firstOrCreate(
            ['name' => 'مدير النظام', 'guard_name' => 'admin-web'],
            ['name' => 'مدير النظام', 'guard_name' => 'admin-web']
        );

        $role->syncPermissions(Permission::all());

        if ($user) {
            $user->assignRole($role->name);
        }
    }
}
