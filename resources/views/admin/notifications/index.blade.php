@extends('layouts.master')
@section('title', 'مركز الإشعارات')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    body, h1, h2, h3, h4, h5, h6, .btn, .alert, select, input, textarea {
        font-family: 'Tajawal', sans-serif !important;
    }
    .main-content-container {
        direction: rtl;
        text-align: right;
    }
    .custom-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        border: 1px solid #eef0f3;
    }
    .form-label {
        font-weight: 700;
        color: #343a40;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .form-label i {
        color: #c1953e;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        min-height: 42px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #c1953e !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #c1953e !important;
        box-shadow: 0 0 0 0.2rem rgba(193, 149, 62, 0.15) !important;
    }
    .custom-switch-label {
        cursor: pointer;
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush

@section('page-header')
<div class="main-content-container">
    <div class="page-header py-3 px-4 mt-3 mb-4 bg-white shadow-sm rounded-3 border">
        <h4 class="mb-1 fw-bold text-primary"><i class="bx bx-paper-plane"></i> مركز الإشعارات والرسائل</h4>
        <p class="text-muted mb-0 small">أداة بسيطة وسريعة لإرسال الرسائل والتنبيهات المباشرة للمستخدمين والخبراء</p>
    </div>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="bx bx-check-circle fs-20"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="card custom-card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                        @csrf
                        
                        <!-- 1. طريقة وقنوات الإرسال -->
                        <div class="mb-4">
                            <label class="form-label"><i class="bx bx-cog"></i> قنوات الإرسال المفعلة:</label>
                            <div class="p-3 bg-light rounded-3 d-flex flex-wrap gap-4">
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input channel-switch" type="checkbox" name="channels[]" value="whatsapp" id="switchWhatsapp" checked style="width: 40px; height: 20px; cursor: pointer;">
                                    <label class="form-check-label custom-switch-label" for="switchWhatsapp">تفعيل إرسال الواتساب (WhatsApp)</label>
                                </div>
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input channel-switch" type="checkbox" name="channels[]" value="push" id="switchPush" style="width: 40px; height: 20px; cursor: pointer;">
                                    <label class="form-check-label custom-switch-label" for="switchPush">تفعيل إشعارات التطبيق (Push Notification)</label>
                                </div>
                            </div>
                        </div>

                        <!-- 2. نوع المستلم والجمهور -->
                        <div class="mb-4">
                            <label for="recipientTypeSelect" class="form-label"><i class="bx bx-group"></i> الجمهور المستهدف (المستلمون):</label>
                            <select name="recipients_type" class="form-select form-select-lg" id="recipientTypeSelect" style="border-radius: 8px; font-size: 0.95rem;">
                                <option value="all" selected>🌍 الكل (جميع العملاء والخبراء المسجلين)</option>
                                <option value="users">👥 العملاء فقط (المستخدمين)</option>
                                <option value="experts">👨‍🏫 الخبراء فقط</option>
                                <option value="custom">🎯 تحديد مستخدمين محددين بالاسم</option>
                            </select>
                        </div>

                        <!-- قائمة تحديد المستخدمين (مخفية افتراضياً) -->
                        <div class="mb-4" id="customRecipientDiv" style="display: none;">
                            <label class="form-label text-primary"><i class="bx bx-search"></i> ابحث عن المستلم بالاسم أو رقم الجوال:</label>
                            <select name="specific_users[]" class="form-control select2" multiple id="specificUsers" style="width: 100%;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4" style="border-top: 1px solid #eef0f3;">

                        <!-- 3. المحتوى والملحقات -->
                        <!-- عنوان الإشعار (يظهر فقط عند تفعيل Push) -->
                        <div class="mb-4" id="pushTitleDiv" style="display: none;">
                            <label for="inputTitle" class="form-label"><i class="bx bx-heading"></i> عنوان التنبيه:</label>
                            <input type="text" name="title" id="inputTitle" class="form-control form-control-lg" placeholder="أدخل عنوان الإشعار هنا..." style="border-radius: 8px; font-size: 0.95rem;">
                        </div>

                        <!-- نص الرسالة -->
                        <div class="mb-4">
                            <label for="inputMessage" class="form-label"><i class="bx bx-envelope"></i> نص الرسالة:</label>
                            <textarea name="message" id="inputMessage" class="form-control" rows="6" placeholder="اكتب محتوى نص الرسالة هنا بالتفصيل..." required style="border-radius: 8px; font-size: 0.95rem;"></textarea>
                        </div>

                        <!-- ملحقات الواتساب (تظهر فقط عند تفعيل الواتساب) -->
                        <div class="row" id="whatsappMediaDiv">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small"><i class="bx bx-image"></i> إرفاق صورة (إختياري للواتساب):</label>
                                <input type="file" name="image" class="form-control" accept="image/*" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small"><i class="bx bx-file"></i> إرفاق ملف/مستند (إختياري للواتساب):</label>
                                <input type="file" name="file" class="form-control" style="border-radius: 8px;">
                            </div>
                        </div>

                        <!-- زر الإرسال -->
                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="border-radius: 8px; background: #c1953e; border-color: #c1953e; padding: 12px;">
                                <i class="bx bx-send me-1"></i> إرسال الرسالة الآن
                            </button>
                        </div>

                        <input type="hidden" name="recipients" id="finalRecipients" value="all">
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // إدارة خيارات تحديد الجمهور من القائمة المنسدلة
        $('#recipientTypeSelect').on('change', function() {
            let val = $(this).val();
            if(val === 'custom') {
                $('#customRecipientDiv').slideDown(200);
                $('#finalRecipients').val('');
                $('#specificUsers').select2({
                    placeholder: "🔍 اكتب الاسم أو رقم الجوال للاختيار...",
                    allowClear: true,
                    dir: "rtl",
                    width: '100%'
                });
            } else {
                $('#customRecipientDiv').slideUp(200);
                $('#finalRecipients').val(val);
            }
        });

        // إدارة إظهار وإخفاء الحقول ديناميكياً حسب تفعيل القنوات
        function adjustFieldsVisibility() {
            let isPushActive = $('#switchPush').is(':checked');
            let isWhatsappActive = $('#switchWhatsapp').is(':checked');
            
            // عنوان التنبيه (للإشعارات)
            if (isPushActive) {
                $('#pushTitleDiv').slideDown(200);
            } else {
                $('#pushTitleDiv').slideUp(200);
            }
            
            // الصورة والمستند المرفق (للواتساب)
            if (isWhatsappActive) {
                $('#whatsappMediaDiv').slideDown(200);
            } else {
                $('#whatsappMediaDiv').slideUp(200);
            }
        }

        // تشغيل التحقق عند التعديل وعند تحميل الصفحة
        adjustFieldsVisibility();
        $('.channel-switch').on('change', function() {
            adjustFieldsVisibility();
        });

        // التحقق والاعتماد قبل الإرسال
        $('#notificationForm').on('submit', function() {
            let type = $('#recipientTypeSelect').val();
            
            if(type === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids.length === 0) {
                    alert('⚠️ يرجى اختيار مستخدم واحد على الأقل من قائمة البحث');
                    return false;
                }
                $('#finalRecipients').val(ids.join(','));
            } else {
                $('#finalRecipients').val(type);
            }

            let checkedChannels = $('.channel-switch:checked').length;
            if(checkedChannels === 0) {
                alert('⚠️ يرجى تفعيل قناة إرسال واحدة على الأقل (WhatsApp أو إشعارات الهاتف)');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
