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
            <a href="{{ route('courses') }}"
               @class([
                   'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                   'bg-teal-600 text-white shadow-md border-teal-600' => empty($selectedCategory),
                   'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' => ! empty($selectedCategory),
               ])>
                All Disciplines
            </a>
            @foreach (['Programming', 'AI & Science', 'Mathematics', 'Languages', 'Design'] as $cat)
                @php $isActive = strtolower($selectedCategory ?? '') === strtolower($cat); @endphp
                <a href="{{ route('courses', ['category' => $cat]) }}"
                   @class([
                       'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white shadow-md border-teal-600' => $isActive,
                       'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' => ! $isActive,
                   ])>
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Courses Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if(isset($courses) && count($courses) > 0)
                @foreach ($courses as $c)
                    @php
                        $isModel = $c instanceof \App\Models\Course;
                        $slug = $isModel ? $c->slug : 'course-details';
                        $cardData = [
                            'image' => $isModel ? ($c->image ?: 'images/course_ai.png') : ($c['image'] ?? 'images/course_ai.png'),
                            'category' => $isModel ? ($c->subject?->name ?: 'Science') : ($c['category'] ?? 'Science'),
                            'categoryBg' => 'bg-teal-600',
                            'instructor' => $isModel ? ($c->teacher?->user?->name ?: 'Dr. Instructor') : ($c['instructor'] ?? 'Dr. Instructor'),
                            'instructorPhoto' => 'images/instructor_portrait.png',
                            'instructorBorder' => 'border-teal-500',
                            'title' => $isModel ? $c->title : ($c['title'] ?? 'Course Title'),
                            'description' => $isModel ? ($c->description ?: 'Interactive curriculum with hands-on labs.') : ($c['description'] ?? 'Course description'),
                            'price' => '$290',
                            'route' => route('course-details', ['slug' => $slug]),
                            'course_id' => $isModel ? $c->id : null,
                        ];
                    @endphp
                    @include('components.course-card', $cardData)
                @endforeach
            @else
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📚</div>
                    <h3 class="font-bold text-lg text-slate-800">No Courses Match Selected Category</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Try selecting "All Disciplines" or another category.</p>
                    <a href="{{ route('courses') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
                        View All Courses
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
