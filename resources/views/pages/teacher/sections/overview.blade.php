        {{-- TAB 1: OVERVIEW & TODAY'S SESSIONS                                       --}}
        {{-- ════════════════════════════════════════════════════════════════════════ --}}
        <div id="teacher-tab-overview"
            class="teacher-tab-content {{ $activeTabKey === 'overview' ? '' : 'hidden' }} space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Today's Live Sessions Card --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
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
                            <div id="todaySessionsListContainer" class="space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-1">
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
                                            <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white truncate">
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
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <h2 class="font-heading text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span><i class="fa-solid fa-pen-to-square"></i></span> {{ __('Pending Grading Queue') }}
                            </h2>
                            <span
                                class="px-3 py-1 bg-orange-50 text-orange-700 font-mono text-xs font-bold rounded-full border border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800/60">
                                {{ $pendingSubmissions->count() }} {{ __('Needs Review') }}
                            </span>
                        </div>

                        @if ($pendingSubmissions->count() > 0)
                            <div id="pendingSubmissionsContainer" class="divide-y divide-slate-100 dark:divide-slate-800 max-h-[450px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach ($pendingSubmissions as $sub)
                                    <div class="pending-sub-item py-3.5 flex items-center justify-between gap-4">
                                        <div class="space-y-1 min-w-0">
                                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
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
                            <div class="text-center py-8 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    {{ __('Great job! All student homework submissions are graded.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar Overview Column: Quick Student Roster Preview & Courses --}}
                <div class="space-y-6">
                    {{-- Quick Students Roster Preview --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h3 class="font-heading font-black text-lg text-slate-900">{{ __('Recent Students') }}</h3>
                            <button type="button" onclick="switchTeacherTab('students')"
                                class="text-xs font-bold text-teal-600 hover:underline">
                                {{ __('View All') }} &rarr;
                            </button>
                        </div>

                        @if ($assignedStudents->count() > 0)
                            <div id="recentStudentsContainer" class="space-y-3 max-h-[380px] overflow-y-auto custom-scrollbar pr-1">
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
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-4">
                        <h3 class="font-heading font-black text-lg text-slate-900 border-b border-slate-100 dark:border-slate-800 pb-3">
                            {{ __('Your Active Courses') }}</h3>
                        @if ($courses->count() > 0)
                            <div id="overviewCoursesListContainer" class="space-y-2.5 max-h-[380px] overflow-y-auto custom-scrollbar pr-1">
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

