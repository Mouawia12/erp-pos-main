<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SalonDepartment;
use App\Models\SalonReservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalonReservationController extends Controller
{
    public function index()
    {
        $reservations = SalonReservation::with(['customer', 'department', 'assignedUser'])
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderByDesc('reservation_time')
            ->get();

        $customers = Company::query()
            ->where('group_id', 3)
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $departments = SalonDepartment::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        return view('admin.salon.reservations', compact('reservations', 'customers', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:companies,id'],
            'salon_department_id' => ['nullable', 'exists:salon_departments,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'reservation_time' => ['required', 'date'],
            'location_text' => ['nullable', 'string', 'max:191'],
            'location_url' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        SalonReservation::create([
            'customer_id' => $data['customer_id'],
            'salon_department_id' => $data['salon_department_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'reservation_time' => $data['reservation_time'],
            'location_text' => $data['location_text'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'notes' => $data['notes'] ?? null,
            'subscriber_id' => Auth::user()->subscriber_id,
            'branch_id' => Auth::user()->branch_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('salon.reservations')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function update(Request $request, $id)
    {
        $reservation = SalonReservation::findOrFail($id);

        $data = $request->validate([
            'customer_id' => ['required', 'exists:companies,id'],
            'salon_department_id' => ['nullable', 'exists:salon_departments,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'reservation_time' => ['required', 'date'],
            'location_text' => ['nullable', 'string', 'max:191'],
            'location_url' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:scheduled,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        $reservation->update([
            'customer_id' => $data['customer_id'],
            'salon_department_id' => $data['salon_department_id'] ?? null,
            'assigned_user_id' => $data['assigned_user_id'] ?? null,
            'reservation_time' => $data['reservation_time'],
            'location_text' => $data['location_text'] ?? null,
            'location_url' => $data['location_url'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('salon.reservations')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $reservation = SalonReservation::findOrFail($id);
        $reservation->delete();

        return redirect()->route('salon.reservations')->with('success', __('main.deleted') ?? 'تم حذف البيانات بنجاح');
    }
}
