@extends('layouts.master')
@section('title', 'تعديل البانر')

@section('content')
    <div class="card p-4">
        <h3 class="mb-4">تعديل البانر</h3>

        <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان (عربي)</label>
                    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar', $banner->title_ar) }}">
                    @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Title (English)</label>
                    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en', $banner->title_en) }}">
                    @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الملف (صورة / فيديو / GIF)</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                        accept="image/*,video/*,.gif">
                    @if($banner->file)
                        <div class="mt-2">
                            <span class="text-muted">الملف الحالي:</span>
                            @if($banner->file_type === 'video')
                                <video src="{{ $banner->file }}" style="max-width: 200px; max-height: 120px; border-radius: 5px;" controls muted></video>
                            @else
                                <img src="{{ $banner->file }}" alt="banner preview"
                                    style="max-width: 200px; max-height: 120px; object-fit: cover; border-radius: 5px;">
                            @endif
                            <br>
                            <a href="{{ $banner->file }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">معاينة</a>
                        </div>
                    @endif
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $banner->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">الحالة</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                            {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="text-end">
                <a href="{{ route('banners.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">تحديث</button>
            </div>
        </form>
    </div>
@endsection
