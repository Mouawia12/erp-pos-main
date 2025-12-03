<?php

namespace App\Http\Middleware;

use App\Models\Subscriber;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin-web')->user();

        if ($user && $user->subscriber_id) {
            $subscriber = Subscriber::find($user->subscriber_id);

            if ($subscriber) {
                $subscriber->refreshLifecycleStatus();

                if ($subscriber->status === 'expired') {
                    if ($request->expectsJson()) {
                        abort(402, __('اشتراك هذا الحساب منتهٍ، الرجاء التواصل مع المالك.'));
                    }

                    return response()->view('admin.subscribers.expired', [
                        'subscriber' => $subscriber,
                    ], 402);
                }
            }
        }

        return $next($request);
    }
}
