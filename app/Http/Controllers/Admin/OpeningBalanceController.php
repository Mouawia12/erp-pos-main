<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class OpeningBalanceController extends Controller
{
    public function index()
    {
        $journals = Journal::query()
            ->where('baseon_text', $this->baseonText())
            ->orderByDesc('id')
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                $q->where('journals.subscriber_id', $sub);
            })
            ->get();

        return view('admin.accounts.opening_balances', compact('journals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'account_id' => ['required', 'array'],
            'account_id.*' => ['required', 'integer'],
            'debit' => ['required', 'array'],
            'credit' => ['required', 'array'],
        ]);

        $accountIds = $validated['account_id'];
        $accounts = AccountsTree::query()
            ->whereIn('id', $accountIds)
            ->where('department', 1)
            ->when(Auth::user()->subscriber_id ?? null, function ($q, $sub) {
                if (Schema::hasColumn('accounts_trees', 'subscriber_id')) {
                    $q->where('subscriber_id', $sub);
                }
            })
            ->get();

        if ($accounts->count() !== count($accountIds)) {
            return back()->withInput()->withErrors([
                'account_id' => __('main.opening_balance_accounts_only') ?? 'Opening balances must use balance sheet accounts only.'
            ]);
        }

        $details = [];
        $totalDebit = 0;
        $totalCredit = 0;
        foreach ($validated['account_id'] as $index => $accountId) {
            $debit = (float) ($validated['debit'][$index] ?? 0);
            $credit = (float) ($validated['credit'][$index] ?? 0);
            if ($debit <= 0 && $credit <= 0) {
                continue;
            }
            $totalDebit += $debit;
            $totalCredit += $credit;
            $details[] = [
                'account_id' => $accountId,
                'credit' => $credit,
                'debit' => $debit,
                'ledger_id' => 0,
                'notes' => '',
            ];
        }

        if (empty($details)) {
            return back()->withInput()->withErrors([
                'account_id' => __('main.opening_balance_empty') ?? 'Add at least one opening balance line.'
            ]);
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()->withInput()->withErrors([
                'debit' => __('main.opening_balance_not_balanced') ?? 'Opening balances must be balanced.'
            ]);
        }

        $header = [
            'branch_id' => Auth::user()?->branch_id ?? 0,
            'date' => $validated['date'],
            'basedon_no' => 'OB-' . now()->format('YmdHis'),
            'basedon_id' => 0,
            'baseon_text' => $this->baseonText(),
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => $request->notes ?? '',
        ];

        $systemController = new SystemController();
        $systemController->insertJournal($header, $details, 0);

        return redirect()->route('opening_balances.index')->with('success', __('main.created'));
    }

    public function searchAccounts($code)
    {
        $single = $this->getSingleAccount($code);
        if ($single) {
            return response()->json([$single]);
        }

        $query = AccountsTree::query()
            ->where('department', 1)
            ->where(function ($q) use ($code) {
                $q->where('code', 'like', '%' . $code . '%')
                    ->orWhere('name', 'like', '%' . $code . '%');
            })
            ->limit(5);

        if (Schema::hasColumn('accounts_trees', 'is_active')) {
            $query->where('is_active', 1);
        }

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('accounts_trees', 'subscriber_id')) {
                $query->where('subscriber_id', Auth::user()->subscriber_id);
            }
        }

        return response()->json($query->get());
    }

    private function getSingleAccount($code): ?AccountsTree
    {
        $query = AccountsTree::query()
            ->where('department', 1)
            ->where(function ($q) use ($code) {
                $q->where('code', '=', $code)
                    ->orWhere('name', '=', $code);
            });

        if (Schema::hasColumn('accounts_trees', 'is_active')) {
            $query->where('is_active', 1);
        }

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('accounts_trees', 'subscriber_id')) {
                $query->where('subscriber_id', Auth::user()->subscriber_id);
            }
        }

        return $query->first();
    }

    private function baseonText(): string
    {
        return 'Opening Balance';
    }
}
