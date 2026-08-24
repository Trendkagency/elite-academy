<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'sections' => [],
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
    'sections' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $locale = app()->getLocale();
    $sectionsMap = [
        'hero-slider' => 'pages.home.hero-slider',
        'stats-overlay' => 'pages.home.stats-overlay',
        'why-choose' => 'pages.home.why-choose',
        'about-preview' => 'pages.home.about-preview',
        'subjects-grid' => 'pages.home.subjects-grid',
        'teachers-marquee' => 'pages.home.teachers-marquee',
        'testimonials' => 'pages.home.testimonials',
        'cta_section' => 'pages.home.cta-section',
    ];
?>

<div class="landing-dynamic-wrapper space-y-2">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            $isEnabled = $sec['is_enabled'] ?? true;
            if (!$isEnabled) continue;

            $secKey = $sec['section_key'] ?? $sec['key'] ?? '';
            $secType = $sec['type'] ?? '';
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sectionsMap[$secKey])): ?>
            <?php echo $__env->make($sectionsMap[$secKey], ['sectionData' => $sec], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php elseif($secType === 'counters' && !empty($sec['counters'])): ?>
            <section class="relative z-30 -mt-10 md:-mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/92 backdrop-blur-md border border-white/80 rounded-3xl shadow-2xl shadow-slate-900/10 p-6 md:p-8">
                    <div class="grid grid-cols-2 md:grid-cols-<?php echo e(min(count($sec['counters']), 5)); ?> gap-6 text-center divide-x-0 md:divide-x divide-slate-200/60">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sec['counters']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $counter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $counterModel = new \App\Models\LandingPageCounter($counter);
                                $val = $counterModel->getComputedValue();
                                $label = ($locale === 'ar' ? ($counter['label_ar'] ?? null) : null) ?: ($counter['label_en'] ?? '');
                                $desc = ($locale === 'ar' ? ($counter['description_ar'] ?? null) : null) ?: ($counter['description_en'] ?? '');
                            ?>
                            <?php if (isset($component)) { $__componentOriginala06ed004c748c5b32452a417e1d68c18 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala06ed004c748c5b32452a417e1d68c18 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dynamic-counter','data' => ['count' => $val,'label' => $label,'description' => $desc,'color' => $counter['color'] ?? 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-counter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($val),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($desc),'color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($counter['color'] ?? 'teal')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala06ed004c748c5b32452a417e1d68c18)): ?>
<?php $attributes = $__attributesOriginala06ed004c748c5b32452a417e1d68c18; ?>
<?php unset($__attributesOriginala06ed004c748c5b32452a417e1d68c18); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala06ed004c748c5b32452a417e1d68c18)): ?>
<?php $component = $__componentOriginala06ed004c748c5b32452a417e1d68c18; ?>
<?php unset($__componentOriginala06ed004c748c5b32452a417e1d68c18); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </section>
        <?php else: ?>
            
            <?php
                $title = ($locale === 'ar' ? ($sec['title_ar'] ?? null) : null) ?: ($sec['title_en'] ?? '');
                $subtitle = ($locale === 'ar' ? ($sec['subtitle_ar'] ?? null) : null) ?: ($sec['subtitle_en'] ?? '');
                $badge = ($locale === 'ar' ? ($sec['badge_ar'] ?? null) : null) ?: ($sec['badge_en'] ?? '');
                $img = $sec['image_url'] ?? null;
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title || $subtitle): ?>
                <section class="py-12 px-4 max-w-7xl mx-auto">
                    <div class="relative group p-8 sm:p-12 rounded-3xl bg-slate-900/90 text-white backdrop-blur-xl border border-white/20 shadow-2xl overflow-hidden transition-all duration-500 hover:scale-[1.01]">
                        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="space-y-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($badge): ?>
                                    <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30 uppercase tracking-widest">
                                        <?php echo e($badge); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                                    <?php echo e($title); ?>

                                </h2>
                                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                                    <?php echo e($subtitle); ?>

                                </p>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($img): ?>
                                <div class="flex justify-center">
                                    <img src="<?php echo e(asset($img)); ?>" alt="<?php echo e($title); ?>" class="max-h-64 object-contain rounded-2xl shadow-lg group-hover:scale-105 transition-transform duration-500">
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\components\landing-page-renderer.blade.php ENDPATH**/ ?>