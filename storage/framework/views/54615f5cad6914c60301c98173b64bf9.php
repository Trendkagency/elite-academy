<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0d111d] text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white overflow-x-hidden">
    <?php echo $__env->make('components.meeting-container', ['session' => $session, 'user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', ['minimalLayout' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\student-meeting.blade.php ENDPATH**/ ?>