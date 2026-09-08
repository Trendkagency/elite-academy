<?php $__env->startSection('content'); ?>
<section class="min-h-[85vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-14 border border-slate-200/90 shadow-2xl space-y-8 relative overflow-hidden">

            
            <div class="w-24 h-24 mx-auto bg-teal-500/10 text-teal-600 rounded-3xl flex items-center justify-center text-4xl border border-teal-500/20 shadow-inner animate-pulse">
                <i class="fa-solid fa-gear"></i>
            </div>

            
            <div class="space-y-4">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-teal-50 text-teal-700 border border-teal-200/80 tracking-widest inline-block uppercase">
                    HTTP 503 — <?php echo e(__('MAINTENANCE MODE')); ?>

                </span>

                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    <?php echo e(__('app.errors.503_title')); ?>

                </h1>

                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-lg mx-auto">
                    <?php echo e(__('app.errors.503_desc')); ?>

                </p>
            </div>

            
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="window.location.reload()" class="btn-lift w-full sm:w-auto px-7 py-3.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span><i class="fa-solid fa-arrows-rotate"></i></span> <?php echo e(__('Check Again')); ?>

                </button>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            <?php echo e(__('Elite Academy Platform — Scheduled System Maintenance')); ?>

        </p>
    </div>

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/errors/503.blade.php ENDPATH**/ ?>