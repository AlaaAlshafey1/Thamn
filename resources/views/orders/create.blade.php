@extends('layouts.master')
@section('title','إنشاء طلب جديد')

@section('css')
<style>
.container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
h2 {
    color: #c1953e;
    margin-bottom: 20px;
}
.form-group {
    margin-bottom: 15px;
}
label {
    font-weight: bold;
}
input, select, textarea {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ddd;
}
button {
    background-color: #c1953e;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
}
button:hover {
    opacity: 0.9;
}
</style>
@endsection

@section('content')
<div class="container">
    <h2>إنشاء طلب جديد</h2>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>اسم المستخدم</label>
            <select name="user_id" required>
                <option value="">اختر المستخدم</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->first_name . ' ' . $user->last_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>فئة الطلب</label>
            <select name="category_id" required>
                <option value="">اختر الفئة</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>تفاصيل الطلب</label>
            <textarea name="details" rows="5" placeholder="أدخل تفاصيل الطلب" required></textarea>
        </div>

        <div class="form-group">
            <label>السعر الإجمالي (SAR)</label>
            <input type="number" step="0.01" name="total_price" required>
        </div>

        <div class="form-group">
            <label>طريقة التقييم</label>
            <select name="evaluation_type" required>
                <option value="ai">التقييم الذكي (AI)</option>
                <option value="expert">تقييم بواسطة خبير موثوق</option>
                <option value="price">تقييم سعر المنتج</option>
            </select>
        </div>

        <button type="submit">إنشاء الطلب</button>
    </form>
</div>
@endsection
