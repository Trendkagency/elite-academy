
<?php
    $badgeColor = $badgeColor ?? 'teal';
    $centered = $centered ?? true;
    $badgeClasses = match($badgeColor) {
        'orange' => 'text-orange-600 bg-orange-50 border-orange-100',
        default  => 'text-teal-600 bg-teal-50 border-teal-100',
    };
?>

<div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['space-y-4', 'text-center' => $centered]); ?>">
    <span class="text-xs font-mono uppercase tracking-widest font-bold px-3.5 py-1 rounded-full border <?php echo e($badgeClasses); ?>">
        <?php echo e($badge); ?>

    </span>
    <h1 class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
        <?php echo $title; ?>

    </h1>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($subtitle)): ?>
        <p class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-slate-600 text-base sm:text-lg', 'max-w-2xl mx-auto' => $centered]); ?>">
            <?php echo e($subtitle); ?>

        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/section-header.blade.php ENDPATH**/ ?>