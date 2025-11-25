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
label.form-label { font-weight: 500; color: #333; }
input.form-control, select.form-select {
    border-radius: 10px;
    padding: 10px 14px;
    min-height: 45px;
    width: 100%;
}
.options-list input { margin-bottom: 8px; }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-edit"></i> تعديل السؤال</h4>
        <small class="text-muted">قم بتحديث بيانات السؤال والخيارات</small>
    </div>
    <div>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i> <span>رجوع</span>
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
            <h6 class="form-section-title">📂 بيانات السؤال</h6>
            <div class="row g-3">

                <div class="col-6">
                    <label class="form-label">الفئة</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $question->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">نوع السؤال</label>
                    <select name="type" id="typeSelect" class="form-select" required>
                        <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>نص</option>
                        <option value="number" {{ $question->type == 'number' ? 'selected' : '' }}>رقم</option>
                        <option value="select" {{ $question->type == 'select' ? 'selected' : '' }}>اختيار واحد</option>
                        <option value="radio" {{ $question->type == 'radio' ? 'selected' : '' }}>راديو</option>
                        <option value="checkbox" {{ $question->type == 'checkbox' ? 'selected' : '' }}>اختيارات متعددة</option>
                        <option value="image" {{ $question->type == 'image' ? 'selected' : '' }}>رفع صورة</option>
                        <option value="slider" {{ $question->type == 'slider' ? 'selected' : '' }}>سلايدر</option>
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">السؤال بالعربية</label>
                    <input type="text" name="question_ar" class="form-control" value="{{ $question->question_ar }}" required>
                </div>

                <div class="col-6">
                    <label class="form-label">السؤال بالإنجليزية</label>
                    <input type="text" name="question_en" class="form-control" value="{{ $question->question_en }}">
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف بالعربية</label>
                    <textarea name="description_ar" class="form-control">{{ old('description_ar', $question->description_ar ?? '') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف بالإنجليزية</label>
                    <textarea name="description_en" class="form-control">{{ old('description_en', $question->description_en ?? '') }}</textarea>
                </div>

                <div class="col-6">
                    <label class="form-label">ترتيب السؤال</label>
                    <input type="number" name="order" class="form-control" value="{{ $question->order }}">
                </div>

                <div class="col-6">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $question->is_active ? 'selected' : '' }}>مفعّل</option>
                        <option value="0" {{ !$question->is_active ? 'selected' : '' }}>غير مفعّل</option>
                    </select>
                </div>

                <div class="col-6 d-flex align-items-center gap-2">
                    <input type="checkbox" name="is_required" value="1" id="is_required" {{ $question->is_required ? 'checked' : '' }}>
                    <label for="is_required" class="form-label mb-0">هل السؤال إجباري؟</label>
                </div>
            </div>
        </div>

        <!-- خيارات السؤال -->
        <div class="form-section mb-4" id="optionsSection"
             style="{{ in_array($question->type,['select','radio','checkbox']) ? 'display:block;' : 'display:none;' }}">
            <h6 class="form-section-title">⚙️ الخيارات</h6>
            <div class="options-list" id="optionsList">

                @foreach($question->options()->get() ?? [] as $option)
                    <div class="option-row d-flex align-items-center gap-2 mb-2">
                        <input type="text" name="options_ar[]" class="form-control" value="{{ $option->option_ar }}" placeholder="الخيار بالعربية">
                        <input type="text" name="options_en[]" class="form-control" value="{{ $option->option_en }}" placeholder="الخيار بالإنجليزية">

                        @if($option->image)
                            <img src="{{ asset('storage/'.$option->image) }}" width="40" height="40" class="rounded">
                        @endif

                        <input type="hidden" name="options_id[]" value="{{ $option->id }}">

                        <input type="file" name="options_image[]" class="form-control" accept="image/*">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف</button>
                    </div>
                @endforeach

            </div>
            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOption()">إضافة خيار آخر</button>
        </div>

        <!-- إعدادات السلايدر -->
        <div class="form-section mb-4" id="sliderSettings"
             style="{{ $question->type == 'slider' ? 'display:block;' : 'display:none;' }}">
            <h6 class="form-section-title">🎚️ إعدادات السلايدر</h6>

            <div class="row g-3">
                <div class="col-4">
                    <label class="form-label">الحد الأدنى</label>
                    <input type="number" name="min_value" class="form-control" value="{{ $question->min_value }}">
                </div>

                <div class="col-4">
                    <label class="form-label">الحد الأقصى</label>
                    <input type="number" name="max_value" class="form-control" value="{{ $question->max_value }}">
                </div>

                <div class="col-4">
                    <label class="form-label">الزيادة (Step)</label>
                    <input type="number" name="step" class="form-control" value="{{ $question->step ?? 1 }}">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> حفظ التغييرات
            </button>
            <a href="{{ route('questions.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>

    </form>
</div>

<script>
document.getElementById('typeSelect').addEventListener('change', function() {
    const type = this.value;

    const optionsSection = document.getElementById('optionsSection');
    const sliderSection = document.getElementById('sliderSettings');

    if (['select', 'radio', 'checkbox'].includes(type)) {
        optionsSection.style.display = 'block';
        sliderSection.style.display = 'none';
    }
    else if (type === 'slider') {
        sliderSection.style.display = 'block';
        optionsSection.style.display = 'none';
    }
    else {
        optionsSection.style.display = 'none';
        sliderSection.style.display = 'none';
    }
});

function addOption() {
    const list = document.getElementById('optionsList');
    const div = document.createElement('div');
    div.className = 'option-row d-flex align-items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="options_ar[]" class="form-control" placeholder="الخيار بالعربية">
        <input type="text" name="options_en[]" class="form-control" placeholder="الخيار بالإنجليزية">
        <input type="file" name="options_image[]" class="form-control" accept="image/*">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف</button>
    `;
    list.appendChild(div);
}
</script>

@endsection
