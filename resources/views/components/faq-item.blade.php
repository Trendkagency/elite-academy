{{-- FAQ Accordion Item Component with Schema.org Microdata for AI Search Engine Crawlers
     @param string $question — The FAQ question text
     @param string $answer — The FAQ answer text
--}}
<details class="group bg-white dark:bg-slate-800/90 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700/80 shadow-xs hover:border-teal-500/40 transition-all" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
    <summary class="flex justify-between items-center font-heading font-bold text-slate-900 dark:text-white cursor-pointer list-none select-none">
        <span itemprop="name" class="text-base sm:text-lg pr-4">{{ $question }}</span>
        <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-slate-700 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-xl shrink-0 group-open:bg-teal-600 group-open:text-white transition-all duration-300">
            <span class="group-open:rotate-45 transition-transform duration-300">+</span>
        </div>
    </summary>
    <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-300 leading-relaxed space-y-2">
        <p itemprop="text">{{ $answer }}</p>
    </div>
</details>
