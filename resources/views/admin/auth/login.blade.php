@extends('admin.layouts.app')
@section('content')
    <style>
        @font-face {
            font-family: 'Almarai';
            src: url("{{asset('fonts/SegoeUI.ttf')}}");
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Almarai' !important;
           
        } 
        .btn, .sp-container button{
            font-size:unset;
        }
    </style>
    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- The image half -->
            <!-- The content half -->
            <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
                <div class="login d-flex align-items-center py-2">
                    <!-- Demo content-->
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                <div class="card-sigin">
                                    <div class="mb-5 text-center">
                                        <a href="{{route('index')}}"><img
                                                src="{{URL::asset('assets/img/logo.png')}}"
                                                class="sign-favicon ht-100" alt="logo">
                                        </a>  
                                        <h2 class="text-center mb-4">{{env('APP_NAME')}}</h2>
                                    </div>

                                    <div class="card-sigin">
                                        <div class="main-signup-header">  
                                            <h5 class="alert alert-primary text-center mb-4">
                                                تسجيل الدخول
                                            </h5>
                                            <form method="POST" action="{{ route('admin.login') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label>البريد الالكتروني</label>
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
                                                    <label>كلمة المرور</label>

                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text showPassword"
                                                                  id="basic-addon1"><i
                                                                    class="fa fa-eye basic-addon1"></i></span>
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
                                                    <div class="form-group row">
                                                        <div class="col-md-6 offset-md-4">
                                                            <div class="form-check">
                                                                <input checked class="form-check-input" type="checkbox"
                                                                       name="remember"
                                                                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <label class="form-check-label" for="remember">
                                                                    {{ __('تذكرني') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-main-primary btn-block">
                                                    {{ __('دخول') }}
                                                </button>
                                            </form>
                                          
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End -->
                </div>
            </div><!-- End -->
            
            <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                <div class="row wd-100p mx-auto text-center">
                    <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">  
                        <img src="{{URL::asset('assets/img/bg-login.jpg')}}" class="rounded-circle ms-auto w-50 h-100" alt="logo">
                        <br><br>
                        <img src="{{URL::asset('assets/img/logo-login.png')}}" class="ms-auto w-50 h-100" alt="logo">
                    </div>
                </div>
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
