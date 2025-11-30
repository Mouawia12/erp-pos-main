<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Database\Seeders\SubscribersTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\BranchesTableSeeder;
use Database\Seeders\WarehousesTableSeeder;
use Database\Seeders\CompaniesTableSeeder;
use Database\Seeders\ProductsTableSeeder;
use Database\Seeders\UnitsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\TaxRatesTableSeeder;
use Database\Seeders\SystemSettingsTableSeeder;
use Database\Seeders\BrandsTableSeeder;
use Database\Seeders\CurrenciesTableSeeder;
use Database\Seeders\CustomerGroupsTableSeeder;
use Database\Seeders\AccountsTreeTableSeeder;
use Database\Seeders\ExpensesTableSeeder;
use Database\Seeders\PaymentsTableSeeder;
use Database\Seeders\SalesTableSeeder;
use Database\Seeders\PurchasesTableSeeder;
use Database\Seeders\SaleDetailsTableSeeder;
use Database\Seeders\PurchaseDetailsTableSeeder;
use Database\Seeders\MultiSubscriberDataSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // الدور الأساس للمشتركين: مدير النظام مع كامل الصلاحيات
        $mainRole = Role::firstOrCreate(
            ['name' => 'مدير النظام', 'guard_name' => 'admin-web'],
            ['name' => 'مدير النظام', 'guard_name' => 'admin-web']
        );

        $mainRole->syncPermissions(Permission::all());

        // تنظيف أي دور قديم باسم subscriber: نقل المستخدمين إليه إلى دور مدير النظام ثم حذفه
        $oldSubscriberRole = Role::where('name', 'subscriber')->where('guard_name','admin-web')->first();
        if ($oldSubscriberRole) {
            $usersWithOld = User::role('subscriber')->get();
            foreach ($usersWithOld as $u) {
                $u->syncRoles([$mainRole->name]);
                $u->update(['role_name' => $mainRole->name]);
            }
            $oldSubscriberRole->delete();
        }

        // Owner account for SaaS control panel
        $ownerRole = Role::firstOrCreate(
            ['name' => 'system_owner', 'guard_name' => 'admin-web'],
            ['name' => 'system_owner', 'guard_name' => 'admin-web']
        );

        $ownerUser = User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'System Owner',
                'password' => Hash::make('password'),
                'branch_id' => 1,
                'role_name' => 'system_owner',
                'status' => 1,
                'phone_number' => '0000000000',
                'profile_pic' => '',
            ]
        );

        if ($ownerUser) {
            $ownerUser->assignRole($ownerRole->name);
        }

        // عينات بيانات للمشتركين وباقي الجداول الأساسية (ترتيب يحترم الاعتمادية)
        $this->call([
            SubscribersTableSeeder::class,
            UsersTableSeeder::class,
            BranchesTableSeeder::class,
            WarehousesTableSeeder::class,
            UnitsTableSeeder::class,
            CustomerGroupsTableSeeder::class,
            CurrenciesTableSeeder::class,
            TaxRatesTableSeeder::class,
            BrandsTableSeeder::class,
            CategoriesTableSeeder::class,
            SystemSettingsTableSeeder::class,
            CompaniesTableSeeder::class,
            ProductsTableSeeder::class,
            AccountsTreeTableSeeder::class,
            ExpensesTableSeeder::class,
            PaymentsTableSeeder::class,
            SalesTableSeeder::class,
            PurchasesTableSeeder::class,
            SaleDetailsTableSeeder::class,
            PurchaseDetailsTableSeeder::class,
            MultiSubscriberDataSeeder::class,
            InvoiceTermTemplatesSeeder::class,
        ]);
    }
}
