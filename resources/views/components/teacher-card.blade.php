{{-- Teacher Card Component
     @param string $photo — Photo asset path
     @param string $name — Teacher full name
     @param string $title — Title / Specialization
     @param string $subject — Subject tag text
     @param string $subjectColor — Subject tag color class (e.g. 'bg-teal-600')
     @param string $rating — Rating text (e.g. "4.9 ★")
     @param string $students — Students count (e.g. "1.4k Students")
     @param string $route — Profile route URL
--}}
@php
    $subjectColor = $subjectColor ?? 'bg-teal-600';
    $route = $route ?? route('teacher-profile');
@endphp

<div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-lg hover:shadow-2xl card-lift group transition-all duration-300 flex flex-col justify-between h-[440px]">
    <div class="relative h-56 overflow-hidden bg-slate-950">
        <img src="{{ media_url($photo, 'images/instructor_portrait.png') }}" loading="lazy" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
        <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold text-white {{ $subjectColor }} px-3 py-1 rounded-full shadow-md">
            {{ $subject }}
        </span>
        <span class="absolute top-4 right-4 text-[10px] font-mono font-extrabold text-white bg-slate-900/80 backdrop-blur-xs px-2.5 py-1 rounded-full border border-white/20">
            {{ $rating }}
        </span>
    </div>

    <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
        <div class="space-y-1">
            <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-teal-600 transition-colors">
                {{ $name }}
            </h3>
            <p class="text-xs font-mono text-slate-500 line-clamp-1">{{ $title }}</p>
        </div>

        <div class="pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 space-y-3">
            <div class="flex items-center justify-between font-mono text-[11px]">
                <span>🎓 Verified Mentor</span>
                <span>👥 {{ $students }}</span>
            </div>
            <a href="{{ $route }}" class="btn-lift block w-full text-center py-2 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white font-extrabold rounded-xl transition-all">
                View Profile &rarr;
            </a>
        </div>
    </div>
</div>
