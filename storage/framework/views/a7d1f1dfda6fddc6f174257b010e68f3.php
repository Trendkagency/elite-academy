<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'count' => '100',
    'prefix' => '',
    'suffix' => '',
    'label' => '',
    'description' => '',
    'color' => 'teal',
    'duration' => 2000,
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
    'count' => '100',
    'prefix' => '',
    'suffix' => '',
    'label' => '',
    'description' => '',
    'color' => 'teal',
    'duration' => 2000,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $colorClass = match($color) {
        'orange' => 'text-orange-500',
        'emerald' => 'text-emerald-500',
        'indigo' => 'text-indigo-500',
        default => 'text-teal-600',
    };

    // Extract numeric portion for counting animation
    preg_match('/[\d\.\,]+/', $count, $matches);
    $numericStr = $matches[0] ?? '0';
    $targetNum = (float) str_replace(',', '', $numericStr);
    $isDecimal = str_contains($numericStr, '.');
?>

<div x-data="{
        current: 0,
        target: <?php echo e($targetNum); ?>,
        isAnimated: false,
        initObserver() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.isAnimated) {
                        this.startCount();
                        this.isAnimated = true;
                    }
                });
            }, { threshold: 0.2 });
            observer.observe(this.$el);
        },
        startCount() {
            let startTime = null;
            const duration = <?php echo e($duration); ?>;
            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3); // Ease-Out Cubic
                this.current = this.target * easeProgress;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    this.current = this.target;
                }
            };
            window.requestAnimationFrame(step);
        }
    }"
    x-init="initObserver()"
    class="anim-hero space-y-1 p-3 text-center group rounded-2xl hover:bg-white/40 transition-all duration-300">

    <p class="font-mono font-extrabold text-3xl sm:text-4xl <?php echo e($colorClass); ?> group-hover:scale-105 transition-transform duration-300">
        <span><?php echo e($prefix); ?></span><span x-text="<?php echo e($isDecimal ? 'current.toFixed(1)' : 'Math.floor(current).toLocaleString()'); ?>"><?php echo e($numericStr); ?></span><span><?php echo e($suffix); ?></span>
    </p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label): ?>
        <p class="text-xs sm:text-sm font-bold text-slate-700"><?php echo e($label); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
        <p class="text-[11px] text-slate-500 leading-tight max-w-[180px] mx-auto"><?php echo e($description); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\components\dynamic-counter.blade.php ENDPATH**/ ?>