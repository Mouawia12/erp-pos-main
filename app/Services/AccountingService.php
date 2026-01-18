<?php

namespace App\Services;

use App\Models\AccountSetting;
use App\Models\AccountsTree;
use App\Models\Company;
use App\Models\Expenses;
use App\Models\Journal;
use App\Models\JournalDetails;
use App\Models\Sales;
use App\Models\SubLedger;
use App\Models\SubLedgerEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    public function recordSale(int $saleId): bool
    {
        $saleInvoice = Sales::find($saleId);
        if (! $saleInvoice) {
            return false;
        }

        if ($saleInvoice->net < 0) {
            return $this->recordSaleReturn($saleInvoice);
        }

        $settings = AccountSetting::query()->where('branch_id', $saleInvoice->branch_id)->first();
        if (! $settings) {
            return false;
        }

        $headerData = [
            'branch_id' => $saleInvoice->branch_id,
            'cost_center_id' => $saleInvoice->cost_center_id ?? null,
            'date' => $saleInvoice->date,
            'basedon_no' => $saleInvoice->invoice_no,
            'basedon_id' => $saleId,
            'baseon_text' => 'فاتورة مبيعات',
            'total_debit' => 0,
            'total_credit' => 0,
            'notes' => '',
        ];

        $detailsData = [];

        if ($saleInvoice->discount > 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_discount_account, $saleInvoice->discount, 0, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->net > 0) {
            $remain = $saleInvoice->net;
            $controlAccount = $this->resolveControlAccountId($saleInvoice->customer_id, $saleInvoice->branch_id);
            if (! $controlAccount) {
                throw ValidationException::withMessages([
                    'journal' => 'لا يوجد حساب تحكم للعملاء. يرجى ضبطه في إعدادات الحسابات.',
                ]);
            }
            if (! $this->pushDetail($detailsData, $controlAccount, $remain, 0, $saleInvoice->customer_id, '')) {
                return false;
            }
        }

        if ($saleInvoice->total > 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_account, 0, $saleInvoice->total, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->tax > 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_tax_account, 0, $saleInvoice->tax, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->tax_excise > 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_tax_excise_account, 0, $saleInvoice->tax_excise, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->total > 0 && $settings->profit_account > 0 && $settings->cost_account > 0) {
            if (! $this->pushDetail($detailsData, $settings->profit_account, $saleInvoice->profit, 0, 0, '')) {
                return false;
            }

            if ($settings->reverse_profit_account > 0) {
                if (! $this->pushDetail($detailsData, $settings->reverse_profit_account, 0, $saleInvoice->profit, 0, '')) {
                    return false;
                }
            }

            if (! $this->pushDetail($detailsData, $settings->cost_account, $saleInvoice->total - $saleInvoice->profit, 0, 0, '')) {
                return false;
            }

            if (! $this->pushDetail($detailsData, $settings->stock_account, 0, $saleInvoice->total - $saleInvoice->profit, 0, '')) {
                return false;
            }
        }

        return $this->insertJournal($headerData, $detailsData);
    }

    public function recordExpense(int $expenseId): bool
    {
        $bill = Expenses::find($expenseId);
        if (! $bill) {
            return false;
        }

        $settings = AccountSetting::where('branch_id', $bill->branch_id)->first();
        if (! $settings) {
            return false;
        }

        $headerData = [
            'branch_id' => $bill->branch_id,
            'date' => $bill->date,
            'basedon_no' => $bill->docNumber,
            'basedon_id' => $expenseId,
            'baseon_text' => 'سند صرف',
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => '',
        ];

        $detailsData = [];
        $from_account = $this->resolveAccountId($bill->from_account);
        if (! $from_account) {
            return false;
        }

        $taxAccount = $settings->purchase_tax_account ?? $settings->sales_tax_account ?? null;
        $details = $bill->details()->get();
        if ($details->isEmpty()) {
            $to_account = $this->resolveAccountId($bill->to_account);
            if (! $to_account) {
                return false;
            }
            $details = collect([[
                'account_id' => $to_account,
                'amount' => $bill->amount,
                'tax_amount' => $bill->tax_amount ?? 0,
            ]]);
        }

        $taxAmount = (float) $details->sum('tax_amount');
        $totalOut = (float) $details->sum('amount') + $taxAmount;

        if (! $this->pushDetail($detailsData, $from_account, 0, $totalOut, 0, '')) {
            return false;
        }

        foreach ($details as $detail) {
            if (! $this->pushDetail($detailsData, $detail['account_id'], $detail['amount'], 0, 0, '')) {
                return false;
            }
            if ($taxAccount && ! empty($detail['tax_amount'])) {
                if (! $this->pushDetail($detailsData, $taxAccount, $detail['tax_amount'], 0, 0, 'ضريبة مصروف')) {
                    return false;
                }
            }
        }

        return $this->insertJournal($headerData, $detailsData);
    }

    public function reverseJournal(int $journalId, ?string $notes = null): ?Journal
    {
        $journal = Journal::find($journalId);
        if (! $journal) {
            return null;
        }

        if ($journal->status === Journal::STATUS_REVERSED) {
            return null;
        }

        if ($journal->status !== Journal::STATUS_POSTED) {
            throw ValidationException::withMessages([
                'journal' => 'لا يمكن عكس قيد غير مرحل.',
            ]);
        }

        $details = JournalDetails::withoutGlobalScope('subscriber')
            ->where('journal_id', $journalId)
            ->get();
        if ($details->isEmpty()) {
            throw ValidationException::withMessages([
                'journal' => 'لا يمكن عكس قيد بلا تفاصيل.',
            ]);
        }

        $header = [
            'branch_id' => $journal->branch_id,
            'cost_center_id' => $journal->cost_center_id ?? null,
            'date' => date('Y-m-d'),
            'basedon_no' => 'REV-' . $journal->id,
            'basedon_id' => $journal->id,
            'baseon_text' => 'عكس قيد: ' . $journal->baseon_text,
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => $notes ?? ('عكس القيد رقم ' . $journal->id),
            'reverses_journal_id' => $journal->id,
        ];

        $reversedDetails = [];
        foreach ($details as $detail) {
            $reversedDetails[] = [
                'account_id' => $detail->account_id,
                'debit' => (float) $detail->credit,
                'credit' => (float) $detail->debit,
                'ledger_id' => $detail->ledger_id ?? 0,
                'notes' => $detail->notes ?? '',
            ];
        }

        return DB::transaction(function () use ($journal, $header, $reversedDetails) {
            $posted = $this->insertJournal($header, $reversedDetails, 0, true);
            if (! $posted) {
                return null;
            }

            $reversal = Journal::query()
                ->where('basedon_no', $header['basedon_no'])
                ->where('basedon_id', $header['basedon_id'])
                ->where('baseon_text', $header['baseon_text'])
                ->first();

            if ($reversal) {
                $journal->update([
                    'status' => Journal::STATUS_REVERSED,
                    'reversed_journal_id' => $reversal->id,
                    'reversed_at' => now(),
                ]);
            }

            return $reversal;
        });
    }

    public function insertJournal(array $header, array $details, int $manual = 0, bool $allowPosted = false): bool
    {
        $header = $this->normalizeJournalHeader($header);
        $details = $this->normalizeJournalDetails($details);
        if (empty($details)) {
            return false;
        }

        $this->validateControlAccountUsage($header, $details);

        $totals = $this->calculateJournalTotals($details);
        $header['total_debit'] = $totals['total_debit'];
        $header['total_credit'] = $totals['total_credit'];
        $header['status'] = Journal::STATUS_DRAFT;

        return (bool) DB::transaction(function () use ($header, $details, $manual, $allowPosted) {
            if ($id = $this->getJournal($header)) {
                $journal = Journal::find($id);
                if (! $journal) {
                    return false;
                }
                if ($journal->status === Journal::STATUS_POSTED && ! $allowPosted) {
                    throw ValidationException::withMessages([
                        'journal' => 'لا يمكن تعديل قيد مرحل. استخدم العكس بدل التعديل.',
                    ]);
                }
                if ($journal->status === Journal::STATUS_REVERSED) {
                    throw ValidationException::withMessages([
                        'journal' => 'لا يمكن تعديل قيد معكوس.',
                    ]);
                }

                $journal->update($header);

                $oldDetails = JournalDetails::query()->where('journal_id', $id)->get();
                foreach ($oldDetails as $oldDetail) {
                    $this->updateAccountBalance($oldDetail->account_id, -1 * $oldDetail->credit, -1 * $oldDetail->debit, $header['date'], $id);
                }

                DB::table('journal_details')->where('journal_id', $id)->delete();
                DB::table('account_movements')->where('journal_id', $id)->delete();

                foreach ($details as $detail) {
                    $detail['journal_id'] = $id;
                    DB::table('journal_details')->insert($detail);
                    $this->updateAccountBalance($detail['account_id'], $detail['credit'], $detail['debit'], $header['date'], $id);
                }

                $this->recordSubLedgerEntries($id, $header, $details);
                $journal->update(['status' => Journal::STATUS_POSTED]);
                return true;
            }

            $journal_id = DB::table('journals')->insertGetId($header);
            foreach ($details as $detail) {
                $detail['journal_id'] = $journal_id;
                DB::table('journal_details')->insert($detail);
                $this->updateAccountBalance($detail['account_id'], $detail['credit'], $detail['debit'], $header['date'], $journal_id);
            }

            $this->recordSubLedgerEntries($journal_id, $header, $details);
            if ($manual === 1) {
                $journal = Journal::find($journal_id);
                if ($journal) {
                    $journal->update(['baseon_text' => 'سند قيد يدوي رقم ' . $journal_id]);
                }
            }

            DB::table('journals')->where('id', $journal_id)->update(['status' => Journal::STATUS_POSTED]);
            return true;
        });
    }

    private function recordSaleReturn(Sales $saleInvoice): bool
    {
        $settings = AccountSetting::query()->where('branch_id', $saleInvoice->branch_id)->first();
        if (! $settings) {
            return false;
        }

        $headerData = [
            'branch_id' => $saleInvoice->branch_id,
            'cost_center_id' => $saleInvoice->cost_center_id ?? null,
            'date' => $saleInvoice->date,
            'basedon_no' => $saleInvoice->invoice_no,
            'basedon_id' => $saleInvoice->id,
            'baseon_text' => 'مرتجع مبيعات',
            'total_debit' => 0,
            'total_credit' => 0,
            'notes' => '',
        ];

        $detailsData = [];

        if ($saleInvoice->discount != 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_discount_account, 0, $saleInvoice->discount * -1, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->net != 0) {
            $controlAccount = $this->resolveControlAccountId($saleInvoice->customer_id, $saleInvoice->branch_id);
            if (! $controlAccount) {
                throw ValidationException::withMessages([
                    'journal' => 'لا يوجد حساب تحكم للعملاء. يرجى ضبطه في إعدادات الحسابات.',
                ]);
            }
            if (! $this->pushDetail($detailsData, $controlAccount, 0, $saleInvoice->net * -1, $saleInvoice->customer_id, '')) {
                return false;
            }
        }

        if ($saleInvoice->total != 0) {
            if (! $this->pushDetail($detailsData, $settings->return_sales_account, $saleInvoice->total * -1, 0, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->tax != 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_tax_account, $saleInvoice->tax * -1, 0, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->tax_excise != 0) {
            if (! $this->pushDetail($detailsData, $settings->sales_tax_excise_account, $saleInvoice->tax_excise * -1, 0, 0, '')) {
                return false;
            }
        }

        if ($saleInvoice->total != 0 && $settings->profit_account > 0 && $settings->cost_account > 0) {
            if (! $this->pushDetail($detailsData, $settings->profit_account, 0, $saleInvoice->profit * -1, 0, '')) {
                return false;
            }

            if ($settings->reverse_profit_account > 0) {
                if (! $this->pushDetail($detailsData, $settings->reverse_profit_account, $saleInvoice->profit * -1, 0, 0, '')) {
                    return false;
                }
            }

            if (! $this->pushDetail($detailsData, $settings->cost_account, 0, ($saleInvoice->total - $saleInvoice->profit) * -1, 0, '')) {
                return false;
            }

            if (! $this->pushDetail($detailsData, $settings->stock_account, ($saleInvoice->total - $saleInvoice->profit) * -1, 0, 0, '')) {
                return false;
            }
        }

        return $this->insertJournal($headerData, $detailsData);
    }

    private function normalizeJournalHeader(array $header): array
    {
        if (! isset($header['branch_id'])) {
            $header['branch_id'] = Auth::user()?->branch_id ?? 0;
        }

        if (! isset($header['subscriber_id']) && Schema::hasColumn('journals', 'subscriber_id')) {
            $header['subscriber_id'] = Auth::user()?->subscriber_id;
        }

        if (! isset($header['date'])) {
            $header['date'] = date('Y-m-d');
        }

        return $header;
    }

    private function normalizeJournalDetails(array $details): array
    {
        $normalized = [];
        foreach ($details as $detail) {
            $accountId = $this->resolveAccountId($detail['account_id'] ?? null);
            if (! $accountId) {
                Log::warning('Accounting detail dropped: missing account', ['detail' => $detail]);
                continue;
            }

            $debit = (float) ($detail['debit'] ?? 0);
            $credit = (float) ($detail['credit'] ?? 0);
            if ($debit == 0.0 && $credit == 0.0) {
                continue;
            }

            $normalized[] = [
                'account_id' => $accountId,
                'debit' => $debit,
                'credit' => $credit,
                'ledger_id' => $detail['ledger_id'] ?? 0,
                'notes' => $detail['notes'] ?? '',
            ];
        }

        return $normalized;
    }

    private function calculateJournalTotals(array $details): array
    {
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($details as $detail) {
            $totalDebit += (float) ($detail['debit'] ?? 0);
            $totalCredit += (float) ($detail['credit'] ?? 0);
        }

        if (abs($totalDebit - $totalCredit) > 0.0001) {
            throw ValidationException::withMessages([
                'journal' => 'القيد غير متوازن: مجموع المدين يجب أن يساوي مجموع الدائن.',
            ]);
        }

        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    private function getJournal(array $data): int
    {
        $journal = Journal::query()
            ->where('basedon_no', $data['basedon_no'])
            ->where('basedon_id', $data['basedon_id'])
            ->where('baseon_text', $data['baseon_text'])
            ->first();

        return $journal?->id ?? 0;
    }

    private function resolveAccountId($accountId): ?int
    {
        if (! $accountId) {
            return null;
        }

        $query = $this->accountQueryForSubscriber()->where('id', $accountId);
        if (Schema::hasColumn('accounts_trees', 'is_active')) {
            $query->where('is_active', 1);
        }

        $account = $query->first();
        return $account ? $account->id : null;
    }

    private function accountQueryForSubscriber()
    {
        $subscriberId = Auth::user()?->subscriber_id;

        return AccountsTree::withoutGlobalScope('subscriber')
            ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                $q->where(function ($query) use ($subscriberId) {
                    $query->whereNull('subscriber_id')
                        ->orWhere('subscriber_id', 0)
                        ->orWhere('subscriber_id', $subscriberId);
                });
            });
    }

    private function getCompanyAccountId($companyId): ?int
    {
        if (! $companyId) {
            return null;
        }

        $company = Company::find($companyId);
        if (! $company) {
            return null;
        }

        $account = $company->ensureAccount();
        return $account?->id;
    }

    private function pushDetail(array &$details, $accountId, $debit, $credit, $ledgerId = 0, $notes = ''): bool
    {
        $resolvedAccount = $this->resolveAccountId($accountId);
        if (! $resolvedAccount) {
            Log::warning('Accounting detail skipped: invalid account', [
                'account_id' => $accountId,
            ]);
            return false;
        }

        if ($ledgerId && $this->isControlAccount($resolvedAccount)) {
            $controlAccountId = $this->resolveControlAccountId($ledgerId, null);
            if ($controlAccountId) {
                $resolvedAccount = $controlAccountId;
            }
        }

        $debit = (float) $debit;
        $credit = (float) $credit;
        if ($debit == 0.0 && $credit == 0.0) {
            return true;
        }

        $details[] = [
            'account_id' => $resolvedAccount,
            'debit' => $debit,
            'credit' => $credit,
            'ledger_id' => $ledgerId ?? 0,
            'notes' => $notes ?? '',
        ];

        return true;
    }

    private function validateControlAccountUsage(array $header, array $details): void
    {
        $controlAccounts = $this->resolveControlAccountIdsForBranch($header['branch_id'] ?? null);
        if (empty($controlAccounts)) {
            return;
        }

        foreach ($details as $detail) {
            $accountId = (int) ($detail['account_id'] ?? 0);
            if (! $accountId) {
                continue;
            }
            if (in_array($accountId, $controlAccounts, true) && empty($detail['ledger_id'])) {
                throw ValidationException::withMessages([
                    'journal' => 'لا يمكن التسجيل مباشرة على حساب التحكم بدون بيانات عميل/مورد.',
                ]);
            }
        }
    }

    private function resolveControlAccountIdsForBranch(?int $branchId): array
    {
        $settingsQuery = AccountSetting::query();
        if ($branchId) {
            $settingsQuery->where('branch_id', $branchId);
        }
        $settings = $settingsQuery->first();

        $controlAccounts = [];
        $customerControl = (int) ($settings->customer_control_account ?? 0);
        $supplierControl = (int) ($settings->supplier_control_account ?? 0);

        if ($customerControl) {
            $controlAccounts[] = $customerControl;
        } else {
            $controlAccounts[] = $this->resolveAccountByCode('1107');
        }

        if ($supplierControl) {
            $controlAccounts[] = $supplierControl;
        } else {
            $controlAccounts[] = $this->resolveAccountByCode('2101');
        }

        return array_values(array_filter($controlAccounts));
    }

    private function resolveControlAccountId(int $companyId, ?int $branchId): ?int
    {
        $company = Company::find($companyId);
        if (! $company) {
            return null;
        }

        $settingsQuery = AccountSetting::query();
        if ($branchId) {
            $settingsQuery->where('branch_id', $branchId);
        }
        $settings = $settingsQuery->first();

        if ((int) $company->group_id === 3) {
            return (int) ($settings->customer_control_account ?? 0) ?: $this->resolveAccountByCode('1107');
        }
        if ((int) $company->group_id === 4) {
            return (int) ($settings->supplier_control_account ?? 0) ?: $this->resolveAccountByCode('2101');
        }

        return null;
    }

    private function resolveAccountByCode(string $code): ?int
    {
        $account = $this->accountQueryForSubscriber()->where('code', $code)->first();
        return $account?->id;
    }

    private function isControlAccount(int $accountId): bool
    {
        $controlIds = $this->resolveControlAccountIdsForBranch(null);
        return in_array($accountId, $controlIds, true);
    }

    private function recordSubLedgerEntries(int $journalId, array $header, array $details): void
    {
        $branchId = $header['branch_id'] ?? null;
        $date = $header['date'] ?? date('Y-m-d');
        $notes = $header['notes'] ?? null;

        $controlAccounts = $this->resolveControlAccountIdsForBranch($branchId);
        if (empty($controlAccounts)) {
            return;
        }

        $touchedControlAccounts = [];

        foreach ($details as $detail) {
            $ledgerId = (int) ($detail['ledger_id'] ?? 0);
            if (! $ledgerId) {
                continue;
            }

            $accountId = (int) ($detail['account_id'] ?? 0);
            if (! in_array($accountId, $controlAccounts, true)) {
                continue;
            }

            $company = Company::find($ledgerId);
            if (! $company) {
                continue;
            }

            $subLedger = SubLedger::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'control_account_id' => $accountId,
                    'branch_id' => $branchId,
                ],
                [
                    'type' => $company->group_id == 4 ? 'supplier' : 'customer',
                    'subscriber_id' => Auth::user()?->subscriber_id,
                ]
            );

            SubLedgerEntry::create([
                'sub_ledger_id' => $subLedger->id,
                'journal_id' => $journalId,
                'date' => $date,
                'debit' => (float) ($detail['debit'] ?? 0),
                'credit' => (float) ($detail['credit'] ?? 0),
                'notes' => $detail['notes'] ?? $notes,
                'branch_id' => $branchId,
                'subscriber_id' => Auth::user()?->subscriber_id,
            ]);

            $touchedControlAccounts[] = $accountId;
        }

        return;
    }

    private function logReconciliationWarning(int $controlAccountId, ?int $branchId): void
    {
        $subLedgerQuery = SubLedgerEntry::query()
            ->join('sub_ledgers', 'sub_ledgers.id', '=', 'sub_ledger_entries.sub_ledger_id')
            ->where('sub_ledgers.control_account_id', $controlAccountId);

        if ($branchId) {
            $subLedgerQuery->where('sub_ledgers.branch_id', $branchId);
        }

        $subTotals = $subLedgerQuery->selectRaw('COALESCE(SUM(sub_ledger_entries.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(sub_ledger_entries.credit),0) as credit')
            ->first();

        $accQuery = DB::table('account_movements')
            ->where('account_id', $controlAccountId);
        $accTotals = $accQuery->selectRaw('COALESCE(SUM(debit),0) as debit')
            ->selectRaw('COALESCE(SUM(credit),0) as credit')
            ->first();

        $subBalance = (float) ($subTotals->debit ?? 0) - (float) ($subTotals->credit ?? 0);
        $accBalance = (float) ($accTotals->debit ?? 0) - (float) ($accTotals->credit ?? 0);

        if (abs($subBalance - $accBalance) > 0.01) {
            return;
        }
    }

    private function updateAccountBalance($id, $credit, $debit, $date, $journalId): void
    {
        $account = $this->accountQueryForSubscriber()->where('id', $id)->first();
        if (! $account) {
            return;
        }

        if ($credit != 0 || $debit != 0) {
            $accountMData = [
                'journal_id' => $journalId,
                'account_id' => $id,
                'credit' => $credit,
                'debit' => $debit,
                'date' => $date,
            ];

            DB::table('account_movements')->insert($accountMData);
        }

        if ($account->parent_id > 0) {
            $this->updateAccountBalance($account->parent_id, $credit, $debit, $date, $journalId);
        }
    }
}
