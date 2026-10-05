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

            <button type="button" onclick="openTeacherFileUploadModal()"
                class="btn-lift px-5 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-black rounded-2xl shadow-lg shadow-teal-600/25 flex items-center gap-2 cursor-pointer self-start sm:self-auto shrink-0">
                <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                <span>{{ $isAr ? 'رفع ملف أو مذكرة جديدة' : __('Upload New File') }}</span>
            </button>
        </div>

        {{-- Quick Filter Pills & Course Selector Bar --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar pb-1 text-xs font-bold" id="teacherFileFilterPills">
                <button type="button" onclick="filterTeacherFiles('all')" data-filter="all"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-teal-600 text-white shadow-xs transition-all cursor-pointer">
                    {{ $isAr ? 'جميع الملفات' : __('All Files') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-white/20 text-[10px] font-mono">{{ $uploadedFiles->count() }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('teacher')" data-filter="teacher"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    {{ $isAr ? 'ملفاتي المرفوعة' : __('My Uploads') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">{{ $myFilesCount ?? 0 }}</span>
                </button>
                <button type="button" onclick="filterTeacherFiles('student')" data-filter="student"
                    class="teacher-file-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                    {{ $isAr ? 'واجبات الطلاب' : __('Student Homework') }}
                    <span class="ms-1 px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 text-[10px] font-mono">{{ $studentFilesCount ?? 0 }}</span>
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
                        $isStudentUpload = $file->uploader?->isStudent();
                        $uploaderName = $file->uploader?->name ?: __('User');
                    @endphp
                    <div class="teacher-file-card bg-[#FAFAF9] dark:bg-slate-800/60 rounded-2xl p-4 sm:p-5 border border-slate-200/90 dark:border-slate-800 hover:border-teal-400 dark:hover:border-teal-500 hover:shadow-lg transition-all flex flex-col justify-between space-y-4 group"
                        data-role="{{ $isStudentUpload ? 'student' : 'teacher' }}"
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
    </div>
</div>
