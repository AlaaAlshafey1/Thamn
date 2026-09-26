@extends('layouts.master')

@section('css')
<style>
    .profile-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .profile-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 25px 30px;
    }
    .profile-card .card-body {
        padding: 30px;
    }
    .premium-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 12px 18px;
        transition: all 0.3s;
        background-color: #f8f9fa;
    }
    .premium-input:focus {
        border-color: #c1953e;
        box-shadow: 0 0 0 3px rgba(193, 149, 62, 0.15);
        background-color: #fff;
    }
    .btn-gold {
        background: linear-gradient(135deg, #d4af37 0%, #c1953e 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(193, 149, 62, 0.3);
        color: white;
    }
</style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between mb-4">
        <div class="my-auto">
            <div class="d-flex align-items-center">
                <h4 class="content-title mb-0 my-auto text-primary"><i class="bx bx-user-circle mr-2 ml-2"></i> الملف الشخصي</h4>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-8 col-md-12 mx-auto">
            <div class="card profile-card mb-4" style="border-top: 4px solid #c1953e;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-weight-bold" style="color: #2c3e50;">تحديث المعلومات الشخصية</h5>
                        <p class="text-muted mb-0 small">قم بتحديث معلوماتك الأساسية وتفاصيل الحساب.</p>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                            <i class="bx bx-check-circle mr-1 ml-1 fs-5 align-middle"></i>
                            <strong>تم بنجاح!</strong> تم تحديث الملف الشخصي.
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" style="border-radius: 10px;">
                            <ul class="mb-0 pr-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row row-sm">
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold text-muted">الاسم الأول <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-user"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="first_name" placeholder="أدخل الاسم الأول" required type="text" value="{{ old('first_name', $user->first_name) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold text-muted">اسم العائلة <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-user"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="last_name" placeholder="أدخل اسم العائلة" required type="text" value="{{ old('last_name', $user->last_name) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row row-sm">
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold text-muted">البريد الإلكتروني <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-envelope"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="email" placeholder="أدخل البريد الإلكتروني" required type="email" value="{{ old('email', $user->email) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold text-muted">رقم الجوال <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-phone"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0 text-left" dir="ltr" name="phone" placeholder="أدخل رقم الجوال" required type="text" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($user->hasRole('expert'))
                            <hr class="my-5 border-light">
                            <h5 class="mb-4 font-weight-bold" style="color: #2c3e50;"><i class="bx bx-building-house text-warning mr-2 ml-2"></i> تفاصيل الحساب البنكي والخبرة</h5>
                            
                            <div class="row row-sm">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">اسم البنك</label>
                                        <input class="form-control premium-input" name="bank_name" placeholder="أدخل اسم البنك" type="text" value="{{ old('bank_name', $user->bank_name) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">IBAN</label>
                                        <input class="form-control premium-input text-left" dir="ltr" name="iban" placeholder="SA..." type="text" value="{{ old('iban', $user->iban) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">رقم الحساب</label>
                                        <input class="form-control premium-input text-left" dir="ltr" name="account_number" placeholder="أدخل رقم الحساب" type="text" value="{{ old('account_number', $user->account_number) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">رمز السويفت (SWIFT)</label>
                                        <input class="form-control premium-input text-left" dir="ltr" name="swift" placeholder="أدخل رمز السويفت" type="text" value="{{ old('swift', $user->swift) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-lg-12">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">التخصص</label>
                                        <input class="form-control premium-input" name="expertise" placeholder="أدخل التخصص" type="text" value="{{ old('expertise', $user->expertise) }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold text-muted">الخبرة</label>
                                        <textarea class="form-control premium-input" name="experience" placeholder="أدخل نبذة عن خبراتك..." rows="4">{{ old('experience', $user->experience) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <button class="btn btn-gold btn-block" type="submit"><i class="bx bx-save mr-1 ml-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card profile-card" style="border-top: 4px solid #3b82f6;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 font-weight-bold" style="color: #2c3e50;">تحديث كلمة المرور</h5>
                        <p class="text-muted mb-0 small">تأكد من استخدام كلمة مرور قوية للحفاظ على أمان حسابك.</p>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px;">
                            <i class="bx bx-check-circle mr-1 ml-1 fs-5 align-middle"></i>
                            <strong>تم بنجاح!</strong> تم تحديث كلمة المرور.
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="row row-sm">
                            <div class="col-lg-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold text-muted">كلمة المرور الحالية</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-lock-open"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="current_password" type="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold text-muted">كلمة المرور الجديدة</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-lock-alt"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="password" type="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold text-muted">تأكيد كلمة المرور الجديدة</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-0"><i class="bx bx-check-shield"></i></span>
                                        </div>
                                        <input class="form-control premium-input border-right-0 pl-0" name="password_confirmation" type="password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700;" type="submit"><i class="bx bx-key mr-1 ml-1"></i> تحديث كلمة المرور</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
