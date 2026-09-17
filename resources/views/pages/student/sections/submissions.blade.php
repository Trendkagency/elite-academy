{{-- SECTION 5: GRADED SUBMISSIONS HISTORY (Full Table & Evaluation Notes) --}}
<section id="sectionPane_submissions" class="student-section-pane space-y-6 {{ $activeTab === 'submissions' ? '' : 'hidden' }}">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <h2 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fa-solid fa-square-poll-vertical"></i>
                    </span>
                    <span>{{ $isAr ? 'سجل تسليمات الواجبات والدرجات المعتمدة' : 'Assignment Submission History & Graded Evaluation' }}</span>
                </h2>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'سجل تفصيلي بكافة الواجبات المسلمة مع الدرجات المئوية، تقييمات المعلمين، وحالة النجاح والاجتياز.' : 'Complete record of all submitted and evaluated assignments across your courses.' }}
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                <button type="button" onclick="openStudentPreviewGuide('submissions')"
                    class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap transition-all">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-xs animate-pulse"></i>
                    <span>{{ $isAr ? 'معاينة وشرح الدرجات' : 'Preview & Guide' }}</span>
                </button>
                <span class="text-xs font-mono font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-2xs">
                    {{ count($submissions) }} {{ $isAr ? 'تسليمات سابقة' : 'Submitted' }}
                </span>
            </div>
        </div>

        {{-- Course Filter Chips for Submissions --}}
        @if(isset($filterCourses) && count($filterCourses) > 1)
            <div class="space-y-1.5">
                <div class="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-filter text-teal-600"></i> {{ $isAr ? 'تصفية حسب المقرر الدراسي:' : 'Filter by Course:' }}
                </div>
                <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2 flex-nowrap w-full">
                    <button type="button" onclick="filterSubmissionsByCourse('all')"
                        class="sub-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-teal-600 text-white shadow-xs cursor-pointer"
                        data-course="all">
                        {{ $isAr ? 'جميع الكورسات' : 'All Courses' }} ({{ count($submissions) }})
                    </button>
                    @foreach($filterCourses as $fc)
                        @php
                            $sCount = $submissions->filter(fn($s) => ($s->assignment?->course_id == $fc->id || $s->assignment?->liveSession?->course_id == $fc->id))->count();
                        @endphp
                        <button type="button" onclick="filterSubmissionsByCourse({{ $fc->id }})"
                            class="sub-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer border border-slate-200/80 dark:border-slate-700"
                            data-course="{{ $fc->id }}">
                            {{ $fc->title }} ({{ $sCount }})
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count($submissions) > 0)
            {{-- Submissions Comprehensive Table --}}
            <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                <table class="w-full text-start text-xs sm:text-sm elite-sortable-table" data-page-size="6">
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
                                    <span>{{ $isAr ? 'المقرر والحصة' : 'Course & Session' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="date">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'تاريخ التسليم' : 'Submitted At' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[120px]" data-sort-type="number">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الدرجة المحققة' : 'Score / Percentage' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[130px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'النتيجة' : 'Result' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-course py-3.5 px-4 text-end min-w-[180px]" data-no-sort="true">{{ $isAr ? 'ملاحظات التقييم' : 'Evaluation Note' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="submissionsTableBody">
                        @foreach($submissions as $sub)
                            @php
                                $subCourseId = $sub->assignment?->course_id ?: ($sub->assignment?->liveSession?->course_id ?: 0);
                                $subCourseTitle = $sub->assignment?->course?->title ?: ($sub->assignment?->liveSession?->course?->title ?: ($isAr ? 'كورس تخصص' : 'Subject Course'));
                                $subSessionTitle = $sub->assignment?->session?->title ?: ($sub->assignment?->liveSession?->title ?: ($isAr ? 'الجلسة التفاعلية' : 'Interactive Session'));
                                $scorePct = $sub->percentage !== null ? $sub->percentage : ($sub->grade !== null ? $sub->grade : null);
                            @endphp
                            <tr class="submission-table-row hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors"
                                data-course-id="{{ $subCourseId }}">
                                
                                {{-- Assignment Title --}}
                                <td class="col-title py-3.5 px-4 font-bold text-slate-900 dark:text-white leading-relaxed min-w-[260px] max-w-[380px]">
                                    <span class="break-words font-semibold text-slate-900 dark:text-slate-100">{{ $sub->assignment?->title ?: ($isAr ? 'واجب الجلسة التفاعلية' : 'Session MSQ Assignment') }}</span>
                                </td>

                                {{-- Course & Session Context --}}
                                <td class="col-course py-3.5 px-3 min-w-[200px] max-w-[280px]">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-teal-700 dark:text-teal-400 block line-clamp-2 leading-snug">{{ $subCourseTitle }}</span>
                                        <p class="text-[11px] text-slate-500">{{ $subSessionTitle }}</p>
                                    </div>
                                </td>

                                {{-- Submitted Date --}}
                                <td class="col-date py-3.5 px-3 whitespace-nowrap text-slate-600 dark:text-slate-400 font-mono text-xs min-w-[140px]">
                                    {{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Completed' }}
                                </td>

                                {{-- Score Percentage --}}
                                <td class="col-badge py-3.5 px-3 whitespace-nowrap min-w-[120px]">
                                    <span class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 inline-block">
                                        {{ $scorePct !== null ? number_format($scorePct, 1) . '%' : 'Evaluated' }}
                                    </span>
                                </td>

                                {{-- Pass/Fail Badge --}}
                                <td class="col-badge py-3.5 px-3 whitespace-nowrap min-w-[130px]">
                                    @if($sub->isPassed())
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>{{ $isAr ? 'ناجح (اجتاز)' : 'PASSED' }}</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                            <span>{{ $isAr ? 'لم يجتز' : 'FAILED' }}</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Evaluation Note --}}
                                <td class="col-course py-3.5 px-4 text-end text-xs text-slate-600 dark:text-slate-400 leading-normal min-w-[180px] max-w-[300px]">
                                    <span class="line-clamp-2">{{ $sub->evaluation_notes ?: ($sub->teacher_notes ?: ($isAr ? 'تم التقييم التلقائي بنجاح.' : 'Graded.')) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-xs font-mono text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                {{ $isAr ? 'لا توجد تسليمات واجبات سابقة مسجلة حتى الآن.' : 'No submission history recorded yet.' }}
            </div>
        @endif
    </div>
</section>
