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
            $handshakeSession = $request->session()->pull('admin_web_session_handshake');
            $isFreshLogin = $request->session()->pull('admin_web_recent_login', false);

            if (!$isFreshLogin && $handshakeSession && $handshakeSession === $currentSession) {
                $isFreshLogin = true;
            }

            if(empty($user->session_id)){
                $user->session_id = $currentSession;
                $user->save();
            }elseif($user->session_id !== $currentSession){
                if ($isFreshLogin) {
                    $this->invalidatePreviousSession($request, $user->session_id);
                    $user->session_id = $currentSession;
                    $user->save();
                } elseif (!$this->sessionExists($request, $user->session_id)) {
                    // Previous session is already gone, adopt the current session instead of forcing a logout loop.
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

    private function sessionExists(Request $request, ?string $sessionId): bool
    {
        if (!$sessionId) {
            return false;
        }

        try {
            $handler = $request->session()->getHandler();

            if (method_exists($handler, 'read')) {
                return !empty($handler->read($sessionId));
            }
        } catch (\Throwable $e) {
            // If we cannot read the session, assume it no longer exists.
        }

        return false;
    }
}
