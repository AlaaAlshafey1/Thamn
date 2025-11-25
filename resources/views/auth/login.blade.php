@extends('layouts.master2')

@section('css')
<!-- Google Arabic Font -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<!-- Sidemenu-respoansive-tabs css -->
<link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}" rel="stylesheet">

<style>
    body, input, button, label, h1, h2, h5 {
        font-family: 'Tajawal', sans-serif !important;
    }

    h2 {
        font-weight: 700;
        color: #1c1c1c;
    }

    h5 {
        color: #666;
    }

    .form-control {
        border-radius: 10px;
        height: 45px;
        font-size: 15px;
        direction: rtl;
    }

    .btn-main-primary {
        font-weight: 600;
        border-radius: 10px;
        height: 45px;
        font-size: 16px;
    }

    label {
        font-weight: 500;
    }

    a {
        color: #0066cc;
    }

    a:hover {
        color: #004c99;
        text-decoration: underline;
    }

    .main-logo1 {
        font-weight: 800;
        color: #0d6efd;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">
        <!-- Image half -->
        <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
            <div class="row wd-100p mx-auto text-center">
                <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                    <img src="{{ URL::asset('assets/img/Logo2.png') }}" class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
            <div class="login d-flex align-items-center py-2">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                            <div class="card-sigin">
                                <div class="mb-5 d-flex align-items-center">
                                    <a href="{{ url('/') }}">
                                        <img src="{{ URL::asset('assets/img/Logo.png') }}" class="sign-favicon ht-40" alt="logo">
                                    </a>
                                    <h1 class="main-logo1 ml-2 mr-0 my-auto tx-28">ثمن</h1>
                                </div>

                                <div class="main-signup-header">
                                    <h2>مرحباً بعودتك 👋</h2>
                                    <h5 class="font-weight-semibold mb-4">من فضلك قم بتسجيل الدخول</h5>

                                    <!-- ✅ Login Form -->
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="email">البريد الإلكتروني</label>
                                            <input id="email" type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required autofocus
                                                placeholder="example@email.com">
                                            @error('email')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group">
                                            <label for="password">كلمة المرور</label>
                                            <input id="password" type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                required placeholder="••••••••">
                                            @error('password')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Remember Me -->
                                        <div class="form-group form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">تذكرني</label>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-main-primary btn-block">
                                            تسجيل الدخول
                                        </button>

                                        <!-- Forgot Password -->
                                        @if (Route::has('password.request'))
                                            <div class="mt-3 text-center">
                                                <a href="{{ route('password.request') }}">هل نسيت كلمة المرور؟</a>
                                            </div>
                                        @endif
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End container -->
            </div>
        </div><!-- End content -->
    </div>
</div>
@endsection

@section('js')
@endsection
