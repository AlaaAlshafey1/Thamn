@extends('layouts.public')

@section('title', $title)

@section('content')
    <h2 class="section-title text-center">{{ $title }}</h2>

    @forelse($terms as $term)
        <div class="term-item mb-5 p-3 border-bottom">
            <h4 class="fw-bold mb-3" style="color: #444;">{{ app()->getLocale() == 'ar' ? $term->title_ar : $term->title_en }}
            </h4>
            <div class="term-content mb-3">
                {!! app()->getLocale() == 'ar' ? $term->content_ar : $term->content_en !!}
            </div>
            @if($term->file)
                <div class="mt-3">
                    <a href="{{ asset($term->file) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="bx bx-file"></i> {{ app()->getLocale() == 'ar' ? 'عرض الملف المرفق' : 'View Attachment' }}
                    </a>
                </div>
            @endif
        </div>
    @empty
        <div class="alert alert-info text-center">
            {{ app()->getLocale() == 'ar' ? 'لا توجد شروط وأحكام مضافة بعد.' : 'No terms and conditions added yet.' }}
        </div>
    @endforelse
@endsection