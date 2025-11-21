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

            if(empty($user->session_id)){
                $user->session_id = $currentSession;
                $user->save();
            }elseif($user->session_id !== $currentSession){
                Auth::guard('admin-web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->withErrors([
                    'email' => __('main.single_device_locked')
                ]);
            }
        }

        return $next($request);
    }
}
