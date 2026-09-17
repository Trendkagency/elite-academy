@extends('layouts.portal-panel')

@section('content')
    @php
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';
        $todayDateStr = \Carbon\Carbon::today()->format('l, F j, Y');
        $activeTabKey = in_array($activeTab ?? 'overview', [
            'overview',
            'sessions',
            'assignments',
            'attendance',
            'students',
            'notifications',
            'schedules',
            'calendar',
            'exceptions',
        ])
            ? $activeTab ?? 'overview'
            : 'overview';
    @endphp

    <div class="space-y-8" id="teacher-portal-root" data-initial-student="{{ $initialStudentId ?? '' }}">

        {{-- Executive Header & Faculty Greeting Banner --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-teal-950 dark:from-slate-950 dark:via-slate-900 dark:to-teal-950 rounded-3xl p-6 sm:p-8 lg:p-10 text-white shadow-2xl border border-slate-700/60 dark:border-slate-800/80">
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">

                {{-- Left Side: Teacher Greeting & Meta Badges --}}
                <div class="lg:col-span-7 space-y-4">
                    {{-- Status Badges Cluster --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-teal-500/15 text-teal-300 border border-teal-500/30 backdrop-blur-md shadow-2xs">
                            <i class="fa-solid fa-chalkboard-user text-teal-400"></i>
                            <span>{{ $teacherProfile->title ?: __('Faculty Instructor') }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 backdrop-blur-md shadow-2xs">
                            <i class="fa-solid fa-star text-amber-400"></i>
                            <span>{{ number_format($teacherProfile->rating_avg ?: 5.0, 1) }} {{ __('Rating') }}</span>
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 backdrop-blur-md shadow-2xs">
                            <i class="fa-solid fa-book-open text-emerald-400"></i>
                            <span>{{ $courses->count() }} {{ __('Active Courses') }}</span>
                        </span>
                    </div>

                    {{-- Title & Avatar Combo --}}
                    <div class="flex items-center gap-3.5 sm:gap-4">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-teal-500 via-emerald-400 to-amber-400 p-0.5 shadow-xl shadow-teal-500/20 shrink-0">
                            <div
                                class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center text-teal-300 font-heading font-black text-lg sm:text-2xl border border-teal-400/30">
                                {{ mb_substr(auth()->user()->name ?? 'D', 0, 1) }}
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h1
                                class="font-heading text-xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-snug truncate">
                                {{ __('Welcome back') }}, <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 via-emerald-300 to-amber-300">{{ auth()->user()->name }}</span>
                            </h1>
                            <p class="text-slate-300 text-xs sm:text-sm font-normal leading-relaxed mt-0.5 line-clamp-2">
                                {{ __('Manage educational cohorts, monitor individual student performance, review homework submissions, and track attendance records.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Integrated Command Hub Card --}}
                <div
                    class="lg:col-span-5 bg-white/5 dark:bg-slate-900/60 p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-800 backdrop-blur-xl shadow-xl space-y-3">
                    <div class="flex items-center justify-between pb-1 border-b border-white/10 dark:border-slate-800">
                        <span
                            class="text-xs font-mono font-bold uppercase tracking-wider text-teal-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-amber-400"></i>
                            <span>{{ $isAr ? 'لوحة التحكم والإجراءات السريعة' : 'Quick Actions' }}</span>
                        </span>
                        <span class="text-[10px] font-mono text-slate-400">{{ $todayDateStr }}</span>
                    </div>

                    {{-- Primary Featured Button: Preview & Guide --}}
                    <button type="button" onclick="openTeacherPreviewModal()"
                        class="btn-lift w-full py-3 px-4 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm rounded-xl sm:rounded-2xl shadow-lg shadow-amber-500/25 flex items-center justify-between transition-all cursor-pointer group">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-sm animate-pulse"></i>
                            <span>{{ $isAr ? 'دليل استخدام المنصة والتوضيح' : 'Platform Guide & Preview' }}</span>
                        </span>
                        <i
                            class="fa-solid fa-arrow-left rtl:rotate-180 text-xs group-hover:-translate-x-1 transition-transform"></i>
                    </button>

                    {{-- 2x2 Action Tiles Grid --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="openCreateSessionModal()"
                            class="btn-lift py-2.5 px-3 bg-gradient-to-r from-teal-500/20 to-emerald-500/20 hover:from-teal-500/30 hover:to-emerald-500/30 text-teal-200 border border-teal-500/30 rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-plus text-teal-400 text-xs"></i>
                            <span class="truncate">{{ __('Schedule Session') }}</span>
                        </button>

                        <button type="button" onclick="openCreateAssignmentModal()"
                            class="btn-lift py-2.5 px-3 bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white border border-white/15 rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-emerald-400 text-xs"></i>
                            <span class="truncate">{{ __('Publish Assignment') }}</span>
                        </button>

                        <button type="button" onclick="switchTeacherTab('schedules')"
                            class="btn-lift py-2.5 px-3 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 hover:text-white border border-slate-700/80 rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-calendar-days text-indigo-400 text-xs"></i>
                            <span class="truncate">{{ __('Schedules') }}</span>
                        </button>

                        <button type="button" onclick="switchTeacherTab('students')"
                            class="btn-lift py-2.5 px-3 bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 hover:text-white border border-slate-700/80 rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-graduation-cap text-teal-400 text-xs"></i>
                            <span class="truncate">{{ __('My Students') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Ambient Glow Orbs --}}
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-teal-500/15 rounded-full blur-3xl pointer-events-none">
            </div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
        </div>

        {{-- Toast Alert Container --}}
        <div id="teacherToastAlert"
            class="hidden p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md"></div>

        {{-- KPI Statistics Grid (Responsive: 2-cols mobile, 3-cols tablet, 6-cols desktop) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-4">
            {{-- KPI 1: Today Sessions --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] group">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('Today Sessions') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-teal-50 dark:bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-calendar-days"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 dark:text-teal-400 js-counter"
                        data-target="{{ $todaySessionsCount }}">0</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ __('Scheduled today') }}</p>
                </div>
            </div>

            {{-- KPI 2: Upcoming --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] group">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('Upcoming') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-blue-50 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-blue-600 dark:text-blue-400 js-counter"
                        data-target="{{ $upcomingSessionsCount }}">0</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ __('Future cohorts') }}</p>
                </div>
            </div>

            {{-- KPI 3: Students --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-teal-500/60 dark:hover:border-teal-500/60 transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] cursor-pointer group"
                onclick="switchTeacherTab('students')">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('My Students') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white js-counter"
                        data-target="{{ $assignedStudentsCount }}">0</p>
                    <p
                        class="text-[10px] sm:text-[11px] text-teal-600 dark:text-teal-400 font-bold flex items-center gap-1 group-hover:underline truncate">
                        {{ __('View roster →') }}</p>
                </div>
            </div>

            {{-- KPI 4: Pending Assignments --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-amber-500/60 dark:hover:border-amber-500/60 transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] cursor-pointer group"
                onclick="switchTeacherTab('assignments')">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('Need Grading') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-amber-500 dark:text-amber-400 js-counter"
                        data-target="{{ $pendingAssignmentsCount }}">0</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ __('Submissions queue') }}</p>
                </div>
            </div>

            {{-- KPI 5: Total Submissions --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] group">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('Submissions') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-purple-50 dark:bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-chart-column"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 dark:text-teal-400 js-counter"
                        data-target="{{ $submittedAssignmentsCount }}">0</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ __('Total handled') }}</p>
                </div>
            </div>

            {{-- KPI 6: Attendance Rate --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between min-h-[114px] sm:min-h-[128px] group">
                <div class="flex items-center justify-between gap-1">
                    <span
                        class="text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ __('Attendance Rate') }}</span>
                    <span
                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <div>
                    <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600 dark:text-emerald-400"><span
                            class="js-counter" data-target="{{ $attendanceRate }}">0</span>%</p>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ __('Historical sessions') }}</p>
                </div>
            </div>
        </div>





        {{-- TAB 1: OVERVIEW & TODAY'S SESSIONS                                       --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-overview"
            class="teacher-tab-content {{ $activeTabKey === 'overview' ? '' : 'hidden' }} space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Today's Live Sessions Card --}}
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                        <div
                            class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div>
                                <h2
                                    class="font-heading text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                                    <span><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i></span>
                                    {{ __('Today\'s Teaching Sessions') }}
                                </h2>
                                <p class="text-xs font-mono text-slate-500 mt-1">{{ $todayDateStr }}</p>
                            </div>
                            <span
                                class="px-3 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200">
                                {{ $todaySessions->count() }} {{ __('Sessions Today') }}
                            </span>
                        </div>

                        @if ($todaySessions->count() > 0)
                            <div id="todaySessionsListContainer"
                                class="space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach ($todaySessions as $session)
                                    <div
                                        class="today-session-item p-5 rounded-2xl bg-[#FAFAF9] dark:bg-slate-800/60 border border-slate-200/90 dark:border-slate-800 hover:border-teal-400 dark:hover:border-teal-500 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="space-y-1.5 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="px-2.5 py-0.5 text-[10px] font-mono font-extrabold uppercase rounded-full bg-teal-100 text-teal-800 dark:bg-teal-950/70 dark:text-teal-300 dark:border dark:border-teal-800/60">
                                                    {{ $session->course?->title ?: __('Course Session') }}
                                                </span>
                                                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                                    <i class="fa-solid fa-stopwatch"></i>
                                                    {{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : __('Scheduled') }}
                                                    ({{ $session->duration_minutes }}m)
                                                </span>
                                            </div>
                                            <h3
                                                class="font-heading font-extrabold text-base text-slate-900 dark:text-white truncate">
                                                {{ $session->title ?: __('Interactive Teaching Session') }}
                                            </h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                                {{ __('Cohort') }}:
                                                {{ $session->subject?->name ?: __('General Curriculum') }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                                            @if ($session->meeting_link)
                                                <a href="{{ $session->meeting_link }}" target="_blank"
                                                    class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                                                    <span><i class="fa-solid fa-video"></i></span>
                                                    {{ __('Join / Broadcast') }}
                                                </a>
                                            @else
                                                <button type="button" data-session-id="{{ $session->id }}"
                                                    data-meeting-link="{{ $session->meeting_link ?? '' }}"
                                                    onclick="openMeetingLinkModal({{ $session->id }}, this)"
                                                    class="btn-lift px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                                                    <i class="fa-solid fa-link"></i> {{ __('Add Link') }}
                                                </button>
                                            @endif
                                            <button type="button" data-session-id="{{ $session->id }}"
                                                data-session-title="{{ $session->title ?: __('Live Session') }}"
                                                onclick="openAttendanceModal({{ $session->id }}, this)"
                                                class="btn-lift px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200 cursor-pointer">
                                                <i class="fa-solid fa-clipboard-list"></i> {{ __('Attendance') }}
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="todaySessionsPagination" class="mt-4"></div>
                        @else
                            <div
                                class="text-center py-12 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 space-y-3">
                                <span class="text-3xl"><i class="fa-solid fa-mug-hot"></i></span>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    {{ __('No teaching sessions scheduled for today.') }}</p>
                                <button type="button" onclick="openCreateSessionModal()"
                                    class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline">
                                    + {{ __('Schedule a live session now') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Pending Grading Queue --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                        <div
                            class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h2
                                class="font-heading text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span><i class="fa-solid fa-pen-to-square"></i></span> {{ __('Pending Grading Queue') }}
                            </h2>
                            <span
                                class="px-3 py-1 bg-orange-50 text-orange-700 font-mono text-xs font-bold rounded-full border border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800/60">
                                {{ $pendingSubmissions->count() }} {{ __('Needs Review') }}
                            </span>
                        </div>

                        @if ($pendingSubmissions->count() > 0)
                            <div id="pendingSubmissionsContainer"
                                class="divide-y divide-slate-100 dark:divide-slate-800 max-h-[450px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach ($pendingSubmissions as $sub)
                                    <div class="pending-sub-item py-3.5 flex items-center justify-between gap-4">
                                        <div class="space-y-1 min-w-0">
                                            <h4
                                                class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                                                {{ $sub->studentUser?->name ?: __('Student') }}
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                {{ $sub->assignment?->title ?: __('Assignment') }} • <span
                                                    class="font-mono text-[11px]">{{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}</span>
                                            </p>
                                        </div>
                                        <button type="button" data-submission-id="{{ $sub->id }}"
                                            data-student-name="{{ $sub->studentUser?->name ?: __('Student') }}"
                                            data-assignment-title="{{ $sub->assignment?->title ?: __('Assignment') }}"
                                            data-score="{{ $sub->score }}"
                                            data-evaluation-notes="{{ $sub->evaluation_notes }}"
                                            onclick="openGradeModal({{ $sub->id }}, this)"
                                            class="btn-lift px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs shrink-0 cursor-pointer">
                                            <i class="fa-solid fa-pen-nib"></i> {{ __('Grade Now') }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <div id="pendingSubmissionsPagination" class="mt-4"></div>
                        @else
                            <div
                                class="text-center py-8 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    {{ __('Great job! All student homework submissions are graded.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar Overview Column: Quick Student Roster Preview & Courses --}}
                <div class="space-y-6">
                    {{-- Quick Students Roster Preview --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                        <div
                            class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h3 class="font-heading font-black text-lg text-slate-900">{{ __('Recent Students') }}</h3>
                            <button type="button" onclick="switchTeacherTab('students')"
                                class="text-xs font-bold text-teal-600 hover:underline">
                                {{ __('View All') }} &rarr;
                            </button>
                        </div>

                        @if ($assignedStudents->count() > 0)
                            <div id="recentStudentsContainer"
                                class="space-y-3 max-h-[380px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach ($assignedStudents as $st)
                                    <div class="recent-student-item p-3 rounded-2xl bg-[#FAFAF9] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 hover:border-teal-400 dark:hover:border-teal-500 transition-all cursor-pointer"
                                        onclick="openStudentDetailsModal({{ $st->user_id }})">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-black text-xs flex items-center justify-center shrink-0">
                                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-900 truncate">
                                                    {{ $st->user?->name }}</p>
                                                <p class="text-[10px] font-mono text-slate-500 truncate">
                                                    {{ $st->gradeLevel?->name ?: __('Secondary') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-end shrink-0 font-mono text-[11px]">
                                            <span class="font-bold text-emerald-600">{{ $st->attendance_rate }}%</span>
                                            <span class="block text-[9px] text-slate-400">{{ __('Att.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="recentStudentsPagination" class="mt-3"></div>
                        @else
                            <p class="text-xs text-slate-400 text-center py-4">{{ __('No students assigned yet.') }}</p>
                        @endif
                    </div>

                    {{-- Active Teaching Courses --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                        <h3
                            class="font-heading font-black text-lg text-slate-900 border-b border-slate-100 dark:border-slate-800 pb-3">
                            {{ __('Your Active Courses') }}</h3>
                        @if ($courses->count() > 0)
                            <div id="overviewCoursesListContainer"
                                class="space-y-2.5 max-h-[380px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach ($courses as $c)
                                    @php
                                        $sessionCount = $c->sessions->count();
                                        $sessionLabel = $isAr
                                            ? ($sessionCount == 1
                                                ? 'حصة'
                                                : ($sessionCount == 2
                                                    ? 'حصتان'
                                                    : ($sessionCount <= 10
                                                        ? 'حصص'
                                                        : 'حصة')))
                                            : ($sessionCount == 1
                                                ? 'Session'
                                                : 'Sessions');
                                    @endphp
                                    <div
                                        class="overview-course-item p-3.5 rounded-2xl bg-[#FAFAF9] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 hover:border-teal-400 dark:hover:border-teal-500 hover:bg-white dark:hover:bg-slate-800 transition-all flex items-center justify-between gap-3 group">
                                        <div class="min-w-0 flex-1 space-y-1">
                                            <h4
                                                class="font-heading font-black text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-teal-700 transition-colors">
                                                {{ $c->title }}
                                            </h4>
                                            <p class="text-[11px] font-mono text-slate-500 truncate">
                                                {{ $c->subject?->name }} • {{ $c->gradeLevel?->name }}
                                            </p>
                                        </div>
                                        <div class="shrink-0">
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-teal-800 border border-teal-200/80 text-xs font-mono font-extrabold rounded-xl shadow-2xs whitespace-nowrap">
                                                <span><i class="fa-solid fa-book-open"></i></span> {{ $sessionCount }}
                                                {{ $sessionLabel }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="overviewCoursesPagination" class="mt-3"></div>
                        @else
                            <p class="text-xs text-slate-400 text-center py-4">{{ __('No active courses configured.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 2: MY STUDENTS (DEDICATED ROSTER & SEARCH)                          --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-students"
            class="teacher-tab-content {{ $activeTabKey === 'students' ? '' : 'hidden' }} space-y-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                {{-- Tab Header --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-graduation-cap"></i></span> {{ __('app.teacher.my_students') }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ __('Search, filter, and inspect detailed educational profiles for all enrolled learners in your courses.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="px-3.5 py-1.5 bg-teal-50 text-teal-800 text-xs font-mono font-bold rounded-xl border border-teal-200">
                            {{ $assignedStudents->count() }} {{ __('Enrolled Students') }}
                        </span>
                    </div>
                </div>

                {{-- Multi-Faceted Student Filter & Search Bar --}}
                <div
                    class="p-4 bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        {{-- Live Search Input --}}
                        <div class="lg:col-span-2 relative">
                            <input type="text" id="studentSearchInput"
                                placeholder="{{ __('app.teacher.search_placeholder') }}"
                                class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pe-8">
                            <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs"><i
                                    class="fa-solid fa-magnifying-glass"></i></span>
                        </div>

                        {{-- Filter by Course --}}
                        <div>
                            <select id="studentCourseFilter"
                                class="elite-custom-select w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                <option value="">{{ __('app.teacher.all_courses') }}</option>
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter by Grade Level --}}
                        <div>
                            <select id="studentGradeFilter"
                                class="elite-custom-select w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                <option value="">{{ __('app.teacher.all_grades') }}</option>
                                @foreach ($gradeLevels ?? [] as $gl)
                                    <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter by Attendance Health --}}
                        <div>
                            <select id="studentAttendanceFilter"
                                class="elite-custom-select w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                <option value="">{{ __('app.teacher.all_attendance') }}</option>
                                <option value="good">{{ __('app.teacher.attendance_good') }}</option>
                                <option value="risk">{{ __('app.teacher.attendance_risk') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Results Count & Active Filter Reset --}}
                    <div class="flex items-center justify-between text-xs font-mono text-slate-500 pt-1">
                        <span id="studentFilterCountText">{{ __('Showing all assigned students') }}</span>
                        <button type="button" onclick="resetStudentFilters()"
                            class="text-teal-600 font-bold hover:underline cursor-pointer">
                            ↺ {{ __('Reset Filters') }}
                        </button>
                    </div>
                </div>

                {{-- Students Roster Cards Grid --}}
                @if ($assignedStudents->count() > 0)
                    <div id="studentsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($assignedStudents as $st)
                            @php
                                $courseIdsList = implode(',', $st->enrolled_course_ids ?? []);
                                $gradeId = $st->grade_level_id ?? 0;
                                $attRate = (int) ($st->attendance_rate ?? 100);
                                $avgSc = $st->avg_score !== null ? (float) $st->avg_score : null;
                                $studentCode = 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT);
                            @endphp
                            <div class="student-roster-card bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl p-5 border border-slate-200/90 dark:border-slate-800 hover:border-teal-500 dark:hover:border-teal-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-4"
                                data-name="{{ strtolower($st->user?->name ?: '') }}"
                                data-code="{{ strtolower($studentCode) }}"
                                data-school="{{ strtolower($st->school_name ?: '') }}"
                                data-courses="{{ $courseIdsList }}" data-grade="{{ $gradeId }}"
                                data-attendance="{{ $attRate }}"
                                data-score="{{ $avgSc !== null ? $avgSc : -1 }}">
                                {{-- Card Top: Avatar, Name & Code --}}
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-base flex items-center justify-center shrink-0 shadow-sm border border-teal-300/40">
                                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h3 class="font-heading font-black text-sm text-slate-900 truncate">
                                                    {{ $st->user?->name }}
                                                </h3>
                                                <span
                                                    class="inline-block font-mono text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
                                                    #{{ $studentCode }}
                                                </span>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full {{ $st->user?->status === \App\Enums\AccountStatus::APPROVED ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $st->user?->status === \App\Enums\AccountStatus::APPROVED ? __('Active') : __('Pending') }}
                                        </span>
                                    </div>

                                    {{-- Academic Meta: School & Grade --}}
                                    <div class="text-xs font-mono text-slate-600 space-y-1 pt-1">
                                        <p class="truncate flex items-center gap-1.5">
                                            <span><i class="fa-solid fa-school"></i></span>
                                            {{ $st->school_name ?: __('Elite Academy') }}
                                        </p>
                                        <p class="truncate flex items-center gap-1.5 text-slate-500">
                                            <span><i class="fa-solid fa-graduation-cap"></i></span>
                                            {{ $st->gradeLevel?->name ?: __('Secondary Level') }}
                                        </p>
                                    </div>

                                    {{-- Course Badges --}}
                                    @if (!empty($st->enrolled_courses))
                                        <div class="flex flex-wrap gap-1 pt-1">
                                            @foreach (collect($st->enrolled_courses)->take(2) as $cMeta)
                                                <span
                                                    class="text-[10px] font-mono font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 truncate max-w-[140px]">
                                                    {{ $cMeta['title'] }}
                                                </span>
                                            @endforeach
                                            @if (count($st->enrolled_courses) > 2)
                                                <span class="text-[10px] font-mono text-slate-400 self-center">
                                                    +{{ count($st->enrolled_courses) - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Card Bottom: KPIs & Action Button --}}
                                <div class="space-y-3 pt-3 border-t border-slate-200/80 dark:border-slate-700/80">
                                    <div class="grid grid-cols-2 gap-2 text-center font-mono text-[11px]">
                                        <div
                                            class="p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                                            <span
                                                class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Attendance') }}</span>
                                            <span
                                                class="font-extrabold {{ $attRate >= 80 ? 'text-emerald-600' : ($attRate >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                                {{ $attRate }}%
                                            </span>
                                        </div>
                                        <div
                                            class="p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800">
                                            <span
                                                class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Avg Score') }}</span>
                                            <span
                                                class="font-extrabold {{ $avgSc !== null ? ($avgSc >= 70 ? 'text-teal-600' : 'text-rose-600') : 'text-slate-400' }}">
                                                {{ $avgSc !== null ? $avgSc . '%' : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>

                                    <button type="button" onclick="openStudentDetailsModal({{ $st->user_id }})"
                                        class="btn-lift w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all">
                                        <span><i class="fa-solid fa-graduation-cap"></i></span>
                                        {{ __('app.teacher.student_profile') }}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Section Responsive Pagination Container --}}
                    <div id="studentsPagination" class="mt-6"></div>

                    {{-- Empty Search Result State --}}
                    <div id="studentEmptySearchState"
                        class="hidden text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                        <span class="text-3xl"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <p class="text-sm font-semibold text-slate-700">{{ __('app.teacher.no_students_found') }}</p>
                        <button type="button" onclick="resetStudentFilters()"
                            class="text-xs text-teal-600 font-bold hover:underline">
                            {{ __('app.teacher.all_courses') }}
                        </button>
                    </div>
                @else
                    <div
                        class="text-center py-12 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ __('No students enrolled in your courses yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 3: SESSIONS & STREAMS                                                --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-sessions"
            class="teacher-tab-content {{ $activeTabKey === 'sessions' ? '' : 'hidden' }} space-y-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-calendar-days"></i></span>
                            {{ __('Live Teaching Schedule & Meeting Links') }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ __('Manage your live sessions, recurring cohorts, update broadcast URLs, and reschedule classes.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <input type="text" id="liveSessionSearchInput"
                                placeholder="{{ __('Search session title, course, or date...') }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:border-teal-500 transition-all pe-8">
                            <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto">
                            <button type="button" onclick="openCreateRecurringModal()"
                                class="btn-lift px-3 py-2.5 sm:px-4 sm:py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center justify-center gap-1.5 truncate">
                                <span><i class="fa-solid fa-arrows-rotate"></i></span> <span
                                    class="truncate">{{ __('Create Recurring Schedule') }}</span>
                            </button>
                            <button type="button" onclick="openCreateSessionModal()"
                                class="btn-lift px-3 py-2.5 sm:px-4 sm:py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center justify-center gap-1.5 truncate">
                                <span><i class="fa-solid fa-plus"></i></span> <span
                                    class="truncate">{{ __('Schedule Single Session') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if ($allSessions->count() > 0)
                    {{-- Desktop & Tablet Data Table View (>= 768px) --}}
                    <div
                        class="hidden md:block table-responsive w-full overflow-x-auto custom-scrollbar rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <table style="min-width: 860px;" class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-3 px-4 col-title min-w-[240px]">{{ __('Session Details') }}</th>
                                    <th class="py-3 px-4 col-course min-w-[170px]">{{ __('Course') }}</th>
                                    <th class="py-3 px-4 col-date min-w-[140px]">{{ __('Date & Time') }}</th>
                                    <th class="py-3 px-4 col-badge min-w-[130px]">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 col-action min-w-[150px] text-right rtl:text-left">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sessionsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @foreach ($allSessions as $session)
                                    <tr class="session-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors"
                                        data-title="{{ strtolower($session->title ?? '') }}"
                                        data-course="{{ strtolower($session->course?->title ?? '') }}"
                                        data-date="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d') : '' }}">
                                        <td class="py-4 px-4 font-bold text-slate-900 dark:text-white">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <p
                                                        class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">
                                                        {{ $session->title ?: __('Live Class Session') }}</p>
                                                    @if ($session->recurring_schedule_id)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                                            <span><i class="fa-solid fa-arrows-rotate"></i></span>
                                                            {{ __('Recurring') }}
                                                        </span>
                                                    @endif
                                                    @if ($session->is_override)
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                                            <span><i class="fa-solid fa-triangle-exclamation"></i></span>
                                                            {{ __('Override') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                                    {{ $session->duration_minutes }} {{ __('minutes') }}</p>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-xs font-semibold text-teal-700 dark:text-teal-400">
                                            {{ $session->course?->title ?: __('General Curriculum') }}
                                        </td>
                                        <td
                                            class="py-4 px-4 font-mono text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                            {{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : __('Not Scheduled') }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @php
                                                $statusText = match ($session->status) {
                                                    'scheduled' => __('Scheduled'),
                                                    'link_visible' => __('Ready / Broadcast Link Live'),
                                                    'live' => __('Live Now'),
                                                    'completed' => __('Completed'),
                                                    'cancelled' => __('Cancelled'),
                                                    'cancelled_by_teacher' => __('Cancelled by Teacher'),
                                                    'rescheduled' => __('Rescheduled'),
                                                    default => __('Scheduled'),
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-mono font-bold rounded-full whitespace-nowrap {{ in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700 dark:bg-red-950/70 dark:text-red-300 dark:border dark:border-red-800/60' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border dark:border-emerald-800/60' : ($session->status === 'rescheduled' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 dark:border dark:border-amber-800/60' : 'bg-teal-100 text-teal-700 dark:bg-teal-950/70 dark:text-teal-300 dark:border dark:border-teal-800/60')) }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td
                                            class="py-4 px-4 text-right rtl:text-left space-x-1 rtl:space-x-reverse whitespace-nowrap">
                                            <button type="button" data-session-id="{{ $session->id }}"
                                                data-title="{{ $session->title }}"
                                                data-scheduled-at="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}"
                                                data-duration="{{ $session->duration_minutes ?: 60 }}"
                                                data-meeting-link="{{ $session->meeting_link ?? '' }}"
                                                data-notes="{{ $session->teacher_notes ?? '' }}"
                                                onclick="openEditSessionOverrideModal({{ $session->id }}, this)"
                                                class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/80 text-teal-800 dark:text-teal-300 text-xs font-bold rounded-lg border border-teal-200/70 dark:border-teal-800/60 transition-colors cursor-pointer"
                                                title="{{ __('Edit or Override Session') }}">
                                                <i class="fa-solid fa-pen"></i> {{ __('Edit Scope') }}
                                            </button>
                                            <button type="button" data-session-id="{{ $session->id }}"
                                                data-meeting-link="{{ $session->meeting_link ?? '' }}"
                                                onclick="openMeetingLinkModal({{ $session->id }}, this)"
                                                class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-lg border border-slate-200/70 dark:border-slate-700 transition-colors cursor-pointer">
                                                <i class="fa-solid fa-link"></i> {{ __('Link') }}
                                            </button>
                                            <button type="button" data-session-id="{{ $session->id }}"
                                                data-scheduled-at="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}"
                                                onclick="openRescheduleModal({{ $session->id }}, this)"
                                                class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/80 text-blue-700 dark:text-blue-300 text-xs font-bold rounded-lg border border-blue-200/70 dark:border-blue-800/60 transition-colors cursor-pointer">
                                                <i class="fa-solid fa-calendar-days"></i> {{ __('Reschedule') }}
                                            </button>
                                            @if (!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                                <button type="button"
                                                    onclick="confirmCancelSession({{ $session->id }})"
                                                    class="px-2.5 py-1 bg-red-50 hover:bg-red-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-red-700 dark:text-rose-300 text-xs font-bold rounded-lg border border-red-200/70 dark:border-rose-800/60 transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                                                    {{ __('Cancel') }}
                                                </button>
                                            @endif
                                            <button type="button"
                                                onclick="confirmDeleteSession({{ $session->id }}, '{{ addslashes($session->title ?: __('Live Session')) }}')"
                                                class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-lg border border-rose-200/70 dark:border-rose-800/60 transition-colors cursor-pointer"
                                                title="{{ __('Delete Session') }}">
                                                <i class="fa-solid fa-trash-can text-rose-500"></i>
                                                {{ __('Delete') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile-First Session Cards (< 768px) --}}
                    <div class="block md:hidden space-y-3.5" id="sessionsMobileListContainer">
                        @foreach ($allSessions as $session)
                            @php
                                $statusText = match ($session->status) {
                                    'scheduled' => __('Scheduled'),
                                    'link_visible' => __('Ready / Broadcast Link Live'),
                                    'live' => __('Live Now'),
                                    'completed' => __('Completed'),
                                    'cancelled' => __('Cancelled'),
                                    'cancelled_by_teacher' => __('Cancelled by Teacher'),
                                    'rescheduled' => __('Rescheduled'),
                                    default => __('Scheduled'),
                                };
                            @endphp
                            <div class="session-mobile-card p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3"
                                id="sessionMobileCard_{{ $session->id }}"
                                data-title="{{ strtolower($session->title ?? '') }}"
                                data-course="{{ strtolower($session->course?->title ?? '') }}"
                                data-date="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d') : '' }}">
                                {{-- Card Header: Title & Status --}}
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <h4
                                                class="font-heading font-black text-sm text-slate-900 dark:text-white leading-snug">
                                                {{ $session->title ?: __('Live Class Session') }}
                                            </h4>
                                            @if ($session->recurring_schedule_id)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                                    <i class="fa-solid fa-arrows-rotate text-[9px]"></i>
                                                    <span>{{ __('Recurring') }}</span>
                                                </span>
                                            @endif
                                            @if ($session->is_override)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                    <span>{{ __('Override') }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span
                                        class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-mono font-bold rounded-full {{ in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700 dark:bg-red-950/70 dark:text-red-300' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300' : ($session->status === 'rescheduled' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300' : 'bg-teal-100 text-teal-700 dark:bg-teal-950/70 dark:text-teal-300')) }}">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                {{-- Card Meta: Course and Date --}}
                                <div
                                    class="grid grid-cols-1 gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                                    <div
                                        class="flex items-center gap-2 text-teal-700 dark:text-teal-400 font-semibold truncate">
                                        <i
                                            class="fa-solid fa-book-open text-xs shrink-0 text-teal-600 dark:text-teal-400"></i>
                                        <span
                                            class="truncate">{{ $session->course?->title ?: __('General Curriculum') }}</span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between gap-2 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-calendar-days text-slate-400 text-xs"></i>
                                            <span>{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d • h:i A') : __('Not Scheduled') }}</span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-stopwatch text-slate-400 text-[10px]"></i>
                                            <span>{{ $session->duration_minutes }} {{ __('min') }}</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Actions Grid --}}
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-5 gap-1.5 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" data-session-id="{{ $session->id }}"
                                        data-title="{{ $session->title }}"
                                        data-scheduled-at="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}"
                                        data-duration="{{ $session->duration_minutes ?: 60 }}"
                                        data-meeting-link="{{ $session->meeting_link ?? '' }}"
                                        data-notes="{{ $session->teacher_notes ?? '' }}"
                                        onclick="openEditSessionOverrideModal({{ $session->id }}, this)"
                                        class="px-2 py-2 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/80 text-teal-800 dark:text-teal-300 text-xs font-bold rounded-xl border border-teal-200/70 dark:border-teal-800/60 transition-colors cursor-pointer flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                        <span>{{ __('Edit Scope') }}</span>
                                    </button>
                                    <button type="button" data-session-id="{{ $session->id }}"
                                        data-meeting-link="{{ $session->meeting_link ?? '' }}"
                                        onclick="openMeetingLinkModal({{ $session->id }}, this)"
                                        class="px-2 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200/70 dark:border-slate-700 transition-colors cursor-pointer flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-link text-[10px]"></i>
                                        <span>{{ __('Link') }}</span>
                                    </button>
                                    <button type="button" data-session-id="{{ $session->id }}"
                                        data-scheduled-at="{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}"
                                        onclick="openRescheduleModal({{ $session->id }}, this)"
                                        class="px-2 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/80 text-blue-700 dark:text-blue-300 text-xs font-bold rounded-xl border border-blue-200/70 dark:border-blue-800/60 transition-colors cursor-pointer flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-calendar-days text-[10px]"></i>
                                        <span>{{ __('Reschedule') }}</span>
                                    </button>
                                    @if (!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                        <button type="button" onclick="confirmCancelSession({{ $session->id }})"
                                            class="px-2 py-2 bg-red-50 hover:bg-red-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-red-700 dark:text-rose-300 text-xs font-bold rounded-xl border border-red-200/70 dark:border-rose-800/60 transition-colors cursor-pointer flex items-center justify-center gap-1">
                                            <i class="fa-solid fa-circle-xmark text-rose-500 text-[10px]"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </button>
                                    @endif
                                    <button type="button"
                                        onclick="confirmDeleteSession({{ $session->id }}, '{{ addslashes($session->title ?: __('Live Session')) }}')"
                                        class="px-2 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl border border-rose-200/70 dark:border-rose-800/60 transition-colors cursor-pointer flex items-center justify-center gap-1 col-span-2 sm:col-span-1">
                                        <i class="fa-solid fa-trash-can text-rose-500 text-[10px]"></i>
                                        <span>{{ __('Delete') }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Pagination --}}
                    <div id="sessionsPagination" class="hidden md:block mt-4"></div>
                    {{-- Mobile Pagination --}}
                    <div id="sessionsMobilePagination" class="block md:hidden mt-4"></div>

                    <div id="sessionsEmptySearchState" class="hidden text-center py-8 text-xs text-slate-400 italic">
                        {{ __('No teaching sessions match your current filter criteria.') }}
                    </div>
                @else
                    <div
                        class="text-center py-12 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No sessions created yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 4: ASSIGNMENTS & SUBMISSIONS                                         --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-assignments"
            class="teacher-tab-content {{ $activeTabKey === 'assignments' ? '' : 'hidden' }} space-y-8">
            {{-- Assignments Header & Publish Action --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">
                            {{ __('Assignments & Homework Manager') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ __('Publish new course assignments, interactive quizzes, and review student work.') }}</p>
                    </div>
                    <button type="button" onclick="openCreateAssignmentModal()"
                        class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                        + {{ __('Publish New Assignment') }}
                    </button>
                </div>

                @if ($assignments->count() > 0)
                    <div id="assignmentsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($assignments as $assignment)
                            @php
                                $subCount = $assignment->submissions->count();
                                $subLabel = $isAr
                                    ? ($subCount == 1
                                        ? 'تسليم'
                                        : ($subCount == 2
                                            ? 'تسليمان'
                                            : ($subCount <= 10
                                                ? 'تسليمات'
                                                : 'تسليم')))
                                    : ($subCount == 1
                                        ? 'Submission'
                                        : 'Submissions');
                                $escapedTitle = addslashes($assignment->title);
                            @endphp
                            <div id="assignmentCard_{{ $assignment->id }}"
                                class="assignment-card bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl p-5 border border-slate-200/90 dark:border-slate-800 space-y-3.5 flex flex-col justify-between hover:border-teal-400 dark:hover:border-teal-500 hover:bg-white dark:hover:bg-slate-800/90 hover:shadow-lg transition-all group shadow-xs">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <span
                                            class="font-mono font-bold text-teal-700 dark:text-teal-400 uppercase truncate flex-1 min-w-0">{{ $assignment->course?->title ?: __('Course') }}</span>
                                        <span
                                            class="px-2.5 py-1 bg-teal-50 dark:bg-teal-950/70 text-teal-800 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800/60 text-[11px] font-mono font-extrabold rounded-xl shrink-0 whitespace-nowrap shadow-2xs">
                                            <i class="fa-solid fa-pen-to-square"></i> {{ $subCount }}
                                            {{ $subLabel }}
                                        </span>
                                    </div>
                                    <h3 onclick="openAssignmentDetailsModal({{ $assignment->id }})"
                                        class="font-heading font-black text-base text-slate-900 dark:text-white leading-snug group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors cursor-pointer">
                                        {{ $assignment->title }}</h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                        {{ $assignment->description ?: __('Homework assignment for student revision.') }}
                                    </p>
                                </div>

                                <div
                                    class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-mono text-slate-500 dark:text-slate-400">
                                    <span class="truncate"><i class="fa-solid fa-calendar-days"></i>
                                        {{ __('Due') }}:
                                        {{ $assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline') }}</span>
                                    <span
                                        class="font-extrabold text-slate-800 dark:text-slate-200 shrink-0 ms-2 bg-slate-100 dark:bg-slate-700/80 px-2 py-0.5 rounded-lg"><i
                                            class="fa-solid fa-bullseye text-teal-600 dark:text-teal-400"></i>
                                        {{ $assignment->passing_score ?: 70 }}%
                                        {{ __('Pass') }}</span>
                                </div>

                                {{-- Action Buttons: View Details, Edit, Delete --}}
                                <div
                                    class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-1.5 flex-wrap">
                                    <button type="button" onclick="openAssignmentDetailsModal({{ $assignment->id }})"
                                        class="btn-lift px-3 py-1.5 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/80 text-teal-800 dark:text-teal-300 text-xs font-bold rounded-xl border border-teal-200/70 dark:border-teal-800/60 flex items-center gap-1.5 cursor-pointer">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>{{ __('Details & Submissions') }}</span>
                                    </button>
                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="openEditAssignmentModal({{ $assignment->id }})"
                                            class="btn-lift px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                                            title="{{ __('Edit Assignment') }}">
                                            <i class="fa-solid fa-pen-to-square text-teal-600 dark:text-teal-400"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </button>
                                        <button type="button"
                                            onclick="confirmDeleteAssignment({{ $assignment->id }}, '{{ $escapedTitle }}')"
                                            class="btn-lift px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl border border-rose-200/70 dark:border-rose-800/60 transition-colors cursor-pointer"
                                            title="{{ __('Delete Assignment') }}">
                                            <i class="fa-solid fa-trash text-rose-500"></i>
                                            <span>{{ __('Delete') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Assignments Pagination Container --}}
                    <div id="assignmentsPagination" class="mt-6"></div>
                @else
                    <div
                        class="text-center py-8 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No assignments created yet.') }}</p>
                    </div>
                @endif
            </div>

            {{-- Submissions Table --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                <h3 class="font-heading text-xl font-black text-slate-900">{{ __('All Student Submissions') }}</h3>

                @if ($submissions->count() > 0)
                    {{-- Desktop & Tablet Data Table View (>= 768px) --}}
                    <div
                        class="hidden md:block table-responsive w-full overflow-x-auto custom-scrollbar rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <table style="min-width: 860px;" class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase">
                                    <th class="py-3 px-4 col-title min-w-[200px]">{{ __('Student') }}</th>
                                    <th class="py-3 px-4 col-course min-w-[220px]">{{ __('Assignment') }}</th>
                                    <th class="py-3 px-4 col-date min-w-[140px]">{{ __('Submitted At') }}</th>
                                    <th class="py-3 px-4 col-badge min-w-[130px]">{{ __('Grade / Score') }}</th>
                                    <th class="py-3 px-4 col-action min-w-[150px] text-right rtl:text-left">
                                        {{ __('Review Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="submissionsTableBody"
                                class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                                @foreach ($submissions as $sub)
                                    @php
                                        $subVal =
                                            $sub->status instanceof \App\Enums\SubmissionStatus
                                                ? $sub->status->value
                                                : (is_object($sub->status)
                                                    ? $sub->status->value ?? ''
                                                    : (string) $sub->status);
                                        $isReviewed = $subVal === 'reviewed';
                                    @endphp
                                    <tr class="submission-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors"
                                        data-student="{{ strtolower($sub->studentUser?->name ?? '') }}"
                                        data-assignment="{{ strtolower($sub->assignment?->title ?? '') }}">
                                        <td class="py-4 px-4 font-bold text-slate-900 dark:text-white">
                                            {{ $sub->studentUser?->name ?: __('Student') }}
                                        </td>
                                        <td class="py-4 px-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                                            {{ $sub->assignment?->title ?: __('Assignment') }}
                                        </td>
                                        <td
                                            class="py-4 px-4 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                            {{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Draft' }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs whitespace-nowrap">
                                            @if ($sub->score !== null)
                                                <span
                                                    class="font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($sub->score, 1) }}%</span>
                                            @else
                                                <span
                                                    class="text-orange-500 dark:text-orange-400 italic">{{ __('Pending Grade') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right rtl:text-left whitespace-nowrap">
                                            <button type="button" data-submission-id="{{ $sub->id }}"
                                                data-student-name="{{ $sub->studentUser?->name ?: __('Student') }}"
                                                data-assignment-title="{{ $sub->assignment?->title ?: __('Assignment') }}"
                                                data-score="{{ $sub->score }}"
                                                data-evaluation-notes="{{ $sub->evaluation_notes }}"
                                                onclick="openGradeModal({{ $sub->id }}, this)"
                                                class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                                {{ $isReviewed ? __('Review & Grade') : __('Review Submission') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile-First Submissions Cards (< 768px) --}}
                    <div class="block md:hidden space-y-3.5" id="submissionsMobileListContainer">
                        @foreach ($submissions as $sub)
                            @php
                                $subVal =
                                    $sub->status instanceof \App\Enums\SubmissionStatus
                                        ? $sub->status->value
                                        : (is_object($sub->status)
                                            ? $sub->status->value ?? ''
                                            : (string) $sub->status);
                                $isReviewed = $subVal === 'reviewed';
                            @endphp
                            <div class="submission-mobile-card p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3"
                                data-student="{{ strtolower($sub->studentUser?->name ?? '') }}"
                                data-assignment="{{ strtolower($sub->assignment?->title ?? '') }}">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <h4
                                            class="font-heading font-black text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ $sub->studentUser?->name ?: __('Student') }}
                                        </h4>
                                        <p class="text-xs font-semibold text-teal-700 dark:text-teal-400 truncate">
                                            <i
                                                class="fa-solid fa-file-lines me-1"></i>{{ $sub->assignment?->title ?: __('Assignment') }}
                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        @if ($sub->score !== null)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 text-xs font-mono font-black rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                {{ number_format($sub->score, 1) }}%
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 text-[10px] font-mono font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                {{ __('Pending Grade') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                                    <span
                                        class="text-slate-500 dark:text-slate-400 font-mono text-[11px] flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock text-slate-400"></i>
                                        <span>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : __('Draft') }}</span>
                                    </span>
                                    <button type="button" data-submission-id="{{ $sub->id }}"
                                        data-student-name="{{ $sub->studentUser?->name ?: __('Student') }}"
                                        data-assignment-title="{{ $sub->assignment?->title ?: __('Assignment') }}"
                                        data-score="{{ $sub->score }}"
                                        data-evaluation-notes="{{ $sub->evaluation_notes }}"
                                        onclick="openGradeModal({{ $sub->id }}, this)"
                                        class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                        <span>{{ $isReviewed ? __('Review & Grade') : __('Review Submission') }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Submissions Pagination --}}
                    <div id="submissionsPagination" class="hidden md:block mt-4"></div>
                    {{-- Mobile Submissions Pagination --}}
                    <div id="submissionsMobilePagination" class="block md:hidden mt-4"></div>
                @else
                    <div
                        class="text-center py-8 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No student submissions yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 5: ATTENDANCE TRACKER & COHORT CHECK-IN (PROFESSIONAL REDESIGN)       --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-attendance"
            class="teacher-tab-content {{ $activeTabKey === 'attendance' ? '' : 'hidden' }} space-y-6">

            {{-- Section Banner / Header --}}
            <div
                class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-teal-800/40 relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2 relative z-10 max-w-2xl">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span
                            class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <i class="fa-solid fa-clipboard-user"></i> {{ __('Attendance & Check-In System') }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                            {{ $attendanceStats['total_sessions'] ?? $attendanceSessions->count() }}
                            {{ __('Total Teaching Sessions') }}
                        </span>
                    </div>
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-white tracking-tight">
                        {{ __('Attendance & Student Check-In') }}
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm font-medium leading-relaxed">
                        {{ __('Record, monitor, and audit live session attendance across your course cohorts. Track present, late, excused, and absent learners with instant reporting.') }}
                    </p>
                </div>

                {{-- Quick Filter / Action Buttons in Banner --}}
                <div class="relative z-10 flex flex-wrap items-center gap-2.5 shrink-0">
                    <button type="button" onclick="setAttendanceFilter('today')"
                        class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer transition-all">
                        <span><i class="fa-solid fa-calendar-day"></i></span> {{ __('Today\'s Sessions') }}
                    </button>
                    <button type="button" onclick="setAttendanceFilter('pending')"
                        class="btn-lift px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs sm:text-sm rounded-2xl shadow-md flex items-center gap-2 cursor-pointer transition-all">
                        <span><i class="fa-solid fa-clock"></i></span> {{ __('Needs Check-In') }}
                        @if (($attendanceStats['pending_count'] ?? 0) > 0)
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] bg-slate-950 text-white font-mono font-black">{{ $attendanceStats['pending_count'] }}</span>
                        @endif
                    </button>
                </div>
                <div
                    class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none">
                </div>
            </div>

            {{-- Executive Attendance KPI Statistics Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- KPI 1: Overall Attendance Rate --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-md hover:shadow-lg transition-all space-y-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-mono font-bold uppercase">{{ __('Cohort Attendance Rate') }}</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i
                                class="fa-solid fa-circle-check"></i></span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600" id="attKpiRate"><span
                                class="js-counter"
                                data-target="{{ $attendanceStats['overall_rate'] ?? 100 }}">{{ $attendanceStats['overall_rate'] ?? 100 }}</span>%
                        </p>
                        <span class="text-[11px] font-mono text-slate-500">{{ __('average') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div id="attKpiProgressBar" class="bg-emerald-500 h-full rounded-full transition-all duration-500"
                            style="width: {{ $attendanceStats['overall_rate'] ?? 100 }}%"></div>
                    </div>
                </div>

                {{-- KPI 2: Total Present Check-ins --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-md hover:shadow-lg transition-all space-y-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-mono font-bold uppercase">{{ __('Present Check-Ins') }}</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i
                                class="fa-solid fa-user-check"></i></span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600" id="attKpiPresent"><span
                                class="js-counter"
                                data-target="{{ $attendanceStats['total_present'] ?? 0 }}">{{ $attendanceStats['total_present'] ?? 0 }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-slate-500">{{ __('records') }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold">{{ __('Verified attending learners') }}</p>
                </div>

                {{-- KPI 3: Absences & Excuses Logged --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-md hover:shadow-lg transition-all space-y-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-mono font-bold uppercase">{{ __('Absences Logged') }}</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-sm"><i
                                class="fa-solid fa-user-xmark"></i></span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="font-heading font-black text-2xl sm:text-3xl text-rose-600" id="attKpiAbsent"><span
                                class="js-counter"
                                data-target="{{ $attendanceStats['total_absent'] ?? 0 }}">{{ $attendanceStats['total_absent'] ?? 0 }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-slate-500">{{ __('records') }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold">{{ __('Notified student & parent') }}</p>
                </div>

                {{-- KPI 4: Pending Check-In Sessions --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 shadow-md hover:shadow-lg transition-all space-y-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-mono font-bold uppercase">{{ __('Pending Check-In') }}</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm"><i
                                class="fa-solid fa-clock"></i></span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <p class="font-heading font-black text-2xl sm:text-3xl text-amber-600" id="attKpiPending"><span
                                class="js-counter"
                                data-target="{{ $attendanceStats['pending_count'] ?? 0 }}">{{ $attendanceStats['pending_count'] ?? 0 }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-slate-500">{{ __('sessions') }}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-semibold">{{ __('Awaiting attendance entry') }}</p>
                </div>
            </div>

            {{-- Main Attendance Container --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">

                {{-- Toolbar: Multi-Faceted Filters, Search, & View Switchers --}}
                <div
                    class="p-4 bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3.5">
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                        {{-- Search & Dropdowns Container --}}
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            {{-- Search Input --}}
                            <div class="relative">
                                <input type="text" id="attSearchInput"
                                    placeholder="{{ __('Search session title, course, or date...') }}"
                                    class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pe-8">
                                <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs"><i
                                        class="fa-solid fa-magnifying-glass"></i></span>
                            </div>

                            {{-- Filter by Course --}}
                            <div>
                                <select id="attCourseFilter"
                                    class="elite-custom-select w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                    <option value="">{{ __('All Courses & Cohorts') }}</option>
                                    @foreach ($courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter by Attendance Status --}}
                            <div>
                                <select id="attStatusFilter"
                                    class="elite-custom-select w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                    <option value="">{{ __('All Attendance States') }}</option>
                                    <option value="recorded">{{ __('Attendance Recorded ✓') }}</option>
                                    <option value="pending">{{ __('Pending Check-In ⏳') }}</option>
                                    <option value="today">{{ __('Scheduled Today 📅') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- View Switcher Buttons --}}
                        <div
                            class="flex items-center gap-1.5 bg-white dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shrink-0 self-start lg:self-center">
                            <button type="button" onclick="switchAttView('cards')" id="attViewBtnCards"
                                class="py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all bg-teal-600 text-white shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <i class="fa-solid fa-grip"></i>
                                <span>{{ __('Cards') }}</span>
                            </button>
                            <button type="button" onclick="switchAttView('table')" id="attViewBtnTable"
                                class="py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <i class="fa-solid fa-table-list"></i>
                                <span>{{ __('Table') }}</span>
                            </button>
                            <button type="button" onclick="switchAttView('matrix')" id="attViewBtnMatrix"
                                class="py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <i class="fa-solid fa-users-viewfinder"></i>
                                <span>{{ __('Learners') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Status Info & Reset --}}
                    <div
                        class="flex items-center justify-between text-xs font-mono text-slate-500 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                        <span id="attResultsCountText">{{ __('Showing all teaching sessions') }} (<span
                                id="attCountBadge">{{ $attendanceSessions->count() }}</span>)</span>
                        <button type="button" onclick="resetAttendanceFilters()"
                            class="text-teal-600 font-bold hover:underline cursor-pointer flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left"></i> {{ __('Reset Filters') }}
                        </button>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════════ --}}
                {{-- VIEW 1: INTERACTIVE SESSION CARDS GRID                          --}}
                {{-- ════════════════════════════════════════════════════════════════ --}}
                <div id="attViewCards" class="att-view-pane space-y-4">
                    @if ($attendanceSessions->count() > 0)
                        <div id="attCardsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($attendanceSessions as $ses)
                                @php
                                    $isToday = $ses->scheduled_at && $ses->scheduled_at->isToday();
                                    $isPast = $ses->scheduled_at && $ses->scheduled_at->isPast();
                                    $isRecorded = $ses->is_recorded;
                                    $statusCategory = $isRecorded
                                        ? 'recorded'
                                        : ($isToday
                                            ? 'today'
                                            : ($isPast
                                                ? 'pending'
                                                : 'upcoming'));
                                    $courseId = $ses->course_id ?: 0;
                                    $searchStr = strtolower(
                                        ($ses->title ?: '') .
                                            ' ' .
                                            ($ses->course?->title ?: '') .
                                            ' ' .
                                            ($ses->subject?->name ?: '') .
                                            ' ' .
                                            ($ses->scheduled_at ? $ses->scheduled_at->format('Y-m-d M') : ''),
                                    );
                                    $sesTitleEscaped = addslashes($ses->title ?: __('Live Session'));
                                @endphp
                                <div class="att-session-card bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl p-5 border border-slate-200/90 dark:border-slate-800 hover:border-teal-500 dark:hover:border-teal-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-4"
                                    id="attSessionCard_{{ $ses->id }}" data-title="{{ $searchStr }}"
                                    data-course="{{ $courseId }}" data-status="{{ $statusCategory }}"
                                    data-recorded="{{ $isRecorded ? '1' : '0' }}"
                                    data-today="{{ $isToday ? '1' : '0' }}">
                                    {{-- Card Header: Course Badge, Date/Time, & Session Title --}}
                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span
                                                    class="px-2.5 py-0.5 text-[10px] font-mono font-extrabold uppercase rounded-full bg-teal-100 text-teal-800 border border-teal-200/80">
                                                    {{ $ses->course?->title ?: __('General Cohort') }}
                                                </span>
                                                @if ($isToday)
                                                    <span
                                                        class="px-2 py-0.5 text-[10px] font-mono font-black rounded-full bg-rose-100 text-rose-800 border border-rose-200 animate-pulse">
                                                        {{ __('TODAY') }}
                                                    </span>
                                                @endif
                                                @if ($ses->recurring_schedule_id)
                                                    <span
                                                        class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                                        <i class="fa-solid fa-arrows-rotate text-[9px]"></i>
                                                        {{ __('Series') }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Attendance Status Pill --}}
                                            <div id="attCardBadge_{{ $ses->id }}">
                                                @if ($isRecorded)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                                        <i
                                                            class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                                        <span>{{ __('Recorded') }}</span>
                                                        @if ($ses->present_count > 0 || $ses->absent_count > 0)
                                                            <span
                                                                class="font-extrabold">({{ $ses->present_count }}/{{ $ses->present_count + $ses->absent_count }})</span>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                        <i class="fa-solid fa-clock text-amber-600 text-[10px]"></i>
                                                        <span>{{ __('Pending Check-In') }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <h3
                                                class="font-heading font-black text-sm sm:text-base text-slate-900 leading-snug">
                                                {{ $ses->title ?: __('Interactive Teaching Session') }}
                                            </h3>
                                            <p
                                                class="text-xs font-mono text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                                <span><i class="fa-solid fa-calendar-days text-slate-400"></i>
                                                    {{ $ses->effective_start_at ? $ses->effective_start_at->format('M d, Y • h:i A') : __('Not Scheduled') }}</span>
                                                <span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span><i class="fa-solid fa-stopwatch text-slate-400"></i>
                                                    {{ $ses->duration_minutes ?: 60 }}m</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Card Body: Metric Badges Strip --}}
                                    <div class="grid grid-cols-3 gap-2 text-center font-mono text-xs pt-1 border-t border-slate-200/70 dark:border-slate-700/70"
                                        id="attCardStats_{{ $ses->id }}">
                                        <div
                                            class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                            <span
                                                class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Present') }}</span>
                                            <span
                                                class="font-black text-emerald-600 text-sm att-stat-present">{{ $ses->present_count }}</span>
                                        </div>
                                        <div
                                            class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                            <span
                                                class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Absent') }}</span>
                                            <span
                                                class="font-black text-rose-600 text-sm att-stat-absent">{{ $ses->absent_count }}</span>
                                        </div>
                                        <div
                                            class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                                            <span
                                                class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Rate') }}</span>
                                            <span
                                                class="font-black {{ ($ses->session_rate ?? 100) >= 80 ? 'text-emerald-600' : 'text-amber-600' }} text-sm att-stat-rate">
                                                {{ $ses->session_rate !== null ? $ses->session_rate . '%' : '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Card Footer: Action Button --}}
                                    <div
                                        class="pt-2 border-t border-slate-200/70 dark:border-slate-700/70 flex items-center justify-between gap-3">
                                        <div class="text-[11px] font-mono text-slate-500 truncate">
                                            <i class="fa-solid fa-graduation-cap text-teal-600"></i>
                                            {{ $ses->subject?->name ?: __('Curriculum Cohort') }}
                                        </div>

                                        <button type="button" data-session-id="{{ $ses->id }}"
                                            data-session-title="{{ $ses->title ?: __('Live Session') }}"
                                            onclick="openAttendanceModal({{ $ses->id }}, this)"
                                            class="btn-lift px-4 py-2 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-sm flex items-center gap-1.5 shrink-0 cursor-pointer transition-all">
                                            <i class="fa-solid fa-clipboard-check"></i>
                                            <span
                                                id="attCardBtnLabel_{{ $ses->id }}">{{ $isRecorded ? __('Review & Edit') : __('Take Attendance') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Attendance Cards Pagination --}}
                        <div id="attCardsPagination" class="mt-6"></div>

                        {{-- Empty State for Search --}}
                        <div id="attEmptySearchState"
                            class="hidden text-center py-12 bg-[#FAFAF9] rounded-2xl border border-dashed border-slate-200 space-y-2">
                            <span class="text-3xl"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <p class="text-sm font-semibold text-slate-700">
                                {{ __('No teaching sessions match your current filter criteria.') }}</p>
                            <button type="button" onclick="resetAttendanceFilters()"
                                class="text-xs text-teal-600 font-bold hover:underline">
                                {{ __('Show all sessions') }}
                            </button>
                        </div>
                    @else
                        <div
                            class="text-center py-16 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 space-y-3">
                            <span class="text-4xl"><i class="fa-solid fa-calendar-xmark text-slate-400"></i></span>
                            <p class="text-base font-bold text-slate-800">
                                {{ __('No teaching sessions found for attendance tracking.') }}</p>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                {{ __('Schedule a single session or create a recurring schedule series to start recording student attendance.') }}
                            </p>
                            <button type="button" onclick="openCreateSessionModal()"
                                class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                                + {{ __('Schedule New Session') }}
                            </button>
                        </div>
                    @endif
                </div>

                {{-- ════════════════════════════════════════════════════════════════ --}}
                {{-- VIEW 2: DETAILED TABLE ROSTER VIEW                              --}}
                {{-- ════════════════════════════════════════════════════════════════ --}}
                <div id="attViewTable" class="att-view-pane hidden space-y-4">
                    <div
                        class="table-responsive w-full overflow-x-auto custom-scrollbar rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <table style="min-width: 860px;" class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase bg-slate-50/70 dark:bg-slate-800/70">
                                    <th class="py-3 px-4 col-title min-w-[240px] rounded-s-xl">
                                        {{ __('Session Details') }}</th>
                                    <th class="py-3 px-4 col-course min-w-[170px]">{{ __('Course / Cohort') }}</th>
                                    <th class="py-3 px-4 col-date min-w-[140px]">{{ __('Scheduled Time') }}</th>
                                    <th class="py-3 px-4 col-badge min-w-[140px]">{{ __('Attendance Status') }}</th>
                                    <th class="py-3 px-4 min-w-[110px]">{{ __('Stats') }}</th>
                                    <th class="py-3 px-4 col-action min-w-[150px] text-right rtl:text-left rounded-e-xl">
                                        {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="attTableBody"
                                class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
                                @foreach ($attendanceSessions as $ses)
                                    @php
                                        $isRecorded = $ses->is_recorded;
                                        $courseId = $ses->course_id ?: 0;
                                        $searchStr = strtolower(
                                            ($ses->title ?: '') .
                                                ' ' .
                                                ($ses->course?->title ?: '') .
                                                ' ' .
                                                ($ses->subject?->name ?: '') .
                                                ' ' .
                                                ($ses->scheduled_at ? $ses->scheduled_at->format('Y-m-d M') : ''),
                                        );
                                        $statusCat = $isRecorded
                                            ? 'recorded'
                                            : ($ses->scheduled_at && $ses->scheduled_at->isToday()
                                                ? 'today'
                                                : 'pending');
                                        $sesTitleEscaped = addslashes($ses->title ?: __('Live Session'));
                                    @endphp
                                    <tr class="att-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors"
                                        data-title="{{ $searchStr }}" data-course="{{ $courseId }}"
                                        data-status="{{ $statusCat }}">
                                        <td class="py-4 px-4 font-bold text-slate-900">
                                            <p class="font-extrabold text-slate-900">
                                                {{ $ses->title ?: __('Live Session') }}</p>
                                            <p class="text-[11px] text-slate-500 font-mono">
                                                {{ $ses->duration_minutes ?: 60 }} {{ __('minutes') }}</p>
                                        </td>
                                        <td class="py-4 px-4 text-xs font-semibold text-teal-700">
                                            {{ $ses->course?->title ?: __('General Cohort') }}
                                        </td>
                                        <td
                                            class="py-4 px-4 font-mono text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                            {{ $ses->effective_start_at ? $ses->effective_start_at->format('Y-m-d h:i A') : __('Not Scheduled') }}
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if ($isRecorded)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 dark:border dark:border-emerald-800/60 whitespace-nowrap">
                                                    <i
                                                        class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-[9px]"></i>
                                                    {{ __('Recorded') }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 dark:border dark:border-amber-800/60 whitespace-nowrap">
                                                    <i
                                                        class="fa-solid fa-clock text-amber-600 dark:text-amber-400 text-[9px]"></i>
                                                    {{ __('Pending') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs whitespace-nowrap">
                                            <span
                                                class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $ses->present_count }}P</span>
                                            /
                                            <span
                                                class="text-rose-600 dark:text-rose-400 font-bold">{{ $ses->absent_count }}A</span>
                                            @if ($ses->session_rate !== null)
                                                <span
                                                    class="ms-1 text-slate-500 dark:text-slate-400 font-bold">({{ $ses->session_rate }}%)</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right rtl:text-left whitespace-nowrap">
                                            <button type="button" data-session-id="{{ $ses->id }}"
                                                data-session-title="{{ $ses->title ?: __('Live Session') }}"
                                                onclick="openAttendanceModal({{ $ses->id }}, this)"
                                                class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-2xs cursor-pointer">
                                                <i class="fa-solid fa-clipboard-check"></i>
                                                {{ $isRecorded ? __('Edit') : __('Mark') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Attendance Table Pagination --}}
                    <div id="attTablePagination" class="mt-4"></div>
                </div>

                {{-- ════════════════════════════════════════════════════════════════ --}}
                {{-- VIEW 3: STUDENT-LEVEL ATTENDANCE HEALTH MATRIX                  --}}
                {{-- ════════════════════════════════════════════════════════════════ --}}
                <div id="attViewMatrix" class="att-view-pane hidden space-y-4">
                    <div
                        class="p-4 bg-teal-50/60 rounded-2xl border border-teal-200/80 flex items-center justify-between gap-4 flex-wrap">
                        <div class="space-y-0.5">
                            <h4 class="font-heading font-black text-sm text-teal-950">
                                {{ __('Student Cohort Attendance Health') }}</h4>
                            <p class="text-xs text-teal-800 font-mono">
                                {{ __('Comprehensive attendance track record across all assigned learners in your curriculum.') }}
                            </p>
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-white dark:bg-slate-800 text-teal-900 dark:text-teal-200 border border-teal-200 dark:border-teal-700">
                            {{ $assignedStudents->count() }} {{ __('Total Learners') }}
                        </span>
                    </div>

                    @if ($assignedStudents->count() > 0)
                        <div id="attMatrixGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($assignedStudents as $st)
                                @php
                                    $attRate = (int) ($st->attendance_rate ?? 100);
                                    $isGood = $attRate >= 80;
                                    $isRisk = $attRate >= 60 && $attRate < 80;
                                    $studentCode = 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT);
                                @endphp
                                <div
                                    class="att-matrix-card p-4 rounded-2xl bg-[#FAFAF9] dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 hover:border-teal-400 dark:hover:border-teal-500 transition-all space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-black text-sm flex items-center justify-center shrink-0">
                                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4
                                                    class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                                                    {{ $st->user?->name }}</h4>
                                                <p class="text-[10px] font-mono text-slate-400">#{{ $studentCode }} •
                                                    {{ $st->gradeLevel?->name ?: __('Secondary') }}</p>
                                            </div>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-mono font-extrabold {{ $isGood ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($isRisk ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}">
                                            {{ $attRate }}%
                                        </span>
                                    </div>

                                    <div class="space-y-1">
                                        <div
                                            class="flex justify-between text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                            <span>{{ __('Attendance Consistency') }}</span>
                                            <span
                                                class="font-bold">{{ $isGood ? __('Good Standing') : ($isRisk ? __('Attention Required') : __('Critical Absence Risk')) }}</span>
                                        </div>
                                        <div
                                            class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full {{ $isGood ? 'bg-emerald-500' : ($isRisk ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                style="width: {{ $attRate }}%"></div>
                                        </div>
                                    </div>

                                    <button type="button" onclick="openStudentDetailsModal({{ $st->user_id }})"
                                        class="btn-lift w-full py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-user-graduate text-teal-600"></i>
                                        {{ __('View Educational Profile') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Attendance Matrix Pagination --}}
                        <div id="attMatrixPagination" class="mt-4"></div>
                    @else
                        <p class="text-xs text-slate-400 italic text-center py-6">
                            {{ __('No student records available.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 6: NOTIFICATIONS                                                     --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-notifications"
            class="teacher-tab-content {{ $activeTabKey === 'notifications' ? '' : 'hidden' }} space-y-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">
                            {{ __('Notification Feed & Alerts') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ __('Real-time session updates, assignment submissions, and student alerts.') }}</p>
                    </div>
                </div>

                @if ($userNotifications->count() > 0)
                    <div id="notifsListContainer" class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar pr-1">
                        @foreach ($userNotifications as $notif)
                            <div
                                class="notif-feed-item p-4 rounded-2xl border transition-colors flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-[#FAFAF9] dark:bg-slate-800/60 border-slate-200/70 dark:border-slate-800' : 'bg-teal-50/80 dark:bg-teal-950/40 border-teal-200 dark:border-teal-800 font-semibold' }}">
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $notif->title }}</h4>
                                    <p class="text-xs text-slate-600 dark:text-slate-300">{{ $notif->body }}</p>
                                    <p class="text-[10px] font-mono text-slate-400">
                                        {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="notifsPagination" class="mt-4"></div>
                @else
                    <div
                        class="text-center py-12 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ __('You\'re all caught up! No new notifications.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 7: SCHEDULES – LIVE SESSION SCHEDULER (NO FILAMENT REDIRECT)       --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-schedules"
            class="teacher-tab-content {{ $activeTabKey === 'schedules' ? '' : 'hidden' }} space-y-6">

            {{-- ── Header ──────────────────────────────────────────────────────────── --}}
            <div
                class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-violet-900 rounded-3xl p-6 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-indigo-500/30 flex items-center justify-center text-indigo-200">
                            <i class="fa-solid fa-calendar-check text-base"></i>
                        </span>
                        <h2 class="font-heading text-2xl font-black text-white">{{ __('Student & Course Schedules') }}
                        </h2>
                    </div>
                    <p class="text-indigo-200 text-xs font-mono">
                        {{ __('Create and manage recurring lesson schedules for your courses. Students are automatically enrolled based on course.') }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="openScheduleModal('singleSessionModal')"
                        class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white font-extrabold text-xs rounded-2xl border border-white/20 flex items-center gap-2 cursor-pointer transition-all">
                        <i class="fa-solid fa-plus"></i> {{ __('Single Session') }}
                    </button>
                    <button type="button" onclick="openScheduleModal('recurringSchedulePortalModal')"
                        class="px-4 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white font-extrabold text-xs rounded-2xl shadow-lg flex items-center gap-2 cursor-pointer transition-all">
                        <i class="fa-solid fa-rotate"></i> {{ __('Recurring Schedule') }}
                    </button>
                </div>
            </div>

            {{-- ── Existing Recurring Schedules List ──────────────────────────────── --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-rotate text-indigo-500"></i>
                        {{ __('Active Recurring Schedules') }}
                        <span
                            class="px-2 py-0.5 text-[10px] bg-indigo-100 text-indigo-700 rounded-full font-mono font-bold">{{ $recurringSchedules->count() }}</span>
                    </h3>
                </div>

                @if ($recurringSchedules->isEmpty())
                    <div
                        class="text-center py-12 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                        <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                        <p class="text-sm font-semibold text-slate-500">{{ __('No recurring schedules created yet.') }}
                        </p>
                        <p class="text-xs font-mono text-slate-400 mt-1">
                            {{ __('Create your first recurring schedule to auto-generate sessions.') }}</p>
                    </div>
                @else
                    <div id="schedulesListContainer"
                        class="space-y-3 max-h-[550px] overflow-y-auto custom-scrollbar pr-1">
                        @foreach ($recurringSchedules as $rs)
                            @php
                                $rsSessions = $rs->sessions ?? collect();
                                $rsNextSession = $rsSessions
                                    ->where('scheduled_at', '>=', now())
                                    ->sortBy('scheduled_at')
                                    ->first();
                                $rsCompletedCount = $rsSessions->where('status', 'completed')->count();
                                $rsTotalCount = $rsSessions->count();
                            @endphp
                            <div id="recurringScheduleCard_{{ $rs->id }}"
                                class="schedule-item-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-indigo-50/30 dark:hover:bg-indigo-950/20 hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors space-y-3"
                                data-id="{{ $rs->id }}" data-title="{{ $rs->title }}"
                                data-course-id="{{ $rs->course_id }}"
                                data-recurrence-type="{{ $rs->recurrence_type }}"
                                data-start-time="{{ $rs->start_time ? substr($rs->start_time, 0, 5) : '10:00' }}"
                                data-duration="{{ $rs->duration_minutes ?? 60 }}"
                                data-start-date="{{ $rs->start_date ? $rs->start_date->format('Y-m-d') : '' }}"
                                data-end-date="{{ $rs->end_date ? $rs->end_date->format('Y-m-d') : '' }}"
                                data-days='@json($rs->days_of_week ?? [])'
                                data-meeting-link="{{ $rs->meeting_link ?? '' }}" data-notes="{{ $rs->notes ?? '' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white">
                                                {{ $rs->title ?? __('Schedule') }}</h4>
                                            <span
                                                class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full
                                                {{ $rs->recurrence_type === 'weekly'
                                                    ? 'bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                                                    : ($rs->recurrence_type === 'monthly'
                                                        ? 'bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800'
                                                        : ($rs->recurrence_type === 'yearly'
                                                            ? 'bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                                            : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300')) }}">
                                                {{ Str::ucfirst($rs->recurrence_type ?? 'custom') }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-book-open"></i> {{ $rs->course?->title ?? __('N/A') }}
                                            @if ($rsNextSession)
                                                &bull; <i class="fa-solid fa-clock"></i> {{ __('Next') }}:
                                                {{ $rsNextSession->scheduled_at?->format('M d, Y H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-center">
                                            <p
                                                class="font-heading font-black text-lg text-indigo-600 dark:text-indigo-400">
                                                {{ $rsTotalCount }}</p>
                                            <p class="text-[10px] font-mono text-slate-400">{{ __('Sessions') }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p
                                                class="font-heading font-black text-lg text-emerald-600 dark:text-emerald-400">
                                                {{ $rsCompletedCount }}</p>
                                            <p class="text-[10px] font-mono text-slate-400">{{ __('Done') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center gap-2 pt-2.5 border-t border-slate-200/80 dark:border-slate-700/80 justify-end">
                                    <button type="button" data-id="{{ $rs->id }}"
                                        data-title="{{ $rs->title }}" data-course-id="{{ $rs->course_id }}"
                                        data-recurrence-type="{{ $rs->recurrence_type }}"
                                        data-start-time="{{ $rs->start_time ? substr($rs->start_time, 0, 5) : '10:00' }}"
                                        data-duration="{{ $rs->duration_minutes ?? 60 }}"
                                        data-start-date="{{ $rs->start_date ? $rs->start_date->format('Y-m-d') : '' }}"
                                        data-end-date="{{ $rs->end_date ? $rs->end_date->format('Y-m-d') : '' }}"
                                        data-days='@json($rs->days_of_week ?? [])'
                                        data-day-start-times='@json($rs->day_start_times ?? [])'
                                        data-day-durations='@json($rs->day_durations ?? [])'
                                        data-day-meeting-links='@json($rs->day_meeting_links ?? [])'
                                        data-meeting-link="{{ $rs->meeting_link ?? '' }}"
                                        data-notes="{{ $rs->notes ?? '' }}"
                                        onclick="openEditRecurringScheduleModal({{ $rs->id }}, this)"
                                        class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300 text-xs font-bold rounded-xl border border-indigo-200/70 dark:border-indigo-800/70 transition-all cursor-pointer flex items-center gap-1.5">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        <span>{{ __('Edit Schedule') }}</span>
                                    </button>
                                    <button type="button"
                                        onclick="confirmDeleteRecurringSchedule({{ $rs->id }}, '{{ addslashes($rs->title ?? __('Schedule')) }}')"
                                        class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 text-xs font-bold rounded-xl border border-rose-200/70 dark:border-rose-800/70 transition-all cursor-pointer flex items-center gap-1.5">
                                        <i class="fa-solid fa-trash-can text-rose-500 text-xs"></i>
                                        <span>{{ __('Delete Schedule') }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Schedules Pagination Container --}}
                    <div id="schedulesPagination" class="mt-4"></div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: FULL CALENDAR SCHEDULE VIEW                                        --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-calendar"
            class="teacher-tab-content {{ $activeTabKey === 'calendar' ? '' : 'hidden' }} space-y-5">

            <style>
                /* ── Responsive 2-Column Dashboard Layout (Desktop side-by-side & Mobile stacked) ── */
                .cal-dashboard-grid {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 1.25rem !important;
                    width: 100% !important;
                }

                @media (min-width: 1024px) {
                    .cal-dashboard-grid {
                        flex-direction: row !important;
                        align-items: flex-start !important;
                        gap: 1.5rem !important;
                    }

                    .cal-sidebar-col {
                        display: block !important;
                        width: 320px !important;
                        min-width: 320px !important;
                        max-width: 320px !important;
                        flex-shrink: 0 !important;
                    }

                    .cal-main-col {
                        flex: 1 1 0% !important;
                        min-width: 0 !important;
                        width: calc(100% - 344px) !important;
                    }
                }

                /* 7-Column Grid rules for Month & Mini Calendar */
                .cal-grid-7 {
                    display: grid !important;
                    grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                }

                /* Timetable wrapper with smooth touch scrolling */
                .cal-timetable-wrapper {
                    overflow-x: auto !important;
                    -webkit-overflow-scrolling: touch !important;
                    scroll-behavior: smooth !important;
                    width: 100% !important;
                }

                .cal-timetable-grid {
                    display: grid !important;
                    grid-template-columns: 50px repeat(7, minmax(130px, 1fr)) !important;
                }

                @media (min-width: 1280px) {
                    .cal-timetable-grid {
                        grid-template-columns: 56px repeat(7, minmax(0, 1fr)) !important;
                    }
                }

                .cal-day-col {
                    position: relative;
                    height: 910px;
                    /* 14 hours (08:00-22:00) * 65px */
                    border-inline-end: 1px solid rgba(226, 232, 240, 0.7);
                    background-size: 100% 65px;
                    background-image: linear-gradient(to bottom, transparent 64px, rgba(226, 232, 240, 0.6) 64px);
                }

                .dark .cal-day-col {
                    border-inline-end: 1px solid rgba(30, 41, 59, 0.7);
                    background-image: linear-gradient(to bottom, transparent 64px, rgba(30, 41, 59, 0.6) 64px);
                }

                /* Single Day View Timetable Grid */
                .cal-single-day-grid {
                    display: grid !important;
                    grid-template-columns: 52px 1fr !important;
                }

                @media (min-width: 640px) {
                    .cal-single-day-grid {
                        grid-template-columns: 60px 1fr !important;
                    }
                }

                .cal-single-day-col {
                    position: relative;
                    height: 910px;
                    border-inline-start: 1px solid rgba(226, 232, 240, 0.8);
                    background-size: 100% 65px;
                    background-image: linear-gradient(to bottom, transparent 64px, rgba(226, 232, 240, 0.6) 64px);
                }

                .dark .cal-single-day-col {
                    border-inline-start: 1px solid rgba(30, 41, 59, 0.8);
                    background-image: linear-gradient(to bottom, transparent 64px, rgba(30, 41, 59, 0.6) 64px);
                }

                .cal-hour-label {
                    height: 65px;
                    border-bottom: 1px solid transparent;
                }

                /* Month Cell Adaptive Sizing */
                @media (max-width: 639px) {
                    .cal-month-cell {
                        min-height: 52px !important;
                        padding: 0.25rem !important;
                    }

                    .cal-grid-cell-content-desktop {
                        display: none !important;
                    }

                    .cal-grid-cell-content-mobile {
                        display: flex !important;
                    }
                }

                @media (min-width: 640px) {
                    .cal-month-cell {
                        min-height: 100px !important;
                        padding: 0.5rem !important;
                    }

                    .cal-grid-cell-content-desktop {
                        display: block !important;
                    }

                    .cal-grid-cell-content-mobile {
                        display: none !important;
                    }
                }

                /* Pastel Event Card Styles */
                .cal-card-emerald {
                    background-color: #ecfdf5 !important;
                    color: #065f46 !important;
                    border: 1px solid #a7f3d0 !important;
                    border-inline-start: 4px solid #10b981 !important;
                }

                .dark .cal-card-emerald {
                    background-color: rgba(6, 78, 59, 0.35) !important;
                    color: #a7f3d0 !important;
                    border: 1px solid rgba(16, 185, 129, 0.4) !important;
                    border-inline-start: 4px solid #10b981 !important;
                }

                .cal-card-purple {
                    background-color: #f5f3ff !important;
                    color: #5b21b6 !important;
                    border: 1px solid #ddd6fe !important;
                    border-inline-start: 4px solid #8b5cf6 !important;
                }

                .dark .cal-card-purple {
                    background-color: rgba(91, 33, 182, 0.35) !important;
                    color: #ddd6fe !important;
                    border: 1px solid rgba(139, 92, 246, 0.4) !important;
                    border-inline-start: 4px solid #8b5cf6 !important;
                }

                .cal-card-sky {
                    background-color: #f0f9ff !important;
                    color: #0369a1 !important;
                    border: 1px solid #bae6fd !important;
                    border-inline-start: 4px solid #0284c7 !important;
                }

                .dark .cal-card-sky {
                    background-color: rgba(3, 105, 161, 0.35) !important;
                    color: #bae6fd !important;
                    border: 1px solid rgba(2, 132, 199, 0.4) !important;
                    border-inline-start: 4px solid #0284c7 !important;
                }

                .cal-card-amber {
                    background-color: #fffbeb !important;
                    color: #92400e !important;
                    border: 1px solid #fde68a !important;
                    border-inline-start: 4px solid #f59e0b !important;
                }

                .dark .cal-card-amber {
                    background-color: rgba(146, 64, 14, 0.35) !important;
                    color: #fde68a !important;
                    border: 1px solid rgba(245, 158, 11, 0.4) !important;
                    border-inline-start: 4px solid #f59e0b !important;
                }

                .cal-card-rose {
                    background-color: #fff1f2 !important;
                    color: #9f1239 !important;
                    border: 1px solid #fecdd3 !important;
                    border-inline-start: 4px solid #f43f5e !important;
                }

                .dark .cal-card-rose {
                    background-color: rgba(159, 18, 57, 0.35) !important;
                    color: #fecdd3 !important;
                    border: 1px solid rgba(244, 63, 94, 0.4) !important;
                    border-inline-start: 4px solid #f43f5e !important;
                }
            </style>

            {{-- Executive Top Banner — Light & Dark Adaptive & Fully Mobile Responsive --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-indigo-100 dark:border-indigo-900/60 bg-white dark:bg-slate-900 shadow-sm">
                {{-- Decorative gradient blobs --}}
                <div
                    class="pointer-events-none absolute -top-10 -end-10 w-60 h-60 rounded-full bg-indigo-100/70 dark:bg-indigo-900/30 blur-3xl">
                </div>
                <div
                    class="pointer-events-none absolute bottom-0 start-24 w-40 h-40 rounded-full bg-violet-100/60 dark:bg-violet-900/20 blur-2xl">
                </div>

                <div
                    class="relative p-4 sm:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    {{-- Left: Icon + Title --}}
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        <div
                            class="w-11 h-11 sm:w-12 sm:h-12 shrink-0 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg sm:text-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div class="min-w-0">
                            <h2
                                class="font-heading text-lg sm:text-2xl font-black text-slate-900 dark:text-white leading-tight truncate">
                                {{ __('Interactive Calendar Schedule') }}
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-mono mt-0.5 truncate">
                                {{ __('View daily & weekly timetables, filter by categories & launch live sessions.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Right: Quick stats + Action Buttons (Responsive on Mobile) --}}
                    <div
                        class="flex items-center justify-between md:justify-end gap-2.5 sm:gap-3 shrink-0 self-stretch md:self-center flex-wrap">
                        {{-- Stat Pills (Visible across all screens) --}}
                        <div class="flex items-center gap-2">
                            <div
                                class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/60 text-center">
                                <span
                                    class="block text-[9px] sm:text-[10px] font-mono font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">{{ __('Total') }}</span>
                                <span id="calBannerTotalStat"
                                    class="block text-sm sm:text-base font-black text-indigo-700 dark:text-indigo-300 leading-tight">—</span>
                            </div>
                            <div
                                class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/60 text-center">
                                <span
                                    class="block text-[9px] sm:text-[10px] font-mono font-bold text-emerald-500 dark:text-emerald-400 uppercase tracking-wide">{{ __('This Week') }}</span>
                                <span id="calBannerWeekStat"
                                    class="block text-sm sm:text-base font-black text-emerald-700 dark:text-emerald-300 leading-tight">—</span>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="hidden sm:block w-px h-9 bg-slate-200 dark:bg-slate-700"></div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openScheduleModal('singleSessionModal')"
                                class="px-3 sm:px-4 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-extrabold text-xs rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 cursor-pointer transition-all shadow-xs">
                                <i class="fa-solid fa-plus text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                <span class="hidden xs:inline sm:inline">{{ __('Single Session') }}</span>
                                <span class="inline xs:hidden sm:hidden">{{ __('Single') }}</span>
                            </button>
                            <button type="button" onclick="openScheduleModal('recurringSchedulePortalModal')"
                                class="px-3 sm:px-4 py-2 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/50 flex items-center gap-1.5 cursor-pointer transition-all">
                                <i class="fa-solid fa-rotate text-xs"></i>
                                <span class="hidden xs:inline sm:inline">{{ __('Recurring') }}</span>
                                <span class="inline xs:hidden sm:hidden">{{ __('Recur') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Tools & Filters Toggle Card (< 1024px) --}}
            <div class="lg:hidden">
                <button type="button" onclick="toggleCalendarSidebarMobile()"
                    class="w-full flex items-center justify-between p-3.5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer select-none">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span
                            class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-sliders"></i>
                        </span>
                        <div class="text-start min-w-0">
                            <span
                                class="font-heading font-extrabold text-xs text-slate-800 dark:text-slate-200 block truncate">
                                {{ __('Filters, Mini Calendar & Tools') }}
                            </span>
                            <span id="calMobileFilterStatus"
                                class="text-[10px] font-mono text-slate-500 dark:text-slate-400 block truncate">
                                {{ __('Tap to toggle mini month, course filters & quick actions') }}
                            </span>
                        </div>
                    </div>
                    <span id="calSidebarToggleIcon"
                        class="w-7 h-7 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 shrink-0 transition-transform duration-200">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </button>
            </div>

            {{-- ════════════════════════════════════════════════════════════════════════ --}}
            {{-- MAIN DASHBOARD: 2-COLUMN VIEW MATCHING DESIGN REFERENCE                  --}}
            {{-- ════════════════════════════════════════════════════════════════════════ --}}
            <div class="cal-dashboard-grid">

                {{-- ── LEFT COLUMN: Mini Calendar, Categories & Prioritize (Collapsible on Mobile) ── --}}
                <div id="calSidebarCol" class="cal-sidebar-col hidden lg:block space-y-5">

                    {{-- 1. Mini Month Calendar Widget --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3 sm:space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 id="miniCalTitle"
                                class="font-heading font-black text-sm text-slate-900 dark:text-white truncate">
                                -- --
                            </h4>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="navigateMiniMonth(-1)"
                                    title="{{ __('Previous Month') }}"
                                    class="w-7 h-7 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-left text-xs rtl:rotate-180"></i>
                                </button>
                                <button type="button" onclick="navigateMiniMonth(1)" title="{{ __('Next Month') }}"
                                    class="w-7 h-7 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-right text-xs rtl:rotate-180"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Mini Weekday Headers --}}
                        <div
                            class="cal-grid-7 text-center text-[10px] font-mono font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider pb-1 border-b border-slate-100 dark:border-slate-800/80">
                            <div>{{ __('Sat') }}</div>
                            <div>{{ __('Sun') }}</div>
                            <div>{{ __('Mon') }}</div>
                            <div>{{ __('Tue') }}</div>
                            <div>{{ __('Wed') }}</div>
                            <div>{{ __('Thu') }}</div>
                            <div>{{ __('Fri') }}</div>
                        </div>

                        {{-- Mini Month Days Grid --}}
                        <div id="miniCalDaysGrid" class="cal-grid-7 gap-y-1 gap-x-1 text-center py-1">
                            {{-- Rendered dynamically in JS --}}
                        </div>
                    </div>

                    {{-- 2. Categories / Courses Card with Durations & Checkboxes --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <h4
                                class="font-heading font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ __('Categories') }}</span>
                            </h4>
                            <button type="button" onclick="resetCourseCategoryFilters()"
                                class="text-[11px] font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer">
                                {{ __('Select All') }}
                            </button>
                        </div>
                        <div id="calCategoriesList" class="space-y-1.5 pt-1 max-h-56 overflow-y-auto custom-scrollbar">
                            {{-- Rendered dynamically in JS with color boxes and calculated durations --}}
                        </div>
                    </div>

                    {{-- 3. Prioritize / Quick Actions Card --}}
                    <div
                        class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-3">
                        <h4 class="font-heading font-black text-sm text-slate-900 dark:text-white">
                            {{ __('Prioritize') }}
                        </h4>
                        <div class="space-y-2">
                            <button type="button" onclick="focusCalendarToday()"
                                class="w-full p-2.5 rounded-2xl bg-slate-50 hover:bg-indigo-50/60 dark:bg-slate-800/60 dark:hover:bg-indigo-950/40 border border-slate-200/70 dark:border-slate-800 flex items-center justify-between text-xs font-heading font-bold text-slate-700 dark:text-slate-300 transition-all cursor-pointer group">
                                <span class="flex items-center gap-2.5">
                                    <span
                                        class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </span>
                                    <span>{{ __('Today\'s Sessions') }}</span>
                                </span>
                                <i
                                    class="fa-solid fa-chevron-right text-[10px] text-slate-400 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform"></i>
                            </button>

                            <button type="button" onclick="openScheduleModal('singleSessionModal')"
                                class="w-full p-2.5 rounded-2xl bg-slate-50 hover:bg-emerald-50/60 dark:bg-slate-800/60 dark:hover:bg-emerald-950/40 border border-slate-200/70 dark:border-slate-800 flex items-center justify-between text-xs font-heading font-bold text-slate-700 dark:text-slate-300 transition-all cursor-pointer group">
                                <span class="flex items-center gap-2.5">
                                    <span
                                        class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <span>{{ __('Single Session') }}</span>
                                </span>
                                <i
                                    class="fa-solid fa-chevron-right text-[10px] text-slate-400 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform"></i>
                            </button>

                            <button type="button" onclick="openScheduleModal('recurringSchedulePortalModal')"
                                class="w-full p-2.5 rounded-2xl bg-slate-50 hover:bg-purple-50/60 dark:bg-slate-800/60 dark:hover:bg-purple-950/40 border border-slate-200/70 dark:border-slate-800 flex items-center justify-between text-xs font-heading font-bold text-slate-700 dark:text-slate-300 transition-all cursor-pointer group">
                                <span class="flex items-center gap-2.5">
                                    <span
                                        class="w-7 h-7 rounded-xl bg-purple-100 dark:bg-purple-900/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-rotate"></i>
                                    </span>
                                    <span>{{ __('Recurring Schedule') }}</span>
                                </span>
                                <i
                                    class="fa-solid fa-chevron-right text-[10px] text-slate-400 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform"></i>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ── RIGHT MAIN PANEL: HERO TIMETABLE & OTHER VIEWS ───────────────── --}}
                <div id="calendarRightPanel"
                    class="cal-main-col bg-white dark:bg-slate-900 rounded-3xl p-3.5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">

                    {{-- Top Toolbar — Fully Responsive Mobile & Desktop --}}
                    <div
                        class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3 pb-3.5 border-b border-slate-100 dark:border-slate-800">

                        {{-- Row 1: Navigation arrows, Date Range Title & Week/Day Badge --}}
                        <div class="flex items-center justify-between sm:justify-start gap-2 sm:gap-3 flex-wrap">
                            <div
                                class="flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200 dark:border-slate-700 shrink-0">
                                <button type="button" onclick="navigateCalendarStep(-1)"
                                    title="{{ __('Previous') }}"
                                    class="w-8 h-8 rounded-xl hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-left text-xs rtl:rotate-180"></i>
                                </button>
                                <button type="button" onclick="navigateCalendarToday()"
                                    class="px-2.5 py-1 rounded-xl hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 font-mono font-extrabold text-xs transition-all cursor-pointer whitespace-nowrap">
                                    {{ __('Today') }}
                                </button>
                                <button type="button" onclick="navigateCalendarStep(1)" title="{{ __('Next') }}"
                                    class="w-8 h-8 rounded-xl hover:bg-white dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center transition-all cursor-pointer">
                                    <i class="fa-solid fa-chevron-right text-xs rtl:rotate-180"></i>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                <h3 id="calendarHeroTitle"
                                    class="font-heading font-black text-sm sm:text-base md:text-xl text-slate-900 dark:text-white truncate">
                                    -- --
                                </h3>
                                <span id="calendarWeekBadge"
                                    class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shrink-0">
                                    --
                                </span>
                            </div>
                        </div>

                        {{-- Row 2: View switcher pills (Day, Week, Month, Agenda) & + Create button --}}
                        <div class="flex items-center justify-between sm:justify-end gap-2 w-full xl:w-auto">

                            {{-- View Switcher Pills --}}
                            <div
                                class="flex-1 sm:flex-initial flex items-center justify-center bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-x-auto custom-scrollbar">
                                <button type="button" id="calViewBtnDay" onclick="switchCalendarView('day')"
                                    class="cal-view-tab-btn flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-xl font-heading font-extrabold text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white whitespace-nowrap text-center flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-calendar-day text-xs"></i> <span>{{ __('Day') }}</span>
                                </button>
                                <button type="button" id="calViewBtnWeek" onclick="switchCalendarView('week')"
                                    class="cal-view-tab-btn flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-xl font-heading font-extrabold text-xs transition-all bg-indigo-600 text-white shadow-2xs whitespace-nowrap text-center flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-calendar-week text-xs"></i> <span>{{ __('Week') }}</span>
                                </button>
                                <button type="button" id="calViewBtnGrid" onclick="switchCalendarView('grid')"
                                    class="cal-view-tab-btn flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-xl font-heading font-extrabold text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white whitespace-nowrap text-center flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-grid-2 text-xs"></i> <span>{{ __('Month') }}</span>
                                </button>
                                <button type="button" id="calViewBtnAgenda" onclick="switchCalendarView('agenda')"
                                    class="cal-view-tab-btn flex-1 sm:flex-initial px-2.5 sm:px-3 py-1.5 rounded-xl font-heading font-extrabold text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white whitespace-nowrap text-center flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-list-check text-xs"></i> <span>{{ __('Agenda') }}</span>
                                </button>
                            </div>

                            {{-- + Create Button with Dropdown --}}
                            <div class="relative inline-block text-left shrink-0" id="calCreateDropdownWrapper">
                                <button type="button" onclick="toggleCalCreateDropdown()"
                                    class="px-3 sm:px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-heading font-extrabold text-xs rounded-2xl shadow-md flex items-center gap-1.5 cursor-pointer transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span class="hidden xs:inline">{{ __('Create') }}</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] opacity-75"></i>
                                </button>
                                <div id="calCreateDropdownMenu"
                                    class="hidden absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-48 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl py-2 z-30 space-y-1">
                                    <button type="button"
                                        onclick="openScheduleModal('singleSessionModal'); toggleCalCreateDropdown();"
                                        class="w-full text-start px-3.5 py-2 text-xs font-heading font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-calendar-plus text-indigo-500"></i>
                                        {{ __('Single Session') }}
                                    </button>
                                    <button type="button"
                                        onclick="openScheduleModal('recurringSchedulePortalModal'); toggleCalCreateDropdown();"
                                        class="w-full text-start px-3.5 py-2 text-xs font-heading font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                                        <i class="fa-solid fa-rotate text-purple-500"></i>
                                        {{ __('Recurring Schedule') }}
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- VIEW 1: DEDICATED DAY HOURLY TIMETABLE (MOBILE & FOCUSED DAILY WORKFLOW) --}}
                    <div id="calendarDayView" class="hidden space-y-3">
                        {{-- Top Day Strip for Quick Jumping in Day View --}}
                        <div
                            class="flex items-center justify-between gap-1.5 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                            <span
                                class="text-[10px] font-mono font-bold text-slate-400 shrink-0 uppercase tracking-wider ps-1">{{ __('Days') }}:</span>
                            <div id="calDayViewDayStrip"
                                class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar flex-1 py-0.5">
                                {{-- Rendered dynamically in JS --}}
                            </div>
                        </div>

                        {{-- Single Day Hourly Grid Container --}}
                        <div
                            class="overflow-x-auto custom-scrollbar rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80">
                            <div id="calendarDayContainer" class="w-full space-y-2 pb-2">
                                {{-- Rendered dynamically in JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- VIEW 2: HERO WEEK TIMETABLE --}}
                    <div id="calendarWeekView" class="space-y-3">
                        {{-- Mobile Day Selector Strip (Tap any day to smoothly center-scroll into it) --}}
                        <div
                            class="sm:hidden flex items-center justify-between gap-1.5 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                            <span
                                class="text-[10px] font-mono font-bold text-slate-400 shrink-0 uppercase tracking-wider ps-1">{{ __('Days') }}:</span>
                            <div id="calMobileDayStrip"
                                class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar flex-1 py-0.5">
                                {{-- Rendered dynamically in JS --}}
                            </div>
                        </div>

                        {{-- Timetable Grid Horizontal Scroll Container --}}
                        <div
                            class="overflow-x-auto custom-scrollbar cal-timetable-wrapper rounded-2xl border border-slate-100 dark:border-slate-800/80">
                            <div id="calendarWeekContainer" class="min-w-[920px] xl:min-w-full space-y-2 pb-2">
                                {{-- Rendered dynamically in JS with day columns and hourly pastel session blocks --}}
                            </div>
                        </div>
                    </div>

                    {{-- VIEW 3: MONTH GRID VIEW --}}
                    <div id="calendarGridView" class="hidden space-y-3">
                        <div
                            class="cal-grid-7 text-center font-heading font-black text-xs text-slate-600 dark:text-slate-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                            <div>{{ __('Sat') }}</div>
                            <div>{{ __('Sun') }}</div>
                            <div>{{ __('Mon') }}</div>
                            <div>{{ __('Tue') }}</div>
                            <div>{{ __('Wed') }}</div>
                            <div>{{ __('Thu') }}</div>
                            <div>{{ __('Fri') }}</div>
                        </div>
                        <div id="calendarMonthGrid" class="cal-grid-7 gap-1 sm:gap-2">
                            {{-- Rendered dynamically in JS --}}
                        </div>
                    </div>

                    {{-- VIEW 4: DAILY AGENDA LIST VIEW --}}
                    <div id="calendarAgendaView" class="hidden space-y-4">
                        <div id="calendarAgendaContainer" class="space-y-4">
                            {{-- Rendered dynamically in JS --}}
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- MODAL: DAY DETAILS MODAL                                                 --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="dayDetailsModal"
            class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
            <div
                class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] max-w-xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 flex flex-col max-h-[85vh] overflow-hidden my-auto">
                <div
                    class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                    <h3 id="dayDetailsModalTitle"
                        class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white flex items-center gap-2 truncate">
                        <i class="fa-solid fa-calendar-day text-indigo-500"></i>
                        <span>{{ __('Sessions for Date') }}</span>
                    </h3>
                    <button type="button" onclick="closeModal('dayDetailsModal')"
                        class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center cursor-pointer hover:bg-slate-200 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div id="dayDetailsModalBody" class="p-4 sm:p-5 overflow-y-auto space-y-3 custom-scrollbar">
                    {{-- Session cards for clicked date --}}
                </div>
                <div class="p-3.5 sm:p-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="button" onclick="closeModal('dayDetailsModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl cursor-pointer">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        {{-- TAB 7: STUDENT ABSENCE EXCUSES & EXCEPTION REQUESTS                      --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-exceptions"
            class="teacher-tab-content {{ $activeTabKey === 'exceptions' ? '' : 'hidden' }} space-y-6">

            {{-- Header Banner & Deduction Policy Notice --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                <div
                    class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base border border-amber-200/60 dark:border-amber-800">
                                <i class="fa-solid fa-file-signature"></i>
                            </span>
                            <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                {{ __('Student Excuses & Exception Requests') }}
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl">
                            {{ __('Review student absence excuses and homework exceptions for your courses and sessions. Approving an excuse preserves the student session balance; rejecting an excuse deducts 1 session credit from their active package.') }}
                        </p>
                    </div>

                    {{-- Quick Counters --}}
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <div
                            class="px-3.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-clock"></i>
                            <span>{{ __('Pending') }}:</span>
                            <span id="statPendingExceptionsCount"
                                class="font-mono font-extrabold">{{ $exceptions->where('status', 'pending')->count() }}</span>
                        </div>
                        <div
                            class="px-3.5 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('Approved') }}:</span>
                            <span id="statApprovedExceptionsCount"
                                class="font-mono font-extrabold">{{ $exceptions->where('status', 'approved')->count() }}</span>
                        </div>
                        <div
                            class="px-3.5 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span>{{ __('Rejected') }}:</span>
                            <span id="statRejectedExceptionsCount"
                                class="font-mono font-extrabold">{{ $exceptions->where('status', 'rejected')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Status Filter Buttons --}}
                <div class="flex items-center gap-2 flex-wrap pt-1">
                    <button type="button" onclick="filterTeacherExceptions('all', this)"
                        class="exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-teal-600 text-white shadow-xs transition-all cursor-pointer">
                        {{ __('All Requests') }} ({{ $exceptions->count() }})
                    </button>
                    <button type="button" onclick="filterTeacherExceptions('pending', this)"
                        class="exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-amber-50 dark:hover:bg-amber-950 text-slate-700 dark:text-slate-300 transition-all cursor-pointer">
                        {{ __('Pending Review') }} ({{ $exceptions->where('status', 'pending')->count() }})
                    </button>
                    <button type="button" onclick="filterTeacherExceptions('approved', this)"
                        class="exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950 text-slate-700 dark:text-slate-300 transition-all cursor-pointer">
                        {{ __('Approved') }} ({{ $exceptions->where('status', 'approved')->count() }})
                    </button>
                    <button type="button" onclick="filterTeacherExceptions('rejected', this)"
                        class="exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950 text-slate-700 dark:text-slate-300 transition-all cursor-pointer">
                        {{ __('Rejected') }} ({{ $exceptions->where('status', 'rejected')->count() }})
                    </button>
                </div>
            </div>

            {{-- Exception Request Cards List --}}
            @if ($exceptions->isEmpty())
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl p-12 border border-slate-200/90 dark:border-slate-800 text-center space-y-3 shadow-md">
                    <div
                        class="w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center text-2xl border border-teal-200/60 dark:border-teal-800">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">
                        {{ __('No Exception Requests Found') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                        {{ __('No student absence excuses or homework exception requests currently submitted for your teaching cohorts.') }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4" id="teacherExceptionsList">
                    @foreach ($exceptions as $exc)
                        @php
                            $stUser = $exc->studentUser;
                            $stProfile = $stUser?->studentProfile;
                            $liveSession = $exc->liveSession;
                            $status = $exc->status;
                            $isPending = $status === 'pending';
                            $isApproved = $status === 'approved';
                            $isRejected = $status === 'rejected';
                        @endphp
                        <div class="teacher-exception-card bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 shadow-md hover:shadow-lg transition-all duration-200 space-y-4"
                            data-status="{{ $status }}" id="exception-card-{{ $exc->id }}">

                            {{-- Top Header Row: Student Info & Status Badge --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-11 h-11 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-black text-sm shrink-0 overflow-hidden">
                                        @if ($stProfile?->avatar)
                                            <img src="{{ $stProfile->avatar }}" alt="{{ $stUser?->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($stUser?->name ?? 'S', 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-2">
                                            <h4
                                                class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white">
                                                {{ $stUser?->name ?: __('Student') }}
                                            </h4>
                                            <span
                                                class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-slate-600 dark:text-slate-400">
                                                {{ 'STU-' . str_pad((string) $exc->student_user_id, 5, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                            {{ $stUser?->email ?: '' }}</p>
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex items-center gap-2 shrink-0"
                                    id="status-badge-container-{{ $exc->id }}">
                                    @if ($isApproved)
                                        <span
                                            class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800 text-xs font-extrabold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>{{ __('Approved (Session Kept)') }}</span>
                                        </span>
                                    @elseif ($isRejected)
                                        <span
                                            class="px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 text-xs font-extrabold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            <span>{{ __('Rejected (Session Deducted)') }}</span>
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800 text-xs font-extrabold flex items-center gap-1.5 animate-pulse">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                            <span>{{ __('Pending Review') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Context Details: Course & Specific Session --}}
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 text-xs">
                                <div class="space-y-1">
                                    <span
                                        class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-book-open text-teal-600"></i>
                                        {{ __('Target Course') }}
                                    </span>
                                    <p class="font-bold text-slate-900 dark:text-slate-100">
                                        {{ $exc->course?->title ?: __('All Courses (Global Exemption)') }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <span
                                        class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-video text-indigo-600"></i>
                                        {{ __('Target Session') }}
                                    </span>
                                    <p class="font-bold text-slate-900 dark:text-slate-100">
                                        @if ($liveSession)
                                            {{ $liveSession->title ?: 'Live Session #' . $liveSession->id }}
                                            <span class="font-mono text-slate-500 text-[11px]">
                                                ({{ $liveSession->scheduled_at ? $liveSession->scheduled_at->format('M d, H:i') : __('Scheduled') }})
                                            </span>
                                        @else
                                            <span
                                                class="text-slate-500 italic">{{ __('General Course Exception') }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Excuse Reason Details --}}
                            <div class="space-y-1.5">
                                <label
                                    class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase font-mono flex items-center gap-1.5">
                                    <i class="fa-solid fa-quote-left text-amber-500"></i>
                                    {{ __('Excuse Reason Details') }}
                                </label>
                                <div
                                    class="p-3.5 rounded-2xl bg-[#FAFAF9] dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
                                    {{ $exc->reason }}
                                </div>
                            </div>

                            {{-- Footer Row: Timestamp & Actions --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                                <div class="text-[11px] font-mono text-slate-400 flex items-center gap-2">
                                    <span><i class="fa-solid fa-calendar-day"></i>
                                        {{ $exc->created_at->format('Y-m-d H:i') }}</span>
                                    @if ($exc->reviewed_at)
                                        <span>&bull; {{ __('Reviewed') }}:
                                            {{ $exc->reviewed_at->diffForHumans() }}</span>
                                    @endif
                                </div>

                                {{-- Action Buttons: Approve / Reject --}}
                                <div class="flex items-center gap-2 self-end sm:self-auto"
                                    id="exception-actions-{{ $exc->id }}">
                                    @if ($status !== 'approved')
                                        <button type="button"
                                            onclick="reviewTeacherException({{ $exc->id }}, 'approve')"
                                            class="btn-lift px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all">
                                            <i class="fa-solid fa-check"></i>
                                            <span>{{ __('Approve (Keep Session)') }}</span>
                                        </button>
                                    @endif

                                    @if ($status !== 'rejected')
                                        <button type="button"
                                            onclick="reviewTeacherException({{ $exc->id }}, 'reject')"
                                            class="btn-lift px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>{{ __('Reject (Deduct Session)') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: CREATE SINGLE SESSION (Schedules Tab)                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    <div id="singleSessionModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-t-[28px] sm:rounded-[28px] max-w-lg w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 max-h-[92dvh] flex flex-col overflow-hidden"
            style="padding-bottom: env(safe-area-inset-bottom)">
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <h3 class="font-heading font-black text-base text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><i
                            class="fa-solid fa-plus text-sm"></i></span>
                    {{ __('Schedule Single Session') }}
                </h3>
                <button type="button" onclick="closeScheduleModal('singleSessionModal')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center cursor-pointer"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>
            <form id="singleSessionPortalForm" class="flex-1 overflow-y-auto p-5 space-y-4">
                @csrf
                <div>
                    <label
                        class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}
                        *</label>
                    <input type="text" name="title" required
                        placeholder="{{ __('e.g. Math Chapter 5 Review') }}" class="input-mobile">
                </div>
                <div>
                    <label
                        class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Course') }}
                        *</label>
                    <select name="course_id" id="singleSessionCourseId" required class="input-mobile">
                        <option value="">{{ __('Select Course') }}</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Date & Time') }}
                        *</label>
                    <input type="datetime-local" name="scheduled_at" required class="input-mobile"
                        min="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <div>
                    <label
                        class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (minutes)') }}</label>
                    <input type="number" name="duration_minutes" value="60" min="15" max="300"
                        class="input-mobile">
                </div>
                <div>
                    <label
                        class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Link') }}</label>
                    <input type="url" name="meeting_link" placeholder="https://..." class="input-mobile">
                </div>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeScheduleModal('singleSessionModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" id="singleSessionSubmitBtn"
                        class="btn-lift px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus"></i> {{ __('Create Session') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: CREATE RECURRING SCHEDULE (Schedules Tab – Portal Embedded)           --}}
    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    <div id="recurringSchedulePortalModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto"
            style="padding-bottom: env(safe-area-inset-bottom)">
            {{-- Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <div>
                    <h3
                        class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                        <span
                            class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm shadow-2xs"><i
                                class="fa-solid fa-rotate"></i></span>
                        <span>{{ __('Create Recurring Schedule') }}</span>
                    </h3>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Create recurring lesson schedules for your courses. Enrolled students are added automatically.') }}
                    </p>
                </div>
                <button type="button" onclick="closeScheduleModal('recurringSchedulePortalModal')"
                    aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center cursor-pointer transition-all active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            {{-- Scrollable Body --}}
            <form id="recurringSchedulePortalForm" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-5">

                    {{-- Schedule Title --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule Title') }}
                            *</label>
                        <input type="text" name="title" required
                            placeholder="{{ __('e.g. Physics Secondary 3 - Weekly Interactive Cohort') }}"
                            class="input-mobile">
                    </div>

                    {{-- Course & Students --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Course') }}
                                *</label>
                            <select name="course_id" id="recPortalCourseId" required class="input-mobile text-xs"
                                onchange="fetchCourseStudents(this.value)">
                                <option value="">{{ __('Select Course') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}
                                        ({{ $course->subject?->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Enrolled Students') }}</label>
                            <div id="recPortalStudentsContainer"
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 min-h-[42px] text-xs font-mono text-slate-500 dark:text-slate-400 flex items-center">
                                <span
                                    id="recPortalStudentsPlaceholder">{{ __('Select a course to load enrolled students') }}</span>
                                <div id="recPortalStudentsList" class="hidden space-y-1 w-full"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Recurrence Pattern & Main Settings --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Recurrence Pattern') }}
                                *</label>
                            <select name="recurrence_type" id="recPortalType" required class="input-mobile text-xs"
                                onchange="handlePortalRecurrenceType(this.value)">
                                <option value="weekly" selected>{{ __('Weekly') }}</option>
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="multi_month">{{ __('Multi-Month') }}</option>
                                <option value="yearly">{{ __('Yearly') }}</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Main Start Time') }}
                                *</label>
                            <input type="time" name="start_time" id="recPortalStartTime" required value="10:00"
                                class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Default Duration (min)') }}
                                *</label>
                            <input type="number" name="duration_minutes" id="recPortalDuration" value="60"
                                min="15" max="300" class="input-mobile">
                        </div>
                    </div>

                    {{-- Date Range & Main Meeting Link --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Start Date') }}
                                *</label>
                            <input type="date" name="start_date" id="recPortalStartDate" required
                                class="input-mobile" value="{{ today()->format('Y-m-d') }}"
                                min="{{ today()->format('Y-m-d') }}" onchange="autoCalcEndDate()">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('End Date') }}
                                *</label>
                            <input type="date" name="end_date" id="recPortalEndDate"
                                value="{{ today()->addMonths(3)->format('Y-m-d') }}" required class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Default Meeting Link') }}</label>
                            <input type="url" name="meeting_link" id="recPortalMeetingLink"
                                placeholder="https://..." class="input-mobile">
                        </div>
                    </div>

                    {{-- Days of Week & Custom Per-Day Timing Grid --}}
                    <div id="recPortalDaysContainer"
                        class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/80">
                            <div>
                                <label
                                    class="block text-xs font-heading font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-week text-indigo-500 text-sm"></i>
                                    <span>{{ __('Select Teaching Days & Custom Session Times') }}</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                    {{ $isAr ? 'اختر الأيام المحددة للتكرار، ويمكنك تحديد وقت خاص ومدة ورابط اجتماع لكل يوم مستقبلي بشكل مستقل.' : 'Select days to recur and customize start times, durations & links independently per day.' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-1.5 self-start sm:self-auto shrink-0">
                                <button type="button" onclick="applyMainTimeToAllPortalDays()"
                                    class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/90 text-indigo-700 dark:text-indigo-300 text-xs font-mono font-bold rounded-xl border border-indigo-200 dark:border-indigo-800 transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>{{ $isAr ? 'مزامنة الإعدادات الرئيسية مع الأيام المحددة' : 'Sync Main Settings to All Days' }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Per-Day Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-3">
                            @php
                                $weekDaysPortalList = [
                                    ['val' => 6, 'name' => 'saturday', 'label' => __('Saturday')],
                                    ['val' => 0, 'name' => 'sunday', 'label' => __('Sunday')],
                                    ['val' => 1, 'name' => 'monday', 'label' => __('Monday')],
                                    ['val' => 2, 'name' => 'tuesday', 'label' => __('Tuesday')],
                                    ['val' => 3, 'name' => 'wednesday', 'label' => __('Wednesday')],
                                    ['val' => 4, 'name' => 'thursday', 'label' => __('Thursday')],
                                    ['val' => 5, 'name' => 'friday', 'label' => __('Friday')],
                                ];
                            @endphp

                            @foreach ($weekDaysPortalList as $wd)
                                @php
                                    $isDefaultChecked = in_array($wd['val'], [0, 2]); // Sun & Tue default
                                @endphp
                                <div id="recPortalDayCard_{{ $wd['val'] }}"
                                    class="rec-portal-day-card p-3 rounded-2xl border transition-all flex flex-col justify-between space-y-2.5 {{ $isDefaultChecked ? 'is-selected-day bg-indigo-50/90 dark:bg-indigo-950/50 border-indigo-500 shadow-xs' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 opacity-60' }}">

                                    {{-- Day Header Checkbox --}}
                                    <div class="flex items-center justify-between">
                                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox" name="days_of_week[]" value="{{ $wd['val'] }}"
                                                class="rec-portal-day-checkbox rounded w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-700 cursor-pointer"
                                                {{ $isDefaultChecked ? 'checked' : '' }}
                                                onchange="toggleDayTimeCard(this, 'recPortalDayCard_{{ $wd['val'] }}')">
                                            <span
                                                class="text-xs font-heading font-extrabold text-slate-900 dark:text-white">{{ $wd['label'] }}</span>
                                        </label>
                                        <span
                                            class="rec-day-status-badge text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md {{ $isDefaultChecked ? 'bg-indigo-100 dark:bg-indigo-900/80 text-indigo-800 dark:text-indigo-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                            {{ $isDefaultChecked ? ($isAr ? 'مُفَعَّل' : 'Active') : ($isAr ? 'غير محدد' : 'Off') }}
                                        </span>
                                    </div>

                                    {{-- Per-Day Time & Duration & Link Controls --}}
                                    <div
                                        class="rec-day-time-container space-y-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 {{ $isDefaultChecked ? '' : 'hidden' }}">
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">{{ __('Start Time') }}</label>
                                            <input type="time" name="day_start_times[{{ $wd['val'] }}]"
                                                value="10:00"
                                                class="rec-day-time-picker w-full text-xs font-mono font-bold px-2 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                                {{ $isDefaultChecked ? '' : 'disabled' }}>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">{{ __('Duration (Min)') }}</label>
                                            <input type="number" name="day_durations[{{ $wd['val'] }}]"
                                                value="60" min="15" max="300"
                                                class="rec-day-duration-picker w-full text-xs font-mono font-bold px-2 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                                {{ $isDefaultChecked ? '' : 'disabled' }}>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">{{ __('Meeting Link') }}</label>
                                            <input type="url" name="day_meeting_links[{{ $wd['val'] }}]"
                                                placeholder="https://..."
                                                class="rec-day-link-picker w-full text-[11px] font-mono px-2 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                                {{ $isDefaultChecked ? '' : 'disabled' }}>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preview Button --}}
                    <button type="button" onclick="previewPortalSchedule()"
                        class="w-full py-2.5 bg-slate-100 hover:bg-indigo-50 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 hover:border-indigo-300 text-slate-700 dark:text-slate-200 font-extrabold text-xs rounded-xl flex items-center justify-center gap-2 cursor-pointer transition-all shadow-2xs">
                        <i class="fa-solid fa-eye text-indigo-500"></i>
                        {{ __('Preview Generated Sessions & Conflicts') }}
                    </button>

                    {{-- Preview Result Container --}}
                    <div id="recPortalPreviewContainer"
                        class="hidden rounded-2xl border border-indigo-200 dark:border-indigo-800/60 bg-indigo-50/60 dark:bg-indigo-950/40 p-4 space-y-3">
                        <div id="recPortalPreviewSummary"
                            class="text-xs font-mono font-bold text-indigo-700 dark:text-indigo-300 flex items-center gap-2">
                        </div>
                        <div id="recPortalConflictWarning"
                            class="hidden p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300 text-xs font-bold flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                            <span id="recPortalConflictText"></span>
                        </div>
                        <div class="max-h-48 overflow-y-auto space-y-1.5 custom-scrollbar" id="recPortalPreviewList">
                        </div>
                    </div>

                </div>

                {{-- Sticky Footer --}}
                <div
                    class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5 bg-white dark:bg-slate-900 sticky bottom-0">
                    <button type="button" onclick="closeScheduleModal('recurringSchedulePortalModal')"
                        class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer transition-all">{{ __('Cancel') }}</button>
                    <button type="submit" id="recPortalSubmitBtn"
                        class="btn-lift px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-calendar-check"></i> {{ __('Create Schedule') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: EDIT RECURRING SCHEDULE (Schedules Tab)                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    <div id="editRecurringScheduleModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-t-[28px] sm:rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto"
            style="padding-bottom: env(safe-area-inset-bottom)">
            {{-- Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3
                    class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span
                        class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-2xs"><i
                            class="fa-solid fa-pen text-sm"></i></span>
                    <span>{{ __('Edit Recurring Schedule') }}</span>
                </h3>
                <button type="button" onclick="closeScheduleModal('editRecurringScheduleModal')"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center cursor-pointer"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            {{-- Scrollable Body --}}
            <form id="editRecurringScheduleForm" class="flex-1 overflow-y-auto custom-scrollbar">
                @csrf
                <input type="hidden" id="editRecScheduleId" name="schedule_id">
                <div class="p-4 sm:p-6 space-y-5">

                    {{-- Title --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule Title') }}
                            *</label>
                        <input type="text" id="editRecTitle" name="title" required
                            placeholder="{{ __('e.g. Math Weekly Sessions') }}" class="input-mobile">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Course (Readonly select) --}}
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Course') }}</label>
                            <select id="editRecCourseId" name="course_id" disabled
                                class="input-mobile opacity-75 cursor-not-allowed text-xs">
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Main Start Time') }}
                                *</label>
                            <input type="time" id="editRecStartTime" name="start_time" required
                                class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Default Duration (Min)') }}
                                *</label>
                            <input type="number" id="editRecDuration" name="duration_minutes" value="60"
                                min="15" max="300" required class="input-mobile">
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule Start Date') }}</label>
                            <input type="date" id="editRecStartDate" name="start_date" class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule End Date') }}</label>
                            <input type="date" id="editRecEndDate" name="end_date" class="input-mobile">
                        </div>
                    </div>

                    {{-- Days of Week Selection & Per-Day Custom Timing Grid --}}
                    <div id="editRecDaysContainer"
                        class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/80">
                            <div>
                                <label
                                    class="block text-xs font-heading font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-week text-indigo-500 text-sm"></i>
                                    <span>{{ __('Select Teaching Days & Custom Session Times') }}</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                    {{ $isAr ? 'اختر الأيام المحددة لتعديل التكرار، ويمكنك تخصيص موعد ومدة ورابط لكل يوم مستقبلي بشكل مستقل.' : 'Select recurring days and customize start times, durations & links independently per day.' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-1.5 self-start sm:self-auto shrink-0">
                                <button type="button" onclick="applyMainTimeToAllEditDays()"
                                    class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/70 hover:bg-indigo-100 dark:hover:bg-indigo-900/90 text-indigo-700 dark:text-indigo-300 text-xs font-mono font-bold rounded-xl border border-indigo-200 dark:border-indigo-800 transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>{{ $isAr ? 'مزامنة الإعدادات الرئيسية مع الأيام المحددة' : 'Sync Main Settings to All Days' }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Per-Day Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-3">
                            @php
                                $editWeekDaysList = [
                                    ['val' => 6, 'name' => 'saturday', 'label' => __('Saturday')],
                                    ['val' => 0, 'name' => 'sunday', 'label' => __('Sunday')],
                                    ['val' => 1, 'name' => 'monday', 'label' => __('Monday')],
                                    ['val' => 2, 'name' => 'tuesday', 'label' => __('Tuesday')],
                                    ['val' => 3, 'name' => 'wednesday', 'label' => __('Wednesday')],
                                    ['val' => 4, 'name' => 'thursday', 'label' => __('Thursday')],
                                    ['val' => 5, 'name' => 'friday', 'label' => __('Friday')],
                                ];
                            @endphp
                            @foreach ($editWeekDaysList as $wd)
                                <div id="editRecDayCard_{{ $wd['val'] }}"
                                    class="edit-rec-day-card p-3.5 rounded-2xl border transition-all duration-300 flex flex-col justify-between space-y-3 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 opacity-60">

                                    {{-- Day Header --}}
                                    <label class="flex items-center justify-between gap-1.5 cursor-pointer select-none">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <input type="checkbox" name="days_of_week[]" value="{{ $wd['val'] }}"
                                                id="editRecDayCheck_{{ $wd['val'] }}"
                                                onchange="toggleEditDayTimeCard(this, 'editRecDayCard_{{ $wd['val'] }}')"
                                                class="w-4 h-4 rounded-md border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 cursor-pointer edit-rec-day-checkbox">
                                            <span
                                                class="text-xs font-bold font-heading text-slate-900 dark:text-white truncate">{{ $wd['label'] }}</span>
                                        </div>
                                        <span
                                            class="edit-rec-day-status-badge text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400">
                                            {{ $isAr ? 'غير محدد' : 'Off' }}
                                        </span>
                                    </label>

                                    {{-- Time & Duration & Link inputs --}}
                                    <div class="edit-rec-day-time-container transition-all duration-200 space-y-2 hidden">
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-clock text-indigo-500 text-[9px]"></i>
                                                <span>{{ __('Start Time') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20">
                                                <input type="time" name="day_start_times[{{ $wd['val'] }}]"
                                                    id="editRecDayTime_{{ $wd['val'] }}" value="10:00" disabled
                                                    aria-label="{{ $wd['label'] }} {{ __('Start Time') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-center edit-rec-day-time-picker">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-stopwatch text-indigo-500 text-[9px]"></i>
                                                <span>{{ __('Duration (Min)') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20">
                                                <input type="number" name="day_durations[{{ $wd['val'] }}]"
                                                    id="editRecDayDuration_{{ $wd['val'] }}" value="60"
                                                    min="15" max="300" disabled
                                                    aria-label="{{ $wd['label'] }} {{ __('Duration') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-center edit-rec-day-duration-picker">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-link text-indigo-500 text-[9px]"></i>
                                                <span>{{ __('Day Meeting Link') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20">
                                                <input type="url" name="day_meeting_links[{{ $wd['val'] }}]"
                                                    id="editRecDayLink_{{ $wd['val'] }}" placeholder="https://..."
                                                    disabled
                                                    aria-label="{{ $wd['label'] }} {{ __('Day Meeting Link') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-start truncate edit-rec-day-link-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Main Meeting Link --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Main Meeting Link (Fallback)') }}</label>
                        <input type="url" id="editRecMeetingLink" name="meeting_link" placeholder="https://..."
                            class="input-mobile">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Teacher Notes') }}</label>
                        <textarea id="editRecNotes" name="notes" rows="2" placeholder="{{ __('Optional notes...') }}"
                            class="input-mobile"></textarea>
                    </div>

                </div>

                {{-- Sticky Footer --}}
                <div
                    class="px-5 pb-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5 bg-white dark:bg-slate-900 sticky bottom-0">
                    <button type="button" onclick="closeScheduleModal('editRecurringScheduleModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" id="editRecSubmitBtn"
                        class="btn-lift px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: COMPREHENSIVE STUDENT EDUCATIONAL PROFILE (8 TABS)                    --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="studentProfileModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden relative my-auto">

            {{-- Modal Top Bar / Header --}}
            <div
                class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 to-teal-950 text-white flex items-start justify-between gap-4 shrink-0">
                <div class="flex items-center gap-4 min-w-0">
                    <div id="spModalAvatar"
                        class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xl flex items-center justify-center shrink-0 border border-teal-300/40 shadow-md">
                        S
                    </div>
                    <div class="min-w-0 space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 id="spModalName"
                                class="font-heading font-black text-xl sm:text-2xl text-white truncate">
                                {{ __('Student Profile') }}
                            </h3>
                            <span id="spModalCode"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                #STU-00000
                            </span>
                        </div>
                        <p id="spModalMeta" class="text-xs font-mono text-slate-300 truncate">
                            {{ __('Loading educational records...') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" id="spModalCreateSessionBtn" onclick="createSessionForCurrentStudent()"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all active:scale-95"
                        title="{{ __('Schedule Session') }}">
                        <span><i class="fa-solid fa-video"></i></span>
                        <span>{{ __('Schedule Session') }}</span>
                    </button>
                    <button type="button" onclick="openAddNoteModal()"
                        class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <span><i class="fa-solid fa-pen-nib"></i></span> {{ __('app.teacher.add_educational_note') }}
                    </button>
                    <button type="button" onclick="closeModal('studentProfileModal')"
                        class="text-slate-300 hover:text-white font-bold text-xl p-1 cursor-pointer"
                        aria-label="{{ __('Close') }}">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Educational Profile Sub-Navigation Tabs --}}
            <div
                class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 px-6 py-2.5 flex items-center gap-2 overflow-x-auto shrink-0 scrollbar-thin">
                <button type="button" onclick="switchSpTab('overview')" id="sp-tab-btn-overview"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap bg-teal-600 text-white shadow-xs">
                    <i class="fa-solid fa-chart-column"></i> {{ __('Overview') }}
                </button>
                <button type="button" onclick="switchSpTab('courses')" id="sp-tab-btn-courses"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-book-open"></i> {{ __('Courses') }}
                </button>
                <button type="button" onclick="switchSpTab('sessions')" id="sp-tab-btn-sessions"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-calendar-days"></i> {{ __('Sessions') }}
                </button>
                <button type="button" onclick="switchSpTab('attendance')" id="sp-tab-btn-attendance"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-clipboard-list"></i> {{ __('Attendance') }}
                </button>
                <button type="button" onclick="switchSpTab('assignments')" id="sp-tab-btn-assignments"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-pen-to-square"></i> {{ __('Assignments') }}
                </button>
                <button type="button" onclick="switchSpTab('assessments')" id="sp-tab-btn-assessments"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-bullseye"></i> {{ __('Assessments') }}
                </button>
                <button type="button" onclick="switchSpTab('progress')" id="sp-tab-btn-progress"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-chart-line"></i> {{ __('Progress') }}
                </button>
                <button type="button" onclick="switchSpTab('notes')" id="sp-tab-btn-notes"
                    class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-comments"></i> {{ __('Notes') }}
                </button>
            </div>

            {{-- Dynamic Tab Body --}}
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                {{-- Loading Spinner Skeleton --}}
                <div id="spLoadingSkeleton" class="py-16 text-center space-y-3">
                    <svg class="animate-spin h-8 w-8 text-teal-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-xs font-mono font-bold text-slate-500">
                        {{ __('Loading educational records from server...') }}</p>
                </div>

                {{-- 1. SP OVERVIEW TAB --}}
                <div id="sp-pane-overview" class="sp-tab-pane space-y-6 hidden">
                    {{-- KPI Summary Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div
                            class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-800/60 text-center space-y-0.5">
                            <span
                                class="text-[10px] font-mono font-bold text-teal-700 uppercase">{{ __('Attendance Rate') }}</span>
                            <p id="spOverviewAttRate"
                                class="font-heading font-black text-2xl text-teal-900 dark:text-teal-200">0%</p>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-center space-y-0.5">
                            <span
                                class="text-[10px] font-mono font-bold text-emerald-700 uppercase">{{ __('Average Grade') }}</span>
                            <p id="spOverviewAvgGrade"
                                class="font-heading font-black text-2xl text-emerald-900 dark:text-emerald-200">0%</p>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 text-center space-y-0.5">
                            <span
                                class="text-[10px] font-mono font-bold text-blue-700 uppercase">{{ __('Total Sessions') }}</span>
                            <p id="spOverviewTotalSessions"
                                class="font-heading font-black text-2xl text-blue-900 dark:text-blue-200">0</p>
                        </div>
                        <div
                            class="p-4 rounded-2xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200/80 dark:border-orange-800/60 text-center space-y-0.5">
                            <span
                                class="text-[10px] font-mono font-bold text-orange-700 uppercase">{{ __('Submissions') }}</span>
                            <p id="spOverviewSubmissions"
                                class="font-heading font-black text-2xl text-orange-900 dark:text-orange-200">0</p>
                        </div>
                    </div>

                    {{-- Overview Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                            <h4 class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <span><i class="fa-solid fa-clock"></i></span> {{ __('Recent Live Sessions Attended') }}
                            </h4>
                            <div id="spOverviewRecentSessions" class="space-y-2 text-xs font-mono">
                                {{-- Populated by JS --}}
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                            <h4 class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                                <span><i class="fa-solid fa-pen-to-square"></i></span>
                                {{ __('Recent Homework & Submissions') }}
                            </h4>
                            <div id="spOverviewRecentSubmissions" class="space-y-2 text-xs font-mono">
                                {{-- Populated by JS --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. SP COURSES TAB --}}
                <div id="sp-pane-courses" class="sp-tab-pane space-y-4 hidden">
                    <div id="spCoursesList" class="space-y-3">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- 3. SP SESSIONS TAB --}}
                <div id="sp-pane-sessions" class="sp-tab-pane space-y-4 hidden">
                    <div class="flex items-center justify-between gap-3 flex-wrap bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800">
                        <div>
                            <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <span><i class="fa-solid fa-calendar-days text-teal-600"></i></span> {{ __('Sessions') }}
                            </h4>
                            <p class="text-[11px] text-slate-500 font-mono">{{ __('All 1-to-1 and group live sessions for this student') }}</p>
                        </div>
                        <button type="button" onclick="createSessionForCurrentStudent()"
                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all active:scale-95">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>{{ __('Schedule Session') }}</span>
                        </button>
                    </div>

                    <div
                        class="table-responsive w-full overflow-x-auto custom-scrollbar rounded-xl border border-slate-200/80 dark:border-slate-800">
                        <table class="w-full min-w-[520px] text-left rtl:text-right border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-2.5 px-3">{{ __('Session') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Course') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Date') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Attendance') }}</th>
                                </tr>
                            </thead>
                            <tbody id="spSessionsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 4. SP ATTENDANCE TAB --}}
                <div id="sp-pane-attendance" class="sp-tab-pane space-y-4 hidden">
                    <div
                        class="table-responsive w-full overflow-x-auto custom-scrollbar rounded-xl border border-slate-200/80 dark:border-slate-800">
                        <table class="w-full min-w-[480px] text-left rtl:text-right border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-2.5 px-3">{{ __('Session Title') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Date & Time') }}</th>
                                    <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody id="spAttendanceTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. SP ASSIGNMENTS TAB --}}
                <div id="sp-pane-assignments" class="sp-tab-pane space-y-4 hidden">
                    <div
                        class="table-responsive w-full overflow-x-auto custom-scrollbar rounded-xl border border-slate-200/80 dark:border-slate-800">
                        <table class="w-full min-w-[560px] text-left rtl:text-right border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-2.5 px-3">{{ __('Assignment Title') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                    <th class="py-2.5 px-3">{{ __('Score') }}</th>
                                    <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Review Details') }}</th>
                                </tr>
                            </thead>
                            <tbody id="spAssignmentsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 6. SP ASSESSMENTS TAB --}}
                <div id="sp-pane-assessments" class="sp-tab-pane space-y-4 hidden">
                    <div id="spAssessmentsContainer" class="space-y-3">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- 7. SP PROGRESS TAB --}}
                <div id="sp-pane-progress" class="sp-tab-pane space-y-4 hidden">
                    <div id="spProgressContainer" class="space-y-4">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- 8. SP NOTES TAB --}}
                <div id="sp-pane-notes" class="sp-tab-pane space-y-4 hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h4 class="font-heading font-black text-sm text-slate-900">{{ __('Teacher Pedagogical Notes') }}
                        </h4>
                        <button type="button" onclick="openAddNoteModal()"
                            class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                            + {{ __('Add Note') }}
                        </button>
                    </div>
                    <div id="spNotesContainer" class="space-y-3">
                        {{-- Populated by JS --}}
                    </div>
                </div>
            </div>

            {{-- Sticky Modal Footer --}}
            <div
                class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                <button type="button" onclick="openAddNoteModal()"
                    class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-pen-nib"></i>
                    <span>{{ __('app.teacher.add_educational_note') }}</span>
                </button>
                <button type="button" onclick="closeModal('studentProfileModal')"
                    class="px-5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200/60 rounded-xl transition-all cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: ADD EDUCATIONAL NOTE FOR STUDENT                                      --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="addNoteModal"
        class="elite-modal fixed inset-0 z-60 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i
                            class="fa-solid fa-pen-nib"></i></span>
                    <span>{{ __('app.teacher.add_educational_note') }}</span>
                </h3>
                <button type="button" onclick="closeModal('addNoteModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="addNoteForm" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="noteStudentUserId" name="student_user_id">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Category') }}</label>
                        <select name="category" required
                            class="input-mobile text-xs dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="academic">{{ __('app.teacher.note_category_academic') }}</option>
                            <option value="homework">{{ __('app.teacher.note_category_homework') }}</option>
                            <option value="participation">{{ __('app.teacher.note_category_participation') }}</option>
                            <option value="behavior">{{ __('app.teacher.note_category_behavior') }}</option>
                            <option value="general" selected>{{ __('app.teacher.note_category_general') }}</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Pedagogical Observation & Feedback') }}</label>
                        <textarea id="noteContentTextarea" name="note" rows="4" required
                            placeholder="{{ __('Write your educational observations and advisory comments...') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-teal-600 transition-colors"></textarea>

                        {{-- Real-time Phone Security Warning Banner --}}
                        <div id="notePhoneWarning"
                            class="hidden mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold flex items-center gap-2 animate-pulse">
                            <span><i class="fa-solid fa-shield-halved"></i></span>
                            <span>{{ __('Security Warning: Sharing phone numbers or contact details in educational notes is prohibited.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('addNoteModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" id="saveNoteBtn"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Save Note') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1: SCHEDULE NEW SESSION                                                --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="createSessionModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-lg w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i
                            class="fa-solid fa-calendar-plus"></i></span>
                    <span>{{ __('Schedule New Live Session') }}</span>
                </h3>
                <button type="button" onclick="closeModal('createSessionModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="createSessionForm" action="{{ route('ajax.teacher.sessions.create') }}" method="POST"
                class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    {{-- 1. Student Selection (Student-First Lifecycle) --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Student') }}
                            *</label>
                        <input type="hidden" id="createSessionStudentId" name="student_user_id" value="">
                        <select id="createSessionStudentSelect" required
                            onchange="onSessionStudentChange(this.value)"
                            class="input-mobile text-xs dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="">{{ __('Select Student...') }}</option>
                            <option value="__group__">{{ __('👥 Group Session (All Enrolled Students)') }}</option>
                            @foreach ($assignedStudents as $st)
                                <option value="{{ $st->user_id }}">{{ $st->user?->name }} ({{ $st->gradeLevel?->name ?: ($isAr ? 'طالب' : 'Student') }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Course Selection (Dynamic based on selected student) --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}
                            *</label>
                        <select id="createSessionCourseId" name="course_id" required disabled
                            class="input-mobile text-xs dark:bg-slate-800 dark:border-slate-700 dark:text-white disabled:opacity-50 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                            <option value="">{{ __('Select Student First...') }}</option>
                        </select>
                        <p id="createSessionCourseHelp" class="text-[11px] font-mono text-rose-600 dark:text-rose-400 mt-1 hidden"></p>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}
                            *</label>
                        <input type="text" name="title"
                            placeholder="{{ __('e.g. Session 4: Electromagnetism & Ohm\'s Law') }}" required
                            class="input-mobile">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }}
                                *</label>
                            <input type="datetime-local" name="scheduled_at" required class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }}
                                *</label>
                            <input type="number" name="duration_minutes" value="60" min="15"
                                max="300" required class="input-mobile">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast Link (Optional)') }}</label>
                        <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..."
                            class="input-mobile">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_free_demo" id="is_free_demo" value="1"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <label for="is_free_demo"
                            class="text-xs font-semibold text-slate-700 cursor-pointer">{{ __('Mark as Free Trial / Demo Session') }}</label>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('createSessionModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Create Session') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1B: CREATE RECURRING SCHEDULE (WEEKLY / MONTHLY / YEARLY)               --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="recurringScheduleModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <div>
                    <h3
                        class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                        <span
                            class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/80 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm shadow-2xs"><i
                                class="fa-solid fa-arrows-rotate"></i></span>
                        <span>{{ __('Create Recurring Schedule') }}</span>
                    </h3>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Automatically generate recurring class sessions with custom per-day schedules and conflict validation.') }}
                    </p>
                </div>
                <button type="button" onclick="closeModal('recurringScheduleModal')"
                    aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="recurringScheduleForm" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-5">
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule Title') }}
                            *</label>
                        <input type="text" id="recTitle" name="title"
                            placeholder="{{ __('e.g. Physics Secondary 3 - Weekly Interactive Cohort') }}" required
                            class="input-mobile">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}
                                *</label>
                            <select id="recCourseId" name="course_id" required class="input-mobile text-xs"
                                title="">
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}
                                        ({{ $c->subject?->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Target Student (Optional)') }}</label>
                            <select id="recStudentUserId" name="student_user_id" class="input-mobile text-xs">
                                <option value="">{{ __('All enrolled students') }}</option>
                                @foreach ($assignedStudents as $st)
                                    <option value="{{ $st->user_id }}">{{ $st->user?->name }}
                                        ({{ $st->gradeLevel?->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="min-w-0">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Recurrence Pattern') }}
                                *</label>
                            <select id="recType" name="recurrence_type" required class="input-mobile text-xs"
                                onchange="toggleRecurrenceFields(this.value)">
                                <option value="weekly" selected>{{ __('Weekly') }}</option>
                                <option value="monthly">{{ __('Monthly') }}</option>
                                <option value="multi_month">{{ __('Multiple Months (3-6 Months)') }}</option>
                                <option value="yearly">{{ __('Yearly (Full Academic Year)') }}</option>
                                <option value="single">{{ __('Single Session') }}</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Main Start Time') }}
                                *</label>
                            <input type="time" id="recStartTime" name="start_time" value="10:00" required
                                class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Default Duration (Min)') }}
                                *</label>
                            <input type="number" id="recDuration" name="duration_minutes" value="60"
                                min="15" max="300" required class="input-mobile">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule Start Date') }}
                                *</label>
                            <input type="date" id="recStartDate" name="start_date"
                                value="{{ now()->format('Y-m-d') }}" required class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Schedule End Date') }}
                                *</label>
                            <input type="date" id="recEndDate" name="end_date"
                                value="{{ now()->addMonths(3)->format('Y-m-d') }}" required class="input-mobile">
                        </div>
                    </div>

                    {{-- Days of Week Selection & Per-Day Custom Timing Grid --}}
                    <div id="recDaysContainer"
                        class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-200/90 dark:border-slate-700/80 space-y-4">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/80">
                            <div>
                                <label
                                    class="block text-xs font-heading font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-week text-teal-500 text-sm"></i>
                                    <span>{{ __('Select Teaching Days & Custom Session Times') }}</span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                    {{ $isAr ? 'اختر الأيام المحددة للتكرار، يمكنك تحديد وقت خاص ومدة خاصة بكل يوم مستقبلي بشكل مستقل.' : 'Select days to recur and customize start times & durations independently per day.' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-1.5 self-start sm:self-auto shrink-0">
                                <button type="button" onclick="applyMainTimeToAllDays()"
                                    class="px-3 py-1.5 bg-teal-50 dark:bg-teal-950/70 hover:bg-teal-100 dark:hover:bg-teal-900/90 text-teal-700 dark:text-teal-300 text-xs font-mono font-bold rounded-xl border border-teal-200 dark:border-teal-800 transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>{{ $isAr ? 'مزامنة الوقت والمدة مع الأيام المحددة' : 'Sync Main Settings to All Days' }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Per-Day Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-3">
                            @php
                                $weekDaysList = [
                                    ['val' => 6, 'name' => 'saturday', 'label' => __('Saturday')],
                                    ['val' => 0, 'name' => 'sunday', 'label' => __('Sunday')],
                                    ['val' => 1, 'name' => 'monday', 'label' => __('Monday')],
                                    ['val' => 2, 'name' => 'tuesday', 'label' => __('Tuesday')],
                                    ['val' => 3, 'name' => 'wednesday', 'label' => __('Wednesday')],
                                    ['val' => 4, 'name' => 'thursday', 'label' => __('Thursday')],
                                    ['val' => 5, 'name' => 'friday', 'label' => __('Friday')],
                                ];
                            @endphp
                            @foreach ($weekDaysList as $wd)
                                @php
                                    $isChecked = in_array($wd['val'], [6, 0]);
                                @endphp
                                <div id="recDayCard_{{ $wd['val'] }}"
                                    class="rec-day-card p-3.5 rounded-2xl border transition-all duration-300 flex flex-col justify-between space-y-3 {{ $isChecked ? 'is-selected-day bg-teal-50/90 dark:bg-teal-950/50 border-teal-500 shadow-xs' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 opacity-60' }}">

                                    {{-- Day Header --}}
                                    <label class="flex items-center justify-between gap-1.5 cursor-pointer select-none">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <input type="checkbox" name="days_of_week[]" value="{{ $wd['val'] }}"
                                                id="recDayCheck_{{ $wd['val'] }}" {{ $isChecked ? 'checked' : '' }}
                                                onchange="toggleDayTimeCard(this, 'recDayCard_{{ $wd['val'] }}')"
                                                class="w-4 h-4 rounded-md border-slate-300 text-teal-600 focus:ring-2 focus:ring-teal-500/30 dark:border-slate-700 dark:bg-slate-800 cursor-pointer rec-day-checkbox">
                                            <span
                                                class="text-xs font-bold font-heading text-slate-900 dark:text-white truncate">{{ $wd['label'] }}</span>
                                        </div>
                                        <span
                                            class="rec-day-status-badge text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md {{ $isChecked ? 'bg-teal-100 dark:bg-teal-900/80 text-teal-800 dark:text-teal-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                            {{ $isChecked ? ($isAr ? 'مُفَعَّل' : 'Active') : ($isAr ? 'غير محدد' : 'Off') }}
                                        </span>
                                    </label>

                                    {{-- Time & Duration inputs --}}
                                    <div
                                        class="rec-day-time-container transition-all duration-200 space-y-2 {{ $isChecked ? '' : 'hidden' }}">
                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-clock text-teal-500 text-[9px]"></i>
                                                <span>{{ __('Start Time') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20">
                                                <input type="time" name="day_start_times[{{ $wd['val'] }}]"
                                                    id="recDayTime_{{ $wd['val'] }}" value="10:00"
                                                    {{ $isChecked ? '' : 'disabled' }}
                                                    aria-label="{{ $wd['label'] }} {{ __('Start Time') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-center rec-day-time-picker">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-stopwatch text-teal-500 text-[9px]"></i>
                                                <span>{{ __('Duration (Min)') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20">
                                                <input type="number" name="day_durations[{{ $wd['val'] }}]"
                                                    id="recDayDuration_{{ $wd['val'] }}" value="60"
                                                    min="15" max="300" {{ $isChecked ? '' : 'disabled' }}
                                                    aria-label="{{ $wd['label'] }} {{ __('Duration') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-center rec-day-duration-picker">
                                            </div>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-link text-teal-500 text-[9px]"></i>
                                                <span>{{ __('Day Meeting Link') }}</span>
                                            </label>
                                            <div
                                                class="flex items-center gap-1 px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/90 rounded-xl shadow-2xs focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/20">
                                                <input type="url" name="day_meeting_links[{{ $wd['val'] }}]"
                                                    id="recDayLink_{{ $wd['val'] }}" placeholder="https://..."
                                                    {{ $isChecked ? '' : 'disabled' }}
                                                    aria-label="{{ $wd['label'] }} {{ __('Day Meeting Link') }}"
                                                    class="w-full text-xs font-mono font-bold text-slate-800 dark:text-slate-100 bg-transparent focus:outline-none p-0 border-none text-start truncate rec-day-link-picker">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast Link (Optional)') }}</label>
                        <input type="url" id="recMeetingLink" name="meeting_link"
                            placeholder="{{ __('https://zoom.us/j/... or classroom stream link') }}"
                            class="input-mobile">
                    </div>

                    {{-- Live Schedule Preview & Conflict Feedback Area --}}
                    <div class="pt-2">
                        <button type="button" onclick="previewRecurringDates()"
                            class="btn-lift w-full py-3 bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700/80 hover:from-slate-200 hover:to-slate-300 dark:hover:from-slate-700 dark:hover:to-slate-600 text-slate-800 dark:text-white text-xs font-extrabold rounded-2xl border border-slate-300 dark:border-slate-700 flex items-center justify-center gap-2 cursor-pointer transition-all shadow-xs">
                            <span><i class="fa-solid fa-magnifying-glass text-teal-500"></i></span>
                            {{ __('Preview Generated Sessions & Validate Conflicts') }}
                        </button>
                    </div>

                    <div id="recPreviewContainer"
                        class="hidden space-y-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 max-h-56 overflow-y-auto">
                        <div class="flex items-center justify-between">
                            <span id="recPreviewSummary"
                                class="text-xs font-bold text-slate-800 dark:text-slate-100"></span>
                            <span id="recConflictStatusBadge"
                                class="text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full"></span>
                        </div>
                        <div id="recConflictWarning"
                            class="hidden p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-semibold">
                        </div>
                        <div id="recPreviewTableWrapper"
                            class="table-responsive w-full overflow-x-auto custom-scrollbar">
                            <table style="min-width: 460px;"
                                class="w-full text-xs text-left rtl:text-right border-collapse">
                                <thead>
                                    <tr
                                        class="border-b border-slate-200 dark:border-slate-700 text-[10px] font-mono text-slate-400 uppercase">
                                        <th class="py-1.5 px-2">{{ __('Date') }}</th>
                                        <th class="py-1.5 px-2">{{ __('Day') }}</th>
                                        <th class="py-1.5 px-2">{{ __('Time Window') }}</th>
                                        <th class="py-1.5 px-2">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="recPreviewTableBody"
                                    class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('recurringScheduleModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" id="saveRecurringBtn"
                        class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Create Recurring Schedule') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1C: EDIT SESSION / OVERRIDE SCOPE SELECTOR                              --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="editSessionOverrideModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-lg w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i
                            class="fa-solid fa-sliders"></i></span>
                    <span>{{ __('Edit Session & Recurrence Scope') }}</span>
                </h3>
                <button type="button" onclick="closeModal('editSessionOverrideModal')"
                    aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="editSessionOverrideForm" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="overrideSessionId" name="session_id">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    {{-- Scope Selection (3 Options) --}}
                    <div
                        class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 uppercase tracking-wider">{{ __('Modification Scope') }}
                            *</label>
                        <div class="space-y-2">
                            <label
                                class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-teal-400 dark:hover:border-teal-500 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 dark:has-checked:bg-teal-950/40 has-checked:border-teal-500">
                                <input type="radio" name="scope" value="this_only" checked
                                    class="mt-0.5 text-teal-600 focus:ring-teal-500">
                                <div class="text-xs">
                                    <span
                                        class="font-bold text-slate-900 block">{{ __('Edit This Session Only') }}</span>
                                    <span
                                        class="text-slate-500 text-[11px]">{{ __('Applies as an individual override. The recurring series remains unchanged.') }}</span>
                                </div>
                            </label>

                            <label
                                class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-teal-400 dark:hover:border-teal-500 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 dark:has-checked:bg-teal-950/40 has-checked:border-teal-500">
                                <input type="radio" name="scope" value="this_and_future"
                                    class="mt-0.5 text-teal-600 focus:ring-teal-500">
                                <div class="text-xs">
                                    <span
                                        class="font-bold text-slate-900 block">{{ __('Edit This and Future Sessions') }}</span>
                                    <span
                                        class="text-slate-500 text-[11px]">{{ __('Updates this class and all remaining future sessions in this recurring series.') }}</span>
                                </div>
                            </label>

                            <label
                                class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-teal-400 dark:hover:border-teal-500 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 dark:has-checked:bg-teal-950/40 has-checked:border-teal-500">
                                <input type="radio" name="scope" value="all"
                                    class="mt-0.5 text-teal-600 focus:ring-teal-500">
                                <div class="text-xs">
                                    <span
                                        class="font-bold text-slate-900 block">{{ __('Edit Entire Recurring Schedule') }}</span>
                                    <span
                                        class="text-slate-500 text-[11px]">{{ __('Modifies the schedule template rule across all non-completed sessions.') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}</label>
                        <input type="text" id="overrideTitle" name="title" class="input-mobile">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }}
                                *</label>
                            <input type="datetime-local" id="overrideDateTime" name="scheduled_at" required
                                class="input-mobile">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }}
                                *</label>
                            <input type="number" id="overrideDuration" name="duration_minutes" value="60"
                                min="15" max="300" required class="input-mobile">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Broadcast Meeting URL') }}</label>
                        <input type="url" id="overrideMeetingLink" name="meeting_link"
                            placeholder="https://zoom.us/j/..." class="input-mobile">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Reason for Modification (Audit Log)') }}</label>
                        <input type="text" id="overrideReason" name="reason"
                            placeholder="{{ __('e.g. Schedule adjustment per student request') }}"
                            class="input-mobile">
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('editSessionOverrideModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit" id="saveOverrideBtn"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Save Changes') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1D: CANCEL SESSION MODAL                                                --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="cancelSessionModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-xl text-rose-600 flex items-center gap-2">
                    <span><i class="fa-solid fa-circle-xmark text-rose-500"></i></span> {{ __('Cancel Session') }}
                </h3>
                <button type="button" onclick="closeModal('cancelSessionModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="cancelSessionForm" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="cancelSessionId">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    <p class="text-xs text-slate-600 leading-relaxed font-semibold">
                        {{ __('Are you sure you want to cancel this session? All enrolled students will be immediately notified via push notification and email.') }}
                    </p>

                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Cancellation Reason') }}
                            *</label>
                        <input type="text" id="cancelReasonInput" name="reason"
                            placeholder="{{ __('e.g. Instructor emergency or holiday rescheduling') }}" required
                            class="input-mobile">
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('cancelSessionModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Keep Session') }}</button>
                    <button type="submit"
                        class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Confirm Cancellation') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2: MEETING LINK EDITOR                                                 --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="meetingLinkModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span
                        class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm"><i
                            class="fa-solid fa-video"></i></span>
                    <span>{{ __('Update Live Stream Link') }}</span>
                </h3>
                <button type="button" onclick="closeModal('meetingLinkModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="meetingLinkForm" method="POST" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="linkSessionId" name="session_id">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast URL') }}</label>
                        <input type="url" id="meetingUrlInput" name="meeting_link"
                            placeholder="https://vimeo.com/... or Zoom link" required
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('meetingLinkModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span>{{ __('Save Meeting Link') }}</span>
                        <i class="fa-solid fa-arrow-left rtl:inline-block ltr:hidden text-xs"></i>
                        <i class="fa-solid fa-arrow-right rtl:hidden ltr:inline-block text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 3: RESCHEDULE SESSION                                                  --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="rescheduleModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-md w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <h3 class="font-heading font-black text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span
                        class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm"><i
                            class="fa-solid fa-calendar-days"></i></span>
                    <span>{{ __('Reschedule Teaching Session') }}</span>
                </h3>
                <button type="button" onclick="closeModal('rescheduleModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="rescheduleForm" method="POST" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="rescheduleSessionId">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('New Scheduled Date & Time') }}</label>
                        <input type="datetime-local" id="rescheduleDateTime" name="scheduled_at" required
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('Reason for Rescheduling') }}</label>
                        <input type="text" id="rescheduleReason" name="reason"
                            placeholder="{{ __('e.g. Time adjustment for upcoming exam revision') }}"
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('rescheduleModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="btn-lift px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span>{{ __('Confirm Reschedule') }}</span>
                        <i class="fa-solid fa-arrow-left rtl:inline-block ltr:hidden text-xs"></i>
                        <i class="fa-solid fa-arrow-right rtl:hidden ltr:inline-block text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 4: CREATE ASSIGNMENT & QUIZ                                            --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="createAssignmentModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-3xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Header --}}
            <div
                class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-teal-800/40">
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center shrink-0 shadow-sm text-base">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div class="space-y-0.5">
                        <h3 class="font-heading font-black text-lg sm:text-xl text-white tracking-tight">
                            {{ __('Publish New Assignment & Quiz') }}
                        </h3>
                        <p class="text-xs text-teal-200/90 font-mono">
                            {{ __('Create homework assignments or interactive MSQ quizzes for your students.') }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('createAssignmentModal')"
                    aria-label="{{ __('Close') }}"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer active:scale-95 shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="createAssignmentForm" action="{{ route('ajax.teacher.assignments.create') }}" method="POST"
                enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf

                {{-- Scrollable Form Body --}}
                <div class="p-5 sm:p-7 overflow-y-auto flex-1 custom-scrollbar space-y-5">
                    {{-- Row 1: Target Course & Live Session Dropdowns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Target Course') }} *</span>
                            </label>
                            <select name="course_id" required
                                class="elite-custom-select w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-video text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Live Session (Optional)') }}</span>
                            </label>
                            <select name="live_session_id"
                                class="elite-custom-select w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                <option value="">{{ __('None / General Course Assignment') }}</option>
                                @foreach ($todaySessions->merge($allSessions)->unique('id') as $ls)
                                    <option value="{{ $ls->id }}">{{ $ls->title ?: __('Live Session') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Title --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-heading text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ __('Assignment Title') }} *</span>
                        </label>
                        <input type="text" name="title"
                            placeholder="{{ __('e.g. Unit 2: Physics Waves & Optics Quiz') }}" required
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                    </div>

                    {{-- Row 3: Description / Guidelines --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-align-left text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ __('Description / Instructions') }}</span>
                        </label>
                        <textarea name="description" rows="3"
                            placeholder="{{ __('Provide guidelines, instructions, or reading materials...') }}"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3.5 text-xs font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all custom-scrollbar"></textarea>
                    </div>

                    {{-- Row 4: Due Date, Duration, and Passing Score (Optimized Proportions) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                        <div class="sm:col-span-6">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-day text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Due Date & Time') }} *</span>
                            </label>
                            <input type="datetime-local" name="due_at" required
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                        <div class="sm:col-span-3">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-stopwatch text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Duration (Minutes)') }}</span>
                            </label>
                            <input type="number" name="duration_minutes" value="30" min="5"
                                max="300"
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                        <div class="sm:col-span-3">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Passing Score (%)') }}</span>
                            </label>
                            <input type="number" name="passing_score" value="70" min="0"
                                max="100"
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    {{-- Interactive Multiple Choice Quiz Questions Builder --}}
                    <div
                        class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 space-y-3">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-heading font-black text-sm text-slate-900 dark:text-white">
                                        {{ __('Questions & Quiz Builder') }}
                                    </h4>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60">
                                        {{ __('Optional') }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                    {{ __('Add interactive multiple choice questions with automated answer key.') }}
                                </p>
                            </div>
                            <button type="button" onclick="addTeacherQuestion()"
                                class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>{{ __('Add Question') }}</span>
                            </button>
                        </div>

                        <div id="teacherQuestionsEmptyState"
                            class="p-5 text-center border border-dashed border-slate-300/80 dark:border-slate-700 rounded-xl space-y-1.5 bg-white dark:bg-slate-900/60">
                            <i class="fa-solid fa-clipboard-question text-2xl text-teal-600/70 dark:text-teal-400/70"></i>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ __('No quiz questions attached yet.') }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">
                                {{ __('Leave empty to publish a standard homework task, or click "+ Add Question" to attach interactive questions.') }}
                            </p>
                        </div>

                        <div id="teacherQuestionsContainer" class="space-y-4 pt-1">
                            {{-- Dynamically Appended Question Blocks --}}
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 sm:p-5 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('createAssignmentModal')"
                        class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition-all cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-teal-600/25 cursor-pointer flex items-center gap-2">
                        <span>{{ __('Publish Assignment') }}</span>
                        <i class="fa-solid fa-arrow-left rtl:inline-block ltr:hidden text-xs"></i>
                        <i class="fa-solid fa-arrow-right rtl:hidden ltr:inline-block text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 4C: EDIT ASSIGNMENT & QUIZ QUESTIONS BUILDER                             --}}
    {{-- ══════════════════════════════════════════════════════════════════════════════ --}}
    <div id="editAssignmentModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-3xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Top Header --}}
            <div
                class="p-4 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-center justify-between gap-4 shrink-0 relative overflow-hidden">
                <div class="space-y-1 min-w-0 flex-1 z-10">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center text-sm border border-teal-500/30">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        <h3 class="font-heading font-black text-xl sm:text-2xl text-white tracking-tight leading-snug">
                            {{ __('Edit Assignment & Quiz') }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-300 font-mono">
                        {{ __('Update homework criteria, due dates, and multiple choice questions.') }}
                    </p>
                </div>
                <button type="button" onclick="closeModal('editAssignmentModal')" aria-label="{{ __('Close') }}"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="editAssignmentForm" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="editAssignmentId" name="assignment_id">

                {{-- Scrollable Form Body --}}
                <div class="p-5 sm:p-7 overflow-y-auto flex-1 custom-scrollbar space-y-5">
                    {{-- Row 1: Target Course & Live Session Dropdowns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Target Course') }} *</span>
                            </label>
                            <select id="editAssignmentCourseId" name="course_id" required
                                class="elite-custom-select w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-video text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Live Session (Optional)') }}</span>
                            </label>
                            <select id="editAssignmentLiveSessionId" name="live_session_id"
                                class="elite-custom-select w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-teal-500">
                                <option value="">{{ __('None / General Course Assignment') }}</option>
                                @foreach ($todaySessions->merge($allSessions)->unique('id') as $ls)
                                    <option value="{{ $ls->id }}">{{ $ls->title ?: __('Live Session') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Title --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-heading text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ __('Assignment Title') }} *</span>
                        </label>
                        <input type="text" id="editAssignmentTitle" name="title"
                            placeholder="{{ __('e.g. Unit 2: Physics Waves & Optics Quiz') }}" required
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                    </div>

                    {{-- Row 3: Description / Guidelines --}}
                    <div>
                        <label
                            class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-align-left text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ __('Description / Instructions') }}</span>
                        </label>
                        <textarea id="editAssignmentDescription" name="description" rows="3"
                            placeholder="{{ __('Provide guidelines, instructions, or reading materials...') }}"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3.5 text-xs font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all custom-scrollbar"></textarea>
                    </div>

                    {{-- Row 4: Due Date, Duration, and Passing Score --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                        <div class="sm:col-span-6">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-day text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Due Date & Time') }} *</span>
                            </label>
                            <input type="datetime-local" id="editAssignmentDueAt" name="due_at" required
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                        <div class="sm:col-span-3">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-stopwatch text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Duration (Minutes)') }}</span>
                            </label>
                            <input type="number" id="editAssignmentDuration" name="duration_minutes" value="30"
                                min="5" max="300"
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                        <div class="sm:col-span-3">
                            <label
                                class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-teal-600 dark:text-teal-400 text-xs"></i>
                                <span>{{ __('Passing Score (%)') }}</span>
                            </label>
                            <input type="number" id="editAssignmentPassScore" name="passing_score" value="70"
                                min="0" max="100"
                                class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-mono font-bold text-slate-900 dark:text-white text-center focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    {{-- Interactive Multiple Choice Quiz Questions Builder --}}
                    <div
                        class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 space-y-3">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-heading font-black text-sm text-slate-900 dark:text-white">
                                        {{ __('Questions & Quiz Builder') }}
                                    </h4>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60">
                                        {{ __('Optional') }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                    {{ __('Add or edit interactive multiple choice questions with automated answer key.') }}
                                </p>
                            </div>
                            <button type="button" onclick="addEditTeacherQuestion()"
                                class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>{{ __('Add Question') }}</span>
                            </button>
                        </div>

                        <div id="editTeacherQuestionsEmptyState"
                            class="p-5 text-center border border-dashed border-slate-300/80 dark:border-slate-700 rounded-xl space-y-1.5 bg-white dark:bg-slate-900/60">
                            <i class="fa-solid fa-clipboard-question text-2xl text-teal-600/70 dark:text-teal-400/70"></i>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ __('No quiz questions attached yet.') }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">
                                {{ __('Leave empty to publish a standard homework task, or click "+ Add Question" to attach interactive questions.') }}
                            </p>
                        </div>

                        <div id="editTeacherQuestionsContainer" class="space-y-4 pt-1">
                            {{-- Dynamically Appended Question Blocks --}}
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 sm:p-5 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('editAssignmentModal')"
                        class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-xl transition-all cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" id="editAssignmentSubmitBtn"
                        class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-teal-600/25 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>{{ __('Save Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 4B: ASSIGNMENT DETAILS, QUIZ QUESTIONS & SUBMISSIONS ROSTER             --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="assignmentDetailsModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-3xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Modal Top Header --}}
            <div
                class="p-4 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-start justify-between gap-4 shrink-0 relative overflow-hidden">
                <div
                    class="absolute -right-10 -bottom-10 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none">
                </div>
                <div class="space-y-1.5 min-w-0 flex-1 z-10">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span id="adModalCourse"
                            class="px-2.5 py-0.5 rounded-full text-xs font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40"></span>
                        <span id="adModalSubject"
                            class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-white/10 text-slate-200"></span>
                        <span id="adModalStatus"
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40"></span>
                    </div>
                    <h3 id="adModalTitle" class="font-heading font-black text-xl sm:text-2xl text-white leading-snug">
                    </h3>
                    <p id="adModalDescription" class="text-xs text-slate-300 leading-relaxed"></p>
                </div>
                <button type="button" onclick="closeModal('assignmentDetailsModal')"
                    aria-label="{{ __('Close') }}"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Modal Scrollable Body --}}
            <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-6">
                {{-- Loading Skeleton --}}
                <div id="adLoadingSkeleton" class="py-12 text-center space-y-3">
                    <div
                        class="inline-block w-8 h-8 border-3 border-teal-500 border-t-transparent rounded-full animate-spin">
                    </div>
                    <p class="text-xs font-mono text-slate-400">{{ __('Loading assignment details...') }}</p>
                </div>

                {{-- Dynamic Content --}}
                <div id="adModalContent" class="hidden space-y-6">
                    {{-- KPI Stats Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div
                            class="p-3 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                            <span
                                class="text-[10px] uppercase font-mono font-bold text-slate-500 dark:text-slate-300 block">{{ __('Due Date') }}</span>
                            <span id="adDueAt"
                                class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white block mt-0.5"></span>
                            <span id="adDueHuman"
                                class="text-[10px] font-mono text-teal-600 dark:text-teal-300 font-semibold block mt-0.5"></span>
                        </div>
                        <div
                            class="p-3 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                            <span
                                class="text-[10px] uppercase font-mono font-bold text-slate-500 dark:text-slate-300 block">{{ __('Passing Score') }}</span>
                            <span id="adPassScore"
                                class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white block mt-0.5"></span>
                            <span
                                class="text-[10px] font-mono text-slate-500 dark:text-slate-300 block mt-0.5">{{ __('Minimum to pass') }}</span>
                        </div>
                        <div
                            class="p-3 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                            <span
                                class="text-[10px] uppercase font-mono font-bold text-slate-500 dark:text-slate-300 block">{{ __('Total Questions') }}</span>
                            <span id="adQuestionsCount"
                                class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white block mt-0.5"></span>
                            <span id="adDuration"
                                class="text-[10px] font-mono text-slate-500 dark:text-slate-300 block mt-0.5"></span>
                        </div>
                        <div
                            class="p-3 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                            <span
                                class="text-[10px] uppercase font-mono font-bold text-slate-500 dark:text-slate-300 block">{{ __('Total Submissions') }}</span>
                            <span id="adSubmissionsCount"
                                class="font-heading font-black text-xs sm:text-sm text-emerald-600 dark:text-emerald-400 block mt-0.5"></span>
                            <span id="adAvgScore"
                                class="text-[10px] font-mono text-slate-500 dark:text-slate-300 block mt-0.5"></span>
                        </div>
                    </div>

                    {{-- Subtabs Selector --}}
                    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                        <button type="button" onclick="switchAdSubTab('questions')" id="ad-tab-btn-questions"
                            class="ad-subtab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all bg-teal-600 text-white shadow-xs cursor-pointer">
                            <i class="fa-solid fa-list-check me-1"></i> {{ __('Quiz Questions') }} (<span
                                id="adQuestionsBadge">0</span>)
                        </button>
                        <button type="button" onclick="switchAdSubTab('submissions')" id="ad-tab-btn-submissions"
                            class="ad-subtab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                            <i class="fa-solid fa-user-graduate me-1"></i> {{ __('Student Submissions') }} (<span
                                id="adSubmissionsBadge">0</span>)
                        </button>
                    </div>

                    {{-- Tab Pane 1: Questions & Answers Key --}}
                    <div id="ad-pane-questions" class="ad-pane space-y-4">
                        <div id="adQuestionsList" class="space-y-4"></div>
                        <div id="adNoQuestionsNotice"
                            class="hidden text-center py-8 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-600 dark:text-slate-300 italic">
                                {{ __('No multiple-choice questions attached to this assignment.') }}</p>
                        </div>
                    </div>

                    {{-- Tab Pane 2: Student Submissions Roster --}}
                    <div id="ad-pane-submissions" class="ad-pane hidden space-y-4">
                        <div id="adSubmissionsTableContainer"
                            class="table-responsive w-full overflow-x-auto custom-scrollbar">
                            <table style="min-width: 540px;"
                                class="w-full text-left rtl:text-right border-collapse text-xs">
                                <thead>
                                    <tr
                                        class="border-b border-slate-200 dark:border-slate-800 font-mono font-bold text-slate-600 dark:text-slate-300 uppercase">
                                        <th class="py-2.5 px-3">{{ __('Student') }}</th>
                                        <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                        <th class="py-2.5 px-3">{{ __('Grade / Score') }}</th>
                                        <th class="py-2.5 px-3">{{ __('Status') }}</th>
                                        <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Review Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="adSubmissionsTableBody"
                                    class="divide-y divide-slate-100 dark:divide-slate-800 font-mono"></tbody>
                            </table>
                        </div>
                        <div id="adNoSubmissionsNotice"
                            class="hidden text-center py-8 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-600 dark:text-slate-300 italic">
                                {{ __('No student submissions recorded for this assignment yet.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div
                class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2.5 shrink-0 rounded-b-[24px] sm:rounded-b-[28px] flex-wrap">
                <div class="flex items-center gap-2">
                    <button type="button" id="adEditBtn" onclick="editCurrentAssignmentFromModal()"
                        class="btn-lift px-3.5 py-2 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/80 text-teal-800 dark:text-teal-300 font-bold text-xs rounded-xl border border-teal-200/70 dark:border-teal-800/60 transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square text-teal-600 dark:text-teal-400 text-xs"></i>
                        <span>{{ __('Edit Assignment') }}</span>
                    </button>
                    <button type="button" id="adDeleteBtn" onclick="deleteCurrentAssignmentFromModal()"
                        class="btn-lift px-3.5 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-700 dark:text-rose-300 font-bold text-xs rounded-xl border border-rose-200/70 dark:border-rose-800/60 transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can text-rose-500 text-xs"></i>
                        <span>{{ __('Delete') }}</span>
                    </button>
                </div>
                <button type="button" onclick="closeModal('assignmentDetailsModal')"
                    class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 5: GRADE ASSIGNMENT SUBMISSION & QUESTION REVIEW                       --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="gradeModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-2xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
            {{-- Header --}}
            <div
                class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
                <div>
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                        <span
                            class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i
                                class="fa-solid fa-award"></i></span>
                        <span>{{ __('Review & Grade Submission') }}</span>
                    </h3>
                    <p id="gradeStudentName" class="text-xs text-teal-600 font-mono font-bold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeModal('gradeModal')" aria-label="{{ __('Close') }}"
                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i
                        class="fa-solid fa-xmark text-sm"></i></button>
            </div>

            <form id="gradeForm" method="POST" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="gradeSubmissionId" name="submission_id">

                {{-- Scrollable Form Body --}}
                <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-5">
                    {{-- Question By Question Auto-Correction Breakdown --}}
                    <div class="space-y-3">
                        <h4 class="font-heading font-black text-sm text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-bullseye text-teal-600"></i></span>
                            <span>{{ __('Questions Auto-Correction & Student Choices') }}</span>
                        </h4>
                        <div id="submissionQuestionsContainer"
                            class="space-y-3 max-h-60 overflow-y-auto p-1 custom-scrollbar">
                            {{-- Populated via AJAX --}}
                        </div>
                    </div>

                    <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Overall Grade Score (0 - 100%)') }}
                                *</label>
                            <input type="number" id="gradeScoreInput" name="score" step="0.1"
                                min="0" max="100" required placeholder="{{ __('e.g. 85.0') }}"
                                class="input-mobile font-mono font-bold text-lg">
                        </div>

                        <div>
                            <label
                                class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Pedagogical Feedback & Notes to Student') }}</label>
                            <textarea id="gradeEvaluationNotes" name="evaluation_notes" rows="3"
                                placeholder="{{ __('Provide detailed feedback, remarks, or praise for the student...') }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-teal-600"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <button type="button" onclick="closeModal('gradeModal')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                    <button type="submit"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                        {{ __('Save & Finalize Grade') }} &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 6: MARK SESSION ATTENDANCE (REAL-TIME COHORT CHECK-IN)                 --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="attendanceModal"
        class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300"
        style="z-index: 99999;">
        <div
            class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-2xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">

            {{-- Header --}}
            <div
                class="p-4 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-center justify-between gap-4 shrink-0 relative overflow-hidden">
                <div
                    class="absolute -right-10 -bottom-10 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none">
                </div>
                <div class="flex items-center gap-3.5 min-w-0 z-10">
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center shrink-0 shadow-sm text-lg sm:text-xl">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div class="min-w-0 space-y-0.5 sm:space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3
                                class="font-heading font-black text-base sm:text-xl text-white tracking-tight leading-snug">
                                {{ __('Record Cohort Attendance') }}
                            </h3>
                            <span id="attendanceModalBadge"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                {{ __('Live Sheet') }}
                            </span>
                        </div>
                        <p id="attendanceSessionTitle"
                            class="text-xs text-teal-200/90 font-mono font-bold truncate max-w-md"></p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('attendanceModal')" aria-label="{{ __('Close') }}"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            {{-- Sub-header with Fast Bulk Controls & Search --}}
            <div
                class="px-4 sm:px-6 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2.5 shrink-0 flex-wrap">
                <div class="flex items-center gap-2">
                    <span
                        class="text-[11px] sm:text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">{{ __('COHORT LEARNERS') }}</span>
                    <span id="attendanceCohortCount"
                        class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-teal-100 text-teal-800 border border-teal-200">0</span>
                </div>

                {{-- Bulk Status Buttons --}}
                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                    <button type="button" onclick="bulkSetAttendance('present')"
                        class="btn-lift px-2.5 sm:px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] sm:text-xs font-bold rounded-xl border border-emerald-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                        <span>{{ __('All Present') }}</span>
                    </button>
                    <button type="button" onclick="bulkSetAttendance('late')"
                        class="btn-lift px-2.5 sm:px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 text-[11px] sm:text-xs font-bold rounded-xl border border-amber-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-clock text-amber-600 text-xs"></i>
                        <span>{{ __('All Late') }}</span>
                    </button>
                    <button type="button" onclick="bulkSetAttendance('absent')"
                        class="btn-lift px-2.5 sm:px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 text-[11px] sm:text-xs font-bold rounded-xl border border-rose-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i>
                        <span>{{ __('All Absent') }}</span>
                    </button>
                </div>
            </div>

            {{-- In-Modal Search --}}
            <div class="px-4 sm:px-6 py-2 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <div class="relative">
                    <input type="text" id="attModalSearchInput"
                        placeholder="{{ __('Search student in roster by name, code or grade...') }}"
                        oninput="filterAttendanceModalStudents(this.value)"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:border-teal-500 transition-all pe-8">
                    <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                </div>
            </div>

            <form id="attendanceForm" method="POST" class="flex-1 flex flex-col overflow-hidden m-0">
                @csrf
                <input type="hidden" id="attendanceSessionId">

                {{-- Dynamic Scrollable Students Container --}}
                <div id="attendanceStudentsContainer"
                    class="divide-y divide-slate-100 dark:divide-slate-800 overflow-y-auto flex-1 p-4 sm:p-6 space-y-2 custom-scrollbar">
                    {{-- Populated dynamically in real-time via AJAX --}}
                </div>

                {{-- Sticky Action Footer --}}
                {{-- Sticky Action Footer --}}
                <div
                    class="p-4 bg-slate-50/90 dark:bg-slate-900/90 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[24px] sm:rounded-b-[28px]">
                    <div class="text-xs font-mono text-slate-500 hidden sm:flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-teal-600"></i>
                        <span>{{ __('Absence notices sent automatically') }}</span>
                    </div>
                    <div class="flex items-center gap-3 ms-auto">
                        <button type="button" onclick="closeModal('attendanceModal')"
                            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-xl transition-all cursor-pointer">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" id="saveAttendanceBtn"
                            class="btn-lift px-5 sm:px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-teal-600/25 cursor-pointer flex items-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('Save Attendance Sheet') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Interactive Teacher Preview & Platform Guide Modal --}}
    @include('components.teacher-preview-guide-modal')
@endsection

@push('styles')
    <style>
        /* ── Teacher Portal Dark Mode Overrides ─────────────────────────── */
        html.dark #teacher-portal-root .bg-white,
        html.dark .elite-modal .bg-white {
            background-color: #0F172A !important;
        }

        html.dark #teacher-portal-root .bg-\[#FAFAF9\],
        html.dark .elite-modal .bg-\[#FAFAF9\] {
            background-color: #0B0F19 !important;
        }

        html.dark #teacher-portal-root .bg-slate-50,
        html.dark .elite-modal .bg-slate-50 {
            background-color: #162032 !important;
        }

        html.dark #teacher-portal-root .bg-slate-50\/50,
        html.dark .elite-modal .bg-slate-50\/50 {
            background-color: rgba(22, 32, 50, 0.5) !important;
        }

        html.dark #teacher-portal-root .border-slate-200\/90,
        html.dark #teacher-portal-root .border-slate-200,
        html.dark .elite-modal .border-slate-200\/90,
        html.dark .elite-modal .border-slate-200 {
            border-color: #1E293B !important;
        }

        html.dark #teacher-portal-root .border-slate-100,
        html.dark .elite-modal .border-slate-100 {
            border-color: rgba(30, 41, 59, 0.7) !important;
        }

        html.dark #teacher-portal-root .text-slate-900,
        html.dark .elite-modal .text-slate-900 {
            color: #F8FAFC !important;
        }

        html.dark #teacher-portal-root .text-slate-800,
        html.dark .elite-modal .text-slate-800 {
            color: #F1F5F9 !important;
        }

        html.dark #teacher-portal-root .text-slate-700,
        html.dark .elite-modal .text-slate-700 {
            color: #E2E8F0 !important;
        }

        html.dark #teacher-portal-root .text-slate-600,
        html.dark .elite-modal .text-slate-600 {
            color: #CBD5E1 !important;
        }

        html.dark #teacher-portal-root .text-slate-500,
        html.dark .elite-modal .text-slate-500 {
            color: #94A3B8 !important;
        }

        html.dark #teacher-portal-root .text-slate-400,
        html.dark .elite-modal .text-slate-400 {
            color: #94A3B8 !important;
        }

        html.dark #teacher-portal-root .text-slate-300,
        html.dark .elite-modal .text-slate-300 {
            color: #CBD5E1 !important;
        }

        html.dark #teacher-portal-root .shadow-xl,
        html.dark #teacher-portal-root .shadow-md,
        html.dark .elite-modal .shadow-xl,
        html.dark .elite-modal .shadow-md {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
        }

        html.dark #teacher-portal-root .divide-slate-100>*+*,
        html.dark .elite-modal .divide-slate-100>*+* {
            border-color: rgba(30, 41, 59, 0.6) !important;
        }

        /* Tab bar dark */
        html.dark .teacher-tab-scroll {
            background-color: #0F172A;
        }

        /* Input & select dark */
        html.dark #teacher-portal-root select.bg-white,
        html.dark #teacher-portal-root input.bg-white,
        html.dark .elite-modal select,
        html.dark .elite-modal input:not([type="checkbox"]):not([type="radio"]),
        html.dark .elite-modal textarea {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
            border-color: #334155 !important;
        }

        html.dark #teacher-portal-root .bg-slate-100,
        html.dark .elite-modal .bg-slate-100 {
            background-color: #1E293B !important;
        }

        html.dark #teacher-portal-root .hover\:bg-slate-100:hover,
        html.dark .elite-modal .hover\:bg-slate-100:hover {
            background-color: #1E293B !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const openModal = (id) => window.openModal(id);
        const closeModal = (id) => window.closeModal(id);

        window.openScheduleModal = function(id) {
            if (typeof window.openModal === 'function') {
                window.openModal(id);
            } else {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('hidden');
                    el.style.setProperty('display', 'flex', 'important');
                }
            }
        };
        window.closeScheduleModal = function(id) {
            if (typeof window.closeModal === 'function') {
                window.closeModal(id);
            } else {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add('hidden');
                    el.style.setProperty('display', 'none', 'important');
                }
            }
        };
        const openScheduleModal = window.openScheduleModal;
        const closeScheduleModal = window.closeScheduleModal;

        window.toggleCalendarSidebarMobile = function() {
            const col = document.getElementById('calSidebarCol');
            const icon = document.getElementById('calSidebarToggleIcon');
            if (!col) return;
            const isHidden = col.classList.contains('hidden');
            if (isHidden) {
                col.classList.remove('hidden');
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                col.classList.add('hidden');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        };

        window.focusCalendarToday = async function() {
            if (typeof switchCalendarView === 'function') {
                if (window.innerWidth < 640 && currentCalView !== 'day') {
                    switchCalendarView('day');
                } else if (window.innerWidth >= 640 && currentCalView !== 'week') {
                    switchCalendarView('week');
                }
            }
            if (typeof navigateCalendarToday === 'function') {
                await navigateCalendarToday();
            }
            const targetEl = document.getElementById('calendarRightPanel') || document.getElementById(
                'calendarHeroTitle');
            if (targetEl) {
                targetEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            setTimeout(() => {
                const todayCol = document.querySelector('.cal-today-col') || document.querySelector(
                    '.cal-single-day-col');
                if (todayCol) {
                    todayCol.style.transition = 'all 0.3s ease';
                    todayCol.style.boxShadow = '0 0 0 3px #6366f1';
                    setTimeout(() => {
                        todayCol.style.boxShadow = '';
                    }, 2000);
                }
            }, 300);
        };

        window.scrollCalendarToDay = function(dayIndex) {
            if (currentCalView === 'day') {
                const weekDays = typeof getWeekDays === 'function' ? getWeekDays(currentCalDate) : [];
                if (weekDays && weekDays[dayIndex]) {
                    currentCalDate = new Date(weekDays[dayIndex]);
                    renderCalendar();
                }
                return;
            }
            const scroller = document.querySelector('.cal-timetable-wrapper');
            if (!scroller) return;
            const targetCol = scroller.querySelector(`.cal-day-col[data-day-idx="${dayIndex}"]`);
            if (targetCol) {
                const scrollerRect = scroller.getBoundingClientRect();
                const colRect = targetCol.getBoundingClientRect();
                const offset = (colRect.left - scrollerRect.left) + scroller.scrollLeft - (scrollerRect.width / 2) + (
                    colRect.width / 2);
                scroller.scrollTo({
                    left: Math.max(0, offset),
                    behavior: 'smooth'
                });
                targetCol.style.transition = 'all 0.3s ease';
                targetCol.style.boxShadow = 'inset 0 0 0 2px #6366f1';
                setTimeout(() => {
                    targetCol.style.boxShadow = '';
                }, 1800);
            }
            const stripBtns = document.querySelectorAll('.cal-day-strip-btn');
            stripBtns.forEach((btn, idx) => {
                if (idx === dayIndex) {
                    btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs');
                    btn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-700',
                        'dark:text-slate-200');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'shadow-xs');
                    btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-200');
                }
            });
        };

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        window.escapeHtml = escapeHtml;

        function escapeJs(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")
                .replace(/"/g, '\\"')
                .replace(/\n/g, '\\n')
                .replace(/\r/g, '\\r');
        }
        window.escapeJs = escapeJs;

        // ── Attendance Tab View & Filtering Logic ────────────────────────────────────
        function switchAttView(viewType) {
            const panes = {
                cards: document.getElementById('attViewCards'),
                table: document.getElementById('attViewTable'),
                matrix: document.getElementById('attViewMatrix'),
            };
            const btns = {
                cards: document.getElementById('attViewBtnCards'),
                table: document.getElementById('attViewBtnTable'),
                matrix: document.getElementById('attViewBtnMatrix'),
            };

            Object.keys(panes).forEach(type => {
                if (panes[type]) panes[type].classList.add('hidden');
                if (btns[type]) {
                    btns[type].className =
                        'py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap';
                }
            });

            if (panes[viewType]) panes[viewType].classList.remove('hidden');
            if (btns[viewType]) {
                btns[viewType].className =
                    'py-1.5 px-3.5 rounded-lg text-xs font-bold transition-all bg-teal-600 text-white shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap';
            }
        }
        window.switchAttView = switchAttView;

        function filterAttendanceData() {
            const searchVal = (document.getElementById('attSearchInput')?.value || '').toLowerCase().trim();
            const courseVal = document.getElementById('attCourseFilter')?.value || '';
            const statusVal = document.getElementById('attStatusFilter')?.value || '';

            let count = 0;

            const matchItem = (item) => {
                const title = (item.dataset.title || '').toLowerCase();
                const course = item.dataset.course || '';
                const status = item.dataset.status || '';

                const matchesSearch = !searchVal || title.includes(searchVal);
                const matchesCourse = !courseVal || course === courseVal;
                const matchesStatus = !statusVal || status === statusVal;

                return matchesSearch && matchesCourse && matchesStatus;
            };

            document.querySelectorAll('.att-session-card').forEach(card => {
                if (matchItem(card)) {
                    card.classList.remove('hidden');
                    count++;
                } else {
                    card.classList.add('hidden');
                }
            });

            document.querySelectorAll('.att-table-row').forEach(row => {
                if (matchItem(row)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            const countBadge = document.getElementById('attCountBadge');
            if (countBadge) countBadge.textContent = count;
        }
        window.filterAttendanceData = filterAttendanceData;

        function setAttendanceFilter(statusKey) {
            if (typeof switchTeacherTab === 'function') {
                switchTeacherTab('attendance');
            }
            const statusSelect = document.getElementById('attStatusFilter');
            if (statusSelect) {
                statusSelect.value = statusKey || '';
            }
            filterAttendanceData();
        }
        window.setAttendanceFilter = setAttendanceFilter;

        function resetAttendanceFilters() {
            const searchInput = document.getElementById('attSearchInput');
            const courseSelect = document.getElementById('attCourseFilter');
            const statusSelect = document.getElementById('attStatusFilter');

            if (searchInput) searchInput.value = '';
            if (courseSelect) courseSelect.value = '';
            if (statusSelect) statusSelect.value = '';

            filterAttendanceData();
        }
        window.resetAttendanceFilters = resetAttendanceFilters;

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('attSearchInput')?.addEventListener('input', filterAttendanceData);
            document.getElementById('attCourseFilter')?.addEventListener('change', filterAttendanceData);
            document.getElementById('attStatusFilter')?.addEventListener('change', filterAttendanceData);
        });

        const isArLocale = @json(app()->getLocale() === 'ar');
        const appBaseUrl = (() => {
            // 1. If currently at /teacher-portal, anything preceding /teacher-portal is the application base path
            const tpIdx = window.location.pathname.indexOf('/teacher-portal');
            if (tpIdx !== -1) {
                return window.location.origin + window.location.pathname.substring(0, tpIdx);
            }
            // 2. If pathname contains /public
            const match = window.location.pathname.match(/^(.*?\/public)/);
            if (match) {
                return window.location.origin + match[1];
            }
            // 3. Fallback to Laravel rendered base path
            const serverUrl = @json(rtrim(url('/'), '/'));
            if (serverUrl && serverUrl.startsWith('http')) {
                try {
                    const p = new URL(serverUrl);
                    return window.location.origin + p.pathname.replace(/\/+$/, '');
                } catch (e) {}
            }
            return window.location.origin;
        })();

        // Global i18n Dictionary for Dynamic JS Elements
        const i18n = {
            studentProfile: @json(__('Student Profile')),
            loadingReview: @json(__('Loading review questions and choices breakdown...')),
            correct: @json(__('Correct')),
            incorrect: @json(__('Incorrect')),
            studentCorrectPick: @json(__('Student Selected (Correct)')),
            studentWrongPick: @json(__('Student Selected (Incorrect)')),
            correctKey: @json(__('Correct Key')),
            explanation: @json(__('Explanation:')),
            question: @json(__('Question')),
            noQuestionBreakdown: @json(__('No interactive questions logged for this assignment.')),
            unableToLoadBreakdown: @json(__('Unable to load submission breakdown.')),
            noCourses: @json(__('app.teacher.no_courses_enrolled')),
            noSessions: @json(__('app.teacher.no_sessions_found')),
            noAttendance: @json(__('app.teacher.no_attendance_found')),
            noAssignments: @json(__('app.teacher.no_assignments_found')),
            noNotes: @json(__('app.teacher.no_notes_found')),
            present: @json(__('Present')),
            late: @json(__('Late')),
            absent: @json(__('Absent')),
            excused: @json(__('Excused')),
            completed: @json(__('Completed')),
            scheduled: @json(__('Scheduled')),
            graded: @json(__('Graded')),
            pendingReview: @json(__('Pending Review')),
            inProgress: @json(__('In Progress')),
            passed: @json(__('Passed <i class="fa-solid fa-check"></i>')),
            failed: @json(__('Needs Improvement')),
        };

        let currentViewingStudentId = null;

        // ── Interactive Calendar Schedule Engine (Modern Dashboard Reference) ──────────
        const calCoursePalettes = [{
                key: 'emerald',
                bgClass: 'cal-card-emerald',
                dot: 'bg-emerald-500',
                bar: '#10b981',
                border: '#a7f3d0'
            },
            {
                key: 'purple',
                bgClass: 'cal-card-purple',
                dot: 'bg-purple-500',
                bar: '#8b5cf6',
                border: '#ddd6fe'
            },
            {
                key: 'sky',
                bgClass: 'cal-card-sky',
                dot: 'bg-sky-500',
                bar: '#0284c7',
                border: '#bae6fd'
            },
            {
                key: 'amber',
                bgClass: 'cal-card-amber',
                dot: 'bg-amber-500',
                bar: '#f59e0b',
                border: '#fde68a'
            },
            {
                key: 'rose',
                bgClass: 'cal-card-rose',
                dot: 'bg-rose-500',
                bar: '#f43f5e',
                border: '#fecdd3'
            }
        ];

        function getCalCoursePalette(courseId) {
            const cid = Math.abs(parseInt(courseId) || 0);
            return calCoursePalettes[cid % calCoursePalettes.length];
        }

        let currentCalDate = new Date();
        let currentMiniDate = new Date();
        let rawCalendarEvents = [];
        let currentCalView = window.innerWidth < 640 ? 'day' : 'week'; // Default to Day on mobile, Week on desktop
        let selectedCourseCategoryIds = []; // Empty = all categories active
        let calendarLoaded = false;

        async function initTeacherCalendar() {
            await fetchCalendarEvents();
            renderCalendar();
            calendarLoaded = true;
        }
        window.initTeacherCalendar = initTeacherCalendar;

        async function fetchCalendarEvents() {
            const startStr = getMonthStartStr(currentCalDate);
            const endStr = getMonthEndStr(currentCalDate);

            try {
                const res = await fetch(`{{ route('ajax.teacher.calendar.feed') }}?start=${startStr}&end=${endStr}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    rawCalendarEvents = await res.json();
                }
            } catch (e) {
                console.error('Failed to fetch calendar events:', e);
            }
        }

        function getMonthStartStr(date) {
            const d = new Date(date.getFullYear(), date.getMonth() - 1, 1);
            return d.toISOString().split('T')[0];
        }

        function getMonthEndStr(date) {
            const d = new Date(date.getFullYear(), date.getMonth() + 2, 0);
            return d.toISOString().split('T')[0];
        }

        function getWeekDays(refDate) {
            const d = new Date(refDate);
            const day = d.getDay(); // 0: Sun, 1: Mon, ... 6: Sat
            let diff;
            if (isArLocale) {
                diff = (day + 1) % 7; // Saturday as first day of week
            } else {
                diff = (day === 0 ? 6 : day - 1); // Monday as first day of week
            }
            const startOfWeek = new Date(d);
            startOfWeek.setDate(d.getDate() - diff);
            startOfWeek.setHours(0, 0, 0, 0);

            const days = [];
            for (let i = 0; i < 7; i++) {
                const wd = new Date(startOfWeek);
                wd.setDate(startOfWeek.getDate() + i);
                days.push(wd);
            }
            return days;
        }

        function getWeekNumber(d) {
            const target = new Date(d.valueOf());
            const dayNr = (d.getDay() + 6) % 7;
            target.setDate(target.getDate() - dayNr + 3);
            const firstThursday = target.valueOf();
            target.setMonth(0, 1);
            if (target.getDay() !== 4) {
                target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
            }
            return 1 + Math.ceil((firstThursday - target) / 604800000);
        }

        window.navigateCalendarStep = async function(delta) {
            if (currentCalView === 'day') {
                currentCalDate.setDate(currentCalDate.getDate() + delta);
            } else if (currentCalView === 'week') {
                currentCalDate.setDate(currentCalDate.getDate() + 7 * delta);
            } else if (currentCalView === 'grid' || currentCalView === 'agenda') {
                currentCalDate.setMonth(currentCalDate.getMonth() + delta);
            }
            currentMiniDate = new Date(currentCalDate);
            await fetchCalendarEvents();
            renderCalendar();
        };
        window.navigateCalendarWeekStep = window.navigateCalendarStep;

        window.navigateCalendarMonth = async function(delta) {
            currentCalDate.setMonth(currentCalDate.getMonth() + delta);
            currentMiniDate = new Date(currentCalDate);
            await fetchCalendarEvents();
            renderCalendar();
        };

        window.navigateCalendarToday = async function() {
            currentCalDate = new Date();
            currentMiniDate = new Date();
            await fetchCalendarEvents();
            renderCalendar();
        };

        window.navigateMiniMonth = function(delta) {
            currentMiniDate.setMonth(currentMiniDate.getMonth() + delta);
            renderMiniCalendar();
        };

        window.selectMiniCalendarDate = function(dateStr) {
            currentCalDate = new Date(dateStr);
            currentMiniDate = new Date(dateStr);
            renderCalendar();
            // On mobile, scroll smoothly to the calendar viewport
            if (window.innerWidth < 1024) {
                const panel = document.getElementById('calendarRightPanel');
                if (panel) panel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        };

        window.toggleCalCreateDropdown = function() {
            const menu = document.getElementById('calCreateDropdownMenu');
            if (menu) menu.classList.toggle('hidden');
        };

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('calCreateDropdownWrapper');
            const menu = document.getElementById('calCreateDropdownMenu');
            if (wrapper && menu && !wrapper.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        window.toggleCourseCategory = function(courseId) {
            const cid = String(courseId);
            const idx = selectedCourseCategoryIds.indexOf(cid);
            if (idx > -1) {
                selectedCourseCategoryIds.splice(idx, 1);
            } else {
                selectedCourseCategoryIds.push(cid);
            }
            renderCalendar();
        };

        window.resetCourseCategoryFilters = function() {
            selectedCourseCategoryIds = [];
            renderCalendar();
        };

        window.switchCalendarView = function(viewType) {
            currentCalView = viewType;
            const views = [{
                    key: 'day',
                    btnId: 'calViewBtnDay',
                    containerId: 'calendarDayView'
                },
                {
                    key: 'week',
                    btnId: 'calViewBtnWeek',
                    containerId: 'calendarWeekView'
                },
                {
                    key: 'grid',
                    btnId: 'calViewBtnGrid',
                    containerId: 'calendarGridView'
                },
                {
                    key: 'agenda',
                    btnId: 'calViewBtnAgenda',
                    containerId: 'calendarAgendaView'
                },
            ];

            views.forEach(v => {
                const btn = document.getElementById(v.btnId);
                const container = document.getElementById(v.containerId);
                const isActive = (v.key === viewType);

                if (btn) {
                    if (isActive) {
                        btn.classList.add('bg-indigo-600', 'text-white', 'shadow-2xs');
                        btn.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:text-slate-900',
                            'dark:hover:text-white');
                    } else {
                        btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-2xs');
                        btn.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:text-slate-900',
                            'dark:hover:text-white');
                    }
                }
                if (container) {
                    if (isActive) {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                }
            });

            renderCalendar();
        };

        function getFilteredCalendarEvents() {
            return rawCalendarEvents.filter(ev => {
                if (selectedCourseCategoryIds.length > 0 && !selectedCourseCategoryIds.includes(String(ev
                        .course_id))) {
                    return false;
                }
                return true;
            });
        }

        function renderCalendar() {
            // Update banner stat pills
            const totalEl = document.getElementById('calBannerTotalStat');
            const weekEl = document.getElementById('calBannerWeekStat');
            if (totalEl) totalEl.textContent = rawCalendarEvents.length;
            if (weekEl) {
                const wDays = getWeekDays(currentCalDate);
                const wStart = wDays[0].toISOString().split('T')[0];
                const wEnd = wDays[6].toISOString().split('T')[0];
                const weekCount = rawCalendarEvents.filter(ev => ev.date_str >= wStart && ev.date_str <= wEnd).length;
                weekEl.textContent = weekCount;
            }

            // Update mobile filter status badge
            const mobileStatus = document.getElementById('calMobileFilterStatus');
            if (mobileStatus) {
                if (selectedCourseCategoryIds.length === 0) {
                    mobileStatus.textContent = isArLocale ? 'جميع التصنيفات نشطة' : 'All course categories active';
                } else {
                    mobileStatus.textContent = isArLocale ?
                        `${selectedCourseCategoryIds.length} تصنيفات محددة (اضغط للتعديل)` :
                        `${selectedCourseCategoryIds.length} categories filtered (tap to edit)`;
                }
            }

            renderMiniCalendar();
            renderCategoriesCard();

            const events = getFilteredCalendarEvents();

            if (currentCalView === 'day') {
                renderCalendarDay(events);
            } else if (currentCalView === 'week') {
                renderCalendarWeek(events);
            } else if (currentCalView === 'grid') {
                renderCalendarGrid(currentCalDate.getFullYear(), currentCalDate.getMonth(), events);
            } else if (currentCalView === 'agenda') {
                renderCalendarAgenda(events);
            }
        }

        function renderMiniCalendar() {
            const titleEl = document.getElementById('miniCalTitle');
            const gridEl = document.getElementById('miniCalDaysGrid');
            if (!gridEl) return;

            const y = currentMiniDate.getFullYear();
            const m = currentMiniDate.getMonth();
            if (titleEl) {
                titleEl.textContent = currentMiniDate.toLocaleString(isArLocale ? 'ar-EG' : 'en-US', {
                    month: 'long',
                    year: 'numeric'
                });
            }

            const firstDayOfMonth = new Date(y, m, 1);
            const lastDayOfMonth = new Date(y, m + 1, 0);
            const dayOffsetMap = isArLocale ? [1, 2, 3, 4, 5, 6, 0] : [6, 0, 1, 2, 3, 4, 5];
            const startDayIndex = dayOffsetMap[firstDayOfMonth.getDay()];
            const totalDays = lastDayOfMonth.getDate();

            const todayStr = (new Date()).toISOString().split('T')[0];
            const selectedDateStr = currentCalDate.toISOString().split('T')[0];

            let html = '';

            const prevMonthLastDay = new Date(y, m, 0).getDate();
            for (let i = startDayIndex - 1; i >= 0; i--) {
                const pDay = prevMonthLastDay - i;
                html +=
                    `<div class="py-1 text-[11px] font-mono font-medium text-slate-300 dark:text-slate-600 opacity-40 select-none">${pDay}</div>`;
            }

            for (let d = 1; d <= totalDays; d++) {
                const dateObj = new Date(y, m, d);
                const yStr = dateObj.getFullYear();
                const mStr = String(dateObj.getMonth() + 1).padStart(2, '0');
                const dStr = String(d).padStart(2, '0');
                const dFormatted = `${yStr}-${mStr}-${dStr}`;

                const isToday = (dFormatted === todayStr);
                const isSelected = (dFormatted === selectedDateStr);
                const dayEvs = rawCalendarEvents.filter(ev => ev.date_str === dFormatted);

                let dotsHtml = '';
                if (dayEvs.length > 0) {
                    const dotCount = Math.min(dayEvs.length, 3);
                    dotsHtml = `<div class="flex items-center justify-center gap-0.5 mt-0.5">`;
                    for (let k = 0; k < dotCount; k++) {
                        const pal = getCalCoursePalette(dayEvs[k].course_id);
                        dotsHtml += `<span class="w-1 h-1 rounded-full ${pal.dot}"></span>`;
                    }
                    dotsHtml += `</div>`;
                }

                let badgeClass = 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800';
                if (isSelected) {
                    badgeClass = 'bg-indigo-600 text-white font-black shadow-xs';
                } else if (isToday) {
                    badgeClass = 'border border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold';
                }

                html += `
                    <button type="button" onclick="selectMiniCalendarDate('${dFormatted}')"
                        class="py-0.5 rounded-xl flex flex-col items-center justify-center transition-all cursor-pointer group min-h-[32px]">
                        <span class="w-6 sm:w-7 h-6 sm:h-7 rounded-full flex items-center justify-center text-xs font-mono ${badgeClass}">
                            ${d}
                        </span>
                        ${dotsHtml}
                    </button>
                `;
            }

            const currentGridCount = startDayIndex + totalDays;
            const targetGridCount = currentGridCount > 35 ? 42 : 35;
            const nextPadding = targetGridCount - currentGridCount;
            for (let n = 1; n <= nextPadding; n++) {
                html +=
                    `<div class="py-1 text-[11px] font-mono font-medium text-slate-300 dark:text-slate-600 opacity-40 select-none">${n}</div>`;
            }

            gridEl.innerHTML = html;
        }

        function renderCategoriesCard() {
            const listEl = document.getElementById('calCategoriesList');
            if (!listEl) return;

            const courseMap = new Map();
            rawCalendarEvents.forEach(ev => {
                if (ev.course_id && !courseMap.has(String(ev.course_id))) {
                    courseMap.set(String(ev.course_id), {
                        id: ev.course_id,
                        title: ev.course,
                        totalMinutes: 0
                    });
                }
            });

            @foreach ($courses as $c)
                if (!courseMap.has('{{ $c->id }}')) {
                    courseMap.set('{{ $c->id }}', {
                        id: '{{ $c->id }}',
                        title: @json($c->title),
                        totalMinutes: 0
                    });
                }
            @endforeach

            rawCalendarEvents.forEach(ev => {
                if (ev.course_id && courseMap.has(String(ev.course_id))) {
                    const c = courseMap.get(String(ev.course_id));
                    c.totalMinutes += (ev.duration_minutes || 60);
                }
            });

            if (courseMap.size === 0) {
                listEl.innerHTML =
                    `<p class="text-xs font-mono text-slate-400 py-2">${isArLocale ? 'لا توجد مقررات' : 'No categories'}</p>`;
                return;
            }

            let html = '';
            courseMap.forEach(c => {
                const pal = getCalCoursePalette(c.id);
                const isChecked = (selectedCourseCategoryIds.length === 0 || selectedCourseCategoryIds.includes(
                    String(c.id)));
                const hours = Math.floor(c.totalMinutes / 60);
                const mins = String(c.totalMinutes % 60).padStart(2, '0');
                const durationFormatted = `${hours}h${mins}`;

                html += `
                    <div onclick="toggleCourseCategory('${c.id}')"
                        class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all cursor-pointer select-none">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-4 h-4 rounded-md flex items-center justify-center transition-all ${isChecked ? pal.dot + ' text-white' : 'border-2 border-slate-300 dark:border-slate-600'}">
                                ${isChecked ? '<i class="fa-solid fa-check text-[9px]"></i>' : ''}
                            </span>
                            <span class="text-xs font-heading font-bold text-slate-800 dark:text-slate-200 truncate" title="${c.title}">
                                ${c.title}
                            </span>
                        </div>
                        <span class="text-[11px] font-mono font-bold text-slate-400 dark:text-slate-500 shrink-0 ms-2">
                            ${durationFormatted}
                        </span>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        // ── VIEW 1: DEDICATED DAY HOURLY TIMETABLE ─────────────────────────────────────
        function renderCalendarDay(events) {
            const container = document.getElementById('calendarDayContainer');
            const heroTitle = document.getElementById('calendarHeroTitle');
            const weekBadge = document.getElementById('calendarWeekBadge');
            const dayStrip = document.getElementById('calDayViewDayStrip');
            if (!container) return;

            const dObj = new Date(currentCalDate);
            const todayStr = (new Date()).toISOString().split('T')[0];
            const activeDateStr = dObj.toISOString().split('T')[0];
            const isToday = (activeDateStr === todayStr);

            // Title & Badge
            if (heroTitle) {
                heroTitle.textContent = isArLocale ?
                    dObj.toLocaleDateString('ar-EG', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    }) :
                    dObj.toLocaleDateString('en-US', {
                        weekday: 'long',
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
            }
            if (weekBadge) {
                weekBadge.textContent = isToday ?
                    (isArLocale ? 'اليوم' : 'Today') :
                    (isArLocale ? dObj.toLocaleDateString('ar-EG', {
                        weekday: 'short'
                    }) : dObj.toLocaleDateString('en-US', {
                        weekday: 'short'
                    }));
            }

            // Render Day View Quick Strip (Current week around this date)
            if (dayStrip) {
                const weekDays = getWeekDays(dObj);
                let stripHtml = '';
                weekDays.forEach(wDay => {
                    const dStr = wDay.toISOString().split('T')[0];
                    const dayName = wDay.toLocaleDateString(isArLocale ? 'ar-EG' : 'en-US', {
                        weekday: 'short'
                    });
                    const dateNum = wDay.getDate();
                    const isSelected = (dStr === activeDateStr);
                    const isDayToday = (dStr === todayStr);
                    const count = events.filter(ev => ev.date_str === dStr).length;

                    stripHtml += `
                        <button type="button" onclick="selectMiniCalendarDate('${dStr}')"
                            class="shrink-0 px-2.5 sm:px-3 py-1 rounded-xl text-center border transition-all cursor-pointer ${isSelected ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : (isDayToday ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-100')}">
                            <span class="block text-[9px] font-mono font-bold leading-tight opacity-75">${dayName}</span>
                            <span class="block text-xs font-mono font-black leading-tight mt-0.5">${dateNum}</span>
                            ${count > 0 ? `<span class="inline-block mt-0.5 w-1.5 h-1.5 rounded-full ${isSelected ? 'bg-white' : 'bg-indigo-500'}"></span>` : ''}
                        </button>
                    `;
                });
                dayStrip.innerHTML = stripHtml;
            }

            // Hourly Day Timetable Grid (08:00 to 21:00 = 14 hours)
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();

            let timeLabelsHtml = `<div class="flex flex-col select-none">`;
            for (let h = 8; h <= 21; h++) {
                const timeLabel = `${String(h).padStart(2, '0')}:00`;
                timeLabelsHtml += `
                    <div class="cal-hour-label flex items-start justify-center pt-1 text-[11px] font-mono font-bold text-slate-400 dark:text-slate-500">
                        ${timeLabel}
                    </div>
                `;
            }
            timeLabelsHtml += `</div>`;

            // Sessions on this specific date
            const dayEvents = events.filter(ev => ev.date_str === activeDateStr);

            let dayColHtml =
                `<div class="cal-single-day-col relative ${isToday ? 'bg-indigo-50/15 dark:bg-indigo-950/15' : ''}">`;

            // Real-time "now" line
            if (isToday && currentHour >= 8 && currentHour <= 21) {
                const minutesFrom8 = (currentHour - 8) * 60 + currentMinute;
                const topPx = (minutesFrom8 / 60) * 65;
                dayColHtml += `
                    <div style="position: absolute; top: ${topPx}px; left: 0; right: 0; height: 2px; background: #4f46e5; z-index: 25; pointer-events: none;">
                        <span style="position: absolute; top: -4px; left: -4px; width: 10px; height: 10px; border-radius: 50%; background: #4f46e5; box-shadow: 0 0 10px rgba(79,70,229,0.8);"></span>
                    </div>
                `;
            }

            if (dayEvents.length === 0) {
                dayColHtml += `
                    <div class="absolute inset-x-4 top-12 p-6 text-center bg-slate-50/80 dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700/80 space-y-2">
                        <i class="fa-solid fa-calendar-day text-2xl text-slate-300 dark:text-slate-600"></i>
                        <p class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">
                            ${isArLocale ? 'لا توجد جلسات تدريس مجدولة لهذا اليوم' : 'No teaching sessions scheduled for this day.'}
                        </p>
                        <button type="button" onclick="openScheduleModal('singleSessionModal')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-heading font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 rounded-xl hover:bg-indigo-100 transition-all cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>${isArLocale ? 'جدولة جلسة جديدة' : 'Schedule New Session'}</span>
                        </button>
                    </div>
                `;
            } else {
                // Check overlaps and arrange cards
                dayEvents.forEach((ev, idx) => {
                    const [hStr, mStr] = (ev.start_time_str || '10:00').split(':');
                    const h = parseInt(hStr) || 8;
                    const m = parseInt(mStr) || 0;
                    const minutesFrom8 = Math.max(0, (h - 8) * 60 + m);
                    const top = (minutesFrom8 / 60) * 65;
                    const duration = ev.duration_minutes || 60;
                    const height = Math.max(62, (duration / 60) * 65);
                    const pal = getCalCoursePalette(ev.course_id);

                    dayColHtml += `
                        <div onclick="openCalendarSessionDetails(${ev.id})"
                            style="position: absolute; top: ${top}px; height: ${height}px; left: 8px; right: 8px; z-index: 15;"
                            class="${pal.bgClass} rounded-2xl p-3 shadow-xs hover:shadow-md hover:scale-[1.008] transition-all cursor-pointer overflow-hidden flex flex-col justify-between">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <span class="text-xs font-heading font-black truncate">${ev.title}</span>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-extrabold bg-white/70 dark:bg-slate-900/60 shadow-2xs">${ev.time_formatted}</span>
                                        ${ev.is_recurring ? '<span title="Recurring" class="w-5 h-5 rounded-full bg-white/70 dark:bg-slate-900/60 flex items-center justify-center text-[9px]"><i class="fa-solid fa-rotate text-purple-600"></i></span>' : ''}
                                    </div>
                                </div>
                                <p class="text-[11px] font-mono opacity-85 truncate">
                                    <i class="fa-solid fa-book-open me-1"></i> ${ev.course} ${ev.subject ? `&bull; ${ev.subject}` : ''}
                                </p>
                                <p class="text-[10px] font-mono opacity-80 truncate">
                                    <i class="fa-solid fa-user-graduate me-1"></i> ${ev.student_name}
                                </p>
                            </div>
                            <div class="flex items-center justify-between gap-2 pt-1 border-t border-black/5 dark:border-white/5">
                                <span class="text-[10px] font-mono font-bold opacity-75">${ev.duration_minutes}m</span>
                                ${ev.meeting_link ? `
                                        <a href="${ev.meeting_link}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();"
                                            class="px-2.5 py-1 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold shadow-xs flex items-center gap-1.5 transition-all">
                                            <i class="fa-solid fa-video text-[9px]"></i>
                                            <span>${isArLocale ? 'انضمام' : 'Join'}</span>
                                        </a>
                                    ` : ''}
                            </div>
                        </div>
                    `;
                });
            }

            dayColHtml += `</div>`;

            container.innerHTML = `
                <div class="cal-single-day-grid relative">
                    ${timeLabelsHtml}
                    ${dayColHtml}
                </div>
            `;
        }

        // ── VIEW 2: HERO WEEK TIMETABLE ──────────────────────────────────────────────
        function renderCalendarWeek(events) {
            const container = document.getElementById('calendarWeekContainer');
            const heroTitle = document.getElementById('calendarHeroTitle');
            const weekBadge = document.getElementById('calendarWeekBadge');
            if (!container) return;

            const weekDays = getWeekDays(currentCalDate);
            const firstDay = weekDays[0];
            const lastDay = weekDays[6];

            const startNum = firstDay.getDate();
            const endNum = lastDay.getDate();
            const monthName = firstDay.toLocaleString(isArLocale ? 'ar-EG' : 'en-US', {
                month: 'long',
                year: 'numeric'
            });
            if (heroTitle) {
                heroTitle.textContent = isArLocale ? `${startNum} - ${endNum} ${monthName}` :
                    `${monthName.split(' ')[0]} ${startNum} - ${endNum}, ${firstDay.getFullYear()}`;
            }
            if (weekBadge) {
                const wNum = getWeekNumber(firstDay);
                weekBadge.textContent = isArLocale ? `الأسبوع ${wNum}` : `Week ${wNum}`;
            }

            const todayStr = (new Date()).toISOString().split('T')[0];
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            let todayIndex = -1;

            // Render Mobile Day Strip
            const mobileStrip = document.getElementById('calMobileDayStrip');
            if (mobileStrip) {
                let stripHtml = '';
                weekDays.forEach((wDay, idx) => {
                    const dStr = wDay.toISOString().split('T')[0];
                    const dayName = wDay.toLocaleDateString(isArLocale ? 'ar-EG' : 'en-US', {
                        weekday: 'short'
                    });
                    const dateNum = wDay.getDate();
                    const isToday = (dStr === todayStr);
                    if (isToday) todayIndex = idx;
                    const dayEventsCount = events.filter(ev => ev.date_str === dStr).length;

                    stripHtml += `
                        <button type="button" onclick="scrollCalendarToDay(${idx})"
                            class="cal-day-strip-btn shrink-0 px-2.5 py-1 rounded-xl text-center border transition-all cursor-pointer ${isToday ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-100'}">
                            <span class="block text-[9px] font-mono font-bold leading-tight opacity-75">${dayName}</span>
                            <span class="block text-xs font-mono font-black leading-tight mt-0.5">${dateNum}</span>
                            ${dayEventsCount > 0 ? `<span class="inline-block mt-0.5 w-1.5 h-1.5 rounded-full ${isToday ? 'bg-white' : 'bg-indigo-500'}"></span>` : ''}
                        </button>
                    `;
                });
                mobileStrip.innerHTML = stripHtml;
            }

            let headerHtml = `
                <div class="cal-timetable-grid pb-2 border-b border-slate-100 dark:border-slate-800 text-center">
                    <div class="w-12 sm:w-14"></div>
            `;

            weekDays.forEach((wDay, idx) => {
                const dStr = wDay.toISOString().split('T')[0];
                const dayName = wDay.toLocaleDateString(isArLocale ? 'ar-EG' : 'en-US', {
                    weekday: 'short'
                });
                const dateNum = wDay.getDate();
                const isToday = (dStr === todayStr);

                headerHtml += `
                    <div class="px-1 py-1 cursor-pointer hover:opacity-80 transition-opacity" onclick="scrollCalendarToDay(${idx})">
                        <span class="block text-[11px] font-mono font-bold ${isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500'} uppercase tracking-wider">${dayName}</span>
                        <span class="inline-flex items-center justify-center w-7 sm:w-8 h-7 sm:h-8 rounded-full font-mono text-xs sm:text-sm font-black mt-0.5 ${isToday ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-300' : 'text-slate-800 dark:text-slate-100'}">
                            ${dateNum}
                        </span>
                    </div>
                `;
            });
            headerHtml += `</div>`;

            let bodyHtml = `
                <div class="cal-timetable-grid relative mt-2">
                    <div class="flex flex-col">
            `;

            for (let h = 8; h <= 21; h++) {
                const timeLabel = `${String(h).padStart(2, '0')}:00`;
                bodyHtml += `
                    <div class="cal-hour-label flex items-start justify-center pt-1 text-[11px] font-mono font-bold text-slate-400 dark:text-slate-500 select-none">
                        ${timeLabel}
                    </div>
                `;
            }
            bodyHtml += `</div>`;

            weekDays.forEach((wDay, idx) => {
                const dStr = wDay.toISOString().split('T')[0];
                const isToday = (dStr === todayStr);
                const dayEvents = events.filter(ev => ev.date_str === dStr);

                bodyHtml +=
                    `<div class="cal-day-col ${isToday ? 'cal-today-col bg-indigo-50/20 dark:bg-indigo-950/20' : ''}" data-is-today="${isToday}" data-day-idx="${idx}">`;

                if (isToday && currentHour >= 8 && currentHour <= 21) {
                    const minutesFrom8am = (currentHour - 8) * 60 + currentMinute;
                    const topPx = (minutesFrom8am / 60) * 65;
                    bodyHtml += `
                        <div style="position: absolute; top: ${topPx}px; left: 0; right: 0; height: 2px; background: #4f46e5; z-index: 20; pointer-events: none;">
                            <span style="position: absolute; top: -4px; left: -4px; width: 10px; height: 10px; border-radius: 50%; background: #4f46e5; box-shadow: 0 0 8px rgba(79,70,229,0.8);"></span>
                        </div>
                    `;
                }

                dayEvents.forEach(ev => {
                    const [hStr, mStr] = (ev.start_time_str || '10:00').split(':');
                    const h = parseInt(hStr) || 8;
                    const m = parseInt(mStr) || 0;
                    const minutesFrom8 = Math.max(0, (h - 8) * 60 + m);
                    const top = (minutesFrom8 / 60) * 65;
                    const duration = ev.duration_minutes || 60;
                    const height = Math.max(54, (duration / 60) * 65);
                    const pal = getCalCoursePalette(ev.course_id);

                    bodyHtml += `
                        <div onclick="event.stopPropagation(); openCalendarSessionDetails(${ev.id})"
                            style="position: absolute; top: ${top}px; height: ${height}px; left: 3px; right: 3px; z-index: 10;"
                            class="${pal.bgClass} rounded-2xl p-2 sm:p-2.5 shadow-2xs hover:shadow-md hover:scale-[1.01] transition-all cursor-pointer overflow-hidden flex flex-col justify-between"
                            title="${ev.title} (${ev.time_formatted})">
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-heading font-black truncate leading-tight">${ev.title}</span>
                                    ${ev.is_recurring ? '<i class="fa-solid fa-rotate text-[9px] opacity-75 shrink-0"></i>' : ''}
                                </div>
                                <span class="block text-[10px] font-mono opacity-85 truncate">${ev.course}</span>
                            </div>
                            <div class="flex items-center justify-between gap-1 pt-1">
                                <span class="text-[10px] font-mono font-extrabold opacity-90">${ev.time_formatted}</span>
                                ${ev.meeting_link ? `
                                        <a href="${ev.meeting_link}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();"
                                            class="px-2 py-0.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[9px] font-bold shadow-2xs flex items-center gap-1 shrink-0 transition-all">
                                            <i class="fa-solid fa-video text-[8px]"></i>
                                        </a>
                                    ` : ''}
                            </div>
                        </div>
                    `;
                });

                bodyHtml += `</div>`;
            });

            bodyHtml += `</div>`;

            container.innerHTML = headerHtml + bodyHtml;

            // Auto-scroll on mobile to today column if today is in this week
            if (window.innerWidth < 1024 && todayIndex >= 0) {
                setTimeout(() => {
                    scrollCalendarToDay(todayIndex);
                }, 100);
            }
        }

        // ── VIEW 3: MONTH GRID VIEW ──────────────────────────────────────────────────
        function renderCalendarGrid(year, month, events) {
            const grid = document.getElementById('calendarMonthGrid');
            const heroTitle = document.getElementById('calendarHeroTitle');
            const weekBadge = document.getElementById('calendarWeekBadge');
            if (!grid) return;

            if (heroTitle) {
                const d = new Date(year, month, 1);
                heroTitle.textContent = d.toLocaleString(isArLocale ? 'ar-EG' : 'en-US', {
                    month: 'long',
                    year: 'numeric'
                });
            }
            if (weekBadge) {
                weekBadge.textContent = isArLocale ? 'عرض الشهر' : 'Month View';
            }

            const firstDayOfMonth = new Date(year, month, 1);
            const lastDayOfMonth = new Date(year, month + 1, 0);

            const dayOffsetMap = isArLocale ? [1, 2, 3, 4, 5, 6, 0] : [6, 0, 1, 2, 3, 4, 5];
            const startDayIndex = dayOffsetMap[firstDayOfMonth.getDay()];
            const totalDays = lastDayOfMonth.getDate();

            const todayStr = (new Date()).toISOString().split('T')[0];

            let html = '';

            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startDayIndex - 1; i >= 0; i--) {
                const pDay = prevMonthLastDay - i;
                html += `<div class="cal-month-cell rounded-2xl bg-slate-50/40 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800/50 opacity-30 select-none flex flex-col justify-between">
                    <span class="text-xs font-mono font-bold text-slate-400">${pDay}</span>
                </div>`;
            }

            for (let d = 1; d <= totalDays; d++) {
                const dateObj = new Date(year, month, d);
                const yearStr = dateObj.getFullYear();
                const monthStr = String(dateObj.getMonth() + 1).padStart(2, '0');
                const dayStr = String(d).padStart(2, '0');
                const formattedDateStr = `${yearStr}-${monthStr}-${dayStr}`;

                const isToday = (formattedDateStr === todayStr);
                const dayEvents = events.filter(ev => ev.date_str === formattedDateStr);

                // Desktop events view
                let desktopEventsHtml = '';
                const displayCount = Math.min(dayEvents.length, 3);
                for (let k = 0; k < displayCount; k++) {
                    const ev = dayEvents[k];
                    const pal = getCalCoursePalette(ev.course_id);

                    desktopEventsHtml += `
                        <div onclick="event.stopPropagation(); openCalendarSessionDetails(${ev.id})"
                            class="px-1.5 py-0.5 rounded-lg ${pal.bgClass} text-[10px] font-mono font-bold truncate cursor-pointer hover:opacity-90 transition-all flex items-center justify-between gap-1 shadow-2xs"
                            title="${ev.title} (${ev.time_formatted})">
                            <span class="truncate">${ev.start_time_str} ${ev.title}</span>
                            ${ev.is_recurring ? '<i class="fa-solid fa-rotate text-[8px] opacity-80"></i>' : ''}
                        </div>
                    `;
                }
                if (dayEvents.length > 3) {
                    desktopEventsHtml += `
                        <div class="text-[9px] font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-center">
                            +${dayEvents.length - 3} ${isArLocale ? 'أكثر' : 'more'}
                        </div>
                    `;
                }

                // Mobile dots view (Guarantees zero text overflow on phone screens!)
                let mobileDotsHtml = '';
                if (dayEvents.length > 0) {
                    mobileDotsHtml = `<div class="flex items-center justify-center gap-1 flex-wrap mt-1">`;
                    const mCount = Math.min(dayEvents.length, 3);
                    for (let m = 0; m < mCount; m++) {
                        const pal = getCalCoursePalette(dayEvents[m].course_id);
                        mobileDotsHtml += `<span class="w-1.5 h-1.5 rounded-full ${pal.dot}"></span>`;
                    }
                    if (dayEvents.length > 3) {
                        mobileDotsHtml +=
                            `<span class="text-[9px] font-mono font-black text-indigo-600 dark:text-indigo-400">+</span>`;
                    }
                    mobileDotsHtml += `</div>`;
                }

                const todayBorder = isToday ? 'border-2 border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/30 shadow-xs' :
                    'border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900/90 hover:border-indigo-300 dark:hover:border-indigo-700';

                html += `
                    <div onclick="openDayDetailsModal('${formattedDateStr}')"
                        class="cal-month-cell rounded-2xl ${todayBorder} transition-all cursor-pointer flex flex-col justify-between">
                        <div class="flex items-center justify-between w-full">
                            <span class="text-xs font-mono font-black ${isToday ? 'w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center' : 'text-slate-700 dark:text-slate-300'}">${d}</span>
                            ${dayEvents.length > 0 ? `<span class="hidden sm:inline-block px-1.5 py-0.2 text-[9px] font-mono font-bold rounded-full bg-indigo-100 dark:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300">${dayEvents.length}</span>` : ''}
                        </div>
                        {{-- Desktop Event Pills --}}
                        <div class="cal-grid-cell-content-desktop space-y-1 overflow-hidden flex-1 mt-1">
                            ${desktopEventsHtml}
                        </div>
                        {{-- Mobile Responsive Dots --}}
                        <div class="cal-grid-cell-content-mobile items-center justify-center flex-1">
                            ${mobileDotsHtml}
                        </div>
                    </div>
                `;
            }

            const currentGridCount = startDayIndex + totalDays;
            const targetGridCount = currentGridCount > 35 ? 42 : 35;
            const nextPadding = targetGridCount - currentGridCount;

            for (let n = 1; n <= nextPadding; n++) {
                html += `<div class="cal-month-cell rounded-2xl bg-slate-50/40 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800/50 opacity-30 select-none flex flex-col justify-between">
                    <span class="text-xs font-mono font-bold text-slate-400">${n}</span>
                </div>`;
            }

            grid.innerHTML = html;
        }

        // ── VIEW 4: DAILY AGENDA LIST VIEW (GROUPED BY DATES) ────────────────────────
        function renderCalendarAgenda(events) {
            const container = document.getElementById('calendarAgendaContainer');
            const heroTitle = document.getElementById('calendarHeroTitle');
            const weekBadge = document.getElementById('calendarWeekBadge');
            if (!container) return;

            if (heroTitle) {
                const d = new Date(currentCalDate);
                heroTitle.textContent = isArLocale ?
                    `أجندة ${d.toLocaleString('ar-EG', { month: 'long', year: 'numeric' })}` :
                    `Agenda: ${d.toLocaleString('en-US', { month: 'long', year: 'numeric' })}`;
            }
            if (weekBadge) {
                weekBadge.textContent = `${events.length} ${isArLocale ? 'جلسات' : 'Sessions'}`;
            }

            if (events.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 space-y-3">
                        <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 dark:text-slate-600 mb-1"></i>
                        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">${isArLocale ? 'لا توجد حصص مجدولة لهذا الشهر' : 'No sessions scheduled for this period.'}</p>
                        <button type="button" onclick="openScheduleModal('singleSessionModal')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-2xl shadow-sm transition-all cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>${isArLocale ? 'إنشاء جلسة جديدة' : 'Create Session'}</span>
                        </button>
                    </div>
                `;
                return;
            }

            // Sort chronologically
            const sortedEvents = [...events].sort((a, b) => {
                const dtA = (a.date_str || '') + ' ' + (a.start_time_str || '');
                const dtB = (b.date_str || '') + ' ' + (b.start_time_str || '');
                return dtA.localeCompare(dtB);
            });

            // Group by date_str
            const groups = new Map();
            sortedEvents.forEach(ev => {
                const dKey = ev.date_str || 'Undated';
                if (!groups.has(dKey)) {
                    groups.set(dKey, []);
                }
                groups.get(dKey).push(ev);
            });

            const todayStr = (new Date()).toISOString().split('T')[0];
            const tomorrowObj = new Date();
            tomorrowObj.setDate(tomorrowObj.getDate() + 1);
            const tomorrowStr = tomorrowObj.toISOString().split('T')[0];

            let html = '';

            groups.forEach((dayEvs, dKey) => {
                const dateObj = new Date(dKey + 'T00:00:00');
                const isToday = (dKey === todayStr);
                const isTomorrow = (dKey === tomorrowStr);

                let dateBadgeText = '';
                if (isToday) {
                    dateBadgeText =
                        `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-indigo-600 text-white shadow-2xs">${isArLocale ? 'اليوم' : 'Today'}</span>`;
                } else if (isTomorrow) {
                    dateBadgeText =
                        `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 dark:bg-teal-950 text-teal-800 dark:text-teal-300">${isArLocale ? 'غداً' : 'Tomorrow'}</span>`;
                }

                const dateFormatted = dateObj.toLocaleDateString(isArLocale ? 'ar-EG' : 'en-US', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                html += `
                    <div class="space-y-2.5">
                        {{-- Date Section Header --}}
                        <div class="flex items-center gap-2 pt-2 border-b border-slate-100 dark:border-slate-800 pb-1.5">
                            <span class="w-2 h-2 rounded-full ${isToday ? 'bg-indigo-600 animate-ping' : 'bg-slate-300 dark:bg-slate-600'}"></span>
                            <h4 class="font-heading font-black text-xs sm:text-sm text-slate-800 dark:text-slate-200">${dateFormatted}</h4>
                            ${dateBadgeText}
                            <span class="text-[10px] font-mono text-slate-400 ms-auto">${dayEvs.length} ${isArLocale ? 'حصص' : 'sessions'}</span>
                        </div>

                        {{-- Session Cards for this date --}}
                        <div class="space-y-2">
                `;

                dayEvs.forEach(ev => {
                    const pal = getCalCoursePalette(ev.course_id);
                    const statusBadge = ev.status === 'completed' ?
                        `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">${isArLocale ? 'مكتملة' : 'Completed'}</span>` :
                        (ev.status === 'in_progress' ?
                            `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200 animate-pulse">${isArLocale ? 'جارية الآن' : 'Live Now'}</span>` :
                            (ev.status === 'cancelled' ?
                                `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200">${isArLocale ? 'ملغاة' : 'Cancelled'}</span>` :
                                `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200">${isArLocale ? 'مجدولة' : 'Scheduled'}</span>`
                                ));

                    html += `
                        <div class="p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/60 hover:border-indigo-300 dark:hover:border-indigo-700 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs border-s-4" style="border-inline-start-color: ${pal.bar};">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h5 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white truncate">${ev.title}</h5>
                                    ${statusBadge}
                                    ${ev.is_recurring ? `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300"><i class="fa-solid fa-rotate text-[8px] me-1"></i>${isArLocale ? 'دوري' : 'Recurring'}</span>` : ''}
                                </div>
                                <p class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-clock text-indigo-500 me-1"></i> ${ev.time_formatted} (${ev.duration_minutes}m)
                                </p>
                                <p class="text-xs font-mono text-slate-600 dark:text-slate-300 truncate">
                                    <i class="fa-solid fa-book-open text-slate-400 me-1"></i> ${ev.course} ${ev.subject ? `&bull; ${ev.subject}` : ''} &bull; <i class="fa-solid fa-user-graduate text-slate-400 me-1"></i> ${ev.student_name}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                ${ev.meeting_link ? `
                                        <a href="${ev.meeting_link}" target="_blank" rel="noopener noreferrer"
                                            class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition-all">
                                            <i class="fa-solid fa-video text-xs"></i>
                                            <span>${isArLocale ? 'انضمام' : 'Join'}</span>
                                        </a>
                                    ` : ''}
                                <button type="button" onclick="openCalendarSessionDetails(${ev.id})"
                                    class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                    ${isArLocale ? 'التفاصيل' : 'Details'}
                                </button>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // ── MODAL: DAY DETAILS & SESSION DETAILS ──────────────────────────────────────
        window.openDayDetailsModal = function(dateStr) {
            const modal = document.getElementById('dayDetailsModal');
            const title = document.getElementById('dayDetailsModalTitle');
            const body = document.getElementById('dayDetailsModalBody');
            if (!modal || !body) return;

            if (title) {
                title.innerHTML =
                    `<i class="fa-solid fa-calendar-day text-indigo-500"></i> <span>${isArLocale ? 'حصص يوم' : 'Sessions for'} ${dateStr}</span>`;
            }

            const dayEvents = rawCalendarEvents.filter(ev => ev.date_str === dateStr);

            if (dayEvents.length === 0) {
                body.innerHTML = `
                    <div class="text-center py-8 text-slate-400 font-mono text-xs space-y-2">
                        <i class="fa-solid fa-calendar-xmark text-2xl text-slate-300 mb-1"></i>
                        <p>${isArLocale ? 'لا توجد حصص مجدولة لهذا اليوم.' : 'No sessions scheduled for this day.'}</p>
                        <button type="button" onclick="closeModal('dayDetailsModal'); openScheduleModal('singleSessionModal');"
                            class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-xs inline-flex items-center gap-1 mt-2 cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i> ${isArLocale ? 'جدولة حصة' : 'Schedule Session'}
                        </button>
                    </div>
                `;
            } else {
                let html = '';
                dayEvents.forEach(ev => {
                    const pal = getCalCoursePalette(ev.course_id);
                    html += `
                        <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 space-y-2 border-s-4" style="border-inline-start-color: ${pal.bar};">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white truncate">${ev.title}</h4>
                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-md bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 shrink-0">${ev.time_formatted}</span>
                            </div>
                            <p class="text-xs font-mono text-slate-500 dark:text-slate-400 truncate">
                                <i class="fa-solid fa-book-open me-1"></i> ${ev.course} &bull; ${ev.student_name}
                            </p>
                            ${ev.meeting_link ? `
                                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700 flex justify-end">
                                        <a href="${ev.meeting_link}" target="_blank" rel="noopener"
                                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                                            <i class="fa-solid fa-video"></i> ${isArLocale ? 'رابط البث' : 'Meeting Link'}
                                        </a>
                                    </div>
                                ` : ''}
                        </div>
                    `;
                });
                body.innerHTML = html;
            }

            if (window.openModal) window.openModal('dayDetailsModal');
        };

        window.openCalendarSessionDetails = function(sessionId) {
            const ev = rawCalendarEvents.find(e => String(e.id) === String(sessionId));
            if (!ev) return;

            openDayDetailsModal(ev.date_str);
        };

        // ── Tab Switcher for Main Teacher Portal ──────────────────────────────────────
        function switchTeacherTab(tabKey) {
            if (!tabKey) return;
            const cleanKey = String(tabKey).replace('#', '').trim();
            const validTabs = ['overview', 'students', 'sessions', 'assignments', 'attendance', 'notifications',
                'schedules', 'calendar', 'exceptions'
            ];
            const targetKey = validTabs.includes(cleanKey) ? cleanKey : 'overview';

            document.querySelectorAll('.teacher-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.teacher-tab-btn').forEach(btn => {
                btn.classList.remove('bg-teal-600', 'bg-indigo-600', 'text-white', 'shadow-md', 'active');
                btn.classList.add('text-slate-700', 'hover:bg-slate-100');
            });

            const activeContent = document.getElementById('teacher-tab-' + targetKey);
            const activeBtn = document.getElementById('tab-btn-' + targetKey);
            if (activeContent) activeContent.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-100');
                const btnColor = (targetKey === 'schedules' || targetKey === 'calendar') ? 'bg-indigo-600' : 'bg-teal-600';
                activeBtn.classList.add(btnColor, 'text-white', 'shadow-md', 'active');
            }

            document.querySelectorAll('#portalSidebar .teacher-tab-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-tab') === targetKey) {
                    btn.classList.add('active');
                }
            });

            if (targetKey === 'calendar' && typeof window.initTeacherCalendar === 'function') {
                window.initTeacherCalendar();
            }

            // Keep URL hash updated without full page reload or jump
            if (window.history && window.history.replaceState) {
                try {
                    const cur = new URL(window.location.href);
                    cur.hash = targetKey;
                    window.history.replaceState(null, '', cur.toString());
                } catch (e) {}
            }

            // Mobile drawer auto-close
            if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
                togglePortalSidebar(false);
            }
        }
        window.switchTeacherTab = switchTeacherTab;

        function filterTeacherExceptions(status, btn) {
            document.querySelectorAll('.exception-filter-btn').forEach(b => {
                b.className =
                    'exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 transition-all cursor-pointer';
            });
            if (btn) {
                btn.className =
                    'exception-filter-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-teal-600 text-white shadow-xs transition-all cursor-pointer';
            }

            document.querySelectorAll('.teacher-exception-card').forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
        window.filterTeacherExceptions = filterTeacherExceptions;

        async function reviewTeacherException(exceptionId, action) {
            const isApprove = action === 'approve';
            const confirmMsg = isApprove ?
                (isArLocale ? 'هل أنت متأكد من قبول العذر؟ لن يتم خصم الحصة من رصيد باقة الطالب.' :
                    'Are you sure you want to approve this excuse? The session will NOT be deducted from the student balance.'
                    ) :
                (isArLocale ? 'هل أنت متأكد من رفض العذر؟ سيتم خصم حصة واحدة من رصيد باقة الطالب.' :
                    'Are you sure you want to reject this excuse? 1 session credit WILL be deducted from the student package balance.'
                    );

            if (!confirm(confirmMsg)) return;

            try {
                const url = `${appBaseUrl}/ajax/teacher/exceptions/${exceptionId}/${action}`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    '{{ csrf_token() }}';

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                });

                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);

                    // Update DOM card
                    const card = document.getElementById(`exception-card-${exceptionId}`);
                    if (card) {
                        card.dataset.status = action === 'approve' ? 'approved' : 'rejected';
                    }

                    const badgeContainer = document.getElementById(`status-badge-container-${exceptionId}`);
                    if (badgeContainer) {
                        if (action === 'approve') {
                            badgeContainer.innerHTML = `
                                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800 text-xs font-extrabold flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>${isArLocale ? 'مقبول (تم حفظ الحصة)' : 'Approved (Session Kept)'}</span>
                                </span>
                            `;
                        } else {
                            badgeContainer.innerHTML = `
                                <span class="px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 text-xs font-extrabold flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    <span>${isArLocale ? 'مرفوض (تم خصم الحصة)' : 'Rejected (Session Deducted)'}</span>
                                </span>
                            `;
                        }
                    }

                    // Update action buttons
                    const actionsContainer = document.getElementById(`exception-actions-${exceptionId}`);
                    if (actionsContainer) {
                        if (action === 'approve') {
                            actionsContainer.innerHTML = `
                                <button type="button" onclick="reviewTeacherException(${exceptionId}, 'reject')"
                                    class="btn-lift px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>${isArLocale ? 'تغيير إلى مرفوض (خصم الحصة)' : 'Reject (Deduct Session)'}</span>
                                </button>
                            `;
                        } else {
                            actionsContainer.innerHTML = `
                                <button type="button" onclick="reviewTeacherException(${exceptionId}, 'approve')"
                                    class="btn-lift px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer transition-all">
                                    <i class="fa-solid fa-check"></i>
                                    <span>${isArLocale ? 'تغيير إلى مقبول (حفظ الحصة)' : 'Approve (Keep Session)'}</span>
                                </button>
                            `;
                        }
                    }

                    // Update badge in top counter
                    const badge = document.getElementById('pendingExceptionsBadge');
                    if (badge) {
                        let cur = parseInt(badge.textContent.trim()) || 0;
                        if (cur > 1) {
                            badge.textContent = cur - 1;
                        } else {
                            badge.remove();
                        }
                    }
                } else {
                    showTeacherToast(data.message || 'Error processing request', false);
                }
            } catch (e) {
                showTeacherToast('Network error. Please try again.', false);
            }
        }
        window.reviewTeacherException = reviewTeacherException;

        // ── Sub-Tab Switcher for Student Profile Modal ────────────────────────────────
        function switchSpTab(tabKey) {
            document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.sp-subtab-btn').forEach(btn => {
                btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-700', 'hover:bg-slate-200/60');
            });

            const activePane = document.getElementById('sp-pane-' + tabKey);
            const activeBtn = document.getElementById('sp-tab-btn-' + tabKey);
            if (activePane) activePane.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-200/60');
                activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
            }
        }

        // ── Open Student Profile & Progressive Educational Data Fetch ─────────────────
        async function openStudentDetailsModal(studentUserId) {
            currentViewingStudentId = studentUserId;
            window.openModal('studentProfileModal');

            // Reset skeleton
            document.getElementById('spLoadingSkeleton').classList.remove('hidden');
            document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
            switchSpTab('overview');

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/students/${studentUserId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = {};
                try {
                    data = await res.json();
                } catch (e) {
                    data = {
                        success: false,
                        message: 'Server error'
                    };
                }

                if (!res.ok || !data.success) {
                    const errorMsg = data.message || (res.status === 403 ?
                        '{{ __('Unauthorized: You do not have permission to access this student educational profile.') }}' :
                        '{{ __('Failed to load student details.') }}');
                    showTeacherToast(errorMsg, false);
                    closeModal('studentProfileModal');
                    return;
                }

                document.getElementById('spLoadingSkeleton').classList.add('hidden');
                document.getElementById('sp-pane-overview').classList.remove('hidden');

                // 1. Populate Header
                const st = data.student;
                const metrics = data.metrics || {};
                document.getElementById('spModalAvatar').textContent = (st.name || 'S').substring(0, 1).toUpperCase();
                document.getElementById('spModalName').textContent = st.name || i18n.studentProfile;
                document.getElementById('spModalCode').textContent = '#' + (st.student_code || 'STU-' + studentUserId);
                document.getElementById('spModalMeta').textContent =
                    `${st.school || 'Elite Academy'} • ${st.grade || 'Secondary'} • <i class="fa-solid fa-envelope"></i> ${st.email || ''}`;

                // 2. Populate Overview KPIs
                document.getElementById('spOverviewAttRate').textContent = `${metrics.attendance_rate || 100}%`;
                document.getElementById('spOverviewAvgGrade').textContent = metrics.avg_score !== null ?
                    `${metrics.avg_score}%` : 'N/A';
                document.getElementById('spOverviewTotalSessions').textContent = metrics.total_sessions || 0;
                document.getElementById('spOverviewSubmissions').textContent = metrics.total_submissions || 0;

                // Populate Overview Mini Lists
                const recentSesContainer = document.getElementById('spOverviewRecentSessions');
                if (data.sessions && data.sessions.length > 0) {
                    let sHtml = '';
                    data.sessions.slice(0, 3).forEach(s => {
                        const attColor = s.attendance_status === 'present' ? 'text-emerald-600' : (s
                            .attendance_status === 'late' ? 'text-amber-600' : 'text-rose-600');
                        sHtml += `<div class="p-2.5 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">${s.title}</p>
                        <p class="text-[10px] text-slate-400">${s.date}</p>
                    </div>
                    <span class="font-bold uppercase text-[10px] ${attColor}">${s.attendance_status || 'scheduled'}</span>
                </div>`;
                    });
                    recentSesContainer.innerHTML = sHtml;
                } else {
                    recentSesContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noSessions}</p>`;
                }

                const recentSubContainer = document.getElementById('spOverviewRecentSubmissions');
                if (data.submissions && data.submissions.length > 0) {
                    let subHtml = '';
                    data.submissions.slice(0, 3).forEach(sub => {
                        const scoreText = sub.score !== null ?
                            `<span class="font-bold text-emerald-600">${sub.score}%</span>` :
                            `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                        subHtml += `<div class="p-2.5 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">${sub.assignment_title}</p>
                        <p class="text-[10px] text-slate-400">${sub.submitted_at || 'Draft'}</p>
                    </div>
                    <div>${scoreText}</div>
                </div>`;
                    });
                    recentSubContainer.innerHTML = subHtml;
                } else {
                    recentSubContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noAssignments}</p>`;
                }

                // 3. Populate Courses Tab
                const coursesList = document.getElementById('spCoursesList');
                if (data.courses && data.courses.length > 0) {
                    let cHtml = '';
                    data.courses.forEach(c => {
                        cHtml += `<div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="font-heading font-bold text-sm text-slate-900 dark:text-white">${c.title}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 text-teal-800">${c.status}</span>
                    </div>
                    <p class="text-xs font-mono text-slate-500">${c.subject} • ${c.grade} • Enrolled: ${c.enrolled_at}</p>
                    <div class="space-y-1 pt-1">
                        <div class="flex justify-between text-[11px] font-mono text-slate-600">
                            <span>Syllabus Progress</span>
                            <span class="font-bold">${c.completed_sessions} / ${c.sessions_count} sessions (${c.progress_pct}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-teal-600 h-full rounded-full" style="width: ${c.progress_pct}%"></div>
                        </div>
                    </div>
                </div>`;
                    });
                    coursesList.innerHTML = cHtml;
                } else {
                    coursesList.innerHTML =
                        `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noCourses}</p>`;
                }

                // 4. Populate Sessions Tab
                const sesTableBody = document.getElementById('spSessionsTableBody');
                if (data.sessions && data.sessions.length > 0) {
                    let sesHtml = '';
                    data.sessions.forEach(s => {
                        const attBadge = s.attendance_status === 'present' ?
                            '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> Present</span>' :
                            (s.attendance_status === 'late' ?
                                '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold"><i class="fa-solid fa-circle text-amber-500 text-[10px]"></i> Late</span>' :
                                '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> Absent</span>'
                            );
                        sesHtml += `<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-slate-100">${s.title}</td>
                    <td class="py-2.5 px-3 text-slate-600">${s.course_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${s.date}</td>
                    <td class="py-2.5 px-3 font-mono">${attBadge}</td>
                </tr>`;
                    });
                    sesTableBody.innerHTML = sesHtml;
                } else {
                    sesTableBody.innerHTML =
                        `<tr><td colspan="4" class="py-8 text-center text-slate-400">
                            <p class="italic text-xs mb-3">${i18n.noSessions}</p>
                            <button type="button" onclick="createSessionForCurrentStudent()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs cursor-pointer transition-all active:scale-95">
                                <i class="fa-solid fa-calendar-plus"></i> ${isArLocale ? 'جدولة حصة جديدة لهذا الطالب' : 'Schedule Session for this student'}
                            </button>
                        </td></tr>`;
                }

                // 5. Populate Attendance Tab
                const attTableBody = document.getElementById('spAttendanceTableBody');
                if (data.attendance && data.attendance.length > 0) {
                    let attHtml = '';
                    data.attendance.forEach(a => {
                        const statusStr = a.attendance_status || 'scheduled';
                        attHtml += `<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-slate-100">${a.title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                    <td class="py-2.5 px-3 font-mono text-right rtl:text-left">
                        <span class="font-bold uppercase text-[10px] px-2 py-0.5 rounded-full ${statusStr === 'present' ? 'bg-emerald-100 text-emerald-800' : (statusStr === 'late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')}">${statusStr}</span>
                    </td>
                </tr>`;
                    });
                    attTableBody.innerHTML = attHtml;
                } else {
                    attTableBody.innerHTML =
                        `<tr><td colspan="3" class="py-4 text-center text-slate-400 italic">${i18n.noAttendance}</td></tr>`;
                }

                // 6. Populate Assignments Tab
                const assignTableBody = document.getElementById('spAssignmentsTableBody');
                if (data.submissions && data.submissions.length > 0) {
                    let assHtml = '';
                    data.submissions.forEach(sub => {
                        const scoreDisplay = sub.score !== null ?
                            `<span class="font-bold text-emerald-600">${sub.score}%</span>` :
                            `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                        assHtml += `<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-slate-100">${sub.assignment_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${sub.submitted_at || 'Draft'}</td>
                    <td class="py-2.5 px-3 font-mono">${scoreDisplay}</td>
                    <td class="py-2.5 px-3 text-right rtl:text-left">
                        <button type="button" onclick="openGradeModal(${sub.id}, '${st.name ? st.name.replace(/'/g, "\\'") : ''}', '${sub.assignment_title.replace(/'/g, "\\'")}', '${sub.score}', '${sub.evaluation_notes ? sub.evaluation_notes.replace(/'/g, "\\'") : ''}')" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] rounded-lg border border-teal-200">
                            <i class="fa-solid fa-magnifying-glass"></i> Review
                        </button>
                    </td>
                </tr>`;
                    });
                    assignTableBody.innerHTML = assHtml;
                } else {
                    assignTableBody.innerHTML =
                        `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noAssignments}</td></tr>`;
                }

                // 7. Populate Assessments & Quizzes Tab
                const assessContainer = document.getElementById('spAssessmentsContainer');
                if (data.assessments && data.assessments.length > 0) {
                    let assessHtml = '';
                    data.assessments.forEach(ass => {
                        const passBadge = ass.is_passed ?
                            '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-check"></i> Passed</span>' :
                            (ass.score !== null ?
                                '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-xmark me-1"></i> Retake</span>' :
                                '<span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">Pending</span>'
                            );
                        assessHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">${ass.assignment_title}</p>
                        <p class="text-[10px] font-mono text-slate-500">${ass.course_title} • ${ass.submitted_at}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-extrabold text-sm ${ass.score >= 70 ? 'text-emerald-600' : 'text-slate-800'}">${ass.score !== null ? ass.score + '%' : 'N/A'}</span>
                        ${passBadge}
                    </div>
                </div>`;
                    });
                    assessContainer.innerHTML = assessHtml;
                } else {
                    assessContainer.innerHTML =
                        `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noAssignments}</p>`;
                }

                // 8. Populate Progress & Analytics Tab
                const progContainer = document.getElementById('spProgressContainer');
                progContainer.innerHTML = `
            <div class="p-5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-heading font-black text-sm text-slate-900 dark:text-white">Comprehensive Academic Health</h4>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-mono">
                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700">
                        <span class="text-slate-400 block text-[10px] uppercase">Attendance Consistency</span>
                        <span class="font-extrabold text-base text-emerald-600">${metrics.attendance_rate || 100}%</span>
                    </div>
                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700">
                        <span class="text-slate-400 block text-[10px] uppercase">Assessment Pass Rate</span>
                        <span class="font-extrabold text-base text-teal-600">${metrics.pass_rate || 100}%</span>
                    </div>
                </div>
            </div>
        `;

                // 9. Populate Notes Tab
                renderEducationalNotes(data.notes || []);

            } catch (err) {
                document.getElementById('spLoadingSkeleton').innerHTML =
                    `<p class="text-xs text-rose-600 italic py-6">Failed to load student details.</p>`;
            }
        }

        function renderEducationalNotes(notes) {
            const container = document.getElementById('spNotesContainer');
            if (!container) return;

            if (notes && notes.length > 0) {
                let nHtml = '';
                notes.forEach(n => {
                    const catBadge = {
                            academic: '<span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded-full text-[10px] font-bold">Academic</span>',
                            homework: '<span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold">Homework</span>',
                            participation: '<span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full text-[10px] font-bold">Participation</span>',
                            behavior: '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">Behavior</span>',
                            general: '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">General</span>',
                        } [n.category] ||
                        '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">Note</span>';

                    nHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    ${catBadge}
                    <span class="text-[10px] font-mono text-slate-400">${n.created_at_human || n.created_at}</span>
                </div>
                <p class="text-slate-800 font-medium leading-relaxed">${n.note}</p>
            </div>`;
                });
                container.innerHTML = nHtml;
            } else {
                container.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noNotes}</p>`;
            }
        }

        // ── Open Add Note Modal for Current Student ──────────────────────────────────
        function openAddNoteModal() {
            if (!currentViewingStudentId) return;
            document.getElementById('noteStudentUserId').value = currentViewingStudentId;
            document.getElementById('addNoteForm').reset();
            window.openModal('addNoteModal');
        }

        // ── Quick Schedule Session For Current Student from Details Modal ────────────
        function createSessionForCurrentStudent(studentUserId) {
            const targetUserId = studentUserId || currentViewingStudentId;
            if (!targetUserId) {
                if (typeof showTeacherToast === 'function') {
                    showTeacherToast(isArLocale ? 'يرجى تحديد الطالب أولاً' : 'No student selected', false);
                }
                return;
            }

            // Close Student Profile modal
            if (typeof closeModal === 'function') {
                closeModal('studentProfileModal');
            }

            // Open Create Session modal pre-configured for this student
            openCreateSessionModal(targetUserId);
        }
        window.createSessionForCurrentStudent = createSessionForCurrentStudent;

        // ════════════════════════════════════════════════════════════════════════
        // UNIVERSAL RESPONSIVE SECTION PAGINATOR ENGINE (SEE MORE & SCROLL LOADING)
        // ════════════════════════════════════════════════════════════════════════
        class EliteSectionPaginator {
            constructor(options) {
                this.container = typeof options.containerId === 'string' ? document.getElementById(options
                    .containerId) : options.containerId;
                this.itemSelector = options.itemSelector;
                this.paginationContainer = typeof options.paginationId === 'string' ? document.getElementById(options
                    .paginationId) : options.paginationId;
                this.pageSize = options.pageSize || 9;
                this.visibleCount = this.pageSize;
                this.scrollToTop = options.scrollToTop || false;
                this.onPageChange = options.onPageChange || null;
                this.filterFn = options.filterFn || ((el) => el.getAttribute('data-filtered-out') !== '1');
                this.isLoadingMore = false;
                this.observer = null;

                this.initScrollObserver();
                this.update();
            }

            getMatchingItems() {
                if (!this.container) return [];
                const allItems = Array.from(this.container.querySelectorAll(this.itemSelector));
                return allItems.filter(el => this.filterFn(el));
            }

            initScrollObserver() {
                if (!('IntersectionObserver' in window)) return;

                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.isLoadingMore) {
                            const matching = this.getMatchingItems();
                            if (this.visibleCount < matching.length) {
                                this.loadMore();
                            }
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '200px',
                    threshold: 0.05
                });
            }

            loadMore() {
                const matching = this.getMatchingItems();
                if (this.visibleCount >= matching.length || this.isLoadingMore) return;

                this.isLoadingMore = true;

                const btn = this.paginationContainer?.querySelector('.elite-see-more-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-80', 'cursor-wait');
                    const spinner = btn.querySelector('.elite-spinner');
                    const icon = btn.querySelector('.elite-btn-icon');
                    if (spinner) spinner.classList.remove('hidden');
                    if (icon) icon.classList.add('hidden');
                }

                setTimeout(() => {
                    this.visibleCount += this.pageSize;
                    this.isLoadingMore = false;
                    this.update();

                    if (typeof this.onPageChange === 'function') {
                        this.onPageChange(this.visibleCount, matching.length);
                    }
                }, 150);
            }

            goToPage(page) {
                const matching = this.getMatchingItems();
                const totalPages = Math.max(1, Math.ceil(matching.length / this.pageSize));
                const targetPage = Math.max(1, Math.min(page, totalPages));
                this.visibleCount = targetPage * this.pageSize;
                this.update();
            }

            reset() {
                this.visibleCount = this.pageSize;
                this.isLoadingMore = false;
                this.update();
            }

            update() {
                if (!this.container) return;
                const allItems = Array.from(this.container.querySelectorAll(this.itemSelector));
                const matching = [];

                allItems.forEach(el => {
                    if (this.filterFn(el)) {
                        matching.push(el);
                    } else {
                        el.classList.add('hidden');
                    }
                });

                const totalItems = matching.length;
                if (this.visibleCount < this.pageSize) {
                    this.visibleCount = this.pageSize;
                }

                matching.forEach((el, idx) => {
                    if (idx < this.visibleCount) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });

                const shownCount = Math.min(this.visibleCount, totalItems);
                this.renderControls(totalItems, shownCount);
            }

            renderControls(totalItems, shownCount) {
                if (!this.paginationContainer) return;

                if (totalItems === 0) {
                    this.paginationContainer.innerHTML = '';
                    this.paginationContainer.classList.add('hidden');
                    if (this.observer) this.observer.disconnect();
                    return;
                }

                this.paginationContainer.classList.remove('hidden');

                const isAr = document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.lang ===
                    'ar' || (typeof isArLocale !== 'undefined' && isArLocale);
                const hasMore = shownCount < totalItems;
                const remaining = totalItems - shownCount;
                const percent = Math.min(100, Math.round((shownCount / totalItems) * 100));

                const statusText = isAr ?
                    `عرض <span class="font-black text-teal-600 dark:text-teal-400">${shownCount}</span> من أصل <span class="font-black text-slate-900 dark:text-white">${totalItems}</span> عنصر` :
                    `Showing <span class="font-black text-teal-600 dark:text-teal-400">${shownCount}</span> of <span class="font-black text-slate-900 dark:text-white">${totalItems}</span> items`;

                const loadMoreLabel = isAr ? `عرض المزيد...` : `See More...`;
                const remainingText = isAr ? `(${remaining} متبقي)` : `(${remaining} remaining)`;
                const allLoadedText = isAr ? `تم عرض جميع العناصر (${totalItems})` : `All items loaded (${totalItems})`;

                let actionHtml = '';

                if (hasMore) {
                    actionHtml = `
                        <button type="button" class="elite-see-more-btn group relative inline-flex items-center justify-center gap-2.5 px-6 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-300 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white shadow-md hover:shadow-lg hover:shadow-teal-500/25 active:scale-95 cursor-pointer">
                            <i class="elite-spinner hidden fa-solid fa-circle-notch fa-spin text-sm"></i>
                            <i class="elite-btn-icon fa-solid fa-angles-down group-hover:translate-y-0.5 transition-transform text-xs"></i>
                            <span>${loadMoreLabel}</span>
                            <span class="opacity-80 text-[11px] font-mono font-normal">${remainingText}</span>
                        </button>
                    `;
                } else if (totalItems > this.pageSize) {
                    actionHtml = `
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>${allLoadedText}</span>
                        </div>
                    `;
                }

                this.paginationContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center gap-3 pt-5 border-t border-slate-200/80 dark:border-slate-800/80 w-full max-w-full overflow-hidden">
                        <div class="flex items-center justify-between w-full max-w-md px-1 text-xs font-mono text-slate-500 dark:text-slate-400">
                            <span>${statusText}</span>
                            <span class="font-bold text-teal-600 dark:text-teal-400">${percent}%</span>
                        </div>
                        <div class="w-full max-w-md bg-slate-100 dark:bg-slate-800/80 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-full transition-all duration-500 rounded-full" style="width: ${percent}%"></div>
                        </div>
                        ${actionHtml ? `<div class="mt-1 flex justify-center w-full">${actionHtml}</div>` : ''}
                    </div>
                `;

                const seeMoreBtn = this.paginationContainer.querySelector('.elite-see-more-btn');
                if (seeMoreBtn) {
                    seeMoreBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.loadMore();
                    });
                }

                if (this.observer) {
                    this.observer.disconnect();
                    if (hasMore) {
                        this.observer.observe(this.paginationContainer);
                    }
                }
            }
        }
        window.EliteSectionPaginator = EliteSectionPaginator;

        // ── Client-Side Real-Time Filter for My Students Roster ───────────────────────
        function applyStudentFilters() {
            const searchVal = (document.getElementById('studentSearchInput')?.value || '').trim().toLowerCase();
            const courseVal = document.getElementById('studentCourseFilter')?.value || '';
            const gradeVal = document.getElementById('studentGradeFilter')?.value || '';
            const attVal = document.getElementById('studentAttendanceFilter')?.value || '';

            const cards = document.querySelectorAll('.student-roster-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const code = card.getAttribute('data-code') || '';
                const school = card.getAttribute('data-school') || '';
                const courses = (card.getAttribute('data-courses') || '').split(',');
                const grade = card.getAttribute('data-grade') || '';
                const attendance = parseInt(card.getAttribute('data-attendance') || '100', 10);

                let matchSearch = !searchVal || name.includes(searchVal) || code.includes(searchVal) || school
                    .includes(searchVal);
                let matchCourse = !courseVal || courses.includes(courseVal);
                let matchGrade = !gradeVal || grade === gradeVal;
                let matchAtt = !attVal || (attVal === 'good' && attendance >= 80) || (attVal === 'risk' &&
                    attendance < 80);

                if (matchSearch && matchCourse && matchGrade && matchAtt) {
                    card.setAttribute('data-filtered-out', '0');
                    visibleCount++;
                } else {
                    card.setAttribute('data-filtered-out', '1');
                }
            });

            if (window.studentPaginator) {
                window.studentPaginator.reset();
            }

            const emptyState = document.getElementById('studentEmptySearchState');
            if (emptyState) {
                if (visibleCount === 0 && cards.length > 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }

            const countText = document.getElementById('studentFilterCountText');
            if (countText) {
                countText.textContent = isArLocale ?
                    `عرض ${visibleCount} من أصل ${cards.length} طالباً` :
                    `Showing ${visibleCount} of ${cards.length} students`;
            }
        }

        function resetStudentFilters() {
            if (document.getElementById('studentSearchInput')) document.getElementById('studentSearchInput').value = '';
            ['studentCourseFilter', 'studentGradeFilter', 'studentAttendanceFilter'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = '';
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
            applyStudentFilters();
        }

        // ── Open Modals & Action Helpers ─────────────────────────────────────────────
        const allTeacherCourses = [
            @foreach ($courses as $c)
                { id: {{ $c->id }}, title: @json($c->title), subject_name: @json($c->subject?->name ?: '') },
            @endforeach
        ];

        async function onSessionStudentChange(selectedValue) {
            const hiddenInput = document.getElementById('createSessionStudentId');
            const courseSelect = document.getElementById('createSessionCourseId');
            const helpText = document.getElementById('createSessionCourseHelp');

            if (!courseSelect) return;

            courseSelect.innerHTML = '';
            courseSelect.value = '';
            if (helpText) {
                helpText.classList.add('hidden');
                helpText.textContent = '';
            }

            if (!selectedValue) {
                if (hiddenInput) hiddenInput.value = '';
                courseSelect.disabled = true;
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = isArLocale ? 'يرجى اختيار الطالب أولاً...' : 'Select Student First...';
                courseSelect.appendChild(opt);
                return;
            }

            if (selectedValue === '__group__') {
                if (hiddenInput) hiddenInput.value = '';
                courseSelect.disabled = false;
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = isArLocale ? 'اختر الكورس / المادة...' : 'Select Course...';
                courseSelect.appendChild(defaultOpt);

                allTeacherCourses.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.title} (${c.subject_name})`;
                    courseSelect.appendChild(opt);
                });
                return;
            }

            // Specific student selected (1-to-1 session)
            const studentUserId = parseInt(selectedValue, 10);
            if (hiddenInput) hiddenInput.value = studentUserId;

            courseSelect.disabled = true;
            const loadingOpt = document.createElement('option');
            loadingOpt.value = '';
            loadingOpt.textContent = isArLocale ? 'جاري تحميل كورسات الطالب...' : 'Loading student courses...';
            courseSelect.appendChild(loadingOpt);

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/students/${studentUserId}/courses`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                courseSelect.innerHTML = '';

                if (!res.ok || !data.success || !data.courses || data.courses.length === 0) {
                    courseSelect.disabled = true;
                    const noOpt = document.createElement('option');
                    noOpt.value = '';
                    noOpt.textContent = isArLocale ? 'لا توجد كورسات متاحة لهذا الطالب' : 'No courses available for this student';
                    courseSelect.appendChild(noOpt);

                    if (helpText) {
                        helpText.textContent = isArLocale 
                            ? 'هذا الطالب غير مسجل في أي من كورساتك النشطة.'
                            : 'This student is not enrolled in any of your active courses.';
                        helpText.classList.remove('hidden');
                    }
                    return;
                }

                courseSelect.disabled = false;
                const chooseOpt = document.createElement('option');
                chooseOpt.value = '';
                chooseOpt.textContent = isArLocale ? 'اختر كورس الطالب...' : 'Select Course...';
                courseSelect.appendChild(chooseOpt);

                data.courses.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.title} (${c.subject_name})`;
                    courseSelect.appendChild(opt);
                });
            } catch (e) {
                courseSelect.innerHTML = '';
                courseSelect.disabled = true;
                const errOpt = document.createElement('option');
                errOpt.value = '';
                errOpt.textContent = isArLocale ? 'حدث خطأ في تحميل الكورسات' : 'Error loading courses';
                courseSelect.appendChild(errOpt);
            }
        }

        function openCreateSessionModal(preSelectedStudentUserId = null) {
            const form = document.getElementById('createSessionForm');
            if (form) form.reset();
            const studentSelect = document.getElementById('createSessionStudentSelect');
            const studentHidden = document.getElementById('createSessionStudentId');
            const courseSelect = document.getElementById('createSessionCourseId');
            const helpText = document.getElementById('createSessionCourseHelp');

            if (courseSelect) {
                courseSelect.innerHTML = `<option value="">${isArLocale ? 'يرجى اختيار الطالب أولاً...' : 'Select Student First...'}</option>`;
                courseSelect.disabled = true;
            }
            if (helpText) {
                helpText.classList.add('hidden');
                helpText.textContent = '';
            }

            if (preSelectedStudentUserId) {
                const targetId = parseInt(preSelectedStudentUserId, 10);
                if (studentSelect) {
                    let optExists = Array.from(studentSelect.options).some(o => parseInt(o.value, 10) === targetId);
                    if (!optExists) {
                        const studentNameEl = document.getElementById('spModalName');
                        const studentName = studentNameEl ? studentNameEl.textContent.trim() : `Student #${targetId}`;
                        const newOpt = document.createElement('option');
                        newOpt.value = targetId;
                        newOpt.textContent = studentName;
                        studentSelect.appendChild(newOpt);
                    }
                    studentSelect.value = targetId;
                }
                if (studentHidden) {
                    studentHidden.value = targetId;
                }
                window.openModal('createSessionModal');
                onSessionStudentChange(targetId);
            } else {
                if (studentSelect) studentSelect.value = '';
                if (studentHidden) studentHidden.value = '';
                window.openModal('createSessionModal');
            }
        }

        function openCreateAssignmentModal() {
            window.openModal('createAssignmentModal');
        }

        async function openAssignmentDetailsModal(assignmentId) {
            window.openModal('assignmentDetailsModal');

            document.getElementById('adLoadingSkeleton').classList.remove('hidden');
            document.getElementById('adModalContent').classList.add('hidden');
            switchAdSubTab('questions');

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/assignments/${assignmentId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = {};
                try {
                    data = await res.json();
                } catch (e) {
                    data = {
                        success: false
                    };
                }

                if (!res.ok || !data.success) {
                    showTeacherToast(data.message || '{{ __('Failed to load assignment details.') }}', false);
                    closeModal('assignmentDetailsModal');
                    return;
                }

                const a = data.assignment;
                window.currentViewingAssignment = a;
                const stats = data.stats || {};

                document.getElementById('adModalCourse').textContent = a.course_title;
                document.getElementById('adModalSubject').textContent = a.subject_name || '';
                document.getElementById('adModalStatus').textContent = (a.status || 'published').toUpperCase();
                document.getElementById('adModalTitle').textContent = a.title;
                document.getElementById('adModalDescription').textContent = a.description ||
                    '{{ __('No instructions provided.') }}';

                document.getElementById('adDueAt').textContent = a.due_at || '{{ __('No deadline') }}';
                document.getElementById('adDueHuman').textContent = a.due_at_human || '';
                document.getElementById('adPassScore').textContent = a.passing_score + '%';
                document.getElementById('adQuestionsCount').textContent = a.total_questions || 0;
                document.getElementById('adDuration').textContent = a.duration_minutes ? a.duration_minutes +
                    ' {{ __('min') }}' : '';
                document.getElementById('adSubmissionsCount').textContent = stats.total_submissions || 0;
                document.getElementById('adAvgScore').textContent = stats.avg_score !== null ?
                    '{{ __('Avg') }}: ' + stats.avg_score + '%' : '';

                document.getElementById('adQuestionsBadge').textContent = (data.questions || []).length;
                document.getElementById('adSubmissionsBadge').textContent = (data.submissions || []).length;

                // Render Questions
                const qContainer = document.getElementById('adQuestionsList');
                const noQNotice = document.getElementById('adNoQuestionsNotice');
                if (data.questions && data.questions.length > 0) {
                    noQNotice.classList.add('hidden');
                    let qHtml = '';
                    data.questions.forEach((q, idx) => {
                        let optsHtml = '';
                        (q.options || []).forEach(opt => {
                            const isCorrect = opt.is_correct;
                            const optImgHtml = opt.image_url ?
                                `<img src="${opt.image_url}" class="h-9 w-auto max-w-[120px] rounded-lg border border-slate-200 dark:border-slate-700 object-contain shadow-2xs ms-2 shrink-0 pointer-events-none" alt="Option Image">` : '';

                            optsHtml += `
                        <div class="p-2.5 rounded-xl border ${isCorrect ? 'bg-emerald-50/90 dark:bg-emerald-950/50 border-emerald-300 dark:border-emerald-800 text-emerald-950 dark:text-emerald-200 font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200'} flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <span class="w-5 h-5 rounded-lg ${isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'} flex items-center justify-center shrink-0 text-[10px]">
                                    ${isCorrect ? '<i class="fa-solid fa-check"></i>' : '•'}
                                </span>
                                <span class="break-words">${escapeHtml(opt.option_text)}</span>
                                ${optImgHtml}
                            </div>
                            ${isCorrect ? '<span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded-md bg-emerald-200 dark:bg-emerald-900/70 text-emerald-900 dark:text-emerald-200 shrink-0 font-extrabold"><i class="fa-solid fa-circle-check me-1"></i>{{ __('Correct Answer') }}</span>' : ''}
                        </div>
                    `;
                        });

                        const qImgHtml = q.image_url ?
                            `<div class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 max-w-md my-2">
                                <img src="${q.image_url}" class="max-h-48 rounded-lg object-contain mx-auto" alt="Question Diagram">
                            </div>` : '';

                        qHtml += `
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-heading font-black text-xs text-teal-800 dark:text-teal-300 bg-teal-100/70 dark:bg-teal-950/70 border border-teal-200 dark:border-teal-800 px-2.5 py-1 rounded-lg">#${idx + 1}</span>
                            <span class="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400">${q.points} {{ __('pts') }}</span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">${escapeHtml(q.question_text)}</p>
                        ${qImgHtml}
                        <div class="space-y-1.5 pt-1">
                            ${optsHtml}
                        </div>
                    </div>
                `;
                    });
                    qContainer.innerHTML = qHtml;
                } else {
                    qContainer.innerHTML = '';
                    noQNotice.classList.remove('hidden');
                }

                // Render Submissions
                const subTBody = document.getElementById('adSubmissionsTableBody');
                const noSubNotice = document.getElementById('adNoSubmissionsNotice');
                const subTableContainer = document.getElementById('adSubmissionsTableContainer');

                if (data.submissions && data.submissions.length > 0) {
                    noSubNotice.classList.add('hidden');
                    subTableContainer.classList.remove('hidden');
                    let subHtml = '';

                    data.submissions.forEach(s => {
                        const isPassed = s.is_passed;
                        const scoreText = s.score !== null ? `${s.score}%` : '{{ __('Pending Grade') }}';
                        const scoreClass = s.score !== null ? (isPassed ?
                            'text-emerald-600 dark:text-emerald-400 font-extrabold' :
                            'text-rose-600 dark:text-rose-400 font-extrabold') : 'text-slate-400 italic';
                        const statusBadgeClass = s.status === 'reviewed' ?
                            'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' :
                            'bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800';

                        subHtml += `
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                        <td class="py-3 px-3">
                            <p class="font-bold text-slate-900 dark:text-white">${escapeHtml(s.student_name)}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">${escapeHtml(s.student_email)}</p>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600 dark:text-slate-300">${s.submitted_at || 'Draft'}</td>
                        <td class="py-3 px-3 font-mono ${scoreClass}">${scoreText}</td>
                        <td class="py-3 px-3 font-mono">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusBadgeClass}">
                                ${escapeHtml(s.status)}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right rtl:text-left">
                            <button type="button" onclick="closeModal('assignmentDetailsModal'); openGradeModal(${s.id}, '${escapeJs(s.student_name)}', '${escapeJs(a.title)}', '${s.score !== null ? s.score : ''}', '${escapeJs(s.evaluation_notes || '')}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                <i class="fa-solid fa-magnifying-glass"></i> {{ __('Review & Grade') }}
                            </button>
                        </td>
                    </tr>
                `;
                    });
                    subTBody.innerHTML = subHtml;
                } else {
                    subTBody.innerHTML = '';
                    subTableContainer.classList.add('hidden');
                    noSubNotice.classList.remove('hidden');
                }

                document.getElementById('adLoadingSkeleton').classList.add('hidden');
                document.getElementById('adModalContent').classList.remove('hidden');

            } catch (err) {
                showTeacherToast('{{ __('Failed to load assignment details.') }}', false);
                closeModal('assignmentDetailsModal');
            }
        }

        function switchAdSubTab(tab) {
            document.querySelectorAll('.ad-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.ad-subtab-btn').forEach(btn => {
                btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
                btn.classList.add('text-slate-700', 'dark:text-slate-200');
            });

            const pane = document.getElementById('ad-pane-' + tab);
            const btn = document.getElementById('ad-tab-btn-' + tab);
            if (pane) pane.classList.remove('hidden');
            if (btn) {
                btn.classList.remove('text-slate-700', 'dark:text-slate-200');
                btn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
            }
        }

        function openMeetingLinkModal(sessionId, currentLinkOrEl) {
            let currentLink = '';
            if (currentLinkOrEl && typeof currentLinkOrEl === 'object' && currentLinkOrEl.nodeType) {
                currentLink = currentLinkOrEl.getAttribute('data-meeting-link') || '';
            } else if (typeof currentLinkOrEl === 'string') {
                currentLink = currentLinkOrEl;
            }
            document.getElementById('linkSessionId').value = sessionId;
            document.getElementById('meetingUrlInput').value = currentLink || '';
            document.getElementById('meetingLinkForm').action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/link`;
            window.openModal('meetingLinkModal');
        }

        function openRescheduleModal(sessionId, currentDateTimeOrEl) {
            let currentDateTime = '';
            if (currentDateTimeOrEl && typeof currentDateTimeOrEl === 'object' && currentDateTimeOrEl.nodeType) {
                currentDateTime = currentDateTimeOrEl.getAttribute('data-scheduled-at') || '';
            } else if (typeof currentDateTimeOrEl === 'string') {
                currentDateTime = currentDateTimeOrEl;
            }
            document.getElementById('rescheduleSessionId').value = sessionId;
            document.getElementById('rescheduleDateTime').value = currentDateTime || '';
            document.getElementById('rescheduleForm').action =
                `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/reschedule`;
            window.openModal('rescheduleModal');
        }

        async function openAttendanceModal(sessionId, sessionTitleOrEl) {
            if (!sessionId) return;

            let sessionTitle = '';
            if (sessionTitleOrEl && typeof sessionTitleOrEl === 'object' && sessionTitleOrEl.nodeType) {
                sessionTitle = sessionTitleOrEl.getAttribute('data-session-title') || sessionTitleOrEl.getAttribute(
                    'data-title') || '';
            } else if (typeof sessionTitleOrEl === 'string') {
                sessionTitle = sessionTitleOrEl;
            }

            if (!sessionTitle) {
                const triggerBtn = document.querySelector(`button[data-session-id="${sessionId}"]`);
                if (triggerBtn) {
                    sessionTitle = triggerBtn.getAttribute('data-session-title') || triggerBtn.getAttribute(
                        'data-title') || '';
                }
            }

            const sessionInput = document.getElementById('attendanceSessionId');
            if (sessionInput) sessionInput.value = sessionId;

            const titleEl = document.getElementById('attendanceSessionTitle');
            if (titleEl) titleEl.textContent = sessionTitle || (isArLocale ? 'جاري التحميل...' : 'Loading...');

            const formEl = document.getElementById('attendanceForm');
            if (formEl) formEl.action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance`;

            const searchInput = document.getElementById('attModalSearchInput');
            if (searchInput) searchInput.value = '';

            const container = document.getElementById('attendanceStudentsContainer');
            if (container) {
                container.innerHTML = `
            <div class="py-8 space-y-3">
                <div class="flex items-center gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl animate-pulse">
                    <div class="w-11 h-11 rounded-xl bg-slate-200 shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 bg-slate-200 rounded-md w-1/3"></div>
                        <div class="h-2.5 bg-slate-200 rounded-md w-1/4"></div>
                    </div>
                    <div class="w-48 h-8 bg-slate-200 rounded-xl shrink-0"></div>
                </div>
                <div class="flex items-center gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl animate-pulse">
                    <div class="w-11 h-11 rounded-xl bg-slate-200 shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 bg-slate-200 rounded-md w-1/2"></div>
                        <div class="h-2.5 bg-slate-200 rounded-md w-1/5"></div>
                    </div>
                    <div class="w-48 h-8 bg-slate-200 rounded-xl shrink-0"></div>
                </div>
            </div>
        `;
            }

            const countBadge = document.getElementById('attendanceCohortCount');
            if (countBadge) countBadge.textContent = '...';

            window.openModal('attendanceModal');

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance-roster`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let data = {};
                try {
                    data = await res.json();
                } catch (e) {
                    data = {
                        success: false,
                        message: 'Server error'
                    };
                }

                if (!res.ok || !data.success) {
                    if (container) {
                        container.innerHTML = `
                    <div class="py-10 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl shadow-xs border border-amber-200">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-slate-800">${escapeHtml(data.message || (isArLocale ? 'تعذر جلب كشف الحضور للجلسة.' : 'Could not load session attendance roster.'))}</p>
                            <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'يرجى التحقق من الصلاحيات والمحاولة مجدداً.' : 'Please verify permissions and retry.'}</p>
                        </div>
                        <button type="button" onclick="openAttendanceModal(${sessionId}, '${escapeJs(sessionTitle)}')" class="btn-lift px-3.5 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                            <i class="fa-solid fa-rotate-right"></i> ${isArLocale ? 'إعادة المحاولة' : 'Retry'}
                        </button>
                    </div>
                `;
                    }
                    return;
                }

                if (data.session && titleEl) {
                    titleEl.textContent =
                        `${data.session.title} ${data.session.course_title ? '— ' + data.session.course_title : ''}`;
                }

                const students = data.students || [];
                if (countBadge) countBadge.textContent = students.length;

                if (students.length === 0) {
                    if (container) {
                        container.innerHTML = `
                    <div class="py-12 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-2xl shadow-2xs border border-teal-100">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-800">${isArLocale ? 'لا يوجد طلاب مسجلين في هذا الكورس حالياً.' : 'No students enrolled in this course yet.'}</p>
                            <p class="text-xs font-mono text-slate-400">${isArLocale ? 'سيظهر الطلاب المسجلون تلقائياً بمجرد انضمامهم إلى الكورس.' : 'Enrolled students will appear here automatically.'}</p>
                        </div>
                    </div>
                `;
                    }
                    return;
                }

                let html = '';
                students.forEach((st, idx) => {
                    const curStatus = st.status || 'present';
                    const isPresent = curStatus === 'present';
                    const isLate = curStatus === 'late';
                    const isExcused = curStatus === 'excused';
                    const isAbsent = curStatus === 'absent';

                    html += `
                <div class="att-student-item p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-slate-800/90 hover:bg-slate-50/80 dark:hover:bg-slate-700/60 transition-all border border-slate-200/80 dark:border-slate-700 hover:border-teal-300 dark:hover:border-teal-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs"
                     data-student-name="${escapeHtml(st.name || '')}"
                     data-student-code="${escapeHtml(st.code || st.student_code || '')}"
                     data-student-school="${escapeHtml(st.school || '')}">
                    
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white font-heading font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                            ${(st.name || 'S').substring(0, 1).toUpperCase()}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">${escapeHtml(st.name)}</p>
                            <p class="text-[11px] font-mono text-slate-500 truncate flex items-center gap-1.5 flex-wrap">
                                <span>${escapeHtml(st.school || 'Elite Academy')}</span>
                                ${st.grade ? `<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span><span>${escapeHtml(st.grade)}</span>` : ''}
                                ${(st.code || st.student_code) ? `<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span><span class="text-slate-400">#${escapeHtml(st.code || st.student_code)}</span>` : ''}
                            </p>
                        </div>
                    </div>

                    <input type="hidden" name="attendance[${idx}][student_user_id]" value="${st.id}">
                    
                    <div class="w-full sm:w-auto shrink-0 flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/90">
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="present" ${isPresent ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'present', this)" class="peer sr-only att-status-radio att-radio-present">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-emerald-800 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>${isArLocale ? 'حاضر' : 'Present'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="late" ${isLate ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'late', this)" class="peer sr-only att-status-radio att-radio-late">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-amber-800 peer-checked:bg-amber-500 peer-checked:text-slate-950 peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span>${isArLocale ? 'متأخر' : 'Late'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="excused" ${isExcused ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'excused', this)" class="peer sr-only att-status-radio att-radio-excused">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-slate-800 peer-checked:bg-slate-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                                <span>${isArLocale ? 'معذور' : 'Excused'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="absent" ${isAbsent ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'absent', this)" class="peer sr-only att-status-radio att-radio-absent">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-rose-800 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                <span>${isArLocale ? 'غائب' : 'Absent'}</span>
                            </span>
                        </label>
                    </div>
                </div>
            `;
                });

                if (container) container.innerHTML = html;

            } catch (err) {
                if (container) {
                    container.innerHTML = `
                <div class="py-10 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-xl shadow-xs border border-rose-100">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-rose-700">${isArLocale ? 'تعذر تحميل كشف حضور الطلاب في الوقت الفعلي.' : 'Failed to load real-time attendance roster.'}</p>
                        <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'يرجى التحقق من الاتصال بالإنترنت والمحاولة مجدداً.' : 'Please check your connection and try again.'}</p>
                    </div>
                    <button type="button" onclick="openAttendanceModal(${sessionId}, '${escapeJs(sessionTitle)}')" class="btn-lift px-3.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-rotate-right"></i> ${isArLocale ? 'إعادة المحاولة' : 'Retry'}
                    </button>
                </div>
            `;
                }
            }
        }

        function onAttendanceStatusRadioChange(studentName, newStatus, radioEl) {
            const statusMap = {
                present: {
                    title: isArLocale ? 'تسجيل حضور' : 'Attendance Check',
                    msg: isArLocale ? `تم تحديد (${studentName}) كـ حاضر` : `Marked (${studentName}) as Present`,
                    type: 'success'
                },
                late: {
                    title: isArLocale ? 'تسجيل تأخير' : 'Attendance Check',
                    msg: isArLocale ? `تم تحديد (${studentName}) كـ متأخر` : `Marked (${studentName}) as Late`,
                    type: 'warning'
                },
                excused: {
                    title: isArLocale ? 'تسجيل عذر' : 'Attendance Check',
                    msg: isArLocale ? `تم تحديد (${studentName}) كـ معذور` : `Marked (${studentName}) as Excused`,
                    type: 'info'
                },
                absent: {
                    title: isArLocale ? 'تسجيل غياب' : 'Attendance Check',
                    msg: isArLocale ? `تم تحديد (${studentName}) كـ غائب` : `Marked (${studentName}) as Absent`,
                    type: 'danger'
                },
            };

            const cfg = statusMap[newStatus] || {
                title: 'Attendance',
                msg: 'Status updated',
                type: 'info'
            };
            if (window.Toast) {
                window.Toast.show({
                    type: cfg.type,
                    title: cfg.title,
                    message: cfg.msg,
                    duration: 2000
                });
            }
        }

        function filterAttendanceModalStudents(query) {
            const q = (query || '').trim().toLowerCase();
            const studentRows = document.querySelectorAll('.att-student-item');
            studentRows.forEach(row => {
                const name = (row.getAttribute('data-student-name') || '').toLowerCase();
                const code = (row.getAttribute('data-student-code') || '').toLowerCase();
                const school = (row.getAttribute('data-student-school') || '').toLowerCase();
                if (!q || name.includes(q) || code.includes(q) || school.includes(q)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        function bulkSetAttendance(status) {
            const targetRadios = document.querySelectorAll(`.att-radio-${status}`);
            targetRadios.forEach(radio => {
                radio.checked = true;
            });

            if (window.Toast) {
                if (status === 'present') {
                    window.Toast.success(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ حضور' :
                        'All students marked as Present', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                } else if (status === 'late') {
                    window.Toast.warning(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ متأخرين' :
                        'All students marked as Late', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                } else if (status === 'absent') {
                    window.Toast.danger(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ غياب' : 'All students marked as Absent',
                        isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                }
            }
        }

        window.openAttendanceModal = openAttendanceModal;
        window.filterAttendanceModalStudents = filterAttendanceModalStudents;
        window.bulkSetAttendance = bulkSetAttendance;
        window.onAttendanceStatusRadioChange = onAttendanceStatusRadioChange;
        window.openCreateSessionModal = openCreateSessionModal;
        window.createSessionForCurrentStudent = createSessionForCurrentStudent;
        window.onSessionStudentChange = onSessionStudentChange;
        window.openCreateAssignmentModal = function() {
            window.openModal('createAssignmentModal');
        };
        window.openAssignmentDetailsModal = openAssignmentDetailsModal;
        window.openMeetingLinkModal = openMeetingLinkModal;
        window.openRescheduleModal = openRescheduleModal;
        window.openGradeModal = openGradeModal;
        window.confirmCancelSession = function(sessionId) {
            const idEl = document.getElementById('cancelSessionId');
            if (idEl) idEl.value = sessionId;
            const rEl = document.getElementById('cancelReasonInput');
            if (rEl) rEl.value = '';
            window.openModal('cancelSessionModal');
        };
        window.openStudentDetailsModal = openStudentDetailsModal;
        window.switchTeacherTab = switchTeacherTab;

        // ── Grade Modal Implementation ───────────────────────────────────────────────
        async function openGradeModal(submissionId, studentName, assignmentTitle, currentScore, evaluationNotes) {
            const isAr = @json(app()->getLocale() === 'ar');
            const nameEl = document.getElementById('gradeModalStudentName');
            const scoreInput = document.getElementById('gradeScoreInput');
            const notesEl = document.getElementById('gradeEvaluationNotes');

            if (nameEl) nameEl.textContent = `${studentName || ''} — ${assignmentTitle || ''}`;
            if (scoreInput) scoreInput.value = (currentScore !== undefined && currentScore !== null && currentScore !==
                'null') ? currentScore : '';
            if (notesEl) notesEl.value = (evaluationNotes && evaluationNotes !== 'null' && evaluationNotes !==
                'undefined') ? evaluationNotes : '';

            const formEl = document.getElementById('gradeForm');
            if (formEl) formEl.action = `${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review`;

            const questionsContainer = document.getElementById('submissionQuestionsContainer');
            if (questionsContainer) {
                questionsContainer.innerHTML =
                    `<p class="text-xs text-slate-400 italic text-center py-4">${isAr ? 'جاري تحميل تفاصيل الإجابات...' : 'Loading question breakdown...'}</p>`;
            }

            window.openModal('gradeModal');

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review-details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success && data.submission) {
                    if (nameEl && data.submission.student_name) {
                        nameEl.textContent =
                            `${data.submission.student_name} — ${data.submission.assignment_title || ''}`;
                    }
                    if (scoreInput && data.submission.score !== null && data.submission.score !== undefined) {
                        scoreInput.value = data.submission.score;
                    }
                    if (notesEl && data.submission.evaluation_notes) {
                        notesEl.value = data.submission.evaluation_notes;
                    }
                }
                if (data.success && data.questions && data.questions.length > 0) {
                    let html = '';
                    data.questions.forEach((q, idx) => {
                        const statusBadge = q.is_correct ?
                            `<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> ${isAr ? 'صحيح' : 'Correct'} (+${q.points_earned}/${q.points} pts)</span>` :
                            `<span class="px-2 py-0.5 bg-red-100 dark:bg-rose-950/70 text-red-800 dark:text-rose-300 border border-red-300 dark:border-rose-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> ${isAr ? 'خطأ' : 'Incorrect'} (0/${q.points} pts)</span>`;

                        let optsHtml = '';
                        q.options.forEach(opt => {
                            let optStyle =
                                'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200';
                            let badge = '';

                            if (opt.is_correct && opt.is_selected) {
                                optStyle =
                                    'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 font-bold';
                                badge =
                                    `<span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px]">${isAr ? 'إجابة الطالب الصحيحة' : 'Student Selected (Correct)'}</span>`;
                            } else if (opt.is_correct) {
                                optStyle =
                                    'bg-teal-50 dark:bg-teal-950/50 border-teal-300 dark:border-teal-800 text-teal-900 dark:text-teal-200 font-bold';
                                badge =
                                    `<span class="text-teal-600 dark:text-teal-400 font-mono text-[10px]">${isAr ? 'الإجابة النموذجية' : 'Correct Answer'}</span>`;
                            } else if (opt.is_selected) {
                                optStyle =
                                    'bg-red-50 dark:bg-rose-950/50 border-red-300 dark:border-rose-800 text-red-900 dark:text-rose-200 font-bold';
                                badge =
                                    `<span class="text-red-600 dark:text-rose-400 font-mono text-[10px]">${isAr ? 'إجابة الطالب الخاطئة' : 'Student Selected (Wrong)'}</span>`;
                            }

                            optsHtml += `<div class="p-2.5 rounded-xl border ${optStyle} text-xs flex items-center justify-between gap-2">
                                <span>${escapeHtml(opt.option_text)}</span>
                                ${badge}
                            </div>`;

                            if (opt.explanation && opt.is_correct) {
                                optsHtml +=
                                    `<p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono italic pl-2">${isAr ? 'التوضيح:' : 'Explanation:'} ${escapeHtml(opt.explanation)}</p>`;
                            }
                        });

                        html += `<div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900 dark:text-white">Q${idx + 1}: ${escapeHtml(q.question_text || (isAr ? 'سؤال' : 'Question'))}</span>
                                ${statusBadge}
                            </div>
                            <div class="space-y-1.5 pt-1">
                                ${optsHtml}
                            </div>
                        </div>`;
                    });
                    if (questionsContainer) questionsContainer.innerHTML = html;
                } else {
                    if (questionsContainer) questionsContainer.innerHTML =
                        `<p class="text-xs text-slate-500 dark:text-slate-400 italic text-center py-4">${isAr ? 'لا توجد تفاصيل أسئلة متاحة' : 'No question details available'}</p>`;
                }
            } catch (err) {
                if (questionsContainer) questionsContainer.innerHTML =
                    `<p class="text-xs text-rose-500 italic text-center py-4">${isAr ? 'تعذر تحميل التفاصيل' : 'Unable to load details'}</p>`;
            }
        }

        // ── View Switcher & Toolbar Filter Helpers for Attendance Tab ───────────────
        window.switchAttView = function(viewType) {
            document.querySelectorAll('.att-view-pane').forEach(el => el.classList.add('hidden'));
            const pane = document.getElementById(viewType === 'cards' ? 'attViewCards' : (viewType === 'table' ?
                'attViewTable' : 'attViewMatrix'));
            if (pane) pane.classList.remove('hidden');

            const btnCards = document.getElementById('attViewBtnCards');
            const btnTable = document.getElementById('attViewBtnTable');
            const btnMatrix = document.getElementById('attViewBtnMatrix');

            [btnCards, btnTable, btnMatrix].forEach(b => {
                if (b) {
                    b.classList.remove('bg-teal-600', 'text-white', 'shadow-2xs');
                    b.classList.add('text-slate-600', 'dark:text-slate-400');
                }
            });

            const activeBtn = viewType === 'cards' ? btnCards : (viewType === 'table' ? btnTable : btnMatrix);
            if (activeBtn) {
                activeBtn.classList.remove('text-slate-600', 'dark:text-slate-400');
                activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-2xs');
            }

            if (viewType === 'cards' && window.attCardsPaginator) {
                window.attCardsPaginator.update();
            } else if (viewType === 'table' && window.attTablePaginator) {
                window.attTablePaginator.update();
            } else if (viewType === 'matrix' && window.attMatrixPaginator) {
                window.attMatrixPaginator.update();
            }
        };

        window.applyAttendanceFilters = function() {
            const courseVal = (document.getElementById('attFilterCourse')?.value || 'all').toLowerCase();
            const dateVal = document.getElementById('attFilterDate')?.value || '';
            const statusVal = (document.getElementById('attFilterStatus')?.value || 'all').toLowerCase();
            const searchVal = (document.getElementById('attSearchInput')?.value || '').toLowerCase().trim();

            const rows = document.querySelectorAll('.att-session-row');
            const cards = document.querySelectorAll('.att-session-card');

            const matches = (el) => {
                const cId = (el.getAttribute('data-course-id') || '').toLowerCase();
                const sDate = el.getAttribute('data-date') || '';
                const sStatus = (el.getAttribute('data-status') || '').toLowerCase();
                const sTitle = (el.getAttribute('data-title') || '').toLowerCase();
                const cTitle = (el.getAttribute('data-course-title') || '').toLowerCase();

                if (courseVal !== 'all' && cId !== courseVal) return false;
                if (dateVal && sDate !== dateVal) return false;
                if (statusVal !== 'all' && sStatus !== statusVal) return false;
                if (searchVal && !sTitle.includes(searchVal) && !cTitle.includes(searchVal)) return false;
                return true;
            };

            rows.forEach(r => {
                if (matches(r)) {
                    r.classList.remove('hidden');
                } else {
                    r.classList.add('hidden');
                }
            });

            cards.forEach(c => {
                if (matches(c)) {
                    c.classList.remove('hidden');
                } else {
                    c.classList.add('hidden');
                }
            });

            if (window.attCardsPaginator) window.attCardsPaginator.update();
            if (window.attTablePaginator) window.attTablePaginator.update();
        };

        // ── Interactive Question Builder for Assignment Creator ───────────────────────
        let teacherQuestionCount = 0;

        function previewQuestionImage(input, previewContainerId) {
            const previewContainer = document.getElementById(previewContainerId);
            if (!previewContainer) return;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <div class="relative inline-block mt-2 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-xs bg-white dark:bg-slate-900 p-1">
                            <img src="${e.target.result}" class="h-20 sm:h-24 w-auto rounded-lg object-contain" alt="Preview">
                            <button type="button" onclick="clearSelectedImage('${input.id}', '${previewContainerId}')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] shadow-sm cursor-pointer" title="Remove image">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearSelectedImage(inputId, previewContainerId) {
            const input = document.getElementById(inputId);
            if (input) input.value = '';
            const previewContainer = document.getElementById(previewContainerId);
            if (previewContainer) previewContainer.innerHTML = '';
        }

        function removeExistingQuestionImage(qIdx) {
            const preview = document.getElementById(`editQImagePreview_${qIdx}`);
            const hiddenRemove = document.getElementById(`editQRemoveImage_${qIdx}`);
            if (hiddenRemove) hiddenRemove.value = '1';
            if (preview) preview.innerHTML = '';
        }

        function removeExistingOptionImage(qIdx, optIdx) {
            const preview = document.getElementById(`editOptImagePreview_${qIdx}_${optIdx}`);
            const hiddenRemove = document.getElementById(`editOptRemoveImage_${qIdx}_${optIdx}`);
            if (hiddenRemove) hiddenRemove.value = '1';
            if (preview) preview.innerHTML = '';
        }

        function addTeacherQuestion() {
            const container = document.getElementById('teacherQuestionsContainer');
            const emptyState = document.getElementById('teacherQuestionsEmptyState');
            if (!container) return;
            if (emptyState) emptyState.classList.add('hidden');

            const qIdx = teacherQuestionCount++;
            const isAr = @json(app()->getLocale() === 'ar');

            const html = `
    <div class="teacher-q-card p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-3.5 relative" id="teacherQCard_${qIdx}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-extrabold text-teal-900 dark:text-teal-200 bg-teal-100 dark:bg-teal-950/70 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                ${isAr ? 'السؤال رقم ' : 'Question #'}${qIdx + 1}
            </span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 text-xs font-mono">
                    <span class="text-slate-500 dark:text-slate-400">${isAr ? 'الدرجة:' : 'Pts:'}</span>
                    <input type="number" step="0.5" name="questions[${qIdx}][points]" value="1" min="0.5" class="w-14 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-2 py-1 text-xs font-bold text-center">
                </div>
                <button type="button" onclick="removeTeacherQuestion(${qIdx})" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 text-xs font-bold font-mono px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i> ${isAr ? 'حذف' : 'Remove'}
                </button>
            </div>
        </div>

        <div>
            <textarea name="questions[${qIdx}][question_text]" rows="2" required placeholder="${isAr ? 'اكتب نص السؤال هنا...' : 'Type question text here...'}" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-xl p-2.5 text-xs font-mono focus:outline-none focus:border-teal-600"></textarea>
        </div>

        {{-- Question Diagram / Image Attachment --}}
        <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80">
            <div class="flex items-center justify-between gap-2">
                <label class="text-[11px] font-mono font-bold text-slate-600 dark:text-slate-400 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-image text-teal-600 dark:text-teal-400 text-xs"></i>
                    <span>${isAr ? 'صورة / رسم بياني للسؤال (اختياري)' : 'Question Image / Diagram (Optional)'}</span>
                </label>
                <label for="createQImage_${qIdx}" class="px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 text-[10px] font-mono font-bold hover:bg-teal-100 cursor-pointer transition-all flex items-center gap-1">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                    <span>${isAr ? 'رفع صورة' : 'Upload Image'}</span>
                </label>
                <input type="file" id="createQImage_${qIdx}" name="questions[${qIdx}][image]" accept="image/*" class="hidden" onchange="previewQuestionImage(this, 'createQPreview_${qIdx}')">
            </div>
            <div id="createQPreview_${qIdx}"></div>
        </div>

        <div class="space-y-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
            <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 font-bold">${isAr ? 'الخيارات (حدد الدائرة بجانب الإجابة الصحيحة وأرفق صورة لكل خيار إن وُجد):' : 'Answer Choices (Select radio button for the correct option and attach choice images if needed):'}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                ${[0, 1, 2, 3].map(optIdx => `
                        <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="questions[${qIdx}][correct_index]" value="${optIdx}" ${optIdx === 0 ? 'checked' : ''} class="text-teal-600 focus:ring-teal-500 cursor-pointer shrink-0">
                                <input type="text" name="questions[${qIdx}][options][${optIdx}]" placeholder="${isAr ? 'نص الخيار ' + String.fromCharCode(65 + optIdx) + ' (أو ارفع صورة فقط)' : 'Option ' + String.fromCharCode(65 + optIdx) + ' text (or image only)'}" class="w-full text-xs font-mono border-0 focus:ring-0 p-0 text-slate-800 dark:text-slate-100 bg-transparent">
                                <label for="createOptImage_${qIdx}_${optIdx}" class="text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 cursor-pointer p-1 shrink-0" title="${isAr ? 'إرفاق صورة للخيار' : 'Attach image to choice'}">
                                    <i class="fa-regular fa-image text-xs"></i>
                                </label>
                                <input type="file" id="createOptImage_${qIdx}_${optIdx}" name="questions[${qIdx}][option_images][${optIdx}]" accept="image/*" class="hidden" onchange="previewQuestionImage(this, 'createOptPreview_${qIdx}_${optIdx}')">
                            </div>
                            <div id="createOptPreview_${qIdx}_${optIdx}"></div>
                        </div>
                    `).join('')}
            </div>
        </div>
    </div>`;

            container.insertAdjacentHTML('beforeend', html);
        }

        function removeTeacherQuestion(qIdx) {
            const el = document.getElementById('teacherQCard_' + qIdx);
            if (el) el.remove();
            const container = document.getElementById('teacherQuestionsContainer');
            const emptyState = document.getElementById('teacherQuestionsEmptyState');
            if (container && container.children.length === 0 && emptyState) {
                emptyState.classList.remove('hidden');
            }
        }

        // ── Interactive Question Builder for Assignment Editor ───────────────────────
        let editTeacherQuestionCount = 0;

        function addEditTeacherQuestion(existingData = null) {
            const container = document.getElementById('editTeacherQuestionsContainer');
            const emptyState = document.getElementById('editTeacherQuestionsEmptyState');
            if (!container) return;
            if (emptyState) emptyState.classList.add('hidden');

            const qIdx = editTeacherQuestionCount++;
            const isAr = @json(app()->getLocale() === 'ar');

            const qId = existingData?.id || '';
            const qText = existingData?.question_text || '';
            const qPoints = existingData?.points || 1;
            const existingOptions = existingData?.options || [];
            const qImagePath = existingData?.image_path || '';
            const qImageUrl = existingData?.image_url || (qImagePath ? `${appBaseUrl}/storage/${qImagePath}` : '');

            let optionsHtml = '';
            for (let optIdx = 0; optIdx < 4; optIdx++) {
                const optObj = existingOptions[optIdx] || {};
                const optText = optObj.option_text || '';
                const isChecked = optObj.is_correct ? 'checked' : (optIdx === 0 && !existingData ? 'checked' : '');
                const optImagePath = optObj.image_path || '';
                const optImageUrl = optObj.image_url || (optImagePath ? `${appBaseUrl}/storage/${optImagePath}` : '');

                optionsHtml += `
                    <div class="bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                        <input type="hidden" name="questions[${qIdx}][existing_option_images][${optIdx}]" value="${escapeHtml(optImagePath)}">
                        <input type="hidden" id="editOptRemoveImage_${qIdx}_${optIdx}" name="questions[${qIdx}][remove_option_images][${optIdx}]" value="0">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="questions[${qIdx}][correct_index]" value="${optIdx}" ${isChecked} class="text-teal-600 focus:ring-teal-500 cursor-pointer shrink-0">
                            <input type="text" name="questions[${qIdx}][options][${optIdx}]" value="${escapeHtml(optText)}" placeholder="${isAr ? 'نص الخيار ' + String.fromCharCode(65 + optIdx) + ' (أو صورة فقط)' : 'Option ' + String.fromCharCode(65 + optIdx) + ' text (or image only)'}" class="w-full text-xs font-mono border-0 focus:ring-0 p-0 text-slate-800 dark:text-slate-100 bg-transparent">
                            <label for="editOptImage_${qIdx}_${optIdx}" class="text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 cursor-pointer p-1 shrink-0" title="${isAr ? 'إرفاق صورة للخيار' : 'Attach image to choice'}">
                                <i class="fa-regular fa-image text-xs"></i>
                            </label>
                            <input type="file" id="editOptImage_${qIdx}_${optIdx}" name="questions[${qIdx}][option_images][${optIdx}]" accept="image/*" class="hidden" onchange="previewQuestionImage(this, 'editOptImagePreview_${qIdx}_${optIdx}')">
                        </div>
                        <div id="editOptImagePreview_${qIdx}_${optIdx}">
                            ${optImageUrl ? `
                                <div class="relative inline-block mt-1 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-xs bg-white dark:bg-slate-900 p-1">
                                    <img src="${optImageUrl}" class="h-16 w-auto rounded-lg object-contain" alt="Option Image">
                                    <button type="button" onclick="removeExistingOptionImage(${qIdx}, ${optIdx})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[9px] shadow-sm cursor-pointer" title="${isAr ? 'حذف الصورة' : 'Remove image'}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            const html = `
    <div class="teacher-q-card p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-2xl space-y-3.5 relative" id="editTeacherQCard_${qIdx}">
        <input type="hidden" name="questions[${qIdx}][id]" value="${qId}">
        <input type="hidden" name="questions[${qIdx}][existing_image]" value="${escapeHtml(qImagePath)}">
        <input type="hidden" id="editQRemoveImage_${qIdx}" name="questions[${qIdx}][remove_image]" value="0">
        <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-extrabold text-teal-900 dark:text-teal-200 bg-teal-100 dark:bg-teal-950/70 px-3 py-1 rounded-full border border-teal-200 dark:border-teal-800">
                ${isAr ? 'السؤال رقم ' : 'Question #'}${qIdx + 1}
            </span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 text-xs font-mono">
                    <span class="text-slate-500 dark:text-slate-400">${isAr ? 'الدرجة:' : 'Pts:'}</span>
                    <input type="number" step="0.5" name="questions[${qIdx}][points]" value="${qPoints}" min="0.5" class="w-14 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-lg px-2 py-1 text-xs font-bold text-center">
                </div>
                <button type="button" onclick="removeEditTeacherQuestion(${qIdx})" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300 text-xs font-bold font-mono px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i> ${isAr ? 'حذف' : 'Remove'}
                </button>
            </div>
        </div>

        <div>
            <textarea name="questions[${qIdx}][question_text]" rows="2" required placeholder="${isAr ? 'اكتب نص السؤال هنا...' : 'Type question text here...'}" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 rounded-xl p-2.5 text-xs font-mono focus:outline-none focus:border-teal-600">${escapeHtml(qText)}</textarea>
        </div>

        {{-- Question Diagram / Image Attachment --}}
        <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80">
            <div class="flex items-center justify-between gap-2">
                <label class="text-[11px] font-mono font-bold text-slate-600 dark:text-slate-400 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-image text-teal-600 dark:text-teal-400 text-xs"></i>
                    <span>${isAr ? 'صورة / رسم بياني للسؤال (اختياري)' : 'Question Image / Diagram (Optional)'}</span>
                </label>
                <label for="editQImage_${qIdx}" class="px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 text-[10px] font-mono font-bold hover:bg-teal-100 cursor-pointer transition-all flex items-center gap-1">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                    <span>${isAr ? 'تغيير أو رفع صورة' : 'Change or Upload Image'}</span>
                </label>
                <input type="file" id="editQImage_${qIdx}" name="questions[${qIdx}][image]" accept="image/*" class="hidden" onchange="previewQuestionImage(this, 'editQImagePreview_${qIdx}')">
            </div>
            <div id="editQImagePreview_${qIdx}">
                ${qImageUrl ? `
                    <div class="relative inline-block mt-2 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-xs bg-white dark:bg-slate-900 p-1">
                        <img src="${qImageUrl}" class="h-20 sm:h-24 w-auto rounded-lg object-contain" alt="Question Diagram">
                        <button type="button" onclick="removeExistingQuestionImage(${qIdx})" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] shadow-sm cursor-pointer" title="${isAr ? 'حذف الصورة' : 'Remove image'}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                ` : ''}
            </div>
        </div>

        <div class="space-y-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
            <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 font-bold">${isAr ? 'الخيارات (حدد الدائرة بجانب الإجابة الصحيحة وأرفق صورة لكل خيار إن وُجد):' : 'Answer Choices (Select radio button for the correct option and attach choice images if needed):'}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                ${optionsHtml}
            </div>
        </div>
    </div>`;

            container.insertAdjacentHTML('beforeend', html);
        }

        function removeEditTeacherQuestion(qIdx) {
            const el = document.getElementById('editTeacherQCard_' + qIdx);
            if (el) el.remove();
            const container = document.getElementById('editTeacherQuestionsContainer');
            const emptyState = document.getElementById('editTeacherQuestionsEmptyState');
            if (container && container.children.length === 0 && emptyState) {
                emptyState.classList.remove('hidden');
            }
        }

        async function openEditAssignmentModal(assignmentId) {
            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/assignments/${assignmentId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (!res.ok || !data.success || !data.assignment) {
                    showTeacherToast(data.message || (isArLocale ? 'تعذر جلب تفاصيل الواجب للتعديل' :
                        'Failed to load assignment details for editing'), false);
                    return;
                }

                const a = data.assignment;
                document.getElementById('editAssignmentId').value = a.id;
                document.getElementById('editAssignmentCourseId').value = a.course_id;
                document.getElementById('editAssignmentLiveSessionId').value = a.live_session_id || '';
                document.getElementById('editAssignmentTitle').value = a.title || '';
                document.getElementById('editAssignmentDescription').value = a.description || '';

                if (a.due_at_raw) {
                    document.getElementById('editAssignmentDueAt').value = a.due_at_raw.replace(' ', 'T').substring(0,
                        16);
                } else if (a.due_at) {
                    const d = new Date(a.due_at);
                    if (!isNaN(d)) {
                        const pad = n => n < 10 ? '0' + n : n;
                        document.getElementById('editAssignmentDueAt').value =
                            `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                    }
                }

                document.getElementById('editAssignmentDuration').value = a.duration_minutes || 30;
                document.getElementById('editAssignmentPassScore').value = a.passing_score || 70;

                // Load Questions
                const qContainer = document.getElementById('editTeacherQuestionsContainer');
                const emptyState = document.getElementById('editTeacherQuestionsEmptyState');
                if (qContainer) {
                    qContainer.innerHTML = '';
                    editTeacherQuestionCount = 0;
                    if (data.questions && data.questions.length > 0) {
                        if (emptyState) emptyState.classList.add('hidden');
                        data.questions.forEach(q => addEditTeacherQuestion(q));
                    } else {
                        if (emptyState) emptyState.classList.remove('hidden');
                    }
                }

                window.openModal('editAssignmentModal');
            } catch (err) {
                showTeacherToast(isArLocale ? 'حدث خطأ أثناء تحميل بيانات الواجب' : 'Error loading assignment details',
                    false);
            }
        }

        async function confirmDeleteAssignment(assignmentId, title) {
            const isAr = @json(app()->getLocale() === 'ar');
            const confirmMsg = isAr ?
                `هل أنت متأكد من حذف الواجب "${title || ''}"؟ سيتم حذف جميع الأسئلة وإجابات الطلاب نهائياً.` :
                `Are you sure you want to delete assignment "${title || ''}"? All questions and student submissions will be permanently removed.`;

            if (!confirm(confirmMsg)) return;

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/assignments/${assignmentId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    const card = document.getElementById(`assignmentCard_${assignmentId}`);
                    if (card) card.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showTeacherToast(data.message || (isAr ? 'فشل حذف الواجب' : 'Failed to delete assignment'), false);
                }
            } catch (err) {
                showTeacherToast(isAr ? 'خطأ في الاتصال بالخادم' : 'Connection error', false);
            }
        }

        function deleteCurrentAssignmentFromModal() {
            if (window.currentViewingAssignment) {
                closeModal('assignmentDetailsModal');
                confirmDeleteAssignment(window.currentViewingAssignment.id, window.currentViewingAssignment.title);
            }
        }

        function editCurrentAssignmentFromModal() {
            if (window.currentViewingAssignment) {
                closeModal('assignmentDetailsModal');
                openEditAssignmentModal(window.currentViewingAssignment.id);
            }
        }

        async function confirmDeleteSession(sessionId, title) {
            const isAr = @json(app()->getLocale() === 'ar');
            const confirmMsg = isAr ?
                `هل أنت متأكد من حذف الحصة "${title || ''}" نهائياً؟` :
                `Are you sure you want to permanently delete session "${title || ''}"?`;

            if (!confirm(confirmMsg)) return;

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    const mobileCard = document.getElementById(`sessionMobileCard_${sessionId}`);
                    if (mobileCard) mobileCard.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showTeacherToast(data.message || (isAr ? 'فشل حذف الحصة' : 'Failed to delete session'), false);
                }
            } catch (err) {
                showTeacherToast(isAr ? 'خطأ في الاتصال بالخادم' : 'Connection error', false);
            }
        }

        function openEditRecurringScheduleModal(scheduleId, el) {
            let title = '',
                courseId = '',
                recurrenceType = '',
                startTime = '10:00',
                duration = 60,
                startDate = '',
                endDate = '',
                days = [],
                meetingLink = '',
                notes = '';
            let dayStartTimes = {},
                dayDurations = {},
                dayMeetingLinks = {};

            if (el && typeof el === 'object' && el.nodeType) {
                const card = el.closest('.schedule-item-card') || el;
                title = card.getAttribute('data-title') || el.getAttribute('data-title') || '';
                courseId = card.getAttribute('data-course-id') || el.getAttribute('data-course-id') || '';
                recurrenceType = card.getAttribute('data-recurrence-type') || el.getAttribute('data-recurrence-type') ||
                    'weekly';
                startTime = card.getAttribute('data-start-time') || el.getAttribute('data-start-time') || '10:00';
                duration = card.getAttribute('data-duration') || el.getAttribute('data-duration') || 60;
                startDate = card.getAttribute('data-start-date') || el.getAttribute('data-start-date') || '';
                endDate = card.getAttribute('data-end-date') || el.getAttribute('data-end-date') || '';
                try {
                    const daysAttr = card.getAttribute('data-days') || el.getAttribute('data-days') || '[]';
                    days = JSON.parse(daysAttr);
                } catch (e) {
                    days = [];
                }
                try {
                    const stAttr = card.getAttribute('data-day-start-times') || el.getAttribute('data-day-start-times') ||
                        '{}';
                    dayStartTimes = JSON.parse(stAttr) || {};
                } catch (e) {
                    dayStartTimes = {};
                }
                try {
                    const durAttr = card.getAttribute('data-day-durations') || el.getAttribute('data-day-durations') ||
                    '{}';
                    dayDurations = JSON.parse(durAttr) || {};
                } catch (e) {
                    dayDurations = {};
                }
                try {
                    const linkAttr = card.getAttribute('data-day-meeting-links') || el.getAttribute(
                        'data-day-meeting-links') || '{}';
                    dayMeetingLinks = JSON.parse(linkAttr) || {};
                } catch (e) {
                    dayMeetingLinks = {};
                }

                meetingLink = card.getAttribute('data-meeting-link') || el.getAttribute('data-meeting-link') || '';
                notes = card.getAttribute('data-notes') || el.getAttribute('data-notes') || '';
            }

            document.getElementById('editRecScheduleId').value = scheduleId;
            document.getElementById('editRecTitle').value = title;
            if (document.getElementById('editRecCourseId')) document.getElementById('editRecCourseId').value = courseId;
            document.getElementById('editRecStartDate').value = startDate;
            document.getElementById('editRecEndDate').value = endDate;
            document.getElementById('editRecStartTime').value = startTime;
            document.getElementById('editRecDuration').value = duration;
            document.getElementById('editRecMeetingLink').value = meetingLink;
            document.getElementById('editRecNotes').value = notes;

            // Populate per-day inputs & check days
            [0, 1, 2, 3, 4, 5, 6].forEach(dayVal => {
                const cb = document.getElementById('editRecDayCheck_' + dayVal);
                const timeInput = document.getElementById('editRecDayTime_' + dayVal);
                const durationInput = document.getElementById('editRecDayDuration_' + dayVal);
                const linkInput = document.getElementById('editRecDayLink_' + dayVal);

                const isDayChecked = days.map(String).includes(String(dayVal));
                if (cb) cb.checked = isDayChecked;

                if (timeInput) {
                    timeInput.value = dayStartTimes[dayVal] || dayStartTimes[String(dayVal)] || startTime ||
                    '10:00';
                }
                if (durationInput) {
                    durationInput.value = dayDurations[dayVal] || dayDurations[String(dayVal)] || duration || 60;
                }
                if (linkInput) {
                    linkInput.value = dayMeetingLinks[dayVal] || dayMeetingLinks[String(dayVal)] || '';
                }

                if (cb && typeof window.toggleEditDayTimeCard === 'function') {
                    window.toggleEditDayTimeCard(cb, 'editRecDayCard_' + dayVal);
                }
            });

            if (typeof window.openScheduleModal === 'function') {
                window.openScheduleModal('editRecurringScheduleModal');
            } else {
                window.openModal('editRecurringScheduleModal');
            }
        }

        async function confirmDeleteRecurringSchedule(scheduleId, title) {
            const isAr = @json(app()->getLocale() === 'ar');
            const confirmMsg = isAr ?
                `هل أنت متأكد من حذف الجدول المتكرر "${title || ''}"؟ سيتم حذف جميع الحصص المستقبلية غير المكتملة المرتبطة به.` :
                `Are you sure you want to delete recurring schedule "${title || ''}"? All future uncompleted session instances will be cancelled and deleted.`;

            if (!confirm(confirmMsg)) return;

            try {
                const res = await fetch(`${appBaseUrl}/ajax/teacher/recurring-schedules/${scheduleId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    const card = document.getElementById(`recurringScheduleCard_${scheduleId}`);
                    if (card) card.remove();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showTeacherToast(data.message || (isAr ? 'فشل حذف الجدول المتكرر' :
                        'Failed to delete recurring schedule'), false);
                }
            } catch (err) {
                showTeacherToast(isAr ? 'خطأ في الاتصال بالخادم' : 'Connection error', false);
            }
        }

        window.openEditAssignmentModal = openEditAssignmentModal;
        window.confirmDeleteAssignment = confirmDeleteAssignment;
        window.deleteCurrentAssignmentFromModal = deleteCurrentAssignmentFromModal;
        window.editCurrentAssignmentFromModal = editCurrentAssignmentFromModal;
        window.confirmDeleteSession = confirmDeleteSession;
        window.openEditRecurringScheduleModal = openEditRecurringScheduleModal;
        window.confirmDeleteRecurringSchedule = confirmDeleteRecurringSchedule;

        function showTeacherToast(message, isSuccess) {
            if (window.Toast) {
                if (isSuccess) {
                    window.Toast.success(message);
                } else {
                    window.Toast.danger(message);
                }
            } else {
                const toast = document.getElementById('teacherToastAlert');
                if (!toast) return;
                toast.className =
                    `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
                toast.textContent = message;
                toast.classList.remove('hidden');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        function bindAjaxForm(formId, onSuccess) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        onSuccess(data);
                    } else {
                        showTeacherToast(data.message || 'Validation error', false);
                    }
                } catch (err) {
                    showTeacherToast('Network connection error', false);
                }
            });
        }

        // ── DOM Initializations ───────────────────────────────────────────────────────
        function initActiveTabFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const queryTab = urlParams.get('tab');
            const hashTab = window.location.hash ? window.location.hash.replace('#', '') : '';
            const requestedTab = queryTab || hashTab;
            if (requestedTab) {
                switchTeacherTab(requestedTab);
            }
        }

        window.addEventListener('hashchange', initActiveTabFromUrl);

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Auto-switch tab from URL query param or hash (e.g. #attendance or ?tab=attendance)
            initActiveTabFromUrl();

            // 2. Check for initial student modal open (e.g. ?student=123)
            const rootEl = document.getElementById('teacher-portal-root');
            const initStudent = rootEl ? rootEl.getAttribute('data-initial-student') : null;
            if (initStudent) {
                switchTeacherTab('students');
                openStudentDetailsModal(initStudent);
            }

            // 3. Counter Animations
            const counters = document.querySelectorAll('.js-counter');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                if (target === 0) return;
                let count = 0;
                const step = Math.max(1, Math.ceil(target / 25));
                const timer = setInterval(() => {
                    count += step;
                    if (count >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = count.toLocaleString();
                    }
                }, 30);
            });

            // 4. Initialize Universal Real-Time Section Paginators
            if (document.getElementById('todaySessionsListContainer')) {
                window.todaySessionsPaginator = new EliteSectionPaginator({
                    containerId: 'todaySessionsListContainer',
                    itemSelector: '.today-session-item',
                    paginationId: 'todaySessionsPagination',
                    pageSize: 4,
                    scrollToTop: false
                });
            }

            if (document.getElementById('pendingSubmissionsContainer')) {
                window.pendingSubmissionsPaginator = new EliteSectionPaginator({
                    containerId: 'pendingSubmissionsContainer',
                    itemSelector: '.pending-sub-item',
                    paginationId: 'pendingSubmissionsPagination',
                    pageSize: 5,
                    scrollToTop: false
                });
            }

            if (document.getElementById('recentStudentsContainer')) {
                window.recentStudentsPaginator = new EliteSectionPaginator({
                    containerId: 'recentStudentsContainer',
                    itemSelector: '.recent-student-item',
                    paginationId: 'recentStudentsPagination',
                    pageSize: 5,
                    scrollToTop: false
                });
            }

            if (document.getElementById('overviewCoursesListContainer')) {
                window.overviewCoursesPaginator = new EliteSectionPaginator({
                    containerId: 'overviewCoursesListContainer',
                    itemSelector: '.overview-course-item',
                    paginationId: 'overviewCoursesPagination',
                    pageSize: 4,
                    scrollToTop: false
                });
            }

            if (document.getElementById('studentsGridContainer')) {
                window.studentPaginator = new EliteSectionPaginator({
                    containerId: 'studentsGridContainer',
                    itemSelector: '.student-roster-card',
                    paginationId: 'studentsPagination',
                    pageSize: 9,
                    scrollToTop: true
                });
            }

            if (document.getElementById('sessionsTableBody')) {
                window.sessionsPaginator = new EliteSectionPaginator({
                    containerId: 'sessionsTableBody',
                    itemSelector: '.session-table-row',
                    paginationId: 'sessionsPagination',
                    pageSize: 10,
                    scrollToTop: true
                });
            }

            if (document.getElementById('sessionsMobileListContainer')) {
                window.sessionsMobilePaginator = new EliteSectionPaginator({
                    containerId: 'sessionsMobileListContainer',
                    itemSelector: '.session-mobile-card',
                    paginationId: 'sessionsMobilePagination',
                    pageSize: 6,
                    scrollToTop: true
                });
            }

            if (document.getElementById('assignmentsGridContainer')) {
                window.assignmentsPaginator = new EliteSectionPaginator({
                    containerId: 'assignmentsGridContainer',
                    itemSelector: '.assignment-card',
                    paginationId: 'assignmentsPagination',
                    pageSize: 6,
                    scrollToTop: true
                });
            }

            if (document.getElementById('submissionsTableBody')) {
                window.submissionsPaginator = new EliteSectionPaginator({
                    containerId: 'submissionsTableBody',
                    itemSelector: '.submission-table-row',
                    paginationId: 'submissionsPagination',
                    pageSize: 10,
                    scrollToTop: true
                });
            }

            if (document.getElementById('submissionsMobileListContainer')) {
                window.submissionsMobilePaginator = new EliteSectionPaginator({
                    containerId: 'submissionsMobileListContainer',
                    itemSelector: '.submission-mobile-card',
                    paginationId: 'submissionsMobilePagination',
                    pageSize: 6,
                    scrollToTop: true
                });
            }

            if (document.getElementById('attCardsGrid')) {
                window.attCardsPaginator = new EliteSectionPaginator({
                    containerId: 'attCardsGrid',
                    itemSelector: '.att-session-card',
                    paginationId: 'attCardsPagination',
                    pageSize: 8,
                    scrollToTop: true
                });
            }

            if (document.getElementById('attTableBody')) {
                window.attTablePaginator = new EliteSectionPaginator({
                    containerId: 'attTableBody',
                    itemSelector: '.att-table-row',
                    paginationId: 'attTablePagination',
                    pageSize: 10,
                    scrollToTop: true
                });
            }

            if (document.getElementById('attMatrixGrid')) {
                window.attMatrixPaginator = new EliteSectionPaginator({
                    containerId: 'attMatrixGrid',
                    itemSelector: '.att-matrix-card',
                    paginationId: 'attMatrixPagination',
                    pageSize: 9,
                    scrollToTop: true
                });
            }

            if (document.getElementById('notifsListContainer')) {
                window.notifsPaginator = new EliteSectionPaginator({
                    containerId: 'notifsListContainer',
                    itemSelector: '.notif-feed-item',
                    paginationId: 'notifsPagination',
                    pageSize: 6,
                    scrollToTop: true
                });
            }

            if (document.getElementById('schedulesListContainer')) {
                window.schedulesPaginator = new EliteSectionPaginator({
                    containerId: 'schedulesListContainer',
                    itemSelector: '.schedule-item-card',
                    paginationId: 'schedulesPagination',
                    pageSize: 6,
                    scrollToTop: true
                });
            }

            // Real-Time Live Session Search Listener (Desktop & Mobile)
            const liveSessionSearch = document.getElementById('liveSessionSearchInput');
            if (liveSessionSearch) {
                liveSessionSearch.addEventListener('input', function() {
                    const term = this.value.trim().toLowerCase();
                    const rows = document.querySelectorAll('#sessionsTableBody .session-table-row');
                    const cards = document.querySelectorAll(
                        '#sessionsMobileListContainer .session-mobile-card');
                    let visibleCount = 0;
                    rows.forEach(r => {
                        const title = r.getAttribute('data-title') || '';
                        const course = r.getAttribute('data-course') || '';
                        const date = r.getAttribute('data-date') || '';
                        const match = !term || title.includes(term) || course.includes(term) || date
                            .includes(term);
                        if (match) {
                            r.removeAttribute('data-filtered-out');
                            visibleCount++;
                        } else {
                            r.setAttribute('data-filtered-out', '1');
                        }
                    });
                    cards.forEach(c => {
                        const title = c.getAttribute('data-title') || '';
                        const course = c.getAttribute('data-course') || '';
                        const date = c.getAttribute('data-date') || '';
                        const match = !term || title.includes(term) || course.includes(term) || date
                            .includes(term);
                        if (match) {
                            c.removeAttribute('data-filtered-out');
                        } else {
                            c.setAttribute('data-filtered-out', '1');
                        }
                    });
                    const empty = document.getElementById('sessionsEmptySearchState');
                    if (empty) {
                        if (visibleCount === 0 && (rows.length > 0 || cards.length > 0)) {
                            empty.classList.remove('hidden');
                        } else {
                            empty.classList.add('hidden');
                        }
                    }
                    if (window.sessionsPaginator) {
                        window.sessionsPaginator.reset();
                    }
                    if (window.sessionsMobilePaginator) {
                        window.sessionsMobilePaginator.reset();
                    }
                });
            }

            // 5. Attach Student Search & Filter Listeners
            const sInput = document.getElementById('studentSearchInput');
            if (sInput) sInput.addEventListener('input', applyStudentFilters);
            const cFilter = document.getElementById('studentCourseFilter');
            if (cFilter) cFilter.addEventListener('change', applyStudentFilters);
            const gFilter = document.getElementById('studentGradeFilter');
            if (gFilter) gFilter.addEventListener('change', applyStudentFilters);
            const aFilter = document.getElementById('studentAttendanceFilter');
            if (aFilter) aFilter.addEventListener('change', applyStudentFilters);

            // 4b. Attach Attendance Search & Filter Listeners
            const attSearch = document.getElementById('attSearchInput');
            if (attSearch) attSearch.addEventListener('input', applyAttendanceFilters);
            const attCourse = document.getElementById('attCourseFilter');
            if (attCourse) attCourse.addEventListener('change', applyAttendanceFilters);
            const attStatus = document.getElementById('attStatusFilter');
            if (attStatus) attStatus.addEventListener('change', applyAttendanceFilters);

            // 4c. Delegated Attendance Modal Trigger (Bulletproof click capture fallback)
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('button[data-session-id]');
                if (btn && !btn.hasAttribute('onclick')) {
                    const sId = btn.getAttribute('data-session-id');
                    const sTitle = btn.getAttribute('data-session-title') || '';
                    if (sId) {
                        e.preventDefault();
                        e.stopPropagation();
                        openAttendanceModal(sId, sTitle);
                    }
                }
            });

            // Initialize Elite Custom Selects for all filter dropdowns
            if (typeof window.initEliteCustomSelects === 'function') {
                window.initEliteCustomSelects();
            }

            // Clean up obsolete server-pagination query parameters from browser URL
            try {
                const url = new URL(window.location.href);
                if (url.searchParams.has('notif_page') || url.searchParams.has('sessions_page')) {
                    url.searchParams.delete('notif_page');
                    url.searchParams.delete('sessions_page');
                    window.history.replaceState({}, document.title, url.pathname + (url.searchParams.toString() ?
                        '?' + url.searchParams.toString() : ''));
                }
            } catch (e) {}

            // Helper function to detect phone numbers in client JS
            function clientHasPhoneNumber(text) {
                if (!text) return false;
                const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                let norm = text;
                for (let i = 0; i < 10; i++) {
                    norm = norm.replaceAll(eastern[i], i.toString());
                }
                if (/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i.test(norm)) return true;
                if (/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i.test(
                        norm)) return true;
                if (/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/.test(norm)) return true;
                if (/\b[0-9]{8,16}\b/.test(norm)) return true;
                return false;
            }

            const noteTextarea = document.getElementById('noteContentTextarea');
            const phoneWarning = document.getElementById('notePhoneWarning');
            if (noteTextarea && phoneWarning) {
                noteTextarea.addEventListener('input', function() {
                    const hasPhone = clientHasPhoneNumber(noteTextarea.value);
                    if (hasPhone) {
                        phoneWarning.classList.remove('hidden');
                        noteTextarea.classList.add('border-rose-500', 'bg-rose-50/20');
                        noteTextarea.classList.remove('border-slate-200');
                    } else {
                        phoneWarning.classList.add('hidden');
                        noteTextarea.classList.remove('border-rose-500', 'bg-rose-50/20');
                        noteTextarea.classList.add('border-slate-200');
                    }
                });
            }

            // 5. Educational Note AJAX Form Handler with Security Policy
            const noteForm = document.getElementById('addNoteForm');
            if (noteForm) {
                noteForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const sId = document.getElementById('noteStudentUserId').value;
                    if (!sId) return;

                    const noteVal = noteTextarea ? noteTextarea.value : '';
                    if (clientHasPhoneNumber(noteVal)) {
                        if (window.Toast) {
                            window.Toast.danger(
                                isArLocale ?
                                'لا يمكنك إرسال أرقام الهواتف أو وسائل التواصل في الملاحظات التعليمية حرصاً على الأمان والخصوصية' :
                                'Security Alert: Sharing phone numbers or contact details in notes is prohibited.',
                                isArLocale ?
                                'تنبيه أمان وخصوصية <i class="fa-solid fa-shield-halved"></i>' :
                                'Security Violation'
                            );
                        }
                        return;
                    }

                    const formData = new FormData(noteForm);
                    const submitBtn = document.getElementById('saveNoteBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
                    }

                    try {
                        const res = await fetch(`${appBaseUrl}/ajax/teacher/students/${sId}/notes`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('addNoteModal');
                            noteForm.reset();
                            if (phoneWarning) phoneWarning.classList.add('hidden');
                            if (noteTextarea) noteTextarea.classList.remove('border-rose-500',
                                'bg-rose-50/20');
                            if (currentViewingStudentId == sId) {
                                openStudentDetailsModal(sId);
                            }
                        } else {
                            const msg = data.message || (data.errors && data.errors.note ? data.errors
                                .note[0] : 'Failed to save note');
                            if (window.Toast) {
                                window.Toast.danger(msg, isArLocale ?
                                    'تنبيه أمان <i class="fa-solid fa-shield-halved"></i>' :
                                    'Security Alert');
                            } else {
                                showTeacherToast(msg, false);
                            }
                        }
                    } catch (err) {
                        showTeacherToast('Network connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = `${isArLocale ? 'حفظ الملاحظة' : 'Save Note'} &rarr;`;
                        }
                    }
                });
            }

            window.toggleDayTimeCard = function(checkboxEl, cardId) {
                const card = document.getElementById(cardId);
                if (!card) return;
                const timeContainer = card.querySelector('.rec-day-time-container');
                const timeInput = card.querySelector('.rec-day-time-picker');
                const durationInput = card.querySelector('.rec-day-duration-picker');
                const linkInput = card.querySelector('.rec-day-link-picker');
                const badge = card.querySelector('.rec-day-status-badge');

                if (checkboxEl.checked) {
                    card.classList.add('is-selected-day', 'bg-teal-50/90', 'dark:bg-teal-950/50',
                        'border-teal-500', 'shadow-xs');
                    card.classList.remove('bg-white', 'dark:bg-slate-900', 'border-slate-200',
                        'dark:border-slate-800', 'opacity-60');
                    if (timeContainer) timeContainer.classList.remove('hidden');
                    if (timeInput) timeInput.disabled = false;
                    if (durationInput) durationInput.disabled = false;
                    if (linkInput) linkInput.disabled = false;
                    if (badge) {
                        badge.textContent = (typeof isArLocale !== 'undefined' && isArLocale) ? 'مُفَعَّل' :
                            'Active';
                        badge.className =
                            'rec-day-status-badge text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md bg-teal-100 dark:bg-teal-900/80 text-teal-800 dark:text-teal-200';
                    }
                } else {
                    card.classList.remove('is-selected-day', 'bg-teal-50/90', 'dark:bg-teal-950/50',
                        'border-teal-500', 'shadow-xs');
                    card.classList.add('bg-white', 'dark:bg-slate-900', 'border-slate-200',
                        'dark:border-slate-800', 'opacity-60');
                    if (timeContainer) timeContainer.classList.add('hidden');
                    if (timeInput) timeInput.disabled = true;
                    if (durationInput) durationInput.disabled = true;
                    if (linkInput) linkInput.disabled = true;
                    if (badge) {
                        badge.textContent = (typeof isArLocale !== 'undefined' && isArLocale) ? 'غير محدد' :
                            'Off';
                        badge.className =
                            'rec-day-status-badge text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400';
                    }
                }
            };
            window.toggleDayTimeInput = window.toggleDayTimeCard;

            window.applyMainTimeToAllDays = function() {
                const mainStartTime = document.getElementById('recStartTime')?.value || '10:00';
                const mainDuration = document.getElementById('recDuration')?.value || '60';
                const mainLink = document.getElementById('recMeetingLink')?.value || '';

                document.querySelectorAll('.rec-day-time-picker').forEach(input => {
                    input.value = mainStartTime;
                    delete input.dataset.userEdited;
                });

                document.querySelectorAll('.rec-day-duration-picker').forEach(input => {
                    input.value = mainDuration;
                });

                if (mainLink) {
                    document.querySelectorAll('.rec-day-link-picker').forEach(input => {
                        input.value = mainLink;
                    });
                }

                if (window.Toast) {
                    window.Toast.success(typeof isArLocale !== 'undefined' && isArLocale ?
                        'تم تطبيق الإعدادات الرئيسية على الأيام المحددة' :
                        'Synced main settings to selected days');
                } else if (typeof showTeacherToast === 'function') {
                    showTeacherToast(typeof isArLocale !== 'undefined' && isArLocale ?
                        'تم تطبيق الإعدادات الرئيسية على الأيام المحددة' :
                        'Synced main settings to selected days', true);
                }
            };

            window.applyMainTimeToAllPortalDays = function() {
                const portalForm = document.getElementById('recurringSchedulePortalForm');
                if (!portalForm) return;
                const mainStartTime = portalForm.querySelector('input[name="start_time"]')?.value || '10:00';
                const mainDuration = portalForm.querySelector('input[name="duration_minutes"]')?.value || '60';
                const mainLink = portalForm.querySelector('input[name="meeting_link"]')?.value || '';

                portalForm.querySelectorAll('.rec-day-time-picker').forEach(input => {
                    input.value = mainStartTime;
                    delete input.dataset.userEdited;
                });

                portalForm.querySelectorAll('.rec-day-duration-picker').forEach(input => {
                    input.value = mainDuration;
                });

                if (mainLink) {
                    portalForm.querySelectorAll('.rec-day-link-picker').forEach(input => {
                        input.value = mainLink;
                    });
                }

                if (window.Toast) {
                    window.Toast.success(typeof isArLocale !== 'undefined' && isArLocale ?
                        'تم تطبيق الإعدادات الرئيسية على الأيام المحددة' :
                        'Synced main settings to selected days');
                } else if (typeof showTeacherToast === 'function') {
                    showTeacherToast(typeof isArLocale !== 'undefined' && isArLocale ?
                        'تم تطبيق الإعدادات الرئيسية على الأيام المحددة' :
                        'Synced main settings to selected days', true);
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                const mainStartTimeInput = document.getElementById('recStartTime');
                if (mainStartTimeInput) {
                    mainStartTimeInput.addEventListener('change', function() {
                        const newTime = this.value;
                        document.querySelectorAll('.rec-day-time-picker').forEach(input => {
                            if (!input.dataset.userEdited) {
                                input.value = newTime;
                            }
                        });
                    });
                }
                document.querySelectorAll('.rec-day-time-picker').forEach(input => {
                    input.addEventListener('change', function() {
                        this.dataset.userEdited = 'true';
                    });
                });
            });

            // ── Recurring Schedule Helpers ──────────────────────────────────────────
            window.openCreateRecurringModal = function() {
                window.openModal('recurringScheduleModal');
            };

            window.toggleRecurrenceFields = function(type) {
                const daysContainer = document.getElementById('recDaysContainer');
                if (daysContainer) {
                    if (type === 'monthly' || type === 'single') {
                        daysContainer.classList.add('hidden');
                    } else {
                        daysContainer.classList.remove('hidden');
                    }
                }
            };

            window.previewRecurringDates = async function() {
                const form = document.getElementById('recurringScheduleForm');
                if (!form) return;

                const formData = new FormData(form);
                const previewContainer = document.getElementById('recPreviewContainer');
                const previewSummary = document.getElementById('recPreviewSummary');
                const conflictBadge = document.getElementById('recConflictStatusBadge');
                const conflictWarning = document.getElementById('recConflictWarning');
                const tableBody = document.getElementById('recPreviewTableBody');

                if (previewContainer) previewContainer.classList.remove('hidden');
                if (previewSummary) previewSummary.innerHTML =
                    `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري فحص المواعيد والتعارضات...' : 'Validating dates and conflicts...'}`;

                try {
                    const res = await fetch('{{ route('ajax.teacher.recurring.preview') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();

                    if (res.status === 401) {
                        const sessionExpiredMsg = isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' :
                            'Your session has expired. Please log in again.';
                        if (previewSummary) previewSummary.textContent = sessionExpiredMsg;
                        if (conflictBadge) {
                            conflictBadge.className =
                                'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                            conflictBadge.innerHTML =
                                `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'جلسة منتهية' : 'Session Expired'}`;
                        }
                        showTeacherToast(sessionExpiredMsg, false);
                        setTimeout(() => window.location.href = '{{ route('login') }}', 1500);
                        return;
                    }

                    if (!res.ok || !data.success) {
                        const errMsg = data.message || 'Validation failed';
                        if (previewSummary) previewSummary.textContent = errMsg;
                        if (conflictBadge) {
                            conflictBadge.className =
                                'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                            conflictBadge.innerHTML =
                                `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'خطأ' : 'Error'}`;
                        }
                        return;
                    }

                    if (previewSummary) {
                        previewSummary.textContent = isArLocale ?
                            `إجمالي الحصص المتولدة: ${data.total_sessions} حصة` :
                            `Total Generated Sessions: ${data.total_sessions}`;
                    }

                    if (data.has_conflicts) {
                        conflictBadge.className =
                            'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 animate-pulse';
                        conflictBadge.innerHTML = isArLocale ?
                            '<i class="fa-solid fa-triangle-exclamation"></i> يوجد تعارض في المواعيد' :
                            '<i class="fa-solid fa-triangle-exclamation"></i> Schedule Conflicts Detected';
                        conflictWarning.classList.remove('hidden');
                        conflictWarning.innerHTML =
                            `<span><i class="fa-solid fa-triangle-exclamation"></i> ${isArLocale ? 'تنبيه: بعض الحصص المقترحة تتعارض مع حصص سابقة لنفس المعلم أو الطالب.' : 'Warning: Some proposed sessions conflict with existing schedules.'}</span>`;
                    } else {
                        conflictBadge.className =
                            'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                        conflictBadge.innerHTML = isArLocale ?
                            '<i class="fa-solid fa-check"></i> المواعيد متاحة بدون تعارض' :
                            '<i class="fa-solid fa-check"></i> All Slots Available';
                        conflictWarning.classList.add('hidden');
                    }

                    if (tableBody) {
                        tableBody.innerHTML = (data.dates || []).map(d => `
                    <tr class="hover:bg-white/80 dark:hover:bg-slate-800/60 ${d.has_conflict ? 'bg-rose-50/60 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200' : ''}">
                        <td class="py-1.5 px-2 font-bold">${d.date}</td>
                        <td class="py-1.5 px-2 text-slate-500">${d.day_name}</td>
                        <td class="py-1.5 px-2">${d.start_time} - ${d.end_time}</td>
                        <td class="py-1.5 px-2 font-bold ${d.has_conflict ? 'text-rose-600' : 'text-emerald-600'}">
                            ${d.has_conflict ? '<i class="fa-solid fa-triangle-exclamation"></i> ' + (isArLocale ? 'تعارض' : 'Conflict') : '<i class="fa-solid fa-check"></i> ' + (isArLocale ? 'متاح' : 'Available')}
                        </td>
                    </tr>
                `).join('');
                    }

                } catch (err) {
                    if (previewSummary) previewSummary.textContent = 'Connection error';
                }
            };

            window.openEditSessionOverrideModal = function(sessionId, titleOrEl, scheduledAt, duration, meetingLink,
                notes) {
                let title = '';
                if (titleOrEl && typeof titleOrEl === 'object' && titleOrEl.nodeType) {
                    title = titleOrEl.getAttribute('data-title') || '';
                    scheduledAt = titleOrEl.getAttribute('data-scheduled-at') || '';
                    duration = titleOrEl.getAttribute('data-duration') || 60;
                    meetingLink = titleOrEl.getAttribute('data-meeting-link') || '';
                    notes = titleOrEl.getAttribute('data-notes') || '';
                } else if (typeof titleOrEl === 'string') {
                    title = titleOrEl;
                }
                document.getElementById('overrideSessionId').value = sessionId;
                document.getElementById('overrideTitle').value = title || '';
                document.getElementById('overrideDateTime').value = scheduledAt || '';
                document.getElementById('overrideDuration').value = duration || 60;
                document.getElementById('overrideMeetingLink').value = meetingLink || '';
                document.getElementById('overrideReason').value = '';
                window.openModal('editSessionOverrideModal');
            };

            window.confirmCancelSession = function(sessionId) {
                document.getElementById('cancelSessionId').value = sessionId;
                document.getElementById('cancelReasonInput').value = '';
                openModal('cancelSessionModal');
            };

            // ── Form Handlers ────────────────────────────────────────────────────────
            bindAjaxForm('createSessionForm', function(data) {
                showTeacherToast(data.message, true);
                closeModal('createSessionModal');
                setTimeout(() => location.reload(), 900);
            });

            const recForm = document.getElementById('recurringScheduleForm');
            if (recForm) {
                recForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('saveRecurringBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري إنشاء الجدول والحصص...' : 'Generating sessions...'}`;
                    }

                    try {
                        const formData = new FormData(recForm);
                        const res = await fetch('{{ route('ajax.teacher.recurring.create') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        if (res.status === 401) {
                            showTeacherToast(isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' :
                                'Your session has expired. Please log in again.', false);
                            setTimeout(() => window.location.href = '{{ route('login') }}', 1500);
                            return;
                        }
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('recurringScheduleModal');
                            setTimeout(() => location.reload(), 900);
                        } else {
                            showTeacherToast(data.message || 'Failed to create schedule', false);
                        }
                    } catch (err) {
                        showTeacherToast('Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `${isArLocale ? 'إنشاء جدول الحصص المتكرر' : 'Create Recurring Schedule'} &rarr;`;
                        }
                    }
                });
            }

            const overrideForm = document.getElementById('editSessionOverrideForm');
            if (overrideForm) {
                overrideForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const sId = document.getElementById('overrideSessionId').value;
                    const submitBtn = document.getElementById('saveOverrideBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
                    }

                    try {
                        const formData = new FormData(overrideForm);
                        const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sId}/override`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('editSessionOverrideModal');
                            setTimeout(() => location.reload(), 900);
                        } else {
                            showTeacherToast(data.message || 'Failed to update session', false);
                        }
                    } catch (err) {
                        showTeacherToast('Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `${isArLocale ? 'حفظ التغييرات' : 'Save Changes'} &rarr;`;
                        }
                    }
                });
            }

            const cancelForm = document.getElementById('cancelSessionForm');
            if (cancelForm) {
                cancelForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const sId = document.getElementById('cancelSessionId').value;
                    const formData = new FormData(cancelForm);

                    try {
                        const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sId}/cancel`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('cancelSessionModal');
                            setTimeout(() => location.reload(), 900);
                        } else {
                            showTeacherToast(data.message || 'Failed to cancel session', false);
                        }
                    } catch (err) {
                        showTeacherToast('Connection error', false);
                    }
                });
            }

            bindAjaxForm('createAssignmentForm', function(data) {
                showTeacherToast(data.message, true);
                closeModal('createAssignmentModal');
                setTimeout(() => location.reload(), 900);
            });

            const editAssignForm = document.getElementById('editAssignmentForm');
            if (editAssignForm) {
                editAssignForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const aId = document.getElementById('editAssignmentId').value;
                    const submitBtn = document.getElementById('editAssignmentSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<i class="fa-solid fa-spinner animate-spin"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
                    }
                    try {
                        const formData = new FormData(editAssignForm);
                        const res = await fetch(
                        `${appBaseUrl}/ajax/teacher/assignments/${aId}/update`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('editAssignmentModal');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showTeacherToast(data.message || (isArLocale ? 'فشل تحديث الواجب' :
                                'Failed to update assignment'), false);
                        }
                    } catch (err) {
                        showTeacherToast(isArLocale ? 'خطأ في الاتصال بالخادم' : 'Connection error',
                            false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-floppy-disk text-xs"></i> <span>${isArLocale ? 'حفظ التغييرات' : 'Save Changes'}</span>`;
                        }
                    }
                });
            }

            const editRecForm = document.getElementById('editRecurringScheduleForm');
            if (editRecForm) {
                editRecForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const sId = document.getElementById('editRecScheduleId').value;
                    const submitBtn = document.getElementById('editRecSubmitBtn') || document
                        .getElementById('saveEditRecurringBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i>`;
                    }
                    try {
                        const formData = new FormData(editRecForm);
                        const res = await fetch(
                            `${appBaseUrl}/ajax/teacher/recurring-schedules/${sId}/update`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                        const data = await res.json();
                        if (data.success) {
                            showTeacherToast(data.message, true);
                            if (typeof window.closeScheduleModal === 'function') {
                                window.closeScheduleModal('editRecurringScheduleModal');
                            } else {
                                closeModal('editRecurringScheduleModal');
                            }
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showTeacherToast(data.message || (isArLocale ? 'فشل تعديل الجدول الدوري' :
                                'Failed to update schedule'), false);
                        }
                    } catch (err) {
                        showTeacherToast(isArLocale ? 'خطأ في الاتصال' : 'Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-floppy-disk"></i> <span>${isArLocale ? 'حفظ التعديلات' : 'Save Changes'}</span>`;
                        }
                    }
                });
            }

            bindAjaxForm('meetingLinkForm', function(data) {
                showTeacherToast(data.message, true);
                closeModal('meetingLinkModal');
                setTimeout(() => location.reload(), 900);
            });

            bindAjaxForm('rescheduleForm', function(data) {
                showTeacherToast(data.message, true);
                closeModal('rescheduleModal');
                setTimeout(() => location.reload(), 900);
            });

            bindAjaxForm('gradeForm', function(data) {
                showTeacherToast(data.message, true);
                closeModal('gradeModal');
                setTimeout(() => location.reload(), 900);
            });

            const attForm = document.getElementById('attendanceForm');
            if (attForm) {
                attForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const sessionId = document.getElementById('attendanceSessionId').value;
                    const submitBtn = document.getElementById('saveAttendanceBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري حفظ كشف الحضور...' : 'Saving sheet...'}`;
                    }

                    try {
                        const formData = new FormData(attForm);
                        const res = await fetch(
                            `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                        const data = await res.json();

                        if (data.success) {
                            showTeacherToast(data.message, true);
                            closeModal('attendanceModal');

                            // Dynamically update session card on the page
                            const card = document.getElementById(`attSessionCard_${sessionId}`);
                            if (card && data.stats) {
                                card.setAttribute('data-recorded', '1');
                                card.setAttribute('data-status', 'recorded');

                                const badgeEl = document.getElementById(`attCardBadge_${sessionId}`);
                                if (badgeEl) {
                                    badgeEl.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                    <span>${isArLocale ? 'تم الرصد' : 'Recorded'}</span>
                                    <span class="font-extrabold">(${data.stats.present}/${data.stats.total})</span>
                                </span>
                            `;
                                }

                                const statsEl = document.getElementById(`attCardStats_${sessionId}`);
                                if (statsEl) {
                                    const presentEl = statsEl.querySelector('.att-stat-present');
                                    if (presentEl) presentEl.textContent = data.stats.present;
                                    const absentEl = statsEl.querySelector('.att-stat-absent');
                                    if (absentEl) absentEl.textContent = data.stats.absent;
                                    const rateEl = statsEl.querySelector('.att-stat-rate');
                                    if (rateEl) {
                                        rateEl.textContent = `${data.stats.rate}%`;
                                        rateEl.className =
                                            `font-black text-sm att-stat-rate ${data.stats.rate >= 80 ? 'text-emerald-600' : 'text-amber-600'}`;
                                    }
                                }

                                const btnLabel = document.getElementById(
                                    `attCardBtnLabel_${sessionId}`);
                                if (btnLabel) {
                                    btnLabel.textContent = isArLocale ? 'مراجعة وتعديل الكشف' :
                                        'Review & Edit';
                                }
                            }

                            // Update Table View if present
                            const tableBadge = document.getElementById(`attTableBadge_${sessionId}`);
                            if (tableBadge && data.stats) {
                                tableBadge.innerHTML = `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                <span>${isArLocale ? 'تم الرصد' : 'Recorded'}</span>
                            </span>
                        `;
                            }
                            const tableRate = document.getElementById(`attTableRate_${sessionId}`);
                            if (tableRate && data.stats) {
                                tableRate.textContent = `${data.stats.rate}%`;
                            }

                            // Re-apply filters so status filter reflects instantly
                            applyAttendanceFilters();
                        } else {
                            showTeacherToast(data.message || (isArLocale ? 'فشل حفظ كشف الحضور' :
                                'Failed to save attendance'), false);
                        }
                    } catch (err) {
                        showTeacherToast('Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-check"></i> <span>${isArLocale ? 'حفظ كشف الحضور' : 'Save Attendance Sheet'}</span>`;
                        }
                    }
                });
            }
            // ════════════════════════════════════════════════════════════════
            // SCHEDULES TAB – Portal-Embedded Schedule Manager
            // ════════════════════════════════════════════════════════════════

            // Open / close schedule modals
            window.openScheduleModal = function(modalId) {
                window.openModal(modalId);
            };

            window.closeScheduleModal = function(modalId) {
                window.closeModal(modalId);
            };

            // Close modals on backdrop click
            ['singleSessionModal', 'recurringSchedulePortalModal'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('click', function(e) {
                        if (e.target === this) window.closeScheduleModal(id);
                    });
                }
            });

            // ── Fetch enrolled students for a course ────────────────────────
            window.fetchCourseStudents = async function(courseId) {
                const placeholder = document.getElementById('recPortalStudentsPlaceholder');
                const list = document.getElementById('recPortalStudentsList');
                if (!list || !placeholder) return;
                if (!courseId) {
                    placeholder.textContent = isArLocale ? 'اختر المقرر لتحميل الطلاب' :
                        'Select a course to load enrolled students';
                    placeholder.classList.remove('hidden');
                    list.classList.add('hidden');
                    list.innerHTML = '';
                    return;
                }
                placeholder.textContent = isArLocale ? 'جاري تحميل الطلاب...' : 'Loading students...';
                placeholder.classList.remove('hidden');
                list.classList.add('hidden');
                try {
                    const res = await fetch(`/ajax/teacher/courses/${courseId}/students`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success && data.students.length > 0) {
                        list.innerHTML = data.students.map(s =>
                            `<div class="flex items-center gap-2 py-1"><span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold">${s.name.charAt(0)}</span><span class="text-slate-800 font-semibold">${s.name}</span></div>`
                        ).join('');
                        placeholder.classList.add('hidden');
                        list.classList.remove('hidden');
                    } else {
                        placeholder.textContent = isArLocale ? 'لا يوجد طلاب مسجلون في هذا المقرر' :
                            'No enrolled students found for this course';
                    }
                } catch (e) {
                    placeholder.textContent = isArLocale ? 'فشل تحميل الطلاب' : 'Failed to load students';
                }
            };

            // ── Auto-calculate end date based on pattern ────────────────────
            window.autoCalcEndDate = function() {
                const startInput = document.getElementById('recPortalStartDate');
                const endInput = document.getElementById('recPortalEndDate');
                const typeSelect = document.getElementById('recPortalType');
                if (!startInput || !endInput || !startInput.value) return;
                const start = new Date(startInput.value);
                const type = typeSelect ? typeSelect.value : 'weekly';
                let end = new Date(start);
                if (type === 'weekly') end.setDate(end.getDate() + 28);
                else if (type === 'monthly') end.setMonth(end.getMonth() + 1);
                else if (type === 'multi_month') end.setMonth(end.getMonth() + 3);
                else if (type === 'yearly') end.setMonth(end.getMonth() + 9);
                endInput.value = end.toISOString().split('T')[0];
            };

            // ── Handle recurrence type toggle ───────────────────────────────
            window.handlePortalRecurrenceType = function(type) {
                const daysContainer = document.getElementById('recPortalDaysContainer');
                if (daysContainer) {
                    if (type === 'monthly') {
                        daysContainer.classList.add('hidden');
                    } else {
                        daysContainer.classList.remove('hidden');
                    }
                }
                // Recalculate end date when pattern changes
                window.autoCalcEndDate();
            };

            // ── Preview recurring schedule ───────────────────────────────────
            window.previewPortalSchedule = async function() {
                const form = document.getElementById('recurringSchedulePortalForm');
                if (!form) return;
                const formData = new FormData(form);
                const previewContainer = document.getElementById('recPortalPreviewContainer');
                const previewSummary = document.getElementById('recPortalPreviewSummary');
                const conflictWarning = document.getElementById('recPortalConflictWarning');
                const conflictText = document.getElementById('recPortalConflictText');
                const previewList = document.getElementById('recPortalPreviewList');
                if (previewContainer) previewContainer.classList.remove('hidden');
                if (previewSummary) previewSummary.innerHTML =
                    `<i class="fa-solid fa-spinner animate-spin"></i> ${isArLocale ? 'جاري الفحص...' : 'Checking dates and conflicts...'}`;
                try {
                    const res = await fetch('{{ route('ajax.teacher.recurring.preview') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const total = data.total_sessions || 0;
                        const hasConflicts = data.has_conflicts || false;
                        if (previewSummary) previewSummary.innerHTML =
                            `<i class="fa-solid fa-calendar-check text-indigo-600"></i> ${total} ${isArLocale ? 'حصة ستُنشأ' : 'sessions will be created'}`;
                        if (conflictWarning && conflictText) {
                            if (hasConflicts) {
                                conflictText.textContent = isArLocale ?
                                    'تحذير: بعض المواعيد قد تتعارض مع جلسات موجودة' :
                                    'Warning: Some dates may conflict with existing sessions';
                                conflictWarning.classList.remove('hidden');
                            } else {
                                conflictWarning.classList.add('hidden');
                            }
                        }
                        if (previewList && data.dates) {
                            previewList.innerHTML = data.dates.slice(0, 10).map(d => {
                                const conflictClass = d.has_conflict ?
                                    'text-amber-700 bg-amber-50 border-amber-200' :
                                    'text-slate-700 bg-white border-slate-200';
                                return `<div class="flex items-center gap-2 p-2 rounded-lg border ${conflictClass} text-xs font-mono">
                                        <i class="fa-solid fa-calendar text-indigo-400"></i>
                                        <span>${d.date || d.scheduled_at || ''}</span>
                                        ${d.has_conflict ? '<span class="ms-auto text-amber-600 font-bold">⚠ Conflict</span>' : ''}
                                    </div>`;
                            }).join('');
                            if (data.dates.length > 10) {
                                previewList.innerHTML +=
                                    `<p class="text-[10px] font-mono text-slate-400 text-center">+ ${data.dates.length - 10} ${isArLocale ? 'أكثر' : 'more'}</p>`;
                            }
                        }
                    } else {
                        if (previewSummary) previewSummary.innerHTML =
                            `<i class="fa-solid fa-triangle-exclamation text-red-500"></i> ${data.message || (isArLocale ? 'فشل المعاينة' : 'Preview failed')}`;
                    }
                } catch (e) {
                    if (previewSummary) previewSummary.innerHTML =
                        `<i class="fa-solid fa-triangle-exclamation text-red-500"></i> ${isArLocale ? 'خطأ في الاتصال' : 'Connection error'}`;
                }
            };

            // ── Single Session Form Submit ───────────────────────────────────
            const singleSessionPortalForm = document.getElementById('singleSessionPortalForm');
            if (singleSessionPortalForm) {
                singleSessionPortalForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('singleSessionSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i>`;
                    }
                    try {
                        const res = await fetch('{{ route('ajax.teacher.sessions.create') }}', {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.closeScheduleModal('singleSessionModal');
                            singleSessionPortalForm.reset();
                            showTeacherToast(data.message || (isArLocale ? 'تم إنشاء الجلسة بنجاح!' :
                                'Session created successfully!'), true);
                        } else {
                            const msg = data.message || (data.errors ? Object.values(data.errors).flat()
                                .join(', ') : (isArLocale ? 'فشل إنشاء الجلسة' :
                                    'Failed to create session'));
                            showTeacherToast(msg, false);
                        }
                    } catch (e) {
                        showTeacherToast(isArLocale ? 'خطأ في الاتصال' : 'Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-calendar-plus"></i> ${isArLocale ? 'إنشاء الجلسة' : 'Create Session'}`;
                        }
                    }
                });
            }

            // ── Recurring Schedule Form Submit ──────────────────────────────
            const recurringSchedulePortalForm = document.getElementById('recurringSchedulePortalForm');
            if (recurringSchedulePortalForm) {
                recurringSchedulePortalForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('recPortalSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i>`;
                    }
                    try {
                        const res = await fetch('{{ route('ajax.teacher.recurring.create') }}', {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success || res.status === 201) {
                            window.closeScheduleModal('recurringSchedulePortalModal');
                            recurringSchedulePortalForm.reset();
                            document.getElementById('recPortalPreviewContainer')?.classList.add(
                                'hidden');
                            showTeacherToast(data.message || (isArLocale ?
                                'تم إنشاء الجدول الدوري بنجاح!' :
                                'Recurring schedule created successfully!'), true);
                            // Reload after short delay so user sees the new schedule in the list
                            setTimeout(() => {
                                window.location.href = window.location.pathname +
                                    '?tab=schedules';
                            }, 1500);
                        } else {
                            const msg = data.message || (data.errors ? Object.values(data.errors).flat()
                                .join(', ') : (isArLocale ? 'فشل إنشاء الجدول' :
                                    'Failed to create schedule'));
                            showTeacherToast(msg, false);
                        }
                    } catch (e) {
                        showTeacherToast(isArLocale ? 'خطأ في الاتصال' : 'Connection error', false);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-calendar-check"></i> ${isArLocale ? 'إنشاء الجدول' : 'Create Schedule'}`;
                        }
                    }
                });
            }

        });
    </script>
@endpush
