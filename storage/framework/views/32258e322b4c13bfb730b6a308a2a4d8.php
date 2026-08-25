<?php
    $locale = app()->getLocale();
    $rawStats = \App\Models\SiteSetting::get('landing_stats_counters');
    $stats = $rawStats ? json_decode($rawStats, true) : [
        ['count' => '25,000+', 'label_ar' => 'الطلاب النشطين', 'label_en' => 'Active Students', 'color' => 'teal'],
        ['count' => '120+', 'label_ar' => 'الكورسات والمقررات المعتمدة', 'label_en' => 'Expert Courses', 'color' => 'teal'],
        ['count' => '45+', 'label_ar' => 'المعلمين والمحاضرين', 'label_en' => 'Instructors & Mentors', 'color' => 'teal'],
        ['count' => '98.5%', 'label_ar' => 'رضا أولياء الأمور', 'label_en' => 'Parent Satisfaction', 'color' => 'orange'],
        ['count' => '100%', 'label_ar' => 'شهادات دولية معتمدة', 'label_en' => 'Global Certifications', 'color' => 'teal'],
    ];
?>


<section class="relative z-30 -mt-10 md:-mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white/92 backdrop-blur-md border border-white/80 rounded-3xl shadow-2xl shadow-slate-900/10 p-6 md:p-8">
        <div class="grid grid-cols-2 md:grid-cols-<?php echo e(min(count($stats), 5)); ?> gap-6 text-center divide-x-0 md:divide-x divide-slate-200/60">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $label = ($locale === 'ar' ? ($stat['label_ar'] ?? null) : null) ?: ($stat['label_en'] ?? '');
                    $colorClass = ($stat['color'] ?? 'teal') === 'orange' ? 'text-orange-500' : (($stat['color'] ?? 'teal') === 'emerald' ? 'text-emerald-600' : 'text-teal-600');
                    $delay = ($index % 5) + 1;
                ?>
                <div class="anim-hero delay-<?php echo e($delay); ?> space-y-1 p-2 group sr-stat <?php echo e($loop->last && count($stats) % 2 !== 0 ? 'col-span-2 md:col-span-1' : ''); ?>">
                    <p class="font-mono font-extrabold text-3xl sm:text-4xl <?php echo e($colorClass); ?> group-hover:scale-105 transition-transform duration-300">
                        <span data-count="<?php echo e($stat['count']); ?>"><?php echo e($stat['count']); ?></span>
                    </p>
                    <p class="text-xs font-semibold text-slate-600"><?php echo e($label); ?></p>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home/stats-overlay.blade.php ENDPATH**/ ?>