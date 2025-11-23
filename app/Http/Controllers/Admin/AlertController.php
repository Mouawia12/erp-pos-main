<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Services\AlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request, AlertService $alertService)
    {
        $alertService->sync();

        $alerts = Alert::query()
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->status, function ($q, $status) {
                if ($status === 'unread') {
                    $q->whereNull('read_at')->whereNull('resolved_at');
                } elseif ($status === 'resolved') {
                    $q->whereNotNull('resolved_at');
                } elseif ($status === 'open') {
                    $q->whereNull('resolved_at');
                }
            })
            ->latest()
            ->paginate(20)
            ->appends($request->only('type', 'status'));

        return view('admin.alerts.index', [
            'alerts' => $alerts,
            'filterType' => $request->type,
            'filterStatus' => $request->status,
        ]);
    }

    public function refresh(AlertService $alertService): RedirectResponse
    {
        $alertService->sync();

        return redirect()->route('alerts.index')->with('success', __('main.alerts_refreshed'));
    }

    public function markRead(Alert $alert): RedirectResponse
    {
        $alert->markRead();

        return back()->with('success', __('main.alert_marked_read'));
    }

    public function resolve(Alert $alert): RedirectResponse
    {
        $alert->resolve();

        return back()->with('success', __('main.alert_resolved'));
    }
}
