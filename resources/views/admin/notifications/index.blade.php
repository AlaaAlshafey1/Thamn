@extends('layouts.master')
@section('title', 'مركز الإشعارات الذكي')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Clean Dashboard Custom Styles */
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
    .step-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
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
    
    /* Step 1: Channel selection cards */
    .channel-checkbox {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .channel-card {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 22px 15px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        text-align: center;
        position: relative;
    }
    .channel-card:hover {
        border-color: #c1953e;
        background: #ffffff;
        transform: translateY(-2px);
    }
    .channel-checkbox:checked + .channel-card {
        border-color: #c1953e;
        background: rgba(193, 149, 62, 0.05);
        box-shadow: 0 5px 15px rgba(193, 149, 62, 0.1);
    }
    .channel-card.whatsapp-card i {
        color: #25d366;
        font-size: 2.5rem;
    }
    .channel-card.push-card i {
        color: #0284c7;
        font-size: 2.5rem;
    }

    /* Step 2: Recipients choice grid */
    .recipient-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }
    @media (max-width: 991px) {
        .recipient-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 575px) {
        .recipient-grid {
            grid-template-columns: 1fr;
        }
    }
    .recipient-option-card {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 18px 10px;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 6px;
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
        font-size: 1.6rem;
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
        font-size: 0.95rem;
        margin: 0;
    }
    .recipient-option-card .card-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }

    /* Select2 customization */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0 !important;
        border-radius: 10px !important;
        min-height: 48px !important;
        padding: 4px 10px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #c1953e !important;
    }

    .form-control:focus {
        border-color: #c1953e !important;
        box-shadow: 0 0 0 0.25rem rgba(193, 149, 62, 0.15) !important;
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
                <small class="text-muted">أرسل حملتك الإعلانية أو التنبيهية بخطوات مرنة وسريعة</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="main-content-container">
    <div class="row">
        <div class="col-xl-9 lg-10 mx-auto">
            <form action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data" id="notificationForm">
                @csrf
                
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-4 p-3 d-flex align-items-center gap-2" style="border-radius: 12px;">
                        <i class="bx bx-check-circle fs-20"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                <!-- الخطوة 1: قنوات الإرسال (نوع الرسالة) -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">١</span>
                            <span>قنوات الإرسال (نوع الرسالة)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-sm-6">
                                <input type="checkbox" name="channels[]" value="whatsapp" id="chanWhatsapp" class="channel-checkbox" checked>
                                <label for="chanWhatsapp" class="channel-card whatsapp-card">
                                    <i class="bx bxl-whatsapp"></i>
                                    <h5 class="fw-bold mb-0 text-dark">رسالة واتساب (WhatsApp)</h5>
                                    <small class="text-muted">مراسلة مباشرة لجميع أرقام الهواتف عبر الواتساب</small>
                                </label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <input type="checkbox" name="channels[]" value="push" id="chanPush" class="channel-checkbox">
                                <label for="chanPush" class="channel-card push-card">
                                    <i class="bx bx-bell"></i>
                                    <h5 class="fw-bold mb-0 text-dark">إشعار الهاتف الذكي (Push)</h5>
                                    <small class="text-muted">تنبيه فوري يظهر على شاشات هواتف المستخدمين</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 2: الجمهور المستهدف -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">٢</span>
                            <span>الجمهور المستهدف (من يستلم الرسالة؟)</span>
                        </div>
                        
                        <!-- كروت اختيار الفئات -->
                        <div class="recipient-grid mb-4">
                            <div class="recipient-option-card active" data-value="all">
                                <div class="icon-wrapper"><i class="bx bx-globe"></i></div>
                                <h5 class="card-title">الكل</h5>
                                <p class="card-desc">جميع المسجلين</p>
                            </div>
                            <div class="recipient-option-card" data-value="users">
                                <div class="icon-wrapper"><i class="bx bx-group"></i></div>
                                <h5 class="card-title">العملاء</h5>
                                <p class="card-desc">المستخدمين فقط</p>
                            </div>
                            <div class="recipient-option-card" data-value="experts">
                                <div class="icon-wrapper"><i class="bx bx-user-voice"></i></div>
                                <h5 class="card-title">الخبراء</h5>
                                <p class="card-desc">خبراء التثمين فقط</p>
                            </div>
                            <div class="recipient-option-card" data-value="custom">
                                <div class="icon-wrapper"><i class="bx bx-target-lock"></i></div>
                                <h5 class="card-title">مستهدف</h5>
                                <p class="card-desc">تحديد بالاسم والرقم</p>
                            </div>
                        </div>

                        <!-- قائمة تحديد المستخدمين (مخفي افتراضياً) -->
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

                <!-- الخطوة 3: المحتوى والمرفقات -->
                <div class="card custom-card mb-4">
                    <div class="card-body p-4">
                        <div class="step-title">
                            <span class="step-number">٣</span>
                            <span>محتوى الرسالة والوسائط</span>
                        </div>
                        
                        <!-- يظهر فقط عند تفعيل Push -->
                        <div class="mb-4" id="pushTitleDiv" style="display: none;">
                            <label class="form-label fw-bold text-dark">عنوان الإشعار (Title)</label>
                            <input type="text" name="title" class="form-control form-control-lg border-2" placeholder="مثال: تحديث هام لمنصة ثمن 🔔">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">نص الرسالة (Message Body)</label>
                            <textarea name="message" class="form-control border-2" rows="6" placeholder="اكتب نص رسالتك هنا بالتفصيل..." required></textarea>
                        </div>

                        <!-- تظهر فقط عند تفعيل الواتساب -->
                        <div class="row" id="whatsappMediaDiv">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark small">إرفاق صورة للمحادثة (إختياري)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-dark small">إرفاق ملف/مستند للمحادثة (إختياري)</label>
                                <input type="file" name="file" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center py-3">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg fw-bold" style="border-radius: 12px; background: #c1953e; border-color: #c1953e; padding: 12px 50px;">
                        <i class="bx bx-send me-1"></i> إرسال الرسائل الآن
                    </button>
                </div>

                <input type="hidden" name="recipients" id="finalRecipients" value="all">
            </form>
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

        // إدارة إظهار وإخفاء الحقول بشكل ديناميكي بناءً على قناة الإرسال
        function toggleDynamicFields() {
            let isPushChecked = $('#chanPush').is(':checked');
            let isWhatsappChecked = $('#chanWhatsapp').is(':checked');
            
            // إدارة حقل العنوان للـ Push
            if (isPushChecked) {
                $('#pushTitleDiv').slideDown(200);
            } else {
                $('#pushTitleDiv').slideUp(200);
            }
            
            // إدارة حقول الميديا للـ WhatsApp
            if (isWhatsappChecked) {
                $('#whatsappMediaDiv').slideDown(200);
            } else {
                $('#whatsappMediaDiv').slideUp(200);
            }
        }

        // تشغيل الفانكشن عند تحميل الصفحة وعند تغيير التشيك بوكس
        toggleDynamicFields();
        $('.channel-checkbox').on('change', function() {
            toggleDynamicFields();
        });

        // التأكيد والتحقق قبل إرسال الفورم
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
                alert('⚠️ يرجى تفعيل قناة إرسال واحدة على الأقل (WhatsApp أو Push)');
                return false;
            }

            return true;
        });
    });
</script>
@endpush
