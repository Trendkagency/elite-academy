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
