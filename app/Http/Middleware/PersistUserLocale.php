<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersistUserLocale
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::guard('admin-web')->user() ?? Auth::user();
        if ($user && $user->preferred_locale !== app()->getLocale()) {
            $user->forceFill(['preferred_locale' => app()->getLocale()])->save();
        }

        return $response;
    }
}
