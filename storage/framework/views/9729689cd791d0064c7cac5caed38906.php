<?php
    $categoryBg = $categoryBg ?? 'bg-teal-600';
    $instructorBorder = $instructorBorder ?? 'border-teal-500';
    $route = $route ?? route('course-details');
    $isEnrolled = $isEnrolled ?? false;
    $hasFreeDemo = $hasFreeDemo ?? true;
    $isArabic = app()->getLocale() === 'ar';
?>

<div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-2xs hover-lift flex flex-col justify-between group">
    <div>
        <div class="relative h-48 overflow-hidden bg-slate-100">
            <img src="<?php echo e(media_url($image, 'images/course_ai.png')); ?>" alt="<?php echo e($title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <span class="absolute top-4 left-4 <?php echo e($categoryBg); ?> text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs"><?php echo e($category); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled): ?>
                <span class="absolute top-4 right-4 bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>✓</span> <?php echo e($isArabic ? 'مشترك' : 'Enrolled'); ?>

                </span>
            <?php elseif($hasFreeDemo): ?>
                <span class="absolute top-4 right-4 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>▶</span> <?php echo e($isArabic ? 'حصة مجانية' : 'Free Demo'); ?>

                </span>
            <?php else: ?>
                <span class="absolute top-4 right-4 bg-rose-700 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1 shadow-rose-700/20">
                    <span>🔒</span> <?php echo e($isArabic ? 'باقة مطلوب' : 'Package Required'); ?>

                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center gap-2">
                <img src="<?php echo e(media_url($instructorPhoto, 'images/instructor_portrait.png')); ?>" alt="<?php echo e($instructor); ?>" class="w-7 h-7 rounded-full object-cover border <?php echo e($instructorBorder); ?>">
                <span class="text-xs font-bold text-slate-900"><?php echo e($instructor); ?></span>
            </div>
            <h3 class="font-heading font-bold text-xl text-slate-900 group-hover:text-teal-600 transition-colors">
                <a href="<?php echo e($route); ?>"><?php echo e($title); ?></a>
            </h3>
            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2"><?php echo e($description); ?></p>
        </div>
    </div>
    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between mt-4">
        <span class="font-mono font-bold text-lg text-slate-900"><?php echo e($price); ?></span>
        <div class="flex items-center gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled): ?>
                <a href="<?php echo e(route('student-portal')); ?>" class="text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    <?php echo e($isArabic ? 'الانتقال للبوابة ←' : 'Go to Portal →'); ?>

                </a>
            <?php else: ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFreeDemo): ?>
                    <a href="<?php echo e($route); ?>#demo" class="text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-xl transition-colors border border-orange-200">
                        <?php echo e($isArabic ? 'حصة تجريبية' : 'Free Demo'); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="<?php echo e($route); ?>" class="text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    <?php echo e($isArabic ? 'تسجيل بالدورة' : 'Enroll'); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views\components\course-card.blade.php ENDPATH**/ ?>