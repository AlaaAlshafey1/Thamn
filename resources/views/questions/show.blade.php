@extends('layouts.master')
@section('title', 'عرض السؤال')

@section('css')
<style>
.card-custom {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 25px;
}

/* بيانات السؤال */
.question-detail {
    background-color: #eaf4ff;
    border-left: 5px solid #0d6efd;
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 500;
    color: #0d6efd;
}

/* وصف السؤال */
.description-detail {
    background-color: #fdf3e7;
    border-left: 5px solid #f0ad4e;
    padding: 10px 16px;
    border-radius: 8px;
    margin-bottom: 15px;
    color: #8a6d3b;
}

/* خيارات السؤال */
.option-item {
    margin-bottom: 10px;
    padding: 10px 14px;
    border-radius: 8px;
    background-color: #f0f8ff;
    border: 1px solid #cce5ff;
    font-weight: 500;
    color: #055160;
    display: flex;
    align-items: center;
    gap: 8px;
}

.option-item img {
    max-height: 40px;
    border-radius: 5px;
}
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">عرض السؤال</h4>
        <small class="text-muted">تفاصيل السؤال وخياراته</small>
    </div>
    <div>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back fs-5"></i> رجوع
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="card card-custom">
    <h5 class="mb-3">📂 بيانات السؤال</h5>

    <div class="question-detail">
        <strong>الفئة:</strong> {{ $question->category->name_ar ?? '-' }}<br>
        <strong>السؤال بالعربية:</strong> {{ $question->question_ar }}<br>
        @if($question->question_en)
            <strong>السؤال بالإنجليزية:</strong> {{ $question->question_en }}<br>
        @endif
        <strong>نوع السؤال:</strong> {{ ucfirst($question->type) }}<br>
        <strong>الحالة:</strong>
        @if($question->is_active)
            <span class="badge bg-success">مفعّل</span>
        @else
            <span class="badge bg-danger">غير مفعّل</span>
        @endif
        <br>
        <strong>ترتيب السؤال:</strong> {{ $question->order }}<br>
        <strong>هل السؤال إجباري:</strong> {{ $question->is_required ? 'نعم' : 'لا' }}
    </div>

    @if($question->description_ar || $question->description_en)
        <div class="description-detail">
            @if($question->description_ar)
                <strong>الوصف بالعربية:</strong> {{ $question->description_ar }}<br>
            @endif
            @if($question->description_en)
                <strong>الوصف بالإنجليزية:</strong> {{ $question->description_en }}
            @endif
        </div>
    @endif

    @if($question->options()->get()->isNotEmpty())
        <hr>
        <h5 class="mb-3">⚙️ خيارات السؤال</h5>
        <div class="d-flex flex-wrap gap-2">
            @foreach($question->options()->get() as $option)
                <div class="option-item">
                    {{ $option->option_ar }}
                    @if($option->option_en) ({{ $option->option_en }}) @endif
                    @if($option->image)
                        <img src="{{ asset('storage/'.$option->image) }}" alt="صورة الخيار">
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if($question->type == 'slider')
        <hr>
        <h5 class="mb-3">🎚️ معاينة السلايدر</h5>

        <div class="p-4 rounded" style="background: #f0f8ff; border: 1px solid #cce5ff; direction: rtl;">

            <div class="d-flex justify-content-between mb-2 fw-bold text-primary">
                <span>الحد الأدنى: {{ $question->min_value }}</span>
                <span>الحد الأقصى: {{ $question->max_value }}</span>
            </div>

            <div style="position: relative;">
                <input type="range"
                    min="{{ $question->min_value }}"
                    max="{{ $question->max_value }}"
                    step="{{ $question->step }}"
                    value="{{ $question->min_value }}"
                    id="sliderPreview"
                    class="custom-slider">

                <!-- Bubble value -->
                <div id="sliderBubble" class="slider-bubble">{{ $question->min_value }}</div>
            </div>
        </div>

        <style>
            .custom-slider {
                -webkit-appearance: none;
                width: 100%;
                height: 12px;
                border-radius: 6px;
                background: linear-gradient(to right, #0d6efd 0%, #0d6efd 0%, #dee2e6 0%, #dee2e6 100%);
                outline: none;
                transition: background 0.3s;
            }

            .custom-slider::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: #0d6efd;
                cursor: pointer;
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                transition: background 0.3s, transform 0.2s;
            }

            .custom-slider::-webkit-slider-thumb:hover {
                transform: scale(1.1);
            }

            .slider-bubble {
                position: absolute;
                top: -35px;
                left: 0;
                background: #0d6efd;
                color: #fff;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 500;
                white-space: nowrap;
                transform: translateX(-50%);
                pointer-events: none;
            }
        </style>

        <script>
            const slider = document.getElementById('sliderPreview');
            const bubble = document.getElementById('sliderBubble');

            function updateSlider() {
                const min = parseFloat(slider.min);
                const max = parseFloat(slider.max);
                const val = parseFloat(slider.value);
                const percent = (val - min) / (max - min) * 100;

                // move bubble
                bubble.style.left = `calc(${percent}% )`;
                bubble.innerText = val;

                // update gradient
                slider.style.background = `linear-gradient(to right, #0d6efd 0%, #0d6efd ${percent}%, #dee2e6 ${percent}%, #dee2e6 100%)`;
            }

            slider.addEventListener('input', updateSlider);
            updateSlider(); // initial
        </script>
    @endif


</div>
@endsection
