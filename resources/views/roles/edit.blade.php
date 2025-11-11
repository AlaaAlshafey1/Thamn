@extends('layouts.master')

@section('title', 'تعديل الدور')

@section('css')
<style>
    .permissions-box {
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        background-color: #fafafa;
    }
    .form-check-label {
        font-size: 14px;
    }
</style>
@endsection

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3" style="direction: rtl;">

    {{-- العنوان والوصف --}}
    <div class="d-flex flex-column">
        <h4 class="content-title mb-1 fw-bold text-primary">تعديل الدور</h4>
        <small class="text-muted">قم بتحديث بيانات الدور أو تعديل الصلاحيات الخاصة به</small>
    </div>

    {{-- زر الرجوع --}}
    <div class="d-flex justify-content-start">
        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bx bx-arrow-back fs-5"></i>
            <span>رجوع</span>
        </a>
    </div>

</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">بيانات الدور</h5>
        <small class="text-muted">قم بتحديث اسم الدور والصلاحيات</small>
    </div>

    <div class="card-body">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">اسم الدور</label>
                    <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold d-block mb-2">الصلاحيات</label>
                <div class="permissions-box">
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-3 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        id="perm-{{ $permission->id }}"
                                        class="form-check-input"
                                        {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                    <label for="perm-{{ $permission->id }}" class="form-check-label">
                                        <i class="bx bx-shield-quarter text-primary"></i> {{ translate_permission($permission->name) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save"></i> حفظ التعديلات
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-light border">
                    <i class="bx bx-x-circle"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


