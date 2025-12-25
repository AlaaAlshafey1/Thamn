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

<form action="{{ route('questions.update',$question->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- ===================== بيانات السؤال ===================== --}}
<h6 class="form-section-title">📂 بيانات السؤال</h6>
<div class="row g-3">

    <div class="col-6">
        <label class="form-label">الفئة</label>
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
        <label class="form-label">نوع السؤال</label>
        <select name="type" id="typeSelect" class="form-select" required>
            @foreach([
                'singleChoiceCard','singleChoiceChip','singleChoiceChipWithImage',
                'singleChoiceDropdown','multiSelection','counterInput','dateCountInput',
                'singleSelectionSlider','valueRangeSlider','rating','price','progress'
            ] as $type)
                <option value="{{ $type }}" {{ $question->type == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-6">
        <label class="form-label">السؤال عربي</label>
        <input type="text" name="question_ar" class="form-control"
               value="{{ $question->question_ar }}" required>
    </div>

    <div class="col-6">
        <label class="form-label">السؤال إنجليزي</label>
        <input type="text" name="question_en" class="form-control"
               value="{{ $question->question_en }}">
    </div>

    <div class="col-12">
        <label class="form-label">الوصف عربي</label>
        <textarea name="description_ar" class="form-control">{{ $question->description_ar }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">الوصف إنجليزي</label>
        <textarea name="description_en" class="form-control">{{ $question->description_en }}</textarea>
    </div>

    <div class="col-6">
        <label class="form-label">المرحلة</label>
        <select name="stageing" class="form-select">
            @foreach($steps as $step)
                <option value="{{ $step->id }}"
                    {{ $question->stageing == $step->id ? 'selected' : '' }}>
                    {{ $step->name_ar }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-6">
        <label class="form-label">ترتيب السؤال</label>
        <input type="number" name="order" class="form-control"
               value="{{ $question->order }}">
    </div>

</div>

{{-- ===================== Settings ===================== --}}
<h6 class="form-section-title mt-4">⚙️ إعدادات العرض</h6>
<div class="row g-3">
    <div class="col-6">
        <label class="form-label">Hint عربي</label>
        <input type="text" name="settings[hint][ar]" class="form-control"
               value="{{ $question->settings['hint']['ar'] ?? '' }}">
    </div>
    <div class="col-6">
        <label class="form-label">Hint إنجليزي</label>
        <input type="text" name="settings[hint][en]" class="form-control"
               value="{{ $question->settings['hint']['en'] ?? '' }}">
    </div>
    <div class="col-6">
        <label class="form-label">Title Description عربي</label>
        <input type="text" name="settings[titleDescription][ar]" class="form-control"
               value="{{ $question->settings['titleDescription']['ar'] ?? '' }}">
    </div>
    <div class="col-6">
        <label class="form-label">Title Description إنجليزي</label>
        <input type="text" name="settings[titleDescription][en]" class="form-control"
               value="{{ $question->settings['titleDescription']['en'] ?? '' }}">
    </div>

    <div class="col-6 d-flex align-items-center gap-2">
        <input type="checkbox" name="settings[addSearch]" value="1"
            {{ ($question->settings['addSearch'] ?? false) ? 'checked' : '' }}>
        <label class="form-label mb-0">تفعيل البحث</label>
    </div>

    <div class="col-6 d-flex align-items-center gap-2" id="cupertinoRow">
        <input type="checkbox" name="settings[useCupertinoPicker]" value="1"
            {{ ($question->settings['useCupertinoPicker'] ?? false) ? 'checked' : '' }}>
        <label class="form-label mb-0">Cupertino Picker</label>
    </div>
</div>

{{-- ===================== Options ===================== --}}
<div id="optionsSection">
<h6 class="form-section-title mt-4">🧩 الخيارات</h6>
<div id="optionsList">
@foreach($question->options->whereNull('parent_option_id') as $index=>$option)
<div class="option-row mb-2 p-2 border rounded">
    <input name="options_ar[]" class="form-control mb-1" value="{{ $option->option_ar }}" placeholder="خيار عربي">
    <input name="options_en[]" class="form-control mb-1" value="{{ $option->option_en }}" placeholder="خيار EN">
    <input name="options_description_ar[]" class="form-control mb-1" value="{{ $option->description_ar }}" placeholder="وصف عربي">
    <input name="options_description_en[]" class="form-control mb-1" value="{{ $option->description_en }}" placeholder="وصف EN">

    <input name="options_order[]" class="form-control mb-1" value="{{ $option->order }}" placeholder="Order">
    <input type="file" name="options_image[]" class="form-control mb-1">
    <input name="options_min[]" class="form-control mb-1" value="{{ $option->min }}" placeholder="Min">
    <input name="options_max[]" class="form-control mb-1" value="{{ $option->max }}" placeholder="Max">
    <input name="options_price[]" class="form-control mb-1" value="{{ $option->price }}" placeholder="Price">
    <input name="options_badge[]" class="form-control mb-1" value="{{ $option->badge }}" placeholder="Badge (مثال: monthly,best,ai)">
    <input name="options_subOptionsTitle[]" class="form-control mb-1" value="{{ $option->sub_options_title }}" placeholder="عنوان الأسئلة الفرعية">

    <div class="sub-options-list ms-3 mt-2">
        @foreach($option->subOptions as $sub)
        <div class="sub-option d-flex gap-2">
            <input name="sub_options_ar[{{ $index }}][]" class="form-control" value="{{ $sub->option_ar }}" placeholder="سؤال فرعي عربي">
            <input name="sub_options_en[{{ $index }}][]" class="form-control" value="{{ $sub->option_en }}" placeholder="سؤال فرعي EN">
            <input name="sub_options_order[{{ $index }}][]" class="form-control" value="{{ $sub->order }}" placeholder="Order">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">حذف</button>
        </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-sm btn-info mt-1" onclick="addSubOption(this)">إضافة سؤال فرعي</button>
    <button type="button" class="btn btn-sm btn-danger mt-1" onclick="this.parentElement.remove()">حذف الخيار الرئيسي</button>
</div>
@endforeach

</div>
<button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOption()">إضافة خيار</button>
</div>

{{-- ===================== Slider ===================== --}}
<div id="sliderSettings">
<h6 class="form-section-title mt-4">🎚️ إعدادات السلايدر</h6>
<div class="row g-3">
    <div class="col-4"><input type="number" name="min_value" class="form-control" value="{{ $question->min_value }}" placeholder="Min"></div>
    <div class="col-4"><input type="number" name="max_value" class="form-control" value="{{ $question->max_value }}" placeholder="Max"></div>
    <div class="col-4"><input type="number" name="step" class="form-control" value="{{ $question->step ?? 1 }}" placeholder="Step"></div>
</div>
</div>


<div class="text-end mt-4">
    <button class="btn btn-primary">💾 حفظ التعديلات</button>
</div>

</form>
</div>

{{-- ===================== JS (نفس create بالظبط) ===================== --}}
<script>
const optionTypes = [
    'singleChoiceCard','singleChoiceChip',
    'singleChoiceChipWithImage','singleChoiceDropdown','multiSelection','progress'
];
const sliderTypes = ['singleSelectionSlider','valueRangeSlider','price'];

function toggleSections(type){
    document.getElementById('optionsSection').style.display =
        optionTypes.includes(type) ? 'block' : 'none';

    document.getElementById('sliderSettings').style.display =
        sliderTypes.includes(type) ? 'block' : 'none';

    document.getElementById('cupertinoRow').style.display =
        type === 'dateCountInput' ? 'flex' : 'none';
}

toggleSections(document.getElementById('typeSelect').value);

document.getElementById('typeSelect').addEventListener('change', function () {
    toggleSections(this.value);
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
        <button type="button" class="btn btn-sm btn-info" onclick="addSubOption(this)">إضافة سؤال فرعي</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">حذف</button>
    </div>`;
    document.getElementById('optionsList').insertAdjacentHTML('beforeend', html);
}


function addSubOption(btn) {
    const optionRow = btn.closest('.option-row');
    const optionIndex = [...document.querySelectorAll('.option-row')].indexOf(optionRow);
    const container = optionRow.querySelector('.sub-options-list');

    const html = `
    <div class="sub-option mb-1 d-flex gap-2 align-items-center">
        <input name="sub_options_ar[${optionIndex}][]" class="form-control" placeholder="سؤال فرعي عربي">
        <input name="sub_options_en[${optionIndex}][]" class="form-control" placeholder="سؤال فرعي EN">
        <input type="number" name="sub_options_order[${optionIndex}][]" class="form-control" placeholder="Order">
        <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">حذف</button>
    </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>
@endsection
