<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountMovement;
use App\Models\Branch;
use App\Models\CostCenter;
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
}
