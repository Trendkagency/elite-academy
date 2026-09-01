@extends('layouts.portal-panel')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $todayDateStr = \Carbon\Carbon::today()->format('l, F j, Y');
    $activeTabKey = in_array($activeTab ?? 'overview', ['overview', 'sessions', 'assignments', 'attendance', 'students', 'notifications']) ? ($activeTab ?? 'overview') : 'overview';
@endphp

<div class="space-y-8">

        {{-- Header & Faculty Greeting Banner --}}
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-3 relative z-10 max-w-2xl">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        👨‍🏫 {{ $teacherProfile->title ?: __('Faculty Instructor') }}
                    </span>
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                        ⭐ {{ number_format($teacherProfile->rating_avg ?: 4.9, 1) }} {{ __('Rating') }}
                    </span>
                </div>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ __('Welcome back') }}, <span class="text-teal-400">{{ auth()->user()->name }}</span>
                </h1>
                <p class="text-slate-300 text-sm sm:text-base font-medium leading-relaxed">
                    {{ __('Manage your teaching sessions, track student attendance, grade assignments, and monitor academic performance.') }}
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-3 shrink-0">
                <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer">
                    <span>➕</span> {{ __('Schedule New Session') }}
                </button>
            </div>
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        {{-- Toast Alert Container --}}
        <div id="teacherToastAlert" class="hidden p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md"></div>

        {{-- KPI Statistics Grid (With Count-Up Counter Animations) --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
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
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Students') }}</span>
                    <span class="text-lg">🎓</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 js-counter" data-target="{{ $assignedStudentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Enrolled learners') }}</p>
            </div>

            {{-- KPI 4: Pending Assignments --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Need Grading') }}</span>
                    <span class="text-lg">📝</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-orange-500 js-counter" data-target="{{ $pendingAssignmentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Submissions') }}</p>
            </div>

            {{-- KPI 5: Total Submissions --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Submissions') }}</span>
                    <span class="text-lg">📊</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $submittedAssignmentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Handled total') }}</p>
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
        <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto">
            <button type="button" onclick="switchTeacherTab('overview')" id="tab-btn-overview" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'overview' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📊 {{ __('Overview & Today') }}
            </button>
            <button type="button" onclick="switchTeacherTab('sessions')" id="tab-btn-sessions" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'sessions' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📅 {{ __('Sessions & Streams') }}
            </button>
            <button type="button" onclick="switchTeacherTab('assignments')" id="tab-btn-assignments" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'assignments' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
                📝 {{ __('Assignments & Submissions') }}
                @if($pendingAssignmentsCount > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-[10px] bg-orange-500 text-white rounded-full font-mono">{{ $pendingAssignmentsCount }}</span>
                @endif
            </button>
            <button type="button" onclick="switchTeacherTab('attendance')" id="tab-btn-attendance" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'attendance' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📋 {{ __('Attendance Tracker') }}
            </button>
            <button type="button" onclick="switchTeacherTab('students')" id="tab-btn-students" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'students' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                🎓 {{ __('My Students') }} ({{ $assignedStudentsCount }})
            </button>
            <button type="button" onclick="switchTeacherTab('notifications')" id="tab-btn-notifications" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'notifications' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
                🔔 {{ __('Notifications') }}
                @if($unreadNotifCount > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-mono">{{ $unreadNotifCount }}</span>
                @endif
            </button>
        </div>

        {{-- TAB 1: OVERVIEW & TODAY'S SESSIONS --}}
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
                                    @php
                                        $state = $session->evaluateState(auth()->user());
                                        $isLive = $state === \App\Enums\LiveSessionState::LIVE;
                                        $isStartingSoon = $state === \App\Enums\LiveSessionState::BEFORE_JOINABLE;
                                    @endphp
                                    <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-4 hover:border-teal-300 transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-xs font-mono font-bold text-teal-600 uppercase">{{ $session->subject?->name ?: __('General') }}</span>
                                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full {{ $isLive ? 'bg-red-500 text-white animate-pulse' : ($isStartingSoon ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700') }}">
                                                        {{ $state->label() }}
                                                    </span>
                                                </div>
                                                <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $session->title ?: __('Live Cohort Session') }}</h3>
                                            </div>
                                            <div class="text-left sm:text-right font-mono text-xs font-semibold text-slate-600">
                                                <p>⏰ {{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : 'Scheduled' }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $session->duration_minutes ?: 60 }} mins</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                            <div class="text-xs font-semibold text-slate-700 flex items-center gap-2">
                                                <span>👨‍🎓 {{ __('Student') }}: {{ $session->studentUser?->name ?: __('Enrolled Cohort') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if(!empty($session->meeting_link))
                                                    <a href="{{ $session->meeting_link }}" target="_blank" class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm">
                                                        📺 {{ __('Launch Broadcast') }}
                                                    </a>
                                                @else
                                                    <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link) }}')" class="btn-lift px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-sm">
                                                        🔗 {{ __('Add Meeting URL') }}
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openAttendanceModal({{ $session->id }}, '{{ addslashes($session->title) }}')" class="btn-lift px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm">
                                                    📋 {{ __('Mark Attendance') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-[#FAFAF9] rounded-2xl p-8 text-center space-y-3 border border-slate-200/80">
                                <div class="text-4xl">☕</div>
                                <h3 class="font-bold text-base text-slate-800">{{ __('No teaching sessions scheduled for today.') }}</h3>
                                <p class="text-xs text-slate-500">{{ __('You have no active live broadcasts for today. Prepare upcoming assignments or check your roster.') }}</p>
                                <button type="button" onclick="openCreateSessionModal()" class="btn-lift inline-block px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl mt-2">
                                    + {{ __('Schedule Session') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submissions Needing Review Sidebar Card --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                                <span>📝</span> {{ __('Pending Review') }}
                            </h3>
                            <span class="px-2.5 py-0.5 bg-orange-100 text-orange-700 font-mono text-xs font-bold rounded-full">
                                {{ $pendingSubmissions->count() }}
                            </span>
                        </div>

                        @if($pendingSubmissions->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingSubmissions->take(4) as $sub)
                                    <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-slate-900">{{ $sub->studentUser?->name ?: __('Student') }}</span>
                                            <span class="font-mono text-[10px] text-slate-500">{{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 line-clamp-1 font-medium">{{ $sub->assignment?->title ?: __('Assignment') }}</p>
                                        <button type="button" onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->studentUser?->name) }}', '{{ addslashes($sub->assignment?->title) }}', '{{ $sub->score }}')" class="btn-lift block w-full text-center py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                                            ✏️ {{ __('Grade Submission') }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-500 space-y-2">
                                <span class="text-3xl">✨</span>
                                <p class="text-xs font-semibold">{{ __('All submissions have been reviewed!') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: SESSIONS & LIVE STREAMS --}}
        <div id="teacher-tab-sessions" class="teacher-tab-content {{ $activeTabKey === 'sessions' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Session & Live Stream Directory') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Manage meeting links, reschedule cohorts, or trigger cancellations.') }}</p>
                    </div>
                    <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                        + {{ __('Create New Session') }}
                    </button>
                </div>

                @if($allSessions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-3 px-4">{{ __('Session / Course') }}</th>
                                    <th class="py-3 px-4">{{ __('Scheduled Date') }}</th>
                                    <th class="py-3 px-4">{{ __('Meeting URL') }}</th>
                                    <th class="py-3 px-4">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 text-right rtl:text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($allSessions as $session)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-slate-900">{{ $session->title ?: __('Live Session') }}</p>
                                            <p class="text-xs text-slate-500 font-mono">{{ $session->course?->title ?: $session->subject?->name }}</p>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs">
                                            <p class="font-bold text-slate-800">{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d') : '' }}</p>
                                            <p class="text-slate-500">{{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : '' }}</p>
                                        </td>
                                        <td class="py-4 px-4 max-w-xs truncate">
                                            @if($session->meeting_link)
                                                <a href="{{ $session->meeting_link }}" target="_blank" class="text-xs font-mono text-teal-600 hover:underline truncate block">
                                                    {{ $session->meeting_link }}
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 italic">{{ __('No link attached') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @php
                                                $statusText = match((string) $session->status) {
                                                    'scheduled' => __('Scheduled'),
                                                    'starting_soon' => __('Starting Soon'),
                                                    'live' => __('Live Now'),
                                                    'completed' => __('Completed'),
                                                    'cancelled' => __('Cancelled'),
                                                    'cancelled_by_teacher' => __('Cancelled by Teacher'),
                                                    default => __('Scheduled'),
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-full {{ in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-teal-100 text-teal-700') }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right rtl:text-left space-x-1 rtl:space-x-reverse whitespace-nowrap">
                                            <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link) }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg transition-colors">
                                                🔗 {{ __('Link') }}
                                            </button>
                                            <button type="button" onclick="openRescheduleModal({{ $session->id }}, '{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors">
                                                🗓️ {{ __('Reschedule') }}
                                            </button>
                                            @if(!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                                <button type="button" onclick="confirmCancelSession({{ $session->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg transition-colors">
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

        {{-- TAB 3: ASSIGNMENTS & SUBMISSIONS --}}
        <div id="teacher-tab-assignments" class="teacher-tab-content {{ $activeTabKey === 'assignments' ? '' : 'hidden' }} space-y-8">
            {{-- Assignments Header & Publish Action --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Assignments & Homework Manager') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Publish new course assignments and review student work.') }}</p>
                    </div>
                    <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                        + {{ __('Publish New Assignment') }}
                    </button>
                </div>

                @if($assignments->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($assignments as $assignment)
                            <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-mono font-bold text-teal-600 uppercase">{{ $assignment->course?->title ?: __('Course') }}</span>
                                        <span class="px-2 py-0.5 bg-teal-100 text-teal-800 text-[10px] font-mono font-bold rounded-full">
                                            {{ $assignment->submissions->count() }} {{ __('Submissions') }}
                                        </span>
                                    </div>
                                    <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $assignment->title }}</h3>
                                    <p class="text-xs text-slate-600 line-clamp-2">{{ $assignment->description ?: __('Homework assignment for student revision.') }}</p>
                                </div>
                                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-mono text-slate-500">
                                    <span>{{ __('Due') }}: {{ $assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline') }}</span>
                                    <span class="font-bold text-slate-800">{{ $assignment->passing_score ?: 70 }}% {{ __('Pass') }}</span>
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

        {{-- TAB 4: ATTENDANCE TRACKER --}}
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
                            @foreach($todaySessions->merge($allSessions->take(6))->unique('id') as $sess)
                                <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-sm text-slate-900">{{ $sess->title ?: __('Live Session') }}</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $sess->effective_start_at ? $sess->effective_start_at->format('Y-m-d h:i A') : '' }}</p>
                                    </div>
                                    <button type="button" onclick="openAttendanceModal({{ $sess->id }}, '{{ addslashes($sess->title) }}')" class="btn-lift px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">
                                        📋 {{ __('Mark Attendance') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No active sessions available for attendance tracking.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 5: MY STUDENTS ROSTER --}}
        <div id="teacher-tab-students" class="teacher-tab-content {{ $activeTabKey === 'students' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Assigned Student Roster') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Authorized learners enrolled in your active courses and cohorts.') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="text" id="studentSearchInput" onkeyup="filterTeacherStudents()" placeholder="🔍 {{ __('Search student by name, email, school...') }}" class="px-4 py-2 text-xs border border-slate-200 rounded-xl bg-white focus:outline-none focus:border-teal-600 w-64">
                        <span class="px-3.5 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200 shrink-0">
                            {{ $assignedStudentsCount }} {{ __('Students') }}
                        </span>
                    </div>
                </div>

                @if($assignedStudents->count() > 0)
                    <div id="studentsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($assignedStudents as $st)
                            <div class="student-card bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-4 hover:border-teal-300 transition-colors" data-name="{{ strtolower($st->user?->name) }}" data-email="{{ strtolower($st->user?->email) }}" data-school="{{ strtolower($st->school_name) }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-base shadow-md shrink-0">
                                        {{ substr($st->user?->name ?: 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-heading font-extrabold text-sm text-slate-900 truncate">{{ $st->user?->name ?: __('Student') }}</h3>
                                        <p class="text-xs text-slate-500 font-mono truncate">{{ $st->gradeLevel?->name ?: __('High School') }}</p>
                                    </div>
                                </div>

                                <div class="pt-2 text-xs font-mono text-slate-600 space-y-1.5 border-t border-slate-200/60">
                                    <p class="truncate">📧 {{ $st->user?->email }}</p>
                                    <p class="truncate">🏫 {{ $st->school_name ?: __('Elite Academy') }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 text-center font-mono text-[11px]">
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[10px]">{{ __('Attendance') }}</span>
                                        <span class="font-bold text-emerald-600">{{ $st->attendance_rate }}%</span>
                                    </div>
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[10px]">{{ __('Avg Grade') }}</span>
                                        <span class="font-bold text-teal-600">{{ $st->avg_score !== null ? $st->avg_score . '%' : 'N/A' }}</span>
                                    </div>
                                </div>

                                <button type="button" onclick="openStudentDetailsModal({{ $st->user_id }})" class="btn-lift w-full text-center py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                    🎓 {{ __('Academic Profile') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No students enrolled in your courses yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 6: NOTIFICATIONS CENTER --}}
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
</section>

{{-- MODAL 1: SCHEDULE NEW SESSION --}}
<div id="createSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Schedule New Live Session') }}</h3>
            <button type="button" onclick="closeModal('createSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="createSessionForm" action="{{ route('ajax.teacher.sessions.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}</label>
                <select name="course_id" required class="input-mobile bg-white">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->subject?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}</label>
                <input type="text" name="title" placeholder="e.g. Session 4: Electromagnetism & Ohm's Law" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }}</label>
                    <input type="datetime-local" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }}</label>
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

{{-- MODAL 2: MEETING LINK EDITOR --}}
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

{{-- MODAL 3: RESCHEDULE SESSION --}}
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

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('rescheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Confirm Reschedule') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: PUBLISH ASSIGNMENT --}}
<div id="createAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Publish New Assignment') }}</h3>
            <button type="button" onclick="closeModal('createAssignmentModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="createAssignmentForm" action="{{ route('ajax.teacher.assignments.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}</label>
                <select name="course_id" required class="input-mobile bg-white">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Assignment Title') }}</label>
                <input type="text" name="title" placeholder="e.g. Kirchhoff's Laws Practice Worksheet" required class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Instructions / Description') }}</label>
                <textarea name="description" rows="3" placeholder="Solve all questions and upload work or text response..." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Submission Deadline') }}</label>
                    <input type="datetime-local" name="due_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Passing Score (%)') }}</label>
                    <input type="number" name="passing_score" value="70" min="0" max="100" class="input-mobile">
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

{{-- MODAL 5: GRADE & REVIEW SUBMISSION --}}
<div id="gradeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-3xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Review & Grade Submission') }}</h3>
                <p id="gradeStudentName" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('gradeModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        {{-- Auto-Correction Read-Only Breakdown Section --}}
        <div class="space-y-3 border-b border-slate-100 pb-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>🔍</span> {{ __('Question Auto-Correction Review (Read-Only)') }}
                </h4>
                <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full font-bold">🔒 {{ __('Read-Only') }}</span>
            </div>
            <div id="submissionQuestionsContainer" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                <p class="text-xs text-slate-400 italic text-center py-4">{{ __('Loading question breakdown...') }}</p>
            </div>
        </div>

        <form id="gradeForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="gradeSubmissionId">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Score / Percentage (%)') }}</label>
                <input type="number" step="0.1" min="0" max="100" id="gradeScoreInput" name="score" required placeholder="e.g. 95.0" class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Teacher Evaluation Notes') }}</label>
                <textarea id="gradeEvaluationNotes" name="evaluation_notes" rows="3" placeholder="Great job! Exceptional work on formulas." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('gradeModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save & Send Grade') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 6: MARK ATTENDANCE --}}
<div id="attendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Mark Session Attendance') }}</h3>
                <p id="attendanceSessionTitle" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('attendanceModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="attendanceForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="attendanceSessionId">

            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                @if($assignedStudents->count() > 0)
                    @foreach($assignedStudents as $idx => $st)
                        <div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
                            <span class="text-xs font-bold text-slate-900">{{ $st->user?->name }}</span>
                            <input type="hidden" name="attendance[{{ $idx }}][student_user_id]" value="{{ $st->user_id }}">
                            <select name="attendance[{{ $idx }}][status]" class="text-xs font-mono font-bold bg-white border border-slate-200 rounded-lg p-1.5 focus:outline-none focus:border-teal-600">
                                <option value="present">🟢 {{ __('Present') }}</option>
                                <option value="absent">🔴 {{ __('Absent') }}</option>
                                <option value="late">🟡 {{ __('Late') }}</option>
                                <option value="excused">🔵 {{ __('Excused') }}</option>
                            </select>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-slate-500 italic text-center py-4">{{ __('No enrolled students to mark attendance.') }}</p>
                @endif
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('attendanceModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save Attendance') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 7: STUDENT ACADEMIC PROFILE & ATTENDANCE / SUBMISSIONS --}}
<div id="studentDetailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 id="stModalName" class="font-heading font-black text-xl text-slate-900">Student Academic Profile</h3>
                <p id="stModalGrade" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('studentDetailsModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <div class="space-y-5 text-xs font-mono">
            <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p><strong class="text-slate-900">Email:</strong> <span id="stModalEmail" class="text-slate-700"></span></p>
                    <p><strong class="text-slate-900">School:</strong> <span id="stModalSchool" class="text-slate-700"></span></p>
                </div>
            </div>

            {{-- Attendance History Section --}}
            <div class="space-y-2">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>📅</span> {{ __('Session Attendance Log') }}
                </h4>
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] text-slate-500 uppercase font-mono border-b border-slate-200">
                                <th class="py-2.5 px-3">{{ __('Session Title') }}</th>
                                <th class="py-2.5 px-3">{{ __('Date') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="stModalAttendanceTable">
                            <tr><td colspan="3" class="p-3 text-center text-slate-400 italic">Loading attendance...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Submissions History Section --}}
            <div class="space-y-2">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>📝</span> {{ __('Submitted Homework & Scores') }}
                </h4>
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] text-slate-500 uppercase font-mono border-b border-slate-200">
                                <th class="py-2.5 px-3">{{ __('Assignment') }}</th>
                                <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('Score') }}</th>
                            </tr>
                        </thead>
                        <tbody id="stModalSubmissionsTable">
                            <tr><td colspan="3" class="p-3 text-center text-slate-400 italic">Loading submissions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end border-t border-slate-100">
            <button type="button" onclick="closeModal('studentDetailsModal')" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm">
                Close &rarr;
            </button>
        </div>
    </div>
</div>

<script>
    const i18n = {
        loading: "{{ __('Loading...') }}",
        loadingAttendance: "{{ __('Loading attendance...') }}",
        loadingSubmissions: "{{ __('Loading submissions...') }}",
        noAttendanceRecords: "{{ __('No attendance records logged yet.') }}",
        noAssignmentsSubmitted: "{{ __('No assignments submitted yet.') }}",
        pendingGrade: "{{ __('Pending Grade') }}",
        draft: "{{ __('Draft') }}",
        studentProfile: "{{ __('Student Academic Profile') }}",
        loadingReview: "{{ __('Loading auto-correction review...') }}",
        correct: "{{ __('Correct') }}",
        incorrect: "{{ __('Incorrect') }}",
        studentCorrectPick: "{{ __('🟢 Student Correct Pick') }}",
        correctKey: "{{ __('✓ Correct Key') }}",
        studentWrongPick: "{{ __('❌ Student Wrong Pick') }}",
        explanation: "{{ __('💡 Explanation:') }}",
        question: "{{ __('Question') }}",
        noQuestionBreakdown: "{{ __('No question breakdown recorded for this submission.') }}",
        unableToLoadBreakdown: "{{ __('Unable to load question breakdown.') }}",
        confirmCancelSession: "{{ __('Are you sure you want to cancel this live session? Affected students will be notified immediately.') }}",
        failedCancelSession: "{{ __('Failed to cancel session') }}",
        validationError: "{{ __('Validation error') }}",
        networkError: "{{ __('Network connection error') }}",
    };

    function filterTeacherStudents() {
        const query = (document.getElementById('studentSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.student-card');
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const email = card.getAttribute('data-email') || '';
            const school = card.getAttribute('data-school') || '';
            if (name.includes(query) || email.includes(query) || school.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    async function openStudentDetailsModal(studentUserId) {
        document.getElementById('stModalName').textContent = i18n.loading;
        document.getElementById('stModalEmail').textContent = '';
        document.getElementById('stModalSchool').textContent = '';
        document.getElementById('stModalGrade').textContent = '';
        document.getElementById('stModalAttendanceTable').innerHTML = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.loadingAttendance}</td></tr>`;
        document.getElementById('stModalSubmissionsTable').innerHTML = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.loadingSubmissions}</td></tr>`;

        openModal('studentDetailsModal');

        try {
            const res = await fetch(`/ajax/teacher/students/${studentUserId}/details`);
            const data = await res.json();
            if (data.success && data.student) {
                document.getElementById('stModalName').textContent = data.student.name;
                document.getElementById('stModalEmail').textContent = data.student.email;
                document.getElementById('stModalSchool').textContent = data.student.school;
                document.getElementById('stModalGrade').textContent = data.student.grade;

                let attHtml = '';
                if (data.attendance && data.attendance.length > 0) {
                    data.attendance.forEach(a => {
                        let badge = 'bg-slate-100 text-slate-700';
                        if (a.status === 'present') badge = 'bg-emerald-100 text-emerald-800';
                        else if (a.status === 'absent') badge = 'bg-red-100 text-red-800';
                        else if (a.status === 'late') badge = 'bg-amber-100 text-amber-800';
                        else if (a.status === 'excused') badge = 'bg-blue-100 text-blue-800';

                        attHtml += `<tr class="border-b border-slate-100 text-xs">
                            <td class="py-2.5 px-3 font-bold text-slate-900">${a.title}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                            <td class="py-2.5 px-3 text-right"><span class="px-2 py-0.5 rounded-full font-mono text-[10px] font-bold ${badge}">${a.status.toUpperCase()}</span></td>
                        </tr>`;
                    });
                } else {
                    attHtml = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.noAttendanceRecords}</td></tr>`;
                }
                document.getElementById('stModalAttendanceTable').innerHTML = attHtml;

                let subHtml = '';
                if (data.submissions && data.submissions.length > 0) {
                    data.submissions.forEach(s => {
                        const scoreStr = s.score !== null ? `<span class="font-extrabold text-emerald-600">${parseFloat(s.score).toFixed(1)}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingGrade}</span>`;
                        subHtml += `<tr class="border-b border-slate-100 text-xs">
                            <td class="py-2.5 px-3 font-bold text-slate-900">${s.assignment_title}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-500">${s.submitted_at || i18n.draft}</td>
                            <td class="py-2.5 px-3 text-right font-mono">${scoreStr}</td>
                        </tr>`;
                    });
                } else {
                    subHtml = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.noAssignmentsSubmitted}</td></tr>`;
                }
                document.getElementById('stModalSubmissionsTable').innerHTML = subHtml;
            }
        } catch (err) {
            document.getElementById('stModalName').textContent = i18n.studentProfile;
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

document.addEventListener('DOMContentLoaded', function () {
    // 0. Auto-switch tab from URL query param (e.g. ?tab=sessions)
    const urlParams = new URLSearchParams(window.location.search);
    const requestedTab = urlParams.get('tab');
    if (requestedTab) {
        switchTeacherTab(requestedTab);
    }

    // 1. Counter Animations
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

    // 2. Form Submissions Handlers
    bindAjaxForm('createSessionForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createSessionModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('createAssignmentForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createAssignmentModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('meetingLinkForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('meetingLinkModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('rescheduleForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('rescheduleModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('gradeForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('gradeModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('attendanceForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('attendanceModal');
        setTimeout(() => location.reload(), 1000);
    });
});

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

    // Only close drawer on mobile screen (< 1024px)
    if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
        togglePortalSidebar(false);
    }
}

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

function openGradeModal(submissionId, studentName, assignmentTitle, currentScore) {
    document.getElementById('gradeSubmissionId').value = submissionId;
    document.getElementById('gradeStudentName').textContent = `${studentName} — ${assignmentTitle}`;
    document.getElementById('gradeScoreInput').value = currentScore && currentScore !== 'null' ? currentScore : '';
    document.getElementById('gradeForm').action = `/ajax/teacher/submissions/${submissionId}/review`;
    openModal('gradeModal');
}

function openAttendanceModal(sessionId, sessionTitle) {
    document.getElementById('attendanceSessionId').value = sessionId;
    document.getElementById('attendanceSessionTitle').textContent = sessionTitle;
    document.getElementById('attendanceForm').action = `/ajax/teacher/sessions/${sessionId}/attendance`;
    openModal('attendanceModal');
}

async function confirmCancelSession(sessionId) {
    if (!confirm('{{ __("Are you sure you want to cancel this live session? Affected students will be notified immediately.") }}')) {
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
            setTimeout(() => location.reload(), 1000);
        }
    } catch (err) {
        showTeacherToast('Failed to cancel session', false);
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

function showTeacherToast(message, isSuccess) {
    const toast = document.getElementById('teacherToastAlert');
    if (!toast) return;
    toast.className = `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
    toast.textContent = message;
    toast.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection
