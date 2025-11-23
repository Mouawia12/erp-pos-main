<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountMovement;
use Illuminate\Http\Request;

class FinancialStatementController extends Controller
{
    public function incomeStatement(Request $request)
    {
        $start = $request->start_date ?? now()->startOfYear()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        $revenue = $this->sumByType('revenue', $start, $end);
        $cost = $this->sumByType('cost', $start, $end);
        $expenses = $this->sumByType('expense', $start, $end);

        $grossProfit = $revenue - $cost;
        $netProfit = $grossProfit - $expenses;

        return view('admin.finance.income_statement', compact('start','end','revenue','cost','expenses','grossProfit','netProfit'));
    }

    public function balanceSheet(Request $request)
    {
        $assets = $this->sumByType('asset');
        $liabilities = $this->sumByType('liability');
        $equity = $this->sumByType('equity');

        return view('admin.finance.balance_sheet', compact('assets','liabilities','equity'));
    }

    private function sumByType($type, $start = null, $end = null): float
    {
        $query = AccountsTree::where('type',$type)
            ->leftJoin('account_movements','account_movements.account_id','=','accounts_trees.id')
            ->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit');

        if($start && $end){
            $query->whereBetween('account_movements.date', [$start, $end]);
        }

        $row = $query->first();
        return (float)(($row->debit ?? 0) - ($row->credit ?? 0));
    }
}
