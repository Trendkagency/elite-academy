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
                    👨‍🏫 {{ $teacherProfile->title ?: __('Faculty Instructor') }}
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                    ⭐ {{ number_format($teacherProfile->rating_avg ?: 4.9, 1) }} {{ __('Rating') }}
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    📚 {{ $courses->count() }} {{ __('Active Courses') }}
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
                <span>🎓</span> {{ __('My Students') }}
            </button>
            <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span>➕</span> {{ __('Schedule Session') }}
            </button>
            <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-emerald-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span>📝</span> {{ __('Publish Assignment') }}
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
                <span class="text-lg">📅</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $todaySessionsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Scheduled today') }}</p>
        </div>

        {{-- KPI 2: Upcoming --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Upcoming') }}</span>
                <span class="text-lg">⏳</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-blue-600 js-counter" data-target="{{ $upcomingSessionsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Future cohorts') }}</p>
        </div>

        {{-- KPI 3: Students --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-teal-400" onclick="switchTeacherTab('students')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('My Students') }}</span>
                <span class="text-lg">🎓</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 js-counter" data-target="{{ $assignedStudentsCount }}">0</p>
            <p class="text-[11px] text-teal-600 font-semibold flex items-center gap-1">{{ __('View roster →') }}</p>
        </div>

        {{-- KPI 4: Pending Assignments --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-orange-400" onclick="switchTeacherTab('assignments')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Need Grading') }}</span>
                <span class="text-lg">📝</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-orange-500 js-counter" data-target="{{ $pendingAssignmentsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Submissions queue') }}</p>
        </div>

        {{-- KPI 5: Total Submissions --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Submissions') }}</span>
                <span class="text-lg">📊</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $submittedAssignmentsCount }}">0</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Total handled') }}</p>
        </div>

        {{-- KPI 6: Attendance Rate --}}
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase">{{ __('Attendance Rate') }}</span>
                <span class="text-lg">✅</span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600"><span class="js-counter" data-target="{{ $attendanceRate }}">0</span>%</p>
            <p class="text-[11px] text-slate-500 font-semibold">{{ __('Historical sessions') }}</p>
        </div>
    </div>

    {{-- Teacher Navigation Tabs --}}
    <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto scrollbar-thin">
        <button type="button" onclick="switchTeacherTab('overview')" id="tab-btn-overview" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'overview' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            📊 {{ __('Overview & Today') }}
        </button>
        <button type="button" onclick="switchTeacherTab('students')" id="tab-btn-students" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'students' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            🎓 {{ __('My Students') }} ({{ $assignedStudentsCount }})
        </button>
        <button type="button" onclick="switchTeacherTab('sessions')" id="tab-btn-sessions" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'sessions' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            📅 {{ __('Sessions & Streams') }}
        </button>
        <button type="button" onclick="switchTeacherTab('assignments')" id="tab-btn-assignments" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'assignments' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
            📝 {{ __('Assignments & Quizzes') }}
            @if($pendingAssignmentsCount > 0)
                <span class="ms-1.5 px-2 py-0.5 text-[10px] bg-orange-500 text-white rounded-full font-mono font-bold">{{ $pendingAssignmentsCount }}</span>
            @endif
        </button>
        <button type="button" onclick="switchTeacherTab('attendance')" id="tab-btn-attendance" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'attendance' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
            📋 {{ __('Attendance Tracker') }}
        </button>
        <button type="button" onclick="switchTeacherTab('notifications')" id="tab-btn-notifications" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'notifications' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
            🔔 {{ __('Notifications') }}
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
                                <span>🔴</span> {{ __('Today\'s Teaching Sessions') }}
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
                                                ⏱️ {{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : 'Scheduled' }} ({{ $session->duration_minutes }}m)
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
                                                <span>🎥</span> {{ __('Join / Broadcast') }}
                                            </a>
                                        @else
                                            <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '')" class="btn-lift px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                                🔗 {{ __('Add Link') }}
                                            </button>
                                        @endif
                                        <button type="button" onclick="openAttendanceModal({{ $session->id }}, '{{ addslashes($session->title) }}')" class="btn-lift px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200">
                                            📋 {{ __('Attendance') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-dashed border-slate-200 space-y-3">
                            <span class="text-3xl">☕</span>
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
                            <span>📝</span> {{ __('Pending Grading Queue') }}
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
                                        ✍️ {{ __('Grade Now') }}
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
                                            <span>📚</span> {{ $sessionCount }} {{ $sessionLabel }}
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
                        <span>🎓</span> {{ __('app.teacher.my_students') }}
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
                        <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs">🔍</span>
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
                                        <span>🏫</span> {{ $st->school_name ?: __('Elite Academy') }}
                                    </p>
                                    <p class="truncate flex items-center gap-1.5 text-slate-500">
                                        <span>🎓</span> {{ $st->gradeLevel?->name ?: __('Secondary Level') }}
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
                                    <span>🎓</span> {{ __('app.teacher.student_profile') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty Search Result State --}}
                <div id="studentEmptySearchState" class="hidden text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <span class="text-3xl">🔍</span>
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
                        <span>📅</span> {{ __('Live Teaching Schedule & Meeting Links') }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Manage your live sessions, recurring cohorts, update broadcast URLs, and reschedule classes.') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCreateRecurringModal()" class="btn-lift px-4 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span>🔄</span> {{ __('Create Recurring Schedule') }}
                    </button>
                    <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span>➕</span> {{ __('Schedule Single Session') }}
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
                                                        <span>🔄</span> {{ __('Recurring') }}
                                                    </span>
                                                @endif
                                                @if($session->is_override)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                        <span>⚠️</span> {{ __('Override') }}
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
                                            ✏️ {{ __('Edit Scope') }}
                                        </button>
                                        <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link ?? '') }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            🔗 {{ __('Link') }}
                                        </button>
                                        <button type="button" onclick="openRescheduleModal({{ $session->id }}, '{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            🗓️ {{ __('Reschedule') }}
                                        </button>
                                        @if(!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                            <button type="button" onclick="confirmCancelSession({{ $session->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                                ❌ {{ __('Cancel') }}
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
                        <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-3 flex flex-col justify-between hover:border-teal-400 hover:bg-white transition-all group shadow-xs">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <span class="font-mono font-bold text-teal-700 uppercase truncate flex-1 min-w-0">{{ $assignment->course?->title ?: __('Course') }}</span>
                                    <span class="px-2.5 py-1 bg-teal-50 text-teal-800 border border-teal-200/80 text-[11px] font-mono font-extrabold rounded-xl shrink-0 whitespace-nowrap shadow-2xs">
                                        📝 {{ $subCount }} {{ $subLabel }}
                                    </span>
                                </div>
                                <h3 class="font-heading font-black text-base text-slate-900 leading-snug group-hover:text-teal-700 transition-colors">{{ $assignment->title }}</h3>
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $assignment->description ?: __('Homework assignment for student revision.') }}</p>
                            </div>
                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-mono text-slate-500">
                                <span class="truncate">📅 {{ __('Due') }}: {{ $assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline') }}</span>
                                <span class="font-extrabold text-slate-800 shrink-0 ms-2 bg-slate-100 px-2 py-0.5 rounded-lg">🎯 {{ $assignment->passing_score ?: 70 }}% {{ __('Pass') }}</span>
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
                                            🔍 {{ $isReviewed ? __('Review & Grade') : __('Review Submission') }}
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
                                    📋 {{ __('Mark') }}
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
<div id="studentProfileModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col overflow-hidden relative">
        
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
                    <span>✍️</span> {{ __('app.teacher.add_educational_note') }}
                </button>
                <button type="button" onclick="closeModal('studentProfileModal')" class="text-slate-300 hover:text-white font-bold text-xl p-1 cursor-pointer" aria-label="Close">
                    ✕
                </button>
            </div>
        </div>

        {{-- Educational Profile Sub-Navigation Tabs --}}
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-2.5 flex items-center gap-2 overflow-x-auto shrink-0 scrollbar-thin">
            <button type="button" onclick="switchSpTab('overview')" id="sp-tab-btn-overview" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap bg-teal-600 text-white shadow-xs">
                📊 {{ __('Overview') }}
            </button>
            <button type="button" onclick="switchSpTab('courses')" id="sp-tab-btn-courses" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                📚 {{ __('Courses') }}
            </button>
            <button type="button" onclick="switchSpTab('sessions')" id="sp-tab-btn-sessions" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                📅 {{ __('Sessions') }}
            </button>
            <button type="button" onclick="switchSpTab('attendance')" id="sp-tab-btn-attendance" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                📋 {{ __('Attendance') }}
            </button>
            <button type="button" onclick="switchSpTab('assignments')" id="sp-tab-btn-assignments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                📝 {{ __('Assignments') }}
            </button>
            <button type="button" onclick="switchSpTab('assessments')" id="sp-tab-btn-assessments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                🎯 {{ __('Assessments') }}
            </button>
            <button type="button" onclick="switchSpTab('progress')" id="sp-tab-btn-progress" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                📈 {{ __('Progress') }}
            </button>
            <button type="button" onclick="switchSpTab('notes')" id="sp-tab-btn-notes" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                💬 {{ __('Notes') }}
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
                            <span>🕒</span> {{ __('Recent Live Sessions Attended') }}
                        </h4>
                        <div id="spOverviewRecentSessions" class="space-y-2 text-xs font-mono">
                            {{-- Populated by JS --}}
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                        <h4 class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                            <span>📝</span> {{ __('Recent Homework & Submissions') }}
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
<div id="addNoteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-heading font-black text-lg text-slate-900">{{ __('app.teacher.add_educational_note') }}</h3>
            <button type="button" onclick="closeModal('addNoteModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
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
                    <span>🛡️</span>
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
<div id="createSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Schedule New Live Session') }}</h3>
            <button type="button" onclick="closeModal('createSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
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
<div id="recurringScheduleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                    <span>🔄</span> {{ __('Create Recurring Schedule') }}
                </h3>
                <p class="text-xs font-mono text-slate-500 mt-0.5">{{ __('Automatically generate recurring class sessions with conflict detection.') }}</p>
            </div>
            <button type="button" onclick="closeModal('recurringScheduleModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
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
                    <span>🔍</span> {{ __('Preview Generated Sessions & Validate Conflicts') }}
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
<div id="editSessionOverrideModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Edit Session & Recurrence Scope') }}</h3>
            <button type="button" onclick="closeModal('editSessionOverrideModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
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
<div id="cancelSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-heading font-black text-xl text-rose-600 flex items-center gap-2">
                <span>❌</span> {{ __('Cancel Session') }}
            </h3>
            <button type="button" onclick="closeModal('cancelSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
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
<div id="meetingLinkModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Update Live Stream Link') }}</h3>
            <button type="button" onclick="closeModal('meetingLinkModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
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
<div id="rescheduleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Reschedule Teaching Session') }}</h3>
            <button type="button" onclick="closeModal('rescheduleModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
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
<div id="createAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Publish New Assignment & Quiz') }}</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ __('Create homework assignments or interactive MSQ quizzes for your students.') }}</p>
            </div>
            <button type="button" onclick="closeModal('createAssignmentModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
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
{{-- MODAL 5: GRADE ASSIGNMENT SUBMISSION & QUESTION REVIEW                       --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="gradeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Review & Grade Submission') }}</h3>
                <p id="gradeStudentName" class="text-xs text-teal-600 font-mono font-bold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('gradeModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer">✕</button>
        </div>

        {{-- Question By Question Auto-Correction Breakdown --}}
        <div class="space-y-3">
            <h4 class="font-heading font-black text-sm text-slate-900 flex items-center gap-2">
                <span>🎯</span> {{ __('Questions Auto-Correction & Student Choices') }}
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
<div id="attendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-5 relative max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 shrink-0">
            <div class="min-w-0 space-y-0.5">
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Record Session Attendance') }}</h3>
                <p id="attendanceSessionTitle" class="text-xs text-teal-600 font-mono font-bold truncate"></p>
            </div>
            <button type="button" onclick="closeModal('attendanceModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer p-1">✕</button>
        </div>

        {{-- Quick Bulk Actions & Cohort Header --}}
        <div class="flex items-center justify-between gap-2 shrink-0">
            <label class="block text-[11px] font-mono font-bold text-slate-500 uppercase tracking-wider">
                {{ __('COHORT STUDENT CHECK-IN') }} (<span id="attendanceCohortCount">0</span>)
            </label>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="bulkSetAttendance('present')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg border border-emerald-200 transition-colors cursor-pointer">
                    🟢 {{ __('All Present') }}
                </button>
                <button type="button" onclick="bulkSetAttendance('absent')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-800 text-[11px] font-bold rounded-lg border border-rose-200 transition-colors cursor-pointer">
                    🔴 {{ __('All Absent') }}
                </button>
            </div>
        </div>

        <form id="attendanceForm" method="POST" class="space-y-4 flex-1 flex flex-col overflow-hidden">
            @csrf
            <input type="hidden" id="attendanceSessionId">

            {{-- Dynamic Scrollable Students Container --}}
            <div id="attendanceStudentsContainer" class="divide-y divide-slate-100 overflow-y-auto flex-1 p-1 scrollbar-thin">
                {{-- Populated dynamically in real-time via AJAX --}}
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('attendanceModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">{{ __('Cancel') }}</button>
                <button type="submit" id="saveAttendanceBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                    <span>→</span> {{ __('Save Attendance Sheet') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const isArLocale = @json(app()->getLocale() === 'ar');

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
    passed: @json(__('Passed ✓')),
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
        const res = await fetch(`/ajax/teacher/students/${studentUserId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (!data.success) {
            showTeacherToast(data.message || 'Unauthorized access', false);
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
        document.getElementById('spModalMeta').textContent = `${st.school || 'Elite Academy'} • ${st.grade || 'Secondary'} • 📧 ${st.email || ''}`;

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
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold">🟢 Present</span>'
                    : (s.attendance_status === 'late'
                        ? '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold">🟡 Late</span>'
                        : '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold">🔴 Absent</span>');
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
                            🔍 Review
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
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">✓ Passed</span>'
                    : (ass.score !== null 
                        ? '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full">✕ Retake</span>'
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
    const m = document.getElementById(id);
    if (m) m.classList.remove('hidden');
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('hidden');
}

function openCreateSessionModal() {
    openModal('createSessionModal');
}

function openCreateAssignmentModal() {
    openModal('createAssignmentModal');
}

function openMeetingLinkModal(sessionId, currentLink) {
    document.getElementById('linkSessionId').value = sessionId;
    document.getElementById('meetingUrlInput').value = currentLink || '';
    document.getElementById('meetingLinkForm').action = `/ajax/teacher/sessions/${sessionId}/link`;
    openModal('meetingLinkModal');
}

function openRescheduleModal(sessionId, currentDateTime) {
    document.getElementById('rescheduleSessionId').value = sessionId;
    document.getElementById('rescheduleDateTime').value = currentDateTime || '';
    document.getElementById('rescheduleForm').action = `/ajax/teacher/sessions/${sessionId}/reschedule`;
    openModal('rescheduleModal');
}

async function openAttendanceModal(sessionId, sessionTitle) {
    document.getElementById('attendanceSessionId').value = sessionId;
    document.getElementById('attendanceSessionTitle').textContent = sessionTitle || 'Loading...';
    document.getElementById('attendanceForm').action = `/ajax/teacher/sessions/${sessionId}/attendance`;

    const container = document.getElementById('attendanceStudentsContainer');
    container.innerHTML = `
        <div class="py-12 text-center space-y-2">
            <svg class="animate-spin h-6 w-6 text-teal-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-xs font-mono text-slate-500 font-bold">${i18n.loadingReview || 'Loading registered students...'}</p>
        </div>
    `;
    document.getElementById('attendanceCohortCount').textContent = '...';

    openModal('attendanceModal');

    try {
        const res = await fetch(`/ajax/teacher/sessions/${sessionId}/attendance-roster`, {
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
                <div class="py-10 text-center space-y-2">
                    <span class="text-2xl">👥</span>
                    <p class="text-xs font-semibold text-slate-700">${isArLocale ? 'لا يوجد طلاب مسجلين في هذا الكورس حالياً.' : 'No students enrolled in this course yet.'}</p>
                    <p class="text-[10px] font-mono text-slate-400">${isArLocale ? 'سيظهر الطلاب المسجلون تلقائياً بمجرد اشتراكهم.' : 'Enrolled students will appear here automatically.'}</p>
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
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            ${(st.name || 'S').substring(0, 1).toUpperCase()}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 truncate">${st.name}</p>
                            <p class="text-[10px] font-mono text-slate-500 truncate">${st.school || 'Elite Academy'} ${st.grade ? '• ' + st.grade : ''}</p>
                        </div>
                    </div>

                    <input type="hidden" name="attendance[${idx}][student_user_id]" value="${st.id}">
                    <div class="shrink-0">
                        <select name="attendance[${idx}][status]" onchange="onAttendanceStatusChange('${st.name ? st.name.replace(/'/g, "\\'") : ''}', this.value)" class="attendance-status-select bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-600 shadow-2xs cursor-pointer">
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
        container.innerHTML = `<p class="text-xs text-rose-600 italic text-center py-6">Failed to load real-time attendance roster.</p>`;
    }
}

function onAttendanceStatusChange(studentName, newStatus) {
    const statusMap = {
        present: {
            title: isArLocale ? 'تسجيل حضور' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ حاضر 🟢` : `Marked (${studentName}) as Present 🟢`,
            type: 'success'
        },
        late: {
            title: isArLocale ? 'تسجيل تأخير' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ متأخر 🟡` : `Marked (${studentName}) as Late 🟡`,
            type: 'warning'
        },
        excused: {
            title: isArLocale ? 'تسجيل عذر' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ معذور ⚪` : `Marked (${studentName}) as Excused ⚪`,
            type: 'info'
        },
        absent: {
            title: isArLocale ? 'تسجيل غياب' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ غائب 🔴` : `Marked (${studentName}) as Absent 🔴`,
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
            window.Toast.success(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ حضور 🟢' : 'All students marked as Present 🟢', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        } else if (status === 'absent') {
            window.Toast.danger(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ غياب 🔴' : 'All students marked as Absent 🔴', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        }
    }
}

async function confirmCancelSession(sessionId) {
    if (!confirm(@json(__('Are you sure you want to cancel this live session? Affected students will be notified immediately.')))) {
        return;
    }

    try {
        const res = await fetch(`/ajax/teacher/sessions/${sessionId}/cancel`, {
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
    document.getElementById('gradeForm').action = `/ajax/teacher/submissions/${submissionId}/review`;

    const questionsContainer = document.getElementById('submissionQuestionsContainer');
    questionsContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">${i18n.loadingReview}</p>`;

    openModal('gradeModal');

    try {
        const res = await fetch(`/ajax/teacher/submissions/${submissionId}/review-details`);
        const data = await res.json();
        if (data.success && data.questions && data.questions.length > 0) {
            let html = '';
            data.questions.forEach((q, idx) => {
                const statusBadge = q.is_correct
                    ? `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold rounded-full">🟢 ${i18n.correct} (+${q.points_earned}/${q.points} pts)</span>`
                    : `<span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-bold rounded-full">🔴 ${i18n.incorrect} (0/${q.points} pts)</span>`;

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
                    ✕ ${isAr ? 'حذف' : 'Remove'}
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
                        isArLocale ? 'تنبيه أمان وخصوصية 🛡️' : 'Security Violation'
                    );
                }
                return;
            }

            const formData = new FormData(noteForm);
            const submitBtn = document.getElementById('saveNoteBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `⏳ ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
            }

            try {
                const res = await fetch(`/ajax/teacher/students/${sId}/notes`, {
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
                        window.Toast.danger(msg, isArLocale ? 'تنبيه أمان 🛡️' : 'Security Alert');
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
        if (previewSummary) previewSummary.innerHTML = `⏳ ${isArLocale ? 'جاري فحص المواعيد والتعارضات...' : 'Validating dates and conflicts...'}`;

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

            if (!res.ok || !data.success) {
                const errMsg = data.message || 'Validation failed';
                if (previewSummary) previewSummary.textContent = errMsg;
                if (conflictBadge) {
                    conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                    conflictBadge.textContent = '❌ Error';
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
                conflictBadge.textContent = isArLocale ? '⚠️ يوجد تعارض في المواعيد' : '⚠️ Schedule Conflicts Detected';
                conflictWarning.classList.remove('hidden');
                conflictWarning.innerHTML = `<span>⚠️ ${isArLocale ? 'تنبيه: بعض الحصص المقترحة تتعارض مع حصص سابقة لنفس المعلم أو الطالب.' : 'Warning: Some proposed sessions conflict with existing schedules.'}</span>`;
            } else {
                conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                conflictBadge.textContent = isArLocale ? '✓ المواعيد متاحة بدون تعارض' : '✓ All Slots Available';
                conflictWarning.classList.add('hidden');
            }

            if (tableBody) {
                tableBody.innerHTML = (data.dates || []).map(d => `
                    <tr class="hover:bg-white/80 ${d.has_conflict ? 'bg-rose-50/60 text-rose-900' : ''}">
                        <td class="py-1.5 px-2 font-bold">${d.date}</td>
                        <td class="py-1.5 px-2 text-slate-500">${d.day_name}</td>
                        <td class="py-1.5 px-2">${d.start_time} - ${d.end_time}</td>
                        <td class="py-1.5 px-2 font-bold ${d.has_conflict ? 'text-rose-600' : 'text-emerald-600'}">
                            ${d.has_conflict ? '⚠️ ' + (isArLocale ? 'تعارض' : 'Conflict') : '✓ ' + (isArLocale ? 'متاح' : 'Available')}
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
                submitBtn.innerHTML = `⏳ ${isArLocale ? 'جاري إنشاء الجدول والحصص...' : 'Generating sessions...'}`;
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
                submitBtn.innerHTML = `⏳ ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
            }

            try {
                const formData = new FormData(overrideForm);
                const res = await fetch(`/ajax/teacher/sessions/${sId}/override`, {
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
                const res = await fetch(`/ajax/teacher/sessions/${sId}/cancel`, {
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
