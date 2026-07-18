@extends('layouts.master')
@section('title', 'عرض السؤال — ' . $question->question_ar)

@section('css')
<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');

body { background: #f0f2f7; font-family: 'Tajawal', sans-serif; }

/* ── Layout ── */
.show-wrapper {
    max-width: 860px;
    margin: 0 auto;
    direction: rtl;
    padding-bottom: 60px;
}

/* ── Back Button ── */
.btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    background: #fff; color: #374151;
    border: 1.5px solid #e8eaf0; border-radius: 12px;
    padding: 9px 18px; font-weight: 700; font-size: 0.88rem;
    text-decoration: none; transition: all 0.2s; margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.btn-back:hover { border-color: #F8B400; color: #92620a; background: #fffbeb; transform: translateX(4px); }

/* ── Hero Card ── */
.hero-card {
    background: linear-gradient(135deg, #1a1d23 0%, #2d3139 60%, #31363F 100%);
    border-radius: 24px;
    padding: 32px 36px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.hero-card::before {
    content: '';
    position: absolute;
    top: -60px; left: -60px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(248,180,0,0.12) 0%, transparent 70%);
    border-radius: 50%;
}
.hero-card::after {
    content: '"';
    position: absolute;
    bottom: -20px; left: 20px;
    font-size: 10rem; line-height: 1;
    color: rgba(248,180,0,0.06);
    font-family: Georgia, serif;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(248,180,0,0.15);
    border: 1px solid rgba(248,180,0,0.3);
    color: #F8B400; border-radius: 20px;
    padding: 5px 14px; font-size: 0.8rem; font-weight: 700;
    margin-bottom: 16px;
    backdrop-filter: blur(4px);
}
.hero-question-text {
    color: #fff; font-size: 1.45rem; font-weight: 800;
    line-height: 1.5; margin: 0 0 8px;
    position: relative; z-index: 1;
}
.hero-question-en {
    color: rgba(255,255,255,0.45); font-size: 0.95rem;
    font-weight: 500; margin: 0;
    position: relative; z-index: 1;
}
.hero-meta {
    display: flex; flex-wrap: wrap; gap: 10px;
    margin-top: 24px; position: relative; z-index: 1;
}
.meta-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.75);
    border-radius: 10px; padding: 6px 14px;
    font-size: 0.82rem; font-weight: 600;
    backdrop-filter: blur(4px);
}
.meta-chip i { font-size: 0.95rem; color: #F8B400; }
.meta-chip.active { background: rgba(16,185,129,0.15); border-color: rgba(16,185,129,0.3); color: #34d399; }
.meta-chip.inactive { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.25); color: #f87171; }
.meta-chip.required { background: rgba(248,180,0,0.15); border-color: rgba(248,180,0,0.3); color: #F8B400; }

/* ── Section Card ── */
.section-card {
    background: #fff;
    border-radius: 18px;
    margin-bottom: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1.5px solid #f0f2f5;
    overflow: hidden;
}
.section-head {
    padding: 16px 24px;
    border-bottom: 1px solid #f0f2f5;
    display: flex; align-items: center; gap: 10px;
}
.section-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.section-icon.yellow { background: #fffbeb; color: #F8B400; }
.section-icon.blue   { background: #eff6ff; color: #3b82f6; }
.section-icon.purple { background: #f5f3ff; color: #8b5cf6; }
.section-icon.green  { background: #ecfdf5; color: #10b981; }
.section-icon.gray   { background: #f3f4f6; color: #6b7280; }

.section-head-title { font-size: 1rem; font-weight: 800; color: #1a1d23; margin: 0; }
.section-head-sub   { font-size: 0.8rem; color: #9ca3af; margin: 0; }

.section-body { padding: 20px 24px; }

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.info-item {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 12px 16px;
    border: 1px solid #f0f2f5;
}
.info-item-label {
    font-size: 0.75rem; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.4px;
    margin-bottom: 5px;
}
.info-item-value { font-size: 0.95rem; font-weight: 700; color: #1a1d23; }
.info-item-value.mono { font-family: monospace; font-size: 0.85rem; color: #6b7280; }

/* ── Options ── */
.options-list { display: flex; flex-direction: column; gap: 10px; }

.option-item {
    border: 1.5px solid #f0f2f5;
    border-radius: 14px;
    overflow: hidden;
    transition: border-color 0.2s;
}
.option-item:hover { border-color: #e0e3ea; }

.option-main {
    padding: 14px 18px;
    display: flex; align-items: center; gap: 12px;
    background: #fff;
}
.option-num {
    width: 30px; height: 30px;
    background: linear-gradient(135deg, #1a1d23, #31363F);
    color: #F8B400; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 0.82rem; flex-shrink: 0;
}
.option-content { flex-grow: 1; }
.option-ar { font-weight: 700; color: #1a1d23; font-size: 0.95rem; }
.option-en { color: #9ca3af; font-size: 0.82rem; margin-top: 2px; }
.option-desc { color: #6b7280; font-size: 0.82rem; margin-top: 4px; font-style: italic; }

.option-thumb {
    width: 44px; height: 44px;
    border-radius: 10px; object-fit: cover;
    border: 2px solid #f0f2f5; flex-shrink: 0;
}

.option-badges { display: flex; gap: 6px; flex-wrap: wrap; }
.opt-badge {
    font-size: 0.75rem; font-weight: 700;
    padding: 3px 10px; border-radius: 6px;
}
.opt-badge.range { background: #eff6ff; color: #3b82f6; }
.opt-badge.price { background: #ecfdf5; color: #10b981; }
.opt-badge.badge-tag { background: #f5f3ff; color: #8b5cf6; }

.sub-options-area {
    background: #f8f9fc;
    border-top: 1.5px dashed #e8eaf0;
    padding: 12px 18px 12px 18px;
}
.sub-options-label {
    font-size: 0.75rem; font-weight: 700; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.4px;
    margin-bottom: 8px;
}
.sub-option-item {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 12px;
    background: #fff; border-radius: 8px;
    margin-bottom: 6px; border: 1px solid #e8eaf0;
    font-size: 0.88rem; color: #374151; font-weight: 600;
}
.sub-option-item:last-child { margin-bottom: 0; }
.sub-dot { width: 6px; height: 6px; background: #F8B400; border-radius: 50%; flex-shrink: 0; }

/* ── Slider Preview ── */
.slider-preview-wrap { padding: 4px 0; }
.slider-labels {
    display: flex; justify-content: space-between;
    margin-bottom: 10px;
}
.slider-label { font-size: 0.82rem; font-weight: 700; color: #6b7280; }
.slider-value-display {
    text-align: center; font-size: 1.6rem; font-weight: 800;
    color: #1a1d23; margin: 12px 0 4px;
}
.slider-value-display span { color: #F8B400; }
input[type=range] {
    width: 100%; appearance: none;
    height: 6px; border-radius: 3px;
    background: linear-gradient(to left, #f0f2f5, #F8B400 0%);
    outline: none; cursor: pointer;
}
input[type=range]::-webkit-slider-thumb {
    appearance: none;
    width: 20px; height: 20px;
    border-radius: 50%;
    background: #1a1d23;
    border: 3px solid #F8B400;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: transform 0.2s;
}
input[type=range]::-webkit-slider-thumb:hover { transform: scale(1.2); }

/* ── Hint Card ── */
.hint-card {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1.5px solid #fde68a;
    border-radius: 12px;
    padding: 14px 18px;
    display: flex; gap: 10px; align-items: flex-start;
}
.hint-card i { color: #F8B400; font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
.hint-text { font-size: 0.9rem; font-weight: 600; color: #78350f; }

/* ── Action Buttons ── */
.actions-bar {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin-top: 28px; padding-top: 24px;
    border-top: 1px solid #f0f2f5;
}
.btn-action {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 22px; border-radius: 12px;
    font-weight: 700; font-size: 0.9rem;
    text-decoration: none; transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: none; cursor: pointer;
}
.btn-edit-action {
    background: linear-gradient(135deg, #F8B400, #e6a500);
    color: #1a1d23;
    box-shadow: 0 4px 14px rgba(248,180,0,0.3);
}
.btn-edit-action:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(248,180,0,0.4); color: #1a1d23; }
.btn-back-action {
    background: #fff; color: #374151;
    border: 1.5px solid #e8eaf0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.btn-back-action:hover { background: #f3f4f6; border-color: #d1d5db; color: #1a1d23; }
</style>
@endsection

@section('content')
<div class="container-fluid">
<div class="show-wrapper">

    {{-- Back --}}
    <a href="{{ route('questions.index', ['flow' => request('flow', $question->flow)]) }}" class="btn-back">
        <i class="bx bx-arrow-right"></i> العودة للأسئلة
    </a>

    {{-- ══ Hero ══ --}}
    <div class="hero-card">
        <div class="hero-badge">
            <i class="bx bx-help-circle"></i>
            سؤال #{{ $question->id }}
            &nbsp;·&nbsp;
            <span style="opacity:0.7;">{{ $question->category->name_ar ?? 'غير مصنف' }}</span>
        </div>

        <h1 class="hero-question-text">{{ $question->question_ar }} ؟</h1>

        @if($question->question_en)
            <p class="hero-question-en">{{ $question->question_en }}</p>
        @endif

        <div class="hero-meta">
            <span class="meta-chip">
                <i class="bx bx-category"></i>
                {{ $question->type }}
            </span>
            <span class="meta-chip">
                <i class="bx bx-sort-alt-2"></i>
                ترتيب {{ $question->order ?? '—' }}
            </span>
            <span class="meta-chip">
                <i class="bx bx-git-branch"></i>
                {{ match($question->flow) {
                    'valuation' => 'تثمين',
                    'market'    => 'السوق',
                    'both'      => 'كلاهما',
                    default     => $question->flow ?? '—'
                } }}
            </span>
            @if($question->is_required)
                <span class="meta-chip required">
                    <i class="bx bx-shield-alt-2"></i> إجباري
                </span>
            @endif
            <span class="meta-chip {{ $question->is_active ? 'active' : 'inactive' }}">
                <i class="bx {{ $question->is_active ? 'bx-check-circle' : 'bx-x-circle' }}"></i>
                {{ $question->is_active ? 'مفعّل' : 'معطّل' }}
            </span>
        </div>
    </div>

    {{-- ══ Description ══ --}}
    @if($question->description_ar || $question->description_en)
    <div class="section-card">
        <div class="section-head">
            <div class="section-icon blue"><i class="bx bx-text"></i></div>
            <div>
                <p class="section-head-title">الوصف</p>
                <p class="section-head-sub">نص توضيحي يظهر أسفل السؤال</p>
            </div>
        </div>
        <div class="section-body">
            @if($question->description_ar)
                <div class="info-item mb-3">
                    <div class="info-item-label">العربية</div>
                    <div class="info-item-value">{{ $question->description_ar }}</div>
                </div>
            @endif
            @if($question->description_en)
                <div class="info-item">
                    <div class="info-item-label">English</div>
                    <div class="info-item-value">{{ $question->description_en }}</div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══ Settings / Hint ══ --}}
    @if($question->settings && (data_get($question->settings,'hint.ar') || data_get($question->settings,'hint.en') || data_get($question->settings,'titleDescription.ar')))
    <div class="section-card">
        <div class="section-head">
            <div class="section-icon yellow"><i class="bx bx-info-circle"></i></div>
            <div>
                <p class="section-head-title">الإعدادات والتلميحات</p>
                <p class="section-head-sub">نصوص مساعدة إضافية</p>
            </div>
        </div>
        <div class="section-body" style="display:flex; flex-direction:column; gap:10px;">
            @if(data_get($question->settings,'hint.ar'))
            <div class="hint-card">
                <i class="bx bx-bulb"></i>
                <div>
                    <div class="hint-text" style="margin-bottom:2px;">{{ data_get($question->settings,'hint.ar') }}</div>
                    @if(data_get($question->settings,'hint.en'))
                        <div style="color:#a16207; font-size:0.82rem;">{{ data_get($question->settings,'hint.en') }}</div>
                    @endif
                </div>
            </div>
            @endif
            @if(data_get($question->settings,'titleDescription.ar'))
            <div class="info-item">
                <div class="info-item-label">وصف العنوان</div>
                <div class="info-item-value">{{ data_get($question->settings,'titleDescription.ar') }}</div>
                @if(data_get($question->settings,'titleDescription.en'))
                    <div style="color:#9ca3af; font-size:0.82rem; margin-top:4px;">{{ data_get($question->settings,'titleDescription.en') }}</div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ══ Slider Preview ══ --}}
    @php
    $sliderTypes = ['valueRangeSlider','singleSelectionSlider','progress'];
    @endphp
    @if(in_array($question->type, $sliderTypes) && ($question->min_value !== null || $question->max_value !== null))
    <div class="section-card">
        <div class="section-head">
            <div class="section-icon purple"><i class="bx bx-slider-alt"></i></div>
            <div>
                <p class="section-head-title">معاينة السلايدر</p>
                <p class="section-head-sub">min: {{ $question->min_value }} — max: {{ $question->max_value }} — step: {{ $question->step ?? 1 }}</p>
            </div>
        </div>
        <div class="section-body">
            <div class="slider-preview-wrap">
                <div class="slider-value-display" id="sliderValDisplay">
                    <span id="sliderVal">{{ $question->min_value }}</span>
                </div>
                <div class="slider-labels">
                    <span class="slider-label">{{ $question->min_value }}</span>
                    <span class="slider-label">{{ $question->max_value }}</span>
                </div>
                <input type="range"
                    min="{{ $question->min_value }}"
                    max="{{ $question->max_value }}"
                    step="{{ $question->step ?? 1 }}"
                    value="{{ $question->min_value }}"
                    id="sliderPreview"
                    oninput="document.getElementById('sliderVal').textContent = this.value; this.style.background = 'linear-gradient(to left, #f0f2f5 ' + ((this.value - this.min)/(this.max - this.min)*100) + '%, #F8B400 ' + ((this.value - this.min)/(this.max - this.min)*100) + '%)'">
            </div>
        </div>
    </div>
    @endif

    {{-- ══ Options ══ --}}
    @php $mainOptions = $question->options->whereNull('parent_option_id'); @endphp
    @if($mainOptions->count())
    <div class="section-card">
        <div class="section-head">
            <div class="section-icon green"><i class="bx bx-list-ul"></i></div>
            <div>
                <p class="section-head-title">الخيارات</p>
                <p class="section-head-sub">{{ $mainOptions->count() }} خيار متاح</p>
            </div>
        </div>
        <div class="section-body">
            <div class="options-list">
                @foreach($mainOptions as $i => $option)
                <div class="option-item">
                    <div class="option-main">
                        <div class="option-num">{{ $i + 1 }}</div>

                        @if($option->image)
                            <img src="{{ asset('storage/' . $option->image) }}" class="option-thumb" alt="">
                        @endif

                        <div class="option-content" style="flex-grow:1;">
                            <div class="option-ar">{{ $option->option_ar }}</div>
                            @if($option->option_en)
                                <div class="option-en">{{ $option->option_en }}</div>
                            @endif
                            @if($option->description_ar)
                                <div class="option-desc">{{ $option->description_ar }}</div>
                            @endif
                        </div>

                        <div class="option-badges">
                            @if($option->min !== null || $option->max !== null)
                                <span class="opt-badge range">
                                    {{ $option->min }} — {{ $option->max }}
                                </span>
                            @endif
                            @if($option->price)
                                <span class="opt-badge price">
                                    <i class="bx bx-dollar"></i> {{ $option->price }}
                                </span>
                            @endif
                            @if($option->badge)
                                <span class="opt-badge badge-tag">{{ $option->badge }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Sub Options --}}
                    @if($option->subOptions && $option->subOptions->count())
                    <div class="sub-options-area">
                        <div class="sub-options-label">خيارات فرعية ({{ $option->subOptions->count() }})</div>
                        @foreach($option->subOptions as $sub)
                        <div class="sub-option-item">
                            <span class="sub-dot"></span>
                            <span>{{ $sub->option_ar }}</span>
                            @if($sub->option_en)
                                <span style="color:#9ca3af; font-size:0.8rem;">({{ $sub->option_en }})</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ══ Actions ══ --}}
    <div class="actions-bar">
        <a href="{{ route('questions.edit', [$question->id, 'flow' => request('flow', $question->flow)]) }}" class="btn-action btn-edit-action">
            <i class="bx bx-edit-alt"></i> تعديل السؤال
        </a>
        <a href="{{ route('questions.index', ['flow' => request('flow', $question->flow)]) }}" class="btn-action btn-back-action">
            <i class="bx bx-arrow-right"></i> العودة للقائمة
        </a>
    </div>

</div>
</div>
@endsection
