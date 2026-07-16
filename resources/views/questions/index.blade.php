@extends('layouts.master')
@section('title', 'إدارة الأسئلة')

@section('css')
<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');

body { background-color: #f0f2f5; font-family: 'Tajawal', sans-serif; }

/* ── Page Header ── */
.page-header {
    margin-bottom: 28px;
    padding: 24px 0 20px;
    border-bottom: 1px solid #e8eaf0;
}
.page-title { font-size: 1.6rem; font-weight: 800; color: #1a1d23; margin: 0 0 4px; }
.page-subtitle { color: #8b92a5; font-size: 0.92rem; margin: 0; }

.btn-add {
    background: linear-gradient(135deg, #31363F 0%, #1a1d23 100%);
    color: #F8B400;
    border: none;
    padding: 12px 28px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(49,54,63,0.25);
}
.btn-add:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(49,54,63,0.35); color: #F8B400; }

/* ── Filter Card ── */
.filter-card {
    background: #fff;
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    border: 1px solid #f0f2f5;
}
.filter-card .form-control,
.filter-card .form-select {
    border-radius: 10px;
    border: 1.5px solid #e8eaf0;
    padding: 10px 14px;
    font-size: 0.92rem;
    transition: all 0.2s;
    background: #f8f9fc;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: #F8B400;
    box-shadow: 0 0 0 3px rgba(248,180,0,0.12);
    background: #fff;
}
.filter-label { font-size: 0.8rem; font-weight: 700; color: #8b92a5; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }

/* ── Category Card ── */
.category-card { margin-bottom: 20px; }

.category-header {
    background: linear-gradient(135deg, #23262d 0%, #2d3139 100%);
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s;
    cursor: pointer;
}
.category-header:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.15); }

.category-header-inner {
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cat-left { display: flex; align-items: center; gap: 14px; }
.cat-dot {
    width: 10px; height: 10px;
    background: #F8B400;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(248,180,0,0.2);
    flex-shrink: 0;
}
.category-title { color: #F8B400; font-size: 1.15rem; font-weight: 800; margin: 0; letter-spacing: -0.2px; }
.cat-count {
    background: rgba(248,180,0,0.15);
    color: #F8B400;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
}
.cat-chevron { color: rgba(255,255,255,0.5); font-size: 1.3rem; transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1); }
.category-card.collapsed .cat-chevron { transform: rotate(180deg); }

/* Steps Tabs */
.steps-bar {
    display: flex;
    overflow-x: auto;
    gap: 0;
    border-top: 1px solid rgba(255,255,255,0.06);
    padding: 0 24px;
    scrollbar-width: none;
}
.steps-bar::-webkit-scrollbar { display: none; }
.step-tab {
    color: rgba(255,255,255,0.35);
    font-weight: 600;
    font-size: 0.88rem;
    padding: 12px 16px;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2.5px solid transparent;
    transition: all 0.2s;
    letter-spacing: 0.1px;
}
.step-tab.active { color: #F8B400; border-bottom-color: #F8B400; }
.step-tab:hover:not(.active) { color: rgba(255,255,255,0.7); }

/* ── Questions Body ── */
.category-body { padding: 14px 0 4px; }

.sortable-list {
    list-style: none;
    padding: 0; margin: 0;
    display: flex; flex-direction: column;
    gap: 8px;
}

.question-item {
    background: #fff;
    border-radius: 14px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.2s ease;
    border: 1.5px solid #f0f2f5;
    position: relative;
}
.question-item:hover {
    border-color: #e0e3ea;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    transform: translateX(-2px);
}
.question-item.sortable-ghost { opacity: 0.4; background: #f8f9fc; border: 2px dashed #F8B400; }
.question-item.sortable-drag { box-shadow: 0 12px 35px rgba(0,0,0,0.12) !important; transform: scale(1.01); }

/* Drag Handle */
.drag-handle {
    color: #d1d5db;
    cursor: grab;
    font-size: 1.1rem;
    padding: 4px;
    flex-shrink: 0;
    transition: color 0.2s;
}
.drag-handle:hover { color: #9ca3af; }
.question-item:active .drag-handle { cursor: grabbing; }

/* Question Number */
.question-number {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, #1a1d23, #31363F);
    color: #F8B400;
    border-radius: 10px;
    display: flex; justify-content: center; align-items: center;
    font-weight: 800; font-size: 0.9rem;
    flex-shrink: 0;
}

/* Question Text */
.question-text-content { flex-grow: 1; min-width: 0; }
.question-title {
    font-size: 1rem; font-weight: 700; color: #1a1d23;
    margin: 0 0 3px;
    display: flex; align-items: center; gap: 6px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.question-subtitle { font-size: 0.82rem; color: #9ca3af; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Actions */
.question-actions-group {
    display: flex; align-items: center;
    gap: 6px; flex-shrink: 0;
}

.move-btns-group { display: flex; flex-direction: column; gap: 3px; }
.move-btn {
    width: 24px; height: 24px;
    border-radius: 6px; border: 1.5px solid #e8eaf0;
    background: #f8f9fc;
    color: #b0b7c3; font-size: 0.85rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; padding: 0;
}
.move-btn:hover { background: #23262d; color: #F8B400; border-color: #23262d; }

.toggle-wrap { padding: 0 4px; }
.form-check-input { cursor: pointer; }
.form-check-input:checked { background-color: #F8B400; border-color: #F8B400; }
.form-check-input:focus { box-shadow: 0 0 0 3px rgba(248,180,0,0.2); }

.action-btn {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: #f8f9fc;
    border: 1.5px solid #e8eaf0;
    color: #8b92a5;
    font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s;
    text-decoration: none; flex-shrink: 0;
}
.action-btn:hover { transform: scale(1.08); }
.action-btn.btn-edit:hover   { color: #F8B400; border-color: #F8B400; background: #fffbeb; }
.action-btn.btn-view:hover   { color: #3b82f6; border-color: #3b82f6; background: #eff6ff; }
.action-btn.btn-copy:hover   { color: #10b981; border-color: #10b981; background: #ecfdf5; }
.action-btn.btn-delete:hover { color: #ef4444; border-color: #ef4444; background: #fef2f2; }

/* ── Duplicate Modal ── */
.dup-modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(10,12,18,0.55);
    backdrop-filter: blur(6px);
    z-index: 1050;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.3s;
}
.dup-modal-backdrop.show { opacity: 1; pointer-events: all; }

.dup-modal {
    background: #fff;
    border-radius: 24px;
    width: 480px; max-width: 95vw;
    box-shadow: 0 24px 60px rgba(0,0,0,0.2);
    transform: translateY(30px) scale(0.96);
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    direction: rtl;
}
.dup-modal-backdrop.show .dup-modal { transform: translateY(0) scale(1); }

.dup-modal-header {
    background: linear-gradient(135deg, #23262d 0%, #31363F 100%);
    padding: 22px 28px;
    display: flex; align-items: center; justify-content: space-between;
}
.dup-modal-title { color: #F8B400; font-weight: 800; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 10px; }
.dup-modal-close {
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(255,255,255,0.1); border: none;
    color: rgba(255,255,255,0.6); font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s;
}
.dup-modal-close:hover { background: rgba(255,255,255,0.2); color: #fff; }

.dup-modal-body { padding: 28px; }

.dup-question-preview {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 24px;
    border-right: 4px solid #F8B400;
}
.dup-question-preview p { margin: 0; color: #1a1d23; font-weight: 700; font-size: 0.95rem; }
.dup-question-preview small { color: #8b92a5; font-size: 0.8rem; }

.dup-field-label {
    font-size: 0.82rem; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.5px;
    margin-bottom: 10px; display: block;
}

.flow-options { display: flex; gap: 10px; margin-bottom: 20px; }
.flow-option {
    flex: 1;
    border: 2px solid #e8eaf0;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}
.flow-option input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
.flow-option.selected { border-color: #F8B400; background: #fffbeb; }
.flow-option .flow-icon { font-size: 1.4rem; margin-bottom: 4px; display: block; }
.flow-option .flow-name { font-size: 0.85rem; font-weight: 700; color: #374151; }
.flow-option.selected .flow-name { color: #92620a; }

.dup-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #e8eaf0;
    border-radius: 12px;
    font-size: 0.92rem;
    background: #f8f9fc;
    color: #1a1d23;
    outline: none;
    transition: all 0.2s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%238b92a5' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: left 14px center;
    padding-left: 36px;
}
.dup-select:focus { border-color: #F8B400; box-shadow: 0 0 0 3px rgba(248,180,0,0.12); background-color: #fff; }

.dup-modal-footer {
    padding: 0 28px 28px;
    display: flex; gap: 10px; justify-content: flex-end;
}
.btn-dup-cancel {
    padding: 11px 22px;
    border-radius: 12px; border: 1.5px solid #e8eaf0;
    background: #fff; color: #6b7280; font-weight: 700;
    cursor: pointer; transition: all 0.2s; font-size: 0.92rem;
}
.btn-dup-cancel:hover { background: #f3f4f6; border-color: #d1d5db; }
.btn-dup-confirm {
    padding: 11px 28px;
    border-radius: 12px; border: none;
    background: linear-gradient(135deg, #F8B400, #e6a500);
    color: #1a1d23; font-weight: 800;
    cursor: pointer; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: 0.92rem; display: flex; align-items: center; gap: 7px;
    box-shadow: 0 4px 14px rgba(248,180,0,0.3);
}
.btn-dup-confirm:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(248,180,0,0.4); }

/* ── Empty State ── */
.empty-state {
    text-align: center; padding: 32px 20px;
    background: #fff; border-radius: 14px;
    border: 1.5px dashed #e0e3ea;
}
.empty-state i { font-size: 2.5rem; color: #d1d5db; display: block; margin-bottom: 10px; }
.empty-state p { color: #9ca3af; font-size: 0.9rem; margin: 0; }

/* ── Toast ── */
.custom-toast {
    position: fixed; bottom: 28px; left: 28px;
    background: #1a1d23; color: #fff;
    padding: 14px 20px; border-radius: 14px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.25);
    transform: translateY(80px) scale(0.9); opacity: 0;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 9999; font-weight: 600; font-size: 0.9rem;
    display: flex; align-items: center; gap: 10px;
    border-right: 3px solid #F8B400; max-width: 320px;
}
.custom-toast.show { transform: translateY(0) scale(1); opacity: 1; }
.custom-toast.error { border-right-color: #ef4444; }
</style>
@endsection

@section('content')
<div class="container-fluid" style="direction: rtl; max-width: 1100px;">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="page-title">إدارة الأسئلة والمراحل</h3>
            <p class="page-subtitle">اسحب وأفلت الأسئلة لإعادة ترتيبها، أو استخدم أزرار ↑↓</p>
        </div>
        <a href="{{ route('questions.create', ['flow' => request('flow')]) }}" class="btn-add">
            <i class="bx bx-plus fs-5"></i> سؤال جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-4" role="alert" style="background:#ecfdf5; color:#065f46; border-right:4px solid #10b981 !important;">
            <i class="bx bx-check-circle fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <div class="filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="filter-label">بحث باسم السؤال</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#f8f9fc; border:1.5px solid #e8eaf0; border-left:none; border-radius:10px 0 0 10px;">
                        <i class="bx bx-search" style="color:#8b92a5;"></i>
                    </span>
                    <input type="text" id="filterName" class="form-control" placeholder="ابحث عن سؤال..." style="border-right:none; border-radius:0 10px 10px 0;">
                </div>
            </div>
            <div class="col-md-4">
                <label class="filter-label">الفئة</label>
                <select id="filterCategory" class="form-select">
                    <option value="">جميع الفئات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="filter-label">المرحلة</label>
                <select id="filterStage" class="form-select">
                    <option value="">جميع المراحل</option>
                    @foreach($steps as $st)
                        <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Categories --}}
    @foreach ($categories as $index => $category)
        @php
            $categoryStepIds = $category->questions->pluck('stageing')->unique()->toArray();
            $categorySteps = $steps->filter(fn($s) => in_array($s->id, $categoryStepIds))->values();
            $totalQ = $category->questions->count();
        @endphp

        <div class="category-card {{ $index == 0 ? '' : 'collapsed' }}" id="category-card-{{ $category->id }}">
            <div class="category-header">
                <div class="category-header-inner" onclick="toggleCategory('category-card-{{ $category->id }}', 'category-collapse-{{ $category->id }}')">
                    <div class="cat-left">
                        <span class="cat-dot"></span>
                        <h4 class="category-title">{{ $category->name_ar }}</h4>
                        <span class="cat-count">{{ $totalQ }} سؤال</span>
                    </div>
                    <i class="bx bx-chevron-up cat-chevron"></i>
                </div>

                <div id="category-collapse-{{ $category->id }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                    @if($categorySteps->count() > 0)
                        <div class="steps-bar">
                            @foreach($categorySteps as $stepIndex => $step)
                                <div class="step-tab {{ $stepIndex == 0 ? 'active' : '' }}"
                                     onclick="switchStep({{ $category->id }}, {{ $step->id }}, this)">
                                    {{ $step->name_ar }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="category-body" id="category-body-{{ $category->id }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                @if($categorySteps->count() > 0)
                    @foreach($categorySteps as $stepIndex => $step)
                        @php $stepQuestions = $category->questions->where('stageing', $step->id); @endphp
                        <div class="step-content step-content-{{ $category->id }}"
                             id="step-content-{{ $category->id }}-{{ $step->id }}"
                             data-step-id="{{ $step->id }}"
                             style="display: {{ $stepIndex == 0 ? 'block' : 'none' }};">
                            @if($stepQuestions->count() > 0)
                                <ul class="sortable-list" data-category-id="{{ $category->id }}" data-step-id="{{ $step->id }}">
                                    @foreach ($stepQuestions as $question)
                                    <li class="question-item" data-id="{{ $question->id }}">
                                        <i class="bx bx-grid-vertical drag-handle"></i>
                                        <div class="question-number">{{ $loop->iteration }}</div>
                                        <div class="question-text-content">
                                            <div class="question-title">
                                                {{ $question->question_ar }} ؟
                                                <i class="bx bx-help-circle text-warning" style="font-size:0.95rem; opacity:0.7;"></i>
                                            </div>
                                            @if($question->question_en)
                                                <div class="question-subtitle">{{ $question->question_en }}</div>
                                            @endif
                                        </div>
                                        <div class="question-actions-group">
                                            <div class="move-btns-group">
                                                <button class="move-btn move-up-btn" title="أعلى"><i class="bx bx-chevron-up"></i></button>
                                                <button class="move-btn move-down-btn" title="أسفل"><i class="bx bx-chevron-down"></i></button>
                                            </div>
                                            <div class="toggle-wrap">
                                                <div class="form-check form-switch m-0" title="تفعيل/تعطيل">
                                                    <input class="form-check-input toggle-active-btn" type="checkbox" role="switch"
                                                           data-id="{{ $question->id }}" {{ $question->is_active ? 'checked' : '' }}
                                                           style="width:2.2em; height:1.1em; cursor:pointer; margin:0;">
                                                </div>
                                            </div>
                                            <a href="{{ route('questions.edit', [$question->id, 'flow' => request('flow')]) }}"
                                               class="action-btn btn-edit" title="تعديل">
                                                <i class="bx bx-edit-alt"></i>
                                            </a>
                                            <a href="{{ route('questions.show', [$question->id, 'flow' => request('flow')]) }}"
                                               class="action-btn btn-view" title="عرض">
                                                <i class="bx bx-show-alt"></i>
                                            </a>
                                            <button class="action-btn btn-copy open-dup-modal" title="نسخ السؤال"
                                                    data-id="{{ $question->id }}"
                                                    data-name="{{ $question->question_ar }}"
                                                    data-url="{{ route('questions.duplicate', $question->id) }}"
                                                    data-flow="{{ $question->flow }}"
                                                    data-category="{{ $question->category_id }}"
                                                    data-step="{{ $question->stageing }}">
                                                <i class="bx bx-copy-alt"></i>
                                            </button>
                                            <form action="{{ route('questions.destroy', [$question->id, 'flow' => request('flow')]) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete" title="حذف">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty-state">
                                    <i class="bx bx-list-plus"></i>
                                    <p>لا توجد أسئلة في هذه المرحلة بعد</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="bx bx-question-mark"></i>
                        <p>لا توجد أسئلة في هذه الفئة بعد</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Uncategorized --}}
    @if(isset($uncategorizedQuestions) && $uncategorizedQuestions->count() > 0)
        <div class="category-card collapsed mt-4" id="category-card-uncategorized">
            <div class="category-header" style="background: linear-gradient(135deg,#374151,#4b5563);">
                <div class="category-header-inner" onclick="toggleCategory('category-card-uncategorized','category-collapse-uncategorized')">
                    <div class="cat-left">
                        <span class="cat-dot" style="background:#9ca3af; box-shadow:0 0 0 3px rgba(156,163,175,0.2);"></span>
                        <h4 class="category-title" style="color:#e5e7eb;">أسئلة غير مصنفة</h4>
                        <span class="cat-count" style="background:rgba(255,255,255,0.1); color:#d1d5db;">{{ $uncategorizedQuestions->count() }}</span>
                    </div>
                    <i class="bx bx-chevron-up cat-chevron"></i>
                </div>
                <div id="category-collapse-uncategorized" style="display:none;"></div>
            </div>
            <div class="category-body" id="category-body-uncategorized" style="display:none;">
                <ul class="sortable-list" data-category-id="0">
                    @foreach ($uncategorizedQuestions as $key => $question)
                    <li class="question-item" data-id="{{ $question->id }}">
                        <i class="bx bx-grid-vertical drag-handle"></i>
                        <div class="question-number" style="background:linear-gradient(135deg,#4b5563,#374151);">{{ $key + 1 }}</div>
                        <div class="question-text-content">
                            <div class="question-title">{{ $question->question_ar }} ؟</div>
                            @if($question->question_en)
                                <div class="question-subtitle">{{ $question->question_en }}</div>
                            @endif
                        </div>
                        <div class="question-actions-group">
                            <div class="move-btns-group">
                                <button class="move-btn move-up-btn" title="أعلى"><i class="bx bx-chevron-up"></i></button>
                                <button class="move-btn move-down-btn" title="أسفل"><i class="bx bx-chevron-down"></i></button>
                            </div>
                            <div class="toggle-wrap">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input toggle-active-btn" type="checkbox" role="switch"
                                           data-id="{{ $question->id }}" {{ $question->is_active ? 'checked' : '' }}
                                           style="width:2.2em; height:1.1em; cursor:pointer; margin:0;">
                                </div>
                            </div>
                            <a href="{{ route('questions.edit', [$question->id, 'flow' => request('flow')]) }}" class="action-btn btn-edit" title="تعديل">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                            <button class="action-btn btn-copy open-dup-modal" title="نسخ السؤال"
                                    data-id="{{ $question->id }}"
                                    data-name="{{ $question->question_ar }}"
                                    data-url="{{ route('questions.duplicate', $question->id) }}"
                                    data-flow="{{ $question->flow }}"
                                    data-category="{{ $question->category_id }}"
                                    data-step="{{ $question->stageing }}">
                                <i class="bx bx-copy-alt"></i>
                            </button>
                            <form action="{{ route('questions.destroy', [$question->id, 'flow' => request('flow')]) }}"
                                  method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn btn-delete" title="حذف">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>

{{-- ═══════════════ Duplicate Modal ═══════════════ --}}
<div class="dup-modal-backdrop" id="dupModalBackdrop">
    <div class="dup-modal">
        <div class="dup-modal-header">
            <h5 class="dup-modal-title">
                <i class="bx bx-copy-alt" style="font-size:1.3rem;"></i>
                نسخ السؤال
            </h5>
            <button class="dup-modal-close" id="closeDupModal">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="dup-modal-body">
            {{-- Question preview --}}
            <div class="dup-question-preview">
                <p id="dupQuestionName"></p>
                <small>سيتم إنشاء نسخة من هذا السؤال مع جميع خياراته</small>
            </div>

            {{-- Flow Selection --}}
            <label class="dup-field-label">إرسال النسخة إلى</label>
            <div class="flow-options mb-4">
                <label class="flow-option selected" id="flow-valuation">
                    <input type="radio" name="dup_flow" value="valuation" checked>
                    <span class="flow-icon">🎯</span>
                    <span class="flow-name">تثمين</span>
                </label>
                <label class="flow-option" id="flow-market">
                    <input type="radio" name="dup_flow" value="market">
                    <span class="flow-icon">🛒</span>
                    <span class="flow-name">السوق</span>
                </label>
                <label class="flow-option" id="flow-both">
                    <input type="radio" name="dup_flow" value="both">
                    <span class="flow-icon">✨</span>
                    <span class="flow-name">كلاهما</span>
                </label>
            </div>

            {{-- Category --}}
            <label class="dup-field-label">الفئة</label>
            <select class="dup-select mb-4" id="dupCategory">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                @endforeach
            </select>

            {{-- Step/Stage --}}
            <label class="dup-field-label">المرحلة</label>
            <select class="dup-select" id="dupStep">
                <option value="">— بدون مرحلة —</option>
                @foreach($steps as $st)
                    <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                @endforeach
            </select>
        </div>
        <div class="dup-modal-footer">
            <button class="btn-dup-cancel" id="cancelDupModal">إلغاء</button>
            <form id="dupForm" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="flow" id="dupFlowInput">
                <input type="hidden" name="category_id" id="dupCategoryInput">
                <input type="hidden" name="stageing" id="dupStepInput">
                <input type="hidden" name="current_flow" value="{{ request('flow') }}">
                <button type="submit" class="btn-dup-confirm">
                    <i class="bx bx-copy-alt"></i> نسخ الآن
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// ── Toggle Category ──
function toggleCategory(cardId, collapseId) {
    let card = $('#' + cardId), collapse = $('#' + collapseId);
    let body = $('#' + cardId.replace('category-card-', 'category-body-'));
    if (card.hasClass('collapsed')) {
        card.removeClass('collapsed');
        collapse.slideDown(280);
        body.slideDown(280);
    } else {
        card.addClass('collapsed');
        collapse.slideUp(280);
        body.slideUp(280);
    }
}

// ── Switch Step Tab ──
function switchStep(categoryId, stepId, tab) {
    $(tab).siblings().removeClass('active');
    $(tab).addClass('active');
    $('.step-content-' + categoryId).hide();
    $('#step-content-' + categoryId + '-' + stepId).fadeIn(180);
}

$(document).ready(function () {

    // ── Filters ──
    function applyFilters() {
        let name = $('#filterName').val().toLowerCase();
        let catId = $('#filterCategory').val();
        let stageId = $('#filterStage').val();

        $('.category-card').each(function () {
            let cardId = $(this).attr('id');
            if (!cardId) return;
            let currentCat = cardId.replace('category-card-', '');
            let matchCat = (catId === '' || currentCat == catId);
            let hasVisible = false;

            $(this).find('.step-content').each(function () {
                let currentStage = $(this).data('step-id');
                let matchStage = (stageId === '' || currentStage == stageId);
                let stepHas = false;

                $(this).find('.question-item').each(function () {
                    let qName = $(this).find('.question-title').text().toLowerCase();
                    let match = matchCat && matchStage && (name === '' || qName.includes(name));
                    $(this)[match ? 'show' : 'hide']();
                    if (match) { stepHas = true; hasVisible = true; }
                });

                if (name !== '' || stageId !== '') {
                    $(this)[stepHas ? 'show' : 'hide']();
                }
            });

            let show = hasVisible || (matchCat && name === '' && stageId === '');
            $(this)[show ? 'show' : 'hide']();
            if (show && (name !== '' || stageId !== '') && $(this).hasClass('collapsed')) {
                toggleCategory(cardId, 'category-collapse-' + currentCat);
            }
        });
    }
    $('#filterName, #filterCategory, #filterStage').on('input change', applyFilters);

    // ── Move Up/Down ──
    $(document).on('click', '.move-up-btn', function () {
        let $item = $(this).closest('.question-item');
        let $prev = $item.prev('.question-item');
        if ($prev.length) { $item.insertBefore($prev); saveOrder($item.closest('.sortable-list')); }
    });
    $(document).on('click', '.move-down-btn', function () {
        let $item = $(this).closest('.question-item');
        let $next = $item.next('.question-item');
        if ($next.length) { $item.insertAfter($next); saveOrder($item.closest('.sortable-list')); }
    });

    function saveOrder(list) {
        let orderData = [];
        $(list).find('.question-item').each(function (i) {
            $(this).find('.question-number').text(i + 1);
            orderData.push({ id: $(this).data('id'), position: i + 1 });
        });
        $.ajax({
            url: '{{ route("questions.reorder") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', order: orderData },
            success: r => r.success && showToast('<i class="bx bx-check-circle"></i> تم حفظ الترتيب', 'success'),
            error: () => showToast('<i class="bx bx-x-circle"></i> خطأ أثناء الحفظ', 'error')
        });
    }

    // ── Sortable Drag ──
    $('.sortable-list').each(function () {
        new Sortable(this, {
            animation: 150, handle: '.drag-handle',
            ghostClass: 'sortable-ghost', dragClass: 'sortable-drag',
            onEnd: function (evt) { saveOrder(evt.to); }
        });
    });

    // ── Toggle Active ──
    $('.toggle-active-btn').on('change', function () {
        $.ajax({
            url: '{{ route("questions.toggleActive") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', id: $(this).data('id'), is_active: $(this).is(':checked') ? 1 : 0 },
            success: r => r.success && showToast('<i class="bx bx-check-circle"></i> ' + r.message, 'success'),
            error: () => showToast('<i class="bx bx-x-circle"></i> خطأ في التحديث', 'error')
        });
    });

    // ── Duplicate Modal ──
    $(document).on('click', '.open-dup-modal', function () {
        let btn = $(this);
        $('#dupQuestionName').text(btn.data('name') + ' ؟');
        $('#dupForm').attr('action', btn.data('url'));

        // Pre-select flow
        let flow = btn.data('flow') || 'valuation';
        $('input[name="dup_flow"]').each(function () {
            $(this).prop('checked', $(this).val() === flow);
        });
        $('.flow-option').removeClass('selected');
        $('#flow-' + flow).addClass('selected');

        // Pre-select category & step
        $('#dupCategory').val(btn.data('category') || '');
        $('#dupStep').val(btn.data('step') || '');

        $('#dupModalBackdrop').addClass('show');
    });

    // Flow option click
    $('.flow-option').on('click', function () {
        $('.flow-option').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Close modal
    $('#closeDupModal, #cancelDupModal').on('click', function () {
        $('#dupModalBackdrop').removeClass('show');
    });
    $('#dupModalBackdrop').on('click', function (e) {
        if ($(e.target).is('#dupModalBackdrop')) $(this).removeClass('show');
    });

    // On form submit — fill hidden inputs
    $('#dupForm').on('submit', function () {
        $('#dupFlowInput').val($('input[name="dup_flow"]:checked').val());
        $('#dupCategoryInput').val($('#dupCategory').val());
        $('#dupStepInput').val($('#dupStep').val());
    });
});

// ── Toast ──
function showToast(msg, type) {
    let t = $('<div class="custom-toast ' + (type === 'error' ? 'error' : '') + '">' + msg + '</div>');
    $('body').append(t);
    t[0].offsetHeight;
    setTimeout(() => t.addClass('show'), 30);
    setTimeout(() => { t.removeClass('show'); setTimeout(() => t.remove(), 400); }, 3000);
}
</script>
@endsection
