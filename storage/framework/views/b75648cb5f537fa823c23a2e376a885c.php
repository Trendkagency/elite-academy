<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $photo = $t->photo_url;
        $name = $t->user->name ?? 'Dr. Instructor';
        $title = $t->title ?? __('Senior Professor');
        $specialization = $t->specialization ?? __('Secondary Education');
        $rating = number_format($t->rating_avg ?: 4.9, 1) . ' ★';
        $studentsCount = number_format($t->students_count ?: 100) . ' ' . __('Students');
        $slug = $t->slug ?: $t->id;
    ?>
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden flex flex-col justify-between hover:shadow-2xl transition-all duration-300 group card-lift">
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border-2 border-teal-500/20 group-hover:border-teal-500 transition-colors flex-shrink-0">
                    <img src="<?php echo e(media_url($photo, 'images/instructor_portrait.png')); ?>" alt="<?php echo e($name); ?>" class="w-full h-full object-cover">
                </div>
                <div class="space-y-1">
                    <span class="inline-block text-[10px] font-mono font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-200">
                        <?php echo e($specialization); ?>

                    </span>
                    <h3 class="font-heading font-black text-lg text-slate-900 group-hover:text-teal-600 transition-colors">
                        <?php echo e($name); ?>

                    </h3>
                    <p class="text-xs text-slate-500 font-medium"><?php echo e($title); ?></p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                <?php echo e($t->bio ?: __('Expert instructor with extensive experience preparing secondary students for top academic achievements.')); ?>

            </p>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs font-mono font-bold">
                <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                    <span class="text-slate-400 block text-[10px]"><?php echo e(__('Rating')); ?></span>
                    <span class="text-amber-500 font-extrabold"><?php echo e($rating); ?></span>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                    <span class="text-slate-400 block text-[10px]"><?php echo e(__('Students')); ?></span>
                    <span class="text-teal-600 font-extrabold"><?php echo e($studentsCount); ?></span>
                </div>
            </div>
        </div>

        <div class="p-6 pt-0">
            <a href="<?php echo e(route('teacher-profile', ['slug' => $slug])); ?>" class="btn-lift w-full block text-center py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-teal-600/20 transition-all">
                <?php echo e(__('View Teacher Profile')); ?> &rarr;
            </a>
        </div>
    </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="text-4xl mb-3">👨‍🏫</div>
        <h3 class="font-bold text-lg text-slate-800"><?php echo e(__('No Teachers Found')); ?></h3>
        <p class="text-xs text-slate-500 mt-1 mb-4"><?php echo e(__('Try clearing filters or search term to see all faculty members.')); ?></p>
        <a href="<?php echo e(route('teachers')); ?>" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
            <?php echo e(__('View All Teachers')); ?>

        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\partials\teachers-grid-items.blade.php ENDPATH**/ ?>