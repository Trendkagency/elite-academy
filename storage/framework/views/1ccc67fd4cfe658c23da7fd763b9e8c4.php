<?php $__env->startSection('content'); ?>
<?php
    $heroBadge = $aboutSettings['hero_badge'] ?? 'ACCREDITED EXCELLENCE • EST. 2020';
    $heroTitle = $aboutSettings['hero_title'] ?? 'Transforming Academic Education For Future Leaders';
    $heroSubtitle = $aboutSettings['hero_subtitle'] ?? 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.';
    $missionTitle = $aboutSettings['mission_title'] ?? 'Our Core Educational Mission';
    $missionText = $aboutSettings['mission_text'] ?? 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.';
    $visionTitle = $aboutSettings['vision_title'] ?? 'Our Vision For Tomorrow';
    $visionText = $aboutSettings['vision_text'] ?? 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.';
    $statStudents = $aboutSettings['stat_students'] ?? '25,000+';
    $statCourses = $aboutSettings['stat_courses'] ?? '120+';
    $statTeachers = $aboutSettings['stat_teachers'] ?? '45+';
    $statPassRate = $aboutSettings['stat_pass_rate'] ?? '98.5%';
?>

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => 'About Elite Academy'],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    <?php echo e($heroBadge); ?>

                </span>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                    <?php echo $heroTitle; ?>

                </h1>

                <p class="text-slate-600 text-base sm:text-lg font-medium leading-relaxed">
                    <?php echo e($heroSubtitle); ?>

                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-center">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block"><?php echo e($statStudents); ?></span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Active Students</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-orange-600 block"><?php echo e($statCourses); ?></span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Accredited Courses</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block"><?php echo e($statTeachers); ?></span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Expert Faculty</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-emerald-600 block"><?php echo e($statPassRate); ?></span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Exam Pass Rate</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="<?php echo e(asset('images/academy_campus.png')); ?>" alt="Elite Academy Campus" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-2xl">
                    <p class="font-heading font-black text-xl text-teal-400">ACCREDITED ACADEMY</p>
                    <p class="text-xs font-mono text-slate-300">Secondary Education Excellence</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    OUR MISSION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900"><?php echo e($missionTitle); ?></h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <?php echo e($missionText); ?>

                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-orange-600 bg-orange-50 px-3.5 py-1.5 rounded-full border border-orange-200/80">
                    OUR VISION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900"><?php echo e($visionTitle); ?></h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    <?php echo e($visionText); ?>

                </p>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\about.blade.php ENDPATH**/ ?>