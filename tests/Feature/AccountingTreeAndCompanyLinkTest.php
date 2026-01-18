<?php

namespace Tests\Feature;

use App\Models\AccountsTree;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountingTreeAndCompanyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_tree_nests_children()
    {
        $root = AccountsTree::create([
            'code' => '1000',
            'name' => 'Root',
            'type' => 0,
            'parent_id' => 0,
            'parent_code' => '',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $child = AccountsTree::create([
            'code' => '1100',
            'name' => 'Child',
            'type' => 1,
            'parent_id' => $root->id,
            'parent_code' => $root->code,
            'level' => 2,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $grandChild = AccountsTree::create([
            'code' => '1110',
            'name' => 'Grand Child',
            'type' => 1,
            'parent_id' => $child->id,
            'parent_code' => $child->code,
            'level' => 3,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $accounts = AccountsTree::orderBy('id')->get();
        $tree = AccountsTree::buildTree($accounts);

        $this->assertCount(1, $tree);
        $this->assertEquals($root->id, $tree->first()->id);
        $this->assertCount(1, $tree->first()->children);
        $this->assertEquals($child->id, $tree->first()->children->first()->id);
        $this->assertCount(1, $tree->first()->children->first()->children);
        $this->assertEquals($grandChild->id, $tree->first()->children->first()->children->first()->id);
    }

    public function test_ensure_account_creates_and_links_customer_account()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.test',
            'password' => Hash::make('password'),
            'branch_id' => 1,
            'phone_number' => '555',
            'profile_pic' => '',
            'role_name' => 'admin',
            'status' => 1,
            'subscriber_id' => 1,
        ]);
        $this->actingAs($user);

        $parent = AccountsTree::create([
            'code' => '1107',
            'name' => 'Customers',
            'type' => 1,
            'parent_id' => 0,
            'parent_code' => '',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $company = Company::create([
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Acme',
            'company' => 'Acme',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'info@example.test',
            'phone' => '555',
            'invoice_footer' => '',
            'logo' => '',
            'award_points' => 0,
            'deposit_amount' => 0,
            'opening_balance' => 0,
            'account_id' => 0,
            'credit_amount' => 0,
            'stop_sale' => 0,
            'representative_id_' => 0,
            'user_id' => $user->id,
            'status' => 0,
        ]);

        $account = $company->ensureAccount();

        $this->assertNotNull($account);
        $this->assertEquals($parent->id, $account->parent_id);
        $this->assertEquals($account->id, $company->fresh()->account_id);
        $this->assertEquals('Acme', $account->name);
    }

    public function test_ensure_account_reuses_existing_account()
    {
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'user2@example.test',
            'password' => Hash::make('password'),
            'branch_id' => 1,
            'phone_number' => '555',
            'profile_pic' => '',
            'role_name' => 'admin',
            'status' => 1,
            'subscriber_id' => 1,
        ]);
        $this->actingAs($user);

        $parent = AccountsTree::create([
            'code' => '1107',
            'name' => 'Customers',
            'type' => 1,
            'parent_id' => 0,
            'parent_code' => '',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $existing = AccountsTree::create([
            'code' => '110701',
            'name' => 'Acme',
            'type' => 3,
            'parent_id' => $parent->id,
            'parent_code' => $parent->code,
            'level' => 2,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $company = Company::create([
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Acme',
            'company' => 'Acme',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'info@example.test',
            'phone' => '555',
            'invoice_footer' => '',
            'logo' => '',
            'award_points' => 0,
            'deposit_amount' => 0,
            'opening_balance' => 0,
            'account_id' => 0,
            'credit_amount' => 0,
            'stop_sale' => 0,
            'representative_id_' => 0,
            'user_id' => $user->id,
            'status' => 0,
        ]);

        $account = $company->ensureAccount();

        $this->assertEquals($existing->id, $account->id);
        $this->assertEquals($existing->id, $company->fresh()->account_id);
        $this->assertEquals(1, AccountsTree::where('parent_id', $parent->id)->where('name', 'Acme')->count());
    }
}
