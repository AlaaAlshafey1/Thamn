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
                    {{ $question->category_id==$category->id?'selected':'' }}>
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
                <option value="{{ $type }}" {{ $question->type==$type?'selected':'' }}>
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
            @for($i=1;$i<=7;$i++)
                <option value="{{ $i }}" {{ $question->stageing==$i?'selected':'' }}>
                    مرحلة {{ $i }}
                </option>
            @endfor
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
        <input type="text" name="settings[hint][ar]" class="form-control"
               value="{{ $question->settings['hint']['ar'] ?? '' }}" placeholder="Hint عربي">
    </div>
    <div class="col-6">
        <input type="text" name="settings[hint][en]" class="form-control"
               value="{{ $question->settings['hint']['en'] ?? '' }}" placeholder="Hint EN">
    </div>
    <div class="col-6">
        <input type="text" name="settings[titleDescription][ar]" class="form-control"
               value="{{ $question->settings['titleDescription']['ar'] ?? '' }}" placeholder="Title Desc عربي">
    </div>
    <div class="col-6">
        <input type="text" name="settings[titleDescription][en]" class="form-control"
               value="{{ $question->settings['titleDescription']['en'] ?? '' }}" placeholder="Title Desc EN">
    </div>
</div>

{{-- ===================== Options ===================== --}}
@php
$optionTypes=[
'singleChoiceCard','singleChoiceChip','singleChoiceChipWithImage',
'singleChoiceDropdown','multiSelection'
];
@endphp

<div id="optionsSection" style="{{ in_array($question->type,$optionTypes)?'display:block':'display:none' }}">
<h6 class="form-section-title mt-4">🧩 الخيارات</h6>
<div id="optionsList">

@foreach($question->options->whereNull('parent_option_id') as $index=>$option)
<div class="option-row mb-2">
<div class="option-row mb-2">
    <input name="options_ar[]" class="form-control mb-1" placeholder="خيار عربي" value="{{ $option->option_ar ?? '' }}">
    <input name="options_en[]" class="form-control mb-1" placeholder="خيار EN" value="{{ $option->option_en ?? '' }}">
    <input name="options_description_ar[]" class="form-control mb-1" placeholder="وصف عربي" value="{{ $option->description_ar ?? '' }}">
    <input name="options_description_en[]" class="form-control mb-1" placeholder="وصف EN" value="{{ $option->description_en ?? '' }}">

    <input type="file" name="options_image[]" class="form-control mb-1">
    @if($option->image)
        <img src="{{ asset('storage/'.$option->image) }}" width="50">
    @endif

    <input type="number" name="options_min[]" class="form-control mb-1" placeholder="Min" value="{{ $option->min ?? '' }}">
    <input type="number" name="options_max[]" class="form-control mb-1" placeholder="Max" value="{{ $option->max ?? '' }}">

    {{-- الحقول الجديدة --}}
    <input type="text" name="options_price[]" class="form-control mb-1" placeholder="Price" value="{{ $option->price ?? '' }}">
    <input type="text" name="options_badge[]" class="form-control mb-1" placeholder="Badge (مثال: monthly,best,ai)" value="{{ $option->badge ?? '' }}">
    <input type="text" name="options_subOptionsTitle[]" class="form-control mb-1" placeholder="عنوان الأسئلة الفرعية" value="{{ $option->sub_options_title ?? '' }}">

    <div class="sub-options-list ms-3">
        @foreach($option->subOptions as $sub)
        <div class="sub-option d-flex gap-2">
            <input name="sub_options_ar[{{ $index }}][]" class="form-control" value="{{ $sub->option_ar }}">
            <input name="sub_options_en[{{ $index }}][]" class="form-control" value="{{ $sub->option_en }}">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">×</button>
        </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-info btn-sm" onclick="addSubOption(this)">إضافة سؤال فرعي</button>
    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف الخيار</button>
</div>
@endforeach

</div>
<button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addOption()">إضافة خيار</button>
</div>

<div class="text-end mt-4">
    <button class="btn btn-primary">💾 حفظ التعديلات</button>
</div>

</form>
</div>

{{-- ===================== JS ===================== --}}
<script>
function addOption() {
    document.getElementById('optionsList').insertAdjacentHTML('beforeend', `
    <div class="option-row mb-2">
        <input name="options_ar[]" class="form-control mb-1" placeholder="خيار عربي">
        <input name="options_en[]" class="form-control mb-1" placeholder="خيار EN">
        <input name="options_description_ar[]" class="form-control mb-1" placeholder="وصف عربي">
        <input name="options_description_en[]" class="form-control mb-1" placeholder="وصف EN">
        <input type="file" name="options_image[]" class="form-control mb-1">
        <input type="number" name="options_min[]" class="form-control mb-1" placeholder="Min">
        <input type="number" name="options_max[]" class="form-control mb-1" placeholder="Max">

        {{-- الحقول الجديدة --}}
        <input type="text" name="options_price[]" class="form-control mb-1" placeholder="Price">
        <input type="text" name="options_badge[]" class="form-control mb-1" placeholder="Badge (مثال: monthly,best,ai)">
        <input type="text" name="options_subOptionsTitle[]" class="form-control mb-1" placeholder="عنوان الأسئلة الفرعية">

        <div class="sub-options-list ms-3"></div>
        <button type="button" class="btn btn-info btn-sm" onclick="addSubOption(this)">إضافة سؤال فرعي</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف</button>
    </div>`);
}


function addSubOption(btn){
btn.previousElementSibling.insertAdjacentHTML('beforeend',`
<div class="sub-option d-flex gap-2">
    <input name="sub_options_ar[][]" class="form-control" placeholder="سؤال فرعي عربي">
    <input name="sub_options_en[][]" class="form-control" placeholder="سؤال فرعي EN">
    <button type="button" class="btn btn-danger btn-sm"
            onclick="this.parentElement.remove()">×</button>
</div>`);
}
</script>
@endsection
