
<?php $route = $route ?? '#'; ?>

<a href="<?php echo e($route); ?>" class="block space-y-6 group cursor-pointer p-6 -mx-6 rounded-3xl hover:bg-white transition-all duration-300 hover:shadow-xl border border-transparent hover:border-slate-200/90">
    
    <div class="relative w-full h-56 sm:h-96 lg:h-[440px] rounded-3xl overflow-hidden shadow-lg bg-slate-950">
        <img src="<?php echo e(media_url($image, 'images/course_ai.webp')); ?>" loading="lazy" alt="<?php echo e($imageAlt ?? $title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
        <span class="absolute top-6 left-6 text-xs font-mono font-extrabold text-white <?php echo e($categoryColor); ?> px-4 py-1.5 rounded-full shadow-md uppercase tracking-wider">
            <?php echo e($category); ?>

        </span>
    </div>

    
    <div class="space-y-4 max-w-3xl">
        <h2 class="font-heading font-black text-2xl sm:text-3xl lg:text-4xl text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
            <?php echo e($title); ?>

        </h2>

        <p class="text-slate-600 text-base sm:text-lg leading-relaxed font-normal">
            <?php echo e($excerpt); ?>

        </p>

        <div class="pt-2 flex items-center justify-between text-xs font-mono text-slate-500 font-bold">
            <div class="flex items-center gap-3">
                <span class="text-slate-700 font-extrabold"><?php echo e($author); ?></span>
                <span>•</span>
                <span><?php echo e($date); ?></span>
                <span>•</span>
                <span class="text-slate-500 font-bold"><?php echo e($readTime); ?></span>
            </div>
        </div>
    </div>
</a>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/article-card.blade.php ENDPATH**/ ?>