@extends('layouts.master')
@section('title', 'مركز الإشعارات الذكي')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-gold: #c1953e;
        --primary-gold-dark: #a0782d;
        --primary-gold-light: rgba(193, 149, 62, 0.1);
        --dark-bg: #1e1e2d;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body, h1, h2, h3, h4, h5, h6, .btn, select, input, textarea, .select2-container {
        font-family: 'Tajawal', 'Outfit', sans-serif !important;
    }

    .main-content-container {
        direction: rtl;
        text-align: right;
        background: #f8fafc;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    /* Page Header styling */
    .premium-header {
        background: linear-gradient(135deg, #1e1e2f 0%, #11111d 100%);
        border-radius: 16px;
        padding: 30px 40px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(193, 149, 62, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .premium-header h4 {
        color: #ffffff;
        font-weight: 900;
        letter-spacing: 0.5px;
    }
    .premium-header p {
        color: #a0aec0;
    }

    /* Card styling */
    .glass-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    .card-header-premium {
        background: linear-gradient(to left, #f8fafc, #ffffff);
        border-bottom: 1px solid #edf2f7;
        padding: 20px 25px;
    }

    /* Form control styling */
    .form-label {
        font-weight: 700;
        color: #2d3748;
        font-size: 0.95rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }
    .form-label i {
        color: var(--primary-gold);
        font-size: 1.25rem;
    }
    .form-group-custom:focus-within .form-label {
        color: var(--primary-gold-dark);
    }
    .custom-input {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.95rem;
        transition: var(--transition);
        background: #f8fafc;
    }
    .custom-input:focus {
        border-color: var(--primary-gold) !important;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(193, 149, 62, 0.1) !important;
        outline: none;
    }

    /* Toggle Switches */
    .channel-pill {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 15px 20px;
        background: #f8fafc;
        cursor: pointer;
        transition: var(--transition);
        flex: 1;
        min-width: 200px;
        position: relative;
    }
    .channel-pill:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .channel-switch:checked + .channel-pill {
        border-color: var(--primary-gold);
        background: rgba(193, 149, 62, 0.03);
        box-shadow: 0 4px 12px rgba(193, 149, 62, 0.08);
    }
    .channel-switch:checked + .channel-pill i {
        color: var(--primary-gold);
        transform: scale(1.1);
    }

    /* Live Phone Preview Mockup */
    .phone-mockup {
        width: 100%;
        max-width: 320px;
        height: 600px;
        background: #09090e;
        border-radius: 40px;
        border: 12px solid #2d3748;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        position: relative;
        margin: 0 auto;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .phone-notch {
        width: 130px;
        height: 25px;
        background: #2d3748;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
        z-index: 10;
    }
    .phone-screen {
        flex: 1;
        padding: 40px 20px 20px 20px;
        background: url('https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1000') no-repeat center center;
        background-size: cover;
        position: relative;
    }
    .notification-banner {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 18px;
        padding: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        direction: rtl;
        text-align: right;
        transform: translateY(-50px);
        opacity: 0;
        animation: slideDownIn 0.8s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards;
        animation-delay: 0.5s;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .banner-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }
    .banner-app-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: #1e1e2f;
        font-size: 0.75rem;
    }
    .banner-app-logo {
        width: 18px;
        height: 18px;
        background: #c1953e;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.6rem;
        font-weight: bold;
    }
    .banner-time {
        font-size: 0.65rem;
        color: #718096;
    }
    .banner-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .banner-body {
        font-size: 0.8rem;
        color: #4a5568;
        line-height: 1.3;
        word-break: break-word;
    }

    @keyframes slideDownIn {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Custom select2 overrides */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 6px 12px !important;
        background: #f8fafc !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-gold) !important;
        background: #ffffff !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-gold-light) !important;
        border: 1px solid rgba(193, 149, 62, 0.2) !important;
        color: var(--primary-gold-dark) !important;
        border-radius: 6px !important;
        padding: 2px 10px !important;
    }

    /* Submit Button styling */
    .btn-send {
        background: linear-gradient(135deg, var(--primary-gold) 0%, var(--primary-gold-dark) 100%);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 14px 28px;
        font-size: 1.05rem;
        font-weight: bold;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(193, 149, 62, 0.2);
    }
    .btn-send:hover {
        background: linear-gradient(135deg, var(--primary-gold-dark) 0%, #88621e 100%);
        box-shadow: 0 6px 20px rgba(193, 149, 62, 0.35);
        transform: translateY(-1px);
        color: white;
    }
    .btn-send:active {
        transform: translateY(1px);
    }
</style>
@endpush

@section('page-header')
<div class="main-content-container">
    <div class="premium-header shadow-sm mb-4">
        <h4 class="mb-2 fw-bold"><i class="bx bx-broadcast" style="color: var(--primary-gold); font-size: 1.6rem; vertical-align: middle;"></i> مركز البث والإشعارات المباشرة</h4>
        <p class="mb-0 small text-muted">قم بصياغة وإرسال التنبيهات الفورية (Push) ورسائل الواتساب الفعالة لجمهورك بلمح البصر وبشكل رائع</p>
    </div>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="row g-4">
        
        <!-- العمود الأيسر: خيارات وصياغة الإشعار -->
        <div class="col-xl-8 col-lg-7">
            
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 12px; background-color: #f0fdf4; border-right: 4px solid #16a34a;">
                    <i class="bx bx-check-circle fs-24 text-success"></i>
                    <span class="text-success fw-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="card glass-card">
                <div class="card-header-premium">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-slider-alt text-gold align-middle ml-1"></i> إعدادات البث والمحتوى</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                        @csrf
                        
                        <!-- 1. قنوات الإرسال -->
                        <div class="mb-4">
                            <label class="form-label"><i class="bx bx-network-chart"></i> اختر قنوات الإرسال المفعلة:</label>
                            <div class="d-flex flex-wrap gap-3">
                                
                                <label style="display: contents;">
                                    <input class="channel-switch d-none" type="checkbox" name="channels[]" value="push" id="switchPush" checked>
                                    <div class="channel-pill d-flex align-items-center gap-3">
                                        <i class="bx bx-mobile-vibration fs-24 text-muted"></i>
                                        <div class="text-right">
                                            <span class="d-block fw-bold text-dark">إشعارات الهاتف (Push)</span>
                                            <small class="text-muted small">إشعار ينبثق على شاشة قفل جهاز العميل</small>
                                        </div>
                                    </div>
                                </label>

                                <label style="display: contents;">
                                    <input class="channel-switch d-none" type="checkbox" name="channels[]" value="whatsapp" id="switchWhatsapp">
                                    <div class="channel-pill d-flex align-items-center gap-3">
                                        <i class="bx bxl-whatsapp fs-24 text-muted"></i>
                                        <div class="text-right">
                                            <span class="d-block fw-bold text-dark">رسائل الواتساب (WhatsApp)</span>
                                            <small class="text-muted small">رسالة نصية مع خيار المرفقات عبر الواتساب</small>
                                        </div>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <!-- 2. نوع المستلم والجمهور -->
                        <div class="mb-4 form-group-custom">
                            <label for="recipientTypeSelect" class="form-label"><i class="bx bx-user-pin"></i> الجمهور المستهدف (المستلمون):</label>
                            <select name="recipients_type" class="form-select custom-input" id="recipientTypeSelect">
                                <option value="users" selected>👥 العملاء فقط (جميع مستخدمي التطبيق)</option>
                                <option value="all">🌍 الكل (جميع العملاء والخبراء المسجلين)</option>
                                <option value="experts">👨‍🏫 الخبراء فقط</option>
                                <option value="custom">🎯 تحديد مستخدمين محددين بالاسم</option>
                            </select>
                        </div>

                        <!-- قائمة تحديد المستخدمين (مخفية افتراضياً) -->
                        <div class="mb-4" id="customRecipientDiv" style="display: none;">
                            <label class="form-label text-primary"><i class="bx bx-search-alt"></i> ابحث واختر المستلمين بالاسم أو الجوال:</label>
                            <select name="specific_users[]" class="form-control select2" multiple id="specificUsers">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4" style="border-top: 1px dashed #e2e8f0;">

                        <!-- 3. المحتوى والملحقات -->
                        
                        <!-- عنوان الإشعار (Header) -->
                        <div class="mb-4 form-group-custom" id="pushTitleDiv">
                            <label for="inputTitle" class="form-label"><i class="bx bx-dock-top"></i> عنوان التنبيه (Header):</label>
                            <input type="text" name="title" id="inputTitle" class="form-control custom-input" placeholder="اكتب عنواناً جذاباً وقصيراً..." value="يا هلا والله بالغالي! 📣">
                        </div>

                        <!-- نص الرسالة (Body) -->
                        <div class="mb-4 form-group-custom">
                            <label for="inputMessage" class="form-label"><i class="bx bx-message-square-detail"></i> محتوى الرسالة (Body):</label>
                            <textarea name="message" id="inputMessage" class="form-control custom-input" rows="5" placeholder="اكتب محتوى الإشعار بالتفصيل هنا..." required></textarea>
                        </div>

                        <!-- ملحقات الواتساب (تظهر فقط عند تفعيل الواتساب) -->
                        <div class="row" id="whatsappMediaDiv" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small"><i class="bx bx-image-add"></i> إرفاق صورة (للواتساب):</label>
                                <input type="file" name="image" class="form-control custom-input" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small"><i class="bx bx-file-blank"></i> إرفاق ملف/مستند (للواتساب):</label>
                                <input type="file" name="file" class="form-control custom-input">
                            </div>
                        </div>

                        <!-- زر الإرسال -->
                        <div class="text-center pt-2">
                            <button type="submit" class="btn btn-send btn-lg w-100 shadow-sm">
                                <i class="bx bx-paper-plane ml-1 align-middle fs-18"></i> إطلاق البث والإرسال الآن
                            </button>
                        </div>

                        <input type="hidden" name="recipients" id="finalRecipients" value="users">
                    </form>
                </div>
            </div>

        </div>

        <!-- العمود الأيمن: المعاينة الحية على الهاتف -->
        <div class="col-xl-4 col-lg-5">
            <div class="card glass-card sticky-top" style="top: 20px;">
                <div class="card-header-premium text-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-show-alt text-gold align-middle ml-1"></i> المعاينة الحية الفورية</h5>
                </div>
                <div class="card-body py-4">
                    <p class="text-muted text-center small mb-4">شاهد كيف سيظهر الإشعار على هواتف المستخدمين أثناء كتابتك</p>
                    
                    <div class="phone-mockup">
                        <div class="phone-notch"></div>
                        <div class="phone-screen">
                            
                            <!-- إشعار الهاتف المنبثق -->
                            <div class="notification-banner" id="liveBanner">
                                <div class="banner-header">
                                    <div class="banner-app-info">
                                        <div class="banner-app-logo">ث</div>
                                        <span>ثمن • Thamn</span>
                                    </div>
                                    <span class="banner-time">الآن</span>
                                </div>
                                <div class="banner-title" id="previewTitle">يا هلا والله بالغالي! 📣</div>
                                <div class="banner-body" id="previewBody">اكتب محتوى الإشعار في النموذج لتشاهد المعاينة الحية هنا فوراً...</div>
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
        
        // ربط المعاينة الحية بحقول الكتابة
        function updateLivePreview() {
            let titleVal = $('#inputTitle').val().trim();
            let bodyVal = $('#inputMessage').val().trim();
            
            // تحديث العنوان
            if(titleVal) {
                $('#previewTitle').text(titleVal);
            } else {
                $('#previewTitle').text('إشعار من تطبيق ثمن');
            }
            
            // تحديث المتن
            if(bodyVal) {
                $('#previewBody').text(bodyVal);
            } else {
                $('#previewBody').text('محتوى التنبيه يظهر هنا...');
            }

            // إعادة تشغيل الأنيميشن لتأثير جذاب
            let banner = $('#liveBanner');
            banner.css('animation', 'none');
            banner[0].offsetHeight; // trigger reflow
            banner.css('animation', 'slideDownIn 0.8s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards');
        }

        $('#inputTitle').on('input keyup change', updateLivePreview);
        $('#inputMessage').on('input keyup change', updateLivePreview);

        // إدارة الجمهور وقوائم تحديد المستخدمين
        $('#recipientTypeSelect').on('change', function() {
            let val = $(this).val();
            if(val === 'custom') {
                $('#customRecipientDiv').slideDown(250);
                $('#finalRecipients').val('');
                $('#specificUsers').select2({
                    placeholder: "🔍 اكتب اسم المستخدم أو الجوال للاختيار...",
                    allowClear: true,
                    dir: "rtl",
                    width: '100%'
                });
            } else {
                $('#customRecipientDiv').slideUp(250);
                $('#finalRecipients').val(val);
            }
        });

        // إدارة إظهار وإخفاء حقول الإدخال حسب القنوات
        function adjustFieldsVisibility() {
            let isPushActive = $('#switchPush').is(':checked');
            let isWhatsappActive = $('#switchWhatsapp').is(':checked');
            
            // حقل عنوان إشعار الهاتف
            if (isPushActive) {
                $('#pushTitleDiv').slideDown(250);
                $('#liveBanner').fadeIn(250);
            } else {
                $('#pushTitleDiv').slideUp(250);
                $('#liveBanner').fadeOut(250);
            }
            
            // حقول صور وملفات الواتساب
            if (isWhatsappActive) {
                $('#whatsappMediaDiv').slideDown(250);
            } else {
                $('#whatsappMediaDiv').slideUp(250);
            }
        }

        // تهيئة التحكم الأولي وتعيين التعديلات
        adjustFieldsVisibility();
        updateLivePreview();

        $('.channel-switch').on('change', function() {
            adjustFieldsVisibility();
        });

        // التحقق قبل تقديم الاستمارة
        $('#notificationForm').on('submit', function() {
            let type = $('#recipientTypeSelect').val();
            
            if(type === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids.length === 0) {
                    alert('⚠️ يرجى تحديد مستخدم واحد على الأقل من قائمة البحث.');
                    return false;
                }
                $('#finalRecipients').val(ids.join(','));
            } else {
                $('#finalRecipients').val(type);
            }

            let checkedChannels = $('.channel-switch:checked').length;
            if(checkedChannels === 0) {
                alert('⚠️ يرجى تفعيل قناة إرسال واحدة على الأقل (WhatsApp أو إشعارات الهاتف).');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
