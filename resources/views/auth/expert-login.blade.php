@extends('layouts.master2')

@section('css')
<!-- Google Arabic Font -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

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

    /* Support RTL */
    .rtl-container {
        direction: rtl;
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="container-fluid rtl-container">
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
                                    <h2>مرحباً بخبرائنا 👋</h2>
                                    <h5 class="font-weight-semibold mb-4">
                                        {{ session('otp_sent') ? 'أدخل رمز التحقق الذي تم إرساله إلى جوالك عبر الواتساب' : 'قم بتسجيل الدخول بأمان باستخدام رقم جوالك' }}
                                    </h5>

                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!session('otp_sent'))
                                        <!-- Step 1: Request OTP -->
                                        <form method="POST" action="{{ route('expert.login.send-otp') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>رقم الجوال</label>
                                                <input type="text" class="form-control text-left" dir="ltr" name="phone" value="{{ old('phone') }}" placeholder="05XXXXXXXX" required autofocus>
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block d-flex justify-content-center align-items-center">
                                                <span class="mr-2">إرسال رمز التحقق</span>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Step 2: Verify OTP -->
                                        <form method="POST" action="{{ route('expert.login.verify-otp') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>رمز التحقق (OTP)</label>
                                                <input type="text" class="form-control text-center" name="otp" placeholder="----" maxlength="4" style="letter-spacing: 15px; font-size: 24px;" required autofocus>
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">
                                                تأكيد وتسجيل الدخول
                                            </button>
                                        </form>
                                        
                                        <div class="mt-4 text-center">
                                            <a href="{{ route('expert.login') }}" class="text-muted">لم يصلك الرمز؟ حاول مرة أخرى</a>
                                        </div>
                                    @endif

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
