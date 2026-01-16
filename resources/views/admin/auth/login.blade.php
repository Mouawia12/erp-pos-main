@extends('admin.layouts.app')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        .login-page {
            --ink: #0f172a;
            --muted: #64748b;
            --accent: #0f766e;
            --accent-2: #f97316;
            --surface: #ffffff;
            --border: rgba(15, 23, 42, 0.08);
            min-height: 100vh;
            background:
                radial-gradient(1200px 600px at 90% -10%, rgba(15, 118, 110, 0.18), transparent 55%),
                radial-gradient(900px 500px at -10% 100%, rgba(249, 115, 22, 0.16), transparent 60%),
                linear-gradient(135deg, #f8fafc 0%, #eef2f7 60%, #e6f0ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: "Cairo", "Segoe UI", Tahoma, sans-serif;
        }
        .login-grid {
            width: min(1100px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 28px;
        }
        .login-panel {
            position: relative;
            background: var(--surface);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
            border: 1px solid var(--border);
            animation: rise 0.7s ease both;
        }
        .login-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }
        .login-brand img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .login-brand h1 {
            font-size: 28px;
            margin: 0 0 6px 0;
            color: var(--ink);
            font-weight: 700;
        }
        .login-brand p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }
        .login-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .login-head h5 {
            margin: 0;
            font-size: 20px;
            color: var(--ink);
            font-weight: 700;
        }
        .login-head span {
            color: var(--accent);
            font-weight: 600;
            font-size: 13px;
            background: rgba(15, 118, 110, 0.12);
            padding: 6px 12px;
            border-radius: 999px;
        }
        .lang-switch {
            position: absolute;
            top: 18px;
            inset-inline-end: 18px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .lang-switch:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        }
        .login-card label {
            color: var(--ink);
            font-weight: 600;
            margin-bottom: 6px;
        }
        .login-card .form-control {
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 12px 14px;
            background: #f8fafc;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .login-card .form-control:focus {
            border-color: rgba(15, 118, 110, 0.5);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
            background: #ffffff;
        }
        .login-card .input-group-text {
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 12px 0 0 12px;
            cursor: pointer;
        }
        .login-card .btn-primary-modern {
            background: linear-gradient(135deg, var(--accent), #0d9488);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 10px 24px rgba(15, 118, 110, 0.25);
        }
        .login-card .btn-primary-modern:hover {
            background: linear-gradient(135deg, #0d9488, #0f766e);
        }
        .login-visual {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            min-height: 540px;
            color: #ffffff;
            background:
                linear-gradient(140deg, rgba(15, 118, 110, 0.95), rgba(2, 132, 199, 0.9)),
                radial-gradient(400px 200px at 80% 20%, rgba(249, 115, 22, 0.6), transparent 70%);
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            animation: float-in 0.8s ease both;
        }
        .login-visual::before,
        .login-visual::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            filter: blur(0.5px);
        }
        .login-visual::before {
            width: 220px;
            height: 220px;
            top: -60px;
            left: -40px;
        }
        .login-visual::after {
            width: 280px;
            height: 280px;
            bottom: -120px;
            right: -80px;
        }
        .login-visual h2 {
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .login-visual p {
            margin: 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
        }
        .visual-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: center;
        }
        .visual-images img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            padding: 12px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.2);
        }
        .login-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--muted);
        }
        .login-footer .form-check-label {
            color: var(--muted);
        }
        .login-footer a {
            color: var(--accent);
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
        @keyframes rise {
            from { transform: translateY(18px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes float-in {
            from { transform: translateY(24px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 991px) {
            .login-grid {
                grid-template-columns: 1fr;
            }
            .login-visual {
                min-height: 360px;
            }
        }
        @media (max-width: 575px) {
            .login-panel {
                padding: 28px 22px;
            }
            .login-brand {
                flex-direction: column;
                align-items: flex-start;
            }
            .visual-images {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="login-page">
        <div class="login-grid">
            <div class="login-panel">
                @php
                    $currentLocale = app()->getLocale();
                    $toggleLocale = $currentLocale === 'ar' ? 'en' : 'ar';
                @endphp
                <a class="lang-switch" rel="alternate" hreflang="{{ $toggleLocale }}"
                   href="{{ LaravelLocalization::getLocalizedURL($toggleLocale, null, [], true) }}"
                   title="{{ $toggleLocale === 'ar' ? 'العربية' : 'English' }}">
                    <i class="fa fa-globe"></i>
                </a>
                <div class="login-brand">
                    <a href="{{route('index')}}">
                        <img src="{{URL::asset('assets/img/logo.png')}}" alt="logo">
                    </a>
                    <div>
                        <h1>{{env('APP_NAME')}}</h1>
                        <p>{{ __('login.tagline') }}</p>
                    </div>
                </div>
                <div class="login-card">
                    <div class="login-head">
                        <h5>{{ __('login.title') }}</h5>
                        <span>{{ __('login.badge') }}</span>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-info">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->has('token'))
                        <div class="alert alert-warning">
                            {{ $errors->first('token') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('login.email') }}</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" dir="ltr" value="{{ old('email') }}" required
                                   autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('login.password') }}</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text showPassword" id="basic-addon1">
                                        <i class="fa fa-eye basic-addon1"></i>
                                    </span>
                                </div>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror text-left"
                                       dir="ltr" name="password" required
                                       aria-describedby="basic-addon1">
                            </div>

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="login-footer mb-3">
                            <div class="form-check">
                                <input checked class="form-check-input" type="checkbox"
                                       name="remember"
                                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('login.remember') }}
                                </label>
                            </div>
                            <span>{{ __('login.forgot') }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary-modern btn-block">
                            {{ __('login.submit') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="login-visual d-none d-lg-flex">
                <div>
                    <h2>{{ __('login.visual_title') }}</h2>
                    <p>{{ __('login.visual_subtitle') }}</p>
                </div>
                <div class="visual-images">
                    <img src="{{URL::asset('assets/img/bg-login.jpg')}}" alt="واجهة">
                    <img src="{{URL::asset('assets/img/logo-login.png')}}" alt="علامة">
                </div>
                <p>{{ __('login.visual_footer') }}</p>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(".showPassword").click(function () {
            if ($("#password").attr("type") == "password") {
                $("#password").attr("type", "text");
                $(".showPassword").find('i.fa').toggleClass('fa-eye fa-eye-slash');
            } else {
                $("#password").attr("type", "password");
                $(".showPassword").find('i.fa').toggleClass('fa-eye fa-eye-slash');
            }
        });
    </script>
@endsection
