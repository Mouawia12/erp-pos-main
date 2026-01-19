<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\Company;
use App\Models\Journal;
use App\Models\VendorMovement;
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

        $journal = Journal::query()
            ->where('basedon_no', $header['basedon_no'])
            ->where('baseon_text', $this->baseonText())
            ->first();

        foreach ($details as $detail) {
            $company = Company::query()
                ->where('account_id', $detail['account_id'])
                ->whereIn('group_id', [3, 4])
                ->first();
            if (! $company) {
                continue;
            }

            VendorMovement::create([
                'vendor_id' => $company->id,
                'paid' => 0,
                'debit' => (float) $detail['debit'],
                'credit' => (float) $detail['credit'],
                'date' => $validated['date'],
                'invoice_type' => 'Opening_Balance',
                'invoice_id' => $journal?->id ?? 0,
                'invoice_no' => $header['basedon_no'],
                'paid_by' => '',
                'branch_id' => $header['branch_id'],
            ]);
        }

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

        $accounts = $query->get();
        $companyAccounts = $this->resolveCompanyAccounts($code);

        return response()->json(
            $accounts->merge($companyAccounts)->unique('id')->values()
        );
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

    private function resolveCompanyAccounts(string $code)
    {
        $query = Company::query()
            ->whereIn('group_id', [3, 4])
            ->where(function ($q) use ($code) {
                $q->where('name', 'like', '%' . $code . '%')
                    ->orWhere('company', 'like', '%' . $code . '%');
            })
            ->limit(5);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('companies', 'subscriber_id')) {
                $query->where('subscriber_id', Auth::user()->subscriber_id);
            }
        }

        return $query->get()
            ->map(function (Company $company) {
                return $company->ensureAccount();
            })
            ->filter();
    }

    private function baseonText(): string
    {
        return 'Opening Balance';
    }
}
