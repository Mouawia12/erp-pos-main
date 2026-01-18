<?php

namespace Tests\Feature;

use App\Models\AccountsTree;
use App\Models\AccountSetting;
use App\Models\Company;
use App\Models\Journal;
use App\Models\SubLedger;
use App\Models\SubLedgerEntry;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountingServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'accounting@example.test',
            'password' => Hash::make('password'),
            'branch_id' => 1,
            'phone_number' => '555',
            'profile_pic' => '',
            'role_name' => 'admin',
            'status' => 1,
            'subscriber_id' => 1,
        ]);
    }

    private function seedAccounts(): array
    {
        $asset = AccountsTree::create([
            'code' => '1100',
            'name' => 'Cash',
            'type' => 2,
            'parent_id' => 0,
            'parent_code' => '0',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);

        $revenue = AccountsTree::create([
            'code' => '4100',
            'name' => 'Sales',
            'type' => 2,
            'parent_id' => 0,
            'parent_code' => '0',
            'level' => 1,
            'list' => 3,
            'department' => 2,
            'side' => 2,
            'is_active' => 1,
        ]);

        return [$asset, $revenue];
    }

    private function seedControlAccount(): AccountsTree
    {
        return AccountsTree::create([
            'code' => '1107',
            'name' => 'Customers Control',
            'type' => 2,
            'parent_id' => 0,
            'parent_code' => '0',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ]);
    }

    public function test_insert_journal_posts_after_success()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'TST-1',
            'basedon_id' => 1,
            'baseon_text' => 'اختبار قيد',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
        ];

        $result = $service->insertJournal($header, $details);
        $this->assertTrue($result);

        $journal = Journal::where('basedon_no', 'TST-1')->first();
        $this->assertNotNull($journal);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);
        $this->assertEquals(100, (float) $journal->total_debit);
        $this->assertEquals(100, (float) $journal->total_credit);
    }

    public function test_insert_journal_rejects_unbalanced_entries()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'TST-2',
            'basedon_id' => 2,
            'baseon_text' => 'قيد غير متوازن',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 90],
        ];

        $this->expectException(ValidationException::class);
        $service->insertJournal($header, $details);
    }

    public function test_reverse_journal_creates_reversal_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'TST-3',
            'basedon_id' => 3,
            'baseon_text' => 'قيد أصلي',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
        ];

        $service->insertJournal($header, $details);
        $original = Journal::where('basedon_no', 'TST-3')->first();
        $this->assertNotNull($original);

        $reversal = $service->reverseJournal($original->id, 'تصحيح القيد');
        $this->assertNotNull($reversal);

        $original->refresh();
        $this->assertEquals(Journal::STATUS_REVERSED, $original->status);
        $this->assertEquals($reversal->id, $original->reversed_journal_id);
        $this->assertEquals($original->id, $reversal->reverses_journal_id);
    }

    public function test_sub_ledger_entries_created_for_control_account()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $control = $this->seedControlAccount();

        AccountSetting::create([
            'warehouse_id' => 0,
            'safe_account' => 0,
            'bank_account' => 0,
            'sales_account' => 0,
            'purchase_account' => 0,
            'return_sales_account' => 0,
            'return_purchase_account' => 0,
            'stock_account' => 0,
            'sales_discount_account' => 0,
            'purchase_discount_account' => 0,
            'cost_account' => 0,
            'reverse_profit_account' => 0,
            'profit_account' => 0,
            'sales_tax_account' => 0,
            'purchase_tax_account' => 0,
            'sales_tax_excise_account' => 0,
            'branch_id' => 1,
            'customer_control_account' => $control->id,
            'supplier_control_account' => 0,
        ]);

        $company = Company::create([
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Client A',
            'company' => 'Client A',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'client@example.test',
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
            'subscriber_id' => 1,
        ]);

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'SL-1',
            'basedon_id' => 10,
            'baseon_text' => 'قيد عميل',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $control->id, 'debit' => 200, 'credit' => 0, 'ledger_id' => $company->id],
            ['account_id' => $control->id, 'debit' => 0, 'credit' => 200, 'ledger_id' => $company->id],
        ];

        $this->assertTrue($service->insertJournal($header, $details));

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(2, SubLedgerEntry::count());
    }
}
