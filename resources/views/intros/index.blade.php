@extends('layouts.master')
@section('title', 'صفحات المقدمة والتنبيهات (Intros)')

@section('content')
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1 text-primary font-weight-bold">صفحات المقدمة والتنبيهات (App Intros & Notices)</h4>
                <p class="text-muted mb-0 small">تتحكم في النصوص الترحيبية وتنبيهات التثمين (مثل تصدير البطاقة، تنبيه الصورة التخيلية، وإخلاء المسؤولية) التي تظهر في تطبيق الموبايل عبر API.</p>
            </div>
            <div>
                <a href="{{ route('intros.create') }}" class="btn btn-primary"><i class="fas fa-plus ml-1"></i> إضافة صفحة مقدمة جديدة</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ session('success') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 180px;">نوع الصفحة (Page Key)</th>
                        <th>العنوان (عربي)</th>
                        <th>العنوان الفرعي (عربي)</th>
                        <th style="width: 70px;">الصورة</th>
                        <th style="width: 90px;">الحالة</th>
                        <th style="width: 70px;">الترتيب</th>
                        <th style="width: 140px;">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($intros as $intro)
                        <tr>
                            <td class="font-weight-bold">{{ $intro->id }}</td>
                            <td>
                                <span class="badge badge-info p-2 font-weight-bold">{{ $intro->page }}</span>
                            </td>
                            <td class="font-weight-semibold text-dark text-right pr-3">{{ $intro->title_ar }}</td>
                            <td class="text-muted small text-right pr-3">{{ $intro->sub_title_ar ?: '—' }}</td>
                            <td>
                                @if($intro->image)
                                    <img src="{{ $intro->image }}" alt="intro"
                                        style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($intro->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-secondary">معطل</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">{{ $intro->sort_order }}</td>
                            <td>
                                <a href="{{ route('intros.edit', $intro->id) }}" class="btn btn-info btn-sm ml-1" title="تعديل">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <form action="{{ route('intros.destroy', $intro->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الصفحة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="حذف">
                                        <i class="fas fa-trash-alt"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">لا توجد صفحات مقدمة حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
