@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $userAuth = auth()->user();
    $todayDateStr = now()->translatedFormat('l، j F Y');
    $usedPct = ($package && $package->total_sessions > 0) ? min(100, round(($package->used_sessions / $package->total_sessions) * 100)) : 0;
    $remainingPct = 100 - $usedPct;
@endphp

<style>
    /* Bulletproof 7-Column Calendar Grid (Independent of Tailwind build purge) */
    .cal-grid-7 {
        display: grid !important;
        grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
        gap: 6px !important;
    }
    .cal-day-btn {
        aspect-ratio: 1 / 1;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        transition: all 0.15s ease-in-out;
        position: relative;
    }
    .cal-day-btn:hover:not(.cal-day-disabled) {
        transform: scale(1.05);
        z-index: 2;
    }
    /* Weekly Strip Container (Mobile Smooth Scroll, Desktop 7-Col Grid) */
    .cal-weekly-strip {
        display: flex;
        gap: 0.625rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    @media (min-width: 768px) {
        .cal-weekly-strip {
            display: grid !important;
            grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
            overflow-x: visible;
            padding-bottom: 0;
        }
    }
    .cal-week-card {
        min-width: 115px;
        flex: 0 0 auto;
        scroll-snap-align: start;
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @media (min-width: 768px) {
        .cal-week-card {
            min-width: 0 !important;
            width: 100% !important;
            flex: 1 1 0% !important;
        }
    }
</style>

<section id="sectionPane_overview" class="student-section-pane space-y-6 sm:space-y-7 {{ $activeTab === 'overview' ? '' : 'hidden' }}">

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- 1. INTERACTIVE SESSIONS CALENDAR & MASTER NAVIGATOR                         --}}
    {{--    (التقويم الذكي: عرض أسبوعي افتراضي مع إمكانية التبديل للشهري)             --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="studentCalendarSection" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Section Header with Mode Switcher & Filter Pills --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg border border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <div>
                    <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $isAr ? 'تقويم الحصص' : 'Sessions Calendar' }}</span>
                        <span class="text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 px-2.5 py-0.5 rounded-full border border-teal-200 dark:border-teal-800">
                            {{ count($allSessionsForCalendar ?? []) }} {{ $isAr ? 'حصة مسجلة' : 'Sessions' }}
                        </span>
                    </h3>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $isAr ? 'الجدول الأسبوعي الذكي والمواعيد المقررة بتصميم تفاعلي متقدم.' : 'Smart weekly schedule and sessions timeline with interactive controls.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                {{-- View Mode Switcher: Weekly (Default) vs Monthly --}}
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80">
                    <button type="button" id="calModeBtn_week" onclick="switchCalViewMode('weekly')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-black transition-all cursor-pointer bg-teal-600 text-white shadow-2xs flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-week text-[11px]"></i>
                        <span>{{ $isAr ? 'عرض أسبوعي' : 'Weekly' }}</span>
                    </button>
                    <button type="button" id="calModeBtn_month" onclick="switchCalViewMode('monthly')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar text-[11px]"></i>
                        <span>{{ $isAr ? 'عرض شهري' : 'Monthly' }}</span>
                    </button>
                </div>

                {{-- Quick Filter Pills --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="setStudentCalFilter('week')" id="calPill_week"
                        class="cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-teal-600 text-white shadow-2xs">
                        <i class="fa-solid fa-calendar-week text-[10px]"></i>
                        <span>{{ $isAr ? 'حصص الأسبوع' : 'This Week' }}</span>
                    </button>
                    <button type="button" onclick="setStudentCalFilter('today')" id="calPill_today"
                        class="cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-star text-[10px]"></i>
                        <span>{{ $isAr ? 'حصص اليوم' : 'Today' }}</span>
                    </button>
                    <button type="button" onclick="setStudentCalFilter('upcoming')" id="calPill_upcoming"
                        class="cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-calendar-plus text-[10px]"></i>
                        <span>{{ $isAr ? 'القادمة' : 'Upcoming' }}</span>
                    </button>
                    <button type="button" onclick="setStudentCalFilter('history')" id="calPill_history"
                        class="cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                        <span>{{ $isAr ? 'السابقة' : 'History' }}</span>
                    </button>
                    <button type="button" onclick="setStudentCalFilter('all')" id="calPill_all"
                        class="cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-globe text-[10px]"></i>
                        <span>{{ $isAr ? 'الكل' : 'All' }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Optional: Live Classroom Broadcast Banner if any session is Live Right Now --}}
        @php
            $liveNowSessions = $todaySessions->filter(fn($s) => $s->evaluateState($userAuth) === \App\Enums\LiveSessionState::LIVE);
            $currentLiveSession = $liveNowSessions->first();
        @endphp
        <div id="studentLiveClassroomBanner" class="{{ $currentLiveSession ? '' : 'hidden ' }}p-4 rounded-2xl bg-gradient-to-r from-rose-500/15 via-rose-500/10 to-teal-500/10 border border-rose-500/30 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm animate-pulse mb-4">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-rose-600 animate-ping shrink-0"></span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-black text-rose-700 dark:text-rose-300 uppercase tracking-wider block">
                            {{ $isAr ? 'بث مباشر منعقد الآن' : 'Live Classroom Active Now' }}
                        </span>
                        <span id="studentLiveBannerDuration" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-800">
                            {{ $currentLiveSession ? ($currentLiveSession->duration_minutes . ' ' . ($isAr ? 'دقيقة' : 'min')) : '' }}
                        </span>
                    </div>
                    <h4 id="studentLiveBannerTitle" class="font-heading font-black text-sm text-slate-900 dark:text-white">
                        <span id="studentLiveBannerTitleText">{{ $currentLiveSession ? $currentLiveSession->studentFacingTitle($isAr ? 'حصة تفاعلية' : 'Live Class') : '' }}</span>
                        <span id="studentLiveBannerTeacher" class="text-xs font-mono text-slate-500 dark:text-slate-400 font-normal">({{ $currentLiveSession?->teacherProfile?->user?->name ?: 'Teacher' }})</span>
                    </h4>
                </div>
            </div>
            <a id="studentLiveClassroomJoinBtn" href="{{ $currentLiveSession ? route('student.meeting.show', ['id' => $currentLiveSession->id]) : '#' }}"
                class="btn-lift px-4 py-2 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white rounded-xl text-xs font-black shadow-md flex items-center gap-2 shrink-0">
                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                <span>{{ $isAr ? 'دخول البث المباشر الآن' : 'Join Live Stream' }} &rarr;</span>
            </a>
        </div>

        {{-- WEEKLY VIEW: 7-DAY INTERACTIVE STRIP (Visible by default in Weekly mode) --}}
        <div id="calWeekStripWrapper" class="space-y-4">
            {{-- Week Navigation Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/80 dark:bg-slate-800/50 p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                <div class="flex items-center gap-2.5">
                    <button type="button" onclick="navStudentWeek(-1)" aria-label="Previous Week"
                        class="w-8 h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 flex items-center justify-center text-xs transition-colors cursor-pointer border border-slate-200 dark:border-slate-600 shadow-2xs">
                        <i class="fa-solid fa-chevron-{{ $isAr ? 'right' : 'left' }}"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                        <div>
                            <h4 id="calWeekRangeTitle" class="font-heading font-black text-xs sm:text-sm md:text-base text-slate-900 dark:text-white"></h4>
                            <span class="text-[10px] font-mono text-teal-600 dark:text-teal-400 font-bold block">
                                {{ $isAr ? 'أيام الأسبوع وحصصه التفاعلية' : 'Weekly days & interactive sessions' }}
                            </span>
                        </div>
                    </div>

                    <button type="button" onclick="navStudentWeek(1)" aria-label="Next Week"
                        class="w-8 h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 flex items-center justify-center text-xs transition-colors cursor-pointer border border-slate-200 dark:border-slate-600 shadow-2xs">
                        <i class="fa-solid fa-chevron-{{ $isAr ? 'left' : 'right' }}"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="jumpStudentWeekToday()"
                        class="px-3 py-1.5 text-xs font-mono font-bold rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 border border-slate-200 dark:border-slate-600 transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-regular fa-calendar-check text-teal-600 dark:text-teal-400"></i>
                        <span>{{ $isAr ? 'هذا الأسبوع' : 'Current Week' }}</span>
                    </button>
                    <button type="button" onclick="showAllWeekSessions()" id="calBtn_showAllWeek"
                        class="px-3 py-1.5 text-xs font-mono font-bold rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 hover:bg-teal-100 dark:hover:bg-teal-900/60 border border-teal-500/30 transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-list-check text-teal-600 dark:text-teal-400"></i>
                        <span>{{ $isAr ? 'عرض كافة حصص الأسبوع' : 'All Week Sessions' }}</span>
                    </button>
                </div>
            </div>

            {{-- 7-Day Responsive Strip Container --}}
            <div id="calWeekDaysStrip" class="cal-weekly-strip select-none">
                {{-- Populated dynamically via renderWeeklyDaysStrip() --}}
            </div>
        </div>

        {{-- MAIN CONTENT AREA (Split into Left Navigator/Digest + Right Sessions List) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- Column 1A: Weekly Summary Sidebar (Visible in Weekly mode) --}}
            <div id="calWeekSummaryCol" class="lg:col-span-4 bg-slate-50 dark:bg-slate-800/60 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/80 space-y-4">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-700/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-500/20">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <h5 class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white">
                                {{ $isAr ? 'إحصائيات هذا الأسبوع' : 'This Week\'s Overview' }}
                            </h5>
                            <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'توزيع الحصص والمواعيد' : 'Classes distribution' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 shadow-2xs">
                        {{ $isAr ? 'أسبوعي' : 'Weekly' }}
                    </span>
                </div>

                {{-- SECTION 1: Week Scope & "All في الأسبوع كـ total sessions in week" --}}
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-mono font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-week text-teal-600 dark:text-teal-400"></i>
                            <span>{{ $isAr ? 'حصص الأسبوع الحالي' : 'Current Week Classes' }}</span>
                        </span>
                        <button type="button" onclick="showAllWeekSessions()"
                            class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/70 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 transition-colors cursor-pointer flex items-center gap-1">
                            <i class="fa-solid fa-list-check text-[9px]"></i>
                            <span>{{ $isAr ? 'عرض الكل (All)' : 'View All' }}</span>
                        </button>
                    </div>

                    {{-- 3 KPI Cards for Week: All Total, Completed, Remaining --}}
                    <div class="grid grid-cols-3 gap-2 text-center">
                        {{-- Total Sessions in Week (All) --}}
                        <div onclick="showAllWeekSessions()" title="{{ $isAr ? 'انقر لعرض كل حصص الأسبوع' : 'Click to view all week sessions' }}"
                            class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl border-2 border-teal-500/30 hover:border-teal-500 cursor-pointer transition-all shadow-2xs group relative overflow-hidden">
                            <div class="absolute -top-1 -right-1 w-6 h-6 bg-teal-500/10 rounded-full blur-xs pointer-events-none"></div>
                            <span class="text-[10px] font-mono font-bold text-slate-500 dark:text-slate-400 block truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                {{ $isAr ? 'إجمالي الأسبوع (All)' : 'Total Week (All)' }}
                            </span>
                            <div class="flex items-baseline justify-center gap-1 mt-1">
                                <span id="calWeekClassesCount" class="text-lg font-heading font-black text-teal-700 dark:text-teal-300">0</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة' : 'sess' }}</span>
                            </div>
                        </div>

                        {{-- Completed in Week --}}
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 block truncate">
                                {{ $isAr ? 'منتهية بالأسبوع' : 'Week Done' }}
                            </span>
                            <div class="flex items-baseline justify-center gap-1 mt-1">
                                <span id="calWeekCompletedCount" class="text-lg font-heading font-black text-emerald-600 dark:text-emerald-400">0</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة' : 'sess' }}</span>
                            </div>
                        </div>

                        {{-- Remaining in Week --}}
                        <div class="p-2.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/70 shadow-2xs">
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 block truncate">
                                {{ $isAr ? 'متبقية بالأسبوع' : 'Week Left' }}
                            </span>
                            <div class="flex items-baseline justify-center gap-1 mt-1">
                                <span id="calWeekRemainingCount" class="text-lg font-heading font-black text-sky-600 dark:text-sky-400">0</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة' : 'sess' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: Student Package Progress (The 3 Core Requested Metrics) --}}
                @php
                    $pkgTotal = (int) ($package?->total_sessions ?? 0);
                    $pkgUsed = (int) ($package?->used_sessions ?? 0);
                    $pkgRemaining = (int) ($package?->remaining_sessions ?? 0);

                    if ((!$package || $pkgTotal <= 0) && isset($allSessionsForCalendar)) {
                        $pkgTotal = $allSessionsForCalendar->count();
                        $pkgUsed = $allSessionsForCalendar->where('is_past', true)->count();
                        $pkgRemaining = $allSessionsForCalendar->where('is_past', false)->count();
                    }

                    $pkgCurrentNum = $pkgTotal > 0 ? min($pkgTotal, max(1, $pkgUsed + ($pkgRemaining > 0 ? 1 : 0))) : 0;
                    $pkgPct = $pkgTotal > 0 ? min(100, round(($pkgUsed / $pkgTotal) * 100)) : 0;
                @endphp
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs space-y-3.5">
                    
                    {{-- Header with: [x] رقم الـSession الحالية من إجمالي Sessions الـPackage --}}
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-500 text-white flex items-center justify-center text-xs shrink-0 shadow-xs">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div class="min-w-0">
                                <h6 class="font-heading font-black text-xs text-slate-900 dark:text-white truncate">
                                    {{ $isAr ? 'تقدم باقة الحصص' : 'Package Progress' }}
                                </h6>
                                <p class="text-[10px] font-mono text-slate-400 truncate">
                                    {{ $package?->packageTemplate?->name ?: ($package?->course?->title ?: ($isAr ? 'الباقة التعليمية' : 'Learning Package')) }}
                                </p>
                            </div>
                        </div>

                        {{-- Current Session Number out of Total Package Sessions --}}
                        <div class="shrink-0 text-end">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-mono font-black bg-gradient-to-r from-teal-500/15 to-emerald-500/15 text-teal-800 dark:text-teal-200 border border-teal-500/30 shadow-2xs">
                                <i class="fa-solid fa-bookmark text-teal-600 text-[10px]"></i>
                                <span>{{ $isAr ? 'الحصة:' : 'Session:' }}</span>
                                <strong class="text-teal-700 dark:text-teal-300">#<span id="pkgCurrentSessionNum">{{ $pkgCurrentNum }}</span></strong>
                                <span class="text-slate-400">/</span>
                                <span><span id="pkgTotalSessionsCount">{{ $pkgTotal }}</span> {{ $isAr ? 'حصة' : 'sess' }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Visual Progress Bar --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] font-mono text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-chart-line text-teal-500"></i>
                                <span>{{ $isAr ? 'نسبة إنجاز الحصص في الباقة' : 'Package Completion' }}</span>
                            </span>
                            <span id="pkgProgressPercentText" class="font-black text-teal-600 dark:text-teal-400">{{ $pkgPct }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden relative border border-slate-200/50 dark:border-slate-600/50">
                            <div id="pkgProgressBarFill" class="h-full bg-gradient-to-r from-teal-500 via-teal-600 to-emerald-500 rounded-full transition-all duration-500 shadow-xs"
                                 style="width: {{ $pkgPct }}%;"></div>
                        </div>
                    </div>

                    {{-- 2 KPI Cards: [x] عدد الـSessions التي تم الانتهاء منها + [x] عدد الـSessions المتبقية --}}
                    <div class="grid grid-cols-2 gap-2 text-center pt-0.5">
                        {{-- عدد الـSessions التي تم الانتهاء منها --}}
                        <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-emerald-200/70 dark:border-emerald-900/50 space-y-0.5">
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 block truncate flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                <span>{{ $isAr ? 'تم الانتهاء منها' : 'Completed' }}</span>
                            </span>
                            <div class="flex items-baseline justify-center gap-1">
                                <span id="pkgUsedSessionsCount" class="text-lg font-heading font-black text-emerald-600 dark:text-emerald-400">
                                    {{ $pkgUsed }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة' : 'sess' }}</span>
                            </div>
                        </div>

                        {{-- عدد الـSessions المتبقية --}}
                        <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-900/60 border border-sky-200/70 dark:border-sky-900/50 space-y-0.5">
                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400 block truncate flex items-center justify-center gap-1">
                                <i class="fa-solid fa-hourglass-half text-sky-600 text-[10px]"></i>
                                <span>{{ $isAr ? 'الحصص المتبقية' : 'Remaining' }}</span>
                            </span>
                            <div class="flex items-baseline justify-center gap-1">
                                <span id="pkgRemainingSessionsCount" class="text-lg font-heading font-black text-sky-600 dark:text-sky-400">
                                    {{ $pkgRemaining }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $isAr ? 'حصة' : 'sess' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Next / Today Class Highlight Card --}}
                <div id="calWeekNextClassCard" class="p-3.5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/70 space-y-2 shadow-2xs">
                    {{-- Dynamically populated via JS --}}
                </div>

                {{-- Action Shortcuts --}}
                <div class="space-y-2 pt-1 border-t border-slate-200/80 dark:border-slate-700/80">
                    <button type="button" onclick="showAllWeekSessions()"
                        class="w-full py-2.5 px-3 rounded-xl bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 text-xs font-mono font-bold border border-teal-200 dark:border-teal-800 flex items-center justify-between cursor-pointer transition-colors shadow-2xs">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-teal-600"></i>
                            <span>{{ $isAr ? 'عرض كافة حصص الأسبوع (All)' : 'Show All Week Sessions (All)' }}</span>
                        </span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-[10px]"></i>
                    </button>

                    <button type="button" onclick="switchCalViewMode('monthly')"
                        class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-mono font-bold border border-slate-200 dark:border-slate-600 flex items-center justify-between cursor-pointer transition-colors">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-indigo-500"></i>
                            <span>{{ $isAr ? 'فتح التقويم الشهري الكامل' : 'Open Full Month Calendar' }}</span>
                        </span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-[10px]"></i>
                    </button>
                </div>
            </div>

            {{-- Column 1B: Interactive Month Calendar (Hidden in Weekly mode, visible in Monthly mode) --}}
            <div id="calMonthPickerCol" class="hidden lg:col-span-5 bg-slate-50 dark:bg-slate-800/60 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-700/80 space-y-4">
                {{-- Calendar Month Navigation Bar --}}
                <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-slate-700/80 pb-3">
                    <button type="button" onclick="navStudentCal(-1)" aria-label="Previous Month"
                        class="w-8 h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 flex items-center justify-center text-xs transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">
                        <i class="fa-solid fa-chevron-{{ $isAr ? 'right' : 'left' }}"></i>
                    </button>

                    <div class="text-center">
                        <h4 id="calMonthYearTitle" class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white"></h4>
                        <span id="calActiveDateSubtitle" class="text-[10px] font-mono text-teal-600 dark:text-teal-400 font-bold"></span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="jumpStudentCalToday()"
                            class="px-2.5 py-1 text-[10px] font-mono font-bold rounded-lg bg-teal-500/10 text-teal-700 dark:text-teal-300 hover:bg-teal-500/20 border border-teal-500/20 cursor-pointer">
                            {{ $isAr ? 'اليوم' : 'Today' }}
                        </button>
                        <button type="button" onclick="navStudentCal(1)" aria-label="Next Month"
                            class="w-8 h-8 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 flex items-center justify-center text-xs transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">
                            <i class="fa-solid fa-chevron-{{ $isAr ? 'left' : 'right' }}"></i>
                        </button>
                    </div>
                </div>

                {{-- Week Days Header (7-Column Grid) --}}
                <div class="cal-grid-7 text-center font-mono text-[11px] font-extrabold text-slate-400 dark:text-slate-500 select-none pb-1"
                     style="display: grid !important; grid-template-columns: repeat(7, minmax(0, 1fr)) !important; gap: 6px !important;">
                    <div>{{ $isAr ? 'أحد' : 'Sun' }}</div>
                    <div>{{ $isAr ? 'إثنين' : 'Mon' }}</div>
                    <div>{{ $isAr ? 'ثلاثاء' : 'Tue' }}</div>
                    <div>{{ $isAr ? 'أربعاء' : 'Wed' }}</div>
                    <div>{{ $isAr ? 'خميس' : 'Thu' }}</div>
                    <div>{{ $isAr ? 'جمعة' : 'Fri' }}</div>
                    <div>{{ $isAr ? 'سبت' : 'Sat' }}</div>
                </div>

                {{-- Days Grid Container --}}
                <div id="calDaysGrid" class="cal-grid-7 select-none"
                     style="display: grid !important; grid-template-columns: repeat(7, minmax(0, 1fr)) !important; gap: 6px !important;"></div>

                {{-- Calendar Legend & Back to Weekly Button --}}
                <div class="pt-3 border-t border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between text-[10px] font-mono text-slate-500 dark:text-slate-400 flex-wrap gap-2">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>{{ $isAr ? 'مباشر / اليوم' : 'Live / Today' }}</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>{{ $isAr ? 'قادمة' : 'Upcoming' }}</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>{{ $isAr ? 'منتهية' : 'Past' }}</span>
                    </span>
                </div>

                <button type="button" onclick="switchCalViewMode('weekly')"
                    class="w-full py-2 px-3 rounded-xl bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/60 dark:hover:bg-teal-900/60 text-teal-700 dark:text-teal-300 text-xs font-mono font-bold border border-teal-200 dark:border-teal-800 flex items-center justify-center gap-1.5 cursor-pointer transition-colors">
                    <i class="fa-solid fa-calendar-week"></i>
                    <span>{{ $isAr ? 'العودة للجدول الأسبوعي' : 'Back to Weekly View' }}</span>
                </button>
            </div>

            {{-- Column 2: Sessions List Display Area (lg:col-span-8 in weekly mode, lg:col-span-7 in monthly mode) --}}
            <div id="calSessionsCol" class="lg:col-span-8 space-y-4">
                {{-- Search & Active Selection Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <h4 id="calListHeaderTitle" class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-list-ul text-teal-600 dark:text-teal-400"></i>
                            <span>{{ $isAr ? 'حصص هذا الأسبوع' : 'This Week\'s Sessions' }}</span>
                        </h4>
                        <span id="calListCounter" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">0</span>
                    </div>

                    {{-- Live Filter Search Input --}}
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="calSearchInput" oninput="onStudentCalSearch(this.value)"
                            placeholder="{{ $isAr ? 'بحث في الحصص أو المعلم أو المادة...' : 'Search session, teacher, or subject...' }}"
                            class="w-full ps-9 pe-3 py-1.5 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none transition-all">
                    </div>
                </div>

                {{-- Dynamic Session Cards Container --}}
                <div id="calSessionsContainer" class="space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar pe-1"></div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- 2. SUBJECTS & SESSIONS BREAKDOWN (المواد الدراسية ورصيد الحصص المتبقية)     --}}
    {{--    (section اشوف المواد و كل مادة فاضل فيها كام session و كل اتفاصيل)      --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div id="studentSubjectsSection" class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg border border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white">
                            {{ $isAr ? 'المواد الدراسية ومتابعة الحصص المتبقية' : 'My Subjects & Remaining Sessions' }}
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                            {{ count($subjectCards) }} {{ $isAr ? 'مواد دراسية' : 'Subjects' }}
                        </span>
                    </div>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $isAr ? 'بيانات تفصيلية لكل مادة: رصيد الحصص المتبقية، الحصص المنفذة، نسبة الإنجاز، المعلم المشرف، وموعد الحصة القادمة.' : 'Detailed metrics per subject: remaining sessions, completed classes, progress, instructor, and next scheduled class.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                @if($package)
                    <div class="px-3.5 py-1.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-500/30 flex items-center gap-2 shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-mono font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $package->remaining_sessions }} {{ $isAr ? 'حصة متبقية في الباقة' : 'Sessions Remaining' }}
                        </span>
                    </div>
                @endif

                <button type="button" onclick="switchStudentTab('courses')"
                    class="btn-lift px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-mono font-bold border border-slate-200 dark:border-slate-700 cursor-pointer transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400"></i>
                    <span>{{ $isAr ? 'تصفح كل المناهج' : 'All Courses' }}</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </div>

        {{-- Subjects Grid --}}
        @if(count($subjectCards) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 sm:gap-6">
                @foreach($subjectCards as $card)
                    @php
                        $pal = $card['palette'];
                        $teacher = $card['teacher'];
                        $nextSess = $card['next_session'];
                    @endphp
                    <div class="bg-slate-50/80 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        {{-- Top Accent Line --}}
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r {{ $pal['progress'] }}"></div>

                        <div class="space-y-4">
                            {{-- Subject Header & Category --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl {{ $pal['icon_bg'] }} flex items-center justify-center text-lg border shrink-0 shadow-2xs">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-mono font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 block truncate">
                                            {{ $card['category_name'] }}
                                        </span>
                                        <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white truncate">
                                            {{ $card['name'] }}
                                        </h4>
                                    </div>
                                </div>

                                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold border shrink-0 {{ $pal['badge'] }}">
                                    {{ $studentProfile?->gradeLevel?->name ?: ($isAr ? 'المرحلة الدراسية' : 'Curriculum') }}
                                </span>
                            </div>

                            {{-- Instructor Row --}}
                            <div class="flex items-center gap-2.5 p-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shrink-0">
                                    {{ $teacher['avatar'] ?? 'T' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $teacher['name'] }}
                                    </p>
                                    <p class="text-[10px] font-mono text-slate-500 dark:text-slate-400 truncate">
                                        {{ $isAr ? 'المعلم الأكاديمي المشرف' : 'Academic Instructor' }}
                                    </p>
                                </div>
                            </div>

                            {{-- HERO HIGHLIGHT: الحصص المتبقية (Remaining Sessions) --}}
                            <div class="p-4 rounded-2xl bg-emerald-500/10 dark:bg-emerald-950/40 border border-emerald-500/30 space-y-1.5 shadow-2xs">
                                <div class="flex items-center justify-between text-xs font-mono">
                                    <span class="font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                        <i class="fa-solid fa-hourglass-half text-emerald-600 dark:text-emerald-400"></i>
                                        <span>{{ $isAr ? 'الحصص المتبقية للمادة:' : 'Remaining Sessions:' }}</span>
                                    </span>
                                    <span class="text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-100/80 dark:bg-emerald-900/60 px-2 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                        {{ $card['remaining_sessions'] }} {{ $isAr ? 'متبقية' : 'Left' }}
                                    </span>
                                </div>

                                <div class="flex items-baseline gap-2">
                                    <span class="font-heading font-black text-3xl sm:text-4xl text-emerald-600 dark:text-emerald-400 leading-none">
                                        {{ $card['remaining_sessions'] }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300">
                                        {{ $isAr ? 'حصة متبقية يمكنك حضورها' : 'Sessions Remaining' }}
                                    </span>
                                    <span class="sr-only">{{ $card['remaining_sessions'] }} {{ $isAr ? 'حصة متبقية' : 'Sessions Remaining' }}</span>
                                </div>
                            </div>

                            {{-- 3-Column Mini Metrics Grid (المتبقية / المنفذة / الإجمالي) --}}
                            <div class="grid grid-cols-3 gap-2 text-center font-mono">
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70 space-y-0.5">
                                    <span class="text-[10px] text-slate-400 block">{{ $isAr ? 'المتبقية' : 'Left' }}</span>
                                    <p class="font-heading font-black text-sm text-emerald-600 dark:text-emerald-400">{{ $card['remaining_sessions'] }}</p>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70 space-y-0.5">
                                    <span class="text-[10px] text-slate-400 block">{{ $isAr ? 'تم حضورها' : 'Attended' }}</span>
                                    <p class="font-heading font-black text-sm text-amber-600 dark:text-amber-400">{{ $card['used_sessions'] }}</p>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70 space-y-0.5">
                                    <span class="text-[10px] text-slate-400 block">{{ $isAr ? 'إجمالي المقرر' : 'Total' }}</span>
                                    <p class="font-heading font-black text-sm text-slate-900 dark:text-white">{{ $card['total_sessions'] }}</p>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px] font-mono">
                                    <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'نسبة إنجاز المادة:' : 'Course Progress:' }}</span>
                                    <span class="font-extrabold text-teal-600 dark:text-teal-400">{{ $card['progress_pct'] }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r {{ $pal['progress'] }} rounded-full transition-all duration-500"
                                         style="width: {{ max(4, $card['progress_pct']) }}%;"></div>
                                </div>
                            </div>

                            {{-- Next Scheduled Session Info Box --}}
                            <div class="p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-700/70 space-y-2 text-xs font-mono">
                                <span class="text-[10px] uppercase font-extrabold text-slate-400 block">
                                    <i class="fa-solid fa-calendar-check text-teal-600 dark:text-teal-400"></i>
                                    {{ $isAr ? 'الحصة القادمة:' : 'Next Scheduled Session:' }}
                                </span>
                                @if($nextSess)
                                    @php
                                        $eval = $nextSess->evaluateState($userAuth);
                                        $isLive = ($eval === \App\Enums\LiveSessionState::LIVE);
                                        $canJoin = $eval->canJoin();
                                    @endphp
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white truncate">
                                                {{ $nextSess->studentFacingTitle($isAr ? 'حصة مباشرة' : 'Live Class') }}
                                            </p>
                                            <p class="text-[10px] text-teal-700 dark:text-teal-300 font-bold">
                                                {{ $nextSess->effective_start_at ? $nextSess->effective_start_at->translatedFormat('l j F - h:i A') : ($isAr ? 'مجدولة قريباً' : 'Scheduled') }}
                                            </p>
                                        </div>
                                        @if($canJoin || $isLive)
                                            <a href="{{ route('student.meeting.show', ['id' => $nextSess->id]) }}"
                                                class="btn-lift px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-[11px] shrink-0 inline-flex items-center gap-1 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                <span>{{ $isAr ? 'دخول' : 'Join' }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-slate-400 text-[11px]">
                                        {{ $isAr ? 'لا توجد حصص قادمة مجدولة حالياً.' : 'No upcoming live sessions scheduled.' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="pt-3 border-t border-slate-200/70 dark:border-slate-700/70 flex items-center justify-between gap-2">
                            <button type="button" onclick="filterCalendarBySubject('{{ addslashes($card['name']) }}')"
                                class="btn-lift flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-bold text-xs font-mono transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-calendar-day text-teal-600 dark:text-teal-400"></i>
                                <span>{{ $isAr ? 'حصص المادة بالتقويم' : 'View in Calendar' }}</span>
                            </button>

                            <button type="button" onclick="switchStudentTab('courses')"
                                class="btn-lift p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 hover:text-slate-900 transition-all cursor-pointer"
                                title="{{ $isAr ? 'تصفح الكورس' : 'Explore Course' }}">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State for Subjects --}}
            <div class="p-8 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl border border-teal-500/20">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                    {{ $isAr ? 'لم يتم العثور على مواد دراسية مرتبطة بحسابك بعد' : 'No Enrolled Subjects Recorded Yet' }}
                </h4>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    {{ $isAr ? 'يمكنك تصفح مقررات الأكاديمية والاشتراك لبدء حضور حصص البث المباشر ومتابعة المواد.' : 'Explore available curriculum and packages to register subjects and attend live classes.' }}
                </p>
                <div class="pt-2 flex justify-center gap-3">
                    <a href="{{ route('courses') }}" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold font-mono inline-flex items-center gap-2">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>{{ $isAr ? 'تصفح المقررات والباقات' : 'Explore Courses' }}</span>
                    </a>
                </div>
            </div>
        @endif

    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- 3. COMMENTED OUT AS REQUESTED: LOGGED-IN STUDENT'S ACTIVE PACKAGE DETAILS     --}}
    {{--    (تفاصيل باقتي التعليمية والرصيد المتاح)                                   --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{--
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg border border-teal-500/20 shrink-0">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white">
                            {{ $isAr ? 'تفاصيل باقتي التعليمية والرصيد المتاح' : 'My Learning Package & Credits Overview' }}
                        </h3>
                        @if($package)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                #PKG-{{ $package->id }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $isAr ? 'بيانات رصيد الحصص المتبقية، الاستهلاك الفعلي، وصلاحية الاشتراك المعتمد الخاص بك.' : 'Real-time record of your remaining session credits, consumption history, and plan status.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:flex-auto flex-wrap">
                @if($hasActivePackage)
                    <span class="px-3 py-1.5 rounded-full text-xs font-mono font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $isAr ? 'باقة نشطة ومفعلة' : 'Active Package' }}</span>
                    </span>
                @else
                    <span class="px-3 py-1.5 rounded-full text-xs font-mono font-bold bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30 inline-flex items-center gap-1.5 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>{{ $isAr ? 'لا توجد باقة نشطة' : 'No Active Package' }}</span>
                    </span>
                @endif

                <button type="button" onclick="window.openModal ? window.openModal('studentOwnPackageModal') : document.getElementById('studentOwnPackageModal')?.classList.remove('hidden')"
                    class="btn-lift px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-mono font-bold border border-slate-200 dark:border-slate-700 cursor-pointer transition-all">
                    <i class="fa-solid fa-list-check text-teal-600 dark:text-teal-400"></i>
                    <span>{{ $isAr ? 'سجل العمليات الكامل' : 'Full Log' }}</span>
                </button>
            </div>
        </div>

        @if($package)
            <div class="space-y-5">
                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-mono uppercase text-teal-600 dark:text-teal-400 font-extrabold tracking-wider block">
                            {{ $isAr ? 'اسم الباقة المسجلة' : 'Subscribed Plan' }}
                        </span>
                        <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white mt-0.5">
                            {{ $package->packageTemplate?->name ?: ($isAr ? 'باقة دراسية معتمدة' : 'Accredited Student Package') }}
                        </h4>
                    </div>

                    <div class="text-start sm:text-end text-xs font-mono text-slate-500 dark:text-slate-400 space-y-0.5">
                        <span class="block">
                            <i class="fa-solid fa-calendar-check text-teal-600 dark:text-teal-400"></i>
                            {{ $isAr ? 'تاريخ انتهاء الصلاحية:' : 'Valid Until:' }}
                            <strong class="text-slate-800 dark:text-slate-200">
                                {{ $package->expires_at ? $package->expires_at->format('Y-m-d') : ($isAr ? 'مفتوحة الصلاحية' : 'Unlimited') }}
                            </strong>
                        </span>
                        <span class="text-[11px] block">
                            {{ $isAr ? 'تاريخ التفعيل:' : 'Activated:' }} {{ $package->activated_at ? $package->activated_at->format('Y-m-d') : ($package->created_at ? $package->created_at->format('Y-m-d') : 'Recent') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-500/10 dark:bg-emerald-950/40 border border-emerald-500/30 text-center space-y-1.5 shadow-2xs">
                        <span class="text-xs font-mono font-bold text-emerald-800 dark:text-emerald-300 block">
                            <i class="fa-solid fa-circle-check"></i> {{ $isAr ? 'الرصيد المتاح حالياً' : 'Available Balance' }}
                        </span>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-700 dark:text-emerald-300 leading-none">
                            {{ $package->remaining_sessions }} <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">{{ $isAr ? 'حصة متبقية' : 'Sessions Remaining' }}</span><span class="sr-only">{{ $package->remaining_sessions }} {{ $isAr ? 'حصة متبقية' : 'Sessions Remaining' }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-emerald-700 dark:text-emerald-400 block pt-0.5">
                            {{ $isAr ? 'يمكنك حضورها في البث المباشر' : 'Credits ready to attend' }}
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 dark:bg-amber-950/40 border border-amber-500/30 text-center space-y-1.5 shadow-2xs">
                        <span class="text-xs font-mono font-bold text-amber-800 dark:text-amber-300 block">
                            <i class="fa-solid fa-clock-rotate-left"></i> {{ $isAr ? 'الحصص المستهلكة' : 'Consumed Credits' }}
                        </span>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-amber-900 dark:text-amber-200 leading-none">
                            {{ $package->used_sessions }} <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">{{ $isAr ? 'حصة منفذة' : 'Used' }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-amber-700 dark:text-amber-300 block pt-0.5">
                            {{ $isAr ? 'تم تسجيل حضورها بنجاح' : 'Attended & deducted' }}
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 text-center space-y-1.5 shadow-2xs">
                        <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-400 block">
                            <i class="fa-solid fa-layer-group"></i> {{ $isAr ? 'إجمالي رصيد الباقة' : 'Total Package Sessions' }}
                        </span>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white leading-none">
                            {{ $package->total_sessions }} <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">{{ $isAr ? 'حصة دراسية' : 'Total Credits' }}</span>
                        </p>
                        <span class="text-[11px] font-mono text-slate-400 block pt-0.5">
                            {{ $isAr ? 'سعة الباقة الأصلية' : 'Full plan capacity' }}
                        </span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="text-slate-700 dark:text-slate-300 flex items-center gap-1.5 font-bold">
                            <i class="fa-solid fa-chart-pie text-teal-600 dark:text-teal-400"></i>
                            <span>{{ $isAr ? 'معدل استهلاك الرصيد:' : 'Consumption Progress:' }}</span>
                            <span class="text-teal-600 dark:text-teal-400 font-extrabold">{{ $usedPct }}%</span>
                        </span>
                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ $package->remaining_sessions }}/{{ $package->total_sessions }} {{ $isAr ? 'حصة متبقية' : 'remaining' }}
                        </span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden flex">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 transition-all duration-500" style="width: {{ $usedPct }}%;" title="{{ $isAr ? 'مستهلك' : 'Used' }}"></div>
                        <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 transition-all duration-500" style="width: {{ $remainingPct }}%;" title="{{ $isAr ? 'متبقي' : 'Remaining' }}"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs font-mono font-medium text-slate-600 dark:text-slate-400 pt-0.5">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span>{{ $isAr ? 'المستهلك:' : 'Used:' }} <strong>{{ $package->used_sessions }}</strong> ({{ $usedPct }}%)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>{{ $isAr ? 'المتبقي المتاح:' : 'Available:' }} <strong>{{ $package->remaining_sessions }}</strong> ({{ $remainingPct }}%)</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 text-xs font-mono text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-xl border border-slate-200/60 dark:border-slate-800">
                    <i class="fa-solid fa-circle-info text-teal-600 dark:text-teal-400 shrink-0 text-sm"></i>
                    <span>{{ $isAr ? 'سياسة الانضباط: يتم خصم الحصة تلقائياً عند حضور الغرفة، وتسترد في رصيدك فوراً عند تقديم عذر رسمي وقبوله.' : 'Rule: 1 session is deducted upon entering the live room, and refunded if an official excuse is approved.' }}</span>
                </div>
            </div>
        @else
            <div class="p-8 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl border border-rose-500/20">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                    {{ $isAr ? 'لا توجد باقة نشطة مسجلة لحسابك حالياً (0 حصة)' : 'No Active Package Subscription (0 Sessions)' }}
                </h4>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    {{ $isAr ? 'اشترك في إحدى باقات الأكاديمية لتفعيل حضور حصص البث المباشر الخاصة والجماعية وحل الواجبات التقييمية.' : 'Subscribe to an academic package to unlock private live sessions, interactive classrooms, and evaluated homework.' }}
                </p>
                <div class="pt-2 flex justify-center gap-3">
                    <a href="{{ route('courses') }}" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold font-mono inline-flex items-center gap-2">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>{{ $isAr ? 'تصفح الباقات والكورسات' : 'Explore Packages' }}</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
    --}}

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- 4. ACADEMIC KPI SNAPSHOT (Attendance streak, Homework Average, Courses)     --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- KPI 1: Attendance Rate --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-emerald-500 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-0.5 rounded-full border border-emerald-200/80 dark:border-emerald-800 shadow-2xs">
                    {{ __('app.portal.attendance_rate') }}
                </span>
                <i class="fa-solid fa-bullseye text-emerald-500"></i>
            </div>
            <p class="font-heading font-black text-2xl text-slate-900 dark:text-white leading-none">
                @if($totalSessionCount > 0)
                    {{ $attendanceRate }}% <span class="text-xs font-bold text-emerald-600">({{ $attendanceRate >= 80 ? ($isAr ? 'ممتاز' : 'High') : ($isAr ? 'مقبول' : 'Fair') }})</span>
                @else
                    <span class="text-sm text-slate-400 font-bold">{{ $isAr ? 'لا توجد حصص بعد' : 'No records yet' }}</span>
                @endif
            </p>
            <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                {{ $attendedSessions }} {{ $isAr ? 'حضور' : 'Attended' }} &bull; {{ $approvedExcuses }} {{ $isAr ? 'أعذار مقبولة' : 'Excuses' }}
            </p>
        </div>

        {{-- KPI 2: Homework Score Average --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-amber-500 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-3 py-0.5 rounded-full border border-amber-200/80 dark:border-amber-800 shadow-2xs">
                    {{ __('app.portal.homework_rate') }}
                </span>
                <i class="fa-solid fa-pen-to-square text-amber-500"></i>
            </div>
            <p class="font-heading font-black text-2xl text-slate-900 dark:text-white leading-none">
                @if(!is_null($avgScore))
                    {{ $avgScore }}% <span class="text-xs font-bold text-amber-600">({{ $avgScore >= 80 ? ($isAr ? 'متميز' : 'High') : ($isAr ? 'مقبول' : 'Fair') }})</span>
                @else
                    <span class="text-sm text-slate-400 font-bold">{{ $isAr ? 'لا توجد درجات بعد' : 'No grades yet' }}</span>
                @endif
            </p>
            <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                {{ count($submissions) }} {{ $isAr ? 'واجبات تم تصحيحها' : 'Evaluated Submissions' }}
            </p>
        </div>

        {{-- KPI 3: Active Courses Enrolled --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-indigo-500 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-3 py-0.5 rounded-full border border-indigo-200/80 dark:border-indigo-800 shadow-2xs">
                    {{ $isAr ? 'المقررات المشترك بها' : 'Enrolled Courses' }}
                </span>
                <i class="fa-solid fa-book-open text-indigo-500"></i>
            </div>
            <p class="font-heading font-black text-2xl text-slate-900 dark:text-white leading-none">
                {{ count($enrollmentCards) }} <span class="text-xs font-bold text-indigo-600">{{ $isAr ? 'كورسات مفعلة' : 'Active Courses' }}</span>
            </p>
            <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                {{ $studentProfile?->gradeLevel?->name ?: ($isAr ? 'المرحلة الثانوية' : 'High School') }}
            </p>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    {{-- 5. PENDING EVALUATION HOMEWORK & MSQ TESTS                                  --}}
    {{-- ════════════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-500/20">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <span>{{ $isAr ? 'الواجبات التقييمية المعلقة' : 'Pending Assignments & MSQs' }}</span>
                </h3>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'حل الواجبات والاختبارات التفاعلية قبل الموعد النهائي لتأكيد الفهم واحتساب الدرجات.' : 'Complete pending homework before deadlines to secure your attendance evaluation.' }}
                </p>
            </div>
            <button type="button" onclick="switchStudentTab('assignments')" class="btn-lift px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 cursor-pointer">
                {{ $isAr ? 'عرض كافة الواجبات' : 'View All Assignments' }} ({{ count($availableAssignments) }}) &rarr;
            </button>
        </div>

        @if(count($availableAssignments) > 0)
            <div class="table-responsive rounded-2xl border border-slate-200/80 dark:border-slate-800">
                <table class="w-full text-start text-xs sm:text-sm elite-sortable-table">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                        <tr>
                            <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[260px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'عنوان الواجب' : 'Assignment' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[200px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المقرر' : 'Course' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الموعد النهائي' : 'Deadline' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[110px]" data-sort-type="number">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span>{{ $isAr ? 'درجة النجاح' : 'Pass Mark' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-action py-3.5 px-4 text-end min-w-[140px]" data-no-sort="true">{{ $isAr ? 'الإجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($availableAssignments->take(5) as $assign)
                            @php
                                $isInProgress = isset($inProgressSubmissions[$assign->id]);
                                $courseTitle = $assign->course?->title ?: ($assign->liveSession?->course?->title ?: ($isAr ? 'كورس التخصص' : 'Course Module'));
                                $isFileHomework = $assign->is_file_homework;
                                $homeworkFile = $assign->homework_file;
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                                <td class="col-title py-3.5 px-4 font-bold text-slate-900 dark:text-white leading-relaxed min-w-[260px] max-w-[380px]">
                                    <div class="flex items-start gap-2">
                                        @if($isInProgress)
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping shrink-0 mt-1.5"></span>
                                        @endif
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($isFileHomework)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-file-arrow-up"></i>
                                                        <span>{{ $isAr ? 'واجب منزلي (ملف)' : 'Homework File' }}</span>
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-list-check"></i>
                                                        <span>MSQ</span>
                                                    </span>
                                                @endif
                                                <span class="break-words font-semibold text-slate-900 dark:text-slate-100">{{ $assign->title }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="col-course py-3.5 px-3 text-slate-600 dark:text-slate-300 leading-normal min-w-[200px] max-w-[300px]">
                                    <span class="line-clamp-2">{{ $courseTitle }}</span>
                                </td>
                                <td class="col-date py-3.5 px-3 text-amber-700 dark:text-amber-400 font-mono font-bold whitespace-nowrap min-w-[140px]">
                                    {{ $assign->effective_due_at ? $assign->effective_due_at->format('Y-m-d H:i') : ($isAr ? '24 ساعة قبل الحصة' : '24h Pre-Session') }}
                                </td>
                                <td class="col-badge py-3.5 px-3 text-center whitespace-nowrap min-w-[110px]">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono font-bold text-xs border border-slate-200 dark:border-slate-700 inline-block">
                                        {{ number_format($assign->passing_score ?? 70, 0) }}%
                                    </span>
                                </td>
                                <td class="col-action py-3.5 px-4 text-end whitespace-nowrap min-w-[140px]">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($isFileHomework && $homeworkFile)
                                            <a href="{{ route('portal.files.download', $homeworkFile->id) }}"
                                                class="p-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 border border-slate-200 dark:border-slate-700 transition-colors"
                                                title="{{ $isAr ? 'تحميل ملف ورقة الواجب' : 'Download Worksheet File' }}">
                                                <i class="fa-solid fa-file-arrow-down"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}"
                                            class="btn-lift px-3.5 py-2 rounded-xl text-xs font-bold {{ $isInProgress ? 'bg-amber-600 hover:bg-amber-500 text-white' : ($isFileHomework ? 'bg-indigo-600 hover:bg-indigo-500 text-white' : 'bg-teal-600 hover:bg-teal-500 text-white') }} shadow-xs inline-flex items-center gap-1.5">
                                            <span><i class="fa-solid {{ $isFileHomework ? 'fa-file-signature' : 'fa-bolt' }} text-[11px]"></i></span>
                                            <span>
                                                @if($isFileHomework)
                                                    {{ $isInProgress ? ($isAr ? 'استكمال رفع الحل' : 'Resume Submission') : ($isAr ? 'تسليم الواجب' : 'Submit Homework') }}
                                                @else
                                                    {{ $isInProgress ? ($isAr ? 'استكمال الاختبار' : 'Resume') : ($isAr ? 'بدء الاختبار' : 'Start Test') }}
                                                @endif
                                            </span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 bg-emerald-500/10 dark:bg-emerald-950/20 rounded-2xl border border-emerald-500/20 text-center space-y-2">
                <div class="text-3xl text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-circle-check"></i></div>
                <h4 class="font-bold text-sm sm:text-base text-emerald-950 dark:text-emerald-200">
                    {{ $isAr ? 'رائع! تم حل جميع الواجبات المتاحة بنجاح' : 'All available assignments solved successfully!' }}
                </h4>
                <p class="text-xs font-mono text-emerald-800 dark:text-emerald-400">
                    {{ $isAr ? 'يمكنك مراجعة درجاتك وتقييماتك في قسم سجل التسليمات والدرجات.' : 'You can inspect your scores in the Submissions & Grades section.' }}
                </p>
            </div>
        @endif
    </div>

</section>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- CALENDAR INTERACTIVE SCRIPT (Vanilla JS, Full Dark/Light Support)           --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<script>
(function() {
    let rawSessions = @json($allSessionsForCalendar ?? []);
    const isAr = {{ $isAr ? 'true' : 'false' }};
    const studentPackageData = {
        total: {{ (int) ($pkgTotal ?? 0) }},
        used: {{ (int) ($pkgUsed ?? 0) }},
        remaining: {{ (int) ($pkgRemaining ?? 0) }},
        currentNum: {{ (int) ($pkgCurrentNum ?? 0) }},
        hasPackage: {{ ($package && $package->total_sessions > 0) ? 'true' : 'false' }}
    };
    
    let currentDate = new Date();
    let calYear = currentDate.getFullYear();
    let calMonth = currentDate.getMonth(); // 0 - 11
    let selectedDate = null; // 'YYYY-MM-DD'
    let currentFilter = 'week'; // DEFAULT: 'week', 'today', 'upcoming', 'history', 'all', 'date'
    let calViewMode = 'weekly'; // DEFAULT: 'weekly', 'monthly'
    let searchQuery = '';

    const monthNamesAr = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    const monthNamesEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    const weekDayNamesAr = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
    const weekDayNamesEn = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    function formatDateKey(year, month, day) {
        const m = String(month + 1).padStart(2, '0');
        const d = String(day).padStart(2, '0');
        return `${year}-${m}-${d}`;
    }

    function getTodayKey() {
        const now = new Date();
        return formatDateKey(now.getFullYear(), now.getMonth(), now.getDate());
    }

    // Week starts on Saturday (Standard Arab / Egyptian Academic Week)
    function getStartOfWeek(date) {
        const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const day = d.getDay(); // 0: Sun, 1: Mon, ..., 6: Sat
        const diff = (day + 1) % 7;
        d.setDate(d.getDate() - diff);
        d.setHours(0, 0, 0, 0);
        return d;
    }

    let currentWeekStart = getStartOfWeek(new Date());

    function getEndOfWeek(startDate) {
        const d = new Date(startDate);
        d.setDate(startDate.getDate() + 6);
        d.setHours(23, 59, 59, 999);
        return d;
    }

    function formatWeekRangeTitle(start, end) {
        const sD = start.getDate();
        const sM = monthNamesAr[start.getMonth()];
        const sY = start.getFullYear();
        const eD = end.getDate();
        const eM = monthNamesAr[end.getMonth()];
        const eY = end.getFullYear();

        if (sY === eY) {
            if (sM === eM) {
                return `${sD} — ${eD} ${sM} ${sY}`;
            }
            return `${sD} ${sM} — ${eD} ${eM} ${sY}`;
        }
        return `${sD} ${sM} ${sY} — ${eD} ${eM} ${eY}`;
    }

    function formatWeekRangeTitleEn(start, end) {
        const sD = start.getDate();
        const sM = monthNamesEn[start.getMonth()];
        const sY = start.getFullYear();
        const eD = end.getDate();
        const eM = monthNamesEn[end.getMonth()];
        const eY = end.getFullYear();

        if (sY === eY) {
            if (sM === eM) {
                return `${sM} ${sD} — ${eD}, ${sY}`;
            }
            return `${sM} ${sD} — ${eM} ${eD}, ${sY}`;
        }
        return `${sM} ${sD}, ${sY} — ${eM} ${eD}, ${eY}`;
    }

    window.navStudentWeek = function(dir) {
        currentWeekStart.setDate(currentWeekStart.getDate() + (dir * 7));
        renderWeeklyDaysStrip();
        renderWeeklySummary();
        renderSessionList();
    };

    window.jumpStudentWeekToday = function() {
        currentWeekStart = getStartOfWeek(new Date());
        currentFilter = 'week';
        selectedDate = null;
        updateFilterPills();
        renderWeeklyDaysStrip();
        renderWeeklySummary();
        renderSessionList();
    };

    window.showAllWeekSessions = function() {
        currentFilter = 'week';
        selectedDate = null;
        updateFilterPills();
        renderWeeklyDaysStrip();
        renderSessionList();
    };

    window.switchCalViewMode = function(mode) {
        calViewMode = mode;
        updateModeButtons();

        const weekStrip = document.getElementById('calWeekStripWrapper');
        const weekSummary = document.getElementById('calWeekSummaryCol');
        const monthPicker = document.getElementById('calMonthPickerCol');
        const sessionsCol = document.getElementById('calSessionsCol');

        if (mode === 'weekly') {
            if (weekStrip) weekStrip.classList.remove('hidden');
            if (weekSummary) weekSummary.classList.remove('hidden');
            if (monthPicker) monthPicker.classList.add('hidden');
            if (sessionsCol) {
                sessionsCol.className = 'lg:col-span-8 space-y-4';
            }
            renderWeeklyDaysStrip();
            renderWeeklySummary();
            renderSessionList();
        } else {
            if (weekStrip) weekStrip.classList.add('hidden');
            if (weekSummary) weekSummary.classList.add('hidden');
            if (monthPicker) monthPicker.classList.remove('hidden');
            if (sessionsCol) {
                sessionsCol.className = 'lg:col-span-7 space-y-4';
            }
            renderCalendar();
            renderSessionList();
        }
    };

    function updateModeButtons() {
        const btnWeek = document.getElementById('calModeBtn_week');
        const btnMonth = document.getElementById('calModeBtn_month');
        if (calViewMode === 'weekly') {
            if (btnWeek) btnWeek.className = 'px-3.5 py-1.5 rounded-xl text-xs font-mono font-black transition-all cursor-pointer bg-teal-600 text-white shadow-2xs flex items-center gap-1.5';
            if (btnMonth) btnMonth.className = 'px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5';
        } else {
            if (btnMonth) btnMonth.className = 'px-3.5 py-1.5 rounded-xl text-xs font-mono font-black transition-all cursor-pointer bg-teal-600 text-white shadow-2xs flex items-center gap-1.5';
            if (btnWeek) btnWeek.className = 'px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-1.5';
        }
    }

    function updateFilterPills() {
        const pills = ['week', 'today', 'upcoming', 'history', 'all'];
        pills.forEach(p => {
            const el = document.getElementById('calPill_' + p);
            if (!el) return;
            if (currentFilter === p) {
                el.className = 'cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-teal-600 text-white shadow-2xs';
            } else {
                el.className = 'cal-filter-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold transition-all cursor-pointer bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300';
            }
        });
    }

    window.navStudentCal = function(dir) {
        calMonth += dir;
        if (calMonth < 0) {
            calMonth = 11;
            calYear--;
        } else if (calMonth > 11) {
            calMonth = 0;
            calYear++;
        }
        renderCalendar();
    };

    window.jumpStudentCalToday = function() {
        const now = new Date();
        calYear = now.getFullYear();
        calMonth = now.getMonth();
        selectedDate = getTodayKey();
        currentFilter = 'today';
        updateFilterPills();
        renderCalendar();
        renderSessionList();
    };

    window.setStudentCalFilter = function(filter) {
        currentFilter = filter;
        selectedDate = null;
        if (filter === 'today') {
            selectedDate = getTodayKey();
            const now = new Date();
            calYear = now.getFullYear();
            calMonth = now.getMonth();
        } else if (filter === 'week') {
            selectedDate = null;
        }
        updateFilterPills();
        if (calViewMode === 'weekly') {
            renderWeeklyDaysStrip();
            renderWeeklySummary();
        } else {
            renderCalendar();
        }
        renderSessionList();
    };

    window.onStudentCalSearch = function(val) {
        searchQuery = (val || '').toLowerCase().trim();
        renderSessionList();
    };

    window.selectStudentCalDate = function(dateStr) {
        selectedDate = dateStr;
        currentFilter = 'date';
        updateFilterPills();
        if (calViewMode === 'weekly') {
            renderWeeklyDaysStrip();
        } else {
            renderCalendar();
        }
        renderSessionList();
    };

    window.filterCalendarBySubject = function(subjectName) {
        currentFilter = 'all';
        selectedDate = null;
        updateFilterPills();
        const searchInput = document.getElementById('calSearchInput');
        if (searchInput) {
            searchInput.value = subjectName;
        }
        searchQuery = (subjectName || '').toLowerCase().trim();
        renderSessionList();
        const calSection = document.getElementById('studentCalendarSection');
        if (calSection) {
            calSection.scrollIntoView({ behavior: 'smooth' });
        }
    };

    function renderWeeklyDaysStrip() {
        const rangeTitle = document.getElementById('calWeekRangeTitle');
        const stripContainer = document.getElementById('calWeekDaysStrip');
        if (!stripContainer) return;

        const endOfWeek = getEndOfWeek(currentWeekStart);
        const todayKey = getTodayKey();

        if (rangeTitle) {
            rangeTitle.innerText = isAr 
                ? formatWeekRangeTitle(currentWeekStart, endOfWeek)
                : formatWeekRangeTitleEn(currentWeekStart, endOfWeek);
        }

        stripContainer.innerHTML = '';

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(currentWeekStart);
            dayDate.setDate(currentWeekStart.getDate() + i);

            const dateKey = formatDateKey(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
            const dayNum = dayDate.getDate();
            const monthShort = isAr ? monthNamesAr[dayDate.getMonth()] : monthNamesEn[dayDate.getMonth()].slice(0, 3);
            const dayName = isAr ? weekDayNamesAr[i] : weekDayNamesEn[i];

            const isToday = (dateKey === todayKey);
            const isSelected = (dateKey === selectedDate);

            // Filter sessions for this day
            const daySessions = rawSessions.filter(s => s.date === dateKey);
            const hasSessions = daySessions.length > 0;
            const hasLive = daySessions.some(s => s.is_live);
            const hasUpcoming = daySessions.some(s => !s.is_past);

            const card = document.createElement('button');
            card.type = 'button';
            card.setAttribute('data-date', dateKey);
            card.onclick = () => window.selectStudentCalDate(dateKey);

            let cardStyle = '';
            let dayNameStyle = '';
            let numberStyle = '';
            let monthStyle = '';
            let badgeHtml = '';

            if (isSelected) {
                cardStyle = 'bg-teal-600 text-white border-teal-600 shadow-md ring-2 ring-teal-400 scale-[1.03] z-10';
                dayNameStyle = 'text-teal-100';
                numberStyle = 'text-white';
                monthStyle = 'text-teal-100';
                badgeHtml = `<span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white">${hasSessions ? `${daySessions.length} ${isAr ? 'حصة' : 'classes'}` : (isAr ? 'محدد' : 'Selected')}</span>`;
            } else if (isToday) {
                cardStyle = 'bg-gradient-to-b from-teal-500/15 via-teal-500/5 to-white dark:to-slate-900 border-2 border-teal-500 shadow-sm hover:border-teal-600';
                dayNameStyle = 'text-teal-700 dark:text-teal-300 font-extrabold';
                numberStyle = 'text-teal-950 dark:text-teal-100 font-black';
                monthStyle = 'text-teal-600 dark:text-teal-400 font-bold';
                if (hasSessions) {
                    badgeHtml = `<span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold px-2 py-0.5 rounded-full ${hasLive ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 animate-pulse' : 'bg-teal-100 dark:bg-teal-900/60 text-teal-800 dark:text-teal-200'}">
                        <span class="w-1.5 h-1.5 rounded-full ${hasLive ? 'bg-rose-500 animate-ping' : 'bg-teal-500'}"></span>
                        ${daySessions.length} ${isAr ? 'حصة' : 'class'}
                    </span>`;
                } else {
                    badgeHtml = `<span class="text-[10px] font-mono text-teal-600 dark:text-teal-400 font-bold">${isAr ? 'اليوم' : 'Today'}</span>`;
                }
            } else if (hasSessions) {
                cardStyle = 'bg-white dark:bg-slate-800/90 text-slate-800 dark:text-slate-200 border-2 border-teal-500/40 hover:border-teal-500 hover:shadow-sm';
                dayNameStyle = 'text-slate-600 dark:text-slate-300 font-bold';
                numberStyle = 'text-slate-900 dark:text-white font-black';
                monthStyle = 'text-slate-400 dark:text-slate-400';
                badgeHtml = `<span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full ${hasLive ? 'bg-rose-500 animate-ping' : (hasUpcoming ? 'bg-teal-500' : 'bg-slate-400')}"></span>
                    ${daySessions.length} ${isAr ? 'حصة' : 'class'}
                </span>`;
            } else {
                cardStyle = 'bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800';
                dayNameStyle = 'text-slate-500 dark:text-slate-400 font-semibold';
                numberStyle = 'text-slate-700 dark:text-slate-300 font-bold';
                monthStyle = 'text-slate-400 dark:text-slate-500';
                badgeHtml = `<span class="text-[10px] font-mono text-slate-400 dark:text-slate-500">—</span>`;
            }

            card.className = `cal-week-card group relative p-3 sm:p-3.5 rounded-2xl border text-center transition-all duration-200 cursor-pointer flex flex-col justify-between items-center ${cardStyle}`;

            card.innerHTML = `
                <div class="w-full flex items-center justify-between gap-1 mb-1">
                    <span class="text-[11px] font-mono ${dayNameStyle}">${dayName}</span>
                    ${isToday ? `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-teal-600 text-white animate-pulse">${isAr ? 'اليوم' : 'Today'}</span>` : ''}
                </div>
                <div class="my-1 text-center">
                    <div class="text-xl sm:text-2xl font-black font-heading leading-tight ${numberStyle}">${dayNum}</div>
                    <div class="text-[10px] font-mono ${monthStyle}">${monthShort}</div>
                </div>
                <div class="w-full pt-1.5 border-t border-slate-100 dark:border-slate-700/60 mt-1 flex items-center justify-center">
                    ${badgeHtml}
                </div>
            `;

            stripContainer.appendChild(card);
        }
    }

    function renderWeeklySummary() {
        const classesCountEl = document.getElementById('calWeekClassesCount');
        const completedCountEl = document.getElementById('calWeekCompletedCount');
        const remainingCountEl = document.getElementById('calWeekRemainingCount');
        const activeDaysCountEl = document.getElementById('calWeekActiveDaysCount');
        const nextClassCard = document.getElementById('calWeekNextClassCard');
        if (!classesCountEl) return;

        const startKey = formatDateKey(currentWeekStart.getFullYear(), currentWeekStart.getMonth(), currentWeekStart.getDate());
        const endOfWeek = getEndOfWeek(currentWeekStart);
        const endKey = formatDateKey(endOfWeek.getFullYear(), endOfWeek.getMonth(), endOfWeek.getDate());

        const weekSessions = rawSessions.filter(s => s.date >= startKey && s.date <= endKey);
        
        // 1. Total Sessions in Week (All)
        classesCountEl.innerText = weekSessions.length;

        // 2. Completed Sessions in Week
        if (completedCountEl) {
            completedCountEl.innerText = weekSessions.filter(s => s.is_past || s.status === 'completed').length;
        }

        // 3. Remaining Sessions in Week
        if (remainingCountEl) {
            remainingCountEl.innerText = weekSessions.filter(s => !s.is_past && s.status !== 'completed').length;
        }

        const activeDays = new Set(weekSessions.map(s => s.date));
        if (activeDaysCountEl) {
            activeDaysCountEl.innerText = activeDays.size;
        }

        // Package Indicators Reactive Update
        let totalPkg = studentPackageData.hasPackage ? studentPackageData.total : rawSessions.length;
        let usedPkg = studentPackageData.hasPackage ? studentPackageData.used : rawSessions.filter(s => s.is_past || s.status === 'completed').length;
        let remainingPkg = studentPackageData.hasPackage ? studentPackageData.remaining : rawSessions.filter(s => !s.is_past && s.status !== 'completed').length;
        let currentPkgNum = studentPackageData.hasPackage ? studentPackageData.currentNum : (totalPkg > 0 ? Math.min(totalPkg, Math.max(1, usedPkg + (remainingPkg > 0 ? 1 : 0))) : 0);

        const usedEl = document.getElementById('pkgUsedSessionsCount');
        if (usedEl) usedEl.innerText = usedPkg;

        const remEl = document.getElementById('pkgRemainingSessionsCount');
        if (remEl) remEl.innerText = remainingPkg;

        const curEl = document.getElementById('pkgCurrentSessionNum');
        if (curEl) curEl.innerText = currentPkgNum;

        const totEl = document.getElementById('pkgTotalSessionsCount');
        if (totEl) totEl.innerText = totalPkg;

        const barEl = document.getElementById('pkgProgressBarFill');
        const pctEl = document.getElementById('pkgProgressPercentText');
        const pct = totalPkg > 0 ? Math.min(100, Math.round((usedPkg / totalPkg) * 100)) : 0;
        if (barEl) barEl.style.width = `${pct}%`;
        if (pctEl) pctEl.innerText = `${pct}%`;

        if (nextClassCard) {
            // Find earliest upcoming session in this week, or today's session
            const upcomingThisWeek = weekSessions.filter(s => !s.is_past);
            const liveThisWeek = weekSessions.find(s => s.is_live);
            const targetSession = liveThisWeek || upcomingThisWeek[0];

            if (targetSession) {
                nextClassCard.innerHTML = `
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-mono font-bold text-teal-600 dark:text-teal-400 uppercase">
                            ${targetSession.is_live ? (isAr ? '🔴 جارية الآن' : '🔴 Live Now') : (isAr ? 'أقرب حصة هذا الأسبوع' : 'Next This Week')}
                        </span>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                            ${targetSession.subject}
                        </span>
                    </div>
                    <h6 class="font-heading font-black text-xs text-slate-900 dark:text-white truncate">
                        ${targetSession.title}
                    </h6>
                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-700/60">
                        <span><i class="fa-regular fa-clock text-amber-500"></i> ${targetSession.date} ${targetSession.time}</span>
                        ${targetSession.can_join ? `<a href="${targetSession.join_url}" class="text-teal-600 dark:text-teal-400 font-bold hover:underline">${isAr ? 'دخول' : 'Join'} &rarr;</a>` : ''}
                    </div>
                `;
            } else if (weekSessions.length > 0) {
                nextClassCard.innerHTML = `
                    <div class="text-center py-1">
                        <span class="text-emerald-600 dark:text-emerald-400 text-sm"><i class="fa-solid fa-circle-check"></i></span>
                        <p class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 mt-1">${isAr ? 'اكتملت جميع حصص هذا الأسبوع' : 'All week classes completed'}</p>
                    </div>
                `;
            } else {
                nextClassCard.innerHTML = `
                    <div class="text-center py-1 text-slate-400">
                        <span class="text-xs font-mono">${isAr ? 'لا توجد حصص مجدولة هذا الأسبوع' : 'No sessions scheduled this week'}</span>
                    </div>
                `;
            }
        }
    }

    function renderCalendar() {
        const monthTitle = document.getElementById('calMonthYearTitle');
        const subTitle = document.getElementById('calActiveDateSubtitle');
        const grid = document.getElementById('calDaysGrid');
        if (!grid || !monthTitle) return;

        const monthName = isAr ? monthNamesAr[calMonth] : monthNamesEn[calMonth];
        monthTitle.innerText = `${monthName} ${calYear}`;

        if (subTitle) {
            if (selectedDate) {
                subTitle.innerText = isAr ? `محدد: ${selectedDate}` : `Selected: ${selectedDate}`;
            } else {
                subTitle.innerText = isAr ? 'انقر على أي يوم لاستعراض حصصه' : 'Click a day to view sessions';
            }
        }

        grid.innerHTML = '';

        const todayKey = getTodayKey();
        const firstDay = new Date(calYear, calMonth, 1).getDay(); // 0 is Sunday
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(calYear, calMonth, 0).getDate();

        // 1. Previous month blank/dim days
        for (let i = firstDay - 1; i >= 0; i--) {
            const dayNum = daysInPrevMonth - i;
            const cell = document.createElement('div');
            cell.className = 'cal-day-btn text-slate-300 dark:text-slate-700 text-xs font-mono font-medium select-none pointer-events-none opacity-40';
            cell.innerText = dayNum;
            cell.style.aspectRatio = '1 / 1';
            cell.style.minHeight = '38px';
            cell.style.display = 'flex';
            cell.style.alignItems = 'center';
            cell.style.justifyContent = 'center';
            grid.appendChild(cell);
        }

        // 2. Days of current month
        for (let d = 1; d <= daysInMonth; d++) {
            const dateKey = formatDateKey(calYear, calMonth, d);
            const isToday = (dateKey === todayKey);
            const isSelected = (dateKey === selectedDate);

            // Find sessions for this date
            const daySessions = rawSessions.filter(s => s.date === dateKey);
            const hasSessions = daySessions.length > 0;
            const hasLive = daySessions.some(s => s.is_live);
            const hasUpcoming = daySessions.some(s => !s.is_past);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.setAttribute('data-date', dateKey);
            btn.onclick = () => window.selectStudentCalDate(dateKey);
            btn.style.aspectRatio = '1 / 1';
            btn.style.minHeight = '38px';
            btn.style.width = '100%';
            btn.style.display = 'flex';
            btn.style.flexDirection = 'column';
            btn.style.alignItems = 'center';
            btn.style.justifyContent = 'center';

            let styleClass = 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700/80 hover:border-teal-500/50';
            if (isSelected) {
                styleClass = 'bg-teal-600 text-white font-black shadow-md ring-2 ring-teal-400 border-teal-600 scale-105';
            } else if (isToday) {
                styleClass = 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 font-black border-2 border-teal-500 shadow-2xs';
            } else if (hasSessions) {
                styleClass = 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-extrabold border-2 border-teal-500/40';
            }

            btn.className = `cal-day-btn text-xs font-mono cursor-pointer ${styleClass}`;

            const numSpan = document.createElement('span');
            numSpan.className = 'font-bold text-xs leading-none';
            numSpan.innerText = d;
            btn.appendChild(numSpan);

            // Indicators
            if (hasSessions) {
                const indicator = document.createElement('div');
                indicator.className = 'flex items-center gap-0.5 mt-1';
                if (hasLive || isToday) {
                    indicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>';
                } else if (hasUpcoming) {
                    indicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>';
                } else {
                    indicator.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>';
                }
                btn.appendChild(indicator);
            }

            grid.appendChild(btn);
        }
    }

    function renderSessionList() {
        const container = document.getElementById('calSessionsContainer');
        const headerTitle = document.getElementById('calListHeaderTitle');
        const counter = document.getElementById('calListCounter');
        if (!container) return;

        const todayKey = getTodayKey();
        let list = [];
        let titleText = '';
        let showUpcomingFallback = false;

        if (currentFilter === 'date' && selectedDate) {
            list = rawSessions.filter(s => s.date === selectedDate);
            const parts = selectedDate.split('-');
            const dObj = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            const dName = isAr ? weekDayNamesAr[(dObj.getDay() + 1) % 7] : weekDayNamesEn[(dObj.getDay() + 1) % 7];
            titleText = isAr ? `حصص يوم: ${dName} (${selectedDate})` : `Sessions on ${dName} (${selectedDate})`;
        } else if (currentFilter === 'week') {
            const startKey = formatDateKey(currentWeekStart.getFullYear(), currentWeekStart.getMonth(), currentWeekStart.getDate());
            const endOfWeek = getEndOfWeek(currentWeekStart);
            const endKey = formatDateKey(endOfWeek.getFullYear(), endOfWeek.getMonth(), endOfWeek.getDate());

            list = rawSessions.filter(s => s.date >= startKey && s.date <= endKey);
            // Sort week sessions chronologically: live first, then date & time
            list.sort((a, b) => {
                if (a.is_live && !b.is_live) return -1;
                if (!a.is_live && b.is_live) return 1;
                return (a.iso_date || a.date).localeCompare(b.iso_date || b.date);
            });

            const rangeStr = isAr ? formatWeekRangeTitle(currentWeekStart, endOfWeek) : formatWeekRangeTitleEn(currentWeekStart, endOfWeek);
            titleText = isAr ? `حصص هذا الأسبوع (${rangeStr})` : `This Week's Sessions (${rangeStr})`;

            if (list.length === 0) {
                showUpcomingFallback = true;
            }
        } else if (currentFilter === 'today') {
            list = rawSessions.filter(s => s.is_today || s.date === todayKey || s.is_live);
            titleText = isAr ? 'حصص اليوم المقررة' : 'Today\'s Scheduled Sessions';
            if (list.length === 0) {
                showUpcomingFallback = true;
            }
        } else if (currentFilter === 'upcoming') {
            list = rawSessions.filter(s => !s.is_past);
            titleText = isAr ? 'جميع الحصص القادمة' : 'All Upcoming Sessions';
        } else if (currentFilter === 'history') {
            list = rawSessions.filter(s => s.is_past);
            titleText = isAr ? 'سجل الحصص المنتهية' : 'Session History';
        } else {
            list = [...rawSessions];
            titleText = isAr ? 'كافة الحصص المسجلة' : 'All Enrolled Sessions';
        }

        // Apply Search filter
        if (searchQuery) {
            list = list.filter(s => {
                const t = (s.title || '').toLowerCase();
                const subj = (s.subject || '').toLowerCase();
                const teacher = (s.teacher || '').toLowerCase();
                const c = (s.course || '').toLowerCase();
                return t.includes(searchQuery) || subj.includes(searchQuery) || teacher.includes(searchQuery) || c.includes(searchQuery);
            });
            titleText += isAr ? ` (نتائج البحث: "${searchQuery}")` : ` (Search: "${searchQuery}")`;
            showUpcomingFallback = false;
        }

        if (headerTitle) {
            headerTitle.innerHTML = `<i class="fa-solid fa-list-ul text-teal-600 dark:text-teal-400"></i> <span>${titleText}</span>`;
        }
        if (counter) {
            counter.innerText = showUpcomingFallback ? rawSessions.filter(s => !s.is_past).length : list.length;
        }

        container.innerHTML = '';

        // If current week or today has no sessions, show friendly notice followed by upcoming sessions
        if (showUpcomingFallback) {
            const nextUpcoming = rawSessions.filter(s => !s.is_past).slice(0, 5);
            
            const notice = document.createElement('div');
            notice.className = 'p-3.5 rounded-2xl bg-teal-50/70 dark:bg-teal-950/40 border border-teal-200/70 dark:border-teal-800/60 text-xs font-mono text-teal-900 dark:text-teal-200 flex items-center justify-between gap-3';
            notice.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <span>${isAr ? 'لا توجد حصص مجدولة لهذا الأسبوع • إليك أقرب الحصص القادمة:' : 'No classes scheduled this week. Here are your upcoming sessions:'}</span>
                </div>
                <span class="text-[10px] font-bold bg-white dark:bg-slate-800 text-teal-600 dark:text-teal-400 px-2 py-0.5 rounded-lg border border-teal-200 dark:border-teal-700 shrink-0">
                    ${isAr ? 'جدول المواعيد' : 'Schedule'}
                </span>
            `;
            container.appendChild(notice);

            if (nextUpcoming.length === 0) {
                const emptyNotice = document.createElement('div');
                emptyNotice.className = 'p-6 text-center text-xs font-mono text-slate-400';
                emptyNotice.innerText = isAr ? 'لا توجد حصص قادمة مجدولة حالياً.' : 'No upcoming sessions scheduled.';
                container.appendChild(emptyNotice);
                return;
            }

            nextUpcoming.forEach(item => {
                container.appendChild(createSessionCard(item));
            });
            return;
        }

        if (list.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 space-y-2">
                    <div class="text-2xl text-slate-400"><i class="fa-regular fa-calendar-xmark"></i></div>
                    <p class="text-xs font-mono font-bold">${isAr ? 'لا توجد حصص تطابق التحديد الحالي' : 'No sessions match the current selection.'}</p>
                    <p class="text-[11px] font-mono text-slate-400">${isAr ? 'اختر يوماً آخر من الأسبوع أو انقر "عرض كافة حصص الأسبوع".' : 'Select another day or click "All Week Sessions".'}</p>
                </div>
            `;
            return;
        }

        list.forEach(item => {
            container.appendChild(createSessionCard(item));
        });
    }

    function createSessionCard(item) {
        const isLive = item.is_live;
        const isPast = item.is_past;
        const canJoin = item.can_join;

        let borderTheme = 'border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900';
        if (isLive) {
            borderTheme = 'border-emerald-500/60 dark:border-emerald-500/50 bg-emerald-500/5 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20';
        }

        let badgeHtml = '';
        if (isLive) {
            badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-rose-600 text-white animate-pulse inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span> ${isAr ? 'مباشر الآن' : 'LIVE'}</span>`;
        } else if (item.is_today) {
            badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">${isAr ? 'اليوم' : 'Today'}</span>`;
        } else if (isPast) {
            badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">${isAr ? 'منتهية' : 'Ended'}</span>`;
        } else {
            badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-300 border border-teal-200 dark:border-teal-800">${isAr ? 'قادمة' : 'Scheduled'}</span>`;
        }

        let actionHtml = '';
        if (canJoin || isLive) {
            actionHtml = `
                <a href="${item.join_url}" class="btn-lift px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs inline-flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>${isAr ? 'دخول الحصة' : 'Join'}</span>
                </a>
            `;
        } else if (isPast) {
            actionHtml = `<span class="text-[11px] font-mono text-slate-400 font-bold"><i class="fa-solid fa-check text-emerald-500"></i> ${isAr ? 'تمت الحصة' : 'Completed'}</span>`;
        } else {
            actionHtml = `<span class="text-[11px] font-mono text-slate-500 dark:text-slate-400"><i class="fa-solid fa-lock text-amber-500"></i> ${isAr ? 'مجدولة' : 'Upcoming'}</span>`;
        }

        const card = document.createElement('div');
        card.className = `p-4 rounded-2xl border transition-all hover:shadow-sm space-y-2.5 ${borderTheme}`;
        card.innerHTML = `
            <div class="flex items-start justify-between gap-2">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        ${badgeHtml}
                        <span class="text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                            ${item.subject}
                        </span>
                    </div>
                    <h5 class="font-heading font-black text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                        ${item.title}
                    </h5>
                </div>
                <div class="shrink-0">
                    ${actionHtml}
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono text-slate-500 dark:text-slate-400 pt-1.5 border-t border-slate-100 dark:border-slate-800/80">
                <div class="flex items-center gap-2 flex-wrap">
                    <span><i class="fa-solid fa-chalkboard-user text-teal-600 dark:text-teal-400"></i> ${item.teacher}</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 font-bold ${isLive ? 'text-rose-600 dark:text-rose-400' : ''}"><i class="fa-solid fa-stopwatch text-slate-400"></i> ${item.duration} ${isAr ? 'د' : 'min'}</span>
                    ${studentPackageData.hasPackage ? `
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 px-2 py-0.5 rounded-full border border-teal-200 dark:border-teal-800" title="${isAr ? 'حصة محسوبة ضمن باقة الطالب' : 'Covered in student package'}">
                            <i class="fa-solid fa-circle-check text-teal-500"></i>
                            ${isLive ? (isAr ? 'محسومة من الباقة' : 'Package Deducted') : (isAr ? 'ضمن رصيد الباقة' : 'In Package')}
                        </span>
                    ` : ''}
                </div>
                <div>
                    <i class="fa-regular fa-clock text-amber-500"></i> <strong>${item.date}</strong> ${item.time}
                </div>
            </div>
        `;
        return card;
    }

    // Global hook to refresh calendar on tab navigation
    window.refreshStudentCalendar = function() {
        if (calViewMode === 'weekly') {
            renderWeeklyDaysStrip();
            renderWeeklySummary();
        } else {
            renderCalendar();
        }
        renderSessionList();
    };

    // Real-time dynamic updates for calendar, live banner, and package balance
    window.updateStudentCalendarData = function(newSessions, newPackage, liveSession) {
        if (Array.isArray(newSessions)) {
            rawSessions = newSessions;
        }

        if (newPackage) {
            studentPackageData.hasPackage = !!newPackage.has_package;
            studentPackageData.total = parseInt(newPackage.total) || 0;
            studentPackageData.used = parseInt(newPackage.used) || 0;
            studentPackageData.remaining = parseInt(newPackage.remaining) || 0;
            studentPackageData.currentNum = studentPackageData.total > 0 
                ? Math.min(studentPackageData.total, Math.max(1, studentPackageData.used + (studentPackageData.remaining > 0 ? 1 : 0))) 
                : 0;
        }

        // Update Live Classroom Banner elements
        const banner = document.getElementById('studentLiveClassroomBanner');
        if (banner) {
            if (liveSession && (liveSession.is_live || liveSession.can_join !== false)) {
                banner.classList.remove('hidden');
                const durEl = document.getElementById('studentLiveBannerDuration');
                if (durEl) durEl.textContent = `${liveSession.duration || 60} ${isAr ? 'دقيقة' : 'min'}`;
                const titleTextEl = document.getElementById('studentLiveBannerTitleText');
                if (titleTextEl) titleTextEl.textContent = liveSession.title || (isAr ? 'حصة تفاعلية' : 'Live Class');
                const teacherEl = document.getElementById('studentLiveBannerTeacher');
                if (teacherEl) teacherEl.textContent = `(${liveSession.teacher || 'Teacher'})`;
                const joinBtn = document.getElementById('studentLiveClassroomJoinBtn');
                if (joinBtn && liveSession.join_url) joinBtn.href = liveSession.join_url;
            } else if (!liveSession) {
                const activeLive = rawSessions.find(s => s.is_live);
                if (activeLive) {
                    banner.classList.remove('hidden');
                    const durEl = document.getElementById('studentLiveBannerDuration');
                    if (durEl) durEl.textContent = `${activeLive.duration || 60} ${isAr ? 'دقيقة' : 'min'}`;
                    const titleTextEl = document.getElementById('studentLiveBannerTitleText');
                    if (titleTextEl) titleTextEl.textContent = activeLive.title || (isAr ? 'حصة تفاعلية' : 'Live Class');
                    const teacherEl = document.getElementById('studentLiveBannerTeacher');
                    if (teacherEl) teacherEl.textContent = `(${activeLive.teacher || 'Teacher'})`;
                    const joinBtn = document.getElementById('studentLiveClassroomJoinBtn');
                    if (joinBtn && activeLive.join_url) joinBtn.href = activeLive.join_url;
                } else {
                    banner.classList.add('hidden');
                }
            }
        }

        // Re-render UI components
        if (calViewMode === 'weekly') {
            renderWeeklyDaysStrip();
            renderWeeklySummary();
        } else {
            renderCalendar();
        }
        renderSessionList();
    };

    function initCalendar() {
        currentWeekStart = getStartOfWeek(new Date());
        calViewMode = 'weekly';
        currentFilter = 'week';
        selectedDate = null;
        updateModeButtons();
        updateFilterPills();
        renderWeeklyDaysStrip();
        renderWeeklySummary();
        renderCalendar();
        renderSessionList();
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
    });

    // In case DOM is already ready
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initCalendar();
    }
})();
</script>
