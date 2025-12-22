<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FinancialReportController extends Controller
{
    public function trialBalance(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        $accounts = AccountsTree::select('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->leftJoin('account_movements', 'account_movements.account_id','=','accounts_trees.id')
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
            })
            ->whereBetween('account_movements.date', [$start, $end])
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit')
            ->orderBy('accounts_trees.code')
            ->get();

        return view('admin.finance.trial_balance', compact('accounts','start','end'));
    }

    public function generalLedger(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();
        $accountId = $request->account_id;

        $accounts = AccountsTree::query()
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
            })
            ->orderBy('code')
            ->get();

        $movements = AccountMovement::with('account')
            ->when($accountId, fn($q,$v)=>$q->where('account_id',$v))
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        return view('admin.finance.general_ledger', compact('movements','accounts','accountId','start','end'));
    }

    public function accountBalances(Request $request)
    {
        $accounts = AccountsTree::query()
            ->leftJoin('account_movements','account_movements.account_id','=','accounts_trees.id')
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('accounts_trees.is_active', 1);
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

        return view('admin.finance.account_balances', compact('accounts'));
    }
}
