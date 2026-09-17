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
                <button type="button" onclick="openStudentPreviewGuide('sessions')"
                    class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap transition-all">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-xs animate-pulse"></i>
                    <span>{{ $isAr ? 'معاينة وشرح الحصص' : 'Preview & Guide' }}</span>
                </button>
                <span class="text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 px-3 py-1.5 rounded-xl border border-teal-200/80 dark:border-teal-800 shadow-2xs whitespace-nowrap">
                    <i class="fa-solid fa-shield-halved"></i> 30-Min Join Rule Active
                </span>
                <span id="sessionRealtimeStatus" class="inline-flex items-center gap-1.5 text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 px-2.5 py-1.5 rounded-xl border border-emerald-200/80 dark:border-emerald-800 shadow-2xs whitespace-nowrap transition-colors" title="{{ $isAr ? 'مزامنة حية ولحظية نشطة' : 'Real-time sync active' }}">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>{{ $isAr ? 'مزامنة حية' : 'Live Sync' }}</span>
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
                @include('pages.student.sections.partials.session_pane_soon')
            </div>

            {{-- 2. Pane: Upcoming Scheduled Dates --}}
            <div id="sessionPane_upcoming" class="session-sub-pane space-y-4 hidden">
                @include('pages.student.sections.partials.session_pane_upcoming')
            </div>

            {{-- 3. Pane: Ended History --}}
            <div id="sessionPane_history" class="session-sub-pane space-y-4 hidden">
                @include('pages.student.sections.partials.session_pane_history')
            </div>
        </div>
    </div>
</section>
