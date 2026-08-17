@extends('layouts.app')

@section('content')
<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => 'Programming', 'route' => 'subject-details'],
                ['label' => 'Full-Stack Systems'],
            ]
        ])

        <div class="space-y-3 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full">Programming & AI</span>
                <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">▶ Free Demo Available</span>
            </div>

            <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                Full-Stack Systems & Deep Learning Architecture
            </h1>

            <p class="text-slate-300 text-base leading-relaxed">
                Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.
            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4 text-xs font-medium text-slate-300">
                <span>⏱️ Duration: 16 Weeks</span>
                <span>👥 Enrolled: 420 Students</span>
                <span>⭐ Rating: 4.9/5 (180 Reviews)</span>
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
                            Watch Sample Lesson 1.1: Building Async Microservices with Python
                        </h2>
                        <p class="text-slate-300 text-xs leading-relaxed">
                            Get a glimpse of our hands-on teaching style before committing. This 12-minute demo covers asynchronous task queues and FastAPI endpoint setup.
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

                {{-- Course Modules --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Curriculum Syllabus Modules</h2>
                    <div class="space-y-3">
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

                        <details class="group bg-[#FAFAF9] rounded-xl p-4 border border-slate-200/80">
                            <summary class="flex justify-between items-center font-bold text-sm text-slate-900 cursor-pointer">
                                <span>Module 3: Microservice Architecture & REST APIs</span>
                                <span class="text-teal-600 font-mono font-bold">+</span>
                            </summary>
                            <p class="mt-3 text-xs text-slate-600">Building scalable API gateways, message queues with RabbitMQ, and Docker containers.</p>
                        </details>
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

                    <a href="{{ route('login') }}" class="w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md transition-all">
                        Enroll in Fall 2026 Cohort
                    </a>

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Instructors</p>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/instructor_portrait.png') }}" alt="Dr. Elena Rostova" class="w-10 h-10 rounded-xl object-cover border border-teal-500">
                            <div>
                                <a href="{{ route('teacher-profile') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600">Dr. Elena Rostova</a>
                                <p class="text-[11px] text-slate-500">Programming Chair</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/instructor_male.png') }}" alt="Marcus Vance" class="w-10 h-10 rounded-xl object-cover border border-purple-500">
                            <div>
                                <a href="{{ route('teacher-profile') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600">Marcus Vance</a>
                                <p class="text-[11px] text-slate-500">AI Research Lead</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
