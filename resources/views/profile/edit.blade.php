@extends('layouts.master')

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الاعدادات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الملف الشخصي</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        تعديل الملف الشخصي
                    </div>
                    <p class="mg-b-20">قم بتحديث معلوماتك الشخصية وتفاصيل الحساب البنكي.</p>
                    
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success" role="alert">
                            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>تم بنجاح!</strong> تم تحديث الملف الشخصي.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
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
                                <div class="form-group mg-b-10">
                                    <label class="form-label">الاسم الأول: <span class="tx-danger">*</span></label>
                                    <input class="form-control" name="first_name" placeholder="أدخل الاسم الأول" required type="text" value="{{ old('first_name', $user->first_name) }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">اسم العائلة: <span class="tx-danger">*</span></label>
                                    <input class="form-control" name="last_name" placeholder="أدخل اسم العائلة" required type="text" value="{{ old('last_name', $user->last_name) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row row-sm">
                            <div class="col-lg-6">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">البريد الإلكتروني: <span class="tx-danger">*</span></label>
                                    <input class="form-control" name="email" placeholder="أدخل البريد الإلكتروني" required type="email" value="{{ old('email', $user->email) }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">رقم الجوال: <span class="tx-danger">*</span></label>
                                    <input class="form-control" name="phone" placeholder="أدخل رقم الجوال" required type="text" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>
                        </div>

                        @if($user->hasRole('expert'))
                            <hr class="mg-y-30">
                            <h5 class="card-title mg-b-20">تفاصيل الحساب البنكي (للخبراء)</h5>
                            
                            <div class="row row-sm">
                                <div class="col-lg-6">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">اسم البنك:</label>
                                        <input class="form-control" name="bank_name" placeholder="أدخل اسم البنك" type="text" value="{{ old('bank_name', $user->bank_name) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">IBAN:</label>
                                        <input class="form-control" name="iban" placeholder="أدخل الـ IBAN" type="text" value="{{ old('iban', $user->iban) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-lg-6">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">رقم الحساب:</label>
                                        <input class="form-control" name="account_number" placeholder="أدخل رقم الحساب" type="text" value="{{ old('account_number', $user->account_number) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">رمز السويفت (SWIFT):</label>
                                        <input class="form-control" name="swift" placeholder="أدخل رمز السويفت" type="text" value="{{ old('swift', $user->swift) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-lg-12">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">التخصص:</label>
                                        <input class="form-control" name="expertise" placeholder="أدخل التخصص" type="text" value="{{ old('expertise', $user->expertise) }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mg-b-10">
                                        <label class="form-label">الخبرة:</label>
                                        <textarea class="form-control" name="experience" placeholder="أدخل الخبرة" rows="3">{{ old('experience', $user->experience) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group mg-t-20 mb-0">
                            <button class="btn btn-primary pd-x-30 mg-r-5 mg-t-5" type="submit">تحديث الملف الشخصي</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        تحديث كلمة المرور
                    </div>
                    <p class="mg-b-20">تأكد من استخدام كلمة مرور قوية.</p>

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success" role="alert">
                            <strong>تم بنجاح!</strong> تم تحديث كلمة المرور.
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="row row-sm">
                            <div class="col-lg-4">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">كلمة المرور الحالية:</label>
                                    <input class="form-control" name="current_password" type="password" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">كلمة المرور الجديدة:</label>
                                    <input class="form-control" name="password" type="password" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mg-b-10">
                                    <label class="form-label">تأكيد كلمة المرور الجديدة:</label>
                                    <input class="form-control" name="password_confirmation" type="password" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mg-t-20 mb-0">
                            <button class="btn btn-secondary pd-x-30 mg-r-5 mg-t-5" type="submit">تحديث كلمة المرور</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
