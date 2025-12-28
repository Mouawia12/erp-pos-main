<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PosShift;
use App\Models\SaleDetails;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosShiftController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscriberId = $user->subscriber_id ?? null;

        $currentShift = PosShift::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->orderByDesc('id')
            ->first();

        $summary = $currentShift ? $this->buildShiftSummary($currentShift) : null;

        $shifts = PosShift::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $siteController = new SystemController();
        $warehouses = $siteController->getAllWarehouses();

        return view('admin.pos.shifts', compact('currentShift', 'summary', 'shifts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $subscriberId = $user->subscriber_id ?? null;

        $existingShift = PosShift::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return redirect()->route('pos.shifts')
                ->with('error', __('main.shift_already_open') ?? 'يوجد شفت مفتوح بالفعل لهذا المستخدم.');
        }

        $data = $request->validate([
            'opening_cash' => ['nullable', 'numeric', 'min:0'],
            'warehouse_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        PosShift::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'subscriber_id' => $subscriberId,
            'opened_at' => now(),
            'opening_cash' => $data['opening_cash'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('pos.shifts')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function close(Request $request, PosShift $shift)
    {
        if ($shift->user_id && $shift->user_id !== Auth::id()) {
            return redirect()->route('pos.shifts')
                ->with('error', __('main.shift_not_allowed') ?? 'لا يمكنك إغلاق شفت لمستخدم آخر.');
        }
        if ($shift->status !== 'open') {
            return redirect()->route('pos.shifts')
                ->with('error', __('main.shift_already_closed') ?? 'تم إغلاق الشفت مسبقًا.');
        }

        $data = $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $summary = $this->buildShiftSummary($shift);

        $shift->update([
            'closing_cash' => $data['closing_cash'],
            'expected_cash' => $summary['cash_total'] ?? 0,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $data['notes'] ?? $shift->notes,
        ]);

        return redirect()->route('pos.shifts')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    private function buildShiftSummary(PosShift $shift): array
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;

        $salesQuery = Sales::query()
            ->where('pos_shift_id', $shift->id)
            ->where('pos', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId));

        $salesTotals = (clone $salesQuery)->selectRaw(
            'SUM(total) as total, SUM(tax) as tax, SUM(tax_excise) as tax_excise, SUM(net) as net, SUM(profit) as profit'
        )->first();

        $saleIds = (clone $salesQuery)->pluck('id');

        $quantityTotal = 0;
        if ($saleIds->isNotEmpty()) {
            $quantityTotal = SaleDetails::query()
                ->whereIn('sale_id', $saleIds)
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->selectRaw('SUM(quantity * COALESCE(unit_factor, 1)) as qty')
                ->value('qty');
        }

        $payments = Payment::query()
            ->whereIn('sale_id', $saleIds)
            ->selectRaw("SUM(CASE WHEN paid_by = 'cash' THEN amount ELSE 0 END) as cash_total")
            ->selectRaw("SUM(CASE WHEN paid_by = 'bank' THEN amount ELSE 0 END) as bank_total")
            ->selectRaw("SUM(CASE WHEN paid_by LIKE 'card:%' THEN amount ELSE 0 END) as card_total")
            ->first();

        $cashTotal = (float) ($payments->cash_total ?? 0);
        $bankTotal = (float) ($payments->bank_total ?? 0);
        $cardTotal = (float) ($payments->card_total ?? 0);

        return [
            'total' => (float) ($salesTotals->total ?? 0),
            'tax' => (float) ($salesTotals->tax ?? 0),
            'tax_excise' => (float) ($salesTotals->tax_excise ?? 0),
            'net' => (float) ($salesTotals->net ?? 0),
            'profit' => (float) ($salesTotals->profit ?? 0),
            'quantity' => (float) ($quantityTotal ?? 0),
            'cash_total' => $cashTotal,
            'bank_total' => $bankTotal,
            'card_total' => $cardTotal,
            'payments_total' => $cashTotal + $bankTotal + $cardTotal,
        ];
    }
}
