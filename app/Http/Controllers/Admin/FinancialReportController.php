<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountMovement;
use App\Models\AccountSetting;
use App\Models\Branch;
use App\Models\CostCenter;
use App\Models\SubLedgerEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FinancialReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $level = $request->level ? (int) $request->level : null;
        $branchId = (int) ($request->branch_id ?? 0);
        $costCenterId = (int) ($request->cost_center_id ?? 0);

        $maxLevel = (int) (AccountsTree::max('level') ?? 1);
        $subscriberId = auth()->user()->subscriber_id ?? null;
        $branches = Branch::where('status', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $accounts = AccountsTree::select('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.level')
            ->leftJoin('account_movements', 'account_movements.account_id','=','accounts_trees.id')
            ->leftJoin('journals', 'journals.id','=','account_movements.journal_id')
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
            })
            ->when($level, fn($q) => $q->where('accounts_trees.level', $level))
            ->whereBetween('account_movements.date', [$start, $end])
            ->when($branchId > 0, function ($q) use ($branchId) {
                $q->where('journals.branch_id', $branchId);
            })
            ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                $q->where('journals.cost_center_id', $costCenterId);
            })
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.level')
            ->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit')
            ->orderBy('accounts_trees.code')
            ->get();

        return view('admin.finance.trial_balance', compact('accounts','start','end','level','maxLevel','branches','costCenters','branchId','costCenterId'));
    }

    public function generalLedger(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $accountId = $request->account_id;
        $branchId = (int) ($request->branch_id ?? 0);
        $costCenterId = (int) ($request->cost_center_id ?? 0);

        $accounts = AccountsTree::query()
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
            })
            ->orderBy('code')
            ->get();

        $subscriberId = auth()->user()->subscriber_id ?? null;
        $branches = Branch::where('status', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $movements = AccountMovement::with('account')
            ->join('journals','journals.id','=','account_movements.journal_id')
            ->when($accountId, fn($q,$v)=>$q->where('account_id',$v))
            ->whereBetween('date', [$start, $end])
            ->when($branchId > 0, function ($q) use ($branchId) {
                $q->where('journals.branch_id', $branchId);
            })
            ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                $q->where('journals.cost_center_id', $costCenterId);
            })
            ->orderBy('date')
            ->get();

        return view('admin.finance.general_ledger', compact('movements','accounts','accountId','start','end','branches','costCenters','branchId','costCenterId'));
    }

    public function accountBalances(Request $request)
    {
        $branchId = (int) ($request->branch_id ?? 0);
        $costCenterId = (int) ($request->cost_center_id ?? 0);
        $subscriberId = auth()->user()->subscriber_id ?? null;
        $branches = Branch::where('status', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $accounts = AccountsTree::query()
            ->leftJoin('account_movements','account_movements.account_id','=','accounts_trees.id')
            ->leftJoin('journals','journals.id','=','account_movements.journal_id')
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
            })
            ->when($branchId > 0, function ($q) use ($branchId) {
                $q->where('journals.branch_id', $branchId);
            })
            ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                $q->where('journals.cost_center_id', $costCenterId);
            })
            ->select('accounts_trees.*')
            ->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit')
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.type','accounts_trees.parent_id','accounts_trees.parent_code','accounts_trees.level','accounts_trees.list','accounts_trees.department','accounts_trees.side')
            ->orderBy('accounts_trees.code')
            ->get()
            ->map(function($acc){
                $acc->balance = ($acc->debit ?? 0) - ($acc->credit ?? 0);
                return $acc;
            });

        return view('admin.finance.account_balances', compact('accounts','branches','costCenters','branchId','costCenterId'));
    }

    public function subLedgerReconciliation(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $branchId = (int) ($request->branch_id ?? 0);

        $subscriberId = auth()->user()->subscriber_id ?? null;
        $branches = Branch::where('status', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();

        $branchIds = $branchId > 0 ? collect([$branchId]) : $branches->pluck('id');

        $defaultCustomerControl = AccountsTree::where('code', '1107')->first();
        $defaultSupplierControl = AccountsTree::where('code', '2101')->first();

        $rows = [];
        foreach ($branchIds as $bid) {
            $settingsQuery = AccountSetting::query()->where('branch_id', $bid);
            if ($subscriberId && \Schema::hasColumn('account_settings', 'subscriber_id')) {
                $settingsQuery->where('subscriber_id', $subscriberId);
            }
            $settings = $settingsQuery->first();

            $customerControlId = (int) ($settings->customer_control_account ?? 0) ?: ($defaultCustomerControl->id ?? 0);
            $supplierControlId = (int) ($settings->supplier_control_account ?? 0) ?: ($defaultSupplierControl->id ?? 0);

            $controlAccounts = array_values(array_filter([
                ['id' => $customerControlId, 'type' => 'customers'],
                ['id' => $supplierControlId, 'type' => 'suppliers'],
            ], fn ($item) => ! empty($item['id'])));

            foreach ($controlAccounts as $control) {
                $account = AccountsTree::find($control['id']);
                if (! $account) {
                    continue;
                }

                $subTotalsQuery = SubLedgerEntry::query()
                    ->join('sub_ledgers', 'sub_ledgers.id', '=', 'sub_ledger_entries.sub_ledger_id')
                    ->where('sub_ledgers.control_account_id', $control['id'])
                    ->where('sub_ledgers.branch_id', $bid)
                    ->whereBetween('sub_ledger_entries.date', [$start, $end]);

                $subTotals = $subTotalsQuery->selectRaw('COALESCE(SUM(sub_ledger_entries.debit),0) as debit')
                    ->selectRaw('COALESCE(SUM(sub_ledger_entries.credit),0) as credit')
                    ->first();

                $accTotalsQuery = DB::table('account_movements')
                    ->join('journals', 'journals.id', '=', 'account_movements.journal_id')
                    ->where('account_movements.account_id', $control['id'])
                    ->whereBetween('account_movements.date', [$start, $end])
                    ->where('journals.branch_id', $bid);
                if ($subscriberId && \Schema::hasColumn('journals', 'subscriber_id')) {
                    $accTotalsQuery->where('journals.subscriber_id', $subscriberId);
                }
                $accTotals = $accTotalsQuery->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
                    ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit')
                    ->first();

                $subDebit = (float) ($subTotals->debit ?? 0);
                $subCredit = (float) ($subTotals->credit ?? 0);
                $accDebit = (float) ($accTotals->debit ?? 0);
                $accCredit = (float) ($accTotals->credit ?? 0);

                $subBalance = $subDebit - $subCredit;
                $accBalance = $accDebit - $accCredit;

                $rows[] = [
                    'branch' => optional($branches->firstWhere('id', $bid))->branch_name ?? ('#' . $bid),
                    'control_type' => $control['type'],
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'sub_debit' => $subDebit,
                    'sub_credit' => $subCredit,
                    'sub_balance' => $subBalance,
                    'acc_debit' => $accDebit,
                    'acc_credit' => $accCredit,
                    'acc_balance' => $accBalance,
                    'difference' => $subBalance - $accBalance,
                ];
            }
        }

        return view('admin.finance.sub_ledger_reconciliation', compact('rows', 'branches', 'branchId', 'start', 'end'));
    }
}
