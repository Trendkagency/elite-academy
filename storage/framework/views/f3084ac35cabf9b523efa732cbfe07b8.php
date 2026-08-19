<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'sessions' => [],
    'hasFreeDemo' => true,
    'title' => 'Curriculum Lifetime Progression Line',
    'subtitle' => 'Step-by-step roadmap of live sessions, coding labs, and homework assignments',
]));

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

foreach (array_filter(([
    'sessions' => [],
    'hasFreeDemo' => true,
    'title' => 'Curriculum Lifetime Progression Line',
    'subtitle' => 'Step-by-step roadmap of live sessions, coding labs, and homework assignments',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $totalSessionsCount = is_countable($sessions) ? count($sessions) : 0;
    $perPage = 4;
?>

<div x-data="{ timelinePage: 1, perPage: <?php echo e($perPage); ?>, total: <?php echo e($totalSessionsCount); ?> }" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-700 font-extrabold bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                MODULE TIMELINE ROADMAP
            </span>
            <h3 class="font-heading font-black text-2xl text-slate-900 mt-1"><?php echo e($title); ?></h3>
            <p class="text-xs font-mono text-slate-500 mt-0.5"><?php echo e($subtitle); ?></p>
        </div>
        <span class="hidden sm:inline-block text-xs font-mono font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
            <?php echo e($totalSessionsCount); ?> Milestones
        </span>
    </div>

    
    <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-gradient-to-b before:from-teal-500 before:via-orange-400 before:to-slate-300">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sessions) && count($sessions) > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $isFree = (bool) $hasFreeDemo && ($s->is_free_demo ?? ($idx === 0));
                    $hasAssignment = $s->assignments && $s->assignments->count() > 0;
                    $statusColor = $isFree ? 'bg-teal-500 text-white ring-4 ring-teal-100' : ($idx === 1 ? 'bg-orange-500 text-white ring-4 ring-orange-100' : 'bg-slate-300 text-slate-700');
                    
                    if ($isFree) {
                        $badgeText = app()->getLocale() === 'ar' ? 'مفتوح / حصة مجانية ✓' : 'Unlocked / Free Demo ✓';
                        $badgeBg = 'bg-teal-100 text-teal-800 border-teal-200';
                    } elseif ($idx === 1) {
                        $badgeText = app()->getLocale() === 'ar' ? 'الحصة الحالية ⏳' : 'Current Session ⏳';
                        $badgeBg = 'bg-orange-100 text-orange-800 border-orange-200';
                    } else {
                        $badgeText = app()->getLocale() === 'ar' ? 'مغلق / يلزم الاشتراك 🔒' : 'Locked / Package Required 🔒';
                        $badgeBg = 'bg-slate-100 text-slate-600 border-slate-200';
                    }
                ?>
                <div x-show="timelinePage === Math.ceil((<?php echo e($idx); ?> + 1) / perPage)" class="relative group space-y-2 transition-all duration-300">
                    
                    <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full <?php echo e($statusColor); ?> font-mono font-extrabold text-xs flex items-center justify-center shadow-md">
                        <?php echo e($idx + 1); ?>

                    </div>

                    <div class="bg-slate-50 hover:bg-teal-50/40 transition-colors p-5 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h4 class="font-bold text-base text-slate-900 group-hover:text-teal-700 transition-colors">
                                Session <?php echo e($s->sort_order ?: ($idx + 1)); ?>: <?php echo e($s->title); ?>

                            </h4>
                            <span class="text-[11px] font-mono font-extrabold px-3 py-0.5 rounded-full border <?php echo e($badgeBg); ?>">
                                <?php echo e($badgeText); ?>

                            </span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-sans">
                            <?php echo e($s->description ?: 'Interactive lecture, hands-on coding exercises, and graded homework.'); ?>

                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-slate-200/60 text-[11px] font-mono text-slate-500">
                            <span>⏱️ <?php echo e($s->duration_minutes ?: 60); ?> Mins Duration</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFree): ?>
                                <span class="text-emerald-700 font-bold">▶ <?php echo e(app()->getLocale() === 'ar' ? 'حصة تجريبية متوفرة' : 'Free Sample Included'); ?></span>
                            <?php else: ?>
                                <span class="text-rose-700 font-bold">🔒 <?php echo e(app()->getLocale() === 'ar' ? 'باستخدام باقة الاشتراك' : 'Subscription Required'); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAssignment): ?>
                                <span class="text-rose-700 font-bold">📝 <?php echo e(app()->getLocale() === 'ar' ? 'الواجب إجباري' : 'Homework Mandatory'); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php else: ?>
            
            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full <?php echo e($hasFreeDemo ? 'bg-teal-500 text-white ring-4 ring-teal-100' : 'bg-slate-400 text-white ring-4 ring-slate-200'); ?> font-mono font-bold text-xs flex items-center justify-center shadow-md">
                    1
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 1: Orientation & Foundations</h4>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFreeDemo): ?>
                            <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-teal-100 text-teal-800 border border-teal-200">
                                <?php echo e(app()->getLocale() === 'ar' ? 'مفتوح / حصة مجانية ✓' : 'Unlocked / Free Demo ✓'); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                <?php echo e(app()->getLocale() === 'ar' ? 'مغلق / يلزم الاشتراك 🔒' : 'Locked / Package Required 🔒'); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Introduction to core principles, environment setup, and baseline diagnostic quiz.
                    </p>
                </div>
            </div>

            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full bg-orange-500 text-white font-mono font-bold text-xs flex items-center justify-center ring-4 ring-orange-100 shadow-md">
                    2
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 2: Advanced Core Architecture</h4>
                        <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-200">
                            <?php echo e(app()->getLocale() === 'ar' ? 'الحصة الحالية ⏳' : 'Current In Progress ⏳'); ?>

                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        In-depth lectures, live problem solving, and mandatory homework assignments.
                    </p>
                </div>
            </div>

            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full bg-slate-300 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shadow-md">
                    3
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 3: Final Certification & Capstone</h4>
                        <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                            <?php echo e(app()->getLocale() === 'ar' ? 'قريباً 🔒' : 'Upcoming 🔒'); ?>

                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Comprehensive exam review, final project defense, and accredited certification.
                    </p>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sessions) && count($sessions) > 4): ?>
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs font-mono">
            <button @click="if (timelinePage > 1) timelinePage--" :disabled="timelinePage <= 1" class="btn-lift px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                <span>&larr;</span> <span><?php echo e(app()->getLocale() === 'ar' ? 'السابق' : 'Prev'); ?></span>
            </button>
            <span class="text-slate-600 font-bold bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-200">
                <?php echo e(app()->getLocale() === 'ar' ? 'صفحة' : 'Page'); ?> <span class="text-teal-700 font-black" x-text="timelinePage">1</span> <?php echo e(app()->getLocale() === 'ar' ? 'من' : 'of'); ?> <span x-text="Math.ceil(total / perPage)">1</span>
            </span>
            <button @click="if (timelinePage < Math.ceil(total / perPage)) timelinePage++" :disabled="timelinePage >= Math.ceil(total / perPage)" class="btn-lift px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                <span><?php echo e(app()->getLocale() === 'ar' ? 'التالي' : 'Next'); ?></span> <span>&rarr;</span>
            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/curriculum-timeline.blade.php ENDPATH**/ ?>