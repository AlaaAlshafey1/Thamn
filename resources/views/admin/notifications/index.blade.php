@extends('layouts.master')
@section('title', 'مركز الإشعارات')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .notify-page { direction: rtl; text-align: right; }

    /* Emoji Picker */
    .field-wrapper { position: relative; }

    .emoji-icon-btn {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.4rem;
        cursor: pointer;
        user-select: none;
        z-index: 10;
        line-height: 1;
    }
    .emoji-icon-btn.for-textarea {
        top: 16px;
        transform: none;
    }

    .emoji-panel {
        display: none;
        position: absolute;
        left: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        width: 340px;
        overflow: hidden;
    }
    .emoji-panel.panel-for-header { top: 50px; }
    .emoji-panel.panel-for-body   { top: 115px; }

    .emoji-panel-tabs {
        display: flex;
        background: #f5f5f5;
        border-bottom: 1px solid #eee;
        padding: 4px 6px;
        gap: 4px;
    }
    .emoji-panel-tab {
        flex: 1;
        background: none;
        border: none;
        border-radius: 6px;
        padding: 5px 0;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .emoji-panel-tab:hover, .emoji-panel-tab.active {
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .emoji-panel-search {
        padding: 6px 8px;
        border-bottom: 1px solid #eee;
    }
    .emoji-panel-search input {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.85rem;
        direction: ltr;
    }
    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 2px;
        padding: 8px;
        max-height: 200px;
        overflow-y: auto;
    }
    .emoji-cell {
        font-size: 1.35rem;
        text-align: center;
        padding: 4px 2px;
        border-radius: 5px;
        cursor: pointer;
        user-select: none;
        transition: background 0.1s, transform 0.1s;
    }
    .emoji-cell:hover {
        background: #f0f0f0;
        transform: scale(1.2);
    }

    /* Selected users tags */
    .user-tags-box {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 12px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        min-height: 46px;
    }
    .user-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #c1953e22;
        border: 1px solid #c1953e55;
        color: #8a6520;
        border-radius: 20px;
        padding: 3px 10px 3px 6px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .user-tag .remove-tag {
        cursor: pointer;
        font-size: 0.9rem;
        color: #c0392b;
        font-weight: bold;
    }

    /* Channel badges */
    .channel-opt { cursor: pointer; }
    .channel-opt input[type=checkbox] { display: none; }
    .channel-label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        background: #f8f9fa;
        font-weight: bold;
        transition: all 0.2s;
        cursor: pointer;
    }
    .channel-opt input:checked + .channel-label {
        border-color: #c1953e;
        background: #c1953e11;
        color: #7a5e1c;
    }

    /* Select2 */
    .select2-container--default .select2-selection--single {
        height: 44px !important;
        border: 1px solid #ced4da !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
    }

    /* Preview box */
    .preview-wrap {
        background: #fafafa;
        border: 1px dashed #c1953e55;
        border-radius: 10px;
        padding: 16px;
        margin-top: 24px;
    }
    .preview-app-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: #888;
        margin-bottom: 6px;
        font-weight: bold;
    }
    .preview-app-dot {
        width: 14px; height: 14px;
        background: #c1953e;
        border-radius: 3px;
        display: inline-block;
    }
    .preview-title { font-weight: bold; font-size: 0.92rem; color: #111; margin-bottom: 2px; }
    .preview-body  { font-size: 0.84rem; color: #555; line-height: 1.45; }
</style>
@endpush

@section('page-header')
<div class="notify-page container-fluid">
    <div class="page-header py-3 px-4 mt-3 mb-4 bg-white shadow-sm rounded-3 border">
        <h4 class="mb-1 fw-bold text-primary"><i class="bx bx-paper-plane"></i> مركز إرسال الإشعارات</h4>
        <p class="text-muted mb-0 small">أرسل تنبيهات فورية (Push) أو رسائل واتساب للعملاء بشكل فردي أو جماعي</p>
    </div>
</div>
@endsection

@section('content')
<div class="notify-page container-fluid">
    <div class="row">
        <div class="col-lg-7 col-md-10 mx-auto">

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2">
                <i class="bx bx-check-circle fs-5 text-success"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-cog text-warning align-middle me-1"></i> إعداد الإشعار</h5>
                </div>
                <div class="card-body p-4">
                    <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- 1. القنوات --}}
                        <div class="mb-4">
                            <label class="fw-bold d-block mb-2 text-dark"><i class="bx bx-broadcast text-warning me-1"></i>قنوات الإرسال:</label>
                            <div class="d-flex gap-3 flex-wrap">
                                <label class="channel-opt m-0">
                                    <input type="checkbox" name="channels[]" value="push" id="chPush" checked>
                                    <div class="channel-label">
                                        <i class="bx bx-mobile-alt fs-5"></i> إشعارات الهاتف (Push)
                                    </div>
                                </label>
                                <label class="channel-opt m-0">
                                    <input type="checkbox" name="channels[]" value="whatsapp" id="chWhatsapp">
                                    <div class="channel-label">
                                        <i class="bx bxl-whatsapp fs-5 text-success"></i> رسائل الواتساب
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- 2. الجمهور --}}
                        <div class="mb-4">
                            <label class="fw-bold d-block mb-2 text-dark" for="recipientType"><i class="bx bx-group text-warning me-1"></i>الجمهور المستهدف:</label>
                            <select id="recipientType" name="recipients_type" class="form-select form-select-lg" style="border-radius:8px; height:46px; font-size:0.95rem;">
                                <option value="users" selected>👥 كل العملاء (إرسال جماعي)</option>
                                <option value="custom">🎯 مستخدم أو أكثر بعينهم</option>
                            </select>
                        </div>

                        {{-- قائمة اختيار المستخدمين (مخفية) --}}
                        <div id="customDiv" class="mb-4" style="display:none;">
                            <label class="fw-bold d-block mb-2 text-dark"><i class="bx bx-search text-warning me-1"></i>اختر المستخدمين (يظهر من لديهم توكن فعال فقط):</label>
                            <select id="specificUsers" name="specific_users[]" class="form-control" style="width:100%;" multiple>
                                @foreach($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        data-name="{{ $user->first_name }} {{ $user->last_name }}"
                                        data-phone="{{ $user->phone }}"
                                    >{{ $user->first_name }} {{ $user->last_name }} &mdash; {{ $user->phone }}</option>
                                @endforeach
                            </select>

                            {{-- تاقات المستخدمين المختارين --}}
                            <div id="tagsBox" class="user-tags-box mt-3" style="display:none;">
                                <span class="text-muted small fw-bold w-100 d-block mb-1">📋 المستلمون المختارون:</span>
                                <div id="tagsList" class="d-flex flex-wrap gap-2 w-100"></div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 3. عنوان الإشعار --}}
                        <div class="mb-4" id="headerDiv">
                            <label class="fw-bold d-block mb-2 text-dark" for="inputTitle">
                                <i class="bx bx-heading text-warning me-1"></i>عنوان الإشعار – Header:
                            </label>
                            <div class="field-wrapper">
                                <input
                                    type="text"
                                    id="inputTitle"
                                    name="title"
                                    class="form-control form-control-lg ps-5"
                                    placeholder="اكتب العنوان هنا..."
                                    style="border-radius:8px; padding-left:42px !important;"
                                >
                                <span class="emoji-icon-btn" data-target="inputTitle" data-panel="emojiPanelTitle" title="أضف إيموجي">😊</span>
                                {{-- لوحة الإيموجي للعنوان --}}
                                <div class="emoji-panel panel-for-header" id="emojiPanelTitle"></div>
                            </div>
                        </div>

                        {{-- 4. محتوى الرسالة --}}
                        <div class="mb-4">
                            <label class="fw-bold d-block mb-2 text-dark" for="inputMessage">
                                <i class="bx bx-message-square-detail text-warning me-1"></i>نص الرسالة – Body:
                            </label>
                            <div class="field-wrapper">
                                <textarea
                                    id="inputMessage"
                                    name="message"
                                    class="form-control"
                                    rows="5"
                                    placeholder="اكتب نص الرسالة والإشعار هنا..."
                                    style="border-radius:8px; padding-left:42px !important;"
                                    required
                                ></textarea>
                                <span class="emoji-icon-btn for-textarea" data-target="inputMessage" data-panel="emojiPanelBody" title="أضف إيموجي">😊</span>
                                {{-- لوحة الإيموجي للرسالة --}}
                                <div class="emoji-panel panel-for-body" id="emojiPanelBody"></div>
                            </div>
                        </div>

                        {{-- مرفقات الواتساب (مخفية) --}}
                        <div id="waMediaDiv" style="display:none;" class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="fw-bold small d-block mb-1"><i class="bx bx-image me-1"></i>صورة مرفقة (للواتساب):</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" style="border-radius:8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold small d-block mb-1"><i class="bx bx-file me-1"></i>ملف مرفق (للواتساب):</label>
                                    <input type="file" name="file" class="form-control" style="border-radius:8px;">
                                </div>
                            </div>
                        </div>

                        {{-- معاينة حية Push --}}
                        <div id="pushPreview" class="preview-wrap">
                            <div class="preview-app-bar">
                                <span class="preview-app-dot"></span>
                                <span>ثمن · Thamn</span>
                                <span class="ms-auto text-muted small">الآن</span>
                            </div>
                            <div class="preview-title" id="pvTitle">عنوان الإشعار سيظهر هنا...</div>
                            <div class="preview-body"  id="pvBody">محتوى الرسالة سيظهر هنا فور الكتابة...</div>
                        </div>

                        <input type="hidden" name="recipients" id="finalRecipients" value="users">

                        {{-- زر الإرسال --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg w-100 fw-bold text-white shadow-sm" style="background:#c1953e; border-radius:10px; padding:13px; font-size:1.05rem;">
                                <i class="bx bx-send me-1"></i> إرسال الآن
                            </button>
                        </div>

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
$(function() {

    /* ===================================================
       1. بيانات الإيموجي
    =================================================== */
    const EMOJI_CATS = {
        '😊 ابتسامات': ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','😉','😌','😍','🥰','😘','😋','😛','😜','🤪','😎','🤩','🥳','😏','😒','😢','😭','😤','😠','😡','😳','😱','😨','😰','🤗','🤔','😶','😐','😑','🙄','😯','😮','😲','🥱','😴','🤐','😷','🤒'],
        '❤️ قلوب': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','♥️','🫀','💌'],
        '👍 إيماءات': ['👍','👎','👌','🤌','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','👇','👋','🤚','✋','🙌','👏','🙏','💪','✍️','💅'],
        '✨ رموز': ['📣','🎉','✅','🔔','🔥','🚀','✨','⭐','🌟','💫','🎁','🎊','🏆','🔑','💡','📌','📍','🔗','💬','📩','📢','⚡','🌙','☀️','🌈','🌺','🌸'],
    };

    /* بناء لوحة إيموجي كاملة */
    function buildEmojiPanel(panelId) {
        const $panel = $('#' + panelId);
        if ($panel.find('.emoji-grid').length) return; // مبنية مسبقاً

        let tabs = '<div class="emoji-panel-tabs">';
        let grids = '';
        let isFirst = true;

        for (let cat in EMOJI_CATS) {
            let catKey = cat.replace(/[^a-zA-Z0-9]/g, '_');
            let activeTab = isFirst ? 'active' : '';
            let activeGrid = isFirst ? '' : 'style="display:none"';
            tabs += `<button type="button" class="emoji-panel-tab ${activeTab}" data-cat="${catKey}">${cat.split(' ')[0]}</button>`;
            grids += `<div class="emoji-grid" data-grid="${catKey}" ${activeGrid}>`;
            EMOJI_CATS[cat].forEach(e => { grids += `<span class="emoji-cell">${e}</span>`; });
            grids += '</div>';
            isFirst = false;
        }

        tabs += '</div>';
        let search = `<div class="emoji-panel-search"><input type="text" placeholder="ابحث عن إيموجي..." id="emojiSearch_${panelId}"></div>`;
        let body = `<div class="emoji-panel-body" style="padding:6px;">${grids}</div>`;

        $panel.html(tabs + search + body);

        // تبويبات
        $panel.on('click', '.emoji-panel-tab', function() {
            $panel.find('.emoji-panel-tab').removeClass('active');
            $(this).addClass('active');
            $panel.find('.emoji-grid').hide();
            $panel.find(`.emoji-grid[data-grid="${$(this).data('cat')}"]`).show();
        });

        // بحث
        $panel.on('input', `#emojiSearch_${panelId}`, function() {
            let q = $(this).val().trim();
            if (!q) {
                $panel.find('.emoji-panel-tab.active').click();
                return;
            }
            $panel.find('.emoji-grid').hide();
            // عرض كل الإيموجي للبحث
            let allEmojis = [];
            for (let c in EMOJI_CATS) allEmojis = allEmojis.concat(EMOJI_CATS[c]);
            let results = allEmojis.filter(e => e.includes(q));
            // استخدم أول جريد لعرض النتائج
            let $first = $panel.find('.emoji-grid').first();
            $first.empty().show();
            results.forEach(e => $first.append(`<span class="emoji-cell">${e}</span>`));
            if (!results.length) $first.html('<span class="text-muted small p-2">لا توجد نتائج</span>');
        });
    }

    /* 2. إظهار/إخفاء لوحة الإيموجي بالنقر على الأيقونة */
    $(document).on('click', '.emoji-icon-btn', function(e) {
        e.stopPropagation();
        let panelId = $(this).data('panel');
        buildEmojiPanel(panelId);
        let $panel = $('#' + panelId);
        // أغلق أي لوحة أخرى
        $('.emoji-panel').not($panel).hide();
        $panel.toggle();
    });

    /* 3. إدراج الإيموجي عند النقر */
    $(document).on('click', '.emoji-cell', function(e) {
        e.stopPropagation();
        let emoji = $(this).text();
        let $panel = $(this).closest('.emoji-panel');
        let panelId = $panel.attr('id');
        // اعرف الـ input المرتبط بهذه اللوحة
        let targetId = $('[data-panel="' + panelId + '"]').data('target');
        let $input = $('#' + targetId);

        let el = $input[0];
        let start = el.selectionStart || 0;
        let end   = el.selectionEnd   || 0;
        let val   = el.value;
        el.value = val.slice(0, start) + emoji + val.slice(end);
        el.setSelectionRange(start + emoji.length, start + emoji.length);
        $input.focus().trigger('input');

        $panel.hide();
    });

    /* إغلاق اللوحة عند النقر خارجها */
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.field-wrapper').length) {
            $('.emoji-panel').hide();
        }
    });

    /* ===================================================
       4. Select2 للمستخدمين
    =================================================== */
    $('#specificUsers').select2({
        placeholder: '🔍 ابحث بالاسم أو رقم الجوال...',
        allowClear: true,
        dir: 'rtl',
        width: '100%',
    });

    /* تحديث تاقات المستلمين */
    $('#specificUsers').on('change', function() {
        let selected = $(this).select2('data');
        let $list = $('#tagsList');
        let $box  = $('#tagsBox');
        $list.empty();

        if (selected && selected.length) {
            $box.show();
            selected.forEach(function(opt) {
                let name  = $(opt.element).data('name')  || opt.text;
                let phone = $(opt.element).data('phone') || '';
                $list.append(
                    `<span class="user-tag" data-id="${opt.id}">
                        <i class="bx bx-user-circle"></i> ${name}
                        <small class="text-muted">(${phone})</small>
                        <span class="remove-tag" data-id="${opt.id}" title="إزالة">✕</span>
                    </span>`
                );
            });
        } else {
            $box.hide();
        }
    });

    /* حذف تاق من القائمة */
    $(document).on('click', '.remove-tag', function() {
        let id  = $(this).data('id').toString();
        let cur = $('#specificUsers').val() || [];
        cur = cur.filter(v => v !== id);
        $('#specificUsers').val(cur).trigger('change');
    });

    /* ===================================================
       5. إظهار/إخفاء قسم المستخدمين
    =================================================== */
    $('#recipientType').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customDiv').slideDown(200);
            $('#finalRecipients').val('');
        } else {
            $('#customDiv').slideUp(200);
            $('#finalRecipients').val($(this).val());
        }
    });

    /* ===================================================
       6. إظهار/إخفاء حسب القنوات
    =================================================== */
    function updateChannelUI() {
        let isPush = $('#chPush').is(':checked');
        let isWa   = $('#chWhatsapp').is(':checked');

        isPush ? $('#headerDiv,#pushPreview').show() : $('#headerDiv,#pushPreview').hide();
        isWa   ? $('#waMediaDiv').show() : $('#waMediaDiv').hide();
    }

    $('.channel-opt input').on('change', updateChannelUI);
    updateChannelUI();

    /* ===================================================
       7. معاينة حية
    =================================================== */
    function updatePreview() {
        let t = $('#inputTitle').val().trim();
        let b = $('#inputMessage').val().trim();
        $('#pvTitle').text(t || 'عنوان الإشعار سيظهر هنا...');
        $('#pvBody').text(b  || 'محتوى الرسالة سيظهر هنا فور الكتابة...');
    }
    $('#inputTitle, #inputMessage').on('input change', updatePreview);

    /* ===================================================
       8. التحقق قبل الإرسال
    =================================================== */
    $('#notificationForm').on('submit', function(e) {
        let type = $('#recipientType').val();

        if (type === 'custom') {
            let ids = $('#specificUsers').val();
            if (!ids || ids.length === 0) {
                alert('⚠️ اختر مستخدماً واحداً على الأقل.');
                e.preventDefault(); return;
            }
            $('#finalRecipients').val(ids.join(','));
        } else {
            $('#finalRecipients').val(type);
        }

        if ($('.channel-opt input:checked').length === 0) {
            alert('⚠️ فعّل قناة إرسال واحدة على الأقل.');
            e.preventDefault();
        }
    });

});
</script>
@endpush
