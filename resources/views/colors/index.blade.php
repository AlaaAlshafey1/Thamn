@extends('layouts.master')
@section('title', 'ألوان التطبيق')

@section('content')
<div class="card p-3">
    <a href="{{ route('colors.create') }}" class="btn btn-primary mb-3">إضافة لون جديد</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>المجموعة</th>
                <th>المفتاح</th>
                <th>القيمة</th>
                <th>معاينة</th>
                <th>التحكم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colors as $key => $color)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $color->group }}</td>
                <td>{{ $color->key }}</td>
                <td>{{ $color->value }}</td>
                <td>
                    <div style="width:30px;height:30px;background:{{ $color->value }};border-radius:5px;margin:auto;"></div>
                </td>
                <td>
                    <a href="{{ route('colors.edit', $color->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                    <form action="{{ route('colors.destroy', $color->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
