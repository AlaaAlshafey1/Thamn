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
                                    <select name="expert_commission_type" class="form-control select2" required>
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
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="main-content-label mg-b-5">
                            <i class="fas fa-exclamation-triangle text-warning ml-2"></i> أسباب الاحتجاج على التقييم (Objection Reasons)
                        </div>
                        <p class="mg-b-0 text-muted">
                            هذه الأسباب الجاهزة تظهر للمستخدمين في التطبيق لاختيار سبب الاحتجاج وطلب إعادة التثمين، ويمكنك إدارتها وتعديلها في أي وقت.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addObjectionReasonModal">
                            <i class="fas fa-plus ml-1"></i> إضافة سبب احتجاج جديد
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-md-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>السبب بالعربية</th>
                                    <th>السبب بالإنجليزية (اختياري)</th>
                                    <th style="width: 100px;" class="text-center">الترتيب</th>
                                    <th style="width: 120px;" class="text-center">الحالة</th>
                                    <th style="width: 160px;" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($objectionReasons as $index => $reason)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                        <td class="font-weight-semibold text-dark">{{ $reason->title_ar }}</td>
                                        <td class="text-muted">{{ $reason->title_en ?: '-' }}</td>
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

                                    <!-- Edit Modal for each reason -->
                                    <div class="modal fade" id="editModal_{{ $reason->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.objection-reasons.update', $reason->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title font-weight-bold">
                                                            <i class="fas fa-edit ml-1"></i> تعديل سبب الاحتجاج
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="form-label font-weight-bold">نص السبب بالعربية <span class="text-danger">*</span></label>
                                                            <textarea name="title_ar" class="form-control" rows="3" required>{{ $reason->title_ar }}</textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="form-label font-weight-bold">نص السبب بالإنجليزية (English)</label>
                                                            <textarea name="title_en" class="form-control" rows="3">{{ $reason->title_en }}</textarea>
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
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
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

    <!-- Modal إضافة سبب جديد -->
    <div class="modal fade" id="addObjectionReasonModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <div class="form-group">
                            <label class="form-label font-weight-bold">نص السبب بالعربية <span class="text-danger">*</span></label>
                            <textarea name="title_ar" class="form-control" rows="3" placeholder="مثال: السعر المقدر أقل بكثير من القيمة السوقية..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold">نص السبب بالإنجليزية (English)</label>
                            <textarea name="title_en" class="form-control" rows="3" placeholder="e.g. The estimated price is much lower than current market value..."></textarea>
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
@endsection
