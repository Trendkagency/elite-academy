{{-- Subject Card Component
     @param string $image — Image asset path
     @param string $grade — Grade badge text (e.g. "Secondary 1 • Grade 10")
     @param string $badgeColor — Grade badge bg class (e.g. "bg-teal-600", "bg-blue-600")
     @param string $name — Subject name (e.g. "Mathematics")
     @param string $description — Short description
     @param string $teachers — Teachers count (e.g. "12 Teachers")
     @param string $lessons — Lessons/courses count (e.g. "48 Lessons")
     @param string $route — Route URL for detail page
--}}
@php
    $badgeColor = $badgeColor ?? 'bg-teal-600';
    $route = $route ?? route('subject-details');
@endphp

<div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-lg hover:shadow-2xl card-lift group transition-all duration-300 flex flex-col justify-between h-[460px]">
    <div class="relative h-56 overflow-hidden bg-slate-950">
        <img src="{{ media_url($image, 'images/course_ai.webp') }}" loading="lazy" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold text-white {{ $badgeColor }} px-3 py-1 rounded-full shadow-md">
            {{ $grade }}
        </span>
    </div>

    <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
        <div class="space-y-1">
            <h3 class="font-heading font-extrabold text-xl text-slate-900 group-hover:text-teal-600 transition-colors">
                {{ $name }}
            </h3>
            <p class="text-xs text-slate-500 line-clamp-2">{{ $description }}</p>
        </div>

        <div class="pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 space-y-3">
            <div class="flex items-center justify-between font-mono text-[11px]">
                <span>👨‍🏫 {{ $teachers }}</span>
                <span>📚 {{ $lessons }}</span>
            </div>
            <a href="{{ $route }}" class="btn-lift block w-full text-center py-2 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white font-extrabold rounded-xl transition-all">
                Explore Subject &rarr;
            </a>
        </div>
    </div>
</div>
