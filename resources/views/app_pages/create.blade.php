@extends('layouts.master')
@section('title', 'إضافة صفحة جديدة')

@section('css')
{{-- استخدم نفس CSS --}}
<style>
.page-form { background-color:#fff;border-radius:15px;padding:35px;margin-bottom:40px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.section-card{border:1px solid #f0f0f0;border-radius:12px;padding:20px 25px;background:#fafafa;margin-bottom:25px;}
.section-title{font-size:16px;font-weight:600;color:#0d6efd;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.form-control, .form-select{border-radius:10px;min-height:42px;padding:10px 14px}
.form-control-color{height:42px;border-radius:10px}
.checkbox-wrapper{display:flex;align-items:center;gap:10px;padding:8px 0}
.btn-main{background-color:#c1953e;border-color:#c1953e}
img.preview {max-height:90px;border-radius:8px;border:1px solid #e6e6e6;margin-top:8px}
</style>

{{-- استدعاء محرر النصوص --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center" style="direction:rtl">
    <div><h4 class="fw-bold text-primary mb-1"><i class="bx bx-layout"></i> إضافة صفحة جديدة</h4><small class="text-muted">أدخل إعدادات الصفحة</small></div>
    <div><a href="{{ route('app_pages.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back"></i> رجوع</a></div>
</div>
@endsection

@section('content')
<div class="page-form">
    <form action="{{ route('app_pages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- بيانات أساسية --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-info-circle"></i> البيانات الأساسية</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">اسم الصفحة (مفتاح فريد)</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="مثال: splash, home" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">نوع الصفحة</label>
                    <select name="type" class="form-select">
                        <option value="screen">Screen</option>
                        <option value="popup">Popup</option>
                        <option value="section">Section</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">العنوان بالعربية</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">العنوان بالإنجليزية</label>
                    <input type="text" name="title_en" value="{{ old('title_en') }}" class="form-control">
                </div>
            </div>
        </div>

        {{-- الوصف --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-edit"></i> الوصف</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">الوصف بالعربية</label>
                    <textarea name="description_ar" class="form-control summernote" rows="4">{{ old('description_ar') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الوصف بالإنجليزية</label>
                    <textarea name="description_en" class="form-control summernote" rows="4">{{ old('description_en') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ألوان وخلفية --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-palette"></i> الألوان والخلفية</div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">لون الخلفية</label>
                    <input type="color" name="background_color" value="{{ old('background_color', '#ffffff') }}" class="form-control form-control-color">
                </div>
                <div class="col-md-3">
                    <label class="form-label">صورة الخلفية</label>
                    <input type="file" name="background_image" accept="image/*" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">اللوجو (اختياري)</label>
                    <input type="file" name="logo" accept="image/*" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">لون النص</label>
                    <input type="color" name="text_color" value="{{ old('text_color', '#000000') }}" class="form-control form-control-color">
                </div>

                <div class="col-md-3">
                    <label class="form-label">لون الزر</label>
                    <input type="color" name="button_color" value="{{ old('button_color', '#c1953e') }}" class="form-control form-control-color">
                </div>
                <div class="col-md-3">
                    <label class="form-label">لون نص الزر</label>
                    <input type="color" name="button_text_color" value="{{ old('button_text_color', '#ffffff') }}" class="form-control form-control-color">
                </div>
            </div>
        </div>

        {{-- بانر --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-image"></i> البانر</div>
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">صورة البانر</label>
                    <input type="file" name="banner_image" accept="image/*" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">لون خلفية البانر</label>
                    <input type="color" name="banner_color" value="{{ old('banner_color', '#ffffff') }}" class="form-control form-control-color">
                </div>
                <div class="col-md-4">
                    <label class="form-label">نص البانر</label>
                    <input type="text" name="banner_text" value="{{ old('banner_text') }}" class="form-control" placeholder="مثال: عرض خاص اليوم">
                </div>

                <div class="col-md-3">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="has_banner" id="has_banner" {{ old('has_banner') ? 'checked' : '' }}>
                        <label for="has_banner" class="form-label mb-0">الصفحة تحتوي على بانر</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- layout JSON --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-code-curly"></i> تكوين الصفحة (Layout JSON)</div>
            <textarea name="layout_json" rows="5" class="form-control" placeholder='{"sections":[{"type":"banner"},{"type":"cta","label":"ابدأ الآن"}]}'>{{ old('layout_json') }}</textarea>
            <small class="text-muted d-block mt-2">اختياري: وصف مكونات الصفحة للموبايل بصيغة JSON.</small>
        </div>

        {{-- إعدادات عامة --}}
        <div class="section-card">
            <div class="section-title"><i class="bx bx-cog"></i> إعدادات عامة</div>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="form-label mb-0">الصفحة فعّالة</label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bx bx-save"></i> حفظ الصفحة
            </button>
            <a href="{{ route('app_pages.index') }}" class="btn btn-light border px-4">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$('.summernote').summernote({
    placeholder: 'اكتب وصف الصفحة هنا...',
    tabsize: 2,
    height: 180,
    direction: 'rtl',
    lang: 'ar-AR'
});
</script>
@endsection
