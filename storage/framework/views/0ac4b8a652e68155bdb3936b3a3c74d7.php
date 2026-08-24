
<?php $active = $active ?? false; ?>

<button class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'px-4 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer',
    'bg-teal-600 text-white shadow-xs' => $active,
    'bg-white text-slate-700 border border-slate-200 hover:border-teal-500' => ! $active,
]); ?>"><?php echo e($label); ?></button>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\components\filter-chip.blade.php ENDPATH**/ ?>