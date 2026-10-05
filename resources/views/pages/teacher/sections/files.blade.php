{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- TAB 4: EDUCATIONAL FILES & HOMEWORK HUB (FILE UPLOAD SYSTEM)             --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div id="teacher-tab-assignments"
    class="teacher-tab-content {{ $activeTabKey === 'assignments' ? '' : 'hidden' }} space-y-6">

    {{-- Master Files Header & Quick Action --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-folder-open"></i>
                    </span>
                    <div>
                        <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                            {{ $isAr ? 'الملفات والمذكرات والواجبات التعليمية' : __('Educational Files & Materials') }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 dark:text-slate-400">
                            {{ $isAr ? 'شارك مذكرات PDF والصور التوضيحية مع طلابك، وتصفح وحمل الواجبات وحلول الطلاب.' : __('Distribute PDF notes and diagrams, and review/download homework submissions.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto shrink-0">
                <button type="button" onclick="openCreateAssignmentModal()"
                    class="btn-lift px-4 sm:px-5 py-2.5 bg-gradient-to-r from-amber-500 via-amber-600 to-teal-600 hover:from-amber-600 hover:to-teal-700 text-white text-xs font-black rounded-2xl shadow-lg shadow-amber-500/20 flex items-center gap-2 cursor-pointer shrink-0">
                    <i class="fa-solid fa-file-circle-plus text-sm"></i>
                    <span>{{ $isAr ? 'تسجيل ونشر واجب جديد' : __('Publish Homework / Quiz') }}</span>
                </button>
                <button type="button" onclick="openTeacherFileUploadModal()"
                    class="btn-lift px-4 sm:px-5 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-black rounded-2xl shadow-lg shadow-teal-600/25 flex items-center gap-2 cursor-pointer shrink-0">
                    <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                    <span>{{ $isAr ? 'رفع ملف أو مذكرة' : __('Upload Material') }}</span>
                </button>
            </div>
        </div>

        {{-- Quick Filter Pills & Course Selector Bar --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar pb-1 text-xs font-bold" id="teacherFileFilterPills">
                <button type="button" onclick="filterTeacherFiles('all')" data-filter="all"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-teal-600 text-white shadow-xs transition-all cursor-pointer">
                    {{ $isAr ? 'جميع الملفات والواجبات' : __('All Items') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-white/20 text-[10px] font-mono">{{ $uploadedFiles->count() }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('material')" data-filter="material"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-book-bookmark text-teal-600 me-1"></i>
                    {{ $isAr ? 'المذكرات والمواد الدراسية' : __('Study Materials') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">{{ $materialsCount ?? $myFilesCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('homework')" data-filter="homework"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-clipboard-question text-amber-500 me-1"></i>
                    {{ $isAr ? 'الواجبات والتكليفات' : __('Assigned Homework') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 text-[10px] font-mono">{{ $homeworkFilesCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('student')" data-filter="student"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-file-arrow-up text-indigo-500 me-1"></i>
                    {{ $isAr ? 'حلول وتسليمات الطلاب' : __('Student Submissions') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-950/80 text-indigo-800 dark:text-indigo-300 text-[10px] font-mono">{{ $studentFilesCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('pdf')" data-filter="pdf"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-rose-500 me-1"></i>
                    PDF
                </button>
                <button type="button" onclick="filterTeacherFiles('image')" data-filter="image"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-file-image text-blue-500 me-1"></i>
                    {{ $isAr ? 'صور' : __('Images') }}
                </button>
            </div>

            {{-- Search Input --}}
            <div class="relative min-w-[220px]">
                <input type="text" id="teacherFileSearchInput" oninput="searchTeacherFiles(this.value)"
                    placeholder="{{ $isAr ? 'بحث بالعنوان أو اسم الملف...' : __('Search by title or filename...') }}"
                    class="w-full bg-[#FAFAF9] dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-teal-500 pe-8">
                <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>

        {{-- Files Grid Container --}}
        @if ($uploadedFiles->count() > 0)
            <div id="teacherFilesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($uploadedFiles as $file)
                    @php
                        $isPdf = $file->is_pdf;
                        $isStudentUpload = $file->uploader?->isStudent() || $file->category === 'submission';
                        $isHomework = $file->is_homework || $file->category === 'homework';
                        $uploaderName = $file->uploader?->name ?: __('User');
                    @endphp
                    <div class="teacher-file-card bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-800 hover:border-teal-400 dark:hover:border-teal-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 group"
                        data-role="{{ $isStudentUpload ? 'student' : 'teacher' }}"
                        data-category="{{ $file->category ?: ($isStudentUpload ? 'submission' : 'material') }}"
                        data-is-homework="{{ $isHomework ? 'true' : 'false' }}"
                        data-type="{{ $file->file_type }}"
                        data-title="{{ strtolower($file->title . ' ' . $file->original_name . ' ' . ($file->course?->title ?? '')) }}"
                        id="teacherFileItem_{{ $file->id }}">

                        {{-- Card Top: Icon, Badges & Meta --}}
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-xs {{ $isPdf ? 'bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-500/20' : 'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 border border-blue-500/20' }}">
                                        <i class="fa-solid {{ $isPdf ? 'fa-file-pdf text-xl' : 'fa-file-image text-xl' }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-sm text-slate-900 dark:text-white truncate leading-snug group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors"
                                            title="{{ $file->title }}">
                                            {{ $file->title }}
                                        </h3>
                                        <p class="text-[11px] font-mono text-slate-400 truncate max-w-[180px]" title="{{ $file->original_name }}">
                                            {{ $file->original_name }}
                                        </p>
                                    </div>
                                </div>

                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-extrabold uppercase shrink-0 {{ $isPdf ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                                    {{ strtoupper($file->file_type) }}
                                </span>
                            </div>

                            @if ($file->description)
                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                    {{ $file->description }}
                                </p>
                            @endif

                            {{-- Association Badges --}}
                            <div class="flex items-center gap-1.5 flex-wrap text-[10px] font-mono">
                                {{-- Purpose Badge --}}
                                @if ($isHomework)
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60 font-bold">
                                        <i class="fa-solid fa-clipboard-question text-[9px] me-1 text-amber-600"></i>{{ $isAr ? 'واجب وتكليف' : __('Homework') }}
                                    </span>
                                    @if ($file->due_at)
                                        <span class="px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60">
                                            <i class="fa-solid fa-clock text-[9px] me-1 text-rose-500"></i>{{ $file->due_at->format('M d, H:i') }}
                                        </span>
                                    @endif
                                @elseif ($isStudentUpload)
                                    <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60 font-bold">
                                        <i class="fa-solid fa-file-arrow-up text-[9px] me-1 text-indigo-500"></i>{{ $isAr ? 'حل طالب' : __('Submission') }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-lg bg-teal-50 dark:bg-teal-950/70 text-teal-800 dark:text-teal-300 border border-teal-200 dark:border-teal-800/60 font-bold">
                                        <i class="fa-solid fa-book-bookmark text-[9px] me-1 text-teal-600"></i>{{ $isAr ? 'مذكرة دراسية' : __('Material') }}
                                    </span>
                                @endif

                                @if ($file->course)
                                    <span class="px-2 py-0.5 rounded-lg bg-teal-50 dark:bg-teal-950/70 text-teal-800 dark:text-teal-300 border border-teal-200/70 dark:border-teal-800/60 truncate max-w-[170px]">
                                        <i class="fa-solid fa-book-open text-[9px] me-1"></i>{{ $file->course->title }}
                                    </span>
                                @endif

                                @if ($file->studentUser)
                                    <span class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/70 text-indigo-800 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-800/60 truncate max-w-[160px]">
                                        <i class="fa-solid fa-user-graduate text-[9px] me-1"></i>{{ $file->studentUser->name }}
                                    </span>
                                @elseif($isStudentUpload)
                                    <span class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 border border-amber-200/70 dark:border-amber-800/60">
                                        <i class="fa-solid fa-upload text-[9px] me-1"></i>{{ $uploaderName }}
                                    </span>
                                @endif

                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 font-bold">
                                    {{ $file->formatted_size }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Footer: Details & Actions --}}
                        <div class="pt-3 border-t border-slate-200/70 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <div class="text-[10px] font-mono text-slate-400">
                                <span>{{ $file->created_at->format('M d, H:i') }}</span>
                                @if ($file->downloads_count > 0)
                                    <span class="ms-1.5 text-slate-500 font-bold">
                                        &bull; <i class="fa-solid fa-download text-[9px]"></i> {{ $file->downloads_count }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5">
                                {{-- Direct Download Button --}}
                                <a href="{{ route('portal.files.download', $file->id) }}"
                                    class="btn-lift px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-arrow-down-to-line text-[11px]"></i>
                                    <span>{{ $isAr ? 'تحميل' : __('Download') }}</span>
                                </a>

                                {{-- Inline Preview Button --}}
                                <a href="{{ route('portal.files.preview', $file->id) }}" target="_blank" rel="noopener"
                                    class="btn-lift px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl shadow-xs transition-all"
                                    title="{{ __('Open Preview') }}">
                                    <i class="fa-solid fa-eye text-teal-600 dark:text-teal-400"></i>
                                </a>

                                {{-- Delete Button (for uploader or admin) --}}
                                @if (auth()->id() === $file->user_id || auth()->user()->isAdmin())
                                    <button type="button" onclick="deleteTeacherUploadedFile({{ $file->id }}, '{{ addslashes($file->title) }}')"
                                        class="btn-lift p-1.5 w-7 h-7 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/60 flex items-center justify-center transition-all cursor-pointer"
                                        title="{{ __('Delete File') }}">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-[#FAFAF9] dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 space-y-3">
                <div class="w-16 h-16 rounded-3xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-folder-plus"></i>
                </div>
                <h4 class="font-heading font-black text-lg text-slate-900 dark:text-white">
                    {{ $isAr ? 'لا توجد ملفات أو مذكرات مرفوعة حتى الآن' : __('No files uploaded yet') }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    {{ $isAr ? 'قم برفع أول مذكرة أو ورقة عمل للطلاب بصيغة PDF أو كصورة ليستفيد منها الطلاب مباشرة.' : __('Upload your first PDF worksheet or diagram to share with your cohorts.') }}
                </p>
                <button type="button" onclick="openTeacherFileUploadModal()"
                    class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-2xl shadow-md cursor-pointer inline-flex items-center gap-2 mt-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ $isAr ? 'رفع ملف الآن' : __('Upload File Now') }}</span>
                </button>
            </div>
        @endif

        {{-- Section 2: Active Homework & Quizzes Registry --}}
        @if (isset($assignments) && $assignments->count() > 0)
            <div class="mt-8 pt-6 border-t border-slate-200/90 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shrink-0">
                            <i class="fa-solid fa-list-check"></i>
                        </span>
                        <div>
                            <h3 class="font-heading text-base font-black text-slate-900 dark:text-white">
                                {{ $isAr ? 'سجل الواجبات والاختبارات التفاعلية المنشورة' : __('Assigned Homework & Quizzes Registry') }}
                            </h3>
                            <p class="text-[11px] font-mono text-slate-400">
                                {{ $isAr ? 'متابعة تسليمات الطلاب لكل واجب، وتحميل أوراق العمل، ومراجعة الدرجات.' : __('Track student submissions, inspect answers, and assign final grades.') }}
                            </p>
                        </div>
                    </div>

                    <button type="button" onclick="openCreateAssignmentModal()"
                        class="btn-lift px-3.5 py-1.5 bg-gradient-to-r from-amber-500 to-teal-600 hover:from-amber-600 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>{{ $isAr ? 'نشر وتكليف واجب جديد' : __('Publish New Homework') }}</span>
                    </button>
                </div>

                <div class="overflow-x-auto custom-scrollbar rounded-2xl border border-slate-200 dark:border-slate-700/80">
                    <table class="w-full text-start text-xs">
                        <thead class="bg-slate-100/80 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-3 text-start">{{ $isAr ? 'عنوان الواجب' : __('Title') }}</th>
                                <th class="p-3 text-start">{{ $isAr ? 'الكورس / الحصة' : __('Course / Session') }}</th>
                                <th class="p-3 text-start">{{ $isAr ? 'موعد التسليم' : __('Deadline') }}</th>
                                <th class="p-3 text-center">{{ $isAr ? 'تسليمات الطلاب' : __('Submissions') }}</th>
                                <th class="p-3 text-center">{{ $isAr ? 'ورقة العمل المرفقة' : __('Attached Worksheet') }}</th>
                                <th class="p-3 text-end">{{ $isAr ? 'الإجراءات' : __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                            @foreach ($assignments as $assign)
                                @php
                                    $subCount = $assign->submissions->count();
                                    $hasFile = !empty($assign->attachment_file_path);
                                @endphp
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center text-[10px]">
                                                <i class="fa-solid fa-file-pen"></i>
                                            </span>
                                            <span>{{ $assign->title }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                                        {{ $assign->course?->title ?: __('General') }}
                                        @if ($assign->liveSession)
                                            <span class="block text-[10px] text-slate-400">
                                                <i class="fa-solid fa-video text-[9px] me-1"></i>{{ $assign->liveSession->title ?: __('Live Session') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                                        @if ($assign->due_at)
                                            <span class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-bold">
                                                <i class="fa-solid fa-clock text-[10px]"></i>
                                                {{ $assign->due_at->format('Y-m-d H:i') }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-extrabold {{ $subCount > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                            {{ $subCount }} {{ $isAr ? 'طالب' : 'students' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        @if ($hasFile)
                                            <a href="{{ Storage::disk('public')->url($assign->attachment_file_path) }}" target="_blank"
                                                class="btn-lift inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 text-[11px] font-bold border border-teal-200 dark:border-teal-800 hover:bg-teal-100 transition-colors">
                                                <i class="fa-solid fa-paperclip text-[10px]"></i>
                                                <span>{{ $isAr ? 'تحميل الورقة' : __('View File') }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 font-mono text-[11px]">—</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-end">
                                        <button type="button" onclick="openAssignmentDetailsModal({{ $assign->id }})"
                                            class="btn-lift px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-2xs inline-flex items-center gap-1.5 transition-all cursor-pointer">
                                            <i class="fa-solid fa-eye text-[11px]"></i>
                                            <span>{{ $isAr ? 'مراجعة التسليمات' : __('Submissions') }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TEACHER EDUCATIONAL FILE UPLOAD                                       --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="teacherFileUploadModal"
    class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div
        class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-lg w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">
        
        {{-- Header --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900">
            <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 dark:text-white flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </span>
                <span>{{ $isAr ? 'رفع ملف أو مذكرة تعليمية' : __('Upload Educational File') }}</span>
            </h3>
            <button type="button" onclick="closeTeacherFileUploadModal()" aria-label="{{ __('Close') }}"
                class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-all cursor-pointer active:scale-95">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="teacherFileUploadForm" action="{{ route('ajax.teacher.files.upload') }}" method="POST" enctype="multipart/form-data"
            class="flex-1 flex flex-col overflow-hidden m-0">
            @csrf

            <div class="p-4 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
                {{-- File Drop Area --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'الملف (PDF أو صورة)' : __('File (PDF or Image)') }} *
                    </label>
                    <div id="teacherFileDropZone"
                        class="border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-teal-500 dark:hover:border-teal-400 rounded-2xl p-5 text-center cursor-pointer transition-colors bg-slate-50/50 dark:bg-slate-800/40 relative group">
                        <input type="file" id="teacherFileInput" name="file" required accept=".pdf,image/*,.png,.jpg,.jpeg,.webp"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            onchange="handleTeacherFileSelection(this)">
                        <div class="space-y-2 pointer-events-none" id="teacherFileDropContent">
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mx-auto text-xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-file-arrow-up"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200" id="teacherFileNamePreview">
                                    {{ $isAr ? 'اضغط لاختيار ملف أو اسحبه إلى هنا' : __('Click to browse or drag file here') }}
                                </p>
                                <p class="text-[11px] font-mono text-slate-400 mt-0.5" id="teacherFileSizePreview">
                                    {{ $isAr ? 'صيغ مدعومة: PDF, PNG, JPG, WEBP (حتى 25MB)' : __('Supported: PDF, PNG, JPG, WEBP (Up to 25MB)') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File Purpose / Category Selector --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'نوع الملف / الغرض' : __('File Category / Purpose') }} *
                    </label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-teal-500/40 bg-teal-50/30 dark:bg-teal-950/20 cursor-pointer hover:border-teal-500 transition-colors">
                            <input type="radio" name="category" value="material" checked onchange="toggleTeacherHomeworkFields(this.value)" class="text-teal-600 focus:ring-teal-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-book-bookmark text-teal-600 text-xs"></i>
                                    {{ $isAr ? 'مذكرة / مادة دراسية' : __('Study Material') }}
                                </span>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $isAr ? 'محتوى للشرح والمذاكرة' : __('Notes & reading') }}</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-amber-500/40 bg-amber-50/30 dark:bg-amber-950/20 cursor-pointer hover:border-amber-500 transition-colors">
                            <input type="radio" name="category" value="homework" onchange="toggleTeacherHomeworkFields(this.value)" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-question text-amber-500 text-xs"></i>
                                    {{ $isAr ? 'واجب منزلي وتكليف' : __('Homework / Task') }}
                                </span>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $isAr ? 'تكليف بموعد تسليم' : __('Assignment with deadline') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Homework Deadline Field (Shows when category is homework) --}}
                <div id="teacherHomeworkDueAtWrapper" class="hidden">
                    <label class="block text-xs font-mono font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day text-xs"></i>
                        <span>{{ $isAr ? 'آخر موعد لتسليم الواجب (تاريخ ووقت)' : __('Submission Deadline (Due Date & Time)') }} *</span>
                    </label>
                    <input type="datetime-local" id="teacherFileDueAtInput" name="due_at"
                        class="w-full bg-[#FAFAF9] dark:bg-slate-800 border border-amber-400/80 dark:border-amber-500/60 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <p class="text-[10px] font-mono text-amber-600/80 dark:text-amber-400/80 mt-1">
                        <i class="fa-solid fa-bell me-1"></i>
                        {{ $isAr ? 'سيتم تسجيل الواجب في بوابة الطالب فوراً وتنبيهه عبر الإشعارات لحل الواجب وتسليمه.' : __('Will be registered in student assignments with notifications.') }}
                    </p>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'عنوان الملف / المذكرة' : __('File Title') }} *
                    </label>
                    <input type="text" id="teacherFileTitleInput" name="title" required
                        placeholder="{{ $isAr ? 'مثال: مذكرة مراجعة الباب الأول - فيزياء' : __('e.g., Chapter 1 Summary - Physics') }}"
                        class="w-full bg-[#FAFAF9] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-teal-500">
                </div>

                {{-- Target Course (Optional) --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'ربط بكورس أو مادة (اختياري)' : __('Assign to Course (Optional)') }}
                    </label>
                    <select id="teacherFileCourseSelect" name="course_id"
                        class="w-full bg-[#FAFAF9] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-medium text-slate-900 dark:text-white focus:outline-none focus:border-teal-500">
                        <option value="">{{ $isAr ? 'عام (بدون ربط بكورس محدد)' : __('General (No specific course)') }}</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Target Specific Student (Optional) --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'تخصيص لطالب معين (اختياري)' : __('Target Specific Student (Optional)') }}
                    </label>
                    <select id="teacherFileStudentSelect" name="student_user_id"
                        class="w-full bg-[#FAFAF9] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-xs font-medium text-slate-900 dark:text-white focus:outline-none focus:border-teal-500">
                        <option value="">{{ $isAr ? 'متاح لجميع الطلاب المسجلين' : __('Available to all enrolled students') }}</option>
                        @foreach ($assignedStudents as $st)
                            <option value="{{ $st->user_id }}">{{ $st->user?->name }} ({{ $st->gradeLevel?->name ?: ($isAr ? 'طالب' : 'Student') }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Description (Optional) --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        {{ $isAr ? 'وصف أو ملاحظات للطلاب (اختياري)' : __('Notes / Description (Optional)') }}
                    </label>
                    <textarea id="teacherFileDescriptionInput" name="description" rows="2"
                        placeholder="{{ $isAr ? 'اكتب ملاحظات توجيهية أو محتويات الملف...' : __('Add instructions or guidelines for students...') }}"
                        class="w-full bg-[#FAFAF9] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-teal-500 resize-none"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5 bg-slate-50/50 dark:bg-slate-900 shrink-0">
                <button type="button" onclick="closeTeacherFileUploadModal()"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="teacherFileUploadSubmitBtn"
                    class="btn-lift px-5 py-2 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-black rounded-xl shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ $isAr ? 'بدء الرفع' : __('Upload Now') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    window.openTeacherFileUploadModal = function() {
        if (typeof window.openModal === 'function') {
            window.openModal('teacherFileUploadModal');
        } else {
            const m = document.getElementById('teacherFileUploadModal');
            if (m) m.classList.remove('hidden');
        }
    };

    window.closeTeacherFileUploadModal = function() {
        if (typeof window.closeModal === 'function') {
            window.closeModal('teacherFileUploadModal');
        } else {
            const m = document.getElementById('teacherFileUploadModal');
            if (m) m.classList.add('hidden');
        }
    };

    window.handleTeacherFileSelection = function(input) {
        if (input.files && input.files[0]) {
            const f = input.files[0];
            const nameEl = document.getElementById('teacherFileNamePreview');
            const sizeEl = document.getElementById('teacherFileSizePreview');
            const titleInput = document.getElementById('teacherFileTitleInput');

            if (nameEl) nameEl.textContent = f.name;
            if (sizeEl) sizeEl.textContent = (f.size / (1024 * 1024)).toFixed(2) + ' MB';
            if (titleInput && !titleInput.value) {
                titleInput.value = f.name.replace(/\.[^/.]+$/, "");
            }
        }
    };

    window.toggleTeacherHomeworkFields = function(category) {
        const dueWrapper = document.getElementById('teacherHomeworkDueAtWrapper');
        const dueInput = document.getElementById('teacherFileDueAtInput');
        const submitBtnText = document.querySelector('#teacherFileUploadSubmitBtn span');
        const isAr = @json($isAr);

        if (category === 'homework') {
            if (dueWrapper) dueWrapper.classList.remove('hidden');
            if (dueInput) {
                dueInput.required = true;
                if (!dueInput.value) {
                    const d = new Date();
                    d.setDate(d.getDate() + 3);
                    d.setHours(23, 59, 0, 0);
                    dueInput.value = d.toISOString().slice(0, 16);
                }
            }
            if (submitBtnText) submitBtnText.textContent = isAr ? 'نشر الواجب ورفع الملف' : 'Publish Homework & Upload';
        } else {
            if (dueWrapper) dueWrapper.classList.add('hidden');
            if (dueInput) dueInput.required = false;
            if (submitBtnText) submitBtnText.textContent = isAr ? 'بدء الرفع' : 'Upload Now';
        }
    };

    window.filterTeacherFiles = function(filter) {
        const buttons = document.querySelectorAll('.teacher-file-filter-btn');
        buttons.forEach(btn => {
            if (btn.dataset.filter === filter) {
                btn.className = 'teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-teal-600 text-white shadow-xs transition-all cursor-pointer';
            } else {
                btn.className = 'teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer';
            }
        });

        const cards = document.querySelectorAll('.teacher-file-card');
        cards.forEach(card => {
            const role = card.getAttribute('data-role');
            const type = card.getAttribute('data-type');
            const category = card.getAttribute('data-category');
            const isHomework = card.getAttribute('data-is-homework') === 'true';
            let show = false;

            if (filter === 'all') show = true;
            else if (filter === 'material' && (category === 'material' || (!category && role === 'teacher' && !isHomework))) show = true;
            else if (filter === 'homework' && (category === 'homework' || isHomework)) show = true;
            else if (filter === 'student' && (category === 'submission' || role === 'student')) show = true;
            else if (filter === 'teacher' && role === 'teacher') show = true;
            else if (filter === 'pdf' && type === 'pdf') show = true;
            else if (filter === 'image' && type === 'image') show = true;

            card.style.display = show ? '' : 'none';
        });
    };

    window.searchTeacherFiles = function(query) {
        const q = (query || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.teacher-file-card');
        cards.forEach(card => {
            const title = (card.getAttribute('data-title') || '').toLowerCase();
            card.style.display = !q || title.includes(q) ? '' : 'none';
        });
    };

    window.deleteTeacherUploadedFile = async function(fileId, fileTitle) {
        const isAr = @json($isAr);
        const confirmMsg = isAr 
            ? `هل أنت متأكد من حذف الملف "${fileTitle}" نهائياً؟`
            : `Are you sure you want to permanently delete "${fileTitle}"?`;

        if (!confirm(confirmMsg)) return;

        try {
            const res = await fetch(`{{ url('/ajax/files') }}/${fileId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();
            if (data.success) {
                if (window.Toast) {
                    window.Toast.success(data.message || (isAr ? 'تم حذف الملف بنجاح' : 'File deleted successfully.'));
                }
                const card = document.getElementById(`teacherFileItem_${fileId}`);
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.remove(), 300);
                }
            } else {
                if (window.Toast) window.Toast.error(data.message || 'Error deleting file');
            }
        } catch (err) {
            console.error('Delete file error:', err);
            if (window.Toast) window.Toast.error(isAr ? 'حدث خطأ أثناء حذف الملف' : 'An error occurred while deleting the file');
        }
    };

    // Ajax Form Submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('teacherFileUploadForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('teacherFileUploadSubmitBtn');
            const originalBtnContent = submitBtn ? submitBtn.innerHTML : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>' + (@json($isAr ? 'جارٍ الرفع...' : 'Uploading...')) + '</span>';
            }

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    if (window.Toast) {
                        window.Toast.success(data.message || (@json($isAr ? 'تم رفع الملف بنجاح' : 'File uploaded successfully!')));
                    }
                    window.closeTeacherFileUploadModal();
                    form.reset();
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?tab=assignments';
                    }, 500);
                } else {
                    const errorMsg = data.message || (@json($isAr ? 'تعذر رفع الملف، يرجى مراجعة البيانات والتحقق من حجم الملف.' : 'Failed to upload file. Please verify details and file size.'));
                    if (window.Toast) window.Toast.error(errorMsg);
                    else alert(errorMsg);
                }
            } catch (err) {
                console.error('Upload error:', err);
                const errText = @json($isAr ? 'حدث خطأ في الاتصال أثناء الرفع' : 'Network error during upload');
                if (window.Toast) window.Toast.error(errText);
                else alert(errText);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    submitBtn.innerHTML = originalBtnContent;
                }
            }
        });
    });
})();
</script>
