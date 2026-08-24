<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'card1Number' => '120',
    'card1Plus' => '+',
    'card1Category' => null,
    'card1Title' => null,
    'card2Number' => '25K',
    'card2Plus' => '+',
    'card2Category' => null,
    'card2Title' => null,
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
    'card1Number' => '120',
    'card1Plus' => '+',
    'card1Category' => null,
    'card1Title' => null,
    'card2Number' => '25K',
    'card2Plus' => '+',
    'card2Category' => null,
    'card2Title' => null,
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
    $isRtl = $locale === 'ar';

    $c1Category = $card1Category ?? ($isRtl ? 'معتمد' : 'ACCREDITED');
    $c1Title = $card1Title ?? ($isRtl ? 'كورسات تخصصية' : 'Expert Courses');
    
    $c2Category = $card2Category ?? ($isRtl ? 'عالمي' : 'GLOBAL');
    $c2Title = $card2Title ?? ($isRtl ? 'طلاب نشطين' : 'Active Students');
?>

<div class="lg:col-span-4 w-full max-w-sm mx-auto flex flex-col gap-3.5 sm:gap-4 pt-4 lg:pt-0 select-none">
    
    <div class="group bg-white/95 backdrop-blur-xl border border-white/80 rounded-[2.2rem] shadow-[0_20px_50px_-15px_rgba(15,23,42,0.18)] p-3.5 sm:p-4 px-5 sm:px-6 flex items-center gap-4.5 hover:-translate-y-1 transition-all duration-300">
        
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-[#E0F7F1] via-[#D1F3EB] to-[#B6EFE2] border border-teal-300/70 shadow-[inset_0_2px_4px_rgba(255,255,255,0.9),0_6px_16px_-4px_rgba(0,106,96,0.12)] flex items-center justify-center p-2 shrink-0 group-hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-center font-heading font-black text-xl sm:text-2xl lg:text-3xl text-[#005C53] tracking-tight leading-none text-center">
                <span><?php echo e($card1Number); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($card1Plus): ?>
                    <span class="text-base sm:text-xl font-black text-[#005C53] ml-0.5 leading-none -mt-1">+</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        
        
        <div class="<?php echo e($isRtl ? 'text-right' : 'text-left'); ?> flex-1 min-w-0">
            <p class="text-[10px] sm:text-[11px] font-mono uppercase tracking-[0.2em] font-extrabold text-slate-400 mb-0.5">
                <?php echo e($c1Category); ?>

            </p>
            <h4 class="text-base sm:text-lg lg:text-xl font-black text-slate-900 tracking-tight leading-tight">
                <?php echo e($c1Title); ?>

            </h4>
        </div>
    </div>

    
    <div class="group bg-white/95 backdrop-blur-xl border border-white/80 rounded-[2.2rem] shadow-[0_20px_50px_-15px_rgba(15,23,42,0.18)] p-3.5 sm:p-4 px-5 sm:px-6 flex items-center gap-4.5 hover:-translate-y-1 transition-all duration-300">
        
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-[#FFF2E2] via-[#FEE8D2] to-[#FEDBBE] border border-orange-300/70 shadow-[inset_0_2px_4px_rgba(255,255,255,0.9),0_6px_16px_-4px_rgba(230,81,0,0.12)] flex items-center justify-center p-2 shrink-0 group-hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-center font-heading font-black text-xl sm:text-2xl lg:text-3xl text-[#E65100] tracking-tight leading-none text-center">
                <span><?php echo e($card2Number); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($card2Plus): ?>
                    <span class="text-base sm:text-xl font-black text-[#E65100] ml-0.5 leading-none -mt-1">+</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        
        
        <div class="<?php echo e($isRtl ? 'text-right' : 'text-left'); ?> flex-1 min-w-0">
            <p class="text-[10px] sm:text-[11px] font-mono uppercase tracking-[0.2em] font-extrabold text-slate-400 mb-0.5">
                <?php echo e($c2Category); ?>

            </p>
            <h4 class="text-base sm:text-lg lg:text-xl font-black text-slate-900 tracking-tight leading-tight">
                <?php echo e($c2Title); ?>

            </h4>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/hero-stat-cards.blade.php ENDPATH**/ ?>