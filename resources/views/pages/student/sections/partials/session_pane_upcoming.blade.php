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
