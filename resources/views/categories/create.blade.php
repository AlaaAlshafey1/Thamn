@extends('layouts.master')
@section('title','إنشاء طلب جديد')

@section('css')
<style>
/* نفس ستايل الباقي */
.card { border:1px solid #ddd; border-radius:6px; margin-bottom:10px; padding:15px; background:#fff; }
.card-header { font-weight:bold; color:#c1953e; cursor:pointer; display:flex; justify-content:space-between; }
.card-body { display:none; padding-top:10px; }
.card-body.show { display:block; }
input, select { width:100%; padding:6px; margin:3px 0; border-radius:4px; border:1px solid #ccc; }
button { background:#c1953e; color:#fff; padding:8px 15px; border:none; border-radius:4px; cursor:pointer; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <h2 class="mb-4">إنشاء طلب جديد</h2>

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
            <label>اسم المستخدم</label>
            <select name="user_id" required class="form-control">
                <option value="">اختر المستخدم</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name . ' ' . $user->last_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>فئة الطلب</label>
            <select name="category_id" required class="form-control">
                <option value="">اختر الفئة</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>طريقة التقييم</label>
            <select name="evaluation_type" class="form-control" required>
                <option value="ai">التقييم الذكي (AI)</option>
                <option value="expert">تقييم بواسطة خبير موثوق</option>
                <option value="price">تقييم سعر المنتج</option>
            </select>
        </div>

        <div class="mb-3">
            <label>السعر الإجمالي (SAR)</label>
            <input type="number" step="0.01" name="total_price" required class="form-control">
        </div>

        <h4>الأسئلة</h4>
        @foreach(\App\Models\Question::with('options')->where('is_active',1)->get() as $question)
            <div class="card">
                <div class="card-header" onclick="this.nextElementSibling.classList.toggle('show')">
                    {{ $question->question_ar }}
                    <span>▼</span>
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
            <button type="submit">إنشاء الطلب</button>
        </div>
    </form>
</div>
@endsection
