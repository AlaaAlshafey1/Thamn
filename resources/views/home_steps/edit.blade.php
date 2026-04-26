@extends('layouts.master')
@section('title', 'تعديل خطوة')

@section('content')
    <div class="card p-4">
        <h3 class="mb-4">تعديل خطوة</h3>

        <form action="{{ route('home_steps.update', $homeStep->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان (عربي) *</label>
                    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar', $homeStep->title_ar) }}" required>
                    @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Title (English) *</label>
                    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en', $homeStep->title_en) }}" required>
                    @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الفرعي (عربي)</label>
                    <input type="text" name="sub_title_ar" class="form-control @error('sub_title_ar') is-invalid @enderror"
                        value="{{ old('sub_title_ar', $homeStep->sub_title_ar) }}">
                    @error('sub_title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Subtitle (English)</label>
                    <input type="text" name="sub_title_en" class="form-control @error('sub_title_en') is-invalid @enderror"
                        value="{{ old('sub_title_en', $homeStep->sub_title_en) }}">
                    @error('sub_title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الوصف (عربي)</label>
                    <textarea name="desc_ar" class="form-control @error('desc_ar') is-invalid @enderror"
                        rows="3">{{ old('desc_ar', $homeStep->desc_ar) }}</textarea>
                    @error('desc_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Description (English)</label>
                    <textarea name="desc_en" class="form-control @error('desc_en') is-invalid @enderror"
                        rows="3">{{ old('desc_en', $homeStep->desc_en) }}</textarea>
                    @error('desc_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">النوع *</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="steps" {{ old('type', $homeStep->type) == 'steps' ? 'selected' : '' }}>خطوات (Steps)
                        </option>
                        <option value="check" {{ old('type', $homeStep->type) == 'check' ? 'selected' : '' }}>قائمة تحقق
                            (Checklist)</option>
                        <option value="image" {{ old('type', $homeStep->type) == 'image' ? 'selected' : '' }}>صور (Images)
                        </option>
                        <option value="banner" {{ old('type', $homeStep->type) == 'banner' ? 'selected' : '' }}>بنر (banner)
                        </option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', $homeStep->sort_order) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الحالة</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $homeStep->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>

            <hr>

            <hr>

            <h5 class="mb-3">العناصر (Items)</h5>
            <div id="items-container">
                @foreach($homeStep->items as $index => $item)
                    <div class="item-row mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label label-text">العنوان (Label) *</label>
                                <input type="text" name="items[{{ $index }}][label]" class="form-control item-label"
                                    value="{{ $item['label'] ?? '' }}" {{ $homeStep->type == 'banner' ? '' : 'required' }}>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label value-text">الوصف (Value) *</label>
                                <input type="text" name="items[{{ $index }}][value]" class="form-control item-value"
                                    value="{{ $item['value'] ?? '' }}" {{ $homeStep->type == 'banner' ? '' : 'required' }}>
                            </div>
                            <div class="col-md-3 image-field"
                                style="display: {{ in_array($homeStep->type, ['image', 'banner']) ? 'block' : 'none' }};">
                                <label class="form-label">الصورة (Image)</label>
                                <input type="file" name="items[{{ $index }}][image]" class="form-control image-input" accept="image/*" {{ $homeStep->type == 'banner' ? 'multiple' : '' }}>
                                @if(!empty($item['image']))
                                    <div class="mt-2">
                                        <img src="{{ $item['image'] }}" class="img-thumbnail" style="height: 50px;">
                                        <small class="text-muted"><a href="{{ $item['image'] }}" target="_blank">عرض</a></small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item" {{ count($homeStep->items) == 1 ? 'disabled' : '' }}>حذف</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-success mb-3" id="add-item">إضافة عنصر +</button>

            <hr>

            <div class="text-end">
                <a href="{{ route('home_steps.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">تحديث</button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = {{ count($homeStep->items) }};

        // Toggle image fields based on type selection
        function toggleImageFields() {
            const type = document.querySelector('select[name="type"]').value;
            const imageFields = document.querySelectorAll('.image-field');
            const bannerUpload = document.getElementById('banner-multiple-upload');
            const itemLabels = document.querySelectorAll('.item-label');
            const itemValues = document.querySelectorAll('.item-value');
            const labelTexts = document.querySelectorAll('.label-text');
            const valueTexts = document.querySelectorAll('.value-text');

            if (type === 'image' || type === 'banner') {
                imageFields.forEach(field => field.style.display = 'block');
            } else {
                imageFields.forEach(field => field.style.display = 'none');
            }

            if (type === 'banner') {
                bannerUpload.style.display = 'block';
                itemLabels.forEach(el => el.required = false);
                itemValues.forEach(el => el.required = false);
                labelTexts.forEach(el => el.innerText = 'العنوان (اختياري)');
                valueTexts.forEach(el => el.innerText = 'الوصف (اختياري)');
            } else {
                bannerUpload.style.display = 'none';
                itemLabels.forEach(el => el.required = true);
                itemValues.forEach(el => el.required = true);
                labelTexts.forEach(el => el.innerText = 'العنوان (Label) *');
                valueTexts.forEach(el => el.innerText = 'الوصف (Value) *');
            }
        }

        // Initialize on page load
        toggleImageFields();

        // Listen for type changes
        document.querySelector('select[name="type"]').addEventListener('change', toggleImageFields);

        document.getElementById('add-item').addEventListener('click', function () {
            const container = document.getElementById('items-container');
            const type = document.querySelector('select[name="type"]').value;
            const imageFieldDisplay = (type === 'image' || type === 'banner') ? 'block' : 'none';
            const isRequired = type !== 'banner' ? 'required' : '';
            const labelText = type === 'banner' ? 'العنوان (اختياري)' : 'العنوان (Label) *';
            const valueText = type === 'banner' ? 'الوصف (اختياري)' : 'الوصف (Value) *';

            const newItem = `
                        <div class="item-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label label-text">${labelText}</label>
                                    <input type="text" name="items[${itemIndex}][label]" class="form-control item-label" ${isRequired}>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label value-text">${valueText}</label>
                                    <input type="text" name="items[${itemIndex}][value]" class="form-control item-value" ${isRequired}>
                                </div>
                                <div class="col-md-3 image-field" style="display: ${imageFieldDisplay};">
                                    <label class="form-label">الصورة (Image)</label>
                                    <input type="file" name="items[${itemIndex}][image]" class="form-control image-input" accept="image/*" ${type === 'banner' ? 'multiple' : ''}>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">حذف</button>
                                </div>
                            </div>
                        </div>
                    `;
            container.insertAdjacentHTML('beforeend', newItem);
            const lastItem = container.lastElementChild;
            const lastInput = lastItem.querySelector('.image-input');
            if (lastInput) {
                lastInput.addEventListener('change', handleImageChange);
            }
            itemIndex++;
            updateRemoveButtons();
        });

        function handleImageChange(e) {
            const type = document.querySelector('select[name="type"]').value;
            if (type !== 'banner') return;

            const files = e.target.files;
            if (files.length > 1) {
                const container = document.getElementById('items-container');
                const label = e.target.closest('.item-row').querySelector('.item-label').value;
                const value = e.target.closest('.item-row').querySelector('.item-value').value;

                // Create a DataTransfer for the current input (keep only the first file)
                const dtFirst = new DataTransfer();
                dtFirst.items.add(files[0]);
                e.target.files = dtFirst.files;

                // For the rest of the files, create new rows
                for (let i = 1; i < files.length; i++) {
                    const newItemHTML = `
                        <div class="item-row mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label label-text">العنوان (اختياري)</label>
                                    <input type="text" name="items[${itemIndex}][label]" class="form-control item-label" value="${label}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label value-text">الوصف (اختياري)</label>
                                    <input type="text" name="items[${itemIndex}][value]" class="form-control item-value" value="${value}">
                                </div>
                                <div class="col-md-3 image-field" style="display: block;">
                                    <label class="form-label">الصورة (Image)</label>
                                    <input type="file" name="items[${itemIndex}][image]" class="form-control image-input" accept="image/*" multiple>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">حذف</button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', newItemHTML);
                    const lastRow = container.lastElementChild;
                    const lastInput = lastRow.querySelector('.image-input');

                    // Assign the file to the new input using DataTransfer
                    const dt = new DataTransfer();
                    dt.items.add(files[i]);
                    lastInput.files = dt.files;

                    lastInput.addEventListener('change', handleImageChange);
                    itemIndex++;
                }
                updateRemoveButtons();
            }
        }

        // Add listener to initial inputs
        document.querySelectorAll('.image-input').forEach(input => {
            input.addEventListener('change', handleImageChange);
        });

        document.getElementById('items-container').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('.item-row').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.item-row');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-item');
                if (items.length === 1) {
                    removeBtn.disabled = true;
                } else {
                    removeBtn.disabled = false;
                }
            });
        }
    </script>
@endsection
