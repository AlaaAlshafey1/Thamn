@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">اسم الصفحة (مفتاح فريد)</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $appPage->name ?? '') }}" placeholder="مثال: splash, home, product_details" required>
        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">النوع</label>
        <select name="type" class="form-control">
            @php $type = old('type', $appPage->type ?? 'screen'); @endphp
            <option value="screen" {{ $type=='screen' ? 'selected' : '' }}>Screen</option>
            <option value="popup" {{ $type=='popup' ? 'selected' : '' }}>Popup</option>
            <option value="section" {{ $type=='section' ? 'selected' : '' }}>Section</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">العنوان بالعربية</label>
        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $appPage->title_ar ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">العنوان بالإنجليزية</label>
        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $appPage->title_en ?? '') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">لون الخلفية</label>
        <input type="color" name="background_color" class="form-control form-control-color" value="{{ old('background_color', $appPage->background_color ?? '#ffffff') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">لون النص</label>
        <input type="color" name="text_color" class="form-control form-control-color" value="{{ old('text_color', $appPage->text_color ?? '#000000') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">لون الزر</label>
        <input type="color" name="button_color" class="form-control form-control-color" value="{{ old('button_color', $appPage->button_color ?? '#c1953e') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">لون نص الزر</label>
        <input type="color" name="button_text_color" class="form-control form-control-color" value="{{ old('button_text_color', $appPage->button_text_color ?? '#ffffff') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label d-block">بانر الصفحة</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="file" name="banner_image" accept="image/*" class="form-control-file">
            <div>
                <label class="form-check">
                    <input type="checkbox" name="has_banner" value="1" class="form-check-input" {{ old('has_banner', $appPage->has_banner ?? false) ? 'checked' : '' }}>
                    <span class="form-check-label">لديه بانر</span>
                </label>
            </div>
        </div>
        @if(!empty($appPage->banner_image))
            <div class="mt-2">
                <img src="{{ asset('storage/'.$appPage->banner_image) }}" alt="banner" style="max-height:80px;">
            </div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">Layout JSON (اختياري — لتخزين العناصر)</label>
        <textarea name="layout_json" rows="6" class="form-control" placeholder='مثال: {"sections":[{"type":"banner"},{"type":"cta","label":"ابدأ"}]}'>{{ old('layout_json', isset($appPage->layout_json) ? json_encode($appPage->layout_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        <small class="text-muted">ضع JSON يصف عناصر الصفحة إن احتجت — سيستخدمه الموبايل لبناء الواجهة.</small>
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">مفعلة</label>
        <label class="form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $appPage->is_active ?? true) ? 'checked' : '' }}>
            <span class="form-check-label">مفعلة</span>
        </label>
    </div>

    <div class="col-12 mt-3">
        <button class="btn btn-primary" style="background-color:#c1953e;border-color:#c1953e">
            حفظ
        </button>
        <a href="{{ route('app_pages.index') }}" class="btn btn-secondary">إلغاء</a>
    </div>
</div>
