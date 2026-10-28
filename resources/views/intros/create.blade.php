@extends('layouts.master')
@section('title', 'إضافة صفحة مقدمة جديدة')

@section('content')
    <div class="card p-4">
        <h3 class="mb-4">إضافة صفحة مقدمة جديدة</h3>

        <form action="{{ route('intros.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">نوع الصفحة (Page Type) *</label>
                    @php
                        $predefinedPages = [
                            'welcome' => 'Welcome (شاشة الترحيب الرئيسية)',
                            'signup' => 'Signup (تسجيل حساب جديد)',
                            'verify' => 'Verify (التحقق من رمز OTP)',
                            'login' => 'Login (تسجيل الدخول)',
                            'valuation_export' => 'Valuation Export (تصدير بطاقة التثمين)',
                            'imaginary_image_warning' => 'Imaginary Image Warning (تنبيه الصورة التخيلية)',
                            'valuation_disclaimer' => 'Valuation Disclaimer (إخلاء مسؤولية وتذكير التثمين)',
                        ];
                        $currentPage = old('page', '');
                        $isCustom = !empty($currentPage) && !array_key_exists($currentPage, $predefinedPages);
                    @endphp
                    <select id="page_selector" class="form-control" onchange="handlePageSelect(this)">
                        <option value="">اختر النوع...</option>
                        @foreach($predefinedPages as $key => $label)
                            <option value="{{ $key }}" {{ (!$isCustom && $currentPage == $key) ? 'selected' : '' }}>{{ $label }} [{{ $key }}]</option>
                        @endforeach
                        <option value="custom" {{ $isCustom ? 'selected' : '' }}>+ صفحة أخرى مخصصة (Custom Key)...</option>
                    </select>

                    <input type="text" name="page" id="page_input" class="form-control mt-2 @error('page') is-invalid @enderror"
                           value="{{ $currentPage }}" placeholder="اكتب مفتاح الصفحة بالإنجليزية (مثال: valuation_export)" required
                           style="{{ $isCustom ? 'display:block;' : 'display:none;' }}">
                    @error('page')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">الصورة (Image)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                        accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">العنوان (عربي) *</label>
                    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar') }}" placeholder="مثال: تمام عليك .." required>
                    @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Title (English)</label>
                    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en') }}" placeholder="e.g. Well done ..">
                    @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">العنوان الفرعي (عربي)</label>
                    <input type="text" name="sub_title_ar" class="form-control @error('sub_title_ar') is-invalid @enderror"
                        value="{{ old('sub_title_ar') }}" placeholder="مثال: أنت على بعد خطوة واحدة من تصدير بطاقة التثمين لسلعتك !!">
                    @error('sub_title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Subtitle (English)</label>
                    <input type="text" name="sub_title_en" class="form-control @error('sub_title_en') is-invalid @enderror"
                        value="{{ old('sub_title_en') }}">
                    @error('sub_title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">الوصف (عربي)</label>
                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
                        rows="3">{{ old('description_ar') }}</textarea>
                    @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Description (English)</label>
                    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                        rows="3">{{ old('description_en') }}</textarea>
                    @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">الحالة</label>
                    <div class="form-check mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label font-weight-bold mr-4" for="is_active">تفعيل الصفحة (نشط)</label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="text-end">
                <a href="{{ route('intros.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save ml-1"></i> حفظ الصفحة</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    function handlePageSelect(select) {
        const val = select.value;
        const input = document.getElementById('page_input');
        if (val === 'custom') {
            input.style.display = 'block';
            input.value = '';
            input.focus();
        } else {
            input.style.display = 'none';
            input.value = val;
        }
    }
</script>
@endsection
