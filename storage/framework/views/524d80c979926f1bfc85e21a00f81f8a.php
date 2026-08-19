
<section class="py-20 md:py-28 bg-white border-y border-slate-200/80 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-3 max-w-xl">
                <span class="anim-subject delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                    OUR SUBJECTS
                </span>
                <h2 class="anim-subject delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    Explore Our <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Learning Subjects.</span>
                </h2>
            </div>

            <div class="hidden md:block w-px h-16 bg-slate-200/80 mx-2 flex-shrink-0"></div>

            <div class="max-w-md my-auto">
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed">
                    Discover industry-focused subjects designed to prepare students for future careers.
                </p>
            </div>
        </div>

        
        <?php
            $homeSubjects = [
                ['name' => 'Programming', 'tag' => 'TECHNOLOGY', 'color' => 'slate', 'teachers' => 14, 'courses' => 24, 'img' => 'images/hero_student.png'],
                ['name' => 'Artificial Intelligence', 'tag' => 'ENGINEERING', 'color' => 'teal', 'teachers' => 10, 'courses' => 16, 'img' => 'images/course_ai.png'],
                ['name' => 'Science', 'tag' => 'SCIENCE', 'color' => 'blue', 'teachers' => 12, 'courses' => 18, 'img' => 'images/academy_campus.png'],
                ['name' => 'Business Administration', 'tag' => 'BUSINESS', 'color' => 'emerald', 'teachers' => 11, 'courses' => 14, 'img' => 'images/instructor_portrait.png'],
                ['name' => 'Creative Design', 'tag' => 'DESIGN', 'color' => 'amber', 'teachers' => 10, 'courses' => 15, 'img' => 'images/course_ai.png'],
                ['name' => 'Mathematics', 'tag' => 'SCIENCE', 'color' => 'purple', 'teachers' => 9, 'courses' => 15, 'img' => 'images/instructor_female.png'],
                ['name' => 'Global Languages', 'tag' => 'LANGUAGE', 'color' => 'cyan', 'teachers' => 8, 'courses' => 10, 'img' => 'images/hero_student.png'],
                ['name' => 'Robotics Engineering', 'tag' => 'ENGINEERING', 'color' => 'slate', 'teachers' => 8, 'courses' => 12, 'img' => 'images/instructor_male.png'],
            ];
        ?>

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $homeSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="anim-subject delay-3 sr-card aspect-[4/5] md:aspect-auto md:h-[520px] rounded-[24px] bg-<?php echo e($sub['color']); ?>-950 text-white shadow-lg shadow-<?php echo e($sub['color']); ?>-950/10 hover:shadow-2xl card-lift flex flex-col justify-between overflow-hidden group transition-all duration-300 relative active:scale-[0.98]">
                    <div class="absolute inset-0 md:relative md:h-[338px] overflow-hidden bg-slate-950">
                        <img src="<?php echo e(asset($sub['img'])); ?>" alt="<?php echo e($sub['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent md:from-<?php echo e($sub['color']); ?>-950 md:via-transparent pointer-events-none"></div>
                    </div>

                    <div class="absolute bottom-0 inset-x-0 p-3.5 sm:p-4 text-white z-10 flex flex-col justify-end space-y-1 md:relative md:p-6 md:flex-1 md:bg-<?php echo e($sub['color']); ?>-950 md:space-y-3">
                        <div class="space-y-1">
                            <span class="text-[9px] sm:text-[10px] font-mono font-extrabold uppercase tracking-widest text-<?php echo e($sub['color']); ?>-300 bg-slate-950/70 md:bg-transparent backdrop-blur-xs md:backdrop-blur-none px-2 py-0.5 md:p-0 rounded-full md:rounded-none border border-white/10 md:border-none inline-block w-max"><?php echo e($sub['tag']); ?></span>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base md:text-2xl text-white group-hover:text-<?php echo e($sub['color']); ?>-300 transition-colors line-clamp-2 leading-snug">
                                <a href="<?php echo e(route('subject-details')); ?>"><?php echo e($sub['name']); ?></a>
                            </h3>
                        </div>

                        <div class="hidden md:flex items-center justify-between pt-3 border-t border-<?php echo e($sub['color']); ?>-900 text-xs text-<?php echo e($sub['color']); ?>-100 font-medium">
                            <span>👨‍🏫 <?php echo e($sub['teachers']); ?> Teachers • 📚 <?php echo e($sub['courses']); ?> Courses</span>
                            <a href="<?php echo e(route('subject-details')); ?>" class="text-xs font-extrabold text-<?php echo e($sub['color']); ?>-300 group-hover:text-<?php echo e($sub['color']); ?>-200 flex items-center gap-1">
                                <span>Explore Subject</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/subjects-grid.blade.php ENDPATH**/ ?>