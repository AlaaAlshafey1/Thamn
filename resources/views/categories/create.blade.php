@extends('layouts.master')
@section('title', 'إضافة فئة جديدة')

@section('css')
<style>
    .category-form-card {
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

    input.form-control, textarea.form-control {
        border-radius: 10px;
        padding: 10px 14px;
        min-height: 45px;
        width: 100%;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-category"></i> إضافة فئة جديدة</h4>
        <small class="text-muted">قم بإدخال بيانات الفئة الجديدة لتظهر في النظام</small>
    </div>
    <div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="category-form-card">
    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-section mb-4">
            <h6 class="form-section-title">📦 معلومات الفئة</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الفئة (عربي)</label>
                    <input type="text" name="name_ar" class="form-control" placeholder="أدخل اسم الفئة بالعربية" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">اسم الفئة (إنجليزي)</label>
                    <input type="text" name="name_en" class="form-control" placeholder="Category Name in English">
                </div>

                <div class="col-md-6">
                    <label class="form-label">الوصف (عربي)</label>
                    <textarea name="description_ar" class="form-control" placeholder="الوصف بالعربية"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الوصف (إنجليزي)</label>
                    <textarea name="description_en" class="form-control" placeholder="Description in English"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">صورة الفئة</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                {{-- @if(isset($category) && $category->image)
                    <img src="{{ asset('storage/'.$category->image) }}" alt="صورة الفئة" class="mt-2" width="80" height="80">
                @endif --}}
            </div>

                <div class="col-md-6">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-select">
                        <option value="1">مفعّلة</option>
                        <option value="0">غير مفعّلة</option>
                    </select>
                </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary" style="background-color:#c1953e; border:none;">
                <i class="bx bx-save"></i> حفظ الفئة
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
