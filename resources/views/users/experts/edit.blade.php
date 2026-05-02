@extends('layouts.master')
@section('title', 'تعديل بيانات الخبير')

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
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-edit"></i> تعديل بيانات الخبير</h4>
        <small class="text-muted">تعديل بيانات الخبير وتحديثها</small>
    </div>
    <div>
        <a href="{{ route('experts.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="user-form-card">
    <form action="{{ route('experts.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-section mb-4">
            <h6 class="form-section-title">👤 بيانات الخبير الأساسية</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم الأول</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الاسم الأخير</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🔐 كلمة المرور والحالة</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور (لو عايز تغيرها)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-select wide-select">
                        <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">القسم التابع له (خاص بالفلترة)</label>
                    <select name="category_id" class="form-select wide-select" required>
                        <option value="" disabled>اختر قسم الخبير</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $user->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name_ar ?? $category->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🏦 بيانات البنك</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم البنك</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $user->bank_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">IBAN</label>
                    <input type="text" name="iban" class="form-control" value="{{ old('iban', $user->iban) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الحساب</label>
                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $user->account_number) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">SWIFT</label>
                    <input type="text" name="swift" class="form-control" value="{{ old('swift', $user->swift) }}">
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">📄 شهادات وخبرة</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">شهادة الخبرة (رفع ملف)</label>
                    <input type="file" name="experience_certificate" class="form-control">
                    @if($user->experience_certificate)
                        <small class="text-muted">الملف الحالي: {{ $user->experience_certificate }}</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">مجال الخبرة</label>
                    <input type="text" name="expertise" class="form-control" value="{{ old('expertise', $user->expertise) }}">
                </div>
            </div>
        </div>

        <div class="form-section mb-4">
            <h6 class="form-section-title">🖼️ الصورة الشخصية</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @if($user->image)
                        <div class="mt-2">
                            <img src="{{ asset('uploads/users/' . $user->image) }}" class="avatar-preview" alt="avatar">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> تحديث بيانات الخبير
            </button>
            <a href="{{ route('experts.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
