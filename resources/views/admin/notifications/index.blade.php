@extends('layouts.master')
@section('title', 'مركز البث')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#1c2230; --ink-soft:#2b3346; --paper:#f3efe6; --card:#ffffff;
  --gold:#b8893f; --gold-deep:#8a6428; --gold-soft:#f1e6d2;
  --line:#e6e1d3; --muted:#8b8779; --text:#211e1a; --wa:#1ea854;
}
.notify-page{direction:rtl;text-align:right;font-family:'Tajawal',sans-serif;color:var(--text);}
.notify-page .wrap{max-width:1180px;margin:0 auto;}

.console{
  background:var(--ink); border-radius:18px; padding:22px 28px;
  display:flex; align-items:center; justify-content:space-between; gap:20px;
  position:relative; overflow:hidden; margin-bottom:28px;
}
.console::after{
  content:''; position:absolute; inset:0;
  background:repeating-linear-gradient(90deg, rgba(255,255,255,0.025) 0 1px, transparent 1px 26px);
  pointer-events:none;
}
.console-left{display:flex;align-items:center;gap:16px;position:relative;z-index:1;}
.console-mark{
  width:50px;height:50px;border-radius:12px;background:var(--gold-soft);
  display:flex;align-items:center;justify-content:center;color:var(--gold-deep);font-size:1.5rem;flex:none;
}
.console-left h1{margin:0;font-size:1.25rem;font-weight:800;color:#fff;}
.console-left p{margin:2px 0 0;font-size:.8rem;color:#9aa3b8;font-weight:500;}
.on-air{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);padding:8px 16px;border-radius:30px;position:relative;z-index:1;}
.on-air .dot{width:8px;height:8px;border-radius:50%;background:#ff5b5b;animation:pulse 1.8s infinite;}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(255,91,91,.55);}70%{box-shadow:0 0 0 9px rgba(255,91,91,0);}100%{box-shadow:0 0 0 0 rgba(255,91,91,0);}}
.on-air span{font-size:.78rem;color:#cfd4e0;font-weight:700;letter-spacing:.3px;}

.notify-grid{display:grid;grid-template-columns:1.55fr 1fr;gap:24px;align-items:start;}
@media (max-width:980px){.notify-grid{grid-template-columns:1fr;}}

.panel{background:var(--card);border-radius:18px;border:1px solid var(--line);}
.panel-head{padding:20px 26px;border-bottom:1px solid var(--line);}
.panel-head .eyebrow{font-size:.68rem;font-weight:800;letter-spacing:1.5px;color:var(--gold-deep);text-transform:uppercase;display:block;}
.panel-head h2{margin:2px 0 0;font-size:1.05rem;font-weight:800;}
.panel-body{padding:26px;}

.field-label{font-weight:700;font-size:.85rem;color:var(--text);margin-bottom:10px;display:flex;align-items:center;gap:7px;}
.field-label i{color:var(--gold);font-size:1.1rem;}
.step-no{width:20px;height:20px;border-radius:6px;background:var(--gold-soft);color:var(--gold-deep);font-size:.7rem;font-weight:800;display:inline-flex;align-items:center;justify-content:center;flex:none;}

.switch-row{display:flex;flex-direction:column;gap:10px;}
.switch-opt{cursor:pointer;display:block;}
.switch-opt input{display:none;}
.switch{display:flex;align-items:center;justify-content:space-between;border:1px solid var(--line);border-radius:14px;padding:14px 18px;transition:.2s;background:#fdfcfa;}
.switch:hover{border-color:#d8cfb6;}
.switch-info{display:flex;align-items:center;gap:12px;}
.switch-ic{width:38px;height:38px;border-radius:10px;background:#f0ede4;display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:#9b9686;}
.switch-ic.wa{background:#eafaf0;color:var(--wa);}
.switch-info strong{display:block;font-size:.92rem;font-weight:700;}
.switch-info small{color:var(--muted);font-size:.76rem;font-weight:500;}
.toggle{width:42px;height:24px;border-radius:30px;background:#ddd6c4;position:relative;transition:.2s;flex:none;}
.toggle::after{content:'';position:absolute;top:3px;right:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2);}
.switch-opt input:checked + .switch{border-color:var(--gold);background:var(--gold-soft);}
.switch-opt input:checked + .switch .switch-ic{background:var(--gold);color:#fff;}
.switch-opt input:checked + .switch .switch-ic.wa{background:var(--wa);}
.switch-opt input:checked + .switch .toggle{background:var(--gold);}
.switch-opt input:checked + .switch.wa .toggle, .switch-opt:has(input[value="whatsapp"]):has(input:checked) .toggle{background:var(--wa);}
.switch-opt input:checked + .switch .toggle::after{right:21px;}

select.console-select{width:100%;border:1px solid var(--line);border-radius:12px;background:#fdfcfa;padding:13px 16px;font-family:'Tajawal',sans-serif;font-size:.92rem;font-weight:600;color:var(--text);}
select.console-select:focus{outline:none;border-color:var(--gold);}

.custom-box{background:#fdfcfa;border:1px dashed #d8cfb6;border-radius:14px;padding:18px;margin-top:14px;}

.field-wrap{position:relative;}
.tinput, textarea.tinput{width:100%;border:1px solid var(--line);border-radius:12px;background:#fdfcfa;padding:14px 46px 14px 16px;font-family:'Tajawal',sans-serif;font-size:.92rem;font-weight:600;color:var(--text);}
textarea.tinput{padding-top:16px;resize:vertical;}
.tinput:focus, textarea.tinput:focus{outline:none;border-color:var(--gold);box-shadow:0 0 0 3px rgba(184,137,63,.12);}
.emoji-btn{position:absolute;left:14px;top:16px;font-size:1.25rem;cursor:pointer;opacity:.7;z-index:5;}
.emoji-btn:hover{opacity:1;}
.char-count{font-size:.72rem;color:var(--muted);margin-top:6px;text-align:left;font-weight:600;}

.wa-attach{border:1px solid var(--line);border-radius:14px;padding:18px;margin-top:6px;background:#fafaf5;}
.wa-attach h6{margin:0 0 12px;font-size:.85rem;font-weight:800;display:flex;align-items:center;gap:8px;color:var(--wa);}
.file-input{border:1px dashed #d8cfb6;border-radius:10px;padding:10px 12px;font-size:.8rem;width:100%;background:#fff;}

.submit-btn{width:100%;background:var(--ink);color:#fff;border:none;border-radius:14px;padding:17px;font-size:1rem;font-weight:800;display:flex;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:.2s;font-family:'Tajawal',sans-serif;}
.submit-btn i{color:var(--gold);font-size:1.3rem;}
.submit-btn:hover{background:#0f1320;color:#fff;}

.preview-sticky{position:sticky;top:24px;}
.meter-card{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:20px 22px;margin-bottom:18px;}
.meter-title{font-size:.78rem;font-weight:800;color:var(--muted);margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.bars{display:flex;align-items:flex-end;gap:5px;height:32px;}
.bars i{width:7px;border-radius:3px;background:#e6e1d3;transition:.25s;font-style:normal;}
.bars i:nth-child(1){height:30%;}.bars i:nth-child(2){height:50%;}.bars i:nth-child(3){height:70%;}.bars i:nth-child(4){height:85%;}.bars i:nth-child(5){height:100%;}
.bars.l1 i:nth-child(1){background:var(--gold);}
.bars.l2 i:nth-child(1),.bars.l2 i:nth-child(2){background:var(--gold);}
.bars.l3 i:nth-child(1),.bars.l3 i:nth-child(2),.bars.l3 i:nth-child(3){background:var(--gold);}
.bars.l4 i:nth-child(1),.bars.l4 i:nth-child(2),.bars.l4 i:nth-child(3),.bars.l4 i:nth-child(4){background:var(--gold);}
.bars.l5 i{background:var(--gold);}

.phone{width:290px;height:430px;background:var(--ink);border-radius:38px;padding:10px;margin:0 auto;box-shadow:0 18px 40px rgba(28,34,48,.22);position:relative;}
.phone-screen{width:100%;height:100%;border-radius:28px;background:linear-gradient(165deg,#3a4258 0%, #1c2230 60%);position:relative;overflow:hidden;}
.phone-notch{width:110px;height:22px;background:#000;border-radius:0 0 14px 14px;position:absolute;top:0;right:50%;transform:translateX(50%);z-index:5;}
.phone-time{position:absolute;top:8px;left:20px;color:#fff;font-size:.78rem;font-weight:700;z-index:4;}
.notif-card{margin:54px 14px 0;background:rgba(255,255,255,.9);backdrop-filter:blur(14px);border-radius:16px;padding:13px 15px;box-shadow:0 8px 22px rgba(0,0,0,.18);}
.notif-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:7px;}
.notif-app{display:flex;align-items:center;gap:6px;}
.notif-app i{width:18px;height:18px;background:var(--gold);border-radius:5px;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.6rem;}
.notif-app span{font-size:.68rem;font-weight:800;letter-spacing:.4px;color:#333;}
.notif-time{font-size:.65rem;color:#777;}
.notif-title{font-weight:800;font-size:.9rem;color:#111;margin-bottom:2px;}
.notif-body{font-size:.78rem;color:#333;line-height:1.45;}

.wa-bubble-wrap{display:none;margin:54px 14px 0;}
.wa-bubble{background:#dcf8c6;border-radius:10px;border-top-right-radius:2px;padding:10px 12px;font-size:.78rem;color:#1b1b1b;line-height:1.5;box-shadow:0 2px 5px rgba(0,0,0,.12);position:relative;}
.wa-bubble .wa-title{font-weight:800;margin-bottom:2px;}
.wa-bubble .wa-time{display:block;text-align:left;font-size:.62rem;color:#5a8c5a;margin-top:4px;}

.hint{font-size:.78rem;color:var(--muted);text-align:center;margin-top:14px;font-weight:600;}

.tags-area{margin-top:14px;display:none;}
.tags-area span.tt{font-size:.76rem;color:var(--muted);font-weight:700;display:block;margin-bottom:8px;}
.tag{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid var(--line);border-radius:30px;padding:6px 12px;font-size:.78rem;font-weight:700;margin:0 4px 8px 0;}
.tag .remove-tag{width:16px;height:16px;border-radius:50%;background:#fbe3e3;color:#d24545;display:inline-flex;align-items:center;justify-content:center;font-size:.6rem;cursor:pointer;}

.emoji-panel{display:none;position:absolute;left:0;top:50px;z-index:1050;background:rgba(255,255,255,.97);border:1px solid var(--line);border-radius:16px;box-shadow:0 15px 35px rgba(0,0,0,.15);width:300px;overflow:hidden;}
.emoji-panel-tabs{display:flex;background:#f8f6f0;border-bottom:1px solid var(--line);padding:6px;gap:6px;}
.emoji-panel-tab{flex:1;background:transparent;border:none;border-radius:8px;padding:6px 0;font-size:.85rem;font-weight:700;color:var(--muted);cursor:pointer;}
.emoji-panel-tab.active{background:#fff;color:var(--gold-deep);}
.emoji-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;padding:10px;max-height:200px;overflow-y:auto;}
.emoji-cell{font-size:1.3rem;text-align:center;padding:6px 2px;border-radius:8px;cursor:pointer;}
.emoji-cell:hover{background:#f1f0ea;}

.select2-container--default .select2-selection--multiple{border:1px solid var(--line) !important;border-radius:12px !important;background:#fdfcfa !important;min-height:48px !important;}
.alert-success-console{background:#eef7ee;color:#2c6b2c;border:1px solid #cfe8cf;border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:10px;margin-bottom:20px;font-weight:700;}
</style>
@endsection

@section('page-header')
<div class="notify-page container-fluid mt-4">
    <div class="wrap">
        <div class="console">
            <div class="console-left">
                <div class="console-mark"><i class='bx bx-broadcast'></i></div>
                <div>
                    <h1>مركز البث</h1>
                    <p>إرسال إشعارات فورية ورسائل واتساب لعملاء ثمن</p>
                </div>
            </div>
            <div class="on-air"><span class="dot"></span><span>جاهز للإرسال</span></div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="notify-page container-fluid">
    <div class="wrap">

        @if(session('success'))
        <div class="alert-success-console">
            <i class="bx bx-check-circle fs-4"></i> {{ session('success') }}
        </div>
        @endif

        <div class="notify-grid">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">إعداد الرسالة</span>
                        <h2>تفاصيل البث</h2>
                    </div>
                </div>
                <div class="panel-body">
                    <form id="notificationForm" action="{{ route('admin.notifications.send') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- القنوات --}}
                        <div style="margin-bottom:28px;">
                            <div class="field-label"><span class="step-no">١</span> قنوات الإرسال</div>
                            <div class="switch-row">
                                <label class="switch-opt">
                                    <input type="checkbox" name="channels[]" value="push" id="chPush" checked>
                                    <div class="switch">
                                        <div class="switch-info">
                                            <div class="switch-ic"><i class='bx bx-mobile-alt'></i></div>
                                            <div><strong>إشعار فوري (Push)</strong><small>يظهر مباشرة على شاشة جوال العميل</small></div>
                                        </div>
                                        <div class="toggle"></div>
                                    </div>
                                </label>
                                <label class="switch-opt">
                                    <input type="checkbox" name="channels[]" value="whatsapp" id="chWhatsapp">
                                    <div class="switch wa">
                                        <div class="switch-info">
                                            <div class="switch-ic wa"><i class='bx bxl-whatsapp'></i></div>
                                            <div><strong>واتساب</strong><small>رسالة نصية مع إمكانية إرفاق صورة أو ملف</small></div>
                                        </div>
                                        <div class="toggle"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- الجمهور --}}
                        <div style="margin-bottom:28px;">
                            <div class="field-label"><span class="step-no">٢</span> الجمهور المستهدف</div>
                            <select id="recipientType" name="recipients_type" class="console-select">
                                <option value="users" selected>جميع العملاء — إرسال جماعي</option>
                                <option value="custom">عملاء محددون</option>
                            </select>

                            <div id="customDiv" class="custom-box" style="display:none;">
                                <div class="field-label" style="margin-bottom:8px;"><i class='bx bx-search-alt'></i> ابحث بالاسم أو رقم الجوال</div>
                                <select id="specificUsers" name="specific_users[]" style="width:100%;" multiple>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}"
                                            data-name="{{ $user->first_name }} {{ $user->last_name }}"
                                            data-phone="{{ $user->phone }}"
                                        >{{ $user->first_name }} {{ $user->last_name }} &mdash; {{ $user->phone }}</option>
                                    @endforeach
                                </select>
                                <div id="tagsBox" class="tags-area">
                                    <span class="tt">المستلمون المختارون</span>
                                    <div id="tagsList"></div>
                                </div>
                            </div>
                        </div>

                        {{-- العنوان --}}
                        <div id="headerDiv" style="margin-bottom:24px;">
                            <div class="field-label"><span class="step-no">٣</span> عنوان الإشعار</div>
                            <div class="field-wrap">
                                <input type="text" id="inputTitle" name="title" class="tinput" placeholder="مثال: عرض اليوم على المنتجات المختارة">
                                <span class="emoji-btn" data-target="inputTitle" data-panel="emojiPanelTitle">🙂</span>
                                <div class="emoji-panel" id="emojiPanelTitle"></div>
                            </div>
                        </div>

                        {{-- الرسالة --}}
                        <div style="margin-bottom:8px;">
                            <div class="field-label"><span class="step-no">٤</span> نص الرسالة</div>
                            <div class="field-wrap">
                                <textarea id="inputMessage" name="message" class="tinput" rows="5" required placeholder="اكتب تفاصيل الإشعار بوضوح ووضّح أي إجراء مطلوب من العميل"></textarea>
                                <span class="emoji-btn" data-target="inputMessage" data-panel="emojiPanelBody" style="top:16px;">🙂</span>
                                <div class="emoji-panel" id="emojiPanelBody" style="top:60px;"></div>
                            </div>
                            <div class="char-count" id="charCount">0 حرف</div>
                        </div>

                        {{-- مرفقات واتساب --}}
                        <div id="waMediaDiv" class="wa-attach" style="display:none;">
                            <h6><i class='bx bxl-whatsapp'></i> مرفقات واتساب (اختياري)</h6>
                            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                                <div style="flex:1;min-width:140px;">
                                    <small style="font-weight:700;color:var(--muted);display:block;margin-bottom:6px;">صورة</small>
                                    <input type="file" name="image" class="file-input" accept="image/*">
                                </div>
                                <div style="flex:1;min-width:140px;">
                                    <small style="font-weight:700;color:var(--muted);display:block;margin-bottom:6px;">فيديو</small>
                                    <input type="file" name="video" class="file-input" accept="video/*">
                                </div>
                                <div style="flex:1;min-width:140px;">
                                    <small style="font-weight:700;color:var(--muted);display:block;margin-bottom:6px;">ملف (PDF..)</small>
                                    <input type="file" name="file" class="file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="recipients" id="finalRecipients" value="users">

                        <div style="margin-top:30px;">
                            <button type="submit" class="submit-btn">إرسال الإشعار الآن <i class='bx bx-send'></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="preview-sticky" id="pushPreviewArea">
                <div class="meter-card">
                    <div class="meter-title"><i class='bx bx-pulse'></i> قوة الرسالة</div>
                    <div class="bars" id="bars"><i></i><i></i><i></i><i></i><i></i></div>
                    <p style="font-size:.74rem;color:var(--muted);margin:10px 0 0;font-weight:600;" id="meterLabel">ابدأ بالكتابة لمعاينة قوة الرسالة</p>
                </div>

                <div class="phone">
                    <div class="phone-screen">
                        <div class="phone-notch"></div>
                        <div class="phone-time">٩:٤١</div>

                        <div class="notif-card" id="iosNotifyNode">
                            <div class="notif-top">
                                <div class="notif-app"><i class='bx bx-cube'></i><span>ثمن</span></div>
                                <span class="notif-time">الآن</span>
                            </div>
                            <div class="notif-title" id="pvTitle">عنوان الإشعار</div>
                            <div class="notif-body" id="pvBody">سيظهر نص رسالتك هنا فور كتابتها...</div>
                        </div>

                        <div class="wa-bubble-wrap" id="waPreview">
                            <div class="wa-bubble">
                                <div class="wa-title" id="waPvTitle">عنوان الإشعار</div>
                                <div id="waPvBody">سيظهر نص رسالتك هنا فور كتابتها...</div>
                                <span class="wa-time">9:41 ص ✓✓</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="hint">هكذا سيرى العميل رسالتك على هاتفه</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    const EMOJI_CATS = {
        '😊 وجوه': ['😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇','🙂','😉','😌','😍','🥰','😘','😋','😎','🤩','🥳','😏','😢','😭','😤','😠','😡','🤔','😶','🙄','😯','😲','😴','🤐','😷'],
        '❤️ قلوب': ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','💕','💞','💓','💗','💖','💘','💝','♥️'],
        '👍 إيماءات': ['👍','👎','👌','✌️','🤞','🤟','🤙','👈','👉','👆','👋','🙌','👏','🙏','💪'],
        '✨ رموز': ['📣','🎉','✅','🔔','🔥','🚀','✨','⭐','🌟','💫','🎁','🎊','🏆','🔑','💡','📌','📍','🔗','💬','📩','📢','⚡','🌙','☀️','🌈'],
    };

    function buildEmojiPanel(panelId) {
        const $panel = $('#' + panelId);
        if ($panel.find('.emoji-grid').length) return;
        let tabs = '<div class="emoji-panel-tabs">';
        let grids = '';
        let isFirst = true;
        for (let cat in EMOJI_CATS) {
            let catKey = cat.replace(/[^a-zA-Z0-9]/g, '_');
            tabs += `<button type="button" class="emoji-panel-tab ${isFirst?'active':''}" data-cat="${catKey}">${cat.split(' ')[0]}</button>`;
            grids += `<div class="emoji-grid" data-grid="${catKey}" ${isFirst?'':'style="display:none"'}>`;
            EMOJI_CATS[cat].forEach(e => { grids += `<span class="emoji-cell">${e}</span>`; });
            grids += '</div>';
            isFirst = false;
        }
        tabs += '</div>';
        $panel.html(tabs + `<div class="emoji-panel-body">${grids}</div>`);
        $panel.on('click', '.emoji-panel-tab', function() {
            $panel.find('.emoji-panel-tab').removeClass('active');
            $(this).addClass('active');
            $panel.find('.emoji-grid').hide();
            $panel.find(`.emoji-grid[data-grid="${$(this).data('cat')}"]`).show();
        });
    }

    $(document).on('click', '.emoji-btn', function(e) {
        e.stopPropagation();
        let panelId = $(this).data('panel');
        buildEmojiPanel(panelId);
        let $panel = $('#' + panelId);
        $('.emoji-panel').not($panel).hide();
        $panel.fadeToggle(150);
    });

    $(document).on('click', '.emoji-cell', function(e) {
        e.stopPropagation();
        let emoji = $(this).text();
        let $panel = $(this).closest('.emoji-panel');
        let targetId = $('[data-panel="' + $panel.attr('id') + '"]').data('target');
        let $input = $('#' + targetId);
        let el = $input[0];
        let start = el.selectionStart || 0, end = el.selectionEnd || 0, val = el.value;
        el.value = val.slice(0, start) + emoji + val.slice(end);
        el.setSelectionRange(start + emoji.length, start + emoji.length);
        $input.focus().trigger('input');
        $panel.fadeOut(150);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.field-wrap').length) $('.emoji-panel').fadeOut(150);
    });

    $('#specificUsers').select2({ placeholder: 'ابحث بالاسم أو رقم الجوال...', allowClear: true, dir: 'rtl', width: '100%' });

    $('#specificUsers').on('change', function() {
        let selected = $(this).select2('data');
        let $list = $('#tagsList'), $box = $('#tagsBox');
        $list.empty();
        if (selected && selected.length) {
            $box.slideDown(150);
            selected.forEach(function(opt) {
                let name = $(opt.element).data('name') || opt.text;
                $list.append(`<span class="tag" data-id="${opt.id}">${name}<span class="remove-tag" data-id="${opt.id}">×</span></span>`);
            });
        } else { $box.slideUp(150); }
    });

    $(document).on('click', '.remove-tag', function() {
        let id = $(this).data('id').toString();
        let cur = ($('#specificUsers').val() || []).filter(v => v !== id);
        $('#specificUsers').val(cur).trigger('change');
    });

    $('#recipientType').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customDiv').slideDown(250);
            $('#finalRecipients').val('');
        } else {
            $('#customDiv').slideUp(250);
            $('#finalRecipients').val($(this).val());
        }
    });

    function updateChannelUI() {
        let isPush = $('#chPush').is(':checked');
        let isWa = $('#chWhatsapp').is(':checked');
        isPush ? $('#headerDiv, #pushPreviewArea').fadeIn(200) : $('#headerDiv, #pushPreviewArea').hide();
        isWa ? $('#waMediaDiv').slideDown(200) : $('#waMediaDiv').slideUp(200);
        $('#inputTitle').prop('required', isPush);
        $('#iosNotifyNode').toggle(isPush);
        $('#waPreview').toggle(isWa);
    }
    $('.switch-opt input').on('change', updateChannelUI);
    updateChannelUI();

    function updatePreview() {
        let t = $('#inputTitle').val().trim() || 'عنوان الإشعار';
        let b = $('#inputMessage').val().trim() || 'سيظهر نص رسالتك هنا فور كتابتها...';
        $('#pvTitle, #waPvTitle').text(t);
        $('#pvBody, #waPvBody').text(b);

        let len = $('#inputTitle').val().length + $('#inputMessage').val().length;
        $('#charCount').text(len + ' حرف');
        let level = len === 0 ? 0 : Math.min(5, Math.ceil(len / 20));
        $('#bars').attr('class', 'bars l' + level);
        let labels = ['ابدأ بالكتابة لمعاينة قوة الرسالة','رسالة مختصرة','رسالة جيدة الطول','رسالة واضحة ومتكاملة','رسالة مفصلة','أقصى وضوح ممكن'];
        $('#meterLabel').text(labels[level]);
    }
    $('#inputTitle, #inputMessage').on('input change', updatePreview);
    updatePreview();

    $('#notificationForm').on('submit', function(e) {
        let type = $('#recipientType').val();
        if (type === 'custom') {
            let ids = $('#specificUsers').val();
            if (!ids || ids.length === 0) {
                alert('الرجاء اختيار مستخدم واحد على الأقل.');
                e.preventDefault(); return;
            }
            $('#finalRecipients').val(ids.join(','));
        } else {
            $('#finalRecipients').val(type);
        }
        if ($('.switch-opt input:checked').length === 0) {
            alert('الرجاء تفعيل قناة إرسال واحدة على الأقل (إشعارات أو واتساب).');
            e.preventDefault();
        }
    });
});
</script>
@endsection
