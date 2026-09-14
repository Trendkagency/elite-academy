{{-- Testimonials Section (Section: Light Background with Infinite Slow Swapper) --}}
@php
    $isAr = app()->getLocale() === 'ar';
    $dbTestimonials = \Illuminate\Support\Facades\Schema::hasTable('testimonials')
        ? \App\Models\Testimonial::where('is_featured', true)->orderBy('sort_order')->get()
        : collect();

    $testimonials = $dbTestimonials->count() > 0 ? $dbTestimonials->map(fn($t) => [
        'quote' => '"' . $t->getLocalizedContent() . '"',
        'photo' => $t->avatar ?: 'images/instructor_portrait.webp',
        'name' => $t->name,
        'course' => $t->getLocalizedCourseName() ?: __('Elite Academic Track'),
        'badge' => $t->is_verified ? '<i class="fa-solid fa-check"></i> ' . ($isAr ? 'طالب موثق' : 'Verified Student') : ($isAr ? 'عضو موثق' : 'Verified Member'),
        'quoteColor' => 'group-hover:text-teal-600',
        'nameColor' => 'group-hover:text-teal-600',
        'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
    ])->all() : [
        [
            'quote' => '"أكاديمية إيليت ساعدتني جداً في استيعاب الفيزياء والكهربية ومتابعة المدرس أولاً بأول."',
            'photo' => 'images/hero_male_student.webp',
            'name' => 'أحمد الفاروق',
            'course' => 'كورس الفيزياء الكهربية والمغناطيسية',
            'badge' => '<i class="fa-solid fa-check"></i> Verified Student',
            'quoteColor' => 'group-hover:text-teal-600',
            'nameColor' => 'group-hover:text-teal-600',
            'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
        ],
        [
            'quote' => '"منصة ممتازة تمكنت من خلال بوابة ولي الأمر متابعة حضور ابنتي مريم ودرجات الواجبات بانتظام."',
            'photo' => 'images/instructor_portrait.webp',
            'name' => 'د. خالد عبد المجيد',
            'course' => 'بوابة متابعة ولي الأمر',
            'badge' => '<i class="fa-solid fa-check"></i> Verified Parent',
            'quoteColor' => 'group-hover:text-purple-600',
            'nameColor' => 'group-hover:text-purple-600',
            'badgeBg' => 'bg-purple-50 text-purple-700 border-purple-200/80',
        ],
        [
            'quote' => '"حصص التفاعلية واختبارات البرمجة التفاعلية ممتازة جداً ومفيدة لبناء المستقبل."',
            'photo' => 'images/instructor_female.webp',
            'name' => 'مريم السعيد',
            'course' => 'كورس البرمجة والذكاء الاصطناعي',
            'badge' => '<i class="fa-solid fa-check"></i> Verified Student',
            'quoteColor' => 'group-hover:text-teal-600',
            'nameColor' => 'group-hover:text-teal-600',
            'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
        ],
        [
            'quote' => '"The structured live labs and step-by-step assignment solver gave me real problem-solving confidence."',
            'photo' => 'images/hero_student.webp',
            'name' => 'Kareem El-Sayed',
            'course' => 'Computer Science & AI Track',
            'badge' => '<i class="fa-solid fa-check"></i> Verified Student',
            'quoteColor' => 'group-hover:text-orange-600',
            'nameColor' => 'group-hover:text-orange-600',
            'badgeBg' => 'bg-orange-50 text-orange-700 border-orange-200/80',
        ],
    ];

    // Ensure we have at least 6 items for smooth endless infinite scroll
    $displayCards = $testimonials;
    while (count($displayCards) < 8) {
        $displayCards = array_merge($displayCards, $testimonials);
    }
@endphp

<section id="testimonials-infinite-section" class="py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-[#080B11] dark:via-[#0B0F19] dark:to-[#080B11] text-slate-900 dark:text-slate-100 relative overflow-hidden border-b border-slate-200/80 dark:border-slate-800/80 transition-colors duration-300 select-none">
    {{-- Ambient Luxury Background Mesh --}}
    <div class="absolute top-1/4 left-1/4 w-[38rem] h-[22rem] bg-teal-500/10 dark:bg-teal-500/15 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse" style="animation-duration: 8s;"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[34rem] h-[20rem] bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse" style="animation-duration: 10s;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-slate-200/70 dark:border-slate-800/80">
            <div class="space-y-3.5 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold tracking-wider uppercase bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-500/20 backdrop-blur-md shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>{{ __('TESTIMONIALS & REVIEWS') }}</span>
                </div>
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.15]">
                    {{ \App\Models\SiteSetting::getLocalized('testimonials_title', __('What Our Students & Parents Say')) }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                    {{ $isAr ? 'قصص نجاح حقيقية وتجارب ملهمة من طلابنا وأولياء الأمور عبر منصة إيليت التعليمية.' : 'Real success stories and experiences from our students and parents thriving across Elite Academy.' }}
                </p>
            </div>

            <div class="flex items-center gap-4 shrink-0">
                <span class="text-xs font-mono text-slate-500 dark:text-slate-400 font-semibold hidden sm:inline-flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-hand-pointer text-teal-500 text-[11px] animate-bounce"></i>
                    {{ $isAr ? 'اسحب بالماوس للتصفح' : 'Drag or swipe to browse' }}
                </span>
                <div class="flex items-center gap-2.5">
                    <button
                        type="button"
                        id="testim-btn-prev"
                        class="w-12 h-12 rounded-2xl bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border border-slate-200/90 dark:border-slate-700/90 shadow-md flex items-center justify-center text-slate-700 dark:text-slate-200 hover:text-white hover:bg-teal-600 dark:hover:bg-teal-500 hover:border-teal-600 hover:shadow-xl hover:shadow-teal-500/20 transition-all duration-300 active:scale-95 cursor-pointer focus:outline-none"
                        aria-label="Previous Reviews"
                    >
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button
                        type="button"
                        id="testim-btn-next"
                        class="w-12 h-12 rounded-2xl bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border border-slate-200/90 dark:border-slate-700/90 shadow-md flex items-center justify-center text-slate-700 dark:text-slate-200 hover:text-white hover:bg-teal-600 dark:hover:bg-teal-500 hover:border-teal-600 hover:shadow-xl hover:shadow-teal-500/20 transition-all duration-300 active:scale-95 cursor-pointer focus:outline-none"
                        aria-label="Next Reviews"
                    >
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /max-w-7xl --}}

    {{-- Infinite Smooth Drag/Swipe Swapper Carousel Track --}}
    <div class="relative w-full overflow-hidden mt-6 py-8">
        {{-- Side Gradient Fades --}}
        <div class="absolute left-0 inset-y-0 w-16 sm:w-32 bg-gradient-to-r from-slate-50 via-slate-50/80 dark:from-[#080B11] dark:via-[#080B11]/80 to-transparent z-20 pointer-events-none"></div>
        <div class="absolute right-0 inset-y-0 w-16 sm:w-32 bg-gradient-to-l from-slate-50 via-slate-50/80 dark:from-[#080B11] dark:via-[#080B11]/80 to-transparent z-20 pointer-events-none"></div>

        <div
            id="testimonials-marquee-viewport"
            class="testimonials-viewport flex items-center gap-7 overflow-x-hidden py-6 px-8 cursor-grab active:cursor-grabbing select-none"
            style="scroll-behavior: auto; -webkit-overflow-scrolling: touch; width: 100%;"
        >
            <div id="testimonials-marquee-track" class="flex items-center gap-7 shrink-0">
                @foreach ($displayCards as $idx => $t)
                    <div class="testim-card relative w-[360px] sm:w-[420px] shrink-0 min-h-[310px] bg-white dark:bg-slate-900/95 backdrop-blur-xl rounded-[28px] p-7 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-[0_12px_36px_-10px_rgba(0,0,0,0.07)] dark:shadow-[0_12px_36px_-10px_rgba(0,0,0,0.5)] hover:shadow-[0_24px_50px_-12px_rgba(20,184,166,0.22)] dark:hover:shadow-[0_24px_50px_-12px_rgba(20,184,166,0.3)] hover:border-teal-500/50 dark:hover:border-teal-400/50 flex flex-col justify-between group transition-all duration-300 hover:-translate-y-2">
                        
                        {{-- Top Accent Gradient Bar --}}
                        <div class="absolute top-0 left-8 right-8 h-1 bg-gradient-to-r from-transparent via-teal-500/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>

                        {{-- Card Header: Rating Pill & Quote Icon --}}
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 dark:bg-amber-400/10 text-amber-600 dark:text-amber-400 rounded-full text-xs font-black font-mono border border-amber-500/20 shadow-xs">
                                <div class="flex items-center gap-0.5 text-[11px] text-amber-400">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="ms-1">5.0</span>
                            </div>

                            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-teal-600 dark:text-teal-400 group-hover:bg-teal-500 group-hover:text-white transition-all duration-300 shadow-xs">
                                <i class="fa-solid fa-quote-right text-sm"></i>
                            </div>
                        </div>

                        {{-- Review Quote Text --}}
                        <p class="font-sans font-normal text-slate-700 dark:text-slate-200 text-[15px] sm:text-[16px] leading-relaxed italic line-clamp-4 my-2 flex-1">
                            {{ $t['quote'] }}
                        </p>

                        {{-- Author Footer --}}
                        <div class="pt-5 mt-3 border-t border-slate-100 dark:border-slate-800/90 flex items-center gap-4">
                            <div class="relative shrink-0">
                                <img
                                    src="{{ media_url($t['photo'], 'images/instructor_portrait_128.webp') }}"
                                    alt="{{ $t['name'] }}"
                                    width="52" height="52"
                                    class="w-13 h-13 rounded-2xl object-cover shadow-md ring-2 ring-slate-200 dark:ring-slate-700 group-hover:ring-teal-500 transition-all duration-300"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full flex items-center justify-center text-[8px] text-white">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                            <div class="space-y-1 min-w-0 flex-1">
                                <h3 class="font-heading font-extrabold text-[15px] text-slate-900 dark:text-white truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                    {{ $t['name'] }}
                                </h3>
                                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 truncate font-semibold">
                                    {{ $t['course'] }}
                                </p>
                                <span class="inline-flex items-center gap-1 {{ $t['badgeBg'] }} text-[10px] font-mono font-extrabold px-2.5 py-0.5 rounded-full border shadow-2xs">
                                    {!! $t['badge'] !!}
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>{{-- /track --}}
        </div>{{-- /viewport --}}
    </div>

</section>

{{-- ═══════════════════════════════════════════════════════════════════
     TESTIMONIALS INFINITE SLOW SCROLLER & MOUSE SWIPER ENGINE
     ════════════════════════════════════════════════════════════════ --}}
<style>
.testimonials-viewport {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.testimonials-viewport::-webkit-scrollbar {
    display: none;
}
.testim-card {
    will-change: transform;
}
</style>

<script>
(function() {
    'use strict';

    function initTestimonialsMarquee() {
        const viewport = document.getElementById('testimonials-marquee-viewport');
        const track    = document.getElementById('testimonials-marquee-track');
        if (!viewport || !track) return;

        // Clone track contents for seamless 100% endless loop
        const clone = track.cloneNode(true);
        clone.id = 'testimonials-marquee-track-clone';
        clone.setAttribute('aria-hidden', 'true');
        viewport.appendChild(clone);

        let speed = 0.65; // Gentle, very slow continuous speed (pixels per frame)
        let isHovered = false;
        let isDragging = false;
        let startX = 0;
        let startScrollLeft = 0;
        let scrollPos = 0;
        let rafId = null;
        let hasMoved = false;
        let isSectionVisible = false;
        let cachedTrackWidth = track.offsetWidth + 24;

        function updateTrackWidth() {
            cachedTrackWidth = track.offsetWidth + 24;
        }
        window.addEventListener('resize', updateTrackWidth, { passive: true });

        function loop() {
            if (!isSectionVisible) {
                rafId = null;
                return;
            }

            if (!isHovered && !isDragging) {
                scrollPos += speed;
                const trackWidth = cachedTrackWidth || 1200;

                if (scrollPos >= trackWidth) {
                    scrollPos -= trackWidth;
                } else if (scrollPos < 0) {
                    scrollPos += trackWidth;
                }

                viewport.scrollLeft = scrollPos;
            } else {
                scrollPos = viewport.scrollLeft;
            }

            rafId = requestAnimationFrame(loop);
        }

        function ensureLoopRunning() {
            if (isSectionVisible && !rafId) {
                rafId = requestAnimationFrame(loop);
            }
        }

        // ── Hover to Pause ──────────────────────────────────────────
        viewport.addEventListener('mouseenter', () => { isHovered = true; }, { passive: true });
        viewport.addEventListener('mouseleave', () => { 
            isHovered = false; 
            isDragging = false;
            viewport.classList.remove('active:cursor-grabbing');
        }, { passive: true });

        // ── Mouse Drag & Touch Swipe Swapper ────────────────────────
        viewport.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return;
            isDragging = true;
            hasMoved = false;
            startX = e.pageX - viewport.offsetLeft;
            startScrollLeft = viewport.scrollLeft;
            viewport.style.cursor = 'grabbing';
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - viewport.offsetLeft;
            const walk = (x - startX) * 1.5; // Drag sensitivity
            if (Math.abs(walk) > 5) hasMoved = true;
            
            let newScroll = startScrollLeft - walk;
            const trackWidth = cachedTrackWidth || 1200;

            if (newScroll >= trackWidth) {
                newScroll -= trackWidth;
                startScrollLeft -= trackWidth;
            } else if (newScroll < 0) {
                newScroll += trackWidth;
                startScrollLeft += trackWidth;
            }

            viewport.scrollLeft = newScroll;
            scrollPos = newScroll;
        });

        window.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                viewport.style.cursor = 'grab';
                setTimeout(() => { hasMoved = false; }, 50);
            }
        });

        // Touch Drag
        viewport.addEventListener('touchstart', (e) => {
            isHovered = true;
            isDragging = true;
            startX = e.touches[0].pageX - viewport.offsetLeft;
            startScrollLeft = viewport.scrollLeft;
        }, { passive: true });

        viewport.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            const x = e.touches[0].pageX - viewport.offsetLeft;
            const walk = (x - startX) * 1.5;
            let newScroll = startScrollLeft - walk;
            const trackWidth = cachedTrackWidth || 1200;

            if (newScroll >= trackWidth) {
                newScroll -= trackWidth;
                startScrollLeft -= trackWidth;
            } else if (newScroll < 0) {
                newScroll += trackWidth;
                startScrollLeft += trackWidth;
            }

            viewport.scrollLeft = newScroll;
            scrollPos = newScroll;
        }, { passive: true });

        viewport.addEventListener('touchend', () => {
            isDragging = false;
            setTimeout(() => { isHovered = false; }, 2000);
        }, { passive: true });

        // Prevent accidental link clicks when dragging
        viewport.addEventListener('click', (e) => {
            if (hasMoved) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        // ── Next / Prev Arrow Buttons ──────────────────────────────
        const btnPrev = document.getElementById('testim-btn-prev');
        const btnNext = document.getElementById('testim-btn-next');
        const cardStep = 420; // single card width + gap

        if (btnPrev) {
            btnPrev.addEventListener('click', () => {
                let target = viewport.scrollLeft - cardStep;
                const trackWidth = cachedTrackWidth || 1200;
                if (target < 0) target += trackWidth;
                viewport.scrollTo({ left: target, behavior: 'smooth' });
                scrollPos = target;
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', () => {
                let target = viewport.scrollLeft + cardStep;
                const trackWidth = cachedTrackWidth || 1200;
                if (target >= trackWidth) target -= trackWidth;
                viewport.scrollTo({ left: target, behavior: 'smooth' });
                scrollPos = target;
            });
        }

        // ── Viewport Observer: ONLY run when visible ──────────────
        const section = document.getElementById('testimonials-infinite-section');
        if ('IntersectionObserver' in window && section) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    isSectionVisible = entry.isIntersecting;
                    if (isSectionVisible) {
                        updateTrackWidth();
                        ensureLoopRunning();
                    } else if (rafId) {
                        cancelAnimationFrame(rafId);
                        rafId = null;
                    }
                });
            }, { rootMargin: '200px 0px' });
            observer.observe(section);
        } else {
            isSectionVisible = true;
            ensureLoopRunning();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTestimonialsMarquee);
    } else {
        initTestimonialsMarquee();
    }
})();
</script>
