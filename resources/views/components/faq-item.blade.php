{{-- FAQ Accordion Item Component
     @param string $question — The FAQ question
     @param string $answer — The FAQ answer text
--}}
<details class="group bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs">
    <summary class="flex justify-between items-center font-bold text-slate-900 cursor-pointer">
        <span>{{ $question }}</span>
        <span class="faq-icon text-teal-600 font-bold text-lg transition-transform duration-300">+</span>
    </summary>
    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $answer }}</p>
</details>
