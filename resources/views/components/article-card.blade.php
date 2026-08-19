{{-- Article Card Component (Blog / Events)
     @param string $image — Image asset path
     @param string $imageAlt — Image alt text
     @param string $category — Category label
     @param string $categoryColor — Tailwind bg color class (e.g. 'bg-teal-600')
     @param string $title — Article title
     @param string $excerpt — Article excerpt text
     @param string $author — Author name
     @param string $date — Publication date
     @param string $readTime — Read time text (e.g. '6 min read')
     @param string|null $route — Link URL (defaults to '#')
--}}
@php $route = $route ?? '#'; @endphp

<a href="{{ $route }}" class="block space-y-6 group cursor-pointer p-6 -mx-6 rounded-3xl hover:bg-white transition-all duration-300 hover:shadow-xl border border-transparent hover:border-slate-200/90">
    {{-- Image --}}
    <div class="relative w-full h-56 sm:h-96 lg:h-[440px] rounded-3xl overflow-hidden shadow-lg bg-slate-950">
        <img src="{{ media_url($image, 'images/course_ai.png') }}" loading="lazy" alt="{{ $imageAlt ?? $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
        <span class="absolute top-6 left-6 text-xs font-mono font-extrabold text-white {{ $categoryColor }} px-4 py-1.5 rounded-full shadow-md uppercase tracking-wider">
            {{ $category }}
        </span>
    </div>

    {{-- Content --}}
    <div class="space-y-4 max-w-3xl">
        <h2 class="font-heading font-black text-2xl sm:text-3xl lg:text-4xl text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
            {{ $title }}
        </h2>

        <p class="text-slate-600 text-base sm:text-lg leading-relaxed font-normal">
            {{ $excerpt }}
        </p>

        <div class="pt-2 flex items-center justify-between text-xs font-mono text-slate-500 font-bold">
            <div class="flex items-center gap-3">
                <span class="text-slate-700 font-extrabold">{{ $author }}</span>
                <span>•</span>
                <span>{{ $date }}</span>
                <span>•</span>
                <span class="text-slate-500 font-bold">{{ $readTime }}</span>
            </div>
        </div>
    </div>
</a>
