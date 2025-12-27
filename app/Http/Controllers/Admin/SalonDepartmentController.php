<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalonDepartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalonDepartmentController extends Controller
{
    public function index()
    {
        $departments = SalonDepartment::with('users')
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        return view('admin.salon.departments', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:0,1'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer', 'exists:users,id'],
        ]);

        $department = SalonDepartment::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 1,
            'branch_id' => Auth::user()->branch_id,
            'subscriber_id' => Auth::user()->subscriber_id,
        ]);

        if (!empty($data['users'])) {
            $department->users()->sync($data['users']);
        }

        return redirect()->route('salon.departments')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function update(Request $request, $id)
    {
        $department = SalonDepartment::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:0,1'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer', 'exists:users,id'],
        ]);

        $department->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 1,
        ]);

        $department->users()->sync($data['users'] ?? []);

        return redirect()->route('salon.departments')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $department = SalonDepartment::findOrFail($id);
        $department->users()->detach();
        $department->delete();

        return redirect()->route('salon.departments')->with('success', __('main.deleted') ?? 'تم حذف البيانات بنجاح');
    }
}
