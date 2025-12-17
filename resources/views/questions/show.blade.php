@extends('layouts.master')
@section('title', 'عرض السؤال')

@section('css')
<style>
.card-custom{
    background:#fff;
    border-radius:15px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.05)
}
.section-title{
    font-weight:600;
    color:#0d6efd;
    border-bottom:2px solid #eee;
    padding-bottom:5px;
    margin-bottom:15px
}
.info-box{
    background:#f8f9fa;
    padding:12px 18px;
    border-radius:10px;
    margin-bottom:12px
}
.option-card{
    background:#f0f8ff;
    border:1px solid #cce5ff;
    padding:12px;
    border-radius:10px;
    margin-bottom:10px
}
.sub-option{
    background:#fff;
    border:1px dashed #ddd;
    padding:8px 10px;
    border-radius:8px;
    margin-top:6px;
    margin-right:25px
}
.option-card img{
    max-height:40px;
    border-radius:6px
}
</style>
@endsection

@section('content')
<div class="card-custom">

{{-- ===================== بيانات السؤال ===================== --}}
<h5 class="section-title">📂 بيانات السؤال</h5>

<div class="info-box">
    <strong>الفئة:</strong> {{ $question->category->name_ar ?? '-' }} <br>
    <strong>السؤال (AR):</strong> {{ $question->question_ar }} <br>
    @if($question->question_en)
        <strong>السؤال (EN):</strong> {{ $question->question_en }} <br>
    @endif
    <strong>النوع:</strong> {{ $question->type }} <br>
    <strong>المرحلة:</strong> {{ $question->stageing }} <br>
    <strong>الترتيب:</strong> {{ $question->order }} <br>
    <strong>إجباري:</strong> {{ $question->is_required ? 'نعم' : 'لا' }} <br>
    <strong>الحالة:</strong>
    {!! $question->is_active
        ? '<span class="badge bg-success">مفعّل</span>'
        : '<span class="badge bg-danger">غير مفعّل</span>' !!}
</div>

{{-- ===================== الوصف ===================== --}}
@if($question->description_ar || $question->description_en)
<h5 class="section-title mt-4">📝 الوصف</h5>
<div class="info-box">
    @if($question->description_ar)
        <strong>AR:</strong> {{ $question->description_ar }} <br>
    @endif
    @if($question->description_en)
        <strong>EN:</strong> {{ $question->description_en }}
    @endif
</div>
@endif

{{-- ===================== Settings ===================== --}}
@if($question->settings)
<h5 class="section-title mt-4">⚙️ Settings</h5>
<div class="info-box">
    @if(data_get($question->settings,'hint.ar'))
        <strong>Hint AR:</strong> {{ data_get($question->settings,'hint.ar') }} <br>
    @endif
    @if(data_get($question->settings,'hint.en'))
        <strong>Hint EN:</strong> {{ data_get($question->settings,'hint.en') }} <br>
    @endif
    @if(data_get($question->settings,'titleDescription.ar'))
        <strong>Title Desc AR:</strong> {{ data_get($question->settings,'titleDescription.ar') }} <br>
    @endif
    @if(data_get($question->settings,'titleDescription.en'))
        <strong>Title Desc EN:</strong> {{ data_get($question->settings,'titleDescription.en') }}
    @endif
</div>
@endif

{{-- ===================== Options ===================== --}}
@if($question->options->whereNull('parent_option_id')->count())
<h5 class="section-title mt-4">🧩 الخيارات</h5>

@foreach($question->options->whereNull('parent_option_id') as $option)
<div class="option-card">
    <strong>{{ $option->option_ar }}</strong>
    @if($option->option_en) ({{ $option->option_en }}) @endif

    @if($option->description_ar)
        <div class="text-muted small mt-1">{{ $option->description_ar }}</div>
    @endif

    @if($option->image)
        <div class="mt-2">
            <img src="{{ asset('storage/'.$option->image) }}">
        </div>
    @endif

    @if($option->min !== null || $option->max !== null)
        <div class="small text-primary mt-1">
            Min: {{ $option->min }} | Max: {{ $option->max }}
        </div>
    @endif

    {{-- Sub Options --}}
    @if($option->subOptions->count())
        <div class="mt-2">
            <strong class="text-secondary">الأسئلة الفرعية:</strong>
            @foreach($option->subOptions as $sub)
                <div class="sub-option">
                    {{ $sub->option_ar }}
                    @if($sub->option_en) ({{ $sub->option_en }}) @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endforeach
@endif

{{-- ===================== Slider Preview ===================== --}}
@php
$sliderTypes=['singleSelectionSlider','valueRangeSlider','price','progress'];
@endphp

@if(in_array($question->type,$sliderTypes))
<h5 class="section-title mt-4">🎚️ معاينة السلايدر</h5>

<div class="info-box">
    <div class="d-flex justify-content-between mb-2">
        <span>Min: {{ $question->min_value }}</span>
        <span>Max: {{ $question->max_value }}</span>
    </div>

    <input type="range"
           min="{{ $question->min_value }}"
           max="{{ $question->max_value }}"
           step="{{ $question->step ?? 1 }}"
           value="{{ $question->min_value }}"
           id="sliderPreview"
           style="width:100%">
</div>
@endif

</div>
@endsection
