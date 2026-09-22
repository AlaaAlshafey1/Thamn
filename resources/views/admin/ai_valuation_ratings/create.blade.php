@extends('layouts.master')
@section('title', 'إضافة تقييم جديد')

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">إدارة التقييمات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة تقييم جديد</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('admin.ai-valuation-ratings.index') }}" class="btn btn-secondary">العودة</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ai-valuation-ratings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الاسم بالعربية <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>اللون المميز (Hex Code)</label>
                        <input type="color" name="color" class="form-control" style="height:40px" value="{{ old('color', '#000000') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الأيقونة (صورة صغيرة PNG/SVG)</label>
                        <input type="file" name="icon" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="isActive">نشط (يظهر للخبير)</label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-3" type="submit">حفظ التقييم</button>
            </form>
        </div>
    </div>
@endsection
