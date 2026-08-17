@extends('layouts.app')

@section('content')
<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => 'Student Portal'],
            ]
        ])

        {{-- Dashboard Hero Banner Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950 px-3.5 py-1.5 rounded-full border border-teal-800">
                    LEARNER DASHBOARD
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Welcome Back, <span class="text-teal-400 underline decoration-orange-500 underline-offset-8">Ahmed Mohamed!</span>
                </h1>
                <p class="text-slate-300 text-sm font-mono">
                    Secondary 1 (Grade 10) • Academic Term 1 Overall Progress: <strong class="text-teal-400">82%</strong>
                </p>
            </div>

            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3">
                <a href="{{ route('subjects') }}" class="btn-lift text-center py-3 px-6 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-500 rounded-xl shadow-lg shadow-teal-600/20">
                    + Enroll New Subject &rarr;
                </a>
                <a href="{{ route('events') }}" class="text-center py-3 px-6 text-sm font-bold text-slate-300 hover:text-white border border-white/20 rounded-xl">
                    View Revision Schedule
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Portal Dashboard Content --}}
<section class="py-12 md:py-20 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        {{-- Live Alert Banner --}}
        <div class="bg-gradient-to-r from-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl border border-teal-800/60">
            <div class="space-y-1">
                <span class="text-xs font-mono font-bold text-teal-300 uppercase tracking-widest">LIVE CLASS TODAY AT 5:00 PM</span>
                <h3 class="font-heading font-extrabold text-xl text-white">Mathematics: Matrix Operations Q&A</h3>
                <p class="text-xs font-mono text-slate-300">Instructor: Dr. Ahmed Hassan</p>
            </div>
            <a href="#" class="btn-lift py-2.5 px-5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-md flex-shrink-0">
                Join Live Class &rarr;
            </a>
        </div>

        {{-- 3-Column Dashboard Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-4">
                    <h2 class="font-heading font-black text-2xl text-slate-900">Active School Subjects</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 card-lift">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3 py-1 rounded-full">Mathematics</span>
                                <span class="text-xs font-mono text-slate-500 font-bold">12 / 16 Lessons</span>
                            </div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Algebra & Matrices</h3>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-teal-600 h-2 rounded-full w-[75%]"></div>
                            </div>
                            <a href="{{ route('subject-details') }}" class="text-xs font-extrabold text-teal-600 hover:text-teal-700 block">Continue Lesson 13 &rarr;</a>
                        </div>

                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 card-lift">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Physics</span>
                                <span class="text-xs font-mono text-slate-500 font-bold">8 / 12 Lessons</span>
                            </div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Kinematics & Optics</h3>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full w-[66%]"></div>
                            </div>
                            <a href="{{ route('subject-details') }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-700 block">Continue Lesson 9 &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- Homework & Exam Deadlines --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                    <h2 class="font-heading font-black text-xl text-slate-900">Upcoming Homework & Exams</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                            <div>
                                <p class="font-extrabold text-slate-900">Mathematics Quiz: Complex Equations</p>
                                <p class="text-xs font-mono text-slate-500">Due: Tomorrow at 11:59 PM</p>
                            </div>
                            <span class="text-xs font-mono font-extrabold text-amber-600 bg-amber-50 px-3 py-1 rounded-full">Pending</span>
                        </div>

                        <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                            <div>
                                <p class="font-extrabold text-slate-900">Physics Lab Sheet: Refraction Laws</p>
                                <p class="text-xs font-mono text-slate-500">Submitted: Oct 10, 2026</p>
                            </div>
                            <span class="text-xs font-mono font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Graded 98/100</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <h3 class="font-heading font-black text-xl text-slate-900">Achievements & Certificates</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-2xl border border-teal-200/80">
                            <span class="text-2xl">🏆</span>
                            <div>
                                <p class="font-extrabold text-sm text-slate-900">Top Math Scholar</p>
                                <p class="text-xs font-mono text-slate-500">Ranked #1 in Grade 10 Cohort</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-2xl border border-orange-200/80">
                            <span class="text-2xl">📜</span>
                            <div>
                                <p class="font-extrabold text-sm text-slate-900">Term 1 Honor Roll</p>
                                <p class="text-xs font-mono text-slate-500">Verified Certificate Issued</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
