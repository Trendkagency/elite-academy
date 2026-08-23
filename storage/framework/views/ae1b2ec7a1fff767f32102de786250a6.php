<?php $__env->startSection('content'); ?>
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        
        <div class="space-y-2 border-b border-slate-200/80 pb-6">
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                <?php echo e(__('Meet Our')); ?> <span class="text-teal-600 underline decoration-orange-500 underline-offset-8"><?php echo e(__('Expert Teachers')); ?></span>
            </h1>
            <p class="text-slate-600 text-base font-medium">
                <?php echo e(__('Browse experienced teachers by subject and grade level.')); ?>

            </p>
        </div>

        
        <form method="GET" action="<?php echo e(route('teachers')); ?>" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-lg space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider"><?php echo e(__('Search Teacher')); ?></label>
                    <input type="text" name="q" value="<?php echo e($searchQuery ?? ''); ?>" placeholder="<?php echo e(__('Search teacher by name or title...')); ?>" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 focus:outline-teal-600">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider"><?php echo e(__('Subject Filter')); ?></label>
                    <select name="subject" onchange="this.form.submit()" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-3.5 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value=""><?php echo e(__('All Subjects')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($s->slug); ?>" <?php if(($selectedSubject ?? '') === $s->slug): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div class="space-y-1 flex items-end">
                    <button type="submit" class="w-full h-11 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold font-mono transition-all">
                        <?php echo e(__('Filter Results')); ?>

                    </button>
                </div>
            </div>

            
            <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold pt-4 border-t border-slate-100">
                <span class="text-slate-400 mr-2 uppercase"><?php echo e(__('Subject Filters:')); ?></span>
                <a href="<?php echo e(route('teachers')); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white border-teal-600' => empty($selectedSubject),
                       'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! empty($selectedSubject),
                   ]); ?>">
                    <?php echo e(__('All')); ?>

                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $isActive = strtolower($selectedSubject ?? '') === strtolower($s->slug); ?>
                    <a href="<?php echo e(route('teachers', ['subject' => $s->slug])); ?>"
                       class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                           'px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border',
                           'bg-teal-600 text-white border-teal-600' => $isActive,
                           'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! $isActive,
                       ]); ?>">
                        <?php echo e($s->name); ?>

                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </form>

        
        <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-500 px-2 py-1">
            <span id="faculty-counter"><?php echo e(__('Showing')); ?> <?php echo e(count($teachers)); ?> <?php echo e(__('Teachers')); ?></span>
            <span><?php echo e(__('Faculty Members • Accredited Subjects')); ?></span>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $photo = $t->photo_url;
                    $name = $t->user->name ?? 'Dr. Ahmed Mahmoud';
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
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/teachers.blade.php ENDPATH**/ ?>