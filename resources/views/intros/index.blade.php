@extends('layouts.master')
@section('title', 'صفحات المقدمة')

@section('content')
    <div class="card p-3">
        <a href="{{ route('intros.create') }}" class="btn btn-primary mb-3">إضافة صفحة مقدمة جديدة</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الصفحة</th>
                    <th>العنوان (عربي)</th>
                    <th>العنوان (English)</th>
                    <th>الصورة</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>التحكم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($intros as $key => $intro)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $intro->page }}</span>
                        </td>
                        <td>{{ $intro->title_ar }}</td>
                        <td>{{ $intro->title_en }}</td>
                        <td>
                            @if($intro->image)
                                <img src="{{ $intro->image }}" alt="intro"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                            @if($intro->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $intro->sort_order }}</td>
                        <td>
                            <a href="{{ route('intros.edit', $intro->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('intros.destroy', $intro->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">لا توجد صفحات مقدمة حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection