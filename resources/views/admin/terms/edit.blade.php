@extends('layouts.master')
@section('title', 'تعديل بند الشروط')

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
                <i class="bx bx-edit"></i> تعديل بند الشروط
            </h4>
            <small class="text-muted">قم بتعديل بيانات بند الشروط والأحكام</small>
        </div>

        <div>
            <a href="{{ route('terms.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bx bx-arrow-back fs-5"></i>
                <span>رجوع</span>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="terms-form-card">
        <form action="{{ route('terms.update', $term->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section mb-4">
                <h6 class="form-section-title">📄 بيانات البند</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">العنوان (عربي)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $term->title_ar }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $term->title_en }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المحتوى (عربي)</label>
                        <textarea name="content_ar" class="form-control" rows="5">{{ $term->content_ar }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">المحتوى (إنجليزي)</label>
                        <textarea name="content_en" class="form-control" rows="5">{{ $term->content_en }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">الملف (اتركه فارغاً للاحتفاظ بالملف الحالي)</label>
                        <input type="file" name="file" class="form-control">
                        @if($term->file)
                            <div class="mt-2 text-muted">
                                <i class="bx bx-file"></i> الملف الحالي:
                                <a href="{{ asset($term->file) }}" target="_blank">عرض الملف</a>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $term->sort_order }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">الحالة</label>
                        <select name="is_active" class="form-select">
                            <option value="1" @selected($term->is_active)>مفعّل</option>
                            <option value="0" @selected(!$term->is_active)>غير مفعّل</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-primary" style="background-color:#c1953e; border:none;">
                    <i class="bx bx-save"></i> تحديث البند
                </button>

                <a href="{{ route('terms.index') }}" class="btn btn-light border">
                    <i class="bx bx-x-circle"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
@endsection