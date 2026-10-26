{{-- ═══════════════ Duplicate Section Modal & Styles ═══════════════ --}}
<style>
/* ── Duplicate Section Modal Backdrop & Box ── */
.dup-section-backdrop {
    position: fixed; inset: 0;
    background: rgba(10, 14, 22, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 1060;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.3s ease;
    padding: 20px;
}
.dup-section-backdrop.show {
    opacity: 1; pointer-events: all;
}
.dup-section-modal {
    background: #ffffff;
    border-radius: 24px;
    width: 620px; max-width: 96vw;
    max-height: 90vh;
    box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.35);
    transform: translateY(30px) scale(0.96);
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    direction: rtl;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(255,255,255,0.2);
}
.dup-section-backdrop.show .dup-section-modal {
    transform: translateY(0) scale(1);
}

/* Header */
.dup-section-header {
    background: linear-gradient(135deg, #1f232b 0%, #2e333d 100%);
    padding: 22px 28px;
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 2px solid rgba(248,180,0,0.2);
}
.dup-section-title-wrap { display: flex; align-items: center; gap: 12px; }
.dup-section-icon {
    width: 44px; height: 44px;
    background: rgba(248,180,0,0.15);
    border: 1px solid rgba(248,180,0,0.3);
    color: #F8B400;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
}
.dup-section-title {
    color: #ffffff;
    font-weight: 800;
    font-size: 1.15rem;
    margin: 0;
}
.dup-section-subtitle {
    color: #F8B400;
    font-size: 0.82rem;
    margin: 2px 0 0;
    font-weight: 600;
}
.dup-section-close {
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,0.08); border: none;
    color: rgba(255,255,255,0.7); font-size: 1.2rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s;
}
.dup-section-close:hover { background: rgba(255,255,255,0.2); color: #fff; transform: rotate(90deg); }

/* Body */
.dup-section-body {
    padding: 24px 28px;
    overflow-y: auto;
    max-height: calc(90vh - 160px);
}
.dup-info-callout {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border-right: 4px solid #F8B400;
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 22px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.dup-info-callout i {
    color: #b45309;
    font-size: 1.4rem;
    flex-shrink: 0;
    margin-top: 2px;
}
.dup-info-callout p {
    margin: 0;
    font-size: 0.88rem;
    color: #78350f;
    line-height: 1.55;
    font-weight: 600;
}

/* Card Boxes */
.dup-box {
    background: #f8f9fc;
    border: 1.5px solid #e8eaf0;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 16px;
    transition: all 0.2s;
}
.dup-box:focus-within {
    border-color: #F8B400;
    box-shadow: 0 4px 18px rgba(248,180,0,0.1);
    background: #fff;
}
.dup-box-title {
    font-size: 0.86rem;
    font-weight: 800;
    color: #1a1d23;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dup-box-badge {
    background: #1a1d23;
    color: #F8B400;
    font-size: 0.72rem;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 700;
}
.dup-box-badge.target {
    background: #10b981;
    color: #ffffff;
}

/* Flow & Form Fields */
.dup-label {
    font-size: 0.82rem; font-weight: 700; color: #4b5563;
    margin-bottom: 6px; display: block;
}
.dup-control-select {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #d1d5db;
    border-radius: 12px;
    font-size: 0.92rem;
    background: #ffffff;
    color: #1a1d23;
    outline: none;
    font-weight: 600;
    transition: all 0.2s;
    font-family: inherit;
}
.dup-control-select:focus {
    border-color: #F8B400;
    box-shadow: 0 0 0 3px rgba(248,180,0,0.15);
}

.dup-transfer-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -6px 0 10px;
    color: #F8B400;
    font-size: 1.5rem;
    position: relative;
}
.dup-transfer-arrow-pill {
    background: #1a1d23;
    color: #F8B400;
    font-size: 0.76rem;
    font-weight: 800;
    padding: 4px 14px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Radio chips */
.dup-chips-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.dup-chip {
    flex: 1;
    min-width: 110px;
    border: 1.5px solid #e8eaf0;
    border-radius: 12px;
    padding: 10px 12px;
    text-align: center;
    cursor: pointer;
    background: #fff;
    transition: all 0.2s;
    user-select: none;
    font-size: 0.85rem;
    font-weight: 700;
    color: #374151;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.dup-chip input[type="radio"] { display: none; }
.dup-chip.selected {
    border-color: #F8B400;
    background: #fffbeb;
    color: #92620a;
    box-shadow: 0 2px 8px rgba(248,180,0,0.15);
}

/* Footer */
.dup-section-footer {
    padding: 16px 28px 24px;
    display: flex; gap: 12px; justify-content: flex-end;
    border-top: 1px solid #f0f2f5;
    background: #ffffff;
}
.btn-dup-sec-cancel {
    padding: 11px 24px;
    border-radius: 12px; border: 1.5px solid #e5e7eb;
    background: #f9fafb; color: #4b5563; font-weight: 700;
    cursor: pointer; transition: all 0.2s; font-size: 0.92rem;
}
.btn-dup-sec-cancel:hover { background: #e5e7eb; color: #111827; }
.btn-dup-sec-confirm {
    padding: 11px 30px;
    border-radius: 12px; border: none;
    background: linear-gradient(135deg, #F8B400 0%, #e6a500 100%);
    color: #1a1d23; font-weight: 800;
    cursor: pointer; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: 0.95rem; display: flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 16px rgba(248,180,0,0.35);
}
.btn-dup-sec-confirm:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(248,180,0,0.45);
}
.btn-dup-sec-confirm:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
</style>

{{-- Modal Backdrop & Dialog --}}
<div class="dup-section-backdrop" id="dupSectionModalBackdrop">
    <div class="dup-section-modal">
        {{-- Modal Header --}}
        <div class="dup-section-header">
            <div class="dup-section-title-wrap">
                <div class="dup-section-icon">
                    <i class="bx bx-duplicate"></i>
                </div>
                <div>
                    <h5 class="dup-section-title">نسخ قسم كامل بجميع أسئلته</h5>
                    <div class="dup-section-subtitle">نسخ تلقائي لكافة الأسئلة والخيارات والخيارات الفرعية (Options & Suboptions)</div>
                </div>
            </div>
            <button type="button" class="dup-section-close" id="closeDupSectionModal">
                <i class="bx bx-x"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="dupSectionForm" action="{{ route('questions.duplicateSection') }}" method="POST">
            @csrf
            <input type="hidden" name="current_flow" value="{{ request('flow') }}">

            <div class="dup-section-body">
                {{-- Info Callout --}}
                <div class="dup-info-callout">
                    <i class="bx bx-info-circle"></i>
                    <p>
                        سيتم نسخ كل سؤال من القسم المصدر، ومعه جميع خياراته الرئيسية وخياراته الفرعية وصوره وترتيبه، ووضعها تحت الفئة التي تختارها دون التأثير على القسم الأصلي.
                    </p>
                </div>

                {{-- 1. Source Category Box --}}
                <div class="dup-box">
                    <div class="dup-box-title">
                        <span class="dup-box-badge">1. المصدر</span>
                        <span>القسم المراد نسخه (Source)</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="dup-label" for="dupSecSourceCat">اختر القسم / الفئة المصدر <span class="text-danger">*</span></label>
                            <select class="dup-control-select" id="dupSecSourceCat" name="source_category_id" required>
                                <option value="" disabled selected>-- اختر القسم المصدر --</option>
                                @foreach($categories as $cat)
                                    @php
                                        $qCount = $cat->questions_count ?? ($cat->questions ? $cat->questions->count() : 0);
                                    @endphp
                                    <option value="{{ $cat->id }}" data-count="{{ $qCount }}">
                                        {{ $cat->name_ar }} ({{ $qCount }} سؤال)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="dup-label" for="dupSecSourceStep">المرحلة المراد نسخها</label>
                            <select class="dup-control-select" id="dupSecSourceStep" name="source_step_id">
                                <option value="all">كل المراحل (كامل القسم)</option>
                                <option value="unstaged">عام (بدون مرحلة فقط)</option>
                                @if(isset($steps) && count($steps))
                                    @foreach($steps as $step)
                                        <option value="{{ $step->id }}">مرحلة: {{ $step->name_ar }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Arrow Divider --}}
                <div class="dup-transfer-arrow">
                    <div class="dup-transfer-arrow-pill">
                        <i class="bx bx-down-arrow-alt"></i>
                        <span>سيتم النسخ ووضع الأسئلة تحت:</span>
                        <i class="bx bx-down-arrow-alt"></i>
                    </div>
                </div>

                {{-- 2. Target Category Box --}}
                <div class="dup-box" style="border-color: #10b981;">
                    <div class="dup-box-title">
                        <span class="dup-box-badge target">2. الوجهة المستهدفة</span>
                        <span>الفئة المستهدفة (Target Category)</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="dup-label" for="dupSecTargetCat">ضع الأسئلة تحت الفئة (Category) <span class="text-danger">*</span></label>
                            <select class="dup-control-select" id="dupSecTargetCat" name="target_category_id" required style="border-color:#10b981;">
                                <option value="" disabled selected>-- اختر الفئة التي تريد وضع الأسئلة تحتها --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">
                                        🎯 {{ $cat->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="dup-label" for="dupSecTargetStep">المرحلة في الفئة الهدف</label>
                            <select class="dup-control-select" id="dupSecTargetStep" name="target_step_id">
                                <option value="keep">نفس مراحل الأسئلة الأصلية (تلقائي)</option>
                                <option value="unstaged">عام (بدون مرحلة)</option>
                                @if(isset($steps) && count($steps))
                                    @foreach($steps as $step)
                                        <option value="{{ $step->id }}">نقل إلى: {{ $step->name_ar }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 3. Additional Settings --}}
                <div class="row g-3 mb-2">
                    {{-- Flow selection --}}
                    <div class="col-md-6">
                        <label class="dup-label">نوع الاستخدام للنسخة (Flow)</label>
                        <div class="dup-chips-group">
                            <label class="dup-chip selected" id="dupSecChip-same">
                                <input type="radio" name="flow" value="same" checked>
                                <span>نفس الأصل</span>
                            </label>
                            <label class="dup-chip" id="dupSecChip-valuation">
                                <input type="radio" name="flow" value="valuation">
                                <span>تثمين</span>
                            </label>
                            <label class="dup-chip" id="dupSecChip-market">
                                <input type="radio" name="flow" value="market">
                                <span>السوق</span>
                            </label>
                            <label class="dup-chip" id="dupSecChip-both">
                                <input type="radio" name="flow" value="both">
                                <span>كلاهما</span>
                            </label>
                        </div>
                    </div>

                    {{-- Naming Mode --}}
                    <div class="col-md-6">
                        <label class="dup-label">عناوين الأسئلة المنسوخة</label>
                        <div class="dup-chips-group">
                            <label class="dup-chip selected" id="dupSecNaming-keep">
                                <input type="radio" name="naming_mode" value="keep" checked>
                                <span>نفس العنوان تماماً</span>
                            </label>
                            <label class="dup-chip" id="dupSecNaming-prefix">
                                <input type="radio" name="naming_mode" value="prefix_copy">
                                <span>إضافة (نسخة)</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="dup-section-footer">
                <button type="button" class="btn-dup-sec-cancel" id="cancelDupSectionModal">إلغاء</button>
                <button type="submit" class="btn-dup-sec-confirm" id="btnSubmitDupSection">
                    <i class="bx bx-duplicate fs-5"></i>
                    <span>تأكيد نسخ القسم بأسئلته</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript Controller for Duplicate Section --}}
<script>
window.openDupSectionModal = function(sourceCatId = null, targetCatId = null) {
    if (sourceCatId) {
        $('#dupSecSourceCat').val(sourceCatId);
    }
    if (targetCatId) {
        $('#dupSecTargetCat').val(targetCatId);
    }
    $('#dupSectionModalBackdrop').addClass('show');
};

$(document).ready(function() {
    // Open modal buttons
    $(document).on('click', '.open-dup-section-modal, #openDupSectionModalBtn, #btnOpenDupSectionModal', function(e) {
        e.preventDefault();
        let src = $(this).data('category-id') || $(this).data('source-id') || null;
        let tgt = $(this).data('target-id') || null;

        // If in create question page, check if category is selected
        if (!tgt && $('select[name="category_id"]').length && $('select[name="category_id"]').val()) {
            tgt = $('select[name="category_id"]').val();
        }

        window.openDupSectionModal(src, tgt);
    });

    // Close modal
    $('#closeDupSectionModal, #cancelDupSectionModal').on('click', function() {
        $('#dupSectionModalBackdrop').removeClass('show');
    });

    $('#dupSectionModalBackdrop').on('click', function(e) {
        if ($(e.target).is('#dupSectionModalBackdrop')) {
            $(this).removeClass('show');
        }
    });

    // Chips selection handlers
    $('.dup-chips-group .dup-chip').on('click', function() {
        $(this).closest('.dup-chips-group').find('.dup-chip').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Form submission validation & loading feedback
    $('#dupSectionForm').on('submit', function(e) {
        let src = $('#dupSecSourceCat').val();
        let tgt = $('#dupSecTargetCat').val();

        if (!src) {
            alert('الرجاء اختيار القسم المصدر المراد نسخه');
            $('#dupSecSourceCat').focus();
            return false;
        }
        if (!tgt) {
            alert('الرجاء اختيار الفئة المستهدفة التي ستوضع الأسئلة تحتها');
            $('#dupSecTargetCat').focus();
            return false;
        }

        if (src == tgt) {
            let confirmed = confirm('لقد اخترت نفس القسم كـ مصدر و وجهة! سيتم تكرار الأسئلة داخل نفس القسم. هل تود الاستمرار؟');
            if (!confirmed) return false;
        }

        let $btn = $('#btnSubmitDupSection');
        $btn.prop('disabled', true);
        $btn.html('<i class="bx bx-loader-alt bx-spin fs-5"></i> جاري نسخ القسم والأسئلة والخيارات...');
    });
});
</script>
