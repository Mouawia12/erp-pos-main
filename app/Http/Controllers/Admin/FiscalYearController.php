<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
