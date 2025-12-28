<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CostCenterController extends Controller
{
    public function index()
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $centers = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderBy('name')
            ->get();

        return view('admin.cost_centers.index', compact('centers'));
    }

    public function store(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'name' => ['required', 'string', 'max:191'],
            'code' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        if (!empty($validated['id'])) {
            $center = CostCenter::query()
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->findOrFail($validated['id']);
            $center->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
                'is_active' => $request->input('is_active', 1),
            ]);
            return redirect()->route('cost_centers')->with('success', __('main.updated'));
        }

        CostCenter::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'is_active' => $request->input('is_active', 1),
            'subscriber_id' => $subscriberId,
        ]);

        return redirect()->route('cost_centers')->with('success', __('main.created'));
    }

    public function edit($id)
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $center = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);

        return response()->json($center);
    }

    public function destroy($id)
    {
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $center = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);
        $center->delete();

        return redirect()->route('cost_centers')->with('success', __('main.deleted'));
    }
}
