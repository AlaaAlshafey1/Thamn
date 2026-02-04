@extends('layouts.master')
@section('title','إنشاء طلب جديد')

@section('css')
<style>
    /* Card Base */
    .card {
        border:1px solid #ddd;
        border-radius:10px;
        margin-bottom:15px;
        background:#fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .card-header {
        font-weight:bold;
        color:#c1953e;
        cursor:pointer;
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:10px 15px;
        border-bottom:1px solid #eee;
        border-radius:10px 10px 0 0;
    }
    .card-body {
        display:none;
        padding:15px;
        border-top:none;
    }
    .card-body.show { display:block; }

    label.form-label { font-weight:500; color:#333; margin-bottom:4px; }
    input.form-control, select.form-control {
        width:100%;
        padding:8px 12px;
        margin-bottom:10px;
        border-radius:6px;
        border:1px solid #ccc;
    }

    button.submit-btn {
        background:#c1953e;
        color:#fff;
        padding:10px 20px;
        border:none;
        border-radius:6px;
        cursor:pointer;
    }
    button.submit-btn:hover { background:#a67f31; }

    .form-check-label { margin-left:8px; }

    .question-collapse span { font-size:14px; color:#666; }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-plus-circle"></i> إنشاء طلب جديد</h4>
        <small class="text-muted">قم بإضافة طلب جديد وربطه بالمستخدم والفئة</small>
    </div>
    <div>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">اسم المستخدم</label>
            <select name="user_id" required class="form-control">
                <option value="">اختر المستخدم</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">فئة الطلب</label>
            <select name="category_id" required class="form-control">
                <option value="">اختر الفئة</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">طريقة التقييم</label>
            <select name="evaluation_type" class="form-control" required>
                <option value="ai">التقييم الذكي (AI)</option>
                <option value="expert">تقييم بواسطة خبير موثوق</option>
                <option value="price">تقييم سعر المنتج</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">السعر الإجمالي (SAR)</label>
            <input type="number" step="0.01" name="total_price" required class="form-control">
        </div>

        <h4 class="mb-2">الأسئلة</h4>
        @foreach(\App\Models\Question::with('options')->where('is_active',1)->get() as $question)
            <div class="card">
                <div class="card-header question-collapse" onclick="this.nextElementSibling.classList.toggle('show')">
                    <span>{{ $question->question_ar }}</span> <span>▼</span>
                </div>
                <div class="card-body">
                    @foreach($question->options as $option)
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio"
                                   name="questions[{{ $question->id }}]"
                                   value="{{ $option->id }}"
                                   id="option_{{ $option->id }}">
                            <label class="form-check-label" for="option_{{ $option->id }}">
                                {{ $option->option_ar }}
                                @if($option->price) - {{ $option->price }} SAR @endif
                            </label>
                        </div>
                        @if($option->sub_options()->count())
                            <div class="ms-4 mt-1">
                                @foreach($option->sub_options as $sub)
                                    <div class="form-check">
                                        <input type="radio" name="questions[{{ $question->id }}]"
                                               value="{{ $sub->id }}" id="sub_{{ $sub->id }}">
                                        <label for="sub_{{ $sub->id }}">{{ $sub->option_ar }}
                                            @if($sub->price) - {{ $sub->price }} SAR @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="mt-3">
            <button type="submit" class="submit-btn"><i class="bx bx-save"></i> إنشاء الطلب</button>
        </div>
    </form>
</div>
@endsection
