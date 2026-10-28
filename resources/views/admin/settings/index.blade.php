@extends('layouts.master')
@section('css')
    <style>
        .badge-active {
            background-color: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .badge-inactive {
            background-color: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
        .sub-option-pill {
            display: inline-block;
            background: #f0f4ff;
            border: 1px solid #cdd9f8;
            color: #1e40af;
            border-radius: 6px;
            padding: 3px 8px;
            margin: 2px;
            font-size: 12px;
            font-weight: 600;
        }
        .option-row {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">إعدادات النظام</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ العمولة وأسباب الاحتجاج</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ session()->get('success') }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- بطاقة عمولة الخبراء -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <i class="fas fa-percentage text-primary ml-2"></i> عمولة الخبراء
                    </div>
                    <p class="mg-b-20 text-muted">تحكم في نسبة أرباح الخبير عند إنجاز طلبات التقييم.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ملاحظة: عند حفظ الإعدادات، سيتم إرسال رسالة واتساب تلقائية لجميع الخبراء المسجلين لإبلاغهم بالنسبة الجديدة.
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row row-sm">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">نوع العمولة</label>
                                    <select name="expert_commission_type" class="form-control" required>
                                        <option value="fixed" {{ ($settings['expert_commission_type'] ?? 'fixed') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت (ريال)</option>
                                        <option value="percentage" {{ ($settings['expert_commission_type'] ?? '') == 'percentage' ? 'selected' : '' }}>نسبة مئوية (%) من قيمة الخدمة</option>
                                    </select>
                                    @error('expert_commission_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">قيمة العمولة</label>
                                    <input type="number" step="0.01" class="form-control" name="expert_commission_value" 
                                           value="{{ $settings['expert_commission_value'] ?? 10 }}" required>
                                    <small class="text-muted">مثال: إذا اخترت مبلغ ثابت اكتب (10)، وإذا اخترت نسبة اكتب (50).</small>
                                    @error('expert_commission_value')
                                        <span class="text-danger"><br>{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12 mt-4">
                                <div class="main-content-label mg-b-5">
                                    الاتفاقية القانونية للتعاون مع الخبير
                                </div>
                                <p class="mg-b-20 text-muted">قم برفع ملف PDF يمثل الاتفاقية التي يجب أن يوافق عليها الخبير عند التسجيل.</p>
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">ملف الاتفاقية (PDF)</label>
                                    <input type="file" name="expert_agreement_pdf" class="form-control" accept="application/pdf">
                                    @error('expert_agreement_pdf')
                                        <span class="text-danger d-block mt-1">{{ $message }}</span>
                                    @enderror
                                    @if(isset($settings['expert_agreement_pdf']) && $settings['expert_agreement_pdf'])
                                        <a href="{{ asset('storage/' . $settings['expert_agreement_pdf']) }}" target="_blank" class="d-block mt-2 text-primary"><i class="fas fa-file-pdf"></i> عرض الملف الحالي (Preview Document)</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-main-primary pd-x-20" type="submit"><i class="fas fa-save ml-1"></i> حفظ الإعدادات والتنبيه</button>
                    </form>
                </div>
            </div>

            <!-- بطاقة أسباب الاحتجاج على التقييم -->
            <div class="card" id="objection-reasons-card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <div class="main-content-label mg-b-5">
                            <i class="fas fa-exclamation-triangle text-warning ml-2"></i> أسباب الاحتجاج على التقييم (Objection Reasons)
                        </div>
                        <p class="mg-b-0 text-muted">
                            إدارة هيكل أسباب الاحتجاج (العنوان الرئيسي، الأنواع، العناوين الفرعية، الأيقونات، والخيارات الفرعية) كما تظهر في تطبيق الموبايل.
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addObjectionReasonModal">
                            <i class="fas fa-plus ml-1"></i> إضافة سبب احتجاج جديد
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- عنوان نافذة الاحتجاج (formTitle) -->
                    <form action="{{ route('admin.objection-reasons.form-title') }}" method="POST" class="mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        @csrf
                        <div class="font-weight-bold text-primary mb-2">
                            <i class="fas fa-heading ml-1"></i> عنوان نافذة الاحتجاج في التطبيق (formTitle)
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <div class="form-group mb-md-0">
                                    <label class="form-label font-weight-semibold">العنوان بالعربية <span class="text-danger">*</span></label>
                                    <input type="text" name="objection_form_title_ar" class="form-control"
                                           value="{{ $settings['objection_form_title_ar'] ?? 'اختر سبب عدم رضاك عن التثمين' }}" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-md-0">
                                    <label class="form-label font-weight-semibold">العنوان بالإنجليزية (English)</label>
                                    <input type="text" name="objection_form_title_en" class="form-control"
                                           value="{{ $settings['objection_form_title_en'] ?? 'Select the reason for your dissatisfaction with the valuation' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-save ml-1"></i> حفظ العنوان
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-md-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">ID</th>
                                    <th style="width: 170px;">النوع (Type)</th>
                                    <th>العنوان والوصف الفرعي</th>
                                    <th>الخيارات الفرعية (Options)</th>
                                    <th style="width: 130px;" class="text-center">الأيقونة (Icon)</th>
                                    <th style="width: 80px;" class="text-center">الترتيب</th>
                                    <th style="width: 110px;" class="text-center">الحالة</th>
                                    <th style="width: 150px;" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($objectionReasons as $index => $reason)
                                    <tr>
                                        <td class="text-center font-weight-bold">
                                            <span class="badge badge-dark">{{ $reason->id }}</span>
                                        </td>
                                        <td>
                                            @if($reason->type === 'cat_with_sub_cat')
                                                <span class="badge badge-primary p-2">cat_with_sub_cat</span>
                                                <small class="d-block text-muted mt-1">تصنيف مع خيارات فرعية</small>
                                            @elseif($reason->type === 'input_modification')
                                                <span class="badge badge-info p-2">input_modification</span>
                                                <small class="d-block text-muted mt-1">تعديل المدخلات</small>
                                            @else
                                                <span class="badge badge-secondary p-2">other_reason</span>
                                                <small class="d-block text-muted mt-1">سبب آخر</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $reason->title_ar }}</div>
                                            @if($reason->subtitle_ar)
                                                <div class="text-muted small">{{ $reason->subtitle_ar }}</div>
                                            @endif
                                            @if($reason->title_en || $reason->subtitle_en)
                                                <div class="text-muted small mt-1" dir="ltr">
                                                    <strong>{{ $reason->title_en }}</strong>
                                                    @if($reason->subtitle_en)
                                                        — {{ $reason->subtitle_en }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reason->type === 'cat_with_sub_cat' && !empty($reason->options))
                                                @foreach($reason->options as $opt)
                                                    <div class="sub-option-pill">
                                                        <span class="text-muted">#{{ $opt['id'] ?? '' }}:</span>
                                                        {{ $opt['title_ar'] ?? $opt['title'] ?? '' }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted small">— لا توجد خيارات فرعية —</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <code>{{ $reason->icon ?: $reason->getDefaultIcon() }}</code>
                                        </td>
                                        <td class="text-center font-weight-bold">{{ $reason->sort_order }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.objection-reasons.toggle', $reason->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @if($reason->is_active)
                                                    <button type="submit" class="btn btn-sm btn-outline-success font-weight-bold" title="انقر للتعطيل">
                                                        <i class="fas fa-check-circle ml-1"></i> نشط
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary font-weight-bold" title="انقر للتفعيل">
                                                        <i class="fas fa-times-circle ml-1"></i> معطل
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info ml-1" data-toggle="modal" data-target="#editModal_{{ $reason->id }}" title="تعديل">
                                                <i class="fas fa-edit"></i> تعديل
                                            </button>
                                            
                                            <form action="{{ route('admin.objection-reasons.destroy', $reason->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا السبب نهائياً؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash-alt"></i> حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-2 d-block text-warning"></i>
                                            لا توجد أسباب احتجاج مسجلة حالياً. اضغط على زر "إضافة سبب احتجاج جديد" لإضافة أسباب.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- row closed -->

    <!-- ======================================================= -->
    <!-- Edit Modals (Placed OUTSIDE the table to avoid DOM bugs) -->
    <!-- ======================================================= -->
    @foreach($objectionReasons as $reason)
        <div class="modal fade" id="editModal_{{ $reason->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.objection-reasons.update', $reason->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-edit ml-1"></i> تعديل سبب الاحتجاج (#{{ $reason->id }})
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">نوع السبب (Type) <span class="text-danger">*</span></label>
                                        <select name="type" class="form-control" onchange="toggleReasonType(this, 'edit_options_box_{{ $reason->id }}', 'edit_icon_{{ $reason->id }}')" required>
                                            <option value="cat_with_sub_cat" {{ $reason->type === 'cat_with_sub_cat' ? 'selected' : '' }}>cat_with_sub_cat (تصنيف مع خيارات فرعية)</option>
                                            <option value="input_modification" {{ $reason->type === 'input_modification' ? 'selected' : '' }}>input_modification (تعديل المدخلات)</option>
                                            <option value="other_reason" {{ (!in_array($reason->type, ['cat_with_sub_cat', 'input_modification'])) ? 'selected' : '' }}>other_reason (سبب آخر)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">الأيقونة (Icon Key)</label>
                                        <input type="text" name="icon" id="edit_icon_{{ $reason->id }}" class="form-control" value="{{ $reason->icon ?: $reason->getDefaultIcon() }}" placeholder="price_estimation / input_modification / other_reason">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">العنوان بالعربية (title) <span class="text-danger">*</span></label>
                                        <input type="text" name="title_ar" class="form-control" value="{{ $reason->title_ar }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">العنوان بالإنجليزية (title_en)</label>
                                        <input type="text" name="title_en" class="form-control" value="{{ $reason->title_en }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">الوصف الفرعي بالعربية (subtitle)</label>
                                        <input type="text" name="subtitle_ar" class="form-control" value="{{ $reason->subtitle_ar }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">الوصف الفرعي بالإنجليزية (subtitle_en)</label>
                                        <input type="text" name="subtitle_en" class="form-control" value="{{ $reason->subtitle_en }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-options container (visible when type == cat_with_sub_cat) -->
                            <div id="edit_options_box_{{ $reason->id }}" class="mt-2 mb-3 p-3 rounded" style="background: #f1f5f9; border: 1px dashed #94a3b8; {{ $reason->type === 'cat_with_sub_cat' ? '' : 'display:none;' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label font-weight-bold mb-0 text-primary">
                                        <i class="fas fa-list-ul ml-1"></i> الخيارات الفرعية (Options)
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOptionRow('edit_options_list_{{ $reason->id }}')">
                                        <i class="fas fa-plus ml-1"></i> إضافة خيار فرعي
                                    </button>
                                </div>
                                <div id="edit_options_list_{{ $reason->id }}">
                                    @php
                                        $existingOptions = is_array($reason->options) ? $reason->options : [];
                                    @endphp
                                    @forelse($existingOptions as $opt)
                                        <div class="option-row">
                                            <input type="hidden" name="option_ids[]" value="{{ $opt['id'] ?? '' }}">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <input type="text" name="option_titles_ar[]" class="form-control form-control-sm" value="{{ $opt['title_ar'] ?? $opt['title'] ?? '' }}" placeholder="النص بالعربية (مثال: السعر أعلى من المتوقع)" required>
                                                </div>
                                                <div class="col-md-5 mt-2 mt-md-0">
                                                    <input type="text" name="option_titles_en[]" class="form-control form-control-sm" value="{{ $opt['title_en'] ?? '' }}" placeholder="English Title (optional)">
                                                </div>
                                                <div class="col-md-2 text-left mt-2 mt-md-0">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOptionRow(this)" title="حذف الخيار">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="option-row">
                                            <input type="hidden" name="option_ids[]" value="">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <input type="text" name="option_titles_ar[]" class="form-control form-control-sm" placeholder="النص بالعربية (مثال: السعر أعلى من المتوقع)">
                                                </div>
                                                <div class="col-md-5 mt-2 mt-md-0">
                                                    <input type="text" name="option_titles_en[]" class="form-control form-control-sm" placeholder="English Title (optional)">
                                                </div>
                                                <div class="col-md-2 text-left mt-2 mt-md-0">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOptionRow(this)" title="حذف الخيار">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label font-weight-bold">الترتيب</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ $reason->sort_order }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="form-check mt-3">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="edit_active_{{ $reason->id }}" {{ $reason->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label font-weight-bold mr-4" for="edit_active_{{ $reason->id }}">
                                            تفعيل السبب (نشط)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save ml-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal إضافة سبب جديد -->
    <div class="modal fade" id="addObjectionReasonModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.objection-reasons.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-plus-circle ml-1"></i> إضافة سبب احتجاج جديد
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">نوع السبب (Type) <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" onchange="toggleReasonType(this, 'new_options_box', 'new_icon')" required>
                                        <option value="cat_with_sub_cat">cat_with_sub_cat (تصنيف مع خيارات فرعية)</option>
                                        <option value="input_modification">input_modification (تعديل المدخلات)</option>
                                        <option value="other_reason" selected>other_reason (سبب آخر)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">الأيقونة (Icon Key)</label>
                                    <input type="text" name="icon" id="new_icon" class="form-control" value="other_reason" placeholder="price_estimation / input_modification / other_reason">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">العنوان بالعربية (title) <span class="text-danger">*</span></label>
                                    <input type="text" name="title_ar" class="form-control" placeholder="مثال: تقدير السعر" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">العنوان بالإنجليزية (title_en)</label>
                                    <input type="text" name="title_en" class="form-control" placeholder="e.g. Price Estimation">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">الوصف الفرعي بالعربية (subtitle)</label>
                                    <input type="text" name="subtitle_ar" class="form-control" placeholder="مثال: السعر أعلى أو أقل من المتوقع">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">الوصف الفرعي بالإنجليزية (subtitle_en)</label>
                                    <input type="text" name="subtitle_en" class="form-control" placeholder="e.g. Price is higher or lower than expected">
                                </div>
                            </div>
                        </div>

                        <!-- Sub-options container -->
                        <div id="new_options_box" class="mt-2 mb-3 p-3 rounded" style="background: #f1f5f9; border: 1px dashed #94a3b8; display:none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label font-weight-bold mb-0 text-primary">
                                    <i class="fas fa-list-ul ml-1"></i> الخيارات الفرعية (Options)
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOptionRow('new_options_list')">
                                    <i class="fas fa-plus ml-1"></i> إضافة خيار فرعي
                                </button>
                            </div>
                            <div id="new_options_list">
                                <div class="option-row">
                                    <input type="hidden" name="option_ids[]" value="">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <input type="text" name="option_titles_ar[]" class="form-control form-control-sm" placeholder="النص بالعربية (مثال: السعر أعلى من المتوقع)">
                                        </div>
                                        <div class="col-md-5 mt-2 mt-md-0">
                                            <input type="text" name="option_titles_en[]" class="form-control form-control-sm" placeholder="English Title (optional)">
                                        </div>
                                        <div class="col-md-2 text-left mt-2 mt-md-0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOptionRow(this)" title="حذف الخيار">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">الترتيب</label>
                                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                                    <small class="text-muted">الأصغر يظهر أولاً</small>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mt-2">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="new_active" checked>
                                    <label class="form-check-label font-weight-bold mr-4" for="new_active">
                                        تفعيل السبب (نشط)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check ml-1"></i> إضافة السبب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    function toggleReasonType(selectElem, boxId, iconId) {
        const type = selectElem.value;
        const box = document.getElementById(boxId);
        const icon = document.getElementById(iconId);
        const defaultIcons = {
            'cat_with_sub_cat': 'price_estimation',
            'input_modification': 'input_modification',
            'other_reason': 'other_reason'
        };

        if (box) {
            if (type === 'cat_with_sub_cat') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }

        if (icon && defaultIcons[type]) {
            icon.value = defaultIcons[type];
        }
    }

    function addOptionRow(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = `
            <input type="hidden" name="option_ids[]" value="">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <input type="text" name="option_titles_ar[]" class="form-control form-control-sm" placeholder="النص بالعربية" required>
                </div>
                <div class="col-md-5 mt-2 mt-md-0">
                    <input type="text" name="option_titles_en[]" class="form-control form-control-sm" placeholder="English Title (optional)">
                </div>
                <div class="col-md-2 text-left mt-2 mt-md-0">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOptionRow(this)" title="حذف الخيار">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    function removeOptionRow(btn) {
        const row = btn.closest('.option-row');
        if (row) {
            row.remove();
        }
    }
</script>
@endsection
