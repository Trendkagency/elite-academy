<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'theme' => 'auto',
    'size' => 'md',
    'showPill' => true,
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
    'theme' => 'auto',
    'size' => 'md',
    'showPill' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $appName = config('demo.branding.app_name') ?: env('DEMO_APP_NAME', 'EDU');
    $sizeClasses = match($size) {
        'sm' => [
            'icon' => 'w-8 h-8 rounded-lg',
            'svg' => 'w-4 h-4',
            'text' => 'text-lg',
            'pill' => 'text-[8px] px-1 py-0.5',
            'sub' => 'hidden',
        ],
        'lg' => [
            'icon' => 'w-12 h-12 rounded-2xl',
            'svg' => 'w-7 h-7',
            'text' => 'text-2xl sm:text-3xl',
            'pill' => 'text-[10px] px-2 py-0.5',
            'sub' => 'text-xs',
        ],
        default => [
            'icon' => 'w-10 h-10 rounded-xl',
            'svg' => 'w-5 h-5',
            'text' => 'text-xl sm:text-2xl',
            'pill' => 'text-[9px] px-1.5 py-0.5',
            'sub' => 'text-[10px]',
        ],
    };

    $textColor = match($theme) {
        'dark' => 'text-white',
        'light' => 'text-slate-900',
        default => 'text-slate-900 dark:text-white',
    };
?>

<div class="demo-brand-logo inline-flex items-center gap-2.5 select-none transition-transform duration-300 group-hover:scale-105">
    
    <div class="<?php echo e($sizeClasses['icon']); ?> bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-700 flex items-center justify-center text-white shadow-md shadow-blue-500/20 shrink-0">
        <svg class="<?php echo e($sizeClasses['svg']); ?> text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
        </svg>
    </div>

    
    <div class="flex flex-col text-start">
        <div class="flex items-center gap-2">
            <span class="font-heading font-black <?php echo e($sizeClasses['text']); ?> <?php echo e($textColor); ?> tracking-tight leading-none">
                <?php echo e($appName); ?>

            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPill): ?>
                <span class="<?php echo e($sizeClasses['pill']); ?> font-black uppercase tracking-wider rounded bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300/70 dark:border-amber-700/60 leading-none">
                    DEMO
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <span class="<?php echo e($sizeClasses['sub']); ?> font-semibold text-slate-400 dark:text-slate-500 leading-none mt-1">
            LMS Platform
        </span>
    </div>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/demo/brand-logo.blade.php ENDPATH**/ ?>