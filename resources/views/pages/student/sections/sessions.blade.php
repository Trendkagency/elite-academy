{{-- SECTION 2: INTERACTIVE LIVE SESSIONS HUB (Full Information Tables, Filters & Sub-Tabs) --}}
<section id="sectionPane_sessions" class="student-section-pane space-y-6 {{ $activeTab === 'sessions' ? '' : 'hidden' }}">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Hub Header & Search/Filter Controls --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <h2 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fa-solid fa-satellite-dish"></i>
                    </span>
                    <span>{{ $isAr ? 'مركز الحصص وبث الفصول التفاعلية' : 'Interactive Sessions & Live Stream Hub' }}</span>
                </h2>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'جدول كامل ومفصل لكافة حصص البث المباشر، الحصص التي تبدأ اليوم، المواعيد القادمة وسجل الحصص المنتهية.' : 'Comprehensive table of active live streams, starting soon sessions, scheduled dates, and past session history.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <div class="relative min-w-[220px] flex-1 sm:flex-initial">
                    <input type="text" id="sessionTableSearch" placeholder="{{ $isAr ? 'بحث في اسم الحصة أو المادة...' : 'Search session or subject...' }}"
                        class="input-mobile text-xs py-2 px-3 pl-8 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 w-full"
                        onkeyup="filterSessionsTable()">
                    <i class="fa-solid fa-magnifying-glass absolute {{ $isAr ? 'right-3' : 'left-3' }} top-3 text-slate-400 text-xs"></i>
                </div>
                <span class="text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 px-3 py-1.5 rounded-xl border border-teal-200/80 dark:border-teal-800 shadow-2xs whitespace-nowrap">
                    <i class="fa-solid fa-shield-halved"></i> 30-Min Join Rule Active
                </span>
            </div>
        </div>

        {{-- Sub-tabs Bar: Starting Soon / Upcoming / History --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700">
            <button type="button" onclick="switchSessionSubTab('soon')" id="sessionSubTab_soon"
                class="session-sub-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-between sm:justify-center gap-2 cursor-pointer bg-white dark:bg-slate-900 text-teal-900 dark:text-teal-300 shadow-sm border border-teal-200/60 dark:border-teal-800">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        @if($liveCount > 0)
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        @else
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-500"></span>
                        @endif
                    </span>
                    <span>{{ $isAr ? 'حصص تبدأ قريباً وبث مباشر' : 'Starting Soon & Live' }}</span>
                </div>
                <span class="sub-tab-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 dark:bg-teal-950 text-teal-900 dark:text-teal-200">
                    {{ count($startingSoonSessions) }}
                </span>
            </button>

            <button type="button" onclick="switchSessionSubTab('upcoming')" id="sessionSubTab_upcoming"
                class="session-sub-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-between sm:justify-center gap-2 cursor-pointer text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-slate-400"></i>
                    <span>{{ $isAr ? 'مواعيد الحصص القادمة' : 'Upcoming Scheduled' }}</span>
                </div>
                <span class="sub-tab-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    {{ count($upcomingScheduledSessions) }}
                </span>
            </button>

            <button type="button" onclick="switchSessionSubTab('history')" id="sessionSubTab_history"
                class="session-sub-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-between sm:justify-center gap-2 cursor-pointer text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                    <span>{{ $isAr ? 'سجل الحصص المنتهية' : 'Session History' }}</span>
                </div>
                <span class="sub-tab-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    {{ count($endedSessionsHistory) }}
                </span>
            </button>
        </div>

        {{-- Sub-tab Panes --}}
        <div id="sessionSubPanesContainer">
            
            {{-- 1. Pane: Starting Soon & Live --}}
            <div id="sessionPane_soon" class="session-sub-pane space-y-4">
                @if(count($startingSoonSessions) > 0)
                    <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                        <table class="w-full text-start text-xs sm:text-sm session-data-table elite-sortable-table" data-subtab="soon" data-page-size="6">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                                <tr>
                                    <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[260px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الحصة الدراسية' : 'Session & Topic' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[180px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'المادة والمعلم' : 'Subject & Teacher' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الموعد المحدد' : 'Scheduled At' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[130px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'العد التنازلي / الإغلاق' : 'Countdown & Cutoff' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[120px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الحالة' : 'Status' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-action py-3.5 px-4 text-end min-w-[140px]" data-no-sort="true">{{ $isAr ? 'الإجراء والانضمام' : 'Action' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($startingSoonSessions as $s)
                                    @php
                                        $state = $s->evaluateState($userAuth);
                                        $startAt = $s->effective_start_at;
                                        $endAt = $s->effective_end_at;
                                        $joinableAt = $s->joinable_at;
                                        $isLive = ($state === \App\Enums\LiveSessionState::LIVE);
                                        $halfAt = $startAt ? $startAt->copy()->addMinutes((int) ceil(($s->duration_minutes ?: 60) / 2)) : null;
                                    @endphp
                                    <tr class="session-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors {{ $isLive ? 'is-live-row bg-emerald-500/[0.04] dark:bg-emerald-400/[0.05] border-s-4 border-s-emerald-500' : '' }}"
                                        data-search="{{ strtolower($s->studentFacingTitle('') . ' ' . ($s->course?->title ?? '') . ' ' . ($s->subject?->name ?? '') . ' ' . ($s->teacherProfile?->user?->name ?? '')) }}">
                                        
                                        {{-- Title & Course --}}
                                        <td class="col-title py-3.5 px-4 min-w-[260px] max-w-[380px]">
                                            <div class="space-y-1">
                                                <div class="flex items-start gap-2">
                                                    @if($isLive)
                                                        <span class="relative flex h-2.5 w-2.5 shrink-0 mt-1">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                                                        </span>
                                                    @else
                                                        <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500 shrink-0 mt-1.5"></span>
                                                    @endif
                                                    <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug break-words flex-1">
                                                        {{ $s->studentFacingTitle($isAr ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Session') }}
                                                    </span>
                                                </div>
                                                @if($s->course)
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 leading-normal">{{ $s->course->title }}</p>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Subject & Teacher --}}
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <div class="space-y-1">
                                                <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                                                    {{ $s->subject?->name ?: 'STEM' }}
                                                </span>
                                                <p class="text-[11px] text-slate-700 dark:text-slate-300 font-medium flex items-center gap-1.5">
                                                    <i class="fa-solid fa-chalkboard-user text-teal-600 dark:text-teal-400 text-[10px]"></i>
                                                    <span>{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</span>
                                                </p>
                                            </div>
                                        </td>

                                        {{-- Scheduled Time --}}
                                        <td class="py-3.5 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                            <div class="space-y-0.5">
                                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ $startAt ? $startAt->format('Y-m-d') : 'Scheduled' }}</span>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $startAt ? $startAt->format('h:i A') : '' }}</p>
                                            </div>
                                        </td>

                                        {{-- Countdown / Cutoff --}}
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            @if($isLive)
                                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-500/30 inline-flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                                    <span>{{ $isAr ? 'جارية الآن' : 'Live Now' }}</span>
                                                </span>
                                            @elseif($startAt && $startAt->isFuture())
                                                <div class="session-countdown-pill text-teal-800 dark:text-teal-300 font-bold text-[11px] bg-teal-500/10 px-2.5 py-1 rounded-lg border border-teal-500/20 inline-flex items-center gap-1.5"
                                                    data-start-time="{{ $startAt->toIso8601String() }}">
                                                    <i class="fa-solid fa-stopwatch text-teal-600 dark:text-teal-400"></i>
                                                    <span class="countdown-text">{{ $isAr ? 'حساب الوقت...' : 'Counting...' }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-xs">--</span>
                                            @endif
                                        </td>

                                        {{-- State Badge --}}
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold inline-flex items-center gap-1.5 {{ $isLive ? 'bg-emerald-500/15 text-emerald-800 dark:text-emerald-300 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                                                <i class="fa-solid fa-circle text-[6px] {{ $isLive ? 'text-emerald-500 animate-ping' : 'text-slate-400' }}"></i>
                                                <span>{{ $state->label() }}</span>
                                            </span>
                                        </td>

                                        {{-- Action Button --}}
                                        <td class="py-3.5 px-4 text-end whitespace-nowrap">
                                            @if($isLive)
                                                <a href="{{ route('student.meeting.show', ['id' => $s->id]) }}"
                                                    class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white rounded-xl font-bold text-xs shadow-xs inline-flex items-center gap-1.5">
                                                    <i class="fa-solid fa-video text-xs"></i>
                                                    <span>{{ $isAr ? 'انضم للبث' : 'Join Stream' }}</span>
                                                </a>
                                            @elseif($state === \App\Enums\LiveSessionState::BEFORE_JOINABLE)
                                                <span class="text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 inline-flex items-center gap-1"
                                                    title="{{ $isAr ? 'يفتح الدخول قبل 30 دقيقة' : 'Opens 30 mins before start' }}">
                                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                                    <span>{{ $joinableAt ? $joinableAt->format('h:i A') : ($startAt ? $startAt->format('h:i A') : '30m prior') }}</span>
                                                </span>
                                            @elseif($state === \App\Enums\LiveSessionState::PACKAGE_REQUIRED)
                                                <a href="{{ route('courses') }}" class="btn-lift text-[11px] font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 px-3 py-1.5 rounded-xl">
                                                    <i class="fa-solid fa-lock"></i> {{ $isAr ? 'تجديد الباقة' : 'Unlock' }}
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-bold">{{ $state->label() }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <div class="text-3xl text-teal-600"><i class="fa-solid fa-satellite-dish"></i></div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ $isAr ? 'لا توجد حصص بث مباشر تبدأ اليوم' : 'No Live Streams or Sessions Starting Today' }}</h4>
                        <p class="text-xs font-mono text-slate-500 max-w-md mx-auto">
                            {{ $isAr ? 'يمكنك مراجعة المواعيد المجدولة القادمة أو تصفح سجل الحصص السابقة.' : 'You can inspect upcoming scheduled dates in the tab above.' }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- 2. Pane: Upcoming Scheduled Dates --}}
            <div id="sessionPane_upcoming" class="session-sub-pane space-y-4 hidden">
                @if(count($upcomingScheduledSessions) > 0)
                    <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                        <table class="w-full text-start text-xs sm:text-sm session-data-table elite-sortable-table" data-subtab="upcoming" data-page-size="6">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                                <tr>
                                    <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[260px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الحصة والمقرر' : 'Session & Course' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[180px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'المادة والمعلم' : 'Subject & Instructor' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'التاريخ والوقت' : 'Scheduled Date & Time' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[120px]" data-sort-type="number">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'المدة' : 'Duration' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[120px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الحالة' : 'Status' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-action py-3.5 px-4 text-end min-w-[140px]" data-no-sort="true">{{ $isAr ? 'تقديم عذر' : 'Excuse' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($upcomingScheduledSessions as $s)
                                    @php
                                        $startAt = $s->effective_start_at;
                                        $endAt = $s->effective_end_at;
                                    @endphp
                                    <tr class="session-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
                                        data-search="{{ strtolower($s->studentFacingTitle('') . ' ' . ($s->course?->title ?? '') . ' ' . ($s->subject?->name ?? '') . ' ' . ($s->teacherProfile?->user?->name ?? '')) }}">
                                        
                                        <td class="col-title py-3.5 px-4 min-w-[260px] max-w-[380px]">
                                            <div class="space-y-0.5">
                                                <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug break-words block">{{ $s->studentFacingTitle($isAr ? 'حصة دراسية قادمة' : 'Scheduled Live Session') }}</span>
                                                @if($s->course)
                                                    <p class="text-[11px] text-slate-500 font-normal line-clamp-1 leading-normal">{{ $s->course->title }}</p>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="col-course py-3.5 px-3 whitespace-nowrap min-w-[180px]">
                                            <div class="space-y-0.5">
                                                <span class="font-bold text-teal-700 dark:text-teal-400 block">{{ $s->subject?->name ?: 'STEM' }}</span>
                                                <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</p>
                                            </div>
                                        </td>

                                        <td class="py-3.5 px-3 whitespace-nowrap font-semibold text-slate-800 dark:text-slate-200">
                                            <div>{{ $startAt ? $startAt->format('Y-m-d') : 'Scheduled' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $startAt ? $startAt->format('h:i A') : '' }}</div>
                                        </td>

                                        <td class="py-3.5 px-3 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                            {{ $s->duration_minutes ?: 60 }} {{ $isAr ? 'دقيقة' : 'mins' }}
                                        </td>

                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                {{ $isAr ? 'مجدولة رسمياً' : 'Scheduled' }}
                                            </span>
                                        </td>

                                        <td class="py-3.5 px-4 text-end whitespace-nowrap">
                                            <button type="button" onclick="window.openModal ? window.openModal('excuseModal') : document.getElementById('excuseModal').classList.remove('hidden')"
                                                class="btn-lift px-3 py-1.5 rounded-xl text-[11px] font-bold bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 dark:hover:bg-amber-900 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 inline-flex items-center gap-1 cursor-pointer">
                                                <i class="fa-solid fa-file-signature text-[10px]"></i>
                                                <span>{{ $isAr ? 'طلب عذر' : 'Excuse' }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 font-mono text-xs">
                        {{ $isAr ? 'لا توجد حصص مجدولة لمواعيد قادمة حالياً.' : 'No future scheduled dates.' }}
                    </div>
                @endif
            </div>

            {{-- 3. Pane: Ended History --}}
            <div id="sessionPane_history" class="session-sub-pane space-y-4 hidden">
                @if(count($endedSessionsHistory) > 0)
                    <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                        <table class="w-full text-start text-xs sm:text-sm session-data-table elite-sortable-table" data-subtab="history" data-page-size="6">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                                <tr>
                                    <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[260px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'الحصة المنتهية' : 'Completed Session' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[180px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'المادة والمعلم' : 'Subject & Teacher' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'تاريخ الانعقاد' : 'Held Date' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[130px]" data-sort-type="text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $isAr ? 'حالة الحضور' : 'Attendance Status' }}</span>
                                            <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                        </div>
                                    </th>
                                    <th class="col-action py-3.5 px-4 text-end min-w-[120px]" data-no-sort="true">{{ $isAr ? 'التفاصيل' : 'Details' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($endedSessionsHistory as $s)
                                    @php
                                        $startAt = $s->effective_start_at;
                                        $endAt = $s->effective_end_at;
                                        $attendance = $s->attendances?->where('user_id', $userAuth->id)->first();
                                        $attended = !is_null($attendance) || $s->status === 'completed';
                                    @endphp
                                    <tr class="session-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
                                        data-search="{{ strtolower($s->studentFacingTitle('') . ' ' . ($s->course?->title ?? '') . ' ' . ($s->subject?->name ?? '') . ' ' . ($s->teacherProfile?->user?->name ?? '')) }}">
                                        
                                        <td class="col-title py-3.5 px-4 font-bold text-slate-900 dark:text-white min-w-[260px] max-w-[380px]">
                                            <div><span class="break-words font-semibold text-slate-900 dark:text-slate-100 block leading-snug">{{ $s->studentFacingTitle($isAr ? 'حصة مكتملة' : 'Completed Live Session') }}</span></div>
                                            @if($s->course)
                                                <p class="text-[11px] text-slate-500 font-normal line-clamp-1 leading-normal">{{ $s->course->title }}</p>
                                            @endif
                                        </td>

                                        <td class="col-course py-3.5 px-3 whitespace-nowrap min-w-[180px]">
                                            <div class="font-bold text-teal-700 dark:text-teal-400">{{ $s->subject?->name ?: 'STEM' }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</div>
                                        </td>

                                        <td class="col-date py-3.5 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300 font-mono text-xs min-w-[140px]">
                                            {{ $startAt ? $startAt->format('Y-m-d h:i A') : 'Completed' }}
                                        </td>

                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            @if($attended)
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                    <i class="fa-solid fa-check"></i> {{ $isAr ? 'حضور معتمد' : 'Attended' }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    {{ $isAr ? 'منتهية' : 'Ended' }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-4 text-end whitespace-nowrap">
                                            @if($s->course)
                                                <button type="button" onclick="openEnrolledCourseModal({{ $s->course->id }})" class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline cursor-pointer">
                                                    {{ $isAr ? 'مراجعة المنهج' : 'Syllabus' }} &rarr;
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-400">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-xs font-mono text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl">
                        {{ $isAr ? 'لا يوجد سجل حصص منتهية حتى الآن.' : 'No ended session history recorded.' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
