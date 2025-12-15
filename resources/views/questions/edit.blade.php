@extends('layouts.master')
@section('title', 'تعديل السؤال')

@section('css')
<style>
.form-card {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 25px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #0d6efd;
    margin-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 5px;
}

label.form-label {
    font-weight: 500;
    color: #333;
}

input.form-control, select.form-select {
    border-radius: 10px;
    padding: 10px 14px;
    min-height: 45px;
    width: 100%;
}

.options-list input {
    margin-bottom: 8px;
}
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">
            <i class="bx bx-edit"></i> تعديل السؤال
        </h4>
        <small class="text-muted">قم بتحديث بيانات السؤال</small>
    </div>
    <div>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> رجوع
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="form-card">
<form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- بيانات السؤال -->
    <div class="form-section mb-4">
        <h6 class="form-section-title">📂 بيانات الفئة والسؤال</h6>
        <div class="row g-3">

            <div class="col-6">
                <label class="form-label">اختر الفئة</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $question->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name_ar }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6">
                <label class="form-label">اختر نوع السؤال</label>
                <select name="type" id="typeSelect" class="form-select" required>
                    <option value="">اختر نوع السؤال</option>

                    <option value="singleChoiceCard" {{ $question->type=='singleChoiceCard'?'selected':'' }}>اختيار واحد - كروت</option>
                    <option value="singleChoiceChip" {{ $question->type=='singleChoiceChip'?'selected':'' }}>اختيار واحد - Chip</option>
                    <option value="singleChoiceChipWithImage" {{ $question->type=='singleChoiceChipWithImage'?'selected':'' }}>اختيار واحد - Chip مع صورة</option>
                    <option value="singleChoiceDropdown" {{ $question->type=='singleChoiceDropdown'?'selected':'' }}>اختيار واحد - Dropdown</option>

                    <option value="multiSelection" {{ $question->type=='multiSelection'?'selected':'' }}>اختيارات متعددة</option>

                    <option value="counterInput" {{ $question->type=='counterInput'?'selected':'' }}>عداد أرقام</option>
                    <option value="dateCountInput" {{ $question->type=='dateCountInput'?'selected':'' }}>إدخال تاريخ / مدة</option>

                    <option value="singleSelectionSlider" {{ $question->type=='singleSelectionSlider'?'selected':'' }}>سلايدر اختيار واحد</option>
                    <option value="valueRangeSlider" {{ $question->type=='valueRangeSlider'?'selected':'' }}>سلايدر مدى</option>

                    <option value="rating" {{ $question->type=='rating'?'selected':'' }}>تقييم</option>
                    <option value="price" {{ $question->type=='price'?'selected':'' }}>سعر</option>
                    <option value="progress" {{ $question->type=='progress'?'selected':'' }}>تقدم</option>
                    <option value="productAges" {{ $question->type=='productAges'?'selected':'' }}>أعمار المنتج</option>
                </select>
            </div>

            <div class="col-6">
                <label class="form-label">السؤال بالعربية</label>
                <input type="text" name="question_ar" class="form-control"
                       value="{{ $question->question_ar }}" required>
            </div>

            <div class="col-6">
                <label class="form-label">السؤال بالإنجليزية</label>
                <input type="text" name="question_en" class="form-control"
                       value="{{ $question->question_en }}">
            </div>

            <div class="col-12">
                <label class="form-label">الوصف بالعربية</label>
                <textarea name="description_ar" class="form-control">{{ $question->description_ar }}</textarea>
            </div>

            <div class="col-12">
                <label class="form-label">الوصف بالإنجليزية</label>
                <textarea name="description_en" class="form-control">{{ $question->description_en }}</textarea>
            </div>

            <div class="col-6">
                <label class="form-label">ترتيب السؤال</label>
                <input type="number" name="order" class="form-control" value="{{ $question->order }}">
            </div>

            <div class="col-6">
                    <label class="form-label">السؤال يوجد فى اى مرحلة</label>
                <select name="stageing" class="form-select">
                    @for($i=1;$i<=7;$i++)
                        <option value="{{ $i }}" {{ $question->stageing==$i?'selected':'' }}>
                            المرحلة {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-6">
                <label class="form-label">الحالة</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $question->is_active?'selected':'' }}>مفعّل</option>
                    <option value="0" {{ !$question->is_active?'selected':'' }}>غير مفعّل</option>
                </select>
            </div>

            <div class="col-6 d-flex align-items-center gap-2">
                <input type="checkbox" name="is_required" value="1"
                       {{ $question->is_required?'checked':'' }}>
                <label class="form-label mb-0">هل السؤال إجباري؟</label>
            </div>

        </div>
    </div>

    <!-- الخيارات -->
    @php
        $optionTypes = [
            'singleChoiceCard',
            'singleChoiceChip',
            'singleChoiceChipWithImage',
            'singleChoiceDropdown',
            'multiSelection'
        ];
    @endphp

    <div class="form-section mb-4" id="optionsSection"
         style="{{ in_array($question->type,$optionTypes)?'display:block':'display:none' }}">
        <h6 class="form-section-title">⚙️ الخيارات</h6>

        <div class="options-list" id="optionsList">
            @foreach($question->options ?? collect() as $option)
                <div class="option-row d-flex align-items-center gap-2 mb-2">
                    <input type="text" name="options_ar[]" class="form-control"
                           value="{{ $option->option_ar }}">
                    <input type="text" name="options_en[]" class="form-control"
                           value="{{ $option->option_en }}">

                    @if($option->image)
                        <img src="{{ asset('storage/'.$option->image) }}" width="40">
                    @endif

                    <input type="hidden" name="options_id[]" value="{{ $option->id }}">
                    <input type="file" name="options_image[]" class="form-control">

                    <button type="button" class="btn btn-danger btn-sm"
                            onclick="this.parentElement.remove()">حذف</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOption()">إضافة خيار</button>
    </div>

    <!-- السلايدر -->
    @php
        $sliderTypes = [
            'singleSelectionSlider',
            'valueRangeSlider',
            'price',
            'progress'
        ];
    @endphp

    <div id="sliderSettings"
         style="{{ in_array($question->type,$sliderTypes)?'display:block':'display:none' }}">
        <h6 class="form-section-title mt-3">🎚️ إعدادات السلايدر</h6>
        <div class="row g-3">
            <div class="col-4">
                <label class="form-label">الحد الأدنى</label>
                <input type="number" name="min_value" class="form-control"
                       value="{{ $question->min_value }}">
            </div>
            <div class="col-4">
                <label class="form-label">الحد الأقصى</label>
                <input type="number" name="max_value" class="form-control"
                       value="{{ $question->max_value }}">
            </div>
            <div class="col-4">
                <label class="form-label">Step</label>
                <input type="number" name="step" class="form-control"
                       value="{{ $question->step ?? 1 }}">
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bx bx-save"></i> حفظ التعديلات
        </button>
        <a href="{{ route('questions.index') }}" class="btn btn-light border">
            إلغاء
        </a>
    </div>
</form>
</div>

<script>
document.getElementById('typeSelect').addEventListener('change', function () {
    const type = this.value;

    const optionTypes = [
        'singleChoiceCard',
        'singleChoiceChip',
        'singleChoiceChipWithImage',
        'singleChoiceDropdown',
        'multiSelection'
    ];

    const sliderTypes = [
        'singleSelectionSlider',
        'valueRangeSlider',
        'price',
        'progress'
    ];

    document.getElementById('optionsSection').style.display =
        optionTypes.includes(type) ? 'block' : 'none';

    document.getElementById('sliderSettings').style.display =
        sliderTypes.includes(type) ? 'block' : 'none';
});

function addOption() {
    const list = document.getElementById('optionsList');
    const div = document.createElement('div');
    div.className = 'option-row d-flex align-items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="options_ar[]" class="form-control">
        <input type="text" name="options_en[]" class="form-control">
        <input type="file" name="options_image[]" class="form-control">
        <button type="button" class="btn btn-danger btn-sm"
                onclick="this.parentElement.remove()">حذف</button>
    `;
    list.appendChild(div);
}
</script>
@endsection
