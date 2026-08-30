@use('App\Models\SiteSetting')
@php
    $locale = app()->getLocale();
    $badge = SiteSetting::getLocalized('why_badge', __('home.why_badge'));
    $title = SiteSetting::getLocalized('why_title', __('Why Students Choose Elite Academy'));
    $subtitle = SiteSetting::getLocalized('why_subtitle', __('Elite Academy blends academic rigour with real-world application to give students the edge they need.'));
    
    $rawItems = SiteSetting::get('landing_why_items');
    $customItems = $rawItems ? json_decode($rawItems, true) : null;
    
    $features = $customItems ? array_map(function($item) use ($locale) {
        return [
            'icon' => '✨',
            'title' => ($locale === 'ar' ? ($item['title_ar'] ?? null) : null) ?: ($item['title_en'] ?? ''),
            'desc' => ($locale === 'ar' ? ($item['desc_ar'] ?? null) : null) ?: ($item['desc_en'] ?? ''),
        ];
    }, $customItems) : [
        ['icon' => '🎓', 'title' => __('250+ Courses'), 'desc' => __('Industry-recognized curriculum.')],
        ['icon' => '👨‍🏫', 'title' => __('Expert Teachers'), 'desc' => __('Learn from experienced educators.')],
        ['icon' => '🌍', 'title' => __('International Certificates'), 'desc' => __('Recognized academic credentials.')],
        ['icon' => '🛠', 'title' => __('Practical Learning'), 'desc' => __('Hands-on projects and labs.')],
    ];
@endphp

{{-- Why Choose Elite Academy Section --}}
<section class="py-12 sm:py-20 lg:py-28 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/70">
    {{-- Ambient Backdrop Blurs --}}
    <div class="absolute top-1/3 -left-20 w-72 h-72 sm:w-[30rem] sm:h-[30rem] bg-teal-400/15 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>
    <div class="absolute bottom-10 right-0 w-64 h-64 sm:w-[28rem] sm:h-[28rem] bg-orange-400/12 rounded-full blur-3xl pointer-events-none -z-10 animate-float"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Unified Side-by-Side Horizontal Composition --}}
        <div class="flex flex-row items-center gap-4 sm:gap-8 lg:gap-14">

            {{-- LEFT SIDE: Mentor Photo Frame --}}
            <div class="w-[38%] sm:w-[40%] lg:w-[42%] flex-shrink-0 relative group sr-img">
                <div class="absolute -top-3 -left-3 sm:-top-6 sm:-left-6 w-24 h-24 sm:w-48 sm:h-48 bg-teal-400/25 rounded-full blur-2xl pointer-events-none -z-10 animate-pulse-glow"></div>
                <div class="absolute -bottom-3 -right-3 sm:-bottom-6 sm:-right-6 w-24 h-24 sm:w-48 sm:h-48 bg-orange-400/20 rounded-full blur-2xl pointer-events-none -z-10 animate-float"></div>

                <div class="relative w-full aspect-[4/5] rounded-[20px] sm:rounded-[28px] lg:rounded-[32px] overflow-hidden shadow-xl sm:shadow-2xl shadow-slate-900/15 border-2 sm:border-4 border-white animate-float">
                    <img src="{{ asset('images/hero_student.webp') }}" alt="Why Students Choose Elite Academy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent pointer-events-none"></div>
                </div>
            </div>

            {{-- RIGHT SIDE: Editorial Content Stack --}}
            <div class="flex-1 space-y-3 sm:space-y-6">

                <div class="space-y-1.5 sm:space-y-3">
                    <span class="inline-block text-[10px] sm:text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-2.5 py-1 sm:px-3.5 sm:py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">{{ $badge }}</span>

                    <h2 class="font-heading font-black text-base sm:text-3xl md:text-4xl lg:text-5xl text-slate-900 tracking-tight leading-tight">
                        {{ $title }}
                    </h2>

                    <p class="text-slate-600 text-xs sm:text-base font-medium leading-relaxed line-clamp-2">{{ $subtitle }}</p>
                </div>

                {{-- Feature Rows --}}
                <div class="space-y-2 sm:space-y-4 pt-1 sm:pt-2" data-stagger>
                    @foreach ($features as $feature)
                        <div class="flex items-center gap-2.5 sm:gap-4 group cursor-default">
                            <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-teal-50 text-slate-900 group-hover:text-teal-600 group-hover:scale-110 flex items-center justify-center font-extrabold text-xs sm:text-lg transition-all duration-300 flex-shrink-0 shadow-2xs">
                                {{ $feature['icon'] }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading font-extrabold text-xs sm:text-lg text-slate-900 group-hover:text-teal-600 transition-colors leading-tight truncate">{{ $feature['title'] }}</h3>
                                <p class="text-[10px] sm:text-xs text-slate-500 font-medium truncate">{{ $feature['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>

    </div>
</section>
