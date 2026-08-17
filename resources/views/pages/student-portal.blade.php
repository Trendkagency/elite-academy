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
                    Welcome Back, <span class="text-teal-400 underline decoration-orange-500 underline-offset-8">{{ auth()->user()->name ?? 'Learner' }}!</span>
                </h1>
                <p class="text-slate-300 text-sm font-mono">
                    Email: {{ auth()->user()->email ?? 'student@eliteacademy.edu' }} • Status: <strong class="text-teal-400">Active Approved Student</strong>
                </p>
            </div>

            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3">
                <a href="{{ route('courses') }}" class="btn-lift text-center py-3 px-6 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-500 rounded-xl shadow-lg shadow-teal-600/20">
                    + Browse & Enroll Courses &rarr;
                </a>
                <a href="{{ route('events') }}" class="text-center py-3 px-6 text-sm font-bold text-slate-300 hover:text-white border border-white/20 rounded-xl">
                    View Live Revision Schedule
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
                <span class="text-xs font-mono font-bold text-teal-300 uppercase tracking-widest">LIVE CLASS SESSION • 2-HOUR EXCUSE RULE ACTIVE</span>
                <h3 class="font-heading font-extrabold text-xl text-white">Advanced Physics: Electromagnetism & Circuits</h3>
                <p class="text-xs font-mono text-slate-300">Absence excuse requests must be submitted at least 2 hours prior to start time.</p>
            </div>
            <button onclick="document.getElementById('excuseModal').classList.remove('hidden')" class="btn-lift py-2.5 px-5 bg-orange-500 hover:bg-orange-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-md flex-shrink-0 cursor-pointer">
                Submit Absence Excuse &rarr;
            </button>
        </div>

        {{-- 3-Column Dashboard Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-4">
                    <h2 class="font-heading font-black text-2xl text-slate-900">Enrolled Courses & Active Progress</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 card-lift">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3 py-1 rounded-full">Mathematics</span>
                                <span class="text-xs font-mono text-slate-500 font-bold">Session 1 Unlocked</span>
                            </div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Algebra & Matrices</h3>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-teal-600 h-2 rounded-full w-[75%]"></div>
                            </div>
                            <a href="{{ route('courses') }}" class="text-xs font-extrabold text-teal-600 hover:text-teal-700 block">Continue Session 1 &rarr;</a>
                        </div>

                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 card-lift">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-extrabold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Physics</span>
                                <span class="text-xs font-mono text-slate-500 font-bold">Session 2 Pending Homework</span>
                            </div>
                            <h3 class="font-heading font-extrabold text-lg text-slate-900">Kinematics & Optics</h3>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full w-[66%]"></div>
                            </div>
                            <a href="{{ route('courses') }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-700 block">Complete Previous Homework &rarr;</a>
                        </div>
                    </div>
                </div>

                {{-- Homework & Exam Submissions --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                    <h2 class="font-heading font-black text-xl text-slate-900">Homework & Homework Submissions</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                            <div>
                                <p class="font-extrabold text-slate-900">Physics Homework 1: Kirchhoff's Laws Network</p>
                                <p class="text-xs font-mono text-slate-500">Passing Grade: 70% • Required to unlock Session 2</p>
                            </div>
                            <span class="text-xs font-mono font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Completed (Passed)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <h3 class="font-heading font-black text-xl text-slate-900">Academic Badges</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-teal-50 rounded-2xl border border-teal-200/80">
                            <span class="text-2xl">🏆</span>
                            <div>
                                <p class="font-extrabold text-sm text-slate-900">Top Science Scholar</p>
                                <p class="text-xs font-mono text-slate-500">Sequential Session Unlocked</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Absence Excuse Modal --}}
<div id="excuseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">Submit Absence Excuse</h3>
            <button onclick="document.getElementById('excuseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="excuseAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="excuseForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="live_session_id" value="1">

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Reason for Absence</label>
                <textarea name="reason" required minlength="10" placeholder="Please provide detailed reason for missing live session..." class="input-mobile h-24"></textarea>
                <p class="text-[11px] text-slate-500">Note: Must be submitted at least 2 hours before session start time.</p>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                Submit Excuse Request
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const excuseForm = document.getElementById('excuseForm');
    const excuseAlert = document.getElementById('excuseAlert');
    if (!excuseForm) return;

    excuseForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        excuseAlert.classList.add('hidden');
        const formData = new FormData(excuseForm);

        try {
            const res = await fetch(excuseForm.action, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            excuseAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            excuseAlert.textContent = data.message;
            excuseAlert.classList.remove('hidden');

            if (data.success) {
                setTimeout(() => document.getElementById('excuseModal').classList.add('hidden'), 1500);
            }
        } catch (err) {
            excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            excuseAlert.textContent = 'Network error. Please try again.';
            excuseAlert.classList.remove('hidden');
        }
    });
});
</script>
@endsection
