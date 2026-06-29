@extends('layouts.master')
@section('title', 'إدارة الأسئلة')

@section('css')
<style>
body {
    background-color: #f8f9fa;
}
.page-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px dashed #e2e8f0;
}
.btn-add {
    background-color: #31363F;
    color: #F8B400;
    border: none;
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-add:hover {
    background-color: #1a1d21;
    color: #F8B400;
    transform: translateY(-2px);
}

/* Category Card */
.category-card {
    background-color: transparent;
    margin-bottom: 30px;
}
.category-header {
    background-color: #2b2e33;
    border-radius: 24px;
    padding: 20px 30px 10px 30px;
    position: relative;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    transition: all 0.3s;
    user-select: none;
}
.category-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #9ca3af;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
}

.category-title-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.category-title {
    color: #F8B400;
    font-size: 1.35rem;
    font-weight: 700;
    margin: 0;
}

.circle-indicator {
    width: 22px;
    height: 22px;
    background-color: #F8B400;
    border-radius: 50%;
    display: inline-block;
}



/* Steps Tabs */
.category-steps {
    display: flex;
    overflow-x: auto;
    gap: 20px;
    margin-top: 25px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    scrollbar-width: none; /* Firefox */
}
.category-steps::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
}
.step-tab {
    color: rgba(255,255,255,0.4);
    font-weight: 600;
    font-size: 0.95rem;
    padding: 0 10px 15px 10px;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}
.step-tab.active {
    color: #F8B400;
    border-bottom-color: #F8B400;
}
.step-tab:hover:not(.active) {
    color: #d1d5db;
}

.category-toggle-container {
    text-align: center;
    padding-top: 5px;
    cursor: pointer;
}

.category-toggle {
    color: #fff;
    font-size: 1.8rem;
    transition: transform 0.3s ease;
}

.category-card.collapsed .category-toggle {
    transform: rotate(180deg);
}

/* Questions Body */
.category-body {
    padding: 20px 0;
}

.sortable-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.question-item {
    background-color: #FFF8F3;
    border-radius: 16px;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.2s;
    cursor: grab;
    border: 1px solid transparent;
}
.question-item:hover {
    background-color: #fff;
    box-shadow: 0 5px 20px rgba(0,0,0,0.04);
    border-color: #e2e8f0;
}
.question-item:active {
    cursor: grabbing;
}

.question-item.sortable-ghost {
    opacity: 0.5;
    background-color: #f1f5f9;
    border: 2px dashed #F8B400;
}
.question-item.sortable-drag {
    background-color: #fff !important;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}

.question-number {
    width: 38px;
    height: 38px;
    background-color: #000;
    color: #fff;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 800;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.question-text-content {
    flex-grow: 1;
}

.question-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.question-subtitle {
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0;
}

.question-actions-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.action-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #6b7280;
    font-size: 1.2rem;
    border: 1px solid #e5e7eb;
    transition: all 0.2s;
    text-decoration: none;
}
.action-icon:hover {
    background-color: #f3f4f6;
    color: #111827;
}

.action-edit:hover { color: #F8B400; border-color: #F8B400; background-color: #fffbeb; }
.action-view:hover { color: #3b82f6; border-color: #3b82f6; background-color: #eff6ff; }
.action-delete:hover { color: #ef4444; border-color: #ef4444; background-color: #fef2f2; }

/* Custom Toast */
.custom-toast {
    position: fixed;
    bottom: 30px;
    left: 30px;
    background: #31363F;
    color: #fff;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    z-index: 9999;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
    border-right: 4px solid #F8B400;
}
.custom-toast.show {
    transform: translateY(0);
    opacity: 1;
}
.custom-toast.error {
    border-right-color: #ef4444;
}
</style>
@endsection

@section('content')
<div class="container-fluid" style="direction: rtl;">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-2">إدارة الأسئلة والمراحل</h3>
            <p class="text-muted mb-0">اضغط على الفئة لعرض المراحل، ثم اختر المرحلة لترتيب أسئلتها بالسحب والإفلات.</p>
        </div>
        <a href="{{ route('questions.create', ['flow' => request('flow')]) }}" class="btn-add">
            <i class="bx bx-plus fs-5 me-2"></i> إضافة سؤال جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4" role="alert">
            <i class="bx bx-check-circle me-2 fs-5 align-middle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="questions-container">
        <!-- Filters Section -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: #fff;">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">بحث باسم السؤال</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bx bx-search text-muted"></i></span>
                            <input type="text" id="filterName" class="form-control border-0 bg-light" placeholder="اكتب اسم السؤال للبحث...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">تصفية بالفئة</label>
                        <select id="filterCategory" class="form-select border-0 bg-light">
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">تصفية بالمرحلة</label>
                        <select id="filterStage" class="form-select border-0 bg-light">
                            <option value="">جميع المراحل</option>
                            @foreach($steps as $st)
                                <option value="{{ $st->id }}">{{ $st->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @php
            $totalCategories = count($categories);
        @endphp

        @foreach ($categories as $index => $category)
            @php
                $percentage = $totalCategories > 0 ? round((($index + 1) / $totalCategories) * 100) : 0;
                $categoryStepIds = $category->questions->pluck('stageing')->unique()->toArray();
                $categorySteps = $steps->filter(function($step) use ($categoryStepIds) {
                    return in_array($step->id, $categoryStepIds);
                })->values();
            @endphp
            <div class="category-card {{ $index == 0 ? '' : 'collapsed' }}" id="category-card-{{ $category->id }}">
                <div class="category-header">
                    <div class="category-header-top" onclick="toggleCategory('category-card-{{ $category->id }}', 'category-collapse-{{ $category->id }}')">

                        <div class="category-title-container">
                            <h4 class="category-title">{{ $category->name_ar }}</h4>
                            <span class="circle-indicator"></span>
                        </div>
                    </div>
                    
                    <div id="category-collapse-{{ $category->id }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                        <!-- Horizontal Steps Tabs -->
                        @if($categorySteps->count() > 0)
                            <div class="category-steps">
                                @foreach($categorySteps as $stepIndex => $step)
                                    <div class="step-tab {{ $stepIndex == 0 ? 'active' : '' }}" onclick="switchStep({{ $category->id }}, {{ $step->id }}, this)">
                                        {{ $step->name_ar }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="category-toggle-container" onclick="toggleCategory('category-card-{{ $category->id }}', 'category-collapse-{{ $category->id }}')">
                        <i class="bx bx-chevron-up category-toggle"></i>
                    </div>
                </div>
                
                <div class="category-body" id="category-body-{{ $category->id }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                    @if($categorySteps->count() > 0)
                        @foreach($categorySteps as $stepIndex => $step)
                            @php 
                                $stepQuestions = $category->questions->where('stageing', $step->id); 
                            @endphp
                            <div class="step-content step-content-{{ $category->id }}" id="step-content-{{ $category->id }}-{{ $step->id }}" data-step-id="{{ $step->id }}" style="display: {{ $stepIndex == 0 ? 'block' : 'none' }};">
                                @if($stepQuestions->count() > 0)
                                    <ul class="sortable-list" data-category-id="{{ $category->id }}" data-step-id="{{ $step->id }}">
                                        @foreach ($stepQuestions as $key => $question)
                                        <li class="question-item" data-id="{{ $question->id }}">
                                            
                                            <div class="question-number">{{ $loop->iteration }}</div>
                                            
                                            <div class="question-text-content">
                                                <h5 class="question-title">
                                                    {{ $question->question_ar }} ؟
                                                    <i class="bx bx-help-circle text-warning fs-5"></i>
                                                </h5>
                                                @if($question->question_en)
                                                    <p class="question-subtitle">{{ $question->question_en }}</p>
                                                @endif
                                            </div>
                                            
                                            <div class="question-actions-group">
                                                <div class="form-check form-switch me-3" style="display: flex; align-items: center;" title="تفعيل/تعطيل">
                                                    <input class="form-check-input toggle-active-btn m-0" type="checkbox" role="switch" data-id="{{ $question->id }}" {{ $question->is_active ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                                </div>
                                                <a href="{{ route('questions.edit', [$question->id, 'flow' => request('flow')]) }}" class="action-icon action-edit" title="تعديل">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                <a href="{{ route('questions.show', [$question->id, 'flow' => request('flow')]) }}" class="action-icon action-view" title="عرض">
                                                    <i class="bx bx-show-alt"></i>
                                                </a>
                                                <form action="{{ route('questions.destroy', [$question->id, 'flow' => request('flow')]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-icon action-delete border-0" title="حذف">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            
                                        </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4 bg-white rounded-4 border border-dashed">
                                        <i class="bx bx-list-minus fs-1 text-muted opacity-50 mb-2"></i>
                                        <h6 class="text-muted">لا توجد أسئلة مضافة في هذه المرحلة بعد</h6>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 bg-white rounded-4 border border-dashed mx-4">
                            <i class="bx bx-list-minus fs-1 text-muted opacity-50 mb-2"></i>
                            <h6 class="text-muted">لا توجد أسئلة في هذه الفئة بعد. قم بإضافة أسئلة لتظهر هنا.</h6>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Uncategorized Items --}}
        @if(isset($uncategorizedQuestions) && $uncategorizedQuestions->count() > 0)
            <div class="category-card collapsed mt-5" id="category-card-uncategorized">
                <div class="category-header" style="background-color: #4b5563;">
                    <div class="category-header-top" onclick="toggleCategory('category-card-uncategorized', 'category-collapse-uncategorized')">

                        <div class="category-title-container">
                            <h4 class="category-title text-white">أسئلة غير مصنفة</h4>
                            <span class="circle-indicator bg-secondary"></span>
                        </div>
                    </div>
                    
                    <div id="category-collapse-uncategorized" style="display: none;">
                        <!-- No steps for uncategorized -->
                    </div>

                    <div class="category-toggle-container" onclick="toggleCategory('category-card-uncategorized', 'category-collapse-uncategorized')">
                        <i class="bx bx-chevron-up category-toggle text-white"></i>
                    </div>
                </div>
                
                <div class="category-body" id="category-body-uncategorized" style="display: none;">
                    <ul class="sortable-list" data-category-id="0">
                        @foreach ($uncategorizedQuestions as $key => $question)
                        <li class="question-item" data-id="{{ $question->id }}">
                            
                            <div class="question-number bg-secondary">{{ $key + 1 }}</div>
                            
                            <div class="question-text-content">
                                <h5 class="question-title">
                                    {{ $question->question_ar }} ؟
                                    <i class="bx bx-help-circle text-muted fs-5"></i>
                                </h5>
                                @if($question->question_en)
                                    <p class="question-subtitle">{{ $question->question_en }}</p>
                                @endif
                            </div>
                            
                            <div class="question-actions-group">
                                <div class="form-check form-switch me-3" style="display: flex; align-items: center;" title="تفعيل/تعطيل">
                                    <input class="form-check-input toggle-active-btn m-0" type="checkbox" role="switch" data-id="{{ $question->id }}" {{ $question->is_active ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                </div>
                                <a href="{{ route('questions.edit', [$question->id, 'flow' => request('flow')]) }}" class="action-icon action-edit" title="تعديل">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="{{ route('questions.destroy', [$question->id, 'flow' => request('flow')]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon action-delete border-0" title="حذف">
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
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function toggleCategory(cardId, collapseId) {
    let card = $('#' + cardId);
    let collapse = $('#' + collapseId);
    
    // We also need to toggle the body depending on the cardId
    let bodyId = cardId.replace('category-card-', 'category-body-');
    let body = $('#' + bodyId);

    if (card.hasClass('collapsed')) {
        card.removeClass('collapsed');
        collapse.slideDown(300);
        body.slideDown(300);
    } else {
        card.addClass('collapsed');
        collapse.slideUp(300);
        body.slideUp(300);
    }
}

function switchStep(categoryId, stepId, tabElement) {
    // Remove active class from all tabs in this category
    $(tabElement).siblings().removeClass('active');
    // Add active class to clicked tab
    $(tabElement).addClass('active');
    
    // Hide all step contents in this category
    $('.step-content-' + categoryId).hide();
    
    // Show the selected step content
    $('#step-content-' + categoryId + '-' + stepId).fadeIn(200);
}

$(document).ready(function() {
    // Search & Filter Logic
    function applyFilters() {
        let name = $('#filterName').val().toLowerCase();
        let catId = $('#filterCategory').val();
        let stageId = $('#filterStage').val();

        $('.category-card').each(function() {
            let cardIdAttr = $(this).attr('id');
            if (!cardIdAttr) return; // Skip uncategorized for now
            let currentCat = cardIdAttr.replace('category-card-', '');
            
            let matchCat = (catId === '' || currentCat == catId);
            let hasVisibleQuestions = false;

            $(this).find('.step-content').each(function() {
                let currentStage = $(this).data('step-id');
                let matchStage = (stageId === '' || currentStage == stageId);
                
                let stepHasVisible = false;

                $(this).find('.question-item').each(function() {
                    let qName = $(this).find('.question-title').text().toLowerCase();
                    let matchName = (name === '' || qName.includes(name));
                    
                    if (matchCat && matchStage && matchName) {
                        $(this).show();
                        stepHasVisible = true;
                        hasVisibleQuestions = true;
                    } else {
                        $(this).hide();
                    }
                });

                // If filtering by name or stage, we might want to expand the matching steps
                if (name !== '' || stageId !== '') {
                    if (stepHasVisible) {
                        $(this).show();
                        // Also activate corresponding tab visually
                        $(this).closest('.category-card').find('.step-tab').each(function(){
                            if($(this).text().trim() === $('#filterStage option:selected').text().trim() || stageId === ''){
                                // Just a visual enhancement, but leaving display block is enough
                            }
                        });
                    } else {
                        $(this).hide();
                    }
                } else {
                    // Reset to normal tabs logic when no search
                    if($(this).index() !== 0 && !$(this).closest('.category-card').find('.step-tab').eq($(this).index()).hasClass('active')) {
                        $(this).hide();
                    }
                }
            });

            if (hasVisibleQuestions || (matchCat && name === '' && stageId === '')) {
                $(this).show();
                if ((name !== '' || stageId !== '') && $(this).hasClass('collapsed')) {
                    // Auto-expand card if it matches search
                    toggleCategory(cardIdAttr, 'category-collapse-' + currentCat);
                }
            } else {
                $(this).hide();
            }
        });
    }

    $('#filterName, #filterCategory, #filterStage').on('input change', function() {
        applyFilters();
    });

    $('.sortable-list').each(function() {
        new Sortable(this, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function (evt) {
                let list = evt.to;
                let items = $(list).find('.question-item');
                let orderData = [];
                
                items.each(function(index) {
                    let id = $(this).data('id');
                    let position = index + 1;
                    
                    // Update visual number instantly
                    $(this).find('.question-number').text(position);
                    
                    orderData.push({ id: id, position: position });
                });

                // AJAX Request
                $.ajax({
                    url: '{{ route("questions.reorder") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: orderData
                    },
                    success: function(response) {
                        if(response.success) {
                            showToast('<i class="bx bx-check-circle fs-4 text-success"></i> ' + response.message, 'success');
                        }
                    },
                    error: function() {
                        showToast('<i class="bx bx-x-circle fs-4 text-danger"></i> حدث خطأ أثناء حفظ الترتيب', 'error');
                    }
                });
            }
        });
    });

    $('.toggle-active-btn').on('change', function() {
        let id = $(this).data('id');
        let isActive = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '{{ route("questions.toggleActive") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                is_active: isActive
            },
            success: function(response) {
                if(response.success) {
                    showToast('<i class="bx bx-check-circle fs-4 text-success"></i> ' + response.message, 'success');
                }
            },
            error: function() {
                showToast('<i class="bx bx-x-circle fs-4 text-danger"></i> حدث خطأ أثناء تحديث الحالة', 'error');
            }
        });
    });
});

function showToast(message, type) {
    let toast = $('<div class="custom-toast ' + (type === 'error' ? 'error' : '') + '">' + message + '</div>');
    $('body').append(toast);
    
    toast[0].offsetHeight; // Trigger reflow
    
    setTimeout(() => { toast.addClass('show'); }, 50);
    setTimeout(() => { 
        toast.removeClass('show'); 
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
</script>
@endsection
