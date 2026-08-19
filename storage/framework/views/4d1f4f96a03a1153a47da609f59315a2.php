<?php $__env->startSection('content'); ?>
<?php
    $name = $subject ? $subject->name : 'Secondary Mathematics';
    $description = $subject ? ($subject->description ?: 'Comprehensive curriculum covering core topics prepared for national curriculum standards.') : 'Comprehensive curriculum covering core topics prepared for national curriculum standards.';
    $categoryName = $subject?->category?->name ?: 'General Curriculum';
    $coursesCount = $subject?->courses ? $subject->courses->count() : 12;
    $image = $subject ? ($subject->image ?: 'images/course_ai.png') : 'images/course_ai.png';
?>


<section class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden">
    <img src="<?php echo e(media_url($image, 'images/course_ai.png')); ?>" alt="<?php echo e($name); ?> Cover" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/80 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => $name],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono font-extrabold text-white bg-teal-600 px-3.5 py-1.5 rounded-full shadow-md">
                    <?php echo e($categoryName); ?>

                </span>
                <span class="text-xs font-mono font-bold text-teal-300 bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                    Term 1 & Term 2
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
                <p class="font-heading font-black text-3xl text-teal-400">48</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Video Lessons</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-orange-400"><?php echo e($coursesCount); ?></p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Active Courses</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">3,400+</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Enrolled Students</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-amber-400">4.9 ★</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">Student Rating</p>
            </div>
        </div>
    </div>
</section>


<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">About the Curriculum</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <?php echo e($description); ?>

                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Courses in <?php echo e($name); ?></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subject && $subject->courses && $subject->courses->count() > 0): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subject->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-3">
                                <span class="text-xs font-mono font-bold text-teal-600 uppercase"><?php echo e($course->gradeLevel?->name ?: 'General'); ?></span>
                                <h3 class="font-heading font-extrabold text-lg text-slate-900"><?php echo e($course->title); ?></h3>
                                <p class="text-xs text-slate-600"><?php echo e($course->description ?: 'Interactive curriculum with hands-on labs.'); ?></p>
                                <div class="pt-2 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700">Teacher: <?php echo e($course->teacher?->user?->name ?: 'Faculty Advisor'); ?></span>
                                    <a href="<?php echo e(route('course-details', ['slug' => $course->slug])); ?>" class="btn-lift px-3.5 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-xl">View Course &rarr;</a>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <div class="col-span-2 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm text-center">
                            <p class="text-sm font-semibold text-slate-700">No individual courses listed yet for this subject.</p>
                            <a href="<?php echo e(route('courses')); ?>" class="btn-lift inline-block mt-3 px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-bold">Browse All Catalog Courses</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">Enroll in <?php echo e($name); ?></h3>
                <p class="text-xs font-mono text-slate-500">Access all video lectures, PDF revision books, and live mentor Q&A cohorts.</p>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-2">✓ Full Term 1 & 2 Access</div>
                    <div class="flex items-center gap-2">✓ Direct Mentor Q&A Sessions</div>
                    <div class="flex items-center gap-2">✓ Ministry Exam Revision Sheets</div>
                </div>

                <a href="<?php echo e(route('courses')); ?>" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    Explore <?php echo e($name); ?> Courses &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/subject-details.blade.php ENDPATH**/ ?>