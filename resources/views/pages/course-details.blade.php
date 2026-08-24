@extends('layouts.app')

@section('content')
@php
    $cTitle = $course ? $course->title : 'Full-Stack Systems & Deep Learning Architecture';
    $cDesc = $course ? ($course->description ?: 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.') : 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.';
    $cTeacher = $course?->teacher?->user?->name ?: 'Dr. Elena Rostova';
    $cSubject = $course?->subject?->name ?: 'Programming & AI';
    $cId = $course ? $course->id : 1;
@endphp

@php
    $courseJsonLd = [
        "@context" => "https://schema.org",
        "@type" => "Course",
        "name" => $cTitle,
        "description" => $cDesc,
        "provider" => [
            "@type" => "EducationalOrganization",
            "name" => "Elite Academy LMS",
            "sameAs" => url('/')
        ],
        "instructor" => [
            "@type" => "Person",
            "name" => $cTeacher
        ],
        "educationalLevel" => "Intermediate to Advanced",
        "inLanguage" => app()->getLocale(),
        "offers" => [
            "@type" => "Offer",
            "category" => "Educational",
            "priceCurrency" => "EGP",
            "price" => (string) ($course?->price ?? '0'),
            "availability" => "https://schema.org/InStock"
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($courseJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => $cSubject],
                ['label' => $cTitle],
            ]
        ])

        <div class="space-y-3 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ $cSubject }}</span>
                @if($isEnrolled ?? false)
                    <span class="bg-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full">✓ Enrolled Course</span>
                @else
                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">▶ Free Demo Available</span>
                @endif
            </div>

            <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                {{ $cTitle }}
            </h1>

            <p class="text-slate-300 text-base leading-relaxed">
                {{ $cDesc }}
            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4 text-xs font-medium text-slate-300">
                <span>⏱️ Duration: 16 Weeks</span>
                <span>👥 Teacher: {{ $cTeacher }}</span>
                <span>⭐ Rating: 4.9/5</span>
                <span>🏆 Accredited Certification</span>
            </div>
        </div>
    </div>
</section>

{{-- Course Content Body Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8 space-y-12">
                {{-- Free Demo Section --}}
                @php
                    $isArabicTitle = preg_match('/\p{Arabic}/u', $cTitle);
                    $demoTitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'الحصة الأولى التجريبية: ' . $cTitle 
                        : 'Watch Sample Lesson 1.1: ' . $cTitle;
                    $demoSubtitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'شاهد الحصة المجانية الأولى واستكشف أسلوب الشرح التفاعلي والتطبيقات العملية قبل الاشتراك.'
                        : 'Get a glimpse of our hands-on teaching style before committing. This sample demo covers core concepts and interactive exercises.';
                    $videoData = $course ? $course->getVideoEmbedData() : ['type' => 'mp4', 'embed_url' => asset('videos/physics_demo.mp4')];
                    $posterImage = $course && $course->image ? media_url($course->image, 'images/course_ai.png') : asset('images/course_ai.png');
                @endphp
                <div id="demo" class="bg-gradient-to-br from-teal-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 border border-teal-500/40 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-teal-500/30 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-orange-400">{{ __('Interactive Preview') }}</span>
                        </div>
                        <span class="text-xs font-mono bg-teal-800/80 text-teal-200 px-3 py-1 rounded-full border border-teal-500/30">{{ __('Free Demo Lesson') }}</span>
                    </div>

                    <div class="space-y-3">
                        <h2 class="font-heading font-extrabold text-2xl text-white">
                            {{ $demoTitle }}
                        </h2>
                        <p class="text-slate-300 text-xs leading-relaxed">
                            {{ $demoSubtitle }}
                        </p>
                    </div>

                    <x-secure-video-player :course="$course" :videoData="$videoData" :posterImage="$posterImage" :title="$cTitle" />
                </div>

                {{-- Interactive Curriculum Lifetime Timeline Component --}}
                @include('components.curriculum-timeline', [
                    'sessions' => $course?->sessions,
                    'title' => 'Course Curriculum & Module Lifetime Roadmap',
                    'subtitle' => 'Structured timeline of lectures, live coding labs, and homework assignments'
                ])
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-4 space-y-6">
                @php
                    $nextLiveSession = $course?->liveSessions?->where('scheduled_at', '>=', now())->sortBy('scheduled_at')->first();
                    $targetDate = $nextLiveSession ? $nextLiveSession->scheduled_at->toIso8601String() : now()->addDays(3)->setTime(18, 0)->toIso8601String();
                    $sessionTitle = $nextLiveSession ? $nextLiveSession->title : null;
                @endphp

                {{-- Live Countdown Timer Widget Component --}}
                @include('components.course-countdown-timer', [
                    'targetDate' => $targetDate,
                    'sessionTitle' => $sessionTitle,
                    'title' => app()->getLocale() === 'ar' ? 'عداد البث المباشر القادم' : 'Live Cohort Start Timer',
                    'subtitle' => app()->getLocale() === 'ar' ? 'الوقت المتبقي لإنطلاق حصة البث المباشر التفاعلية' : 'Countdown to upcoming interactive live stream'
                ])

                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="space-y-2 border-b border-slate-100 pb-4">
                        <span class="text-xs font-mono font-extrabold text-slate-400 uppercase">Tuition Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">$290 <span class="text-xs text-slate-400 font-normal">/ term</span></p>
                    </div>

                    <div id="enrollAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

                    @auth
                        @if($isEnrolled ?? false)
                            <a href="{{ route('student-portal') }}" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                                {{ app()->getLocale() === 'ar' ? 'مشترك في هذا الكورس ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Course ✓ — Go to Student Portal &rarr;' }}
                            </a>
                        @else
                            <button id="btnEnroll" class="w-full text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md transition-all cursor-pointer">
                                {{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                            {{ app()->getLocale() === 'ar' ? 'سجل الدخول للتسجيل في الكورس' : 'Log In to Enroll' }}
                        </a>
                    @endauth

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Teacher</p>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/instructor_portrait.png') }}" alt="{{ $cTeacher }}" class="w-10 h-10 rounded-xl object-cover border border-teal-500">
                            <div>
                                <a href="{{ route('teachers') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600">{{ $cTeacher }}</a>
                                <p class="text-[11px] text-slate-500">Senior Academic Lead</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@auth
@if(! ($isEnrolled ?? false))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnEnroll');
    const alertBox = document.getElementById('enrollAlert');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = "{{ app()->getLocale() === 'ar' ? 'جاري التسجيل...' : 'Enrolling...' }}";

        try {
            const res = await fetch("{{ route('ajax.course.enroll', $cId) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (window.Toast) {
                if (data.success) {
                    window.Toast.success(data.message || 'Enrolled in course successfully!');
                } else {
                    window.Toast.error(data.message || 'Enrollment failed.');
                }
            }

            alertBox.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            alertBox.textContent = data.message;
            alertBox.classList.remove('hidden');

            if (data.success) {
                const portalUrl = "{{ route('student-portal') }}";
                const linkText = "{{ app()->getLocale() === 'ar' ? 'تم التسجيل بنجاح ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Successfully ✓ — Go to Student Portal &rarr;' }}";
                btn.outerHTML = `<a href="${portalUrl}" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">${linkText}</a>`;
            } else {
                btn.disabled = false;
                btn.textContent = "{{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}";
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = "{{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}";
            alertBox.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            alertBox.textContent = 'Network error. Please try again.';
            alertBox.classList.remove('hidden');
        }
    });
});
</script>
@endif
@endauth
@endsection
