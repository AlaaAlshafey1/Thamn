@extends('layouts.master')
@section('title', isset($faq) ? 'تعديل السؤال' : 'إضافة سؤال جديد')

@section('css')
<style>
    .faq-card { background-color:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08); max-width:900px; margin:auto; }
    .faq-card h4 { color:#c1953e; margin-bottom:25px; font-weight:700; }
    label.form-label { font-weight:600; color:#333; }
    .btn-submit { background-color:#c1953e; border:none; padding:10px 20px; font-weight:600; border-radius:6px; cursor:pointer; }
    .alert { border-radius:6px; margin-bottom:20px; }
</style>
@endsection

@section('content')
<div class="faq-card">
    <h4>{{ isset($faq) ? 'تعديل السؤال' : 'إضافة سؤال جديد' }}</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ isset($faq) ? route('faqs.update', $faq->id) : route('faqs.store') }}" method="POST">
        @csrf
        @if(isset($faq)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">الفئة</label>
            <input type="text" name="category" class="form-control" required value="{{ old('category', $faq->category ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">السؤال بالعربية</label>
            <textarea name="question_ar" rows="4" class="form-control" required>{{ old('question_ar', $faq->question_ar ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">السؤال بالإنجليزية</label>
            <textarea name="question_en" rows="4" class="form-control" required>{{ old('question_en', $faq->question_en ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">الإجابة بالعربية</label>
            <textarea name="answer_ar" rows="4" class="form-control" required>{{ old('answer_ar', $faq->answer_ar ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">الإجابة بالإنجليزية</label>
            <textarea name="answer_en" rows="4" class="form-control" required>{{ old('answer_en', $faq->answer_en ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn-submit">
            {{ isset($faq) ? 'تحديث' : 'حفظ' }}
        </button>
    </form>
</div>
@endsection
