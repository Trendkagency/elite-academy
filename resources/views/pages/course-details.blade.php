@extends('layouts.app')

@section('content')
@php
    $cTitle = $course ? $course->title : 'Full-Stack Systems & Deep Learning Architecture';
    $cDesc = $course ? ($course->description ?: 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.') : 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.';
    $cTeacher = $course?->teacher?->user?->name ?: 'Dr. Elena Rostova';
    $cSubject = $course?->subject?->name ?: 'Programming & AI';
    $cId = $course ? $course->id : 1;
@endphp

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
                <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">▶ Free Demo Available</span>
            </div>

            <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                {{ $cTitle }}
            </h1>

            <p class="text-slate-300 text-base leading-relaxed">
                {{ $cDesc }}
            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4 text-xs font-medium text-slate-300">
                <span>⏱️ Duration: 16 Weeks</span>
                <span>👥 Instructor: {{ $cTeacher }}</span>
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
                <div id="demo" class="bg-gradient-to-br from-teal-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 border border-teal-500/40 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-teal-500/30 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-orange-400">Interactive Preview</span>
                        </div>
                        <span class="text-xs font-mono bg-teal-800/80 text-teal-200 px-3 py-1 rounded-full border border-teal-500/30">Free Demo Lesson</span>
                    </div>

                    <div class="space-y-3">
                        <h2 class="font-heading font-extrabold text-2xl text-white">
                            Watch Sample Lesson 1.1: Building Async Microservices
                        </h2>
                        <p class="text-slate-300 text-xs leading-relaxed">
                            Get a glimpse of our hands-on teaching style before committing. This sample demo covers task queues and API setup.
                        </p>
                    </div>

                    <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-950 border border-teal-500/30 group cursor-pointer shadow-2xl">
                        <img src="{{ asset('images/course_ai.png') }}" alt="Demo Video Thumbnail" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-2xl shadow-xl group-hover:scale-110 transition-transform duration-300">
                                ▶
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Modules & Sessions --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Curriculum Sessions & Homework</h2>
                    <div class="space-y-3">
                        @if($course && $course->sessions->count() > 0)
                            @foreach($course->sessions as $idx => $s)
                                <details class="group bg-[#FAFAF9] rounded-xl p-4 border border-slate-200/80" @if($idx === 0) open @endif>
                                    <summary class="flex justify-between items-center font-bold text-sm text-slate-900 cursor-pointer">
                                        <span>Session {{ $s->sort_order }}: {{ $s->title }}</span>
                                        <span class="text-teal-600 font-mono font-bold">+</span>
                                    </summary>
                                    <p class="mt-3 text-xs text-slate-600">{{ $s->description ?: 'Interactive lecture, hands-on coding exercises, and graded homework.' }}</p>
                                </details>
                            @endforeach
                        @else
                            <details class="group bg-[#FAFAF9] rounded-xl p-4 border border-slate-200/80" open>
                                <summary class="flex justify-between items-center font-bold text-sm text-slate-900 cursor-pointer">
                                    <span>Module 1: Advanced Async Python & FastAPI Microservices</span>
                                    <span class="text-teal-600 font-mono font-bold">+</span>
                                </summary>
                                <p class="mt-3 text-xs text-slate-600">Asynchronous I/O, Pydantic schemas, dependency injection, and PyTest integration.</p>
                            </details>
                            <details class="group bg-[#FAFAF9] rounded-xl p-4 border border-slate-200/80">
                                <summary class="flex justify-between items-center font-bold text-sm text-slate-900 cursor-pointer">
                                    <span>Module 2: Relational Databases & PostgreSQL Indexing</span>
                                    <span class="text-teal-600 font-mono font-bold">+</span>
                                </summary>
                                <p class="mt-3 text-xs text-slate-600">Schema design, query optimization, ACID transactions, and Redis caching layers.</p>
                            </details>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Enrollment Card Sidebar --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg sticky top-24 space-y-6">
                    <div class="space-y-2">
                        <span class="text-xs font-mono uppercase tracking-wider text-slate-400">Enrollment Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">$290 <span class="text-xs text-slate-400 font-normal">/ term</span></p>
                    </div>

                    <div id="enrollAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

                    @auth
                        <button id="btnEnroll" class="w-full text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md transition-all cursor-pointer">
                            Enroll in Fall Cohort
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                            Log In to Enroll
                        </a>
                    @endauth

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Instructor</p>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/instructor_portrait.png') }}" alt="{{ $cTeacher }}" class="w-10 h-10 rounded-xl object-cover border border-teal-500">
                            <div>
                                <a href="{{ route('teacher-profile') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600">{{ $cTeacher }}</a>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnEnroll');
    const alertBox = document.getElementById('enrollAlert');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = 'Enrolling...';
        
        try {
            const res = await fetch("{{ route('ajax.course.enroll', $cId) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            alertBox.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            alertBox.textContent = data.message;
            alertBox.classList.remove('hidden');

            if (data.success) {
                btn.textContent = 'Already Enrolled ✓';
                btn.className = 'w-full text-center py-3.5 px-6 font-semibold text-slate-700 bg-slate-100 rounded-xl border border-slate-200';
            } else {
                btn.disabled = false;
                btn.textContent = 'Enroll in Fall Cohort';
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = 'Enroll in Fall Cohort';
            alertBox.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            alertBox.textContent = 'Network error. Please try again.';
            alertBox.classList.remove('hidden');
        }
    });
});
</script>
@endauth
@endsection
