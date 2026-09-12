@extends('layouts.portal-panel')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $todayDateStr = \Carbon\Carbon::today()->format('l, F j, Y');
    $activeTabKey = in_array($activeTab ?? 'overview', ['overview', 'sessions', 'assignments', 'attendance', 'students', 'notifications']) ? ($activeTab ?? 'overview') : 'overview';
@endphp

<div class="space-y-8" id="teacher-portal-root" data-initial-student="{{ $initialStudentId ?? '' }}">

    {{-- Executive Header & Faculty Greeting Banner --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-700/60">
        <div class="space-y-3 relative z-10 max-w-2xl">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 shadow-xs">
                    <i class="fa-solid fa-chalkboard-user"></i> {{ $teacherProfile->title ?: __('Faculty Instructor') }}
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                    <i class="fa-solid fa-star text-amber-400"></i> {{ number_format($teacherProfile->rating_avg ?: 4.9, 1) }} {{ __('Rating') }}
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <i class="fa-solid fa-book-open"></i> {{ $courses->count() }} {{ __('Active Courses') }}
                </span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                {{ __('Welcome back') }}, <span class="text-teal-400">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm font-medium leading-relaxed max-w-xl">
                {{ __('Manage educational cohorts, monitor individual student performance, review homework submissions, and track attendance records.') }}
            </p>
        </div>

        {{-- Quick Action Launchers in Header --}}
        <div class="relative z-10 flex flex-wrap items-center gap-2.5 shrink-0">
            <button type="button" onclick="switchTeacherTab('students')" class="btn-lift px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl border border-slate-600 shadow-md flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-graduation-cap"></i></span> {{ __('My Students') }}
            </button>
            <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-plus"></i></span> {{ __('Schedule Session') }}
            </button>
            <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-emerald-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-pen-to-square"></i></span> {{ __('Publish Assignment') }}
            </button>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    {{-- Toast Alert Container --}}
    <div id="teacherToastAlert" class="hidden p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md"></div>

    {{-- KPI Statistics Grid (Animated Count-Up) --}}
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
        {{-- KPI 1: Today Sessions --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Today Sessions') }}</span>
                <span class="text-lg"><i class="fa-solid fa-calendar-days"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $todaySessionsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Scheduled today') }}</p>
        </div>

        {{-- KPI 2: Upcoming --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Upcoming') }}</span>
                <span class="text-lg"><i class="fa-solid fa-hourglass-half"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-blue-600 js-counter" data-target="{{ $upcomingSessionsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Future cohorts') }}</p>
        </div>

        {{-- KPI 3: Students --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-teal-400" onclick="switchTeacherTab('students')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('My Students') }}</span>
                <span class="text-lg"><i class="fa-solid fa-graduation-cap"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 js-counter" data-target="{{ $assignedStudentsCount }}">0</p>
            <p class="text-[11px] text-teal-600 font-semibold flex items-center gap-1">{{ __('View roster →') }}</p>
        </div>

        {{-- KPI 4: Pending Assignments --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-orange-400" onclick="switchTeacherTab('assignments')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Need Grading') }}</span>
                <span class="text-lg"><i class="fa-solid fa-pen-to-square"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-orange-500 js-counter" data-target="{{ $pendingAssignmentsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Submissions queue') }}</p>
        </div>

        {{-- KPI 5: Total Submissions --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Submissions') }}</span>
                <span class="text-lg"><i class="fa-solid fa-chart-column"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $submittedAssignmentsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Total handled') }}</p>
        </div>

        {{-- KPI 6: Attendance Rate --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Attendance Rate') }}</span>
                <span class="text-lg"><i class="fa-solid fa-circle-check text-emerald-500"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600"><span class="js-counter" data-target="{{ $attendanceRate }}">0</span>%</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Historical sessions') }}</p>
        </div>
    </div>

    {{-- Teacher Navigation Tabs --}}
    <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto scrollbar-thin">
        <button type="button" onclick="switchTeacherTab('overview')" id="tab-btn-overview" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'overview' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-chart-column"></i> {{ __('Overview & Today') }}
        </button>
        <button type="button" onclick="switchTeacherTab('students')" id="tab-btn-students" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'students' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-graduation-cap"></i> {{ __('My Students') }} ({{ $assignedStudentsCount }})
        </button>
        <button type="button" onclick="switchTeacherTab('sessions')" id="tab-btn-sessions" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'sessions' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-calendar-days"></i> {{ __('Sessions & Streams') }}
        </button>
        <button type="button" onclick="switchTeacherTab('assignments')" id="tab-btn-assignments" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'assignments' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
            <i class="fa-solid fa-pen-to-square"></i> {{ __('Assignments & Quizzes') }}
            @if($pendingAssignmentsCount > 0)
                <span class="ms-1.5 px-2 py-0.5 text-[10px] bg-orange-500 text-white rounded-full font-mono font-bold">{{ $pendingAssignmentsCount }}</span>
            @endif
        </button>
        <button type="button" onclick="switchTeacherTab('attendance')" id="tab-btn-attendance" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'attendance' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            <i class="fa-solid fa-clipboard-list"></i> {{ __('Attendance Tracker') }}
        </button>
        <button type="button" onclick="switchTeacherTab('notifications')" id="tab-btn-notifications" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'notifications' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
            <i class="fa-solid fa-bell"></i> {{ __('Notifications') }}
            @if($unreadNotifCount > 0)
                <span class="ms-1.5 px-2 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-mono font-bold">{{ $unreadNotifCount }}</span>
            @endif
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: OVERVIEW & TODAY'S SESSIONS                                       --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-overview" class="teacher-tab-content {{ $activeTabKey === 'overview' ? '' : 'hidden' }} space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Today's Live Sessions Card --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                                <span><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i></span> {{ __('Today\'s Teaching Sessions') }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ $todayDateStr }}</p>
                        </div>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200">
                            {{ $todaySessions->count() }} {{ __('Sessions Today') }}
                        </span>
                    </div>

                    @if($todaySessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($todaySessions as $session)
                                <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 hover:border-teal-400 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2.5 py-0.5 text-[10px] font-mono font-extrabold uppercase rounded-full bg-teal-100 text-teal-800">
                                                {{ $session->course?->title ?: __('Course Session') }}
                                            </span>
                                            <span class="text-xs font-mono text-slate-500">
                                                <i class="fa-solid fa-stopwatch"></i> {{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : 'Scheduled' }} ({{ $session->duration_minutes }}m)
                                            </span>
                                        </div>
                                        <h3 class="font-heading font-extrabold text-base text-slate-900 truncate">
                                            {{ $session->title ?: __('Interactive Teaching Session') }}
                                        </h3>
                                        <p class="text-xs text-slate-500 font-mono">
                                            {{ __('Cohort') }}: {{ $session->subject?->name ?: __('General Curriculum') }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                                        @if($session->meeting_link)
                                            <a href="{{ $session->meeting_link }}" target="_blank" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                                                <span><i class="fa-solid fa-video"></i></span> {{ __('Join / Broadcast') }}
                                            </a>
                                        @else
                                            <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '')" class="btn-lift px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                                <i class="fa-solid fa-link"></i> {{ __('Add Link') }}
                                            </button>
                                        @endif
                                        <button type="button" onclick="openAttendanceModal({{ $session->id }}, '{{ addslashes($session->title) }}')" class="btn-lift px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200">
                                            <i class="fa-solid fa-clipboard-list"></i> {{ __('Attendance') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-dashed border-slate-200 space-y-3">
                            <span class="text-3xl"><i class="fa-solid fa-mug-hot"></i></span>
                            <p class="text-sm font-semibold text-slate-700">{{ __('No teaching sessions scheduled for today.') }}</p>
                            <button type="button" onclick="openCreateSessionModal()" class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline">
                                + {{ __('Schedule a live session now') }}
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Pending Grading Queue --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="font-heading text-xl font-black text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-pen-to-square"></i></span> {{ __('Pending Grading Queue') }}
                        </h2>
                        <span class="px-3 py-1 bg-orange-50 text-orange-700 font-mono text-xs font-bold rounded-full border border-orange-200">
                            {{ $pendingSubmissions->count() }} {{ __('Needs Review') }}
                        </span>
                    </div>

                    @if($pendingSubmissions->count() > 0)
                        <div class="divide-y divide-slate-100">
                            @foreach($pendingSubmissions->take(5) as $sub)
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 truncate">
                                            {{ $sub->studentUser?->name ?: __('Student') }}
                                        </h4>
                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $sub->assignment?->title ?: __('Assignment') }} • <span class="font-mono text-[11px]">{{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}</span>
                                        </p>
                                    </div>
                                    <button type="button" onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->studentUser?->name) }}', '{{ addslashes($sub->assignment?->title) }}', '{{ $sub->score }}', '{{ addslashes($sub->evaluation_notes) }}')" class="btn-lift px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs shrink-0">
                                        <i class="fa-solid fa-pen-nib"></i> {{ __('Grade Now') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                            <p class="text-xs font-semibold text-slate-600">{{ __('Great job! All student homework submissions are graded.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar Overview Column: Quick Student Roster Preview & Courses --}}
            <div class="space-y-6">
                {{-- Quick Students Roster Preview --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-heading font-black text-lg text-slate-900">{{ __('Recent Students') }}</h3>
                        <button type="button" onclick="switchTeacherTab('students')" class="text-xs font-bold text-teal-600 hover:underline">
                            {{ __('View All') }} &rarr;
                        </button>
                    </div>

                    @if($assignedStudents->count() > 0)
                        <div class="space-y-3">
                            @foreach($assignedStudents->take(5) as $st)
                                <div class="p-3 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 flex items-center justify-between gap-3 hover:border-teal-400 transition-all cursor-pointer" onclick="openStudentDetailsModal({{ $st->user_id }})">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-black text-xs flex items-center justify-center shrink-0">
                                            {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ $st->user?->name }}</p>
                                            <p class="text-[10px] font-mono text-slate-500 truncate">{{ $st->gradeLevel?->name ?: __('Secondary') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-end shrink-0 font-mono text-[11px]">
                                        <span class="font-bold text-emerald-600">{{ $st->attendance_rate }}%</span>
                                        <span class="block text-[9px] text-slate-400">{{ __('Att.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-4">{{ __('No students assigned yet.') }}</p>
                    @endif
                </div>

                {{-- Active Teaching Courses --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xl space-y-4">
                    <h3 class="font-heading font-black text-lg text-slate-900 border-b border-slate-100 pb-3">{{ __('Your Active Courses') }}</h3>
                    @if($courses->count() > 0)
                        <div class="space-y-2.5">
                            @foreach($courses as $c)
                                @php
                                    $sessionCount = $c->sessions->count();
                                    $sessionLabel = $isAr ? ($sessionCount == 1 ? 'حصة' : ($sessionCount == 2 ? 'حصتان' : ($sessionCount <= 10 ? 'حصص' : 'حصة'))) : ($sessionCount == 1 ? 'Session' : 'Sessions');
                                @endphp
                                <div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 hover:border-teal-400 hover:bg-white transition-all flex items-center justify-between gap-3 group">
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <h4 class="font-heading font-black text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-teal-700 transition-colors">
                                            {{ $c->title }}
                                        </h4>
                                        <p class="text-[11px] font-mono text-slate-500 truncate">
                                            {{ $c->subject?->name }} • {{ $c->gradeLevel?->name }}
                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-teal-800 border border-teal-200/80 text-xs font-mono font-extrabold rounded-xl shadow-2xs whitespace-nowrap">
                                            <span><i class="fa-solid fa-book-open"></i></span> {{ $sessionCount }} {{ $sessionLabel }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 text-center py-4">{{ __('No active courses configured.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: MY STUDENTS (DEDICATED ROSTER & SEARCH)                          --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-students" class="teacher-tab-content {{ $activeTabKey === 'students' ? '' : 'hidden' }} space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            {{-- Tab Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-graduation-cap"></i></span> {{ __('app.teacher.my_students') }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">
                        {{ __('Search, filter, and inspect detailed educational profiles for all enrolled learners in your courses.') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 bg-teal-50 text-teal-800 text-xs font-mono font-bold rounded-xl border border-teal-200">
                        {{ $assignedStudents->count() }} {{ __('Enrolled Students') }}
                    </span>
                </div>
            </div>

            {{-- Multi-Faceted Student Filter & Search Bar --}}
            <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    {{-- Live Search Input --}}
                    <div class="lg:col-span-2 relative">
                        <input 
                            type="text" 
                            id="studentSearchInput" 
                            placeholder="{{ __('app.teacher.search_placeholder') }}" 
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pe-8"
                        >
                        <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                    </div>

                    {{-- Filter by Course --}}
                    <div>
                        <select id="studentCourseFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">{{ __('app.teacher.all_courses') }}</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter by Grade Level --}}
                    <div>
                        <select id="studentGradeFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">{{ __('app.teacher.all_grades') }}</option>
                            @foreach($gradeLevels ?? [] as $gl)
                                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter by Attendance Health --}}
                    <div>
                        <select id="studentAttendanceFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value="">{{ __('app.teacher.all_attendance') }}</option>
                            <option value="good">{{ __('app.teacher.attendance_good') }}</option>
                            <option value="risk">{{ __('app.teacher.attendance_risk') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Results Count & Active Filter Reset --}}
                <div class="flex items-center justify-between text-xs font-mono text-slate-500 pt-1">
                    <span id="studentFilterCountText">{{ __('Showing all assigned students') }}</span>
                    <button type="button" onclick="resetStudentFilters()" class="text-teal-600 font-bold hover:underline cursor-pointer">
                        ↺ {{ __('Reset Filters') }}
                    </button>
                </div>
            </div>

            {{-- Students Roster Cards Grid --}}
            @if($assignedStudents->count() > 0)
                <div id="studentsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($assignedStudents as $st)
                        @php
                            $courseIdsList = implode(',', $st->enrolled_course_ids ?? []);
                            $gradeId = $st->grade_level_id ?? 0;
                            $attRate = (int) ($st->attendance_rate ?? 100);
                            $avgSc = $st->avg_score !== null ? (float) $st->avg_score : null;
                            $studentCode = 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <div 
                            class="student-roster-card bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 hover:border-teal-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-4"
                            data-name="{{ strtolower($st->user?->name ?: '') }}"
                            data-code="{{ strtolower($studentCode) }}"
                            data-school="{{ strtolower($st->school_name ?: '') }}"
                            data-courses="{{ $courseIdsList }}"
                            data-grade="{{ $gradeId }}"
                            data-attendance="{{ $attRate }}"
                            data-score="{{ $avgSc !== null ? $avgSc : -1 }}"
                        >
                            {{-- Card Top: Avatar, Name & Code --}}
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-base flex items-center justify-center shrink-0 shadow-sm border border-teal-300/40">
                                            {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-heading font-black text-sm text-slate-900 truncate">
                                                {{ $st->user?->name }}
                                            </h3>
                                            <span class="inline-block font-mono text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
                                                #{{ $studentCode }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full {{ $st->user?->status === \App\Enums\AccountStatus::APPROVED ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $st->user?->status === \App\Enums\AccountStatus::APPROVED ? __('Active') : __('Pending') }}
                                    </span>
                                </div>

                                {{-- Academic Meta: School & Grade --}}
                                <div class="text-xs font-mono text-slate-600 space-y-1 pt-1">
                                    <p class="truncate flex items-center gap-1.5">
                                        <span><i class="fa-solid fa-school"></i></span> {{ $st->school_name ?: __('Elite Academy') }}
                                    </p>
                                    <p class="truncate flex items-center gap-1.5 text-slate-500">
                                        <span><i class="fa-solid fa-graduation-cap"></i></span> {{ $st->gradeLevel?->name ?: __('Secondary Level') }}
                                    </p>
                                </div>

                                {{-- Course Badges --}}
                                @if(!empty($st->enrolled_courses))
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach(collect($st->enrolled_courses)->take(2) as $cMeta)
                                            <span class="text-[10px] font-mono font-bold bg-white text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 truncate max-w-[140px]">
                                                {{ $cMeta['title'] }}
                                            </span>
                                        @endforeach
                                        @if(count($st->enrolled_courses) > 2)
                                            <span class="text-[10px] font-mono text-slate-400 self-center">
                                                +{{ count($st->enrolled_courses) - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Card Bottom: KPIs & Action Button --}}
                            <div class="space-y-3 pt-3 border-t border-slate-200/80">
                                <div class="grid grid-cols-2 gap-2 text-center font-mono text-[11px]">
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Attendance') }}</span>
                                        <span class="font-extrabold {{ $attRate >= 80 ? 'text-emerald-600' : ($attRate >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $attRate }}%
                                        </span>
                                    </div>
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[9px] uppercase font-bold">{{ __('Avg Score') }}</span>
                                        <span class="font-extrabold {{ $avgSc !== null ? ($avgSc >= 70 ? 'text-teal-600' : 'text-rose-600') : 'text-slate-400' }}">
                                            {{ $avgSc !== null ? $avgSc . '%' : 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    onclick="openStudentDetailsModal({{ $st->user_id }})" 
                                    class="btn-lift w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all"
                                >
                                    <span><i class="fa-solid fa-graduation-cap"></i></span> {{ __('app.teacher.student_profile') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty Search Result State --}}
                <div id="studentEmptySearchState" class="hidden text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <span class="text-3xl"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <p class="text-sm font-semibold text-slate-700">{{ __('app.teacher.no_students_found') }}</p>
                    <button type="button" onclick="resetStudentFilters()" class="text-xs text-teal-600 font-bold hover:underline">
                        {{ __('app.teacher.all_courses') }}
                    </button>
                </div>
            @else
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700">{{ __('No students enrolled in your courses yet.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: SESSIONS & STREAMS                                                --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-sessions" class="teacher-tab-content {{ $activeTabKey === 'sessions' ? '' : 'hidden' }} space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-calendar-days"></i></span> {{ __('Live Teaching Schedule & Meeting Links') }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Manage your live sessions, recurring cohorts, update broadcast URLs, and reschedule classes.') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCreateRecurringModal()" class="btn-lift px-4 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span><i class="fa-solid fa-arrows-rotate"></i></span> {{ __('Create Recurring Schedule') }}
                    </button>
                    <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span><i class="fa-solid fa-plus"></i></span> {{ __('Schedule Single Session') }}
                    </button>
                </div>
            </div>

            @if($allSessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                <th class="py-3 px-4">{{ __('Session Details') }}</th>
                                <th class="py-3 px-4">{{ __('Course') }}</th>
                                <th class="py-3 px-4">{{ __('Date & Time') }}</th>
                                <th class="py-3 px-4">{{ __('Status') }}</th>
                                <th class="py-3 px-4 text-right rtl:text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($allSessions as $session)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $session->title ?: __('Live Class Session') }}</p>
                                                @if($session->recurring_schedule_id)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                        <span><i class="fa-solid fa-arrows-rotate"></i></span> {{ __('Recurring') }}
                                                    </span>
                                                @endif
                                                @if($session->is_override)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                        <span><i class="fa-solid fa-triangle-exclamation"></i></span> {{ __('Override') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-500 font-mono">{{ $session->duration_minutes }} {{ __('minutes') }}</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-xs font-semibold text-teal-700">
                                        {{ $session->course?->title ?: __('General Curriculum') }}
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                                        {{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : __('Not Scheduled') }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @php
                                            $statusText = match($session->status) {
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
                                        <span class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-full {{ in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($session->status === 'rescheduled' ? 'bg-amber-100 text-amber-800' : 'bg-teal-100 text-teal-700')) }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right rtl:text-left space-x-1 rtl:space-x-reverse whitespace-nowrap">
                                        <button type="button" onclick="openEditSessionOverrideModal({{ $session->id }}, '{{ addslashes($session->title) }}', '{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}', {{ $session->duration_minutes ?: 60 }}, '{{ addslashes($session->meeting_link ?? '') }}', '{{ addslashes($session->teacher_notes ?? '') }}')" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold rounded-lg transition-colors cursor-pointer" title="{{ __('Edit or Override Session') }}">
                                            <i class="fa-solid fa-pen"></i> {{ __('Edit Scope') }}
                                        </button>
                                        <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link ?? '') }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            <i class="fa-solid fa-link"></i> {{ __('Link') }}
                                        </button>
                                        <button type="button" onclick="openRescheduleModal({{ $session->id }}, '{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            <i class="fa-solid fa-calendar-days"></i> {{ __('Reschedule') }}
                                        </button>
                                        @if(!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                            <button type="button" onclick="confirmCancelSession({{ $session->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                                <i class="fa-solid fa-circle-xmark text-rose-500"></i> {{ __('Cancel') }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    {{ $allSessions->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <p class="text-sm font-semibold text-slate-700">{{ __('No sessions created yet.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: ASSIGNMENTS & SUBMISSIONS                                         --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-assignments" class="teacher-tab-content {{ $activeTabKey === 'assignments' ? '' : 'hidden' }} space-y-8">
        {{-- Assignments Header & Publish Action --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Assignments & Homework Manager') }}</h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Publish new course assignments, interactive quizzes, and review student work.') }}</p>
                </div>
                <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                    + {{ __('Publish New Assignment') }}
                </button>
            </div>

            @if($assignments->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($assignments as $assignment)
                        @php
                            $subCount = $assignment->submissions->count();
                            $subLabel = $isAr ? ($subCount == 1 ? 'تسليم' : ($subCount == 2 ? 'تسليمان' : ($subCount <= 10 ? 'تسليمات' : 'تسليم'))) : ($subCount == 1 ? 'Submission' : 'Submissions');
                        @endphp
                        <div 
                            onclick="openAssignmentDetailsModal({{ $assignment->id }})"
                            class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-3 flex flex-col justify-between hover:border-teal-400 hover:bg-white hover:shadow-lg transition-all group shadow-xs cursor-pointer"
                        >
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <span class="font-mono font-bold text-teal-700 uppercase truncate flex-1 min-w-0">{{ $assignment->course?->title ?: __('Course') }}</span>
                                    <span class="px-2.5 py-1 bg-teal-50 text-teal-800 border border-teal-200/80 text-[11px] font-mono font-extrabold rounded-xl shrink-0 whitespace-nowrap shadow-2xs">
                                        <i class="fa-solid fa-pen-to-square"></i> {{ $subCount }} {{ $subLabel }}
                                    </span>
                                </div>
                                <h3 class="font-heading font-black text-base text-slate-900 leading-snug group-hover:text-teal-700 transition-colors">{{ $assignment->title }}</h3>
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $assignment->description ?: __('Homework assignment for student revision.') }}</p>
                            </div>
                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-mono text-slate-500">
                                <span class="truncate"><i class="fa-solid fa-calendar-days"></i> {{ __('Due') }}: {{ $assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline') }}</span>
                                <span class="font-extrabold text-slate-800 shrink-0 ms-2 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fa-solid fa-bullseye"></i> {{ $assignment->passing_score ?: 70 }}% {{ __('Pass') }}</span>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-teal-600 font-bold group-hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-info"></i> {{ __('View Details & Questions') }} &rarr;
                                </span>
                                @if($subCount > 0)
                                    <span class="text-emerald-600 font-bold font-mono text-[11px]"><i class="fa-solid fa-users"></i> {{ $subCount }} {{ $subLabel }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700">{{ __('No assignments created yet.') }}</p>
                </div>
            @endif
        </div>

        {{-- Submissions Table --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <h3 class="font-heading text-xl font-black text-slate-900">{{ __('All Student Submissions') }}</h3>

            @if($submissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                <th class="py-3 px-4">{{ __('Student') }}</th>
                                <th class="py-3 px-4">{{ __('Assignment') }}</th>
                                <th class="py-3 px-4">{{ __('Submitted At') }}</th>
                                <th class="py-3 px-4">{{ __('Grade / Score') }}</th>
                                <th class="py-3 px-4 text-right rtl:text-left">{{ __('Review Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($submissions as $sub)
                                @php
                                    $subVal = $sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->value : (is_object($sub->status) ? ($sub->status->value ?? '') : (string) $sub->status);
                                    $isReviewed = $subVal === 'reviewed';
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        {{ $sub->studentUser?->name ?: __('Student') }}
                                    </td>
                                    <td class="py-4 px-4 text-xs font-medium text-slate-700">
                                        {{ $sub->assignment?->title ?: __('Assignment') }}
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                        {{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Draft' }}
                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs">
                                        @if($sub->score !== null)
                                            <span class="font-extrabold text-emerald-600">{{ number_format($sub->score, 1) }}%</span>
                                        @else
                                            <span class="text-orange-500 italic">{{ __('Pending Grade') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right rtl:text-left">
                                        <button type="button" onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->studentUser?->name) }}', '{{ addslashes($sub->assignment?->title) }}', '{{ $sub->score }}', '{{ addslashes($sub->evaluation_notes) }}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs">
                                            <i class="fa-solid fa-magnifying-glass"></i> {{ $isReviewed ? __('Review & Grade') : __('Review Submission') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700">{{ __('No student submissions yet.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 5: ATTENDANCE TRACKER                                                --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-attendance" class="teacher-tab-content {{ $activeTabKey === 'attendance' ? '' : 'hidden' }} space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div>
                <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Attendance & Student Check-In') }}</h2>
                <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Select a session to record attendance for enrolled cohort learners.') }}</p>
            </div>

            @if($todaySessions->count() > 0 || $allSessions->count() > 0)
                <div class="space-y-4">
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">{{ __('Select Teaching Session to Mark Attendance') }}</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($todaySessions->merge($allSessions->take(6))->unique('id') as $ses)
                            <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200 hover:border-teal-400 transition-all flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900">{{ $ses->title ?: __('Live Session') }}</h4>
                                    <p class="text-xs text-slate-500 font-mono">{{ $ses->effective_start_at ? $ses->effective_start_at->format('M d, Y h:i A') : '' }}</p>
                                </div>
                                <button type="button" onclick="openAttendanceModal({{ $ses->id }}, '{{ addslashes($ses->title) }}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs shrink-0">
                                    <i class="fa-solid fa-clipboard-list"></i> {{ __('Mark') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700">{{ __('No sessions available for attendance tracking.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 6: NOTIFICATIONS                                                     --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    <div id="teacher-tab-notifications" class="teacher-tab-content {{ $activeTabKey === 'notifications' ? '' : 'hidden' }} space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Notification Feed & Alerts') }}</h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Real-time session updates, assignment submissions, and student alerts.') }}</p>
                </div>
            </div>

            @if($userNotifications->count() > 0)
                <div class="space-y-3">
                    @foreach($userNotifications as $notif)
                        <div class="p-4 rounded-2xl border transition-colors flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-[#FAFAF9] border-slate-200/70' : 'bg-teal-50/80 border-teal-200 font-semibold' }}">
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900">{{ $notif->title }}</h4>
                                <p class="text-xs text-slate-600">{{ $notif->body }}</p>
                                <p class="text-[10px] font-mono text-slate-400">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-4 border-t border-slate-100">
                    {{ $userNotifications->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700">{{ __('You\'re all caught up! No new notifications.') }}</p>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: COMPREHENSIVE STUDENT EDUCATIONAL PROFILE (8 TABS)                    --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="studentProfileModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 max-h-[92vh] sm:max-h-[88vh] flex flex-col overflow-hidden relative">
        
        {{-- Modal Top Bar / Header --}}
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 to-teal-950 text-white flex items-start justify-between gap-4 shrink-0">
            <div class="flex items-center gap-4 min-w-0">
                <div id="spModalAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xl flex items-center justify-center shrink-0 border border-teal-300/40 shadow-md">
                    S
                </div>
                <div class="min-w-0 space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 id="spModalName" class="font-heading font-black text-xl sm:text-2xl text-white truncate">
                            {{ __('Student Profile') }}
                        </h3>
                        <span id="spModalCode" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                            #STU-00000
                        </span>
                    </div>
                    <p id="spModalMeta" class="text-xs font-mono text-slate-300 truncate">
                        {{ __('Loading educational records...') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openAddNoteModal()" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span><i class="fa-solid fa-pen-nib"></i></span> {{ __('app.teacher.add_educational_note') }}
                </button>
                <button type="button" onclick="closeModal('studentProfileModal')" class="text-slate-300 hover:text-white font-bold text-xl p-1 cursor-pointer" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Educational Profile Sub-Navigation Tabs --}}
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-2.5 flex items-center gap-2 overflow-x-auto shrink-0 scrollbar-thin">
            <button type="button" onclick="switchSpTab('overview')" id="sp-tab-btn-overview" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap bg-teal-600 text-white shadow-xs">
                <i class="fa-solid fa-chart-column"></i> {{ __('Overview') }}
            </button>
            <button type="button" onclick="switchSpTab('courses')" id="sp-tab-btn-courses" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-book-open"></i> {{ __('Courses') }}
            </button>
            <button type="button" onclick="switchSpTab('sessions')" id="sp-tab-btn-sessions" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-calendar-days"></i> {{ __('Sessions') }}
            </button>
            <button type="button" onclick="switchSpTab('attendance')" id="sp-tab-btn-attendance" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-clipboard-list"></i> {{ __('Attendance') }}
            </button>
            <button type="button" onclick="switchSpTab('assignments')" id="sp-tab-btn-assignments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-pen-to-square"></i> {{ __('Assignments') }}
            </button>
            <button type="button" onclick="switchSpTab('assessments')" id="sp-tab-btn-assessments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-bullseye"></i> {{ __('Assessments') }}
            </button>
            <button type="button" onclick="switchSpTab('progress')" id="sp-tab-btn-progress" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-chart-line"></i> {{ __('Progress') }}
            </button>
            <button type="button" onclick="switchSpTab('notes')" id="sp-tab-btn-notes" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-comments"></i> {{ __('Notes') }}
            </button>
        </div>

        {{-- Dynamic Tab Body --}}
        <div class="p-6 overflow-y-auto flex-1 space-y-6">
            {{-- Loading Spinner Skeleton --}}
            <div id="spLoadingSkeleton" class="py-16 text-center space-y-3">
                <svg class="animate-spin h-8 w-8 text-teal-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-xs font-mono font-bold text-slate-500">{{ __('Loading educational records from server...') }}</p>
            </div>

            {{-- 1. SP OVERVIEW TAB --}}
            <div id="sp-pane-overview" class="sp-tab-pane space-y-6 hidden">
                {{-- KPI Summary Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-teal-700 uppercase">{{ __('Attendance Rate') }}</span>
                        <p id="spOverviewAttRate" class="font-heading font-black text-2xl text-teal-900">0%</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-emerald-700 uppercase">{{ __('Average Grade') }}</span>
                        <p id="spOverviewAvgGrade" class="font-heading font-black text-2xl text-emerald-900">0%</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-blue-700 uppercase">{{ __('Total Sessions') }}</span>
                        <p id="spOverviewTotalSessions" class="font-heading font-black text-2xl text-blue-900">0</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-orange-700 uppercase">{{ __('Submissions') }}</span>
                        <p id="spOverviewSubmissions" class="font-heading font-black text-2xl text-orange-900">0</p>
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
                            <span><i class="fa-solid fa-pen-to-square"></i></span> {{ __('Recent Homework & Submissions') }}
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
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3">{{ __('Session') }}</th>
                                <th class="py-2.5 px-3">{{ __('Course') }}</th>
                                <th class="py-2.5 px-3">{{ __('Date') }}</th>
                                <th class="py-2.5 px-3">{{ __('Attendance') }}</th>
                            </tr>
                        </thead>
                        <tbody id="spSessionsTableBody" class="divide-y divide-slate-100">
                            {{-- Populated by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. SP ATTENDANCE TAB --}}
            <div id="sp-pane-attendance" class="sp-tab-pane space-y-4 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3">{{ __('Session Title') }}</th>
                                <th class="py-2.5 px-3">{{ __('Date & Time') }}</th>
                                <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="spAttendanceTableBody" class="divide-y divide-slate-100">
                            {{-- Populated by JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 5. SP ASSIGNMENTS TAB --}}
            <div id="sp-pane-assignments" class="sp-tab-pane space-y-4 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3">{{ __('Assignment Title') }}</th>
                                <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                <th class="py-2.5 px-3">{{ __('Score') }}</th>
                                <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Review Details') }}</th>
                            </tr>
                        </thead>
                        <tbody id="spAssignmentsTableBody" class="divide-y divide-slate-100">
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
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="font-heading font-black text-sm text-slate-900">{{ __('Teacher Pedagogical Notes') }}</h4>
                    <button type="button" onclick="openAddNoteModal()" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        + {{ __('Add Note') }}
                    </button>
                </div>
                <div id="spNotesContainer" class="space-y-3">
                    {{-- Populated by JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: ADD EDUCATIONAL NOTE FOR STUDENT                                      --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="addNoteModal" class="elite-modal fixed inset-0 z-60 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-200/90 space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
            <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-pen-nib"></i></span>
                <span>{{ __('app.teacher.add_educational_note') }}</span>
            </h3>
            <button type="button" onclick="closeModal('addNoteModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="addNoteForm" class="space-y-4">
            @csrf
            <input type="hidden" id="noteStudentUserId" name="student_user_id">

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Category') }}</label>
                <select name="category" required class="input-mobile bg-white text-xs">
                    <option value="academic">{{ __('app.teacher.note_category_academic') }}</option>
                    <option value="homework">{{ __('app.teacher.note_category_homework') }}</option>
                    <option value="participation">{{ __('app.teacher.note_category_participation') }}</option>
                    <option value="behavior">{{ __('app.teacher.note_category_behavior') }}</option>
                    <option value="general" selected>{{ __('app.teacher.note_category_general') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Pedagogical Observation & Feedback') }}</label>
                <textarea id="noteContentTextarea" name="note" rows="4" required placeholder="{{ __('Write your educational observations and advisory comments...') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600 transition-colors"></textarea>
                
                {{-- Real-time Phone Security Warning Banner --}}
                <div id="notePhoneWarning" class="hidden mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold flex items-center gap-2 animate-pulse">
                    <span><i class="fa-solid fa-shield-halved"></i></span>
                    <span>{{ __('Security Warning: Sharing phone numbers or contact details in educational notes is prohibited.') }}</span>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('addNoteModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                <button type="submit" id="saveNoteBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    {{ __('Save Note') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1: SCHEDULE NEW SESSION                                                --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="createSessionModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200/90 space-y-6 relative max-h-[90vh] sm:max-h-[85vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-calendar-plus"></i></span>
                <span>{{ __('Schedule New Live Session') }}</span>
            </h3>
            <button type="button" onclick="closeModal('createSessionModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="createSessionForm" action="{{ route('ajax.teacher.sessions.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }} *</label>
                <select name="course_id" required class="input-mobile bg-white text-xs">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->subject?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }} *</label>
                <input type="text" name="title" placeholder="e.g. Session 4: Electromagnetism & Ohm's Law" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }} *</label>
                    <input type="datetime-local" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }} *</label>
                    <input type="number" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast Link (Optional)') }}</label>
                <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..." class="input-mobile">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_free_demo" id="is_free_demo" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                <label for="is_free_demo" class="text-xs font-semibold text-slate-700">{{ __('Mark as Free Trial / Demo Session') }}</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createSessionModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Create Session') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1B: CREATE RECURRING SCHEDULE (WEEKLY / MONTHLY / YEARLY)               --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="recurringScheduleModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200/90 space-y-6 relative max-h-[90vh] sm:max-h-[85vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-arrows-rotate"></i></span>
                    <span>{{ __('Create Recurring Schedule') }}</span>
                </h3>
                <p class="text-xs font-mono text-slate-500 mt-0.5">{{ __('Automatically generate recurring class sessions with conflict detection.') }}</p>
            </div>
            <button type="button" onclick="closeModal('recurringScheduleModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="recurringScheduleForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Schedule Title') }} *</label>
                <input type="text" id="recTitle" name="title" placeholder="e.g. Physics Secondary 3 - Weekly Interactive Cohort" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }} *</label>
                    <select id="recCourseId" name="course_id" required class="input-mobile bg-white text-xs">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->subject?->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Target Student (Optional)') }}</label>
                    <select id="recStudentUserId" name="student_user_id" class="input-mobile bg-white text-xs">
                        <option value="">{{ __('All Enrolled Course Students (General Cohort)') }}</option>
                        @foreach($assignedStudents as $st)
                            <option value="{{ $st->user_id }}">{{ $st->user?->name }} ({{ $st->gradeLevel?->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Recurrence Pattern') }} *</label>
                    <select id="recType" name="recurrence_type" required class="input-mobile bg-white text-xs" onchange="toggleRecurrenceFields(this.value)">
                        <option value="weekly" selected>{{ __('Weekly') }}</option>
                        <option value="monthly">{{ __('Monthly') }}</option>
                        <option value="multi_month">{{ __('Multiple Months (3-6 Months)') }}</option>
                        <option value="yearly">{{ __('Yearly (Full Academic Year)') }}</option>
                        <option value="single">{{ __('Single Session') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Start Time') }} *</label>
                    <input type="time" id="recStartTime" name="start_time" value="10:00" required class="input-mobile">
                </div>
            </div>

            {{-- Days of Week Selection (For Weekly / Multi-Month / Yearly) --}}
            <div id="recDaysContainer" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <label class="block text-xs font-mono font-bold text-slate-600 uppercase tracking-wider">{{ __('Select Days of Week') }} *</label>
                <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 text-center text-xs font-mono font-bold">
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
                    @foreach($weekDaysList as $wd)
                        <label class="p-2 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex flex-col items-center gap-1.5 transition-all has-checked:bg-teal-50 has-checked:border-teal-500 has-checked:text-teal-800">
                            <input type="checkbox" name="days_of_week[]" value="{{ $wd['val'] }}" {{ in_array($wd['val'], [6, 0]) ? 'checked' : '' }} class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 rec-day-checkbox">
                            <span class="text-[11px]">{{ $wd['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }} *</label>
                    <input type="number" id="recDuration" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Start Date') }} *</label>
                    <input type="date" id="recStartDate" name="start_date" value="{{ now()->format('Y-m-d') }}" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('End Date') }} *</label>
                    <input type="date" id="recEndDate" name="end_date" value="{{ now()->addMonths(3)->format('Y-m-d') }}" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast Link (Optional)') }}</label>
                <input type="url" id="recMeetingLink" name="meeting_link" placeholder="https://zoom.us/j/... or classroom stream link" class="input-mobile">
            </div>

            {{-- Live Schedule Preview & Conflict Feedback Area --}}
            <div class="pt-2">
                <button type="button" onclick="previewRecurringDates()" class="btn-lift w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-xl border border-slate-300 flex items-center justify-center gap-2 cursor-pointer transition-all">
                    <span><i class="fa-solid fa-magnifying-glass"></i></span> {{ __('Preview Generated Sessions & Validate Conflicts') }}
                </button>
            </div>

            <div id="recPreviewContainer" class="hidden space-y-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 max-h-56 overflow-y-auto">
                <div class="flex items-center justify-between">
                    <span id="recPreviewSummary" class="text-xs font-bold text-slate-800"></span>
                    <span id="recConflictStatusBadge" class="text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full"></span>
                </div>
                <div id="recConflictWarning" class="hidden p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"></div>
                <div id="recPreviewTableWrapper" class="overflow-x-auto">
                    <table class="w-full text-xs text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[10px] font-mono text-slate-400 uppercase">
                                <th class="py-1 px-2">{{ __('Date') }}</th>
                                <th class="py-1 px-2">{{ __('Day') }}</th>
                                <th class="py-1 px-2">{{ __('Time Window') }}</th>
                                <th class="py-1 px-2">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="recPreviewTableBody" class="divide-y divide-slate-100 font-mono text-[11px]"></tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('recurringScheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                <button type="submit" id="saveRecurringBtn" class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    {{ __('Create Recurring Schedule') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1C: EDIT SESSION / OVERRIDE SCOPE SELECTOR                              --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="editSessionOverrideModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200/90 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-sliders"></i></span>
                <span>{{ __('Edit Session & Recurrence Scope') }}</span>
            </h3>
            <button type="button" onclick="closeModal('editSessionOverrideModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="editSessionOverrideForm" class="space-y-4">
            @csrf
            <input type="hidden" id="overrideSessionId" name="session_id">

            {{-- Scope Selection (3 Options) --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5">
                <label class="block text-xs font-mono font-bold text-slate-600 uppercase tracking-wider">{{ __('Modification Scope') }} *</label>
                <div class="space-y-2">
                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="this_only" checked class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">{{ __('Edit This Session Only') }}</span>
                            <span class="text-slate-500 text-[11px]">{{ __('Applies as an individual override. The recurring series remains unchanged.') }}</span>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="this_and_future" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">{{ __('Edit This and Future Sessions') }}</span>
                            <span class="text-slate-500 text-[11px]">{{ __('Updates this class and all remaining future sessions in this recurring series.') }}</span>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="all" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block">{{ __('Edit Entire Recurring Schedule') }}</span>
                            <span class="text-slate-500 text-[11px]">{{ __('Modifies the schedule template rule across all non-completed sessions.') }}</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}</label>
                <input type="text" id="overrideTitle" name="title" class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }} *</label>
                    <input type="datetime-local" id="overrideDateTime" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }} *</label>
                    <input type="number" id="overrideDuration" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Broadcast Meeting URL') }}</label>
                <input type="url" id="overrideMeetingLink" name="meeting_link" placeholder="https://zoom.us/j/..." class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Reason for Modification (Audit Log)') }}</label>
                <input type="text" id="overrideReason" name="reason" placeholder="{{ __('e.g. Schedule adjustment per student request') }}" class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('editSessionOverrideModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                <button type="submit" id="saveOverrideBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    {{ __('Save Changes') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 1D: CANCEL SESSION MODAL                                                --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="cancelSessionModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200/90 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-heading font-black text-xl text-rose-600 flex items-center gap-2">
                <span><i class="fa-solid fa-circle-xmark text-rose-500"></i></span> {{ __('Cancel Session') }}
            </h3>
            <button type="button" onclick="closeModal('cancelSessionModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="cancelSessionForm" class="space-y-4">
            @csrf
            <input type="hidden" id="cancelSessionId">
            <p class="text-xs text-slate-600 leading-relaxed font-semibold">
                {{ __('Are you sure you want to cancel this session? All enrolled students will be immediately notified via push notification and email.') }}
            </p>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Cancellation Reason') }} *</label>
                <input type="text" id="cancelReasonInput" name="reason" placeholder="{{ __('e.g. Instructor emergency or holiday rescheduling') }}" required class="input-mobile">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('cancelSessionModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">{{ __('Keep Session') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    {{ __('Confirm Cancellation') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 2: MEETING LINK EDITOR                                                 --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="meetingLinkModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200/90 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-video"></i></span>
                <span>{{ __('Update Live Stream Link') }}</span>
            </h3>
            <button type="button" onclick="closeModal('meetingLinkModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="meetingLinkForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="linkSessionId" name="session_id">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast URL') }}</label>
                <input type="url" id="meetingUrlInput" name="meeting_link" placeholder="https://vimeo.com/... or Zoom link" required class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('meetingLinkModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save Meeting Link') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 3: RESCHEDULE SESSION                                                  --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="rescheduleModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200/90 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="fa-solid fa-calendar-days"></i></span>
                <span>{{ __('Reschedule Teaching Session') }}</span>
            </h3>
            <button type="button" onclick="closeModal('rescheduleModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="rescheduleForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="rescheduleSessionId">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('New Scheduled Date & Time') }}</label>
                <input type="datetime-local" id="rescheduleDateTime" name="scheduled_at" required class="input-mobile">
            </div>
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Reason for Rescheduling') }}</label>
                <input type="text" id="rescheduleReason" name="reason" placeholder="{{ __('e.g. Time adjustment for upcoming exam revision') }}" class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('rescheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Confirm Reschedule') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 4: PUBLISH ASSIGNMENT                                                  --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="createAssignmentModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200/90 space-y-6 relative max-h-[90vh] sm:max-h-[85vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-file-signature"></i></span>
                    <span>{{ __('Publish New Assignment & Quiz') }}</span>
                </h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ __('Create homework assignments or interactive MSQ quizzes for your students.') }}</p>
            </div>
            <button type="button" onclick="closeModal('createAssignmentModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        <form id="createAssignmentForm" action="{{ route('ajax.teacher.assignments.create') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Target Course') }} *</label>
                    <select name="course_id" required class="input-mobile bg-white text-xs">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Live Session (Optional)') }}</label>
                    <select name="live_session_id" class="input-mobile bg-white text-xs">
                        <option value="">{{ __('None / General Course Assignment') }}</option>
                        @foreach($todaySessions->merge($allSessions)->unique('id') as $ls)
                            <option value="{{ $ls->id }}">{{ $ls->title ?: __('Live Session') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Assignment Title') }} *</label>
                <input type="text" name="title" placeholder="e.g. Unit 2: Physics Waves & Optics Quiz" required class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Description / Instructions') }}</label>
                <textarea name="description" rows="2" placeholder="{{ __('Provide guidelines, instructions, or reading materials...') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Due Date & Time') }} *</label>
                    <input type="datetime-local" name="due_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }}</label>
                    <input type="number" name="duration_minutes" value="30" min="5" max="300" class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Passing Score (%)') }}</label>
                    <input type="number" name="passing_score" value="70" min="0" max="100" class="input-mobile">
                </div>
            </div>

            {{-- Interactive Multiple Choice Quiz Questions Builder --}}
            <div class="pt-3 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-heading font-black text-sm text-slate-900">{{ __('Questions & Quiz Builder') }}</h4>
                        <p class="text-[11px] text-slate-500 font-mono">{{ __('Add interactive multiple choice questions with automated answer key.') }}</p>
                    </div>
                    <button type="button" onclick="addTeacherQuestion()" class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-extrabold text-xs rounded-xl border border-teal-200 cursor-pointer transition-all">
                        + {{ __('Add Question') }}
                    </button>
                </div>

                <div id="teacherQuestionsContainer" class="space-y-4 pt-2">
                    {{-- Dynamically Appended Question Blocks --}}
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createAssignmentModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Publish Assignment') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 4B: ASSIGNMENT DETAILS, QUIZ QUESTIONS & SUBMISSIONS ROSTER             --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="assignmentDetailsModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] max-w-3xl w-full shadow-2xl border border-slate-200/90 relative max-h-[90vh] sm:max-h-[85vh] flex flex-col overflow-hidden">
        {{-- Modal Top Header --}}
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-start justify-between gap-4 shrink-0 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1.5 min-w-0 flex-1 z-10">
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="adModalCourse" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40"></span>
                    <span id="adModalSubject" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-white/10 text-slate-200"></span>
                    <span id="adModalStatus" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40"></span>
                </div>
                <h3 id="adModalTitle" class="font-heading font-black text-xl sm:text-2xl text-white leading-snug"></h3>
                <p id="adModalDescription" class="text-xs text-slate-300 leading-relaxed"></p>
            </div>
            <button type="button" onclick="closeModal('assignmentDetailsModal')" aria-label="{{ __('Close') }}" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                <i class="fa-solid fa-xmark text-sm"></i>
        </div>

        {{-- Modal Scrollable Body --}}
        <div class="p-5 sm:p-7 overflow-y-auto flex-1 custom-scrollbar space-y-6">
            {{-- Loading Skeleton --}}
            <div id="adLoadingSkeleton" class="py-12 text-center space-y-3">
                <div class="inline-block w-8 h-8 border-3 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-mono text-slate-400">{{ __('Loading assignment details...') }}</p>
            </div>

            {{-- Dynamic Content --}}
            <div id="adModalContent" class="hidden space-y-6">
                {{-- KPI Stats Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="p-3 bg-[#FAFAF9] rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] uppercase font-mono font-bold text-slate-400 block">{{ __('Due Date') }}</span>
                        <span id="adDueAt" class="font-heading font-black text-xs sm:text-sm text-slate-900 block mt-0.5"></span>
                        <span id="adDueHuman" class="text-[10px] font-mono text-teal-600 block mt-0.5"></span>
                    </div>
                    <div class="p-3 bg-[#FAFAF9] rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] uppercase font-mono font-bold text-slate-400 block">{{ __('Passing Score') }}</span>
                        <span id="adPassScore" class="font-heading font-black text-xs sm:text-sm text-slate-900 block mt-0.5"></span>
                        <span class="text-[10px] font-mono text-slate-400 block mt-0.5">{{ __('Minimum to pass') }}</span>
                    </div>
                    <div class="p-3 bg-[#FAFAF9] rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] uppercase font-mono font-bold text-slate-400 block">{{ __('Total Questions') }}</span>
                        <span id="adQuestionsCount" class="font-heading font-black text-xs sm:text-sm text-slate-900 block mt-0.5"></span>
                        <span id="adDuration" class="text-[10px] font-mono text-slate-400 block mt-0.5"></span>
                    </div>
                    <div class="p-3 bg-[#FAFAF9] rounded-2xl border border-slate-200/80">
                        <span class="text-[10px] uppercase font-mono font-bold text-slate-400 block">{{ __('Total Submissions') }}</span>
                        <span id="adSubmissionsCount" class="font-heading font-black text-xs sm:text-sm text-emerald-600 block mt-0.5"></span>
                        <span id="adAvgScore" class="text-[10px] font-mono text-slate-400 block mt-0.5"></span>
                    </div>
                </div>

            {{-- Subtabs Selector --}}
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <button type="button" onclick="switchAdSubTab('questions')" id="ad-tab-btn-questions" class="ad-subtab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all bg-teal-600 text-white shadow-xs cursor-pointer">
                    <i class="fa-solid fa-list-check me-1"></i> {{ __('Quiz Questions') }} (<span id="adQuestionsBadge">0</span>)
                </button>
                <button type="button" onclick="switchAdSubTab('submissions')" id="ad-tab-btn-submissions" class="ad-subtab-btn px-4 py-2 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-slate-100 cursor-pointer">
                    <i class="fa-solid fa-user-graduate me-1"></i> {{ __('Student Submissions') }} (<span id="adSubmissionsBadge">0</span>)
                </button>
            </div>

            {{-- Tab Pane 1: Questions & Answers Key --}}
            <div id="ad-pane-questions" class="ad-pane space-y-4">
                <div id="adQuestionsList" class="space-y-4"></div>
                <div id="adNoQuestionsNotice" class="hidden text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-500 italic">{{ __('No multiple-choice questions attached to this assignment.') }}</p>
                </div>
            </div>

            {{-- Tab Pane 2: Student Submissions Roster --}}
            <div id="ad-pane-submissions" class="ad-pane hidden space-y-4">
                <div id="adSubmissionsTableContainer" class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3">{{ __('Student') }}</th>
                                <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                <th class="py-2.5 px-3">{{ __('Grade / Score') }}</th>
                                <th class="py-2.5 px-3">{{ __('Status') }}</th>
                                <th class="py-2.5 px-3 text-right rtl:text-left">{{ __('Review Action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="adSubmissionsTableBody" class="divide-y divide-slate-100 font-mono"></tbody>
                    </table>
                </div>
                <div id="adNoSubmissionsNotice" class="hidden text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-500 italic">{{ __('No student submissions recorded for this assignment yet.') }}</p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 sm:p-5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-end shrink-0 rounded-b-[28px]">
            <button type="button" onclick="closeModal('assignmentDetailsModal')" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-colors cursor-pointer">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 5: GRADE ASSIGNMENT SUBMISSION & QUESTION REVIEW                       --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="gradeModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200/90 space-y-6 relative max-h-[90vh] sm:max-h-[85vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm"><i class="fa-solid fa-award"></i></span>
                    <span>{{ __('Review & Grade Submission') }}</span>
                </h3>
                <p id="gradeStudentName" class="text-xs text-teal-600 font-mono font-bold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('gradeModal')" aria-label="{{ __('Close') }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-all cursor-pointer active:scale-95"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>

        {{-- Question By Question Auto-Correction Breakdown --}}
        <div class="space-y-3">
            <h4 class="font-heading font-black text-sm text-slate-900 flex items-center gap-2">
                <span><i class="fa-solid fa-bullseye"></i></span> {{ __('Questions Auto-Correction & Student Choices') }}
            </h4>
            <div id="submissionQuestionsContainer" class="space-y-3 max-h-72 overflow-y-auto p-1 scrollbar-thin">
                {{-- Populated via AJAX --}}
            </div>
        </div>

        <form id="gradeForm" method="POST" class="space-y-4 pt-3 border-t border-slate-100">
            @csrf
            <input type="hidden" id="gradeSubmissionId" name="submission_id">

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Overall Grade Score (0 - 100%)') }} *</label>
                <input type="number" id="gradeScoreInput" name="score" step="0.1" min="0" max="100" required placeholder="e.g. 85.0" class="input-mobile font-mono font-bold text-lg">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Pedagogical Feedback & Notes to Student') }}</label>
                <textarea id="gradeEvaluationNotes" name="evaluation_notes" rows="3" placeholder="{{ __('Provide detailed feedback, remarks, or praise for the student...') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('gradeModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save & Finalize Grade') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL 6: MARK SESSION ATTENDANCE (REAL-TIME COURSE COHORT)                   --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="attendanceModal" class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div class="elite-modal-dialog bg-white rounded-[28px] max-w-xl w-full shadow-2xl border border-slate-200/90 relative max-h-[90vh] sm:max-h-[85vh] flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-center justify-between gap-4 shrink-0 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-3.5 min-w-0 z-10">
                <div class="w-11 h-11 rounded-2xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center shrink-0 shadow-sm text-lg">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
                <div class="min-w-0 space-y-1">
                    <h3 class="font-heading font-black text-lg sm:text-xl text-white tracking-tight leading-snug">
                        {{ __('Record Session Attendance') }}
                    </h3>
                    <p id="attendanceSessionTitle" class="text-xs text-teal-300/90 font-mono font-bold truncate max-w-md"></p>
                </div>
            </div>
            <button type="button" onclick="closeModal('attendanceModal')" aria-label="{{ __('Close') }}" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- Sub-header with Bulk Controls --}}
        <div class="px-5 sm:px-6 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">{{ __('COHORT CHECK-IN') }}</span>
                <span id="attendanceCohortCount" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200">0</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="bulkSetAttendance('present')" class="btn-lift px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold rounded-xl border border-emerald-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                    <span>{{ __('All Present') }}</span>
                </button>
                <button type="button" onclick="bulkSetAttendance('absent')" class="btn-lift px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-800 text-xs font-bold rounded-xl border border-rose-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-circle-xmark text-rose-600 text-xs"></i>
                    <span>{{ __('All Absent') }}</span>
                </button>
            </div>
        </div>

        <form id="attendanceForm" method="POST" class="flex-1 flex flex-col overflow-hidden m-0">
            @csrf
            <input type="hidden" id="attendanceSessionId">

            {{-- Dynamic Scrollable Students Container --}}
            <div id="attendanceStudentsContainer" class="divide-y divide-slate-100 overflow-y-auto flex-1 p-4 sm:p-6 space-y-1 custom-scrollbar">
                {{-- Populated dynamically in real-time via AJAX --}}
            </div>

            {{-- Sticky Action Footer --}}
            <div class="p-4 sm:p-5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0 rounded-b-[28px]">
                <button type="button" onclick="closeModal('attendanceModal')" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-xl transition-all cursor-pointer">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="saveAttendanceBtn" class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-teal-600/25 cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ __('Save Attendance Sheet') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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

// ── Tab Switcher for Main Teacher Portal ──────────────────────────────────────
function switchTeacherTab(tabKey) {
    document.querySelectorAll('.teacher-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.teacher-tab-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-md', 'active');
        btn.classList.add('text-slate-700', 'hover:bg-slate-100');
    });

    const activeContent = document.getElementById('teacher-tab-' + tabKey);
    const activeBtn = document.getElementById('tab-btn-' + tabKey);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-100');
        activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-md');
    }

    document.querySelectorAll('#portalSidebar .teacher-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === tabKey) {
            btn.classList.add('active');
        }
    });

    // Mobile drawer auto-close
    if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
        togglePortalSidebar(false);
    }
}

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
    openModal('studentProfileModal');

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
            data = { success: false, message: 'Server error' };
        }

        if (!res.ok || !data.success) {
            const errorMsg = data.message || (res.status === 403 
                ? '{{ __("Unauthorized: You do not have permission to access this student educational profile.") }}'
                : '{{ __("Failed to load student details.") }}');
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
        document.getElementById('spModalMeta').textContent = `${st.school || 'Elite Academy'} • ${st.grade || 'Secondary'} • <i class="fa-solid fa-envelope"></i> ${st.email || ''}`;

        // 2. Populate Overview KPIs
        document.getElementById('spOverviewAttRate').textContent = `${metrics.attendance_rate || 100}%`;
        document.getElementById('spOverviewAvgGrade').textContent = metrics.avg_score !== null ? `${metrics.avg_score}%` : 'N/A';
        document.getElementById('spOverviewTotalSessions').textContent = metrics.total_sessions || 0;
        document.getElementById('spOverviewSubmissions').textContent = metrics.total_submissions || 0;

        // Populate Overview Mini Lists
        const recentSesContainer = document.getElementById('spOverviewRecentSessions');
        if (data.sessions && data.sessions.length > 0) {
            let sHtml = '';
            data.sessions.slice(0, 3).forEach(s => {
                const attColor = s.attendance_status === 'present' ? 'text-emerald-600' : (s.attendance_status === 'late' ? 'text-amber-600' : 'text-rose-600');
                sHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${s.title}</p>
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
                const scoreText = sub.score !== null ? `<span class="font-bold text-emerald-600">${sub.score}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                subHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${sub.assignment_title}</p>
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
                        <h4 class="font-heading font-bold text-sm text-slate-900">${c.title}</h4>
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
            coursesList.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noCourses}</p>`;
        }

        // 4. Populate Sessions Tab
        const sesTableBody = document.getElementById('spSessionsTableBody');
        if (data.sessions && data.sessions.length > 0) {
            let sesHtml = '';
            data.sessions.forEach(s => {
                const attBadge = s.attendance_status === 'present' 
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> Present</span>'
                    : (s.attendance_status === 'late'
                        ? '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold"><i class="fa-solid fa-circle text-amber-500 text-[10px]"></i> Late</span>'
                        : '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> Absent</span>');
                sesHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${s.title}</td>
                    <td class="py-2.5 px-3 text-slate-600">${s.course_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${s.date}</td>
                    <td class="py-2.5 px-3 font-mono">${attBadge}</td>
                </tr>`;
            });
            sesTableBody.innerHTML = sesHtml;
        } else {
            sesTableBody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noSessions}</td></tr>`;
        }

        // 5. Populate Attendance Tab
        const attTableBody = document.getElementById('spAttendanceTableBody');
        if (data.attendance && data.attendance.length > 0) {
            let attHtml = '';
            data.attendance.forEach(a => {
                const statusStr = a.attendance_status || 'scheduled';
                attHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${a.title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                    <td class="py-2.5 px-3 font-mono text-right rtl:text-left">
                        <span class="font-bold uppercase text-[10px] px-2 py-0.5 rounded-full ${statusStr === 'present' ? 'bg-emerald-100 text-emerald-800' : (statusStr === 'late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')}">${statusStr}</span>
                    </td>
                </tr>`;
            });
            attTableBody.innerHTML = attHtml;
        } else {
            attTableBody.innerHTML = `<tr><td colspan="3" class="py-4 text-center text-slate-400 italic">${i18n.noAttendance}</td></tr>`;
        }

        // 6. Populate Assignments Tab
        const assignTableBody = document.getElementById('spAssignmentsTableBody');
        if (data.submissions && data.submissions.length > 0) {
            let assHtml = '';
            data.submissions.forEach(sub => {
                const scoreDisplay = sub.score !== null ? `<span class="font-bold text-emerald-600">${sub.score}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                assHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${sub.assignment_title}</td>
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
            assignTableBody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noAssignments}</td></tr>`;
        }

        // 7. Populate Assessments & Quizzes Tab
        const assessContainer = document.getElementById('spAssessmentsContainer');
        if (data.assessments && data.assessments.length > 0) {
            let assessHtml = '';
            data.assessments.forEach(ass => {
                const passBadge = ass.is_passed 
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-check"></i> Passed</span>'
                    : (ass.score !== null 
                        ? '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-xmark me-1"></i> Retake</span>'
                        : '<span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">Pending</span>');
                assessHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-900">${ass.assignment_title}</p>
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
            assessContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noAssignments}</p>`;
        }

        // 8. Populate Progress & Analytics Tab
        const progContainer = document.getElementById('spProgressContainer');
        progContainer.innerHTML = `
            <div class="p-5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-heading font-black text-sm text-slate-900">Comprehensive Academic Health</h4>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-mono">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Attendance Consistency</span>
                        <span class="font-extrabold text-base text-emerald-600">${metrics.attendance_rate || 100}%</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Assessment Pass Rate</span>
                        <span class="font-extrabold text-base text-teal-600">${metrics.pass_rate || 100}%</span>
                    </div>
                </div>
            </div>
        `;

        // 9. Populate Notes Tab
        renderEducationalNotes(data.notes || []);

    } catch (err) {
        document.getElementById('spLoadingSkeleton').innerHTML = `<p class="text-xs text-rose-600 italic py-6">Failed to load student details.</p>`;
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
            }[n.category] || '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">Note</span>';

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
    openModal('addNoteModal');
}

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

        let matchSearch = !searchVal || name.includes(searchVal) || code.includes(searchVal) || school.includes(searchVal);
        let matchCourse = !courseVal || courses.includes(courseVal);
        let matchGrade = !gradeVal || grade === gradeVal;
        let matchAtt = !attVal || (attVal === 'good' && attendance >= 80) || (attVal === 'risk' && attendance < 80);

        if (matchSearch && matchCourse && matchGrade && matchAtt) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const emptyState = document.getElementById('studentEmptySearchState');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    const countText = document.getElementById('studentFilterCountText');
    if (countText) {
        countText.textContent = `Showing ${visibleCount} of ${cards.length} students`;
    }
}

function resetStudentFilters() {
    if (document.getElementById('studentSearchInput')) document.getElementById('studentSearchInput').value = '';
    if (document.getElementById('studentCourseFilter')) document.getElementById('studentCourseFilter').value = '';
    if (document.getElementById('studentGradeFilter')) document.getElementById('studentGradeFilter').value = '';
    if (document.getElementById('studentAttendanceFilter')) document.getElementById('studentAttendanceFilter').value = '';
    applyStudentFilters();
}

// ── Open Modals & Action Helpers ─────────────────────────────────────────────
function openModal(id) {
    if (window.openModal) {
        window.openModal(id);
    } else {
        const m = document.getElementById(id);
        if (m) m.classList.remove('hidden');
    }
}

function closeModal(id) {
    if (window.closeModal) {
        window.closeModal(id);
    } else {
        const m = document.getElementById(id);
        if (m) m.classList.add('hidden');
    }
}

function openCreateSessionModal() {
    openModal('createSessionModal');
}

function openCreateAssignmentModal() {
    openModal('createAssignmentModal');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeJs(str) {
    if (!str) return '';
    return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}

async function openAssignmentDetailsModal(assignmentId) {
    openModal('assignmentDetailsModal');

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
            data = { success: false };
        }

        if (!res.ok || !data.success) {
            showTeacherToast(data.message || '{{ __("Failed to load assignment details.") }}', false);
            closeModal('assignmentDetailsModal');
            return;
        }

        const a = data.assignment;
        const stats = data.stats || {};

        document.getElementById('adModalCourse').textContent = a.course_title;
        document.getElementById('adModalSubject').textContent = a.subject_name || '';
        document.getElementById('adModalStatus').textContent = (a.status || 'published').toUpperCase();
        document.getElementById('adModalTitle').textContent = a.title;
        document.getElementById('adModalDescription').textContent = a.description || '{{ __("No instructions provided.") }}';

        document.getElementById('adDueAt').textContent = a.due_at || '{{ __("No deadline") }}';
        document.getElementById('adDueHuman').textContent = a.due_at_human || '';
        document.getElementById('adPassScore').textContent = a.passing_score + '%';
        document.getElementById('adQuestionsCount').textContent = a.total_questions || 0;
        document.getElementById('adDuration').textContent = a.duration_minutes ? a.duration_minutes + ' {{ __("min") }}' : '';
        document.getElementById('adSubmissionsCount').textContent = stats.total_submissions || 0;
        document.getElementById('adAvgScore').textContent = stats.avg_score !== null ? '{{ __("Avg") }}: ' + stats.avg_score + '%' : '';

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
                    optsHtml += `
                        <div class="p-2.5 rounded-xl border ${isCorrect ? 'bg-emerald-50/90 border-emerald-300 text-emerald-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700'} flex items-start justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-lg ${isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500'} flex items-center justify-center shrink-0 text-[10px]">
                                    ${isCorrect ? '<i class="fa-solid fa-check"></i>' : '•'}
                                </span>
                                <span class="break-words">${escapeHtml(opt.option_text)}</span>
                            </div>
                            ${isCorrect ? '<span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded-md bg-emerald-200 text-emerald-900 shrink-0 font-extrabold"><i class="fa-solid fa-circle-check me-1"></i>{{ __("Correct Answer") }}</span>' : ''}
                        </div>
                    `;
                });

                qHtml += `
                    <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-heading font-black text-xs text-teal-800 bg-teal-100/70 px-2.5 py-1 rounded-lg">#${idx + 1}</span>
                            <span class="text-[11px] font-mono font-bold text-slate-500">${q.points} {{ __('pts') }}</span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 leading-snug">${escapeHtml(q.question_text)}</p>
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
                const scoreText = s.score !== null ? `${s.score}%` : '{{ __("Pending Grade") }}';
                const scoreClass = s.score !== null ? (isPassed ? 'text-emerald-600 font-extrabold' : 'text-rose-600 font-extrabold') : 'text-slate-400 italic';
                const statusBadgeClass = s.status === 'reviewed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800';

                subHtml += `
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-3">
                            <p class="font-bold text-slate-900">${escapeHtml(s.student_name)}</p>
                            <p class="text-[10px] text-slate-500 font-mono">${escapeHtml(s.student_email)}</p>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600">${s.submitted_at || 'Draft'}</td>
                        <td class="py-3 px-3 font-mono ${scoreClass}">${scoreText}</td>
                        <td class="py-3 px-3 font-mono">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusBadgeClass}">
                                ${escapeHtml(s.status)}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right rtl:text-left">
                            <button type="button" onclick="closeModal('assignmentDetailsModal'); openGradeModal(${s.id}, '${escapeJs(s.student_name)}', '${escapeJs(a.title)}', '${s.score !== null ? s.score : ''}', '${escapeJs(s.evaluation_notes || '')}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
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
        showTeacherToast('{{ __("Failed to load assignment details.") }}', false);
        closeModal('assignmentDetailsModal');
    }
}

function switchAdSubTab(tab) {
    document.querySelectorAll('.ad-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.ad-subtab-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
        btn.classList.add('text-slate-600');
    });

    const pane = document.getElementById('ad-pane-' + tab);
    const btn = document.getElementById('ad-tab-btn-' + tab);
    if (pane) pane.classList.remove('hidden');
    if (btn) {
        btn.classList.remove('text-slate-600');
        btn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
    }
}

function openMeetingLinkModal(sessionId, currentLink) {
    document.getElementById('linkSessionId').value = sessionId;
    document.getElementById('meetingUrlInput').value = currentLink || '';
    document.getElementById('meetingLinkForm').action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/link`;
    openModal('meetingLinkModal');
}

function openRescheduleModal(sessionId, currentDateTime) {
    document.getElementById('rescheduleSessionId').value = sessionId;
    document.getElementById('rescheduleDateTime').value = currentDateTime || '';
    document.getElementById('rescheduleForm').action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/reschedule`;
    openModal('rescheduleModal');
}

async function openAttendanceModal(sessionId, sessionTitle) {
    document.getElementById('attendanceSessionId').value = sessionId;
    document.getElementById('attendanceSessionTitle').textContent = sessionTitle || 'Loading...';
    document.getElementById('attendanceForm').action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance`;

    const container = document.getElementById('attendanceStudentsContainer');
    container.innerHTML = `
        <div class="py-6 space-y-3">
            <div class="flex items-center gap-3 p-3 bg-slate-50/80 rounded-2xl animate-pulse">
                <div class="w-10 h-10 rounded-xl bg-slate-200 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-slate-200 rounded-md w-1/3"></div>
                    <div class="h-2.5 bg-slate-200 rounded-md w-1/4"></div>
                </div>
                <div class="w-28 h-8 bg-slate-200 rounded-xl shrink-0"></div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50/80 rounded-2xl animate-pulse">
                <div class="w-10 h-10 rounded-xl bg-slate-200 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-slate-200 rounded-md w-1/2"></div>
                    <div class="h-2.5 bg-slate-200 rounded-md w-1/5"></div>
                </div>
                <div class="w-28 h-8 bg-slate-200 rounded-xl shrink-0"></div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50/80 rounded-2xl animate-pulse">
                <div class="w-10 h-10 rounded-xl bg-slate-200 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3.5 bg-slate-200 rounded-md w-2/5"></div>
                    <div class="h-2.5 bg-slate-200 rounded-md w-1/4"></div>
                </div>
                <div class="w-28 h-8 bg-slate-200 rounded-xl shrink-0"></div>
            </div>
        </div>
    `;
    document.getElementById('attendanceCohortCount').textContent = '...';

    openModal('attendanceModal');

    try {
        const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance-roster`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (!data.success) {
            showTeacherToast(data.message || 'Unauthorized', false);
            closeModal('attendanceModal');
            return;
        }

        if (data.session) {
            document.getElementById('attendanceSessionTitle').textContent = `${data.session.title} ${data.session.course_title ? '— ' + data.session.course_title : ''}`;
        }

        const students = data.students || [];
        document.getElementById('attendanceCohortCount').textContent = students.length;

        if (students.length === 0) {
            container.innerHTML = `
                <div class="py-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-xl shadow-2xs border border-teal-100">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-slate-700">${isArLocale ? 'لا يوجد طلاب مسجلين في هذا الكورس حالياً.' : 'No students enrolled in this course yet.'}</p>
                        <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'سيظهر الطلاب المسجلون تلقائياً بمجرد اشتراكهم.' : 'Enrolled students will appear here automatically.'}</p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        students.forEach((st, idx) => {
            const isPresent = st.status === 'present';
            const isLate = st.status === 'late';
            const isExcused = st.status === 'excused';
            const isAbsent = st.status === 'absent';

            html += `
                <div class="p-3 sm:p-3.5 rounded-2xl hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-3 border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-sm flex items-center justify-center shrink-0 shadow-sm border border-teal-200/50">
                            ${(st.name || 'S').substring(0, 1).toUpperCase()}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs sm:text-sm font-bold text-slate-900 truncate">${escapeHtml(st.name)}</p>
                            <p class="text-[11px] font-mono text-slate-500 truncate flex items-center gap-1.5">
                                <span>${escapeHtml(st.school || 'Elite Academy')}</span>
                                ${st.grade ? `<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span><span>${escapeHtml(st.grade)}</span>` : ''}
                            </p>
                        </div>
                    </div>

                    <input type="hidden" name="attendance[${idx}][student_user_id]" value="${st.id}">
                    <div class="shrink-0">
                        <select name="attendance[${idx}][status]" onchange="onAttendanceStatusChange('${escapeJs(st.name)}', this.value)" class="attendance-status-select bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-500/20 shadow-2xs cursor-pointer transition-all">
                            <option value="present" ${isPresent ? 'selected' : ''}>🟢 ${isArLocale ? 'حاضر (Present)' : 'Present'}</option>
                            <option value="late" ${isLate ? 'selected' : ''}>🟡 ${isArLocale ? 'متأخر (Late)' : 'Late'}</option>
                            <option value="excused" ${isExcused ? 'selected' : ''}>⚪ ${isArLocale ? 'معذور (Excused)' : 'Excused'}</option>
                            <option value="absent" ${isAbsent ? 'selected' : ''}>🔴 ${isArLocale ? 'غائب (Absent)' : 'Absent'}</option>
                        </select>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

    } catch (err) {
        container.innerHTML = `
            <div class="py-10 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-xl shadow-xs border border-rose-100">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-rose-700">${isArLocale ? 'تعذر تحميل كشف حضور الطلاب في الوقت الفعلي.' : 'Failed to load real-time attendance roster.'}</p>
                    <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'يرجى التحقق من الاتصال بالإنترنت والمحاولة مجدداً.' : 'Please check your connection and try again.'}</p>
                </div>
                <button type="button" onclick="openAttendanceModal(${sessionId}, '${escapeJs(sessionTitle)}')" class="btn-lift px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-rotate-right"></i> ${isArLocale ? 'إعادة المحاولة' : 'Retry'}
                </button>
            </div>
        `;
    }
}

function onAttendanceStatusChange(studentName, newStatus) {
    const statusMap = {
        present: {
            title: isArLocale ? 'تسجيل حضور' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ حاضر <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>` : `Marked (${studentName}) as Present <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>`,
            type: 'success'
        },
        late: {
            title: isArLocale ? 'تسجيل تأخير' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ متأخر <i class="fa-solid fa-circle text-amber-500 text-[10px]"></i>` : `Marked (${studentName}) as Late <i class="fa-solid fa-circle text-amber-500 text-[10px]"></i>`,
            type: 'warning'
        },
        excused: {
            title: isArLocale ? 'تسجيل عذر' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ معذور <i class="fa-solid fa-circle text-slate-300 text-[10px]"></i>` : `Marked (${studentName}) as Excused <i class="fa-solid fa-circle text-slate-300 text-[10px]"></i>`,
            type: 'info'
        },
        absent: {
            title: isArLocale ? 'تسجيل غياب' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ غائب <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>` : `Marked (${studentName}) as Absent <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>`,
            type: 'danger'
        },
    };

    const cfg = statusMap[newStatus] || { title: 'Attendance', msg: 'Status updated', type: 'info' };
    if (window.Toast) {
        window.Toast.show({
            type: cfg.type,
            title: cfg.title,
            message: cfg.msg,
            duration: 2800
        });
    }
}

function bulkSetAttendance(status) {
    document.querySelectorAll('.attendance-status-select').forEach(sel => {
        sel.value = status;
    });

    if (window.Toast) {
        if (status === 'present') {
            window.Toast.success(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ حضور <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>' : 'All students marked as Present <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        } else if (status === 'absent') {
            window.Toast.danger(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ غياب <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>' : 'All students marked as Absent <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        }
    }
}

async function confirmCancelSession(sessionId) {
    if (!confirm(@json(__('Are you sure you want to cancel this live session? Affected students will be notified immediately.')))) {
        return;
    }

    try {
        const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        showTeacherToast(data.message, data.success);
        if (data.success) {
            setTimeout(() => location.reload(), 900);
        }
    } catch (err) {
        showTeacherToast('Failed to cancel session', false);
    }
}

async function openGradeModal(submissionId, studentName, assignmentTitle, currentScore, evaluationNotes) {
    document.getElementById('gradeSubmissionId').value = submissionId;
    document.getElementById('gradeStudentName').textContent = `${studentName} — ${assignmentTitle}`;
    document.getElementById('gradeScoreInput').value = currentScore && currentScore !== 'null' ? currentScore : '';
    const notesEl = document.getElementById('gradeEvaluationNotes');
    if (notesEl) notesEl.value = evaluationNotes && evaluationNotes !== 'null' ? evaluationNotes : '';
    document.getElementById('gradeForm').action = `${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review`;

    const questionsContainer = document.getElementById('submissionQuestionsContainer');
    questionsContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">${i18n.loadingReview}</p>`;

    openModal('gradeModal');

    try {
        const res = await fetch(`${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review-details`);
        const data = await res.json();
        if (data.success && data.questions && data.questions.length > 0) {
            let html = '';
            data.questions.forEach((q, idx) => {
                const statusBadge = q.is_correct
                    ? `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> ${i18n.correct} (+${q.points_earned}/${q.points} pts)</span>`
                    : `<span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> ${i18n.incorrect} (0/${q.points} pts)</span>`;

                let optsHtml = '';
                q.options.forEach(opt => {
                    let optStyle = 'bg-white border-slate-200 text-slate-700';
                    let badge = '';

                    if (opt.is_correct && opt.is_selected) {
                        optStyle = 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold';
                        badge = `<span class="text-emerald-600 font-mono text-[10px]">${i18n.studentCorrectPick}</span>`;
                    } else if (opt.is_correct) {
                        optStyle = 'bg-teal-50 border-teal-300 text-teal-900 font-bold';
                        badge = `<span class="text-teal-600 font-mono text-[10px]">${i18n.correctKey}</span>`;
                    } else if (opt.is_selected) {
                        optStyle = 'bg-red-50 border-red-300 text-red-900 font-bold';
                        badge = `<span class="text-red-600 font-mono text-[10px]">${i18n.studentWrongPick}</span>`;
                    }

                    optsHtml += `<div class="p-2.5 rounded-xl border ${optStyle} text-xs flex items-center justify-between gap-2">
                        <span>${opt.option_text}</span>
                        ${badge}
                    </div>`;

                    if (opt.explanation && opt.is_correct) {
                        optsHtml += `<p class="text-[11px] text-slate-500 font-mono italic pl-2">${i18n.explanation} ${opt.explanation}</p>`;
                    }
                });

                html += `<div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900">Q${idx + 1}: ${q.question_text || i18n.question}</span>
                        ${statusBadge}
                    </div>
                    <div class="space-y-1.5 pt-1">
                        ${optsHtml}
                    </div>
                </div>`;
            });
            questionsContainer.innerHTML = html;
        } else {
            questionsContainer.innerHTML = `<p class="text-xs text-slate-500 italic text-center py-4">${i18n.noQuestionBreakdown}</p>`;
        }
    } catch (err) {
        questionsContainer.innerHTML = `<p class="text-xs text-red-500 italic text-center py-4">${i18n.unableToLoadBreakdown}</p>`;
    }
}

// ── Interactive Question Builder for Assignment Creator ───────────────────────
let teacherQuestionCount = 0;
function addTeacherQuestion() {
    const container = document.getElementById('teacherQuestionsContainer');
    if (!container) return;

    const qIdx = teacherQuestionCount++;
    const isAr = @json(app()->getLocale() === 'ar');

    const html = `
    <div class="teacher-q-card p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 relative" id="teacherQCard_${qIdx}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-extrabold text-teal-900 bg-teal-100 px-3 py-1 rounded-full border border-teal-200">
                ${isAr ? 'السؤال رقم ' : 'Question #'}${qIdx + 1}
            </span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 text-xs font-mono">
                    <span class="text-slate-500">${isAr ? 'الدرجة:' : 'Pts:'}</span>
                    <input type="number" step="0.5" name="questions[${qIdx}][points]" value="1" min="0.5" class="w-14 bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-center">
                </div>
                <button type="button" onclick="removeTeacherQuestion(${qIdx})" class="text-rose-500 hover:text-rose-700 text-xs font-bold font-mono px-2 py-1 rounded-lg hover:bg-rose-50 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i> ${isAr ? 'حذف' : 'Remove'}
                </button>
            </div>
        </div>

        <div>
            <textarea name="questions[${qIdx}][question_text]" rows="2" required placeholder="${isAr ? 'اكتب نص السؤال هنا...' : 'Type question text here...'}" class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-xs font-mono focus:outline-none focus:border-teal-600"></textarea>
        </div>

        <div class="space-y-2 pt-1 border-t border-slate-200/60">
            <p class="text-[10px] font-mono text-slate-500 font-bold">${isAr ? 'الخيارات (حدد الدائرة بجانب الإجابة الصحيحة):' : 'Answer Choices (Select radio button for the correct option):'}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                ${[0, 1, 2, 3].map(optIdx => `
                    <div class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200">
                        <input type="radio" name="questions[${qIdx}][correct_index]" value="${optIdx}" ${optIdx === 0 ? 'checked' : ''} class="text-teal-600 focus:ring-teal-500 cursor-pointer">
                        <input type="text" name="questions[${qIdx}][options][${optIdx}]" required placeholder="${isAr ? 'الخيار ' + String.fromCharCode(65 + optIdx) : 'Option ' + String.fromCharCode(65 + optIdx)}" class="w-full text-xs font-mono border-0 focus:ring-0 p-0 text-slate-800">
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
}

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
        toast.className = `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
        toast.textContent = message;
        toast.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function bindAjaxForm(formId, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
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
document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto-switch tab from URL query param (e.g. ?tab=students)
    const urlParams = new URLSearchParams(window.location.search);
    const requestedTab = urlParams.get('tab');
    if (requestedTab) {
        switchTeacherTab(requestedTab);
    }

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

    // 4. Attach Student Search & Filter Listeners
    const sInput = document.getElementById('studentSearchInput');
    if (sInput) sInput.addEventListener('input', applyStudentFilters);
    const cFilter = document.getElementById('studentCourseFilter');
    if (cFilter) cFilter.addEventListener('change', applyStudentFilters);
    const gFilter = document.getElementById('studentGradeFilter');
    if (gFilter) gFilter.addEventListener('change', applyStudentFilters);
    const aFilter = document.getElementById('studentAttendanceFilter');
    if (aFilter) aFilter.addEventListener('change', applyStudentFilters);

    // Helper function to detect phone numbers in client JS
    function clientHasPhoneNumber(text) {
        if (!text) return false;
        const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        let norm = text;
        for (let i = 0; i < 10; i++) {
            norm = norm.replaceAll(eastern[i], i.toString());
        }
        if (/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i.test(norm)) return true;
        if (/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i.test(norm)) return true;
        if (/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/.test(norm)) return true;
        if (/\b[0-9]{8,16}\b/.test(norm)) return true;
        return false;
    }

    const noteTextarea = document.getElementById('noteContentTextarea');
    const phoneWarning = document.getElementById('notePhoneWarning');
    if (noteTextarea && phoneWarning) {
        noteTextarea.addEventListener('input', function () {
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
        noteForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sId = document.getElementById('noteStudentUserId').value;
            if (!sId) return;

            const noteVal = noteTextarea ? noteTextarea.value : '';
            if (clientHasPhoneNumber(noteVal)) {
                if (window.Toast) {
                    window.Toast.danger(
                        isArLocale 
                            ? 'لا يمكنك إرسال أرقام الهواتف أو وسائل التواصل في الملاحظات التعليمية حرصاً على الأمان والخصوصية' 
                            : 'Security Alert: Sharing phone numbers or contact details in notes is prohibited.',
                        isArLocale ? 'تنبيه أمان وخصوصية <i class="fa-solid fa-shield-halved"></i>' : 'Security Violation'
                    );
                }
                return;
            }

            const formData = new FormData(noteForm);
            const submitBtn = document.getElementById('saveNoteBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
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
                    if (noteTextarea) noteTextarea.classList.remove('border-rose-500', 'bg-rose-50/20');
                    if (currentViewingStudentId == sId) {
                        openStudentDetailsModal(sId);
                    }
                } else {
                    const msg = data.message || (data.errors && data.errors.note ? data.errors.note[0] : 'Failed to save note');
                    if (window.Toast) {
                        window.Toast.danger(msg, isArLocale ? 'تنبيه أمان <i class="fa-solid fa-shield-halved"></i>' : 'Security Alert');
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

    // ── Recurring Schedule Helpers ──────────────────────────────────────────
    window.openCreateRecurringModal = function() {
        openModal('recurringScheduleModal');
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
        if (previewSummary) previewSummary.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري فحص المواعيد والتعارضات...' : 'Validating dates and conflicts...'}`;

        try {
            const res = await fetch('{{ route("ajax.teacher.recurring.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();

            if (res.status === 401) {
                const sessionExpiredMsg = isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' : 'Your session has expired. Please log in again.';
                if (previewSummary) previewSummary.textContent = sessionExpiredMsg;
                if (conflictBadge) {
                    conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                    conflictBadge.innerHTML = `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'جلسة منتهية' : 'Session Expired'}`;
                }
                showTeacherToast(sessionExpiredMsg, false);
                setTimeout(() => window.location.href = '{{ route("login") }}', 1500);
                return;
            }

            if (!res.ok || !data.success) {
                const errMsg = data.message || 'Validation failed';
                if (previewSummary) previewSummary.textContent = errMsg;
                if (conflictBadge) {
                    conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                    conflictBadge.innerHTML = `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'خطأ' : 'Error'}`;
                }
                return;
            }

            if (previewSummary) {
                previewSummary.textContent = isArLocale 
                    ? `إجمالي الحصص المتولدة: ${data.total_sessions} حصة` 
                    : `Total Generated Sessions: ${data.total_sessions}`;
            }

            if (data.has_conflicts) {
                conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 animate-pulse';
                conflictBadge.innerHTML = isArLocale ? '<i class="fa-solid fa-triangle-exclamation"></i> يوجد تعارض في المواعيد' : '<i class="fa-solid fa-triangle-exclamation"></i> Schedule Conflicts Detected';
                conflictWarning.classList.remove('hidden');
                conflictWarning.innerHTML = `<span><i class="fa-solid fa-triangle-exclamation"></i> ${isArLocale ? 'تنبيه: بعض الحصص المقترحة تتعارض مع حصص سابقة لنفس المعلم أو الطالب.' : 'Warning: Some proposed sessions conflict with existing schedules.'}</span>`;
            } else {
                conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                conflictBadge.innerHTML = isArLocale ? '<i class="fa-solid fa-check"></i> المواعيد متاحة بدون تعارض' : '<i class="fa-solid fa-check"></i> All Slots Available';
                conflictWarning.classList.add('hidden');
            }

            if (tableBody) {
                tableBody.innerHTML = (data.dates || []).map(d => `
                    <tr class="hover:bg-white/80 ${d.has_conflict ? 'bg-rose-50/60 text-rose-900' : ''}">
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

    window.openEditSessionOverrideModal = function(sessionId, title, scheduledAt, duration, meetingLink, notes) {
        document.getElementById('overrideSessionId').value = sessionId;
        document.getElementById('overrideTitle').value = title || '';
        document.getElementById('overrideDateTime').value = scheduledAt || '';
        document.getElementById('overrideDuration').value = duration || 60;
        document.getElementById('overrideMeetingLink').value = meetingLink || '';
        document.getElementById('overrideReason').value = '';
        openModal('editSessionOverrideModal');
    };

    window.confirmCancelSession = function(sessionId) {
        document.getElementById('cancelSessionId').value = sessionId;
        document.getElementById('cancelReasonInput').value = '';
        openModal('cancelSessionModal');
    };

    // ── Form Handlers ────────────────────────────────────────────────────────
    bindAjaxForm('createSessionForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createSessionModal');
        setTimeout(() => location.reload(), 900);
    });

    const recForm = document.getElementById('recurringScheduleForm');
    if (recForm) {
        recForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('saveRecurringBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري إنشاء الجدول والحصص...' : 'Generating sessions...'}`;
            }

            try {
                const formData = new FormData(recForm);
                const res = await fetch('{{ route("ajax.teacher.recurring.create") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (res.status === 401) {
                    showTeacherToast(isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' : 'Your session has expired. Please log in again.', false);
                    setTimeout(() => window.location.href = '{{ route("login") }}', 1500);
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
                    submitBtn.innerHTML = `${isArLocale ? 'إنشاء جدول الحصص المتكرر' : 'Create Recurring Schedule'} &rarr;`;
                }
            }
        });
    }

    const overrideForm = document.getElementById('editSessionOverrideForm');
    if (overrideForm) {
        overrideForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sId = document.getElementById('overrideSessionId').value;
            const submitBtn = document.getElementById('saveOverrideBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
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
                    submitBtn.innerHTML = `${isArLocale ? 'حفظ التغييرات' : 'Save Changes'} &rarr;`;
                }
            }
        });
    }

    const cancelForm = document.getElementById('cancelSessionForm');
    if (cancelForm) {
        cancelForm.addEventListener('submit', async function (e) {
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

    bindAjaxForm('createAssignmentForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createAssignmentModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('meetingLinkForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('meetingLinkModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('rescheduleForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('rescheduleModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('gradeForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('gradeModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('attendanceForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('attendanceModal');
        setTimeout(() => location.reload(), 900);
    });
});
</script>
@endsection
