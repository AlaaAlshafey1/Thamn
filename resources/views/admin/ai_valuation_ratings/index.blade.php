@extends('layouts.master')
@section('title', 'إدارة التقييمات للذكاء الاصطناعي')

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الإعدادات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ التقييمات للذكاء الاصطناعي</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('admin.ai-valuation-ratings.create') }}" class="btn btn-primary">إضافة تقييم جديد</a>
        </div>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-md-nowrap" id="example1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم بالعربية</th>
                            <th>اللون</th>
                            <th>الأيقونة</th>
                            <th>الحالة</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratings as $rating)
                            <tr>
                                <td>{{ $rating->id }}</td>
                                <td>{{ $rating->name_ar }}</td>
                                <td>
                                    @if($rating->color)
                                        <span style="display:inline-block; width:20px; height:20px; background-color:{{ $rating->color }}; border-radius:50%;"></span>
                                    @endif
                                </td>
                                <td>
                                    @if($rating->icon)
                                        <img src="{{ asset('storage/' . $rating->icon) }}" width="40" height="40" alt="icon">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($rating->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.ai-valuation-ratings.edit', $rating->id) }}" class="btn btn-sm btn-info">تعديل</a>
                                    <form action="{{ route('admin.ai-valuation-ratings.destroy', $rating->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
