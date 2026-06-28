@extends('layouts.master')
@section('title', 'مركز الإشعارات الذكي')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Premium Modern Styles */
    body, h1, h2, h3, h4, h5, h6, .btn, .alert, select, input, textarea {
        font-family: 'Tajawal', sans-serif !important;
    }
    .main-content-container {
        direction: rtl;
        text-align: right;
    }
    .custom-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        transition: all 0.3s ease;
        border: 1px solid #f0f2f5;
    }
    .custom-card:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }
    .step-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .step-number {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #c1953e, #d4af37);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: bold;
    }
    
    /* Interactive Card Grid for Recipients */
    .recipient-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    @media (max-width: 768px) {
        .recipient-grid {
            grid-template-columns: 1fr;
        }
    }
    .recipient-option-card {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 20px 15px;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
    }
    .recipient-option-card:hover {
        border-color: #c1953e;
        background: #ffffff;
        transform: translateY(-2px);
    }
    .recipient-option-card.active {
        border-color: #c1953e;
        background: rgba(193, 149, 62, 0.05);
        box-shadow: 0 5px 15px rgba(193, 149, 62, 0.1);
    }
    .recipient-option-card .icon-wrapper {
        font-size: 1.8rem;
        color: #64748b;
        transition: all 0.25s ease;
    }
    .recipient-option-card.active .icon-wrapper {
        color: #c1953e;
        transform: scale(1.1);
    }
    .recipient-option-card .card-title {
        font-weight: 700;
        color: #334155;
        font-size: 1rem;
        margin: 0;
    }
    .recipient-option-card .card-desc {
        font-size: 0.8rem;
        color: #64748b;
        margin: 0;
    }

    /* Channel Selection Toggle Cards */
    .channel-checkbox {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .channel-card {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        text-align: center;
    }
    .channel-card:hover {
        border-color: #c1953e;
        background: #ffffff;
    }
    .channel-checkbox:checked + .channel-card {
        border-color: #c1953e;
        background: rgba(193, 149, 62, 0.05);
        box-shadow: 0 5px 15px rgba(193, 149, 62, 0.1);
    }
    .channel-card.whatsapp-card i {
        color: #25d366;
        font-size: 2.2rem;
    }
    .channel-card.push-card i {
        color: #0284c7;
        font-size: 2.2rem;
    }

    /* Select2 customizations */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0 !important;
        border-radius: 10px !important;
        min-height: 48px !important;
        padding: 4px 10px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #c1953e !important;
    }

    /* Live Preview Panel (Mockups) */
    .sticky-preview {
        position: sticky;
        top: 25px;
    }
    .preview-header-tabs {
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .preview-tab-btn {
        background: none;
        border: none;
        padding: 10px 15px;
        font-weight: 700;
        color: #64748b;
        border-bottom: 3px solid transparent;
        transition: all 0.25s ease;
        font-size: 0.9rem;
    }
    .preview-tab-btn.active {
        color: #c1953e;
        border-bottom-color: #c1953e;
    }
    
    /* Device Mockups CSS */
    .phone-mockup {
        background: #1e293b;
        border-radius: 36px;
        padding: 12px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        border: 4px solid #475569;
        max-width: 320px;
        margin: 0 auto;
        aspect-ratio: 9 / 18.5;
        position: relative;
        overflow: hidden;
    }
    .phone-screen {
        background-size: cover;
        background-position: center;
        border-radius: 26px;
        width: 100%;
        height: 100%;
        position: relative;
        padding: 15px 12px;
    }
    .lockscreen-bg {
        background-image: url('https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=500&q=80');
    }
    .whatsapp-bg {
        background-color: #efeae2;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
    }
    
    /* Lockscreen Notification */
    .lock-notification {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 12px 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-top: 50px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 0.85rem;
        direction: rtl;
        text-align: right;
    }
    .lock-notification .noti-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #475569;
        font-weight: bold;
        font-size: 0.75rem;
    }
    .lock-notification .noti-title {
        font-weight: 800;
        color: #1e293b;
    }
    .lock-notification .noti-body {
        color: #334155;
        line-height: 1.4;
        word-break: break-word;
    }

    /* WhatsApp Chat Bubble */
    .whatsapp-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: flex-end;
    }
    .wa-bubble {
        background: #ffffff;
        border-radius: 12px 0 12px 12px;
        padding: 8px 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        max-width: 85%;
        margin-right: auto;
        position: relative;
        font-size: 0.82rem;
        line-height: 1.4;
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 10px;
    }
    .wa-bubble::before {
        content: "";
        position: absolute;
        top: 0;
        right: -8px;
        width: 0;
        height: 0;
        border-top: 0px solid transparent;
        border-left: 8px solid #ffffff;
        border-bottom: 8px solid transparent;
    }
    .wa-media-preview {
        width: 100%;
        aspect-ratio: 16/10;
        border-radius: 8px;
        background: #e2e8f0;
        background-size: cover;
        background-position: center;
        display: none;
    }
    .wa-doc-preview {
        background: #f0f2f5;
        border-radius: 6px;
        padding: 8px 10px;
        display: none;
        align-items: center;
        gap: 8px;
        border: 1px solid #e2e8f0;
    }
    .wa-doc-preview i {
        color: #ef4444;
        font-size: 1.3rem;
    }
    .wa-doc-info {
        display: flex;
        flex-direction: column;
        font-size: 0.72rem;
        overflow: hidden;
    }
    .wa-doc-name {
        font-weight: 700;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        color: #334155;
    }
    .wa-text {
        color: #111827;
        word-break: break-word;
        white-space: pre-wrap;
    }
    .wa-time {
        align-self: flex-end;
        font-size: 0.65rem;
        color: #8892b0;
        margin-top: 2px;
    }
</style>
@endpush

@section('page-header')
<div class="main-content-container">
    <div class="page-header py-3 px-4 mt-3 mb-4 bg-white shadow-sm rounded-3 border d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md bg-gold-transparent text-gold rounded" style="background-color: rgba(193, 149, 62, 0.1); color: #c1953e;">
                <i class="bx bx-paper-plane fs-20"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-primary">مركز الإشعارات الذكي</h4>
                <small class="text-muted">إرسال تنبيهات مخصصة لجمهورك عبر WhatsApp وPush Notifications</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="row">
        <!-- قسم الفورم الرئيسي -->
        <div class="col-lg-8">
            <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                @csrf
                
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 12px;">
                        <i class="bx bx-check-circle fs-20"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                <!-- الخطوة 1: الجمهور المستهدف -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">١</span>
                            <span>الجمهور المستهدف (من تريد مراسلته؟)</span>
                        </div>
                        
                        <!-- شبكة كروت اختيار الجمهور -->
                        <div class="recipient-grid mb-4">
                            <div class="recipient-option-card active" data-value="all">
                                <div class="icon-wrapper"><i class="bx bx-globe"></i></div>
                                <h5 class="card-title">الكل</h5>
                                <p class="card-desc">إرسال التنبيه لكافة المستخدمين والخبراء بالمنصة</p>
                            </div>
                            <div class="recipient-option-card" data-value="users">
                                <div class="icon-wrapper"><i class="bx bx-group"></i></div>
                                <h5 class="card-title">العملاء</h5>
                                <p class="card-desc">إرسال التنبيه للمستخدمين وطالبي التثمين فقط</p>
                            </div>
                            <div class="recipient-option-card" data-value="experts">
                                <div class="icon-wrapper"><i class="bx bx-user-voice"></i></div>
                                <h5 class="card-title">الخبراء</h5>
                                <p class="card-desc">إرسال التنبيه لخبراء التثمين المسجلين فقط</p>
                            </div>
                            <div class="recipient-option-card" data-value="custom">
                                <div class="icon-wrapper"><i class="bx bx-target-lock"></i></div>
                                <h5 class="card-title">مستهدفين بالاسم</h5>
                                <p class="card-desc">تحديد عميل أو خبير معين من القائمة وإرسال له</p>
                            </div>
                        </div>

                        <!-- اختيار مستخدمين محددين (مخفي افتراضياً) -->
                        <div class="form-group mb-2" id="customRecipientDiv" style="display: none;">
                            <label class="form-label fw-bold text-primary mb-2">اختر المستلم أو ابحث بالاسم أو رقم الجوال:</label>
                            <select name="specific_users[]" class="form-control select2" multiple id="specificUsers">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 2: قنوات الإرسال -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">٢</span>
                            <span>قنوات الإرسال المفعلة</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-sm-6">
                                <input type="checkbox" name="channels[]" value="whatsapp" id="chanWhatsapp" class="channel-checkbox" checked>
                                <label for="chanWhatsapp" class="channel-card whatsapp-card">
                                    <i class="bx bxl-whatsapp"></i>
                                    <h5 class="fw-bold mb-0 text-dark">رسائل الواتساب</h5>
                                    <small class="text-muted">إرسال عبر UltraMsg</small>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="checkbox" name="channels[]" value="push" id="chanPush" class="channel-checkbox">
                                <label for="chanPush" class="channel-card push-card">
                                    <i class="bx bx-bell"></i>
                                    <h5 class="fw-bold mb-0 text-dark">تنبيهات الهاتف (Push)</h5>
                                    <small class="text-muted">إرسال إشعار فوري للتطبيق</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 3: المحتوى والملحقات -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">٣</span>
                            <span>محتوى الرسالة والوسائط</span>
                        </div>
                        
                        <div class="mb-4" id="pushTitleDiv" style="display: none;">
                            <label class="form-label fw-bold text-dark">عنوان الإشعار (للتنبيهات فقط)</label>
                            <input type="text" name="title" id="inputTitle" class="form-control form-control-lg border-2" placeholder="مثال: عرض خاص جديد! 🎁">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">نص الرسالة</label>
                            <textarea name="message" id="inputMessage" class="form-control border-2" rows="5" placeholder="اكتب نص رسالتك بالتفصيل هنا..." required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark small">إرفاق صورة (تظهر في الواتساب)</label>
                                <input type="file" name="image" id="inputImage" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark small">إرفاق مستند/ملف (يظهر في الواتساب)</label>
                                <input type="file" name="file" id="inputFile" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center py-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg fw-bold" style="border-radius: 12px; background: #c1953e; border-color: #c1953e; padding: 12px 40px;">
                        <i class="bx bx-send me-1"></i> إرسال الحملة الآن
                    </button>
                </div>

                <input type="hidden" name="recipients" id="finalRecipients" value="all">
            </form>
        </div>

        <!-- المعاينة الحية الجانبية (Sticky Live Preview) -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="sticky-preview card custom-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bx bx-show-alt"></i> المعاينة الحية التفاعلية</h5>
                    
                    <div class="preview-header-tabs">
                        <button type="button" class="preview-tab-btn active" id="tabPushBtn">تنبيه الهاتف (Push)</button>
                        <button type="button" class="preview-tab-btn" id="tabWaBtn">الواتساب (WhatsApp)</button>
                    </div>

                    <!-- معاينة إشعار الهاتف -->
                    <div class="preview-container" id="previewPush">
                        <div class="phone-mockup">
                            <div class="phone-screen lockscreen-bg">
                                <div class="lock-notification">
                                    <div class="noti-header">
                                        <div class="d-flex align-items-center gap-1">
                                            <img src="{{ URL::asset('assets/img/brand/favicon.png') }}" width="14" height="14" style="border-radius: 3px;">
                                            <span style="font-size: 0.7rem;">Thamn · ثمن</span>
                                        </div>
                                        <span style="font-size: 0.7rem; opacity: 0.7;">الآن</span>
                                    </div>
                                    <div class="noti-title" id="previewPushTitle">عنوان التنبيه يظهر هنا</div>
                                    <div class="noti-body" id="previewPushBody">محتوى الرسالة يظهر هنا للتنبيه الفوري على الهاتف.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- معاينة إشعار واتساب -->
                    <div class="preview-container" id="previewWa" style="display: none;">
                        <div class="phone-mockup">
                            <div class="phone-screen whatsapp-bg">
                                <div class="whatsapp-container">
                                    <div class="wa-bubble">
                                        <div class="wa-media-preview" id="waImagePreview"></div>
                                        <div class="wa-doc-preview" id="waDocPreview">
                                            <i class="far fa-file-pdf"></i>
                                            <div class="wa-doc-info">
                                                <span class="wa-doc-name" id="waDocName">file.pdf</span>
                                                <span style="font-size: 0.6rem; color: #8892b0;">مستند مرفق</span>
                                            </div>
                                        </div>
                                        <div class="wa-text" id="previewWaBody">رسالة واتساب تظهر هنا...</div>
                                        <div class="wa-time">
                                            <span>{{ date('h:i A') }}</span>
                                            <i class="fas fa-check-double text-primary" style="font-size: 0.65rem; margin-right: 2px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
        // إدارة كروت اختيار الجمهور
        $('.recipient-option-card').on('click', function() {
            $('.recipient-option-card').removeClass('active');
            $(this).addClass('active');
            let val = $(this).data('value');
            
            if(val === 'custom') {
                $('#customRecipientDiv').slideDown(200);
                $('#finalRecipients').val('');
                $('#specificUsers').select2({
                    placeholder: "🔍 ابحث بالاسم أو الرقم...",
                    allowClear: true,
                    dir: "rtl",
                    width: '100%'
                });
            } else {
                $('#customRecipientDiv').slideUp(200);
                $('#finalRecipients').val(val);
            }
        });

        // إدارة إظهار حقل العنوان عند اختيار Push
        $('#chanPush').on('change', function() {
            if($(this).is(':checked')) {
                $('#pushTitleDiv').slideDown(200);
            } else {
                $('#pushTitleDiv').slideUp(200);
            }
        });

        // التبديل بين التبويبات في المعاينة
        $('#tabPushBtn').on('click', function() {
            $('.preview-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('#previewWa').hide();
            $('#previewPush').fadeIn(200);
        });

        $('#tabWaBtn').on('click', function() {
            $('.preview-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('#previewPush').hide();
            $('#previewWa').fadeIn(200);
        });

        // التحديث التفاعلي للمعاينة عند الكتابة
        $('#inputTitle').on('keyup change', function() {
            let val = $(this).val().trim();
            $('#previewPushTitle').text(val ? val : 'عنوان التنبيه يظهر هنا');
        });

        $('#inputMessage').on('keyup change', function() {
            let val = $(this).val().trim();
            $('#previewPushBody').text(val ? val : 'محتوى الرسالة يظهر هنا للتنبيه الفوري على الهاتف.');
            $('#previewWaBody').text(val ? val : 'رسالة واتساب تظهر هنا...');
        });

        // معاينة الصورة المرفقة
        $('#inputImage').on('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#waImagePreview').css('background-image', 'url(' + e.target.result + ')').show();
                    // التبديل تلقائياً لمعاينة واتساب لرؤية الصورة
                    $('#tabWaBtn').click();
                };
                reader.readAsDataURL(file);
            } else {
                $('#waImagePreview').hide();
            }
        });

        // معاينة الملف المرفق
        $('#inputFile').on('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                $('#waDocName').text(file.name);
                $('#waDocPreview').css('display', 'flex');
                // التبديل تلقائياً لمعاينة واتساب لرؤية الملف
                $('#tabWaBtn').click();
            } else {
                $('#waDocPreview').hide();
            }
        });

        // التأكيد قبل الإرسال وتمرير القيم المحددة
        $('#notificationForm').on('submit', function() {
            let type = $('.recipient-option-card.active').data('value');
            
            if(type === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids.length === 0) {
                    alert('⚠️ يرجى اختيار مستخدم واحد على الأقل من القائمة');
                    return false;
                }
                $('#finalRecipients').val(ids.join(','));
            } else {
                $('#finalRecipients').val(type);
            }

            let checkedChannels = $('.channel-checkbox:checked').length;
            if(checkedChannels === 0) {
                alert('⚠️ يرجى تفعيل قناة واحدة على الأقل للإرسال (WhatsApp أو Push)');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
