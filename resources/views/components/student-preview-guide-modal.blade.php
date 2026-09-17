@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
@endphp

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- STUDENT PORTAL INTERACTIVE PREVIEW & GUIDED TOUR MODAL                       --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<div id="studentPreviewGuideModal"
    class="elite-modal fixed inset-0 z-50 hidden flex items-center justify-center p-2.5 sm:p-4 md:p-6 bg-slate-950/80 backdrop-blur-md transition-all duration-300"
    style="z-index: 99999;"
    role="dialog"
    aria-modal="true"
    aria-labelledby="studentGuideModalTitle">
    
    <div class="elite-modal-dialog bg-white dark:bg-slate-900 rounded-[28px] max-w-5xl w-full shadow-2xl border border-slate-200/90 dark:border-slate-800 relative max-h-[94dvh] sm:max-h-[90vh] flex flex-col overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-200">

        {{-- Modal Header: Ultra-Premium Student Teal & Amber Glow Theme --}}
        <div class="p-4 sm:p-6 bg-gradient-to-r from-slate-950 via-teal-950 to-slate-900 text-white flex items-center justify-between gap-4 shrink-0 relative overflow-hidden border-b border-teal-800/40">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-48 h-48 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3.5 min-w-0 z-10">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-teal-400 via-emerald-400 to-amber-300 text-slate-950 flex items-center justify-center shrink-0 shadow-lg shadow-teal-500/25 text-xl font-black">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 id="studentGuideModalTitle" class="font-heading font-black text-base sm:text-xl md:text-2xl text-white tracking-tight">
                            {{ $isAr ? 'دليل استخدام بوابة الطالب والتجربة الحية' : 'Student Portal Interactive Guide & Live Demo' }}
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                            {{ $isAr ? 'معاينة حية تفاعلية' : 'Interactive Demo Simulator' }}
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-300 font-medium truncate max-w-xl mt-0.5">
                        {{ $isAr ? 'تعلم كيفية حضور الحصص المباشرة، حل الواجبات، متابعة رصيد الباقة وتقديم الأعذار خطوة بخطوة' : 'Master your learning journey: attend live classes, solve assignments, track credits, and submit excuses.' }}
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeStudentPreviewGuide()" aria-label="{{ __('Close') }}"
                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-rose-500/80 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer z-10 active:scale-95 shrink-0 border border-white/10">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        {{-- Guide Navigation Pills: Scrollable Horizontal Bar --}}
        <div class="p-2 sm:p-3 bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200/80 dark:border-slate-800 flex items-center gap-1.5 overflow-x-auto scrollbar-none shrink-0">
            <button type="button" onclick="switchStudentGuideTab('sessions')" id="stu-guide-tab-sessions"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 bg-teal-600 text-white shadow-sm cursor-pointer">
                <i class="fa-solid fa-satellite-dish"></i>
                <span>{{ $isAr ? 'الحصص والفصول المباشرة' : 'Live Sessions' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('courses')" id="stu-guide-tab-courses"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-book-open"></i>
                <span>{{ $isAr ? 'الكورسات والمنهج' : 'Courses & Roadmap' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('assignments')" id="stu-guide-tab-assignments"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>{{ $isAr ? 'الواجبات والاختبارات' : 'Assignments & MSQs' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('submissions')" id="stu-guide-tab-submissions"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-square-poll-vertical"></i>
                <span>{{ $isAr ? 'الدرجات والتسليمات' : 'Submissions & Grades' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('packages')" id="stu-guide-tab-packages"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-credit-card"></i>
                <span>{{ $isAr ? 'رصيد الباقة والحصص' : 'Package & Credits' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('exceptions')" id="stu-guide-tab-exceptions"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>{{ $isAr ? 'الأعذار والاستثناءات' : 'Excuses & Extensions' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('notifications')" id="stu-guide-tab-notifications"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-bell"></i>
                <span>{{ $isAr ? 'الإشعارات والتنبيهات' : 'Notifications' }}</span>
            </button>
            <button type="button" onclick="switchStudentGuideTab('overview')" id="stu-guide-tab-overview"
                class="stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer">
                <i class="fa-solid fa-chart-pie"></i>
                <span>{{ $isAr ? 'نظرة عامة واللوحة' : 'Overview' }}</span>
            </button>
        </div>

        {{-- Scrollable Content Body --}}
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 custom-scrollbar bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200">

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 1: SESSIONS & LIVE CLASSROOMS                             --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-sessions" class="stu-guide-pane space-y-6">
                {{-- Explanation Header Card --}}
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0 border border-teal-200 dark:border-teal-800">
                            <i class="fa-solid fa-satellite-dish"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تحضر حصص البث المباشر والفصول التفاعلية؟' : 'How to attend live sessions and virtual classrooms?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'شرح كامل لآلية الانضمام للحصة، التحقق من الميكروفون والكاميرا، وقواعد الحضور.' : 'Step-by-step workflow: timetable inspection, audio/video readiness, and joining the live class.' }}
                            </p>
                        </div>
                    </div>

                    {{-- 3-Step Workflow --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'متابعة المواعيد والعد التنازلي' : 'Timetable & Countdown' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'في تبويب "الحصص اليومية"، يظهر عد تنازلي دقيق بالثواني لكل حصة قادمة مع اسم المعلم والمقرر.' : 'Check the exact live countdown for upcoming cohort and 1-to-1 sessions with teacher details.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'تفعيل زر الانضمام' : 'Join Button Unlocks' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يتحول الزر إلى "دخول البث المباشر" قبل الموعد بـ 15 دقيقة أو فور قيام المعلم ببدء البث.' : 'The join button lights up 15 minutes before start or immediately when teacher broadcasts.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'تسجيل الحضور التلقائي' : 'Instant Attendance Log' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'بمجرد دخولك الغرفة، يتم توثيق حضورك وتحديث نسبة التزامك دون أي تدخل يدوي.' : 'Entering the session automatically records your attendance timestamp and attendance streak.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Classroom Card Simulator --}}
                <div class="p-4 sm:p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 text-white border border-teal-500/30 shadow-xl space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                            <span class="font-mono text-xs font-black uppercase text-teal-300 tracking-wider">
                                {{ $isAr ? 'محاكاة تفاعلية حية: بطاقة الحصة المباشرة' : 'Live Interactive Classroom Simulator' }}
                            </span>
                        </div>
                        <span class="text-[11px] font-mono text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full border border-amber-400/30">
                            {{ $isAr ? 'جرّب التفاعل الآن' : 'Interactive Demo' }}
                        </span>
                    </div>

                    {{-- Simulator Component Body --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-rose-600 text-white shadow-sm flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        {{ $isAr ? 'مباشر الآن' : 'LIVE NOW' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                                        {{ $isAr ? 'جلسة خاصة 1-على-1' : 'Private 1-on-1' }}
                                    </span>
                                </div>
                                <h5 class="font-heading font-black text-base sm:text-lg text-white">
                                    {{ $isAr ? 'الرياضيات المتقدمة: حساب التفاضل والتكامل' : 'Advanced Calculus & Analytical Geometry' }}
                                </h5>
                                <p class="text-xs text-slate-300 font-mono flex items-center gap-2">
                                    <span><i class="fa-solid fa-chalkboard-user text-teal-400"></i> {{ $isAr ? 'د. أحمد الصالح' : 'Dr. Ahmed Al-Saleh' }}</span>
                                    <span>•</span>
                                    <span><i class="fa-solid fa-clock text-amber-400"></i> 60 {{ $isAr ? 'دقيقة' : 'Mins' }}</span>
                                </p>
                            </div>

                            {{-- Interactive Simulated Audio/Video Controls --}}
                            <div class="flex items-center gap-2 bg-slate-900/80 p-2 rounded-2xl border border-slate-700 shrink-0">
                                <button type="button" id="demoMicToggle" onclick="toggleDemoDevice('demoMicToggle', 'fa-microphone', 'fa-microphone-slash')"
                                    class="w-9 h-9 rounded-xl bg-teal-600/30 hover:bg-teal-600/50 text-teal-300 flex items-center justify-center text-xs transition-all cursor-pointer"
                                    title="{{ $isAr ? 'كتم/تشغيل الميكروفون' : 'Toggle Microphone' }}">
                                    <i class="fa-solid fa-microphone"></i>
                                </button>
                                <button type="button" id="demoCamToggle" onclick="toggleDemoDevice('demoCamToggle', 'fa-video', 'fa-video-slash')"
                                    class="w-9 h-9 rounded-xl bg-teal-600/30 hover:bg-teal-600/50 text-teal-300 flex items-center justify-center text-xs transition-all cursor-pointer"
                                    title="{{ $isAr ? 'تعطيل/تشغيل الكاميرا' : 'Toggle Camera' }}">
                                    <i class="fa-solid fa-video"></i>
                                </button>
                                <button type="button" onclick="playDemoSoundCheck()"
                                    class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-mono font-bold flex items-center gap-1.5 transition-all cursor-pointer border border-amber-500/40">
                                    <i class="fa-solid fa-volume-high"></i>
                                    <span>{{ $isAr ? 'فحص الصوت' : 'Sound Test' }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Action simulator bar --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-white/10">
                            <div id="demoClassStatus" class="text-xs font-mono text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                <span>{{ $isAr ? 'غرفة الفصل الافتراضي الآمنة جاهزة للاتصال' : 'Secure classroom room is online and ready' }}</span>
                            </div>

                            <button type="button" onclick="triggerDemoClassroomJoin()" id="demoJoinBtn"
                                class="btn-lift w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-teal-400 to-emerald-500 hover:from-teal-300 hover:to-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-teal-500/30 flex items-center justify-center gap-2 cursor-pointer transition-all">
                                <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                                <span id="demoJoinBtnText">{{ $isAr ? 'تجربة دخول البث المباشر' : 'Simulate Join Classroom' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Pro-Tip & Direct Jump Action --}}
                <div class="p-3.5 rounded-2xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-800/80 flex items-start gap-3 text-xs text-teal-900 dark:text-teal-200">
                    <i class="fa-solid fa-lightbulb text-teal-600 dark:text-teal-400 text-base mt-0.5 shrink-0"></i>
                    <div class="space-y-1">
                        <p class="font-bold">{{ $isAr ? 'قاعدة الانضباط والـ 30 دقيقة:' : '30-Minute Attendance Rule:' }}</p>
                        <p class="text-[11px] leading-relaxed text-teal-800 dark:text-teal-300/90">
                            {{ $isAr ? 'يتم احتساب الغياب في حال عدم الدخول خلال 30 دقيقة من موعد بدء الحصة. إذا طرأ لك ظرف قاهر، يمكنك تقديم عذر رسمي فوراً من البوابة لحفظ رصيد حصتك.' : 'Absence is recorded if you do not enter within 30 minutes of session start. In emergencies, submit an excuse ticket immediately to protect your credit balance.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('sessions')"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-teal-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى جدول الحصص الآن' : 'Go to Sessions Hub' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 2: ENROLLED COURSES & ROADMAP                             --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-courses" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shrink-0 border border-indigo-200 dark:border-indigo-800">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تستكشف المقررات والمحتوى المنهجي؟' : 'How to navigate enrolled courses and curriculum roadmap?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'طريقة الاطلاع على الخطة المنهجية، المحاضرات المسجلة وملفات الـ PDF لكل مادة.' : 'Access certified curriculum syllabi, recorded video modules, and attached lecture resources.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'استعراض الكورسات المفعلة' : 'Active Course Cards' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تظهر جميع المقررات المرتبطة بصفك الدراسي مع اسم المحاضر ونسبة التقدم في المنهج.' : 'View all registered subjects with instructor profiles, enrolled dates, and completion status.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'نافذة تفاصيل المقرر' : 'Curriculum Explorer' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اضغط على زر "استعراض المنهج" لفتح نافذة شاملة بجميع الوحدات التعليمية والحصص.' : 'Click "Explore Curriculum" to open the modular drawer of all lectures and assignments.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'الدروس المسجلة 24/7' : '24/7 Recorded Replays' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يمكنك إعادة مشاهدة الدروس المسجلة في أي وقت ومراجعة الشروحات قبل الاختبارات.' : 'Replay high-definition recorded lessons at your own pace anytime from any device.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Interactive Course Progress & Syllabus Explorer --}}
                <div class="p-4 sm:p-6 rounded-3xl bg-white dark:bg-slate-800/90 border border-indigo-200 dark:border-indigo-800/80 shadow-md space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/80 pb-3">
                        <span class="font-mono text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                            <i class="fa-solid fa-sliders"></i>
                            <span>{{ $isAr ? 'محاكاة تفاعلية: استكشاف المنهج ونسبة الإنجاز' : 'Interactive Syllabus & Progress Demo' }}</span>
                        </span>
                        <span class="text-[10px] font-mono font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 px-2.5 py-0.5 rounded-full border border-indigo-300 dark:border-indigo-800">
                            {{ $isAr ? 'تفاعلي' : 'Interactive' }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800">
                                    STEM Grade 12
                                </span>
                                <h5 class="font-heading font-black text-sm sm:text-base text-slate-900 dark:text-white mt-1">
                                    {{ $isAr ? 'الفيزياء الحديثة: ميكانيكا الكم والنسبية' : 'Modern Physics: Quantum & Relativity' }}
                                </h5>
                            </div>
                            <div class="text-end">
                                <span id="demoCourseProgressText" class="font-heading font-black text-lg text-indigo-600 dark:text-indigo-400">75%</span>
                                <span class="text-[10px] text-slate-400 block font-mono">{{ $isAr ? 'نسبة الإنجاز' : 'Completed' }}</span>
                            </div>
                        </div>

                        {{-- Simulated Progress Bar --}}
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                            <div id="demoCourseProgressBar" class="bg-gradient-to-r from-indigo-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: 75%;"></div>
                        </div>

                        {{-- Progress Selector Toggles --}}
                        <div class="flex items-center gap-2 pt-1">
                            <span class="text-[11px] text-slate-500 font-mono">{{ $isAr ? 'جرّب تغيير نسبة التقدم:' : 'Simulate Progress:' }}</span>
                            <button type="button" onclick="setDemoCourseProgress(25)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-600 hover:text-white transition-all cursor-pointer">25%</button>
                            <button type="button" onclick="setDemoCourseProgress(75)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-indigo-600 text-white transition-all cursor-pointer">75%</button>
                            <button type="button" onclick="setDemoCourseProgress(100)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-indigo-600 hover:text-white transition-all cursor-pointer">100% (تخرج)</button>
                        </div>

                        {{-- Interactive Module Items preview --}}
                        <div class="space-y-2 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                            <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? 'الوحدة الأولى: إشعاع الجسم الأسود' : 'Module 1: Blackbody Radiation' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'فيديو مسجل + ملخص PDF' : 'Video Lesson + PDF Notes' }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono text-emerald-600 font-bold bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded">{{ $isAr ? 'مكتمل' : 'Passed' }}</span>
                            </div>

                            <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200/90 dark:border-slate-700 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-play"></i>
                                    </span>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? 'الوحدة الثانية: الظاهرة الكهروضوئية والواجب' : 'Module 2: Photoelectric Effect & MSQ' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'حصـة قادمة + واجب تقييمي' : 'Live Class + Homework' }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono text-amber-600 font-bold bg-amber-50 dark:bg-amber-950 px-2 py-0.5 rounded">{{ $isAr ? 'قيد الدراسة' : 'Current' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('courses')"
                        class="btn-lift px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-indigo-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى الكورسات والمنهج' : 'Go to Enrolled Courses' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 3: ASSIGNMENTS & MSQ QUIZZES                              --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-assignments" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0 border border-amber-200 dark:border-amber-800">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تحل الواجبات والاختبارات التفاعلية (MSQ)؟' : 'How to complete assignments and interactive MSQ tests?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'دورة حل الأسئلة، المؤقت الزمني، والتصحيح التلقائي المباشر للدرجة.' : 'Instant quiz taking, countdown clock, option selection, and automated scoring rubric.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'تنبيهات الموعد النهائي' : 'Deadline Alert 24h' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تصلك إشعارات قبل 24 ساعة من انتهاء مهلة أي واجب لتفادي فوات وقت التسليم.' : 'Instant notifications alert you before deadlines to prevent overdue submissions.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'المؤقت الزمني للاختبار' : 'Live Exam Countdown' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'عند فتح الاختبار يظهر عداد الوقت أعلى الشاشة. يجب إرسال الإجابات قبل انتهاء الوقت.' : 'A countdown timer stays active at the top. Answers must be submitted before expiry.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'التقييم الفوري والشهادة' : 'Automated Grading' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تظهر النسبة المئوية فوراً، ويتم إدراج الدرجة تلقائياً في ملفك وسجل التقييمات.' : 'Get instant automated scoring, pass/fail status, and direct recording into your academic profile.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Fully Interactive Mini-Quiz Solver --}}
                <div class="p-4 sm:p-6 rounded-3xl bg-slate-900 text-white border border-amber-500/30 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span class="font-mono text-xs font-black uppercase text-amber-300">
                                {{ $isAr ? 'محاكاة حية: حل سؤال تفاعلي (MSQ)' : 'Live Interactive MSQ Quiz Simulator' }}
                            </span>
                        </div>
                        <span class="text-xs font-mono font-bold text-teal-400 bg-teal-400/10 px-2.5 py-0.5 rounded-full border border-teal-400/30">
                            {{ $isAr ? 'المؤقت التجريبي: 04:59' : 'Demo Timer: 04:59' }}
                        </span>
                    </div>

                    {{-- Question Simulation Box --}}
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm text-white">
                                {{ $isAr ? 'سؤال 1: ما هو العضي الخلوي المسؤول عن إنتاج الطاقة (ATP) في الخلية؟' : 'Q1: Which organelle is primarily responsible for cellular ATP synthesis?' }}
                            </span>
                            <span class="text-[10px] font-mono font-bold bg-white/10 text-amber-300 px-2 py-0.5 rounded">2 Pts</span>
                        </div>

                        {{-- Clickable Options --}}
                        <div class="space-y-2 pt-1" id="demoQuizOptions">
                            <button type="button" onclick="selectDemoQuizOption(this, false)"
                                class="demo-quiz-opt w-full text-start p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-mono text-slate-200 transition-all flex items-center justify-between cursor-pointer">
                                <span>A) {{ $isAr ? 'الريبوسومات (Ribosomes)' : 'Ribosomes' }}</span>
                                <span class="opt-feedback text-[11px] font-bold hidden"></span>
                            </button>
                            <button type="button" onclick="selectDemoQuizOption(this, true)"
                                class="demo-quiz-opt w-full text-start p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-mono text-slate-200 transition-all flex items-center justify-between cursor-pointer">
                                <span>B) {{ $isAr ? 'الميتوكوندريا (Mitochondria)' : 'Mitochondria' }}</span>
                                <span class="opt-feedback text-[11px] font-bold hidden"></span>
                            </button>
                            <button type="button" onclick="selectDemoQuizOption(this, false)"
                                class="demo-quiz-opt w-full text-start p-3 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-mono text-slate-200 transition-all flex items-center justify-between cursor-pointer">
                                <span>C) {{ $isAr ? 'جهاز جولجي (Golgi Apparatus)' : 'Golgi Apparatus' }}</span>
                                <span class="opt-feedback text-[11px] font-bold hidden"></span>
                            </button>
                        </div>

                        {{-- Result Alert --}}
                        <div id="demoQuizResult" class="hidden p-3 rounded-xl text-xs font-bold font-mono transition-all"></div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('assignments')"
                        class="btn-lift px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-amber-500/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى قسم الواجبات' : 'Go to Assignments Hub' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 4: SUBMISSIONS HISTORY & GRADES                           --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-submissions" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0 border border-emerald-200 dark:border-emerald-800">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تستعرض سجل التقييمات وملاحظات المعلم؟' : 'How to inspect graded submissions and pedagogical feedback?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'استعراض درجاتك المعتمدة، ملاحظات التصحيح، ومعدل تفوقك الأكاديمي.' : 'Track lifetime graded submissions, rubric breakdowns, and personalized teacher advice.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'بطاقة الدرجة المئوية' : 'Score Percentage' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'عرض النسبة المئوية للواجب مع مؤشر لوني (أخضر للممتاز، برتقالي للمتوسط).' : 'Visual color-coded badges for high distinction, pass, and needs-improvement thresholds.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'ملاحظات المعلم التوجيهية' : 'Teacher Notes & Feedback' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'قراءة التعليقات التعليمية التي كتبها المعلم على حلولك ومواطن القوة والضعف.' : 'Read custom feedback written by your instructor guiding your revision and exam prep.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'تصفية سريعة حسب الكورس' : 'Filter by Subject' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'أزرار الفلترة تتيح عزل تسليمات مادة معينة بضغطة واحدة لمتابعة الأداء بدقة.' : 'Quick filter pills let you isolate physics, math, or chemistry submissions with one tap.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Graded Submission Card with Feedback --}}
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-emerald-300 dark:border-emerald-800 shadow-md space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                        <span class="font-mono text-xs font-black uppercase text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                            <i class="fa-solid fa-award"></i>
                            <span>{{ $isAr ? 'معاينة حية: نموذج بطاقة تقييم واجب معتمد' : 'Live Demo: Graded Submission Preview' }}</span>
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300">
                            95% • ممتاز A+
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h5 class="font-heading font-black text-sm text-slate-900 dark:text-white">
                                    {{ $isAr ? 'واجب الحصة الثالثة: قوانين نيوتن وتطبيقات الحركة' : 'Session 3 Homework: Newton Laws & Kinematics' }}
                                </h5>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'تم التصحيح والاعتماد بواسطة المعلم' : 'Graded & Approved by Instructor' }}</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                19 / 20 {{ $isAr ? 'درجة' : 'Pts' }}
                            </span>
                        </div>

                        {{-- Teacher Feedback Box --}}
                        <div class="p-3 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-900 flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 flex items-center justify-center text-xs font-black shrink-0 mt-0.5">
                                A
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-xs font-bold text-emerald-950 dark:text-emerald-200">{{ $isAr ? 'ملاحظة المعلم د. أحمد:' : 'Instructor Note from Dr. Ahmed:' }}</p>
                                <p class="text-[11px] text-emerald-900 dark:text-emerald-300/90 leading-relaxed font-mono">
                                    {{ $isAr ? '"إجابات نموذجية ممتازة وتطبيق رائع لمعادلات الحركة، انتبه فقط لوحدات القياس في المسألة الأخيرة. أحسنت!"' : '"Outstanding analytical derivation! Pay slight attention to SI units on question 4. Great work!"' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('submissions')"
                        class="btn-lift px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-emerald-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى سجل التسليمات والدرجات' : 'Go to Submissions' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 5: PACKAGES & SESSION CREDITS BALANCE                     --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-packages" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shrink-0 border border-teal-200 dark:border-teal-800">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف يتم احتساب رصيد الحصص واستهلاك الباقة؟' : 'How does your package balance & session credit deduction work?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'قواعد الشفافية المالية والأكاديمية لرصيد الحصص المتبقية والمستهلكة.' : 'Clear accounting for total credits, consumed sessions, and excused restorations.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'رصيد الحصص المتبقية' : 'Remaining Credits' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'يتم خصم حصة واحدة عند حضورك الفصل المباشر، أو في حال الغياب بدون عذر مقبول.' : 'One session credit is consumed upon classroom attendance, or on unexcused absence.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'حماية الرصيد بالأعذار' : 'Credit Protection via Excuses' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'عند قبول عذر غيابك من قبل الإدارة والمعلم، يُحفظ رصيد الحصة كاملاً ولا يُخصم أبداً.' : 'When your absence excuse is approved, your credit is preserved and never deducted.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-teal-600 dark:text-teal-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-teal-100 dark:bg-teal-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'تجديد الباقة التلقائي' : 'Package Renewal Alerts' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'عند وصول الرصيد إلى حصتين أو أقل، تظهر شارة تنبيهية لتجديد الباقة دون انقطاع.' : 'Early renewal reminders trigger when 2 or fewer credits remain to avoid disruptions.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Credit Wallet Counter Simulator --}}
                <div class="p-4 sm:p-6 rounded-3xl bg-slate-900 text-white border border-teal-500/30 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <span class="font-mono text-xs font-black uppercase text-teal-300 flex items-center gap-2">
                            <i class="fa-solid fa-calculator"></i>
                            <span>{{ $isAr ? 'محاكاة تفاعلية: تجربة استهلاك واسترجاع رصيد الحصص' : 'Interactive Credit Wallet Simulator' }}</span>
                        </span>
                        <span class="text-[10px] font-mono text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/30">
                            {{ $isAr ? 'تجربة حية' : 'Live Test' }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono uppercase text-slate-400 font-bold">{{ $isAr ? 'رصيد الحصص التجريبي' : 'Simulated Session Balance' }}</span>
                            <div class="flex items-baseline gap-2">
                                <span id="demoCreditCounter" class="font-heading font-black text-3xl sm:text-4xl text-teal-300">8</span>
                                <span class="text-xs text-slate-300 font-mono">{{ $isAr ? 'حصة متبقية من أصل 12' : 'credits left of 12' }}</span>
                            </div>
                            <p id="demoCreditStatusNote" class="text-[11px] text-slate-400 font-mono">
                                {{ $isAr ? 'حالتك ممتازة، الرصيد يغطي جميع حصص الشهر الحالي.' : 'Balance is healthy for this month.' }}
                            </p>
                        </div>

                        {{-- Interactive Simulation Buttons --}}
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            <button type="button" onclick="simulateCreditDeduction()"
                                class="px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 text-xs font-mono font-bold border border-rose-500/30 flex items-center gap-1.5 cursor-pointer transition-all">
                                <i class="fa-solid fa-minus"></i>
                                <span>{{ $isAr ? 'محاكاة حضور حصة (-1)' : 'Attend Class (-1)' }}</span>
                            </button>
                            <button type="button" onclick="simulateCreditRefund()"
                                class="px-3.5 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-mono font-bold border border-emerald-500/30 flex items-center gap-1.5 cursor-pointer transition-all">
                                <i class="fa-solid fa-plus"></i>
                                <span>{{ $isAr ? 'محاكاة قبول عذر (+1)' : 'Approve Excuse (+1)' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('packages')"
                        class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-teal-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'عرض تفاصيل باقتي الحالية' : 'View My Active Package' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 6: EXCUSES & EXCEPTIONS                                   --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-exceptions" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg shrink-0 border border-orange-200 dark:border-orange-800">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'كيف تقدم عذر غياب أو تطلب استثناء واجب؟' : 'How to submit absence excuses and assignment exceptions?' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'خطوات التقديم الإلكتروني، إرفاق التقارير الطبية، ومتابعة قرار الإدارة والمعلم.' : 'Electronic excuse submissions, medical attachments, and tracking real-time admin decisions.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'اختيار المقرر والحصة' : 'Select Session Target' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اختر الكورس ثم حدد تاريخ الحصة أو الواجب الذي تعذر عليك حضوره.' : 'Pick your course from the dynamic dropdown to populate target sessions automatically.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'توضيح السبب والمستندات' : 'Explain Reason' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'اكتب تفاصيل سبب الغياب (مرضي، سفر، عطل تقني) لضمان سرعة قبول الطلب.' : 'Provide a clear explanation to assist academy administration with fast evaluation.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'المتابعة اللحظية للحالة' : 'Track Approval Status' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'تتحول حالة الطلب في جدولك من "قيد المراجعة" إلى "مقبول" فور اعتماده مع إشعار فوري.' : 'Status transitions from Pending to Approved with immediate FCM push notification.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Excuse Stepper & Timeline Preview --}}
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-orange-200 dark:border-orange-800/80 shadow-md space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                        <span class="font-mono text-xs font-black uppercase text-orange-600 dark:text-orange-400 flex items-center gap-2">
                            <i class="fa-solid fa-route"></i>
                            <span>{{ $isAr ? 'محاكاة دورة حياة طلب العذر والاستثناء' : 'Excuse Lifecycle Stepper Demo' }}</span>
                        </span>
                        <span class="text-[10px] font-mono font-bold bg-orange-100 dark:bg-orange-950 text-orange-700 dark:text-orange-300 px-2.5 py-0.5 rounded-full">
                            {{ $isAr ? 'مسار الاعتماد' : 'Workflow' }}
                        </span>
                    </div>

                    {{-- Stepper UI --}}
                    <div class="grid grid-cols-3 gap-2 text-center pt-2">
                        <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-black text-[10px] flex items-center justify-center mx-auto mb-1">✓</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? '1. إرسال الطلب' : '1. Submitted' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'بواسطة الطالب' : 'By Student' }}</span>
                        </div>

                        <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800">
                            <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] flex items-center justify-center mx-auto mb-1 animate-pulse">2</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? '2. المراجعة' : '2. Under Review' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'المعلم والإدارة' : 'Faculty Staff' }}</span>
                        </div>

                        <div class="p-3 rounded-2xl bg-teal-50 dark:bg-teal-950/60 border border-teal-300 dark:border-teal-800">
                            <span class="w-6 h-6 rounded-full bg-teal-600 text-white font-black text-[10px] flex items-center justify-center mx-auto mb-1">3</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? '3. حفظ الرصيد' : '3. Credit Saved' }}</span>
                            <span class="text-[10px] text-teal-600 font-mono font-bold">{{ $isAr ? 'اعتماد رسمي' : 'Approved' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('exceptions')"
                        class="btn-lift px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-slate-950 text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-orange-500/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى سجل الأعذار والاستثناءات' : 'Go to Excuses & Exceptions' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 7: NOTIFICATIONS & REAL-TIME ALERTS                       --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-notifications" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-lg shrink-0 border border-cyan-200 dark:border-cyan-800">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'مركز الإشعارات والتنبيهات الحية (Real-Time)' : 'Live Notifications & Real-Time Push Feed' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'كيف تستقبل التنبيهات الفورية عند بدء المعلم للبث أو إضافة حصة أو اعتماد عذر.' : 'Stay updated with automated web push and live feeds for session launches and grades.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-cyan-600 dark:text-cyan-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/60 flex items-center justify-center text-[10px] font-black">1</span>
                                <span>{{ $isAr ? 'تنبيه بدء البث المباشر' : 'Live Class Launched' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'إشعار فوري صوتي ومرئي ينقلك مباشرة بضغطة زر إلى قاعة البث.' : 'Instant chime and popup alerting you the moment your instructor launches the stream.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-cyan-600 dark:text-cyan-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/60 flex items-center justify-center text-[10px] font-black">2</span>
                                <span>{{ $isAr ? 'تنبيهات الواجبات الجديدة' : 'New Assignment Alerts' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'إشعار مباشر بتفاصيل الواجب الجديد والوقت المتبقي لتسليمه.' : 'Notification highlighting title, deadline, and total score points of newly posted homework.' }}
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                            <div class="flex items-center gap-2 text-cyan-600 dark:text-cyan-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/60 flex items-center justify-center text-[10px] font-black">3</span>
                                <span>{{ $isAr ? 'ملاحظات المعلم الخاصة' : 'Personal Teacher Notes' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ $isAr ? 'قسم مخصص للتوجيهات الفردية والنصائح الأكاديمية الخاصة بولي الأمر والطالب.' : 'Direct messages and pedagogical guidance addressed directly to you from your teachers.' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- LIVE INTERACTIVE DEMO SIMULATOR: Trigger Demo Notification Feed --}}
                <div class="p-4 sm:p-5 rounded-3xl bg-slate-900 text-white border border-cyan-500/30 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <span class="font-mono text-xs font-black uppercase text-cyan-300 flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ $isAr ? 'محاكاة تفاعلية: تجربة استقبال إشعار حي فوري' : 'Live Interactive Notification Simulator' }}</span>
                        </span>
                        <button type="button" onclick="triggerDemoNotification()"
                            class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-mono font-black flex items-center gap-1.5 transition-all cursor-pointer shadow-md">
                            <i class="fa-solid fa-bell"></i>
                            <span>{{ $isAr ? 'إرسال إشعار تجريبي الآن' : 'Test Alert Now' }}</span>
                        </button>
                    </div>

                    {{-- Live Simulated Feed Container --}}
                    <div id="demoNotifContainer" class="space-y-2">
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 space-y-1">
                            <div class="flex justify-between items-center text-[10px] font-mono text-teal-400 font-bold">
                                <span><i class="fa-solid fa-satellite-dish"></i> {{ $isAr ? 'جلسة بث مباشر جديدة' : 'Session Launched' }}</span>
                                <span class="text-slate-400">{{ $isAr ? 'منذ قليل' : 'Just now' }}</span>
                            </div>
                            <h6 class="font-bold text-xs text-white">{{ $isAr ? 'بدأ د. أحمد الصالح بث مراجعة الفيزياء للثانوية العامة' : 'Dr. Ahmed launched the live Physics Review stream.' }}</h6>
                            <p class="text-[11px] text-slate-300 font-mono">{{ $isAr ? 'اضغط لدخول قاعة البث المباشر فوراً.' : 'Click to enter the live classroom immediately.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('notifications')"
                        class="btn-lift px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-cyan-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى مركز الإشعارات' : 'Go to Notifications Hub' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────────── --}}
            {{-- TAB 8: OVERVIEW & GENERAL COCKPIT                             --}}
            {{-- ───────────────────────────────────────────────────────────── --}}
            <div id="stu-guide-content-overview" class="stu-guide-pane hidden space-y-6">
                <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0 border border-purple-200 dark:border-purple-800">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'لوحة القيادة الرئيسية والمؤشرات الأكاديمية' : 'Overview Dashboard & Academic KPI Cockpit' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'نظرة شمولية على معدل حضورك، رصيد باقتك، الحصص القادمة، واختصارات سريعة لكافة الخدمات.' : 'Comprehensive summary of student performance, quick action triggers, and upcoming session roadmap.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-teal-600 uppercase block">{{ $isAr ? 'رصيد الباقة' : 'Active Credits' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'رصيد الحصص المتبقي للاستهلاك' : 'Remaining session balance' }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-indigo-600 uppercase block">{{ $isAr ? 'الحصص القادمة' : 'Upcoming Classes' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'الحصص المؤكدة للأيام المقبلة' : 'Planned live meetings' }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-amber-600 uppercase block">{{ $isAr ? 'الواجبات المعلقة' : 'Pending Homework' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'واجبات يلزم حلها قبل الموعد' : 'Tasks waiting for solution' }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800">
                            <span class="text-[10px] font-mono font-bold text-purple-600 uppercase block">{{ $isAr ? 'الكورسات المفعلة' : 'Enrolled Courses' }}</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">{{ $isAr ? 'المقررات المسجلة في حسابك' : 'All active study modules' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" onclick="switchStudentTabAndCloseGuide('overview')"
                        class="btn-lift px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-md shadow-purple-600/20 cursor-pointer">
                        <span>{{ $isAr ? 'الانتقال إلى اللوحة الرئيسية' : 'Go to Overview Cockpit' }}</span>
                        <i class="fa-solid fa-arrow-{{ $isAr ? 'left' : 'right' }} text-xs"></i>
                    </button>
                </div>
            </div>

        </div>

        {{-- Sticky Footer with Academy Branding & Close Button --}}
        <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 rounded-b-[28px]">
            <div class="text-xs font-mono text-slate-500 hidden sm:flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-teal-600"></i>
                <span>{{ $isAr ? 'أكاديمية النخبة التعليمية • دليل وتجربة الطالب التفاعلية' : 'Elite Academy LMS • Student Interactive Guidance Suite' }}</span>
            </div>
            <div class="flex items-center gap-2.5 ms-auto">
                <button type="button" onclick="closeStudentPreviewGuide()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    {{ __('Close') }}
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT CONTROLLER FOR STUDENT GUIDE & DEMO SIMULATORS                   --}}
{{-- ════════════════════════════════════════════════════════════════════════════ --}}
<script>
    // ── Student Preview Guide Tab Switcher ────────────────────────────────────
    window.switchStudentGuideTab = function(tabKey) {
        document.querySelectorAll('.stu-guide-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.stu-guide-tab-btn').forEach(btn => {
            btn.className = 'stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 cursor-pointer';
        });

        const activeContent = document.getElementById(`stu-guide-content-${tabKey}`);
        if (activeContent) activeContent.classList.remove('hidden');

        const activeBtn = document.getElementById(`stu-guide-tab-${tabKey}`);
        if (activeBtn) {
            activeBtn.className = 'stu-guide-tab-btn px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-2 bg-teal-600 text-white shadow-sm cursor-pointer';
        }
    };

    window.openStudentPreviewGuide = function(initialTab = 'sessions') {
        window.switchStudentGuideTab(initialTab);
        if (typeof window.openModal === 'function') {
            window.openModal('studentPreviewGuideModal');
        } else {
            const modal = document.getElementById('studentPreviewGuideModal');
            if (modal) modal.classList.remove('hidden');
        }
    };

    window.closeStudentPreviewGuide = function() {
        if (typeof window.closeModal === 'function') {
            window.closeModal('studentPreviewGuideModal');
        } else {
            const modal = document.getElementById('studentPreviewGuideModal');
            if (modal) modal.classList.add('hidden');
        }
    };

    window.switchStudentTabAndCloseGuide = function(tabKey) {
        window.closeStudentPreviewGuide();
        if (typeof window.switchStudentTab === 'function') {
            window.switchStudentTab(tabKey);
        }
    };

    // ── Interactive Simulators Logic ───────────────────────────────────────────
    
    // 1. Live Class Device Toggles Simulator
    window.toggleDemoDevice = function(btnId, iconOn, iconOff) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (icon.classList.contains(iconOn)) {
            icon.className = `fa-solid ${iconOff}`;
            btn.classList.remove('bg-teal-600/30', 'text-teal-300');
            btn.classList.add('bg-rose-500/30', 'text-rose-300');
            if (window.Toast) window.Toast.info(isArLocale ? 'تم كتم الجهاز في المحاكاة' : 'Device muted in simulation');
        } else {
            icon.className = `fa-solid ${iconOn}`;
            btn.classList.remove('bg-rose-500/30', 'text-rose-300');
            btn.classList.add('bg-teal-600/30', 'text-teal-300');
            if (window.Toast) window.Toast.success(isArLocale ? 'الجهاز نشط وجاهز للبث' : 'Device active and ready');
        }
    };

    window.playDemoSoundCheck = function() {
        if (window.Toast) {
            window.Toast.success(isArLocale ? 'جهاز الصوت وسماعات الأذن تعمل بكفاءة عالية 100%' : 'Audio speakers operating with 100% clarity!', 'Sound Test Passed');
        }
    };

    window.triggerDemoClassroomJoin = function() {
        const btn = document.getElementById('demoJoinBtn');
        const btnText = document.getElementById('demoJoinBtnText');
        const statusNote = document.getElementById('demoClassStatus');
        
        if (btnText) btnText.textContent = isArLocale ? 'جاري الاتصال بالقاعة الآمنة...' : 'Connecting to Live Stream...';
        if (btn) btn.classList.add('opacity-75', 'cursor-not-allowed');

        setTimeout(() => {
            if (btnText) btnText.textContent = isArLocale ? 'أنت الآن متصل بالفصل الافتراضي!' : 'Connected to Classroom!';
            if (btn) {
                btn.className = 'btn-lift w-full sm:w-auto px-5 py-2.5 bg-emerald-500 text-slate-950 font-black text-xs rounded-xl shadow-lg flex items-center justify-center gap-2 cursor-default';
            }
            if (statusNote) {
                statusNote.innerHTML = `<span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check"></i> ${isArLocale ? 'تم تسجيل حضورك بنجاح في الجلسة المباشرة' : 'Attendance verified and logged!'}</span>`;
            }
            if (window.Toast) {
                window.Toast.success(isArLocale ? 'تمت المحاكاة بنجاح: تم توثيق حضورك وتفعيل البث المباشر!' : 'Simulation Success: Classroom joined & attendance logged!');
            }
        }, 900);
    };

    // 2. Interactive Course Progress Simulator
    window.setDemoCourseProgress = function(pct) {
        const text = document.getElementById('demoCourseProgressText');
        const bar = document.getElementById('demoCourseProgressBar');
        if (text) text.textContent = `${pct}%`;
        if (bar) bar.style.width = `${pct}%`;
        if (window.Toast) {
            window.Toast.info(`${isArLocale ? 'تم تحديث نسبة الإنجاز إلى' : 'Progress updated to'} ${pct}%`);
        }
    };

    // 3. Interactive Quiz Question Simulator
    window.selectDemoQuizOption = function(btnEl, isCorrect) {
        const allOpts = document.querySelectorAll('.demo-quiz-opt');
        allOpts.forEach(b => {
            b.className = 'demo-quiz-opt w-full text-start p-3 rounded-xl bg-slate-800/80 border border-slate-700 text-xs font-mono text-slate-200 transition-all flex items-center justify-between opacity-60 cursor-not-allowed';
            b.disabled = true;
            const fb = b.querySelector('.opt-feedback');
            if (fb) fb.classList.add('hidden');
        });

        const resultBox = document.getElementById('demoQuizResult');
        const feedbackSpan = btnEl.querySelector('.opt-feedback');

        if (isCorrect) {
            btnEl.className = 'demo-quiz-opt w-full text-start p-3 rounded-xl bg-emerald-950/80 border-2 border-emerald-400 text-xs font-mono text-emerald-200 transition-all flex items-center justify-between';
            if (feedbackSpan) {
                feedbackSpan.textContent = isArLocale ? '✓ إجابة صحيحة (+2)' : '✓ Correct (+2 Pts)';
                feedbackSpan.className = 'opt-feedback text-[11px] font-bold text-emerald-300';
                feedbackSpan.classList.remove('hidden');
            }
            if (resultBox) {
                resultBox.className = 'p-3 rounded-xl text-xs font-bold font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                resultBox.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${isArLocale ? 'أحسنت! إجابة صحيحة تماماً. الميتوكوندريا هي مصنع الطاقة بالخلية.' : 'Excellent! Mitochondria generate most of the chemical energy needed by cells.'}`;
                resultBox.classList.remove('hidden');
            }
            if (window.Toast) window.Toast.success(isArLocale ? 'إجابة صحيحة! نلت الدرجة الكاملة 100%' : 'Correct Answer! +2 Points');
        } else {
            btnEl.className = 'demo-quiz-opt w-full text-start p-3 rounded-xl bg-rose-950/80 border-2 border-rose-400 text-xs font-mono text-rose-200 transition-all flex items-center justify-between';
            if (feedbackSpan) {
                feedbackSpan.textContent = isArLocale ? '✗ إجابة غير دقيقة' : '✗ Incorrect';
                feedbackSpan.className = 'opt-feedback text-[11px] font-bold text-rose-300';
                feedbackSpan.classList.remove('hidden');
            }
            if (resultBox) {
                resultBox.className = 'p-3 rounded-xl text-xs font-bold font-mono bg-rose-500/20 text-rose-300 border border-rose-500/40';
                resultBox.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> ${isArLocale ? 'إجابة خاطئة. الإجابة الصحيحة هي: (الميتوكوندريا).' : 'Incorrect. The correct answer is Mitochondria.'}`;
                resultBox.classList.remove('hidden');
            }
            if (window.Toast) window.Toast.error(isArLocale ? 'إجابة خاطئة، جرب مراجعة ملخص الدرس.' : 'Incorrect option selected.');
        }
    };

    // 4. Interactive Credit Wallet Simulator
    let demoCredits = 8;
    window.simulateCreditDeduction = function() {
        if (demoCredits <= 0) {
            if (window.Toast) window.Toast.warning(isArLocale ? 'رصيد الحصص نفد! يلزم تجديد الباقة.' : 'Zero credits remaining!');
            return;
        }
        demoCredits--;
        updateDemoCreditUI();
        if (window.Toast) window.Toast.info(isArLocale ? 'تم خصم حصة واحدة (-1) لحضورك البث المباشر' : '1 Credit deducted for attending live class');
    };

    window.simulateCreditRefund = function() {
        if (demoCredits >= 12) {
            if (window.Toast) window.Toast.info(isArLocale ? 'الرصيد في الحد الأقصى (12 حصة)' : 'Max credits reached');
            return;
        }
        demoCredits++;
        updateDemoCreditUI();
        if (window.Toast) window.Toast.success(isArLocale ? 'تمت الموافقة على العذر واسترجاع رصيد الحصة (+1)' : 'Excuse approved: +1 credit refunded to balance');
    };

    function updateDemoCreditUI() {
        const counter = document.getElementById('demoCreditCounter');
        const note = document.getElementById('demoCreditStatusNote');
        if (counter) counter.textContent = demoCredits;
        if (note) {
            if (demoCredits <= 2) {
                note.textContent = isArLocale ? 'تنبيه: الرصيد منخفض جداً، يرجى تجديد الباقة.' : 'Warning: Balance low, renewal recommended.';
                note.className = 'text-[11px] text-rose-400 font-mono font-bold';
            } else {
                note.textContent = isArLocale ? 'حالتك ممتازة، الرصيد يغطي جميع حصص الشهر الحالي.' : 'Balance is healthy for this month.';
                note.className = 'text-[11px] text-slate-400 font-mono';
            }
        }
    }

    // 5. Interactive Test Notification Trigger
    window.triggerDemoNotification = function() {
        const container = document.getElementById('demoNotifContainer');
        if (!container) return;

        const card = document.createElement('div');
        card.className = 'p-3.5 rounded-2xl bg-cyan-950/80 border border-cyan-400/60 space-y-1 animate-in fade-in slide-in-from-top-2 duration-300';
        card.innerHTML = `
            <div class="flex justify-between items-center text-[10px] font-mono text-cyan-300 font-bold">
                <span><i class="fa-solid fa-bolt text-amber-400"></i> ${isArLocale ? 'إشعار فوري جديد (FCM)' : 'Live Push Alert'}</span>
                <span class="text-cyan-400">${isArLocale ? 'الآن' : 'Just now'}</span>
            </div>
            <h6 class="font-bold text-xs text-white">${isArLocale ? 'تم اعتماد عذر الغياب لحصة الرياضيات' : 'Excuse Approved for Advanced Math'}</h6>
            <p class="text-[11px] text-slate-300 font-mono">${isArLocale ? 'وافقت الإدارة على طلب العذر وحفظت رصيد حصتك بالكامل.' : 'Administration approved your excuse request. Credit preserved.'}</p>
        `;
        container.prepend(card);

        if (window.Toast) {
            window.Toast.success(isArLocale ? 'وصلك إشعار فوري جديد على شاشتك!' : 'New live push notification delivered!');
        }
    };
</script>
