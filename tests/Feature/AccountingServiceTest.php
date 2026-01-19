<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\SystemController;
use App\Models\AccountsTree;
use App\Models\AccountSetting;
use App\Models\Company;
use App\Models\Expenses;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\SubLedger;
use App\Models\SubLedgerEntry;
use App\Models\User;
use App\Models\Sales;
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

    private function createAccount(array $overrides = []): AccountsTree
    {
        $defaults = [
            'code' => '1000',
            'name' => 'Account',
            'type' => 2,
            'parent_id' => 0,
            'parent_code' => '0',
            'level' => 1,
            'list' => 1,
            'department' => 1,
            'side' => 1,
            'is_active' => 1,
        ];

        return AccountsTree::create(array_merge($defaults, $overrides));
    }

    private function seedAccounts(): array
    {
        $asset = $this->createAccount([
            'code' => '1100',
            'name' => 'Cash',
            'side' => 1,
        ]);

        $revenue = $this->createAccount([
            'code' => '4100',
            'name' => 'Sales',
            'list' => 3,
            'department' => 2,
            'side' => 2,
        ]);

        return [$asset, $revenue];
    }

    private function seedControlAccount(): AccountsTree
    {
        return $this->createAccount([
            'code' => '1107',
            'name' => 'Customers Control',
            'side' => 1,
        ]);
    }

    private function createAccountSetting(array $overrides = []): AccountSetting
    {
        $defaults = [
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
            'customer_control_account' => 0,
            'supplier_control_account' => 0,
        ];

        return AccountSetting::create(array_merge($defaults, $overrides));
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

    public function test_control_account_requires_ledger_id()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $control = $this->seedControlAccount();
        $revenue = $this->createAccount([
            'code' => '4101',
            'name' => 'Other Sales',
            'side' => 2,
        ]);

        $this->createAccountSetting([
            'customer_control_account' => $control->id,
        ]);

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'CTRL-1',
            'basedon_id' => 20,
            'baseon_text' => 'قيد تحكم',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $control->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
        ];

        $this->expectException(ValidationException::class);
        $service->insertJournal($header, $details);
    }

    public function test_record_sale_creates_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $control = $this->seedControlAccount();
        $salesAccount = $this->createAccount([
            'code' => '4100',
            'name' => 'Sales',
            'side' => 2,
        ]);

        $this->createAccountSetting([
            'sales_account' => $salesAccount->id,
            'customer_control_account' => $control->id,
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

        $sale = Sales::create([
            'date' => '2025-01-01',
            'invoice_no' => 'S-100',
            'customer_id' => $company->id,
            'customer_name' => 'Client A',
            'customer_phone' => '555',
            'biller_id' => 1,
            'warehouse_id' => 1,
            'total' => 100,
            'discount' => 0,
            'tax' => 0,
            'tax_excise' => 0,
            'net' => 100,
            'paid' => 0,
            'sale_status' => 'completed',
            'payment_status' => 'unpaid',
            'created_by' => $user->id,
            'pos' => 0,
            'lista' => 0,
            'profit' => 0,
            'note' => '',
            'branch_id' => 1,
            'user_id' => $user->id,
            'status' => 0,
            'subscriber_id' => 1,
        ]);

        $service = new AccountingService();
        $this->assertTrue($service->recordSale($sale->id));

        $journal = Journal::where('basedon_no', 'S-100')->first();
        $this->assertNotNull($journal);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
        $this->assertEquals($control->id, SubLedger::first()->control_account_id);
    }

    public function test_record_sale_return_creates_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $control = $this->seedControlAccount();
        $returnSalesAccount = $this->createAccount([
            'code' => '4200',
            'name' => 'Sales Returns',
            'side' => 1,
        ]);

        $this->createAccountSetting([
            'return_sales_account' => $returnSalesAccount->id,
            'customer_control_account' => $control->id,
        ]);

        $company = Company::create([
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Client B',
            'company' => 'Client B',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'clientb@example.test',
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

        $sale = Sales::create([
            'date' => '2025-01-02',
            'invoice_no' => 'S-101',
            'customer_id' => $company->id,
            'customer_name' => 'Client B',
            'customer_phone' => '555',
            'biller_id' => 1,
            'warehouse_id' => 1,
            'total' => -100,
            'discount' => 0,
            'tax' => 0,
            'tax_excise' => 0,
            'net' => -100,
            'paid' => 0,
            'sale_status' => 'returned',
            'payment_status' => 'unpaid',
            'created_by' => $user->id,
            'pos' => 0,
            'lista' => 0,
            'profit' => 0,
            'note' => '',
            'branch_id' => 1,
            'user_id' => $user->id,
            'status' => 0,
            'subscriber_id' => 1,
        ]);

        $service = new AccountingService();
        $this->assertTrue($service->recordSale($sale->id));

        $journal = Journal::where('basedon_no', 'S-101')->first();
        $this->assertNotNull($journal);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
    }

    public function test_record_expense_posts_journal()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $cash = $this->createAccount([
            'code' => '1100',
            'name' => 'Cash',
            'side' => 1,
        ]);
        $expenseAccount = $this->createAccount([
            'code' => '5100',
            'name' => 'Expense',
            'side' => 1,
        ]);

        $this->createAccountSetting();

        $expense = Expenses::create([
            'branch_id' => 1,
            'from_account' => $cash->id,
            'to_account' => $expenseAccount->id,
            'client' => 'Vendor',
            'amount' => 50,
            'tax_amount' => 0,
            'notes' => 'Office supplies',
            'date' => '2025-01-01',
            'docNumber' => 'EXP-1',
            'payment_type' => 0,
            'user_id' => $user->id,
            'subscriber_id' => 1,
        ]);

        $service = new AccountingService();
        $this->assertTrue($service->recordExpense($expense->id));

        $journal = Journal::where('basedon_no', 'EXP-1')->first();
        $this->assertNotNull($journal);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);
        $this->assertEquals(50, (float) $journal->total_debit);
        $this->assertEquals(50, (float) $journal->total_credit);
    }

    public function test_posted_journal_cannot_be_modified()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();

        $service = new AccountingService();
        $header = [
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'TST-4',
            'basedon_id' => 4,
            'baseon_text' => 'قيد ثابت',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 100, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
        ];

        $this->assertTrue($service->insertJournal($header, $details));

        $this->expectException(ValidationException::class);
        $service->insertJournal($header, [
            ['account_id' => $asset->id, 'debit' => 150, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 150],
        ]);
    }

    public function test_reverse_requires_posted_journal()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $journal = Journal::create([
            'branch_id' => 1,
            'date' => '2025-01-01',
            'basedon_no' => 'DRAFT-1',
            'basedon_id' => 5,
            'baseon_text' => 'قيد مؤقت',
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => '',
            'status' => Journal::STATUS_DRAFT,
        ]);

        $service = new AccountingService();
        $this->expectException(ValidationException::class);
        $service->reverseJournal($journal->id);
    }

    public function test_purchase_invoice_creates_control_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $supplierControl = $this->createAccount([
            'code' => '2101',
            'name' => 'Suppliers Control',
            'side' => 2,
        ]);
        $purchaseAccount = $this->createAccount([
            'code' => '5100',
            'name' => 'Purchases',
            'side' => 1,
        ]);
        $stockAccount = $this->createAccount([
            'code' => '1200',
            'name' => 'Inventory',
            'side' => 1,
        ]);
        $costAccount = $this->createAccount([
            'code' => '4101',
            'name' => 'Cost of Sales',
            'side' => 2,
        ]);

        $this->createAccountSetting([
            'purchase_account' => $purchaseAccount->id,
            'stock_account' => $stockAccount->id,
            'cost_account' => $costAccount->id,
            'supplier_control_account' => $supplierControl->id,
        ]);

        $supplier = Company::create([
            'group_id' => 4,
            'group_name' => 'supplier',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Vendor A',
            'company' => 'Vendor A',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'vendor@example.test',
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

        $purchase = Purchase::create([
            'date' => '2025-01-03',
            'invoice_no' => 'P-100',
            'customer_id' => $supplier->id,
            'biller_id' => 1,
            'warehouse_id' => 1,
            'note' => '',
            'total' => 100,
            'discount' => 0,
            'tax' => 0,
            'net' => 100,
            'paid' => 0,
            'purchase_status' => 'received',
            'payment_status' => 'unpaid',
            'created_by' => $user->id,
            'branch_id' => 1,
            'user_id' => $user->id,
            'status' => 0,
            'subscriber_id' => 1,
        ]);

        $controller = app(SystemController::class);
        $controller->purchaseJournals($purchase->id);

        $journal = Journal::where('basedon_no', 'P-100')->first();
        $this->assertNotNull($journal);
        $this->assertEquals('فاتورة مشتريات', $journal->baseon_text);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);
        $this->assertEquals(200, (float) $journal->total_debit);
        $this->assertEquals(200, (float) $journal->total_credit);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
        $this->assertEquals($supplierControl->id, SubLedger::first()->control_account_id);
    }

    public function test_purchase_return_creates_control_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $supplierControl = $this->createAccount([
            'code' => '2101',
            'name' => 'Suppliers Control',
            'side' => 2,
        ]);
        $returnPurchase = $this->createAccount([
            'code' => '5200',
            'name' => 'Purchase Returns',
            'side' => 2,
        ]);
        $stockAccount = $this->createAccount([
            'code' => '1200',
            'name' => 'Inventory',
            'side' => 1,
        ]);
        $costAccount = $this->createAccount([
            'code' => '4101',
            'name' => 'Cost of Sales',
            'side' => 2,
        ]);

        $this->createAccountSetting([
            'return_purchase_account' => $returnPurchase->id,
            'stock_account' => $stockAccount->id,
            'cost_account' => $costAccount->id,
            'supplier_control_account' => $supplierControl->id,
        ]);

        $supplier = Company::create([
            'group_id' => 4,
            'group_name' => 'supplier',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Vendor B',
            'company' => 'Vendor B',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'vendorb@example.test',
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

        $purchase = Purchase::create([
            'date' => '2025-01-04',
            'invoice_no' => 'P-101',
            'customer_id' => $supplier->id,
            'biller_id' => 1,
            'warehouse_id' => 1,
            'note' => '',
            'total' => -100,
            'discount' => 0,
            'tax' => 0,
            'net' => -100,
            'paid' => 0,
            'purchase_status' => 'returned',
            'payment_status' => 'unpaid',
            'created_by' => $user->id,
            'branch_id' => 1,
            'user_id' => $user->id,
            'status' => 0,
            'subscriber_id' => 1,
        ]);

        $controller = app(SystemController::class);
        $controller->purchaseJournals($purchase->id);

        $journal = Journal::where('basedon_no', 'P-101')->first();
        $this->assertNotNull($journal);
        $this->assertEquals('مردود مشتريات', $journal->baseon_text);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
    }

    public function test_receipt_payment_creates_control_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $customerControl = $this->createAccount([
            'code' => '1107',
            'name' => 'Customers Control',
            'side' => 1,
        ]);
        $cash = $this->createAccount([
            'code' => '1100',
            'name' => 'Cash',
            'side' => 1,
        ]);

        $this->createAccountSetting([
            'safe_account' => $cash->id,
            'customer_control_account' => $customerControl->id,
        ]);

        $customer = Company::create([
            'group_id' => 3,
            'group_name' => 'customer',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Client C',
            'company' => 'Client C',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'clientc@example.test',
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

        $payment = Payment::create([
            'date' => '2025-01-05',
            'doc_number' => 'RCPT-1',
            'company_id' => $customer->id,
            'amount' => 75,
            'paid_by' => 'cash',
            'remain' => 0,
            'branch_id' => 1,
            'user_id' => $user->id,
            'subscriber_id' => 1,
        ]);

        $controller = app(SystemController::class);
        $controller->EnterMoneyAccounting($payment->id);

        $journal = Journal::where('basedon_no', 'RCPT-1')->first();
        $this->assertNotNull($journal);
        $this->assertEquals('مستند قبض', $journal->baseon_text);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
    }

    public function test_disbursement_payment_creates_control_sub_ledger_entry()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $supplierControl = $this->createAccount([
            'code' => '2101',
            'name' => 'Suppliers Control',
            'side' => 2,
        ]);
        $cash = $this->createAccount([
            'code' => '1100',
            'name' => 'Cash',
            'side' => 1,
        ]);

        $this->createAccountSetting([
            'safe_account' => $cash->id,
            'supplier_control_account' => $supplierControl->id,
        ]);

        $supplier = Company::create([
            'group_id' => 4,
            'group_name' => 'supplier',
            'customer_group_id' => 0,
            'customer_group_name' => '',
            'name' => 'Vendor C',
            'company' => 'Vendor C',
            'vat_no' => 'N/A',
            'address' => '',
            'city' => '',
            'state' => 'N/A',
            'postal_code' => '',
            'country' => '',
            'email' => 'vendorc@example.test',
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

        $payment = Payment::create([
            'date' => '2025-01-06',
            'doc_number' => 'PAY-1',
            'company_id' => $supplier->id,
            'amount' => 60,
            'paid_by' => 'cash',
            'remain' => 0,
            'branch_id' => 1,
            'user_id' => $user->id,
            'subscriber_id' => 1,
        ]);

        $controller = app(SystemController::class);
        $controller->ExitMoneyAccounting($payment->id);

        $journal = Journal::where('basedon_no', 'PAY-1')->first();
        $this->assertNotNull($journal);
        $this->assertEquals('مستند صرف', $journal->baseon_text);

        $this->assertEquals(1, SubLedger::count());
        $this->assertEquals(1, SubLedgerEntry::count());
    }

    public function test_manual_journal_updates_baseon_text()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();
        $this->createAccountSetting();

        $header = [
            'branch_id' => 1,
            'date' => '2025-01-07',
            'basedon_no' => 'MAN-1',
            'basedon_id' => 70,
            'baseon_text' => 'قيد يدوي',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 120, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 120],
        ];

        $controller = app(SystemController::class);
        $this->assertTrue($controller->insertJournal($header, $details, 1));

        $journal = Journal::where('basedon_no', 'MAN-1')->first();
        $this->assertNotNull($journal);
        $this->assertSame('سند قيد يدوي رقم ' . $journal->id, $journal->baseon_text);
    }

    public function test_opening_balance_posts_journal()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        [$asset, $revenue] = $this->seedAccounts();
        $this->createAccountSetting();

        $header = [
            'branch_id' => 1,
            'date' => '2025-01-08',
            'basedon_no' => 'OPEN-1',
            'basedon_id' => 80,
            'baseon_text' => 'رصيد افتتاحي',
            'notes' => '',
        ];
        $details = [
            ['account_id' => $asset->id, 'debit' => 300, 'credit' => 0],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 300],
        ];

        $controller = app(SystemController::class);
        $this->assertTrue($controller->insertJournal($header, $details, 0));

        $journal = Journal::where('basedon_no', 'OPEN-1')->first();
        $this->assertNotNull($journal);
        $this->assertEquals(Journal::STATUS_POSTED, $journal->status);
    }
}
