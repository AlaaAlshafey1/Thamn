@extends('layouts.master')
@section('title', 'إضافة بند شروط')

@section('css')
    <style>
        .terms-form-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        input.form-control,
        textarea.form-control,
        select.form-select {
            border-radius: 10px;
            padding: 10px 14px;
            min-height: 45px;
        }
    </style>
@endsection

@section('page-header')
    <div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3"
        style="direction: rtl;">
        <div class="d-flex flex-column">
            <h4 class="content-title mb-1 fw-bold text-primary">
                <i class="bx bx-file-plus"></i> إضافة بند جديد
            </h4>
            <small class="text-muted">إضافة بند جديد إلى الشروط والأحكام</small>
        </div>

        <div>
            <a href="{{ route('terms.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bx bx-arrow-back fs-5"></i> رجوع
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="terms-form-card">
        <form action="{{ route('terms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section mb-4">
                <h6 class="form-section-title">📄 بيانات البند</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">العنوان (عربي)</label>
                        <input type="text" name="title_ar" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المحتوى (عربي)</label>
                        <textarea name="content_ar" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المحتوى (إنجليزي)</label>
                        <textarea name="content_en" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">الملف</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">الحالة</label>
                        <select name="is_active" class="form-select">
                            <option value="1">مفعّل</option>
                            <option value="0">غير مفعّل</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">نوع الشروط</label>
                        <select name="type" id="typeSelect" class="form-select">
                            <option value="general">شروط عامة</option>
                            <option value="sale_terms">شروط التثمين والبيع</option>
                        </select>
                        <small class="text-muted">اختر "شروط التثمين والبيع" لتظهر في شاشة الموافقة عند اختيار التثمين والبيع</small>
                    </div>
                </div>
            </div>

            {{-- قسم خاص بـ "التثمين والبيع" --}}
            <div id="saleTermsSection" class="sale-terms-section d-none mb-4" style="background: rgba(248,180,0,0.03); border: 1px dashed rgba(248,180,0,0.5); padding: 20px; border-radius: 12px;">
                <h6 class="form-section-title" style="color: #92620a;">
                    🕌 نصوص شاشة "التثمين والبيع"
                </h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            ✅ نص الإقرار (عربي) <span class="text-muted small">(يظهر بجانب الـ checkbox)</span>
                        </label>
                        <textarea name="checkbox_label_ar" class="form-control" rows="4" placeholder="مثال: أقر وأتعهد بأنني قرأت وفهمت وأوافق على جميع الشروط..."></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            ✅ نص الإقرار (إنجليزي) <span class="text-muted small">(اختياري)</span>
                        </label>
                        <textarea name="checkbox_label_en" class="form-control" rows="4" placeholder="Example: I hereby acknowledge that I have read and agree..."></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary" style="background-color:#c1953e;border:none;">
                    <i class="bx bx-save"></i> حفظ
                </button>
                <a href="{{ route('terms.index') }}" class="btn btn-light border">
                    <i class="bx bx-x-circle"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('typeSelect');
        const saleSection = document.getElementById('saleTermsSection');

        function toggleSaleSection() {
            if (typeSelect && saleSection) {
                if (typeSelect.value === 'sale_terms') {
                    saleSection.classList.remove('d-none');
                } else {
                    saleSection.classList.add('d-none');
                }
            }
        }

        if(typeSelect) {
            typeSelect.addEventListener('change', toggleSaleSection);
            toggleSaleSection();
        }
    });
</script>
@endsection