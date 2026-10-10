@extends('layouts.master')
@section('title', 'إضافة سؤال جديد')

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
input.form-control, select.form-select, textarea.form-control {
    border-radius: 10px;
    padding: 10px 14px;
    min-height: 45px;
    width: 100%;
}
.option-row {
    border: 1px dashed #ddd;
    padding: 10px;
    border-radius: 10px;
}
.sub-option {
    margin-right: 25px;
    margin-top: 8px;
}
</style>
@endsection

@section('content')
<div class="form-card">
<form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
@csrf

{{-- ===================== بيانات السؤال ===================== --}}
<h6 class="form-section-title">📂 بيانات السؤال</h6>
<div class="row g-3">

    <div class="col-6">
        <label class="form-label">الفئة</label>
        <select name="category_id" class="form-select" required>
            <option value="">اختر</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-6">
        <label class="form-label">نوع السؤال</label>
        <select name="type" id="typeSelect" class="form-select" required>
            <option value="">اختر</option>
            <option value="singleChoiceCard">اختيار واحد - كروت</option>
            <option value="singleChoiceChip">اختيار واحد - Chip</option>
            <option value="singleChoiceChipWithImage">Chip مع صورة</option>
            <option value="singleChoiceDropdown">Dropdown</option>
            <option value="multiSelection">اختيارات متعددة</option>
            <option value="counterInput">عداد</option>
            <option value="dateCountInput">تاريخ</option>
            <option value="singleSelectionSlider">Slider</option>
            <option value="valueRangeSlider">Slider مدى</option>
            <option value="rating">تقييم</option>
            <option value="price">سعر</option>
            <option value="rateTypeSelection">rate Type Selection</option>
            <option value="productAges">product Ages</option>
            <option value="dropdown">dropdown</option>
            <option value="number">number</option>
            <option value="timeCount">timeCount</option>
            <option value="count">count</option>
            <option value="typeSelect">typeSelect</option>
            <option value="text">text</option>
            <option value="note">note</option>
            <option value="progress">Progress</option>
            <option value="warningLights">لمبات التنبيه (نعم/لا + اختيار متعدد)</option>
        </select>
    </div>
    <div class="col-6">
        <label class="form-label">نوع المجموعة</label>
        <select name="group_type" class="form-select" required>
            <option value="first" {{ old('group_type') == 'first' ? 'selected' : '' }}>First</option>
            <option value="main" {{ old('group_type') == 'main' ? 'selected' : '' }}>Main</option>
            <option value="secondary" {{ old('group_type') == 'secondary' ? 'selected' : '' }}>Secondary</option>
        </select>
    </div>

    <div class="col-6">
        <label class="form-label">نوع الاستخدام</label>
        <select name="flow" class="form-select" required>
            <option value="valuation">تثمين فقط</option>
            <option value="market">السوق فقط</option>
            <option value="both">الاثنين</option>
        </select>
    </div>

    <div class="col-6 d-flex align-items-center mt-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_required" id="isRequiredSwitch" value="1" checked>
            <label class="form-check-label fw-bold" for="isRequiredSwitch">هل السؤال إجباري؟ (Is Required)</label>
        </div>
    </div>

    <div class="col-6">
        <label class="form-label">السؤال عربي</label>
        <input type="text" name="question_ar" class="form-control" required>
    </div>

    <div class="col-6">
        <label class="form-label">السؤال إنجليزي</label>
        <input type="text" name="question_en" class="form-control">
    </div>

    <div class="col-12">
        <label class="form-label">الوصف عربي</label>
        <textarea name="description_ar" class="form-control"></textarea>
    </div>

    <div class="col-12">
        <label class="form-label">الوصف إنجليزي</label>
        <textarea name="description_en" class="form-control"></textarea>
    </div>

    <div class="col-6">
        <label class="form-label">المرحلة</label>
        <select name="stageing" class="form-select">
            <option value="">اختر المرحلة</option>
            @foreach($steps as $step)
                <option value="{{ $step->id }}"
                    {{ old('stageing', $question->stageing ?? '') == $step->id ? 'selected' : '' }}>
                    {{ $step->name_ar }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label class="form-label">ترتيب السؤال</label>
        <input type="number" name="order" class="form-control" placeholder="رقم ترتيب السؤال" value="1">
    </div>


</div>

{{-- ===================== Settings ===================== --}}
<h6 class="form-section-title mt-4">⚙️ إعدادات العرض</h6>
<div class="row g-3">
    <div class="col-6">
        <label class="form-label">Hint عربي</label>
        <input type="text" name="settings[hint][ar]" class="form-control">
    </div>
    <div class="col-6">
        <label class="form-label">Hint إنجليزي</label>
        <input type="text" name="settings[hint][en]" class="form-control">
    </div>
    <div class="col-6">
        <label class="form-label">Title Description عربي</label>
        <input type="text" name="settings[titleDescription][ar]" class="form-control">
    </div>
    <div class="col-6">
        <label class="form-label">Title Description إنجليزي</label>
        <input type="text" name="settings[titleDescription][en]" class="form-control">
    </div>
    <div class="col-6 d-flex align-items-center gap-2">
        <input type="checkbox" name="settings[addSearch]" value="1">
        <label class="form-label mb-0">تفعيل البحث</label>
    </div>
    <div class="col-6 d-flex align-items-center gap-2" id="cupertinoRow" style="display:none">
        <input type="checkbox" name="settings[useCupertinoPicker]" value="1">
        <label class="form-label mb-0">Cupertino Picker</label>
    </div>
</div>

{{-- ===================== Options ===================== --}}
<div id="optionsSection" style="display:none">
<h6 class="form-section-title mt-4">🧩 الخيارات</h6>
<div id="optionsList"></div>
<button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOption()">إضافة خيار</button>
</div>

{{-- ===================== Slider ===================== --}}
<div id="sliderSettings" style="display:none">
<h6 class="form-section-title mt-4">🎚️ إعدادات السلايدر</h6>
<div class="row g-3">
    <div class="col-4"><input type="number" name="min_value" class="form-control" placeholder="Min"></div>
    <div class="col-4"><input type="number" name="max_value" class="form-control" placeholder="Max"></div>
    <div class="col-4"><input type="number" name="step" class="form-control" value="1"></div>
</div>
</div>

<div class="text-end mt-4">
    <button class="btn btn-primary">💾 حفظ</button>
</div>

</form>
</div>

{{-- ===================== JS ===================== --}}
<script>
const optionTypes = [
                'singleChoiceCard','singleChoiceChip','singleChoiceChipWithImage',
                'singleChoiceDropdown','multiSelection','counterInput','dateCountInput',
                'singleSelectionSlider','valueRangeSlider','rating','price','progress','rateTypeSelection','productAges',
                'dropdown','number','timeCount','count','text','note','typeSelect','warningLights'
];
const sliderTypes = [];

document.getElementById('typeSelect').addEventListener('change', function () {
    const type = this.value;
    document.getElementById('optionsSection').style.display =
        optionTypes.includes(type) ? 'block' : 'none';

    document.getElementById('sliderSettings').style.display =
        sliderTypes.includes(type) ? 'block' : 'none';

    document.getElementById('cupertinoRow').style.display =
        type === 'dateCountInput' ? 'flex' : 'none';
});

function addOption() {
    const html = `
    <div class="option-row mb-2 p-2 border rounded">
        <input name="options_ar[]" class="form-control mb-1" placeholder="خيار عربي">
        <input name="options_en[]" class="form-control mb-1" placeholder="خيار EN">
        <input type="text" name="options_description_ar[]" class="form-control mb-1" placeholder="وصف عربي">
        <input type="text" name="options_description_en[]" class="form-control mb-1" placeholder="وصف EN">

        <input type="number" name="options_order[]" class="form-control mb-1" placeholder="Order">
        <input type="file" name="options_image[]" class="form-control mb-1" accept="image/*">
        <input type="number" name="options_min[]" class="form-control mb-1" placeholder="Min">
        <input type="number" name="options_max[]" class="form-control mb-1" placeholder="Max">
        <input type="text" name="options_price[]" class="form-control mb-1" placeholder="Price">
        <input type="text" name="options_badge[]" class="form-control mb-1" placeholder="Badge (مثال: monthly,best,ai)">
        <input type="text" name="options_subOptionsTitle[]" class="form-control mb-1" placeholder="عنوان الأسئلة الفرعية">

        <div class="sub-options-list ms-3 mt-2"></div>
        <button type="button" class="btn btn-sm btn-info mt-1" onclick="addSubOption(this)">إضافة سؤال فرعي</button>
        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="this.parentElement.remove()">حذف الخيار الرئيسي</button>
    </div>`;
    document.getElementById('optionsList').insertAdjacentHTML('beforeend', html);
}


function addSubOption(btn) {
    const container = btn.previousElementSibling;
    const html = `
    <div class="sub-option mb-1 d-flex gap-2 align-items-center">
        <input name="sub_options_ar[][]" class="form-control" placeholder="سؤال فرعي عربي">
        <input name="sub_options_en[][]" class="form-control" placeholder="سؤال فرعي EN">
        <input type="number" name="sub_options_order[][]" class="form-control" placeholder="Order">
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">حذف</button>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
