@extends('layouts.master')
@section('title','مركز الإشعارات الذكي')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* إجبار إخفاء مربعات الاختيار القديمة */
    .channel-checkbox {
        position: absolute !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
    }
    .channel-box {
        border: 2px solid #edf2f9 !important;
        border-radius: 15px !important;
        padding: 25px 15px !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        background: #fdfdfd !important;
        text-align: center !important;
        display: block !important;
    }
    .channel-box:hover {
        border-color: #4e73df !important;
        background: #fff !important;
    }
    .channel-checkbox:checked + .channel-box {
        border-color: #4e73df !important;
        background: #f0f4ff !important;
        box-shadow: 0 4px 12px rgba(78,115,223,0.15) !important;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e1e5ef !important;
        border-radius: 8px !important;
        min-height: 45px !important;
    }
    .section-title {
        background: #f8f9fc;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        color: #4e73df;
        font-weight: bold;
    }
</style>
@endpush

@section('page-header')
<div class="page-header py-3 px-3 mt-3 mb-4 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center">
    <h4 class="mb-0 fw-bold text-primary"><i class="bx bx-paper-plane"></i> مركز الإشعارات والرسائل</h4>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-9 mx-auto">
        <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
            @csrf
            
            <!-- الخطوة 1: الجمهور -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="section-title">1. لمن تريد الإرسال؟</div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">اختر الفئة المستهدفة:</label>
                        <select name="recipients_type" class="form-control form-control-lg border-2" id="recipientType">
                            <option value="all">🌍 الكل (جميع المستخدمين والخبراء)</option>
                            <option value="users">👥 كل المستخدمين (Customers)</option>
                            <option value="experts">👨‍🏫 كل الخبراء (Experts)</option>
                            <option value="custom">🎯 مستلم محدد (اختيار بالاسم)</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="customRecipientDiv" style="display: none;">
                        <label class="form-label fw-bold text-danger">ابدأ بكتابة الاسم أو رقم الجوال هنا:</label>
                        <select name="specific_users[]" class="form-control select2" multiple id="specificUsers">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- الخطوة 2: القنوات -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="section-title">2. قنوات الإرسال</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="checkbox" name="channels[]" value="whatsapp" id="chanWhatsapp" class="channel-checkbox" checked>
                            <label for="chanWhatsapp" class="channel-box">
                                <i class="bx bxl-whatsapp fs-1 text-success mb-2"></i>
                                <div class="fw-bold">WhatsApp</div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" name="channels[]" value="push" id="chanPush" class="channel-checkbox">
                            <label for="chanPush" class="channel-box">
                                <i class="bx bx-notification fs-1 text-primary mb-2"></i>
                                <div class="fw-bold">Push Notification</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الخطوة 3: المحتوى -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="section-title">3. محتوى الإشعار والوسائط</div>
                    <div class="mb-3" id="pushTitleDiv" style="display: none;">
                        <label class="form-label fw-bold">عنوان التنبيه</label>
                        <input type="text" name="title" class="form-control border-2" placeholder="أدخل العنوان هنا">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">نص الرسالة</label>
                        <textarea name="message" class="form-control border-2" rows="5" placeholder="اكتب رسالتك..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">إرفاق صورة</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small">إرفاق ملف</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center py-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="bx bx-send me-1"></i> إرسال الآن
                </button>
            </div>

            <input type="hidden" name="recipients" id="finalRecipients" value="all">
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        function initSelect2() {
            $('#specificUsers').select2({
                placeholder: "🔍 ابحث بالاسم أو الرقم واضغط عليه...",
                allowClear: true,
                dir: "rtl"
            });
        }

        $('#recipientType').on('change', function() {
            if($(this).val() === 'custom') {
                $('#customRecipientDiv').show();
                $('#finalRecipients').val('');
                initSelect2();
            } else {
                $('#customRecipientDiv').hide();
                $('#finalRecipients').val($(this).val());
            }
        });

        $('#chanPush').on('change', function() {
            if($(this).is(':checked')) {
                $('#pushTitleDiv').show();
            } else {
                $('#pushTitleDiv').hide();
            }
        });

        $('#notificationForm').on('submit', function() {
            if($('#recipientType').val() === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids.length === 0) {
                    alert('⚠️ يرجى اختيار مستخدم واحد على الأقل');
                    return false;
                }
                $('#finalRecipients').val(ids.join(','));
            }
            return true;
        });
    });
</script>
@endpush
