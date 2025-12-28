<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosReservation;
use App\Models\PosSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosReservationController extends Controller
{
    public function index(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $sections = PosSection::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderBy('name')
            ->get();

        $query = PosReservation::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderByDesc('reservation_time');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('section_id')) {
            $query->where('pos_section_id', $request->section_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('reservation_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('reservation_time', '<=', $request->date_to);
        }

        $reservations = $query->limit(200)->get();

        return view('admin.pos.reservations', compact('reservations', 'sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:191'],
            'customer_phone' => ['nullable', 'string', 'max:191'],
            'reservation_time' => ['nullable', 'date'],
            'guests' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'max:50'],
            'pos_section_id' => ['nullable', 'integer'],
            'session_location' => ['nullable', 'string', 'max:191'],
            'session_type' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string'],
        ]);

        PosReservation::create([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'reservation_time' => $data['reservation_time'] ?? null,
            'guests' => $data['guests'] ?? null,
            'status' => $data['status'] ?? 'booked',
            'pos_section_id' => $data['pos_section_id'] ?? null,
            'session_location' => $data['session_location'] ?? null,
            'session_type' => $data['session_type'] ?? null,
            'notes' => $data['notes'] ?? null,
            'branch_id' => Auth::user()->branch_id,
            'subscriber_id' => Auth::user()->subscriber_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('pos.reservations')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function update(Request $request, PosReservation $reservation)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:191'],
            'customer_phone' => ['nullable', 'string', 'max:191'],
            'reservation_time' => ['nullable', 'date'],
            'guests' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'max:50'],
            'pos_section_id' => ['nullable', 'integer'],
            'session_location' => ['nullable', 'string', 'max:191'],
            'session_type' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string'],
        ]);

        $reservation->update([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'reservation_time' => $data['reservation_time'] ?? null,
            'guests' => $data['guests'] ?? null,
            'status' => $data['status'] ?? $reservation->status,
            'pos_section_id' => $data['pos_section_id'] ?? null,
            'session_location' => $data['session_location'] ?? null,
            'session_type' => $data['session_type'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('pos.reservations')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    public function destroy(PosReservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('pos.reservations')->with('success', __('main.deleted') ?? 'تم حذف البيانات بنجاح');
    }
}
