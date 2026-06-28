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
    
    /* WhatsApp and Push channel switchers styling */
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
        user-select: none;
    }
    .channel-checkbox:checked + .channel-badge {
        border-color: #c1953e;
        background: rgba(193, 149, 62, 0.08);
        color: #c1953e;
    }

    /* WhatsApp Styled Input Wrapper */
    .wa-input-wrapper {
        position: relative;
        display: flex;
        align-items: stretch;
    }
    .wa-input-wrapper .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-right: 1px solid #ced4da;
    }
    .wa-emoji-trigger {
        border: 1px solid #ced4da;
        border-left: 1px solid #ced4da;
        background: #f8fafc;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        padding: 0 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .wa-emoji-trigger:hover {
        background: #e2e8f0;
    }

    /* WhatsApp Style Emoji Picker Drawer Popover */
    .wa-emoji-picker {
        position: absolute;
        z-index: 1000;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        width: 320px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        margin-top: 5px;
    }
    .wa-emoji-tabs {
        display: flex;
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        padding: 5px;
    }
    .wa-emoji-tab-btn {
        flex: 1;
        background: none;
        border: none;
        padding: 6px 0;
        font-size: 1rem;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-align: center;
    }
    .wa-emoji-tab-btn:hover, .wa-emoji-tab-btn.active {
        background: #ffffff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .wa-emoji-grid-container {
        padding: 10px;
        max-height: 200px;
        overflow-y: auto;
    }
    .wa-emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 6px;
    }
    .wa-emoji-item {
        font-size: 1.4rem;
        text-align: center;
        padding: 4px;
        cursor: pointer;
        border-radius: 6px;
        user-select: none;
        transition: transform 0.1s ease;
    }
    .wa-emoji-item:hover {
        background: #f1f5f9;
        transform: scale(1.2);
    }

    /* Double Live Previews: WhatsApp & iPhone */
    .preview-section {
        margin-top: 30px;
    }
    
    /* WhatsApp Chat Preview Window */
    .wa-chat-window {
        background: #efeae2;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        direction: rtl;
    }
    .wa-chat-header {
        background: #008069;
        color: white;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .wa-chat-avatar {
        width: 38px;
        height: 38px;
        background: #ffffff;
        color: #008069;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .wa-chat-info {
        display: flex;
        flex-direction: column;
    }
    .wa-chat-name {
        font-weight: bold;
        font-size: 0.9rem;
        line-height: 1.2;
    }
    .wa-chat-status {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.85);
    }
    .wa-chat-body {
        padding: 20px 15px;
        min-height: 160px;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-size: cover;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .wa-message-bubble {
        background: #d9fdd3;
        border-radius: 10px;
        border-top-right-radius: 0;
        padding: 8px 12px;
        max-width: 85%;
        align-self: flex-start;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        position: relative;
        word-break: break-word;
    }
    .wa-message-text {
        font-size: 0.85rem;
        color: #111b21;
        line-height: 1.4;
        margin-bottom: 4px;
        white-space: pre-line;
    }
    .wa-message-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        font-size: 0.65rem;
        color: #667781;
    }
    .wa-double-check {
        color: #53bdeb;
        font-weight: bold;
    }

    /* iPhone Style Push Preview */
    .iphone-push-banner {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 14px;
        padding: 12px 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.4);
    }
    .iphone-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        font-size: 0.75rem;
        color: #718096;
        font-weight: bold;
    }
    .iphone-app-name {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #1a202c;
    }
    .iphone-app-logo {
        width: 16px;
        height: 16px;
        background: #c1953e;
        border-radius: 4px;
        color: white;
        font-size: 0.6rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .iphone-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #1a202c;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .iphone-body {
        font-size: 0.8rem;
        color: #4a5568;
        line-height: 1.35;
        word-break: break-word;
    }

    /* Multi-selected users visual list */
    .selected-users-list {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        max-height: 150px;
        overflow-y: auto;
    }
    .selected-user-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(193, 149, 62, 0.08);
        border: 1px solid rgba(193, 149, 62, 0.2);
        color: #c1953e;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: bold;
        margin: 3px;
    }

    /* Select2 overrides for multi-select styling */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        min-height: 46px !important;
        padding: 4px 8px !important;
    }
</style>
@endpush

@section('page-header')
<div class="notify-container container-fluid">
    <div class="page-header py-3 px-4 mt-3 mb-4 bg-white shadow-sm rounded-3 border">
        <h4 class="mb-1 fw-bold text-primary"><i class="bx bx-paper-plane"></i> مركز إرسال الإشعارات والرسائل</h4>
        <p class="text-muted mb-0 small">أداة لإرسال الإشعارات الفورية والتنبيهات المخصصة لكافة العملاء أو لمستلمين محددين</p>
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
                            <label class="form-group-title d-block"><i class="bx bx-broadcast text-muted ml-1"></i> قنوات الإرسال المفعلة:</label>
                            <div class="d-flex gap-3">
                                <label class="m-0">
                                    <input class="channel-checkbox d-none" type="checkbox" name="channels[]" value="push" id="switchPush" checked>
                                    <div class="channel-badge">
                                        <i class="bx bx-mobile-vibration fs-20"></i>
                                        <span>إشعارات الهاتف (Push)</span>
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

                        <!-- 2. الجمهور المستهدف -->
                        <div class="mb-4">
                            <label class="form-group-title d-block" for="recipientTypeSelect"><i class="bx bx-group text-muted ml-1"></i> الجمهور المستهدف (المستلمون):</label>
                            <select name="recipients_type" class="form-select form-select-lg" id="recipientTypeSelect" style="border-radius: 8px; font-size: 0.95rem; height: 46px;">
                                <option value="users" selected>👥 إرسال جماعي (لكل العملاء)</option>
                                <option value="custom">🎯 إرسال لمستخدمين محددين بالاسم</option>
                            </select>
                        </div>

                        <!-- قائمة تحديد المستخدمين المتعددة (فقط من لديهم توكن) -->
                        <div class="mb-4" id="customRecipientDiv" style="display: none;">
                            <label class="form-group-title d-block" for="specificUsers"><i class="bx bx-search text-muted ml-1"></i> اختر العملاء المستهدفين (يظهر فقط من لديهم هواتف مسجلة):</label>
                            <select name="specific_users[]" class="form-control select2" id="specificUsers" style="width: 100%;" multiple="multiple">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-phone="{{ $user->phone }}" data-name="{{ $user->first_name }} {{ $user->last_name }}">
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->phone }})
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- قائمة المعاينة المرئية للمستلمين المحددين -->
                            <div class="mt-3" id="selectedUsersContainer" style="display: none;">
                                <label class="small text-muted font-weight-bold d-block mb-1">المستلمون المختارون للإرسال حالياً:</label>
                                <div class="selected-users-list" id="selectedUsersList">
                                    <!-- تضاف ديناميكياً هنا -->
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" style="border-top: 1px solid #edf2f7;">

                        <!-- 3. عنوان الإشعار (Header) -->
                        <div class="mb-4" id="pushTitleDiv" style="position: relative;">
                            <label for="inputTitle" class="form-group-title d-block"><i class="bx bx-heading text-muted ml-1"></i> عنوان التنبيه (Header):</label>
                            <div class="wa-input-wrapper">
                                <input type="text" name="title" id="inputTitle" class="form-control form-control-lg" placeholder="اكتب عنوان الإشعار هنا..." value="يا هلا والله بالغالين! 📣" style="border-radius: 8px; font-size: 0.95rem;">
                                <button type="button" class="wa-emoji-trigger" data-picker="pickerTitle">😊</button>
                            </div>
                            
                            <!-- درج الإيموجي للعنوان -->
                            <div class="wa-emoji-picker" id="pickerTitle">
                                <div class="wa-emoji-tabs">
                                    <button type="button" class="wa-emoji-tab-btn active" data-cat="smileys">😊</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="hearts">❤️</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="gestures">👍</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="symbols">✨</button>
                                </div>
                                <div class="wa-emoji-grid-container">
                                    <div class="wa-emoji-grid">
                                        <!-- تعبأ ديناميكياً -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. محتوى الرسالة (Body) -->
                        <div class="mb-4" style="position: relative;">
                            <label for="inputMessage" class="form-group-title d-block"><i class="bx bx-envelope text-muted ml-1"></i> محتوى الرسالة (Body):</label>
                            <div class="wa-input-wrapper">
                                <textarea name="message" id="inputMessage" class="form-control" rows="5" placeholder="اكتب نص الإشعار والرسالة هنا..." required style="border-radius: 8px; font-size: 0.95rem;"></textarea>
                                <button type="button" class="wa-emoji-trigger" data-picker="pickerBody">😊</button>
                            </div>
                            
                            <!-- درج الإيموجي للمحتوى -->
                            <div class="wa-emoji-picker" id="pickerBody">
                                <div class="wa-emoji-tabs">
                                    <button type="button" class="wa-emoji-tab-btn active" data-cat="smileys">😊</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="hearts">❤️</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="gestures">👍</button>
                                    <button type="button" class="wa-emoji-tab-btn" data-cat="symbols">✨</button>
                                </div>
                                <div class="wa-emoji-grid-container">
                                    <div class="wa-emoji-grid">
                                        <!-- تعبأ ديناميكياً -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ملحقات الواتساب (تظهر فقط عند تحديد الواتساب) -->
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

                        <!-- 5. المعاينات المباشرة التفاعلية -->
                        <div class="preview-section">
                            
                            <!-- معاينة إشعار الهاتف -->
                            <div class="mb-4" id="pushPreviewContainer">
                                <label class="form-group-title small d-block"><i class="bx bx-show-alt text-muted ml-1"></i> معاينة الإشعار على الهاتف (Push Notification):</label>
                                <div class="iphone-push-banner">
                                    <div class="iphone-header">
                                        <div class="iphone-app-name">
                                            <div class="iphone-app-logo">ث</div>
                                            <span>تطبيق ثمن</span>
                                        </div>
                                        <span>الآن</span>
                                    </div>
                                    <div class="iphone-title" id="previewPushTitle">يا هلا والله بالغالين! 📣</div>
                                    <div class="iphone-body" id="previewPushBody">اكتب محتوى الرسالة لتشاهد المعاينة الفورية هنا...</div>
                                </div>
                            </div>

                            <!-- معاينة المحادثة على الواتساب -->
                            <div class="mb-4" id="whatsappPreviewContainer" style="display: none;">
                                <label class="form-group-title small d-block"><i class="bx bxl-whatsapp text-success ml-1"></i> معاينة رسالة الواتساب (WhatsApp Message):</label>
                                <div class="wa-chat-window">
                                    <div class="wa-chat-header">
                                        <div class="wa-chat-avatar">ث</div>
                                        <div class="wa-chat-info">
                                            <span class="wa-chat-name">تطبيق ثمن</span>
                                            <span class="wa-chat-status">متصل الآن</span>
                                        </div>
                                    </div>
                                    <div class="wa-chat-body">
                                        <div class="wa-message-bubble">
                                            <div class="wa-message-text" id="previewWaBody">اكتب محتوى الرسالة لتشاهد المعاينة الفورية هنا...</div>
                                            <div class="wa-message-meta">
                                                <span class="wa-time">الآن</span>
                                                <span class="wa-double-check">✓✓</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
        
        // Emojis lists
        const emojiCategories = {
            "smileys": ["😀","😃","😄","😁","😆","😅","😂","🤣","😊","😇","🙂","🙃","😉","😌","😍","🥰","😘","😗","😙","😚","😋","😛","😝","😜","🤪","🤨","🧐","🤓","😎","🤩","🥳","😏","😒","😞","😔","😟","😕","🙁","☹️","😣","😖","😫","😩","🥺","😢","😭","😤","😠","😡","🤬","🤯","😳","🥵","🥶","😱","😨","😰","😥","😓","🤗","🤔","🤭","🤫","🤥","😶","😐","😑","😬","🙄","😯","😦","😧","😮","😲","🥱","😴","🤤","😪","😵","🤐","🥴","🤢","🤮","🤧","😷","🤒","🤕"],
            "hearts": ["❤️","🧡","💛","💚","💙","💜","🖤","🤍","🤎","💔","❣️","💕","💞","💓","💗","💖","💘","💝","💟"],
            "gestures": ["👍","👎","👌","🤌","🤏","✌️","🤞","🤟","🤘","🤙","👈","👉","👆","🖕","👇","☝️","👋","🤚","🖐️","✋","🖖","👏","🙌","👐","🤲","🤝","🙏","✍️","💅","🤳","💪","🦾"],
            "symbols": ["📣","🎉","⚖️","🔍","🔄","🔥","✅","🔔","🛒","⚠️","🚀","✨","💬","💭","✉️","📦"]
        };

        // تعبئة إيموجي درور
        function populateEmojiPicker(pickerId, category) {
            let grid = $('#' + pickerId).find('.wa-emoji-grid');
            grid.empty();
            let list = emojiCategories[category] || [];
            list.forEach(emoji => {
                grid.append(`<div class="wa-emoji-item">${emoji}</div>`);
            });
        }

        // تهيئة التاب والبحث للإيموجي
        $('.wa-emoji-picker').each(function() {
            let pickerId = $(this).attr('id');
            populateEmojiPicker(pickerId, 'smileys');
        });

        // النقر على تبويب الفئات داخل درج الإيموجي
        $('.wa-emoji-tab-btn').on('click', function(e) {
            e.stopPropagation();
            let cat = $(this).data('cat');
            let picker = $(this).closest('.wa-emoji-picker');
            let pickerId = picker.attr('id');
            
            picker.find('.wa-emoji-tab-btn').removeClass('active');
            $(this).addClass('active');
            
            populateEmojiPicker(pickerId, cat);
        });

        // فتح وإغلاق درج الإيموجي للعنوان والرسالة
        $('.wa-emoji-trigger').on('click', function(e) {
            e.stopPropagation();
            let pickerId = $(this).data('picker');
            $('.wa-emoji-picker').not('#' + pickerId).fadeOut(150);
            $('#' + pickerId).fadeToggle(150);
        });

        // إغلاق درج الإيموجي عند النقر خارج أي عنصر
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.wa-emoji-picker, .wa-emoji-trigger').length) {
                $('.wa-emoji-picker').fadeOut(150);
            }
        });

        // النقر على إيموجي وإدراجه
        $(document).on('click', '.wa-emoji-item', function(e) {
            e.stopPropagation();
            let emoji = $(this).text();
            let picker = $(this).closest('.wa-emoji-picker');
            let inputWrapper = picker.siblings('.wa-input-wrapper');
            let input = inputWrapper.find('input, textarea');
            
            let pos = input[0].selectionStart;
            let val = input.val();
            let newVal = val.slice(0, pos) + emoji + val.slice(pos);
            
            input.val(newVal);
            input.focus();
            
            // ضبط المؤشر
            input[0].setSelectionRange(pos + emoji.length, pos + emoji.length);
            
            updateLivePreview();
        });

        // تفعيل محرك البحث المتعدد Select2
        $('#specificUsers').select2({
            placeholder: "🔍 ابحث بالاسم أو رقم الجوال للاختيار...",
            allowClear: true,
            dir: "rtl"
        });

        // عند تغيير مستخدم في قائمة البحث المتعدد
        $('#specificUsers').on('change', function() {
            let selectedOptions = $(this).select2('data');
            let listContainer = $('#selectedUsersList');
            let container = $('#selectedUsersContainer');
            
            listContainer.empty();
            
            if(selectedOptions && selectedOptions.length > 0) {
                container.slideDown(200);
                selectedOptions.forEach(opt => {
                    let phone = $(opt.element).data('phone') || '';
                    let name = $(opt.element).data('name') || opt.text;
                    listContainer.append(`
                        <span class="selected-user-tag" data-id="${opt.id}">
                            <i class="bx bx-user align-middle"></i> ${name} (${phone})
                            <i class="bx bx-x align-middle user-tag-remove" style="cursor:pointer; color:#ef4444;" data-id="${opt.id}"></i>
                        </span>
                    `);
                });
            } else {
                container.slideUp(200);
            }
        });

        // حذف مستخدم من التاقات
        $(document).on('click', '.user-tag-remove', function() {
            let id = $(this).data('id');
            let select = $('#specificUsers');
            let currentValues = select.val();
            
            if(currentValues) {
                let index = currentValues.indexOf(id.toString());
                if (index > -1) {
                    currentValues.splice(index, 1);
                    select.val(currentValues).trigger('change');
                }
            }
        });

        // تحديث المعاينة الحية الفورية
        function updateLivePreview() {
            let titleVal = $('#inputTitle').val().trim();
            let bodyVal = $('#inputMessage').val().trim();
            
            // Push Notification
            $('#previewPushTitle').text(titleVal ? titleVal : 'تنبيه من تطبيق ثمن');
            $('#previewPushBody').text(bodyVal ? bodyVal : 'محتوى الرسالة يظهر هنا...');
            
            // WhatsApp Message
            $('#previewWaBody').text(bodyVal ? bodyVal : 'اكتب محتوى الرسالة لتشاهد المعاينة هنا...');
        }

        $('#inputTitle').on('input keyup change', updateLivePreview);
        $('#inputMessage').on('input keyup change', updateLivePreview);

        // تغيير خيارات الجمهور
        $('#recipientTypeSelect').on('change', function() {
            let val = $(this).val();
            if(val === 'custom') {
                $('#customRecipientDiv').slideDown(250);
                $('#finalRecipients').val('');
            } else {
                $('#customRecipientDiv').slideUp(250);
                $('#finalRecipients').val(val);
            }
        });

        // إظهار وإخفاء خيارات الملحقات والمعاينات بناءً على القنوات
        function adjustFieldsVisibility() {
            let isPushActive = $('#switchPush').is(':checked');
            let isWhatsappActive = $('#switchWhatsapp').is(':checked');
            
            if (isPushActive) {
                $('#pushTitleDiv').slideDown(200);
                $('#pushPreviewContainer').slideDown(200);
            } else {
                $('#pushTitleDiv').slideUp(200);
                $('#pushPreviewContainer').slideUp(200);
            }
            
            if (isWhatsappActive) {
                $('#whatsappMediaDiv').slideDown(200);
                $('#whatsappPreviewContainer').slideDown(200);
            } else {
                $('#whatsappMediaDiv').slideUp(200);
                $('#whatsappPreviewContainer').slideUp(200);
            }
        }

        adjustFieldsVisibility();
        updateLivePreview();

        $('.channel-checkbox').on('change', function() {
            adjustFieldsVisibility();
        });

        // التحقق عند الإرسال
        $('#notificationForm').on('submit', function() {
            let type = $('#recipientTypeSelect').val();
            
            if(type === 'custom') {
                let ids = $('#specificUsers').val();
                if(!ids || ids.length === 0) {
                    alert('⚠️ يرجى تحديد العملاء المستهدفين أولاً من قائمة البحث.');
                    return false;
                }
                $('#finalRecipients').val(ids.join(','));
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
