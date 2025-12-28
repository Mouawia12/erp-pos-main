<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Models\AccountsTree;
use App\Models\AccountMovement;
use App\Models\AccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FiscalYearController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = FiscalYear::query();
        if (!empty($user?->subscriber_id)) {
            $query->where('subscriber_id', $user->subscriber_id);
        } else {
            $query->whereNull('subscriber_id');
        }

        $years = $query->orderBy('start_date', 'desc')->get();
        return view('admin.fiscal_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:191',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $subscriberId = $user?->subscriber_id;

        $overlap = FiscalYear::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->when(!$subscriberId, fn($q) => $q->whereNull('subscriber_id'))
            ->where('start_date', '<=', $validated['end_date'])
            ->where('end_date', '>=', $validated['start_date'])
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => __('main.fiscal_year_overlap')
            ]);
        }

        FiscalYear::create([
            'name' => $validated['name'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_closed' => false,
            'subscriber_id' => $subscriberId,
        ]);

        return redirect()->route('fiscal_years.index')->with('success', __('main.created'));
    }

    public function close(FiscalYear $fiscal_year)
    {
        $this->ensureAccess($fiscal_year);
        $fiscal_year->update(['is_closed' => true]);
        return back()->with('success', __('main.updated'));
    }

    public function closeWithEntries(FiscalYear $fiscal_year)
    {
        $this->ensureAccess($fiscal_year);
        $branchId = Auth::user()->branch_id ?? null;
        $subscriberId = Auth::user()->subscriber_id ?? null;

        $setting = AccountSetting::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->first();

        if (!$setting || empty($setting->profit_account)) {
            return back()->with('error', __('main.account_settings') . ': ' . __('validation.required', ['attribute' => __('main.profit_account')]));
        }

        $start = $fiscal_year->start_date;
        $end = $fiscal_year->end_date;

        $rows = AccountsTree::query()
            ->leftJoin('account_movements', 'account_movements.account_id', '=', 'accounts_trees.id')
            ->whereIn('accounts_trees.list', [3, 4]) // Income, Expenses
            ->whereBetween('account_movements.date', [$start, $end])
            ->groupBy('accounts_trees.id', 'accounts_trees.list')
            ->select('accounts_trees.id', 'accounts_trees.list')
            ->selectRaw('COALESCE(SUM(account_movements.debit),0) as debit')
            ->selectRaw('COALESCE(SUM(account_movements.credit),0) as credit')
            ->get();

        $details = [];
        $profitAccount = (int) $setting->profit_account;
        foreach ($rows as $row) {
            $net = (float) ($row->debit - $row->credit);
            if ($row->list == 3 && $net < 0) { // Income
                $amount = abs($net);
                $details[] = ['account_id' => $row->id, 'debit' => $amount, 'credit' => 0, 'notes' => 'إقفال دخل'];
                $details[] = ['account_id' => $profitAccount, 'debit' => 0, 'credit' => $amount, 'notes' => 'إقفال دخل'];
            }
            if ($row->list == 4 && $net > 0) { // Expenses
                $amount = $net;
                $details[] = ['account_id' => $row->id, 'debit' => 0, 'credit' => $amount, 'notes' => 'إقفال مصروف'];
                $details[] = ['account_id' => $profitAccount, 'debit' => $amount, 'credit' => 0, 'notes' => 'إقفال مصروف'];
            }
        }

        if (empty($details)) {
            return back()->with('error', __('main.no_data'));
        }

        $header = [
            'branch_id' => $branchId ?? 0,
            'date' => $end,
            'basedon_no' => 'FY-CLOSE-' . $fiscal_year->id,
            'basedon_id' => $fiscal_year->id,
            'baseon_text' => 'قيد إقفال السنة المالية',
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => '',
            'subscriber_id' => $subscriberId,
        ];

        $system = new SystemController();
        $system->insertJournal($header, $details, 1);

        $fiscal_year->update(['is_closed' => true]);
        return back()->with('success', __('main.updated'));
    }

    public function open(FiscalYear $fiscal_year)
    {
        $this->ensureAccess($fiscal_year);
        $fiscal_year->update(['is_closed' => false]);
        return back()->with('success', __('main.updated'));
    }

    private function ensureAccess(FiscalYear $fiscal_year): void
    {
        $user = Auth::user();
        if (!empty($user?->subscriber_id) && $fiscal_year->subscriber_id && $fiscal_year->subscriber_id !== $user->subscriber_id) {
            abort(403);
        }
    }
}
