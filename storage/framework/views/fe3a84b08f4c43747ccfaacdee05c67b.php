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
    $retrySeconds = isset($retryAfter) ? (int)$retryAfter : 60;
?>

<section class="min-h-[80vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-12 border border-amber-200/90 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="w-24 h-24 mx-auto bg-amber-500/10 text-amber-600 rounded-3xl flex items-center justify-center text-4xl border border-amber-500/20 shadow-inner animate-pulse">
                🛡️
            </div>

            <div class="space-y-3">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-amber-100 text-amber-800 border border-amber-200 tracking-wider inline-block">
                    HTTP 429 — <?php echo e(__('RATE LIMIT EXCEEDED')); ?>

                </span>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    <?php echo e(__('Too Many Requests')); ?>

                </h1>
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-md mx-auto">
                    <?php echo e((isset($exception) && $exception->getMessage()) ? $exception->getMessage() : __('You have made too many requests in a short period. System rate limiting is active to protect server performance and mitigate DDoS attacks.')); ?>

                </p>
            </div>

            
            <div class="bg-amber-50/80 rounded-2xl p-4 border border-amber-200/70 max-w-xs mx-auto space-y-1">
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider"><?php echo e(__('Retry Available In')); ?></p>
                <div class="text-2xl font-mono font-black text-amber-600 flex items-center justify-center gap-1">
                    <span id="rate-limit-timer"><?php echo e($retrySeconds); ?></span>
                    <span class="text-xs font-sans font-bold text-amber-800"><?php echo e(__('seconds')); ?></span>
                </div>
            </div>

            
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="window.location.reload()" class="btn-lift w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-2xl border border-slate-300 transition-all flex items-center justify-center gap-2">
                    <span>🔄</span> <?php echo e(__('Refresh Page')); ?>

                </button>
                <a href="<?php echo e($dashUrl); ?>" class="btn-lift w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2">
                    <span>📊</span> <?php echo e($dashLabel); ?>

                </a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            <?php echo e(__('Elite Academy DDoS Protection — RateLimiter Infrastructure Active')); ?>

        </p>

    </div>

    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let seconds = <?php echo e($retrySeconds); ?>;
        const timerEl = document.getElementById('rate-limit-timer');
        if (!timerEl || seconds <= 0) return;

        const interval = setInterval(function() {
            seconds--;
            if (seconds <= 0) {
                clearInterval(interval);
                timerEl.textContent = '0';
                window.location.reload();
            } else {
                timerEl.textContent = seconds;
            }
        }, 1000);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\errors\429.blade.php ENDPATH**/ ?>