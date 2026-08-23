@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">إعدادات النظام</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عمولة الخبراء</span>
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

            <div class="card">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        عمولة الخبراء
                    </div>
                    <p class="mg-b-20">تحكم في نسبة أرباح الخبير عند إنجاز طلبات التقييم.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ملاحظة: عند حفظ الإعدادات، سيتم إرسال رسالة واتساب تلقائية لجميع الخبراء المسجلين لإبلاغهم بالنسبة الجديدة.
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST">
                        @csrf
                        <div class="row row-sm">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">نوع العمولة</label>
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
                                    <label class="form-label">قيمة العمولة</label>
                                    <input type="number" step="0.01" class="form-control" name="expert_commission_value" 
                                           value="{{ $settings['expert_commission_value'] ?? 10 }}" required>
                                    <small class="text-muted">مثال: إذا اخترت مبلغ ثابت اكتب (10)، وإذا اخترت نسبة اكتب (50).</small>
                                    @error('expert_commission_value')
                                        <span class="text-danger"><br>{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-main-primary pd-x-20" type="submit">حفظ الإعدادات والتنبيه</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
@endsection
