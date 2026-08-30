@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('content')
<section class="py-12 md:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.section-header', [
            'badge' => $isArabic ? 'دليل الكورسات والمناهج • الحصة الأولى مجانية' : 'Curriculum Catalog • Free Demos Included',
            'badgeColor' => 'orange',
            'title' => $isArabic ? 'الكورسات التعليمية والمقررات المعتمدة' : 'Featured Learning Courses',
            'subtitle' => $isArabic ? 'تصفح البرامج الدراسية المعتمدة. كل كورس يتضمن حصة تجريبية مجانية قبل الاشتراك.' : 'Filter through our accredited programs. Every course includes a free sample demo lesson before enrollment.',
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
                {{ $isArabic ? 'جميع التخصصات' : 'All Disciplines' }}
            </a>
            @php
                $categories = [
                    'Programming' => $isArabic ? 'البرمجة' : 'Programming',
                    'AI & Science' => $isArabic ? 'العلوم والتكنولوجيا' : 'AI & Science',
                    'Mathematics' => $isArabic ? 'الرياضيات' : 'Mathematics',
                    'Languages' => $isArabic ? 'اللغات' : 'Languages',
                    'Design' => $isArabic ? 'التصميم' : 'Design',
                    'Physics' => $isArabic ? 'الفيزياء' : 'Physics',
                ];
            @endphp
            @foreach ($categories as $catKey => $catLabel)
                @php $isActive = strtolower($selectedCategory ?? '') === strtolower($catKey); @endphp
                <a href="{{ route('courses', ['category' => $catKey]) }}"
                   @class([
                       'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white shadow-md border-teal-600' => $isActive,
                       'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' => ! $isActive,
                   ])>
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Courses Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if(isset($courses) && count($courses) > 0)
                @foreach ($courses as $c)
                    @php
                        $isModel = $c instanceof \App\Models\Course;
                        $slug = $isModel ? $c->slug : 'course-details';
                        $courseId = $isModel ? $c->id : null;
                        $cardData = [
                            'image' => $isModel ? ($c->image ?: 'images/course_ai.webp') : ($c['image'] ?? 'images/course_ai.webp'),
                            'category' => $isModel ? ($c->subject?->name ?: ($isArabic ? 'العلوم' : 'Science')) : ($c['category'] ?? ($isArabic ? 'العلوم' : 'Science')),
                            'categoryBg' => 'bg-teal-600',
                            'instructor' => $isModel ? ($c->teacher?->user?->name ?: ($isArabic ? 'أستاذ المادة' : 'Dr. Teacher')) : ($c['instructor'] ?? ($isArabic ? 'أستاذ المادة' : 'Dr. Teacher')),
                            'instructorPhoto' => 'images/instructor_portrait.webp',
                            'instructorBorder' => 'border-teal-500',
                            'title' => $isModel ? $c->title : ($c['title'] ?? 'Course Title'),
                            'description' => $isModel ? ($c->description ?: ($isArabic ? 'مقرر تعليمي تفاعلي شامل للمرحلة الثانوية.' : 'Interactive curriculum with hands-on labs.')) : ($c['description'] ?? 'Course description'),
                            'price' => '$290',
                            'route' => route('course-details', ['slug' => $slug]),
                            'course_id' => $courseId,
                            'hasFreeDemo' => $isModel ? (bool) $c->has_free_demo : true,
                            'isEnrolled' => $courseId ? in_array((int) $courseId, array_map('intval', $enrolledCourseIds ?? [])) : false,
                        ];
                    @endphp
                    @include('components.course-card', $cardData)
                @endforeach
            @else
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📚</div>
                    <h3 class="font-bold text-lg text-slate-800">{{ $isArabic ? 'لا توجد كورسات في هذا التخصص حالياً' : 'No Courses Match Selected Category' }}</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">{{ $isArabic ? 'جرب اختيار "جميع التخصصات" لمشاهدة كافة المناهج المتاحة.' : 'Try selecting "All Disciplines" or another category.' }}</p>
                    <a href="{{ route('courses') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
                        {{ $isArabic ? 'عرض جميع الكورسات' : 'View All Courses' }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Custom Clean Responsive Pagination Controls --}}
        @if(method_exists($courses, 'hasPages') && $courses->hasPages())
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80">
                <div class="text-xs font-bold text-slate-500">
                    {{ $isArabic ? 'عرض' : 'Showing' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->firstItem() }}</span>
                    {{ $isArabic ? 'إلى' : 'to' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->lastItem() }}</span>
                    {{ $isArabic ? 'من إجمالي' : 'of' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->total() }}</span>
                    {{ $isArabic ? 'كورسات' : 'courses' }}
                </div>

                <div class="flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($courses->onFirstPage())
                        <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 cursor-not-allowed">
                            ◀ {{ $isArabic ? 'السابق' : 'Previous' }}
                        </span>
                    @else
                        <a href="{{ $courses->previousPageUrl() }}" class="btn-lift px-4 py-2 text-xs font-extrabold text-teal-700 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-xl border border-teal-200/80 transition-all shadow-xs">
                            ◀ {{ $isArabic ? 'السابق' : 'Previous' }}
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="flex items-center gap-1.5 px-2">
                        @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                            @if ($page == $courses->currentPage())
                                <span class="w-9 h-9 rounded-xl bg-teal-600 text-white font-black text-xs flex items-center justify-center shadow-md shadow-teal-600/30">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 flex items-center justify-center transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($courses->hasMorePages())
                        <a href="{{ $courses->nextPageUrl() }}" class="btn-lift px-4 py-2 text-xs font-extrabold text-teal-700 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-xl border border-teal-200/80 transition-all shadow-xs">
                            {{ $isArabic ? 'التالي' : 'Next' }} ▶
                        </a>
                    @else
                        <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 cursor-not-allowed">
                            {{ $isArabic ? 'التالي' : 'Next' }} ▶
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
