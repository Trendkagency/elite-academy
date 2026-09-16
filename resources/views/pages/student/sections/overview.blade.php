{{-- SECTION 1: OVERVIEW & DASHBOARD SUMMARY --}}
<section id="sectionPane_overview" class="student-section-pane space-y-6 {{ $activeTab === 'overview' ? '' : 'hidden' }}">
    
    {{-- 4 Stat KPI Cards with Accent Borders & Fluid Typography --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        {{-- KPI 1: Package Remaining Credits (Student's Own Package Details) --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 border-t-4 {{ $hasActivePackage ? 'border-t-teal-500' : 'border-t-rose-500' }} shadow-sm hover:shadow-xl transition-all space-y-3 cursor-pointer"
            onclick="window.openModal ? window.openModal('studentOwnPackageModal') : document.getElementById('studentOwnPackageModal').classList.remove('hidden')">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold {{ $hasActivePackage ? 'text-teal-800 dark:text-teal-300 bg-teal-500/10 border-teal-500/20' : 'text-rose-800 dark:text-rose-300 bg-rose-500/10 border-rose-500/20' }} px-3 py-1 rounded-full border shadow-2xs">
                    {{ $isAr ? 'باقتي الحالية' : 'My Active Package' }}
                </span>
                <div class="w-10 h-10 rounded-2xl {{ $hasActivePackage ? 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }} flex items-center justify-center text-lg shadow-2xs">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
            </div>
            <div>
                <p class="font-heading font-black text-xl sm:text-2xl md:text-3xl text-slate-900 dark:text-white leading-none">
                    @if($hasActivePackage)
                        {{ $package->remaining_sessions }} <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">{{ $isAr ? 'حصة متبقية' : 'Remaining' }}</span>
                    @else
                        <span class="text-rose-600 dark:text-rose-400 text-base sm:text-lg font-bold">{{ $isAr ? 'لا توجد باقة نشطة (0 حصة)' : 'No Active Package' }}</span>
                    @endif
                </p>
                <p class="text-[11px] sm:text-xs font-mono text-slate-500 dark:text-slate-400 truncate pt-1.5">
                    @if($hasActivePackage)
                        {{ $package?->packageTemplate?->name ?: "Total: {$package->total_sessions} | Used: {$package->used_sessions}" }}
                    @else
                        <span class="text-rose-500 font-semibold">{{ $isAr ? 'يلزم الاشتراك لتفعيل الحصص' : 'Subscription required' }}</span>
                    @endif
                </p>
            </div>
            <button type="button" class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1 pt-1">
                <span>{{ $isAr ? 'عرض تفاصيل باقتي' : 'View My Package Details' }}</span>
                <span>&rarr;</span>
            </button>
        </div>

        {{-- KPI 2: Confirmed Upcoming Sessions --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-indigo-500 shadow-sm hover:shadow-xl transition-all space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-indigo-800 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-3 py-1 rounded-full border border-indigo-200/80 dark:border-indigo-800 shadow-2xs">
                    {{ __('app.portal.upcoming_sessions') }}
                </span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 flex items-center justify-center text-lg shadow-2xs">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
            <div>
                <p class="font-heading font-black text-xl sm:text-2xl md:text-3xl text-slate-900 dark:text-white leading-none">
                    {{ count($upcomingSessions) }} <span class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400">{{ $isAr ? 'حصة معتمدة' : 'Confirmed' }}</span>
                </p>
                <p class="text-[11px] sm:text-xs font-mono text-slate-500 dark:text-slate-400 truncate pt-1.5">
                    {{ count($upcomingSessions) > 0 ? ($isAr ? 'مواعيد البث المباشر المجدولة' : 'Upcoming live stream schedule') : ($isAr ? 'لا توجد حصص مجدولة حالياً' : 'No upcoming sessions') }}
                </p>
            </div>
            <button type="button" onclick="switchStudentTab('sessions')" class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 pt-1">
                <span>{{ $isAr ? 'جدول الحصص والبث' : 'Open Sessions Hub' }}</span>
                <span>&rarr;</span>
            </button>
        </div>

        {{-- KPI 3: Attendance Rate --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-emerald-500 shadow-sm hover:shadow-xl transition-all space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full border border-emerald-200/80 dark:border-emerald-800 shadow-2xs">
                    {{ __('app.portal.attendance_rate') }}
                </span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900 flex items-center justify-center text-lg shadow-2xs">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
            </div>
            <div>
                <p class="font-heading font-black text-xl sm:text-2xl md:text-3xl text-slate-900 dark:text-white leading-none">
                    @if($totalSessionCount > 0)
                        {{ $attendanceRate }}% <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $attendanceRate >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800' }}">{{ $attendanceRate >= 80 ? ($isAr ? 'ممتاز' : 'Excellent') : ($isAr ? 'متوسط' : 'Fair') }}</span>
                    @else
                        <span class="text-sm sm:text-base text-slate-400 font-bold">{{ $isAr ? 'لا توجد حصص بعد' : 'No records yet' }}</span>
                    @endif
                </p>
                <p class="text-[11px] sm:text-xs font-mono text-slate-500 dark:text-slate-400 truncate pt-1.5">
                    {{ $attendedSessions }} {{ $isAr ? 'حضور' : 'Attended' }} &bull; {{ $approvedExcuses }} {{ $isAr ? 'أعذار' : 'Excuses' }}
                </p>
            </div>
            <button type="button" onclick="switchStudentTab('exceptions')" class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 pt-1">
                <span>{{ $isAr ? 'سجل الأعذار والغياب' : 'View Excuses History' }}</span>
                <span>&rarr;</span>
            </button>
        </div>

        {{-- KPI 4: Homework Evaluation Average --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/90 dark:border-slate-800 border-t-4 border-t-amber-500 shadow-sm hover:shadow-xl transition-all space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-extrabold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-3 py-1 rounded-full border border-amber-200/80 dark:border-amber-800 shadow-2xs">
                    {{ __('app.portal.homework_rate') }}
                </span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900 flex items-center justify-center text-lg shadow-2xs">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
            </div>
            <div>
                <p class="font-heading font-black text-xl sm:text-2xl md:text-3xl text-slate-900 dark:text-white leading-none">
                    @if(!is_null($avgScore))
                        {{ $avgScore }}% <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $avgScore >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800' }}">{{ $avgScore >= 80 ? ($isAr ? 'متميز' : 'High') : ($isAr ? 'مقبول' : 'Fair') }}</span>
                    @else
                        <span class="text-sm sm:text-base text-slate-400 font-bold">{{ $isAr ? 'لا توجد درجات بعد' : 'No grades yet' }}</span>
                    @endif
                </p>
                <p class="text-[11px] sm:text-xs font-mono text-slate-500 dark:text-slate-400 truncate pt-1.5">
                    {{ count($submissions) }} {{ $isAr ? 'واجبات تم تصحيحها' : 'Evaluated Submissions' }}
                </p>
            </div>
            <button type="button" onclick="switchStudentTab('submissions')" class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 pt-1">
                <span>{{ $isAr ? 'سجل الدرجات والتسليمات' : 'View Graded Results' }}</span>
                <span>&rarr;</span>
            </button>
        </div>
    </div>

    {{-- Next Live Session Banner with Real-Time Countdown (High-Contrast Dual Theme) --}}
    @php
        $nextLiveSession = $startingSoonSessions->first();
    @endphp
    @if($nextLiveSession)
        @php
            $nextState = $nextLiveSession->evaluateState($userAuth);
            $nextStartAt = $nextLiveSession->effective_start_at;
            $isNextLive = ($nextState === \App\Enums\LiveSessionState::LIVE);
        @endphp
        <div class="rounded-3xl p-5 sm:p-6 md:p-7 {{ $isNextLive ? 'bg-gradient-to-br from-emerald-500/10 via-teal-500/5 to-white dark:from-emerald-950/80 dark:via-slate-900 dark:to-teal-950/80 border-2 border-emerald-500/50 dark:border-emerald-500/40 ring-4 ring-emerald-500/10' : 'bg-white dark:bg-slate-900 border-2 border-teal-500/30 dark:border-slate-800 shadow-md hover:shadow-lg' }} transition-all flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
            
            {{-- Background decorative ambient glow --}}
            <div class="absolute -right-12 -top-12 w-48 h-48 {{ $isNextLive ? 'bg-emerald-500/15' : 'bg-teal-500/10 dark:bg-teal-500/5' }} rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-3 relative z-10 max-w-2xl">
                {{-- Badges Row --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @if($isNextLive)
                        <span class="px-3.5 py-1 rounded-full text-xs font-mono font-black bg-rose-600 text-white animate-pulse shadow-md inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            <span>{{ $isAr ? 'بث مباشر جارٍ الآن' : 'LIVE BROADCAST NOW' }}</span>
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300 dark:border-amber-700/80 inline-flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                            <span>{{ $isAr ? 'الحصة التفاعلية القادمة اليوم' : 'Next Session Starting Soon' }}</span>
                        </span>
                    @endif

                    <span class="text-xs font-mono font-bold bg-teal-100 text-teal-950 dark:bg-teal-950/80 dark:text-teal-300 px-3 py-1 rounded-full border border-teal-300 dark:border-teal-800/80 shadow-2xs">
                        <i class="fa-solid fa-book-bookmark text-teal-700 dark:text-teal-400"></i>
                        {{ $nextLiveSession->subject?->name ?: ($isAr ? 'مادة تخصص' : 'Subject Course') }}
                    </span>
                </div>

                {{-- Session Title --}}
                <h3 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white leading-tight">
                    {{ $nextLiveSession->studentFacingTitle($isAr ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream') }}
                </h3>

                {{-- Meta Information List --}}
                <div class="text-xs font-mono text-slate-700 dark:text-slate-300 flex flex-wrap items-center gap-2.5 sm:gap-4">
                    <span class="inline-flex items-center gap-1.5 font-bold text-slate-900 dark:text-slate-200 bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-chalkboard-user text-teal-600 dark:text-teal-400 text-sm"></i>
                        <span>{{ $nextLiveSession->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</span>
                    </span>

                    <span class="inline-flex items-center gap-1.5 font-bold text-slate-900 dark:text-slate-200 bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 rounded-lg">
                        <i class="fa-solid fa-calendar-days text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        <span>{{ $nextStartAt ? $nextStartAt->format('Y-m-d h:i A') : 'Today' }}</span>
                    </span>

                    @if($nextStartAt && $nextStartAt->isFuture())
                        <span class="session-countdown-pill inline-flex items-center gap-1.5 text-xs font-mono font-black bg-teal-600 text-white dark:bg-teal-900/90 dark:text-teal-200 px-3 py-1 rounded-lg shadow-xs border border-teal-500/60"
                            data-start-time="{{ $nextStartAt->toIso8601String() }}">
                            <i class="fa-solid fa-stopwatch animate-pulse"></i>
                            <span class="countdown-text">{{ $isAr ? 'حساب الوقت...' : 'Calculating...' }}</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="relative z-10 flex items-center gap-3 shrink-0 self-start md:self-center">
                @if($isNextLive)
                    <a href="{{ route('student.meeting.show', ['id' => $nextLiveSession->id]) }}"
                        class="btn-lift px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs sm:text-sm rounded-2xl shadow-xl shadow-emerald-600/30 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                        <span>{{ $isAr ? 'انضم إلى البث المباشر الآن' : 'Join Live Stream Now' }}</span>
                        <span>&rarr;</span>
                    </a>
                @else
                    <button type="button" onclick="switchStudentTab('sessions')"
                        class="btn-lift px-5 py-3 bg-slate-900 hover:bg-teal-600 text-white dark:bg-slate-800 dark:hover:bg-teal-600 dark:text-white font-bold text-xs sm:text-sm rounded-2xl border border-slate-700/50 shadow-sm flex items-center gap-2 cursor-pointer transition-all">
                        <i class="fa-solid fa-calendar-check text-teal-400"></i>
                        <span>{{ $isAr ? 'عرض تفاصيل الحصة والجدول' : 'View Session Schedule' }}</span>
                        <span>&rarr;</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Full Width: Quick Pending Assignments Table --}}
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
                <table class="w-full text-start text-xs elite-sortable-table">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                        <tr>
                            <th class="py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'عنوان الواجب' : 'Assignment' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المقرر' : 'Course' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الموعد النهائي' : 'Deadline' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="number">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'درجة النجاح' : 'Pass Mark' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-4 text-end" data-no-sort="true">{{ $isAr ? 'الإجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
                        @foreach($availableAssignments->take(6) as $assign)
                            @php
                                $isInProgress = isset($inProgressSubmissions[$assign->id]);
                                $courseTitle = $assign->course?->title ?: ($assign->liveSession?->course?->title ?: ($isAr ? 'كورس التخصص' : 'Course Module'));
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white allow-wrap">
                                    <div class="flex items-center gap-2">
                                        @if($isInProgress)
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                        @endif
                                        <span>{{ $assign->title }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-slate-600 dark:text-slate-400 truncate max-w-[200px]">
                                    {{ $courseTitle }}
                                </td>
                                <td class="py-3.5 px-3 text-amber-700 dark:text-amber-400 font-medium whitespace-nowrap">
                                    {{ $assign->effective_due_at ? $assign->effective_due_at->format('Y-m-d H:i') : ($isAr ? '24 ساعة قبل الحصة' : '24h Pre-Session') }}
                                </td>
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700">
                                        {{ number_format($assign->passing_score ?? 70, 0) }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                                    <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}"
                                        class="btn-lift px-4 py-2 rounded-xl text-xs font-bold {{ $isInProgress ? 'bg-amber-600 hover:bg-amber-500 text-white' : 'bg-teal-600 hover:bg-teal-500 text-white' }} shadow-xs inline-flex items-center gap-1.5">
                                        <span><i class="fa-solid fa-bolt text-[11px]"></i></span>
                                        <span>{{ $isInProgress ? ($isAr ? 'استكمال الحل' : 'Resume') : ($isAr ? 'بدء الحل الآن' : 'Start Test') }}</span>
                                    </a>
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

    {{-- Full Width: Enrolled Courses & Curriculum Progress --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="font-heading font-black text-base sm:text-lg md:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm border border-teal-500/20">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    <span>{{ $isAr ? 'المقررات الدراسية النشطة ونسب الإنجاز' : 'Active Enrolled Courses & Curriculum Progress' }}</span>
                </h3>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'متابعة نسبة التقدم والدروس المكتملة في كل مقرر مسجل لديك.' : 'Monitor your completion rate and lesson modules across your courses.' }}
                </p>
            </div>
            <button type="button" onclick="switchStudentTab('courses')" class="btn-lift px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl text-xs font-bold font-mono border border-slate-200 dark:border-slate-700 cursor-pointer">
                {{ $isAr ? 'عرض كافة المقررات' : 'View All Courses' }} ({{ count($enrollments) }}) &rarr;
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($enrollmentCards->take(4) as $card)
                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 space-y-3 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-mono font-bold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 px-2.5 py-0.5 rounded-full">
                            {{ $card['subject'] }}
                        </span>
                        <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400">
                            {{ $card['progressPct'] }}%
                        </span>
                    </div>
                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                        {{ $card['course']->title }}
                    </h4>
                    <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full" style="width: {{ max(6, $card['progressPct']) }}%;"></div>
                    </div>
                    <div class="flex items-center justify-between pt-1 text-[11px] font-mono text-slate-500 dark:text-slate-400">
                        <span><i class="fa-solid fa-chalkboard-user text-teal-600 dark:text-teal-400"></i> {{ $card['teacher'] }}</span>
                        <button type="button" onclick="openEnrolledCourseModal({{ $card['course']->id }})" class="text-teal-600 dark:text-teal-400 hover:underline font-bold">
                            {{ $isAr ? 'المنهج' : 'Syllabus' }} &rarr;
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-6 text-center text-xs font-mono text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl">
                    {{ $isAr ? 'لم تسجل في أي كورس بعد.' : 'No enrolled courses found.' }}
                </div>
            @endforelse
        </div>
    </div>
</section>
