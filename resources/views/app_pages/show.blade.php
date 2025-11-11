@extends('layouts.master')
@section('title', 'عرض الصفحة - ' . $appPage->name)

@section('css')
<style>
.phone-preview {
    width: 320px;
    height: 650px;
    border: 10px solid #000;
    border-radius: 40px;
    overflow: hidden;
    margin: 30px auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    position: relative;
}
.phone-top-notch {
    width: 160px;
    height: 25px;
    background: #000;
    border-radius: 0 0 20px 20px;
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
}
.phone-screen {
    width: 100%;
    height: 100%;
    position: relative;
    font-family: 'Cairo', sans-serif;
    background-color: {{ $appPage->background_color ?? '#fff' }};
    color: {{ $appPage->text_color ?? '#000' }};
}

/* ✅ اللوجو أعلى اليمين */
.phone-logo {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 70px;
    height: 70px;
    object-fit: contain;
    z-index: 10;
}

/* ✅ منطقة البانر */
.banner-area {
    position: absolute;
    top: 0;
    width: 100%;
    height: 45%;
    background-color: {{ $appPage->banner_color ?? $appPage->background_color ?? '#fff' }};
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* ✅ صورة البانر في المنتصف */
.banner-area img {
    max-width: 90%;
    max-height: 80%;
    object-fit: contain;
}

/* ✅ النص أسفل البانر */
.text-overlay {
    position: absolute;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255,255,255,0.85);
    border-radius: 20px;
    padding: 20px;
    width: 85%;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.text-overlay h5 {
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
}
.text-overlay p {
    font-size: 14px;
    color: #333;
}

/* ✅ الزر */
.phone-button {
    margin-top: 15px;
    padding: 10px 25px;
    border: none;
    border-radius: 25px;
    background-color: {{ $appPage->button_color ?? '#c1953e' }};
    color: {{ $appPage->button_text_color ?? '#fff' }};
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center" style="direction: rtl;">
    <div>
        <h4 class="fw-bold text-primary mb-1"><i class="bx bx-show"></i> عرض الصفحة</h4>
        <small class="text-muted">معاينة شكل الصفحة داخل الهاتف</small>
    </div>
    <div>
        <a href="{{ route('app_pages.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back"></i> رجوع</a>
    </div>
</div>
@endsection

@section('content')
<div class="text-center mb-4">
    <h5 class="fw-bold">{{ $appPage->title_ar ?? $appPage->name }}</h5>
    <span class="badge bg-{{ $appPage->is_active ? 'success' : 'danger' }}">
        {{ $appPage->is_active ? 'نشطة' : 'غير نشطة' }}
    </span>
</div>

{{-- ✅ شاشة الموبايل --}}
<div class="phone-preview">
    <div class="phone-top-notch"></div>

    <div class="phone-screen">

        {{-- ✅ اللوجو أعلى اليمين --}}
        @if($appPage->logo)
            <img src="{{ asset('storage/'.$appPage->logo) }}" alt="Logo" class="phone-logo">
        @endif

        {{-- ✅ منطقة البانر --}}
        @if($appPage->has_banner)
            <div class="banner-area">
                @if($appPage->banner_image)
                    <img src="{{ asset('storage/'.$appPage->banner_image) }}" alt="Banner">
                @endif
            </div>
        @endif

        {{-- ✅ النص أسفل البانر --}}
        <div class="text-overlay">
            @if($appPage->title_ar)
                <h5>{{ $appPage->title_ar }}</h5>
            @endif
            @if($appPage->description_ar)
                <p>{!! $appPage->description_ar !!}</p>
            @endif
            <button class="phone-button">زر تجريبي</button>
        </div>
    </div>
</div>

{{-- ✅ تفاصيل الصفحة --}}
<div class="mt-5 card p-3 shadow-sm">
    <h5 class="fw-bold text-primary mb-3">تفاصيل الصفحة</h5>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>الاسم:</strong> {{ $appPage->name }}</li>
        <li class="list-group-item"><strong>النوع:</strong> {{ $appPage->type }}</li>
        <li class="list-group-item"><strong>العنوان بالعربية:</strong> {{ $appPage->title_ar ?? '-' }}</li>
        <li class="list-group-item"><strong>العنوان بالإنجليزية:</strong> {{ $appPage->title_en ?? '-' }}</li>
        <li class="list-group-item"><strong>الوصف بالعربية:</strong> {{ $appPage->description_ar ?? '-' }}</li>
        <li class="list-group-item"><strong>الوصف بالإنجليزية:</strong> {{ $appPage->description_en ?? '-' }}</li>
        <li class="list-group-item"><strong>كود JSON:</strong>
            <pre class="mb-0" style="white-space: pre-wrap; background:#f8f9fa; border-radius:6px; padding:8px;">{{ $appPage->layout_json ?? '{}' }}</pre>
        </li>
    </ul>
</div>
@endsection
