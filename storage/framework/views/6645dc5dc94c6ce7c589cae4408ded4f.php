<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('Back to Home');
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

    $rawMsg = $exception->getMessage();
    $cleanMsg = $rawMsg ? rtrim($rawMsg, '.') : __('Oops! The page or resource you are looking for does not exist, has been moved, or is temporarily unavailable');
?>

<style>
    @keyframes floatMascot {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(-3deg); }
    }
    @keyframes tearDrop {
        0%, 100% { opacity: 0.3; transform: translateY(0px) scale(0.9); }
        50% { opacity: 1; transform: translateY(6px) scale(1.1); }
    }
    @keyframes numberPulse {
        0%, 100% { opacity: 0.9; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.04); }
    }

    .anim-mascot {
        animation: floatMascot 4s ease-in-out infinite;
    }
    .anim-tear {
        animation: tearDrop 2.5s ease-in-out infinite;
    }
    .anim-num-pulse {
        animation: numberPulse 3s ease-in-out infinite;
    }
</style>

<section class="min-h-[85vh] flex items-center justify-center py-16 px-4 bg-slate-50 dark:bg-slate-950 transition-colors relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        
        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl p-8 sm:p-14 border border-slate-200/90 dark:border-slate-800 shadow-2xl space-y-8 relative overflow-hidden transition-colors">

            
            <div class="flex items-center justify-center gap-4 mx-auto py-2">
                
                <div class="relative w-24 h-24 bg-white dark:bg-slate-800 rounded-3xl p-3 shadow-xl border border-slate-200/90 dark:border-slate-700 flex items-center justify-center anim-mascot">
                    <img src="<?php echo e(asset('images/logo.webp')); ?>" alt="Elite Academy" class="w-full h-full object-contain">
                </div>

                
                <div class="relative w-20 h-20 bg-rose-50/90 dark:bg-rose-950/60 rounded-3xl border border-rose-200/90 dark:border-rose-800 shadow-lg flex items-center justify-center text-4xl anim-mascot" style="animation-delay: 0.6s;">
                    <i class="fa-solid fa-face-frown text-rose-500 dark:text-rose-400" aria-hidden="true"></i>
                    
                    <span class="absolute -bottom-1 -right-1 text-sm anim-tear text-sky-500"><i class="fa-solid fa-droplet" aria-hidden="true"></i></span>
                </div>
            </div>

            
            <div class="space-y-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 tracking-widest inline-block uppercase">
                        HTTP 404 — <?php echo e(__('PAGE NOT FOUND')); ?>

                    </span>
                </div>

                <h1 class="font-heading text-5xl sm:text-6xl font-black text-slate-900 dark:text-white tracking-tight flex items-center justify-center gap-3 anim-num-pulse">
                    <span class="text-teal-600 dark:text-teal-400">4</span>
                    <span class="text-rose-500 dark:text-rose-400">0</span>
                    <span class="text-teal-600 dark:text-teal-400">4</span>
                </h1>

                <h2 class="font-heading text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center justify-center gap-2">
                    <span><?php echo e(__('Oh no! Page Not Found')); ?></span> <i class="fa-solid fa-face-frown" aria-hidden="true"></i>
                </h2>

                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base font-medium leading-relaxed max-w-lg mx-auto dir-auto" style="unicode-bidi: plaintext;">
                    <?php echo e($cleanMsg); ?>

                </p>
            </div>

            
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?php echo e($dashUrl); ?>" class="btn-primary w-full sm:w-auto px-7 py-3.5 text-xs font-extrabold rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <span><i class="fa-solid fa-house" aria-hidden="true"></i></span> <?php echo e($dashLabel); ?>

                </a>

                <button type="button" onclick="window.history.back()" class="btn-secondary w-full sm:w-auto px-6 py-3.5 text-xs font-extrabold rounded-2xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <?php echo e(__('Go Back')); ?>

                </button>
            </div>

            
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-center gap-4 text-xs font-mono font-bold text-slate-500 dark:text-slate-400">
                <a href="<?php echo e(route('home')); ?>" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"><?php echo e(__('Home')); ?></a>
                <span>•</span>
                <a href="<?php echo e(route('courses')); ?>" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"><?php echo e(__('Courses')); ?></a>
                <span>•</span>
                <a href="<?php echo e(route('teachers')); ?>" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"><?php echo e(__('Teachers')); ?></a>
                <span>•</span>
                <a href="<?php echo e(route('contact')); ?>" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"><?php echo e(__('Contact Support')); ?></a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400 dark:text-slate-500">
            <?php echo e(__('Elite Academy Platform — Intelligent Error Handling & Navigation')); ?>

        </p>
    </div>

    
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/errors/404.blade.php ENDPATH**/ ?>