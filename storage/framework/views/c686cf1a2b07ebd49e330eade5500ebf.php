<?php $__env->startSection('content'); ?>
<section class="py-12 md:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <?php echo $__env->make('components.section-header', [
            'badge' => 'Help Center & Support',
            'title' => 'Frequently Asked Questions',
            'subtitle' => 'Everything you need to know about our curriculums, faculty, certification, and parent tools.',
            'centered' => true,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</section>


<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="space-y-4">
            <h2 class="font-heading font-bold text-xl text-slate-900 border-b border-slate-200 pb-2">Admissions & Enrollment</h2>

            <?php echo $__env->make('components.faq-item', [
                'question' => 'How do I enroll in a course?',
                'answer' => 'Simply select your desired course from the catalog and click \'Enroll\'. Every course includes a free sample demo lesson prior to payment.',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('components.faq-item', [
                'question' => 'Are certificates accredited?',
                'answer' => 'Yes! All capstone completions earn a verified digital certificate recognized by global university admissions boards.',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views\pages\faq.blade.php ENDPATH**/ ?>