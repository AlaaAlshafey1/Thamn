@extends('layouts.master')
@section('title', isset($color) ? 'تعديل اللون' : 'إضافة لون')

@section('content')
    <div class="card p-3" style="max-width:500px;margin:auto;">
        <h4>{{ isset($color) ? 'تعديل اللون' : 'إضافة لون جديد' }}</h4>
        <form action="{{ isset($color) ? route('colors.update', $color->id) : route('colors.store') }}" method="POST">
            @csrf
            @if(isset($color)) @method('PUT') @endif

            <div class="mb-3">
                <label>المجموعة</label>
                <input type="text" name="group" class="form-control" required
                    value="{{ old('group', $color->group ?? '') }}">
            </div>

            <div class="mb-3">
                <label>المفتاح</label>
                <input type="text" name="key" class="form-control" required value="{{ old('key', $color->key ?? '') }}"
                    @readonly(isset($color))>
            </div>

            <div class="mb-3">
                <label>القيمة</label>
                <input type="color" name="value" class="form-control form-control-color" required
                    value="{{ old('value', $color->value ?? '#000000') }}">
            </div>

            <button type="submit" class="btn btn-success">{{ isset($color) ? 'تحديث' : 'حفظ' }}</button>
        </form>
    </div>
@endsection