@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* ==========================================================================
   Elite Academy Exam Suite - Design System & Theme Engine (Dual-Mode)
   ========================================================================== */

:root {
    --exam-bg-light: #F8FAFC;
    --exam-card-bg-light: #FFFFFF;
    --exam-card-border-light: #E2E8F0;
    --exam-text-main-light: #0F172A;
    --exam-text-sub-light: #475569;
    --exam-option-bg-light: #FFFFFF;
    --exam-option-border-light: #E2E8F0;
    --exam-option-hover-light: #F0FDFA;
    --exam-primary: #0D9488;
    --exam-primary-hover: #0F766E;
    --exam-primary-glow: rgba(13, 148, 136, 0.35);
}

.dark {
    --exam-bg-dark: #070B14;
    --exam-card-bg-dark: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
    --exam-card-border-dark: rgba(51, 65, 85, 0.8);
    --exam-text-main-dark: #F8FAFC;
    --exam-text-sub-dark: #94A3B8;
    --exam-option-bg-dark: rgba(30, 41, 59, 0.7);
    --exam-option-border-dark: rgba(51, 65, 85, 0.9);
    --exam-option-hover-dark: rgba(51, 65, 85, 0.95);
    --exam-primary-dark: #14B8A6;
}

/* Base Exam Background */
.quiz-page-bg {
    background: #F8FAFC;
    min-height: 100vh;
    transition: background 0.3s ease, color 0.3s ease;
}

.dark .quiz-page-bg {
    background: radial-gradient(circle at top, #0F172A 0%, #070B14 100%);
}

/* Master Exam Card Frame */
.quiz-main-card {
    background: #FFFFFF;
    border-radius: 28px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.dark .quiz-main-card {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
    border: 1px solid rgba(51, 65, 85, 0.8);
    box-shadow: 0 25px 55px -10px rgba(0, 0, 0, 0.6), 0 0 30px rgba(13, 148, 136, 0.12);
    backdrop-filter: blur(20px);
}

/* Option Card Items */
.option-card-elite {
    border: 2px solid #E2E8F0;
    border-radius: 20px;
    background: #FFFFFF;
    color: #0F172A;
    padding: 1.15rem 1.4rem;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    position: relative;
    overflow: hidden;
}

.dark .option-card-elite {
    border: 1.5px solid rgba(51, 65, 85, 0.85);
    background: rgba(30, 41, 59, 0.7);
    color: #F8FAFC;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}

.option-card-elite:hover {
    border-color: #0D9488;
    background: #F0FDFA;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.18);
}

.dark .option-card-elite:hover {
    border-color: #2DD4BF;
    background: rgba(51, 65, 85, 0.95);
    box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.3);
}

/* Selected Option State */
.option-card-elite.selected {
    border-color: #0D9488 !important;
    background: #CCFBF1 !important;
    color: #0F172A !important;
    box-shadow: 0 6px 20px -2px rgba(13, 148, 136, 0.25) !important;
    transform: translateY(-1px);
}

.dark .option-card-elite.selected {
    border-color: #14B8A6 !important;
    background: linear-gradient(135deg, rgba(13, 148, 136, 0.32), rgba(16, 185, 129, 0.18)) !important;
    box-shadow: 0 0 25px rgba(20, 184, 166, 0.4), inset 0 0 15px rgba(20, 184, 166, 0.15) !important;
    color: #FFFFFF !important;
}

/* Letter Badges (A, B, C, D) */
.option-letter-badge {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #F1F5F9;
    border: 1.5px solid #CBD5E1;
    color: #475569;
    font-family: ui-monospace, monospace;
    font-weight: 800;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.dark .option-letter-badge {
    background: #1E293B;
    border-color: #475569;
    color: #94A3B8;
}

.option-card-elite.selected .option-letter-badge {
    background: linear-gradient(135deg, #0D9488, #059669) !important;
    border-color: #14B8A6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 0 12px rgba(20, 184, 166, 0.5) !important;
}

/* Primary Action Buttons */
.btn-elite-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #0D9488, #059669);
    color: #FFFFFF;
    font-weight: 800;
    border-radius: 14px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
    box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.35);
}

.btn-elite-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #14B8A6, #10B981);
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -4px rgba(20, 184, 166, 0.45);
}

.btn-elite-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Secondary Navigation Buttons */
.btn-elite-nav {
    border: 1.5px solid #CBD5E1;
    background: #FFFFFF;
    color: #334155;
    font-weight: 700;
    border-radius: 14px;
    transition: all 0.2s ease;
    user-select: none;
}

.dark .btn-elite-nav {
    border: 1.5px solid rgba(71, 85, 105, 0.8);
    background: rgba(30, 41, 59, 0.85);
    color: #E2E8F0;
}

.btn-elite-nav:hover:not(:disabled) {
    border-color: #0D9488;
    background: #F0FDFA;
    color: #0D9488;
    transform: translateY(-1px);
}

.dark .btn-elite-nav:hover:not(:disabled) {
    border-color: #2DD4BF;
    background: rgba(51, 65, 85, 0.95);
    color: #FFFFFF;
}

.btn-elite-nav:disabled {
    opacity: 0.35;
    cursor: not-allowed;
    transform: none;
}

/* Question Number Pagination Dots */
.dot-item {
    width: 44px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-family: ui-monospace, monospace;
    font-size: 0.875rem;
    font-weight: 700;
    background: #FFFFFF;
    border: 1.5px solid #E2E8F0;
    color: #64748B;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}

.dark .dot-item {
    background: rgba(30, 41, 59, 0.9);
    border: 1.5px solid rgba(71, 85, 105, 0.7);
    color: #94A3B8;
}

.dot-item:hover:not(:disabled) {
    border-color: #0D9488;
    background: #F0FDFA;
    color: #0D9488;
    transform: translateY(-1px);
}

.dark .dot-item:hover:not(:disabled) {
    border-color: #64748B;
    background: rgba(51, 65, 85, 0.95);
    color: #FFFFFF;
}

.dot-item.answered-step {
    background: #CCFBF1 !important;
    border-color: #0D9488 !important;
    color: #0F766E !important;
    font-weight: 800 !important;
}

.dark .dot-item.answered-step {
    background: rgba(13, 148, 136, 0.25) !important;
    border-color: rgba(45, 212, 191, 0.75) !important;
    color: #2DD4BF !important;
}

.dot-item.active-step {
    background: linear-gradient(135deg, #0D9488, #059669) !important;
    border-color: #2DD4BF !important;
    color: #FFFFFF !important;
    box-shadow: 0 0 16px rgba(20, 184, 166, 0.5) !important;
    font-weight: 900 !important;
    transform: scale(1.08);
}

/* Anti-Cheat and Proctoring Security Shield Styling */
.unselectable {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}

.security-badge {
    background: rgba(13, 148, 136, 0.1);
    border: 1px solid rgba(13, 148, 136, 0.3);
    color: #0D9488;
}

.dark .security-badge {
    background: rgba(20, 184, 166, 0.15);
    border: 1px solid rgba(20, 184, 166, 0.4);
    color: #2DD4BF;
}

/* Security Strike Alert Banner */
#securityWarningBanner {
    animation: pulseBorder 1.5s infinite;
}

@keyframes pulseBorder {
    0%, 100% { border-color: rgba(225, 29, 72, 0.8); }
    50% { border-color: rgba(225, 29, 72, 0.3); }
}
</style>
@endpush

@section('content')
<section class="quiz-page-bg py-6 sm:py-10 px-4 sm:px-6 lg:px-8 min-h-screen unselectable" id="quizSectionContainer" oncontextmenu="return false;" oncopy="return false;" oncut="return false;" ondragstart="return false;">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Security Violation Warning Toast/Banner (Triggered on tab-switch/blur) --}}
        <div id="securityWarningBanner" class="hidden rounded-2xl p-4 bg-rose-500/10 border-2 border-rose-500 text-rose-700 dark:text-rose-400 flex items-center justify-between shadow-lg backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h5 class="font-extrabold text-sm">{{ app()->getLocale() === 'ar' ? 'تنبيه أمني: تم رصد مغادرة نافذة الاختبار!' : 'Security Warning: Window blur or tab-switch detected!' }}</h5>
                    <p class="text-xs opacity-90" id="securityWarningText">
                        {{ app()->getLocale() === 'ar' ? 'يتم مراقبة نشاطك بدقة. يرجى البقاء في هذه الصفحة حتى تسليم الاختبار.' : 'Your session is monitored. Please remain on this exam tab until submission.' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="strikeCountBadge" class="font-mono text-xs font-black px-3 py-1 bg-rose-500 text-white rounded-lg">
                    Strike 1/3
                </span>
                <button type="button" onclick="document.getElementById('securityWarningBanner').classList.add('hidden')" class="w-7 h-7 rounded-lg hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center cursor-pointer transition-colors" title="Dismiss">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Top Elite Academy Brand, Security Pill & User Profile Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 px-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('student-portal') }}" class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight hover:opacity-80 transition-opacity flex items-center gap-2">
                    <span class="text-teal-600 dark:text-teal-400">Elite</span> Academy<span class="text-teal-500">.</span>
                </a>
                
                {{-- Proctoring Shield Indicator --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full security-badge text-xs font-mono font-bold shadow-xs">
                    <i class="fa-solid fa-shield-halved text-teal-600 dark:text-teal-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'نظام مراقبة آمن نشط' : 'Secure Proctoring Active' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Fullscreen Toggle Button --}}
                <button type="button" id="toggleFullscreenBtn" onclick="toggleExamFullscreen()" class="btn-elite-nav px-3.5 py-2 text-xs font-bold font-mono flex items-center gap-2 shadow-xs cursor-pointer" title="Toggle Fullscreen Mode">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                    <span class="hidden md:inline" id="fullscreenBtnText">{{ app()->getLocale() === 'ar' ? 'ملء الشاشة' : 'Fullscreen' }}</span>
                </button>

                {{-- User Profile Pill --}}
                <div class="flex items-center gap-3 bg-white dark:bg-slate-900/90 px-4 py-2 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-md backdrop-blur-md">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-teal-600 to-emerald-500 text-white font-bold flex items-center justify-center text-sm shadow-md overflow-hidden border-2 border-teal-300/40">
                        {{ mb_substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                    <div class="text-start text-xs leading-tight">
                        <h4 class="font-extrabold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Learner' }}</h4>
                        <span class="font-mono text-slate-500 dark:text-slate-400 text-[11px] block">ID: {{ auth()->user()->id ?? '1001' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Quiz Card Container --}}
        <form id="eliteQuizForm" action="{{ route('ajax.assignment.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

            <div class="quiz-main-card p-6 sm:p-10 md:p-12 relative overflow-hidden space-y-8">
                
                {{-- Quiz Top Bar: Timer & Submit Button --}}
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/90 pb-6">
                    <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 px-4 py-2.5 rounded-2xl shadow-inner">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-teal-600 dark:text-teal-400 text-lg shadow-xs">
                            <i class="fa-solid fa-clock" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">{{ app()->getLocale() === 'ar' ? 'الوقت المتبقي' : 'Time remaining' }}</span>
                            @php
                                $dispRemaining = $remainingSeconds ?? 1800;
                                $dispHrs = floor($dispRemaining / 3600);
                                $dispMins = floor(($dispRemaining % 3600) / 60);
                                $dispSecs = $dispRemaining % 60;
                            @endphp
                            <span id="quizTimer" class="font-mono font-black text-slate-900 dark:text-teal-300 text-base sm:text-lg">
                                {{ sprintf('%02d : %02d : %02d', $dispHrs, $dispMins, $dispSecs) }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" id="submitQuizBtn" class="btn-elite-primary px-7 py-3 font-extrabold text-sm cursor-pointer flex items-center gap-2">
                        <span>{{ app()->getLocale() === 'ar' ? 'تسليم الاختبار' : 'Submit Quiz' }}</span>
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>

                {{-- Main Quiz Body (Questions + Circular Gauge) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center min-h-[320px]">
                    
                    {{-- Left 8 Columns: Question & Options Grid --}}
                    <div class="lg:col-span-8 space-y-6">
                        @forelse($assignment->questions as $index => $q)
                            <div id="questionStep{{ $index }}" class="question-step space-y-6 {{ $index === 0 ? '' : 'hidden' }}" data-step="{{ $index }}">
                                
                                {{-- Question Number Tag --}}
                                <div class="space-y-2">
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-teal-50 dark:bg-teal-950/70 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-400 font-mono text-xs font-bold shadow-xs">
                                        <i class="fa-solid fa-circle-question text-[11px]"></i>
                                        {{ app()->getLocale() === 'ar' ? 'السؤال' : 'Question' }} {{ $index + 1 }} {{ app()->getLocale() === 'ar' ? 'من' : 'of' }} {{ count($assignment->questions) }}
                                    </span>
                                    <h3 class="font-heading font-black text-xl sm:text-2xl text-slate-900 dark:text-white leading-snug math-render">
                                        {{ $q->question_text }}
                                    </h3>

                                    @if($q->image_path)
                                        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-3 max-w-lg my-3 shadow-xs">
                                            <img src="{{ asset('storage/' . $q->image_path) }}" class="max-h-56 rounded-xl object-contain pointer-events-none" alt="Question Image">
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Option Cards (2x2 Grid on Desktop) --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                    @foreach($q->options as $optIndex => $opt)
                                        @php
                                            $inputType = $q->is_multiple_choice ? 'checkbox' : 'radio';
                                            $letter = chr(65 + $optIndex); // A, B, C, D
                                            $savedOptIds = $savedAnswers[(string) $q->id] ?? $savedAnswers[(int) $q->id] ?? [];
                                            if (is_string($savedOptIds)) {
                                                $savedOptIds = json_decode($savedOptIds, true) ?: [$savedOptIds];
                                            }
                                            $savedOptIdsInt = array_map('intval', (array) $savedOptIds);
                                            $isChecked = in_array((int) $opt->id, $savedOptIdsInt, true);
                                        @endphp
                                        <label class="option-label option-card-elite flex items-center justify-between cursor-pointer {{ $isChecked ? 'selected' : '' }}">
                                            <div class="flex items-center gap-3.5 w-full">
                                                <input type="{{ $inputType }}" name="answers[{{ $q->id }}][]" value="{{ $opt->id }}" {{ $isChecked ? 'checked' : '' }} class="option-input accent-teal-600 w-4 h-4 cursor-pointer">
                                                
                                                <span class="option-letter-badge">
                                                    {{ $letter }}
                                                </span>

                                                <span class="math-render leading-relaxed text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100 flex-1">{{ $opt->option_text }}</span>
                                            </div>

                                            @if($opt->image_path)
                                                <img src="{{ asset('storage/' . $opt->image_path) }}" class="h-8 rounded border border-slate-200 dark:border-slate-700 pointer-events-none ms-2" alt="Option Image">
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 dark:text-slate-400 font-mono text-xs">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد أسئلة مضافة لهذا الاختبار بعد.' : 'No questions configured for this assignment yet.' }}
                            </div>
                        @endforelse
                    </div>

                    {{-- Right 4 Columns: Circular Gauge Progress Ring --}}
                    <div class="lg:col-span-4 flex flex-col items-center justify-center p-6 bg-slate-50/70 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/90 rounded-3xl backdrop-blur-sm shadow-xl">
                        <div class="relative w-44 h-44 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="48" stroke="currentColor" stroke-width="10" fill="transparent" class="text-slate-200 dark:text-slate-800" />
                                <circle id="gaugeRingFill" cx="60" cy="60" r="48" stroke="#14B8A6" stroke-width="10" fill="transparent"
                                        stroke-dasharray="301.59" stroke-dashoffset="271.43" stroke-linecap="round" class="transition-all duration-500 ease-out"
                                        style="filter: drop-shadow(0 0 8px rgba(20, 184, 166, 0.4));" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <span id="gaugeText" class="font-heading font-black text-3xl sm:text-4xl text-slate-900 dark:text-white tracking-tight">1/{{ count($assignment->questions) }}</span>
                                <span class="text-[11px] font-mono font-bold text-teal-600 dark:text-teal-400 uppercase tracking-widest mt-1">{{ app()->getLocale() === 'ar' ? 'تقدم الإجابات' : 'Progress' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom Navigation Toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-200 dark:border-slate-800/90">
                    <button type="button" id="prevBtn" disabled class="btn-elite-nav px-6 py-3 text-xs font-bold font-mono disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                    </button>

                    {{-- Question Numbers Grid Map (1, 2, 3, 4...) --}}
                    <div class="flex flex-wrap items-center justify-center gap-2 overflow-x-auto py-1" id="dotsContainer">
                        @foreach($assignment->questions as $i => $q)
                            <button type="button" data-step-index="{{ $i }}" class="dot-item {{ $i === 0 ? 'active-step' : '' }}">
                                {{ $i + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <button type="button" id="nextBtn" class="btn-elite-nav px-8 py-3 text-xs font-bold font-mono cursor-pointer flex items-center gap-2">
                        <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </div>
        </form>

    </div>
</section>

{{-- Result Breakdown Modal (Displays Full Scores & Evaluation Breakdown on Screen) --}}
<div id="resultModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/80 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 text-center space-y-6 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
        
        {{-- Passed/Failed Icon Badge --}}
        <div id="resultIconBadge" class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4">
            <i class="fa-solid fa-sparkles text-amber-400" aria-hidden="true"></i>
        </div>

        <div class="space-y-2">
            <h3 id="resultTitle" class="font-heading font-black text-2xl text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'نتيجة الاختبار' : 'Assignment Evaluated' }}</h3>
            <p id="resultMessage" class="text-xs font-mono text-slate-600 dark:text-slate-300 leading-relaxed"></p>
        </div>

        {{-- Score Numbers Breakdown Grid --}}
        <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/80 font-mono text-xs">
            <div class="space-y-1 p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-xs">
                <span class="text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold block">Final Percentage</span>
                <span id="resultPercentage" class="font-black text-2xl text-teal-600 dark:text-teal-400">100%</span>
            </div>
            <div class="space-y-1 p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-xs">
                <span class="text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold block">Points Earned</span>
                <span id="resultScore" class="font-black text-2xl text-slate-900 dark:text-white">10 / 10</span>
            </div>
        </div>

        {{-- Modal Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('student-portal') }}" class="w-full sm:w-auto btn-elite-primary px-8 py-3 font-bold text-xs shadow-md">
                Go to Student Portal &rarr;
            </a>
            <button type="button" onclick="closeResultModal()" class="w-full sm:w-auto btn-elite-nav px-6 py-3 font-bold text-xs cursor-pointer">
                Review Questions
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>

<script>
window.currentStep = {{ $currentStepIndex ?? 0 }};
window.totalSteps = {{ count($assignment->questions) }};
window.durationMinutes = {{ $assignment->duration_minutes ?? 30 }};
window.serverRemainingSeconds = {{ $remainingSeconds ?? 1800 }};
window.examDeadline = Date.now() + (window.serverRemainingSeconds * 1000);
window.timerSeconds = window.serverRemainingSeconds;
window.assignmentId = {{ $assignment->id }};
window.savedAnswers = @json($savedAnswers ?? []);
window.isSubmittedSuccessfully = false;

// =========================================================================
// KaTeX Math Renderer
// =========================================================================
window.triggerKaTeXRender = function() {
    if (window.renderMathInElement) {
        window.renderMathInElement(document.body, {
            delimiters: [
                {left: '$$', right: '$$', display: true},
                {left: '$', right: '$', display: false},
                {left: '\\(', right: '\\)', display: false},
                {left: '\\[', right: '\\]', display: true}
            ],
            throwOnError: false
        });
    }
};

// =========================================================================
// Server Step Synchronization
// =========================================================================
window.persistStepIndexToServer = async function(stepIdx) {
    try {
        await fetch("{{ route('ajax.assignment.update-step') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                assignment_id: window.assignmentId,
                current_step_index: parseInt(stepIdx, 10)
            })
        });
    } catch (e) {}
};

// =========================================================================
// Restore Draft Answers
// =========================================================================
window.restoreSavedAnswers = function() {
    if (!window.savedAnswers || typeof window.savedAnswers !== 'object') return;

    for (const [questionId, selectedOpts] of Object.entries(window.savedAnswers)) {
        if (!selectedOpts) continue;
        let optsArray = selectedOpts;
        if (typeof optsArray === 'string') {
            try { optsArray = JSON.parse(optsArray); } catch(e) { optsArray = [optsArray]; }
        }
        if (!Array.isArray(optsArray)) {
            optsArray = [optsArray];
        }
        if (optsArray.length === 0) continue;

        const inputs = document.querySelectorAll(`input[name="answers[${questionId}][]"]`);
        inputs.forEach(input => {
            const valInt = parseInt(input.value, 10);
            const valStr = String(input.value);
            const isMatch = optsArray.some(opt => opt == valInt || opt == valStr);

            if (isMatch) {
                input.checked = true;
                const label = input.closest('.option-card-elite');
                if (label) label.classList.add('selected');
            }
        });
    }

    window.currentStep = {{ $currentStepIndex ?? 0 }};
    window.updateStepUI();
};

window.saveDraftTimers = window.saveDraftTimers || {};

window.saveDraftAnswerToServer = function(questionId, selectedOptionIds) {
    const qIdStr = String(questionId);
    if (window.saveDraftTimers[qIdStr]) {
        clearTimeout(window.saveDraftTimers[qIdStr]);
    }

    window.saveDraftTimers[qIdStr] = setTimeout(async function() {
        try {
            const res = await fetch("{{ route('ajax.assignment.save-answer') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    assignment_id: window.assignmentId,
                    question_id: parseInt(questionId, 10),
                    selected_option_ids: selectedOptionIds
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                window.queueOfflineDraft(questionId, selectedOptionIds);
            }
        } catch (err) {
            window.queueOfflineDraft(questionId, selectedOptionIds);
        }
    }, 400);
};

window.queueOfflineDraft = function(questionId, selectedOptionIds) {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queue = JSON.parse(localStorage.getItem(key) || '{}');
        queue[questionId] = selectedOptionIds;
        localStorage.setItem(key, JSON.stringify(queue));
    } catch (e) {}
};

window.flushOfflineDrafts = async function() {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queueStr = localStorage.getItem(key);
        if (!queueStr) return;
        const queue = JSON.parse(queueStr);

        for (const [qId, optIds] of Object.entries(queue)) {
            await window.saveDraftAnswerToServer(qId, optIds);
        }
        localStorage.removeItem(key);
    } catch (e) {}
};

window.addEventListener('online', window.flushOfflineDrafts);

window.syncOptionUI = function(input) {
    const labelEl = input.closest('.option-card-elite');
    if (!labelEl) return;

    const parentContainer = labelEl.closest('.question-step');
    const isMulti = input.type === 'checkbox';
    const questionIdMatch = input.name.match(/answers\[(\d+)\]/);
    const questionId = questionIdMatch ? questionIdMatch[1] : null;

    if (!isMulti) {
        parentContainer.querySelectorAll('.option-card-elite').forEach(card => {
            const cardInput = card.querySelector('input');
            if (cardInput && cardInput.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    } else {
        if (input.checked) {
            labelEl.classList.add('selected');
        } else {
            labelEl.classList.remove('selected');
        }
    }

    if (questionId) {
        const selectedOptionIds = [];
        parentContainer.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`).forEach(checkedInput => {
            selectedOptionIds.push(parseInt(checkedInput.value, 10));
        });

        window.saveDraftAnswerToServer(questionId, selectedOptionIds);
    }

    window.updateStepUI();

    if (!isMulti && window.currentStep < window.totalSteps - 1) {
        setTimeout(() => {
            window.navigateStep(1);
        }, 320);
    }
};

window.updateStepUI = function() {
    document.querySelectorAll('.question-step').forEach((step, idx) => {
        if (idx === window.currentStep) {
            step.classList.remove('hidden');
        } else {
            step.classList.add('hidden');
        }
    });

    const gaugeText = document.getElementById('gaugeText');
    const ringFill = document.getElementById('gaugeRingFill');
    if (gaugeText) gaugeText.textContent = `${window.currentStep + 1}/${window.totalSteps}`;

    if (ringFill) {
        const circumference = 301.59;
        const progress = (window.currentStep + 1) / window.totalSteps;
        const offset = circumference * (1 - progress);
        ringFill.style.strokeDashoffset = offset;
    }

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) prevBtn.disabled = window.currentStep === 0;
    if (nextBtn) nextBtn.disabled = window.currentStep === window.totalSteps - 1;

    document.querySelectorAll('.dot-item').forEach((dot) => {
        const idx = parseInt(dot.getAttribute('data-step-index') || '0', 10);
        if (idx === window.currentStep) {
            dot.classList.add('active-step');
        } else {
            dot.classList.remove('active-step');
        }

        if (window.isStepAnswered(idx)) {
            dot.classList.add('answered-step');
        } else {
            dot.classList.remove('answered-step');
        }
    });

    window.triggerKaTeXRender();
};

window.isStepAnswered = function(stepIndex) {
    const stepEl = document.querySelector(`.question-step[data-step="${stepIndex}"]`);
    if (!stepEl) return true;
    return stepEl.querySelector('input:checked') !== null;
};

window.navigateStep = function(direction) {
    if (direction > 0) {
        if (!window.isStepAnswered(window.currentStep)) {
            if (window.Toast) {
                window.Toast.warning(
                    "{{ app()->getLocale() === 'ar' ? 'يرجى اختيار إجابة للسؤال الحالي أولاً قبل الانتقال للسؤال التالي.' : 'Please select an answer for the current question before advancing.' }}",
                    "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                );
            }
            return false;
        }
    }

    const newStep = window.currentStep + direction;
    if (newStep >= 0 && newStep < window.totalSteps) {
        window.currentStep = newStep;
        window.updateStepUI();
        window.persistStepIndexToServer(newStep);
        return true;
    }
    return false;
};

window.jumpToStep = function(targetStepIdx) {
    if (targetStepIdx === window.currentStep) return;

    if (targetStepIdx > window.currentStep) {
        for (let i = 0; i < targetStepIdx; i++) {
            if (!window.isStepAnswered(i)) {
                if (window.Toast) {
                    window.Toast.warning(
                        `{{ app()->getLocale() === 'ar' ? 'يرجى إجابة السؤال رقم (' : 'Please answer question #' }}${i + 1}{{ app()->getLocale() === 'ar' ? ') أولاً قبل الانتقال لأسئلة لاحقة.' : ' first before skipping ahead.' }}`,
                        "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                    );
                }
                window.currentStep = i;
                window.updateStepUI();
                window.persistStepIndexToServer(i);
                return;
            }
        }
    }

    if (targetStepIdx >= 0 && targetStepIdx < window.totalSteps) {
        window.currentStep = targetStepIdx;
        window.updateStepUI();
        window.persistStepIndexToServer(targetStepIdx);
    }
};

window.showResultModal = function(data) {
    const modal = document.getElementById('resultModal');
    const badge = document.getElementById('resultIconBadge');
    const title = document.getElementById('resultTitle');
    const msg = document.getElementById('resultMessage');
    const perc = document.getElementById('resultPercentage');
    const score = document.getElementById('resultScore');

    if (!modal) return;

    if (data.is_passed) {
        badge.innerHTML = '<i class="fa-solid fa-sparkles text-amber-400"></i>';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-emerald-50 text-emerald-600 border-emerald-300 dark:bg-emerald-950/60 dark:border-emerald-700';
        title.innerHTML = 'Passed Successfully! <i class="fa-solid fa-check ms-1"></i>';
        title.className = 'font-heading font-black text-2xl text-emerald-700 dark:text-emerald-400';
    } else {
        badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-rose-50 text-rose-600 border-rose-300 dark:bg-rose-950/60 dark:border-rose-700';
        title.innerHTML = 'Did Not Pass <i class="fa-solid fa-xmark ms-1"></i>';
        title.className = 'font-heading font-black text-2xl text-rose-700 dark:text-rose-400';
    }

    if (msg) msg.textContent = data.message || 'Evaluation completed.';
    if (perc) perc.textContent = `${Math.round(data.percentage || 0)}%`;
    if (score) score.textContent = `${data.score || 0} / ${data.total_points || 10}`;

    if (window.openModal) {
        window.openModal('resultModal');
    } else {
        modal.classList.remove('hidden');
    }
};

window.closeResultModal = function() {
    if (window.closeModal) {
        window.closeModal('resultModal');
    } else {
        const modal = document.getElementById('resultModal');
        if (modal) modal.classList.add('hidden');
    }
};

// =========================================================================
// Security, Anti-Cheat & Proctoring Engine
// =========================================================================
window.tabSwitchViolations = 0;
window.maxAllowedViolations = 3;
window.lastViolationTime = 0;

function triggerSecurityViolation(reason) {
    if (window.isSubmittedSuccessfully) return;
    
    // Debounce guard: prevent simultaneous blur and visibilitychange double triggers within 3 seconds
    const now = Date.now();
    if (now - window.lastViolationTime < 3000) {
        return;
    }
    window.lastViolationTime = now;

    if (window.tabSwitchViolations < window.maxAllowedViolations) {
        window.tabSwitchViolations++;
    }

    const banner = document.getElementById('securityWarningBanner');
    const badge = document.getElementById('strikeCountBadge');

    if (banner && badge) {
        banner.classList.remove('hidden');
        badge.textContent = `Strike ${window.tabSwitchViolations}/${window.maxAllowedViolations}`;
    }

    if (window.Toast) {
        window.Toast.warning(
            `{{ app()->getLocale() === 'ar' ? 'تنبيه أمني: تم رصد مغادرة نافذة الاختبار! مخالفة (' : 'Security Warning: Tab switch/blur detected! Strike (' }}${window.tabSwitchViolations}/${window.maxAllowedViolations})`,
            "{{ app()->getLocale() === 'ar' ? 'مراقبة الاختبار' : 'Proctoring Warning' }}"
        );
    }
}

// Fullscreen Proctoring Toggle
window.toggleExamFullscreen = function() {
    const elem = document.documentElement;
    const icon = document.getElementById('fullscreenIcon');
    const text = document.getElementById('fullscreenBtnText');

    if (!document.fullscreenElement) {
        elem.requestFullscreen().then(() => {
            if (icon) icon.className = 'fa-solid fa-compress';
            if (text) text.textContent = "{{ app()->getLocale() === 'ar' ? 'إنهاء التكبير' : 'Exit Fullscreen' }}";
        }).catch(() => {});
    } else {
        document.exitFullscreen().then(() => {
            if (icon) icon.className = 'fa-solid fa-expand';
            if (text) text.textContent = "{{ app()->getLocale() === 'ar' ? 'ملء الشاشة' : 'Fullscreen' }}";
        }).catch(() => {});
    }
};

// Anti-Cheat Event Listeners (Debounced unified proctoring)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        triggerSecurityViolation('Tab Switch');
    }
});

window.addEventListener('blur', function() {
    triggerSecurityViolation('Window Blur');
});

// Disable developer hotkeys, copy/cut, view-source, inspect
window.addEventListener('keydown', function(e) {
    // F12 or Ctrl+Shift+I or Ctrl+Shift+J or Ctrl+Shift+C (Devtools)
    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && ['I', 'J', 'C', 'i', 'j', 'c'].includes(e.key))) {
        e.preventDefault();
        return false;
    }
    // Ctrl+U (View Source) or Ctrl+S (Save) or Ctrl+P (Print)
    if (e.ctrlKey && ['u', 'U', 's', 'S', 'p', 'P'].includes(e.key)) {
        e.preventDefault();
        return false;
    }
    // Ctrl+C, Ctrl+X, Ctrl+A inside the exam area
    if (e.ctrlKey && ['c', 'C', 'x', 'X', 'a', 'A'].includes(e.key)) {
        e.preventDefault();
        return false;
    }
});

// Guard against accidental page close before submit
window.addEventListener('beforeunload', function(e) {
    if (!window.isSubmittedSuccessfully) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// =========================================================================
// Initialization on DOM Load
// =========================================================================
document.addEventListener('DOMContentLoaded', function () {
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(1);
        });
    }

    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(-1);
        });
    }

    document.querySelectorAll('.option-input').forEach(input => {
        input.addEventListener('change', function() {
            window.syncOptionUI(this);
        });
    });

    document.querySelectorAll('.dot-item').forEach(dot => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            const stepIdx = parseInt(this.getAttribute('data-step-index') || '0', 10);
            window.jumpToStep(stepIdx);
        });
    });

    function updateQuizTimerDisplay() {
        const remainingMs = Math.max(0, window.examDeadline - Date.now());
        const totalSecs = Math.floor(remainingMs / 1000);
        window.timerSeconds = totalSecs;

        const hrs = Math.floor(totalSecs / 3600);
        const mins = Math.floor((totalSecs % 3600) / 60);
        const secs = Math.floor(totalSecs % 60);
        const timerEl = document.getElementById('quizTimer');
        if (timerEl) {
            timerEl.textContent = `${String(hrs).padStart(2, '0')} : ${String(mins).padStart(2, '0')} : ${String(secs).padStart(2, '0')}`;
            
            if (totalSecs <= 180 && totalSecs > 0) {
                timerEl.classList.add('text-rose-600', 'animate-pulse');
                timerEl.classList.remove('text-slate-900');
            }
        }

        if (totalSecs <= 0) {
            if (window.quizTimerInterval) {
                clearInterval(window.quizTimerInterval);
                window.quizTimerInterval = null;
            }
            handleQuizTimeExpired();
        }
    }

    function handleQuizTimeExpired() {
        if (window.isTimeExpiredHandled) return;
        window.isTimeExpiredHandled = true;

        if (window.Toast) {
            window.Toast.error(
                "{{ app()->getLocale() === 'ar' ? 'انتهى الوقت المحدد للواجب! يتم الآن إرسال إجاباتك وتقييمها تلقائياً...' : 'Time is up! Submitting and evaluating your answers automatically...' }}",
                "{{ app()->getLocale() === 'ar' ? 'انتهى الوقت' : 'Time Expired' }}"
            );
        }

        const quizForm = document.getElementById('eliteQuizForm');
        if (quizForm) {
            const submitBtn = document.getElementById('submitQuizBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Auto-submitting...';
            }
            if (typeof quizForm.requestSubmit === 'function') {
                quizForm.requestSubmit();
            } else {
                quizForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        }
    }

    updateQuizTimerDisplay();
    window.quizTimerInterval = setInterval(updateQuizTimerDisplay, 1000);

    window.restoreSavedAnswers();
    window.flushOfflineDrafts();

    const quizForm = document.getElementById('eliteQuizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitQuizBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Evaluating...';
            }

            try {
                const formData = new FormData(quizForm);
                const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (window.Toast) window.Toast.error(data.message || 'Evaluation failed.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Quiz';
                    }
                    return;
                }

                window.isSubmittedSuccessfully = true;

                if (window.Toast) {
                    if (data.is_passed) {
                        window.Toast.success(`Score: ${data.percentage}% (PASSED)`, 'Assignment Complete!');
                    } else {
                        window.Toast.error(`Score: ${data.percentage}% (FAILED)`, 'Assignment Result');
                    }
                }

                window.showResultModal(data);

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Results Evaluated <i class="fa-solid fa-check ms-1"></i>';
                }
            } catch (err) {
                if (window.Toast) window.Toast.error('Network error during evaluation submission.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Quiz';
                }
            }
        });
    }

    window.updateStepUI();
});
</script>
@endpush
@endsection
