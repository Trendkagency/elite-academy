@extends('layouts.app')

@section('content')
{{-- Hero Cover & Stats --}}
<section class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden">
    <img src="{{ asset('images/course_ai.png') }}" alt="Mathematics Cover" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/80 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => 'Mathematics'],
            ]
        ])

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono font-extrabold text-white bg-teal-600 px-3.5 py-1.5 rounded-full shadow-md">
                    Secondary 1 • Grade 10
                </span>
                <span class="text-xs font-mono font-bold text-teal-300 bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                    Term 1 & Term 2
                </span>
            </div>

            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight">
                Secondary Mathematics
            </h1>
            <p class="text-slate-300 text-base sm:text-xl font-medium leading-relaxed">
                Comprehensive curriculum covering Algebra, Trigonometry, Analytical Geometry, and Calculus prepared for Egyptian Ministry exams.
            </p>
        </div>

        {{-- Statistics Banner Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 max-w-4xl text-center">
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">48</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Video Lessons</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-orange-400">12</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Expert Teachers</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">3,400+</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Enrolled Students</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-amber-400">4.9 ★</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Student Rating</p>
            </div>
        </div>
    </div>
</section>

{{-- About & Syllabus Units --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">About the Curriculum</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Grade 10 Mathematics lays the foundation for advanced STEM education. Students master quadratic equations, matrices, trigonometric identities, and coordinate geometry through step-by-step video tutorials, downloadable revision sheets, and weekly live quizzes.
                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Syllabus Units & Lessons</h2>
                <div class="space-y-4">
                    <details class="group bg-white rounded-2xl p-6 border border-slate-200/90 shadow-md card-lift" open>
                        <summary class="flex justify-between items-center font-heading font-extrabold text-lg text-slate-900 cursor-pointer">
                            <span>Unit 1: Algebra & Matrix Equations (14 Lessons)</span>
                            <span class="text-teal-600 font-bold text-xl transition-transform group-open:rotate-45">+</span>
                        </summary>
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3 text-sm text-slate-600">
                            <div class="flex justify-between items-center py-1">
                                <span>Lesson 1: Complex Numbers & Quadratic Equations</span>
                                <span class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">45 Mins</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span>Lesson 2: Matrix Operations & Determinants</span>
                                <span class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">50 Mins</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span>Lesson 3: Linear Programming & Inequalities</span>
                                <span class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">40 Mins</span>
                            </div>
                        </div>
                    </details>

                    <details class="group bg-white rounded-2xl p-6 border border-slate-200/90 shadow-md card-lift">
                        <summary class="flex justify-between items-center font-heading font-extrabold text-lg text-slate-900 cursor-pointer">
                            <span>Unit 2: Trigonometry & Analytical Geometry (16 Lessons)</span>
                            <span class="text-teal-600 font-bold text-xl transition-transform group-open:rotate-45">+</span>
                        </summary>
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-3 text-sm text-slate-600">
                            <div class="flex justify-between items-center py-1">
                                <span>Lesson 1: Trigonometric Identities & Angle Measures</span>
                                <span class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">55 Mins</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span>Lesson 2: Straight Lines & Vector Equations</span>
                                <span class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full">48 Mins</span>
                            </div>
                        </div>
                    </details>
                </div>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Featured Mathematics Mentors</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg flex items-center gap-4 card-lift">
                        <img src="{{ asset('images/instructor_portrait.png') }}" alt="Dr. Ahmed Hassan" class="w-18 h-18 rounded-2xl object-cover">
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Dr. Ahmed Hassan</h3>
                            <p class="text-xs font-mono text-slate-500">15+ Yrs Exp • PhD MIT</p>
                            <a href="{{ route('teacher-profile') }}" class="text-xs font-extrabold text-teal-600 hover:text-teal-700 mt-2 inline-block">View Profile &rarr;</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg flex items-center gap-4 card-lift">
                        <img src="{{ asset('images/instructor_female.png') }}" alt="Dr. Nour Ibrahim" class="w-18 h-18 rounded-2xl object-cover">
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Dr. Nour Ibrahim</h3>
                            <p class="text-xs font-mono text-slate-500">16+ Yrs Exp • PhD ETH Zurich</p>
                            <a href="{{ route('teacher-profile') }}" class="text-xs font-extrabold text-teal-600 hover:text-teal-700 mt-2 inline-block">View Profile &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">Enroll in Mathematics</h3>
                <p class="text-xs font-mono text-slate-500">Access all 48 video lectures, PDF revision books, and live mentor Q&A cohorts.</p>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-2">✓ Full Term 1 & 2 Access</div>
                    <div class="flex items-center gap-2">✓ Direct Mentor Q&A Sessions</div>
                    <div class="flex items-center gap-2">✓ Ministry Exam Revision Sheets</div>
                </div>

                <a href="{{ route('student-portal') }}" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    Start Learning Now &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
