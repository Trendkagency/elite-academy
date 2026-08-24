<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['session', 'user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['session', 'user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $teacherUser = $session->teacherProfile?->user;
    $teacherName = $teacherUser?->name ?: 'Ahmed Mohamed';
    $teacherAvatar = $teacherUser?->profile_photo ? media_url($teacherUser->profile_photo) : asset('images/instructor_male.png');
    $studentAvatar = $user->profile_photo ? media_url($user->profile_photo) : asset('images/hero_male_student.png');
    $subjectName = $session->subject?->name ?: ($isRtl ? 'الفيزياء' : 'Physics');
    $sessionTitle = $session->title ?: ($isRtl ? 'الفيزياء — الموجات الكهرومغناطيسية' : 'Physics — Electromagnetic Waves');
?>

<div id="lmsMeetingWrapper" class="flex flex-col lg:flex-row h-screen w-full bg-[#0b0f19] text-slate-100 font-sans overflow-hidden select-none">
    
    
    <aside id="lmsSidebar" class="w-full lg:w-64 bg-[#111625] border-r border-slate-800/80 flex flex-col justify-between shrink-0 transition-all duration-300 z-30">
        <div class="p-4 space-y-6 overflow-y-auto">
            
            <div class="flex items-center justify-between">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white font-heading font-black text-lg flex items-center justify-center shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                        🎓
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-heading font-black text-lg text-white tracking-tight">Elite Academy</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">LMS</span>
                    </div>
                </a>
                <button class="lg:hidden text-slate-400 hover:text-white p-1" onclick="document.getElementById('lmsSidebar').classList.toggle('hidden')">
                    ✕
                </button>
            </div>

            
            <nav class="space-y-1 text-xs font-semibold">
                <a href="<?php echo e(route('student.meeting.show', ['id' => $session->id])); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/20 transition-all">
                    <span class="text-base">📹</span>
                    <span class="flex-1"><?php echo e($isRtl ? 'الحصة المباشرة' : 'Live Class'); ?></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">🏠</span>
                    <span><?php echo e($isRtl ? 'لوحة التحكم' : 'Dashboard'); ?></span>
                </a>

                <a href="<?php echo e(route('courses')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">📚</span>
                    <span><?php echo e($isRtl ? 'الكورسات' : 'Courses'); ?></span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">📝</span>
                    <span><?php echo e($isRtl ? 'الواجبات' : 'Assignments'); ?></span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">📅</span>
                    <span><?php echo e($isRtl ? 'التقويم' : 'Calendar'); ?></span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-base">💬</span>
                        <span><?php echo e($isRtl ? 'الرسائل' : 'Messages'); ?></span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-500 text-white">3</span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">🎥</span>
                    <span><?php echo e($isRtl ? 'التسجيلات' : 'Recordings'); ?></span>
                </a>

                <a href="<?php echo e(route('student-portal')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">📊</span>
                    <span><?php echo e($isRtl ? 'التقارير' : 'Reports'); ?></span>
                </a>

                <a href="<?php echo e(route('student.profile')); ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-all">
                    <span class="text-base">⚙️</span>
                    <span><?php echo e($isRtl ? 'الإعدادات' : 'Settings'); ?></span>
                </a>
            </nav>
        </div>

        
        <div class="p-3 border-t border-slate-800/80 bg-[#0e121e]">
            <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/50 transition-all cursor-pointer">
                <div class="flex items-center gap-3">
                    <img src="<?php echo e($studentAvatar); ?>" alt="<?php echo e($user->name); ?>" class="w-9 h-9 rounded-full object-cover border border-slate-700">
                    <div class="text-xs">
                        <p class="font-bold text-white leading-tight truncate max-w-[110px]"><?php echo e($user->name); ?></p>
                        <p class="text-[11px] text-slate-400 font-mono"><?php echo e($isRtl ? 'طالب' : 'Student'); ?></p>
                    </div>
                </div>
                <span class="text-slate-500 text-xs">▼</span>
            </div>
        </div>
    </aside>

    
    <main class="flex-1 flex flex-col min-w-0 bg-[#0b0f19] overflow-y-auto">
        
        
        <header class="px-6 py-3.5 bg-[#111625] border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-20 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-heading font-bold text-base sm:text-lg text-white tracking-tight">
                            <?php echo e($sessionTitle); ?>

                        </h1>
                        <span id="meetingStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                            <span id="meetingStatusText">Live</span>
                        </span>
                    </div>
                    <p class="text-xs font-mono text-slate-400 mt-0.5">
                        <?php echo e($isRtl ? 'المعلم:' : 'Teacher:'); ?> <span class="text-slate-200 font-semibold"><?php echo e($teacherName); ?></span> 
                        <span class="mx-1.5 text-slate-600">|</span> 
                        <span class="text-emerald-400 font-bold">🟢 <span id="meetingDurationTimer">00:24:18</span></span>
                    </p>
                </div>
            </div>

            
            <div class="flex items-center gap-2 sm:gap-3 text-xs">
                <button class="px-3 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 text-slate-200 rounded-lg font-medium transition-all flex items-center gap-1.5">
                    <span>🎛️</span> <?php echo e($isRtl ? 'التخطيط' : 'Layout'); ?>

                </button>
                <button class="p-2 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 text-slate-200 rounded-lg transition-all" title="Settings">
                    ⚙️
                </button>
                <button onclick="toggleRightSidebar()" class="px-3 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/60 text-slate-200 rounded-lg font-medium transition-all flex items-center gap-1.5">
                    <span>👥</span> <span id="participantsHeaderCount">24</span>
                </button>
                <button onclick="leaveMeetingSession()" class="btn-lift px-4 py-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-rose-600/20 flex items-center gap-1.5">
                    <span>📞</span> <?php echo e($isRtl ? 'مغادرة' : 'Leave'); ?>

                </button>
            </div>
        </header>

        
        <div class="p-4 sm:p-6 space-y-5 flex-1">
            
            
            <div class="relative w-full aspect-video min-h-[380px] max-h-[640px] bg-slate-950 rounded-2xl border border-slate-800/80 shadow-2xl overflow-hidden flex items-center justify-center">
                
                
                <div class="absolute top-4 left-4 z-20 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center text-xs shadow-md">
                        ✓
                    </span>
                </div>

                
                <div id="dynamicWatermark" class="absolute inset-0 pointer-events-none z-30 overflow-hidden flex items-center justify-center opacity-60 select-none">
                    <div id="watermarkContent" class="text-teal-300 font-mono font-black text-xs tracking-widest uppercase transition-all duration-1000 transform -rotate-12 bg-slate-950/80 px-3.5 py-1.5 rounded-xl border border-teal-500/40 shadow-2xl backdrop-blur-md">
                        🛡️ <?php echo e($user->name); ?> • STU-<?php echo e(str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)); ?> • SES-<?php echo e(str_pad((string) $session->id, 5, '0', STR_PAD_LEFT)); ?> • <span id="watermarkClock"></span>
                    </div>
                </div>

                
                <div id="screenRecordSecurityOverlay" class="hidden absolute inset-0 z-50 bg-slate-950/95 backdrop-blur-3xl flex flex-col items-center justify-center p-6 text-center space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-2xl shadow-xl animate-bounce">
                        🔒
                    </div>
                    <div class="space-y-1 max-w-md">
                        <h4 id="securityBlurTitle" class="font-heading font-bold text-base text-white">
                            <?php echo e($isRtl ? 'محتوى محمي — تم رصد محاولة تسجيل أو تصوير الشاشة' : 'Protected Content — Screen Recording/Capture Detected'); ?>

                        </h4>
                        <p id="securityBlurMsg" class="text-xs font-mono text-slate-400">
                            <?php echo e($isRtl ? 'لحماية الملكية الفكرية، تم تعتيم الشاشة فوراً.' : 'To protect IP rights, playback is obfuscated.'); ?>

                        </p>
                    </div>
                    <button onclick="resumeMeetingPlayback()" class="mt-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 px-5 py-2 rounded-xl shadow-lg transition-all cursor-pointer font-mono">
                        <?php echo e($isRtl ? 'العودة لمتابعة الحصة ▶' : 'Resume Session Playback ▶'); ?>

                    </button>
                </div>

                
                <div id="meetingStateOverlay" class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-6 text-center space-y-4">
                    <div id="meetingSpinner" class="w-12 h-12 border-4 border-indigo-500/30 border-t-indigo-500 rounded-full animate-spin"></div>
                    <div class="space-y-1">
                        <h4 id="overlayTitle" class="font-heading font-bold text-lg text-white">
                            <?php echo e($isRtl ? 'جاري الاتصال بالغرفة المباشرة...' : 'Connecting to Live Session...'); ?>

                        </h4>
                        <p id="overlayMessage" class="text-xs font-mono text-slate-400 max-w-md">
                            <?php echo e($isRtl ? 'يتم التحقق من هوية الطالب ورصيد الحصص...' : 'Verifying student identity & stream tokens...'); ?>

                        </p>
                    </div>
                    <button id="btnRetryMeeting" onclick="initializeInSystemMeeting()" class="hidden px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold font-mono transition-all shadow-lg">
                        🔄 <?php echo e($isRtl ? 'إعادة المحاولة' : 'Retry Connection'); ?>

                    </button>
                </div>

                
                <div id="teacherStreamCanvas" class="w-full h-full relative z-10 flex items-center justify-center bg-slate-900 overflow-hidden">
                    <img src="<?php echo e($teacherAvatar); ?>" alt="<?php echo e($teacherName); ?>" class="w-full h-full object-cover opacity-90 scale-105 transition-all">
                    
                    
                    <div class="absolute bottom-4 left-4 z-20 px-3 py-1.5 rounded-xl bg-slate-900/80 backdrop-blur-md border border-slate-700/60 text-xs font-semibold text-white flex items-center gap-2 shadow-lg">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span><?php echo e($teacherName); ?> (<?php echo e($isRtl ? 'المعلم' : 'Teacher'); ?>)</span>
                    </div>
                </div>

                
                <div id="studentPipContainer" class="absolute bottom-4 right-4 z-20 w-44 sm:w-56 aspect-video bg-slate-900 rounded-2xl border border-slate-700/80 shadow-2xl overflow-hidden group transition-all">
                    <img src="<?php echo e($studentAvatar); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover">
                    
                    <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between px-2 py-1 rounded-lg bg-slate-950/80 backdrop-blur-sm text-[10px] font-medium text-white">
                        <span class="truncate max-w-[100px]"><?php echo e($isRtl ? 'أنت (الطالب)' : 'You (Student)'); ?></span>
                        <span id="studentMicPipBadge" class="text-rose-400">🎙️✕</span>
                    </div>
                </div>

                
                <iframe id="embeddedMeetingFrame" src="about:blank" class="w-full h-full border-0 relative z-10 hidden" allow="camera *; microphone *; display-capture *; autoplay *; encrypted-media *; fullscreen *" allowfullscreen></iframe>

                
                <div id="externalMeetingLauncher" class="hidden w-full h-full flex flex-col items-center justify-center p-6 text-center space-y-4 bg-slate-950 relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-3xl shadow-xl">
                        📹
                    </div>
                    <div class="space-y-1 max-w-lg">
                        <h4 id="externalPlatformTitle" class="font-heading font-extrabold text-xl text-white">
                            <?php echo e($isRtl ? 'غرفة البث المباشر — Google Meet' : 'Interactive Live Stream Room'); ?>

                        </h4>
                        <p class="text-xs font-mono text-slate-400">
                            <?php echo e($isRtl ? 'يتم تسجيل الحضور تلقائياً. يرجى إبقاء هذه الصفحة مفتوحة.' : 'Attendance & duration recorded automatically. Keep this tab open.'); ?>

                        </p>
                    </div>
                    <a id="btnExternalLaunch" href="#" target="_blank" rel="noopener noreferrer" class="btn-lift px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-xl flex items-center gap-2">
                        <span>🟢</span> <span id="externalLaunchBtnText"><?php echo e($isRtl ? 'انضم للبث عبر Google Meet 🚀' : 'Join via Google Meet 🚀'); ?></span>
                    </a>
                </div>

                
                <div id="zoomMeetingContainer" class="w-full h-full relative z-10 hidden"></div>
            </div>

            
            <div class="flex items-center justify-center">
                <div class="bg-[#121725]/90 border border-slate-800/80 backdrop-blur-xl p-2 rounded-2xl flex items-center justify-center gap-2 sm:gap-4 shadow-2xl">
                    
                    <button id="btnMicToggle" onclick="toggleMic()" class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span id="micIcon" class="text-base">🎙️</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'المايك' : 'Mic'); ?></span>
                    </button>

                    
                    <button id="btnCamToggle" onclick="toggleCamera()" class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span id="camIcon" class="text-base">📹</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'الكاميرا' : 'Camera'); ?></span>
                    </button>

                    
                    <button id="btnShareToggle" onclick="toggleShareScreen()" class="w-16 h-14 rounded-xl bg-indigo-600 text-white flex flex-col items-center justify-center gap-1 transition-all shadow-lg shadow-indigo-600/30">
                        <span class="text-base">🖥️</span>
                        <span class="text-[10px] font-bold"><?php echo e($isRtl ? 'مشاركة الشاشة' : 'Share Screen'); ?></span>
                    </button>

                    
                    <button id="btnChatToggle" onclick="toggleRightSidebar()" class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span class="text-base">💬</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'المحادثة' : 'Chat'); ?></span>
                    </button>

                    
                    <button id="btnRaiseHand" onclick="raiseHand()" class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span id="handIcon" class="text-base">✋</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'رفع اليد' : 'Raise Hand'); ?></span>
                    </button>

                    
                    <button id="btnRecordToggle" onclick="toggleRecording()" class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span id="recordIcon" class="text-base">🔘</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'تسجيل' : 'Record'); ?></span>
                    </button>

                    
                    <button class="w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all">
                        <span class="text-base">•••</span>
                        <span class="text-[10px] font-semibold"><?php echo e($isRtl ? 'المزيد' : 'More'); ?></span>
                    </button>

                    
                    <button onclick="leaveMeetingSession()" class="w-12 h-12 rounded-full bg-rose-600 hover:bg-rose-500 text-white flex items-center justify-center text-lg shadow-lg shadow-rose-600/30 transition-transform hover:scale-105 active:scale-95 ml-1">
                        📞
                    </button>
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                
                <div class="bg-[#121725] rounded-2xl border border-slate-800/80 p-4 space-y-3 flex flex-col justify-between shadow-xl">
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                            <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                                <span>📝</span> <?php echo e($isRtl ? 'ملاحظات الحصة' : 'Class Notes'); ?>

                            </h3>
                            <span id="notesSavedStatus" class="text-[11px] font-mono text-emerald-400 flex items-center gap-1">
                                ✓ <?php echo e($isRtl ? 'تم الحفظ' : 'Saved'); ?>

                            </span>
                        </div>

                        
                        <div class="flex items-center gap-2 pt-2 text-xs font-mono text-slate-400">
                            <button onclick="formatNote('bold')" class="w-6 h-6 rounded hover:bg-slate-800 hover:text-white font-bold">B</button>
                            <button onclick="formatNote('italic')" class="w-6 h-6 rounded hover:bg-slate-800 hover:text-white italic">I</button>
                            <button onclick="formatNote('underline')" class="w-6 h-6 rounded hover:bg-slate-800 hover:text-white underline">U</button>
                            <button onclick="formatNote('insertUnorderedList')" class="w-6 h-6 rounded hover:bg-slate-800 hover:text-white">≡</button>
                        </div>

                        
                        <div id="classNotesEditor" contenteditable="true" oninput="saveNotesLocally()" class="mt-3 text-xs text-slate-300 space-y-1.5 focus:outline-none min-h-[110px] leading-relaxed">
                            <p class="font-semibold text-white"><?php echo e($isRtl ? 'موضوعات اليوم:' : 'Today we will cover:'); ?></p>
                            <ul class="list-disc list-inside space-y-0.5 text-slate-400">
                                <li><?php echo e($isRtl ? 'مقدمة في الموجات الكهرومغناطيسية' : 'Introduction to EM Waves'); ?></li>
                                <li><?php echo e($isRtl ? 'الخصائص الفيزيائية' : 'Properties'); ?></li>
                                <li><?php echo e($isRtl ? 'معادلة الموجات' : 'Wave Equation'); ?></li>
                                <li><?php echo e($isRtl ? 'أمثلة وتطبيقات' : 'Examples'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="bg-[#121725] rounded-2xl border border-slate-800/80 p-4 space-y-3 shadow-xl">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                        <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                            <span>📁</span> <?php echo e($isRtl ? 'المحتوى المشارك' : 'Shared Content'); ?>

                        </h3>
                    </div>

                    
                    <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center gap-3 hover:border-slate-700 transition-all cursor-pointer">
                        <div class="w-9 h-9 rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-lg">
                            📄
                        </div>
                        <div class="flex-1 min-w-0 text-xs">
                            <p class="font-bold text-white truncate">EM_Waves_Notes.pdf</p>
                            <p class="text-[10px] text-slate-400 font-mono">2.4 MB</p>
                        </div>
                    </div>

                    
                    <div class="rounded-xl border border-slate-800 overflow-hidden bg-slate-950 p-2.5 relative group">
                        <div class="w-full h-20 rounded-lg bg-slate-900 flex flex-col items-center justify-center p-2 font-mono text-[10px] text-indigo-300 space-y-1">
                            <p class="font-bold">E = E₀ sin(kx - ωt)</p>
                            <p class="text-slate-400">B = B₀ sin(kx - ωt)</p>
                            <div class="w-full border-t border-slate-800 pt-1 text-center text-slate-500">c = 1 / √(μ₀ε₀)</div>
                        </div>
                    </div>
                </div>

                
                <div class="bg-[#121725] rounded-2xl border border-slate-800/80 p-4 space-y-3 flex flex-col justify-between shadow-xl">
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                            <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                                <span>📊</span> <?php echo e($isRtl ? 'استطلاع مباشر' : 'Live Poll'); ?>

                            </h3>
                            <button class="text-slate-400 hover:text-white text-xs">^</button>
                        </div>

                        <p class="text-xs font-semibold text-slate-200 mt-2 mb-3">
                            <?php echo e($isRtl ? 'ما هو الموضوع الذي ترغب بمراجعته أولاً؟' : 'What topic should we review next?'); ?>

                        </p>

                        
                        <div class="space-y-2.5 text-xs">
                            
                            <div onclick="votePoll('A')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 cursor-pointer space-y-1 transition-all">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="font-medium"><strong class="text-indigo-400">A.</strong> <?php echo e($isRtl ? 'معادلة الموجات' : 'Wave Equation'); ?></span>
                                    <span id="pollPctA" class="font-mono text-[11px] text-slate-400">12 (50%)</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                    <div id="pollBarA" class="h-full bg-indigo-500 transition-all duration-500" style="width: 50%"></div>
                                </div>
                            </div>

                            
                            <div onclick="votePoll('B')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 cursor-pointer space-y-1 transition-all">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="font-medium"><strong class="text-indigo-400">B.</strong> <?php echo e($isRtl ? 'خصائص الموجات' : 'Properties of Waves'); ?></span>
                                    <span id="pollPctB" class="font-mono text-[11px] text-slate-400">8 (33%)</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                    <div id="pollBarB" class="h-full bg-indigo-500 transition-all duration-500" style="width: 33%"></div>
                                </div>
                            </div>

                            
                            <div onclick="votePoll('C')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 cursor-pointer space-y-1 transition-all">
                                <div class="flex items-center justify-between text-slate-300">
                                    <span class="font-medium"><strong class="text-indigo-400">C.</strong> <?php echo e($isRtl ? 'المسائل والتمارين' : 'Numerical Problems'); ?></span>
                                    <span id="pollPctC" class="font-mono text-[11px] text-slate-400">4 (17%)</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                    <div id="pollBarC" class="h-full bg-indigo-500 transition-all duration-500" style="width: 17%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between text-[11px] font-mono text-slate-400">
                        <span id="pollTotalVotes">24 <?php echo e($isRtl ? 'صوت' : 'votes'); ?></span>
                        <span class="text-emerald-400 font-bold">• <?php echo e($isRtl ? 'نشط' : 'Active'); ?></span>
                    </div>
                </div>

            </div>
        </div>
    </main>

    
    <aside id="lmsRightSidebar" class="w-full lg:w-80 xl:w-96 bg-[#111625] border-l border-slate-800/80 flex flex-col h-full overflow-hidden shrink-0 z-20">
        
        
        <div class="p-4 border-b border-slate-800/80 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                    <span>👥</span> <?php echo e($isRtl ? 'المشاركون' : 'Participants'); ?> (<span id="participantsCountText">24</span>)
                </h3>
                <button class="text-slate-400 hover:text-white text-xs">^</button>
            </div>

            
            <div class="relative">
                <input type="text" id="participantSearchInput" placeholder="<?php echo e($isRtl ? 'بحث في المشاركين...' : 'Search participants'); ?>" oninput="filterParticipantsList()" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                <span class="absolute left-2.5 top-2 text-slate-500 text-xs">🔍</span>
            </div>

            
            <div id="participantsList" class="space-y-2 max-h-40 overflow-y-auto text-xs pr-1">
                
                <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-900 transition-all">
                    <div class="flex items-center gap-2.5">
                        <img src="<?php echo e($studentAvatar); ?>" class="w-7 h-7 rounded-full object-cover border border-slate-700">
                        <div>
                            <p class="font-bold text-white leading-none"><?php echo e($user->name); ?> (<?php echo e($isRtl ? 'أنت' : 'You'); ?>)</p>
                            <p class="text-[10px] text-slate-400"><?php echo e($isRtl ? 'طالب' : 'Student'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-400">
                        <span id="userMicIndicator" class="text-rose-400 text-xs">🎙️✕</span>
                        <span class="text-slate-500 cursor-pointer">⋮</span>
                    </div>
                </div>

                
                <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-900 transition-all">
                    <div class="flex items-center gap-2.5">
                        <img src="<?php echo e($teacherAvatar); ?>" class="w-7 h-7 rounded-full object-cover border border-slate-700">
                        <div>
                            <p class="font-bold text-white leading-none"><?php echo e($teacherName); ?></p>
                            <p class="text-[10px] text-slate-400"><?php echo e($isRtl ? 'المعلم' : 'Teacher'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-600 text-white">Host</span>
                        <span class="text-emerald-400 text-xs">🎙️</span>
                        <span class="text-slate-500 cursor-pointer">⋮</span>
                    </div>
                </div>

                
                <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-900 transition-all">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-pink-500/20 text-pink-300 font-bold flex items-center justify-center text-xs">SA</div>
                        <div>
                            <p class="font-bold text-white leading-none">Sara Ali</p>
                            <p class="text-[10px] text-slate-400"><?php echo e($isRtl ? 'طالبة' : 'Student'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-400">
                        <span class="text-rose-400 text-xs">🎙️✕</span>
                        <span class="text-slate-500 cursor-pointer">⋮</span>
                    </div>
                </div>

                
                <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-900 transition-all">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-300 font-bold flex items-center justify-center text-xs">OH</div>
                        <div>
                            <p class="font-bold text-white leading-none">Omar Hassan</p>
                            <p class="text-[10px] text-slate-400"><?php echo e($isRtl ? 'طالب' : 'Student'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-400 text-xs">🎙️</span>
                        <span class="text-slate-500 cursor-pointer">⋮</span>
                    </div>
                </div>

                
                <div class="flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-900 transition-all">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-300 font-bold flex items-center justify-center text-xs">NM</div>
                        <div>
                            <p class="font-bold text-white leading-none">Nour Mohamed</p>
                            <p class="text-[10px] text-slate-400"><?php echo e($isRtl ? 'طالب' : 'Student'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-400">
                        <span class="text-rose-400 text-xs">🎙️✕</span>
                        <span class="text-slate-500 cursor-pointer">⋮</span>
                    </div>
                </div>
            </div>

            <div class="text-center pt-1">
                <button class="text-indigo-400 hover:underline text-xs font-semibold"><?php echo e($isRtl ? 'عرض الكل' : 'View all'); ?></button>
            </div>
        </div>

        
        <div class="flex-1 flex flex-col justify-between overflow-hidden border-b border-slate-800/80">
            <div class="p-3 border-b border-slate-800/80 flex items-center gap-4 text-xs font-bold">
                <button onclick="switchChatTab('class')" id="tabClassChat" class="pb-1 border-b-2 border-indigo-500 text-white"><?php echo e($isRtl ? 'محادثة الحصة' : 'Class Chat'); ?></button>
                <button onclick="switchChatTab('private')" id="tabPrivateChat" class="pb-1 border-b-2 border-transparent text-slate-400 hover:text-white"><?php echo e($isRtl ? 'خاص' : 'Private'); ?></button>
            </div>

            
            <div id="chatMessagesFeed" class="p-4 space-y-3 overflow-y-auto text-xs flex-1">
                
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-pink-500/20 text-pink-300 font-bold flex items-center justify-center text-[10px]">SA</div>
                    <div class="space-y-0.5 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">Sara Ali</span>
                            <span class="text-[10px] text-slate-500 font-mono">10:30 AM</span>
                        </div>
                        <p class="p-2 rounded-xl bg-slate-900 text-slate-300 leading-relaxed"><?php echo e($isRtl ? 'صباح الخير جميعاً 👋' : 'Good morning everyone 👋'); ?></p>
                    </div>
                </div>

                
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-300 font-bold flex items-center justify-center text-[10px]">OH</div>
                    <div class="space-y-0.5 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-200">Omar Hassan</span>
                            <span class="text-[10px] text-slate-500 font-mono">10:31 AM</span>
                        </div>
                        <p class="p-2 rounded-xl bg-slate-900 text-slate-300 leading-relaxed"><?php echo e($isRtl ? 'هل يمكنك إعانة هذا الجزء مرة أخرى؟' : 'Can you explain this part again?'); ?></p>
                    </div>
                </div>

                
                <div class="flex items-start gap-2.5">
                    <img src="<?php echo e($teacherAvatar); ?>" class="w-6 h-6 rounded-full object-cover">
                    <div class="space-y-0.5 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-indigo-400"><?php echo e($teacherName); ?> (Teacher)</span>
                            <span class="text-[10px] text-slate-500 font-mono">10:32 AM</span>
                        </div>
                        <p class="p-2 rounded-xl bg-indigo-950/60 border border-indigo-900/60 text-slate-200 leading-relaxed"><?php echo e($isRtl ? 'بالتأكيد! دعني أشارك الشاشة مع الجميع.' : 'Sure! Let me share my screen.'); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="p-3 bg-[#0d111d] border-t border-slate-800 flex items-center gap-2">
                <input type="text" id="chatInput" placeholder="<?php echo e($isRtl ? 'اكتب رسالة...' : 'Type a message...'); ?>" onkeydown="if(event.key==='Enter') sendChatMessage()" class="flex-1 bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                <button onclick="sendChatMessage()" class="w-8 h-8 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center text-xs shadow-md">
                    ✈️
                </button>
                <button class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs">
                    +
                </button>
            </div>
        </div>

        
        <div class="p-4 space-y-2 text-xs">
            <h4 class="font-heading font-bold text-white text-xs mb-2"><?php echo e($isRtl ? 'معلومات الجلسة' : 'Session Info'); ?></h4>
            <div class="space-y-1.5 text-slate-400 font-mono text-[11px]">
                <div class="flex justify-between">
                    <span><?php echo e($isRtl ? 'المادة:' : 'Class:'); ?></span>
                    <span class="text-slate-200 font-sans font-semibold truncate max-w-[170px]"><?php echo e($sessionTitle); ?></span>
                </div>
                <div class="flex justify-between">
                    <span><?php echo e($isRtl ? 'التاريخ:' : 'Date:'); ?></span>
                    <span class="text-slate-200"><?php echo e(now()->format('M d, Y')); ?></span>
                </div>
                <div class="flex justify-between">
                    <span><?php echo e($isRtl ? 'المدة:' : 'Duration:'); ?></span>
                    <span class="text-slate-200">60 minutes</span>
                </div>
                <div class="flex justify-between">
                    <span><?php echo e($isRtl ? 'بدأ في:' : 'Started at:'); ?></span>
                    <span class="text-slate-200">10:00 AM</span>
                </div>
            </div>
        </div>
    </aside>

</div>

<script>
    let meetingSessionId = <?php echo e($session->id); ?>;
    let meetingAccessToken = null;
    let meetingTokenExpiresAt = null;
    let heartbeatInterval = null;
    let watermarkInterval = null;
    let durationTimerInterval = null;
    let sessionSecondsCount = 1458; // Default 24 mins 18 secs as in design

    let isMicOn = false;
    let isCamOn = true;
    let isSharing = true;
    let isRecording = false;

    document.addEventListener('DOMContentLoaded', function () {
        initializeInSystemMeeting();

        // Anti-Tamper & Security Detection Event Listeners
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        window.addEventListener('keydown', function (e) {
            if (
                e.key === 'PrintScreen' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'S' || e.key === 's')) ||
                (e.metaKey && e.shiftKey && (e.key === '4' || e.key === '5' || e.key === '3')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S' || e.key === 'p' || e.key === 'P')) ||
                e.key === 'F12'
            ) {
                e.preventDefault();
                e.stopPropagation();
                if (navigator.clipboard) navigator.clipboard.writeText('');
                logSecurityEvent('SCREEN_CAPTURE_KEY_BLOCKED', { key: e.key });
                triggerSecurityBlur('<?php echo e($isRtl ? "تم رصد محاولة تصوير أو تسجيل الشاشة! المحتوى محمي بحقوق الملكية الفكرية." : "Screen capture/recording attempt blocked! Content is IP protected."); ?>');
                return false;
            }
        });

        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            const origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
            navigator.mediaDevices.getDisplayMedia = function() {
                logSecurityEvent('DISPLAY_MEDIA_REQUESTED', {});
                triggerSecurityBlur('<?php echo e($isRtl ? "تم رصد محاولة تسجيل/مشاركة الشاشة عبر المتصفح! تم حجب البث لحماية المحتوى." : "Display screen recording request detected! Stream obfuscated."); ?>');
                return origGetDisplayMedia.apply(this, arguments);
            };
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                logSecurityEvent('TAB_HIDDEN', { path: window.location.pathname });
            } else {
                logSecurityEvent('TAB_VISIBLE', {});
            }
        });

        window.addEventListener('blur', function () {
            logSecurityEvent('WINDOW_BLURRED', {});
        });
    });

    function toggleMic() {
        isMicOn = !isMicOn;
        const btn = document.getElementById('btnMicToggle');
        const icon = document.getElementById('micIcon');
        const pipBadge = document.getElementById('studentMicPipBadge');
        const userMic = document.getElementById('userMicIndicator');

        if (isMicOn) {
            btn.className = "w-16 h-14 rounded-xl bg-emerald-600 text-white flex flex-col items-center justify-center gap-1 transition-all shadow-lg shadow-emerald-600/30";
            icon.innerText = "🎙️";
            if (pipBadge) pipBadge.innerText = "🎙️";
            if (userMic) userMic.innerHTML = '<span class="text-emerald-400 text-xs">🎙️</span>';
        } else {
            btn.className = "w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all";
            icon.innerText = "🎙️✕";
            if (pipBadge) pipBadge.innerText = "🎙️✕";
            if (userMic) userMic.innerHTML = '<span class="text-rose-400 text-xs">🎙️✕</span>';
        }
    }

    function toggleCamera() {
        isCamOn = !isCamOn;
        const btn = document.getElementById('btnCamToggle');
        const pip = document.getElementById('studentPipContainer');

        if (isCamOn) {
            btn.className = "w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all";
            if (pip) pip.classList.remove('hidden');
        } else {
            btn.className = "w-16 h-14 rounded-xl bg-rose-600/80 text-white flex flex-col items-center justify-center gap-1 transition-all";
            if (pip) pip.classList.add('hidden');
        }
    }

    function toggleShareScreen() {
        isSharing = !isSharing;
        const btn = document.getElementById('btnShareToggle');
        if (isSharing) {
            btn.className = "w-16 h-14 rounded-xl bg-indigo-600 text-white flex flex-col items-center justify-center gap-1 transition-all shadow-lg shadow-indigo-600/30";
        } else {
            btn.className = "w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all";
        }
    }

    function raiseHand() {
        const hand = document.getElementById('handIcon');
        hand.classList.add('animate-bounce');
        setTimeout(() => hand.classList.remove('animate-bounce'), 2000);
        if (window.Toast) {
            window.Toast.info('<?php echo e($isRtl ? "تم رفع يدك للمعلم" : "You raised your hand to the teacher"); ?>');
        }
    }

    function toggleRecording() {
        isRecording = !isRecording;
        const btn = document.getElementById('btnRecordToggle');
        if (isRecording) {
            btn.className = "w-16 h-14 rounded-xl bg-rose-600 text-white flex flex-col items-center justify-center gap-1 transition-all animate-pulse";
        } else {
            btn.className = "w-16 h-14 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex flex-col items-center justify-center gap-1 transition-all";
        }
    }

    function toggleRightSidebar() {
        const sidebar = document.getElementById('lmsRightSidebar');
        if (sidebar) sidebar.classList.toggle('hidden');
    }

    function votePoll(option) {
        let countA = 12, countB = 8, countC = 4;
        if (option === 'A') countA++;
        if (option === 'B') countB++;
        if (option === 'C') countC++;

        const total = countA + countB + countC;
        const pctA = Math.round((countA / total) * 100);
        const pctB = Math.round((countB / total) * 100);
        const pctC = Math.round((countC / total) * 100);

        document.getElementById('pollPctA').innerText = `${countA} (${pctA}%)`;
        document.getElementById('pollPctB').innerText = `${countB} (${pctB}%)`;
        document.getElementById('pollPctC').innerText = `${countC} (${pctC}%)`;

        document.getElementById('pollBarA').style.width = pctA + '%';
        document.getElementById('pollBarB').style.width = pctB + '%';
        document.getElementById('pollBarC').style.width = pctC + '%';

        document.getElementById('pollTotalVotes').innerText = `${total} <?php echo e($isRtl ? 'صوت' : 'votes'); ?>`;
    }

    function formatNote(command) {
        document.execCommand(command, false, null);
        saveNotesLocally();
    }

    function saveNotesLocally() {
        const content = document.getElementById('classNotesEditor').innerHTML;
        localStorage.setItem('lms_notes_' + meetingSessionId, content);
        const status = document.getElementById('notesSavedStatus');
        if (status) {
            status.innerHTML = '✓ <?php echo e($isRtl ? "تم الحفظ" : "Saved"); ?>';
        }
    }

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const feed = document.getElementById('chatMessagesFeed');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const msgHtml = `
            <div class="flex items-start gap-2.5">
                <img src="<?php echo e($studentAvatar); ?>" class="w-6 h-6 rounded-full object-cover">
                <div class="space-y-0.5 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-200"><?php echo e($user->name); ?></span>
                        <span class="text-[10px] text-slate-500 font-mono">${time}</span>
                    </div>
                    <p class="p-2 rounded-xl bg-indigo-600/30 border border-indigo-500/30 text-slate-100 leading-relaxed">${text}</p>
                </div>
            </div>
        `;

        feed.insertAdjacentHTML('beforeend', msgHtml);
        feed.scrollTop = feed.scrollHeight;
        input.value = '';
    }

    function triggerSecurityBlur(msg) {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        const msgEl = document.getElementById('securityBlurMsg');
        if (overlay) {
            if (msgEl && msg) msgEl.innerText = msg;
            overlay.classList.remove('hidden');
        }
    }

    function resumeMeetingPlayback() {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    function initializeInSystemMeeting() {
        const overlay = document.getElementById('meetingStateOverlay');
        const spinner = document.getElementById('meetingSpinner');
        const overlayTitle = document.getElementById('overlayTitle');
        const overlayMessage = document.getElementById('overlayMessage');
        const retryBtn = document.getElementById('btnRetryMeeting');
        const frame = document.getElementById('embeddedMeetingFrame');
        const statusBadge = document.getElementById('meetingStatusBadge');
        const statusText = document.getElementById('meetingStatusText');

        overlay.classList.remove('hidden');
        spinner.classList.remove('hidden');
        retryBtn.classList.add('hidden');
        overlayTitle.innerText = '<?php echo e($isRtl ? "جاري الاتصال بالغرفة المباشرة..." : "Connecting to Live Session..."); ?>';

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                meetingAccessToken = res.body.access_token;
                meetingTokenExpiresAt = res.body.expires_at;

                statusBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                statusText.innerText = '<?php echo e($isRtl ? "مباشر LIVE" : "LIVE"); ?>';

                const supportsEmbed = res.body.supports_embedding ?? true;
                const streamUrl = res.body.stream_url || res.body.join_url;
                const provider = res.body.provider || '';

                const isExternalMeeting = (!supportsEmbed) || (streamUrl && (streamUrl.includes('meet.google.com') || streamUrl.includes('teams.microsoft.com')));
                const embeddableUrl = getEmbeddableUrl(streamUrl);

                if (isExternalMeeting && streamUrl) {
                    const launcher = document.getElementById('externalMeetingLauncher');
                    const launchBtn = document.getElementById('btnExternalLaunch');
                    const launchBtnText = document.getElementById('externalLaunchBtnText');
                    const platformTitle = document.getElementById('externalPlatformTitle');

                    if (launchBtn) launchBtn.href = streamUrl;

                    if (platformTitle) {
                        platformTitle.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '<?php echo e($isRtl ? "غرفة البث المباشر التفاعلي — Google Meet" : "Interactive Live Stream — Google Meet"); ?>'
                            : '<?php echo e($isRtl ? "غرفة البث المباشر التفاعلي — Microsoft Teams" : "Interactive Live Stream — Microsoft Teams"); ?>';
                    }

                    if (launchBtnText) {
                        launchBtnText.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '<?php echo e($isRtl ? "انضم للبث عبر Google Meet 🚀" : "Join via Google Meet 🚀"); ?>'
                            : '<?php echo e($isRtl ? "انضم للبث عبر Microsoft Teams 🚀" : "Join via Microsoft Teams 🚀"); ?>';
                    }

                    if (launcher) launcher.classList.remove('hidden');
                } else if (embeddableUrl) {
                    frame.src = embeddableUrl;
                    frame.classList.remove('hidden');
                }

                overlay.classList.add('hidden');

                startWatermarkAnimation();
                startHeartbeatLoop();
                startDurationTimer();
            } else {
                spinner.classList.add('hidden');
                retryBtn.classList.remove('hidden');
                statusBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30';
                statusText.innerText = '<?php echo e($isRtl ? "غير متاح" : "UNAVAILABLE"); ?>';

                overlayTitle.innerText = res.body.message || '<?php echo e($isRtl ? "تعذر الوصول للحصة المباشرة" : "Unable to Join Live Session"); ?>';
                overlayMessage.innerText = res.body.reason_code ? `Error Code: ${res.body.reason_code}` : '<?php echo e($isRtl ? "تأكد من شروط الموعد والرصيد والواجبات." : "Verify session timing and package."); ?>';
            }
        })
        .catch(err => {
            spinner.classList.add('hidden');
            retryBtn.classList.remove('hidden');
            overlayTitle.innerText = '<?php echo e($isRtl ? "خطأ في الاتصال" : "Connection Failure"); ?>';
            overlayMessage.innerText = err.message;
        });
    }

    function startHeartbeatLoop() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        heartbeatInterval = setInterval(function () {
            if (!meetingAccessToken) return;

            fetch(`/ajax/sessions/${meetingSessionId}/meeting/heartbeat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    access_token: meetingAccessToken,
                    expires_at: meetingTokenExpiresAt
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.formatted_duration) {
                    document.getElementById('meetingDurationTimer').innerText = data.formatted_duration;
                } else if (data.session_ended) {
                    alert('<?php echo e($isRtl ? "انتهت هذه الجلسة المباشرة." : "This live session has ended."); ?>');
                    leaveMeetingSession();
                }
            })
            .catch(console.error);
        }, 30000);
    }

    function startDurationTimer() {
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        durationTimerInterval = setInterval(function () {
            sessionSecondsCount++;
            const hrs = String(Math.floor(sessionSecondsCount / 3600)).padStart(2, '0');
            const mins = String(Math.floor((sessionSecondsCount % 3600) / 60)).padStart(2, '0');
            const secs = String(sessionSecondsCount % 60).padStart(2, '0');
            document.getElementById('meetingDurationTimer').innerText = `${hrs}:${mins}:${secs}`;
        }, 1000);
    }

    function startWatermarkAnimation() {
        const watermark = document.getElementById('watermarkContent');
        const clock = document.getElementById('watermarkClock');

        function updateClock() {
            const now = new Date();
            clock.innerText = now.toLocaleTimeString();
        }
        updateClock();
        setInterval(updateClock, 1000);

        if (watermarkInterval) clearInterval(watermarkInterval);
        watermarkInterval = setInterval(function () {
            const topPct = Math.floor(Math.random() * 60) + 20;
            const leftPct = Math.floor(Math.random() * 60) + 20;
            watermark.style.top = topPct + '%';
            watermark.style.left = leftPct + '%';
        }, 8000);
    }

    function logSecurityEvent(eventType, metadata) {
        fetch(`/ajax/sessions/${meetingSessionId}/meeting/security-event`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                event_type: eventType,
                metadata: metadata
            })
        }).catch(console.error);
    }

    function getEmbeddableUrl(rawUrl) {
        if (!rawUrl) return '';

        const ytMatch = rawUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
        if (ytMatch && ytMatch[1]) {
            return `https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0&modestbranding=1&enablejsapi=1`;
        }

        const vimeoMatch = rawUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/\d+\/video\/|video\/|)(\d+)/);
        if (vimeoMatch && vimeoMatch[1]) {
            return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }

        if (rawUrl.includes('zoom.us/j/')) {
            const parts = rawUrl.split('zoom.us/j/');
            if (parts[1]) {
                const meetingParts = parts[1].split('?');
                const meetingId = meetingParts[0];
                const query = meetingParts[1] ? '&' + meetingParts[1] : '';
                return `https://zoom.us/wc/${meetingId}/join?prefer=1${query}`;
            }
        }

        return rawUrl;
    }

    function leaveMeetingSession() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        if (watermarkInterval) clearInterval(watermarkInterval);

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/leave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        }).finally(() => {
            window.location.href = '<?php echo e(route("student-portal")); ?>';
        });
    }
</script>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\components\meeting-container.blade.php ENDPATH**/ ?>