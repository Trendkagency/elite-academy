@extends('layouts.app')

@section('content')
<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.instructors'), 'route' => 'teachers'],
                ['label' => 'Dr. Ahmed Hassan'],
            ]
        ])

        {{-- Magazine Hero Card Layout --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 rounded-3xl p-8 lg:p-12 text-white shadow-2xl relative overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5 relative">
                <div class="rounded-2xl overflow-hidden border-4 border-white/20 shadow-2xl h-[380px] bg-slate-950">
                    <img src="{{ asset('images/instructor_portrait.png') }}" alt="Dr. Ahmed Hassan" class="w-full h-full object-cover">
                </div>
                <span class="absolute bottom-4 left-4 bg-teal-500 text-slate-950 font-mono font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow-lg">
                    ✔ Senior Department Chair
                </span>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                        FACULTY SPOTLIGHT
                    </span>
                    <h1 class="font-heading text-3xl sm:text-5xl font-black text-white tracking-tight">
                        Dr. Ahmed Hassan
                    </h1>
                    <p class="text-slate-300 text-base font-mono">
                        PhD in Applied Mathematics — Massachusetts Institute of Technology (MIT)
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-mono font-semibold text-slate-200">
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-teal-400 font-bold block text-sm">15+ Years</span>
                        Teaching Experience
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-amber-400 font-bold block text-sm">4.9 ★ Rating</span>
                        Student Evaluation
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10 col-span-2 sm:col-span-1">
                        <span class="text-teal-400 font-bold block text-sm">1,400+</span>
                        Active Students
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Bio & Teaching Sections --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">Biography & Academic Background</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Dr. Ahmed Hassan has served as Department Chair of Secondary Mathematics at Elite Academy since 2016. Holding a PhD from MIT in Applied Matrix Equations, he has mentored over 15,000 students across the Middle East to achieve top national scores.
                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Courses Taught by Dr. Ahmed</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 card-lift">
                        <span class="text-xs font-mono font-extrabold text-teal-600">SECONDARY 1 (GRADE 10)</span>
                        <h3 class="font-heading font-extrabold text-xl text-slate-900">Algebra & Trigonometry</h3>
                        <p class="text-xs font-mono text-slate-500">48 Lessons • 1,400 Students</p>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 card-lift">
                        <span class="text-xs font-mono font-extrabold text-purple-600">SECONDARY 3 (GRADE 12)</span>
                        <h3 class="font-heading font-extrabold text-xl text-slate-900">Calculus & Solid Geometry</h3>
                        <p class="text-xs font-mono text-slate-500">64 Lessons • 1,800 Students</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">Join Dr. Ahmed's Cohort</h3>
                <p class="text-xs font-mono text-slate-500">Get direct access to weekly live Q&A webinars and revision worksheets.</p>

                <a href="{{ route('student-portal') }}" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    Enroll with Teacher &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
