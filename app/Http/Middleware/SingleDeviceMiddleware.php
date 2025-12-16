<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSettings;

class SingleDeviceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $settings = SystemSettings::first();
        $user = Auth::guard('admin-web')->user();

        if($settings && $settings->single_device_login && $user){
            $currentSession = $request->session()->getId();
            $isFreshLogin = $request->session()->pull('admin_web_recent_login', false);

            if(empty($user->session_id)){
                $user->session_id = $currentSession;
                $user->save();
            }elseif($user->session_id !== $currentSession){
                if ($isFreshLogin || Auth::guard('admin-web')->viaRemember()) {
                    $this->invalidatePreviousSession($request, $user->session_id);
                    $user->session_id = $currentSession;
                    $user->save();
                } else {
                    Auth::guard('admin-web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('admin.login')->withErrors([
                        'email' => __('main.single_device_locked')
                    ]);
                }
            }
        }

        return $next($request);
    }

    private function invalidatePreviousSession(Request $request, ?string $previousSessionId): void
    {
        if (!$previousSessionId) {
            return;
        }

        try {
            $handler = $request->session()->getHandler();

            if (method_exists($handler, 'destroy')) {
                $handler->destroy($previousSessionId);
            }
        } catch (\Throwable $e) {
            // If we cannot destroy the previous session, continue without blocking the request.
        }
    }
}
