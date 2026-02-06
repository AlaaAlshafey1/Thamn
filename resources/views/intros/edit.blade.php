@extends('layouts.master')
@section('title', 'تعديل صفحة مقدمة')

@section('content')
    <div class="card p-4">
        <h3 class="mb-4">تعديل صفحة مقدمة</h3>

        <form action="{{ route('intros.update', $intro->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">نوع الصفحة (Page Type) *</label>
                    <select name="page" class="form-control @error('page') is-invalid @enderror" required>
                        <option value="welcome" {{ old('page', $intro->page) == 'welcome' ? 'selected' : '' }}>Welcome
                        </option>
                        <option value="login" {{ old('page', $intro->page) == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="signup" {{ old('page', $intro->page) == 'signup' ? 'selected' : '' }}>Signup</option>
                        <option value="verify" {{ old('page', $intro->page) == 'verify' ? 'selected' : '' }}>Verify</option>
                    </select>
                    @error('page')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الصورة (Image)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                        accept="image/*">
                    @if($intro->image)
                        <div class="mt-2 text-muted">الصورة الحالية: <a href="{{ $intro->image }}" target="_blank">معاينة</a>
                        </div>
                    @endif
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان (عربي) *</label>
                    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar', $intro->title_ar) }}" required>
                    @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Title (English) *</label>
                    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en', $intro->title_en) }}" required>
                    @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الفرعي (عربي)</label>
                    <input type="text" name="sub_title_ar" class="form-control @error('sub_title_ar') is-invalid @enderror"
                        value="{{ old('sub_title_ar', $intro->sub_title_ar) }}">
                    @error('sub_title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Subtitle (English)</label>
                    <input type="text" name="sub_title_en" class="form-control @error('sub_title_en') is-invalid @enderror"
                        value="{{ old('sub_title_en', $intro->sub_title_en) }}">
                    @error('sub_title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الوصف (عربي)</label>
                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
                        rows="3">{{ old('description_ar', $intro->description_ar) }}</textarea>
                    @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Description (English)</label>
                    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                        rows="3">{{ old('description_en', $intro->description_en) }}</textarea>
                    @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $intro->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الحالة</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $intro->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="text-end">
                <a href="{{ route('intros.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">تحديث</button>
            </div>
        </form>
    </div>
@endsection