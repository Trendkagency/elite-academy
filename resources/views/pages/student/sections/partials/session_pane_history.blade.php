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
