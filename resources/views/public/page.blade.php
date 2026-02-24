@extends('layouts.public')

@section('title', $title)

@section('content')
    <h2 class="section-title text-center">{{ $title }}</h2>

    @if($page)
        <div class="content">
            @php
                $content = app()->getLocale() == 'ar' ? $page->content_ar : $page->content_en;
            @endphp
            {!! $content !!}
        </div>
    @else
        <div class="alert alert-info text-center">
            {{ app()->getLocale() == 'ar' ? 'المحتوى غير متوفر حالياً.' : 'Content is not available yet.' }}
        </div>
    @endif
@endsection