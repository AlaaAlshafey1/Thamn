@extends('layouts.master')
@section('title', 'البانرات الإعلانية')

@section('content')
    <div class="card p-3">
        @if($banners->isEmpty())
            <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createBannerModal">إضافة بانر جديد</a>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان (عربي)</th>
                    <th>العنوان (English)</th>
                    <th>الملف</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>التحكم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $key => $banner)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $banner->title_ar }}</td>
                        <td>{{ $banner->title_en }}</td>
                        <td>
                            @if($banner->file)
                                @if($banner->file_type === 'video')
                                    <video src="{{ $banner->file }}" style="width: 80px; height: 50px; object-fit: cover; border-radius: 5px;" muted></video>
                                @else
                                    <img src="{{ $banner->file }}" alt="banner"
                                        style="width: 80px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @endif
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $banner->file_type ?? '-' }}</span>
                        </td>
                        <td>
                            @if($banner->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">لا توجد بانرات حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Create Banner Modal -->
    <div class="modal fade" id="createBannerModal" tabindex="-1" aria-labelledby="createBannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createBannerModalLabel">إضافة بانر جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان (عربي)</label>
                                <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Title (English)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الملف (صورة / فيديو / GIF)</label>
                                <input type="file" name="file" class="form-control" accept="image/*,video/*,.gif">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">الترتيب</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">الحالة</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active_create" checked>
                                    <label class="form-check-label" for="is_active_create">نشط</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
