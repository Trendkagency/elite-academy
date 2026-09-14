@use('App\Models\HeroSlide')
@use('App\Models\SiteSetting')
{{-- ═══════════════════════════════════════════════════════════════════
     ELITE ACADEMY — CINEMATIC HERO SLIDER & SWIPER
     Fully dynamic from DB · Per-slide design control
     Features: Mouse Drag / Swipe Swapper · Floating Side Arrows
     Ken-Burns zoom · Stagger text fade · Smooth Cross-Fade Transitions
     ════════════════════════════════════════════════════════════════ --}}
@php
    $slides = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? HeroSlide::where('is_active', true)->orderBy('sort_order')->get()
        : collect();

    $hasSlides    = $slides->count() > 0;
    $totalSlides  = $hasSlides ? $slides->count() : 1;
    $autoplayMs   = 6000; // ms per slide
@endphp

<section
    id="hero-slider-section"
    class="hero-slider-container w-full relative overflow-hidden bg-slate-950 text-white select-none"
    style="display:block; width:100%; min-height:580px; height:88vh; max-height:940px; position:relative; overflow:hidden; background-color:#020617; cursor:grab;"
    aria-label="{{ __('Hero Slider') }}"
    data-autoplay="{{ $autoplayMs }}"
>
    {{-- ── SLIDES TRACK ──────────────────────────────────────────── --}}
    <div id="hero-track" class="relative w-full h-full" style="position:relative; width:100%; height:100%;">

    @if($hasSlides)
        @foreach($slides as $idx => $slide)
            @php
                $accent   = $slide->getAccentClasses();
                $align    = $slide->getTextAlignClass();        // "text-left items-start" etc
                $position = $slide->getPositionClasses();       // [justify, items]
                $isFirst  = $idx === 0;
                $imgAttr  = $isFirst ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"';
            @endphp

            <article
                id="hero-slide-{{ $idx }}"
                data-slide-index="{{ $idx }}"
                role="group"
                aria-roledescription="slide"
                aria-label="{{ __('Slide') }} {{ $idx + 1 }} {{ __('of') }} {{ $totalSlides }}"
                aria-hidden="{{ $isFirst ? 'false' : 'true' }}"
                class="hero-slide absolute inset-0 w-full h-full flex flex-col
                       {{ $isFirst ? 'is-active opacity-100 z-10' : 'is-hidden opacity-0 z-0 pointer-events-none' }}"
            >
                {{-- Background Image with Ken-Burns --}}
                <div class="hero-img-wrap absolute inset-0 overflow-hidden">
                    <img
                        src="{{ media_url($slide->image, 'images/hero_student.webp') }}"
                        alt="{{ $slide->getLocalizedTitle() }}"
                        width="1920" height="1080"
                        {!! $imgAttr !!}
                        class="hero-bg-img w-full h-full object-cover origin-center scale-105 {{ $isFirst ? 'kb-active' : '' }}"
                    >
                </div>

                {{-- Dynamic Overlay --}}
                <div class="absolute inset-0" style="{{ $slide->getOverlayStyle() }}"></div>

                {{-- Decorative Accent Glow --}}
                <div class="absolute -top-32 -left-32 w-[40rem] h-[40rem] rounded-full blur-3xl opacity-20 pointer-events-none
                            {{ match($slide->accent_color ?? 'teal') {
                                'purple' => 'bg-purple-500',
                                'orange' => 'bg-orange-500',
                                'rose'   => 'bg-rose-500',
                                'sky'    => 'bg-sky-500',
                                'amber'  => 'bg-amber-500',
                                default  => 'bg-teal-500'
                            } }}">
                </div>

                {{-- ── CONTENT BLOCK ──────────────────────────────── --}}
                <div class="relative z-20 flex-1 flex {{ $position[0] }} {{ $position[1] }}
                            max-w-7xl mx-auto w-full px-6 sm:px-10 lg:px-16 pt-12 sm:pt-16 lg:pt-20 pb-24 sm:pb-28 lg:pb-32">

                    <div class="hero-content max-w-3xl lg:max-w-4xl xl:max-w-5xl space-y-4 sm:space-y-5 flex flex-col {{ $align }}">

                        {{-- Badge --}}
                        @if($slide->track_label)
                            <div class="hero-anim-badge inline-flex items-center gap-2.5 px-4 py-2 rounded-full
                                        {{ $accent['badge_bg'] }} {{ $accent['badge_border'] }} {{ $accent['badge_text'] }}
                                        border text-xs font-bold tracking-widest backdrop-blur-md shadow-md">
                                @if($slide->badge_icon)
                                    <i class="{{ $slide->badge_icon }} text-xs"></i>
                                @else
                                    <span class="w-2 h-2 rounded-full {{ $accent['dot_bg'] }}"></span>
                                @endif
                                <span>{{ $slide->getLocalizedTrackLabel() }}</span>
                            </div>
                        @endif

                        {{-- Headline --}}
                        <h1 class="hero-anim-title font-heading font-extrabold
                                   text-2xl sm:text-4xl md:text-5xl lg:text-5xl xl:text-6xl
                                   text-white tracking-tight leading-[1.18] sm:leading-[1.15] lg:leading-[1.12] drop-shadow-lg">
                            {!! $slide->getLocalizedTitle() !!}
                        </h1>

                        {{-- Subtitle --}}
                        @if($slide->subtitle)
                            <p class="hero-anim-sub text-slate-200 text-[16px] sm:text-lg lg:text-xl
                                      font-medium leading-relaxed max-w-xl drop-shadow-sm">
                                {{ $slide->getLocalizedSubtitle() }}
                            </p>
                        @endif

                        {{-- CTA Buttons --}}
                        @if($slide->cta_primary_url || $slide->cta_secondary_url)
                            <div class="hero-anim-btns flex flex-col sm:flex-row items-center gap-3.5 pt-2
                                        {{ $slide->text_align === 'center' ? 'justify-center' : ($slide->text_align === 'right' ? 'justify-end' : 'justify-start') }}">
                                @if($slide->cta_primary_url)
                                    <a href="{{ $slide->cta_primary_url }}"
                                       class="group btn-lift inline-flex items-center justify-center gap-2.5
                                              px-7 py-3.5 rounded-full font-bold text-sm sm:text-base
                                              shadow-lg transition-all duration-300
                                              {{ $accent['btn_primary'] }}">
                                        <span>{{ $slide->getLocalizedCtaPrimaryText() }}</span>
                                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">→</span>
                                    </a>
                                @endif
                                @if($slide->cta_secondary_url)
                                    <a href="{{ $slide->cta_secondary_url }}"
                                       class="btn-lift inline-flex items-center justify-center gap-2
                                              px-7 py-3.5 rounded-full font-bold text-sm sm:text-base
                                              text-white bg-white/10 hover:bg-white/20
                                              border border-white/25 backdrop-blur-md shadow-md
                                              transition-all duration-300">
                                        <span>{{ $slide->getLocalizedCtaSecondaryText() }}</span>
                                    </a>
                                @endif
                            </div>
                        @endif

                    </div>{{-- /hero-content --}}
                </div>{{-- /content wrapper --}}

            </article>
        @endforeach

    @else
        {{-- ── SINGLE ELEGANT FALLBACK (no DB slides) ─────────────── --}}
        <article
            id="hero-slide-0"
            data-slide-index="0"
            class="hero-slide is-active absolute inset-0 w-full h-full flex flex-col opacity-100 z-10"
            aria-label="{{ __('Welcome to Elite Academy') }}"
        >
            <div class="hero-img-wrap absolute inset-0 overflow-hidden">
                <img
                    src="{{ asset('images/hero_student.webp') }}"
                    alt="{{ __('Elite Academy') }}"
                    width="1920" height="1080"
                    fetchpriority="high" loading="eager"
                    class="hero-bg-img kb-active w-full h-full object-cover origin-center scale-105"
                >
            </div>
            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(2,6,23,0.92) 0%, rgba(2,6,23,0.55) 50%, rgba(2,6,23,0.28) 100%);"></div>
            <div class="absolute -top-32 -left-32 w-[40rem] h-[40rem] rounded-full blur-3xl opacity-20 pointer-events-none bg-teal-500"></div>

            <div class="relative z-20 flex-1 flex justify-start items-center
                        max-w-7xl mx-auto w-full px-6 sm:px-10 lg:px-16 pt-12 sm:pt-16 lg:pt-20 pb-24 sm:pb-28 lg:pb-32">
                <div class="hero-content max-w-3xl lg:max-w-4xl xl:max-w-5xl space-y-4 sm:space-y-5 flex flex-col items-start text-start rtl:text-right">
                    <div class="hero-anim-badge inline-flex items-center gap-2.5 px-4 py-2 rounded-full
                                bg-teal-500/20 border border-teal-400/30 text-teal-300
                                text-xs font-bold tracking-widest backdrop-blur-md shadow-md">
                        <i class="fa-solid fa-rocket text-xs"></i>
                        <span>{!! SiteSetting::getLocalized('landing_hero_badge', "🚀 EGYPT'S #1 ACADEMIC PLATFORM") !!}</span>
                    </div>
                    <h1 class="hero-anim-title font-heading font-extrabold
                               text-2xl sm:text-4xl md:text-5xl lg:text-5xl xl:text-6xl
                               text-white tracking-tight leading-[1.18] sm:leading-[1.15] lg:leading-[1.12] drop-shadow-lg">
                        {{ SiteSetting::getLocalized('landing_hero_title', 'Empowering Future Leaders') }}
                    </h1>
                    <p class="hero-anim-sub text-slate-200 text-[16px] sm:text-lg lg:text-xl
                              font-medium leading-relaxed max-w-xl drop-shadow-sm">
                        {{ SiteSetting::getLocalized('landing_hero_subtitle', 'Join thousands of students learning Programming, AI, Science, and Business from Egypt\'s top educators.') }}
                    </p>
                    <div class="hero-anim-btns flex flex-col sm:flex-row items-center gap-3.5 pt-2">
                        <a href="{{ SiteSetting::get('landing_cta_primary_link', '/subjects') }}"
                           class="group btn-lift inline-flex items-center justify-center gap-2.5
                                  px-7 py-3.5 rounded-full font-bold text-sm sm:text-base
                                  bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400
                                  text-slate-950 shadow-lg shadow-teal-500/25 transition-all duration-300">
                            <span>{{ SiteSetting::getLocalized('landing_cta_primary_text', 'Explore Now') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">→</span>
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn-lift inline-flex items-center justify-center gap-2
                                  px-7 py-3.5 rounded-full font-bold text-sm sm:text-base
                                  text-white bg-white/10 hover:bg-white/20 border border-white/25
                                  backdrop-blur-md shadow-md transition-all duration-300">
                            <span>{{ __('Book Free Trial') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </article>
    @endif

    </div>{{-- /hero-track --}}

    {{-- ── 1. PROMINENT FLOATING SIDE SWAPPER ARROWS (Left & Right) ── --}}
    @if($totalSlides > 1)
        {{-- Previous Arrow (Left) --}}
        <button
            type="button"
            id="hero-prev-btn"
            onclick="window.__heroSlider && window.__heroSlider.prev()"
            class="hero-side-btn hero-side-prev absolute left-3 sm:left-6 md:left-8 top-1/2 -translate-y-1/2 z-40
                   w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center
                   bg-slate-950/70 hover:bg-teal-400 text-white hover:text-slate-950
                   border border-white/25 hover:border-teal-300 backdrop-blur-xl shadow-2xl
                   transition-all duration-300 hover:scale-110 active:scale-95 cursor-pointer
                   focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400 group"
            aria-label="{{ __('Previous slide') }}"
        >
            <i class="fa-solid fa-chevron-left text-base sm:text-lg group-hover:-translate-x-0.5 transition-transform duration-200"></i>
        </button>

        {{-- Next Arrow (Right) --}}
        <button
            type="button"
            id="hero-next-btn"
            onclick="window.__heroSlider && window.__heroSlider.next()"
            class="hero-side-btn hero-side-next absolute right-3 sm:right-6 md:right-8 top-1/2 -translate-y-1/2 z-40
                   w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center
                   bg-slate-950/70 hover:bg-teal-400 text-white hover:text-slate-950
                   border border-white/25 hover:border-teal-300 backdrop-blur-xl shadow-2xl
                   transition-all duration-300 hover:scale-110 active:scale-95 cursor-pointer
                   focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400 group"
            aria-label="{{ __('Next slide') }}"
        >
            <i class="fa-solid fa-chevron-right text-base sm:text-lg group-hover:translate-x-0.5 transition-transform duration-200"></i>
        </button>
    @endif

    {{-- ── 2. ELEVATED SLIDE INDICATORS & PROGRESS BAR ─────────────── --}}
    @if($totalSlides > 1)
        <div class="absolute bottom-5 sm:bottom-7 md:bottom-8 left-0 right-0 z-35
                    max-w-7xl mx-auto px-6 sm:px-10 lg:px-16
                    flex items-center justify-between pointer-events-none">
            
            {{-- Slide Counter + Smooth Progress Line --}}
            <div class="flex items-center gap-3 font-mono text-sm font-bold text-slate-300 pointer-events-auto
                        bg-slate-950/60 backdrop-blur-md px-4 py-2 rounded-full border border-white/15 shadow-lg">
                <span id="hero-active-num" class="text-teal-400 text-base sm:text-lg font-extrabold tabular-nums">01</span>
                <div class="w-20 sm:w-36 h-1 bg-white/20 rounded-full relative overflow-hidden">
                    <div id="hero-progress-fill"
                         class="absolute inset-0 bg-teal-400 rounded-full transition-none w-full origin-left rtl:origin-right"
                         style="transform: scaleX(0); will-change: transform;">
                    </div>
                </div>
                <span class="text-slate-400">{{ str_pad($totalSlides, 2, '0', STR_PAD_LEFT) }}</span>
            </div>

            {{-- Interactive Dot Navigation --}}
            <div id="hero-dots" class="flex items-center gap-2 pointer-events-auto bg-slate-950/60 backdrop-blur-md px-3.5 py-2.5 rounded-full border border-white/15 shadow-lg" role="tablist">
                @for ($i = 0; $i < $totalSlides; $i++)
                    <button
                        type="button"
                        id="hero-dot-{{ $i }}"
                        role="tab"
                        aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                        onclick="window.__heroSlider && window.__heroSlider.goTo({{ $i }})"
                        class="hero-dot h-2.5 rounded-full transition-all duration-300 focus:outline-none cursor-pointer
                               {{ $i === 0 ? 'bg-teal-400 w-8' : 'bg-white/40 w-2.5 hover:bg-white/80' }}"
                        aria-label="{{ __('Go to slide') }} {{ $i + 1 }}"
                    ></button>
                @endfor
            </div>

        </div>
    @endif

</section>

{{-- ═══════════════════════════════════════════════════════════════════
     HERO SLIDER ENGINE + SMOOTH CROSS-FADE STYLES
     ════════════════════════════════════════════════════════════════ --}}
<style>
/* ── Container dimensions & structure ────────────────────────── */
#hero-slider-section {
    display: block !important;
    width: 100% !important;
    min-height: 580px !important;
    height: 88vh !important;
    max-height: 940px !important;
    position: relative !important;
    overflow: hidden !important;
    background-color: #020617 !important;
    user-select: none;
    -webkit-user-select: none;
}
#hero-slider-section.is-dragging {
    cursor: grabbing !important;
}
@media (min-width: 1024px) {
    #hero-slider-section {
        min-height: 640px !important;
        height: 90vh !important;
        max-height: 960px !important;
    }
}
#hero-track {
    position: relative !important;
    width: 100% !important;
    height: 100% !important;
    min-height: inherit !important;
}

/* ── Side Swapper Arrow Styling ──────────────────────────────────── */
.hero-side-btn {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6), 0 0 15px rgba(20, 184, 166, 0.15);
}
.hero-side-btn:hover {
    box-shadow: 0 15px 35px -5px rgba(20, 184, 166, 0.5), 0 0 25px rgba(20, 184, 166, 0.4);
}

/* ── Cinematic Ken-Burns zoom animation ──────────────────────────── */
@keyframes heroKenBurns {
    0%   { transform: scale(1.05) translate(0,    0);    }
    50%  { transform: scale(1.12) translate(-1%, -1%);   }
    100% { transform: scale(1.05) translate(0,    0);    }
}
.hero-bg-img.kb-active {
    animation: heroKenBurns 8s ease-in-out forwards;
}

/* ── Stagger entrance animations ─────────────────────────────────── */
@keyframes heroFadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0);    }
}
.hero-slide .hero-anim-badge,
.hero-slide .hero-anim-title,
.hero-slide .hero-anim-sub,
.hero-slide .hero-anim-btns {
    opacity: 1;
    transform: translateY(0);
}
.hero-slide.is-entering .hero-anim-badge,
.hero-slide.is-entering .hero-anim-title,
.hero-slide.is-entering .hero-anim-sub,
.hero-slide.is-entering .hero-anim-btns {
    opacity: 0;
    transform: translateY(22px);
}
.hero-slide.is-active .hero-anim-badge {
    animation: heroFadeUp 0.5s cubic-bezier(.22,1,.36,1) 0.1s both;
}
.hero-slide.is-active .hero-anim-title {
    animation: heroFadeUp 0.6s cubic-bezier(.22,1,.36,1) 0.2s both;
}
.hero-slide.is-active .hero-anim-sub {
    animation: heroFadeUp 0.6s cubic-bezier(.22,1,.36,1) 0.35s both;
}
.hero-slide.is-active .hero-anim-btns {
    animation: heroFadeUp 0.55s cubic-bezier(.22,1,.36,1) 0.48s both;
}

/* ── Ultra-Smooth Cross-Fade Slide transitions ───────────────────── */
.hero-slide {
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.75s cubic-bezier(.4, 0, .2, 1), visibility 0.75s;
    z-index: 0;
    pointer-events: none;
}
.hero-slide.is-entering {
    opacity: 0;
    visibility: visible;
    z-index: 11;
}
.hero-slide.is-active {
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 10 !important;
    pointer-events: auto !important;
}
.hero-slide.is-leaving {
    opacity: 0 !important;
    visibility: visible !important;
    z-index: 5 !important;
    pointer-events: none !important;
}
.hero-slide.is-hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    z-index: 0 !important;
    pointer-events: none !important;
}
</style>

<script>
(function () {
    'use strict';

    const TOTAL      = {{ $totalSlides }};
    const AUTOPLAY   = {{ $autoplayMs }};
    let   current    = 0;
    let   timer      = null;
    let   progTimer  = null;
    let   progStart  = null;
    let   paused     = false;
    let   isTransitioning = false;

    // DOM refs
    const getSlides = () => document.querySelectorAll('.hero-slide');
    const getDots   = () => document.querySelectorAll('.hero-dot');
    const getNumEl  = () => document.getElementById('hero-active-num');
    const getFillEl = () => document.getElementById('hero-progress-fill');

    // ── Smooth Cross-Fade State Handler ───────────────────────────
    function setSlide(idx) {
        if (isTransitioning || TOTAL <= 1) return;
        const all = getSlides();
        if (!all.length) return;

        const nextIdx = (idx + TOTAL) % TOTAL;
        if (nextIdx === current) return;

        isTransitioning = true;
        const prev = current;
        current = nextIdx;

        // Update ARIA
        all[prev].setAttribute('aria-hidden', 'true');
        all[current].setAttribute('aria-hidden', 'false');

        // Setup entering & leaving classes
        all[prev].classList.remove('is-active');
        all[prev].classList.add('is-leaving');

        all[current].classList.remove('is-hidden', 'is-leaving');
        all[current].classList.add('is-entering');

        // Trigger next frame for browser CSS transition
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                all[current].classList.remove('is-entering');
                all[current].classList.add('is-active');
                startKenBurns(all[current]);
                resetElementAnimations(all[current]);
            });
        });

        // Cleanup after cross-fade completes (750ms)
        setTimeout(() => {
            all[prev].classList.remove('is-leaving');
            all[prev].classList.add('is-hidden');
            isTransitioning = false;
        }, 750);

        updateDots(current);
        updateCounter(current);
        resetProgress();
        startProgress();
    }

    function resetElementAnimations(slide) {
        const animated = slide.querySelectorAll('.hero-anim-badge,.hero-anim-title,.hero-anim-sub,.hero-anim-btns');
        animated.forEach(el => {
            el.style.animation = 'none';
        });
        requestAnimationFrame(() => {
            animated.forEach(el => {
                el.style.animation = '';
            });
        });
    }

    function startKenBurns(slide) {
        const img = slide.querySelector('.hero-bg-img');
        if (!img) return;
        img.classList.remove('kb-active');
        requestAnimationFrame(() => {
            img.classList.add('kb-active');
        });
    }

    // ── Dot Nav Indicators ─────────────────────────────────────────
    function updateDots(idx) {
        getDots().forEach((dot, i) => {
            if (i === idx) {
                dot.classList.add('bg-teal-400', 'w-8');
                dot.classList.remove('bg-white/40', 'w-2.5', 'hover:bg-white/80');
                dot.setAttribute('aria-selected', 'true');
            } else {
                dot.classList.remove('bg-teal-400', 'w-8');
                dot.classList.add('bg-white/40', 'w-2.5', 'hover:bg-white/80');
                dot.setAttribute('aria-selected', 'false');
            }
        });
    }

    // ── Numeric Counter ────────────────────────────────────────────
    function updateCounter(idx) {
        const el = getNumEl();
        if (el) el.textContent = String(idx + 1).padStart(2, '0');
    }

    // ── Countdown Progress Bar (Composited GPU Transform) ───────────
    function resetProgress() {
        cancelAnimationFrame(progTimer);
        const fill = getFillEl();
        if (fill) fill.style.transform = 'scaleX(0)';
        progStart = null;
    }

    function startProgress() {
        if (paused || TOTAL <= 1) return;
        progStart = performance.now();

        function tick(now) {
            if (paused || !progStart) return;
            const elapsed = now - progStart;
            const scale = Math.min(elapsed / AUTOPLAY, 1);
            const fill = getFillEl();
            if (fill) fill.style.transform = `scaleX(${scale})`;
            if (scale < 1) {
                progTimer = requestAnimationFrame(tick);
            }
        }
        progTimer = requestAnimationFrame(tick);
    }

    // ── Autoplay ──────────────────────────────────────────────────
    function startAutoplay() {
        clearInterval(timer);
        if (TOTAL <= 1) return;
        timer = setInterval(() => {
            if (!paused && !isTransitioning) setSlide(current + 1);
        }, AUTOPLAY);
    }

    function stopAutoplay() {
        clearInterval(timer);
    }

    // ── Public Global API ─────────────────────────────────────────
    window.__heroSlider = {
        next: () => { resetProgress(); setSlide(current + 1); startAutoplay(); },
        prev: () => { resetProgress(); setSlide(current - 1); startAutoplay(); },
        goTo: (i) => { resetProgress(); setSlide(i); startAutoplay(); },
        pause: () => { paused = true; stopAutoplay(); cancelAnimationFrame(progTimer); },
        play: () => { paused = false; startAutoplay(); startProgress(); },
    };

    // ── Interactive Mouse Drag & Touch Swipe Swapper ──────────────
    let startX = 0;
    let startY = 0;
    let isPointerDown = false;
    let hasDragged = false;

    function handleStart(clientX, clientY) {
        startX = clientX;
        startY = clientY;
        isPointerDown = true;
        hasDragged = false;
    }

    function handleMove(clientX, clientY, section) {
        if (!isPointerDown) return;
        const dx = clientX - startX;
        const dy = clientY - startY;

        if (Math.abs(dx) > 10) {
            hasDragged = true;
            section.classList.add('is-dragging');
        }
    }

    function handleEnd(clientX, clientY, section) {
        if (!isPointerDown) return;
        isPointerDown = false;
        section.classList.remove('is-dragging');

        const dx = clientX - startX;
        const dy = clientY - startY;

        // If swipe horizontal threshold (40px) met & horizontal > vertical
        if (Math.abs(dx) >= 40 && Math.abs(dx) > Math.abs(dy)) {
            if (dx < 0) {
                window.__heroSlider.next();
            } else {
                window.__heroSlider.prev();
            }
        }
        
        setTimeout(() => { hasDragged = false; }, 100);
    }

    // ── Keyboard Navigation ───────────────────────────────────────
    function onKeyDown(e) {
        if (e.key === 'ArrowRight') window.__heroSlider.next();
        if (e.key === 'ArrowLeft')  window.__heroSlider.prev();
    }

    // ── Init Engine ───────────────────────────────────────────────
    function initSlider() {
        const section = document.getElementById('hero-slider-section');
        if (!section) return;

        const all = getSlides();
        if (!all.length) return;

        // Boot initial active slide
        all.forEach((s, i) => {
            if (i === 0) {
                s.classList.remove('opacity-0', 'is-hidden', 'is-leaving', 'is-entering');
                s.classList.add('is-active');
            } else {
                s.classList.remove('is-active', 'is-entering', 'is-leaving');
                s.classList.add('is-hidden');
            }
        });
        startKenBurns(all[0]);
        updateCounter(0);

        // ── Mouse Drag Events (Swapper) ──
        section.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return; // Only primary button
            handleStart(e.clientX, e.clientY);
        });

        window.addEventListener('mousemove', (e) => {
            handleMove(e.clientX, e.clientY, section);
        });

        window.addEventListener('mouseup', (e) => {
            if (isPointerDown) handleEnd(e.clientX, e.clientY, section);
        });

        // Prevent accidental link clicks while dragging
        section.addEventListener('click', (e) => {
            if (hasDragged) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        // ── Touch Events (Mobile/Tablet Swipe) ──
        section.addEventListener('touchstart', (e) => {
            handleStart(e.changedTouches[0].clientX, e.changedTouches[0].clientY);
        }, { passive: true });

        section.addEventListener('touchmove', (e) => {
            handleMove(e.changedTouches[0].clientX, e.changedTouches[0].clientY, section);
        }, { passive: true });

        section.addEventListener('touchend', (e) => {
            handleEnd(e.changedTouches[0].clientX, e.changedTouches[0].clientY, section);
        }, { passive: true });

        // ── Keyboard ──
        document.addEventListener('keydown', onKeyDown);

        // ── Pause on Hover ──
        section.addEventListener('mouseenter', () => window.__heroSlider.pause());
        section.addEventListener('mouseleave', () => window.__heroSlider.play());

        // ── Autoplay Start ──
        startAutoplay();
        startProgress();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider);
    } else {
        initSlider();
    }
})();
</script>
