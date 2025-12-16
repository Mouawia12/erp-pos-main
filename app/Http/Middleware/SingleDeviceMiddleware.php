<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SingleDeviceLoginService;

class SingleDeviceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $singleDevice = app(SingleDeviceLoginService::class);
        $user = Auth::guard('admin-web')->user();
        if ($user && $user->exists) {
            $user->refresh();
        }

        if($user && $singleDevice->isEnabledFor($user)){
            $currentSession = $request->session()->getId();
            $handshakeSession = $request->session()->pull('admin_web_session_handshake');
            $isFreshLogin = $request->session()->pull('admin_web_recent_login', false);

            if (
                !$isFreshLogin
                && $handshakeSession
                && $handshakeSession === $currentSession
                && $user->session_id === $currentSession
            ) {
                $isFreshLogin = true;
            }

            if(empty($user->session_id)){
                $user->session_id = $currentSession;
                $user->save();
            }elseif($user->session_id !== $currentSession){
                if ($isFreshLogin) {
                    $singleDevice->invalidateStoredSession($user->session_id, $currentSession);
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

}
