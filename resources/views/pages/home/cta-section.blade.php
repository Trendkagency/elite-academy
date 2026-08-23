@use('App\Models\SiteSetting')
@php
    $ctaBg = \App\Models\SiteSetting::get('cta_bg_image');
@endphp

{{-- Full-Width CTA Banner Section --}}
<section class="py-20 md:py-28 bg-gradient-to-br from-teal-950 via-slate-900 to-teal-950 text-white relative overflow-hidden">
    @if($ctaBg)
        <img src="{{ media_url($ctaBg) }}" alt="CTA Background" class="absolute inset-0 w-full h-full object-cover opacity-20 pointer-events-none">
    @endif

    {{-- Ambient Glow Effects --}}
    <div class="absolute -top-16 left-1/4 w-[32rem] h-[32rem] bg-teal-500/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
    <div class="absolute -bottom-16 right-1/4 w-[32rem] h-[32rem] bg-orange-500/15 rounded-full blur-3xl pointer-events-none animate-float"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 sm:space-y-8 relative z-10">

        <span class="inline-block text-xs sm:text-sm font-mono font-extrabold uppercase tracking-widest text-teal-300 bg-teal-900/80 px-4 py-2 rounded-full border border-teal-500/30 shadow-lg animate-badge-pulse">
            🚀 {{ __('Ready To Start Learning?') }}
        </span>

        <h2 class="font-heading font-black text-3xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-tight drop-shadow-md">
            {{ \App\Models\SiteSetting::getLocalized('cta_headline', "Ready to Excel in Your Academic Journey?") }}
        </h2>

        <p class="text-slate-300 text-base sm:text-lg font-medium max-w-2xl mx-auto leading-relaxed">
            {{ \App\Models\SiteSetting::getLocalized('cta_subtitle', "Join Elite Academy today and gain unlimited access to top teachers, interactive live streams, and accredited courses.") }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            <a href="{{ route('student-portal') }}" class="btn-lift btn-mobile-lg sm:w-auto text-base font-extrabold text-slate-200 bg-slate-800/90 hover:bg-slate-700 px-8 py-4 rounded-2xl border border-slate-700/80 touch-press shadow-lg">
                {{ __('Student Portal') }}
            </a>
            <a href="{{ route('subjects') }}" class="btn-lift btn-mobile-lg sm:w-auto text-base font-extrabold text-white bg-teal-600 hover:bg-teal-500 px-8 py-4 rounded-2xl shadow-xl shadow-teal-600/30 touch-press border border-teal-400/30">
                {{ __('Explore Subjects') }} →
            </a>
        </div>

    </div>
</section>

{{-- Full-Width Bottom Stats Strip --}}
<section class="bg-slate-950 py-8 border-t border-slate-800 text-slate-300">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-4 text-center divide-x rtl:divide-x-reverse divide-slate-800/80">

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-teal-400" data-count="+25K">+25K</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">{{ __('Students') }}</p>
            </div>

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-orange-400" data-count="+120">+120</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">{{ __('Courses') }}</p>
            </div>

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-teal-400" data-count="+45">+45</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">{{ __('Teachers') }}</p>
            </div>

        </div>
    </div>
</section>

