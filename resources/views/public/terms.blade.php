@extends('layouts.public')

@section('title', $title)

@section('content')
    <h2 class="section-title text-center">{{ $title }}</h2>

    @if($terms)
        <div class="term-content mb-3">
            {!! app()->getLocale() == 'ar' ? $terms->content_ar : $terms->content_en !!}
        </div>
    @else
        <div class="alert alert-info text-center">
            {{ app()->getLocale() == 'ar' ? 'المحتوى غير متوفر حالياً.' : 'Content is not available yet.' }}
        </div>
    @endif
@endsection