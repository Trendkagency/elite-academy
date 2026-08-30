@use('App\Models\SiteSetting')
{{-- Home Hero Slider: Zero-Dependency High-Performance Carousel --}}
@php
    $dbHeroSlides = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? \App\Models\HeroSlide::where('is_active', true)->orderBy('sort_order')->get()
        : collect();
    $totalSlideCount = $dbHeroSlides->count() > 0 ? $dbHeroSlides->count() : 4;
@endphp

<section 
    id="hero-slider-section"
    class="w-full min-h-[75vh] lg:min-h-[92vh] relative overflow-hidden bg-slate-950 text-white flex flex-col justify-between hero-section select-none"
>
    {{-- Subtle Decorative Static Accent Glows --}}
    <div class="absolute -top-24 -left-24 w-[36rem] h-[36rem] bg-teal-500/15 rounded-full blur-2xl pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 right-0 w-[40rem] h-[40rem] bg-orange-500/10 rounded-full blur-2xl pointer-events-none -z-10"></div>

    <div class="relative w-full flex-1 flex flex-col justify-between min-h-[65vh] lg:min-h-[78vh]">
    {{-- DYNAMIC DB SLIDES --}}
    @if($dbHeroSlides->count() > 0)
        @foreach($dbHeroSlides as $idx => $s)
            <div 
                data-slide-index="{{ $idx }}"
                class="hero-slide absolute inset-0 {{ $idx === 0 ? 'opacity-100 z-10 block' : 'opacity-0 z-0 hidden pointer-events-none' }} transition-opacity duration-700 ease-out flex flex-col justify-between"
            >
                <img src="{{ media_url($s->image, 'images/hero_student.webp') }}" alt="{{ $s->title }}" width="1920" height="1080" class="absolute inset-0 w-full h-full object-cover" {{ $idx === 0 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"' }}>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                    <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                        @if($s->track_label)
                            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md shadow-md">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                                <span>{{ $s->getLocalizedTrackLabel() }}</span>
                            </div>
                        @endif

                        <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                            {{ $s->getLocalizedTitle() }}
                        </h1>

                        <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                            {{ $s->getLocalizedSubtitle() }}
                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                            @if($s->cta_primary_url)
                                <a href="{{ $s->cta_primary_url }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25" aria-label="Explore {{ $s->getLocalizedTitle() }}">
                                    <span>{{ __('Explore Now') }}</span>
                                    <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                                </a>
                            @endif
                            @if($s->cta_secondary_url)
                                <a href="{{ $s->cta_secondary_url }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10" aria-label="Learn more about {{ $s->getLocalizedTitle() }}">
                                    <span>{{ __('Learn More') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                        <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                            <div class="text-left">
                                <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_accredited') }}</p>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_courses') }}</p>
                            </div>
                        </div>
                        <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                            <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                            <div class="text-left">
                                <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_global') }}</p>
                                <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_students') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        {{-- DEFAULT SLIDE 01: ACADEMIC PLATFORM --}}
        <div 
            data-slide-index="0"
            class="hero-slide absolute inset-0 opacity-100 z-10 block transition-opacity duration-700 ease-out flex flex-col justify-between"
        >
            <img src="{{ asset('images/hero_student.webp') }}" alt="Programming & Tech Lab" width="1920" height="1080" class="absolute inset-0 w-full h-full object-cover" fetchpriority="high" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                        <span>{{ SiteSetting::getLocalized('landing_hero_badge', '🚀 EGYPT’S #1 ACADEMIC PLATFORM') }}</span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        {{ SiteSetting::getLocalized('landing_hero_title', 'Empowering Future Leaders with Practical Academic Excellence') }}
                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        {{ SiteSetting::getLocalized('landing_hero_subtitle', 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="{{ SiteSetting::get('landing_cta_primary_link', '/subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25" aria-label="Explore all academic subjects">
                            <span>{{ SiteSetting::getLocalized('landing_cta_primary_text', 'Explore All Subjects') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10" aria-label="Book a free academic trial session">
                            <span>{{ __('Book Free Trial') }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_accredited') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_courses') }}</p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_global') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_students') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEFAULT SLIDE 02: ARTIFICIAL INTELLIGENCE --}}
        <div 
            data-slide-index="1"
            class="hero-slide absolute inset-0 opacity-0 z-0 hidden pointer-events-none transition-opacity duration-700 ease-out flex flex-col justify-between"
        >
            <img src="{{ asset('images/course_ai.webp') }}" alt="AI Neural Networks Lab" width="1920" height="1080" class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold tracking-wide backdrop-blur-md shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>🧠 {{ __('Artificial Intelligence Track') }}</span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        {!! __('Learn Artificial Intelligence. <span class="text-purple-300 underline decoration-teal-400 underline-offset-8">Shape Tomorrow.</span>') !!}
                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        {{ __('Explore Machine Learning, Deep Neural Networks, and modern computer vision.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="{{ route('subject-details') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 shadow-lg shadow-purple-600/25" aria-label="Explore AI Subject Curriculum">
                            <span>{{ __('Explore AI') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('courses') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10" aria-label="View Full Course Curriculum">
                            <span>{{ __('View Curriculum') }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">50+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('Autonomous') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('AI Models') }}</p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">Live</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('Hands-On') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('Mentorship') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEFAULT SLIDE 03: ROBOTICS & ENGINEERING --}}
        <div 
            data-slide-index="2"
            class="hero-slide absolute inset-0 opacity-0 z-0 hidden pointer-events-none transition-opacity duration-700 ease-out flex flex-col justify-between"
        >
            <img src="{{ asset('images/instructor_male.webp') }}" alt="Robotics Engineering Lab" width="1920" height="1080" class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-orange-500/20 border border-orange-400/30 text-orange-300 text-xs font-bold tracking-wide backdrop-blur-md shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                        <span>🤖 {{ __('Robotics Track') }}</span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        {!! __('Build. Create. <span class="text-orange-300 underline decoration-purple-400 underline-offset-8">Innovate.</span>') !!}
                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        {{ __('Design real robots and autonomous engineering hardware inside state-of-the-art labs.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="{{ route('subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 shadow-lg shadow-orange-500/25" aria-label="Explore Robotics Track">
                            <span>{{ __('Explore Robotics') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('event-details') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10" aria-label="Join Robotics Engineering Workshop">
                            <span>{{ __('Join Workshop') }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">50+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('Autonomous') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('Robotics Projects') }}</p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">Live</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('Hands-On') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('Workshops') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEFAULT SLIDE 04: SCIENCE & MATHEMATICS --}}
        <div 
            data-slide-index="3"
            class="hero-slide absolute inset-0 opacity-0 z-0 hidden pointer-events-none transition-opacity duration-700 ease-out flex flex-col justify-between"
        >
            <img src="{{ asset('images/academy_campus.webp') }}" alt="Science Laboratory" width="1920" height="1080" class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md shadow-md">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>🔬 {{ __('Science & Math Track') }}</span>
                    </div>

                    <h1 class="font-heading font-extrabold text-[28px] sm:text-[34px] md:text-5xl lg:text-7xl text-white tracking-tight leading-snug lg:leading-[1.1] drop-shadow-md text-center lg:text-left line-clamp-2">
                        {!! __('Curiosity Creates <span class="text-teal-300 underline decoration-orange-500 underline-offset-8">Excellence.</span>') !!}
                    </h1>

                    <p class="text-slate-200 text-[16px] sm:text-[18px] lg:text-xl font-medium leading-relaxed max-w-xl drop-shadow-sm text-center lg:text-left">
                        {{ __('Interactive science and mathematics education designed to build problem-solving mindsets.') }}
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3.5 pt-2 w-full max-w-md lg:max-w-none">
                        <a href="{{ route('subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 shadow-lg shadow-teal-500/25" aria-label="Explore Science & Math">
                            <span>{{ __('Explore Science') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10" aria-label="Book a free science trial session">
                            <span>{{ __('Book Trial') }}</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 w-full max-w-md mx-auto grid grid-cols-2 lg:flex lg:flex-col gap-3 pt-4 lg:pt-0">
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">120+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_accredited') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_courses') }}</p>
                        </div>
                    </div>
                    <div class="bg-white/95 backdrop-blur-md border border-white/70 rounded-3xl shadow-xl p-3.5 sm:p-4 flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-14 sm:h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-xl sm:text-2xl shadow-xs shrink-0">25K+</div>
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] font-mono uppercase tracking-widest font-semibold text-slate-400">{{ __('home.stat_global') }}</p>
                            <p class="text-xs sm:text-base font-bold text-slate-900 leading-tight">{{ __('home.stat_students') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>

    {{-- Progress Bar & Interactive Slide Controls --}}
    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-8 flex items-center justify-between border-t border-white/15 pt-6">
        <div class="font-mono text-sm font-bold text-slate-200 flex items-center gap-4">
            <span id="hero-active-slide-num" class="text-teal-400 text-xl font-extrabold tracking-wider">01</span>
            <div class="w-32 sm:w-48 h-1.5 bg-white/20 rounded-full relative overflow-hidden">
                <div id="hero-progress-bar" class="absolute top-0 bottom-0 left-0 bg-teal-400 rounded-full transition-all duration-500" style="width: {{ round((1 / $totalSlideCount) * 100) }}%"></div>
            </div>
            <span class="text-slate-400 text-sm">{{ str_pad($totalSlideCount, 2, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="flex items-center gap-4">
            <div id="hero-dots-container" class="hidden sm:flex items-center gap-2.5">
                @for ($i = 0; $i < $totalSlideCount; $i++)
                    <button 
                        type="button" 
                        onclick="window.heroSlider && window.heroSlider.goTo({{ $i }})" 
                        class="hero-dot h-3 rounded-full transition-all duration-300 cursor-pointer {{ $i === 0 ? 'bg-teal-400 w-7' : 'bg-white/30 w-3 hover:bg-white/70' }}" 
                        aria-label="Go to slide {{ $i + 1 }}"
                    ></button>
                @endfor
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="window.heroSlider && window.heroSlider.prev()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Previous Slide">&larr;</button>
                <button type="button" onclick="window.heroSlider && window.heroSlider.next()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Next Slide">&rarr;</button>
            </div>
        </div>
    </div>
</section>

{{-- Lightweight Vanilla JS Slider Controller --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const section = document.getElementById('hero-slider-section');
    if (!section) return;

    const slides = Array.from(section.querySelectorAll('.hero-slide'));
    const dots = Array.from(section.querySelectorAll('.hero-dot'));
    const numEl = document.getElementById('hero-active-slide-num');
    const progressEl = document.getElementById('hero-progress-bar');
    const total = slides.length;
    if (total <= 1) return;

    let active = 0;
    let timer = null;

    function renderSlide(nextIdx) {
        if (nextIdx === active) return;
        const currentSlide = slides[active];
        const nextSlide = slides[nextIdx];

        if (currentSlide) {
            currentSlide.classList.remove('opacity-100', 'z-10', 'block');
            currentSlide.classList.add('opacity-0', 'z-0', 'pointer-events-none');
            setTimeout(() => {
                currentSlide.classList.add('hidden');
            }, 700);
        }

        if (nextSlide) {
            nextSlide.classList.remove('hidden', 'opacity-0', 'z-0', 'pointer-events-none');
            nextSlide.classList.add('block', 'z-10');
            requestAnimationFrame(() => {
                nextSlide.classList.add('opacity-100');
            });
        }

        dots.forEach((dot, idx) => {
            if (idx === nextIdx) {
                dot.className = 'hero-dot h-3 rounded-full transition-all duration-300 cursor-pointer bg-teal-400 w-7';
            } else {
                dot.className = 'hero-dot h-3 rounded-full transition-all duration-300 cursor-pointer bg-white/30 w-3 hover:bg-white/70';
            }
        });

        if (numEl) {
            numEl.textContent = String(nextIdx + 1).padStart(2, '0');
        }
        if (progressEl) {
            progressEl.style.width = (((nextIdx + 1) / total) * 100) + '%';
        }

        active = nextIdx;
    }

    function next() {
        renderSlide((active + 1) % total);
    }

    function prev() {
        renderSlide((active - 1 + total) % total);
    }

    function goTo(index) {
        renderSlide(index);
    }

    function startTimer() {
        stopTimer();
        timer = setInterval(next, 7000);
    }

    function stopTimer() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    window.heroSlider = { next, prev, goTo, start: startTimer, stop: stopTimer };

    section.addEventListener('mouseenter', stopTimer, { passive: true });
    section.addEventListener('mouseleave', startTimer, { passive: true });

    startTimer();
});
</script>
