@extends('layouts.portal-panel')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $userAuth = auth()->user();

    $liveCount = $startingSoonSessions->filter(function($s) use ($userAuth) {
        return $s->evaluateState($userAuth) === \App\Enums\LiveSessionState::LIVE;
    })->count();

    // Determine initial active section tab
    $requestedTab = request()->query('tab', 'overview');
    $validTabs = ['overview', 'sessions', 'courses', 'assignments', 'submissions', 'packages', 'exceptions', 'notifications'];
    $activeTab = in_array($requestedTab, $validTabs, true) ? $requestedTab : 'overview';
@endphp

<div class="student-portal-shell space-y-6" id="studentPortalRoot">

    {{-- ========================================================================= --}}
    {{-- 1. Ultra-Premium Glassmorphic Hero Banner --}}
    {{-- ========================================================================= --}}
    <section class="relative rounded-3xl py-6 sm:py-8 px-4 sm:px-6 md:px-8 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border border-slate-800/80 overflow-hidden shadow-2xl">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-5 relative z-10">
            @include('components.breadcrumb', [
                'items' => [
                    ['label' => __('navbar.home'), 'route' => 'home'],
                    ['label' => __('navbar.portal')],
                ]
            ])

            {{-- Learner Header Info --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex items-center gap-3 sm:gap-5 min-w-0">
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg shadow-teal-500/20 border-2 border-teal-300/40">
                            {{ mb_substr($userAuth->name ?? 'S', 0, 1) }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-slate-950 flex items-center justify-center text-[9px] font-bold text-white shadow-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </div>

                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-1 rounded-full border border-teal-700/60 shadow-xs">
                                <i class="fa-solid fa-graduation-cap"></i> {{ __('app.student_portal') }}
                            </span>
                            @if($hasActivePackage)
                                <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {{ $isAr ? 'اشتراك نشط' : 'Active Enrollment' }}
                                </span>
                            @else
                                <span class="text-xs font-mono text-rose-400 font-bold bg-rose-950/60 px-2.5 py-0.5 rounded-full border border-rose-800/60 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                    {{ $isAr ? 'يلزم تجديد الباقة' : 'Package Required' }}
                                </span>
                            @endif

                            @if($liveCount > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-mono font-black bg-rose-600 text-white shadow-md shadow-rose-600/40 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                    {{ $liveCount }} {{ $isAr ? 'بث مباشر الآن' : 'LIVE NOW' }}
                                </span>
                            @endif
                        </div>

                        <h1 class="font-heading text-xl sm:text-2xl md:text-3xl font-black text-white tracking-tight break-words">
                            {{ __('app.portal.welcome_back') }}، <span class="bg-gradient-to-r from-teal-300 to-emerald-400 bg-clip-text text-transparent underline decoration-orange-500 decoration-2 underline-offset-8">{{ $userAuth->name ?? __('Learner') }}!</span>
                        </h1>

                        <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-2 pt-0.5">
                            <span><i class="fa-solid fa-layer-group text-teal-400"></i> {{ __('app.portal.grade_level') }}: <strong class="text-teal-300">{{ $studentProfile?->gradeLevel?->name ?: ($isAr ? 'الصف الثاني عشر STEM' : 'Grade 12 STEM') }}</strong></span>
                            <span class="text-slate-600">•</span>
                            <span><i class="fa-solid fa-school text-orange-400"></i> {{ __('app.portal.school') }}: <strong class="text-slate-200">{{ $studentProfile?->school_name ?: 'Elite STEM Academy' }}</strong></span>
                        </p>
                    </div>
                </div>

                {{-- Quick Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                    <button type="button" onclick="window.openModal ? window.openModal('excuseModal') : document.getElementById('excuseModal').classList.remove('hidden')"
                        class="btn-lift px-4 py-2.5 sm:py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-extrabold rounded-2xl shadow-lg shadow-orange-500/20 cursor-pointer flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial">
                        <span><i class="fa-solid fa-file-signature"></i></span>
                        <span>{{ __('app.portal.submit_excuse') }}</span>
                    </button>
                    <button type="button" onclick="window.openModal ? window.openModal('homeworkExceptionModal') : document.getElementById('homeworkExceptionModal').classList.remove('hidden')"
                        class="btn-lift px-4 py-2.5 sm:py-3 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-teal-600/25 cursor-pointer flex items-center justify-center gap-2 transition-all flex-1 sm:flex-initial">
                        <span><i class="fa-solid fa-clipboard-question"></i></span>
                        <span>{{ __('app.portal.submit_exception') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Package Alert if missing --}}
    @if(!$hasActivePackage)
        <div class="p-5 sm:p-6 bg-rose-50/95 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-rose-950 dark:text-rose-100 shadow-md">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-900/60 flex items-center justify-center text-rose-600 dark:text-rose-300 text-xl font-bold shrink-0 mt-0.5">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="font-bold text-sm sm:text-base leading-tight">
                        {{ $isAr ? 'تنبيه: لا توجد باقة حصص نشطة لديك حالياً!' : 'Warning: No Active Session Package Subscription Found!' }}
                    </h4>
                    <p class="text-xs font-mono text-rose-800 dark:text-rose-300/90 leading-relaxed">
                        {{ $isAr ? 'يلزم الاشتراك في باقة حصص للوصول الكامل إلى البث المباشر، الدروس المسجلة، وحل الواجبات التقييمية.' : 'An active package subscription is required to unlock live classroom streams, recorded modules, and interactive homework.' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('courses') }}"
                class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-rose-600/30 whitespace-nowrap flex items-center gap-2 shrink-0 self-stretch sm:self-auto justify-center">
                <span><i class="fa-solid fa-cart-shopping"></i></span>
                <span>{{ $isAr ? 'تصفح الباقات والكورسات' : 'Explore Packages & Courses' }}</span>
            </a>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 2. MASTER FULL-SECTION PANES CONTAINER (Zero Page Scroll) --}}
    {{-- ========================================================================= --}}
    <div id="studentSectionsMasterContainer">
        {{-- Modular Separated Section Views --}}
        @include('pages.student.sections.overview')
        @include('pages.student.sections.sessions')
        @include('pages.student.sections.courses')
        @include('pages.student.sections.assignments')
        @include('pages.student.sections.submissions')
        @include('pages.student.sections.exceptions')
        @include('pages.student.sections.notifications')
    </div>

    {{-- ========================================================================= --}}
    {{-- 4. MODALS (MSQ Solver, Excuses, Homework Exceptions, Own Package Details, Course Syllabus) --}}
    {{-- ========================================================================= --}}
    @include('pages.student.sections.packages')

    {{-- 1. Modal: Interactive MSQ Assignment Solver --}}
    <div id="takeMsqModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] max-w-2xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[90vh] flex flex-col overflow-hidden">
            <div class="p-5 sm:p-6 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-700 flex justify-between items-start shrink-0">
                <div>
                    <span class="text-[10px] font-mono font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950 px-3 py-1 rounded-full uppercase border border-teal-200 dark:border-teal-800">
                        Interactive MSQ Evaluation
                    </span>
                    <h3 id="msqModalTitle" class="font-heading font-black text-xl sm:text-2xl text-slate-900 dark:text-white mt-1">Loading Assignment...</h3>
                    <p id="msqModalDesc" class="text-xs text-slate-500 font-mono mt-0.5"></p>
                </div>
                <button type="button" onclick="closeMsqModal()"
                    class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-all cursor-pointer border border-slate-200 dark:border-slate-700 shrink-0 text-xl font-bold"
                    aria-label="{{ __('Close') }}">&times;</button>
            </div>

            <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5 flex-1">
                {{-- Timer & Warning Bar --}}
                <div id="msqTimerBar" class="flex items-center justify-between p-4 bg-slate-900 text-white rounded-2xl text-xs font-mono shadow-md">
                    <span class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>Session Deadline Rule Active</span>
                    </span>
                    <span id="msqTimerDisplay" class="font-bold text-teal-300 text-sm">Time Remaining: --:--</span>
                </div>

                <form id="msqAnswerForm" class="space-y-6">
                    @csrf
                    <input type="hidden" id="msqAssignmentId" name="assignment_id" value="">

                    <div id="msqQuestionsContainer" class="space-y-6">
                        <div class="text-center py-8 text-slate-500 font-mono text-xs">Loading questions...</div>
                    </div>

                    <button type="submit" id="msqSubmitBtn"
                        class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/30 font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2 cursor-pointer">
                        <span>{{ $isAr ? 'إرسال الإجابات والتصحيح التلقائي' : 'Submit Assignment for Automated Evaluation' }}</span> &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Modal: Submit Session Absence Excuse --}}
    <div id="excuseModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 space-y-4 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-200/60 dark:border-teal-800">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">{{ __('app.portal.submit_excuse') }}</h3>
                </div>
                <button type="button" onclick="window.closeModal ? window.closeModal('excuseModal') : document.getElementById('excuseModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-all cursor-pointer border border-slate-200 dark:border-slate-700 text-lg font-bold"
                    aria-label="{{ __('Close') }}">&times;</button>
            </div>

            <div id="excuseAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

            <form id="excuseForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Target Course Selection --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'اختر المقرر الدراسي' : 'Select Target Course' }}
                    </label>
                    <select name="course_id" id="excuseCourseSelect" required class="input-mobile bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"
                        onchange="onExcuseCourseChange(this.value, 'excuseSessionSelect')">
                        <option value="">{{ $isAr ? '-- اختر الكورس --' : '-- Select Course --' }}</option>
                        @if(isset($enrollments) && count($enrollments) > 0)
                            @foreach($enrollments as $e)
                                <option value="{{ $e->course_id }}">{{ $e->course?->title ?: ('Course #' . $e->course_id) }}</option>
                            @endforeach
                        @elseif(isset($filterCourses) && count($filterCourses) > 0)
                            @foreach($filterCourses as $fc)
                                <option value="{{ $fc->id }}">{{ $fc->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Target Session Selection --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-satellite-dish text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'الحصة الدراسية المستهدفة' : 'Target Session' }}
                    </label>
                    <select name="session_id" id="excuseSessionSelect" class="input-mobile bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100">
                        <option value="">{{ $isAr ? '-- اختر الكورس أولاً لعرض الحصص --' : '-- Select Course first --' }}</option>
                    </select>
                </div>

                {{-- Reason --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-pen text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'سبب الغياب أو العذر' : 'Excuse Reason' }}
                    </label>
                    <textarea name="reason" required minlength="10"
                        placeholder="{{ $isAr ? 'يرجى توضيح سبب الغياب بالتفصيل لإحالته للإدارة والمعلم...' : 'Explain the reason for absence...' }}"
                        class="input-mobile h-24 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
                </div>

                <button type="submit" id="excuseSubmitBtn"
                    class="w-full btn-mobile-lg btn-lift text-slate-950 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 shadow-md font-bold py-3 rounded-2xl cursor-pointer transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>{{ $isAr ? 'إرسال طلب العذر' : 'Submit Excuse Request' }}</span>
                </button>
            </form>
        </div>
    </div>

    {{-- 3. Modal: Submit Homework Exception --}}
    <div id="homeworkExceptionModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 space-y-4 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-200/60 dark:border-teal-800">
                        <i class="fa-solid fa-clipboard-question"></i>
                    </div>
                    <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">{{ __('app.portal.submit_exception') }}</h3>
                </div>
                <button type="button" onclick="window.closeModal ? window.closeModal('homeworkExceptionModal') : document.getElementById('homeworkExceptionModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-all cursor-pointer border border-slate-200 dark:border-slate-700 text-lg font-bold"
                    aria-label="{{ __('Close') }}">&times;</button>
            </div>

            <div id="hwExceptionAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

            <form id="hwExceptionForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Course Selection --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'اختر المقرر الدراسي' : 'Select Target Course' }}
                    </label>
                    <select name="course_id" id="hwExceptionCourseSelect" required class="input-mobile bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"
                        onchange="onExcuseCourseChange(this.value, 'hwExceptionSessionSelect')">
                        <option value="">{{ $isAr ? '-- اختر الكورس --' : '-- Select Course --' }}</option>
                        @if(isset($enrollments) && count($enrollments) > 0)
                            @foreach($enrollments as $e)
                                <option value="{{ $e->course_id }}">{{ $e->course?->title ?: ('Course #' . $e->course_id) }}</option>
                            @endforeach
                        @elseif(isset($filterCourses) && count($filterCourses) > 0)
                            @foreach($filterCourses as $fc)
                                <option value="{{ $fc->id }}">{{ $fc->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Session Selection --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-tv text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'الحصة أو الدرس المرتبط' : 'Target Session' }}
                    </label>
                    <select name="session_id" id="hwExceptionSessionSelect" class="input-mobile bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100">
                        <option value="">{{ $isAr ? '-- اختر الكورس أولاً لعرض الحصص --' : '-- Select Course first --' }}</option>
                    </select>
                </div>

                {{-- Reason --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-pen text-teal-600 dark:text-teal-400"></i>
                        {{ $isAr ? 'سبب طلب استثناء الواجب' : 'Reason for Exception' }}
                    </label>
                    <textarea name="reason" required minlength="10"
                        placeholder="{{ $isAr ? 'اشرح المشكلة التقنية أو سبب طلب استثناء الواجب...' : 'Describe technical issue or homework exception...' }}"
                        class="input-mobile h-24 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100"></textarea>
                </div>

                <button type="submit" id="hwExceptionSubmitBtn"
                    class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md font-bold py-3 rounded-2xl cursor-pointer transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>{{ $isAr ? 'إرسال طلب استثناء الواجب' : 'Submit Homework Exception' }}</span>
                </button>
            </form>
        </div>
    </div>

    {{-- 4. Modal: Enrolled Course Details & Syllabus Explorer --}}
    <div id="enrolledCourseModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200/90 dark:border-slate-800 flex flex-col relative">
            
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white p-6 sm:p-8 flex items-start justify-between relative overflow-hidden shrink-0">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="space-y-2 relative z-10">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="modalCourseSubject" class="bg-teal-600 text-white text-xs font-bold px-3 py-0.5 rounded-full shadow-xs"></span>
                        <span id="modalCourseGrade" class="bg-slate-800 text-slate-300 text-xs font-mono px-2.5 py-0.5 rounded-full border border-slate-700"></span>
                        <span class="bg-emerald-500/20 text-emerald-300 text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/40">
                            <i class="fa-solid fa-check"></i> {{ $isAr ? 'مشترك بالنظام' : 'Enrolled' }}
                        </span>
                    </div>
                    <h2 id="modalCourseTitle" class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight"></h2>
                    <p id="modalCourseTeacher" class="text-xs font-mono text-teal-300 flex items-center gap-1.5"></p>
                </div>
                <button type="button" onclick="closeEnrolledCourseModal()"
                    class="w-10 h-10 rounded-full bg-slate-800/80 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center font-bold text-lg transition-all cursor-pointer border border-slate-700 shrink-0 relative z-10"
                    aria-label="{{ __('Close') }}">&times;</button>
            </div>

            {{-- Modal Scrollable Body --}}
            <div class="p-6 sm:p-8 space-y-6 overflow-y-auto custom-scrollbar max-h-[calc(90vh-180px)] font-mono text-slate-800 dark:text-slate-200 flex-1">
                {{-- Overview Card --}}
                <div class="p-5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'نبذة عن الكورس والمحتوى التعليمي' : 'Course Overview & Learning Objectives' }}
                    </h4>
                    <p id="modalCourseDesc" class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed"></p>
                </div>

                {{-- Curriculum Header --}}
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                    <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-tv text-teal-600"></i>
                        <span>{{ $isAr ? 'منهج الكورس والحصص التفصيلية' : 'Full Curriculum & Session Modules' }}</span>
                    </h3>
                    <span id="modalTotalSessionsBadge" class="text-xs font-bold bg-teal-100 dark:bg-teal-950 text-teal-900 dark:text-teal-200 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800"></span>
                </div>

                {{-- Live Streams Subsection --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-teal-800 dark:text-teal-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>
                            <span>{{ $isAr ? 'جدول الحصص والبث المباشر (Live Streams)' : 'Live Stream Schedule' }}</span>
                        </h4>
                        <span id="liveSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                    </div>
                    <div id="modalLiveSessionsList" class="space-y-2.5"></div>
                </div>

                {{-- Recorded Modules Subsection --}}
                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-indigo-800 dark:text-indigo-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-video text-indigo-500"></i>
                            <span>{{ $isAr ? 'دروس الفيديو والواجبات المنهجية (Recorded Modules & MSQs)' : 'Recorded Modules & Assignments' }}</span>
                        </h4>
                        <span id="recSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                    </div>
                    <div id="modalRecordedSessionsList" class="space-y-2.5"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center text-xs font-mono text-slate-500 shrink-0">
                <span><i class="fa-solid fa-graduation-cap"></i> Elite Academy Certified Curriculum</span>
                <button type="button" onclick="closeEnrolledCourseModal()"
                    class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold cursor-pointer transition-all">
                    {{ $isAr ? 'إغلاق النافذة' : 'Close' }}
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- 5. JAVASCRIPT CONTROLLER & REAL-TIME LOGIC --}}
{{-- ========================================================================= --}}
<script>
    const isArLocale = @json($isAr);
    window.enrolledCoursesData = @json($enrolledCoursesDataMap ?? []);

    // ─────────────────────────────────────────────────────────────────────────
    // Master Full-Section Tab Controller (Zero Page Scroll)
    // ─────────────────────────────────────────────────────────────────────────
    let currentMasterTab = @json($activeTab);

    function switchStudentTab(tabKey) {
        if (tabKey === 'packages') {
            tabKey = 'overview';
            setTimeout(() => {
                if (window.openModal) window.openModal('studentOwnPackageModal');
                else {
                    const m = document.getElementById('studentOwnPackageModal');
                    if (m) m.classList.remove('hidden');
                }
            }, 60);
        }

        currentMasterTab = tabKey;
        const validTabs = ['overview', 'sessions', 'courses', 'assignments', 'submissions', 'exceptions', 'notifications'];
        
        validTabs.forEach(key => {
            const pane = document.getElementById(`sectionPane_${key}`);
            if (pane) {
                if (key === tabKey) {
                    pane.classList.remove('hidden');
                } else {
                    pane.classList.add('hidden');
                }
            }
        });

        // Sync and refresh table engines
        if (window.eliteTableInstances) {
            window.eliteTableInstances.forEach(inst => inst.refreshDisplay());
        }

        // Sync sidebar active links
        document.querySelectorAll('.student-nav-item').forEach(item => {
            const itemTab = item.getAttribute('data-tab') || item.getAttribute('href')?.replace('#', '');
            if (itemTab === tabKey) {
                item.classList.add('active', 'bg-teal-950/60', 'text-teal-300');
            } else {
                item.classList.remove('active', 'bg-teal-950/60', 'text-teal-300');
            }
        });

        // Update URL hash smoothly without reloading
        try {
            history.replaceState(null, '', `#tab=${tabKey}`);
        } catch (e) {}

        // Scroll to top of content area if scrolled
        if (window.scrollY > 200) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Close mobile sidebar off-canvas if open
        if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
            togglePortalSidebar(false);
        }
    }
    window.switchStudentTab = switchStudentTab;
    window.switchPortalSection = switchStudentTab; // backward compatibility

    // ─────────────────────────────────────────────────────────────────────────
    // Live Sessions Sub-Tabs Controller & Real-Time Sync
    // ─────────────────────────────────────────────────────────────────────────
    let currentSessionSubTab = 'soon';

    function switchSessionSubTab(subTabKey) {
        currentSessionSubTab = subTabKey;
        ['soon', 'upcoming', 'history'].forEach(key => {
            const pane = document.getElementById(`sessionPane_${key}`);
            const btn = document.getElementById(`sessionSubTab_${key}`);
            const badge = btn ? btn.querySelector('.sub-tab-badge') : null;

            if (key === subTabKey) {
                if (pane) pane.classList.remove('hidden');
                if (btn) {
                    btn.classList.add('bg-white', 'dark:bg-slate-900', 'text-teal-900', 'dark:text-teal-300', 'shadow-sm', 'border', 'border-teal-200/60', 'dark:border-teal-800');
                    btn.classList.remove('text-slate-600', 'dark:text-slate-300', 'hover:bg-white/60');
                }
                if (badge) {
                    badge.classList.add('bg-teal-100', 'dark:bg-teal-950', 'text-teal-900', 'dark:text-teal-200');
                    badge.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-200');
                }
            } else {
                if (pane) pane.classList.add('hidden');
                if (btn) {
                    btn.classList.remove('bg-white', 'dark:bg-slate-900', 'text-teal-900', 'dark:text-teal-300', 'shadow-sm', 'border', 'border-teal-200/60', 'dark:border-teal-800');
                    btn.classList.add('text-slate-600', 'dark:text-slate-300', 'hover:bg-white/60');
                }
                if (badge) {
                    badge.classList.remove('bg-teal-100', 'dark:bg-teal-950', 'text-teal-900', 'dark:text-teal-200');
                    badge.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-700', 'dark:text-slate-200');
                }
            }
        });
    }
    window.switchSessionSubTab = switchSessionSubTab;

    // ─────────────────────────────────────────────────────────────────────────
    // Real-Time Sessions Sync Engine (Automatic live update on create/update/delete)
    // ─────────────────────────────────────────────────────────────────────────
    let studentSessionsHash = @json($sessionsHash ?? '');
    let isSessionsPollingActive = false;

    async function pollStudentSessions(forceImmediate = false) {
        if (isSessionsPollingActive && !forceImmediate) return;
        isSessionsPollingActive = true;

        try {
            const feedUrl = '{{ route('ajax.student.sessions.feed') }}?hash=' + encodeURIComponent(studentSessionsHash);
            const res = await fetch(feedUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                isSessionsPollingActive = false;
                return;
            }

            const data = await res.json();
            if (data && data.success && data.has_changes) {
                const prevHash = studentSessionsHash;
                studentSessionsHash = data.hash;

                // 1. Update Subpane HTML contents
                if (data.panes) {
                    const paneSoon = document.getElementById('sessionPane_soon');
                    const paneUpcoming = document.getElementById('sessionPane_upcoming');
                    const paneHistory = document.getElementById('sessionPane_history');

                    if (paneSoon && data.panes.soon !== undefined) paneSoon.innerHTML = data.panes.soon;
                    if (paneUpcoming && data.panes.upcoming !== undefined) paneUpcoming.innerHTML = data.panes.upcoming;
                    if (paneHistory && data.panes.history !== undefined) paneHistory.innerHTML = data.panes.history;

                    // Ensure user's current subtab remains active and others hidden
                    ['soon', 'upcoming', 'history'].forEach(key => {
                        const pane = document.getElementById(`sessionPane_${key}`);
                        if (pane) {
                            if (key === currentSessionSubTab) {
                                pane.classList.remove('hidden');
                            } else {
                                pane.classList.add('hidden');
                            }
                        }
                    });
                }

                // 2. Update Badge Counts
                if (data.counts) {
                    const badgeSoon = document.querySelector('#sessionSubTab_soon .sub-tab-badge');
                    const badgeUpcoming = document.querySelector('#sessionSubTab_upcoming .sub-tab-badge');
                    const badgeHistory = document.querySelector('#sessionSubTab_history .sub-tab-badge');

                    if (badgeSoon) badgeSoon.textContent = data.counts.soon;
                    if (badgeUpcoming) badgeUpcoming.textContent = data.counts.upcoming;
                    if (badgeHistory) badgeHistory.textContent = data.counts.history;

                    // Live Ping indicator on Sub-tab
                    const subtabPingWrapper = document.querySelector('#sessionSubTab_soon .relative.flex');
                    if (subtabPingWrapper) {
                        if (data.counts.live > 0) {
                            subtabPingWrapper.innerHTML = `
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            `;
                        } else {
                            subtabPingWrapper.innerHTML = `
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-500"></span>
                            `;
                        }
                    }
                }

                // 3. Re-initialize Countdowns
                if (typeof initSessionCountdowns === 'function') {
                    initSessionCountdowns();
                }

                // 4. Re-bind Elite Table Engine for updated session tables
                if (typeof initEliteTables === 'function') {
                    if (window.eliteTableInstances) {
                        window.eliteTableInstances = window.eliteTableInstances.filter(inst => {
                            return !inst.table || !inst.table.classList.contains('session-data-table');
                        });
                    }
                    document.querySelectorAll('table.session-data-table').forEach(tbl => {
                        delete tbl._eliteEngine;
                        if (tbl.parentElement && tbl.parentElement.parentElement) {
                            const oldFooters = tbl.parentElement.parentElement.querySelectorAll('.table-pagination-footer');
                            oldFooters.forEach(f => f.remove());
                        }
                    });
                    initEliteTables();
                }

                // 5. Re-apply any active search filter
                if (typeof filterSessionsTable === 'function') {
                    filterSessionsTable();
                }

                // 6. Flash Live Sync indicator
                const syncPill = document.getElementById('sessionRealtimeStatus');
                if (syncPill) {
                    syncPill.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-100', 'dark:bg-emerald-900');
                    setTimeout(() => {
                        syncPill.classList.remove('ring-2', 'ring-emerald-400', 'bg-emerald-100', 'dark:bg-emerald-900');
                    }, 1200);
                }

                // 7. If this wasn't initial load and a session is live, alert student
                if (prevHash && data.counts && data.counts.live > 0 && window.Toast) {
                    window.Toast.info(
                        isArLocale ? 'هناك حصة تفاعلية نشطة الآن! يمكنك الانضمام مباشرة.' : 'A live stream is active now! You can join directly.',
                        isArLocale ? 'بث مباشر' : 'Live Session'
                    );
                }
            }
        } catch (err) {
            console.debug('[Sessions RealTime] Sync error note:', err);
        } finally {
            isSessionsPollingActive = false;
        }
    }
    window.pollStudentSessions = pollStudentSessions;

    // Automatic Live Poll interval every 4.5s
    setInterval(() => {
        pollStudentSessions(false);
    }, 4500);

    // Immediate sync on tab focus or notification events
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            pollStudentSessions(true);
        }
    });

    window.addEventListener('new-notification-received', () => {
        setTimeout(() => pollStudentSessions(true), 300);
    });

    window.addEventListener('notifications-updated', () => {
        pollStudentSessions(false);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Universal EliteTableEngine: DataTable-Style Column Sorting & Progressive Scroll Loading / "See More..."
    // ─────────────────────────────────────────────────────────────────────────
    class EliteTableEngine {
        constructor(table) {
            this.table = table;
            this.tbody = table.querySelector('tbody');
            this.pageSize = parseInt(table.getAttribute('data-page-size')) || 6;
            this.currentLimit = this.pageSize;
            this.sortColumnIndex = null;
            this.sortDirection = 'asc'; // 'asc' | 'desc'
            this.filterPredicate = null; // optional external filter function

            this.initSorting();
            this.initProgressiveLoading();
        }

        initSorting() {
            const headers = this.table.querySelectorAll('thead th');
            headers.forEach((th, index) => {
                if (th.getAttribute('data-no-sort') === 'true') return;
                th.classList.add('cursor-pointer', 'select-none');
                
                // Ensure sort icon container exists
                let iconWrapper = th.querySelector('.sort-icon');
                if (!iconWrapper) {
                    iconWrapper = document.createElement('span');
                    iconWrapper.className = 'sort-icon opacity-40 text-[10px] ml-1 inline-block';
                    iconWrapper.innerHTML = '<i class="fa-solid fa-sort"></i>';
                    th.appendChild(iconWrapper);
                }

                th.addEventListener('click', () => {
                    const sortType = th.getAttribute('data-sort-type') || 'text';
                    if (this.sortColumnIndex === index) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumnIndex = index;
                        this.sortDirection = 'asc';
                    }
                    this.updateHeaderIcons(headers, index);
                    this.sortTable(index, sortType, this.sortDirection);
                });
            });
        }

        updateHeaderIcons(headers, activeIndex) {
            headers.forEach((th, idx) => {
                const icon = th.querySelector('.sort-icon');
                if (!icon) return;
                if (idx === activeIndex) {
                    icon.classList.remove('opacity-40');
                    icon.classList.add('opacity-100', 'text-teal-600', 'dark:text-teal-400');
                    icon.innerHTML = this.sortDirection === 'asc' 
                        ? '<i class="fa-solid fa-sort-up"></i>' 
                        : '<i class="fa-solid fa-sort-down"></i>';
                } else {
                    icon.classList.remove('opacity-100', 'text-teal-600', 'dark:text-teal-400');
                    icon.classList.add('opacity-40');
                    icon.innerHTML = '<i class="fa-solid fa-sort"></i>';
                }
            });
        }

        getCellValue(row, colIndex) {
            const cell = row.children[colIndex];
            if (!cell) return '';
            return (cell.getAttribute('data-sort-value') || cell.innerText || cell.textContent || '').trim();
        }

        parseValue(val, type) {
            if (type === 'number') {
                const num = parseFloat(val.replace(/[^\d.-]/g, ''));
                return isNaN(num) ? -Infinity : num;
            }
            if (type === 'date') {
                const parsed = Date.parse(val);
                return isNaN(parsed) ? 0 : parsed;
            }
            return val.toLowerCase();
        }

        sortTable(colIndex, sortType, direction) {
            if (!this.tbody) return;
            const rows = Array.from(this.tbody.querySelectorAll('tr'));
            if (rows.length <= 1) return;

            rows.sort((a, b) => {
                const valA = this.parseValue(this.getCellValue(a, colIndex), sortType);
                const valB = this.parseValue(this.getCellValue(b, colIndex), sortType);

                let cmp = 0;
                if (typeof valA === 'number' && typeof valB === 'number') {
                    cmp = valA - valB;
                } else {
                    cmp = String(valA).localeCompare(String(valB), isArLocale ? 'ar' : 'en', { numeric: true, sensitivity: 'base' });
                }
                return direction === 'asc' ? cmp : -cmp;
            });

            // Re-attach sorted rows
            rows.forEach(r => this.tbody.appendChild(r));
            this.refreshDisplay();
        }

        initProgressiveLoading() {
            let container = this.table.closest('.table-responsive');
            if (!container) return;

            const tableId = this.table.id || 'tbl_' + Math.random().toString(36).substr(2, 6);
            this.table.id = tableId;

            let footer = container.parentNode.querySelector(`.elite-table-footer[data-table-id="${tableId}"]`);
            if (!footer) {
                footer = document.createElement('div');
                footer.className = 'elite-table-footer pt-3 pb-1 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-mono text-slate-500';
                footer.setAttribute('data-table-id', tableId);
                footer.innerHTML = `
                    <div class="row-counter flex items-center gap-1.5 font-bold">
                        <span class="counter-text"></span>
                    </div>
                    <div class="load-actions flex items-center gap-2">
                        <button type="button" class="btn-see-more px-4 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl border border-slate-200 dark:border-slate-700 transition-all cursor-pointer inline-flex items-center gap-2 shadow-2xs">
                            <i class="fa-solid fa-angles-down text-teal-600 dark:text-teal-400"></i>
                            <span class="btn-text">${isArLocale ? 'عرض المزيد...' : 'See More...'}</span>
                        </button>
                    </div>
                `;
                container.parentNode.insertBefore(footer, container.nextSibling);

                const seeMoreBtn = footer.querySelector('.btn-see-more');
                if (seeMoreBtn) {
                    seeMoreBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const rows = this.getMatchingRows();
                        if (this.currentLimit < rows.length) {
                            this.currentLimit += this.pageSize;
                            this.refreshDisplay();
                        }
                    });
                }
            }
            this.footerEl = footer;
            this.refreshDisplay();
        }

        getMatchingRows() {
            if (!this.tbody) return [];
            return Array.from(this.tbody.querySelectorAll('tr:not(.empty-state-row)')).filter(r => {
                return !r.classList.contains('filtered-out-by-category');
            });
        }

        refreshDisplay() {
            if (!this.tbody) return;
            const allRows = Array.from(this.tbody.querySelectorAll('tr:not(.empty-state-row)'));
            const matchingRows = this.getMatchingRows();
            const total = matchingRows.length;

            allRows.forEach(row => {
                if (row.classList.contains('filtered-out-by-category')) {
                    row.classList.remove('hidden-by-limit');
                    row.style.display = 'none';
                    return;
                }
                const matchIdx = matchingRows.indexOf(row);
                if (matchIdx !== -1 && matchIdx < this.currentLimit) {
                    row.classList.remove('hidden-by-limit');
                    row.style.display = '';
                } else {
                    row.classList.add('hidden-by-limit');
                    row.style.display = 'none';
                }
            });

            // Handle empty state row inside tbody
            let emptyStateRow = this.tbody.querySelector('.empty-state-row');
            if (total === 0) {
                if (!emptyStateRow) {
                    emptyStateRow = document.createElement('tr');
                    emptyStateRow.className = 'empty-state-row';
                    const colSpan = this.table.querySelectorAll('thead th').length || 6;
                    emptyStateRow.innerHTML = `
                        <td colspan="${colSpan}" class="py-10 text-center text-slate-500 dark:text-slate-400 font-mono text-xs">
                            <div class="flex flex-col items-center justify-center gap-2.5">
                                <span class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xl text-slate-400">
                                    <i class="fa-solid fa-filter-circle-xmark"></i>
                                </span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm">${isArLocale ? 'لا توجد بيانات مطابقة لهذا التصنيف أو المقرر' : 'No records matching the selected filter'}</span>
                                <p class="text-[11px] text-slate-400">${isArLocale ? 'جرب اختيار تصنيف آخر أو اختيار (جميع الكورسات).' : 'Try selecting another course or category filter.'}</p>
                            </div>
                        </td>
                    `;
                    this.tbody.appendChild(emptyStateRow);
                }
                emptyStateRow.style.display = '';
            } else if (emptyStateRow) {
                emptyStateRow.style.display = 'none';
            }

            if (this.footerEl) {
                const shown = Math.min(this.currentLimit, total);
                const counterEl = this.footerEl.querySelector('.counter-text');
                const seeMoreBtn = this.footerEl.querySelector('.btn-see-more');
                const btnText = this.footerEl.querySelector('.btn-text');

                if (counterEl) {
                    if (total === 0) {
                        counterEl.innerHTML = isArLocale ? 'لا توجد سجلات مطابقة' : 'No matching records';
                    } else if (shown >= total) {
                        counterEl.innerHTML = isArLocale 
                            ? `<i class="fa-solid fa-check text-emerald-500"></i> تم عرض كافة السجلات بالكامل (${total} من ${total})` 
                            : `<i class="fa-solid fa-check text-emerald-500"></i> All records loaded (${total} of ${total})`;
                    } else {
                        counterEl.innerHTML = isArLocale 
                            ? `عرض <strong>${shown}</strong> من أصل <strong>${total}</strong> سجل` 
                            : `Showing <strong>${shown}</strong> of <strong>${total}</strong> records`;
                    }
                }

                if (seeMoreBtn) {
                    if (shown >= total) {
                        seeMoreBtn.style.display = 'none';
                        seeMoreBtn.classList.add('hidden');
                    } else {
                        seeMoreBtn.style.display = 'inline-flex';
                        seeMoreBtn.classList.remove('hidden');
                        const remaining = total - shown;
                        const nextChunk = Math.min(this.pageSize, remaining);
                        if (btnText) {
                            btnText.textContent = isArLocale ? `عرض المزيد (${nextChunk} إضافية)...` : `See More (${nextChunk} more)...`;
                        }
                    }
                }

                if (total <= 1 && total > 0) {
                    this.footerEl.style.display = 'none';
                } else {
                    this.footerEl.style.display = '';
                }
            }
        }
    }

    // Initialize all tables on load (singleton pattern per table)
    window.eliteTableInstances = [];
    function initEliteTables() {
        document.querySelectorAll('table.elite-sortable-table').forEach((tbl, i) => {
            if (tbl._eliteEngine) {
                tbl._eliteEngine.refreshDisplay();
                return;
            }
            if (!tbl.id) tbl.id = `elite_table_${i}`;
            const instance = new EliteTableEngine(tbl);
            tbl._eliteEngine = instance;
            window.eliteTableInstances.push(instance);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Live Sessions Instant Table Search Filter
    // ─────────────────────────────────────────────────────────────────────────
    function filterSessionsTable() {
        const query = (document.getElementById('sessionTableSearch')?.value || '').toLowerCase().trim();
        document.querySelectorAll('.session-table-row').forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            if (!query || searchData.includes(query)) {
                row.classList.remove('filtered-out-by-category');
            } else {
                row.classList.add('filtered-out-by-category');
            }
        });
        window.eliteTableInstances.forEach(inst => {
            inst.currentLimit = inst.pageSize;
            inst.refreshDisplay();
        });
    }
    window.filterSessionsTable = filterSessionsTable;

    // ─────────────────────────────────────────────────────────────────────────
    // Filter Assignments By Course & Status
    // ─────────────────────────────────────────────────────────────────────────
    let currentAssignCourseFilter = 'all';
    let currentAssignStatusFilter = 'all';

    function applyAssignmentFilters() {
        document.querySelectorAll('.available-assign-row').forEach(row => {
            const courseId = String(row.getAttribute('data-course-id') || '0');
            const statusCat = String(row.getAttribute('data-status-category') || 'pending');

            const matchCourse = (currentAssignCourseFilter === 'all' || courseId === currentAssignCourseFilter);
            const matchStatus = (currentAssignStatusFilter === 'all' || statusCat === currentAssignStatusFilter);

            if (matchCourse && matchStatus) {
                row.classList.remove('filtered-out-by-category');
            } else {
                row.classList.add('filtered-out-by-category');
            }
        });
        window.eliteTableInstances.forEach(inst => {
            inst.currentLimit = inst.pageSize;
            inst.refreshDisplay();
        });
    }

    function filterAssignmentsByCourse(courseId) {
        currentAssignCourseFilter = String(courseId);
        document.querySelectorAll('.assign-filter-btn').forEach(btn => {
            const isMatch = (btn.getAttribute('data-course') === String(courseId));
            if (isMatch) {
                btn.className = 'assign-filter-btn px-4 py-2 rounded-2xl text-xs font-extrabold font-mono transition-all whitespace-nowrap shrink-0 bg-teal-600 text-white shadow-md shadow-teal-600/30 border border-teal-500 cursor-pointer';
            } else {
                btn.className = 'assign-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer border border-slate-200/80 dark:border-slate-700';
            }
        });
        applyAssignmentFilters();
    }
    window.filterAssignmentsByCourse = filterAssignmentsByCourse;

    function filterAssignmentsByStatus(statusKey) {
        currentAssignStatusFilter = statusKey;
        ['all', 'pending', 'in_progress', 'completed'].forEach(key => {
            const btn = document.getElementById(`assignStatusTab_${key}`);
            if (!btn) return;
            const badge = btn.querySelector('.status-badge');
            if (key === statusKey) {
                btn.className = 'assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-extrabold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-teal-600 text-white shadow-md shadow-teal-600/30 border border-teal-500 whitespace-nowrap';
                if (badge) {
                    badge.className = 'status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 text-white';
                }
            } else {
                btn.className = 'assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 whitespace-nowrap';
                if (badge) {
                    badge.className = 'status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200';
                }
            }
        });
        applyAssignmentFilters();
    }
    window.filterAssignmentsByStatus = filterAssignmentsByStatus;

    // ─────────────────────────────────────────────────────────────────────────
    // Filter Submissions By Course
    // ─────────────────────────────────────────────────────────────────────────
    function filterSubmissionsByCourse(courseId) {
        document.querySelectorAll('.sub-filter-btn').forEach(btn => {
            const isMatch = (btn.getAttribute('data-course') === String(courseId));
            if (isMatch) {
                btn.className = 'sub-filter-btn px-4 py-2 rounded-2xl text-xs font-extrabold font-mono transition-all whitespace-nowrap shrink-0 bg-teal-600 text-white shadow-md shadow-teal-600/30 border border-teal-500 cursor-pointer';
            } else {
                btn.className = 'sub-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer border border-slate-200/80 dark:border-slate-700';
            }
        });
        document.querySelectorAll('.submission-table-row').forEach(row => {
            const rowCourseId = String(row.getAttribute('data-course-id') || '0');
            if (courseId === 'all' || rowCourseId === String(courseId)) {
                row.classList.remove('filtered-out-by-category');
            } else {
                row.classList.add('filtered-out-by-category');
            }
        });
        window.eliteTableInstances.forEach(inst => {
            inst.currentLimit = inst.pageSize;
            inst.refreshDisplay();
        });
    }
    window.filterSubmissionsByCourse = filterSubmissionsByCourse;
    window.filterSubmissionsByCourse = filterSubmissionsByCourse;

    // ─────────────────────────────────────────────────────────────────────────
    // Live Session Countdowns
    // ─────────────────────────────────────────────────────────────────────────
    function initSessionCountdowns() {
        function update() {
            const now = new Date().getTime();
            document.querySelectorAll('.session-countdown-pill').forEach(pill => {
                const startStr = pill.getAttribute('data-start-time');
                const textEl = pill.querySelector('.countdown-text');
                if (!startStr || !textEl) return;

                const startTime = new Date(startStr).getTime();
                const diff = startTime - now;

                if (diff <= 0) {
                    textEl.innerHTML = isArLocale ? 'بدأت الآن <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>' : 'Live Now <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>';
                    return;
                }

                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                let parts = [];
                if (d > 0) parts.push(d + (isArLocale ? 'ي ' : 'd '));
                if (h > 0 || d > 0) parts.push(String(h).padStart(2, '0') + (isArLocale ? 'س ' : 'h '));
                parts.push(String(m).padStart(2, '0') + (isArLocale ? 'د ' : 'm '));
                parts.push(String(s).padStart(2, '0') + (isArLocale ? 'ث' : 's'));

                textEl.textContent = (isArLocale ? 'تبدأ خلال: ' : 'Starts in: ') + parts.join('');
            });
        }
        update();
        setInterval(update, 1000);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MSQ Interactive Modal Solver Logic
    // ─────────────────────────────────────────────────────────────────────────
    let msqTimerInterval = null;

    async function openMsqAssignmentModal(assignmentId) {
        const modal = document.getElementById('takeMsqModal');
        const titleEl = document.getElementById('msqModalTitle');
        const descEl = document.getElementById('msqModalDesc');
        const container = document.getElementById('msqQuestionsContainer');
        const assignIdInput = document.getElementById('msqAssignmentId');

        assignIdInput.value = assignmentId;
        if (window.openModal) window.openModal('takeMsqModal');
        else modal.classList.remove('hidden');

        container.innerHTML = '<div class="text-center py-8 text-slate-500 font-mono text-xs">Loading assignment questions...</div>';

        try {
            const baseUrl = "{{ url('/ajax/assignments') }}";
            const res = await fetch(`${baseUrl}/${assignmentId}/details`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                if (window.Toast) window.Toast.error(data.message || 'Error loading assignment');
                closeMsqModal();
                return;
            }

            const assign = data.assignment;
            titleEl.textContent = assign.title;
            descEl.textContent = assign.description || 'Complete all MSQ questions before the timer runs out.';

            // Setup Timer
            let durationSecs = (assign.duration_minutes || 30) * 60;
            const timerDisplay = document.getElementById('msqTimerDisplay');
            if (msqTimerInterval) clearInterval(msqTimerInterval);

            msqTimerInterval = setInterval(() => {
                if (durationSecs <= 0) {
                    clearInterval(msqTimerInterval);
                    timerDisplay.textContent = 'TIME EXPIRED';
                    timerDisplay.className = 'font-bold text-red-400 animate-pulse';
                    if (window.Toast) window.Toast.warning('Assignment time expired!', 'Deadline Alert');
                    return;
                }
                durationSecs--;
                const mins = Math.floor(durationSecs / 60);
                const secs = durationSecs % 60;
                timerDisplay.textContent = `Time Remaining: ${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }, 1000);

            if (!assign.questions || assign.questions.length === 0) {
                container.innerHTML = '<div class="p-6 text-center text-slate-500 font-mono text-xs bg-slate-50 dark:bg-slate-800 rounded-2xl">No questions configured for this assignment yet.</div>';
                return;
            }

            let html = '';
            assign.questions.forEach((q, idx) => {
                const inputType = q.is_multiple_choice ? 'checkbox' : 'radio';
                html += `
                    <div class="p-5 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-slate-900 dark:text-white">Q${idx + 1}. ${escapeHtml(q.question_text || '')}</span>
                            <span class="text-[10px] font-mono font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md">${q.points || 1} Points</span>
                        </div>
                        ${q.image_path ? `<img src="${q.image_path}" class="max-h-48 rounded-xl border border-slate-200 dark:border-slate-700 my-2 object-contain">` : ''}
                        <div class="space-y-2 pt-1">
                `;

                (q.options || []).forEach(opt => {
                    html += `
                        <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-900 hover:bg-teal-50/50 dark:hover:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 cursor-pointer transition-colors text-xs font-semibold text-slate-800 dark:text-slate-200">
                            <input type="${inputType}" name="answers[${q.id}][]" value="${opt.id}" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                            <span>${escapeHtml(opt.option_text || '')}</span>
                        </label>
                    `;
                });

                html += `</div></div>`;
            });

            container.innerHTML = html;
        } catch (err) {
            if (window.Toast) window.Toast.error('Network error loading assignment');
            closeMsqModal();
        }
    }
    window.openMsqAssignmentModal = openMsqAssignmentModal;

    function closeMsqModal() {
        if (window.closeModal) window.closeModal('takeMsqModal');
        else document.getElementById('takeMsqModal').classList.add('hidden');
        if (msqTimerInterval) clearInterval(msqTimerInterval);
    }
    window.closeMsqModal = closeMsqModal;

    // ─────────────────────────────────────────────────────────────────────────
    // Course Syllabus Explorer Modal
    // ─────────────────────────────────────────────────────────────────────────
    function openEnrolledCourseModal(courseId) {
        const data = window.enrolledCoursesData[courseId];
        if (!data) return;

        document.getElementById('modalCourseTitle').textContent = data.title;
        document.getElementById('modalCourseSubject').textContent = data.subject;
        document.getElementById('modalCourseGrade').textContent = data.grade;
        document.getElementById('modalCourseTeacher').innerHTML = '<i class="fa-solid fa-chalkboard-user"></i> ' + (data.teacher || 'Dr. Instructor');
        document.getElementById('modalCourseDesc').textContent = data.description;

        const liveCount = data.live_sessions ? data.live_sessions.length : 0;
        const recCount = data.recorded_sessions ? data.recorded_sessions.length : 0;
        document.getElementById('modalTotalSessionsBadge').textContent = (liveCount + recCount) + ' Sessions Total';
        document.getElementById('liveSessionsCountBadge').textContent = liveCount + (isArLocale ? ' بث مباشر' : ' streams');
        document.getElementById('recSessionsCountBadge').textContent = recCount + (isArLocale ? ' دروس مسجلة' : ' modules');

        // Render Live Sessions List
        const liveContainer = document.getElementById('modalLiveSessionsList');
        liveContainer.innerHTML = '';
        if (data.live_sessions && data.live_sessions.length > 0) {
            data.live_sessions.forEach(ls => {
                const card = document.createElement('div');
                card.className = 'p-3.5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-2';
                let btnHtml = `<span class="text-[11px] font-bold px-3 py-1 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">${escapeHtml(ls.state_label)}</span>`;
                if (ls.can_join && ls.meeting_link) {
                    btnHtml = `<a href="${escapeHtml(ls.meeting_link)}" target="_blank" class="btn-lift text-[11px] font-bold px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs"><i class="fa-solid fa-circle text-emerald-300 text-[10px]"></i> Join</a>`;
                }
                card.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full ${ls.is_live ? 'bg-emerald-500 animate-ping' : 'bg-slate-400'}"></span>
                        <span class="font-bold text-xs text-slate-900 dark:text-white">${escapeHtml(ls.title)}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500 font-mono"><i class="fa-solid fa-calendar-days"></i> ${escapeHtml(ls.start_at)}</span>
                        ${btnHtml}
                    </div>
                `;
                liveContainer.appendChild(card);
            });
        } else {
            liveContainer.innerHTML = `<div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs text-slate-500 text-center font-mono">${isArLocale ? 'لا توجد جلسات بث مباشر مجدولة لهذا الكورس حالياً.' : 'No live streams currently scheduled.'}</div>`;
        }

        // Render Recorded Modules List
        const recContainer = document.getElementById('modalRecordedSessionsList');
        recContainer.innerHTML = '';
        if (data.recorded_sessions && data.recorded_sessions.length > 0) {
            data.recorded_sessions.forEach(rs => {
                const card = document.createElement('div');
                card.className = 'p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2.5';
                let assignHtml = '';
                if (rs.assignments && rs.assignments.length > 0) {
                    assignHtml = `<div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex flex-wrap items-center gap-2">`;
                    rs.assignments.forEach(a => {
                        assignHtml += `<a href="${escapeHtml(a.url)}" class="btn-lift inline-flex items-center gap-1 text-[11px] font-bold bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800 px-2.5 py-1 rounded-lg"><i class="fa-solid fa-pen-to-square"></i> ${escapeHtml(a.title)} (${a.points} pts) &rarr;</a>`;
                    });
                    assignHtml += `</div>`;
                }
                card.innerHTML = `
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded">Module ${rs.index}</span>
                                <span class="font-bold text-xs text-slate-900 dark:text-white">${escapeHtml(rs.title)}</span>
                            </div>
                            ${rs.description ? `<p class="text-[11px] text-slate-600 dark:text-slate-400">${escapeHtml(rs.description)}</p>` : ''}
                        </div>
                        <span class="text-[11px] text-slate-500 font-bold bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700"><i class="fa-solid fa-stopwatch"></i> ${rs.duration} mins</span>
                    </div>
                    ${assignHtml}
                `;
                recContainer.appendChild(card);
            });
        } else {
            recContainer.innerHTML = `<div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs text-slate-500 text-center font-mono">${isArLocale ? 'لا توجد دروس مسجلة منشورة لهذا الكورس حالياً.' : 'No recorded lessons published yet.'}</div>`;
        }

        if (window.openModal) window.openModal('enrolledCourseModal');
        else document.getElementById('enrolledCourseModal').classList.remove('hidden');
    }
    window.openEnrolledCourseModal = openEnrolledCourseModal;

    function closeEnrolledCourseModal() {
        if (window.closeModal) window.closeModal('enrolledCourseModal');
        else document.getElementById('enrolledCourseModal').classList.add('hidden');
    }
    window.closeEnrolledCourseModal = closeEnrolledCourseModal;

    // ─────────────────────────────────────────────────────────────────────────
    // Excuse / Exception Course Dynamic Dropdown
    // ─────────────────────────────────────────────────────────────────────────
    function onExcuseCourseChange(courseId, selectElId) {
        const selectEl = document.getElementById(selectElId);
        if (!selectEl) return;
        selectEl.innerHTML = '';

        if (!courseId) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = isArLocale ? '-- اختر الكورس أولاً لعرض الحصص --' : '-- Select Course first --';
            selectEl.appendChild(opt);
            return;
        }

        const courseData = window.enrolledCoursesData[courseId];
        if (!courseData) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = isArLocale ? 'كافة حصص المقرر' : 'All Course Sessions';
            selectEl.appendChild(opt);
            return;
        }

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = isArLocale ? '-- اختر الحصة الدراسية المحددة --' : '-- Select Specific Session --';
        selectEl.appendChild(defaultOpt);

        let hasSessions = false;
        if (courseData.live_sessions && courseData.live_sessions.length > 0) {
            const liveGroup = document.createElement('optgroup');
            liveGroup.label = isArLocale ? 'الحصص المباشرة (Live Sessions)' : 'Live Sessions';
            courseData.live_sessions.forEach(ls => {
                const opt = document.createElement('option');
                opt.value = ls.id;
                opt.textContent = `${ls.title} (${ls.start_at || 'Scheduled'})`;
                liveGroup.appendChild(opt);
                hasSessions = true;
            });
            selectEl.appendChild(liveGroup);
        }

        if (courseData.recorded_sessions && courseData.recorded_sessions.length > 0) {
            const recGroup = document.createElement('optgroup');
            recGroup.label = isArLocale ? 'الدروس المسجلة للمقرر' : 'Curriculum Lessons';
            courseData.recorded_sessions.forEach(cs => {
                const opt = document.createElement('option');
                opt.value = cs.id;
                opt.textContent = `${cs.title} (${cs.duration} min)`;
                recGroup.appendChild(opt);
                hasSessions = true;
            });
            selectEl.appendChild(recGroup);
        }

        if (!hasSessions) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = isArLocale ? 'استثناء عام للمقرر (لا توجد حصص مجدولة)' : 'General Course Exception';
            selectEl.appendChild(opt);
        }
    }
    window.onExcuseCourseChange = onExcuseCourseChange;

    // ─────────────────────────────────────────────────────────────────────────
    // Real-Time AJAX Notifications Pagination
    // ─────────────────────────────────────────────────────────────────────────
    let notifCurrentPage = {{ $notifCurrentPage }};
    let notifLastPage = {{ $notifLastPage }};

    function updatePaginationControls(page, lastPage, total) {
        notifCurrentPage = page;
        notifLastPage = lastPage;

        const btnPrev = document.getElementById('btnNotifPrev');
        const btnNext = document.getElementById('btnNotifNext');
        const currText = document.getElementById('notifCurrentPageText');
        const lastText = document.getElementById('notifLastPageText');
        const totalAlerts = document.getElementById('notifTotalAlerts');
        const pagBar = document.getElementById('notificationsPaginationBar');

        if (currText) currText.textContent = page;
        if (lastText) lastText.textContent = lastPage;
        if (totalAlerts) totalAlerts.textContent = `${total} Alerts`;

        if (btnPrev) btnPrev.disabled = (page <= 1);
        if (btnNext) btnNext.disabled = (page >= lastPage);

        if (pagBar) {
            if (lastPage <= 1) pagBar.classList.add('hidden');
            else pagBar.classList.remove('hidden');
        }
    }

    async function fetchNotificationsPage(page) {
        if (page < 1 || (notifLastPage && page > notifLastPage)) return;
        const container = document.getElementById('notificationsFeedContainer');
        if (container) container.classList.add('opacity-40');

        try {
            const res = await fetch(`{{ route('ajax.notifications.feed') }}?page=${page}&per_page=5`);
            const data = await res.json();
            if (!data.success) {
                if (container) container.classList.remove('opacity-40');
                return;
            }

            if (container && data.notifications) {
                container.innerHTML = '';
                data.notifications.forEach(n => {
                    const card = document.createElement('div');
                    card.className = 'p-4 bg-slate-50/90 dark:bg-slate-800/80 hover:bg-slate-100/90 dark:hover:bg-slate-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 space-y-1.5 shadow-2xs transition-all';
                    let badgeHtml = '<span class="text-teal-800 dark:text-teal-300 bg-teal-100/90 dark:bg-teal-950/60 px-2.5 py-0.5 rounded-md border border-teal-200 dark:border-teal-800"><i class="fa-solid fa-bell"></i> FCM Alert</span>';
                    if (n.type === 'ASSIGNMENT_DEADLINE_REMINDER') {
                        badgeHtml = '<span class="text-amber-800 dark:text-amber-300 bg-amber-100/90 dark:bg-amber-950/60 px-2.5 py-0.5 rounded-md border border-amber-200 dark:border-amber-800"><i class="fa-solid fa-clock"></i> Deadline 24h</span>';
                    }
                    card.innerHTML = `
                        <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                            ${badgeHtml}
                            <span class="text-slate-400 font-normal">${n.created_at ? formatTimeAgo(n.created_at) : 'Just now'}</span>
                        </div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white leading-snug">${escapeHtml(n.title)}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-mono">${escapeHtml(n.body)}</p>
                    `;
                    container.appendChild(card);
                });
                container.classList.remove('opacity-40');
            }
            if (data.pagination) {
                updatePaginationControls(data.pagination.current_page, data.pagination.last_page, data.pagination.total);
            }
        } catch (err) {
            if (container) container.classList.remove('opacity-40');
        }
    }
    window.fetchNotificationsPage = fetchNotificationsPage;

    function formatTimeAgo(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        const diffHours = Math.floor(diffMins / 60);
        if (diffHours < 24) return `${diffHours}h ago`;
        return `${Math.floor(diffHours / 24)}d ago`;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Real-Time Notification Prepend Listener
    // ─────────────────────────────────────────────────────────────────────────
    window.addEventListener('new-notification-received', function (e) {
        const n = e.detail;
        const container = document.getElementById('notificationsFeedContainer');
        if (!container) return;

        const card = document.createElement('div');
        card.className = 'p-4 bg-teal-50 dark:bg-teal-950/80 rounded-2xl border-2 border-teal-500 space-y-1.5 shadow-lg animate-pulse';
        card.innerHTML = `
            <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                <span class="text-teal-800 dark:text-teal-300 bg-teal-100 dark:bg-teal-900 px-2.5 py-0.5 rounded-md border border-teal-300"><i class="fa-solid fa-bolt text-teal-600"></i> New Alert</span>
                <span class="text-teal-600 font-bold">${isArLocale ? 'الآن' : 'Just now'}</span>
            </div>
            <h4 class="font-bold text-xs text-slate-900 dark:text-white leading-snug">${escapeHtml(n.title)}</h4>
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-mono">${escapeHtml(n.body)}</p>
        `;
        container.prepend(card);
        setTimeout(() => card.classList.remove('animate-pulse'), 4000);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // Forms AJAX Submission Listeners
    // ─────────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initSessionCountdowns();
        initEliteTables();

        // Check URL hash on page load (e.g. #tab=sessions or #sessions)
        const hash = window.location.hash.replace('#', '').replace('tab=', '');
        if (hash && ['overview', 'sessions', 'courses', 'assignments', 'submissions', 'packages', 'exceptions', 'notifications'].includes(hash)) {
            switchStudentTab(hash);
        }

        // Initialize excuse dropdowns if course pre-selected
        const excuseCourse = document.getElementById('excuseCourseSelect');
        if (excuseCourse && excuseCourse.value) onExcuseCourseChange(excuseCourse.value, 'excuseSessionSelect');

        const hwCourse = document.getElementById('hwExceptionCourseSelect');
        if (hwCourse && hwCourse.value) onExcuseCourseChange(hwCourse.value, 'hwExceptionSessionSelect');

        // Excuse Form Submit
        const excuseForm = document.getElementById('excuseForm');
        const excuseAlert = document.getElementById('excuseAlert');
        const excuseSubmitBtn = document.getElementById('excuseSubmitBtn') || (excuseForm ? excuseForm.querySelector('button[type="submit"]') : null);

        if (excuseForm && excuseSubmitBtn) {
            excuseForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (excuseSubmitBtn.disabled) return;

                excuseAlert.classList.add('hidden');
                const originalBtnHtml = excuseSubmitBtn.innerHTML;

                // Loading State & anti-multiple click
                excuseSubmitBtn.disabled = true;
                excuseSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                excuseSubmitBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> <span>${isArLocale ? 'جاري إرسال الطلب...' : 'Submitting Request...'}</span>`;

                try {
                    const res = await fetch(excuseForm.action, {
                        method: 'POST',
                        body: new FormData(excuseForm),
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        excuseAlert.textContent = data.message;
                        excuseAlert.classList.remove('hidden');
                        excuseSubmitBtn.innerHTML = `<i class="fa-solid fa-check"></i> <span>${isArLocale ? 'تم الإرسال بنجاح' : 'Submitted Successfully'}</span>`;
                        if (window.Toast) window.Toast.success(data.message, isArLocale ? 'تم إرسال العذر' : 'Success');
                        setTimeout(() => {
                            if (window.closeModal) window.closeModal('excuseModal');
                            else document.getElementById('excuseModal').classList.add('hidden');
                            window.location.reload();
                        }, 1200);
                    } else {
                        const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat()[0] : (isArLocale ? 'تعذر إرسال الطلب، يرجى المحاولة مرة أخرى.' : 'Submission failed. Please try again.'));
                        excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                        excuseAlert.textContent = errorMsg;
                        excuseAlert.classList.remove('hidden');
                        if (window.Toast) window.Toast.error(errorMsg, isArLocale ? 'تنبيه' : 'Submission Error');

                        // Restore button
                        excuseSubmitBtn.disabled = false;
                        excuseSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        excuseSubmitBtn.innerHTML = originalBtnHtml;
                    }
                } catch (err) {
                    const errorMsg = isArLocale ? 'خطأ في الاتصال بالشبكة. يرجى إعادة المحاولة.' : 'Network error. Please try again.';
                    excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                    excuseAlert.textContent = errorMsg;
                    excuseAlert.classList.remove('hidden');
                    if (window.Toast) window.Toast.error(errorMsg, isArLocale ? 'خطأ في الاتصال' : 'Network Error');

                    // Restore button
                    excuseSubmitBtn.disabled = false;
                    excuseSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    excuseSubmitBtn.innerHTML = originalBtnHtml;
                }
            });
        }

        // Homework Exception Form Submit
        const hwForm = document.getElementById('hwExceptionForm');
        const hwAlert = document.getElementById('hwExceptionAlert');
        const hwSubmitBtn = document.getElementById('hwExceptionSubmitBtn') || (hwForm ? hwForm.querySelector('button[type="submit"]') : null);

        if (hwForm && hwSubmitBtn) {
            hwForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (hwSubmitBtn.disabled) return;

                hwAlert.classList.add('hidden');
                const originalBtnHtml = hwSubmitBtn.innerHTML;

                // Loading State & anti-multiple click
                hwSubmitBtn.disabled = true;
                hwSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                hwSubmitBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> <span>${isArLocale ? 'جاري إرسال طلب الاستثناء...' : 'Submitting Exception...'}</span>`;

                try {
                    const res = await fetch(hwForm.action, {
                        method: 'POST',
                        body: new FormData(hwForm),
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200';
                        hwAlert.textContent = data.message;
                        hwAlert.classList.remove('hidden');
                        hwSubmitBtn.innerHTML = `<i class="fa-solid fa-check"></i> <span>${isArLocale ? 'تم الإرسال بنجاح' : 'Submitted Successfully'}</span>`;
                        if (window.Toast) window.Toast.success(data.message, isArLocale ? 'تم إرسال الطلب' : 'Success');
                        setTimeout(() => {
                            if (window.closeModal) window.closeModal('homeworkExceptionModal');
                            else document.getElementById('homeworkExceptionModal').classList.add('hidden');
                            window.location.reload();
                        }, 1200);
                    } else {
                        const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat()[0] : (isArLocale ? 'تعذر إرسال طلب الاستثناء، يرجى المحاولة مرة أخرى.' : 'Submission failed. Please try again.'));
                        hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                        hwAlert.textContent = errorMsg;
                        hwAlert.classList.remove('hidden');
                        if (window.Toast) window.Toast.error(errorMsg, isArLocale ? 'تنبيه' : 'Submission Error');

                        // Restore button
                        hwSubmitBtn.disabled = false;
                        hwSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        hwSubmitBtn.innerHTML = originalBtnHtml;
                    }
                } catch (err) {
                    const errorMsg = isArLocale ? 'خطأ في الاتصال بالشبكة. يرجى إعادة المحاولة.' : 'Network error. Please try again.';
                    hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                    hwAlert.textContent = errorMsg;
                    hwAlert.classList.remove('hidden');
                    if (window.Toast) window.Toast.error(errorMsg, isArLocale ? 'خطأ في الاتصال' : 'Network Error');

                    // Restore button
                    hwSubmitBtn.disabled = false;
                    hwSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    hwSubmitBtn.innerHTML = originalBtnHtml;
                }
            });
        }

        // MSQ Solver Form Submit
        const msqForm = document.getElementById('msqAnswerForm');
        const msqSubmitBtn = document.getElementById('msqSubmitBtn');
        if (msqForm) {
            msqForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                msqSubmitBtn.disabled = true;
                msqSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');

                try {
                    const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                        method: 'POST',
                        body: new FormData(msqForm),
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        if (window.Toast) window.Toast.error(data.message || 'Submission failed');
                        msqSubmitBtn.disabled = false;
                        msqSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        return;
                    }

                    if (window.Toast) {
                        if (data.is_passed) {
                            window.Toast.success(`Score: ${data.percentage}% (PASSED)`, 'Assignment Evaluated');
                        } else {
                            window.Toast.error(`Score: ${data.percentage}% (FAILED - Passing: ${data.passing_score}%)`, 'Assignment Result');
                        }
                    }

                    closeMsqModal();
                    setTimeout(() => window.location.reload(), 1200);
                } catch (err) {
                    if (window.Toast) window.Toast.error('Network error submitting assignment');
                    msqSubmitBtn.disabled = false;
                    msqSubmitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            });
        }
    });
</script>
@endsection