{{-- SECTION 4: ASSIGNMENTS & MSQ TESTS (Full Table & Instant Solver) --}}
@php
    $displayAssignments = isset($allStudentAssignments) && count($allStudentAssignments) > 0 ? $allStudentAssignments : $availableAssignments;
    $completedCount = isset($completedSubmissions) ? count($completedSubmissions) : 0;
    $inProgressCount = isset($inProgressSubmissions) ? count($inProgressSubmissions) : 0;
    $pendingCount = count($availableAssignments);
    $totalAssignmentsCount = count($displayAssignments);
@endphp

<section id="sectionPane_assignments" class="student-section-pane space-y-6 {{ $activeTab === 'assignments' ? '' : 'hidden' }}">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 md:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Header & Metrics --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="space-y-1">
                <h2 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shadow-2xs shrink-0">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <span>{{ $isAr ? 'قسم الواجبات والاختبارات التفاعلية (MSQ)' : 'Assignments & MSQ Quizzes Department' }}</span>
                </h2>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                    {{ $isAr ? 'حل الواجبات قبل الموعد النهائي لتأكيد حضور وفهم الدرس، ومراجعة سجل الدرجات والتقييمات المعتمدة.' : 'Complete homework before session deadline to fulfill attendance prerequisites and inspect evaluated scores.' }}
                </p>
            </div>

            {{-- Metric Badges --}}
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="openStudentPreviewGuide('assignments')"
                    class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-200 text-slate-950 rounded-2xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap transition-all">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-950 text-xs animate-pulse"></i>
                    <span>{{ $isAr ? 'معاينة وشرح الواجبات' : 'Preview & Guide' }}</span>
                </button>
                <span class="text-xs font-mono font-extrabold bg-teal-100 dark:bg-teal-950/80 text-teal-900 dark:text-teal-200 px-3.5 py-1.5 rounded-2xl border border-teal-200 dark:border-teal-800 shadow-2xs whitespace-nowrap">
                    <i class="fa-solid fa-layer-group text-teal-600"></i> {{ $totalAssignmentsCount }} {{ $isAr ? 'إجمالي الواجبات' : 'Total' }}
                </span>
                @if($pendingCount > 0)
                    <span class="text-xs font-mono font-extrabold bg-amber-100 dark:bg-amber-950/80 text-amber-900 dark:text-amber-200 px-3.5 py-1.5 rounded-2xl border border-amber-300 dark:border-amber-800 shadow-2xs whitespace-nowrap">
                        <i class="fa-solid fa-clock text-amber-600 animate-pulse"></i> {{ $pendingCount }} {{ $isAr ? 'معلقة للحل' : 'Pending' }}
                    </span>
                @endif
                @if($completedCount > 0)
                    <span class="text-xs font-mono font-extrabold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-900 dark:text-emerald-200 px-3.5 py-1.5 rounded-2xl border border-emerald-300 dark:border-emerald-800 shadow-2xs whitespace-nowrap">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ $completedCount }} {{ $isAr ? 'تم تقييمها' : 'Completed' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Status Filter Sub-tabs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700">
            <button type="button" onclick="filterAssignmentsByStatus('all')" id="assignStatusTab_all"
                class="assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-extrabold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-teal-600 text-white shadow-md shadow-teal-600/30 border border-teal-500 whitespace-nowrap">
                <span>{{ $isAr ? 'الكل' : 'All' }}</span>
                <span class="status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 text-white">
                    {{ $totalAssignmentsCount }}
                </span>
            </button>
            <button type="button" onclick="filterAssignmentsByStatus('pending')" id="assignStatusTab_pending"
                class="assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 whitespace-nowrap">
                <span>{{ $isAr ? 'متاحة للحل' : 'Pending' }}</span>
                <span class="status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    {{ $pendingCount }}
                </span>
            </button>
            <button type="button" onclick="filterAssignmentsByStatus('in_progress')" id="assignStatusTab_in_progress"
                class="assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 whitespace-nowrap">
                <span>{{ $isAr ? 'قيد الحل' : 'In Progress' }}</span>
                <span class="status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    {{ $inProgressCount }}
                </span>
            </button>
            <button type="button" onclick="filterAssignmentsByStatus('completed')" id="assignStatusTab_completed"
                class="assign-status-tab-btn px-3 py-2 rounded-xl text-xs font-bold font-mono transition-all flex items-center justify-center gap-1.5 cursor-pointer bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/80 border border-slate-200 dark:border-slate-700 whitespace-nowrap">
                <span>{{ $isAr ? 'تم التسليم والتقييم' : 'Completed' }}</span>
                <span class="status-badge px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                    {{ $completedCount }}
                </span>
            </button>
        </div>

        {{-- Horizontal Course Filter Chips (No Vertical Wrapping) --}}
        @if(isset($filterCourses) && count($filterCourses) > 1)
            <div class="space-y-1.5">
                <div class="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-filter text-teal-600"></i> {{ $isAr ? 'تصفية حسب المقرر الدراسي:' : 'Filter by Course:' }}
                </div>
                <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-2.5 flex-nowrap w-full">
                    <button type="button" onclick="filterAssignmentsByCourse('all')"
                        class="assign-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-teal-600 text-white shadow-xs cursor-pointer"
                        data-course="all">
                        {{ $isAr ? 'جميع الكورسات' : 'All Courses' }} ({{ $totalAssignmentsCount }})
                    </button>
                    @foreach($filterCourses as $fc)
                        @php
                            $fcCount = $displayAssignments->filter(fn($a) => ($a->course_id == $fc->id || $a->liveSession?->course_id == $fc->id))->count();
                        @endphp
                        <button type="button" onclick="filterAssignmentsByCourse({{ $fc->id }})"
                            class="assign-filter-btn px-4 py-2 rounded-2xl text-xs font-bold font-mono transition-all whitespace-nowrap shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 cursor-pointer border border-slate-200/80 dark:border-slate-700"
                            data-course="{{ $fc->id }}">
                            {{ $fc->title }} ({{ $fcCount }})
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if($totalAssignmentsCount > 0)
            {{-- Assignments Comprehensive Table --}}
            <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                <table class="w-full text-start text-xs sm:text-sm elite-sortable-table" data-page-size="8" id="portalAssignmentsTable">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                        <tr>
                            <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[280px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'اسم الواجب والتفاصيل' : 'Assignment & Topic' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[200px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المقرر والمادة' : 'Course & Module' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="date">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الموعد النهائي' : 'Deadline' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[130px]" data-sort-type="number">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المدة والنجاح' : 'Duration & Pass' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-badge py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'حالة التسليم والدرجة' : 'Status & Score' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-action py-3.5 px-4 text-end min-w-[150px]" data-no-sort="true">{{ $isAr ? 'الإجراء والحل' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="assignmentsTableBody">
                        @foreach($displayAssignments as $assign)
                            @php
                                $sub = isset($submissionsMap[$assign->id]) ? $submissionsMap[$assign->id] : (isset($submissions) ? $submissions->where('assignment_id', $assign->id)->first() : null);
                                $isCompleted = !is_null($sub) && in_array($sub->status, [\App\Enums\SubmissionStatus::COMPLETED, \App\Enums\SubmissionStatus::SUBMITTED, \App\Enums\SubmissionStatus::REVIEWED]);
                                $isInProgress = isset($inProgressSubmissions[$assign->id]) || (!is_null($sub) && $sub->status === \App\Enums\SubmissionStatus::IN_PROGRESS && is_null($sub->submitted_at));
                                
                                $statusCategory = 'pending';
                                if ($isCompleted) $statusCategory = 'completed';
                                elseif ($isInProgress) $statusCategory = 'in_progress';

                                $assignCourseId = $assign->course_id ?: ($assign->liveSession?->course_id ?: 0);
                                $courseTitle = $assign->course?->title ?: ($assign->liveSession?->course?->title ?: ($isAr ? 'كورس تخصص' : 'Course'));
                                $subjectTitle = $assign->course?->subject?->name ?: ($assign->liveSession?->subject?->name ?: 'STEM');
                                $teacherName = $assign->course?->teacher?->user?->name ?: ($assign->liveSession?->teacherProfile?->user?->name ?: 'Dr. Instructor');
                                $qCount = $assign->questions ? $assign->questions->count() : 0;
                                $scorePct = $sub ? ($sub->percentage !== null ? $sub->percentage : $sub->grade) : null;
                                $isPassed = $sub ? $sub->isPassed() : false;
                            @endphp
                            <tr class="available-assign-row hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors {{ $isInProgress ? 'bg-amber-500/[0.04] dark:bg-amber-400/[0.05] border-s-4 border-s-amber-500' : ($isCompleted ? 'bg-slate-500/[0.02] dark:bg-slate-400/[0.02]' : '') }}"
                                data-course-id="{{ $assignCourseId }}"
                                data-status-category="{{ $statusCategory }}">
                                
                                {{-- Title & Details --}}
                                <td class="col-title py-3.5 px-4 min-w-[280px] max-w-[400px]">
                                    <div class="space-y-1">
                                        <div class="flex items-start gap-2 flex-wrap">
                                            @if($isCompleted)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/25 shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-check"></i> {{ $isAr ? 'تم التسليم' : 'Submitted' }}
                                                </span>
                                            @elseif($isInProgress)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/25 shrink-0 mt-0.5 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping inline-block"></span>
                                                    {{ $isAr ? 'قيد الحل' : 'In Progress' }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20 shrink-0 mt-0.5">
                                                    MSQ
                                                </span>
                                            @endif
                                            <h4 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm leading-snug break-words flex-1">{{ $assign->title }}</h4>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-normal line-clamp-2">
                                            {{ $assign->description ?: ($isAr ? 'واجب تقييمي تفاعلي لغلق فجوات الدرس والتأكد من الفهم.' : 'Interactive MSQ assignment to verify lesson understanding.') }}
                                        </p>
                                        @if($qCount > 0)
                                            <span class="inline-block text-[10px] text-teal-700 dark:text-teal-400 font-bold">
                                                <i class="fa-solid fa-list-check"></i> {{ $qCount }} {{ $isAr ? 'أسئلة اختيار من متعدد' : 'Questions' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Course & Module --}}
                                <td class="col-course py-3.5 px-3 min-w-[200px] max-w-[300px]">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-900 dark:text-slate-100 text-xs line-clamp-2 leading-snug">{{ $courseTitle }}</span>
                                        <p class="text-[11px] text-teal-700 dark:text-teal-400 font-medium flex items-center gap-1">
                                            <span class="px-1.5 py-0.2 rounded bg-teal-500/10 border border-teal-500/20 text-[10px]">{{ $subjectTitle }}</span>
                                            <span>• {{ $teacherName }}</span>
                                        </p>
                                    </div>
                                </td>

                                {{-- Deadline --}}
                                <td class="col-date py-3.5 px-3 whitespace-nowrap min-w-[140px]">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-medium bg-amber-500/10 text-amber-800 dark:text-amber-300 border border-amber-500/20 flex items-center gap-1 w-fit">
                                        <i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                                        <span>{{ $assign->effective_due_at ? $assign->effective_due_at->format('Y-m-d H:i') : ($isAr ? '24h قبل الحصة' : '24h Pre-Session') }}</span>
                                    </span>
                                </td>

                                {{-- Duration & Pass Mark --}}
                                <td class="col-badge py-3.5 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300 min-w-[130px]">
                                    <div class="space-y-0.5 text-xs">
                                        <div><i class="fa-solid fa-stopwatch text-teal-600 dark:text-teal-400"></i> <strong class="text-slate-900 dark:text-slate-100 font-mono">{{ $assign->duration_minutes ?: 30 }}</strong> {{ $isAr ? 'دقيقة' : 'mins' }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $isAr ? 'درجة النجاح:' : 'Pass:' }} <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ number_format($assign->passing_score ?? 70, 0) }}%</strong></div>
                                    </div>
                                </td>

                                {{-- Status & Achieved Score --}}
                                <td class="col-badge py-3.5 px-3 whitespace-nowrap min-w-[140px]">
                                    @if($isCompleted)
                                        <div class="space-y-0.5">
                                            @if($isPassed)
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold font-mono bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-check"></i> {{ $scorePct !== null ? number_format($scorePct, 0) . '%' : 'Passed' }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold font-mono bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30 inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-xmark"></i> {{ $scorePct !== null ? number_format($scorePct, 0) . '%' : 'Failed' }}
                                                </span>
                                            @endif
                                            @if($sub->submitted_at)
                                                <p class="text-[10px] text-slate-400 font-mono font-normal">{{ $sub->submitted_at->format('Y-m-d') }}</p>
                                            @endif
                                        </div>
                                    @elseif($isInProgress)
                                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                            {{ $isAr ? 'قيد الحل' : 'In Progress' }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/25">
                                            {{ $isAr ? 'متاح للحل الآن' : 'Available' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Action Buttons --}}
                                <td class="col-action py-3.5 px-4 text-end whitespace-nowrap min-w-[150px]">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($isCompleted)
                                            <button type="button" onclick="switchStudentTab('submissions')"
                                                class="btn-lift px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl font-bold text-xs border border-slate-200 dark:border-slate-700 cursor-pointer inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-square-poll-vertical text-teal-600 dark:text-teal-400"></i>
                                                <span>{{ $isAr ? 'عرض النتيجة' : 'View Grade' }}</span>
                                            </button>
                                        @else
                                            <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}"
                                                class="btn-lift px-3.5 py-1.5 rounded-xl font-bold text-xs shadow-xs inline-flex items-center gap-1.5 {{ $isInProgress ? 'bg-amber-600 hover:bg-amber-500 text-white' : 'bg-teal-600 hover:bg-teal-500 text-white' }}">
                                                <span><i class="fa-solid fa-bolt"></i></span>
                                                <span>{{ $isInProgress ? ($isAr ? 'استكمال' : 'Resume') : ($isAr ? 'بدء الحل' : 'Take Test') }}</span>
                                            </a>
                                        @endif
                                        <button type="button" onclick="openMsqAssignmentModal({{ $assign->id }})"
                                            class="btn-lift px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs border border-slate-200 dark:border-slate-700 cursor-pointer"
                                            title="{{ $isAr ? 'معاينة سريعة' : 'Quick Preview' }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-10 bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-slate-200 dark:border-slate-700 text-center space-y-2">
                <div class="text-4xl text-teal-600"><i class="fa-solid fa-pen-to-square"></i></div>
                <h4 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white">
                    {{ $isAr ? 'لا توجد واجبات دراسية منشورة لمقرراتك حالياً' : 'No Published Assignments Available' }}
                </h4>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    {{ $isAr ? 'سيتم إدراج الواجبات والاختبارات التفاعلية تلقائياً فور نشرها من قبل المعلمين المشرفين.' : 'Homework quizzes will appear here automatically once scheduled by your subject instructors.' }}
                </p>
            </div>
        @endif
    </div>
</section>
