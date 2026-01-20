<?php

namespace App\Console\Commands;

use App\Models\AccountSetting;
use App\Models\AccountsTree;
use App\Models\Company;
use App\Models\SubLedger;
use App\Models\SubLedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillSubLedgerEntries extends Command
{
    protected $signature = 'accounting:backfill-sub-ledger
        {--dry-run : Show counts without writing}
        {--from= : Start date (YYYY-MM-DD)}
        {--to= : End date (YYYY-MM-DD)}
        {--branch= : Limit to a specific branch id}';

    protected $description = 'Backfill sub ledger entries for existing posted journals.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $from = $this->option('from');
        $to = $this->option('to');
        $branchId = (int) ($this->option('branch') ?? 0);

        $query = DB::table('journal_details')
            ->join('journals', 'journals.id', '=', 'journal_details.journal_id')
            ->select(
                'journal_details.id',
                'journal_details.journal_id',
                'journal_details.account_id',
                'journal_details.debit',
                'journal_details.credit',
                'journal_details.ledger_id',
                'journal_details.notes',
                'journals.branch_id',
                'journals.date',
                'journals.subscriber_id',
                'journals.status'
            )
            ->where('journal_details.ledger_id', '>', 0);

        if ($branchId > 0) {
            $query->where('journals.branch_id', $branchId);
        }
        if ($from) {
            $query->where('journals.date', '>=', $from);
        }
        if ($to) {
            $query->where('journals.date', '<=', $to);
        }
        if (Schema::hasColumn('journals', 'status')) {
            $query->where('journals.status', 'posted');
        }

        $created = 0;
        $skipped = 0;
        $missingCompany = 0;
        $missingControl = 0;

        $query->orderBy('journal_details.id')
            ->chunkById(500, function ($rows) use ($dryRun, &$created, &$skipped, &$missingCompany, &$missingControl) {
                foreach ($rows as $row) {
                    $ledgerId = (int) $row->ledger_id;
                    if (! $ledgerId) {
                        $skipped++;
                        continue;
                    }

                    $company = Company::find($ledgerId);
                    if (! $company || ! in_array((int) $company->group_id, [3, 4], true)) {
                        $missingCompany++;
                        continue;
                    }

                    $controlIds = $this->resolveControlAccountIdsForBranch($row->branch_id, $row->subscriber_id);
                    if (! in_array((int) $row->account_id, $controlIds, true)) {
                        $missingControl++;
                        continue;
                    }

                    $subLedger = SubLedger::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'control_account_id' => (int) $row->account_id,
                            'branch_id' => $row->branch_id,
                        ],
                        [
                            'type' => (int) $company->group_id === 4 ? 'supplier' : 'customer',
                            'subscriber_id' => $row->subscriber_id,
                        ]
                    );

                    $exists = SubLedgerEntry::query()
                        ->where('sub_ledger_id', $subLedger->id)
                        ->where('journal_id', $row->journal_id)
                        ->where('debit', (float) $row->debit)
                        ->where('credit', (float) $row->credit)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    if (! $dryRun) {
                        SubLedgerEntry::create([
                            'sub_ledger_id' => $subLedger->id,
                            'journal_id' => $row->journal_id,
                            'date' => $row->date,
                            'debit' => (float) $row->debit,
                            'credit' => (float) $row->credit,
                            'notes' => $row->notes,
                            'branch_id' => $row->branch_id,
                            'subscriber_id' => $row->subscriber_id,
                        ]);
                    }

                    $created++;
                }
            });

        $this->info('Backfill complete.');
        $this->line('Created: ' . $created);
        $this->line('Skipped: ' . $skipped);
        $this->line('Missing company: ' . $missingCompany);
        $this->line('Missing control account: ' . $missingControl);
        if ($dryRun) {
            $this->warn('Dry run mode: no records were written.');
        }

        return self::SUCCESS;
    }

    private function resolveControlAccountIdsForBranch(?int $branchId, ?int $subscriberId): array
    {
        $settingsQuery = AccountSetting::query();
        if ($branchId) {
            $settingsQuery->where('branch_id', $branchId);
        }
        if ($subscriberId && Schema::hasColumn('account_settings', 'subscriber_id')) {
            $settingsQuery->where('subscriber_id', $subscriberId);
        }
        $settings = $settingsQuery->first();

        $controlAccounts = [];
        $customerControl = (int) ($settings->customer_control_account ?? 0);
        $supplierControl = (int) ($settings->supplier_control_account ?? 0);

        if ($customerControl) {
            $controlAccounts[] = $customerControl;
        } else {
            $controlAccounts[] = $this->resolveAccountByCode('1107', $subscriberId);
        }

        if ($supplierControl) {
            $controlAccounts[] = $supplierControl;
        } else {
            $controlAccounts[] = $this->resolveAccountByCode('2101', $subscriberId);
        }

        return array_values(array_filter($controlAccounts));
    }

    private function resolveAccountByCode(string $code, ?int $subscriberId): ?int
    {
        $query = AccountsTree::withoutGlobalScope('subscriber')->where('code', $code);
        if ($subscriberId !== null) {
            $query->where(function ($q) use ($subscriberId) {
                $q->whereNull('subscriber_id')
                    ->orWhere('subscriber_id', 0)
                    ->orWhere('subscriber_id', $subscriberId);
            });
        }
        $account = $query->first();
        return $account?->id;
    }

}
