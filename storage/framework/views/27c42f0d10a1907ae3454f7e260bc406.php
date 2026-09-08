<?php $__env->startSection('content'); ?>
<?php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $todayDateStr = \Carbon\Carbon::today()->format('l, F j, Y');
    $activeTabKey = in_array($activeTab ?? 'overview', ['overview', 'sessions', 'assignments', 'attendance', 'students', 'notifications']) ? ($activeTab ?? 'overview') : 'overview';
?>

<div class="space-y-8" id="teacher-portal-root" data-initial-student="<?php echo e($initialStudentId ?? ''); ?>">

    
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-700/60">
        <div class="space-y-3 relative z-10 max-w-2xl">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 shadow-xs">
                    <i class="fa-solid fa-chalkboard-user"></i> <?php echo e($teacherProfile->title ?: __('Faculty Instructor')); ?>

                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                    <i class="fa-solid fa-star text-amber-400"></i> <?php echo e(number_format($teacherProfile->rating_avg ?: 4.9, 1)); ?> <?php echo e(__('Rating')); ?>

                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <i class="fa-solid fa-book-open"></i> <?php echo e($courses->count()); ?> <?php echo e(__('Active Courses')); ?>

                </span>
            </div>
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                <?php echo e(__('Welcome back')); ?>, <span class="text-teal-400"><?php echo e(auth()->user()->name); ?></span>
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm font-medium leading-relaxed max-w-xl">
                <?php echo e(__('Manage educational cohorts, monitor individual student performance, review homework submissions, and track attendance records.')); ?>

            </p>
        </div>

        
        <div class="relative z-10 flex flex-wrap items-center gap-2.5 shrink-0">
            <button type="button" onclick="switchTeacherTab('students')" class="btn-lift px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl border border-slate-600 shadow-md flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-graduation-cap"></i></span> <?php echo e(__('My Students')); ?>

            </button>
            <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-plus"></i></span> <?php echo e(__('Schedule Session')); ?>

            </button>
            <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-emerald-600/30 flex items-center gap-2 cursor-pointer transition-all">
                <span><i class="fa-solid fa-pen-to-square"></i></span> <?php echo e(__('Publish Assignment')); ?>

            </button>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    
    <div id="teacherToastAlert" class="hidden p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md"></div>

    
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4">
        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('Today Sessions')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-calendar-days"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="<?php echo e($todaySessionsCount); ?>">0</p>
            <p class="text-[11px] text-slate-500 font-semibold"><?php echo e(__('Scheduled today')); ?></p>
        </div>

        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('Upcoming')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-hourglass-half"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-blue-600 js-counter" data-target="<?php echo e($upcomingSessionsCount); ?>">0</p>
            <p class="text-[11px] text-slate-500 font-semibold"><?php echo e(__('Future cohorts')); ?></p>
        </div>

        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-teal-400" onclick="switchTeacherTab('students')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('My Students')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-graduation-cap"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 js-counter" data-target="<?php echo e($assignedStudentsCount); ?>">0</p>
            <p class="text-[11px] text-teal-600 font-semibold flex items-center gap-1"><?php echo e(__('View roster →')); ?></p>
        </div>

        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1 cursor-pointer hover:border-orange-400" onclick="switchTeacherTab('assignments')">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('Need Grading')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-pen-to-square"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-orange-500 js-counter" data-target="<?php echo e($pendingAssignmentsCount); ?>">0</p>
            <p class="text-[11px] text-slate-500 font-semibold"><?php echo e(__('Submissions queue')); ?></p>
        </div>

        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('Submissions')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-chart-column"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="<?php echo e($submittedAssignmentsCount); ?>">0</p>
            <p class="text-[11px] text-slate-500 font-semibold"><?php echo e(__('Total handled')); ?></p>
        </div>

        
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-mono font-bold uppercase"><?php echo e(__('Attendance Rate')); ?></span>
                <span class="text-lg"><i class="fa-solid fa-circle-check text-emerald-500"></i></span>
            </div>
            <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600"><span class="js-counter" data-target="<?php echo e($attendanceRate); ?>">0</span>%</p>
            <p class="text-[11px] text-slate-500 font-semibold"><?php echo e(__('Historical sessions')); ?></p>
        </div>
    </div>

    
    <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto scrollbar-thin">
        <button type="button" onclick="switchTeacherTab('overview')" id="tab-btn-overview" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'overview' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?>">
            <i class="fa-solid fa-chart-column"></i> <?php echo e(__('Overview & Today')); ?>

        </button>
        <button type="button" onclick="switchTeacherTab('students')" id="tab-btn-students" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'students' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?>">
            <i class="fa-solid fa-graduation-cap"></i> <?php echo e(__('My Students')); ?> (<?php echo e($assignedStudentsCount); ?>)
        </button>
        <button type="button" onclick="switchTeacherTab('sessions')" id="tab-btn-sessions" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'sessions' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?>">
            <i class="fa-solid fa-calendar-days"></i> <?php echo e(__('Sessions & Streams')); ?>

        </button>
        <button type="button" onclick="switchTeacherTab('assignments')" id="tab-btn-assignments" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'assignments' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?> relative">
            <i class="fa-solid fa-pen-to-square"></i> <?php echo e(__('Assignments & Quizzes')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingAssignmentsCount > 0): ?>
                <span class="ms-1.5 px-2 py-0.5 text-[10px] bg-orange-500 text-white rounded-full font-mono font-bold"><?php echo e($pendingAssignmentsCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
        <button type="button" onclick="switchTeacherTab('attendance')" id="tab-btn-attendance" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'attendance' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?>">
            <i class="fa-solid fa-clipboard-list"></i> <?php echo e(__('Attendance Tracker')); ?>

        </button>
        <button type="button" onclick="switchTeacherTab('notifications')" id="tab-btn-notifications" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap <?php echo e($activeTabKey === 'notifications' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'); ?> relative">
            <i class="fa-solid fa-bell"></i> <?php echo e(__('Notifications')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifCount > 0): ?>
                <span class="ms-1.5 px-2 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-mono font-bold"><?php echo e($unreadNotifCount); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
    </div>

    
    
    
    <div id="teacher-tab-overview" class="teacher-tab-content <?php echo e($activeTabKey === 'overview' ? '' : 'hidden'); ?> space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                                <span><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i></span> <?php echo e(__('Today\'s Teaching Sessions')); ?>

                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1"><?php echo e($todayDateStr); ?></p>
                        </div>
                        <span class="px-3 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200">
                            <?php echo e($todaySessions->count()); ?> <?php echo e(__('Sessions Today')); ?>

                        </span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todaySessions->count() > 0): ?>
                        <div class="space-y-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $todaySessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 hover:border-teal-400 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2.5 py-0.5 text-[10px] font-mono font-extrabold uppercase rounded-full bg-teal-100 text-teal-800">
                                                <?php echo e($session->course?->title ?: __('Course Session')); ?>

                                            </span>
                                            <span class="text-xs font-mono text-slate-500">
                                                <i class="fa-solid fa-stopwatch"></i> <?php echo e($session->effective_start_at ? $session->effective_start_at->format('h:i A') : 'Scheduled'); ?> (<?php echo e($session->duration_minutes); ?>m)
                                            </span>
                                        </div>
                                        <h3 class="font-heading font-extrabold text-base text-slate-900 truncate">
                                            <?php echo e($session->title ?: __('Interactive Teaching Session')); ?>

                                        </h3>
                                        <p class="text-xs text-slate-500 font-mono">
                                            <?php echo e(__('Cohort')); ?>: <?php echo e($session->subject?->name ?: __('General Curriculum')); ?>

                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->meeting_link): ?>
                                            <a href="<?php echo e($session->meeting_link); ?>" target="_blank" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5">
                                                <span><i class="fa-solid fa-video"></i></span> <?php echo e(__('Join / Broadcast')); ?>

                                            </a>
                                        <?php else: ?>
                                            <button type="button" onclick="openMeetingLinkModal(<?php echo e($session->id); ?>, '')" class="btn-lift px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                                <i class="fa-solid fa-link"></i> <?php echo e(__('Add Link')); ?>

                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <button type="button" onclick="openAttendanceModal(<?php echo e($session->id); ?>, '<?php echo e(addslashes($session->title)); ?>')" class="btn-lift px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200">
                                            <i class="fa-solid fa-clipboard-list"></i> <?php echo e(__('Attendance')); ?>

                                        </button>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-dashed border-slate-200 space-y-3">
                            <span class="text-3xl"><i class="fa-solid fa-mug-hot"></i></span>
                            <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No teaching sessions scheduled for today.')); ?></p>
                            <button type="button" onclick="openCreateSessionModal()" class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline">
                                + <?php echo e(__('Schedule a live session now')); ?>

                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h2 class="font-heading text-xl font-black text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-pen-to-square"></i></span> <?php echo e(__('Pending Grading Queue')); ?>

                        </h2>
                        <span class="px-3 py-1 bg-orange-50 text-orange-700 font-mono text-xs font-bold rounded-full border border-orange-200">
                            <?php echo e($pendingSubmissions->count()); ?> <?php echo e(__('Needs Review')); ?>

                        </span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingSubmissions->count() > 0): ?>
                        <div class="divide-y divide-slate-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingSubmissions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div class="space-y-1 min-w-0">
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 truncate">
                                            <?php echo e($sub->studentUser?->name ?: __('Student')); ?>

                                        </h4>
                                        <p class="text-xs text-slate-500 truncate">
                                            <?php echo e($sub->assignment?->title ?: __('Assignment')); ?> • <span class="font-mono text-[11px]"><?php echo e($sub->submitted_at ? $sub->submitted_at->diffForHumans() : ''); ?></span>
                                        </p>
                                    </div>
                                    <button type="button" onclick="openGradeModal(<?php echo e($sub->id); ?>, '<?php echo e(addslashes($sub->studentUser?->name)); ?>', '<?php echo e(addslashes($sub->assignment?->title)); ?>', '<?php echo e($sub->score); ?>', '<?php echo e(addslashes($sub->evaluation_notes)); ?>')" class="btn-lift px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs shrink-0">
                                        <i class="fa-solid fa-pen-nib"></i> <?php echo e(__('Grade Now')); ?>

                                    </button>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                            <p class="text-xs font-semibold text-slate-600"><?php echo e(__('Great job! All student homework submissions are graded.')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="space-y-6">
                
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-heading font-black text-lg text-slate-900"><?php echo e(__('Recent Students')); ?></h3>
                        <button type="button" onclick="switchTeacherTab('students')" class="text-xs font-bold text-teal-600 hover:underline">
                            <?php echo e(__('View All')); ?> &rarr;
                        </button>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignedStudents->count() > 0): ?>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $assignedStudents->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="p-3 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 flex items-center justify-between gap-3 hover:border-teal-400 transition-all cursor-pointer" onclick="openStudentDetailsModal(<?php echo e($st->user_id); ?>)">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-black text-xs flex items-center justify-center shrink-0">
                                            <?php echo e(mb_substr($st->user?->name ?: 'S', 0, 1)); ?>

                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate"><?php echo e($st->user?->name); ?></p>
                                            <p class="text-[10px] font-mono text-slate-500 truncate"><?php echo e($st->gradeLevel?->name ?: __('Secondary')); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-end shrink-0 font-mono text-[11px]">
                                        <span class="font-bold text-emerald-600"><?php echo e($st->attendance_rate); ?>%</span>
                                        <span class="block text-[9px] text-slate-400"><?php echo e(__('Att.')); ?></span>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 text-center py-4"><?php echo e(__('No students assigned yet.')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xl space-y-4">
                    <h3 class="font-heading font-black text-lg text-slate-900 border-b border-slate-100 pb-3"><?php echo e(__('Your Active Courses')); ?></h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->count() > 0): ?>
                        <div class="space-y-2.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $sessionCount = $c->sessions->count();
                                    $sessionLabel = $isAr ? ($sessionCount == 1 ? 'حصة' : ($sessionCount == 2 ? 'حصتان' : ($sessionCount <= 10 ? 'حصص' : 'حصة'))) : ($sessionCount == 1 ? 'Session' : 'Sessions');
                                ?>
                                <div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 hover:border-teal-400 hover:bg-white transition-all flex items-center justify-between gap-3 group">
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <h4 class="font-heading font-black text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-teal-700 transition-colors">
                                            <?php echo e($c->title); ?>

                                        </h4>
                                        <p class="text-[11px] font-mono text-slate-500 truncate">
                                            <?php echo e($c->subject?->name); ?> • <?php echo e($c->gradeLevel?->name); ?>

                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-teal-800 border border-teal-200/80 text-xs font-mono font-extrabold rounded-xl shadow-2xs whitespace-nowrap">
                                            <span><i class="fa-solid fa-book-open"></i></span> <?php echo e($sessionCount); ?> <?php echo e($sessionLabel); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 text-center py-4"><?php echo e(__('No active courses configured.')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    
    
    <div id="teacher-tab-students" class="teacher-tab-content <?php echo e($activeTabKey === 'students' ? '' : 'hidden'); ?> space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-graduation-cap"></i></span> <?php echo e(__('app.teacher.my_students')); ?>

                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">
                        <?php echo e(__('Search, filter, and inspect detailed educational profiles for all enrolled learners in your courses.')); ?>

                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 bg-teal-50 text-teal-800 text-xs font-mono font-bold rounded-xl border border-teal-200">
                        <?php echo e($assignedStudents->count()); ?> <?php echo e(__('Enrolled Students')); ?>

                    </span>
                </div>
            </div>

            
            <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    
                    <div class="lg:col-span-2 relative">
                        <input 
                            type="text" 
                            id="studentSearchInput" 
                            placeholder="<?php echo e(__('app.teacher.search_placeholder')); ?>" 
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all pe-8"
                        >
                        <span class="absolute top-1/2 -translate-y-1/2 end-3 text-slate-400 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                    </div>

                    
                    <div>
                        <select id="studentCourseFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value=""><?php echo e(__('app.teacher.all_courses')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    
                    <div>
                        <select id="studentGradeFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value=""><?php echo e(__('app.teacher.all_grades')); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gradeLevels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($gl->id); ?>"><?php echo e($gl->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    
                    <div>
                        <select id="studentAttendanceFilter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-500">
                            <option value=""><?php echo e(__('app.teacher.all_attendance')); ?></option>
                            <option value="good"><?php echo e(__('app.teacher.attendance_good')); ?></option>
                            <option value="risk"><?php echo e(__('app.teacher.attendance_risk')); ?></option>
                        </select>
                    </div>
                </div>

                
                <div class="flex items-center justify-between text-xs font-mono text-slate-500 pt-1">
                    <span id="studentFilterCountText"><?php echo e(__('Showing all assigned students')); ?></span>
                    <button type="button" onclick="resetStudentFilters()" class="text-teal-600 font-bold hover:underline cursor-pointer">
                        ↺ <?php echo e(__('Reset Filters')); ?>

                    </button>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignedStudents->count() > 0): ?>
                <div id="studentsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $assignedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $courseIdsList = implode(',', $st->enrolled_course_ids ?? []);
                            $gradeId = $st->grade_level_id ?? 0;
                            $attRate = (int) ($st->attendance_rate ?? 100);
                            $avgSc = $st->avg_score !== null ? (float) $st->avg_score : null;
                            $studentCode = 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT);
                        ?>
                        <div 
                            class="student-roster-card bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 hover:border-teal-500 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-4"
                            data-name="<?php echo e(strtolower($st->user?->name ?: '')); ?>"
                            data-code="<?php echo e(strtolower($studentCode)); ?>"
                            data-school="<?php echo e(strtolower($st->school_name ?: '')); ?>"
                            data-courses="<?php echo e($courseIdsList); ?>"
                            data-grade="<?php echo e($gradeId); ?>"
                            data-attendance="<?php echo e($attRate); ?>"
                            data-score="<?php echo e($avgSc !== null ? $avgSc : -1); ?>"
                        >
                            
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-base flex items-center justify-center shrink-0 shadow-sm border border-teal-300/40">
                                            <?php echo e(mb_substr($st->user?->name ?: 'S', 0, 1)); ?>

                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-heading font-black text-sm text-slate-900 truncate">
                                                <?php echo e($st->user?->name); ?>

                                            </h3>
                                            <span class="inline-block font-mono text-[10px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
                                                #<?php echo e($studentCode); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-full <?php echo e($st->user?->status === \App\Enums\AccountStatus::APPROVED ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'); ?>">
                                        <?php echo e($st->user?->status === \App\Enums\AccountStatus::APPROVED ? __('Active') : __('Pending')); ?>

                                    </span>
                                </div>

                                
                                <div class="text-xs font-mono text-slate-600 space-y-1 pt-1">
                                    <p class="truncate flex items-center gap-1.5">
                                        <span><i class="fa-solid fa-school"></i></span> <?php echo e($st->school_name ?: __('Elite Academy')); ?>

                                    </p>
                                    <p class="truncate flex items-center gap-1.5 text-slate-500">
                                        <span><i class="fa-solid fa-graduation-cap"></i></span> <?php echo e($st->gradeLevel?->name ?: __('Secondary Level')); ?>

                                    </p>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($st->enrolled_courses)): ?>
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = collect($st->enrolled_courses)->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <span class="text-[10px] font-mono font-bold bg-white text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 truncate max-w-[140px]">
                                                <?php echo e($cMeta['title']); ?>

                                            </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($st->enrolled_courses) > 2): ?>
                                            <span class="text-[10px] font-mono text-slate-400 self-center">
                                                +<?php echo e(count($st->enrolled_courses) - 2); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="space-y-3 pt-3 border-t border-slate-200/80">
                                <div class="grid grid-cols-2 gap-2 text-center font-mono text-[11px]">
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[9px] uppercase font-bold"><?php echo e(__('Attendance')); ?></span>
                                        <span class="font-extrabold <?php echo e($attRate >= 80 ? 'text-emerald-600' : ($attRate >= 60 ? 'text-amber-600' : 'text-rose-600')); ?>">
                                            <?php echo e($attRate); ?>%
                                        </span>
                                    </div>
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[9px] uppercase font-bold"><?php echo e(__('Avg Score')); ?></span>
                                        <span class="font-extrabold <?php echo e($avgSc !== null ? ($avgSc >= 70 ? 'text-teal-600' : 'text-rose-600') : 'text-slate-400'); ?>">
                                            <?php echo e($avgSc !== null ? $avgSc . '%' : 'N/A'); ?>

                                        </span>
                                    </div>
                                </div>

                                <button 
                                    type="button" 
                                    onclick="openStudentDetailsModal(<?php echo e($st->user_id); ?>)" 
                                    class="btn-lift w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-sm flex items-center justify-center gap-2 cursor-pointer transition-all"
                                >
                                    <span><i class="fa-solid fa-graduation-cap"></i></span> <?php echo e(__('app.teacher.student_profile')); ?>

                                </button>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <div id="studentEmptySearchState" class="hidden text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <span class="text-3xl"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('app.teacher.no_students_found')); ?></p>
                    <button type="button" onclick="resetStudentFilters()" class="text-xs text-teal-600 font-bold hover:underline">
                        <?php echo e(__('app.teacher.all_courses')); ?>

                    </button>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No students enrolled in your courses yet.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div id="teacher-tab-sessions" class="teacher-tab-content <?php echo e($activeTabKey === 'sessions' ? '' : 'hidden'); ?> space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-calendar-days"></i></span> <?php echo e(__('Live Teaching Schedule & Meeting Links')); ?>

                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1"><?php echo e(__('Manage your live sessions, recurring cohorts, update broadcast URLs, and reschedule classes.')); ?></p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCreateRecurringModal()" class="btn-lift px-4 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span><i class="fa-solid fa-arrows-rotate"></i></span> <?php echo e(__('Create Recurring Schedule')); ?>

                    </button>
                    <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                        <span><i class="fa-solid fa-plus"></i></span> <?php echo e(__('Schedule Single Session')); ?>

                    </button>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allSessions->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                <th class="py-3 px-4"><?php echo e(__('Session Details')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Course')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Date & Time')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Status')); ?></th>
                                <th class="py-3 px-4 text-right rtl:text-left"><?php echo e(__('Actions')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p class="font-extrabold text-slate-900 text-xs sm:text-sm"><?php echo e($session->title ?: __('Live Class Session')); ?></p>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->recurring_schedule_id): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                        <span><i class="fa-solid fa-arrows-rotate"></i></span> <?php echo e(__('Recurring')); ?>

                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($session->is_override): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                                        <span><i class="fa-solid fa-triangle-exclamation"></i></span> <?php echo e(__('Override')); ?>

                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <p class="text-xs text-slate-500 font-mono"><?php echo e($session->duration_minutes); ?> <?php echo e(__('minutes')); ?></p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-xs font-semibold text-teal-700">
                                        <?php echo e($session->course?->title ?: __('General Curriculum')); ?>

                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                                        <?php echo e($session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : __('Not Scheduled')); ?>

                                    </td>
                                    <td class="py-4 px-4">
                                        <?php
                                            $statusText = match($session->status) {
                                                'scheduled' => __('Scheduled'),
                                                'link_visible' => __('Ready / Broadcast Link Live'),
                                                'live' => __('Live Now'),
                                                'completed' => __('Completed'),
                                                'cancelled' => __('Cancelled'),
                                                'cancelled_by_teacher' => __('Cancelled by Teacher'),
                                                'rescheduled' => __('Rescheduled'),
                                                default => __('Scheduled'),
                                            };
                                        ?>
                                        <span class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-full <?php echo e(in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($session->status === 'rescheduled' ? 'bg-amber-100 text-amber-800' : 'bg-teal-100 text-teal-700'))); ?>">
                                            <?php echo e($statusText); ?>

                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right rtl:text-left space-x-1 rtl:space-x-reverse whitespace-nowrap">
                                        <button type="button" onclick="openEditSessionOverrideModal(<?php echo e($session->id); ?>, '<?php echo e(addslashes($session->title)); ?>', '<?php echo e($session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : ''); ?>', <?php echo e($session->duration_minutes ?: 60); ?>, '<?php echo e(addslashes($session->meeting_link ?? '')); ?>', '<?php echo e(addslashes($session->teacher_notes ?? '')); ?>')" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-bold rounded-lg transition-colors cursor-pointer" title="<?php echo e(__('Edit or Override Session')); ?>">
                                            <i class="fa-solid fa-pen"></i> <?php echo e(__('Edit Scope')); ?>

                                        </button>
                                        <button type="button" onclick="openMeetingLinkModal(<?php echo e($session->id); ?>, '<?php echo e(addslashes($session->meeting_link ?? '')); ?>')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            <i class="fa-solid fa-link"></i> <?php echo e(__('Link')); ?>

                                        </button>
                                        <button type="button" onclick="openRescheduleModal(<?php echo e($session->id); ?>, '<?php echo e($session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : ''); ?>')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                            <i class="fa-solid fa-calendar-days"></i> <?php echo e(__('Reschedule')); ?>

                                        </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($session->status, ['cancelled', 'cancelled_by_teacher'])): ?>
                                            <button type="button" onclick="confirmCancelSession(<?php echo e($session->id); ?>)" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg transition-colors cursor-pointer">
                                                <i class="fa-solid fa-circle-xmark text-rose-500"></i> <?php echo e(__('Cancel')); ?>

                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <?php echo e($allSessions->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No sessions created yet.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div id="teacher-tab-assignments" class="teacher-tab-content <?php echo e($activeTabKey === 'assignments' ? '' : 'hidden'); ?> space-y-8">
        
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900"><?php echo e(__('Assignments & Homework Manager')); ?></h2>
                    <p class="text-xs font-mono text-slate-500 mt-1"><?php echo e(__('Publish new course assignments, interactive quizzes, and review student work.')); ?></p>
                </div>
                <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                    + <?php echo e(__('Publish New Assignment')); ?>

                </button>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignments->count() > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $subCount = $assignment->submissions->count();
                            $subLabel = $isAr ? ($subCount == 1 ? 'تسليم' : ($subCount == 2 ? 'تسليمان' : ($subCount <= 10 ? 'تسليمات' : 'تسليم'))) : ($subCount == 1 ? 'Submission' : 'Submissions');
                        ?>
                        <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-3 flex flex-col justify-between hover:border-teal-400 hover:bg-white transition-all group shadow-xs">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <span class="font-mono font-bold text-teal-700 uppercase truncate flex-1 min-w-0"><?php echo e($assignment->course?->title ?: __('Course')); ?></span>
                                    <span class="px-2.5 py-1 bg-teal-50 text-teal-800 border border-teal-200/80 text-[11px] font-mono font-extrabold rounded-xl shrink-0 whitespace-nowrap shadow-2xs">
                                        <i class="fa-solid fa-pen-to-square"></i> <?php echo e($subCount); ?> <?php echo e($subLabel); ?>

                                    </span>
                                </div>
                                <h3 class="font-heading font-black text-base text-slate-900 leading-snug group-hover:text-teal-700 transition-colors"><?php echo e($assignment->title); ?></h3>
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed"><?php echo e($assignment->description ?: __('Homework assignment for student revision.')); ?></p>
                            </div>
                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-mono text-slate-500">
                                <span class="truncate"><i class="fa-solid fa-calendar-days"></i> <?php echo e(__('Due')); ?>: <?php echo e($assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline')); ?></span>
                                <span class="font-extrabold text-slate-800 shrink-0 ms-2 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fa-solid fa-bullseye"></i> <?php echo e($assignment->passing_score ?: 70); ?>% <?php echo e(__('Pass')); ?></span>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No assignments created yet.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <h3 class="font-heading text-xl font-black text-slate-900"><?php echo e(__('All Student Submissions')); ?></h3>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submissions->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                <th class="py-3 px-4"><?php echo e(__('Student')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Assignment')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Submitted At')); ?></th>
                                <th class="py-3 px-4"><?php echo e(__('Grade / Score')); ?></th>
                                <th class="py-3 px-4 text-right rtl:text-left"><?php echo e(__('Review Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $subVal = $sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->value : (is_object($sub->status) ? ($sub->status->value ?? '') : (string) $sub->status);
                                    $isReviewed = $subVal === 'reviewed';
                                ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        <?php echo e($sub->studentUser?->name ?: __('Student')); ?>

                                    </td>
                                    <td class="py-4 px-4 text-xs font-medium text-slate-700">
                                        <?php echo e($sub->assignment?->title ?: __('Assignment')); ?>

                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                        <?php echo e($sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Draft'); ?>

                                    </td>
                                    <td class="py-4 px-4 font-mono text-xs">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sub->score !== null): ?>
                                            <span class="font-extrabold text-emerald-600"><?php echo e(number_format($sub->score, 1)); ?>%</span>
                                        <?php else: ?>
                                            <span class="text-orange-500 italic"><?php echo e(__('Pending Grade')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-right rtl:text-left">
                                        <button type="button" onclick="openGradeModal(<?php echo e($sub->id); ?>, '<?php echo e(addslashes($sub->studentUser?->name)); ?>', '<?php echo e(addslashes($sub->assignment?->title)); ?>', '<?php echo e($sub->score); ?>', '<?php echo e(addslashes($sub->evaluation_notes)); ?>')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs">
                                            <i class="fa-solid fa-magnifying-glass"></i> <?php echo e($isReviewed ? __('Review & Grade') : __('Review Submission')); ?>

                                        </button>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No student submissions yet.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div id="teacher-tab-attendance" class="teacher-tab-content <?php echo e($activeTabKey === 'attendance' ? '' : 'hidden'); ?> space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div>
                <h2 class="font-heading text-2xl font-black text-slate-900"><?php echo e(__('Attendance & Student Check-In')); ?></h2>
                <p class="text-xs font-mono text-slate-500 mt-1"><?php echo e(__('Select a session to record attendance for enrolled cohort learners.')); ?></p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($todaySessions->count() > 0 || $allSessions->count() > 0): ?>
                <div class="space-y-4">
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider"><?php echo e(__('Select Teaching Session to Mark Attendance')); ?></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $todaySessions->merge($allSessions->take(6))->unique('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200 hover:border-teal-400 transition-all flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900"><?php echo e($ses->title ?: __('Live Session')); ?></h4>
                                    <p class="text-xs text-slate-500 font-mono"><?php echo e($ses->effective_start_at ? $ses->effective_start_at->format('M d, Y h:i A') : ''); ?></p>
                                </div>
                                <button type="button" onclick="openAttendanceModal(<?php echo e($ses->id); ?>, '<?php echo e(addslashes($ses->title)); ?>')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs shrink-0">
                                    <i class="fa-solid fa-clipboard-list"></i> <?php echo e(__('Mark')); ?>

                                </button>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('No sessions available for attendance tracking.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    
    
    <div id="teacher-tab-notifications" class="teacher-tab-content <?php echo e($activeTabKey === 'notifications' ? '' : 'hidden'); ?> space-y-6">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900"><?php echo e(__('Notification Feed & Alerts')); ?></h2>
                    <p class="text-xs font-mono text-slate-500 mt-1"><?php echo e(__('Real-time session updates, assignment submissions, and student alerts.')); ?></p>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userNotifications->count() > 0): ?>
                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $userNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="p-4 rounded-2xl border transition-colors flex items-start justify-between gap-4 <?php echo e($notif->is_read ? 'bg-[#FAFAF9] border-slate-200/70' : 'bg-teal-50/80 border-teal-200 font-semibold'); ?>">
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-900"><?php echo e($notif->title); ?></h4>
                                <p class="text-xs text-slate-600"><?php echo e($notif->body); ?></p>
                                <p class="text-[10px] font-mono text-slate-400"><?php echo e($notif->created_at ? $notif->created_at->diffForHumans() : ''); ?></p>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <?php echo e($userNotifications->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                    <p class="text-sm font-semibold text-slate-700"><?php echo e(__('You\'re all caught up! No new notifications.')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

</div>




<div id="studentProfileModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col overflow-hidden relative">
        
        
        <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 to-teal-950 text-white flex items-start justify-between gap-4 shrink-0">
            <div class="flex items-center gap-4 min-w-0">
                <div id="spModalAvatar" class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xl flex items-center justify-center shrink-0 border border-teal-300/40 shadow-md">
                    S
                </div>
                <div class="min-w-0 space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 id="spModalName" class="font-heading font-black text-xl sm:text-2xl text-white truncate">
                            <?php echo e(__('Student Profile')); ?>

                        </h3>
                        <span id="spModalCode" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-extrabold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                            #STU-00000
                        </span>
                    </div>
                    <p id="spModalMeta" class="text-xs font-mono text-slate-300 truncate">
                        <?php echo e(__('Loading educational records...')); ?>

                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openAddNoteModal()" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span><i class="fa-solid fa-pen-nib"></i></span> <?php echo e(__('app.teacher.add_educational_note')); ?>

                </button>
                <button type="button" onclick="closeModal('studentProfileModal')" class="text-slate-300 hover:text-white font-bold text-xl p-1 cursor-pointer" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-2.5 flex items-center gap-2 overflow-x-auto shrink-0 scrollbar-thin">
            <button type="button" onclick="switchSpTab('overview')" id="sp-tab-btn-overview" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap bg-teal-600 text-white shadow-xs">
                <i class="fa-solid fa-chart-column"></i> <?php echo e(__('Overview')); ?>

            </button>
            <button type="button" onclick="switchSpTab('courses')" id="sp-tab-btn-courses" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-book-open"></i> <?php echo e(__('Courses')); ?>

            </button>
            <button type="button" onclick="switchSpTab('sessions')" id="sp-tab-btn-sessions" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-calendar-days"></i> <?php echo e(__('Sessions')); ?>

            </button>
            <button type="button" onclick="switchSpTab('attendance')" id="sp-tab-btn-attendance" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-clipboard-list"></i> <?php echo e(__('Attendance')); ?>

            </button>
            <button type="button" onclick="switchSpTab('assignments')" id="sp-tab-btn-assignments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-pen-to-square"></i> <?php echo e(__('Assignments')); ?>

            </button>
            <button type="button" onclick="switchSpTab('assessments')" id="sp-tab-btn-assessments" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-bullseye"></i> <?php echo e(__('Assessments')); ?>

            </button>
            <button type="button" onclick="switchSpTab('progress')" id="sp-tab-btn-progress" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-chart-line"></i> <?php echo e(__('Progress')); ?>

            </button>
            <button type="button" onclick="switchSpTab('notes')" id="sp-tab-btn-notes" class="sp-subtab-btn px-4 py-1.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap text-slate-700 hover:bg-slate-200/60">
                <i class="fa-solid fa-comments"></i> <?php echo e(__('Notes')); ?>

            </button>
        </div>

        
        <div class="p-6 overflow-y-auto flex-1 space-y-6">
            
            <div id="spLoadingSkeleton" class="py-16 text-center space-y-3">
                <svg class="animate-spin h-8 w-8 text-teal-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-xs font-mono font-bold text-slate-500"><?php echo e(__('Loading educational records from server...')); ?></p>
            </div>

            
            <div id="sp-pane-overview" class="sp-tab-pane space-y-6 hidden">
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-teal-700 uppercase"><?php echo e(__('Attendance Rate')); ?></span>
                        <p id="spOverviewAttRate" class="font-heading font-black text-2xl text-teal-900">0%</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-emerald-700 uppercase"><?php echo e(__('Average Grade')); ?></span>
                        <p id="spOverviewAvgGrade" class="font-heading font-black text-2xl text-emerald-900">0%</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-blue-700 uppercase"><?php echo e(__('Total Sessions')); ?></span>
                        <p id="spOverviewTotalSessions" class="font-heading font-black text-2xl text-blue-900">0</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200/80 text-center space-y-0.5">
                        <span class="text-[10px] font-mono font-bold text-orange-700 uppercase"><?php echo e(__('Submissions')); ?></span>
                        <p id="spOverviewSubmissions" class="font-heading font-black text-2xl text-orange-900">0</p>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                        <h4 class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-clock"></i></span> <?php echo e(__('Recent Live Sessions Attended')); ?>

                        </h4>
                        <div id="spOverviewRecentSessions" class="space-y-2 text-xs font-mono">
                            
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                        <h4 class="font-heading font-extrabold text-sm text-slate-900 flex items-center gap-2">
                            <span><i class="fa-solid fa-pen-to-square"></i></span> <?php echo e(__('Recent Homework & Submissions')); ?>

                        </h4>
                        <div id="spOverviewRecentSubmissions" class="space-y-2 text-xs font-mono">
                            
                        </div>
                    </div>
                </div>
            </div>

            
            <div id="sp-pane-courses" class="sp-tab-pane space-y-4 hidden">
                <div id="spCoursesList" class="space-y-3">
                    
                </div>
            </div>

            
            <div id="sp-pane-sessions" class="sp-tab-pane space-y-4 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3"><?php echo e(__('Session')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Course')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Date')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Attendance')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="spSessionsTableBody" class="divide-y divide-slate-100">
                            
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="sp-pane-attendance" class="sp-tab-pane space-y-4 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3"><?php echo e(__('Session Title')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Date & Time')); ?></th>
                                <th class="py-2.5 px-3 text-right rtl:text-left"><?php echo e(__('Status')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="spAttendanceTableBody" class="divide-y divide-slate-100">
                            
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="sp-pane-assignments" class="sp-tab-pane space-y-4 hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 font-mono font-bold text-slate-500 uppercase">
                                <th class="py-2.5 px-3"><?php echo e(__('Assignment Title')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Submitted At')); ?></th>
                                <th class="py-2.5 px-3"><?php echo e(__('Score')); ?></th>
                                <th class="py-2.5 px-3 text-right rtl:text-left"><?php echo e(__('Review Details')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="spAssignmentsTableBody" class="divide-y divide-slate-100">
                            
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div id="sp-pane-assessments" class="sp-tab-pane space-y-4 hidden">
                <div id="spAssessmentsContainer" class="space-y-3">
                    
                </div>
            </div>

            
            <div id="sp-pane-progress" class="sp-tab-pane space-y-4 hidden">
                <div id="spProgressContainer" class="space-y-4">
                    
                </div>
            </div>

            
            <div id="sp-pane-notes" class="sp-tab-pane space-y-4 hidden">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="font-heading font-black text-sm text-slate-900"><?php echo e(__('Teacher Pedagogical Notes')); ?></h4>
                    <button type="button" onclick="openAddNoteModal()" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        + <?php echo e(__('Add Note')); ?>

                    </button>
                </div>
                <div id="spNotesContainer" class="space-y-3">
                    
                </div>
            </div>
        </div>
    </div>
</div>




<div id="addNoteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-heading font-black text-lg text-slate-900"><?php echo e(__('app.teacher.add_educational_note')); ?></h3>
            <button type="button" onclick="closeModal('addNoteModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="addNoteForm" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="noteStudentUserId" name="student_user_id">

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Category')); ?></label>
                <select name="category" required class="input-mobile bg-white text-xs">
                    <option value="academic"><?php echo e(__('app.teacher.note_category_academic')); ?></option>
                    <option value="homework"><?php echo e(__('app.teacher.note_category_homework')); ?></option>
                    <option value="participation"><?php echo e(__('app.teacher.note_category_participation')); ?></option>
                    <option value="behavior"><?php echo e(__('app.teacher.note_category_behavior')); ?></option>
                    <option value="general" selected><?php echo e(__('app.teacher.note_category_general')); ?></option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Pedagogical Observation & Feedback')); ?></label>
                <textarea id="noteContentTextarea" name="note" rows="4" required placeholder="<?php echo e(__('Write your educational observations and advisory comments...')); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600 transition-colors"></textarea>
                
                
                <div id="notePhoneWarning" class="hidden mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] font-bold flex items-center gap-2 animate-pulse">
                    <span><i class="fa-solid fa-shield-halved"></i></span>
                    <span><?php echo e(__('Security Warning: Sharing phone numbers or contact details in educational notes is prohibited.')); ?></span>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('addNoteModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" id="saveNoteBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    <?php echo e(__('Save Note')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="createSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Schedule New Live Session')); ?></h3>
            <button type="button" onclick="closeModal('createSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="createSessionForm" action="<?php echo e(route('ajax.teacher.sessions.create')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Select Course')); ?> *</label>
                <select name="course_id" required class="input-mobile bg-white text-xs">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?> (<?php echo e($c->subject?->name); ?>)</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Session Title')); ?> *</label>
                <input type="text" name="title" placeholder="e.g. Session 4: Electromagnetism & Ohm's Law" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Scheduled Date & Time')); ?> *</label>
                    <input type="datetime-local" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Duration (Minutes)')); ?> *</label>
                    <input type="number" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Meeting Broadcast Link (Optional)')); ?></label>
                <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..." class="input-mobile">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_free_demo" id="is_free_demo" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                <label for="is_free_demo" class="text-xs font-semibold text-slate-700"><?php echo e(__('Mark as Free Trial / Demo Session')); ?></label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createSessionModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    <?php echo e(__('Create Session')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="recurringScheduleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                    <span><i class="fa-solid fa-arrows-rotate"></i></span> <?php echo e(__('Create Recurring Schedule')); ?>

                </h3>
                <p class="text-xs font-mono text-slate-500 mt-0.5"><?php echo e(__('Automatically generate recurring class sessions with conflict detection.')); ?></p>
            </div>
            <button type="button" onclick="closeModal('recurringScheduleModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="recurringScheduleForm" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Schedule Title')); ?> *</label>
                <input type="text" id="recTitle" name="title" placeholder="e.g. Physics Secondary 3 - Weekly Interactive Cohort" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Select Course')); ?> *</label>
                    <select id="recCourseId" name="course_id" required class="input-mobile bg-white text-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?> (<?php echo e($c->subject?->name); ?>)</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Target Student (Optional)')); ?></label>
                    <select id="recStudentUserId" name="student_user_id" class="input-mobile bg-white text-xs">
                        <option value=""><?php echo e(__('All Enrolled Course Students (General Cohort)')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $assignedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($st->user_id); ?>"><?php echo e($st->user?->name); ?> (<?php echo e($st->gradeLevel?->name); ?>)</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Recurrence Pattern')); ?> *</label>
                    <select id="recType" name="recurrence_type" required class="input-mobile bg-white text-xs" onchange="toggleRecurrenceFields(this.value)">
                        <option value="weekly" selected><?php echo e(__('Weekly')); ?></option>
                        <option value="monthly"><?php echo e(__('Monthly')); ?></option>
                        <option value="multi_month"><?php echo e(__('Multiple Months (3-6 Months)')); ?></option>
                        <option value="yearly"><?php echo e(__('Yearly (Full Academic Year)')); ?></option>
                        <option value="single"><?php echo e(__('Single Session')); ?></option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Start Time')); ?> *</label>
                    <input type="time" id="recStartTime" name="start_time" value="10:00" required class="input-mobile">
                </div>
            </div>

            
            <div id="recDaysContainer" class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <label class="block text-xs font-mono font-bold text-slate-600 uppercase tracking-wider"><?php echo e(__('Select Days of Week')); ?> *</label>
                <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 text-center text-xs font-mono font-bold">
                    <?php
                        $weekDaysList = [
                            ['val' => 6, 'name' => 'saturday', 'label' => __('Saturday')],
                            ['val' => 0, 'name' => 'sunday', 'label' => __('Sunday')],
                            ['val' => 1, 'name' => 'monday', 'label' => __('Monday')],
                            ['val' => 2, 'name' => 'tuesday', 'label' => __('Tuesday')],
                            ['val' => 3, 'name' => 'wednesday', 'label' => __('Wednesday')],
                            ['val' => 4, 'name' => 'thursday', 'label' => __('Thursday')],
                            ['val' => 5, 'name' => 'friday', 'label' => __('Friday')],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDaysList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="p-2 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex flex-col items-center gap-1.5 transition-all has-checked:bg-teal-50 has-checked:border-teal-500 has-checked:text-teal-800">
                            <input type="checkbox" name="days_of_week[]" value="<?php echo e($wd['val']); ?>" <?php echo e(in_array($wd['val'], [6, 0]) ? 'checked' : ''); ?> class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 rec-day-checkbox">
                            <span class="text-[11px]"><?php echo e($wd['label']); ?></span>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Duration (Minutes)')); ?> *</label>
                    <input type="number" id="recDuration" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Start Date')); ?> *</label>
                    <input type="date" id="recStartDate" name="start_date" value="<?php echo e(now()->format('Y-m-d')); ?>" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('End Date')); ?> *</label>
                    <input type="date" id="recEndDate" name="end_date" value="<?php echo e(now()->addMonths(3)->format('Y-m-d')); ?>" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Meeting Broadcast Link (Optional)')); ?></label>
                <input type="url" id="recMeetingLink" name="meeting_link" placeholder="https://zoom.us/j/... or classroom stream link" class="input-mobile">
            </div>

            
            <div class="pt-2">
                <button type="button" onclick="previewRecurringDates()" class="btn-lift w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-xl border border-slate-300 flex items-center justify-center gap-2 cursor-pointer transition-all">
                    <span><i class="fa-solid fa-magnifying-glass"></i></span> <?php echo e(__('Preview Generated Sessions & Validate Conflicts')); ?>

                </button>
            </div>

            <div id="recPreviewContainer" class="hidden space-y-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 max-h-56 overflow-y-auto">
                <div class="flex items-center justify-between">
                    <span id="recPreviewSummary" class="text-xs font-bold text-slate-800"></span>
                    <span id="recConflictStatusBadge" class="text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full"></span>
                </div>
                <div id="recConflictWarning" class="hidden p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"></div>
                <div id="recPreviewTableWrapper" class="overflow-x-auto">
                    <table class="w-full text-xs text-left rtl:text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[10px] font-mono text-slate-400 uppercase">
                                <th class="py-1 px-2"><?php echo e(__('Date')); ?></th>
                                <th class="py-1 px-2"><?php echo e(__('Day')); ?></th>
                                <th class="py-1 px-2"><?php echo e(__('Time Window')); ?></th>
                                <th class="py-1 px-2"><?php echo e(__('Status')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="recPreviewTableBody" class="divide-y divide-slate-100 font-mono text-[11px]"></tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('recurringScheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" id="saveRecurringBtn" class="btn-lift px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    <?php echo e(__('Create Recurring Schedule')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="editSessionOverrideModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Edit Session & Recurrence Scope')); ?></h3>
            <button type="button" onclick="closeModal('editSessionOverrideModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="editSessionOverrideForm" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="overrideSessionId" name="session_id">

            
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5">
                <label class="block text-xs font-mono font-bold text-slate-600 uppercase tracking-wider"><?php echo e(__('Modification Scope')); ?> *</label>
                <div class="space-y-2">
                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="this_only" checked class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block"><?php echo e(__('Edit This Session Only')); ?></span>
                            <span class="text-slate-500 text-[11px]"><?php echo e(__('Applies as an individual override. The recurring series remains unchanged.')); ?></span>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="this_and_future" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block"><?php echo e(__('Edit This and Future Sessions')); ?></span>
                            <span class="text-slate-500 text-[11px]"><?php echo e(__('Updates this class and all remaining future sessions in this recurring series.')); ?></span>
                        </div>
                    </label>

                    <label class="p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 cursor-pointer flex items-start gap-3 transition-all has-checked:bg-teal-50 has-checked:border-teal-500">
                        <input type="radio" name="scope" value="all" class="mt-0.5 text-teal-600 focus:ring-teal-500">
                        <div class="text-xs">
                            <span class="font-bold text-slate-900 block"><?php echo e(__('Edit Entire Recurring Schedule')); ?></span>
                            <span class="text-slate-500 text-[11px]"><?php echo e(__('Modifies the schedule template rule across all non-completed sessions.')); ?></span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Session Title')); ?></label>
                <input type="text" id="overrideTitle" name="title" class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Scheduled Date & Time')); ?> *</label>
                    <input type="datetime-local" id="overrideDateTime" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Duration (Minutes)')); ?> *</label>
                    <input type="number" id="overrideDuration" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Broadcast Meeting URL')); ?></label>
                <input type="url" id="overrideMeetingLink" name="meeting_link" placeholder="https://zoom.us/j/..." class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Reason for Modification (Audit Log)')); ?></label>
                <input type="text" id="overrideReason" name="reason" placeholder="<?php echo e(__('e.g. Schedule adjustment per student request')); ?>" class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('editSessionOverrideModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" id="saveOverrideBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    <?php echo e(__('Save Changes')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="cancelSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-5 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-heading font-black text-xl text-rose-600 flex items-center gap-2">
                <span><i class="fa-solid fa-circle-xmark text-rose-500"></i></span> <?php echo e(__('Cancel Session')); ?>

            </h3>
            <button type="button" onclick="closeModal('cancelSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="cancelSessionForm" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="cancelSessionId">
            <p class="text-xs text-slate-600 leading-relaxed font-semibold">
                <?php echo e(__('Are you sure you want to cancel this session? All enrolled students will be immediately notified via push notification and email.')); ?>

            </p>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Cancellation Reason')); ?> *</label>
                <input type="text" id="cancelReasonInput" name="reason" placeholder="<?php echo e(__('e.g. Instructor emergency or holiday rescheduling')); ?>" required class="input-mobile">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('cancelSessionModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"><?php echo e(__('Keep Session')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                    <?php echo e(__('Confirm Cancellation')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="meetingLinkModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Update Live Stream Link')); ?></h3>
            <button type="button" onclick="closeModal('meetingLinkModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="meetingLinkForm" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="linkSessionId" name="session_id">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Meeting Broadcast URL')); ?></label>
                <input type="url" id="meetingUrlInput" name="meeting_link" placeholder="https://vimeo.com/... or Zoom link" required class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('meetingLinkModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    <?php echo e(__('Save Meeting Link')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="rescheduleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Reschedule Teaching Session')); ?></h3>
            <button type="button" onclick="closeModal('rescheduleModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="rescheduleForm" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="rescheduleSessionId">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('New Scheduled Date & Time')); ?></label>
                <input type="datetime-local" id="rescheduleDateTime" name="scheduled_at" required class="input-mobile">
            </div>
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Reason for Rescheduling')); ?></label>
                <input type="text" id="rescheduleReason" name="reason" placeholder="<?php echo e(__('e.g. Time adjustment for upcoming exam revision')); ?>" class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('rescheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    <?php echo e(__('Confirm Reschedule')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="createAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Publish New Assignment & Quiz')); ?></h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5"><?php echo e(__('Create homework assignments or interactive MSQ quizzes for your students.')); ?></p>
            </div>
            <button type="button" onclick="closeModal('createAssignmentModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="createAssignmentForm" action="<?php echo e(route('ajax.teacher.assignments.create')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Target Course')); ?> *</label>
                    <select name="course_id" required class="input-mobile bg-white text-xs">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->title); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Live Session (Optional)')); ?></label>
                    <select name="live_session_id" class="input-mobile bg-white text-xs">
                        <option value=""><?php echo e(__('None / General Course Assignment')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $todaySessions->merge($allSessions)->unique('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($ls->id); ?>"><?php echo e($ls->title ?: __('Live Session')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Assignment Title')); ?> *</label>
                <input type="text" name="title" placeholder="e.g. Unit 2: Physics Waves & Optics Quiz" required class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Description / Instructions')); ?></label>
                <textarea name="description" rows="2" placeholder="<?php echo e(__('Provide guidelines, instructions, or reading materials...')); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Due Date & Time')); ?> *</label>
                    <input type="datetime-local" name="due_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Duration (Minutes)')); ?></label>
                    <input type="number" name="duration_minutes" value="30" min="5" max="300" class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Passing Score (%)')); ?></label>
                    <input type="number" name="passing_score" value="70" min="0" max="100" class="input-mobile">
                </div>
            </div>

            
            <div class="pt-3 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-heading font-black text-sm text-slate-900"><?php echo e(__('Questions & Quiz Builder')); ?></h4>
                        <p class="text-[11px] text-slate-500 font-mono"><?php echo e(__('Add interactive multiple choice questions with automated answer key.')); ?></p>
                    </div>
                    <button type="button" onclick="addTeacherQuestion()" class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 font-extrabold text-xs rounded-xl border border-teal-200 cursor-pointer transition-all">
                        + <?php echo e(__('Add Question')); ?>

                    </button>
                </div>

                <div id="teacherQuestionsContainer" class="space-y-4 pt-2">
                    
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createAssignmentModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    <?php echo e(__('Publish Assignment')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="gradeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Review & Grade Submission')); ?></h3>
                <p id="gradeStudentName" class="text-xs text-teal-600 font-mono font-bold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('gradeModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>

        
        <div class="space-y-3">
            <h4 class="font-heading font-black text-sm text-slate-900 flex items-center gap-2">
                <span><i class="fa-solid fa-bullseye"></i></span> <?php echo e(__('Questions Auto-Correction & Student Choices')); ?>

            </h4>
            <div id="submissionQuestionsContainer" class="space-y-3 max-h-72 overflow-y-auto p-1 scrollbar-thin">
                
            </div>
        </div>

        <form id="gradeForm" method="POST" class="space-y-4 pt-3 border-t border-slate-100">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="gradeSubmissionId" name="submission_id">

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Overall Grade Score (0 - 100%)')); ?> *</label>
                <input type="number" id="gradeScoreInput" name="score" step="0.1" min="0" max="100" required placeholder="e.g. 85.0" class="input-mobile font-mono font-bold text-lg">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5"><?php echo e(__('Pedagogical Feedback & Notes to Student')); ?></label>
                <textarea id="gradeEvaluationNotes" name="evaluation_notes" rows="3" placeholder="<?php echo e(__('Provide detailed feedback, remarks, or praise for the student...')); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-mono focus:bg-white focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('gradeModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    <?php echo e(__('Save & Finalize Grade')); ?> &rarr;
                </button>
            </div>
        </form>
    </div>
</div>




<div id="attendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-5 relative max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 shrink-0">
            <div class="min-w-0 space-y-0.5">
                <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Record Session Attendance')); ?></h3>
                <p id="attendanceSessionTitle" class="text-xs text-teal-600 font-mono font-bold truncate"></p>
            </div>
            <button type="button" onclick="closeModal('attendanceModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg cursor-pointer p-1"><i class="fa-solid fa-xmark"></i></button>
        </div>

        
        <div class="flex items-center justify-between gap-2 shrink-0">
            <label class="block text-[11px] font-mono font-bold text-slate-500 uppercase tracking-wider">
                <?php echo e(__('COHORT STUDENT CHECK-IN')); ?> (<span id="attendanceCohortCount">0</span>)
            </label>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="bulkSetAttendance('present')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg border border-emerald-200 transition-colors cursor-pointer">
                    <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> <?php echo e(__('All Present')); ?>

                </button>
                <button type="button" onclick="bulkSetAttendance('absent')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-800 text-[11px] font-bold rounded-lg border border-rose-200 transition-colors cursor-pointer">
                    <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> <?php echo e(__('All Absent')); ?>

                </button>
            </div>
        </div>

        <form id="attendanceForm" method="POST" class="space-y-4 flex-1 flex flex-col overflow-hidden">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="attendanceSessionId">

            
            <div id="attendanceStudentsContainer" class="divide-y divide-slate-100 overflow-y-auto flex-1 p-1 scrollbar-thin">
                
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 shrink-0">
                <button type="button" onclick="closeModal('attendanceModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer"><?php echo e(__('Cancel')); ?></button>
                <button type="submit" id="saveAttendanceBtn" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center gap-1.5">
                    <span>→</span> <?php echo e(__('Save Attendance Sheet')); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<script>
const isArLocale = <?php echo json_encode(app()->getLocale() === 'ar', 15, 512) ?>;

// Global i18n Dictionary for Dynamic JS Elements
const i18n = {
    studentProfile: <?php echo json_encode(__('Student Profile'), 15, 512) ?>,
    loadingReview: <?php echo json_encode(__('Loading review questions and choices breakdown...'), 15, 512) ?>,
    correct: <?php echo json_encode(__('Correct'), 15, 512) ?>,
    incorrect: <?php echo json_encode(__('Incorrect'), 15, 512) ?>,
    studentCorrectPick: <?php echo json_encode(__('Student Selected (Correct)'), 15, 512) ?>,
    studentWrongPick: <?php echo json_encode(__('Student Selected (Incorrect)'), 15, 512) ?>,
    correctKey: <?php echo json_encode(__('Correct Key'), 15, 512) ?>,
    explanation: <?php echo json_encode(__('Explanation:'), 15, 512) ?>,
    question: <?php echo json_encode(__('Question'), 15, 512) ?>,
    noQuestionBreakdown: <?php echo json_encode(__('No interactive questions logged for this assignment.'), 15, 512) ?>,
    unableToLoadBreakdown: <?php echo json_encode(__('Unable to load submission breakdown.'), 15, 512) ?>,
    noCourses: <?php echo json_encode(__('app.teacher.no_courses_enrolled'), 15, 512) ?>,
    noSessions: <?php echo json_encode(__('app.teacher.no_sessions_found'), 15, 512) ?>,
    noAttendance: <?php echo json_encode(__('app.teacher.no_attendance_found'), 15, 512) ?>,
    noAssignments: <?php echo json_encode(__('app.teacher.no_assignments_found'), 15, 512) ?>,
    noNotes: <?php echo json_encode(__('app.teacher.no_notes_found'), 15, 512) ?>,
    present: <?php echo json_encode(__('Present'), 15, 512) ?>,
    late: <?php echo json_encode(__('Late'), 15, 512) ?>,
    absent: <?php echo json_encode(__('Absent'), 15, 512) ?>,
    excused: <?php echo json_encode(__('Excused'), 15, 512) ?>,
    completed: <?php echo json_encode(__('Completed'), 15, 512) ?>,
    scheduled: <?php echo json_encode(__('Scheduled'), 15, 512) ?>,
    graded: <?php echo json_encode(__('Graded'), 15, 512) ?>,
    pendingReview: <?php echo json_encode(__('Pending Review'), 15, 512) ?>,
    inProgress: <?php echo json_encode(__('In Progress'), 15, 512) ?>,
    passed: <?php echo json_encode(__('Passed <i class="fa-solid fa-check"></i>'), 15, 512) ?>,
    failed: <?php echo json_encode(__('Needs Improvement'), 15, 512) ?>,
};

let currentViewingStudentId = null;

// ── Tab Switcher for Main Teacher Portal ──────────────────────────────────────
function switchTeacherTab(tabKey) {
    document.querySelectorAll('.teacher-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.teacher-tab-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-md', 'active');
        btn.classList.add('text-slate-700', 'hover:bg-slate-100');
    });

    const activeContent = document.getElementById('teacher-tab-' + tabKey);
    const activeBtn = document.getElementById('tab-btn-' + tabKey);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-100');
        activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-md');
    }

    document.querySelectorAll('#portalSidebar .teacher-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === tabKey) {
            btn.classList.add('active');
        }
    });

    // Mobile drawer auto-close
    if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
        togglePortalSidebar(false);
    }
}

// ── Sub-Tab Switcher for Student Profile Modal ────────────────────────────────
function switchSpTab(tabKey) {
    document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.sp-subtab-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
        btn.classList.add('text-slate-700', 'hover:bg-slate-200/60');
    });

    const activePane = document.getElementById('sp-pane-' + tabKey);
    const activeBtn = document.getElementById('sp-tab-btn-' + tabKey);
    if (activePane) activePane.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-200/60');
        activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
    }
}

// ── Open Student Profile & Progressive Educational Data Fetch ─────────────────
async function openStudentDetailsModal(studentUserId) {
    currentViewingStudentId = studentUserId;
    openModal('studentProfileModal');

    // Reset skeleton
    document.getElementById('spLoadingSkeleton').classList.remove('hidden');
    document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
    switchSpTab('overview');

    try {
        const res = await fetch(`/ajax/teacher/students/${studentUserId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (!data.success) {
            showTeacherToast(data.message || 'Unauthorized access', false);
            closeModal('studentProfileModal');
            return;
        }

        document.getElementById('spLoadingSkeleton').classList.add('hidden');
        document.getElementById('sp-pane-overview').classList.remove('hidden');

        // 1. Populate Header
        const st = data.student;
        const metrics = data.metrics || {};
        document.getElementById('spModalAvatar').textContent = (st.name || 'S').substring(0, 1).toUpperCase();
        document.getElementById('spModalName').textContent = st.name || i18n.studentProfile;
        document.getElementById('spModalCode').textContent = '#' + (st.student_code || 'STU-' + studentUserId);
        document.getElementById('spModalMeta').textContent = `${st.school || 'Elite Academy'} • ${st.grade || 'Secondary'} • <i class="fa-solid fa-envelope"></i> ${st.email || ''}`;

        // 2. Populate Overview KPIs
        document.getElementById('spOverviewAttRate').textContent = `${metrics.attendance_rate || 100}%`;
        document.getElementById('spOverviewAvgGrade').textContent = metrics.avg_score !== null ? `${metrics.avg_score}%` : 'N/A';
        document.getElementById('spOverviewTotalSessions').textContent = metrics.total_sessions || 0;
        document.getElementById('spOverviewSubmissions').textContent = metrics.total_submissions || 0;

        // Populate Overview Mini Lists
        const recentSesContainer = document.getElementById('spOverviewRecentSessions');
        if (data.sessions && data.sessions.length > 0) {
            let sHtml = '';
            data.sessions.slice(0, 3).forEach(s => {
                const attColor = s.attendance_status === 'present' ? 'text-emerald-600' : (s.attendance_status === 'late' ? 'text-amber-600' : 'text-rose-600');
                sHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${s.title}</p>
                        <p class="text-[10px] text-slate-400">${s.date}</p>
                    </div>
                    <span class="font-bold uppercase text-[10px] ${attColor}">${s.attendance_status || 'scheduled'}</span>
                </div>`;
            });
            recentSesContainer.innerHTML = sHtml;
        } else {
            recentSesContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noSessions}</p>`;
        }

        const recentSubContainer = document.getElementById('spOverviewRecentSubmissions');
        if (data.submissions && data.submissions.length > 0) {
            let subHtml = '';
            data.submissions.slice(0, 3).forEach(sub => {
                const scoreText = sub.score !== null ? `<span class="font-bold text-emerald-600">${sub.score}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                subHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${sub.assignment_title}</p>
                        <p class="text-[10px] text-slate-400">${sub.submitted_at || 'Draft'}</p>
                    </div>
                    <div>${scoreText}</div>
                </div>`;
            });
            recentSubContainer.innerHTML = subHtml;
        } else {
            recentSubContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noAssignments}</p>`;
        }

        // 3. Populate Courses Tab
        const coursesList = document.getElementById('spCoursesList');
        if (data.courses && data.courses.length > 0) {
            let cHtml = '';
            data.courses.forEach(c => {
                cHtml += `<div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="font-heading font-bold text-sm text-slate-900">${c.title}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 text-teal-800">${c.status}</span>
                    </div>
                    <p class="text-xs font-mono text-slate-500">${c.subject} • ${c.grade} • Enrolled: ${c.enrolled_at}</p>
                    <div class="space-y-1 pt-1">
                        <div class="flex justify-between text-[11px] font-mono text-slate-600">
                            <span>Syllabus Progress</span>
                            <span class="font-bold">${c.completed_sessions} / ${c.sessions_count} sessions (${c.progress_pct}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-teal-600 h-full rounded-full" style="width: ${c.progress_pct}%"></div>
                        </div>
                    </div>
                </div>`;
            });
            coursesList.innerHTML = cHtml;
        } else {
            coursesList.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noCourses}</p>`;
        }

        // 4. Populate Sessions Tab
        const sesTableBody = document.getElementById('spSessionsTableBody');
        if (data.sessions && data.sessions.length > 0) {
            let sesHtml = '';
            data.sessions.forEach(s => {
                const attBadge = s.attendance_status === 'present' 
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> Present</span>'
                    : (s.attendance_status === 'late'
                        ? '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold"><i class="fa-solid fa-circle text-amber-500 text-[10px]"></i> Late</span>'
                        : '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> Absent</span>');
                sesHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${s.title}</td>
                    <td class="py-2.5 px-3 text-slate-600">${s.course_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${s.date}</td>
                    <td class="py-2.5 px-3 font-mono">${attBadge}</td>
                </tr>`;
            });
            sesTableBody.innerHTML = sesHtml;
        } else {
            sesTableBody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noSessions}</td></tr>`;
        }

        // 5. Populate Attendance Tab
        const attTableBody = document.getElementById('spAttendanceTableBody');
        if (data.attendance && data.attendance.length > 0) {
            let attHtml = '';
            data.attendance.forEach(a => {
                const statusStr = a.attendance_status || 'scheduled';
                attHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${a.title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                    <td class="py-2.5 px-3 font-mono text-right rtl:text-left">
                        <span class="font-bold uppercase text-[10px] px-2 py-0.5 rounded-full ${statusStr === 'present' ? 'bg-emerald-100 text-emerald-800' : (statusStr === 'late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')}">${statusStr}</span>
                    </td>
                </tr>`;
            });
            attTableBody.innerHTML = attHtml;
        } else {
            attTableBody.innerHTML = `<tr><td colspan="3" class="py-4 text-center text-slate-400 italic">${i18n.noAttendance}</td></tr>`;
        }

        // 6. Populate Assignments Tab
        const assignTableBody = document.getElementById('spAssignmentsTableBody');
        if (data.submissions && data.submissions.length > 0) {
            let assHtml = '';
            data.submissions.forEach(sub => {
                const scoreDisplay = sub.score !== null ? `<span class="font-bold text-emerald-600">${sub.score}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                assHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${sub.assignment_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${sub.submitted_at || 'Draft'}</td>
                    <td class="py-2.5 px-3 font-mono">${scoreDisplay}</td>
                    <td class="py-2.5 px-3 text-right rtl:text-left">
                        <button type="button" onclick="openGradeModal(${sub.id}, '${st.name ? st.name.replace(/'/g, "\\'") : ''}', '${sub.assignment_title.replace(/'/g, "\\'")}', '${sub.score}', '${sub.evaluation_notes ? sub.evaluation_notes.replace(/'/g, "\\'") : ''}')" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] rounded-lg border border-teal-200">
                            <i class="fa-solid fa-magnifying-glass"></i> Review
                        </button>
                    </td>
                </tr>`;
            });
            assignTableBody.innerHTML = assHtml;
        } else {
            assignTableBody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noAssignments}</td></tr>`;
        }

        // 7. Populate Assessments & Quizzes Tab
        const assessContainer = document.getElementById('spAssessmentsContainer');
        if (data.assessments && data.assessments.length > 0) {
            let assessHtml = '';
            data.assessments.forEach(ass => {
                const passBadge = ass.is_passed 
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-check"></i> Passed</span>'
                    : (ass.score !== null 
                        ? '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-xmark me-1"></i> Retake</span>'
                        : '<span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">Pending</span>');
                assessHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-900">${ass.assignment_title}</p>
                        <p class="text-[10px] font-mono text-slate-500">${ass.course_title} • ${ass.submitted_at}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-extrabold text-sm ${ass.score >= 70 ? 'text-emerald-600' : 'text-slate-800'}">${ass.score !== null ? ass.score + '%' : 'N/A'}</span>
                        ${passBadge}
                    </div>
                </div>`;
            });
            assessContainer.innerHTML = assessHtml;
        } else {
            assessContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noAssignments}</p>`;
        }

        // 8. Populate Progress & Analytics Tab
        const progContainer = document.getElementById('spProgressContainer');
        progContainer.innerHTML = `
            <div class="p-5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-heading font-black text-sm text-slate-900">Comprehensive Academic Health</h4>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-mono">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Attendance Consistency</span>
                        <span class="font-extrabold text-base text-emerald-600">${metrics.attendance_rate || 100}%</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Assessment Pass Rate</span>
                        <span class="font-extrabold text-base text-teal-600">${metrics.pass_rate || 100}%</span>
                    </div>
                </div>
            </div>
        `;

        // 9. Populate Notes Tab
        renderEducationalNotes(data.notes || []);

    } catch (err) {
        document.getElementById('spLoadingSkeleton').innerHTML = `<p class="text-xs text-rose-600 italic py-6">Failed to load student details.</p>`;
    }
}

function renderEducationalNotes(notes) {
    const container = document.getElementById('spNotesContainer');
    if (!container) return;

    if (notes && notes.length > 0) {
        let nHtml = '';
        notes.forEach(n => {
            const catBadge = {
                academic: '<span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded-full text-[10px] font-bold">Academic</span>',
                homework: '<span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold">Homework</span>',
                participation: '<span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full text-[10px] font-bold">Participation</span>',
                behavior: '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">Behavior</span>',
                general: '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">General</span>',
            }[n.category] || '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">Note</span>';

            nHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    ${catBadge}
                    <span class="text-[10px] font-mono text-slate-400">${n.created_at_human || n.created_at}</span>
                </div>
                <p class="text-slate-800 font-medium leading-relaxed">${n.note}</p>
            </div>`;
        });
        container.innerHTML = nHtml;
    } else {
        container.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noNotes}</p>`;
    }
}

// ── Open Add Note Modal for Current Student ──────────────────────────────────
function openAddNoteModal() {
    if (!currentViewingStudentId) return;
    document.getElementById('noteStudentUserId').value = currentViewingStudentId;
    document.getElementById('addNoteForm').reset();
    openModal('addNoteModal');
}

// ── Client-Side Real-Time Filter for My Students Roster ───────────────────────
function applyStudentFilters() {
    const searchVal = (document.getElementById('studentSearchInput')?.value || '').trim().toLowerCase();
    const courseVal = document.getElementById('studentCourseFilter')?.value || '';
    const gradeVal = document.getElementById('studentGradeFilter')?.value || '';
    const attVal = document.getElementById('studentAttendanceFilter')?.value || '';

    const cards = document.querySelectorAll('.student-roster-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const code = card.getAttribute('data-code') || '';
        const school = card.getAttribute('data-school') || '';
        const courses = (card.getAttribute('data-courses') || '').split(',');
        const grade = card.getAttribute('data-grade') || '';
        const attendance = parseInt(card.getAttribute('data-attendance') || '100', 10);

        let matchSearch = !searchVal || name.includes(searchVal) || code.includes(searchVal) || school.includes(searchVal);
        let matchCourse = !courseVal || courses.includes(courseVal);
        let matchGrade = !gradeVal || grade === gradeVal;
        let matchAtt = !attVal || (attVal === 'good' && attendance >= 80) || (attVal === 'risk' && attendance < 80);

        if (matchSearch && matchCourse && matchGrade && matchAtt) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    const emptyState = document.getElementById('studentEmptySearchState');
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    const countText = document.getElementById('studentFilterCountText');
    if (countText) {
        countText.textContent = `Showing ${visibleCount} of ${cards.length} students`;
    }
}

function resetStudentFilters() {
    if (document.getElementById('studentSearchInput')) document.getElementById('studentSearchInput').value = '';
    if (document.getElementById('studentCourseFilter')) document.getElementById('studentCourseFilter').value = '';
    if (document.getElementById('studentGradeFilter')) document.getElementById('studentGradeFilter').value = '';
    if (document.getElementById('studentAttendanceFilter')) document.getElementById('studentAttendanceFilter').value = '';
    applyStudentFilters();
}

// ── Open Modals & Action Helpers ─────────────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('hidden');
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('hidden');
}

function openCreateSessionModal() {
    openModal('createSessionModal');
}

function openCreateAssignmentModal() {
    openModal('createAssignmentModal');
}

function openMeetingLinkModal(sessionId, currentLink) {
    document.getElementById('linkSessionId').value = sessionId;
    document.getElementById('meetingUrlInput').value = currentLink || '';
    document.getElementById('meetingLinkForm').action = `/ajax/teacher/sessions/${sessionId}/link`;
    openModal('meetingLinkModal');
}

function openRescheduleModal(sessionId, currentDateTime) {
    document.getElementById('rescheduleSessionId').value = sessionId;
    document.getElementById('rescheduleDateTime').value = currentDateTime || '';
    document.getElementById('rescheduleForm').action = `/ajax/teacher/sessions/${sessionId}/reschedule`;
    openModal('rescheduleModal');
}

async function openAttendanceModal(sessionId, sessionTitle) {
    document.getElementById('attendanceSessionId').value = sessionId;
    document.getElementById('attendanceSessionTitle').textContent = sessionTitle || 'Loading...';
    document.getElementById('attendanceForm').action = `/ajax/teacher/sessions/${sessionId}/attendance`;

    const container = document.getElementById('attendanceStudentsContainer');
    container.innerHTML = `
        <div class="py-12 text-center space-y-2">
            <svg class="animate-spin h-6 w-6 text-teal-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-xs font-mono text-slate-500 font-bold">${i18n.loadingReview || 'Loading registered students...'}</p>
        </div>
    `;
    document.getElementById('attendanceCohortCount').textContent = '...';

    openModal('attendanceModal');

    try {
        const res = await fetch(`/ajax/teacher/sessions/${sessionId}/attendance-roster`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (!data.success) {
            showTeacherToast(data.message || 'Unauthorized', false);
            closeModal('attendanceModal');
            return;
        }

        if (data.session) {
            document.getElementById('attendanceSessionTitle').textContent = `${data.session.title} ${data.session.course_title ? '— ' + data.session.course_title : ''}`;
        }

        const students = data.students || [];
        document.getElementById('attendanceCohortCount').textContent = students.length;

        if (students.length === 0) {
            container.innerHTML = `
                <div class="py-10 text-center space-y-2">
                    <span class="text-2xl"><i class="fa-solid fa-users"></i></span>
                    <p class="text-xs font-semibold text-slate-700">${isArLocale ? 'لا يوجد طلاب مسجلين في هذا الكورس حالياً.' : 'No students enrolled in this course yet.'}</p>
                    <p class="text-[10px] font-mono text-slate-400">${isArLocale ? 'سيظهر الطلاب المسجلون تلقائياً بمجرد اشتراكهم.' : 'Enrolled students will appear here automatically.'}</p>
                </div>
            `;
            return;
        }

        let html = '';
        students.forEach((st, idx) => {
            const isPresent = st.status === 'present';
            const isLate = st.status === 'late';
            const isExcused = st.status === 'excused';
            const isAbsent = st.status === 'absent';

            html += `
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            ${(st.name || 'S').substring(0, 1).toUpperCase()}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 truncate">${st.name}</p>
                            <p class="text-[10px] font-mono text-slate-500 truncate">${st.school || 'Elite Academy'} ${st.grade ? '• ' + st.grade : ''}</p>
                        </div>
                    </div>

                    <input type="hidden" name="attendance[${idx}][student_user_id]" value="${st.id}">
                    <div class="shrink-0">
                        <select name="attendance[${idx}][status]" onchange="onAttendanceStatusChange('${st.name ? st.name.replace(/'/g, "\\'") : ''}', this.value)" class="attendance-status-select bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-600 shadow-2xs cursor-pointer">
                            <option value="present" ${isPresent ? 'selected' : ''}><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> ${isArLocale ? 'حاضر (Present)' : 'Present'}</option>
                            <option value="late" ${isLate ? 'selected' : ''}><i class="fa-solid fa-circle text-amber-500 text-[10px]"></i> ${isArLocale ? 'متأخر (Late)' : 'Late'}</option>
                            <option value="excused" ${isExcused ? 'selected' : ''}><i class="fa-solid fa-circle text-slate-300 text-[10px]"></i> ${isArLocale ? 'معذور (Excused)' : 'Excused'}</option>
                            <option value="absent" ${isAbsent ? 'selected' : ''}><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> ${isArLocale ? 'غائب (Absent)' : 'Absent'}</option>
                        </select>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

    } catch (err) {
        container.innerHTML = `<p class="text-xs text-rose-600 italic text-center py-6">Failed to load real-time attendance roster.</p>`;
    }
}

function onAttendanceStatusChange(studentName, newStatus) {
    const statusMap = {
        present: {
            title: isArLocale ? 'تسجيل حضور' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ حاضر <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>` : `Marked (${studentName}) as Present <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>`,
            type: 'success'
        },
        late: {
            title: isArLocale ? 'تسجيل تأخير' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ متأخر <i class="fa-solid fa-circle text-amber-500 text-[10px]"></i>` : `Marked (${studentName}) as Late <i class="fa-solid fa-circle text-amber-500 text-[10px]"></i>`,
            type: 'warning'
        },
        excused: {
            title: isArLocale ? 'تسجيل عذر' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ معذور <i class="fa-solid fa-circle text-slate-300 text-[10px]"></i>` : `Marked (${studentName}) as Excused <i class="fa-solid fa-circle text-slate-300 text-[10px]"></i>`,
            type: 'info'
        },
        absent: {
            title: isArLocale ? 'تسجيل غياب' : 'Attendance Check',
            msg: isArLocale ? `تم تحديد الطالب (${studentName}) كـ غائب <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>` : `Marked (${studentName}) as Absent <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>`,
            type: 'danger'
        },
    };

    const cfg = statusMap[newStatus] || { title: 'Attendance', msg: 'Status updated', type: 'info' };
    if (window.Toast) {
        window.Toast.show({
            type: cfg.type,
            title: cfg.title,
            message: cfg.msg,
            duration: 2800
        });
    }
}

function bulkSetAttendance(status) {
    document.querySelectorAll('.attendance-status-select').forEach(sel => {
        sel.value = status;
    });

    if (window.Toast) {
        if (status === 'present') {
            window.Toast.success(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ حضور <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>' : 'All students marked as Present <i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i>', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        } else if (status === 'absent') {
            window.Toast.danger(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ غياب <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>' : 'All students marked as Absent <i class="fa-solid fa-circle text-rose-500 text-[10px]"></i>', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 3000);
        }
    }
}

async function confirmCancelSession(sessionId) {
    if (!confirm(<?php echo json_encode(__('Are you sure you want to cancel this live session? Affected students will be notified immediately.'), 15, 512) ?>)) {
        return;
    }

    try {
        const res = await fetch(`/ajax/teacher/sessions/${sessionId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        showTeacherToast(data.message, data.success);
        if (data.success) {
            setTimeout(() => location.reload(), 900);
        }
    } catch (err) {
        showTeacherToast('Failed to cancel session', false);
    }
}

async function openGradeModal(submissionId, studentName, assignmentTitle, currentScore, evaluationNotes) {
    document.getElementById('gradeSubmissionId').value = submissionId;
    document.getElementById('gradeStudentName').textContent = `${studentName} — ${assignmentTitle}`;
    document.getElementById('gradeScoreInput').value = currentScore && currentScore !== 'null' ? currentScore : '';
    const notesEl = document.getElementById('gradeEvaluationNotes');
    if (notesEl) notesEl.value = evaluationNotes && evaluationNotes !== 'null' ? evaluationNotes : '';
    document.getElementById('gradeForm').action = `/ajax/teacher/submissions/${submissionId}/review`;

    const questionsContainer = document.getElementById('submissionQuestionsContainer');
    questionsContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">${i18n.loadingReview}</p>`;

    openModal('gradeModal');

    try {
        const res = await fetch(`/ajax/teacher/submissions/${submissionId}/review-details`);
        const data = await res.json();
        if (data.success && data.questions && data.questions.length > 0) {
            let html = '';
            data.questions.forEach((q, idx) => {
                const statusBadge = q.is_correct
                    ? `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> ${i18n.correct} (+${q.points_earned}/${q.points} pts)</span>`
                    : `<span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> ${i18n.incorrect} (0/${q.points} pts)</span>`;

                let optsHtml = '';
                q.options.forEach(opt => {
                    let optStyle = 'bg-white border-slate-200 text-slate-700';
                    let badge = '';

                    if (opt.is_correct && opt.is_selected) {
                        optStyle = 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold';
                        badge = `<span class="text-emerald-600 font-mono text-[10px]">${i18n.studentCorrectPick}</span>`;
                    } else if (opt.is_correct) {
                        optStyle = 'bg-teal-50 border-teal-300 text-teal-900 font-bold';
                        badge = `<span class="text-teal-600 font-mono text-[10px]">${i18n.correctKey}</span>`;
                    } else if (opt.is_selected) {
                        optStyle = 'bg-red-50 border-red-300 text-red-900 font-bold';
                        badge = `<span class="text-red-600 font-mono text-[10px]">${i18n.studentWrongPick}</span>`;
                    }

                    optsHtml += `<div class="p-2.5 rounded-xl border ${optStyle} text-xs flex items-center justify-between gap-2">
                        <span>${opt.option_text}</span>
                        ${badge}
                    </div>`;

                    if (opt.explanation && opt.is_correct) {
                        optsHtml += `<p class="text-[11px] text-slate-500 font-mono italic pl-2">${i18n.explanation} ${opt.explanation}</p>`;
                    }
                });

                html += `<div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900">Q${idx + 1}: ${q.question_text || i18n.question}</span>
                        ${statusBadge}
                    </div>
                    <div class="space-y-1.5 pt-1">
                        ${optsHtml}
                    </div>
                </div>`;
            });
            questionsContainer.innerHTML = html;
        } else {
            questionsContainer.innerHTML = `<p class="text-xs text-slate-500 italic text-center py-4">${i18n.noQuestionBreakdown}</p>`;
        }
    } catch (err) {
        questionsContainer.innerHTML = `<p class="text-xs text-red-500 italic text-center py-4">${i18n.unableToLoadBreakdown}</p>`;
    }
}

// ── Interactive Question Builder for Assignment Creator ───────────────────────
let teacherQuestionCount = 0;
function addTeacherQuestion() {
    const container = document.getElementById('teacherQuestionsContainer');
    if (!container) return;

    const qIdx = teacherQuestionCount++;
    const isAr = <?php echo json_encode(app()->getLocale() === 'ar', 15, 512) ?>;

    const html = `
    <div class="teacher-q-card p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 relative" id="teacherQCard_${qIdx}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-extrabold text-teal-900 bg-teal-100 px-3 py-1 rounded-full border border-teal-200">
                ${isAr ? 'السؤال رقم ' : 'Question #'}${qIdx + 1}
            </span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 text-xs font-mono">
                    <span class="text-slate-500">${isAr ? 'الدرجة:' : 'Pts:'}</span>
                    <input type="number" step="0.5" name="questions[${qIdx}][points]" value="1" min="0.5" class="w-14 bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-center">
                </div>
                <button type="button" onclick="removeTeacherQuestion(${qIdx})" class="text-rose-500 hover:text-rose-700 text-xs font-bold font-mono px-2 py-1 rounded-lg hover:bg-rose-50 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i> ${isAr ? 'حذف' : 'Remove'}
                </button>
            </div>
        </div>

        <div>
            <textarea name="questions[${qIdx}][question_text]" rows="2" required placeholder="${isAr ? 'اكتب نص السؤال هنا...' : 'Type question text here...'}" class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-xs font-mono focus:outline-none focus:border-teal-600"></textarea>
        </div>

        <div class="space-y-2 pt-1 border-t border-slate-200/60">
            <p class="text-[10px] font-mono text-slate-500 font-bold">${isAr ? 'الخيارات (حدد الدائرة بجانب الإجابة الصحيحة):' : 'Answer Choices (Select radio button for the correct option):'}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                ${[0, 1, 2, 3].map(optIdx => `
                    <div class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200">
                        <input type="radio" name="questions[${qIdx}][correct_index]" value="${optIdx}" ${optIdx === 0 ? 'checked' : ''} class="text-teal-600 focus:ring-teal-500 cursor-pointer">
                        <input type="text" name="questions[${qIdx}][options][${optIdx}]" required placeholder="${isAr ? 'الخيار ' + String.fromCharCode(65 + optIdx) : 'Option ' + String.fromCharCode(65 + optIdx)}" class="w-full text-xs font-mono border-0 focus:ring-0 p-0 text-slate-800">
                    </div>
                `).join('')}
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
}

function removeTeacherQuestion(qIdx) {
    const el = document.getElementById('teacherQCard_' + qIdx);
    if (el) el.remove();
}

function showTeacherToast(message, isSuccess) {
    if (window.Toast) {
        if (isSuccess) {
            window.Toast.success(message);
        } else {
            window.Toast.danger(message);
        }
    } else {
        const toast = document.getElementById('teacherToastAlert');
        if (!toast) return;
        toast.className = `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
        toast.textContent = message;
        toast.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function bindAjaxForm(formId, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });
            const data = await res.json();
            if (data.success) {
                onSuccess(data);
            } else {
                showTeacherToast(data.message || 'Validation error', false);
            }
        } catch (err) {
            showTeacherToast('Network connection error', false);
        }
    });
}

// ── DOM Initializations ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto-switch tab from URL query param (e.g. ?tab=students)
    const urlParams = new URLSearchParams(window.location.search);
    const requestedTab = urlParams.get('tab');
    if (requestedTab) {
        switchTeacherTab(requestedTab);
    }

    // 2. Check for initial student modal open (e.g. ?student=123)
    const rootEl = document.getElementById('teacher-portal-root');
    const initStudent = rootEl ? rootEl.getAttribute('data-initial-student') : null;
    if (initStudent) {
        switchTeacherTab('students');
        openStudentDetailsModal(initStudent);
    }

    // 3. Counter Animations
    const counters = document.querySelectorAll('.js-counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target') || '0', 10);
        if (target === 0) return;
        let count = 0;
        const step = Math.max(1, Math.ceil(target / 25));
        const timer = setInterval(() => {
            count += step;
            if (count >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = count.toLocaleString();
            }
        }, 30);
    });

    // 4. Attach Student Search & Filter Listeners
    const sInput = document.getElementById('studentSearchInput');
    if (sInput) sInput.addEventListener('input', applyStudentFilters);
    const cFilter = document.getElementById('studentCourseFilter');
    if (cFilter) cFilter.addEventListener('change', applyStudentFilters);
    const gFilter = document.getElementById('studentGradeFilter');
    if (gFilter) gFilter.addEventListener('change', applyStudentFilters);
    const aFilter = document.getElementById('studentAttendanceFilter');
    if (aFilter) aFilter.addEventListener('change', applyStudentFilters);

    // Helper function to detect phone numbers in client JS
    function clientHasPhoneNumber(text) {
        if (!text) return false;
        const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        let norm = text;
        for (let i = 0; i < 10; i++) {
            norm = norm.replaceAll(eastern[i], i.toString());
        }
        if (/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i.test(norm)) return true;
        if (/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i.test(norm)) return true;
        if (/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/.test(norm)) return true;
        if (/\b[0-9]{8,16}\b/.test(norm)) return true;
        return false;
    }

    const noteTextarea = document.getElementById('noteContentTextarea');
    const phoneWarning = document.getElementById('notePhoneWarning');
    if (noteTextarea && phoneWarning) {
        noteTextarea.addEventListener('input', function () {
            const hasPhone = clientHasPhoneNumber(noteTextarea.value);
            if (hasPhone) {
                phoneWarning.classList.remove('hidden');
                noteTextarea.classList.add('border-rose-500', 'bg-rose-50/20');
                noteTextarea.classList.remove('border-slate-200');
            } else {
                phoneWarning.classList.add('hidden');
                noteTextarea.classList.remove('border-rose-500', 'bg-rose-50/20');
                noteTextarea.classList.add('border-slate-200');
            }
        });
    }

    // 5. Educational Note AJAX Form Handler with Security Policy
    const noteForm = document.getElementById('addNoteForm');
    if (noteForm) {
        noteForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sId = document.getElementById('noteStudentUserId').value;
            if (!sId) return;

            const noteVal = noteTextarea ? noteTextarea.value : '';
            if (clientHasPhoneNumber(noteVal)) {
                if (window.Toast) {
                    window.Toast.danger(
                        isArLocale 
                            ? 'لا يمكنك إرسال أرقام الهواتف أو وسائل التواصل في الملاحظات التعليمية حرصاً على الأمان والخصوصية' 
                            : 'Security Alert: Sharing phone numbers or contact details in notes is prohibited.',
                        isArLocale ? 'تنبيه أمان وخصوصية <i class="fa-solid fa-shield-halved"></i>' : 'Security Violation'
                    );
                }
                return;
            }

            const formData = new FormData(noteForm);
            const submitBtn = document.getElementById('saveNoteBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
            }

            try {
                const res = await fetch(`/ajax/teacher/students/${sId}/notes`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    closeModal('addNoteModal');
                    noteForm.reset();
                    if (phoneWarning) phoneWarning.classList.add('hidden');
                    if (noteTextarea) noteTextarea.classList.remove('border-rose-500', 'bg-rose-50/20');
                    if (currentViewingStudentId == sId) {
                        openStudentDetailsModal(sId);
                    }
                } else {
                    const msg = data.message || (data.errors && data.errors.note ? data.errors.note[0] : 'Failed to save note');
                    if (window.Toast) {
                        window.Toast.danger(msg, isArLocale ? 'تنبيه أمان <i class="fa-solid fa-shield-halved"></i>' : 'Security Alert');
                    } else {
                        showTeacherToast(msg, false);
                    }
                }
            } catch (err) {
                showTeacherToast('Network connection error', false);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `${isArLocale ? 'حفظ الملاحظة' : 'Save Note'} &rarr;`;
                }
            }
        });
    }

    // ── Recurring Schedule Helpers ──────────────────────────────────────────
    window.openCreateRecurringModal = function() {
        openModal('recurringScheduleModal');
    };

    window.toggleRecurrenceFields = function(type) {
        const daysContainer = document.getElementById('recDaysContainer');
        if (daysContainer) {
            if (type === 'monthly' || type === 'single') {
                daysContainer.classList.add('hidden');
            } else {
                daysContainer.classList.remove('hidden');
            }
        }
    };

    window.previewRecurringDates = async function() {
        const form = document.getElementById('recurringScheduleForm');
        if (!form) return;

        const formData = new FormData(form);
        const previewContainer = document.getElementById('recPreviewContainer');
        const previewSummary = document.getElementById('recPreviewSummary');
        const conflictBadge = document.getElementById('recConflictStatusBadge');
        const conflictWarning = document.getElementById('recConflictWarning');
        const tableBody = document.getElementById('recPreviewTableBody');

        if (previewContainer) previewContainer.classList.remove('hidden');
        if (previewSummary) previewSummary.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري فحص المواعيد والتعارضات...' : 'Validating dates and conflicts...'}`;

        try {
            const res = await fetch('<?php echo e(route("ajax.teacher.recurring.preview")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });
            const data = await res.json();

            if (!res.ok || !data.success) {
                const errMsg = data.message || 'Validation failed';
                if (previewSummary) previewSummary.textContent = errMsg;
                if (conflictBadge) {
                    conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                    conflictBadge.textContent = '<i class="fa-solid fa-circle-xmark text-rose-500"></i> Error';
                }
                return;
            }

            if (previewSummary) {
                previewSummary.textContent = isArLocale 
                    ? `إجمالي الحصص المتولدة: ${data.total_sessions} حصة` 
                    : `Total Generated Sessions: ${data.total_sessions}`;
            }

            if (data.has_conflicts) {
                conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 animate-pulse';
                conflictBadge.textContent = isArLocale ? '<i class="fa-solid fa-triangle-exclamation"></i> يوجد تعارض في المواعيد' : '<i class="fa-solid fa-triangle-exclamation"></i> Schedule Conflicts Detected';
                conflictWarning.classList.remove('hidden');
                conflictWarning.innerHTML = `<span><i class="fa-solid fa-triangle-exclamation"></i> ${isArLocale ? 'تنبيه: بعض الحصص المقترحة تتعارض مع حصص سابقة لنفس المعلم أو الطالب.' : 'Warning: Some proposed sessions conflict with existing schedules.'}</span>`;
            } else {
                conflictBadge.className = 'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                conflictBadge.textContent = isArLocale ? '<i class="fa-solid fa-check"></i> المواعيد متاحة بدون تعارض' : '<i class="fa-solid fa-check"></i> All Slots Available';
                conflictWarning.classList.add('hidden');
            }

            if (tableBody) {
                tableBody.innerHTML = (data.dates || []).map(d => `
                    <tr class="hover:bg-white/80 ${d.has_conflict ? 'bg-rose-50/60 text-rose-900' : ''}">
                        <td class="py-1.5 px-2 font-bold">${d.date}</td>
                        <td class="py-1.5 px-2 text-slate-500">${d.day_name}</td>
                        <td class="py-1.5 px-2">${d.start_time} - ${d.end_time}</td>
                        <td class="py-1.5 px-2 font-bold ${d.has_conflict ? 'text-rose-600' : 'text-emerald-600'}">
                            ${d.has_conflict ? '<i class="fa-solid fa-triangle-exclamation"></i> ' + (isArLocale ? 'تعارض' : 'Conflict') : '<i class="fa-solid fa-check"></i> ' + (isArLocale ? 'متاح' : 'Available')}
                        </td>
                    </tr>
                `).join('');
            }

        } catch (err) {
            if (previewSummary) previewSummary.textContent = 'Connection error';
        }
    };

    window.openEditSessionOverrideModal = function(sessionId, title, scheduledAt, duration, meetingLink, notes) {
        document.getElementById('overrideSessionId').value = sessionId;
        document.getElementById('overrideTitle').value = title || '';
        document.getElementById('overrideDateTime').value = scheduledAt || '';
        document.getElementById('overrideDuration').value = duration || 60;
        document.getElementById('overrideMeetingLink').value = meetingLink || '';
        document.getElementById('overrideReason').value = '';
        openModal('editSessionOverrideModal');
    };

    window.confirmCancelSession = function(sessionId) {
        document.getElementById('cancelSessionId').value = sessionId;
        document.getElementById('cancelReasonInput').value = '';
        openModal('cancelSessionModal');
    };

    // ── Form Handlers ────────────────────────────────────────────────────────
    bindAjaxForm('createSessionForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createSessionModal');
        setTimeout(() => location.reload(), 900);
    });

    const recForm = document.getElementById('recurringScheduleForm');
    if (recForm) {
        recForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('saveRecurringBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري إنشاء الجدول والحصص...' : 'Generating sessions...'}`;
            }

            try {
                const formData = new FormData(recForm);
                const res = await fetch('<?php echo e(route("ajax.teacher.recurring.create")); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    closeModal('recurringScheduleModal');
                    setTimeout(() => location.reload(), 900);
                } else {
                    showTeacherToast(data.message || 'Failed to create schedule', false);
                }
            } catch (err) {
                showTeacherToast('Connection error', false);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `${isArLocale ? 'إنشاء جدول الحصص المتكرر' : 'Create Recurring Schedule'} &rarr;`;
                }
            }
        });
    }

    const overrideForm = document.getElementById('editSessionOverrideForm');
    if (overrideForm) {
        overrideForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sId = document.getElementById('overrideSessionId').value;
            const submitBtn = document.getElementById('saveOverrideBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
            }

            try {
                const formData = new FormData(overrideForm);
                const res = await fetch(`/ajax/teacher/sessions/${sId}/override`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    closeModal('editSessionOverrideModal');
                    setTimeout(() => location.reload(), 900);
                } else {
                    showTeacherToast(data.message || 'Failed to update session', false);
                }
            } catch (err) {
                showTeacherToast('Connection error', false);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `${isArLocale ? 'حفظ التغييرات' : 'Save Changes'} &rarr;`;
                }
            }
        });
    }

    const cancelForm = document.getElementById('cancelSessionForm');
    if (cancelForm) {
        cancelForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sId = document.getElementById('cancelSessionId').value;
            const formData = new FormData(cancelForm);

            try {
                const res = await fetch(`/ajax/teacher/sessions/${sId}/cancel`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showTeacherToast(data.message, true);
                    closeModal('cancelSessionModal');
                    setTimeout(() => location.reload(), 900);
                } else {
                    showTeacherToast(data.message || 'Failed to cancel session', false);
                }
            } catch (err) {
                showTeacherToast('Connection error', false);
            }
        });
    }

    bindAjaxForm('createAssignmentForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createAssignmentModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('meetingLinkForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('meetingLinkModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('rescheduleForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('rescheduleModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('gradeForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('gradeModal');
        setTimeout(() => location.reload(), 900);
    });

    bindAjaxForm('attendanceForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('attendanceModal');
        setTimeout(() => location.reload(), 900);
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/teacher-portal.blade.php ENDPATH**/ ?>