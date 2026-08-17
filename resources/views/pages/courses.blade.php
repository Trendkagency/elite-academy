@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.section-header', [
            'badge' => 'Curriculum Catalog • Free Demos Included',
            'badgeColor' => 'orange',
            'title' => 'Featured Learning Courses',
            'subtitle' => 'Filter through our accredited programs. Every course includes a free sample demo lesson before enrollment.',
            'centered' => true,
        ])

        {{-- Category Filter Chips --}}
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
            @include('components.filter-chip', ['label' => 'All Disciplines', 'active' => true])
            @foreach (['Programming', 'AI & Science', 'Mathematics', 'Languages', 'Design'] as $cat)
                @include('components.filter-chip', ['label' => $cat])
            @endforeach
        </div>
    </div>
</section>

{{-- Courses Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $courses = [
                [
                    'image' => 'images/course_ai.png',
                    'category' => 'Programming',
                    'categoryBg' => 'bg-teal-600',
                    'instructor' => 'Dr. Elena Rostova',
                    'instructorPhoto' => 'images/instructor_portrait.png',
                    'instructorBorder' => 'border-teal-500',
                    'title' => 'Full-Stack Systems & Python Architecture',
                    'description' => 'Build microservices, database APIs, and React interfaces with industry experts.',
                    'price' => '$290',
                ],
                [
                    'image' => 'images/academy_campus.png',
                    'category' => 'AI & Science',
                    'categoryBg' => 'bg-purple-600',
                    'instructor' => 'Marcus Vance',
                    'instructorPhoto' => 'images/instructor_male.png',
                    'instructorBorder' => 'border-purple-500',
                    'title' => 'Deep Neural Networks & Machine Vision',
                    'description' => 'Train PyTorch convolutional networks, transformer pipelines, and LLM fine-tuning.',
                    'price' => '$340',
                ],
                [
                    'image' => 'images/hero_student.png',
                    'category' => 'Mathematics',
                    'categoryBg' => 'bg-blue-600',
                    'instructor' => 'Dr. Ahmed Hassan',
                    'instructorPhoto' => 'images/instructor_portrait.png',
                    'instructorBorder' => 'border-blue-500',
                    'title' => 'Advanced Secondary Mathematics & Calculus',
                    'description' => 'Master Algebra, Analytical Geometry, and Calculus prepared for Egyptian Ministry exams.',
                    'price' => '$260',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($courses as $c)
                @include('components.course-card', $c)
            @endforeach
        </div>
    </div>
</section>
@endsection
