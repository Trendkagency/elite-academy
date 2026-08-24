<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('Go to Home');
    if ($user) {
        if ($user->isAdmin()) {
            $dashUrl = url('/admin');
            $dashLabel = __('Go to Admin Panel');
        } elseif ($user->isTeacher()) {
            $dashUrl = route('teacher-portal');
            $dashLabel = __('Go to Teacher Portal');
        } elseif ($user->isParent()) {
            $dashUrl = route('parent-portal');
            $dashLabel = __('Go to Parent Portal');
        } elseif ($user->isStudent()) {
            $dashUrl = route('student-portal');
            $dashLabel = __('Go to Student Portal');
        }
    }
?>

<section class="min-h-[80vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="w-24 h-24 mx-auto bg-rose-500/10 text-rose-600 rounded-3xl flex items-center justify-center text-4xl border border-rose-500/20 shadow-inner">
                🔒
            </div>

            <div class="space-y-3">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-rose-100 text-rose-700 border border-rose-200 tracking-wider inline-block">
                    HTTP 403 — <?php echo e(__('ACCESS FORBIDDEN')); ?>

                </span>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    <?php echo e(__('Access Forbidden')); ?>

                </h1>
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-md mx-auto">
                    <?php echo e($exception->getMessage() ?: __('You do not have permission to access this page or resource.')); ?>

                </p>
            </div>

            
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="window.history.back()" class="btn-lift w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-2xl border border-slate-300 transition-all flex items-center justify-center gap-2">
                    <span>←</span> <?php echo e(__('Go Back')); ?>

                </button>
                <a href="<?php echo e($dashUrl); ?>" class="btn-lift w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2">
                    <span>📊</span> <?php echo e($dashLabel); ?>

                </a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            <?php echo e(__('Elite Academy Security System — Strict Role-Based Authorization Enforced')); ?>

        </p>

    </div>

    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-rose-500/5 rounded-full blur-3xl pointer-events-none"></div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\errors\403.blade.php ENDPATH**/ ?>