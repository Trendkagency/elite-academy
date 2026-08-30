@extends('layouts.app')

@section('content')
@php
    $title = $article ? $article->title : 'How to Prepare for Final Exams Without Stress';
    $excerpt = $article ? ($article->excerpt ?: 'Final exams don\'t have to trigger burnout or anxiety.') : 'Final exams don\'t have to trigger burnout or anxiety.';
    $content = $article ? $article->content : 'Final exams don\'t have to trigger burnout or anxiety. By breaking revision sessions into structured Pomodoro blocks, prioritizing high-yield topics, and reviewing past exam papers, you can build steady confidence and achieve top scores while maintaining a healthy sleep schedule.';
    $category = $article ? $article->category : 'Study Tips';
    $author = $article?->authorUser?->name ?: 'Dr. Ahmed Hassan';
    $date = $article?->published_at ? $article->published_at->format('M d, Y') : 'Oct 12, 2026';
    $readTime = $article ? ($article->read_time_minutes . ' min read') : '6 min read';
    $image = $article ? $article->featured_image_url : 'images/hero_student.webp';
@endphp

<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.blog'), 'route' => 'blog'],
                ['label' => $category, 'url' => route('blog', ['category' => $category])],
                ['label' => $title],
            ]
        ])

        <div class="space-y-4">
            <span class="inline-block bg-teal-600 text-white text-xs font-mono font-bold uppercase tracking-wider px-3.5 py-1 rounded-full">
                {{ $category }}
            </span>

            <h1 class="font-heading text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                {{ $title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-slate-300 font-bold pt-2">
                <span>By {{ $author }}</span>
                <span>•</span>
                <span>{{ $date }}</span>
                <span>•</span>
                <span class="text-teal-400">{{ $readTime }}</span>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        {{-- Featured Image --}}
        <div class="relative w-full h-64 sm:h-96 lg:h-[480px] rounded-3xl overflow-hidden shadow-2xl bg-slate-950">
            <img src="{{ media_url($image, 'images/course_ai.webp') }}" alt="{{ $title }}" class="w-full h-full object-cover">
        </div>

        {{-- Excerpt Callout --}}
        <div class="p-6 bg-teal-50/80 rounded-2xl border-l-4 border-teal-600 text-slate-800 text-base sm:text-lg font-medium leading-relaxed">
            {{ $excerpt }}
        </div>

        {{-- Body Content --}}
        <div class="prose prose-slate max-w-none text-slate-800 text-base sm:text-lg leading-relaxed space-y-6">
            {!! nl2br(e($content)) !!}
        </div>

        {{-- Share & Navigation Footer --}}
        <div class="pt-8 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('blog') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-all">
                &larr; Back to All Articles
            </a>
            <a href="{{ route('blog', ['category' => $category]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-50 text-teal-700 rounded-xl text-xs font-bold hover:bg-teal-100">
                More in {{ $category }} &rarr;
            </a>
        </div>
    </div>
</section>
@endsection
