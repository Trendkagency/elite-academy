<?php $__env->startSection('content'); ?>
<?php
    $name = $subject ? $subject->getLocalizedName() : __('Subject Details');
    $description = $subject ? ($subject->getLocalizedDescription() ?: __('Comprehensive curriculum covering core topics prepared for national curriculum standards.')) : __('Comprehensive curriculum covering core topics prepared for national curriculum standards.');
    $categoryName = $subject?->category ? $subject->category->getLocalizedName() : __('General Curriculum');

    $coursesCount = isset($activeCoursesCount) ? $activeCoursesCount : ($subject?->getActiveCoursesCount() ?? 0);
    $lessonsCount = isset($videoLessonsCount) ? $videoLessonsCount : ($subject?->getVideoLessonsCount() ?? 0);
    $studentsCount = isset($activeStudentsCount) ? $activeStudentsCount : ($subject?->getActiveStudentsCount() ?? 0);
    $rating = isset($ratingAvg) ? $ratingAvg : ($subject?->getRatingAvg() ?? 4.9);

    $image = $subject ? ($subject->image ?: 'images/course_ai.png') : 'images/course_ai.png';
?>


<section class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden">
    <img src="<?php echo e(media_url($image, 'images/course_ai.png')); ?>" alt="<?php echo e($name); ?> Cover" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/80 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('Home'), 'route' => 'home'],
                ['label' => __('Subjects'), 'route' => 'subjects'],
                ['label' => $name],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-mono font-extrabold text-white bg-teal-600 px-3.5 py-1.5 rounded-full shadow-md">
                    <?php echo e($categoryName); ?>

                </span>
                <span class="text-xs font-mono font-bold text-teal-300 bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                    <?php echo e(__('Term 1 & Term 2')); ?>

                </span>
            </div>

            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight">
                <?php echo e($name); ?>

            </h1>
            <p class="text-slate-300 text-base sm:text-xl font-medium leading-relaxed">
                <?php echo e($description); ?>

            </p>
        </div>

        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 max-w-4xl text-center">
            <div>
                <p class="font-heading font-black text-3xl text-teal-400"><?php echo e(number_format($lessonsCount)); ?></p>
                <p class="text-xs font-mono text-slate-300 font-semibold"><?php echo e(__('Video Lessons')); ?></p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-orange-400"><?php echo e(number_format($coursesCount)); ?></p>
                <p class="text-xs font-mono text-slate-300 font-semibold"><?php echo e(__('Active Courses')); ?></p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-teal-400"><?php echo e($studentsCount > 0 ? '+' . number_format($studentsCount) : '0'); ?></p>
                <p class="text-xs font-mono text-slate-300 font-semibold"><?php echo e(__('Active Students')); ?></p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-amber-400"><?php echo e(number_format($rating, 1)); ?> ★</p>
                <p class="text-xs font-mono text-slate-300 font-semibold"><?php echo e(__('Student Rating')); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900"><?php echo e(__('About the Curriculum')); ?></h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <?php echo e($description); ?>

                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900"><?php echo e(__('Courses in')); ?> <?php echo e($name); ?></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject && $subject->courses && $subject->courses->count() > 0): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subject->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-mono font-bold text-teal-600 uppercase bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                                            <?php echo e($course->gradeLevel?->name ?: __('General')); ?>

                                        </span>
                                        <?php
                                            $courseSessionsNum = $course->sessions ? $course->sessions->count() : ($course->sessions_count ?: 0);
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courseSessionsNum > 0): ?>
                                            <span class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                                                <?php echo e($courseSessionsNum); ?> <?php echo e(__('Lessons')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <h3 class="font-heading font-extrabold text-xl text-slate-900 leading-snug"><?php echo e(__($course->title)); ?></h3>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3"><?php echo e(__($course->description ?: 'Interactive curriculum with hands-on labs.')); ?></p>
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->teacher?->photo): ?>
                                            <img src="<?php echo e(media_url($course->teacher->photo)); ?>" class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200" alt="<?php echo e($course->teacher->user?->name); ?>">
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                                                <?php echo e(substr($course->teacher?->user?->name ?: 'F', 0, 1)); ?>

                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800"><?php echo e($course->teacher?->user?->name ?: __('Faculty Advisor')); ?></p>
                                            <p class="text-[10px] text-slate-500 font-mono"><?php echo e($course->teacher?->title ?: __('Senior Instructor')); ?></p>
                                        </div>
                                    </div>
                                    <a href="<?php echo e(route('course-details', ['slug' => $course->slug])); ?>" class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-teal-600/20">
                                        <?php echo e(__('View Details')); ?> &rarr;
                                    </a>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <div class="col-span-2 bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-4">
                            <p class="text-base font-semibold text-slate-700"><?php echo e(__('No individual courses listed yet for this subject.')); ?></p>
                            <a href="<?php echo e(route('courses')); ?>" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md">
                                <?php echo e(__('Browse Courses')); ?> &rarr;
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900"><?php echo e(__('Enroll in')); ?> <?php echo e($name); ?></h3>
                <p class="text-xs font-mono text-slate-500"><?php echo e(__('Access all video lectures, PDF revision books, and live mentor Q&A cohorts.')); ?></p>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> <?php echo e(__('Full Term 1 & 2 Access')); ?>

                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> <?php echo e(__('Direct Mentor Q&A Sessions')); ?>

                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> <?php echo e(__('Ministry Exam Revision Sheets')); ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coursesCount > 0): ?>
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> <?php echo e($coursesCount); ?> <?php echo e(__('Active Accredited Courses')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lessonsCount > 0): ?>
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> <?php echo e($lessonsCount); ?> <?php echo e(__('Video Lessons & Labs')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <a href="<?php echo e(route('courses')); ?>" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    <?php echo e(__('Explore Courses')); ?> &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/subject-details.blade.php ENDPATH**/ ?>