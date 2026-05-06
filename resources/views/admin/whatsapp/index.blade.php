@extends('layouts.master')
@section('title','إعدادات واتساب')

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-3 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-1 fw-bold text-primary">إعدادات واتساب (UltraMsg)</h4>
        <small class="text-muted">اربط جهازك لإرسال الإشعارات تلقائياً</small>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">حالة الإتصال</h5>
            </div>
            <div class="card-body text-center">
                @if(isset($status['account_status']))
                    @if($status['account_status'] == 'authenticated')
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle fs-1 d-block mb-2"></i>
                            متصل بنجاح
                        </div>
                        <p>الجهاز مرتبط الآن ويمكنك إرسال الرسائل.</p>
                        <form action="{{ route('admin.whatsapp.logout') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من قطع الاتصال؟')">
                            @csrf
                            <button type="submit" class="btn btn-danger">قطع الاتصال (تسجيل الخروج)</button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bx bx-error fs-1 d-block mb-2"></i>
                            غير متصل
                        </div>
                        <p>يرجى مسح الرمز المقابل لربط الجهاز.</p>
                        <p class="text-muted small">حالة الحساب: {{ $status['account_status'] }}</p>
                    @endif
                @else
                    <div class="alert alert-danger">
                        فشل في جلب الحالة. 
                        @if(isset($status['message']))
                            <br> <small>{{ $status['message'] }}</small>
                        @endif
                        <hr>
                        تأكد من صحة Instance ID و Token في ملف .env
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0 text-white">مسح الرمز (QR Code)</h5>
            </div>
            <div class="card-body text-center">
                @if(isset($status['account_status']) && $status['account_status'] != 'authenticated')
                    <img src="{{ $qrCode }}" alt="WhatsApp QR Code" class="img-fluid border p-2 bg-white" style="max-width: 300px;">
                    <div class="mt-3">
                        <p class="text-danger fw-bold">الباركود صالح لمدة 45 ثانية فقط.</p>
                        <p>افتح تطبيق WhatsApp على هاتفك واذهب إلى "الأجهزة المرتبطة" ثم امسح الرمز.</p>
                        <button onclick="window.location.reload()" class="btn btn-outline-primary btn-sm">تحديث الرمز</button>
                    </div>
                @elseif(isset($status['account_status']) && $status['account_status'] == 'authenticated')
                    <div class="py-5">
                        <i class="bx bxl-whatsapp text-success" style="font-size: 100px;"></i>
                        <h4 class="mt-3 text-success">تم الربط بنجاح!</h4>
                    </div>
                @else
                    <p>لا يمكن عرض الرمز حالياً.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">بيانات الربط الحالية</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Instance ID</th>
                        <td><code>{{ config('services.ultramsg.instance') }}</code></td>
                    </tr>
                    <tr>
                        <th>Token</th>
                        <td><code>{{ config('services.ultramsg.token') }}</code></td>
                    </tr>
                </table>
                <p class="text-muted small">يمكنك تعديل هذه البيانات من ملف <code>.env</code></p>
            </div>
        </div>
    </div>
</div>
@endsection
