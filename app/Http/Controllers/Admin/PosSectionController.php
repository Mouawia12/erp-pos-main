<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosSectionController extends Controller
{
    public function index()
    {
        $sections = PosSection::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q, $sub) => $q->where('subscriber_id', $sub))
            ->orderBy('name')
            ->get();

        return view('admin.pos.sections', compact('sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:191'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        PosSection::create([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            'branch_id' => Auth::user()->branch_id,
            'subscriber_id' => Auth::user()->subscriber_id,
        ]);

        return redirect()->route('pos.sections')->with('success', __('main.created') ?? 'تم اضافة عنصر جديد بنجاح');
    }

    public function update(Request $request, PosSection $section)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:191'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        $section->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ]);

        return redirect()->route('pos.sections')->with('success', __('main.updated') ?? 'تم التعديل بنجاح');
    }

    public function destroy(PosSection $section)
    {
        $section->delete();

        return redirect()->route('pos.sections')->with('success', __('main.deleted') ?? 'تم حذف البيانات بنجاح');
    }
}
