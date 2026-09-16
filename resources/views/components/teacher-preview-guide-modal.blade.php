@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
@endphp

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- TEACHER PORTAL INTERACTIVE PREVIEW & GUIDED TOUR MODAL                       --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="teacherPreviewGuideModal"
    class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950/80 backdrop-blur-md transition-all duration-300"
    style="z-index: 99999;">
    
    <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] max-w-4xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[92dvh] sm:max-h-[88vh] flex flex-col overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">

        {{-- Header with Luxury Emerald/Gold Theme --}}
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 text-white flex items-center justify-between gap-4 shrink-0 relative overflow-hidden border-b border-slate-700/60">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3.5 min-w-0 z-10">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-300 text-slate-950 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20 text-xl font-black">
                    <i class="fa-solid fa-wand-magic-sparkles animate-pulse"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-heading font-black text-lg sm:text-2xl text-white tracking-tight">
                            {{ $isAr ? 'دليل استخدام بوابة المعلم' : 'Teacher Portal Interactive Guide' }}
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                            {{ $isAr ? 'جولة تفاعلية' : 'Guided Tour' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 font-medium truncate max-w-lg mt-0.5">
                        {{ $isAr ? 'تعرف على كيفية إدارة الحصص، رصد الحضور، تصحيح الواجبات ومتابعة الطلاب بكفاءة واحترافية' : 'Learn how to schedule live sessions, record attendance, grade submissions, and manage learners.' }}
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeModal('teacherPreviewGuideModal')" aria-label="{{ __('Close') }}"
                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- Guide Navigation Pills --}}
        <div class="p-2 sm:p-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-800 flex items-center gap-1.5 overflow-x-auto scrollbar-none shrink-0">
            <button type="button" onclick="switchPreviewGuideTab('sessions')" id="guide-tab-btn-sessions"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 bg-teal-600 text-white shadow-sm">
                <i class="fa-solid fa-video"></i>
                <span>{{ $isAr ? 'الحصص والبث المباشر' : 'Live Sessions' }}</span>
            </button>
            <button type="button" onclick="switchPreviewGuideTab('assignments')" id="guide-tab-btn-assignments"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>{{ $isAr ? 'الواجبات والتصحيح' : 'Assignments & Grading' }}</span>
            </button>
            <button type="button" onclick="switchPreviewGuideTab('attendance')" id="guide-tab-btn-attendance"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>{{ $isAr ? 'رصد الحضور والأعذار' : 'Attendance & Excuses' }}</span>
            </button>
            <button type="button" onclick="switchPreviewGuideTab('students')" id="guide-tab-btn-students"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>{{ $isAr ? 'سجل الطلاب والملاحظات' : 'Students & Notes' }}</span>
            </button>
            <button type="button" onclick="switchPreviewGuideTab('schedules')" id="guide-tab-btn-schedules"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">
                <i class="fa-solid fa-calendar-days"></i>
                <span>{{ $isAr ? 'الجداول الدورية' : 'Recurring Timetables' }}</span>
            </button>
            <button type="button" onclick="switchPreviewGuideTab('overview')" id="guide-tab-btn-overview"
                class="guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60">
                <i class="fa-solid fa-chart-pie"></i>
                <span>{{ $isAr ? 'نظرة عامة والمؤشرات' : 'Overview & KPIs' }}</span>
            </button>
        </div>

        {{-- Scrollable Content Body --}}
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 custom-scrollbar bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200">

            {{-- ── SECTION 1: LIVE SESSIONS & STREAMING ─────────────────── --}}
            <div id="guide-content-sessions" class="guide-content-section space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تدير الحصص الافتراضية واللقاءات الحية؟' : 'How to manage live virtual classes?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'المنصة تدعم الربط التلقائي مع غرف البث الآمنة (Jitsi / Zoom / Google Meet)' : 'Seamlessly connect with secure virtual classrooms.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px]">1</span>
                                <span>{{ $isAr ? 'جدولة الحصة' : 'Schedule Session' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اضغط "جدولة حصة" وحدد المادة، التاريخ، التوقيت والمدة. يُنشأ رابط الاجتماع التلقائي فوراً.' : 'Click Schedule Session, select course, date and time. Auto-meeting link generates instantly.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px]">2</span>
                                <span>{{ $isAr ? 'بدء البث والتدريس' : 'Start & Broadcast' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اضغط "بدء البث / انضمام" للانتقال إلى الغرفة التفاعلية ومشاركة الشاشة والكاميرا مع الطلاب.' : 'Click "Join / Broadcast" to open the interactive classroom and share screen/camera.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px]">3</span>
                                <span>{{ $isAr ? 'تعديل أو إعادة جدولة' : 'Reschedule / Override' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يمكنك في أي وقت تغيير موعد أي حصة أو تحديث الرابط، ويتم إشعار الطلاب فوراً.' : 'Change session time or custom URL anytime; enrolled students receive instant notifications.' }}
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-800/80 flex items-start gap-2.5 text-xs text-teal-900 dark:text-teal-200">
                        <i class="fa-solid fa-lightbulb text-teal-600 dark:text-teal-400 text-sm mt-0.5 shrink-0"></i>
                        <span>{{ $isAr ? 'نصيحة: يظهر زر البث للطلاب تلقائياً قبل بدء الحصة بـ 15 دقيقة لضمان انضباط الحضور.' : 'Pro Tip: The broadcast join button becomes active for students 15 minutes before session time.' }}</span>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('sessions')"
                            class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى جدول الحصص الآن' : 'Go to Sessions Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 2: ASSIGNMENTS & GRADING ─────────────────────── --}}
            <div id="guide-content-assignments" class="guide-content-section hidden space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'نشر الواجبات وتصحيح إجابات الطلاب' : 'Publishing assignments and reviewing submissions' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'دورة متكاملة لتقييم أداء الطلاب، وضع الدرجات، وكتابة الملاحظات التعليمية' : 'End-to-end homework publishing, scoring rubric, and written pedagogical feedback.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px]">1</span>
                                <span>{{ $isAr ? 'نشر الواجب' : 'Publish Assignment' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'حدد المقرر، العنوان، الموعد النهائي، ملفات الشرح أو الأسئلة، والدرجة العظمى.' : 'Set course, title, deadline, PDF attachments, and maximum score.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px]">2</span>
                                <span>{{ $isAr ? 'طابور المراجعة' : 'Grading Queue' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تظهر ملفات الطلاب المرفوعة تلقائياً في طابور "في انتظار التصحيح".' : 'Uploaded student files appear in real-time under the "Pending Grading Queue".' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px]">3</span>
                                <span>{{ $isAr ? 'رصد الدرجة والملاحظات' : 'Grade & Feedback' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'افتح نافذة التصحيح، عاين ملف الإجابة، ضع النسبة المئوية واكتب توجيهاتك للطالب.' : 'Open grading modal, preview submitted work, enter score %, and submit feedback notes.' }}
                            </p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200/80 dark:border-orange-800/80 flex items-start gap-2.5 text-xs text-orange-900 dark:text-orange-200">
                        <i class="fa-solid fa-bell text-orange-600 dark:text-orange-400 text-sm mt-0.5 shrink-0"></i>
                        <span>{{ $isAr ? 'ملاحظة: فور اعتماد الدرجة، يستلم الطالب وولي الأمر إشعاراً فورياً مع تقرير التقييم.' : 'Note: Upon grading submission, student & parent receive instant SMS/Portal notification.' }}</span>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('assignments')"
                            class="btn-lift px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى الواجبات والتصحيح' : 'Go to Assignments Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 3: ATTENDANCE & EXCUSES ──────────────────────── --}}
            <div id="guide-content-attendance" class="guide-content-section hidden space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'رصد الحضور الفوري والتعامل مع طلبات الأعذار' : 'Real-time attendance recording and excuse handling' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'أدوات سريعة بنقرة واحدة لتسجيل حضور المجموعات ومعالجة الغياب' : 'One-click bulk actions and automated credit balance deduction rules.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px]">1</span>
                                <span>{{ $isAr ? 'كشف الحضور المباشر' : 'Live Attendance Sheet' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اضغط على زر "رصد الحضور" بجانب أي حصة لفتح قائمة الطلاب المسجلين.' : 'Click "Record Attendance" next to any session to open the live student cohort sheet.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px]">2</span>
                                <span>{{ $isAr ? 'أزرار التحديد الجماعي' : 'Fast Bulk Toggles' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'استخدم أزرار "الكل حاضر"، "الكل متأخر"، أو "الكل غائب" لتحديد الحالة بضغطة واحدة ثم تعديل الاستثناءات.' : 'Use "All Present", "All Late", or "All Absent" for 1-click batch updates.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px]">3</span>
                                <span>{{ $isAr ? 'سجل الأعذار والتقارير الطبية' : 'Excuses & Medical Docs' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'عاين أعذار الطلاب المرفوعة في تبويب "الأعذار"؛ الموافقة تحفظ رصيد الطالب والرفض يخصم حصة واحدة.' : 'Review excuses tab: approval preserves student credit; rejection deducts 1 session balance.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('attendance')"
                            class="btn-lift px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى سجل الحضور' : 'Go to Attendance Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 4: STUDENTS ROSTER & NOTES ───────────────────── --}}
            <div id="guide-content-students" class="guide-content-section hidden space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'ملفات الطلاب والتواصل التربوي' : 'Student profiles & pedagogical communication' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'فلاتر شاملة حسب الصف، نسبة الحضور، وإرسال ملاحظات شخصية' : 'Comprehensive filtering by grade, attendance health, and personalized notes.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <h5 class="font-bold text-xs text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                                <i class="fa-solid fa-id-card"></i>
                                <span>{{ $isAr ? 'الملف الأكاديمي للطالب' : 'Student Academic Dossier' }}</span>
                            </h5>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اضغط على بطاقة أي طالب للاطلاع على المقررات المسجلة، معدل الدرجات، نسبة الحضور، ورقم ولي الأمر.' : 'Click any student card to view enrolled courses, average score, attendance rate, and guardian phone.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <h5 class="font-bold text-xs text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                                <i class="fa-solid fa-comment-dots"></i>
                                <span>{{ $isAr ? 'إرسال ملاحظة تربوية' : 'Send Direct Pedagogical Note' }}</span>
                            </h5>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يمكنك إرسال إشادة أو تنبيه أكاديمي خاص يصل مباشرة إلى حساب الطالب وتطبيق ولي الأمر.' : 'Send private praises or academic alerts directly to student and parent portal.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('students')"
                            class="btn-lift px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى قائمة الطلاب' : 'Go to Students Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 5: RECURRING SCHEDULES ──────────────────────── --}}
            <div id="guide-content-schedules" class="guide-content-section hidden space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'الجداول الدورية والأتمتة الذكية' : 'Smart recurring timetables & automated generation' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'أنشئ جدول الحصص الأسبوعي أو الشهري دفعة واحدة مع كاشف التعارضات التلقائي' : 'Set up recurring schedules with intelligent collision detection before publishing.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/60 flex items-center justify-center text-[10px]">1</span>
                                <span>{{ $isAr ? 'اختيار النمط والأيام' : 'Pattern & Days' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'حدد الأيام (مثلاً: الأحد والثلاثاء)، توقيت البدء، وتاريخ النهاية التلقائي.' : 'Select days of week, start time, and auto-calculated end date.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/60 flex items-center justify-center text-[10px]">2</span>
                                <span>{{ $isAr ? 'فحص التعارضات' : 'Conflict Scan' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يقوم النظام فوراً بالتحقق من عدم وجود حصص أخرى في نفس المواعيد مع تنبيهك.' : 'System scans calendar and warns if any scheduled slot collides with existing classes.' }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-900/60 flex items-center justify-center text-[10px]">3</span>
                                <span>{{ $isAr ? 'التوليد التلقائي' : 'Batch Creation' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تُنشأ جميع الحصص دفعة واحدة وتظهر في جدول الطلاب مع روابط البث الخاصة.' : 'Generates entire session series with meeting links and timetable sync.' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('schedules')"
                            class="btn-lift px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى الجداول الدورية' : 'Go to Schedules Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 6: OVERVIEW & KPIS ───────────────────────────── --}}
            <div id="guide-content-overview" class="guide-content-section hidden space-y-5">
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base text-slate-900 dark:text-white">
                                {{ $isAr ? 'فهم المؤشرات والإحصائيات في اللوحة الرئيسية' : 'Understanding KPIs & Dashboard Metrics' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'نظرة سريعة على أدائك التدريسي ونشاط الطلاب اليومي' : 'Quick overview of your teaching load and active cohort activity.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-teal-600 uppercase block">{{ $isAr ? 'حصص اليوم' : 'Today Sessions' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'عدد الحصص المجدولة لتاريخ اليوم' : 'Classes scheduled for today' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-blue-600 uppercase block">{{ $isAr ? 'الحصص القادمة' : 'Upcoming Sessions' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'إجمالي الحصص المستقبلية المبرمجة' : 'All future planned cohort meetings' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-indigo-600 uppercase block">{{ $isAr ? 'طلابي' : 'My Students' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'إجمالي الطلاب المسجلين بمقرراتك' : 'Total enrolled learners' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-amber-600 uppercase block">{{ $isAr ? 'بانتظار التصحيح' : 'Need Grading' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'واجبات تم تسليمها وتنتظر تقييمك' : 'Submissions waiting for review' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-purple-600 uppercase block">{{ $isAr ? 'إجمالي التسليمات' : 'Submissions' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'مجموع الواجبات المصححة والمكتملة' : 'All lifetime submitted assignments' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-emerald-600 uppercase block">{{ $isAr ? 'نسبة الحضور' : 'Attendance Rate' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'معدل الالتزام بالحضور لجميع حصصك' : 'Overall student attendance %' }}</p>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" onclick="switchTeacherTabAndClosePreview('overview')"
                            class="btn-lift px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl flex items-center gap-2">
                            <span>{{ $isAr ? 'الانتقال إلى اللوحة الرئيسية' : 'Go to Overview Tab' }}</span>
                            <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sticky Footer with Action Controls --}}
        <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[28px]">
            <div class="text-xs font-mono text-slate-500 hidden sm:flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-teal-600"></i>
                <span>{{ $isAr ? 'أكاديمية النخبة التعليمية • نظام إدارة التعلم المتطور' : 'Elite Academy LMS • Faculty Management Suite' }}</span>
            </div>
            <div class="flex items-center gap-2.5 ms-auto">
                <button type="button" onclick="closeModal('teacherPreviewGuideModal')"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // ── Teacher Preview Guide Tab Switcher ────────────────────────────────
    window.switchPreviewGuideTab = function(tabKey) {
        document.querySelectorAll('.guide-content-section').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.guide-tab-btn').forEach(btn => {
            btn.className = 'guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer';
        });

        const activeContent = document.getElementById(`guide-content-${tabKey}`);
        if (activeContent) activeContent.classList.remove('hidden');

        const activeBtn = document.getElementById(`guide-tab-btn-${tabKey}`);
        if (activeBtn) {
            activeBtn.className = 'guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 bg-teal-600 text-white shadow-sm cursor-pointer';
        }
    };

    window.openTeacherPreviewModal = function(initialTab = 'sessions') {
        window.switchPreviewGuideTab(initialTab);
        window.openModal('teacherPreviewGuideModal');
    };

    window.switchTeacherTabAndClosePreview = function(tabKey) {
        window.closeModal('teacherPreviewGuideModal');
        if (typeof window.switchTeacherTab === 'function') {
            window.switchTeacherTab(tabKey);
        }
    };
</script>
