@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12">

        {{-- Section Header --}}
        @include('components.section-header', [
            'badge' => 'CAMPUS EVENTS & WORKSHOPS',
            'title' => 'Upcoming <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Academic Events</span>',
            'subtitle' => 'Join interactive live workshops, STEM competitions, and faculty revision sessions.',
            'centered' => true,
        ])

        {{-- Events Feed --}}
        @php
            $events = [
                [
                    'image' => 'images/hero_student.webp',
                    'category' => 'Revision Workshop',
                    'categoryColor' => 'bg-teal-600',
                    'title' => 'Grade 10 Mathematics Live Final Exam Revision',
                    'excerpt' => 'Join Dr. Ahmed Hassan for an intensive 3-hour live revision workshop covering Algebra, Trigonometry, and key past exam questions to prepare for final term examinations.',
                    'author' => 'Dr. Ahmed Hassan',
                    'date' => 'Nov 15, 2026',
                    'readTime' => 'Live at 5:00 PM',
                    'route' => route('event-details'),
                ],
                [
                    'image' => 'images/course_ai.webp',
                    'category' => 'STEM Competition',
                    'categoryColor' => 'bg-orange-600',
                    'title' => 'Annual Robotics & AI Student Hackathon 2026',
                    'excerpt' => 'Showcase your engineering skills at our New Cairo STEM campus. Teams will build autonomous robots and AI classification models with prizes sponsored by top tech partners.',
                    'author' => 'Omar Khaled',
                    'date' => 'Dec 01, 2026',
                    'readTime' => 'Full Day Event',
                    'route' => route('event-details'),
                ],
            ];
        @endphp

        <div class="space-y-8 md:space-y-12">
            @foreach ($events as $e)
                @include('components.article-card', $e)
                @if (! $loop->last)
                    <hr class="border-t border-slate-200/80">
                @endif
            @endforeach
        </div>

    </div>
</section>
@endsection
