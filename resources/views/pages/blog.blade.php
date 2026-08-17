@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12">

        {{-- Section Header --}}
        @include('components.section-header', [
            'badge' => 'ACADEMIC INSIGHTS & BLOG',
            'title' => 'Latest Articles & <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Educational News</span>',
            'subtitle' => 'Expert advice, study tips, and academic insights from Elite Academy faculty.',
            'centered' => true,
        ])

        {{-- Articles Feed --}}
        @php
            $articles = [
                [
                    'image' => 'images/hero_student.png',
                    'category' => 'Study Tips',
                    'categoryColor' => 'bg-teal-600',
                    'title' => 'How to Prepare for Final Exams Without Stress',
                    'excerpt' => 'Final exams don\'t have to trigger burnout or anxiety. By breaking revision sessions into structured Pomodoro blocks, prioritizing high-yield topics, and reviewing past exam papers, you can build steady confidence and achieve top scores while maintaining a healthy sleep schedule.',
                    'author' => 'Dr. Ahmed Hassan',
                    'date' => 'Oct 12, 2026',
                    'readTime' => '6 min read',
                ],
                [
                    'image' => 'images/course_ai.png',
                    'category' => 'Programming',
                    'categoryColor' => 'bg-cyan-600',
                    'title' => 'Top 10 Python Projects Every Student Should Build',
                    'excerpt' => 'Building real-world projects is the fastest way to master software engineering concepts. From automated web scrapers and interactive quiz bots to data analysis dashboards, these 10 beginner-friendly Python applications will sharpen your computational thinking and boost your academic portfolio.',
                    'author' => 'Eng. Kareem Zaki',
                    'date' => 'Oct 05, 2026',
                    'readTime' => '8 min read',
                ],
            ];
        @endphp

        <div class="space-y-8 md:space-y-12">
            @foreach ($articles as $a)
                @include('components.article-card', $a)
                @if (! $loop->last)
                    <hr class="border-t border-slate-200/80">
                @endif
            @endforeach
        </div>

    </div>
</section>
@endsection
