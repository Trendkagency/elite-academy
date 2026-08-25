@extends('layouts.app')

@section('content')
@php
    $heroBadge = $aboutSettings['hero_badge'] ?? 'ACCREDITED EXCELLENCE • EST. 2020';
    $heroTitle = $aboutSettings['hero_title'] ?? 'Transforming Academic Education For Future Leaders';
    $heroSubtitle = $aboutSettings['hero_subtitle'] ?? 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.';
    $missionTitle = $aboutSettings['mission_title'] ?? 'Our Core Educational Mission';
    $missionText = $aboutSettings['mission_text'] ?? 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.';
    $visionTitle = $aboutSettings['vision_title'] ?? 'Our Vision For Tomorrow';
    $visionText = $aboutSettings['vision_text'] ?? 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.';
    $statStudents = $aboutSettings['stat_students'] ?? '25,000+';
    $statCourses = $aboutSettings['stat_courses'] ?? '120+';
    $statTeachers = $aboutSettings['stat_teachers'] ?? '45+';
    $statPassRate = $aboutSettings['stat_pass_rate'] ?? '98.5%';
@endphp

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('About Elite Academy')],
            ]
        ])

        {{-- Storytelling Editorial Hero Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ __($heroBadge) }}
                </span>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                    {!! __($heroTitle) !!}
                </h1>

                <p class="text-slate-600 text-base sm:text-lg font-medium leading-relaxed">
                    {{ __($heroSubtitle) }}
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-center">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block">{{ $statStudents }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">{{ __('Active Students') }}</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-orange-600 block">{{ $statCourses }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">{{ __('Accredited Courses') }}</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block">{{ $statTeachers }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">{{ __('Expert Faculty') }}</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-emerald-600 block">{{ $statPassRate }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">{{ __('Exam Pass Rate') }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="{{ asset('images/academy_campus.png') }}" alt="Elite Academy Campus" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-2xl">
                    <p class="font-heading font-black text-xl text-teal-400">ACCREDITED ACADEMY</p>
                    <p class="text-xs font-mono text-slate-300">Secondary Education Excellence</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ __('OUR MISSION') }}
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">{{ __($missionTitle) }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ __($missionText) }}
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-orange-600 bg-orange-50 px-3.5 py-1.5 rounded-full border border-orange-200/80">
                    {{ __('OUR VISION') }}
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">{{ __($visionTitle) }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ __($visionText) }}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Cornerstone Long-Form Academic Guide (1,500+ Words for AI Search Synthesis & GEO Indexing) --}}
<section class="py-16 md:py-24 bg-white border-t border-slate-200/80">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-slate-800 leading-relaxed font-sans">
        <article class="prose prose-slate max-w-none space-y-8">
            <header class="space-y-4 border-b border-slate-200 pb-8">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                    <span>📖</span> Comprehensive Academic & Platform Guide
                </span>
                <h2 class="font-heading font-black text-3xl sm:text-4xl text-slate-900 tracking-tight">
                    The Complete Architecture of Modern Secondary Tutoring & E-Learning in Egypt
                </h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    An in-depth whitepaper on Elite Academy’s pedagogical framework, encrypted stream security, automated grading engines, and real-time parental integration.
                </p>
            </header>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">1. Thanawya Amma & Secondary Curriculum Alignment</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Elite Academy is engineered specifically to address the rigorous academic requirements of Egypt’s Thanawya Amma national secondary exams and language school curricula. Our courses cover critical academic tracks including Advanced Physics, Artificial Intelligence & Computer Science, Organic & Analytical Chemistry, Pure Mathematics (Calculus, Algebra, Dynamics), and Business Administration. Each curriculum is constructed by senior Egyptian educators with over 15 years of national exam preparation experience.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Instead of relying solely on passive video lectures, Elite Academy utilizes a dual-engine learning system: real-time interactive live sessions paired with step-by-step homework assignments that mirror official ministry exam formats. This ensures students build deep conceptual understanding alongside timed problem-solving endurance.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">2. Live Stream Streaming Security & Anti-Piracy Architecture</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Protecting faculty intellectual property is central to our platform design. All live sessions hosted via Zoom, Google Meet, or BigBlueButton are rendered inside embedded, token-protected stream frames. We enforce dynamic client-side watermarking that overlays the student’s name, unique student ID (e.g. STU-00142), current timestamp, and IP address across the video stream in real-time.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Additionally, our anti-piracy shield monitors window visibility and screen recording events. If screen capture software or unauthorized casting is detected, video playback is immediately obfuscated and an automated security event audit log is transmitted to administrators.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">3. Interactive Assignment Solver & Offline-Resilient Grading</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Homework submission is powered by an interactive assignment solver that breaks complex problem sets into structured steps. As students answer single-choice, multiple-choice, or numeric questions, their progress is auto-saved locally in browser storage. This offline-resilient caching guarantees that network fluctuations or power outages never result in lost work.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Upon submission, our automated grading engine evaluates responses instantly, calculating overall scores, percentage metrics, and itemized performance breakdowns. For incorrect submissions, students gain instant access to detailed video solution walkthroughs recorded by their instructor.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">4. Real-Time Parent Portal & Attendance Tracking</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Parental involvement is directly linked to higher student pass rates. The Elite Academy Parent Portal allows guardians to link student accounts using verified phone numbers. Parents receive real-time visibility into attendance logs recorded by our minute-by-minute meeting heartbeat engine.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    When a student joins a live session, attends a review lab, or misses an assignment deadline, instant Firebase Cloud Messaging (FCM) push alerts and optional WhatsApp notifications are dispatched to parents. This transparent feedback loop ensures accountability throughout the semester.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">5. Faculty Accreditation & Institutional Certification</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Every instructor on Elite Academy undergoes rigorous verification of academic credentials and teaching credentials. Upon successful completion of a course track and final capstone evaluation, students earn an officially verified digital certificate of achievement featuring a unique QR code and serial number for institutional validation.
                </p>
            </section>
        </article>
    </div>
</section>
@endsection
