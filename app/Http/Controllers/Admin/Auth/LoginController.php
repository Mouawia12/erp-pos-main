<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SingleDeviceLoginService;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * @var SingleDeviceLoginService
     */
    private SingleDeviceLoginService $singleDeviceLoginService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(SingleDeviceLoginService $singleDeviceLoginService)
    {
        $this->singleDeviceLoginService = $singleDeviceLoginService;
        $this->middleware('guest:admin-web')->except('logout');
    }

    protected function loggedOut(Request $request)
    {
        Auth::guard('admin-web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }

    protected function guard()
    {
        return Auth::guard('admin-web');
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function showLoginForm(Request $request) {
        $guard = Auth::guard('admin-web');

        if ($guard->check()) {
            if ($request->boolean('switch')) {
                $guard->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return view('admin.auth.login')->with('status', __('تم تسجيل الخروج، يمكنك الآن تسجيل الدخول بحساب آخر.'));
            }

            return redirect()->route('admin.home');
        }

        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::guard('admin-web')->attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended('home');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    protected function authenticated(Request $request, $user)
    {
        $currentSession = $request->session()->getId();
        $request->session()->put('admin_web_recent_login', true);
        $request->session()->put('admin_web_session_handshake', $currentSession);

        $this->singleDeviceLoginService->ensureExclusiveWebSession(
            $request,
            $user,
            $request->input('password')
        );

        $locale = $user->preferred_locale ?? config('app.locale');
        $request->session()->put('locale', $locale);
        app()->setLocale($locale);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('admin-web')->user();
        Auth::guard('admin-web')->logout();

        if($user){
            $this->singleDeviceLoginService->releaseSessionClaim($user);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}
