@extends('layouts.master')
@section('title', 'إضافة مستخدم جديد')

@section('css')
<style>
    .user-form-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 25px;
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 5px;
    }

    label.form-label {
        font-weight: 500;
        color: #333;
    }

    input.form-control, select.form-select {
        border-radius: 10px;
        padding: 10px 14px;
        min-height: 45px;
        width: 100%;
    }

    select.form-select {
        background-color: #fff;
        border: 1px solid #ced4da;
        font-size: 15px;
    }

    .avatar-preview {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }

    /* لجعل select ياخد مساحة أكبر */
    .wide-select {
        width: 100%;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-user-plus"></i> إضافة مستخدم جديد</h4>
        <small class="text-muted">قم بإدخال بيانات المستخدم وتحديد الدور الخاص به</small>
    </div>
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="user-form-card">
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-section mb-4">
            <h6 class="form-section-title">👤 المعلومات الأساسية</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" name="first_name" class="form-control" placeholder="أدخل الاسم الأول" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم الأخير</label>
                    <input type="text" name="last_name" class="form-control" placeholder="أدخل الاسم الأخير" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" placeholder="مثال: 01012345678">
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🔐 كلمة المرور والدور</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">الدور</label>
                    <select name="role_id" class="form-select wide-select" required>
                        <option value="">اختر الدور</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-select wide-select">
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🖼️ الصورة الشخصية</h6>
            <div class="row">
                <div class="col-md-6 d-flex align-items-center gap-3">
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> حفظ المستخدم
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
