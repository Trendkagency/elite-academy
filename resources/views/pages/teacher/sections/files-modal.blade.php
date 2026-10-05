{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: TEACHER FILE UPLOAD (PDF / IMAGE EDUCATIONAL DOCUMENTS)              --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="teacherFileUploadModal"
    class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md transition-all duration-300">
    <div
        class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[24px] sm:rounded-[28px] max-w-2xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto">

        {{-- Modal Header --}}
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-teal-800/40">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/20 border border-teal-400/30 text-teal-300 flex items-center justify-center shrink-0 shadow-sm text-base">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div class="space-y-0.5">
                    <h3 class="font-heading font-black text-lg sm:text-xl text-white tracking-tight">
                        {{ $isAr ? 'رفع ملف أو مذكرة تعليمية' : __('Upload Educational File') }}
                    </h3>
                    <p class="text-xs text-teal-200/90 font-mono">
                        {{ $isAr ? 'يدعم ملفات PDF والصور (JPG, PNG, WEBP) بحد أقصى 25 ميجابايت' : __('Supports PDF and Images up to 25MB') }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeModal('teacherFileUploadModal')"
                aria-label="{{ __('Close') }}"
                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer active:scale-95 shrink-0">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Upload Form --}}
        <form id="teacherFileUploadForm" onsubmit="handleTeacherFileUpload(event)" class="flex-1 flex flex-col overflow-hidden m-0">
            @csrf

            <div class="p-5 sm:p-7 overflow-y-auto flex-1 custom-scrollbar space-y-5">
                {{-- Dropzone / File Picker --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-paperclip text-teal-600 dark:text-teal-400 text-xs"></i>
                        <span>{{ $isAr ? 'اختر ملف المستند أو الصورة' : __('Select Document or Image') }} *</span>
                    </label>

                    <div id="teacherFileDropzone" onclick="document.getElementById('teacherFileInput').click()"
                        class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-teal-500 dark:hover:border-teal-400 rounded-2xl p-6 text-center bg-[#FAFAF9] dark:bg-slate-800/50 hover:bg-teal-50/30 dark:hover:bg-teal-950/20 transition-all cursor-pointer group">
                        <input type="file" id="teacherFileInput" name="file" accept=".pdf,image/png,image/jpeg,image/webp,image/gif" class="hidden" onchange="previewSelectedTeacherFile(this)">
                        
                        <div id="teacherFileDropzonePrompt" class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-950/70 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    {{ $isAr ? 'اضغط لاختيار ملف أو اسحبه إلى هنا' : __('Click to browse or drag and drop file here') }}
                                </p>
                                <p class="text-[11px] font-mono text-slate-400 mt-0.5">
                                    {{ $isAr ? 'ملفات PDF ومستندات الصور فقط (الحد الأقصى 25 ميجابايت)' : __('PDF files & images only (Max 25MB)') }}
                                </p>
                            </div>
                        </div>

                        {{-- Selected File Box --}}
                        <div id="teacherFileSelectedBox" class="hidden flex items-center justify-between gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl border border-teal-200 dark:border-teal-800">
                            <div class="flex items-center gap-3 min-w-0">
                                <div id="teacherFileSelectedIcon" class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-solid fa-file"></i>
                                </div>
                                <div class="min-w-0 text-start">
                                    <p id="teacherFileSelectedName" class="text-xs font-bold text-slate-900 dark:text-white truncate">file.pdf</p>
                                    <p id="teacherFileSelectedSize" class="text-[10px] font-mono text-slate-400">0 KB</p>
                                </div>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); resetTeacherFileInput()"
                                class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 flex items-center justify-center text-xs shrink-0 cursor-pointer">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- File Title --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-heading text-teal-600 dark:text-teal-400 text-xs"></i>
                        <span>{{ $isAr ? 'عنوان الملف / المذكرة' : __('File Title') }} *</span>
                    </label>
                    <input type="text" id="teacherFileTitleInput" name="title" required
                        placeholder="{{ $isAr ? 'مثال: مذكرة مراجعة الباب الثاني - تفاضل وتكامل' : __('e.g. Unit 2 Physics Waves & Optics Worksheet') }}"
                        class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                </div>

                {{-- Target Course & Target Student --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-book-open text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ $isAr ? 'الكورس المستهدف' : __('Target Course') }}</span>
                        </label>
                        <select name="course_id" id="teacherFileCourseSelect"
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-teal-500">
                            <option value="">{{ $isAr ? '— عام / اختياري —' : __('— General / Optional —') }}</option>
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-graduate text-teal-600 dark:text-teal-400 text-xs"></i>
                            <span>{{ $isAr ? 'طالب محدد (اختياري)' : __('Target Specific Student') }}</span>
                        </label>
                        <select name="student_user_id" id="teacherFileStudentSelect"
                            class="w-full h-11 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-teal-500">
                            <option value="">{{ $isAr ? 'جميع طلاب الكورس' : __('All Students in Course') }}</option>
                            @foreach ($assignedStudents as $st)
                                <option value="{{ $st->user_id }}">{{ $st->user?->name ?: 'Student #' . $st->user_id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Description / Instructions --}}
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-align-left text-teal-600 dark:text-teal-400 text-xs"></i>
                        <span>{{ $isAr ? 'تعليمات وملاحظات للطلاب' : __('Instructions / Notes') }}</span>
                    </label>
                    <textarea name="description" rows="3"
                        placeholder="{{ $isAr ? 'اكتب ملاحظات إرشادية للطلاب بشأن هذا الملف أو الواجب...' : __('Add instructions or reading notes for learners...') }}"
                        class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3.5 text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all custom-scrollbar"></textarea>
                </div>
            </div>

            {{-- Sticky Action Footer --}}
            <div class="p-4 sm:p-5 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200/80 dark:border-slate-700 flex items-center justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModal('teacherFileUploadModal')"
                    class="btn-lift px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="teacherFileUploadSubmitBtn"
                    class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white rounded-xl text-xs font-black shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer transition-all">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ $isAr ? 'رفع وحفظ الملف' : __('Upload & Share File') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
