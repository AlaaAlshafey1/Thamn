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

input.form-control, select.form-select {
    border-radius: 10px;
    padding: 10px 14px;
    min-height: 45px;
    width: 100%;
}

select.form-select {
    background-color: #fff;
    border: 1px solid #ced4da;
    font-size: 15px;
}

.options-list input {
    margin-bottom: 8px;
}

</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary"><i class="bx bx-question-mark"></i> إضافة سؤال جديد</h4>
        <small class="text-muted">قم بإدخال بيانات السؤال واختيار الفئة والنوع</small>
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
    <form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- بيانات السؤال والفئة -->
        <div class="form-section mb-4">
            <h6 class="form-section-title">📂 بيانات الفئة والسؤال</h6>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label">اختر الفئة</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label">اختر نوع السؤال</label>

                        <select name="type" id="typeSelect" class="form-select" required>
                            <option value="">اختر نوع السؤال</option>

                            <!-- Single choice -->
                            <option value="singleChoiceCard">اختيار واحد - كروت</option>
                            <option value="singleChoiceChip">اختيار واحد - Chip</option>
                            <option value="singleChoiceChipWithImage">اختيار واحد - Chip مع صورة</option>
                            <option value="singleChoiceDropdown">اختيار واحد - Dropdown</option>

                            <!-- Multi -->
                            <option value="multiSelection">اختيارات متعددة</option>

                            <!-- Inputs -->
                            <option value="counterInput">عداد أرقام</option>
                            <option value="dateCountInput">إدخال تاريخ / مدة</option>

                            <!-- Sliders -->
                            <option value="singleSelectionSlider">سلايدر اختيار واحد</option>
                            <option value="valueRangeSlider">سلايدر مدى (من - إلى)</option>

                            <!-- Special -->
                            <option value="rating">تقييم (نجوم)</option>
                            <option value="price">سعر</option>
                            <option value="progress">تقدم / نسبة</option>
                            <option value="productAges">أعمار المنتج</option>
                        </select>
                </div>

                <div class="col-6">
                    <label class="form-label">السؤال بالعربية</label>
                    <input type="text" name="question_ar" class="form-control" required>
                </div>

                <div class="col-6">
                    <label class="form-label">السؤال بالإنجليزية</label>
                    <input type="text" name="question_en" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">الوصف بالعربية</label>
                    <textarea name="description_ar" class="form-control">{{ old('description_ar') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">الوصف بالإنجليزية</label>
                    <textarea name="description_en" class="form-control">{{ old('description_en') }}</textarea>
                </div>
                <div class="col-6">
                    <label class="form-label">ترتيب السؤال</label>
                    <input type="number" name="order" class="form-control" value="0">
                </div>
                <div class="col-6">
                    <label class="form-label">السؤال يوجد فى اى مرحلة</label>
                    <select name="stageing" class="form-select" required>
                        <option value="1">المرحلة الأولى</option>
                        <option value="2">المرحلة الثانية</option>
                        <option value="3">المرحلة الثالثة</option>
                        <option value="4">المرحلة الرابعة</option>
                        <option value="5">المرحلة الخامسة</option>
                        <option value="6">المرحلة السادسة</option>
                        <option value="7">المرحلة السابعة</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">الحالة</label>
                    <select name="is_active" class="form-select">
                        <option value="1" selected>مفعّل</option>
                        <option value="0">غير مفعّل</option>
                    </select>
                </div>

                <div class="col-6 d-flex align-items-center gap-2">
                    <input type="checkbox" name="is_required" value="1" id="is_required">
                    <label for="is_required" class="form-label mb-0">هل السؤال إجباري؟</label>
                </div>
            </div>
        </div>

        <!-- خيارات السؤال -->
        <div class="form-section mb-4" id="optionsSection" style="display:none;">
            <h6 class="form-section-title">⚙️ الخيارات (للأسئلة القابلة للاختيار)</h6>
            <div class="options-list" id="optionsList">
                <div class="option-row">
                    <input type="text" name="options_ar[]" class="form-control" placeholder="الخيار بالعربية">
                    <input type="text" name="options_en[]" class="form-control" placeholder="الخيار بالإنجليزية">
                    <input type="file" name="options_image[]" class="form-control" accept="image/*">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف</button>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOption()">إضافة خيار آخر</button>
        </div>
        <div id="sliderSettings" style="display:none;">
            <h6 class="form-section-title mt-3">🎚️ إعدادات السلايدر</h6>

            <div class="row g-3">
                <div class="col-4">
                    <label class="form-label">الحد الأدنى</label>
                    <input type="number" name="min_value" class="form-control">
                </div>

                <div class="col-4">
                    <label class="form-label">الحد الأقصى</label>
                    <input type="number" name="max_value" class="form-control">
                </div>

                <div class="col-4">
                    <label class="form-label">الزيادة (Step)</label>
                    <input type="number" name="step" class="form-control" value="1">
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> حفظ السؤال
            </button>
            <a href="{{ route('questions.index') }}" class="btn btn-light border">
                <i class="bx bx-x-circle"></i> إلغاء
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('typeSelect').addEventListener('change', function() {
    const optionsSection = document.getElementById('optionsSection');
    if(['select','radio','checkbox'].includes(this.value)) {
        optionsSection.style.display = 'block';
    } else {
        optionsSection.style.display = 'none';
    }
});

function addOption() {
    const optionsList = document.getElementById('optionsList');
    const div = document.createElement('div');
    div.className = 'option-row d-flex align-items-center gap-2 mb-2';
    div.innerHTML = `
        <input type="text" name="options_ar[]" class="form-control" placeholder="الخيار بالعربية">
        <input type="text" name="options_en[]" class="form-control" placeholder="الخيار بالإنجليزية">
        <input type="file" name="options_image[]" class="form-control" accept="image/*">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">حذف</button>
    `;
    optionsList.appendChild(div);
}


document.getElementById('typeSelect').addEventListener('change', function () {
    const type = this.value;

    const optionsSection = document.getElementById('optionsSection');
    const sliderSettings = document.getElementById('sliderSettings');

    // الأنواع اللي بتحتاج options
    const optionTypes = [
        'singleChoiceCard',
        'singleChoiceChip',
        'singleChoiceChipWithImage',
        'singleChoiceDropdown',
        'multiSelection'
    ];

    // أنواع السلايدر
    const sliderTypes = [
        'singleSelectionSlider',
        'valueRangeSlider',
        'price',
        'progress'
    ];

    optionsSection.style.display = optionTypes.includes(type) ? 'block' : 'none';
    sliderSettings.style.display = sliderTypes.includes(type) ? 'block' : 'none';
});


</script>
@endsection
