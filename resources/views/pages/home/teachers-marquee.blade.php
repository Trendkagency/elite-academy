{{-- Featured Mentors Showcase Section (Section: Deep Dark Background with Interactive Swiper) --}}
@php
    $isAr = app()->getLocale() === 'ar';
    $mentors = \Illuminate\Support\Facades\Cache::remember('home_featured_mentors_list', 600, function () {
        try {
            $query = \App\Models\TeacherProfile::with(['user', 'subjects', 'media'])
                ->where('is_public', true)
                ->orderByDesc('is_featured')
                ->take(12);

            $dbTeachers = $query->get();
            if ($dbTeachers->isEmpty()) {
                $dbTeachers = \App\Models\TeacherProfile::with(['user', 'subjects', 'media'])->take(8)->get();
            }

            if ($dbTeachers->isNotEmpty()) {
                return $dbTeachers->map(fn($t) => [
                    'name' => $t->user?->name ?: 'Teacher Profile',
                    'title' => $t->specialization ?: ($t->title ?: 'Senior Academic Mentor'),
                    'dept' => $t->subjects?->first()?->name ?: 'Faculty',
                    'badgeBg' => 'bg-teal-600',
                    'textColor' => 'group-hover:text-teal-300',
                    'meta' => ($t->years_experience ?: 5) . '+ Yrs Exp • Active Educator',
                    'photo' => $t->photo_url ?: 'images/instructor_portrait.webp',
                    'slug' => $t->slug ?: $t->id,
                ])->all();
            }
        } catch (\Throwable $e) {}

        return [
            ['name' => 'Dr. Ahmed Hassan', 'title' => 'Senior AI & Systems Researcher', 'dept' => 'Programming', 'badgeBg' => 'bg-teal-600', 'textColor' => 'group-hover:text-teal-300', 'meta' => '15+ Yrs Exp • 1,400+ Students • PhD - MIT', 'photo' => 'images/instructor_portrait.webp', 'slug' => 'dr-ahmed-hassan'],
            ['name' => 'Sarah Mohamed', 'title' => 'Deep Learning Lead Architect', 'dept' => 'Artificial Intelligence', 'badgeBg' => 'bg-purple-600', 'textColor' => 'group-hover:text-purple-300', 'meta' => '12+ Yrs Exp • 1,100+ Students • MSc - Stanford', 'photo' => 'images/instructor_female.webp', 'slug' => 'sarah-mohamed'],
            ['name' => 'Omar Khaled', 'title' => 'Robotics & Autonomous Systems Specialist', 'dept' => 'Robotics', 'badgeBg' => 'bg-orange-600', 'textColor' => 'group-hover:text-orange-300', 'meta' => '10+ Yrs Exp • 950+ Students • PhD - Cambridge', 'photo' => 'images/instructor_male.webp', 'slug' => 'omar-khaled'],
        ];
    });
@endphp

<section id="teachers-showcase-section" class="py-16 md:py-24 bg-slate-950 text-white relative overflow-hidden border-y border-slate-800/80 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">

        {{-- Featured Mentors Header with Navigation Controls --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 pb-2 border-b border-slate-800/80">
            <div class="space-y-3">
                <span class="anim-projects delay-1 inline-block text-xs font-mono uppercase tracking-widest text-teal-300 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-500/40 shadow-sm">
                    {{ __('FACULTY') }}
                </span>
                <h2 class="anim-projects delay-2 font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ \App\Models\SiteSetting::getLocalized('teachers_title', __('Meet Our Featured Mentors.')) }}
                </h2>
            </div>

            {{-- Slider Controls --}}
            <div class="flex items-center gap-4 shrink-0">
                <a href="{{ route('teachers') }}" class="text-xs font-mono font-bold text-teal-400 hover:text-teal-300 transition-colors hidden sm:inline">
                    {{ $isAr ? '← تصفح كافة المعلمين' : 'Browse All Faculty →' }}
                </a>
                <div class="flex items-center gap-2.5">
                    <button type="button" onclick="scrollTeachersSlider(-1)" class="w-11 h-11 rounded-2xl bg-slate-900/90 hover:bg-teal-600 text-slate-200 hover:text-white border border-slate-700/80 hover:border-teal-500 shadow-lg flex items-center justify-center text-base transition-all duration-300 active:scale-90 cursor-pointer touch-press" aria-label="Previous Teacher Slide">
                        {{ $isAr ? '→' : '←' }}
                    </button>
                    <button type="button" onclick="scrollTeachersSlider(1)" class="w-11 h-11 rounded-2xl bg-slate-900/90 hover:bg-teal-600 text-slate-200 hover:text-white border border-slate-700/80 hover:border-teal-500 shadow-lg flex items-center justify-center text-base transition-all duration-300 active:scale-90 cursor-pointer touch-press" aria-label="Next Teacher Slide">
                        {{ $isAr ? '←' : '→' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Swiper Track Container --}}
        <div id="teachersSwiperTrack" class="flex items-center gap-6 overflow-x-auto py-4 snap-x snap-mandatory scroll-smooth no-scrollbar select-none cursor-grab active:cursor-grabbing" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach ($mentors as $index => $m)
                @php
                    $profileUrl = !empty($m['slug']) ? route('teacher-profile', ['slug' => $m['slug']]) : route('teacher-profile');
                @endphp
                <div class="teacher-slide-card w-[300px] sm:w-[380px] lg:w-[420px] shrink-0 h-[440px] rounded-3xl overflow-hidden shadow-2xl border border-slate-800/90 relative group card-lift transition-all duration-500 snap-center bg-slate-900" data-slide-index="{{ $index }}">
                    <img src="{{ media_url($m['photo'], 'images/instructor_portrait.webp') }}" alt="{{ $m['name'] }}" width="420" height="440" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy" decoding="async">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent pointer-events-none"></div>

                    {{-- Dept Badge --}}
                    <span class="absolute top-5 left-5 text-xs font-mono font-extrabold text-white {{ $m['badgeBg'] }} px-3.5 py-1.5 rounded-full shadow-lg z-10 border border-white/20">
                        {{ $m['dept'] }}
                    </span>

                    {{-- Teacher Card Footer Information --}}
                    <div class="absolute bottom-6 left-6 right-6 text-white space-y-3 z-10">
                        <div>
                            <h3 class="font-heading font-black text-2xl text-white {{ $m['textColor'] }} transition-colors line-clamp-1">
                                {{ $m['name'] }}
                            </h3>
                            <p class="text-xs font-mono text-slate-300 font-semibold mt-0.5 line-clamp-1">{{ $m['title'] }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-white/15 text-xs font-medium text-slate-300">
                            <span class="text-[11px] font-mono text-slate-400">{{ $m['meta'] }}</span>
                            <a href="{{ $profileUrl }}" class="btn-lift px-3.5 py-1.5 bg-teal-500/20 hover:bg-teal-500 text-teal-300 hover:text-slate-950 font-extrabold rounded-xl border border-teal-500/40 hover:border-teal-400 text-xs flex items-center gap-1.5 transition-all shadow-md" aria-label="View faculty profile for {{ $m['name'] }}">
                                <span>{{ __('View Profile') }}</span>
                                <span class="group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Interactive Pagination Dots Indicator --}}
        <div id="teachersSwiperDots" class="flex items-center justify-center gap-2 pt-2">
            @foreach ($mentors as $index => $m)
                <button type="button" onclick="scrollTeachersToSlide({{ $index }})" class="teacher-dot h-2.5 rounded-full transition-all duration-300 cursor-pointer {{ $index === 0 ? 'bg-teal-400 w-8' : 'bg-slate-700 hover:bg-slate-500 w-2.5' }}" aria-label="Go to teacher slide {{ $index + 1 }}"></button>
            @endforeach
        </div>

    </div>

    {{-- Subtle Ambient Background Glows --}}
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
</section>

{{-- Interactive Swiper JavaScript Engine --}}
<script>
(function() {
    let track = null;
    let dots = [];
    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;
    let autoPlayTimer = null;

    function initTeachersSwiper() {
        track = document.getElementById('teachersSwiperTrack');
        dots = Array.from(document.querySelectorAll('#teachersSwiperDots .teacher-dot'));
        if (!track) return;

        // Active Dot Observer on Scroll
        track.addEventListener('scroll', () => {
            updateActiveDot();
        }, { passive: true });

        // Mouse Drag to Swipe
        track.addEventListener('mousedown', (e) => {
            isDown = true;
            track.classList.add('cursor-grabbing');
            track.classList.remove('cursor-grab');
            startX = e.pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
            stopAutoPlay();
        });

        track.addEventListener('mouseleave', () => {
            isDown = false;
            track.classList.remove('cursor-grabbing');
            track.classList.add('cursor-grab');
            startAutoPlay();
        });

        track.addEventListener('mouseup', () => {
            isDown = false;
            track.classList.remove('cursor-grabbing');
            track.classList.add('cursor-grab');
            startAutoPlay();
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        });

        // Hover pause
        track.addEventListener('mouseenter', stopAutoPlay, { passive: true });
        track.addEventListener('mouseleave', startAutoPlay, { passive: true });

        startAutoPlay();
    }

    function updateActiveDot() {
        if (!track || !dots.length) return;
        const cards = Array.from(track.querySelectorAll('.teacher-slide-card'));
        if (!cards.length) return;

        const trackCenter = track.getBoundingClientRect().left + track.offsetWidth / 2;
        let closestIndex = 0;
        let minDistance = Infinity;

        cards.forEach((card, idx) => {
            const rect = card.getBoundingClientRect();
            const cardCenter = rect.left + rect.width / 2;
            const dist = Math.abs(cardCenter - trackCenter);
            if (dist < minDistance) {
                minDistance = dist;
                closestIndex = idx;
            }
        });

        dots.forEach((dot, idx) => {
            if (idx === closestIndex) {
                dot.className = 'teacher-dot h-2.5 rounded-full transition-all duration-300 cursor-pointer bg-teal-400 w-8';
            } else {
                dot.className = 'teacher-dot h-2.5 rounded-full transition-all duration-300 cursor-pointer bg-slate-700 hover:bg-slate-500 w-2.5';
            }
        });
    }

    window.scrollTeachersSlider = function(direction) {
        if (!track) track = document.getElementById('teachersSwiperTrack');
        if (!track) return;
        const card = track.querySelector('.teacher-slide-card');
        const scrollAmount = card ? card.offsetWidth + 24 : 380;
        
        const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
        const scrollDelta = isRtl ? (direction * -scrollAmount) : (direction * scrollAmount);
        
        track.scrollBy({ left: scrollDelta, behavior: 'smooth' });
    };

    window.scrollTeachersToSlide = function(index) {
        if (!track) track = document.getElementById('teachersSwiperTrack');
        if (!track) return;
        const cards = track.querySelectorAll('.teacher-slide-card');
        if (cards[index]) {
            cards[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    };

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(() => {
            if (!track) return;
            const maxScroll = track.scrollWidth - track.clientWidth;
            const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
            
            if (Math.abs(track.scrollLeft) >= maxScroll - 20) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                window.scrollTeachersSlider(1);
            }
        }, 5000);
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTeachersSwiper);
    } else {
        initTeachersSwiper();
    }
})();
</script>
