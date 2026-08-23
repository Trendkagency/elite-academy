<?php $__env->startSection('content'); ?>
<?php
    $name = $teacher->user->name ?? 'Dr. Ahmed Hassan';
    $title = $teacher->title ?? 'Senior Professor';
    $specialization = $teacher->specialization ?? 'Secondary Education';
    $bio = $teacher->bio ?: 'Expert instructor with extensive experience preparing secondary students for top academic achievements.';
    $rating = number_format($teacher->rating_avg ?: 4.9, 1) . ' ★';
    $studentsCount = number_format($teacher->students_count ?: 100) . '+';
    $yearsExp = ($teacher->years_experience ?: 5) . '+ Years';
    $photo = $teacher->photo_url;
    $courses = $teacher->courses ?: [];
?>

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.teachers'), 'route' => 'teachers'],
                ['label' => $name],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 rounded-3xl p-8 lg:p-12 text-white shadow-2xl relative overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5 relative">
                <div class="rounded-2xl overflow-hidden border-4 border-white/20 shadow-2xl h-[380px] bg-slate-950">
                    <img src="<?php echo e(media_url($photo, 'images/instructor_portrait.png')); ?>" alt="<?php echo e($name); ?>" class="w-full h-full object-cover">
                </div>
                <span class="absolute bottom-4 left-4 bg-teal-500 text-slate-950 font-mono font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow-lg">
                    ✔ Faculty Member
                </span>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                        <?php echo e($specialization); ?>

                    </span>
                    <h1 class="font-heading text-3xl sm:text-5xl font-black text-white tracking-tight">
                        <?php echo e($name); ?>

                    </h1>
                    <p class="text-slate-300 text-base font-mono">
                        <?php echo e($title); ?>

                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-mono font-semibold text-slate-200">
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-teal-400 font-bold block text-sm"><?php echo e($yearsExp); ?></span>
                        Teaching Experience
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-amber-400 font-bold block text-sm"><?php echo e($rating); ?></span>
                        Student Evaluation
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10 col-span-2 sm:col-span-1">
                        <span class="text-teal-400 font-bold block text-sm"><?php echo e($studentsCount); ?></span>
                        Active Students
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">Biography & Academic Background</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <?php echo e($bio); ?>

                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Courses Taught by <?php echo e($name); ?></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 card-lift">
                            <span class="text-xs font-mono font-extrabold text-teal-600 uppercase"><?php echo e($c->subject->name ?? 'ACADEMIC COURSE'); ?></span>
                            <h3 class="font-heading font-extrabold text-xl text-slate-900"><?php echo e($c->title); ?></h3>
                            <p class="text-xs font-mono text-slate-500">Accredited Course Curriculum</p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 col-span-2">
                            <h3 class="font-heading font-extrabold text-base text-slate-800">Secondary Education Courses</h3>
                            <p class="text-xs font-mono text-slate-500">Curriculum modules available in student portal.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">Join <?php echo e($name); ?>'s Cohort</h3>
                <p class="text-xs font-mono text-slate-500">Get direct access to weekly live Q&A webinars and revision worksheets.</p>

                <a href="<?php echo e(route('student-portal')); ?>" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    Enroll with Teacher &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/teacher-profile.blade.php ENDPATH**/ ?>