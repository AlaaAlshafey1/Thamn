@extends('layouts.master')
@section('title', 'إضافة خطوة جديدة')

@section('content')
    <div class="card p-4">
        <h3 class="mb-4">إضافة خطوة جديدة</h3>

        <form action="{{ route('home_steps.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان (عربي) *</label>
                    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                        value="{{ old('title_ar') }}" required>
                    @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Title (English) *</label>
                    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                        value="{{ old('title_en') }}" required>
                    @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الفرعي (عربي)</label>
                    <input type="text" name="sub_title_ar" class="form-control @error('sub_title_ar') is-invalid @enderror"
                        value="{{ old('sub_title_ar') }}">
                    @error('sub_title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Subtitle (English)</label>
                    <input type="text" name="sub_title_en" class="form-control @error('sub_title_en') is-invalid @enderror"
                        value="{{ old('sub_title_en') }}">
                    @error('sub_title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الوصف (عربي)</label>
                    <textarea name="desc_ar" class="form-control @error('desc_ar') is-invalid @enderror"
                        rows="3">{{ old('desc_ar') }}</textarea>
                    @error('desc_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Description (English)</label>
                    <textarea name="desc_en" class="form-control @error('desc_en') is-invalid @enderror"
                        rows="3">{{ old('desc_en') }}</textarea>
                    @error('desc_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">النوع *</label>
                    <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="steps" {{ old('type') == 'steps' ? 'selected' : '' }}>خطوات (Steps)</option>
                        <option value="check" {{ old('type') == 'check' ? 'selected' : '' }}>قائمة تحقق (Checklist)</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>صور (Images)</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                        value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الحالة</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">العناصر (Items)</h5>
            <div id="items-container">
                <div class="item-row mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">العنوان (Label) *</label>
                            <input type="text" name="items[0][label]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الوصف (Value) *</label>
                            <input type="text" name="items[0][value]" class="form-control">
                        </div>
                        <div class="col-md-3 image-field" style="display: none;">
                            <label class="form-label">رابط الصورة (Image)</label>
                            <input type="file" name="items[0][image]" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item" disabled>حذف</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success mb-3" id="add-item">إضافة عنصر +</button>

            <hr>

            <div class="text-end">
                <a href="{{ route('home_steps.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>

    <script>
        let itemIndex = 1;

        // Toggle image fields based on type selection
        function toggleImageFields() {
            const type = document.querySelector('select[name="type"]').value;
            const imageFields = document.querySelectorAll('.image-field');

            if (type === 'image') {
                imageFields.forEach(field => field.style.display = 'block');
            } else {
                imageFields.forEach(field => field.style.display = 'none');
            }
        }

        // Initialize on page load
        toggleImageFields();

        // Listen for type changes
        document.querySelector('select[name="type"]').addEventListener('change', toggleImageFields);

        document.getElementById('add-item').addEventListener('click', function () {
            const container = document.getElementById('items-container');
            const type = document.querySelector('select[name="type"]').value;
            const imageFieldDisplay = type === 'image' ? 'block' : 'none';

            const newItem = `
                    <div class="item-row mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-5">
                                <label class="form-label">العنوان (Label) *</label>
                                <input type="text" name="items[${itemIndex}][label]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الوصف (Value) *</label>
                                <input type="text" name="items[${itemIndex}][value]" class="form-control" >
                            </div>
                            <div class="col-md-3 image-field" style="display: ${imageFieldDisplay};">
                                <label class="form-label">رابط الصورة (Image)</label>
                                <input type="file" name="items[${itemIndex}][image]" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item">حذف</button>
                            </div>
                        </div>
                    </div>
                `;
            container.insertAdjacentHTML('beforeend', newItem);
            itemIndex++;
            updateRemoveButtons();
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
