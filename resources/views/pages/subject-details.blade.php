@extends('layouts.app')

@section('content')
@php
    $name = $subject ? $subject->getLocalizedName() : __('Subject Details');
    $description = $subject ? ($subject->getLocalizedDescription() ?: __('Comprehensive curriculum covering core topics prepared for national curriculum standards.')) : __('Comprehensive curriculum covering core topics prepared for national curriculum standards.');
    $categoryName = $subject?->category ? $subject->category->getLocalizedName() : __('General Curriculum');

    $coursesCount = isset($activeCoursesCount) ? $activeCoursesCount : ($subject?->getActiveCoursesCount() ?? 0);
    $lessonsCount = isset($videoLessonsCount) ? $videoLessonsCount : ($subject?->getVideoLessonsCount() ?? 0);
    $studentsCount = isset($activeStudentsCount) ? $activeStudentsCount : ($subject?->getActiveStudentsCount() ?? 0);
    $rating = isset($ratingAvg) ? $ratingAvg : ($subject?->getRatingAvg() ?? 4.9);

    $image = $subject ? ($subject->image ?: 'images/course_ai.webp') : 'images/course_ai.webp';
@endphp

{{-- Hero Cover & Stats --}}
<section class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden">
    <img src="{{ media_url($image, 'images/course_ai.webp') }}" alt="{{ $name }} Cover" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/80 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('Home'), 'route' => 'home'],
                ['label' => __('Subjects'), 'route' => 'subjects'],
                ['label' => $name],
            ]
        ])

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-mono font-extrabold text-white bg-teal-600 px-3.5 py-1.5 rounded-full shadow-md">
                    {{ $categoryName }}
                </span>
                <span class="text-xs font-mono font-bold text-teal-300 bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                    {{ __('Term 1 & Term 2') }}
                </span>
            </div>

            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight">
                {{ $name }}
            </h1>
            <p class="text-slate-300 text-base sm:text-xl font-medium leading-relaxed">
                {{ $description }}
            </p>
        </div>

        {{-- Statistics Banner Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 max-w-4xl text-center">
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">{{ number_format($lessonsCount) }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Video Lessons') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-orange-400">{{ number_format($coursesCount) }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Active Courses') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">{{ $studentsCount > 0 ? '+' . number_format($studentsCount) : '0' }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Active Students') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-amber-400">{{ number_format($rating, 1) }} ★</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Student Rating') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- About & Syllabus Units --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('About the Curriculum') }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ $description }}
                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Courses in') }} {{ $name }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if($subject && $subject->courses && $subject->courses->count() > 0)
                        @foreach($subject->courses as $course)
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-mono font-bold text-teal-600 uppercase bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                                            {{ $course->gradeLevel?->name ?: __('General') }}
                                        </span>
                                        @php
                                            $courseSessionsNum = $course->sessions ? $course->sessions->count() : ($course->sessions_count ?: 0);
                                        @endphp
                                        @if($courseSessionsNum > 0)
                                            <span class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                                                {{ $courseSessionsNum }} {{ __('Lessons') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="font-heading font-extrabold text-xl text-slate-900 leading-snug">{{ __($course->title) }}</h3>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">{{ __($course->description ?: 'Interactive curriculum with hands-on labs.') }}</p>
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        @if($course->teacher?->photo)
                                            <img src="{{ media_url($course->teacher->photo) }}" class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200" alt="{{ $course->teacher->user?->name }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                                                {{ substr($course->teacher?->user?->name ?: 'F', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">{{ $course->teacher?->user?->name ?: __('Faculty Advisor') }}</p>
                                            <p class="text-[10px] text-slate-500 font-mono">{{ $course->teacher?->title ?: __('Senior Instructor') }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('course-details', ['slug' => $course->slug]) }}" class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-teal-600/20">
                                        {{ __('View Details') }} &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-2 bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-4">
                            <p class="text-base font-semibold text-slate-700">{{ __('No individual courses listed yet for this subject.') }}</p>
                            <a href="{{ route('courses') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md">
                                {{ __('Browse Courses') }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Enroll in') }} {{ $name }}</h3>
                <p class="text-xs font-mono text-slate-500">{{ __('Access all video lectures, PDF revision books, and live mentor Q&A cohorts.') }}</p>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Full Term 1 & 2 Access') }}
                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Direct Mentor Q&A Sessions') }}
                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Ministry Exam Revision Sheets') }}
                    </div>
                    @if($coursesCount > 0)
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> {{ $coursesCount }} {{ __('Active Accredited Courses') }}
                        </div>
                    @endif
                    @if($lessonsCount > 0)
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> {{ $lessonsCount }} {{ __('Video Lessons & Labs') }}
                        </div>
                    @endif
                </div>

                <a href="{{ route('courses') }}" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    {{ __('Explore Courses') }} &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
