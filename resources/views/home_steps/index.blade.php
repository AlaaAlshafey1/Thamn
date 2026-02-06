@extends('layouts.master')
@section('title', 'خطوات الصفحة الرئيسية')

@section('content')
    <div class="card p-3">
        <a href="{{ route('home_steps.create') }}" class="btn btn-primary mb-3">إضافة خطوة جديدة</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>العنوان (عربي)</th>
                    <th>العنوان (English)</th>
                    <th>النوع</th>
                    <th>عدد العناصر</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th>التحكم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($homeSteps as $key => $step)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $step->title_ar }}</td>
                        <td>{{ $step->title_en }}</td>
                        <td>
                            @if($step->type == 'steps')
                                <span class="badge bg-primary">خطوات</span>
                            @elseif($step->type == 'check')
                                <span class="badge bg-success">قائمة تحقق</span>
                            @else
                                <span class="badge bg-info">صور</span>
                            @endif
                        </td>
                        <td>{{ count($step->items) }}</td>
                        <td>
                            @if($step->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </td>
                        <td>{{ $step->sort_order }}</td>
                        <td>
                            <a href="{{ route('home_steps.edit', $step->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('home_steps.destroy', $step->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">لا توجد خطوات حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection