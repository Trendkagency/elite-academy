@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('app.errors.back_home');
    if ($user) {
        if ($user->isAdmin()) {
            $dashUrl = url('/admin');
            $dashLabel = __('app.admin_portal');
        } elseif ($user->isTeacher()) {
            $dashUrl = route('teacher-portal');
            $dashLabel = __('app.teacher_portal');
        } elseif ($user->isParent()) {
            $dashUrl = route('parent-portal');
            $dashLabel = __('app.parent_portal');
        } elseif ($user->isStudent()) {
            $dashUrl = route('student-portal');
            $dashLabel = __('app.student_portal');
        }
    }
@endphp

<section class="min-h-[85vh] flex items-center justify-center py-16 px-4 bg-slate-50 dark:bg-slate-950 transition-colors relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        {{-- Main Glassmorphic Card Container --}}
        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl p-8 sm:p-14 border border-slate-200/90 dark:border-slate-800 shadow-2xl space-y-8 relative overflow-hidden transition-colors">

            {{-- Icon Badge --}}
            <div class="w-24 h-24 mx-auto bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-3xl flex items-center justify-center text-4xl border border-rose-500/20 shadow-inner">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            </div>

            {{-- Typography --}}
            <div class="space-y-4">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 tracking-widest inline-block uppercase">
                    HTTP 500 — {{ __('INTERNAL SERVER ERROR') }}
                </span>

                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ __('app.errors.500_title') }}
                </h1>

                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base font-medium leading-relaxed max-w-lg mx-auto">
                    {{ __('app.errors.500_desc') }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ $dashUrl }}" class="btn-primary w-full sm:w-auto px-7 py-3.5 text-xs font-extrabold rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <span><i class="fa-solid fa-house" aria-hidden="true"></i></span> {{ $dashLabel }}
                </a>

                <a href="{{ route('contact') }}" class="btn-secondary w-full sm:w-auto px-6 py-3.5 text-xs font-extrabold rounded-2xl transition-all flex items-center justify-center gap-2">
                    <span><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></span> {{ __('Contact Support') }}
                </a>
            </div>

            {{-- Quick Links --}}
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-center gap-4 text-xs font-mono font-bold text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors">{{ __('Home') }}</a>
                <span>•</span>
                <a href="{{ route('courses') }}" class="hover:text-teal-600 transition-colors">{{ __('Courses') }}</a>
                <span>•</span>
                <a href="{{ route('teachers') }}" class="hover:text-teal-600 transition-colors">{{ __('Teachers') }}</a>
                <span>•</span>
                <a href="{{ route('contact') }}" class="hover:text-teal-600 transition-colors">{{ __('Contact Support') }}</a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            {{ __('Elite Academy Platform — High Availability Architecture') }}
        </p>
    </div>

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
</section>
@endsection
