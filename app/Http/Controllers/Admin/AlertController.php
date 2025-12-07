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

        $alerts = Alert::forUser($request->user())
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
        $this->ensureAlertAccessible($alert);
        $alert->markRead();

        return back()->with('success', __('main.alert_marked_read'));
    }

    public function resolve(Alert $alert): RedirectResponse
    {
        $this->ensureAlertAccessible($alert);
        $alert->resolve();

        return back()->with('success', __('main.alert_resolved'));
    }

    private function ensureAlertAccessible(Alert $alert): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('system_owner')) {
            if (! is_null($alert->subscriber_id)) {
                abort(403);
            }
            return;
        }

        if ($user->subscriber_id != $alert->subscriber_id) {
            abort(403);
        }

        if ($user->branch_id && $alert->branch_id && $alert->branch_id != $user->branch_id) {
            abort(403);
        }
    }
}
