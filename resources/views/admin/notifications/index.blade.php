@extends('layouts.master')
@section('title', 'مركز الإشعارات')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .notify-container {
        direction: rtl;
        text-align: right;
        font-family: 'Tajawal', sans-serif;
    }
    .form-group-title {
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
    }
    .emoji-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
        background: #f8fafc;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .emoji-btn {
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 1.15rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .emoji-btn:hover {
        background: #f1f5f9;
        transform: scale(1.15);
        border-color: #94a3b8;
    }
    .preview-box {
        background: rgba(193, 149, 62, 0.05);
        border: 2px dashed rgba(193, 149, 62, 0.2);
        border-radius: 12px;
        padding: 18px;
        margin-top: 20px;
    }
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #718096;
        font-weight: bold;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding-bottom: 6px;
    }
    .preview-title {
        font-size: 0.95rem;
        font-weight: bold;
        color: #1a202c;
        margin-bottom: 4px;
        word-break: break-word;
    }
    .preview-body {
        font-size: 0.85rem;
        color: #4a5568;
        line-height: 1.4;
        word-break: break-word;
    }
    .channel-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.2s ease;
        margin: 0;
    }
    .channel-checkbox:checked + .channel-badge {
        border-color: #c1953e;
        background: rgba(193, 149, 62, 0.08);
        color: #c1953e;
    }
    .select2-container--default .select2-selection--single {
        height: 46px !important;
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        padding-top: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }
</style>
@endpush

@section('page-header')
<div class="notify-container container-fluid">
    <div class="page-header py-3 px-4 mt-3 mb-4 bg-white shadow-sm rounded-3 border">
        <h4 class="mb-1 fw-bold text-primary"><i class="bx bx-paper-plane"></i> مركز إرسال الإشعارات المباشرة</h4>
        <p class="text-muted mb-0 small">أداة لإرسال الإشعارات الفورية والتنبيهات المخصصة إلى هواتف العملاء المشتركين</p>
    </div>
</div>
@endsection

@section('content')
<div class="notify-container container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 8px;">
                    <i class="bx bx-check-circle fs-20 text-success"></i>
                    <span class="text-success fw-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold"><i class="bx bx-cog text-warning align-middle ml-1"></i> خيارات صياغة التنبيه</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                        @csrf
                        
                        <!-- 1. قنوات الإرسال -->
                        <div class="mb-4">
                            <label class="form-group-title d-block"><i class="bx bx-broadcast text-muted ml-1"></i> قناة الإرسال المفعلة:</label>
                            <div class="d-flex gap-3">
                                <label class="m-0">
                                    <input class="channel-checkbox d-none" type="checkbox" name="channels[]" value="push" id="switchPush" checked>
                                    <div class="channel-badge">
                                        <i class="bx bx-mobile-vibration fs-20"></i>
                                        <span>إشعارات الهاتف (Push Notification)</span>
                                    </div>
                                </label>
                                <label class="m-0">
                                    <input class="channel-checkbox d-none" type="checkbox" name="channels[]" value="whatsapp" id="switchWhatsapp">
                                    <div class="channel-badge">
                                        <i class="bx bxl-whatsapp fs-20"></i>
                                        <span>رسائل الواتساب (WhatsApp)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- 2. نوع المستلم والجمهور -->
                        <div class="mb-4">
                            <label class="form-group-title d-block" for="recipientTypeSelect"><i class="bx bx-group text-muted ml-1"></i> الجمهور المستهدف (المستلمون):</label>
                            <select name="recipients_type" class="form-select form-select-lg" id="recipientTypeSelect" style="border-radius: 8px; font-size: 0.95rem; height: 46px;">
                                <option value="users" selected>👥 إرسال جماعي (لكل العملاء)</option>
                                <option value="custom">🎯 إرسال لمستخدم محدد (بالاسم أو الجوال)</option>
                            </select>
                        </div>

                        <!-- قائمة تحديد المستخدم (يظهر فقط من لديهم توكنات) -->
                        <div class="mb-4" id="customRecipientDiv" style="display: none;">
                            <label class="form-group-title d-block" for="specificUsers"><i class="bx bx-search text-muted ml-1"></i> اختر العميل المستهدف (يظهر فقط من لديهم هواتف مسجلة):</label>
                            <select name="specific_users[]" class="form-control select2" id="specificUsers" style="width: 100%;">
                                <option value="">-- ابحث بالاسم أو رقم الجوال --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4" style="border-top: 1px solid #edf2f7;">

                        <!-- 3. عنوان الإشعار (Header) -->
                        <div class="mb-4" id="pushTitleDiv">
                            <label for="inputTitle" class="form-group-title d-block"><i class="bx bx-heading text-muted ml-1"></i> عنوان التنبيه (Header):</label>
                            <input type="text" name="title" id="inputTitle" class="form-control form-control-lg" placeholder="اكتب عنوان الإشعار هنا..." value="يا هلا والله بالغالين! 📣" style="border-radius: 8px; font-size: 0.95rem;">
                            
                            <!-- بار الإيموجي للعنوان -->
                            <div class="emoji-bar" data-target="inputTitle">
                                <button type="button" class="emoji-btn">📣</button>
                                <button type="button" class="emoji-btn">🎉</button>
                                <button type="button" class="emoji-btn">🧡</button>
                                <button type="button" class="emoji-btn">👋</button>
                                <button type="button" class="emoji-btn">🔔</button>
                                <button type="button" class="emoji-btn">🔥</button>
                                <button type="button" class="emoji-btn">✅</button>
                                <button type="button" class="emoji-btn">⚖️</button>
                                <button type="button" class="emoji-btn">🔍</button>
                                <button type="button" class="emoji-btn">🔄</button>
                            </div>
                        </div>

                        <!-- 4. محتوى الرسالة (Body) -->
                        <div class="mb-4">
                            <label for="inputMessage" class="form-group-title d-block"><i class="bx bx-envelope text-muted ml-1"></i> محتوى الإشعار (Body):</label>
                            <textarea name="message" id="inputMessage" class="form-control" rows="5" placeholder="اكتب نص الإشعار هنا بالتفصيل..." required style="border-radius: 8px; font-size: 0.95rem;"></textarea>
                            
                            <!-- بار الإيموجي للمحتوى -->
                            <div class="emoji-bar" data-target="inputMessage">
                                <button type="button" class="emoji-btn">📣</button>
                                <button type="button" class="emoji-btn">🎉</button>
                                <button type="button" class="emoji-btn">🧡</button>
                                <button type="button" class="emoji-btn">👋</button>
                                <button type="button" class="emoji-btn">🔔</button>
                                <button type="button" class="emoji-btn">🔥</button>
                                <button type="button" class="emoji-btn">✅</button>
                                <button type="button" class="emoji-btn">⚖️</button>
                                <button type="button" class="emoji-btn">🔍</button>
                                <button type="button" class="emoji-btn">🔄</button>
                            </div>
                        </div>

                        <!-- ملفات الواتساب (تظهر فقط عند تحديد الواتساب) -->
                        <div class="row" id="whatsappMediaDiv" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-group-title small d-block"><i class="bx bx-image text-muted ml-1"></i> إرفاق صورة (للواتساب فقط):</label>
                                <input type="file" name="image" class="form-control" accept="image/*" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-group-title small d-block"><i class="bx bx-file text-muted ml-1"></i> إرفاق ملف/مستند (للواتساب فقط):</label>
                                <input type="file" name="file" class="form-control" style="border-radius: 8px;">
                            </div>
                        </div>

                        <!-- 5. المعاينة المباشرة المدمجة -->
                        <div class="preview-box" id="livePreviewContainer">
                            <div class="preview-header">
                                <span><i class="bx bx-show align-middle"></i> معاينة حية لشاشة الهاتف</span>
                                <span>الآن</span>
                            </div>
                            <div class="preview-title" id="previewTitle">يا هلا والله بالغالين! 📣</div>
                            <div class="preview-body" id="previewBody">اكتب محتوى الرسالة لتشاهد المعاينة هنا فوراً...</div>
                        </div>

                        <!-- زر الإرسال -->
                        <div class="text-center pt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="border-radius: 8px; background: #c1953e; border-color: #c1953e; padding: 12px; font-size: 1.1rem;">
                                <i class="bx bx-send me-1"></i> إرسال الإشعار الآن
                            </button>
                        </div>

                        <input type="hidden" name="recipients" id="finalRecipients" value="users">
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
        
        // تفعيل محرك البحث Select2
        $('#specificUsers').select2({
            placeholder: "🔍 ابحث بالاسم أو رقم الجوال للاختيار...",
            allowClear: true,
            dir: "rtl"
        });

        // إدارة إدخال الإيموجي بالنقر
        $('.emoji-btn').on('click', function() {
            let emoji = $(this).text();
            let targetId = $(this).parent().data('target');
            let input = $('#' + targetId);
            
            let pos = input[0].selectionStart;
            let val = input.val();
            let newVal = val.slice(0, pos) + emoji + val.slice(pos);
            
            input.val(newVal);
            input.focus();
            
            // تحديث مؤشر الكتابة بعد الإيموجي المدرج
            input[0].setSelectionRange(pos + emoji.length, pos + emoji.length);
            
            updateLivePreview();
        });

        // تحديث المعاينة الحية
        function updateLivePreview() {
            let titleVal = $('#inputTitle').val().trim();
            let bodyVal = $('#inputMessage').val().trim();
            
            $('#previewTitle').text(titleVal ? titleVal : 'تنبيه من تطبيق ثمن');
            $('#previewBody').text(bodyVal ? bodyVal : 'محتوى الرسالة يظهر هنا...');
        }

        $('#inputTitle').on('input keyup change', updateLivePreview);
        $('#inputMessage').on('input keyup change', updateLivePreview);

        // تغيير الجمهور المستهدف
        $('#recipientTypeSelect').on('change', function() {
            let val = $(this).val();
            if(val === 'custom') {
                $('#customRecipientDiv').slideDown(200);
                $('#finalRecipients').val('');
            } else {
                $('#customRecipientDiv').slideUp(200);
                $('#finalRecipients').val(val);
            }
        });

        // إظهار حقول الملحقات حسب القنوات
        function adjustFieldsVisibility() {
            let isPushActive = $('#switchPush').is(':checked');
            let isWhatsappActive = $('#switchWhatsapp').is(':checked');
            
            if (isPushActive) {
                $('#pushTitleDiv').slideDown(200);
                $('#livePreviewContainer').slideDown(200);
            } else {
                $('#pushTitleDiv').slideUp(200);
                $('#livePreviewContainer').slideUp(200);
            }
            
            if (isWhatsappActive) {
                $('#whatsappMediaDiv').slideDown(200);
            } else {
                $('#whatsappMediaDiv').slideUp(200);
            }
        }

        adjustFieldsVisibility();
        updateLivePreview();

        $('.channel-checkbox').on('change', function() {
            adjustFieldsVisibility();
        });

        // التحقق قبل الإرسال
        $('#notificationForm').on('submit', function() {
            let type = $('#recipientTypeSelect').val();
            
            if(type === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids === '') {
                    alert('⚠️ يرجى اختيار العميل المستهدف أولاً من قائمة البحث.');
                    return false;
                }
                $('#finalRecipients').val(ids);
            } else {
                $('#finalRecipients').val(type);
            }

            let checkedChannels = $('.channel-checkbox:checked').length;
            if(checkedChannels === 0) {
                alert('⚠️ يرجى تحديد قناة إرسال واحدة على الأقل (WhatsApp أو إشعارات الهاتف).');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
