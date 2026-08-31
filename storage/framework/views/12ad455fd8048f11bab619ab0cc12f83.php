<?php $__env->startSection('content'); ?>
<section class="py-8 md:py-12 bg-slate-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <?php echo $__env->make('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal'), 'route' => 'student-portal'],
                ['label' => $session->title ?: (app()->getLocale() === 'ar' ? 'البث المباشر' : 'Live Meeting')],
            ]
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex items-center justify-between">
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight">
                <?php echo e($session->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream Session')); ?>

            </h1>
            <a href="<?php echo e(route('student-portal')); ?>" class="btn-lift px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold font-mono transition-all">
                ← <?php echo e(app()->getLocale() === 'ar' ? 'العودة للمنصة' : 'Back to Dashboard'); ?>

            </a>
        </div>

        
        <?php echo $__env->make('components.meeting-container', ['session' => $session, 'user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/student-meeting.blade.php ENDPATH**/ ?>