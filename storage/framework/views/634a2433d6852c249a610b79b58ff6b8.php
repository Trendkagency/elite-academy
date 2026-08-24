
<details class="group bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-teal-500/40 transition-all" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
    <summary class="flex justify-between items-center font-heading font-bold text-slate-900 cursor-pointer list-none select-none">
        <span itemprop="name" class="text-base sm:text-lg pr-4"><?php echo e($question); ?></span>
        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xl shrink-0 group-open:bg-teal-600 group-open:text-white transition-all duration-300">
            <span class="group-open:rotate-45 transition-transform duration-300">+</span>
        </div>
    </summary>
    <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" class="mt-4 pt-3 border-t border-slate-100 text-sm text-slate-600 leading-relaxed space-y-2">
        <p itemprop="text"><?php echo e($answer); ?></p>
    </div>
</details>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/faq-item.blade.php ENDPATH**/ ?>