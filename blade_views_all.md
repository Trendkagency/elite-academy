# Application Blade Views Catalog

Total Blade Files: 63

## File: `resources/views/components/article-card.blade.php`

```blade
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

```

---

## File: `resources/views/components/breadcrumb.blade.php`

```blade
{{-- Breadcrumb Navigation Component
     @param array $items — [['label' => 'Home', 'route' => 'home'], ['label' => 'Subjects']]
     Last item is rendered as the active breadcrumb (no link).
--}}
<nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-8">
    @foreach ($items as $item)
        @if (! $loop->last)
            @if(isset($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']))
                <a href="{{ route($item['route']) }}" class="hover:text-teal-600 transition-colors">{{ $item['label'] }}</a>
            @elseif(isset($item['url']))
                <a href="{{ $item['url'] }}" class="hover:text-teal-600 transition-colors">{{ $item['label'] }}</a>
            @else
                <span class="hover:text-teal-600 transition-colors">{{ $item['label'] }}</span>
            @endif
            <span>/</span>
        @else
            <span class="text-teal-600 font-bold">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>

```

---

## File: `resources/views/components/course-card.blade.php`

```blade
@php
    $categoryBg = $categoryBg ?? 'bg-teal-600';
    $instructorBorder = $instructorBorder ?? 'border-teal-500';
    $route = $route ?? route('course-details');
    $isEnrolled = $isEnrolled ?? false;
    $hasFreeDemo = $hasFreeDemo ?? true;
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-2xs hover-lift flex flex-col justify-between group">
    <div>
        <div class="relative h-48 overflow-hidden bg-slate-100">
            <img src="{{ media_url($image, 'images/course_ai.png') }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <span class="absolute top-4 left-4 {{ $categoryBg }} text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs">{{ $category }}</span>
            @if($isEnrolled)
                <span class="absolute top-4 right-4 bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>✓</span> {{ $isArabic ? 'مشترك' : 'Enrolled' }}
                </span>
            @elseif($hasFreeDemo)
                <span class="absolute top-4 right-4 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1">
                    <span>▶</span> {{ $isArabic ? 'حصة مجانية' : 'Free Demo' }}
                </span>
            @else
                <span class="absolute top-4 right-4 bg-rose-700 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-xs flex items-center gap-1 shadow-rose-700/20">
                    <span>🔒</span> {{ $isArabic ? 'باقة مطلوب' : 'Package Required' }}
                </span>
            @endif
        </div>
        <div class="p-6 space-y-3">
            <div class="flex items-center gap-2">
                <img src="{{ media_url($instructorPhoto, 'images/instructor_portrait.png') }}" alt="{{ $instructor }}" class="w-7 h-7 rounded-full object-cover border {{ $instructorBorder }}">
                <span class="text-xs font-bold text-slate-900">{{ $instructor }}</span>
            </div>
            <h3 class="font-heading font-bold text-xl text-slate-900 group-hover:text-teal-600 transition-colors">
                <a href="{{ $route }}">{{ $title }}</a>
            </h3>
            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2">{{ $description }}</p>
        </div>
    </div>
    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between mt-4">
        <span class="font-mono font-bold text-lg text-slate-900">{{ $price }}</span>
        <div class="flex items-center gap-2">
            @if($isEnrolled)
                <a href="{{ route('student-portal') }}" class="text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    {{ $isArabic ? 'الانتقال للبوابة ←' : 'Go to Portal →' }}
                </a>
            @else
                @if($hasFreeDemo)
                    <a href="{{ $route }}#demo" class="text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 px-3 py-2 rounded-xl transition-colors border border-orange-200">
                        {{ $isArabic ? 'حصة تجريبية' : 'Free Demo' }}
                    </a>
                @endif
                <a href="{{ $route }}" class="text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 px-4 py-2 rounded-xl transition-colors">
                    {{ $isArabic ? 'تسجيل بالدورة' : 'Enroll' }}
                </a>
            @endif
        </div>
    </div>
</div>

```

---

## File: `resources/views/components/course-countdown-timer.blade.php`

```blade
@props([
    'targetDate' => null,
    'sessionTitle' => null,
    'title' => null,
    'subtitle' => null,
])

@php
    $isArabic = app()->getLocale() === 'ar';
    $title = $title ?: ($isArabic ? 'عداد البث المباشر التفاعلي' : 'Live Session Start Timer');
    $subtitle = $subtitle ?: ($isArabic ? 'الوقت المتبقي لإنطلاق حصة البث المباشر القادمة' : 'Countdown to live stream lecture & interactive Q&A');
    $defaultTarget = now()->addDays(3)->setTime(18, 0)->toIso8601String();
    $targetISO = $targetDate ?: $defaultTarget;
@endphp

<div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950 text-white rounded-3xl p-5 sm:p-6 border border-teal-500/30 shadow-2xl space-y-5 group">
    {{-- Decorative Ambient Glows --}}
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

    {{-- Top Header Badge Row --}}
    <div class="flex items-center justify-between border-b border-teal-500/20 pb-3.5 relative z-10">
        <div class="flex items-center gap-2">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
            </span>
            <span class="font-mono text-[11px] font-extrabold uppercase tracking-widest text-rose-400">
                {{ $isArabic ? 'بث مباشر حي' : 'LIVE TICKER' }}
            </span>
        </div>
        <span class="text-[11px] font-mono bg-teal-950/90 text-teal-300 px-3 py-1 rounded-full border border-teal-500/30 font-bold shadow-xs">
            ⚡ {{ $isArabic ? 'حصّة تفاعلية' : 'Upcoming Session' }}
        </span>
    </div>

    {{-- Title & Session Info --}}
    <div class="space-y-1 relative z-10">
        <h3 class="font-heading font-black text-lg sm:text-xl text-white tracking-tight flex items-center gap-2">
            <span>{{ $title }}</span>
        </h3>
        @if($sessionTitle)
            <p class="text-xs font-bold text-teal-400 flex items-center gap-1.5 pt-0.5">
                <span>📌 {{ $sessionTitle }}</span>
            </p>
        @else
            <p class="text-xs text-slate-300 leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>

    {{-- Countdown Display Grid (4 Equal Compact Columns) --}}
    <div id="countdown-widget-container" class="relative z-10">
        <div id="countdown-widget" class="grid grid-cols-4 gap-2 text-center" data-target="{{ $targetISO }}">
            {{-- Days --}}
            <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-teal-500/25 shadow-inner flex flex-col items-center justify-center space-y-0.5 group-hover:border-teal-400/40 transition-colors">
                <span id="timer-days" class="font-mono font-black text-xl sm:text-2xl text-teal-400 tracking-tight">00</span>
                <span class="text-[9px] font-mono uppercase tracking-wider text-slate-400 font-extrabold">
                    {{ $isArabic ? 'يوم' : 'DAYS' }}
                </span>
            </div>

            {{-- Hours --}}
            <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-amber-500/25 shadow-inner flex flex-col items-center justify-center space-y-0.5 group-hover:border-amber-400/40 transition-colors">
                <span id="timer-hours" class="font-mono font-black text-xl sm:text-2xl text-amber-400 tracking-tight">00</span>
                <span class="text-[9px] font-mono uppercase tracking-wider text-slate-400 font-extrabold">
                    {{ $isArabic ? 'ساعة' : 'HOURS' }}
                </span>
            </div>

            {{-- Mins --}}
            <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-teal-500/25 shadow-inner flex flex-col items-center justify-center space-y-0.5 group-hover:border-teal-400/40 transition-colors">
                <span id="timer-mins" class="font-mono font-black text-xl sm:text-2xl text-cyan-400 tracking-tight">00</span>
                <span class="text-[9px] font-mono uppercase tracking-wider text-slate-400 font-extrabold">
                    {{ $isArabic ? 'دقيقة' : 'MINS' }}
                </span>
            </div>

            {{-- Secs --}}
            <div class="bg-slate-900/90 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-rose-500/25 shadow-inner flex flex-col items-center justify-center space-y-0.5 group-hover:border-rose-400/40 transition-colors">
                <span id="timer-secs" class="font-mono font-black text-xl sm:text-2xl text-rose-400 tracking-tight animate-pulse">00</span>
                <span class="text-[9px] font-mono uppercase tracking-wider text-slate-400 font-extrabold">
                    {{ $isArabic ? 'ثانية' : 'SECS' }}
                </span>
            </div>
        </div>

        {{-- Live State Alert Box (Hidden when counting down, shown when diff <= 0) --}}
        <div id="countdown-live-alert" class="hidden text-center py-4 px-4 bg-emerald-500/20 border border-emerald-500/40 rounded-2xl space-y-2">
            <div class="inline-flex items-center gap-2 text-emerald-400 font-bold text-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span>{{ $isArabic ? 'البث المباشر يعمل الآن! 🔴' : 'LIVE STREAM IS ACTIVE NOW! 🔴' }}</span>
            </div>
            <p class="text-xs text-slate-200 font-medium">
                {{ $isArabic ? 'انقر أدناه للانضمام فوراً إلى القاعة التفاعلية.' : 'Click below to join the interactive live session immediately.' }}
            </p>
        </div>
    </div>

    {{-- Progress Bar Indicator --}}
    <div class="space-y-1.5 pt-1 relative z-10">
        <div class="flex items-center justify-between text-[10px] font-mono text-slate-400 font-bold">
            <span>{{ $isArabic ? 'مؤشر الوقت المتبقي' : 'Time Remaining Ratio' }}</span>
            <span id="timer-progress-pct" class="text-teal-400">100%</span>
        </div>
        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden p-0.5">
            <div id="timer-progress-bar" class="h-full bg-gradient-to-r from-teal-500 via-cyan-400 to-amber-400 rounded-full transition-all duration-1000" style="width: 100%;"></div>
        </div>
    </div>
</div>

<script>
(function() {
    function initCountdown() {
        const widget = document.getElementById('countdown-widget');
        if (!widget) return;

        const targetStr = widget.getAttribute('data-target');
        if (!targetStr) return;

        const targetTime = new Date(targetStr).getTime();
        const startTime = new Date().getTime(); // Snapshot for progress bar calculation
        const totalDuration = Math.max(1, targetTime - startTime);

        const daysEl = document.getElementById('timer-days');
        const hoursEl = document.getElementById('timer-hours');
        const minsEl = document.getElementById('timer-mins');
        const secsEl = document.getElementById('timer-secs');
        const progressBar = document.getElementById('timer-progress-bar');
        const progressPct = document.getElementById('timer-progress-pct');
        const liveAlert = document.getElementById('countdown-live-alert');

        function update() {
            const now = new Date().getTime();
            const diff = targetTime - now;

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minsEl) minsEl.textContent = '00';
                if (secsEl) secsEl.textContent = '00';
                if (progressBar) progressBar.style.width = '0%';
                if (progressPct) progressPct.textContent = '0%';
                if (liveAlert) {
                    liveAlert.classList.remove('hidden');
                    widget.classList.add('hidden');
                }
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            if (daysEl) daysEl.textContent = d < 10 ? '0' + d : d;
            if (hoursEl) hoursEl.textContent = h < 10 ? '0' + h : h;
            if (minsEl) minsEl.textContent = m < 10 ? '0' + m : m;
            if (secsEl) secsEl.textContent = s < 10 ? '0' + s : s;

            // Progress bar
            const remainingRatio = Math.max(0, Math.min(1, diff / totalDuration));
            const pctVal = Math.round(remainingRatio * 100);
            if (progressBar) progressBar.style.width = pctVal + '%';
            if (progressPct) progressPct.textContent = pctVal + '%';
        }

        update();
        setInterval(update, 1000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountdown);
    } else {
        initCountdown();
    }
})();
</script>

```

---

## File: `resources/views/components/curriculum-timeline.blade.php`

```blade
@props([
    'sessions' => [],
    'hasFreeDemo' => true,
    'title' => 'Curriculum Lifetime Progression Line',
    'subtitle' => 'Step-by-step roadmap of live sessions, coding labs, and homework assignments',
])

@php
    $totalSessionsCount = is_countable($sessions) ? count($sessions) : 0;
    $perPage = 4;
@endphp

<div x-data="{ timelinePage: 1, perPage: {{ $perPage }}, total: {{ $totalSessionsCount }} }" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-700 font-extrabold bg-teal-50 px-3 py-1 rounded-full border border-teal-200">
                MODULE TIMELINE ROADMAP
            </span>
            <h3 class="font-heading font-black text-2xl text-slate-900 mt-1">{{ $title }}</h3>
            <p class="text-xs font-mono text-slate-500 mt-0.5">{{ $subtitle }}</p>
        </div>
        <span class="hidden sm:inline-block text-xs font-mono font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
            {{ $totalSessionsCount }} Milestones
        </span>
    </div>

    {{-- Lifetime Timeline Flow --}}
    <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-gradient-to-b before:from-teal-500 before:via-orange-400 before:to-slate-300">
        @if(isset($sessions) && count($sessions) > 0)
            @foreach($sessions as $idx => $s)
                @php
                    $isFree = (bool) $hasFreeDemo && ($s->is_free_demo ?? ($idx === 0));
                    $hasAssignment = $s->assignments && $s->assignments->count() > 0;
                    $statusColor = $isFree ? 'bg-teal-500 text-white ring-4 ring-teal-100' : ($idx === 1 ? 'bg-orange-500 text-white ring-4 ring-orange-100' : 'bg-slate-300 text-slate-700');
                    
                    if ($isFree) {
                        $badgeText = app()->getLocale() === 'ar' ? 'مفتوح / حصة مجانية ✓' : 'Unlocked / Free Demo ✓';
                        $badgeBg = 'bg-teal-100 text-teal-800 border-teal-200';
                    } elseif ($idx === 1) {
                        $badgeText = app()->getLocale() === 'ar' ? 'الحصة الحالية ⏳' : 'Current Session ⏳';
                        $badgeBg = 'bg-orange-100 text-orange-800 border-orange-200';
                    } else {
                        $badgeText = app()->getLocale() === 'ar' ? 'مغلق / يلزم الاشتراك 🔒' : 'Locked / Package Required 🔒';
                        $badgeBg = 'bg-slate-100 text-slate-600 border-slate-200';
                    }
                @endphp
                <div x-show="timelinePage === Math.ceil(({{ $idx }} + 1) / perPage)" class="relative group space-y-2 transition-all duration-300">
                    {{-- Timeline Bullet Node --}}
                    <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full {{ $statusColor }} font-mono font-extrabold text-xs flex items-center justify-center shadow-md">
                        {{ $idx + 1 }}
                    </div>

                    <div class="bg-slate-50 hover:bg-teal-50/40 transition-colors p-5 rounded-2xl border border-slate-200/80 space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h4 class="font-bold text-base text-slate-900 group-hover:text-teal-700 transition-colors">
                                Session {{ $s->sort_order ?: ($idx + 1) }}: {{ $s->title }}
                            </h4>
                            <span class="text-[11px] font-mono font-extrabold px-3 py-0.5 rounded-full border {{ $badgeBg }}">
                                {{ $badgeText }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-sans">
                            {{ $s->description ?: 'Interactive lecture, hands-on coding exercises, and graded homework.' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-slate-200/60 text-[11px] font-mono text-slate-500">
                            <span>⏱️ {{ $s->duration_minutes ?: 60 }} Mins Duration</span>
                            @if($isFree)
                                <span class="text-emerald-700 font-bold">▶ {{ app()->getLocale() === 'ar' ? 'حصة تجريبية متوفرة' : 'Free Sample Included' }}</span>
                            @else
                                <span class="text-rose-700 font-bold">🔒 {{ app()->getLocale() === 'ar' ? 'باستخدام باقة الاشتراك' : 'Subscription Required' }}</span>
                            @endif
                            @if($hasAssignment)
                                <span class="text-rose-700 font-bold">📝 {{ app()->getLocale() === 'ar' ? 'الواجب إجباري' : 'Homework Mandatory' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Default Timeline Fallback Milestones --}}
            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full {{ $hasFreeDemo ? 'bg-teal-500 text-white ring-4 ring-teal-100' : 'bg-slate-400 text-white ring-4 ring-slate-200' }} font-mono font-bold text-xs flex items-center justify-center shadow-md">
                    1
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 1: Orientation & Foundations</h4>
                        @if($hasFreeDemo)
                            <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-teal-100 text-teal-800 border border-teal-200">
                                {{ app()->getLocale() === 'ar' ? 'مفتوح / حصة مجانية ✓' : 'Unlocked / Free Demo ✓' }}
                            </span>
                        @else
                            <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                {{ app()->getLocale() === 'ar' ? 'مغلق / يلزم الاشتراك 🔒' : 'Locked / Package Required 🔒' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Introduction to core principles, environment setup, and baseline diagnostic quiz.
                    </p>
                </div>
            </div>

            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full bg-orange-500 text-white font-mono font-bold text-xs flex items-center justify-center ring-4 ring-orange-100 shadow-md">
                    2
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 2: Advanced Core Architecture</h4>
                        <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-orange-100 text-orange-800 border border-orange-200">
                            {{ app()->getLocale() === 'ar' ? 'الحصة الحالية ⏳' : 'Current In Progress ⏳' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        In-depth lectures, live problem solving, and mandatory homework assignments.
                    </p>
                </div>
            </div>

            <div class="relative group space-y-2">
                <div class="absolute -left-9 sm:-left-11 top-0.5 w-6 h-6 rounded-full bg-slate-300 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shadow-md">
                    3
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="font-bold text-base text-slate-900">Module 3: Final Certification & Capstone</h4>
                        <span class="text-[11px] font-mono font-bold px-3 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                            {{ app()->getLocale() === 'ar' ? 'قريباً 🔒' : 'Upcoming 🔒' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Comprehensive exam review, final project defense, and accredited certification.
                    </p>
                </div>
            </div>
        @endif
    </div>

    @if(isset($sessions) && count($sessions) > 4)
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs font-mono">
            <button @click="if (timelinePage > 1) timelinePage--" :disabled="timelinePage <= 1" class="btn-lift px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
            </button>
            <span class="text-slate-600 font-bold bg-slate-100 px-3.5 py-1.5 rounded-xl border border-slate-200">
                {{ app()->getLocale() === 'ar' ? 'صفحة' : 'Page' }} <span class="text-teal-700 font-black" x-text="timelinePage">1</span> {{ app()->getLocale() === 'ar' ? 'من' : 'of' }} <span x-text="Math.ceil(total / perPage)">1</span>
            </span>
            <button @click="if (timelinePage < Math.ceil(total / perPage)) timelinePage++" :disabled="timelinePage >= Math.ceil(total / perPage)" class="btn-lift px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
            </button>
        </div>
    @endif
</div>


```

---

## File: `resources/views/components/dynamic-counter.blade.php`

```blade
@props([
    'count' => '100',
    'prefix' => '',
    'suffix' => '',
    'label' => '',
    'description' => '',
    'color' => 'teal',
    'duration' => 2000,
])

@php
    $colorClass = match($color) {
        'orange' => 'text-orange-500',
        'emerald' => 'text-emerald-500',
        'indigo' => 'text-indigo-500',
        default => 'text-teal-600',
    };

    // Extract numeric portion for counting animation
    preg_match('/[\d\.\,]+/', $count, $matches);
    $numericStr = $matches[0] ?? '0';
    $targetNum = (float) str_replace(',', '', $numericStr);
    $isDecimal = str_contains($numericStr, '.');
@endphp

<div x-data="{
        current: 0,
        target: {{ $targetNum }},
        isAnimated: false,
        initObserver() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.isAnimated) {
                        this.startCount();
                        this.isAnimated = true;
                    }
                });
            }, { threshold: 0.2 });
            observer.observe(this.$el);
        },
        startCount() {
            let startTime = null;
            const duration = {{ $duration }};
            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3); // Ease-Out Cubic
                this.current = this.target * easeProgress;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    this.current = this.target;
                }
            };
            window.requestAnimationFrame(step);
        }
    }"
    x-init="initObserver()"
    class="anim-hero space-y-1 p-3 text-center group rounded-2xl hover:bg-white/40 transition-all duration-300">

    <p class="font-mono font-extrabold text-3xl sm:text-4xl {{ $colorClass }} group-hover:scale-105 transition-transform duration-300">
        <span>{{ $prefix }}</span><span x-text="{{ $isDecimal ? 'current.toFixed(1)' : 'Math.floor(current).toLocaleString()' }}">{{ $numericStr }}</span><span>{{ $suffix }}</span>
    </p>

    @if($label)
        <p class="text-xs sm:text-sm font-bold text-slate-700">{{ $label }}</p>
    @endif

    @if($description)
        <p class="text-[11px] text-slate-500 leading-tight max-w-[180px] mx-auto">{{ $description }}</p>
    @endif
</div>

```

---

## File: `resources/views/components/faq-item.blade.php`

```blade
{{-- FAQ Accordion Item Component with Schema.org Microdata for AI Search Engine Crawlers
     @param string $question — The FAQ question text
     @param string $answer — The FAQ answer text
--}}
<details class="group bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-teal-500/40 transition-all" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
    <summary class="flex justify-between items-center font-heading font-bold text-slate-900 cursor-pointer list-none select-none">
        <span itemprop="name" class="text-base sm:text-lg pr-4">{{ $question }}</span>
        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xl shrink-0 group-open:bg-teal-600 group-open:text-white transition-all duration-300">
            <span class="group-open:rotate-45 transition-transform duration-300">+</span>
        </div>
    </summary>
    <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" class="mt-4 pt-3 border-t border-slate-100 text-sm text-slate-600 leading-relaxed space-y-2">
        <p itemprop="text">{{ $answer }}</p>
    </div>
</details>

```

---

## File: `resources/views/components/filter-chip.blade.php`

```blade
{{-- Filter Chip Component
     @param string $label — Chip text
     @param bool $active — Whether chip is currently active (default false)
--}}
@php $active = $active ?? false; @endphp

<button @class([
    'px-4 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer',
    'bg-teal-600 text-white shadow-xs' => $active,
    'bg-white text-slate-700 border border-slate-200 hover:border-teal-500' => ! $active,
])>{{ $label }}</button>

```

---

## File: `resources/views/components/landing-page-renderer.blade.php`

```blade
@props([
    'sections' => [],
])

@php
    $locale = app()->getLocale();
    $sectionsMap = [
        'hero-slider' => 'pages.home.hero-slider',
        'stats-overlay' => 'pages.home.stats-overlay',
        'why-choose' => 'pages.home.why-choose',
        'about-preview' => 'pages.home.about-preview',
        'subjects-grid' => 'pages.home.subjects-grid',
        'teachers-marquee' => 'pages.home.teachers-marquee',
        'testimonials' => 'pages.home.testimonials',
        'cta_section' => 'pages.home.cta-section',
    ];
@endphp

<div class="landing-dynamic-wrapper space-y-2">
    @foreach($sections as $sec)
        @php
            $isEnabled = $sec['is_enabled'] ?? true;
            if (!$isEnabled) continue;

            $secKey = $sec['section_key'] ?? $sec['key'] ?? '';
            $secType = $sec['type'] ?? '';
        @endphp

        @if(isset($sectionsMap[$secKey]))
            @include($sectionsMap[$secKey], ['sectionData' => $sec])
        @elseif($secType === 'counters' && !empty($sec['counters']))
            <section class="relative z-30 -mt-10 md:-mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/92 backdrop-blur-md border border-white/80 rounded-3xl shadow-2xl shadow-slate-900/10 p-6 md:p-8">
                    <div class="grid grid-cols-2 md:grid-cols-{{ min(count($sec['counters']), 5) }} gap-6 text-center divide-x-0 md:divide-x divide-slate-200/60">
                        @foreach($sec['counters'] as $counter)
                            @php
                                $counterModel = new \App\Models\LandingPageCounter($counter);
                                $val = $counterModel->getComputedValue();
                                $label = ($locale === 'ar' ? ($counter['label_ar'] ?? null) : null) ?: ($counter['label_en'] ?? '');
                                $desc = ($locale === 'ar' ? ($counter['description_ar'] ?? null) : null) ?: ($counter['description_en'] ?? '');
                            @endphp
                            <x-dynamic-counter
                                :count="$val"
                                :label="$label"
                                :description="$desc"
                                :color="$counter['color'] ?? 'teal'"
                            />
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            {{-- Custom 3D Card / Dynamic Fallback Section --}}
            @php
                $title = ($locale === 'ar' ? ($sec['title_ar'] ?? null) : null) ?: ($sec['title_en'] ?? '');
                $subtitle = ($locale === 'ar' ? ($sec['subtitle_ar'] ?? null) : null) ?: ($sec['subtitle_en'] ?? '');
                $badge = ($locale === 'ar' ? ($sec['badge_ar'] ?? null) : null) ?: ($sec['badge_en'] ?? '');
                $img = $sec['image_url'] ?? null;
            @endphp
            @if($title || $subtitle)
                <section class="py-12 px-4 max-w-7xl mx-auto">
                    <div class="relative group p-8 sm:p-12 rounded-3xl bg-slate-900/90 text-white backdrop-blur-xl border border-white/20 shadow-2xl overflow-hidden transition-all duration-500 hover:scale-[1.01]">
                        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="space-y-4">
                                @if($badge)
                                    <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-400/30 uppercase tracking-widest">
                                        {{ $badge }}
                                    </span>
                                @endif
                                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                                    {{ $title }}
                                </h2>
                                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                                    {{ $subtitle }}
                                </p>
                            </div>
                            @if($img)
                                <div class="flex justify-center">
                                    <img src="{{ asset($img) }}" alt="{{ $title }}" class="max-h-64 object-contain rounded-2xl shadow-lg group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        @endif
    @endforeach
</div>

```

---

## File: `resources/views/components/meeting-container.blade.php`

```blade
@props(['session', 'user'])

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

<div id="inSystemMeetingContainer" class="relative w-full max-w-6xl mx-auto bg-slate-950 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden text-white font-sans transition-all duration-300">
    
    {{-- Meeting Header Bar --}}
    <div class="px-6 py-4 bg-slate-900/90 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4 backdrop-blur-md relative z-30">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                🎓
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span id="meetingStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span id="meetingStatusText">{{ $isRtl ? 'جاري الاتصال...' : 'CONNECTING...' }}</span>
                    </span>
                    <span class="text-xs font-mono text-slate-400">
                        ID: {{ 'SES-' . str_pad((string) $session->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <h3 class="font-heading font-bold text-base sm:text-lg text-white tracking-tight mt-0.5">
                    {{ $session->title ?: ($isRtl ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream Session') }}
                </h3>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs font-mono">
            <div class="hidden sm:flex flex-col items-end text-slate-300">
                <span>👨‍🏫 {{ $session->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</span>
                <span class="text-[11px] text-slate-400">📚 {{ $session->subject?->name ?: 'Physics' }}</span>
            </div>
            <div class="px-3.5 py-1.5 bg-slate-800/80 rounded-xl border border-slate-700/70 text-teal-300 font-bold flex items-center gap-2">
                <span>⏱️</span>
                <span id="meetingDurationTimer">00:00:00</span>
            </div>
            <button onclick="leaveMeetingSession()" class="btn-lift px-4 py-2 bg-rose-600/80 hover:bg-rose-600 text-white rounded-xl font-bold cursor-pointer transition-all flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                <span>🚪</span> {{ $isRtl ? 'مغادرة الحصة' : 'Leave Session' }}
            </button>
        </div>
    </div>

    {{-- Main Embed Area & Video Wrapper --}}
    <div class="relative w-full aspect-video min-h-[420px] max-h-[680px] bg-slate-950 flex items-center justify-center overflow-hidden select-none">
        
        {{-- Floating Security Dynamic Watermark Layer --}}
        <div id="dynamicWatermark" class="absolute inset-0 pointer-events-none z-30 overflow-hidden flex items-center justify-center opacity-70 select-none">
            <div id="watermarkContent" class="text-teal-300 font-mono font-black text-xs sm:text-sm tracking-widest uppercase transition-all duration-1000 transform -rotate-12 bg-slate-950/80 px-4 py-2 rounded-xl border border-teal-500/50 shadow-2xl backdrop-blur-md">
                🛡️ {{ $user->name }} • {{ 'STU-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT) }} • {{ 'SES-' . str_pad((string) $session->id, 5, '0', STR_PAD_LEFT) }} • IP: {{ request()->ip() }} • <span id="watermarkClock"></span>
            </div>
        </div>

        {{-- Anti-Piracy Screen Recording & Capture Security Shield --}}
        <div id="screenRecordSecurityOverlay" class="hidden absolute inset-0 z-50 bg-slate-950/95 backdrop-blur-3xl flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-3xl shadow-xl animate-bounce">
                🔒
            </div>
            <div class="space-y-1 max-w-md">
                <h4 id="securityBlurTitle" class="font-heading font-bold text-lg text-white">
                    {{ $isRtl ? 'محتوى محمي — تم رصد محاولة تسجيل أو تصوير الشاشة' : 'Protected Content — Screen Recording/Capture Detected' }}
                </h4>
                <p id="securityBlurMsg" class="text-xs font-mono text-slate-400">
                    {{ $isRtl ? 'لحماية الملكية الفكرية، تم تعتيم الشاشة وحظر العرض فوراً. يتم تسجيل بيانات الطالب والعنوان الرقمي (IP) لدى الإدارة.' : 'To protect IP rights, playback is obfuscated. Security events are audited with your identity.' }}
                </p>
            </div>
            <button onclick="resumeMeetingPlayback()" class="mt-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-500 px-6 py-2.5 rounded-xl shadow-lg transition-all cursor-pointer font-mono">
                {{ $isRtl ? 'العودة لمتابعة الحصة المباشرة ▶' : 'Resume Session Playback ▶' }}
            </button>
        </div>

        {{-- Loading & State Overlays --}}
        <div id="meetingStateOverlay" class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div id="meetingSpinner" class="w-14 h-14 border-4 border-teal-500/30 border-t-teal-400 rounded-full animate-spin"></div>
            <div class="space-y-1">
                <h4 id="overlayTitle" class="font-heading font-extrabold text-xl text-white">
                    {{ $isRtl ? 'جاري تجهيز غرفة الاجتماع الأكاديمي المباشر...' : 'Preparing Secure In-System Live Session...' }}
                </h4>
                <p id="overlayMessage" class="text-xs font-mono text-slate-400 max-w-md">
                    {{ $isRtl ? 'يتم التحقق من هوية الطالب، رصيد الحصص، والواجبات المطلوبة...' : 'Verifying enrollment, timing rules, and security tokens...' }}
                </p>
            </div>
            <button id="btnRetryMeeting" onclick="initializeInSystemMeeting()" class="hidden px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold font-mono transition-all shadow-lg">
                🔄 {{ $isRtl ? 'إعادة المحاولة' : 'Retry Connection' }}
            </button>
        </div>

        {{-- Embedded Stream Frame --}}
        <iframe id="embeddedMeetingFrame" src="about:blank" class="w-full h-full border-0 relative z-10 hidden" allow="camera *; microphone *; display-capture *; autoplay *; encrypted-media *; fullscreen *" allowfullscreen></iframe>

        {{-- External Platform Launcher Container (For Google Meet / Teams) --}}
        <div id="externalMeetingLauncher" class="hidden w-full h-full flex flex-col items-center justify-center p-6 text-center space-y-5 bg-slate-950 relative z-10">
            <div class="w-16 h-16 rounded-3xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-3xl shadow-xl shadow-teal-500/10">
                📹
            </div>
            <div class="space-y-1.5 max-w-lg">
                <h4 id="externalPlatformTitle" class="font-heading font-extrabold text-xl sm:text-2xl text-white">
                    {{ $isRtl ? 'غرفة البث المباشر — Google Meet' : 'Interactive Live Stream Room' }}
                </h4>
                <p class="text-xs font-mono text-slate-400 leading-relaxed">
                    {{ $isRtl ? 'تنبيه الأمان والسرية: يتم تسجيل الحضور والغياب ومدة التواجد تلقائياً في خلفية هذا التبويب. يرجى إبقاء هذه الصفحة مفتوحة.' : 'Attendance & security duration are recorded automatically. Keep this tab open.' }}
                </p>
            </div>
            <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <a id="btnExternalLaunch" href="#" target="_blank" rel="noopener noreferrer" class="btn-lift px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 rounded-2xl font-heading font-black text-sm shadow-xl shadow-emerald-500/20 flex items-center gap-2">
                    <span>🟢</span> <span id="externalLaunchBtnText">{{ $isRtl ? 'انضم للبث عبر Google Meet 🚀' : 'Join via Google Meet 🚀' }}</span>
                </a>
            </div>
        </div>

        {{-- Zoom SDK Container (Rendered when Zoom Web SDK is used) --}}
        <div id="zoomMeetingContainer" class="w-full h-full relative z-10 hidden"></div>
    </div>

    {{-- Security Alert Banner --}}
    <div id="securityAlertBanner" class="hidden px-6 py-3 bg-amber-950/80 border-t border-amber-800/80 text-amber-200 text-xs font-mono flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>⚠️</span>
            <span id="securityAlertText">{{ $isRtl ? 'تنبيه: يرجى إبقاء نافذة الحصة نشطة ومفتوحة دائماً للحفاظ على الحضور.' : 'Warning: Please keep the session tab active to record attendance.' }}</span>
        </div>
        <button onclick="document.getElementById('securityAlertBanner').classList.add('hidden')" class="text-amber-400 hover:text-white font-bold cursor-pointer">✕</button>
    </div>
</div>

<script>
    let meetingSessionId = {{ $session->id }};
    let meetingAccessToken = null;
    let meetingTokenExpiresAt = null;
    let heartbeatInterval = null;
    let watermarkInterval = null;
    let durationTimerInterval = null;
    let sessionSecondsCount = 0;

    document.addEventListener('DOMContentLoaded', function () {
        initializeInSystemMeeting();

        // Anti-Tamper & Security Detection Event Listeners
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // 1. Intercept Screen Capture & Screen Recording Shortcuts
        window.addEventListener('keydown', function (e) {
            if (
                e.key === 'PrintScreen' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'S' || e.key === 's')) ||
                (e.metaKey && e.shiftKey && (e.key === '4' || e.key === '5' || e.key === '3')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S' || e.key === 'p' || e.key === 'P')) ||
                e.key === 'F12'
            ) {
                e.preventDefault();
                e.stopPropagation();
                if (navigator.clipboard) navigator.clipboard.writeText('');
                logSecurityEvent('SCREEN_CAPTURE_KEY_BLOCKED', { key: e.key });
                triggerSecurityBlur('{{ $isRtl ? "تم رصد محاولة تصوير أو تسجيل الشاشة! المحتوى محمي بحقوق الملكية الفكرية." : "Screen capture/recording attempt blocked! Content is IP protected." }}');
                return false;
            }
        });

        // 2. Intercept Display Capture API (getDisplayMedia)
        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            const origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
            navigator.mediaDevices.getDisplayMedia = function() {
                logSecurityEvent('DISPLAY_MEDIA_REQUESTED', {});
                triggerSecurityBlur('{{ $isRtl ? "تم رصد محاولة تسجيل/مشاركة الشاشة عبر المتصفح! تم حجب البث لحماية المحتوى." : "Display screen recording request detected! Stream obfuscated." }}');
                return origGetDisplayMedia.apply(this, arguments);
            };
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                logSecurityEvent('TAB_HIDDEN', { path: window.location.pathname });
                showSecurityAlert('{{ $isRtl ? "تنبيه: يرجى إبقاء تبويب الحصة نشطاً ومفتوحاً للحفاظ على تسجيل الحضور." : "Warning: Please keep session tab active to record attendance." }}');
            } else {
                logSecurityEvent('TAB_VISIBLE', {});
            }
        });

        window.addEventListener('blur', function () {
            logSecurityEvent('WINDOW_BLURRED', {});
        });

        window.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) {
                logSecurityEvent('FULLSCREEN_EXIT', {});
            }
        });
    });

    function triggerSecurityBlur(msg) {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        const msgEl = document.getElementById('securityBlurMsg');
        if (overlay) {
            if (msgEl && msg) msgEl.innerText = msg;
            overlay.classList.remove('hidden');
        }
    }

    function resumeMeetingPlayback() {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    function initializeInSystemMeeting() {
        const overlay = document.getElementById('meetingStateOverlay');
        const spinner = document.getElementById('meetingSpinner');
        const overlayTitle = document.getElementById('overlayTitle');
        const overlayMessage = document.getElementById('overlayMessage');
        const retryBtn = document.getElementById('btnRetryMeeting');
        const frame = document.getElementById('embeddedMeetingFrame');
        const statusBadge = document.getElementById('meetingStatusBadge');
        const statusText = document.getElementById('meetingStatusText');

        overlay.classList.remove('hidden');
        spinner.classList.remove('hidden');
        retryBtn.classList.add('hidden');
        overlayTitle.innerText = '{{ $isRtl ? "جاري الاتصال بالغرفة المباشرة..." : "Connecting to Live Session..." }}';

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                meetingAccessToken = res.body.access_token;
                meetingTokenExpiresAt = res.body.expires_at;

                statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                statusText.innerText = '{{ $isRtl ? "مباشر LIVE" : "LIVE SESSION" }}';

                const supportsEmbed = res.body.supports_embedding ?? true;
                const streamUrl = res.body.stream_url || res.body.join_url;
                const provider = res.body.provider || '';

                const isExternalMeeting = (!supportsEmbed) || (streamUrl && (streamUrl.includes('meet.google.com') || streamUrl.includes('teams.microsoft.com')));
                const embeddableUrl = getEmbeddableUrl(streamUrl);

                if (isExternalMeeting && streamUrl) {
                    const launcher = document.getElementById('externalMeetingLauncher');
                    const launchBtn = document.getElementById('btnExternalLaunch');
                    const launchBtnText = document.getElementById('externalLaunchBtnText');
                    const platformTitle = document.getElementById('externalPlatformTitle');

                    if (launchBtn) launchBtn.href = streamUrl;

                    if (platformTitle) {
                        platformTitle.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '{{ $isRtl ? "غرفة البث المباشر التفاعلي — Google Meet" : "Interactive Live Stream — Google Meet" }}'
                            : '{{ $isRtl ? "غرفة البث المباشر التفاعلي — Microsoft Teams" : "Interactive Live Stream — Microsoft Teams" }}';
                    }

                    if (launchBtnText) {
                        launchBtnText.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '{{ $isRtl ? "انضم للبث عبر Google Meet 🚀" : "Join via Google Meet 🚀" }}'
                            : '{{ $isRtl ? "انضم للبث عبر Microsoft Teams 🚀" : "Join via Microsoft Teams 🚀" }}';
                    }

                    if (launcher) launcher.classList.remove('hidden');
                } else if (embeddableUrl) {
                    frame.src = embeddableUrl;
                    frame.classList.remove('hidden');
                }

                overlay.classList.add('hidden');

                startWatermarkAnimation();
                startHeartbeatLoop();
                startDurationTimer();
            } else {
                spinner.classList.add('hidden');
                retryBtn.classList.remove('hidden');
                statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40';
                statusText.innerText = '{{ $isRtl ? "مغلق / غير متاح" : "UNAVAILABLE" }}';

                overlayTitle.innerText = res.body.message || '{{ $isRtl ? "تعذر الوصول للحصة المباشرة" : "Unable to Join Live Session" }}';
                overlayMessage.innerText = res.body.reason_code ? `Error Code: ${res.body.reason_code}` : '{{ $isRtl ? "تأكد من شروط الموعد، الباقة الفعالة، وتسليم الواجبات." : "Please verify session timing, active package, and prerequisite homework." }}';
            }
        })
        .catch(err => {
            spinner.classList.add('hidden');
            retryBtn.classList.remove('hidden');
            overlayTitle.innerText = '{{ $isRtl ? "خطأ في الاتصال بالخادم" : "Server Connection Failure" }}';
            overlayMessage.innerText = err.message;
        });
    }

    let isHeartbeatPending = false;

    function startHeartbeatLoop() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        heartbeatInterval = setInterval(function () {
            if (!meetingAccessToken || isHeartbeatPending) return;

            isHeartbeatPending = true;
            fetch(`/ajax/sessions/${meetingSessionId}/meeting/heartbeat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    access_token: meetingAccessToken,
                    expires_at: meetingTokenExpiresAt
                })
            })
            .then(res => res.json())
            .then(data => {
                isHeartbeatPending = false;
                if (data.success && data.formatted_duration) {
                    document.getElementById('meetingDurationTimer').innerText = data.formatted_duration;
                } else if (data.session_ended) {
                    alert('{{ $isRtl ? "انتهت هذه الجلسة المباشرة." : "This live session has ended." }}');
                    leaveMeetingSession();
                }
            })
            .catch(err => {
                isHeartbeatPending = false;
                console.error(err);
            });
        }, 30000);
    }

    function startDurationTimer() {
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        durationTimerInterval = setInterval(function () {
            sessionSecondsCount++;
            const hrs = String(Math.floor(sessionSecondsCount / 3600)).padStart(2, '0');
            const mins = String(Math.floor((sessionSecondsCount % 3600) / 60)).padStart(2, '0');
            const secs = String(sessionSecondsCount % 60).padStart(2, '0');
            document.getElementById('meetingDurationTimer').innerText = `${hrs}:${mins}:${secs}`;
        }, 1000);
    }

    function startWatermarkAnimation() {
        const watermark = document.getElementById('watermarkContent');
        const clock = document.getElementById('watermarkClock');

        function updateClock() {
            const now = new Date();
            clock.innerText = now.toLocaleTimeString();
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Move watermark position periodically across container
        if (watermarkInterval) clearInterval(watermarkInterval);
        watermarkInterval = setInterval(function () {
            const topPct = Math.floor(Math.random() * 60) + 20;
            const leftPct = Math.floor(Math.random() * 60) + 20;
            watermark.style.top = topPct + '%';
            watermark.style.left = leftPct + '%';
        }, 8000);
    }

    function logSecurityEvent(eventType, metadata) {
        fetch(`/ajax/sessions/${meetingSessionId}/meeting/security-event`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                event_type: eventType,
                metadata: metadata
            })
        }).catch(console.error);
    }

    function getEmbeddableUrl(rawUrl) {
        if (!rawUrl) return '';

        // 1. YouTube conversion
        const ytMatch = rawUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
        if (ytMatch && ytMatch[1]) {
            return `https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0&modestbranding=1&enablejsapi=1`;
        }

        // 2. Vimeo conversion
        const vimeoMatch = rawUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/\d+\/video\/|video\/|)(\d+)/);
        if (vimeoMatch && vimeoMatch[1]) {
            return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }

        // 3. Zoom Web Client URL conversion (In-page Web Client player)
        if (rawUrl.includes('zoom.us/j/')) {
            const parts = rawUrl.split('zoom.us/j/');
            if (parts[1]) {
                const meetingParts = parts[1].split('?');
                const meetingId = meetingParts[0];
                const query = meetingParts[1] ? '&' + meetingParts[1] : '';
                return `https://zoom.us/wc/${meetingId}/join?prefer=1${query}`;
            }
        }

        return rawUrl;
    }

    function showSecurityAlert(msg) {
        const banner = document.getElementById('securityAlertBanner');
        const text = document.getElementById('securityAlertText');
        if (banner && text) {
            text.innerText = msg;
            banner.classList.remove('hidden');
        }
    }

    function leaveMeetingSession() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        if (watermarkInterval) clearInterval(watermarkInterval);

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/leave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        }).finally(() => {
            window.location.href = '{{ route("student-portal") }}';
        });
    }
</script>

```

---

## File: `resources/views/components/pagination.blade.php`

```blade
@if ($paginator->hasPages())
    @php
        $locale = app()->getLocale();
        $isRtl = $locale === 'ar';
        $elements = $elements ?? [];
    @endphp
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between font-mono text-xs font-bold text-slate-700">
        {{-- Previous Page Link --}}
        <div>
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl cursor-not-allowed border border-slate-200 opacity-60">
                    {!! $isRtl ? 'التالي &rarr;' : '&larr; Previous' !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" data-page="{{ $paginator->currentPage() - 1 }}" class="pagination-link px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200/90 shadow-2xs transition-all card-lift flex items-center gap-1.5">
                    {!! $isRtl ? 'التالي &rarr;' : '&larr; Previous' !!}
                </a>
            @endif
        </div>

        {{-- Page Numbers --}}
        <div class="hidden sm:flex items-center gap-1.5">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-slate-400 font-extrabold">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3.5 py-2 bg-teal-600 text-white rounded-xl font-extrabold border border-teal-600 shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" data-page="{{ $page }}" class="pagination-link px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 rounded-xl border border-slate-200/90 transition-all font-bold">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        <div>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" data-page="{{ $paginator->currentPage() + 1 }}" class="pagination-link px-4 py-2 bg-white hover:bg-slate-50 text-slate-800 rounded-xl border border-slate-200/90 shadow-2xs transition-all card-lift flex items-center gap-1.5">
                    {!! $isRtl ? '&larr; السابق' : 'Next &rarr;' !!}
                </a>
            @else
                <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-xl cursor-not-allowed border border-slate-200 opacity-60">
                    {!! $isRtl ? '&larr; السابق' : 'Next &rarr;' !!}
                </span>
            @endif
        </div>
    </nav>
@endif

```

---

## File: `resources/views/components/section-header.blade.php`

```blade
{{-- Centered Section Header Component
     @param string $badge — Eyebrow badge text (e.g. "OUR SUBJECTS")
     @param string $title — Section title (HTML allowed for highlighting)
     @param string|null $subtitle — Optional subtitle text
     @param string $badgeColor — Badge color scheme: 'teal' (default), 'orange'
     @param bool $centered — Center text (default true)
--}}
@php
    $badgeColor = $badgeColor ?? 'teal';
    $centered = $centered ?? true;
    $badgeClasses = match($badgeColor) {
        'orange' => 'text-orange-600 bg-orange-50 border-orange-100',
        default  => 'text-teal-600 bg-teal-50 border-teal-100',
    };
@endphp

<div @class(['space-y-4', 'text-center' => $centered])>
    <span class="text-xs font-mono uppercase tracking-widest font-bold px-3.5 py-1 rounded-full border {{ $badgeClasses }}">
        {{ $badge }}
    </span>
    <h1 class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
        {!! $title !!}
    </h1>
    @isset($subtitle)
        <p @class(['text-slate-600 text-base sm:text-lg', 'max-w-2xl mx-auto' => $centered])>
            {{ $subtitle }}
        </p>
    @endisset
</div>

```

---

## File: `resources/views/components/secure-video-player.blade.php`

```blade
@props([
    'course' => null,
    'videoData' => null,
    'posterImage' => null,
    'title' => null,
])

@php
    $cId = $course ? $course->id : 1;
    $vData = $videoData ?: ($course ? $course->getVideoEmbedData() : ['type' => 'mp4', 'embed_url' => asset('videos/physics_demo.mp4')]);
    $poster = $posterImage ?: ($course && $course->image ? media_url($course->image, 'images/course_ai.png') : asset('images/course_ai.png'));
    $user = auth()->user();
    $userName = $user ? $user->name : 'Guest Student';
    $userPhone = $user ? ($user->phone ?: 'ID: ' . $user->id) : 'ID: Guest';
    $userIp = request()->ip();
@endphp

<div x-data="secureVideoPlayer({
        courseId: {{ $cId }},
        videoType: '{{ $vData['type'] }}',
        rawEmbedUrl: '{{ $vData['embed_url'] }}',
        userName: '{{ addslashes($userName) }}',
        userPhone: '{{ addslashes($userPhone) }}',
        userIp: '{{ $userIp }}',
        tokenRoute: '{{ route('ajax.secure-video.token', $cId) }}'
    })"
    x-init="initPlayer()"
    @contextmenu.prevent
    class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-950 border border-teal-500/40 shadow-2xl select-none group">

    {{-- Anti-Piracy Blur Shield (Triggers only when tab is hidden) --}}
    <div x-show="isBlurred"
         x-cloak
         class="absolute inset-0 z-50 bg-slate-950/95 backdrop-blur-3xl flex flex-col items-center justify-center gap-4 text-center p-6 transition-all duration-300">
        <div class="w-16 h-16 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/40 flex items-center justify-center text-3xl shadow-xl animate-bounce">
            🔒
        </div>
        <div class="space-y-1 max-w-md">
            <h3 class="font-heading text-lg font-bold text-white">
                {{ app()->getLocale() === 'ar' ? 'محتوى محمي — تم رصد مغادرة التبويب' : 'Protected Content — Tab Switched' }}
            </h3>
            <p class="text-xs text-slate-400">
                {{ app()->getLocale() === 'ar' ? 'لحماية الملكية الفكرية، تم تعتيم الشاشة وإيقاف الشرح أثناء التواجد خارج تبويب الدرس.' : 'To protect intellectual property, video playback is obfuscated when navigating away from the lesson tab.' }}
            </p>
        </div>
        <button @click="resumePlayer()" class="mt-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-6 py-2.5 rounded-xl shadow-lg transition-all cursor-pointer">
            {{ app()->getLocale() === 'ar' ? 'العودة لمتابعة الشرح ▶' : 'Resume Playback ▶' }}
        </button>
    </div>

    {{-- Dynamic Identity Watermark Overlay --}}
    <div x-ref="watermark"
         class="absolute z-40 pointer-events-none select-none px-3 py-1.5 rounded-lg bg-slate-900/70 backdrop-blur-md border border-teal-500/30 text-[10px] font-mono text-teal-300 shadow-xl transition-all duration-1000 flex items-center gap-2"
         :style="watermarkStyle">
        <span class="font-bold text-orange-400">{{ $userName }}</span>
        <span class="opacity-40">|</span>
        <span>{{ $userPhone }}</span>
        <span class="opacity-40">|</span>
        <span>IP: {{ $userIp }}</span>
        <span class="opacity-40">|</span>
        <span x-text="currentTimeStr"></span>
    </div>

    {{-- Video Render Canvas & Custom Controls --}}
    <div class="w-full h-full relative group">
        @if($vData['type'] === 'youtube' || $vData['type'] === 'vimeo')
            <iframe x-ref="iframePlayer"
                    :src="activeStreamUrl"
                    class="w-full h-full border-0 rounded-2xl"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
            </iframe>
        @else
            {{-- Native Video with Custom UI Controls Bar --}}
            <video x-ref="nativePlayer"
                   :src="activeStreamUrl"
                   class="w-full h-full object-cover cursor-pointer"
                   playsinline
                   preload="metadata"
                   disablePictureInPicture
                   oncontextmenu="return false;"
                   poster="{{ $poster }}"
                   @click="togglePlay()"
                   @timeupdate="onTimeUpdate()"
                   @loadedmetadata="onLoadedMetadata()"
                   @ended="isPlaying = false">
                Your browser does not support HTML5 secure video.
            </video>

            {{-- Big Center Play Button Overlay --}}
            <div x-show="!isPlaying"
                 @click="togglePlay()"
                 class="absolute inset-0 z-20 flex items-center justify-center bg-slate-950/40 cursor-pointer group-hover:bg-slate-950/50 transition-all">
                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-orange-500 to-teal-500 text-white flex items-center justify-center text-3xl font-bold shadow-2xl group-hover:scale-110 transition-transform duration-300 ring-8 ring-white/10">
                    ▶
                </div>
            </div>

            {{-- Premium Custom Styled Control Bar (No 3-Dots Download Menu) --}}
            <div class="absolute bottom-0 inset-x-0 z-30 bg-gradient-to-t from-slate-950 via-slate-900/90 to-transparent p-4 flex flex-col gap-2 transition-opacity duration-300 opacity-90 group-hover:opacity-100">
                {{-- Interactive Custom Timeline Scrubber --}}
                <div class="relative w-full flex items-center">
                    <input type="range"
                           min="0"
                           :max="duration || 100"
                           :value="currentTime"
                           @input="seek($event.target.value)"
                           class="w-full h-1.5 bg-slate-700/80 rounded-lg appearance-none cursor-pointer accent-teal-400 focus:outline-none">
                </div>

                {{-- Controls Row --}}
                <div class="flex items-center justify-between text-xs font-mono text-white">
                    <div class="flex items-center gap-3">
                        <button @click="togglePlay()" class="p-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white shadow-md transition-all cursor-pointer font-bold">
                            <span x-text="isPlaying ? '⏸' : '▶'"></span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button @click="toggleMute()" class="text-slate-300 hover:text-white text-sm cursor-pointer">
                                <span x-text="isMuted ? '🔇' : '🔊'"></span>
                            </button>
                            <input type="range" min="0" max="1" step="0.1" :value="volume" @input="setVolume($event.target.value)" class="w-16 h-1 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-teal-400">
                        </div>

                        <span class="text-[11px] text-slate-300 font-bold" x-text="formatTime(currentTime) + ' / ' + formatTime(duration)"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-[10px] bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2 py-0.5 rounded-full font-bold">
                            🛡️ HD Protected Stream
                        </span>
                        <button @click="toggleFullscreen()" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-all cursor-pointer text-sm">
                            ⛶
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function secureVideoPlayer(config) {
    return {
        courseId: config.courseId,
        videoType: config.videoType,
        rawEmbedUrl: config.rawEmbedUrl,
        userName: config.userName,
        userPhone: config.userPhone,
        userIp: config.userIp,
        activeStreamUrl: config.rawEmbedUrl || '{{ asset('videos/appropriate-sharing.mp4') }}',
        isBlurred: false,
        isPlaying: false,
        isMuted: false,
        currentTime: 0,
        duration: 0,
        volume: 1,
        watermarkStyle: 'top: 15%; left: 10%;',
        currentTimeStr: '',
        watermarkInterval: null,
        clockInterval: null,

        initPlayer() {
            this.updateClock();
            this.moveWatermark();

            // 1. Watermark repositioning every 4 seconds
            this.watermarkInterval = setInterval(() => {
                this.moveWatermark();
            }, 4000);

            // 2. Realtime timestamp ticker
            this.clockInterval = setInterval(() => {
                this.updateClock();
            }, 1000);

            // 3. Tab Visibility Protection (Only blur when switching away from tab)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.triggerSecurityBlur();
                } else {
                    this.resumePlayer();
                }
            });

            // 4. Keyboard & Shortcut Shield
            window.addEventListener('keydown', (e) => {
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j')) ||
                    (e.ctrlKey && (e.key === 'U' || e.key === 'u' || e.key === 'S' || e.key === 's')) ||
                    e.key === 'PrintScreen'
                ) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.key === 'PrintScreen') {
                        navigator.clipboard?.writeText('');
                    }
                    return false;
                }
            });
        },

        togglePlay() {
            const player = this.$refs.nativePlayer;
            if (!player) return;

            if (!player.src || player.src === window.location.href || player.src.endsWith('/null')) {
                player.src = this.activeStreamUrl || '{{ asset('videos/appropriate-sharing.mp4') }}';
                player.load();
            }

            if (player.paused) {
                const playPromise = player.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        this.isPlaying = true;
                    }).catch(err => {
                        console.log('Play error, attempting local fallback:', err);
                        player.src = '{{ asset('videos/appropriate-sharing.mp4') }}';
                        player.load();
                        player.play().then(() => {
                            this.isPlaying = true;
                        }).catch(e => console.log('Fallback playback failed:', e));
                    });
                }
            } else {
                player.pause();
                this.isPlaying = false;
            }
        },

        onTimeUpdate() {
            const player = this.$refs.nativePlayer;
            if (player) {
                this.currentTime = player.currentTime;
            }
        },

        onLoadedMetadata() {
            const player = this.$refs.nativePlayer;
            if (player) {
                this.duration = player.duration;
            }
        },

        seek(val) {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.currentTime = val;
                this.currentTime = val;
            }
        },

        toggleMute() {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.muted = !player.muted;
                this.isMuted = player.muted;
            }
        },

        setVolume(val) {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.volume = val;
                this.volume = val;
                player.muted = (val == 0);
                this.isMuted = (val == 0);
            }
        },

        toggleFullscreen() {
            const el = this.$el;
            if (!document.fullscreenElement) {
                el.requestFullscreen?.() || el.webkitRequestFullscreen?.();
            } else {
                document.exitFullscreen?.() || document.webkitExitFullscreen?.();
            }
        },

        formatTime(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        },

        moveWatermark() {
            const top = Math.floor(Math.random() * 65) + 10;
            const left = Math.floor(Math.random() * 55) + 5;
            this.watermarkStyle = `top: ${top}%; left: ${left}%;`;
        },

        updateClock() {
            const now = new Date();
            this.currentTimeStr = now.toTimeString().split(' ')[0];
        },

        triggerSecurityBlur() {
            this.isBlurred = true;
            const native = this.$refs.nativePlayer;
            if (native && !native.paused) {
                native.pause();
                this.isPlaying = false;
            }
        },

        resumePlayer() {
            this.isBlurred = false;
        }
    };
}
</script>
@endpush

```

---

## File: `resources/views/components/subject-card.blade.php`

```blade
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
        <img src="{{ media_url($image, 'images/course_ai.png') }}" loading="lazy" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
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

```

---

## File: `resources/views/components/teacher-card.blade.php`

```blade
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

```

---

## File: `resources/views/errors/403.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('Go to Home');
    if ($user) {
        if ($user->isAdmin()) {
            $dashUrl = url('/admin');
            $dashLabel = __('Go to Admin Panel');
        } elseif ($user->isTeacher()) {
            $dashUrl = route('teacher-portal');
            $dashLabel = __('Go to Teacher Portal');
        } elseif ($user->isParent()) {
            $dashUrl = route('parent-portal');
            $dashLabel = __('Go to Parent Portal');
        } elseif ($user->isStudent()) {
            $dashUrl = route('student-portal');
            $dashLabel = __('Go to Student Portal');
        }
    }
@endphp

<section class="min-h-[80vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        {{-- Glassmorphism Security Badge Card --}}
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="w-24 h-24 mx-auto bg-rose-500/10 text-rose-600 rounded-3xl flex items-center justify-center text-4xl border border-rose-500/20 shadow-inner">
                🔒
            </div>

            <div class="space-y-3">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-rose-100 text-rose-700 border border-rose-200 tracking-wider inline-block">
                    HTTP 403 — {{ __('ACCESS FORBIDDEN') }}
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    {{ __('Access Forbidden') }}
                </h1>
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-md mx-auto">
                    {{ $exception->getMessage() ?: __('You do not have permission to access this page or resource.') }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="window.history.back()" class="btn-lift w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-2xl border border-slate-300 transition-all flex items-center justify-center gap-2">
                    <span>←</span> {{ __('Go Back') }}
                </button>
                <a href="{{ $dashUrl }}" class="btn-lift w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2">
                    <span>📊</span> {{ $dashLabel }}
                </a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            {{ __('Elite Academy Security System — Strict Role-Based Authorization Enforced') }}
        </p>

    </div>

    {{-- Subtle Decorative Ambient Glow --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-rose-500/5 rounded-full blur-3xl pointer-events-none"></div>
</section>
@endsection

```

---

## File: `resources/views/errors/404.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('Back to Home');
    if ($user) {
        if ($user->isAdmin()) {
            $dashUrl = url('/admin');
            $dashLabel = __('Go to Admin Panel');
        } elseif ($user->isTeacher()) {
            $dashUrl = route('teacher-portal');
            $dashLabel = __('Go to Teacher Portal');
        } elseif ($user->isParent()) {
            $dashUrl = route('parent-portal');
            $dashLabel = __('Go to Parent Portal');
        } elseif ($user->isStudent()) {
            $dashUrl = route('student-portal');
            $dashLabel = __('Go to Student Portal');
        }
    }

    $rawMsg = $exception->getMessage();
    $cleanMsg = $rawMsg ? rtrim($rawMsg, '.') : __('Oops! The page or resource you are looking for does not exist, has been moved, or is temporarily unavailable');
@endphp

<style>
    @keyframes floatMascot {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(-3deg); }
    }
    @keyframes tearDrop {
        0%, 100% { opacity: 0.3; transform: translateY(0px) scale(0.9); }
        50% { opacity: 1; transform: translateY(6px) scale(1.1); }
    }
    @keyframes numberPulse {
        0%, 100% { opacity: 0.9; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.04); }
    }

    .anim-mascot {
        animation: floatMascot 4s ease-in-out infinite;
    }
    .anim-tear {
        animation: tearDrop 2.5s ease-in-out infinite;
    }
    .anim-num-pulse {
        animation: numberPulse 3s ease-in-out infinite;
    }
</style>

<section class="min-h-[85vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        {{-- Main Glassmorphic Card Container --}}
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-14 border border-slate-200/90 shadow-2xl space-y-8 relative overflow-hidden">

            {{-- Header Showcase: Elite Academy Logo + Sad Emotional Mascot Badge --}}
            <div class="flex items-center justify-center gap-4 mx-auto py-2">
                {{-- Elite Academy Logo Badge --}}
                <div class="relative w-24 h-24 bg-white rounded-3xl p-3 shadow-xl border border-slate-200/90 flex items-center justify-center anim-mascot">
                    <img src="{{ asset('images/logo.png') }}" alt="Elite Academy" class="w-full h-full object-contain">
                </div>

                {{-- Expressive Sad Emotion Mascot Badge --}}
                <div class="relative w-20 h-20 bg-rose-50/90 rounded-3xl border border-rose-200/90 shadow-lg flex items-center justify-center text-4xl anim-mascot" style="animation-delay: 0.6s;">
                    🥺
                    {{-- Tear Drop Pulse Badge --}}
                    <span class="absolute -bottom-1 -right-1 text-sm anim-tear">💧</span>
                </div>
            </div>

            {{-- 404 Typography & Message --}}
            <div class="space-y-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-rose-50 text-rose-700 border border-rose-200/80 tracking-widest inline-block uppercase">
                        HTTP 404 — {{ __('PAGE NOT FOUND') }}
                    </span>
                </div>

                <h1 class="font-heading text-5xl sm:text-6xl font-black text-slate-900 tracking-tight flex items-center justify-center gap-3 anim-num-pulse">
                    <span class="text-teal-600">4</span>
                    <span class="text-rose-500">0</span>
                    <span class="text-teal-600">4</span>
                </h1>

                <h2 class="font-heading text-xl sm:text-2xl font-bold text-slate-800 flex items-center justify-center gap-2">
                    <span>{{ __('Oh no! Page Not Found') }}</span> 😔
                </h2>

                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-lg mx-auto dir-auto" style="unicode-bidi: plaintext;">
                    {{ $cleanMsg }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ $dashUrl }}" class="btn-lift w-full sm:w-auto px-7 py-3.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2">
                    <span>🏠</span> {{ $dashLabel }}
                </a>

                <button type="button" onclick="window.history.back()" class="btn-lift w-full sm:w-auto px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-2xl border border-slate-300 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    {{ __('Go Back') }}
                </button>
            </div>

            {{-- Quick System Links (Fully Localized) --}}
            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-center gap-4 text-xs font-mono font-bold text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-teal-600 transition-colors">{{ __('Home') }}</a>
                <span>•</span>
                <a href="{{ route('courses') }}" class="hover:text-teal-600 transition-colors">{{ __('Courses') }}</a>
                <span>•</span>
                <a href="{{ route('teachers') }}" class="hover:text-teal-600 transition-colors">{{ __('Teachers') }}</a>
                <span>•</span>
                <a href="{{ route('contact') }}" class="hover:text-teal-600 transition-colors">{{ __('Contact Support') }}</a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            {{ __('Elite Academy Platform — Intelligent Error Handling & Navigation') }}
        </p>
    </div>

    {{-- Background Decorative Ambient Glows --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
</section>
@endsection

```

---

## File: `resources/views/errors/429.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $dashUrl = route('home');
    $dashLabel = __('Go to Home');
    if ($user) {
        if ($user->isAdmin()) {
            $dashUrl = url('/admin');
            $dashLabel = __('Go to Admin Panel');
        } elseif ($user->isTeacher()) {
            $dashUrl = route('teacher-portal');
            $dashLabel = __('Go to Teacher Portal');
        } elseif ($user->isParent()) {
            $dashUrl = route('parent-portal');
            $dashLabel = __('Go to Parent Portal');
        } elseif ($user->isStudent()) {
            $dashUrl = route('student-portal');
            $dashLabel = __('Go to Student Portal');
        }
    }
    $retrySeconds = isset($retryAfter) ? (int)$retryAfter : 60;
@endphp

<section class="min-h-[80vh] flex items-center justify-center py-16 px-4 bg-[#FAFAF9] relative overflow-hidden">
    <div class="max-w-2xl w-full text-center space-y-8 relative z-10">

        {{-- Glassmorphism DDoS Rate Limiting Shield Card --}}
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-8 sm:p-12 border border-amber-200/90 shadow-2xl space-y-6 relative overflow-hidden">
            <div class="w-24 h-24 mx-auto bg-amber-500/10 text-amber-600 rounded-3xl flex items-center justify-center text-4xl border border-amber-500/20 shadow-inner animate-pulse">
                🛡️
            </div>

            <div class="space-y-3">
                <span class="px-4 py-1.5 rounded-full text-xs font-mono font-black bg-amber-100 text-amber-800 border border-amber-200 tracking-wider inline-block">
                    HTTP 429 — {{ __('RATE LIMIT EXCEEDED') }}
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    {{ __('Too Many Requests') }}
                </h1>
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed max-w-md mx-auto">
                    {{ (isset($exception) && $exception->getMessage()) ? $exception->getMessage() : __('You have made too many requests in a short period. System rate limiting is active to protect server performance and mitigate DDoS attacks.') }}
                </p>
            </div>

            {{-- Countdown Widget --}}
            <div class="bg-amber-50/80 rounded-2xl p-4 border border-amber-200/70 max-w-xs mx-auto space-y-1">
                <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">{{ __('Retry Available In') }}</p>
                <div class="text-2xl font-mono font-black text-amber-600 flex items-center justify-center gap-1">
                    <span id="rate-limit-timer">{{ $retrySeconds }}</span>
                    <span class="text-xs font-sans font-bold text-amber-800">{{ __('seconds') }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button type="button" onclick="window.location.reload()" class="btn-lift w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold rounded-2xl border border-slate-300 transition-all flex items-center justify-center gap-2">
                    <span>🔄</span> {{ __('Refresh Page') }}
                </button>
                <a href="{{ $dashUrl }}" class="btn-lift w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-600/30 transition-all flex items-center justify-center gap-2">
                    <span>📊</span> {{ $dashLabel }}
                </a>
            </div>
        </div>

        <p class="text-xs font-mono text-slate-400">
            {{ __('Elite Academy DDoS Protection — RateLimiter Infrastructure Active') }}
        </p>

    </div>

    {{-- Subtle Decorative Ambient Glow --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let seconds = {{ $retrySeconds }};
        const timerEl = document.getElementById('rate-limit-timer');
        if (!timerEl || seconds <= 0) return;

        const interval = setInterval(function() {
            seconds--;
            if (seconds <= 0) {
                clearInterval(interval);
                timerEl.textContent = '0';
                window.location.reload();
            } else {
                timerEl.textContent = seconds;
            }
        }, 1000);
    });
</script>
@endsection

```

---

## File: `resources/views/filament/hooks/ctrl-s-shortcut.blade.php`

```blade
<script>
    (function() {
        function isLogoutElement(el) {
            if (!el) return true;
            const form = el.closest("form");
            if (form) {
                const action = (form.getAttribute("action") || "").toLowerCase();
                if (action.includes("logout") || action.includes("sign-out") || action.includes("signout")) return true;
            }
            const txt = (el.textContent || "").trim().toLowerCase();
            const href = (el.getAttribute("href") || "").toLowerCase();
            const wireClick = (el.getAttribute("wire:click") || "").toLowerCase();
            if (txt.includes("logout") || txt.includes("sign out") || txt.includes("تسجيل الخروج") || txt.includes("خروج")) return true;
            if (href.includes("logout") || wireClick.includes("logout")) return true;
            return false;
        }

        document.addEventListener("keydown", function (e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === "s" || e.key === "S" || e.code === "KeyS")) {
                e.preventDefault();
                e.stopPropagation();

                let saveBtn = null;

                // 1. Check active modal first
                const activeModal = document.querySelector(".fi-modal:not(.hidden), [role='dialog']:not(.hidden), .fi-modal-window");
                if (activeModal) {
                    const modalButtons = Array.from(activeModal.querySelectorAll("button[type='submit'], [wire\\:click*='save'], [wire\\:click*='create'], [wire\\:click*='submit'], .fi-btn-color-primary"));
                    saveBtn = modalButtons.find(b => !b.disabled && !isLogoutElement(b));
                }

                // 2. Check main form / page submit actions (explicitly filtering out any logout form/button)
                if (!saveBtn) {
                    const pageButtons = Array.from(document.querySelectorAll(
                        ".fi-form-actions button[type='submit'], " +
                        ".fi-form-actions button.fi-btn-color-primary, " +
                        ".fi-page-header-actions button.fi-btn-color-primary, " +
                        "form:not([action*='logout']) button[type='submit'], " +
                        "button[wire\\:click*='save'], " +
                        "button[wire\\:click*='create'], " +
                        "button[wire\\:click*='update'], " +
                        "button[wire\\:click*='submit']"
                    ));
                    saveBtn = pageButtons.find(b => !b.disabled && !isLogoutElement(b));
                }

                // 3. Fallback: Search buttons by text content (excluding logout buttons)
                if (!saveBtn) {
                    const allButtons = Array.from(document.querySelectorAll("button:not([disabled])"));
                    saveBtn = allButtons.find(b => {
                        if (isLogoutElement(b)) return false;
                        const txt = (b.textContent || "").trim().toLowerCase();
                        return txt.includes("save") || txt.includes("حفظ") || txt.includes("update") || txt.includes("تحديث") || txt.includes("create") || txt.includes("إنشاء");
                    });
                }

                if (saveBtn) {
                    // Show subtle visual shortcut feedback indicator
                    const isAr = document.documentElement.lang === "ar" || document.dir === "rtl";
                    const toast = document.createElement("div");
                    toast.className = "fixed top-4 right-4 z-[99999] bg-slate-900 text-white font-mono text-xs px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2 border border-slate-700 transition-all duration-300 transform translate-y-0 opacity-100";
                    toast.innerHTML = "<span>💾</span> <span>" + (isAr ? "جاري الحفظ... (Ctrl + S)" : "Saving Changes... (Ctrl + S)") + "</span>";
                    document.body.appendChild(toast);

                    setTimeout(function() {
                        toast.style.opacity = "0";
                        toast.style.transform = "translateY(-8px)";
                        setTimeout(function() { toast.remove(); }, 300);
                    }, 1200);

                    saveBtn.click();
                }
            }
        }, true);
    })();
</script>

```

---

## File: `resources/views/filament/hooks/head-styles.blade.php`

```blade
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
<style>
    :root {
        --font-family-english: "Rubik", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        --font-family-arabic: "GE Jarida", "Cairo", "Traditional Arabic", sans-serif;
        --font-family-mono: "JetBrains Mono", monospace;
        --font-size-xs: 0.75rem;
        --font-size-sm: 0.875rem;
        --font-size-md: 1rem;
        --font-size-lg: 1.125rem;
        --font-size-xl: 1.25rem;
        --font-size-2xl: 1.5rem;
        --font-size-3xl: 1.875rem;
        --font-size-4xl: 2.25rem;
        --font-weight-regular: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;
        --line-height-tight: 1.2;
        --line-height-normal: 1.5;
        --line-height-relaxed: 1.7;
    }
    html[lang="ar"], [dir="rtl"], body[dir="rtl"], .fi-body[dir="rtl"] {
        --font-sans: var(--font-family-arabic);
        --font-heading: var(--font-family-arabic);
    }
    html[lang="en"], [dir="ltr"], body[dir="ltr"], .fi-body[dir="ltr"] {
        --font-sans: var(--font-family-english);
        --font-heading: var(--font-family-english);
    }
    html, body, button, input, select, textarea, table, th, td, label, .fi-body, .fi-panel, .fi-modal, .fi-dropdown, .fi-input, .fi-btn, .fi-sidebar, .fi-header {
        font-family: var(--font-sans) !important;
    }
</style>

```

---

## File: `resources/views/filament/pages/manage-about-page.blade.php`

```blade
<x-filament-panels::page>
    <div x-data="{ viewMode: 'desktop' }" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        {{-- Left Side: About CMS Edit Form --}}
        <div class="xl:col-span-5 space-y-6">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full">
                    Save About Page Content Settings
                </x-filament::button>
            </form>
        </div>

        {{-- Right Side: iFrame Live UI Preview of About Page --}}
        <div class="xl:col-span-7 space-y-4">
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span>Live About UI Preview</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Real-time view of the about page layout</p>
                </div>

                {{-- Device Viewport Mode Toggles --}}
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Desktop
                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Tablet
                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Mobile
                    </button>
                </div>
            </div>

            {{-- iFrame Preview Container --}}
            <div class="p-4 bg-slate-200/60 dark:bg-slate-800/60 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-inner flex justify-center items-center overflow-hidden min-h-[750px]">
                <div :class="{
                    'w-full h-[720px] border-2 border-slate-300': viewMode === 'desktop',
                    'w-[768px] max-w-full h-[720px] mx-auto border-4 border-slate-600': viewMode === 'tablet',
                    'w-[375px] max-w-full h-[720px] mx-auto border-8 border-slate-900 rounded-[2rem]': viewMode === 'mobile'
                }" class="transition-all duration-300 bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <iframe id="about-preview-iframe" src="{{ url('/about?iframe=1') }}" title="About Page UI Preview" class="w-full h-full border-0 block"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

```

---

## File: `resources/views/filament/pages/manage-contact-page.blade.php`

```blade
<x-filament-panels::page>
    <div x-data="{ viewMode: 'desktop' }" class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        {{-- Left Side: Contact CMS Edit Form --}}
        <div class="xl:col-span-5 space-y-6">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full">
                    Save Contact Page Content Settings
                </x-filament::button>
            </form>
        </div>

        {{-- Right Side: iFrame Live UI Preview of Contact Page --}}
        <div class="xl:col-span-7 space-y-4">
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span>Live Contact UI Preview</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Real-time view of the contact page layout</p>
                </div>

                {{-- Device Viewport Mode Toggles --}}
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                    <button type="button" @click.prevent="viewMode = 'desktop'" :class="viewMode === 'desktop' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Desktop
                    </button>
                    <button type="button" @click.prevent="viewMode = 'tablet'" :class="viewMode === 'tablet' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Tablet
                    </button>
                    <button type="button" @click.prevent="viewMode = 'mobile'" :class="viewMode === 'mobile' ? 'bg-white dark:bg-slate-700 text-teal-600 dark:text-teal-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'" class="px-3 py-1 text-xs rounded-lg transition-all cursor-pointer">
                        Mobile
                    </button>
                </div>
            </div>

            {{-- iFrame Preview Container --}}
            <div class="p-4 bg-slate-200/60 dark:bg-slate-800/60 rounded-2xl border border-slate-300 dark:border-slate-700 shadow-inner flex justify-center items-center overflow-hidden min-h-[750px]">
                <div :class="{
                    'w-full h-[720px] border-2 border-slate-300': viewMode === 'desktop',
                    'w-[768px] max-w-full h-[720px] mx-auto border-4 border-slate-600': viewMode === 'tablet',
                    'w-[375px] max-w-full h-[720px] mx-auto border-8 border-slate-900 rounded-[2rem]': viewMode === 'mobile'
                }" class="transition-all duration-300 bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <iframe id="contact-preview-iframe" src="{{ url('/contact?iframe=1') }}" title="Contact Page UI Preview" class="w-full h-full border-0 block"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

```

---

## File: `resources/views/filament/pages/manage-landing-page.blade.php`

```blade
<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-4 pt-4">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Save & Apply Landing Page Settings
            </x-filament::button>

            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-600 hover:text-teal-700 underline">
                <span>Preview Live Landing Page ↗</span>
            </a>
        </div>
    </form>
</x-filament-panels::page>
```

---

## File: `resources/views/filament/pages/send-fcm-broadcast-page.blade.php`

```blade
<x-filament-panels::page>
    <div class="max-w-4xl space-y-6">
        <div class="p-6 bg-slate-900 text-white rounded-3xl border border-teal-500/40 shadow-xl space-y-2">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-teal-400 animate-ping"></span>
                <h2 class="font-bold text-lg text-teal-300">Firebase Cloud Messaging (FCM) & Web Push Broadcaster</h2>
            </div>
            <p class="text-xs text-slate-300 font-mono leading-relaxed">
                Compose customized push notifications and send them directly to specific target audience groups (Students, Teachers, Parents, or All Users). Each user receives an instant Web Push notification and a system notification entry in their portal feed.
            </p>
        </div>

        <form wire:submit="sendBroadcast" class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit" size="lg" color="primary" icon="heroicon-o-paper-airplane" class="w-full sm:w-auto">
                Dispatch FCM Push Broadcast Now
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>

```

---

## File: `resources/views/filament/resources/parent-profile/children-details.blade.php`

```blade
@php
    $record = $getRecord();
    $students = $record?->students ?? collect();
    $totalCount = $students->count();
@endphp

<style>
    .linked-children-container {
        width: 100%;
        margin-top: 8px;
        margin-bottom: 8px;
    }
    .child-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 20px;
    }
    .child-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: #1e293b;
        border: 1px solid #334155;
        padding: 22px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .child-card:hover {
        transform: translateY(-5px);
        border-color: #14b8a6;
        box-shadow: 0 20px 40px -10px rgba(20, 184, 166, 0.3);
    }
    .child-card-top-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #14b8a6, #10b981, #6366f1);
    }
    .child-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 14px;
        margin-bottom: 12px;
        border-bottom: 1px solid #334155;
    }
    .child-info {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .child-info:hover .child-name {
        color: #2dd4bf;
    }
    .child-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.25), rgba(16, 185, 129, 0.25));
        border: 1px solid rgba(20, 184, 166, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .child-card:hover .child-avatar {
        transform: scale(1.1) rotate(4deg);
    }
    .child-name {
        font-size: 16px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        line-height: 1.3;
        transition: color 0.2s ease;
    }
    .child-id-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        background: #334155;
        color: #cbd5e1;
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 3px;
    }
    .badge-approved {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    .badge-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }
    .quick-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .stat-chip {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 8px;
        text-align: center;
    }
    .stat-chip-num {
        font-size: 14px;
        font-weight: 900;
        color: #38bdf8;
    }
    .stat-chip-lbl {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .detail-item {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 12px;
        padding: 10px 12px;
    }
    .detail-label {
        display: block;
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-value {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .detail-value-teal {
        color: #2dd4bf;
    }
    .progress-footer {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #334155;
    }
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        color: #cbd5e1;
        margin-bottom: 8px;
    }
    .progress-pill {
        background: rgba(20, 184, 166, 0.15);
        color: #2dd4bf;
        border: 1px solid rgba(20, 184, 166, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-pill-none {
        background: rgba(148, 163, 184, 0.15);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.3);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .progress-bar-bg {
        width: 100%;
        height: 8px;
        background: #0f172a;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #334155;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #10b981);
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    /* Action Buttons Row */
    .card-actions-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 14px;
    }
    .action-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 12px;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        transition: all 0.2s ease;
    }
    .action-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(20, 184, 166, 0.5);
    }
    .action-btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 12px;
        background: #0f172a;
        color: #cbd5e1;
        border: 1px solid #334155;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .action-btn-secondary:hover {
        background: #334155;
        color: #ffffff;
    }
    /* Real-Time Pagination Bar Styles */
    .pagination-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        padding: 12px 18px;
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 16px;
    }
    .pagination-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 800;
        background: #1e293b;
        color: #14b8a6;
        border: 1px solid rgba(20, 184, 166, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .pagination-btn:hover:not(:disabled) {
        background: #14b8a6;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }
    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        border-color: #334155;
        color: #64748b;
    }
    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .page-number-pill {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        background: #1e293b;
        color: #cbd5e1;
        border: 1px solid #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .page-number-pill:hover {
        border-color: #14b8a6;
        color: #14b8a6;
    }
    .page-number-pill.active {
        background: #14b8a6;
        color: #ffffff;
        border-color: #14b8a6;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
    }
    .pagination-info {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
    }
    .per-page-select {
        background: #1e293b;
        color: #14b8a6;
        border: 1px solid rgba(20, 184, 166, 0.4);
        border-radius: 10px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 800;
        outline: none;
        cursor: pointer;
    }
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        background: #1e293b;
        border-radius: 20px;
        border: 2px dashed #334155;
    }
    .empty-icon {
        font-size: 36px;
        margin-bottom: 8px;
    }
    .empty-title {
        font-size: 15px;
        font-weight: 800;
        color: #f8fafc;
        margin: 0;
    }
    .empty-desc {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }
</style>

<div class="linked-children-container"
     x-data="{
        page: 1,
        perPage: 2,
        total: {{ $totalCount }},
        get totalPages() {
            return Math.max(1, Math.ceil(this.total / this.perPage));
        },
        nextPage() {
            if (this.page < this.totalPages) this.page++;
        },
        prevPage() {
            if (this.page > 1) this.page--;
        },
        setPage(p) {
            this.page = p;
        }
     }">

    @if($students->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">👨‍👩‍👧</div>
            <h3 class="empty-title">لم يتم ربط أي أبناء بولي الأمر بعد</h3>
            <p class="empty-desc">
                No linked children for this parent profile yet. Select students from the dropdown above to link them.
            </p>
        </div>
    @else
        <div class="child-card-grid">
            @foreach($students as $index => $child)
                @php
                    $studentProfile = $child->studentProfile;
                    $gradeName = $studentProfile?->gradeLevel?->name ?: 'غير محدد';
                    $schoolName = $studentProfile?->school_name ?: 'غير محددة';
                    $phoneStr = $child->phone ?: 'غير متوفر';

                    $isApproved = $child->status === \App\Enums\AccountStatus::APPROVED || $child->status === 'approved';

                    $activePkg = \App\Models\StudentPackage::where('student_user_id', $child->id)->where('status', 'active')->first();
                    $remaining = $activePkg?->remaining_sessions ?? 0;
                    $total = $activePkg?->total_sessions ?? 0;
                    $percent = $total > 0 ? min(100, round(($remaining / $total) * 100)) : 0;

                    $submissionsCount = \App\Models\AssignmentSubmission::where('student_user_id', $child->id)->count();
                    $exceptionsCount = \App\Models\ExceptionRequest::where('student_user_id', $child->id)->count();
                    $enrollmentsCount = \App\Models\CourseEnrollment::where('student_user_id', $child->id)->count();

                    $studentProfileUrl = $studentProfile ? route('filament.admin.resources.student-profiles.edit', ['record' => $studentProfile->id]) : '#';
                    $userEditUrl = route('filament.admin.resources.users.edit', ['record' => $child->id]);
                @endphp

                <div class="child-card"
                     x-show="{{ $index }} >= (page - 1) * perPage && {{ $index }} < page * perPage"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="child-card-top-bar"></div>

                    <div>
                        <!-- Header: Clickable Link to Student Profile -->
                        <div class="child-header">
                            <a href="{{ $studentProfileUrl }}" target="_blank" class="child-info" title="انقر لعرض وإدارة ملف الطالب بالكامل">
                                <div class="child-avatar">🎓</div>
                                <div>
                                    <h4 class="child-name">
                                        {{ $child->name }} ↗
                                    </h4>
                                    <span class="child-id-tag">ID: #{{ $child->id }}</span>
                                </div>
                            </a>

                            <div>
                                @if($isApproved)
                                    <span class="badge-approved">✅ مقبول (Approved)</span>
                                @else
                                    <span class="badge-pending">⏳ قيد المراجعة (Pending)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Metrics Chips -->
                        <div class="quick-stats-row">
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $enrollmentsCount }}</div>
                                <div class="stat-chip-lbl">الكورسات (Courses)</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $submissionsCount }}</div>
                                <div class="stat-chip-lbl">الواجبات (Homework)</div>
                            </div>
                            <div class="stat-chip">
                                <div class="stat-chip-num">{{ $exceptionsCount }}</div>
                                <div class="stat-chip-lbl">الأعذار (Exceptions)</div>
                            </div>
                        </div>

                        <!-- Details 2x2 Grid -->
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">البريد / Email</span>
                                <span class="detail-value" title="{{ $child->email }}">📧 {{ $child->email }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الهاتف / Phone</span>
                                <span class="detail-value" title="{{ $phoneStr }}">📱 {{ $phoneStr }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">الصف / Grade</span>
                                <span class="detail-value detail-value-teal" title="{{ $gradeName }}">🏫 {{ $gradeName }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">المدرسة / School</span>
                                <span class="detail-value" title="{{ $schoolName }}">🏢 {{ $schoolName }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Package Progress Bar Footer -->
                    <div>
                        <div class="progress-footer">
                            <div class="progress-header">
                                <span>💳 رصيد الباقة / Package Balance:</span>
                                @if($activePkg)
                                    <span class="progress-pill">{{ $remaining }} / {{ $total }} Sessions</span>
                                @else
                                    <span class="progress-pill-none">لا توجد باقة نشطة</span>
                                @endif
                            </div>

                            @if($activePkg && $total > 0)
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ $percent }}%;"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons Row -->
                        <div class="card-actions-row">
                            <a href="{{ $studentProfileUrl }}" target="_blank" class="action-btn-primary" title="الانتقال لبروفايل الطالب وإدارته بالكامل">
                                ⚡ <span>إدارة الطالب (Manage Student)</span>
                            </a>
                            <a href="{{ $userEditUrl }}" target="_blank" class="action-btn-secondary" title="إدارة حساب المستخدم والصلاحيات">
                                👤 <span>إدارة الحساب (User Account)</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Real-Time Interactive Pagination Toolbar -->
        @if($totalCount > 2)
            <div class="pagination-toolbar">
                <button class="pagination-btn"
                        @click="prevPage()"
                        :disabled="page === 1">
                    <span>◀</span>
                    <span>السابق (Prev)</span>
                </button>

                <div class="flex items-center gap-3">
                    <span class="pagination-info">
                        صفحة <strong x-text="page" class="text-teal-400"></strong> من <strong x-text="totalPages" class="text-teal-400"></strong> (إجمالي <span class="text-white font-extrabold">{{ $totalCount }}</span> أبناء)
                    </span>

                    <!-- Page Number Pills -->
                    <div class="pagination-numbers">
                        <template x-for="p in totalPages" :key="p">
                            <div class="page-number-pill"
                                 :class="{ 'active': page === p }"
                                 @click="setPage(p)"
                                 x-text="p"></div>
                        </template>
                    </div>

                    <!-- Cards Per Page Selector -->
                    <div class="flex items-center gap-1.5 ml-2">
                        <span class="text-[11px] font-bold text-slate-400">العرض:</span>
                        <select class="per-page-select" x-model.number="perPage" @change="page = 1">
                            <option value="2">2 كروت / صفحة</option>
                            <option value="4">4 كروت / صفحة</option>
                            <option value="6">6 كروت / صفحة</option>
                        </select>
                    </div>
                </div>

                <button class="pagination-btn"
                        @click="nextPage()"
                        :disabled="page === totalPages">
                    <span>التالي (Next)</span>
                    <span>▶</span>
                </button>
            </div>
        @endif
    @endif
</div>

```

---

## File: `resources/views/filament/resources/student-profile/academic-overview.blade.php`

```blade
@php
    $studentProfile = $getRecord();
    $studentUser = $studentProfile?->user;
    $studentUserId = $studentProfile?->user_id;

    $activePackage = \App\Models\StudentPackage::where('student_user_id', $studentUserId)->where('status', 'active')->first();
    $parents = \App\Models\ParentProfile::whereHas('students', fn($q) => $q->where('student_user_id', $studentUserId))->with('user')->get();
    $submissions = \App\Models\AssignmentSubmission::where('student_user_id', $studentUserId)->with('assignment')->latest()->take(5)->get();
    $exceptions = \App\Models\ExceptionRequest::where('student_user_id', $studentUserId)->latest()->take(5)->get();
    $enrollments = \App\Models\CourseEnrollment::where('student_user_id', $studentUserId)->with('course')->latest()->take(5)->get();
@endphp

<style>
    .student-overview-wrapper {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }
    .overview-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
    .overview-card:hover {
        border-color: #14b8a6;
        box-shadow: 0 15px 30px -10px rgba(20, 184, 166, 0.2);
    }
    .card-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid #334155;
    }
    .card-title-text {
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 12px;
    }
    .badge-teal {
        background: rgba(20, 184, 166, 0.15);
        color: #2dd4bf;
        border: 1px solid rgba(20, 184, 166, 0.3);
    }
    .badge-gray {
        background: rgba(148, 163, 184, 0.15);
        color: #94a3b8;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }
    .item-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .list-row {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 14px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .list-row-title {
        font-size: 12px;
        font-weight: 700;
        color: #f8fafc;
    }
    .list-row-sub {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        margin-top: 2px;
    }
    .pkg-progress-bar-bg {
        width: 100%;
        height: 10px;
        background: #0f172a;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #334155;
        margin-top: 10px;
    }
    .pkg-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #14b8a6, #10b981);
        border-radius: 10px;
    }
    .empty-box {
        text-align: center;
        padding: 20px;
        background: #0f172a;
        border-radius: 14px;
        border: 1px dashed #334155;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="student-overview-wrapper">
    <div class="overview-grid">
        <!-- 💳 Active Package & Credit Balance -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">💳 Active Session Package & Credits</span>
                @if($activePackage)
                    <span class="card-badge badge-teal">✅ Active Package</span>
                @else
                    <span class="card-badge badge-gray">No Active Package</span>
                @endif
            </div>

            @if($activePackage)
                @php
                    $rem = $activePackage->remaining_sessions;
                    $tot = $activePackage->total_sessions;
                    $pct = $tot > 0 ? round(($rem / $tot) * 100) : 0;
                @endphp
                <div style="background: #0f172a; padding: 14px; border-radius: 14px; border: 1px solid #1e293b;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 800; color: #ffffff;">
                        <span>{{ $activePackage->packageTemplate?->name ?? 'Custom Package' }}</span>
                        <span style="color: #2dd4bf;">{{ $rem }} / {{ $tot }} Sessions Remaining</span>
                    </div>
                    <div class="pkg-progress-bar-bg">
                        <div class="pkg-progress-bar-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 8px;">
                        <span>Activated: {{ $activePackage->activated_at ? $activePackage->activated_at->format('d M Y') : 'N/A' }}</span>
                        <span>Expires: {{ $activePackage->expires_at ? $activePackage->expires_at->format('d M Y') : 'No Expiry' }}</span>
                    </div>
                </div>
            @else
                <div class="empty-box">
                    No active package assigned to this student yet. Use the "Assign Package" button in the header actions above to issue credits.
                </div>
            @endif
        </div>

        <!-- 👨‍👩‍👧 Linked Parent Account(s) -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">👨‍👩‍👧 Linked Parent / Guarding Accounts</span>
                <span class="card-badge badge-teal">{{ $parents->count() }} Linked</span>
            </div>

            @if($parents->isNotEmpty())
                <div class="item-list">
                    @foreach($parents as $parent)
                        @php $parentUser = $parent->user; @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">👨‍👩‍👧 {{ $parentUser?->name ?? 'Unknown Parent' }}</div>
                                <div class="list-row-sub">📧 {{ $parentUser?->email ?? 'N/A' }} | 📱 {{ $parentUser?->phone ?? 'N/A' }}</div>
                            </div>
                            <a href="{{ route('filament.admin.resources.parent-profiles.edit', ['record' => $parent->id]) }}" target="_blank" style="font-size: 11px; font-weight: 800; color: #38bdf8; text-decoration: none;">
                                View ↗
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    No parent account linked to this student profile yet.
                </div>
            @endif
        </div>

        <!-- 📚 Course Enrollments -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">📚 Enrolled Courses</span>
                <span class="card-badge badge-teal">{{ $enrollments->count() }} Courses</span>
            </div>

            @if($enrollments->isNotEmpty())
                <div class="item-list">
                    @foreach($enrollments as $enrollment)
                        @php
                            $eStatus = is_object($enrollment->status) ? ($enrollment->status->value ?? (string)$enrollment->status) : (string)$enrollment->status;
                        @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">📘 {{ $enrollment->course?->title ?? 'Course' }}</div>
                                <div class="list-row-sub">Status: {{ ucfirst($eStatus) }} | Date: {{ $enrollment->created_at->format('d M Y') }}</div>
                            </div>
                            <span style="font-size: 11px; font-weight: 800; color: #2dd4bf;">Enrolled</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    Student has not enrolled in any courses yet.
                </div>
            @endif
        </div>

        <!-- 📝 Homework Submissions -->
        <div class="overview-card">
            <div class="card-title-bar">
                <span class="card-title-text">📝 Recent Homework Submissions</span>
                <span class="card-badge badge-teal">{{ $submissions->count() }} Submissions</span>
            </div>

            @if($submissions->isNotEmpty())
                <div class="item-list">
                    @foreach($submissions as $sub)
                        @php
                            $sStatus = is_object($sub->status) ? ($sub->status->value ?? (string)$sub->status) : (string)$sub->status;
                        @endphp
                        <div class="list-row">
                            <div>
                                <div class="list-row-title">📝 {{ $sub->assignment?->title ?? 'Assignment' }}</div>
                                <div class="list-row-sub">Score: {{ $sub->grade ?? 'Pending Review' }}% | Submitted: {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y') : 'N/A' }}</div>
                            </div>
                            <span style="font-size: 11px; font-weight: 800; color: #fbbf24;">{{ ucfirst($sStatus) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-box">
                    No homework submissions recorded yet.
                </div>
            @endif
        </div>
    </div>
</div>

```

---

## File: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth h-full bg-[#FAFAF9] text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0F172A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform in Egypt' }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'Elite Academy empowers Egyptian students with accredited academic tracks in Programming, Artificial Intelligence, Science, and Business led by top educators.' }}">
    <meta name="keywords" content="Elite Academy, أكاديمية إيليت, منصة تعليمية, الثانوية العامة, برمجة, ذكاء اصطناعي, مصر, كورس, دروس مباشرة">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="Elite Academy Team">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Multilingual Hreflang Alternates --}}
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook Meta Tags --}}
    <meta property="og:site_name" content="Elite Academy | أكاديمية إيليت">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform' }}">
    <meta property="og:description" content="{{ $pageDescription ?? 'Join Egypt’s premier academic platform for live classes, accredited tracks, and expert mentors.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ media_url($ogImage ?? 'images/academy_campus.png') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_EG' : 'en_US' }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت' }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? 'Egypt’s premier academic platform for live classes and accredited tracks.' }}">
    <meta name="twitter:image" content="{{ media_url($ogImage ?? 'images/academy_campus.png') }}">

    {{-- Master JSON-LD Schema.org Structured Data Graph --}}
    @php
        $globalAppJsonLd = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "WebSite",
                    "@id" => url('/') . "/#website",
                    "url" => url('/'),
                    "name" => "Elite Academy LMS",
                    "description" => "Egypt premier accredited K-12 interactive tutoring and learning platform.",
                    "publisher" => [
                        "@id" => url('/') . "/#organization"
                    ],
                    "inLanguage" => app()->getLocale(),
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => url('/courses') . "?search={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "EducationalOrganization",
                    "@id" => url('/') . "/#organization",
                    "name" => "Elite Academy LMS",
                    "alternateName" => "أكاديمية إيليت التعليمية",
                    "url" => url('/'),
                    "logo" => asset('images/logo.png'),
                    "image" => asset('images/academy_campus.png'),
                    "description" => "Ministry-accredited interactive educational platform providing live classes, auto-graded assignments, and verified tutoring in Egypt.",
                    "telephone" => "+201000000000",
                    "email" => "support@elite-academy.com",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "Academic Center Tower, New Cairo",
                        "addressLocality" => "Cairo",
                        "addressCountry" => "EG"
                    ],
                    "aggregateRating" => [
                        "@type" => "AggregateRating",
                        "ratingValue" => "4.9",
                        "reviewCount" => "1280",
                        "bestRating" => "5",
                        "worstRating" => "1"
                    ],
                    "sameAs" => [
                        "https://facebook.com/eliteacademy",
                        "https://twitter.com/eliteacademy",
                        "https://instagram.com/eliteacademy"
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($globalAppJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Rubik:ital,wght@0,300..900;1,300..900&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family-english: "Rubik", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-arabic: "GE Jarida", "Cairo", "Traditional Arabic", sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-md: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            --font-weight-regular: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --line-height-tight: 1.2;
            --line-height-normal: 1.5;
            --line-height-relaxed: 1.7;
        }
        html[lang="ar"], [dir="rtl"] {
            --font-sans: var(--font-family-arabic);
            --font-heading: var(--font-family-arabic);
        }
        html[lang="en"], [dir="ltr"] {
            --font-sans: var(--font-family-english);
            --font-heading: var(--font-family-english);
        }
        html, body, button, input, select, textarea, table, .font-sans, .font-heading {
            font-family: var(--font-sans) !important;
        }

        html, body {
            background-color: #FAFAF9 !important;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Smooth UI Keyframe Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out forwards;
        }

        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.12s; }
        .stagger-3 { animation-delay: 0.19s; }
        .stagger-4 { animation-delay: 0.26s; }

        /* Card Elevation & Hover Micro-interactions */
        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        }

        .btn-lift {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-lift:hover {
            transform: translateY(-1.5px);
            filter: brightness(1.05);
        }
        .btn-lift:active {
            transform: translateY(0.5px) scale(0.98);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
    @include('partials.inp-optimizer')
</head>
<body class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] text-slate-900 selection:bg-teal-100 selection:text-teal-900 flex flex-col min-h-screen m-0 p-0">

    {{-- Scroll Progress Bar --}}
    @if(!request()->boolean('iframe'))
        <div id="scroll-progress" class="fixed top-0 left-0 right-0 h-1 bg-teal-500 z-[60]" style="width: 0%"></div>
    @endif

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.ambient')
        @include('partials.navbar')
    @endif

    <main class="flex-grow w-full bg-[#FAFAF9] min-h-[60vh]">
        @yield('content')
    </main>

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.footer')
    @endif

    {{-- Back to Top Button --}}
    @if(!request()->boolean('iframe'))
        <button id="back-to-top" aria-label="Back to top">↑</button>
    @endif

    <script src="{{ asset('js/scroll-reveal.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}?v={{ time() }}"></script>
    @php
        $flashToasts = array_filter([
            'success' => session('success'),
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ]);
    @endphp
    @if(!empty($flashToasts))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toasts = @json($flashToasts);
                if (window.Toast) {
                    Object.keys(toasts).forEach(type => {
                        if (toasts[type] && typeof window.Toast[type] === 'function') {
                            window.Toast[type](toasts[type]);
                        }
                    });
                }
            });
        </script>
    @endif

    @if(auth()->check() && !request()->boolean('iframe'))
        {{-- FCM Push Notification Permission Popup Modal --}}
        <div id="fcm-permission-modal" class="hidden fixed bottom-6 right-6 left-6 sm:left-auto sm:max-w-md bg-slate-900/95 backdrop-blur-md text-white p-6 rounded-3xl shadow-2xl border border-slate-700/80 z-50 transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/40 flex items-center justify-center text-2xl shrink-0">
                    🔔
                </div>
                <div class="space-y-2 flex-1">
                    <h4 class="font-heading font-bold text-sm sm:text-base text-white">
                        {{ __('Enable Live Push Notifications') }}
                    </h4>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('Stay updated with instant alerts for upcoming live sessions, 24h assignment deadlines, and admin approvals.') }}
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <button id="btn-enable-fcm" type="button" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-xl text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>✨</span> {{ __('Allow Notifications Now') }}
                        </button>
                        <button id="btn-dismiss-fcm" type="button" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs transition-all cursor-pointer">
                            {{ __('Later') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Firebase JS SDK (v9 Compat) for Real Native FCM Web Push -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

    <script>
        window.firebaseConfig = {
            apiKey: "{{ config('fcm.web_config.api_key') }}",
            authDomain: "{{ config('fcm.web_config.auth_domain', 'elite-academy-67a15.firebaseapp.com') }}",
            projectId: "{{ config('fcm.v1.project_id', 'elite-academy-67a15') }}",
            storageBucket: "{{ config('fcm.web_config.storage_bucket', 'elite-academy-67a15.firebasestorage.app') }}",
            messagingSenderId: "{{ config('fcm.web_config.messaging_sender_id', '53377882422') }}",
            appId: "{{ config('fcm.web_config.app_id') }}"
        };

        window.sendFcmTokenToServer = function(token) {
            const shortToken = token.length > 25 ? token.substring(0, 22) + '...' : token;
            console.log('[FCM] Token obtained:', shortToken);

            const tokenInputs = document.querySelectorAll('#userFcmTokenInput');
            tokenInputs.forEach(input => {
                input.value = token;
            });

            const currentUserId = "{{ auth()->id() ?? 0 }}";
            const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
            localStorage.setItem(fcmTokenKey, token);

            fetch('{{ route('ajax.notifications.token') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: token,
                    device_type: 'web_browser'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('[FCM] Token saved to server.');
                }
            })
            .catch(() => {});
        };

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('fcm-permission-modal');
            const btnEnable = document.getElementById('btn-enable-fcm');
            const btnDismiss = document.getElementById('btn-dismiss-fcm');

            let messaging = null;
            if (typeof firebase !== 'undefined') {
                try {
                    if (!firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                    }
                    if (firebase.messaging.isSupported()) {
                        messaging = firebase.messaging();
                        
                        if ('serviceWorker' in navigator) {
                            navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}').then((reg) => {
                                if (messaging && Notification.permission === 'granted') {
                                    const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                                    const tokenOpts = { serviceWorkerRegistration: reg };
                                    if (vapidKey) tokenOpts.vapidKey = vapidKey;

                                    messaging.getToken(tokenOpts).then((token) => {
                                        if (token) {
                                            sendFcmTokenToServer(token);
                                        }
                                    }).catch((err) => {
                                        const saved = getSavedToken();
                                        if (saved) sendFcmTokenToServer(saved);
                                    });
                                }
                            }).catch(() => {});
                        }

                        messaging.onMessage((payload) => {
                            const title = (payload.notification && payload.notification.title) ||
                                          (payload.data && payload.data.title) ||
                                          '🔔 Firebase Push Notification';

                            const body = (payload.notification && payload.notification.body) ||
                                         (payload.data && payload.data.body) ||
                                         '';

                            const icon = (payload.notification && payload.notification.image) ||
                                         (payload.data && payload.data.icon) ||
                                         '/images/logo.png';

                            if (window.Toast) {
                                window.Toast.info(body, title);
                            }

                            if ('Notification' in window && Notification.permission === 'granted') {
                                try {
                                    new Notification(title, { body: body, icon: icon });
                                } catch (e) {}
                            }

                            window.dispatchEvent(new CustomEvent('fcm-realtime-message', { detail: { notification: { title, body, image: icon }, data: payload.data || {} } }));
                        });
                    }
                } catch (err) {
                    console.warn('Firebase Messaging init fallback:', err);
                }
            }

            function getSavedToken() {
                const currentUserId = "{{ auth()->id() ?? 0 }}";
                const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
                return localStorage.getItem(fcmTokenKey);
            }

            const savedToken = getSavedToken();
            if (savedToken) {
                sendFcmTokenToServer(savedToken);
            }

            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    if (modal) modal.classList.remove('hidden');
                } else if (Notification.permission === 'granted') {
                    const saved = getSavedToken();
                    if (!saved && messaging) {
                        window.requestLiveFirebaseToken();
                    }
                }
            }

            if (btnEnable) {
                btnEnable.addEventListener('click', function () {
                    if (modal) modal.classList.add('hidden');
                    window.requestLiveFirebaseToken();
                });
            }

            if (btnDismiss) {
                btnDismiss.addEventListener('click', function () {
                    if (modal) modal.classList.add('hidden');
                });
            }
        });

        window.copyFcmTokenToClipboard = function() {
            const input = document.getElementById('userFcmTokenInput');
            if (input && input.value) {
                navigator.clipboard.writeText(input.value).then(() => {
                    if (window.Toast) {
                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم نسخ رمز FCM للحافظة!' : 'FCM Token copied to clipboard!'));
                    }
                }).catch(() => {
                    input.select();
                    document.execCommand('copy');
                    if (window.Toast) {
                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم نسخ رمز FCM للحافظة!' : 'FCM Token copied to clipboard!'));
                    }
                });
            }
        };

        window.registerCustomFcmToken = function() {
            const input = document.getElementById('userFcmTokenInput');
            const token = input ? input.value.trim() : '';

            if (!token) {
                if (window.Toast) window.Toast.error(@json(app()->getLocale() === 'ar' ? 'يرجى إدخال رمز FCM أولاً!' : 'Please enter an FCM token string first!'));
                return;
            }

            const currentUserId = "{{ auth()->id() ?? 0 }}";
            const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
            localStorage.setItem(fcmTokenKey, token);

            fetch('{{ route('ajax.notifications.token') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: token,
                    device_type: 'web_browser'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && window.Toast) {
                    window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم تسجيل وتحديث رمز FCM في النظام بنجاح!' : 'FCM Token registered and updated cleanly!'));
                }
            })
            .catch(() => {
                if (window.Toast) window.Toast.error('Failed to update FCM token');
            });
        };

        window.requestLiveFirebaseToken = function() {
            if (!('Notification' in window) || !('PushManager' in window) || !('serviceWorker' in navigator)) {
                if (window.Toast) window.Toast.error(document.documentElement.lang === 'ar' ? 'المتصفح لا يدعم إشعارات المتصفح الفورية' : 'Browser does not support Web Push notifications');
                return;
            }

            Notification.requestPermission().then(async (permission) => {
                if (permission !== 'granted') {
                    if (window.Toast) window.Toast.warning(document.documentElement.lang === 'ar' ? 'لم يتم منح إذن الإشعارات' : 'Notification permission was not granted');
                    return;
                }

                if (typeof firebase !== 'undefined') {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        if (firebase.messaging.isSupported()) {
                            const messaging = firebase.messaging();
                            const reg = await navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}', { scope: '/' });
                            await navigator.serviceWorker.ready;

                            const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                            const opts = { serviceWorkerRegistration: reg };
                            if (vapidKey) opts.vapidKey = vapidKey;

                            messaging.getToken(opts).then((token) => {
                                if (token) {
                                    sendFcmTokenToServer(token);
                                    
                                    fetch("{{ route('ajax.notifications.test-push') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    }).catch(() => {});

                                    if (window.Toast) {
                                        window.Toast.success(document.documentElement.lang === 'ar' ? 'تم تفعيل وتحديث إشعارات الفايبربيس بنجاح! 🔔' : 'Live Firebase Push Notifications Enabled Successfully! 🔔');
                                    }
                                } else {
                                    if (window.Toast) window.Toast.warning('No FCM registration token returned by Firebase');
                                }
                            }).catch(err => {
                                console.error('Firebase getToken error:', err);
                                const currentUserId = "{{ auth()->id() ?? 0 }}";
                                const saved = localStorage.getItem('elite_fcm_token_' + currentUserId);
                                if (saved) sendFcmTokenToServer(saved);
                                if (window.Toast) window.Toast.error('Firebase token request: ' + (err ? err.message : 'Push manager not ready'));
                            });
                        }
                    } catch (e) {
                        console.error('Firebase error:', e);
                    }
                }
            });
        };
    </script>

    @stack('scripts')
</body>
</html>

```

---

## File: `resources/views/pages/about.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $heroBadge = $aboutSettings['hero_badge'] ?? 'ACCREDITED EXCELLENCE • EST. 2020';
    $heroTitle = $aboutSettings['hero_title'] ?? 'Transforming Academic Education For Future Leaders';
    $heroSubtitle = $aboutSettings['hero_subtitle'] ?? 'Elite Academy combines accredited national curricula with interactive video sessions, real-time mentor Q&A, and parent progress tracking.';
    $missionTitle = $aboutSettings['mission_title'] ?? 'Our Core Educational Mission';
    $missionText = $aboutSettings['mission_text'] ?? 'To deliver accessible, high-quality, and interactive learning experiences that empower secondary students to excel in national and international examinations.';
    $visionTitle = $aboutSettings['vision_title'] ?? 'Our Vision For Tomorrow';
    $visionText = $aboutSettings['vision_text'] ?? 'Empowering 100,000+ students across Egypt and the MENA region with personalized academic paths, expert faculty mentorship, and innovative digital learning tools.';
    $statStudents = $aboutSettings['stat_students'] ?? '25,000+';
    $statCourses = $aboutSettings['stat_courses'] ?? '120+';
    $statTeachers = $aboutSettings['stat_teachers'] ?? '45+';
    $statPassRate = $aboutSettings['stat_pass_rate'] ?? '98.5%';
@endphp

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => 'About Elite Academy'],
            ]
        ])

        {{-- Storytelling Editorial Hero Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ $heroBadge }}
                </span>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                    {!! $heroTitle !!}
                </h1>

                <p class="text-slate-600 text-base sm:text-lg font-medium leading-relaxed">
                    {{ $heroSubtitle }}
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-center">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block">{{ $statStudents }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Active Students</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-orange-600 block">{{ $statCourses }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Accredited Courses</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-teal-600 block">{{ $statTeachers }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Expert Faculty</span>
                    </div>
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80">
                        <span class="font-heading font-black text-2xl sm:text-3xl text-emerald-600 block">{{ $statPassRate }}</span>
                        <span class="text-[11px] font-mono text-slate-500 font-bold">Exam Pass Rate</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="{{ asset('images/academy_campus.png') }}" alt="Elite Academy Campus" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-slate-900 text-white p-5 rounded-2xl border border-slate-800 shadow-2xl">
                    <p class="font-heading font-black text-xl text-teal-400">ACCREDITED ACADEMY</p>
                    <p class="text-xs font-mono text-slate-300">Secondary Education Excellence</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-teal-600 bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    OUR MISSION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">{{ $missionTitle }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ $missionText }}
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-4 card-lift">
                <span class="text-xs font-mono font-extrabold text-orange-600 bg-orange-50 px-3.5 py-1.5 rounded-full border border-orange-200/80">
                    OUR VISION
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">{{ $visionTitle }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ $visionText }}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Cornerstone Long-Form Academic Guide (1,500+ Words for AI Search Synthesis & GEO Indexing) --}}
<section class="py-16 md:py-24 bg-white border-t border-slate-200/80">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-slate-800 leading-relaxed font-sans">
        <article class="prose prose-slate max-w-none space-y-8">
            <header class="space-y-4 border-b border-slate-200 pb-8">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200">
                    <span>📖</span> Comprehensive Academic & Platform Guide
                </span>
                <h2 class="font-heading font-black text-3xl sm:text-4xl text-slate-900 tracking-tight">
                    The Complete Architecture of Modern Secondary Tutoring & E-Learning in Egypt
                </h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    An in-depth whitepaper on Elite Academy’s pedagogical framework, encrypted stream security, automated grading engines, and real-time parental integration.
                </p>
            </header>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">1. Thanawya Amma & Secondary Curriculum Alignment</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Elite Academy is engineered specifically to address the rigorous academic requirements of Egypt’s Thanawya Amma national secondary exams and language school curricula. Our courses cover critical academic tracks including Advanced Physics, Artificial Intelligence & Computer Science, Organic & Analytical Chemistry, Pure Mathematics (Calculus, Algebra, Dynamics), and Business Administration. Each curriculum is constructed by senior Egyptian educators with over 15 years of national exam preparation experience.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Instead of relying solely on passive video lectures, Elite Academy utilizes a dual-engine learning system: real-time interactive live sessions paired with step-by-step homework assignments that mirror official ministry exam formats. This ensures students build deep conceptual understanding alongside timed problem-solving endurance.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">2. Live Stream Streaming Security & Anti-Piracy Architecture</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Protecting faculty intellectual property is central to our platform design. All live sessions hosted via Zoom, Google Meet, or BigBlueButton are rendered inside embedded, token-protected stream frames. We enforce dynamic client-side watermarking that overlays the student’s name, unique student ID (e.g. STU-00142), current timestamp, and IP address across the video stream in real-time.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Additionally, our anti-piracy shield monitors window visibility and screen recording events. If screen capture software or unauthorized casting is detected, video playback is immediately obfuscated and an automated security event audit log is transmitted to administrators.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">3. Interactive Assignment Solver & Offline-Resilient Grading</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Homework submission is powered by an interactive assignment solver that breaks complex problem sets into structured steps. As students answer single-choice, multiple-choice, or numeric questions, their progress is auto-saved locally in browser storage. This offline-resilient caching guarantees that network fluctuations or power outages never result in lost work.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Upon submission, our automated grading engine evaluates responses instantly, calculating overall scores, percentage metrics, and itemized performance breakdowns. For incorrect submissions, students gain instant access to detailed video solution walkthroughs recorded by their instructor.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">4. Real-Time Parent Portal & Attendance Tracking</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Parental involvement is directly linked to higher student pass rates. The Elite Academy Parent Portal allows guardians to link student accounts using verified phone numbers. Parents receive real-time visibility into attendance logs recorded by our minute-by-minute meeting heartbeat engine.
                </p>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    When a student joins a live session, attends a review lab, or misses an assignment deadline, instant Firebase Cloud Messaging (FCM) push alerts and optional WhatsApp notifications are dispatched to parents. This transparent feedback loop ensures accountability throughout the semester.
                </p>
            </section>

            <section class="space-y-4">
                <h3 class="font-heading font-bold text-2xl text-slate-900">5. Faculty Accreditation & Institutional Certification</h3>
                <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                    Every instructor on Elite Academy undergoes rigorous verification of academic credentials and teaching credentials. Upon successful completion of a course track and final capstone evaluation, students earn an officially verified digital certificate of achievement featuring a unique QR code and serial number for institutional validation.
                </p>
            </section>
        </article>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/blog-details.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $title = $article ? $article->title : 'How to Prepare for Final Exams Without Stress';
    $excerpt = $article ? ($article->excerpt ?: 'Final exams don\'t have to trigger burnout or anxiety.') : 'Final exams don\'t have to trigger burnout or anxiety.';
    $content = $article ? $article->content : 'Final exams don\'t have to trigger burnout or anxiety. By breaking revision sessions into structured Pomodoro blocks, prioritizing high-yield topics, and reviewing past exam papers, you can build steady confidence and achieve top scores while maintaining a healthy sleep schedule.';
    $category = $article ? $article->category : 'Study Tips';
    $author = $article?->authorUser?->name ?: 'Dr. Ahmed Hassan';
    $date = $article?->published_at ? $article->published_at->format('M d, Y') : 'Oct 12, 2026';
    $readTime = $article ? ($article->read_time_minutes . ' min read') : '6 min read';
    $image = $article ? $article->featured_image_url : 'images/hero_student.png';
@endphp

<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.blog'), 'route' => 'blog'],
                ['label' => $category, 'url' => route('blog', ['category' => $category])],
                ['label' => $title],
            ]
        ])

        <div class="space-y-4">
            <span class="inline-block bg-teal-600 text-white text-xs font-mono font-bold uppercase tracking-wider px-3.5 py-1 rounded-full">
                {{ $category }}
            </span>

            <h1 class="font-heading text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                {{ $title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-slate-300 font-bold pt-2">
                <span>By {{ $author }}</span>
                <span>•</span>
                <span>{{ $date }}</span>
                <span>•</span>
                <span class="text-teal-400">{{ $readTime }}</span>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        {{-- Featured Image --}}
        <div class="relative w-full h-64 sm:h-96 lg:h-[480px] rounded-3xl overflow-hidden shadow-2xl bg-slate-950">
            <img src="{{ media_url($image, 'images/course_ai.png') }}" alt="{{ $title }}" class="w-full h-full object-cover">
        </div>

        {{-- Excerpt Callout --}}
        <div class="p-6 bg-teal-50/80 rounded-2xl border-l-4 border-teal-600 text-slate-800 text-base sm:text-lg font-medium leading-relaxed">
            {{ $excerpt }}
        </div>

        {{-- Body Content --}}
        <div class="prose prose-slate max-w-none text-slate-800 text-base sm:text-lg leading-relaxed space-y-6">
            {!! nl2br(e($content)) !!}
        </div>

        {{-- Share & Navigation Footer --}}
        <div class="pt-8 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('blog') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-all">
                &larr; Back to All Articles
            </a>
            <a href="{{ route('blog', ['category' => $category]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-50 text-teal-700 rounded-xl text-xs font-bold hover:bg-teal-100">
                More in {{ $category }} &rarr;
            </a>
        </div>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/blog.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12">

        {{-- Section Header --}}
        @include('components.section-header', [
            'badge' => 'ACADEMIC INSIGHTS & BLOG',
            'title' => 'Latest Articles & <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Educational News</span>',
            'subtitle' => 'Expert advice, study tips, and academic insights from Elite Academy faculty.',
            'centered' => true,
        ])

        {{-- Category Filter Chips --}}
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
            <a href="{{ route('blog') }}"
               @class([
                   'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                   'bg-teal-600 text-white shadow-md border-teal-600' => empty($selectedCategory),
                   'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! empty($selectedCategory),
               ])>
                All Articles
            </a>
            @foreach ($categories as $cat)
                @php $isActive = strtolower($selectedCategory ?? '') === strtolower($cat); @endphp
                <a href="{{ route('blog', ['category' => $cat]) }}"
                   @class([
                       'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white shadow-md border-teal-600' => $isActive,
                       'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! $isActive,
                   ])>
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- Articles Feed --}}
        <div class="space-y-8 md:space-y-12">
            @if(isset($articles) && count($articles) > 0)
                @foreach ($articles as $a)
                    @php
                        $isModel = $a instanceof \App\Models\Article;
                        $slug = $isModel ? $a->slug : 'blog-details';
                        $cardData = [
                            'image' => $isModel ? $a->featured_image_url : ($a['image'] ?? 'images/hero_student.png'),
                            'category' => $isModel ? $a->category : ($a['category'] ?? 'Study Tips'),
                            'categoryColor' => 'bg-teal-600',
                            'title' => $isModel ? $a->title : ($a['title'] ?? 'Article Title'),
                            'excerpt' => $isModel ? ($a->excerpt ?: 'Read our comprehensive academic guidance article.') : ($a['excerpt'] ?? 'Article excerpt'),
                            'author' => $isModel ? ($a->authorUser?->name ?: 'Dr. Ahmed Hassan') : ($a['author'] ?? 'Dr. Ahmed Hassan'),
                            'date' => $isModel ? ($a->published_at ? $a->published_at->format('M d, Y') : now()->format('M d, Y')) : ($a['date'] ?? 'Oct 12, 2026'),
                            'readTime' => $isModel ? ($a->read_time_minutes . ' min read') : ($a['readTime'] ?? '6 min read'),
                            'route' => route('blog-details', ['slug' => $slug]),
                        ];
                    @endphp
                    @include('components.article-card', $cardData)
                    @if (! $loop->last)
                        <hr class="border-t border-slate-200/80">
                    @endif
                @endforeach
            @else
                <div class="text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📰</div>
                    <h3 class="font-bold text-lg text-slate-800">No Articles Found for Selected Category</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Try selecting "All Articles" or check back soon for new publications.</p>
                    <a href="{{ route('blog') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
                        View All Articles
                    </a>
                </div>
            @endif
        </div>

        {{-- Creative Pagination Bar --}}
        @if(method_exists($articles, 'hasPages') && $articles->hasPages())
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80">
                <div class="text-xs font-mono text-slate-500 font-bold">
                    Showing {{ $articles->firstItem() }} to {{ $articles->lastItem() }} of {{ $articles->total() }} Articles
                </div>

                <div class="flex items-center gap-1.5">
                    @if ($articles->onFirstPage())
                        <span class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed">
                            &larr; Prev
                        </span>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}" class="btn-lift px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                            &larr; Prev
                        </a>
                    @endif

                    @foreach (range(1, $articles->lastPage()) as $page)
                        @if ($page == $articles->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center text-xs font-bold text-white bg-teal-600 rounded-xl shadow-md shadow-teal-600/20">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $articles->url($page) }}" class="btn-lift w-9 h-9 flex items-center justify-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}" class="btn-lift px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl shadow-2xs transition-all">
                            Next &rarr;
                        </a>
                    @else
                        <span class="px-3.5 py-2 text-xs font-bold text-slate-300 bg-slate-100 rounded-xl cursor-not-allowed">
                            Next &rarr;
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/contact.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $heroBadge = $contactSettings['hero_badge'] ?? 'STUDENT & PARENT SUPPORT';
    $heroTitle = $contactSettings['hero_title'] ?? 'We Are Always Here To Help';
    $heroSubtitle = $contactSettings['hero_subtitle'] ?? 'Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.';
    $heroImage = $contactSettings['hero_image'] ?? 'images/academy_campus.png';
    $cardTitle = $contactSettings['card_title'] ?? 'Support Desk 24/7';
    $cardSubtitle = $contactSettings['card_subtitle'] ?? 'Direct Academic Assistance';
    $cardIcon = $contactSettings['card_icon'] ?? '🎧';
    $phone = $contactSettings['phone'] ?? '+20 100 123 4567';
    $whatsapp = $contactSettings['whatsapp'] ?? '+20 100 123 4568';
    $email = $contactSettings['email'] ?? 'support@elite-academy.edu.eg';
    $address = $contactSettings['address'] ?? 'New Cairo Hub, Egypt';
    $formTitle = $contactSettings['form_title'] ?? 'Send Us a Message';
    $formSubtitle = $contactSettings['form_subtitle'] ?? 'Our student advisors will respond within 24 hours.';
    $mapUrl = $contactSettings['map_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg';
@endphp

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.contact')],
            ]
        ])

        {{-- Premium Split Hero Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6 space-y-6">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ $heroBadge }}
                </span>

                <h1 class="font-heading text-4xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    {!! $heroTitle !!}
                </h1>

                <p class="text-slate-600 text-base font-medium leading-relaxed">
                    {{ $heroSubtitle }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Phone Support</span>
                        <p class="font-extrabold text-slate-900 text-sm">{{ $phone }}</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">WhatsApp Help</span>
                        <p class="font-extrabold text-slate-900 text-sm">{{ $whatsapp }}</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Support Email</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate">{{ $email }}</p>
                    </div>

                    <div class="bg-[#FAFAF9] p-4 rounded-2xl border border-slate-200/80 space-y-1">
                        <span class="text-xs font-mono font-bold text-teal-600 uppercase">Campus Location</span>
                        <p class="font-extrabold text-slate-900 text-sm truncate">{{ $address }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[420px] bg-slate-950">
                    <img src="{{ media_url($heroImage, 'images/academy_campus.png') }}" alt="Campus Support Desk" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-teal-600 text-white p-5 rounded-2xl shadow-2xl flex items-center gap-3">
                    <span class="text-3xl">{{ $cardIcon }}</span>
                    <div>
                        <p class="font-heading font-black text-lg">{{ $cardTitle }}</p>
                        <p class="text-xs font-mono text-teal-100">{{ $cardSubtitle }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form & Map --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-xl h-96 relative">
            <iframe title="Campus Location Map" src="{{ $mapUrl }}" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
            <div class="space-y-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900">{{ $formTitle }}</h2>
                <p class="text-xs font-mono text-slate-500">{{ $formSubtitle }}</p>
            </div>

            <div id="contactAlert" class="hidden p-4 rounded-2xl text-xs font-semibold"></div>

            <form id="contactForm" action="{{ route('ajax.contact.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" name="full_name" placeholder="e.g. Ahmed Mohamed" required class="input-mobile">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="email" placeholder="name@example.com" required class="input-mobile">
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+20 100..." class="input-mobile">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Subject of Inquiry</label>
                    <input type="text" name="subject" placeholder="e.g. Grade 10 Mathematics Enrollment Inquiry" class="input-mobile">
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">Message</label>
                    <textarea name="message" rows="4" placeholder="How can we help you?" required class="w-full bg-white border border-slate-200 rounded-xl p-4 text-[16px] font-medium focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-500/20"></textarea>
                </div>

                <button type="submit" class="btn-mobile-lg btn-lift bg-teal-600 hover:bg-teal-700 text-white font-extrabold shadow-lg shadow-teal-600/20 cursor-pointer touch-press">
                    Submit Inquiry &rarr;
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');
    const contactAlert = document.getElementById('contactAlert');
    if (!contactForm) return;

    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        contactAlert.classList.add('hidden');
        const formData = new FormData(contactForm);

        try {
            const res = await fetch(contactForm.action, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            contactAlert.className = `p-4 rounded-2xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            contactAlert.textContent = data.message;
            contactAlert.classList.remove('hidden');

            if (data.success) {
                contactForm.reset();
            }
        } catch (err) {
            contactAlert.className = 'p-4 rounded-2xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            contactAlert.textContent = 'Network error. Please try again.';
            contactAlert.classList.remove('hidden');
        }
    });
});
</script>
@endsection

```

---

## File: `resources/views/pages/course-details.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $cTitle = $course ? $course->title : 'Full-Stack Systems & Deep Learning Architecture';
    $cDesc = $course ? ($course->description ?: 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.') : 'Master end-to-end Python microservices, distributed database indexing, computer vision algorithms, and neural network tuning.';
    $cTeacher = $course?->teacher?->user?->name ?: 'Dr. Elena Rostova';
    $cSubject = $course?->subject?->name ?: 'Programming & AI';
    $cId = $course ? $course->id : 1;
@endphp

@php
    $courseJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "Course",
                "@id" => url()->current() . "#course",
                "name" => $cTitle,
                "description" => $cDesc,
                "provider" => [
                    "@type" => "EducationalOrganization",
                    "name" => "Elite Academy LMS",
                    "sameAs" => url('/')
                ],
                "instructor" => [
                    "@type" => "Person",
                    "name" => $cTeacher,
                    "jobTitle" => "Senior Accredited Educator",
                    "worksFor" => [
                        "@type" => "EducationalOrganization",
                        "name" => "Elite Academy LMS"
                    ]
                ],
                "educationalLevel" => "Secondary K-12 & Thanawya Amma Accredited",
                "inLanguage" => app()->getLocale(),
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.9",
                    "reviewCount" => "342",
                    "bestRating" => "5"
                ],
                "offers" => [
                    "@type" => "Offer",
                    "category" => "Educational Track",
                    "priceCurrency" => "EGP",
                    "price" => (string) ($course?->price ?? '0'),
                    "availability" => "https://schema.org/InStock"
                ]
            ],
            [
                "@type" => "BreadcrumbList",
                "@id" => url()->current() . "#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => app()->getLocale() === 'ar' ? 'المواد الدراسية' : 'Subjects',
                        "item" => route('subjects')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 3,
                        "name" => $cSubject,
                        "item" => url()->current()
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 4,
                        "name" => $cTitle,
                        "item" => url()->current()
                    ]
                ]
            ],
            [
                "@type" => "FAQPage",
                "@id" => url()->current() . "#faq",
                "mainEntity" => [
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل تشمل هذه الدورة حصصاً تفاعلية مباشرة وتسجيلات؟' : 'Does this course include live interactive sessions and recordings?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، تشمل الدورة حصص بث مباشر أسبوعية تفاعلية مع إمكانية مشاهدة التسجيلات المشفرة في أي وقت.' 
                                : 'Yes, the course includes weekly live interactive streams along with full access to encrypted high-definition session recordings.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل يتم تقديم شهادة إتمام معتمدة؟' : 'Is an accredited certificate issued upon completion?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، يحصل الطالب على شهادة معتمدة من أكاديمية إيليت بعد اجتياز الاختبارات والواجبات بنجاح.' 
                                : 'Yes, students earn a verified certificate of completion from Elite Academy upon successfully submitting all assignments and passing final assessments.'
                        ]
                    ]
                ]
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($courseJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<section class="py-12 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.subjects'), 'route' => 'subjects'],
                ['label' => $cSubject],
                ['label' => $cTitle],
            ]
        ])

        <div class="space-y-3 max-w-3xl">
            <div class="flex items-center gap-3">
                <span class="bg-teal-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ $cSubject }}</span>
                @if($isEnrolled ?? false)
                    <span class="bg-teal-500 text-white text-xs font-bold px-3 py-1 rounded-full">✓ Enrolled Course</span>
                @else
                    <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">▶ Free Demo Available</span>
                @endif
            </div>

            <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                {{ $cTitle }}
            </h1>

            <p class="text-slate-300 text-base leading-relaxed">
                {{ $cDesc }}
            </p>

            <div class="flex flex-wrap items-center gap-6 pt-4 text-xs font-medium text-slate-300">
                <span>⏱️ Duration: 16 Weeks</span>
                <span>👥 Teacher: {{ $cTeacher }}</span>
                <span>⭐ Rating: 4.9/5</span>
                <span>🏆 Accredited Certification</span>
            </div>
        </div>
    </div>
</section>

{{-- Course Content Body Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8 space-y-12">
                {{-- Free Demo Section --}}
                @php
                    $isArabicTitle = preg_match('/\p{Arabic}/u', $cTitle);
                    $demoTitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'الحصة الأولى التجريبية: ' . $cTitle 
                        : 'Watch Sample Lesson 1.1: ' . $cTitle;
                    $demoSubtitle = ($isArabicTitle || app()->getLocale() === 'ar')
                        ? 'شاهد الحصة المجانية الأولى واستكشف أسلوب الشرح التفاعلي والتطبيقات العملية قبل الاشتراك.'
                        : 'Get a glimpse of our hands-on teaching style before committing. This sample demo covers core concepts and interactive exercises.';
                    $videoData = $course ? $course->getVideoEmbedData() : ['type' => 'mp4', 'embed_url' => asset('videos/physics_demo.mp4')];
                    $posterImage = $course && $course->image ? media_url($course->image, 'images/course_ai.png') : asset('images/course_ai.png');
                @endphp
                <div id="demo" class="bg-gradient-to-br from-teal-900 via-slate-900 to-teal-950 text-white rounded-3xl p-8 border border-teal-500/40 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-teal-500/30 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-orange-400">{{ __('Interactive Preview') }}</span>
                        </div>
                        <span class="text-xs font-mono bg-teal-800/80 text-teal-200 px-3 py-1 rounded-full border border-teal-500/30">{{ __('Free Demo Lesson') }}</span>
                    </div>

                    <div class="space-y-3">
                        <h2 class="font-heading font-extrabold text-2xl text-white">
                            {{ $demoTitle }}
                        </h2>
                        <p class="text-slate-300 text-xs leading-relaxed">
                            {{ $demoSubtitle }}
                        </p>
                    </div>

                    <x-secure-video-player :course="$course" :videoData="$videoData" :posterImage="$posterImage" :title="$cTitle" />
                </div>

                {{-- Interactive Curriculum Lifetime Timeline Component --}}
                @include('components.curriculum-timeline', [
                    'sessions' => $course?->sessions,
                    'title' => 'Course Curriculum & Module Lifetime Roadmap',
                    'subtitle' => 'Structured timeline of lectures, live coding labs, and homework assignments'
                ])
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-4 space-y-6">
                @php
                    $nextLiveSession = $course?->liveSessions?->where('scheduled_at', '>=', now())->sortBy('scheduled_at')->first();
                    $targetDate = $nextLiveSession ? $nextLiveSession->scheduled_at->toIso8601String() : now()->addDays(3)->setTime(18, 0)->toIso8601String();
                    $sessionTitle = $nextLiveSession ? $nextLiveSession->title : null;
                @endphp

                {{-- Live Countdown Timer Widget Component --}}
                @include('components.course-countdown-timer', [
                    'targetDate' => $targetDate,
                    'sessionTitle' => $sessionTitle,
                    'title' => app()->getLocale() === 'ar' ? 'عداد البث المباشر القادم' : 'Live Cohort Start Timer',
                    'subtitle' => app()->getLocale() === 'ar' ? 'الوقت المتبقي لإنطلاق حصة البث المباشر التفاعلية' : 'Countdown to upcoming interactive live stream'
                ])

                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="space-y-2 border-b border-slate-100 pb-4">
                        <span class="text-xs font-mono font-extrabold text-slate-400 uppercase">Tuition Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">$290 <span class="text-xs text-slate-400 font-normal">/ term</span></p>
                    </div>

                    <div id="enrollAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

                    @auth
                        @if($isEnrolled ?? false)
                            <a href="{{ route('student-portal') }}" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                                {{ app()->getLocale() === 'ar' ? 'مشترك في هذا الكورس ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Course ✓ — Go to Student Portal &rarr;' }}
                            </a>
                        @else
                            <button id="btnEnroll" class="w-full text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md transition-all cursor-pointer">
                                {{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">
                            {{ app()->getLocale() === 'ar' ? 'سجل الدخول للتسجيل في الكورس' : 'Log In to Enroll' }}
                        </a>
                    @endauth

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Teacher</p>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/instructor_portrait.png') }}" alt="{{ $cTeacher }}" class="w-10 h-10 rounded-xl object-cover border border-teal-500">
                            <div>
                                <a href="{{ route('teachers') }}" class="text-xs font-bold text-slate-900 hover:text-teal-600">{{ $cTeacher }}</a>
                                <p class="text-[11px] text-slate-500">Senior Academic Lead</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@auth
@if(! ($isEnrolled ?? false))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnEnroll');
    const alertBox = document.getElementById('enrollAlert');
    if (!btn) return;

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = "{{ app()->getLocale() === 'ar' ? 'جاري التسجيل...' : 'Enrolling...' }}";

        try {
            const res = await fetch("{{ route('ajax.course.enroll', $cId) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (window.Toast) {
                if (data.success) {
                    window.Toast.success(data.message || 'Enrolled in course successfully!');
                } else {
                    window.Toast.error(data.message || 'Enrollment failed.');
                }
            }

            alertBox.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
            alertBox.textContent = data.message;
            alertBox.classList.remove('hidden');

            if (data.success) {
                const portalUrl = "{{ route('student-portal') }}";
                const linkText = "{{ app()->getLocale() === 'ar' ? 'تم التسجيل بنجاح ✓ — الذهاب لبوابة الطالب ←' : 'Enrolled Successfully ✓ — Go to Student Portal &rarr;' }}";
                btn.outerHTML = `<a href="${portalUrl}" class="btn-lift w-full inline-block text-center py-3.5 px-6 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md transition-all">${linkText}</a>`;
            } else {
                btn.disabled = false;
                btn.textContent = "{{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}";
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = "{{ app()->getLocale() === 'ar' ? 'التسجيل في الكورس الآن 🚀' : 'Enroll in Course Now 🚀' }}";
            alertBox.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
            alertBox.textContent = 'Network error. Please try again.';
            alertBox.classList.remove('hidden');
        }
    });
});
</script>
@endif
@endauth
@endsection

```

---

## File: `resources/views/pages/courses.blade.php`

```blade
@extends('layouts.app')

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

@section('content')
<section class="py-12 md:py-16 bg-white border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.section-header', [
            'badge' => $isArabic ? 'دليل الكورسات والمناهج • الحصة الأولى مجانية' : 'Curriculum Catalog • Free Demos Included',
            'badgeColor' => 'orange',
            'title' => $isArabic ? 'الكورسات التعليمية والمقررات المعتمدة' : 'Featured Learning Courses',
            'subtitle' => $isArabic ? 'تصفح البرامج الدراسية المعتمدة. كل كورس يتضمن حصة تجريبية مجانية قبل الاشتراك.' : 'Filter through our accredited programs. Every course includes a free sample demo lesson before enrollment.',
            'centered' => true,
        ])

        {{-- Category Filter Chips --}}
        <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
            <a href="{{ route('courses') }}"
               @class([
                   'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                   'bg-teal-600 text-white shadow-md border-teal-600' => empty($selectedCategory),
                   'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' => ! empty($selectedCategory),
               ])>
                {{ $isArabic ? 'جميع التخصصات' : 'All Disciplines' }}
            </a>
            @php
                $categories = [
                    'Programming' => $isArabic ? 'البرمجة' : 'Programming',
                    'AI & Science' => $isArabic ? 'العلوم والتكنولوجيا' : 'AI & Science',
                    'Mathematics' => $isArabic ? 'الرياضيات' : 'Mathematics',
                    'Languages' => $isArabic ? 'اللغات' : 'Languages',
                    'Design' => $isArabic ? 'التصميم' : 'Design',
                    'Physics' => $isArabic ? 'الفيزياء' : 'Physics',
                ];
            @endphp
            @foreach ($categories as $catKey => $catLabel)
                @php $isActive = strtolower($selectedCategory ?? '') === strtolower($catKey); @endphp
                <a href="{{ route('courses', ['category' => $catKey]) }}"
                   @class([
                       'px-4 py-2 rounded-full text-xs font-bold transition-all border',
                       'bg-teal-600 text-white shadow-md border-teal-600' => $isActive,
                       'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200' => ! $isActive,
                   ])>
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Courses Grid --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if(isset($courses) && count($courses) > 0)
                @foreach ($courses as $c)
                    @php
                        $isModel = $c instanceof \App\Models\Course;
                        $slug = $isModel ? $c->slug : 'course-details';
                        $courseId = $isModel ? $c->id : null;
                        $cardData = [
                            'image' => $isModel ? ($c->image ?: 'images/course_ai.png') : ($c['image'] ?? 'images/course_ai.png'),
                            'category' => $isModel ? ($c->subject?->name ?: ($isArabic ? 'العلوم' : 'Science')) : ($c['category'] ?? ($isArabic ? 'العلوم' : 'Science')),
                            'categoryBg' => 'bg-teal-600',
                            'instructor' => $isModel ? ($c->teacher?->user?->name ?: ($isArabic ? 'أستاذ المادة' : 'Dr. Teacher')) : ($c['instructor'] ?? ($isArabic ? 'أستاذ المادة' : 'Dr. Teacher')),
                            'instructorPhoto' => 'images/instructor_portrait.png',
                            'instructorBorder' => 'border-teal-500',
                            'title' => $isModel ? $c->title : ($c['title'] ?? 'Course Title'),
                            'description' => $isModel ? ($c->description ?: ($isArabic ? 'مقرر تعليمي تفاعلي شامل للمرحلة الثانوية.' : 'Interactive curriculum with hands-on labs.')) : ($c['description'] ?? 'Course description'),
                            'price' => '$290',
                            'route' => route('course-details', ['slug' => $slug]),
                            'course_id' => $courseId,
                            'hasFreeDemo' => $isModel ? (bool) $c->has_free_demo : true,
                            'isEnrolled' => $courseId ? in_array((int) $courseId, array_map('intval', $enrolledCourseIds ?? [])) : false,
                        ];
                    @endphp
                    @include('components.course-card', $cardData)
                @endforeach
            @else
                <div class="col-span-3 text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📚</div>
                    <h3 class="font-bold text-lg text-slate-800">{{ $isArabic ? 'لا توجد كورسات في هذا التخصص حالياً' : 'No Courses Match Selected Category' }}</h3>
                    <p class="text-xs text-slate-500 mt-1 mb-4">{{ $isArabic ? 'جرب اختيار "جميع التخصصات" لمشاهدة كافة المناهج المتاحة.' : 'Try selecting "All Disciplines" or another category.' }}</p>
                    <a href="{{ route('courses') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
                        {{ $isArabic ? 'عرض جميع الكورسات' : 'View All Courses' }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Custom Clean Responsive Pagination Controls --}}
        @if(method_exists($courses, 'hasPages') && $courses->hasPages())
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/80">
                <div class="text-xs font-bold text-slate-500">
                    {{ $isArabic ? 'عرض' : 'Showing' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->firstItem() }}</span>
                    {{ $isArabic ? 'إلى' : 'to' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->lastItem() }}</span>
                    {{ $isArabic ? 'من إجمالي' : 'of' }}
                    <span class="font-extrabold text-slate-800">{{ $courses->total() }}</span>
                    {{ $isArabic ? 'كورسات' : 'courses' }}
                </div>

                <div class="flex items-center gap-2">
                    {{-- Previous Page Link --}}
                    @if ($courses->onFirstPage())
                        <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 cursor-not-allowed">
                            ◀ {{ $isArabic ? 'السابق' : 'Previous' }}
                        </span>
                    @else
                        <a href="{{ $courses->previousPageUrl() }}" class="btn-lift px-4 py-2 text-xs font-extrabold text-teal-700 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-xl border border-teal-200/80 transition-all shadow-xs">
                            ◀ {{ $isArabic ? 'السابق' : 'Previous' }}
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="flex items-center gap-1.5 px-2">
                        @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                            @if ($page == $courses->currentPage())
                                <span class="w-9 h-9 rounded-xl bg-teal-600 text-white font-black text-xs flex items-center justify-center shadow-md shadow-teal-600/30">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 flex items-center justify-center transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($courses->hasMorePages())
                        <a href="{{ $courses->nextPageUrl() }}" class="btn-lift px-4 py-2 text-xs font-extrabold text-teal-700 bg-teal-50 hover:bg-teal-600 hover:text-white rounded-xl border border-teal-200/80 transition-all shadow-xs">
                            {{ $isArabic ? 'التالي' : 'Next' }} ▶
                        </a>
                    @else
                        <span class="px-4 py-2 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl border border-slate-200 cursor-not-allowed">
                            {{ $isArabic ? 'التالي' : 'Next' }} ▶
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/event-details.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 bg-slate-900 text-white border-b border-slate-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.events'), 'route' => 'events'],
                ['label' => 'Computer Vision Lab'],
            ]
        ])

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="px-3.5 py-1 rounded-full bg-orange-500 text-white text-xs font-mono font-bold">
                        Live Workshop
                    </span>
                    <span class="px-3.5 py-1 rounded-full bg-teal-800/90 text-teal-200 text-xs font-mono font-semibold border border-teal-500/30">
                        Sat, Oct 12 • 10:00 AM EST
                    </span>
                </div>

                <h1 class="font-heading text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Applied Computer Vision & PyTorch Workshop
                </h1>

                <p class="text-slate-300 text-base leading-relaxed max-w-2xl">
                    A hands-on 3-hour intensive lab building real-time object tracking algorithms and neural networks with Marcus Vance.
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs font-mono text-slate-300">
                    <span>📍 Location: Main Innovation Lab & Zoom Live</span>
                    <span>🎟️ Capacity: 50 Students</span>
                    <span>🔥 Seats Left: 12 Remaining</span>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-white rounded-3xl p-6 text-slate-900 border border-slate-200/80 shadow-2xl space-y-4">
                    <div class="space-y-1">
                        <span class="text-xs font-mono uppercase tracking-wider text-slate-400">Registration Fee</span>
                        <p class="font-mono text-3xl font-extrabold text-slate-900">FREE <span class="text-xs text-teal-600 font-normal">(Verified Students)</span></p>
                    </div>

                    <a href="#register" class="btn-lift w-full block text-center py-3.5 px-6 font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-xl shadow-md">
                        Reserve Seat Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Content Body --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8 space-y-12">
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Workshop Overview</h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Computer vision is driving key innovations in autonomous vehicles, medical diagnostics, and spatial computing. In this workshop, students will construct real-time video analytics pipelines using OpenCV and PyTorch.
                    </p>
                </div>

                {{-- Agenda --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Interactive Agenda Schedule</h2>
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 space-y-1">
                            <span class="text-xs font-mono font-bold text-teal-600">10:00 AM - 11:00 AM</span>
                            <h3 class="font-heading font-bold text-base text-slate-900">Convolutional Filters & Edge Detection</h3>
                            <p class="text-xs text-slate-600">Understanding matrix transformations, Sobel filters, and spatial convolutions.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/80 space-y-1">
                            <span class="text-xs font-mono font-bold text-teal-600">11:00 AM - 12:30 PM</span>
                            <h3 class="font-heading font-bold text-base text-slate-900">Live PyTorch Neural Object Tracking</h3>
                            <p class="text-xs text-slate-600">Implementing pretrained YOLO models and custom bounding box classification.</p>
                        </div>
                    </div>
                </div>

                {{-- Keynote Leaders --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-2xs space-y-4">
                    <h2 class="font-heading font-bold text-2xl text-slate-900">Workshop Keynote Leaders</h2>
                    <div class="flex items-center gap-4 pt-2">
                        <img src="{{ asset('images/instructor_male.png') }}" alt="Marcus Vance" class="w-16 h-16 rounded-2xl object-cover border-2 border-purple-500">
                        <div>
                            <h3 class="font-heading font-bold text-lg text-slate-900">Marcus Vance</h3>
                            <p class="text-xs font-semibold text-purple-600">AI Research Lead • Neural Networks Chair</p>
                            <p class="text-xs text-slate-500 mt-1">10+ years in computer vision research and deep learning models.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Reservation Form --}}
            <div class="lg:col-span-4 space-y-6">
                <div id="register" class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-lg sticky top-24 space-y-6">
                    <div class="space-y-2">
                        <h3 class="font-heading font-bold text-xl text-slate-900">Reserve Your Seat</h3>
                        <p class="text-xs text-slate-500">Fill in your details to receive access credentials.</p>
                    </div>

                    <form action="{{ route('events') }}" method="GET" class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Full Name</label>
                            <input type="text" placeholder="e.g. Alex Johnson" required class="input-mobile">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                            <input type="email" placeholder="alex@example.com" required class="input-mobile">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Attendance Mode</label>
                            <select class="input-mobile cursor-pointer">
                                <option>In-Person (Campus Lab)</option>
                                <option>Live Zoom Stream</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-lift w-full py-3.5 px-4 text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-md">
                            Confirm Seat Registration
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/events.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 md:space-y-12">

        {{-- Section Header --}}
        @include('components.section-header', [
            'badge' => 'CAMPUS EVENTS & WORKSHOPS',
            'title' => 'Upcoming <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Academic Events</span>',
            'subtitle' => 'Join interactive live workshops, STEM competitions, and faculty revision sessions.',
            'centered' => true,
        ])

        {{-- Events Feed --}}
        @php
            $events = [
                [
                    'image' => 'images/hero_student.png',
                    'category' => 'Revision Workshop',
                    'categoryColor' => 'bg-teal-600',
                    'title' => 'Grade 10 Mathematics Live Final Exam Revision',
                    'excerpt' => 'Join Dr. Ahmed Hassan for an intensive 3-hour live revision workshop covering Algebra, Trigonometry, and key past exam questions to prepare for final term examinations.',
                    'author' => 'Dr. Ahmed Hassan',
                    'date' => 'Nov 15, 2026',
                    'readTime' => 'Live at 5:00 PM',
                    'route' => route('event-details'),
                ],
                [
                    'image' => 'images/course_ai.png',
                    'category' => 'STEM Competition',
                    'categoryColor' => 'bg-orange-600',
                    'title' => 'Annual Robotics & AI Student Hackathon 2026',
                    'excerpt' => 'Showcase your engineering skills at our New Cairo STEM campus. Teams will build autonomous robots and AI classification models with prizes sponsored by top tech partners.',
                    'author' => 'Omar Khaled',
                    'date' => 'Dec 01, 2026',
                    'readTime' => 'Full Day Event',
                    'route' => route('event-details'),
                ],
            ];
        @endphp

        <div class="space-y-8 md:space-y-12">
            @foreach ($events as $e)
                @include('components.article-card', $e)
                @if (! $loop->last)
                    <hr class="border-t border-slate-200/80">
                @endif
            @endforeach
        </div>

    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/faq.blade.php`

```blade
@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';

    $faqCategories = [
        [
            'title' => $isAr ? 'القبول والتسجيل والاشتراكات' : 'Admissions, Enrollment & Packages',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يتم الاشتراك في الكورسات أو باقات الحصص؟' : 'How do I enroll in a course or purchase session packages?',
                    'a' => $isAr 
                        ? 'تصفح قائمة المواد ثم اختر الكورس أو الباقة المطلوبة. اضغط على "اشتراك" واختر طريقة الدفع المناسبة (فوري، كروت ائتمان، محفظة إلكترونية). تتاح حصة تجريبية مجانية لجميع الكورسات قبل الدفع.' 
                        : 'Browse our subjects catalog and select your target course. Click "Enroll Now" and select your preferred payment method (Fawry, Credit Card, Mobile Wallet). A free demo lesson is available for every course.'
                ],
                [
                    'q' => $isAr ? 'هل تتاح حصص تجريبية مجانية قبل الشراء؟' : 'Are free demo lessons available before purchasing?',
                    'a' => $isAr 
                        ? 'نعم، توفر المنصة حصة تجريبية مجانية كاملة لكل كورس حتى يتمكن الطالب وولي الأمر من تجربة الشرح التفاعلي وجودة البث.' 
                        : 'Yes! Every course includes a complete free preview demo lesson so students and parents can evaluate the teaching style and stream quality.'
                ],
                [
                    'q' => $isAr ? 'ما هي سياسة الاسترداد وإلغاء الاشتراك؟' : 'What is the refund and cancellation policy?',
                    'a' => $isAr 
                        ? 'يمكن طلب استرداد المبلغ خلال 7 أيام من الشراء بشرط عدم مشاهدة أكثر من حصة واحدة أو إجراء اختبار نضج.' 
                        : 'Full refunds can be requested within 7 days of purchase provided no more than 1 live session or assignment has been consumed.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'تقنية البث المباشر والحماية الأكاديمية' : 'Live Session Streaming & Anti-Piracy Tech',
            'items' => [
                [
                    'q' => $isAr ? 'كيف تعمل غرفة الاجتماعات والبث المباشر داخل المنصة؟' : 'How does the in-system live meeting room operate?',
                    'a' => $isAr 
                        ? 'يتم الدخول للبث بنقرة واحدة داخل المنصة عبر زوم أو جيتسي دون الحاجة لتنزيل تطبيقات خارجية، مع علامة مائية أمنية ديناميكية لمنع تصوير الشاشة.' 
                        : 'Students join live classes directly inside their dashboard via embedded Zoom/Jitsi stream frames, featuring dynamic security watermarks to prevent piracy.'
                ],
                [
                    'q' => $isAr ? 'ماذا يحدث إذا فاتني موعد البث المباشر؟' : 'What happens if I miss a live stream session?',
                    'a' => $isAr 
                        ? 'تتم أرشفة جميع الحصص المباشرة فور انتهائها وتتوفر بتنسيق HD عالي الجودة داخل حساب الطالب لمشاهدتها في أي وقت طوال فترة الكورس.' 
                        : 'All live streams are automatically recorded in Full HD and uploaded to the student portal for unlimited re-watching throughout the semester.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'نظام الواجبات والتصحيح التلقائي' : 'Interactive Assignment Solver & Auto-Grading',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يتم حل الواجبات وتصحيحها؟' : 'How are assignments submitted and evaluated?',
                    'a' => $isAr 
                        ? 'يستخدم الطالب واجهة حل الواجبات التفاعلية خطوة بخطوة مع حفظ مسودات الإجابات تلقائياً. يتم الاحتساب الفوري للدرجات وتوفير شروحات للإجابات الصحيحة.' 
                        : 'Students use our step-by-step solver interface with auto-saved drafts. Scores are calculated instantly with detailed solution walkthroughs.'
                ],
                [
                    'q' => $isAr ? 'ماذا يحدث في حال انقطاع اتصال الإنترنت أثناء الاختبار؟' : 'What happens if my internet disconnects during a quiz?',
                    'a' => $isAr 
                        ? 'تحفظ الواجبات إجاباتك محلياً في الذاكرة المؤقتة، وتتم مزامنتها تلقائياً مع الخادم فور إعادة الاتصال دون فقدان أي بيانات.' 
                        : 'Draft answers are cached locally in your browser memory and synchronized automatically when internet connectivity is restored.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'بوابة ولي الأمر والإشعارات' : 'Parent Portal & Real-time Progress Tracking',
            'items' => [
                [
                    'q' => $isAr ? 'كيف يمكن لولي الأمر متابعة الطالب بالدقيقة؟' : 'How do parents monitor real-time student performance?',
                    'a' => $isAr 
                        ? 'يقوم ولي الأمر بربط حساب الابن عبر بوابة ولي الأمر باستخدام رقم الهاتف لمشاهدة سجل الحضور بالدقيقة، درجات الواجبات، وتنبيهات الغياب.' 
                        : 'Parents link student profiles on the Parent Portal using phone verification to access live attendance logs, homework grades, and absence alerts.'
                ],
                [
                    'q' => $isAr ? 'كيف يتم تلقي إشعارات الحصص والواجبات؟' : 'How are notification alerts delivered?',
                    'a' => $isAr 
                        ? 'ترسل المنصة إشعارات برمجية خفيفة (FCM Push Notifications) وتنبيهات لحظية عند اقتراب مواعيد الحصص المباشرة أو موعد تسليم الواجبات.' 
                        : 'We send native browser FCM push alerts and real-time dashboard notifications 30 minutes before live streams and 24 hours prior to homework deadlines.'
                ]
            ]
        ],
        [
            'title' => $isAr ? 'الشهادات والاعتماد الأكاديمي' : 'Accreditation & Certificate Verification',
            'items' => [
                [
                    'q' => $isAr ? 'هل الشهادات الصادرة موثقة وقابلة للتحقق؟' : 'Are completion certificates verified and official?',
                    'a' => $isAr 
                        ? 'نعم، يحصل الخريجون على شهادة إتمام رقمية تحمل رمز QR ورمز تحقق فريد يمكن الاستعلام عنه رسمياً من موقع المنصة.' 
                        : 'Yes! Graduates receive encrypted digital certificates featuring QR codes and unique verification IDs for institutional validation.'
                ]
            ]
        ]
    ];

    $allFaqItems = [];
    foreach ($faqCategories as $cat) {
        foreach ($cat['items'] as $item) {
            $allFaqItems[] = [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a']
                ]
            ];
        }
    }

    $faqPageJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "BreadcrumbList",
                "@id" => route('faq') . "#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => $isAr ? 'الرئيسية' : 'Home',
                        "item" => url('/')
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $isAr ? 'الأسئلة الشائعة' : 'FAQ',
                        "item" => route('faq')
                    ]
                ]
            ],
            [
                "@type" => "FAQPage",
                "@id" => route('faq') . "#faq",
                "mainEntity" => $allFaqItems
            ]
        ]
    ];
@endphp

@section('content')
<script type="application/ld+json">
{!! json_encode($faqPageJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<section class="py-12 md:py-16 bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => $isAr ? 'الأسئلة الشائعة والمركز المعرفي' : 'Frequently Asked Questions'],
            ]
        ])

        <div class="text-center max-w-3xl mx-auto space-y-4 pt-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/40">
                <span>💡</span> {{ $isAr ? 'المركز المعرفي والمساعدة الشاملة' : 'Knowledge Base & Support Hub' }}
            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
                {{ $isAr ? 'كيف يمكننا مساعدتك اليوم؟' : 'Frequently Asked Questions' }}
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                {{ $isAr 
                    ? 'دليل استرشادي شامل يجيب على كافة التساؤلات الفنية والأكاديمية حول الحصص، التصحيح، والباقات.' 
                    : 'Everything you need to know about our curriculums, live sessions, auto-grading, parent monitoring, and accredited track certificates.' 
                }}
            </p>
        </div>
    </div>
</section>

{{-- Categorized Accordions Section --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]" itemscope itemtype="https://schema.org/FAQPage">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        @foreach($faqCategories as $category)
            <div class="space-y-4">
                <h2 class="font-heading font-bold text-xl sm:text-2xl text-slate-900 border-b border-slate-200/90 pb-3 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                    {{ $category['title'] }}
                </h2>

                <div class="space-y-3">
                    @foreach($category['items'] as $faq)
                        @include('components.faq-item', [
                            'question' => $faq['q'],
                            'answer' => $faq['a']
                        ])
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-6">
            <div class="space-y-1">
                <h3 class="font-heading font-bold text-lg text-slate-900">
                    {{ $isAr ? 'لم تجد الإجابة التي تبحث عنها؟' : 'Still have unanswered questions?' }}
                </h3>
                <p class="text-xs text-slate-600">
                    {{ $isAr ? 'يمكنك التواصل المباشر مع فريق الدعم الأكاديمي عبر الهاتف أو النموذج الإلكتروني.' : 'Reach out directly to our academic support staff for one-on-one assistance.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="btn-lift px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-md transition-all">
                ✉️ {{ $isAr ? 'إرسال استفسار للدعم' : 'Submit Support Ticket' }}
            </a>
        </div>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/home.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $siteJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "WebSite",
                "@id" => url('/') . "/#website",
                "url" => url('/'),
                "name" => "Elite Academy LMS",
                "description" => "Leading K-12 Interactive Learning Management System & Online Tutoring Platform in Egypt",
                "publisher" => [
                    "@id" => url('/') . "/#organization"
                ],
                "inLanguage" => app()->getLocale(),
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => url('/courses') . "?search={search_term_string}",
                    "query-input" => "required name=search_term_string"
                ]
            ],
            [
                "@type" => "EducationalOrganization",
                "@id" => url('/') . "/#organization",
                "name" => "Elite Academy LMS",
                "alternateName" => "أكاديمية إيليت التعليمية",
                "url" => url('/'),
                "logo" => asset('images/logo.png'),
                "image" => asset('images/academy_campus.png'),
                "description" => "Ministry-accredited interactive K-12 educational platform providing live classes, auto-graded assignments, and verified tutoring in Egypt.",
                "telephone" => "+201000000000",
                "email" => "support@elite-academy.com",
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => "Academic Center Tower, New Cairo",
                    "addressLocality" => "Cairo",
                    "addressCountry" => "EG"
                ],
                "contactPoint" => [
                    [
                        "@type" => "ContactPoint",
                        "telephone" => "+201000000000",
                        "contactType" => "customer service",
                        "availableLanguage" => ["Arabic", "English"],
                        "areaServed" => "EG"
                    ]
                ],
                "aggregateRating" => [
                    "@type" => "AggregateRating",
                    "ratingValue" => "4.9",
                    "reviewCount" => "1280",
                    "bestRating" => "5",
                    "worstRating" => "1"
                ],
                "sameAs" => [
                    "https://facebook.com/eliteacademy",
                    "https://twitter.com/eliteacademy",
                    "https://instagram.com/eliteacademy"
                ]
            ],
            [
                "@type" => "BreadcrumbList",
                "@id" => url('/') . "/#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home',
                        "item" => url('/')
                    ]
                ]
            ],
            [
                "@type" => "FAQPage",
                "@id" => url('/') . "/#faq",
                "mainEntity" => [
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'ما هي أكاديمية إيليت التعليمية؟' : 'What is Elite Academy LMS?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'أكاديمية إيليت هي المنصة التعليمية الرقمية الرائدة في مصر لحصص البث المباشر المعتمدة، متابعة أولياء الأمور، والحل التفاعلي للواجبات بصفة لحظية.' 
                                : 'Elite Academy LMS is Egypt premier accredited K-12 interactive tutoring platform featuring live streaming, instant assignment solver, and real-time parent progress tracking.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'كيف تعمل حصص البث المباشر والتفاعلي؟' : 'How do live streaming interactive sessions work?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'يتصل الطلاب بالبث التفاعلي عبر زوم أو جيتسي مع علامة مائية أمنية ديناميكية لحماية المحتوى وتسجيل حضور تلقائي بالدقيقة.' 
                                : 'Students connect to encrypted live sessions powered by Zoom/Jitsi with dynamic security watermarking and minute-by-minute automatic attendance tracking.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'هل يتم تصحيح الواجبات والاختبارات تلقائياً؟' : 'Are assignments and quizzes auto-graded?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'نعم، تشمل المنصة واجهة تفاعلية لحل الواجبات مع حفظ المسودات تلقائياً وإمكانية التصحيح الفوري وإرسال التغذية الراجعة.' 
                                : 'Yes, assignments feature step-by-step solver interfaces with offline draft auto-saving, automated instant grading, and teacher reviews.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'كيف يمكن لأولياء الأمور متابعة مستوى الطالب؟' : 'How can parents track student academic progress?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'يمكن لولي الأمر ربط حساب الطالب برقم الهاتف عبر بوابة ولي الأمر لمتابعة نسبة الحضور، درجات الاختبارات، وتنبيهات الغياب اللحظية.' 
                                : 'Parents link their child account via phone number verification on the Parent Portal to monitor attendance, quiz scores, and real-time alerts.'
                        ]
                    ],
                    [
                        "@type" => "Question",
                        "name" => app()->getLocale() === 'ar' ? 'ما هي المراحل الدراسية والمواد المتاحة؟' : 'What grade levels and subjects are available?',
                        "acceptedAnswer" => [
                            "@type" => "Answer",
                            "text" => app()->getLocale() === 'ar' 
                                ? 'تغطي المنصة جميع مراحل الثانوية العامة واللغات في مواد الفيزياء، البرمجة، والذكاء الاصطناعي، الكيمياء، والرياضيات.' 
                                : 'We offer accredited tracks across Thanawya Amma secondary grades in Physics, Computer Science & AI, Chemistry, and Advanced Mathematics.'
                        ]
                    ]
                ]
            ]
        ]
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($siteJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
    @php
        $layoutRaw = \App\Models\SiteSetting::get('sections_layout');
        $layout = $layoutRaw ? json_decode($layoutRaw, true) : null;

        $sectionsMap = [
            'hero-slider' => 'pages.home.hero-slider',
            'stats-overlay' => 'pages.home.stats-overlay',
            'why-choose' => 'pages.home.why-choose',
            'about-preview' => 'pages.home.about-preview',
            'subjects-grid' => 'pages.home.subjects-grid',
            'teachers-marquee' => 'pages.home.teachers-marquee',
            'testimonials' => 'pages.home.testimonials',
            'faq-section' => 'pages.home.faq-section',
            'cta_section' => 'pages.home.cta-section',
        ];
    @endphp

    @if(is_array($layout) && count($layout) > 0)
        @foreach($layout as $sec)
            @if(($sec['is_enabled'] ?? true) && isset($sectionsMap[$sec['key']]))
                @include($sectionsMap[$sec['key']])
            @endif
        @endforeach
    @else
        {{-- Full Original Landing Page --}}
        @include('pages.home.hero-slider')
        @include('pages.home.stats-overlay')
        @include('pages.home.why-choose')
        @include('pages.home.about-preview')
        @include('pages.home.subjects-grid')
        @include('pages.home.teachers-marquee')
        @include('pages.home.testimonials')
        @include('pages.home.faq-section')
        @include('pages.home.cta-section')
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
      const slides = ['slide-1', 'slide-2', 'slide-3', 'slide-4'];
      let currentIndex = 0;
      let autoplayInterval = null;

      function goToNextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        const targetRadio = document.getElementById(slides[currentIndex]);
        if (targetRadio) {
          targetRadio.checked = true;
        }
      }

      function startAutoplay() {
        stopAutoplay();
        autoplayInterval = setInterval(goToNextSlide, 6000);
      }

      function stopAutoplay() {
        if (autoplayInterval) {
          clearInterval(autoplayInterval);
          autoplayInterval = null;
        }
      }

      startAutoplay();

      const controls = document.querySelectorAll('label[for^="slide-"]');
      controls.forEach((control, index) => {
        control.addEventListener('click', () => {
          currentIndex = index;
          startAutoplay();
        });
      });
    });
</script>
@endpush

```

---

## File: `resources/views/pages/home/about-preview.blade.php`

```blade
@use('App\Models\SiteSetting')
{{-- About Elite Academy Preview Section --}}
<section class="py-16 md:py-24 lg:py-32 bg-[#FAFAF9] relative overflow-hidden">
    <div class="absolute top-1/2 -left-24 w-[36rem] h-[36rem] bg-teal-400/12 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>
    <div class="absolute bottom-10 right-0 w-[32rem] h-[32rem] bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10 animate-float"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- MOBILE-ONLY ABOUT LAYOUT (<768px) --}}
        <div class="block md:hidden space-y-6 anim-about delay-1">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION') }}
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery') }}
                </h2>
                <p class="text-slate-600 text-sm font-medium leading-relaxed line-clamp-2">
                    {{ SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.') }}
                </p>
            </div>

            <div class="flex flex-row items-center gap-4">
                <div class="w-[40%] shrink-0">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/5] border border-slate-200 shadow-md">
                        <img src="{{ media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.png') }}" alt="Elite Academy Campus" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 text-[9px] font-mono font-bold text-white bg-slate-900/80 px-2 py-0.5 rounded-md backdrop-blur-xs">{{ __('Campus') }}</span>
                    </div>
                </div>

                <div class="w-[60%] space-y-3.5">
                    @foreach ([
                        ['icon' => '🎓', 'title' => __('Expert Mentors'), 'desc' => __('PhD faculty guidance.')],
                        ['icon' => '💻', 'title' => __('Practical Learning'), 'desc' => __('Hands-on lab projects.')],
                        ['icon' => '🌍', 'title' => __('Global Certificates'), 'desc' => __('Accredited diplomas.')],
                        ['icon' => '🚀', 'title' => __('Career Support'), 'desc' => __('Job readiness tracks.')],
                    ] as $feat)
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-base font-bold shrink-0">
                                {{ $feat['icon'] }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading font-extrabold text-xs text-slate-900 leading-tight">{{ $feat['title'] }}</h3>
                                <p class="text-[10px] text-slate-500 font-medium leading-tight truncate">{{ $feat['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <a href="{{ route('about') }}" class="btn-lift w-full inline-flex items-center justify-center gap-2 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 min-h-[52px] rounded-2xl shadow-md touch-press">
                    <span>{{ __('Learn More') }}</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        {{-- DESKTOP / TABLET ABOUT LAYOUT (>=768px) --}}
        <div class="hidden md:grid md:grid-cols-12 md:gap-10 lg:gap-16 md:items-center">
            <div class="md:col-span-6 flex flex-col space-y-6 lg:space-y-8">
                <div class="space-y-3 sm:space-y-4">
                    <span class="anim-about delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                        {{ SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION') }}
                    </span>
                    <h2 class="anim-about delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                        {{ SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery') }}
                    </h2>
                    <p class="anim-about delay-3 sr-img text-slate-600 text-sm sm:text-base font-medium leading-relaxed">
                        {{ SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.') }}
                    </p>
                </div>

                <div class="anim-about delay-4 sr grid grid-cols-2 gap-4 border-t border-slate-200/80 pt-6">
                    @foreach ([
                        ['icon' => '🎓', 'title' => __('Expert Mentors')],
                        ['icon' => '💻', 'title' => __('Practical Learning')],
                        ['icon' => '🌍', 'title' => __('Global Certificates')],
                        ['icon' => '🚀', 'title' => __('Career Support')],
                    ] as $item)
                        <div class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-slate-900 group-hover:text-teal-600 group-hover:scale-110 flex items-center justify-center font-extrabold text-xl transition-all duration-300 shadow-xs flex-shrink-0">
                                {{ $item['icon'] }}
                            </div>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
                                {{ $item['title'] }}
                            </h3>
                        </div>
                    @endforeach
                </div>

                <div class="anim-about delay-5 pt-1">
                    <a href="{{ route('about') }}" class="btn-lift group inline-flex items-center justify-center gap-2.5 text-sm sm:text-base font-extrabold text-white bg-teal-600 hover:bg-teal-700 active:bg-teal-800 px-8 py-4 rounded-2xl shadow-lg shadow-teal-600/20 touch-press">
                        <span>{{ __('Learn More') }}</span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="md:col-span-6 relative anim-about delay-2">
                <div class="relative max-w-lg lg:max-w-none mx-auto">
                    <div class="relative rounded-[36px] p-3 bg-white border border-slate-200/90 shadow-2xl shadow-slate-900/15 group overflow-hidden card-lift">
                        <div class="relative rounded-[26px] overflow-hidden">
                            <img src="{{ media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.png') }}" alt="Elite Academy Campus Photography" class="w-full h-80 sm:h-[400px] lg:h-[440px] object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent pointer-events-none"></div>
                            <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold uppercase tracking-wider text-white bg-slate-950/70 px-3 py-1 rounded-full backdrop-blur-xs border border-white/20">
                                🏫 Campus Life
                            </span>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -right-4 lg:-bottom-8 lg:-right-6 w-40 sm:w-48 h-28 sm:h-32 rounded-2xl overflow-hidden border-2 border-orange-500 shadow-xl z-20 card-lift group">
                        <img src="{{ asset('images/course_ai.png') }}" alt="AI Neural Research" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent pointer-events-none"></div>
                        <span class="absolute bottom-2 left-2.5 text-[9px] font-mono font-bold text-white bg-orange-600/90 px-2 py-0.5 rounded-full backdrop-blur-xs shadow-xs">
                            🧠 AI Research
                        </span>
                    </div>

                    <div class="hidden lg:block absolute top-1/3 -right-6 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-xl z-30 animate-float group">
                        <img src="{{ asset('images/instructor_portrait.png') }}" alt="Faculty Mentor Session" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>

                    <div class="absolute bottom-6 left-6 z-30 glass-card bg-white/92 backdrop-blur-md p-4 rounded-2xl border border-white/80 shadow-2xl shadow-slate-950/15 space-y-2 animate-float max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-100/90 text-teal-700 flex items-center justify-center font-extrabold text-sm icon-rotate flex-shrink-0 shadow-xs">
                                ⭐
                            </div>
                            <div>
                                <p class="text-[9px] font-mono uppercase tracking-wider text-slate-500 font-bold">Trusted by</p>
                                <p class="text-xs font-extrabold text-slate-900">25,000+ Active Students</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[11px]">
                            <div>
                                <p class="text-amber-500 font-extrabold">★★★★★ 4.9</p>
                                <p class="text-[9px] text-slate-500 font-medium">Verified Rating</p>
                            </div>
                            <div>
                                <p class="text-teal-600 font-extrabold">120+ Courses</p>
                                <p class="text-[9px] text-slate-500 font-medium">Accredited Tracks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

```

---

## File: `resources/views/pages/home/cta-section.blade.php`

```blade
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


```

---

## File: `resources/views/pages/home/faq-section.blade.php`

```blade
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';

    $homeFaqs = [
        [
            'q' => $isAr ? 'ما هي أكاديمية إيليت التعليمية وكيف تختلف عن المنصات الأخرى؟' : 'What is Elite Academy LMS and how does it differ from other platforms?',
            'a' => $isAr 
                ? 'أكاديمية إيليت هي المنصة التعليمية الرقمية المعتمدة الأولى في مصر المخصصة لمراحل الثانوية العامة واللغات. تجمع المنصة بين البث المباشر التفاعلي المشفر، الواجبات التفاعلية المصححة تلقائياً، وبوابة ولي الأمر اللحظية لمتابعة الحضور والنتائج.' 
                : 'Elite Academy LMS is Egypt premier accredited interactive K-12 learning platform for secondary tracks. It combines encrypted live streaming sessions, auto-graded interactive assignment solvers, and real-time parent progress portals.'
        ],
        [
            'q' => $isAr ? 'كيف تعمل حصص البث المباشر التفاعلية والأمان ضد التسريب؟' : 'How do live interactive streaming sessions work and what anti-piracy protection is included?',
            'a' => $isAr 
                ? 'يتصل الطالب بالبث المباشر عبر سيرفرات مشفرة مدمجة مع علامة مائية أمنية ديناميكية تحمل اسم الطالب ورقم الهوية وحاسوب الوصول، مما يحظر تسجيل الشاشة أو إعادة النشر تلقائياً.' 
                : 'Students join encrypted live streams with dynamic security watermarking displaying their identity, IP address, and timestamp to protect intellectual property and prevent screen recording.'
        ],
        [
            'q' => $isAr ? 'هل يتم حفظ مسودات الواجبات والاختبارات وتصحيحها فورياً؟' : 'Are assignment drafts saved automatically and scored instantly?',
            'a' => $isAr 
                ? 'نعم، تتميز واجهة حل الواجبات بحفظ المسودات حتى عند انقطاع الإنترنت، مع تصحيح فوري للمرحلة وتقديم تغذية راجعة وشرح بالفيديو لكل سؤال.' 
                : 'Yes, the interactive assignment solver auto-saves drafts even offline, calculates step-by-step scores instantly, and provides video explanations for incorrect answers.'
        ],
        [
            'q' => $isAr ? 'كيف يمكن لأولياء الأمور متابعة الحضور والدرجات؟' : 'How can parents monitor real-time attendance and academic performance?',
            'a' => $isAr 
                ? 'من خلال بوابة ولي الأمر، يقوم ولي الأمر بربط حساب الطالب برقم الهاتف لمتابعة نسبة الحضور بالدقيقة، نتائج الواجبات، وتلقي إشعارات لحظية عبر الواتساب أو FCM عند الغياب.' 
                : 'Parents access the Parent Portal by verifying their child phone number to view live attendance logs, quiz scores, and receive instant push/WhatsApp alerts for absences.'
        ],
        [
            'q' => $isAr ? 'هل الشهادات الصادرة من المنصة معتمدة؟' : 'Are certificates earned on the platform officially accredited?',
            'a' => $isAr 
                ? 'جميع المسارات التعليمية وحصص إتمام المناهج تمنح الطلاب شهادات إتمام رقمية مشفرة وموثقة برقم تسلسلي معتمد لدى الجهات الأكاديمية.' 
                : 'All completed academic tracks issue verified digital certificates with unique serial numbers recognized by leading educational institutions.'
        ],
        [
            'q' => $isAr ? 'ما هي طرق الدفع واشتراكات الحصص المتاحة؟' : 'What payment options and subscription package choices are available?',
            'a' => $isAr 
                ? 'توفر المنصة اشتراكات شهرية، باقات بالحصص، أو شراء كورس كامل مع دعم وسائل الدفع الإلكتروني (فوري، كروت ائتمان، المحافظ الإلكترونية مثل فودافون كاش).' 
                : 'We offer flexible monthly subscriptions, pay-per-session packages, and full course unlocks via Fawry, Credit Cards, and Mobile Wallets (Vodafone Cash).'
        ],
    ];
@endphp

<section id="faq-section" class="py-16 md:py-24 bg-white border-t border-slate-200/80" itemscope itemtype="https://schema.org/FAQPage">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-4 max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                <span>💬</span> {{ $isAr ? 'مركز الأسئلة الشائعة والمعلومات' : 'Frequently Asked Questions' }}
            </span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                {{ $isAr ? 'كل ما تحتاج معرفته عن منصة إيليت التعليمية' : 'Everything You Need to Know About Elite Academy' }}
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                {{ $isAr 
                    ? 'إجابات شاملة وموثقة حول البث المباشر، تصحيح الواجبات، أدوات أولياء الأمور، والشهادات المعتمدة.' 
                    : 'Clear, authoritative answers regarding our live stream tech, auto-graded assignments, parent monitoring, and accredited tracks.' 
                }}
            </p>
        </div>

        <div class="space-y-4">
            @foreach($homeFaqs as $faq)
                @include('components.faq-item', [
                    'question' => $faq['q'],
                    'answer' => $faq['a']
                ])
            @endforeach
        </div>

        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-8 text-white flex flex-wrap items-center justify-between gap-6 shadow-xl border border-slate-700/80">
            <div class="space-y-1 max-w-xl">
                <h4 class="font-heading font-bold text-lg text-white">
                    {{ $isAr ? 'لديك استفسار آخر لم نجب عليه؟' : 'Have a specific question not listed here?' }}
                </h4>
                <p class="text-xs text-slate-300">
                    {{ $isAr ? 'فريق الدعم الأكاديمي والتقني متاح على مدار الساعة للإجابة على جميع تساؤلاتك.' : 'Our academic support specialists are available 24/7 to guide you.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="btn-lift px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-2xl text-xs shadow-lg transition-all">
                💬 {{ $isAr ? 'تواصل مع الدعم المباشر' : 'Contact Support Team' }}
            </a>
        </div>
    </div>
</section>

```

---

## File: `resources/views/pages/home/hero-slider.blade.php`

```blade
@use('App\Models\SiteSetting')
{{-- Home Hero Slider: Alpine.js Auto-playing Carousel with Zero Text Overlap --}}
@php
    $dbHeroSlides = \Illuminate\Support\Facades\Schema::hasTable('hero_slides')
        ? \App\Models\HeroSlide::where('is_active', true)->orderBy('sort_order')->get()
        : collect();
    $totalSlideCount = $dbHeroSlides->count() > 0 ? $dbHeroSlides->count() : 4;
@endphp

<section 
    x-data="{ 
        activeSlide: 0, 
        totalSlides: {{ $totalSlideCount }},
        timer: null,
        startAutoplay() {
            this.stopAutoplay();
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 6000);
        },
        stopAutoplay() {
            if (this.timer) clearInterval(this.timer);
        },
        nextSlide() {
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
        },
        prevSlide() {
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        goToSlide(index) {
            this.activeSlide = index;
        }
    }"
    x-init="startAutoplay()"
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
    class="w-full min-h-[75vh] lg:min-h-[92vh] relative overflow-hidden bg-slate-950 text-white flex flex-col justify-between hero-section select-none"
>
    {{-- Subtle Floating Decorative Ambient Shapes --}}
    <div class="absolute top-28 left-[10%] w-4 h-4 border-2 border-teal-500/30 rounded-full animate-drift pointer-events-none -z-10"></div>
    <div class="absolute top-96 right-[12%] w-6 h-6 border-2 border-orange-500/20 rounded-md rotate-12 animate-float pointer-events-none -z-10"></div>

    {{-- Ambient Mesh Radial Glows --}}
    <div class="absolute -top-24 -left-24 w-[40rem] h-[40rem] bg-teal-500/20 rounded-full blur-3xl pointer-events-none z-0 animate-pulse-glow"></div>
    <div class="absolute bottom-0 right-0 w-[45rem] h-[45rem] bg-orange-500/15 rounded-full blur-3xl pointer-events-none z-0 animate-float"></div>

    {{-- DYNAMIC DB SLIDES --}}
    @if($dbHeroSlides->count() > 0)
        @foreach($dbHeroSlides as $idx => $s)
            <div 
                x-show="activeSlide === {{ $idx }}" 
                x-transition:enter="transition ease-out duration-700" 
                x-transition:enter-start="opacity-0 scale-98" 
                x-transition:enter-end="opacity-100 scale-100" 
                x-transition:leave="transition ease-in duration-400" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 z-10 flex flex-col justify-between"
            >
                <img src="{{ media_url($s->image, 'images/hero_student.png') }}" alt="{{ $s->title }}" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                    <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                        @if($s->track_label)
                            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
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
                                <a href="{{ $s->cta_primary_url }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25">
                                    <span>{{ __('Explore Now') }}</span>
                                    <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                                </a>
                            @endif
                            @if($s->cta_secondary_url)
                                <a href="{{ $s->cta_secondary_url }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
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
            x-show="activeSlide === 0" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="{{ asset('images/hero_student.png') }}" alt="Programming & Tech Lab" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
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
                        <a href="{{ SiteSetting::get('landing_cta_primary_link', '/subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 shadow-lg shadow-teal-500/25">
                            <span>{{ SiteSetting::getLocalized('landing_cta_primary_text', 'Explore All Subjects') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
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
            x-show="activeSlide === 1" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="{{ asset('images/course_ai.png') }}" alt="AI Neural Networks Lab" class="absolute inset-0 w-full h-full object-cover animate-ken-burns">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
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
                        <a href="{{ route('subject-details') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 shadow-lg shadow-purple-600/25">
                            <span>{{ __('Explore AI') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('courses') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
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
            x-show="activeSlide === 2" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="{{ asset('images/instructor_male.png') }}" alt="Robotics Engineering Lab" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-orange-500/20 border border-orange-400/30 text-orange-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
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
                        <a href="{{ route('subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 shadow-lg shadow-orange-500/25">
                            <span>{{ __('Explore Robotics') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('event-details') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
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
            x-show="activeSlide === 3" 
            x-transition:enter="transition ease-out duration-700" 
            x-transition:enter-start="opacity-0 scale-98" 
            x-transition:enter-end="opacity-100 scale-100" 
            x-transition:leave="transition ease-in duration-400" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-10 flex flex-col justify-between"
        >
            <img src="{{ asset('images/academy_campus.png') }}" alt="Science Laboratory" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-slate-950/30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto py-10 lg:py-20 relative z-20 flex flex-col lg:grid lg:grid-cols-12 gap-6 items-center text-center lg:text-left">
                <div class="lg:col-span-8 space-y-4 sm:space-y-6 max-w-xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-teal-500/20 border border-teal-400/30 text-teal-300 text-xs font-bold tracking-wide backdrop-blur-md animate-badge-pulse shadow-md">
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
                        <a href="{{ route('subjects') }}" class="btn-mobile-lg btn-lift group w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 text-slate-950 bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 shadow-lg shadow-teal-500/25">
                            <span>{{ __('Explore Science') }}</span>
                            <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn-mobile-lg btn-lift w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 text-white bg-white/12 hover:bg-white/20 border border-white/30 backdrop-blur-md shadow-md shadow-white/10">
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

    {{-- Progress Bar & Interactive Slide Controls --}}
    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pb-8 flex items-center justify-between border-t border-white/15 pt-6">
        <div class="font-mono text-sm font-bold text-slate-200 flex items-center gap-4">
            <span class="text-teal-400 text-xl font-extrabold tracking-wider" x-text="String(activeSlide + 1).padStart(2, '0')">01</span>
            <div class="w-32 sm:w-48 h-1.5 bg-white/20 rounded-full relative overflow-hidden">
                <div class="absolute top-0 bottom-0 left-0 bg-teal-400 rounded-full transition-all duration-500" :style="'width: ' + (((activeSlide + 1) / totalSlides) * 100) + '%'"></div>
            </div>
            <span class="text-slate-400 text-sm" x-text="String(totalSlides).padStart(2, '0')">04</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2.5">
                <template x-for="i in totalSlides" :key="i">
                    <button 
                        type="button" 
                        @click="goToSlide(i - 1)" 
                        :class="activeSlide === (i - 1) ? 'bg-teal-400 w-7' : 'bg-white/30 w-3 hover:bg-white/70'" 
                        class="h-3 rounded-full transition-all duration-300 cursor-pointer" 
                        :aria-label="'Go to slide ' + i"
                    ></button>
                </template>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" @click="prevSlide()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Previous Slide">&larr;</button>
                <button type="button" @click="nextSlide()" class="btn-lift w-10 h-10 rounded-full bg-white/12 border border-white/25 flex items-center justify-center text-white cursor-pointer hover:bg-teal-500/40 shadow-sm backdrop-blur-md" aria-label="Next Slide">&rarr;</button>
            </div>
        </div>
    </div>
</section>

```

---

## File: `resources/views/pages/home/stats-overlay.blade.php`

```blade
@php
    $locale = app()->getLocale();
    $rawStats = \App\Models\SiteSetting::get('landing_stats_counters');
    $stats = $rawStats ? json_decode($rawStats, true) : [
        ['count' => '25,000+', 'label_ar' => 'الطلاب النشطين', 'label_en' => 'Active Students', 'color' => 'teal'],
        ['count' => '120+', 'label_ar' => 'الكورسات والمقررات المعتمدة', 'label_en' => 'Expert Courses', 'color' => 'teal'],
        ['count' => '45+', 'label_ar' => 'المعلمين والمحاضرين', 'label_en' => 'Instructors & Mentors', 'color' => 'teal'],
        ['count' => '98.5%', 'label_ar' => 'رضا أولياء الأمور', 'label_en' => 'Parent Satisfaction', 'color' => 'orange'],
        ['count' => '100%', 'label_ar' => 'شهادات دولية معتمدة', 'label_en' => 'Global Certifications', 'color' => 'teal'],
    ];
@endphp

{{-- Stats Overlay Section --}}
<section class="relative z-30 -mt-10 md:-mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white/92 backdrop-blur-md border border-white/80 rounded-3xl shadow-2xl shadow-slate-900/10 p-6 md:p-8">
        <div class="grid grid-cols-2 md:grid-cols-{{ min(count($stats), 5) }} gap-6 text-center divide-x-0 md:divide-x divide-slate-200/60">
            @foreach($stats as $index => $stat)
                @php
                    $label = ($locale === 'ar' ? ($stat['label_ar'] ?? null) : null) ?: ($stat['label_en'] ?? '');
                    $colorClass = ($stat['color'] ?? 'teal') === 'orange' ? 'text-orange-500' : (($stat['color'] ?? 'teal') === 'emerald' ? 'text-emerald-600' : 'text-teal-600');
                    $delay = ($index % 5) + 1;
                @endphp
                <div class="anim-hero delay-{{ $delay }} space-y-1 p-2 group sr-stat {{ $loop->last && count($stats) % 2 !== 0 ? 'col-span-2 md:col-span-1' : '' }}">
                    <p class="font-mono font-extrabold text-3xl sm:text-4xl {{ $colorClass }} group-hover:scale-105 transition-transform duration-300">
                        <span data-count="{{ $stat['count'] }}">{{ $stat['count'] }}</span>
                    </p>
                    <p class="text-xs font-semibold text-slate-600">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

```

---

## File: `resources/views/pages/home/subjects-grid.blade.php`

```blade
{{-- Subjects Showcase Grid Section --}}
<section class="py-20 md:py-28 bg-white border-y border-slate-200/80 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

        {{-- 2-Column Section Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-3 max-w-xl">
                <span class="anim-subject delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                    {{ __('OUR SUBJECTS') }}
                </span>
                <h2 class="anim-subject delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ \App\Models\SiteSetting::getLocalized('subjects_title', __('Explore Specialized Subjects & Programs')) }}
                </h2>
            </div>

            <div class="hidden md:block w-px h-16 bg-slate-200/80 mx-2 flex-shrink-0"></div>

            <div class="max-w-md my-auto">
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed">
                    {{ \App\Models\SiteSetting::getLocalized('subjects_subtitle', __('Cutting-edge curriculum designed by industry experts and academic researchers.')) }}
                </p>
            </div>
        </div>

        {{-- Dynamic Subjects 4-Column Grid --}}
        @php
            $dbSubjects = \App\Models\Subject::where('is_active', true)
                ->with(['category', 'courses'])
                ->orderBy('sort_order')
                ->take(8)
                ->get();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8">
            @foreach ($dbSubjects as $sub)
                @php
                    $categoryName = $sub->category ? $sub->category->getLocalizedName() : __('General');
                    $coursesCount = $sub->courses ? $sub->courses->count() : 0;
                    $subjectUrl = route('subject-details', ['slug' => $sub->slug]);
                    $image = media_url($sub->image, 'images/hero_student.png');
                @endphp
                <div class="anim-subject delay-3 sr-card aspect-[4/5] md:aspect-auto md:h-[520px] rounded-[24px] bg-slate-950 text-white shadow-lg hover:shadow-2xl card-lift flex flex-col justify-between overflow-hidden group transition-all duration-300 relative active:scale-[0.98]">
                    <div class="absolute inset-0 md:relative md:h-[338px] overflow-hidden bg-slate-950">
                        <img src="{{ $image }}" alt="{{ $sub->getLocalizedName() }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent pointer-events-none"></div>
                    </div>

                    <div class="absolute bottom-0 inset-x-0 p-3.5 sm:p-4 text-white z-10 flex flex-col justify-end space-y-1 md:relative md:p-6 md:flex-1 md:bg-slate-950 md:space-y-3">
                        <div class="space-y-1">
                            <span class="text-[9px] sm:text-[10px] font-mono font-extrabold uppercase tracking-widest text-teal-300 bg-slate-950/70 md:bg-transparent backdrop-blur-xs md:backdrop-blur-none px-2 py-0.5 md:p-0 rounded-full md:rounded-none border border-white/10 md:border-none inline-block w-max">
                                {{ $categoryName }}
                            </span>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base md:text-2xl text-white group-hover:text-teal-300 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ $subjectUrl }}">{{ $sub->getLocalizedName() }}</a>
                            </h3>
                        </div>

                        <div class="hidden md:flex items-center justify-between pt-3 border-t border-slate-800 text-xs text-slate-300 font-medium">
                            <span>📚 {{ $coursesCount }} {{ __('Courses') }}</span>
                            <a href="{{ $subjectUrl }}" class="text-xs font-extrabold text-teal-300 group-hover:text-teal-200 flex items-center gap-1">
                                <span>{{ __('View Details') }}</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

```

---

## File: `resources/views/pages/home/teachers-marquee.blade.php`

```blade
{{-- Featured Mentors Showcase Section --}}
<section class="py-16 md:py-24 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Featured Mentors Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-3">
                <span class="anim-projects delay-1 inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                    {{ __('FACULTY') }}
                </span>
                <h2 class="anim-projects delay-2 font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ \App\Models\SiteSetting::getLocalized('teachers_title', __('Meet Our Featured Mentors.')) }}
                </h2>
            </div>
            <span class="text-xs font-mono text-slate-500 font-medium">&larr; {{ __('Swipe Teachers') }} &rarr;</span>
        </div>

        {{-- Mentor Slider Carousel (Dynamic from DB) --}}
        @php
            $dbTeachers = \Illuminate\Support\Facades\Schema::hasTable('teacher_profiles')
                ? \App\Models\TeacherProfile::with('user')->get()
                : collect();

            $mentors = $dbTeachers->count() > 0 ? $dbTeachers->map(fn($t) => [
                'name' => $t->user?->name ?: 'Teacher Profile',
                'title' => $t->specialization ?: 'Senior Academic Mentor',
                'dept' => 'Faculty',
                'badgeBg' => 'bg-teal-600',
                'textColor' => 'group-hover:text-teal-300',
                'meta' => ($t->years_experience ?: 5) . '+ Yrs Exp • Active Educator',
                'photo' => 'images/instructor_portrait.png',
            ]) : [
                ['name' => 'Dr. Ahmed Hassan', 'title' => 'Senior AI & Systems Researcher', 'dept' => 'Programming', 'badgeBg' => 'bg-teal-600', 'textColor' => 'group-hover:text-teal-300', 'meta' => '15+ Yrs Exp • 1,400+ Students • PhD - MIT', 'photo' => 'images/instructor_portrait.png'],
                ['name' => 'Sarah Mohamed', 'title' => 'Deep Learning Lead Architect', 'dept' => 'Artificial Intelligence', 'badgeBg' => 'bg-purple-600', 'textColor' => 'group-hover:text-purple-300', 'meta' => '12+ Yrs Exp • 1,100+ Students • MSc - Stanford', 'photo' => 'images/instructor_female.png'],
                ['name' => 'Omar Khaled', 'title' => 'Robotics & Autonomous Systems Specialist', 'dept' => 'Robotics', 'badgeBg' => 'bg-orange-600', 'textColor' => 'group-hover:text-orange-300', 'meta' => '10+ Yrs Exp • 950+ Students • PhD - Cambridge', 'photo' => 'images/instructor_male.png'],
            ];
        @endphp

        <div class="carousel-container no-scrollbar">
            @foreach ($mentors as $m)
                <div class="carousel-card-large-peek anim-projects delay-3 rounded-3xl overflow-hidden shadow-xl border border-slate-200/80 h-96 relative group card-lift flex-shrink-0 transition-all duration-300">
                    <img src="{{ media_url($m['photo'], 'images/instructor_portrait.png') }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent pointer-events-none"></div>

                    <span class="absolute top-5 left-5 text-xs font-mono font-extrabold text-white {{ $m['badgeBg'] }} px-3.5 py-1.5 rounded-full shadow-md">
                        {{ $m['dept'] }}
                    </span>

                    <div class="absolute bottom-6 left-6 right-6 text-white space-y-2">
                        <div>
                            <h3 class="font-heading font-extrabold text-2xl text-white {{ $m['textColor'] }} transition-colors">
                                {{ $m['name'] }}
                            </h3>
                            <p class="text-xs font-mono text-slate-300 font-semibold">{{ $m['title'] }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-white/20 text-xs font-medium text-slate-200">
                            <span class="text-[11px] font-mono">{{ $m['meta'] }}</span>
                            <a href="{{ route('teacher-profile') }}" class="text-xs font-extrabold text-teal-300 group-hover:text-teal-200 flex items-center gap-1">
                                <span>View Profile</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

```

---

## File: `resources/views/pages/home/testimonials.blade.php`

```blade
{{-- Testimonials Carousel Section --}}
<section class="py-24 lg:py-32 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[45rem] h-[25rem] bg-teal-400/10 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 relative z-10">

        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-4 border-b border-slate-200/60">
            <div class="space-y-3 max-w-xl">
                <span class="anim-testimonials delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-4 py-1.5 rounded-full border border-teal-200/80 shadow-xs">
                    {{ __('TESTIMONIALS') }}
                </span>
                <h2 class="anim-testimonials delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ \App\Models\SiteSetting::getLocalized('testimonials_title', __('What Our Students & Parents Say.')) }}
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs font-mono text-slate-500 font-medium hidden sm:inline">&larr; {{ __('Swipe Reviews') }} &rarr;</span>
                <div class="flex items-center gap-2">
                    <button class="w-10 h-10 rounded-full bg-white border border-slate-200/90 shadow-md flex items-center justify-center text-slate-700 hover:text-teal-600 hover:border-teal-300 transition-all duration-300 active:scale-95 cursor-pointer">&larr;</button>
                    <button class="w-10 h-10 rounded-full bg-white border border-slate-200/90 shadow-md flex items-center justify-center text-slate-700 hover:text-teal-600 hover:border-teal-300 transition-all duration-300 active:scale-95 cursor-pointer">&rarr;</button>
                </div>
            </div>
        </div>

        {{-- Testimonial Cards Carousel (Dynamic from DB) --}}
        @php
            $dbTestimonials = \Illuminate\Support\Facades\Schema::hasTable('testimonials')
                ? \App\Models\Testimonial::where('is_featured', true)->orderBy('sort_order')->get()
                : collect();
            $testimonials = $dbTestimonials->count() > 0 ? $dbTestimonials->map(fn($t) => [
                'quote' => '"' . $t->getLocalizedContent() . '"',
                'photo' => $t->avatar ?: 'images/instructor_portrait.png',
                'name' => $t->name,
                'course' => $t->getLocalizedCourseName() ?: __('Elite Academic Track'),
                'badge' => $t->is_verified ? '✔ Verified ' . ucfirst($t->reviewer_type) : ucfirst($t->reviewer_type),
                'quoteColor' => 'group-hover:text-teal-600',
                'nameColor' => 'group-hover:text-teal-600',
                'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
            ]) : [
                [
                    'quote' => '"Elite Academy completely transformed my son\'s approach to coding and math. Having direct access to PhD mentors made all the difference."',
                    'photo' => 'images/hero_student.png',
                    'name' => 'Mariam Al-Mansoor',
                    'course' => 'Full-Stack Programming',
                    'badge' => '✔ Verified Student',
                    'quoteColor' => 'group-hover:text-teal-600',
                    'nameColor' => 'group-hover:text-teal-600',
                    'badgeBg' => 'bg-teal-50 text-teal-700 border-teal-200/80',
                ],
                [
                    'quote' => '"The robotics and AI labs gave me real hands-on experience building computer vision models. I secured a software engineering role right after graduation!"',
                    'photo' => 'images/instructor_portrait.png',
                    'name' => 'Kareem El-Sayed',
                    'course' => 'AI & Machine Learning',
                    'badge' => '✔ Verified Student',
                    'quoteColor' => 'group-hover:text-purple-600',
                    'nameColor' => 'group-hover:text-purple-600',
                    'badgeBg' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                ],
            ];
        @endphp

        <div class="carousel-container no-scrollbar flex items-center gap-8 overflow-x-auto py-6 snap-x snap-mandatory scroll-smooth">
            @foreach ($testimonials as $t)
                <div class="w-full max-w-[420px] sm:w-[420px] shrink-0 h-[340px] bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl hover:shadow-2xl card-lift flex flex-col justify-between group transition-all duration-500 snap-center">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 text-amber-400 text-sm">★★★★★</div>
                        <span class="text-4xl font-serif font-black text-slate-300 {{ $t['quoteColor'] }} transition-colors duration-300 select-none leading-none">"</span>
                    </div>

                    <p class="font-heading font-medium text-slate-700 text-base leading-relaxed italic line-clamp-3 my-3 flex-1">
                        {{ $t['quote'] }}
                    </p>

                    <div class="pt-5 border-t border-slate-100 flex items-center gap-4">
                        <img src="{{ media_url($t['photo'], 'images/instructor_portrait.png') }}" alt="{{ $t['name'] }}" class="w-14 h-14 sm:w-14 sm:h-14 rounded-full object-cover shadow-md group-hover:scale-105 transition-transform duration-500 border-2 border-white flex-shrink-0">
                        <div class="space-y-1 min-w-0 flex-1">
                            <h3 class="font-heading font-extrabold text-base text-slate-900 truncate {{ $t['nameColor'] }} transition-colors">{{ $t['name'] }}</h3>
                            <p class="text-xs font-mono text-slate-500 font-semibold truncate">{{ $t['course'] }}</p>
                            <span class="inline-block {{ $t['badgeBg'] }} text-[10px] font-mono font-extrabold px-2.5 py-0.5 rounded-full border">{{ $t['badge'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination Dots --}}
        <div class="flex items-center justify-center gap-2 pt-4">
            <span class="w-8 h-2.5 rounded-full bg-teal-600 transition-all duration-300"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors duration-300 cursor-pointer"></span>
        </div>

    </div>
</section>

```

---

## File: `resources/views/pages/home/why-choose.blade.php`

```blade
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
                    <img src="{{ asset('images/hero_student.png') }}" alt="Why Students Choose Elite Academy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
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

```

---

## File: `resources/views/pages/login.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9]">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100 shadow-xs">🔑</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول للمنصة' : 'Sign In to Portal' }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'أدخل معلومات حسابك للوصول لبوابة المقررات والأداء الأكاديمي.' : 'Access your courses, grades, and academic dashboard.' }}
            </p>
        </div>

        {{-- Fallback Error / Success Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        {{-- Dedicated Sign In Form --}}
        <form id="signinForm" action="{{ route('ajax.login') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <input type="email" name="email" required placeholder="student@eliteacademy.edu.eg" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                    <a href="#" class="text-xs text-teal-600 hover:underline font-bold">{{ app()->getLocale() === 'ar' ? 'نسيت كلمة المرور؟' : 'Forgot Password?' }}</a>
                </div>
                <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer text-slate-600 font-medium">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>{{ app()->getLocale() === 'ar' ? 'تذكرني على هذا الجهاز' : 'Remember me' }}</span>
                </label>
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl">
                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In to Portal' }}</span>
                <span class="arrow-icon">&rarr;</span>
            </button>
        </form>

        {{-- Separate Register Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'ليس لديك حساب حتى الآن؟' : "Don't have an account yet?" }}
                <a href="{{ route('register') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ml-1">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد ←' : 'Create an Account &rarr;' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    const form = document.getElementById('signinForm');
    const submitBtn = document.getElementById('submitBtn');
    
    function showNotification(msg, isError = true, title = null) {
        if (window.Toast) {
            if (isError) {
                window.Toast.error(msg, title || (document.documentElement.lang === 'ar' ? 'فشل تسجيل الدخول' : 'Login Failed'));
            } else {
                window.Toast.success(msg, title || (document.documentElement.lang === 'ar' ? 'تم تسجيل الدخول' : 'Login Successful'));
            }
        } else if (alertBox) {
            alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
            alertBox.textContent = msg;
            alertBox.classList.remove('hidden');
        }
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');
            
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    let errMsg = data.message || (document.documentElement.lang === 'ar' ? 'البيانات المدخلة غير صحيحة.' : 'Login failed. Please check your credentials.');
                    if (data.errors) {
                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }
                    showNotification(errMsg, true);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                showNotification(data.message || (document.documentElement.lang === 'ar' ? 'تم تسجيل الدخول بنجاح! جاري التوجيه...' : 'Login successful! Redirecting...'), false);
                
                // Smooth role-based redirection delay
                setTimeout(() => {
                    window.location.href = data.redirect_url || '/student-portal';
                }, 900);
            } catch (err) {
                showNotification(document.documentElement.lang === 'ar' ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endsection

```

---

## File: `resources/views/pages/parent-portal.blade.php`

```blade
@extends('layouts.app')

@section('content')
{{-- Specialized Print CSS Styling --}}
<style>
@media print {
    /* Hide all web UI chrome, navigation, headers, footers, modals, & buttons */
    header, footer, nav, .no-print, #section-children, #linkChildModal, .btn-lift, button, .breadcrumb, [role="navigation"], section:first-of-type {
        display: none !important;
    }
    
    body, html {
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 11pt !important;
        font-family: 'Cairo', 'Rubik', sans-serif !important;
        width: 100% !important;
    }

    /* Print Container Styling */
    section {
        padding: 0 !important;
        background: #ffffff !important;
    }

    #progressResult {
        border: 2px solid #0d9488 !important;
        box-shadow: none !important;
        padding: 20px !important;
        margin: 0 auto !important;
        width: 100% !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        page-break-inside: avoid;
    }

    .print-official-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        border-bottom: 2px solid #0d9488 !important;
        padding-bottom: 12px !important;
        margin-bottom: 20px !important;
    }

    .print-watermark-stamp {
        display: block !important;
        border: 2px dashed #0d9488 !important;
        padding: 6px 14px !important;
        border-radius: 12px !important;
        color: #0d9488 !important;
        font-weight: 800 !important;
        font-size: 10pt !important;
        text-align: center !important;
        background: #f0fdf4 !important;
    }

    /* Preserve colors & layout in print PDF */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .grid {
        display: grid !important;
    }
}
</style>

{{-- Parent Portal Header --}}
<section class="py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.parent_portal')],
            ]
        ])

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800/80 shadow-xs">
                    👨‍gsub👨‍👧‍👦 {{ __('PARENT DASHBOARD • ACADEMIC MONITORING') }}
                </span>
                <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                    {{ __('Parent Portal: Student Academic Dashboard') }}
                </h1>
                <p class="text-slate-300 text-sm font-mono max-w-2xl">
                    {{ __('Monitor your children’s live stream classes, attendance records, homework submissions, active packages, and credit usage in real-time.') }}
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openLinkChildModal()" class="btn-lift px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs rounded-2xl shadow-lg shadow-teal-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>➕</span> {{ __('Link New Child by Phone') }}
                </button>

                @php
                    $whatsappNumber = \App\Models\SiteSetting::get('owner_whatsapp', '+201000000000');
                    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
                @endphp
                <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ urlencode(__('Hello Elite Academy Admin, I am a parent inquiring about package renewal.')) }}" target="_blank" class="btn-lift px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-2xl border border-emerald-400/40 shadow-sm flex items-center gap-2">
                    <span>💬</span> {{ __('WhatsApp Payment & Renewal') }}
                </a>

                <div class="inline-flex items-center gap-2 bg-amber-500/20 px-3.5 py-2 rounded-2xl border border-amber-500/30 text-xs font-mono text-amber-300 font-bold">
                    <span>🔒</span>
                    <span>{{ __('Read-Only Monitoring') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Main Dashboard Body --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Section 1: Children Selector Grid --}}
        <div id="section-children" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6 scroll-mt-28 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 gap-4">
                <div>
                    <h2 class="font-heading font-black text-2xl text-slate-900 flex items-center gap-2.5">
                        <span>👨‍👩‍👧‍👦</span> {{ __('Your Linked Children') }}
                    </h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Select a child to inspect detailed academic performance, package & attendance.') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span id="linkedCountBadge" class="text-xs font-mono font-extrabold text-teal-700 bg-teal-50 px-3 py-1.5 rounded-full border border-teal-200">
                        {{ count($linkedStudents) }} {{ __('Children Linked') }}
                    </span>
                    <button type="button" onclick="openLinkChildModal()" class="text-xs font-bold font-mono text-teal-600 hover:text-teal-700 underline">
                        + {{ __('Link New Child') }}
                    </button>
                </div>
            </div>

            <div id="linkedChildrenGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($linkedStudents as $st)
                    <div id="child-card-{{ $st->user_id }}" onclick="loadStudentProgress({{ $st->user_id }})" class="child-card cursor-pointer bg-slate-50 hover:bg-teal-50/60 transition-all duration-300 rounded-2xl p-6 border-2 border-slate-200 hover:border-teal-500 shadow-sm space-y-4 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-teal-600 text-white font-heading font-black text-xl flex items-center justify-center shadow-md">
                                {{ mb_substr($st->user?->name ?: 'S', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-900 group-hover:text-teal-700 transition-colors">{{ $st->user?->name }}</h3>
                                <span class="text-[11px] font-mono font-bold bg-teal-100 text-teal-800 px-2.5 py-0.5 rounded-md">
                                    {{ $st->gradeLevel?->name ?: __('Grade Level') }}
                                </span>
                            </div>
                        </div>

                        <div class="text-xs font-mono text-slate-500 space-y-1 pt-2 border-t border-slate-200/60">
                            <p class="truncate">🏫 {{ $st->school_name ?: __('Elite STEM Academy') }}</p>
                            <p class="text-teal-600 font-bold">✔ {{ __('Independent Student Account') }}</p>
                        </div>

                        <button type="button" class="w-full py-2 bg-white group-hover:bg-teal-600 group-hover:text-white text-slate-800 rounded-xl text-xs font-bold font-mono border border-slate-200 transition-all shadow-xs">
                            {{ __('Inspect Child Dashboard') }} &rarr;
                        </button>
                    </div>
                @empty
                    <div id="emptyChildrenBox" class="col-span-3 text-center py-12 bg-slate-50 rounded-2xl border border-slate-200 text-slate-500 space-y-3">
                        <div class="text-4xl">👨‍👩‍👧‍👦</div>
                        <h3 class="font-bold text-base text-slate-800">{{ __('No Linked Children Found') }}</h3>
                        <p class="text-xs font-mono text-slate-500 max-w-md mx-auto">
                            {{ __('Link your child by entering their registered phone number or email address.') }}
                        </p>
                        <button type="button" onclick="openLinkChildModal()" class="btn-lift px-5 py-2.5 bg-teal-600 text-white font-bold text-xs rounded-xl shadow-md">
                            ➕ {{ __('Link Child Account Now') }}
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Section 2: Selected Child Detailed Performance Panel --}}
        <div id="progressResult" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 id="selectedStudentName" class="font-heading font-black text-2xl text-slate-900">{{ __('Student Academic Overview') }}</h3>
                    <p id="selectedStudentMeta" class="text-xs font-mono text-teal-600 font-bold mt-0.5"></p>
                </div>
                <div class="flex items-center gap-3 no-print">
                    <span id="packageBadge" class="text-xs font-mono font-bold bg-teal-100 text-teal-800 px-3 py-1.5 rounded-xl border border-teal-200"></span>
                    <span id="attendanceBadge" class="text-xs font-mono font-bold bg-emerald-100 text-emerald-800 px-3 py-1.5 rounded-xl border border-emerald-200"></span>
                </div>
            </div>

            <div id="progressContent" class="space-y-6">
                @if(count($linkedStudents) === 0)
                    <div class="p-8 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-700">{{ __('Please link a child account above using their phone number to view academic reports.') }}</p>
                        <button type="button" onclick="openLinkChildModal()" class="px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-bold">➕ {{ __('Link Child Account') }}</button>
                    </div>
                @else
                    <div class="p-8 text-center text-xs font-mono text-slate-500 font-bold">{{ __('Loading student progress metrics...') }}</div>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- Link New Child Modal --}}
<div id="linkChildModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden no-print">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full border border-slate-200 shadow-2xl space-y-6 relative anim-lift">
        <button type="button" onclick="closeLinkChildModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>

        <div class="space-y-2">
            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center text-2xl font-bold border border-teal-200">
                🔗
            </div>
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Link New Child Account') }}</h3>
            <p class="text-xs text-slate-500 font-medium">
                {{ __('Enter the phone number or registered email address of your student to link their account for monitoring.') }}
            </p>
        </div>

        <form id="linkChildForm" onsubmit="handleLinkChildSubmit(event)" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="block text-xs font-mono font-extrabold text-slate-600 uppercase">{{ __('Student Phone Number or Email') }}</label>
                <input type="text" id="phone_or_email" name="phone_or_email" required placeholder="e.g. 01012345678 or student@email.com" class="w-full h-11 bg-slate-50 border border-slate-300 rounded-xl px-4 text-sm font-semibold text-slate-900 focus:outline-teal-600">
            </div>

            <div id="linkChildFeedback" class="hidden text-xs font-bold p-3 rounded-xl"></div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeLinkChildModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" id="linkSubmitBtn" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-teal-600/20">
                    {{ __('Link Child Account') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($linkedStudents) > 0)
        loadStudentProgress({{ $linkedStudents->first()->user_id }});
    @endif

    // Dynamic Navbar Active Link Observer & Hash Highlight
    function updateActiveNavbarLink(targetHash) {
        const navLinks = document.querySelectorAll('header nav a');
        navLinks.forEach(link => {
            const href = link.getAttribute('href') || '';
            const isActive = targetHash && href.includes(targetHash);
            
            if (isActive) {
                link.className = 'px-2 py-1 lg:px-3 lg:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 text-teal-700 font-extrabold bg-teal-50/90 border border-teal-200/80 shadow-xs';
            } else if (href.includes('#section-')) {
                link.className = 'px-2 py-1 lg:px-3 lg:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 text-slate-800 font-bold hover:text-teal-600 hover:bg-slate-100/90';
            }
        });
    }

    function scrollToTarget() {
        const hash = window.location.hash.replace('#', '');
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        const targetId = hash || (tab ? `section-${tab}` : null);

        if (targetId) {
            updateActiveNavbarLink(targetId);
            const el = document.getElementById(targetId);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    setTimeout(scrollToTarget, 500);
    window.addEventListener('hashchange', scrollToTarget);

    // Intersection Observer to update Navbar active state on scroll
    const sections = document.querySelectorAll('[id^="section-"]');
    if ('IntersectionObserver' in window && sections.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateActiveNavbarLink(entry.target.id);
                }
            });
        }, { threshold: 0.3 });

        sections.forEach(sec => observer.observe(sec));
    }
});

function openLinkChildModal() {
    document.getElementById('linkChildModal').classList.remove('hidden');
    document.getElementById('phone_or_email').focus();
}

function closeLinkChildModal() {
    document.getElementById('linkChildModal').classList.add('hidden');
    document.getElementById('linkChildFeedback').classList.add('hidden');
    document.getElementById('linkChildForm').reset();
}

async function handleLinkChildSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('phone_or_email').value;
    const feedback = document.getElementById('linkChildFeedback');
    const submitBtn = document.getElementById('linkSubmitBtn');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Linking...';
    feedback.classList.add('hidden');

    try {
        const res = await fetch("{{ route('ajax.parent.link-child') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone_or_email: input })
        });
        const data = await res.json();

        if (data.success) {
            feedback.className = 'text-xs font-bold p-3 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 block';
            feedback.textContent = data.message;
            
            setTimeout(() => {
                closeLinkChildModal();
                window.location.reload();
            }, 1200);
        } else {
            feedback.className = 'text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 block';
            feedback.textContent = data.message || 'Error linking student.';
        }
    } catch (err) {
        feedback.className = 'text-xs font-bold p-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-200 block';
        feedback.textContent = 'Network error. Please try again.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = "{{ __('Link Child Account') }}";
    }
}

async function loadStudentProgress(studentId) {
    const content = document.getElementById('progressContent');
    const nameEl = document.getElementById('selectedStudentName');
    const metaEl = document.getElementById('selectedStudentMeta');
    const pkgBadge = document.getElementById('packageBadge');
    const attBadge = document.getElementById('attendanceBadge');

    document.querySelectorAll('.child-card').forEach(card => card.classList.remove('ring-4', 'ring-teal-500/40', 'border-teal-500', 'bg-teal-50/40'));
    const activeCard = document.getElementById(`child-card-${studentId}`);
    if (activeCard) {
        activeCard.classList.add('ring-4', 'ring-teal-500/40', 'border-teal-500', 'bg-teal-50/40');
    }

    content.innerHTML = '<div class="p-8 text-center text-xs font-mono text-slate-500 font-bold">{{ __("Loading student progress metrics...") }}</div>';

    try {
        const baseUrl = "{{ url('/ajax/parent/student') }}";
        const res = await fetch(`${baseUrl}/${studentId}/progress`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (!data.success) {
            content.innerHTML = `<div class="p-6 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-xs font-bold">${data.message}</div>`;
            return;
        }

        const isAr = "{{ app()->getLocale() }}" === "ar";
        const todayStr = new Date().toLocaleDateString(isAr ? 'ar-EG' : 'en-US');

        nameEl.textContent = isAr ? `تقرير الأداء الأكاديمي للطالب: ${data.student.name}` : `Academic Performance Report: ${data.student.name}`;
        metaEl.textContent = `${data.student.grade} • ${isAr ? 'المدرسة' : 'School'}: ${data.student.school}`;
        pkgBadge.textContent = `💳 ${data.package.name} (${data.package.remaining_sessions} ${isAr ? 'حصص متبقية' : 'sessions remaining'})`;
        attBadge.textContent = `🎯 ${isAr ? 'الحضور والغياب' : 'Attendance'}: ${data.attendance.rate}`;

        const pct = Math.round((data.package.remaining_sessions / data.package.total_sessions) * 100);

        let html = `
            {{-- Official Print Report Header --}}
            <div class="print-official-header hidden flex justify-between items-center pb-4 mb-4 border-b-2 border-teal-600">
                <div class="space-y-1">
                    <h2 class="font-heading font-black text-xl text-teal-950">أكاديمية إيليت — ELITE ACADEMY</h2>
                    <p class="text-xs font-mono text-slate-600">${isAr ? 'كشف تقرير الأداء الأكاديمي الرسمي للطالب' : 'Official Student Academic Performance Report'}</p>
                </div>
                <div class="print-watermark-stamp text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1.5 rounded-xl border border-teal-300">
                    ✔ ${isAr ? 'مستند معتمد' : 'Verified Report'} — ${todayStr}
                </div>
            </div>

            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs font-semibold text-amber-900 flex items-center justify-between flex-wrap gap-2 no-print">
                <span>🔒 ${isAr ? 'هذا التقرير مخصص للمتابعة والقراءة فقط لولي الأمر. لا يمكنك تعديل التقييمات أو دفع المصروفات من هذه الشاشة.' : 'Strict Read-Only Parent Monitoring Mode. You cannot edit grades or payments.'}</span>
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 bg-amber-200 hover:bg-amber-300 text-amber-950 text-xs font-mono rounded-xl font-bold border border-amber-300 transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                    🖨 ${isAr ? 'طباعة التقرير الرسمي' : 'Print Official Certificate'}
                </button>
            </div>

            {{-- Metric Summary Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Package & Remaining Sessions Card --}}
                <div id="section-package" class="p-6 bg-teal-50 rounded-3xl border border-teal-200/80 space-y-3 shadow-sm scroll-mt-28">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-teal-800">
                        <span>💳 ${isAr ? 'الباقة ورصيد الحصص المتبقية' : 'Active Package & Session Credits'}</span>
                        <span class="bg-teal-200 text-teal-900 px-2 py-0.5 rounded-md">${data.package.status}</span>
                    </div>
                    <div>
                        <p class="font-black text-2xl text-teal-950">${data.package.remaining_sessions} ${isAr ? 'حصة متبقية' : 'sessions remaining'}</p>
                        <p class="text-xs text-teal-700 font-medium">${data.package.name}</p>
                    </div>
                    <div class="space-y-1 pt-1">
                        <div class="flex justify-between text-[11px] font-mono text-teal-700 font-bold">
                            <span>${isAr ? 'المستخدم' : 'Used'}: ${data.package.used_sessions}/${data.package.total_sessions}</span>
                            <span>${pct}% ${isAr ? 'متبقي' : 'left'}</span>
                        </div>
                        <div class="w-full h-2.5 bg-teal-200 rounded-full overflow-hidden">
                            <div class="h-full bg-teal-600 rounded-full transition-all duration-500" style="width: ${pct}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Attendance Card --}}
                <div id="section-attendance" class="p-6 bg-emerald-50 rounded-3xl border border-emerald-200/80 space-y-3 shadow-sm scroll-mt-28">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-emerald-800">
                        <span>🎯 ${isAr ? 'مؤشر الحضور والغياب' : 'Attendance & Absence Index'}</span>
                        <span class="bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded-md">${data.attendance.rate}</span>
                    </div>
                    <div>
                        <p class="font-black text-2xl text-emerald-950">${data.attendance.rate} ${isAr ? 'نسبة الحضور' : 'Attendance Rate'}</p>
                        <p class="text-xs text-emerald-700 font-medium">${data.attendance.attended_count} ${isAr ? 'حصة حضور' : 'attended'} • ${data.attendance.absences_count} ${isAr ? 'غياب' : 'absences'}</p>
                    </div>
                    <div class="pt-1 text-[11px] font-mono text-emerald-700 font-semibold">
                        ✔ ${isAr ? 'يلتزم الطالب بمواعيد البث المباشر بانتظام' : 'Student consistently attends live sessions'}
                    </div>
                </div>

                {{-- Upcoming Sessions Summary Card --}}
                <div class="p-6 bg-blue-50 rounded-3xl border border-blue-200/80 space-y-3 shadow-sm">
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-blue-800">
                        <span>📅 ${isAr ? 'الحصص القادمة' : 'Upcoming Sessions'}</span>
                        <span class="bg-blue-200 text-blue-900 px-2 py-0.5 rounded-md">${data.upcoming_sessions.length}</span>
                    </div>
                    <div>
                        <p class="font-black text-2xl text-blue-950">${data.upcoming_sessions.length} ${isAr ? 'حصص قادمة معتمدة' : 'upcoming sessions'}</p>
                        <p class="text-xs text-blue-700 font-medium">${isAr ? 'جدول البث المباشر المعتمد' : 'Scheduled live stream sessions'}</p>
                    </div>
                    <div class="pt-1 text-[11px] font-mono text-blue-700 font-semibold">
                        🔔 ${isAr ? 'تنبيهات البث تظهر للطالب في حسابه' : 'Session notifications sent to student dashboard'}
                    </div>
                </div>
            </div>

            {{-- 2 Columns: Upcoming Sessions & Homework Submissions --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-4">

                {{-- Upcoming Sessions Column --}}
                <div id="section-sessions" class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200/80 scroll-mt-28">
                    <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                        <span>📅</span> ${isAr ? 'الحصص القادمة ومواعيد البث المباشر' : 'Upcoming Live Stream Sessions'}
                    </h4>
                    <div class="space-y-3">
        `;

        if (data.upcoming_sessions.length > 0) {
            data.upcoming_sessions.forEach(s => {
                html += `
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-1.5">
                        <div class="flex justify-between items-center gap-2">
                            <span class="font-bold text-sm text-slate-900">${s.title}</span>
                            <span class="text-[11px] font-mono font-extrabold bg-blue-50 text-blue-800 border border-blue-200 px-2.5 py-0.5 rounded-lg whitespace-nowrap">${s.scheduled_at}</span>
                        </div>
                        <p class="text-xs font-mono text-slate-500">${isAr ? 'المدرس' : 'Teacher'}: ${s.teacher_name} • ${isAr ? 'المادة' : 'Subject'}: ${s.subject_name}</p>
                    </div>
                `;
            });
        } else {
            html += `<div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs text-slate-500">${isAr ? 'لا توجد حصص قادمة حالياً.' : 'No upcoming sessions scheduled.'}</div>`;
        }

        html += `
                    </div>
                </div>

                {{-- Homework Submissions Column --}}
                <div id="section-assignments" class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200/80 scroll-mt-28">
                    <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                        <span>📝</span> ${isAr ? 'الواجبات والتسليمات والدرجات' : 'Homework Submissions & Grades'}
                    </h4>
                    <div class="space-y-3">
        `;

        if (data.submissions.length > 0) {
            data.submissions.forEach(s => {
                html += `
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs space-y-1.5">
                        <div class="flex justify-between items-center gap-2">
                            <span class="font-bold text-sm text-slate-900">${s.assignment_title}</span>
                            <span class="text-[11px] font-mono font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 rounded-lg whitespace-nowrap">${s.grade}</span>
                        </div>
                        <p class="text-xs font-mono text-slate-500">${isAr ? 'تاريخ التسليم' : 'Submitted At'}: ${s.submitted_at}</p>
                    </div>
                `;
            });
        } else {
            html += `<div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs text-slate-500">${isAr ? 'لا توجد تسليمات واجبات مسجلة.' : 'No homework submissions recorded.'}</div>`;
        }

        html += `
                    </div>
                </div>
            </div>

            {{-- Academic Notifications Section --}}
            <div class="space-y-4 pt-6 border-t border-slate-100">
                <h4 class="font-bold text-sm text-slate-900 uppercase tracking-wider font-mono flex items-center gap-2">
                    <span>🔔</span> ${isAr ? 'الإشعارات والتنبيهات الأكاديمية الخاصة بالابن' : 'Student Academic Notifications & Alerts'}
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        `;

        data.notifications.forEach(n => {
            html += `
                <div class="p-4 bg-teal-50/70 rounded-2xl border border-teal-200/80 space-y-1">
                    <div class="flex justify-between items-center text-[11px] font-mono font-bold text-teal-800">
                        <span>${n.title}</span>
                        <span>${n.time}</span>
                    </div>
                    <p class="text-xs text-slate-800 font-semibold">${n.message}</p>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        content.innerHTML = html;

        // Scroll to target hash if requested
        const hash = window.location.hash.replace('#', '');
        if (hash) {
            setTimeout(() => {
                const el = document.getElementById(hash);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 200);
        }
    } catch (e) {
        content.innerHTML = '<div class="p-6 bg-red-50 text-red-700 rounded-2xl border border-red-200 text-xs font-bold">Network error while fetching student progress data.</div>';
    }
}
</script>
@endsection

```

---

## File: `resources/views/pages/register.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-20 px-4 bg-[#FAFAF9]">
    <div class="max-w-md mx-auto bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl font-bold mx-auto border border-teal-100 shadow-xs">🎓</div>
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create an Account' }}
            </h1>
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'انضم إلى أكاديمية النخبة وابدأ رحلة التعلم التفاعلي.' : 'Join Elite Academy and start interactive learning.' }}
            </p>
        </div>

        {{-- Fallback Error / Success Alert Container --}}
        <div id="authAlert" class="hidden p-3.5 rounded-2xl text-xs font-semibold"></div>

        {{-- Dedicated Registration Form --}}
        <form id="registerForm" action="{{ route('ajax.register') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }}</label>
                <input type="text" name="name" required placeholder="{{ app()->getLocale() === 'ar' ? 'مثل: أحمد محمود' : 'e.g. David Kovacs' }}" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                <input type="email" name="email" required placeholder="name@example.com" class="input-mobile">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span>{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</span>
                    <span class="text-[10px] text-rose-500 font-bold">* {{ app()->getLocale() === 'ar' ? 'مطلوب لربط ولي الأمر' : 'Required' }}</span>
                </label>
                <input type="tel" name="phone" required placeholder="{{ app()->getLocale() === 'ar' ? '01000000000' : '+1234567890' }}" class="input-mobile">
            </div>

            <div class="space-y-4 pt-1">
                <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span>{{ app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account Type' }}</span>
                    <span class="text-[10px] font-semibold text-teal-600 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-100/80">
                        {{ app()->getLocale() === 'ar' ? 'حدد دورك' : 'Select Role' }}
                    </span>
                </label>

                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 py-1">
                    <!-- Student Card (Default Checked) -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="student" checked class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">🎓</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'طالب' : 'Student' }}
                            </span>
                        </div>
                    </label>

                    <!-- Parent Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="parent" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍👩‍👧</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'ولي أمر' : 'Parent' }}
                            </span>
                        </div>
                    </label>

                    <!-- Teacher Card -->
                    <label class="relative group cursor-pointer select-none block">
                        <input type="radio" name="user_type" value="teacher" class="peer account-type-radio" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="toggleStudentGrade(this.value)">
                        <div class="h-full p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 bg-white hover:border-teal-400 transition-all duration-200 ease-out flex flex-col items-center justify-center text-center space-y-1.5 peer-checked:border-teal-600 peer-checked:bg-teal-50/50 peer-checked:shadow-md peer-checked:shadow-teal-500/10 peer-checked:ring-2 peer-checked:ring-teal-500/20 active:scale-95">
                            <div class="text-2xl sm:text-3xl transition-transform duration-200 group-hover:scale-110 peer-checked:scale-110">👨‍🏫</div>
                            <span class="text-xs font-bold text-slate-700 peer-checked:text-teal-900 transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'معلم' : 'Teacher' }}
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Student-Specific Fields: Grade Level & School Name --}}
            <div id="studentFieldsGroup" class="space-y-4 pt-2">
                {{-- Grade Level Select --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 flex items-center justify-between">
                        <span>{{ app()->getLocale() === 'ar' ? 'الصف الدراسي' : 'Grade Level' }}</span>
                        <span class="text-[10px] text-teal-600 font-bold">* {{ app()->getLocale() === 'ar' ? 'مطلوب للطالب' : 'Required for Student' }}</span>
                    </label>
                    <select name="grade_level_id" class="w-full bg-white border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 shadow-xs">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الصف الدراسي...' : 'Select Grade Level...' }}</option>
                        @foreach($gradeLevels ?? [] as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- School Name --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'اسم المدرسة (اختياري)' : 'School Name (Optional)' }}</label>
                    <input type="text" name="school_name" placeholder="{{ app()->getLocale() === 'ar' ? 'مثل: مدرسة المتفوقين STEM' : 'e.g. Cairo International School' }}" class="input-mobile">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" required placeholder="••••••••" class="input-mobile">
            </div>

            <button type="submit" id="submitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20 touch-press mt-2 flex items-center justify-center gap-2 font-bold py-3.5 rounded-2xl">
                <span>{{ app()->getLocale() === 'ar' ? 'إنشاء الحساب والانضمام' : 'Create Account & Start' }}</span>
                <span class="arrow-icon">&rarr;</span>
            </button>
        </form>

        {{-- Separate Login Redirection Link --}}
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500 font-mono">
                {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already have an account?' }}
                <a href="{{ route('login') }}" class="font-bold text-teal-600 hover:text-teal-700 hover:underline ml-1">
                    {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول ←' : 'Log In to Portal &rarr;' }}
                </a>
            </p>
        </div>
    </div>
</section>

<script>
function toggleStudentGrade(val) {
    const studentGroup = document.getElementById('studentFieldsGroup');
    if (studentGroup) {
        studentGroup.style.display = (val === 'student') ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('authAlert');
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    
    function showNotification(msg, isError = true, title = null) {
        if (window.Toast) {
            if (isError) {
                window.Toast.error(msg, title || (document.documentElement.lang === 'ar' ? 'فشل إنشاء الحساب' : 'Registration Failed'));
            } else {
                window.Toast.success(msg, title || (document.documentElement.lang === 'ar' ? 'تم إنشاء الحساب' : 'Registration Successful'));
            }
        } else if (alertBox) {
            alertBox.className = `p-3.5 rounded-2xl text-xs font-semibold ${isError ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}`;
            alertBox.textContent = msg;
            alertBox.classList.remove('hidden');
        }
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (alertBox) alertBox.classList.add('hidden');

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    let errMsg = data.message || (document.documentElement.lang === 'ar' ? 'فشل إنشاء الحساب. يرجى التأكد من الحقول المدخلة.' : 'Registration failed. Please check input fields.');
                    if (data.errors) {
                        const errs = Object.values(data.errors).flat();
                        if (errs.length > 0) errMsg = errs;
                    }
                    showNotification(errMsg, true);
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                showNotification(data.message || (document.documentElement.lang === 'ar' ? 'تم إنشاء الحساب بنجاح! جاري التوجيه...' : 'Account created successfully! Redirecting...'), false);
                
                setTimeout(() => {
                    window.location.href = data.redirect_url || '/student-portal';
                }, 900);
            } catch (err) {
                showNotification(document.documentElement.lang === 'ar' ? 'حدث خطأ في الاتصال بالشبكة. يرجى المحاولة لاحقاً.' : 'Network connection error. Please try again.', true);
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endsection

```

---

## File: `resources/views/pages/student-assignment-take.blade.php`

```blade
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* Elite Academy Signature Teal/Emerald Theme System */
.quiz-page-bg {
    background-color: #FAFAF9;
    min-height: 100vh;
}

.quiz-main-card {
    background: #FFFFFF;
    border-radius: 32px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06), 0 0 1px rgba(15, 23, 42, 0.1);
}

.option-card-elite {
    border: 2px solid #E2E8F0;
    border-radius: 20px;
    background: #FFFFFF;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}

.option-card-elite:hover {
    border-color: #0D9488;
    background-color: #F0FDF4;
}

.option-card-elite.selected {
    border-color: #0D9488 !important;
    background-color: #F0FDF4 !important;
    box-shadow: 0 6px 16px rgba(13, 148, 136, 0.15) !important;
}

.btn-elite-primary {
    background-color: #0D9488;
    color: #FFFFFF;
    border-radius: 14px;
    transition: all 0.2s ease;
}

.btn-elite-primary:hover {
    background-color: #0F766E;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(13, 148, 136, 0.25);
}

.btn-elite-nav {
    border: 2px solid #E2E8F0;
    background: #FFFFFF;
    color: #1E293B;
    border-radius: 14px;
    transition: all 0.2s ease;
}

.btn-elite-nav:hover:not(:disabled) {
    border-color: #0D9488;
    background: #F0FDF4;
}

.btn-elite-nav.active-step {
    background-color: #0D9488 !important;
    border-color: #0D9488 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
}

.unselectable {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}
</style>
@endpush

@section('content')
<section class="quiz-page-bg py-6 sm:py-10 px-4 sm:px-6 lg:px-8 min-h-screen unselectable" oncontextmenu="return false;" oncopy="return false;" oncut="return false;" ondragstart="return false;">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Top Elite Academy Brand & User Profile Header --}}
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('student-portal') }}" class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight hover:opacity-80 transition-opacity flex items-center gap-2">
                    <span class="text-teal-600">Elite</span> Academy<span class="text-teal-500">.</span>
                </a>
            </div>

            {{-- User Profile Pill --}}
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center text-sm shadow-sm overflow-hidden border-2 border-teal-300">
                    {{ mb_substr(auth()->user()->name ?? 'S', 0, 1) }}
                </div>
                <div class="text-left text-xs leading-tight">
                    <h4 class="font-extrabold text-slate-900">{{ auth()->user()->name ?? 'Learner' }}</h4>
                    <span class="font-mono text-slate-500 text-[11px] block">ID: {{ auth()->user()->id ?? '1001' }}</span>
                </div>
            </div>
        </div>

        {{-- Main Quiz Card Container --}}
        <form id="eliteQuizForm" action="{{ route('ajax.assignment.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

            <div class="quiz-main-card p-6 sm:p-10 md:p-12 relative overflow-hidden space-y-8">
                
                {{-- Quiz Top Bar: Timer & Submit Button --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-200/80 flex items-center justify-center text-teal-700 text-xl shadow-xs">
                            🕒
                        </div>
                        <div>
                            <span class="text-[11px] font-mono font-bold text-slate-400 uppercase tracking-wider block">Time remaining</span>
                            <span id="quizTimer" class="font-mono font-black text-slate-900 text-base sm:text-lg">00 : 30 : 00</span>
                        </div>
                    </div>

                    <button type="submit" id="submitQuizBtn" class="btn-elite-primary px-8 py-3 font-bold text-sm shadow-md cursor-pointer flex items-center gap-2">
                        Submit
                    </button>
                </div>

                {{-- Main Quiz Body (Questions + Circular Gauge) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center min-h-[320px]">
                    
                    {{-- Left 8 Columns: Question & Options Grid --}}
                    <div class="lg:col-span-8 space-y-6">
                        @forelse($assignment->questions as $index => $q)
                            <div id="questionStep{{ $index }}" class="question-step space-y-6 {{ $index === 0 ? '' : 'hidden' }}" data-step="{{ $index }}">
                                
                                {{-- Question Number Tag --}}
                                <div class="space-y-1">
                                    <span class="text-xs font-mono font-extrabold text-slate-500 block">
                                        Question <span class="text-teal-700">{{ $index + 1 }}</span> of {{ count($assignment->questions) }}
                                    </span>
                                    <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 leading-snug math-render">
                                        {{ $q->question_text }}
                                    </h3>

                                    @if($q->image_path)
                                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 p-3 max-w-lg my-3 shadow-xs">
                                            <img src="{{ asset('storage/' . $q->image_path) }}" class="max-h-56 rounded-xl object-contain pointer-events-none">
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Option Cards (2x2 Grid on Desktop) --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                    @foreach($q->options as $optIndex => $opt)
                                        @php
                                            $inputType = $q->is_multiple_choice ? 'checkbox' : 'radio';
                                            $letter = chr(65 + $optIndex); // A, B, C, D
                                            $savedOptIds = $savedAnswers[(string) $q->id] ?? $savedAnswers[(int) $q->id] ?? [];
                                            if (is_string($savedOptIds)) {
                                                $savedOptIds = json_decode($savedOptIds, true) ?: [$savedOptIds];
                                            }
                                            $savedOptIdsInt = array_map('intval', (array) $savedOptIds);
                                            $isChecked = in_array((int) $opt->id, $savedOptIdsInt, true);
                                        @endphp
                                        <label class="option-label option-card-elite p-4 sm:p-5 flex items-center justify-between cursor-pointer text-sm font-semibold text-slate-800 shadow-xs {{ $isChecked ? 'selected' : '' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="{{ $inputType }}" name="answers[{{ $q->id }}][]" value="{{ $opt->id }}" {{ $isChecked ? 'checked' : '' }} class="option-input accent-teal-600 w-4 h-4 cursor-pointer">
                                                
                                                <span class="option-letter-badge font-mono font-bold text-slate-400 w-6">
                                                    {{ $letter }}.
                                                </span>

                                                <span class="math-render leading-relaxed text-sm sm:text-base font-bold text-slate-800">{{ $opt->option_text }}</span>
                                            </div>

                                            @if($opt->image_path)
                                                <img src="{{ asset('storage/' . $opt->image_path) }}" class="h-8 rounded border border-slate-200 pointer-events-none">
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 font-mono text-xs">
                                No questions configured for this assignment yet.
                            </div>
                        @endforelse
                    </div>

                    {{-- Right 4 Columns: Circular Gauge Progress Ring --}}
                    <div class="lg:col-span-4 flex flex-col items-center justify-center p-4">
                        <div class="relative w-44 h-44 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="48" stroke="#E2E8F0" stroke-width="12" fill="transparent" />
                                <circle id="gaugeRingFill" cx="60" cy="60" r="48" stroke="#0D9488" stroke-width="12" fill="transparent"
                                        stroke-dasharray="301.59" stroke-dashoffset="271.43" stroke-linecap="round" class="transition-all duration-500 ease-out" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <span id="gaugeText" class="font-heading font-black text-3xl sm:text-4xl text-slate-900 tracking-tight">1/{{ count($assignment->questions) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom Navigation Toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-100">
                    <button type="button" id="prevBtn" disabled class="btn-elite-nav px-6 py-3 text-xs font-bold font-mono disabled:opacity-40 disabled:cursor-not-allowed">
                        Prev
                    </button>

                    {{-- Question Numbers Grid Map (1, 2, 3, 4...) --}}
                    <div class="flex flex-wrap items-center justify-center gap-2 overflow-x-auto py-1" id="dotsContainer">
                        @foreach($assignment->questions as $i => $q)
                            <button type="button" data-step-index="{{ $i }}" class="dot-item btn-elite-nav w-10 h-10 text-xs font-bold font-mono flex items-center justify-center {{ $i === 0 ? 'active-step' : '' }}">
                                {{ $i + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <button type="button" id="nextBtn" class="btn-elite-nav px-8 py-3 text-xs font-bold font-mono">
                        Next
                    </button>
                </div>

            </div>
        </form>

    </div>
</section>

{{-- Result Breakdown Modal (Displays Full Scores & Evaluation Breakdown on Screen) --}}
<div id="resultModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 border border-slate-200 text-center">
        
        {{-- Passed/Failed Icon Badge --}}
        <div id="resultIconBadge" class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4">
            🎉
        </div>

        <div class="space-y-2">
            <h3 id="resultTitle" class="font-heading font-black text-2xl text-slate-900">Assignment Evaluated</h3>
            <p id="resultMessage" class="text-xs font-mono text-slate-600 leading-relaxed"></p>
        </div>

        {{-- Score Numbers Breakdown Grid --}}
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 font-mono text-xs">
            <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100">
                <span class="text-slate-400 uppercase text-[10px] font-bold block">Final Percentage</span>
                <span id="resultPercentage" class="font-black text-2xl text-teal-600">100%</span>
            </div>
            <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100">
                <span class="text-slate-400 uppercase text-[10px] font-bold block">Points Earned</span>
                <span id="resultScore" class="font-black text-2xl text-slate-900">10 / 10</span>
            </div>
        </div>

        {{-- Modal Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('student-portal') }}" class="w-full sm:w-auto btn-elite-primary px-8 py-3 font-bold text-xs shadow-md">
                Go to Student Portal &rarr;
            </a>
            <button type="button" onclick="closeResultModal()" class="w-full sm:w-auto btn-elite-nav px-6 py-3 font-bold text-xs">
                Review Questions
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>

<script>
window.currentStep = {{ $currentStepIndex ?? 0 }};
window.totalSteps = {{ count($assignment->questions) }};
window.durationMinutes = {{ $assignment->duration_minutes ?? 30 }};
window.timerSeconds = {{ $remainingSeconds ?? 1800 }};
window.assignmentId = {{ $assignment->id }};
window.savedAnswers = @json($savedAnswers ?? []);

window.triggerKaTeXRender = function() {
    if (window.renderMathInElement) {
        window.renderMathInElement(document.body, {
            delimiters: [
                {left: '$$', right: '$$', display: true},
                {left: '$', right: '$', display: false},
                {left: '\\(', right: '\\)', display: false},
                {left: '\\[', right: '\\]', display: true}
            ],
            throwOnError: false
        });
    }
};

window.persistStepIndexToServer = async function(stepIdx) {
    try {
        await fetch("{{ route('ajax.assignment.update-step') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                assignment_id: window.assignmentId,
                current_step_index: parseInt(stepIdx, 10)
            })
        });
    } catch (e) {}
};

window.restoreSavedAnswers = function() {
    if (!window.savedAnswers || typeof window.savedAnswers !== 'object') return;

    for (const [questionId, selectedOpts] of Object.entries(window.savedAnswers)) {
        if (!selectedOpts) continue;
        let optsArray = selectedOpts;
        if (typeof optsArray === 'string') {
            try { optsArray = JSON.parse(optsArray); } catch(e) { optsArray = [optsArray]; }
        }
        if (!Array.isArray(optsArray)) {
            optsArray = [optsArray];
        }
        if (optsArray.length === 0) continue;

        const inputs = document.querySelectorAll(`input[name="answers[${questionId}][]"]`);
        inputs.forEach(input => {
            const valInt = parseInt(input.value, 10);
            const valStr = String(input.value);
            const isMatch = optsArray.some(opt => opt == valInt || opt == valStr);

            if (isMatch) {
                input.checked = true;
                const label = input.closest('.option-card-elite');
                if (label) label.classList.add('selected');
            }
        });
    }

    // Set step to server-restored currentStepIndex
    window.currentStep = {{ $currentStepIndex ?? 0 }};
    window.updateStepUI();
};

window.saveDraftTimers = window.saveDraftTimers || {};

window.saveDraftAnswerToServer = function(questionId, selectedOptionIds) {
    const qIdStr = String(questionId);
    if (window.saveDraftTimers[qIdStr]) {
        clearTimeout(window.saveDraftTimers[qIdStr]);
    }

    window.saveDraftTimers[qIdStr] = setTimeout(async function() {
        try {
            const res = await fetch("{{ route('ajax.assignment.save-answer') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    assignment_id: window.assignmentId,
                    question_id: parseInt(questionId, 10),
                    selected_option_ids: selectedOptionIds
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                window.queueOfflineDraft(questionId, selectedOptionIds);
            }
        } catch (err) {
            window.queueOfflineDraft(questionId, selectedOptionIds);
        }
    }, 500);
};

window.queueOfflineDraft = function(questionId, selectedOptionIds) {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queue = JSON.parse(localStorage.getItem(key) || '{}');
        queue[questionId] = selectedOptionIds;
        localStorage.setItem(key, JSON.stringify(queue));
    } catch (e) {}
};

window.flushOfflineDrafts = async function() {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queueStr = localStorage.getItem(key);
        if (!queueStr) return;
        const queue = JSON.parse(queueStr);

        for (const [qId, optIds] of Object.entries(queue)) {
            await window.saveDraftAnswerToServer(qId, optIds);
        }
        localStorage.removeItem(key);
    } catch (e) {}
};

window.addEventListener('online', window.flushOfflineDrafts);

window.syncOptionUI = function(input) {
    const labelEl = input.closest('.option-card-elite');
    if (!labelEl) return;

    const parentContainer = labelEl.closest('.question-step');
    const isMulti = input.type === 'checkbox';
    const questionIdMatch = input.name.match(/answers\[(\d+)\]/);
    const questionId = questionIdMatch ? questionIdMatch[1] : null;

    if (!isMulti) {
        // Single choice: update selected class on all option cards for this question
        parentContainer.querySelectorAll('.option-card-elite').forEach(card => {
            const cardInput = card.querySelector('input');
            if (cardInput && cardInput.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    } else {
        if (input.checked) {
            labelEl.classList.add('selected');
        } else {
            labelEl.classList.remove('selected');
        }
    }

    // Collect all checked option IDs for this question
    if (questionId) {
        const selectedOptionIds = [];
        parentContainer.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`).forEach(checkedInput => {
            selectedOptionIds.push(parseInt(checkedInput.value, 10));
        });

        // Trigger real-time server auto-save
        window.saveDraftAnswerToServer(questionId, selectedOptionIds);
    }

    // Update bottom map indicators
    window.updateStepUI();

    // Auto-advance to Next Question on single choice selection after 350ms
    if (!isMulti && window.currentStep < window.totalSteps - 1) {
        setTimeout(() => {
            window.navigateStep(1);
        }, 350);
    }
};

window.updateStepUI = function() {
    document.querySelectorAll('.question-step').forEach((step, idx) => {
        if (idx === window.currentStep) {
            step.classList.remove('hidden');
        } else {
            step.classList.add('hidden');
        }
    });

    // Update Circular Gauge Ring
    const gaugeText = document.getElementById('gaugeText');
    const ringFill = document.getElementById('gaugeRingFill');
    if (gaugeText) gaugeText.textContent = `${window.currentStep + 1}/${window.totalSteps}`;

    if (ringFill) {
        const circumference = 301.59;
        const progress = (window.currentStep + 1) / window.totalSteps;
        const offset = circumference * (1 - progress);
        ringFill.style.strokeDashoffset = offset;
    }

    // Buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) prevBtn.disabled = window.currentStep === 0;
    if (nextBtn) nextBtn.disabled = window.currentStep === window.totalSteps - 1;

    // Dots & Map Items
    document.querySelectorAll('.dot-item').forEach((dot) => {
        const idx = parseInt(dot.getAttribute('data-step-index') || '0', 10);
        if (idx === window.currentStep) {
            dot.classList.add('active-step');
        } else {
            dot.classList.remove('active-step');
        }

        if (window.isStepAnswered(idx)) {
            dot.classList.add('border-teal-600', 'bg-teal-50', 'text-teal-700');
        } else {
            dot.classList.remove('border-teal-600', 'bg-teal-50', 'text-teal-700');
        }
    });

    window.triggerKaTeXRender();
};

window.isStepAnswered = function(stepIndex) {
    const stepEl = document.querySelector(`.question-step[data-step="${stepIndex}"]`);
    if (!stepEl) return true;
    return stepEl.querySelector('input:checked') !== null;
};

window.navigateStep = function(direction) {
    // If going forward, enforce that current question MUST be answered first
    if (direction > 0) {
        if (!window.isStepAnswered(window.currentStep)) {
            if (window.Toast) {
                window.Toast.warning(
                    "{{ app()->getLocale() === 'ar' ? '⚠️ يرجى اختيار إجابة للسؤال الحالي أولاً قبل الانتقال للسؤال التالي.' : '⚠️ Please select an answer for the current question before advancing.' }}",
                    "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                );
            }
            return false;
        }
    }

    const newStep = window.currentStep + direction;
    if (newStep >= 0 && newStep < window.totalSteps) {
        window.currentStep = newStep;
        window.updateStepUI();
        window.persistStepIndexToServer(newStep);
        return true;
    }
    return false;
};

window.jumpToStep = function(targetStepIdx) {
    if (targetStepIdx === window.currentStep) return;

    // If jumping forward, verify all previous questions up to targetStepIdx are answered
    if (targetStepIdx > window.currentStep) {
        for (let i = 0; i < targetStepIdx; i++) {
            if (!window.isStepAnswered(i)) {
                if (window.Toast) {
                    window.Toast.warning(
                        `{{ app()->getLocale() === 'ar' ? '⚠️ يرجى إجابة السؤال رقم (' : '⚠️ Please answer question #' }}${i + 1}{{ app()->getLocale() === 'ar' ? ') أولاً قبل الانتقال لأسئلة لاحقة.' : ' first before skipping ahead.' }}`,
                        "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                    );
                }
                window.currentStep = i;
                window.updateStepUI();
                window.persistStepIndexToServer(i);
                return;
            }
        }
    }

    if (targetStepIdx >= 0 && targetStepIdx < window.totalSteps) {
        window.currentStep = targetStepIdx;
        window.updateStepUI();
        window.persistStepIndexToServer(targetStepIdx);
    }
};

window.showResultModal = function(data) {
    const modal = document.getElementById('resultModal');
    const badge = document.getElementById('resultIconBadge');
    const title = document.getElementById('resultTitle');
    const msg = document.getElementById('resultMessage');
    const perc = document.getElementById('resultPercentage');
    const score = document.getElementById('resultScore');

    if (!modal) return;

    if (data.is_passed) {
        badge.textContent = '🎉';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-emerald-50 text-emerald-600 border-emerald-300';
        title.textContent = 'Passed Successfully! ✓';
        title.className = 'font-heading font-black text-2xl text-emerald-700';
    } else {
        badge.textContent = '⚠️';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-rose-50 text-rose-600 border-rose-300';
        title.textContent = 'Did Not Pass ✕';
        title.className = 'font-heading font-black text-2xl text-rose-700';
    }

    if (msg) msg.textContent = data.message || 'Evaluation completed.';
    if (perc) perc.textContent = `${Math.round(data.percentage || 0)}%`;
    if (score) score.textContent = `${data.score || 0} / ${data.total_points || 10}`;

    modal.classList.remove('hidden');
};

window.closeResultModal = function() {
    const modal = document.getElementById('resultModal');
    if (modal) modal.classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', function () {
    // Next Button listener
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(1);
        });
    }

    // Prev Button listener
    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(-1);
        });
    }

    // Option Input change listeners (Native reliable checking)
    document.querySelectorAll('.option-input').forEach(input => {
        input.addEventListener('change', function() {
            window.syncOptionUI(this);
        });
    });

    // Dot navigation map listeners
    document.querySelectorAll('.dot-item').forEach(dot => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            const stepIdx = parseInt(this.getAttribute('data-step-index') || '0', 10);
            window.jumpToStep(stepIdx);
        });
    });

    // Timer (00:30:00 Format)
    setInterval(() => {
        if (window.timerSeconds <= 0) return;
        window.timerSeconds--;
        const hrs = Math.floor(window.timerSeconds / 3600);
        const mins = Math.floor((window.timerSeconds % 3600) / 60);
        const secs = Math.floor(window.timerSeconds % 60);
        const timerEl = document.getElementById('quizTimer');
        if (timerEl) {
            timerEl.textContent = `${String(hrs).padStart(2, '0')} : ${String(mins).padStart(2, '0')} : ${String(secs).padStart(2, '0')}`;
        }
    }, 1000);

    // Restore pre-saved draft answers from server
    window.restoreSavedAnswers();
    window.flushOfflineDrafts();

    // Form Submission AJAX
    const quizForm = document.getElementById('eliteQuizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitQuizBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Evaluating...';
            }

            try {
                const formData = new FormData(quizForm);
                const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (window.Toast) window.Toast.error(data.message || 'Evaluation failed.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                    }
                    return;
                }

                if (window.Toast) {
                    if (data.is_passed) {
                        window.Toast.success(`Score: ${data.percentage}% (PASSED ✓)`, 'Assignment Complete!');
                    } else {
                        window.Toast.error(`Score: ${data.percentage}% (FAILED ✕)`, 'Assignment Result');
                    }
                }

                // Show On-Screen Results Modal Directly
                window.showResultModal(data);

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Results Evaluated ✓';
                }
            } catch (err) {
                if (window.Toast) window.Toast.error('Network error during evaluation submission.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }
            }
        });
    }

    window.updateStepUI();
});
</script>
@endpush
@endsection

```

---

## File: `resources/views/pages/student-meeting.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-8 md:py-12 bg-slate-900 min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal'), 'route' => 'student-portal'],
                ['label' => $session->title ?: (app()->getLocale() === 'ar' ? 'البث المباشر' : 'Live Meeting')],
            ]
        ])

        <div class="flex items-center justify-between">
            <h1 class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight">
                {{ $session->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream Session') }}
            </h1>
            <a href="{{ route('student-portal') }}" class="btn-lift px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold font-mono transition-all">
                ← {{ app()->getLocale() === 'ar' ? 'العودة للمنصة' : 'Back to Dashboard' }}
            </a>
        </div>

        {{-- Embedded Meeting Container Component --}}
        @include('components.meeting-container', ['session' => $session, 'user' => $user])
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/student-portal.blade.php`

```blade
@extends('layouts.app')

@section('content')
{{-- Ultra-Premium Glassmorphic Hero Banner --}}
<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800/80 overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.portal')],
            ]
        ])

        {{-- Learner Header Info --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-2xl sm:text-3xl flex items-center justify-center shadow-lg shadow-teal-500/20 border-2 border-teal-300/40">
                        {{ mb_substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-slate-950 flex items-center justify-center text-[9px] font-bold">✓</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-1 rounded-full border border-teal-700/60 shadow-xs">
                            {{ __('app.student_portal') }}
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60">
                            ● Active Enrollment
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-4xl font-black text-white tracking-tight">
                        {{ __('app.portal.welcome_back') }}، <span class="bg-gradient-to-r from-teal-300 to-emerald-400 bg-clip-text text-transparent underline decoration-orange-500 decoration-2 underline-offset-8">{{ auth()->user()->name ?? (app()->getLocale() === 'ar' ? 'طالبنا المتميز' : 'Learner') }}!</span>
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-2 pt-1">
                        <span>🎓 {{ __('app.portal.grade_level') }}: <strong class="text-teal-300">{{ $studentProfile?->gradeLevel?->name ?: (app()->getLocale() === 'ar' ? 'الصف الثالث الثانوي (الثانوية العامة & STEM)' : 'Grade 12 STEM') }}</strong></span>
                        <span class="text-slate-600">•</span>
                        <span>🏫 {{ __('app.portal.school') }}: <strong class="text-slate-200">{{ $studentProfile?->school_name ?: 'Elite STEM Academy Cairo' }}</strong></span>
                    </p>
                </div>
            </div>

            {{-- Quick Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button id="btn30sTestPush" onclick="trigger30SecTestPush()" class="btn-lift px-5 py-3 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-teal-500/20 cursor-pointer flex items-center gap-2 transition-all">
                    <span>🚀</span> {{ app()->getLocale() === 'ar' ? 'اختبار إشعار FCM (خلال 30 ثانية)' : 'Start 30s FCM Test Push' }}
                </button>
                <button onclick="document.getElementById('excuseModal').classList.remove('hidden')" class="btn-lift px-5 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-extrabold rounded-2xl shadow-lg shadow-orange-500/20 cursor-pointer flex items-center gap-2 transition-all">
                    <span>📄</span> {{ __('app.portal.submit_excuse') }}
                </button>
                <button onclick="document.getElementById('homeworkExceptionModal').classList.remove('hidden')" class="btn-lift px-5 py-3 bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-teal-600/20 cursor-pointer flex items-center gap-2 transition-all">
                    <span>📋</span> {{ __('app.portal.submit_exception') }}
                </button>
            </div>
        </div>

        {{-- 30-Second Test Push Active Countdown Banner --}}
        <div id="testPushTimerBanner" class="hidden p-4 bg-slate-900 text-white rounded-2xl border border-teal-500/40 shadow-xl flex items-center justify-between text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-teal-400 animate-ping"></span>
                <span>🔔 {{ app()->getLocale() === 'ar' ? 'اختبار الإشعارات المباشرة شغال (FCM Web Push Test Active)' : 'FCM Web Push Test Active' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span id="testPushCountdown" class="font-bold text-teal-300 text-sm">Dispatched in: 30s</span>
            </div>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 md:space-y-12">

        @if(! $hasActivePackage)
            <div class="animate-fade-in-up p-6 bg-rose-50/90 border border-rose-200 rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-5 text-rose-950 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100/90 flex items-center justify-center text-rose-600 text-2xl font-bold shrink-0">💳</div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-sm sm:text-base text-rose-950 leading-tight">{{ app()->getLocale() === 'ar' ? 'تنبيه: لا توجد باقة حصص نشطة لديك!' : 'Warning: No Active Package Subscription Found!' }}</h4>
                        <p class="text-xs font-mono text-rose-800 leading-relaxed">{{ app()->getLocale() === 'ar' ? 'يلزم الاشتراك في باقة حصص للتسجيل في الكورسات والدخول للبث المباشر والواجبات التفاعلية.' : 'An active package subscription with available session credits is required to enroll in courses, access live streams, and solve assignments.' }}</p>
                    </div>
                </div>
                <a href="{{ route('courses') }}" class="btn-lift px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-rose-600/30 whitespace-nowrap flex items-center gap-2 shrink-0">
                    <span>🛒</span> {{ app()->getLocale() === 'ar' ? 'تصفح الكورسات والباقات الآن' : 'Browse Courses & Packages' }}
                </a>
            </div>
        @endif

        {{-- 4 Modern Stat Cards with Accent Borders --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 md:mb-12">
            {{-- Package & Remaining Sessions --}}
            <div class="animate-fade-in-up stagger-1 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 {{ $hasActivePackage ? 'border-t-teal-500' : 'border-t-rose-500' }} shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold {{ $hasActivePackage ? 'text-teal-800 bg-teal-50 border-teal-200/80' : 'text-rose-800 bg-rose-50 border-rose-200/80' }} px-3 py-1 rounded-full border shadow-2xs">{{ __('app.portal.current_package') }}</span>
                    <div class="w-11 h-11 rounded-2xl {{ $hasActivePackage ? 'bg-teal-50 text-teal-600 border border-teal-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }} flex items-center justify-center text-xl shadow-2xs">💳</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if($hasActivePackage)
                        {{ $package->remaining_sessions }} {{ app()->getLocale() === 'ar' ? 'حصص متبقية' : 'Sessions Remaining' }}
                    @else
                        <span class="text-rose-600 text-lg sm:text-xl font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد باقة نشطة (0 حصة)' : 'No Active Package (0 Credits)' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 truncate pt-0.5">
                    @if($hasActivePackage)
                        {{ $package?->packageTemplate?->name ?: "Total: {$package->total_sessions} | Used: {$package->used_sessions}" }}
                    @else
                        <span class="text-rose-500 font-bold">{{ app()->getLocale() === 'ar' ? 'يلزم الاشتراك في باقة للوصول للكورسات والبث' : 'Subscription required to unlock courses & streams' }}</span>
                    @endif
                </p>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="animate-fade-in-up stagger-2 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-indigo-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-indigo-800 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200/80 shadow-2xs">{{ __('app.portal.upcoming_sessions') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl shadow-2xs">📅</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    {{ count($upcomingSessions) }} <span class="text-base font-bold text-slate-600">{{ app()->getLocale() === 'ar' ? 'حصص معتمدة' : 'Confirmed Sessions' }}</span>
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">{{ count($upcomingSessions) > 0 ? (app()->getLocale() === 'ar' ? 'مواعيد البث المباشر القادمة' : 'Upcoming Live Stream Schedule') : (app()->getLocale() === 'ar' ? 'لا توجد حصص قادمة حالياً' : 'No upcoming sessions scheduled') }}</p>
            </div>

            {{-- Attendance & Absence Rate --}}
            <div class="animate-fade-in-up stagger-3 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-emerald-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/80 shadow-2xs">{{ __('app.portal.attendance_rate') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl shadow-2xs">🎯</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if($totalSessionCount > 0)
                        {{ $attendanceRate }}% <span class="text-base font-bold text-slate-500">{{ $attendanceRate >= 80 ? (app()->getLocale() === 'ar' ? 'ممتاز' : 'Excellent') : (app()->getLocale() === 'ar' ? 'بحاجة للتحسين' : 'Needs Improvement') }}</span>
                    @else
                        <span class="text-lg text-slate-400 font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد حصص بعد' : 'No sessions yet' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">
                    {{ $attendedSessions }} {{ app()->getLocale() === 'ar' ? 'حصة حضور' : 'Attended' }} • {{ $approvedExcuses }} {{ app()->getLocale() === 'ar' ? 'أعذار مقبولة' : 'Approved Excuses' }}
                </p>
            </div>

            {{-- Homework Submissions Score --}}
            <div class="animate-fade-in-up stagger-4 glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 border-t-4 border-t-amber-500 shadow-sm hover:shadow-xl transition-all space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-extrabold text-amber-800 bg-amber-50 px-3 py-1 rounded-full border border-amber-200/80 shadow-2xs">{{ __('app.portal.homework_rate') }}</span>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl shadow-2xs">📝</div>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 leading-none pt-1">
                    @if(!is_null($avgScore))
                        {{ $avgScore }}% <span class="text-base font-bold text-slate-500">{{ $avgScore >= 80 ? (app()->getLocale() === 'ar' ? 'ممتاز' : 'Excellent') : (app()->getLocale() === 'ar' ? 'مقبول' : 'Fair') }}</span>
                    @else
                        <span class="text-lg text-slate-400 font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد درجات بعد' : 'No grades yet' }}</span>
                    @endif
                </p>
                <p class="text-xs font-mono text-slate-500 pt-0.5">{{ count($submissions) }} {{ app()->getLocale() === 'ar' ? 'واجبات تم تصحيحها' : 'Submissions Evaluated' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            {{-- Main Dashboard Column --}}
            <div class="lg:col-span-8 space-y-8 lg:space-y-10">

                {{-- 1. Upcoming Live Sessions Section --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📅</span> {{ __('app.portal.upcoming_sessions') }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'رابط الحصة التفاعلية يتفعل قبل موعد البث بـ 30 دقيقة بشرط تسليم الواجب أو طلب استثناء.' : 'Stream link activates 30 mins before start time provided homework or exception request is fulfilled.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-bold bg-teal-50 text-teal-800 px-3.5 py-1.5 rounded-full border border-teal-200/80 self-start sm:self-auto shadow-2xs">
                            🛡️ 30-Min & Prerequisite Rules Active
                        </span>
                    </div>

                    <div id="upcomingSessionsContainer" class="space-y-4">
                        @if(! $hasActivePackage)
                            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl leading-none">⚠️</span>
                                    <div>
                                        <h4 class="font-bold text-sm text-amber-900">
                                            {{ app()->getLocale() === 'ar' ? 'تنبيه الحصص التجريبية والباقات:' : 'Demo & Package Subscription Alert:' }}
                                        </h4>
                                        <p class="text-xs font-mono text-amber-800 mt-0.5">
                                            {{ app()->getLocale() === 'ar' 
                                                ? 'الكورس لا يتضمن حصة تجريبية مجانية. يلزم الاشتراك في باقة حصص لتفعيل ودخول الحصص المباشرة والمنهجية.' 
                                                : 'Course does not have a free demo session. An active package subscription is required to unlock live streams and curriculum sessions.' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('courses') }}" class="btn-lift px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold font-mono shadow-sm flex items-center gap-1.5 whitespace-nowrap self-stretch sm:self-auto justify-center">
                                    <span>🛒</span> {{ app()->getLocale() === 'ar' ? 'عرض الكورسات والتفعيل' : 'Explore Courses & Activate' }}
                                </a>
                            </div>
                        @endif

                        @forelse($upcomingSessions as $s)
                            @php
                                $state = $s->evaluateState(auth()->user());
                                $startAt = $s->effective_start_at;
                                $endAt = $s->effective_end_at;
                                $joinableAt = $s->joinable_at;
                            @endphp
                            <div class="p-5 bg-slate-50/80 hover:bg-slate-50 rounded-2xl border border-slate-200/90 space-y-4 transition-all hover:shadow-md hover:-translate-y-0.5">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($state === \App\Enums\LiveSessionState::LIVE)
                                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                        @else
                                            <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                                        @endif
                                        <h3 class="font-bold text-base text-slate-900">{{ $s->title ?: (app()->getLocale() === 'ar' ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Session') }}</h3>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold">
                                        <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full border border-blue-200 whitespace-nowrap">
                                            📅 {{ app()->getLocale() === 'ar' ? 'البداية' : 'Start' }}: {{ $startAt ? $startAt->format('Y-m-d h:i A') : 'Scheduled' }}
                                        </span>
                                        @if($startAt && $startAt->isFuture())
                                            <span class="session-countdown-pill bg-indigo-50 text-indigo-900 px-3 py-1 rounded-full border border-indigo-200 font-mono font-bold flex items-center justify-center gap-1.5 shadow-2xs whitespace-nowrap tabular-nums"
                                                  data-start-time="{{ $startAt->toIso8601String() }}"
                                                  data-join-time="{{ $joinableAt ? $joinableAt->toIso8601String() : $startAt->toIso8601String() }}">
                                                <span>⏳</span>
                                                <span class="countdown-text">{{ app()->getLocale() === 'ar' ? 'حساب الوقت...' : 'Calculating...' }}</span>
                                            </span>
                                        @endif
                                        @if($endAt)
                                            <span class="bg-slate-200/80 text-slate-800 px-3 py-1 rounded-full border border-slate-300/60 whitespace-nowrap">
                                                ⏱️ {{ app()->getLocale() === 'ar' ? 'النهاية' : 'End' }}: {{ $endAt->format('h:i A') }}
                                            </span>
                                        @endif
                                        @php
                                            $halfAt = $startAt ? $startAt->copy()->addMinutes((int) ceil(($s->duration_minutes ?: 60) / 2)) : null;
                                        @endphp
                                        @if($halfAt)
                                            <span class="bg-amber-100/90 text-amber-900 px-3 py-1 rounded-full border border-amber-300/80 whitespace-nowrap" title="{{ app()->getLocale() === 'ar' ? 'آخر موعد للدخول هو منتصف وقت الحصة' : 'Last allowed join time is half-session' }}">
                                                ⏳ {{ app()->getLocale() === 'ar' ? 'إغلاق الدخول' : 'Cutoff' }}: {{ $halfAt->format('h:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-200/70 text-xs font-mono text-slate-700">
                                    <div class="flex flex-wrap items-center gap-4">
                                        <span>👨‍🏫 {{ app()->getLocale() === 'ar' ? 'المدرس' : 'Instructor' }}: <strong>{{ $s->teacherProfile?->user?->name ?: 'Dr. Instructor' }}</strong></span>
                                        <span>📚 {{ app()->getLocale() === 'ar' ? 'المادة' : 'Subject' }}: <strong>{{ $s->subject?->name ?: 'Physics' }}</strong></span>
                                    </div>

                                    @if($state === \App\Enums\LiveSessionState::LIVE)
                                        <a href="{{ route('student.meeting.show', ['id' => $s->id]) }}" class="btn-lift px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-600/30 flex items-center gap-2">
                                            <span>🟢</span> {{ app()->getLocale() === 'ar' ? 'انضم للبث المباشر الان' : 'Join Live Stream' }}
                                        </a>
                                    @elseif($state === \App\Enums\LiveSessionState::BEFORE_JOINABLE)
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                            <span class="text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300/80 px-4 py-2 rounded-xl flex items-center gap-2 shadow-2xs" title="{{ app()->getLocale() === 'ar' ? 'رابط الدخول ينشط تلقائياً قبل 30 دقيقة من موعد الحصة' : 'Join button activates 30 minutes before start time' }}">
                                                <span>🔒</span>
                                                <span>{{ app()->getLocale() === 'ar' ? 'يتفعل الدخول:' : 'Access Opens:' }}</span>
                                                <span class="text-teal-700 font-extrabold">{{ $joinableAt ? $joinableAt->format('h:i A') : ($startAt ? $startAt->format('h:i A') : '30 mins before') }}</span>
                                            </span>
                                        </div>
                                    @elseif($state === \App\Enums\LiveSessionState::PACKAGE_REQUIRED)
                                        <a href="{{ route('courses') }}" class="btn-lift text-xs font-mono font-extrabold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200/90 px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all shadow-2xs">
                                            <span>🔒</span> {{ $state->label() }}
                                        </a>
                                    @elseif($state === \App\Enums\LiveSessionState::ENDED)
                                        <span class="text-xs font-mono font-bold bg-slate-100 text-slate-600 border border-slate-300 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>⏹️</span> {{ $state->label() }}
                                        </span>
                                    @elseif($state === \App\Enums\LiveSessionState::PREREQUISITE_REQUIRED)
                                        <span class="text-xs font-mono font-bold bg-amber-100 text-amber-900 border border-amber-300 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>⚠️</span> {{ $state->label() }}
                                        </span>
                                    @else
                                        <span class="text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300/80 px-4 py-2 rounded-xl flex items-center gap-1.5">
                                            <span>🔒</span> {{ $state->label() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-slate-500 space-y-3 bg-slate-50/50 rounded-2xl border border-slate-200/80 p-6">
                                <div class="text-4xl">🎓</div>
                                <h3 class="font-bold text-slate-800 text-base">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد حصص مجانية متوفرة حالياً' : 'No Free Demo Sessions Currently Available' }}
                                </h3>
                                <p class="text-xs font-mono text-slate-600 max-w-md mx-auto">
                                    {{ app()->getLocale() === 'ar' 
                                        ? 'تم إغلاق الحصص التجريبية المجانية لهذه الكورسات. للانضمام للحصص المنهجية المباشرة والمتابعة الأكاديمية، يرجى تفعيل باقة حصص.' 
                                        : 'Free trial demo sessions for these courses are closed. Please subscribe to an active session package to join live streams.' }}
                                </p>
                                <div class="pt-2">
                                    <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold font-mono rounded-xl shadow-md shadow-indigo-600/20">
                                        <span>🚀</span> {{ app()->getLocale() === 'ar' ? 'استكشاف الكورسات والباقات المتاحة' : 'Explore Available Courses & Packages' }}
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Pending MSQ Assignments Department (Unsubmitted Only) --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📝</span> {{ app()->getLocale() === 'ar' ? 'قسم الواجبات التفاعلية غير المجابة (Pending MSQs)' : 'Pending MSQ Assignment Department' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'تظهر هنا فقط الواجبات التي لم تقم بإجابتها بعد. بمجرد الإجابة تنتقل لسجل النتائج.' : 'Shows only unsubmitted assignments. Once answered, assignments move to submission history.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-teal-100 text-teal-900 px-3.5 py-1.5 rounded-full border border-teal-200 self-start sm:self-auto shadow-2xs">
                            {{ count($availableAssignments) }} {{ app()->getLocale() === 'ar' ? 'واجبات متبقية' : 'Pending' }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($availableAssignments as $assign)
                            <div class="p-6 bg-gradient-to-br from-teal-50/70 via-emerald-50/30 to-white rounded-3xl border border-teal-200/80 space-y-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                                
                                {{-- Course & Session Context Badges --}}
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-teal-100/80 pb-3 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-teal-900 bg-teal-100/90 px-3 py-0.5 rounded-full border border-teal-200">
                                            📚 {{ $assign->course?->title ?: (app()->getLocale() === 'ar' ? 'كورس الفيزياء الكهربية' : 'Course Domain') }}
                                        </span>
                                        <span class="font-bold text-slate-800 bg-slate-200/80 px-3 py-0.5 rounded-full">
                                            📺 {{ $assign->session?->title ?: ($assign->liveSession?->title ?: (app()->getLocale() === 'ar' ? 'الجلسة التفاعلية' : 'Interactive Session')) }}
                                        </span>
                                    </div>
                                    <span class="text-teal-700 font-bold bg-teal-100/60 px-2.5 py-0.5 rounded-md">
                                        ⏱️ {{ $assign->time_limit_minutes ?: 30 }} {{ app()->getLocale() === 'ar' ? 'دقيقة إجابة' : 'Mins Evaluation' }}
                                    </span>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-mono font-bold uppercase bg-teal-700 text-white px-2 py-0.5 rounded">MSQ Evaluation</span>
                                            <h3 class="font-bold text-base text-slate-900">{{ $assign->title }}</h3>
                                        </div>
                                        <p class="text-xs text-slate-600 font-mono leading-relaxed">{{ $assign->description ?: (app()->getLocale() === 'ar' ? 'واجب تقييمي تفاعلي لغلق فجوات الدرس والتأكد من الفهم الكامل.' : 'Interactive MSQ assignment to verify lesson mastery.') }}</p>
                                    </div>
                                    <span class="text-xs font-mono font-extrabold text-slate-800 bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-2xs self-start sm:self-auto">
                                        🎯 Total: {{ number_format($assign->total_points, 1) }} pts
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-3 text-slate-600">
                                        <span class="flex items-center gap-1 font-bold text-amber-900 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                                            ⏰ {{ app()->getLocale() === 'ar' ? 'الموعد النهائي' : 'Deadline' }}: 
                                            {{ $assign->effective_due_at ? $assign->effective_due_at->format('Y-m-d H:i') : '24h Pre-Session' }}
                                        </span>
                                        <span class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full border border-slate-200 font-bold">
                                            🔒 {{ app()->getLocale() === 'ar' ? 'محاولة واحدة فقط' : '1 Attempt Only' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('student.assignment.take', ['id' => $assign->id]) }}" class="btn-lift px-6 py-3 bg-[#0D9488] hover:bg-[#0F766E] text-black rounded-xl font-extrabold text-xs shadow-md shadow-teal-600/30 flex items-center gap-2">
                                            <span>⚡</span> {{ app()->getLocale() === 'ar' ? 'بدء حل الواجب التفاعلي' : 'Start Interactive MSQ' }}
                                        </a>
                                        <button onclick="openMsqAssignmentModal({{ $assign->id }})" class="btn-lift px-4 py-3 bg-white hover:bg-slate-100 text-slate-800 rounded-xl font-bold text-xs border border-slate-300 shadow-2xs cursor-pointer">
                                            {{ app()->getLocale() === 'ar' ? 'معاينة سريعة 👁️' : 'Quick Preview 👁️' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 bg-emerald-50/70 rounded-3xl border border-emerald-200 text-center space-y-2">
                                <div class="text-4xl animate-bounce">🎉</div>
                                <h4 class="font-bold text-lg text-emerald-950">{{ app()->getLocale() === 'ar' ? 'ممتاز! تم حل جميع الواجبات المتاحة بنجاح' : 'All Available Assignments Completed!' }}</h4>
                                <p class="text-xs font-mono text-emerald-800">{{ app()->getLocale() === 'ar' ? 'لا توجد واجبات معلقة حالياً. يمكن مراجعة النتائج والتفاصيل في قسم السجل أدناه.' : 'No pending assignments remaining. Inspect your scores and evaluations in the submission history below.' }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 2.5 Enrolled Courses & Detailed Sessions Roadmap --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📚</span> {{ app()->getLocale() === 'ar' ? 'الكورسات المشترك بها والمنهج التفصيلي' : 'My Enrolled Courses & Detailed Modules' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'استكشف المقررات الدراسية المشترك بها، وتصفح تفاصيل كل كورس، الجدول الزمني، والحصص المباشرة والمسجلة.' 
                                    : 'Explore your active enrolled courses, inspect full module roadmaps, live stream schedules, and session materials.' }}
                            </p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-teal-100 text-teal-900 px-3.5 py-1.5 rounded-full border border-teal-200 self-start sm:self-auto shadow-2xs">
                            {{ count($enrollments) }} {{ app()->getLocale() === 'ar' ? 'كورسات مسجلة' : 'Active Courses' }}
                        </span>
                    </div>

                    @if(count($enrollmentCards) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($enrollmentCards as $card)
                                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group space-y-4">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="bg-teal-600 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-xs">
                                                {{ $card['subject'] }}
                                            </span>
                                            <span class="text-[11px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                                ✓ {{ app()->getLocale() === 'ar' ? 'مشترك بنجاح' : 'Enrolled' }}
                                            </span>
                                        </div>

                                        <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
                                            {{ $card['course']->title }}
                                        </h3>

                                        <p class="text-xs text-slate-600 font-mono leading-relaxed line-clamp-2">
                                            {{ $card['course']->description ?: (app()->getLocale() === 'ar' ? 'مقرر تعليمي تفاعلي شامل للمرحلة الثانوية مع متابعة واختبارات.' : 'Comprehensive interactive curriculum with practical labs.') }}
                                        </p>

                                        <div class="flex items-center gap-2 pt-1 text-xs font-mono text-slate-700">
                                            <img src="{{ asset('images/instructor_portrait.png') }}" alt="{{ $card['teacher'] }}" class="w-6 h-6 rounded-full object-cover border border-teal-500">
                                            <span>👨‍🏫 <strong>{{ $card['teacher'] }}</strong></span>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                            <div class="flex justify-between text-[11px] font-mono font-bold text-slate-600">
                                                <span>{{ app()->getLocale() === 'ar' ? 'نسبة إنجاز المنهج' : 'Curriculum Progress' }}</span>
                                                <span class="text-teal-600 font-extrabold">{{ $card['progressPct'] }}%</span>
                                            </div>
                                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ max(8, $card['progressPct']) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 text-xs font-mono text-slate-500">
                                            <span>📹 <strong>{{ $card['recCount'] }}</strong> {{ app()->getLocale() === 'ar' ? 'دروس' : 'Lectures' }}</span>
                                            <span>•</span>
                                            <span>🟢 <strong>{{ $card['liveCount'] }}</strong> {{ app()->getLocale() === 'ar' ? 'بث مباشر' : 'Live Streams' }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <button onclick="openEnrolledCourseModal({{ $card['course']->id }})" class="btn-lift px-4 py-2 bg-slate-900 hover:bg-teal-600 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-1.5 cursor-pointer transition-all">
                                                <span>🔍</span> {{ app()->getLocale() === 'ar' ? 'التفاصيل والحصص' : 'Full Details & Sessions' }}
                                            </button>
                                            <a href="{{ route('course-details', ['slug' => $card['course']->slug]) }}" class="btn-lift px-3 py-2 bg-teal-50 hover:bg-teal-100 text-teal-800 rounded-xl text-xs font-bold border border-teal-200/80 flex items-center gap-1" title="{{ app()->getLocale() === 'ar' ? 'صفحة الكورس' : 'Course Page' }}">
                                                <span>▶</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-slate-500 space-y-3 bg-slate-50/50 rounded-2xl border border-slate-200/80 p-6">
                            <div class="text-4xl">📚</div>
                            <h3 class="font-bold text-slate-800 text-base">
                                {{ app()->getLocale() === 'ar' ? 'لم تقم بالتسجيل في أي كورس بعد' : 'No Enrolled Courses Yet' }}
                            </h3>
                            <p class="text-xs font-mono text-slate-600 max-w-md mx-auto">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'تصفح قائمة الكورسات والمناهج المتاحة في الأكاديمية وقم بالتسجيل فوراً لبدء رحلة التعلم.' 
                                    : 'Explore the full course catalog and enroll in accredited STEM programs to unlock your modules.' }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold font-mono rounded-xl shadow-md shadow-teal-600/20">
                                    <span>🚀</span> {{ app()->getLocale() === 'ar' ? 'تصفح الكورسات المتاحة الآن' : 'Browse Available Courses' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 3. Dedicated Assignment Submission History & Graded Evaluation Section --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                                <span>📜</span> {{ app()->getLocale() === 'ar' ? 'سجل تسليمات الواجبات والدرجات (Submissions History)' : 'Assignment Submission History & Graded Evaluation' }}
                            </h2>
                            <p class="text-xs font-mono text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'تظهر هنا جميع الواجبات التي تمت إجابتها مع تفاصيل الكورس والجلسة والنتيجة المحققة.' : 'Complete record of all answered assignments with course, session context, and evaluated scores.' }}</p>
                        </div>
                        <span class="text-xs font-mono font-extrabold bg-slate-100 text-slate-800 px-3.5 py-1.5 rounded-full border border-slate-200 self-start sm:self-auto shadow-2xs">
                            {{ count($submissions) }} {{ app()->getLocale() === 'ar' ? 'تسليمات سابقة' : 'Submitted' }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($submissions as $sub)
                            <div class="p-6 bg-slate-50/90 hover:bg-slate-100/80 rounded-3xl border border-slate-200/90 space-y-4 transition-all hover:shadow-md hover:-translate-y-0.5">
                                
                                {{-- Course & Session Context Header --}}
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/70 pb-3 text-xs font-mono">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-teal-900 bg-teal-100/90 px-3 py-0.5 rounded-full border border-teal-200">
                                            📚 {{ $sub->assignment?->course?->title ?: (app()->getLocale() === 'ar' ? 'كورس مادة التخصص' : 'Subject Course') }}
                                        </span>
                                        <span class="font-bold text-slate-800 bg-slate-200 px-3 py-0.5 rounded-full">
                                            📺 {{ $sub->assignment?->session?->title ?: ($sub->assignment?->liveSession?->title ?: (app()->getLocale() === 'ar' ? 'الجلسة التفاعلية' : 'Interactive Session')) }}
                                        </span>
                                    </div>
                                    <span class="text-slate-500">
                                        📅 {{ app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Submitted' }}: <strong>{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Completed' }}</strong>
                                    </span>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-base text-slate-900">{{ $sub->assignment?->title ?: (app()->getLocale() === 'ar' ? 'واجب الجلسة التفاعلية' : 'Session MSQ Assignment') }}</h4>
                                        <p class="text-xs font-mono text-slate-600 leading-relaxed">{{ $sub->evaluation_notes ?: ($sub->teacher_notes ?: (app()->getLocale() === 'ar' ? 'تم التقييم التلقائي بنجاح.' : 'Server evaluated submission cleanly.')) }}</p>
                                    </div>

                                    <div class="flex items-center gap-3 self-start sm:self-auto">
                                        <span class="text-base font-mono font-black text-slate-900 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-2xs">
                                            {{ $sub->percentage !== null ? number_format($sub->percentage, 1) . '%' : ($sub->grade !== null ? number_format($sub->grade, 1) . '%' : 'Evaluated') }}
                                        </span>
                                        @if($sub->isPassed())
                                            <span class="text-xs font-mono font-extrabold bg-emerald-100 text-emerald-900 px-4 py-2 rounded-2xl border border-emerald-300 flex items-center gap-1.5 shadow-2xs">
                                                <span>✓</span> {{ app()->getLocale() === 'ar' ? 'ناجح' : 'PASSED' }}
                                            </span>
                                        @else
                                            <span class="text-xs font-mono font-extrabold bg-rose-100 text-rose-900 px-4 py-2 rounded-2xl border border-rose-300 flex items-center gap-1.5 shadow-2xs">
                                                <span>✕</span> {{ app()->getLocale() === 'ar' ? 'لم يجتاز' : 'FAILED' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد تسليمات واجبات سابقة في السجل حتى الآن.' : 'No previous assignment submission history recorded yet.' }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Sidebar (Notifications & Exception Status) --}}
            <div class="lg:col-span-4 space-y-8">

                {{-- Notifications Feed --}}
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                            <span>🔔</span> {{ __('app.portal.notifications') }}
                        </h3>
                        <span id="notifTotalAlerts" class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/80 shadow-2xs">
                            {{ $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator ? $userNotifications->total() : count($userNotifications) }} Alerts
                        </span>
                    </div>

                    <div class="space-y-3 transition-opacity duration-200" id="notificationsFeedContainer">
                        @forelse($userNotifications as $n)
                            <div class="p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                                    @if($n->type === 'ASSIGNMENT_DEADLINE_REMINDER')
                                        <span class="text-amber-800 bg-amber-100/90 px-2.5 py-0.5 rounded-md border border-amber-200">⏰ Deadline 24h</span>
                                    @elseif($n->type === 'ADMIN_APPROVAL_ALERT')
                                        <span class="text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-md border border-emerald-200">✅ Admin Approved</span>
                                    @else
                                        <span class="text-teal-800 bg-teal-100/90 px-2.5 py-0.5 rounded-md border border-teal-200">🔔 Real-Time FCM Alert</span>
                                    @endif
                                    <span class="text-slate-400 font-normal">{{ $n->created_at ? $n->created_at->diffForHumans() : 'Just now' }}</span>
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 leading-snug">{{ $n->title }}</h4>
                                <p class="text-xs text-slate-600 leading-relaxed font-mono">{{ $n->body }}</p>
                            </div>
                        @empty
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                                <div class="text-2xl">🔕</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لا توجد إشعارات مسجلة حالياً.' : 'No notifications in feed yet.' }}</div>
                            </div>
                        @endforelse
                    </div>


                    <div id="notificationsPaginationBar" class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono {{ $notifLastPage <= 1 ? 'hidden' : '' }}">
                        <button id="btnNotifPrev" onclick="fetchNotificationsPage(notifCurrentPage - 1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                            <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                        </button>

                        <div class="text-slate-600 font-bold bg-slate-100/80 px-3.5 py-1 rounded-xl border border-slate-200/90 text-[11px] shadow-2xs">
                            {{ app()->getLocale() === 'ar' ? 'صفحة' : 'Page' }} <span id="notifCurrentPageText" class="text-teal-700 font-extrabold">{{ $notifCurrentPage }}</span> {{ app()->getLocale() === 'ar' ? 'من' : 'of' }} <span id="notifLastPageText" class="text-slate-800">{{ $notifLastPage }}</span>
                        </div>

                        <button id="btnNotifNext" onclick="fetchNotificationsPage(notifCurrentPage + 1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                            <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                        </button>
                    </div>
                </div>

                {{-- FCM Token Copy & Register Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-4 animate-fade-in-up stagger-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                            <span>🔑</span> {{ app()->getLocale() === 'ar' ? 'رمز جهاز الإشعارات (FCM Token)' : 'FCM Web Device Token' }}
                        </h3>
                        <span id="fcmTokenStatusBadge" class="text-[10px] font-mono font-bold bg-emerald-100 text-emerald-900 px-2.5 py-0.5 rounded-full border border-emerald-200">
                            🟢 Active Token
                        </span>
                    </div>

                    <p class="text-xs font-mono text-slate-500 leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'يمكنك نسخ أو لصق رمز FCM الحقيقي الخاص بك هنا وتحديثه بنقرة واحدة:' : 'Copy or paste your real FCM registration token here and update it with one click:' }}
                    </p>

                    <div class="space-y-3">
                        <input type="text" id="userFcmTokenInput" placeholder="Paste your real FCM Token (e.g. c1z...:APA91b...)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-[11px] font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white shadow-2xs transition-all">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <button onclick="registerCustomFcmToken()" class="btn-lift flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-xs shadow-md shadow-teal-600/30 whitespace-nowrap cursor-pointer flex items-center justify-center gap-1.5">
                                    <span>💾</span> <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الرمز في النظام' : 'Save / Register Token' }}</span>
                                </button>
                                <button onclick="copyFcmTokenToClipboard()" class="btn-lift px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold text-xs shadow-md whitespace-nowrap cursor-pointer flex items-center gap-1">
                                    <span>📋</span> <span>{{ app()->getLocale() === 'ar' ? 'نسخ' : 'Copy' }}</span>
                                </button>
                            </div>
                            <button onclick="requestLiveFirebaseToken()" class="btn-lift w-full py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 rounded-xl font-extrabold text-[11px] shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>🔥</span> <span>{{ app()->getLocale() === 'ar' ? 'جلب الرمز المباشر من Google Firebase' : 'Fetch Live Token from Google Firebase' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Live Firebase Push Message Log Stream --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-4 animate-fade-in-up stagger-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                            <span>📡</span> {{ app()->getLocale() === 'ar' ? 'سجل رسائل فايبربيس المباشرة' : 'Live Firebase Push Stream' }}
                        </h3>
                        <span class="text-[10px] font-mono font-bold bg-teal-100 text-teal-900 px-2.5 py-0.5 rounded-full border border-teal-200 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                        </span>
                    </div>

                    <div id="fcmRealtimeLogStream" class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-[11px] font-mono text-slate-500 text-center">
                            {{ app()->getLocale() === 'ar' ? 'في انتظار وصول أي إشعار مباشر من فايبربيس...' : 'Listening for real-time Firebase push alerts...' }}
                        </div>
                    </div>
                </div>

                <script>
                    window.addEventListener('fcm-realtime-message', function(e) {
                        const stream = document.getElementById('fcmRealtimeLogStream');
                        if (!stream) return;
                        const payload = e.detail;
                        const title = payload.notification ? payload.notification.title : (payload.data ? payload.data.title : 'Push Alert');
                        const body = payload.notification ? payload.notification.body : (payload.data ? payload.data.body : '');
                        const time = new Date().toLocaleTimeString();

                        const item = document.createElement('div');
                        item.className = 'p-3.5 bg-emerald-50/90 border border-emerald-200/90 rounded-2xl space-y-1 shadow-2xs animate-fade-in-up';
                        item.innerHTML = `
                            <div class="flex justify-between items-center text-[10px] font-mono text-emerald-900 font-bold">
                                <span>🔔 ${title}</span>
                                <span>${time}</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-snug font-mono">${body}</p>
                        `;
                        if (stream.children.length === 1 && stream.children[0].innerText.includes('Listening')) {
                            stream.innerHTML = '';
                        }
                        stream.prepend(item);
                    });

                    // Real-Time Polling for Interactive Live Sessions & Demo Changes
                    setInterval(() => {
                        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContainer = doc.querySelector('#upcomingSessionsContainer');
                                const currentContainer = document.querySelector('#upcomingSessionsContainer');
                                if (newContainer && currentContainer && newContainer.innerHTML.trim() !== currentContainer.innerHTML.trim()) {
                                    currentContainer.innerHTML = newContainer.innerHTML;
                                }
                            }).catch(() => {});
                    }, 8000);
                </script>

                {{-- Submitted Exceptions List --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-5 animate-fade-in-up stagger-2">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span>📋</span> {{ __('app.portal.exceptions_history') }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($exceptions as $exc)
                            <div class="p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-xs text-slate-900">
                                        {{ $exc->is_global || $exc->scope === 'global' ? (app()->getLocale() === 'ar' ? 'استثناء شامل (كل الكورسات)' : 'Global System Exemption') : (app()->getLocale() === 'ar' ? 'عذر كورس خاص' : 'Course Exception') }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold uppercase px-2.5 py-0.5 rounded-full {{ $exc->status === 'approved' ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : ($exc->status === 'rejected' ? 'bg-red-100 text-red-900 border border-red-200' : 'bg-amber-100 text-amber-900 border border-amber-200') }}">
                                        {{ $exc->status }}
                                    </span>
                                </div>
                                <p class="text-xs font-mono text-slate-600 truncate">{{ $exc->reason }}</p>
                            </div>
                        @empty
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                                <div class="text-xl">📋</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لا توجد طلبات استثناء سابقة.' : 'No previous exception requests found.' }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- 1. Modal: Interactive MSQ Assignment Solver --}}
<div id="takeMsqModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 border border-slate-200 my-8">
        <div class="flex justify-between items-start pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-mono font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full uppercase border border-teal-200">Interactive MSQ Evaluation</span>
                <h3 id="msqModalTitle" class="font-bold text-xl text-slate-900 mt-1">Loading Assignment...</h3>
                <p id="msqModalDesc" class="text-xs text-slate-500 font-mono"></p>
            </div>
            <button onclick="closeMsqModal()" class="text-slate-400 hover:text-slate-800 font-bold text-2xl cursor-pointer p-1">&times;</button>
        </div>

        {{-- Timer & Warning Banner --}}
        <div id="msqTimerBar" class="flex items-center justify-between p-4 bg-slate-900 text-white rounded-2xl text-xs font-mono shadow-md">
            <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                <span>Session Deadline Rule Active</span>
            </span>
            <span id="msqTimerDisplay" class="font-bold text-teal-300 text-sm">Time Remaining: --:--</span>
        </div>

        <form id="msqAnswerForm" class="space-y-6">
            @csrf
            <input type="hidden" id="msqAssignmentId" name="assignment_id" value="">

            <div id="msqQuestionsContainer" class="space-y-6 max-h-[50vh] overflow-y-auto pr-2">
                <div class="text-center py-8 text-slate-500 font-mono text-xs">Loading questions...</div>
            </div>

            <button type="submit" id="msqSubmitBtn" class="w-full btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/30 touch-press font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2">
                <span>Submit Assignment for Automated Evaluation</span> &rarr;
            </button>
        </form>
    </div>
</div>

{{-- 2. Modal: Submit Session Absence Excuse --}}
<div id="excuseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_excuse') }}</h3>
            <button onclick="document.getElementById('excuseModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="excuseAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="excuseForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="live_session_id" value="1">

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'سبب الاعتذار عن الحصة' : 'Reason for Absence' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اذكر سبب الغياب بالتفصيل...' : 'Provide detailed absence reason...' }}" class="input-mobile h-24"></textarea>
                <p class="text-[11px] text-amber-700 font-bold">⚠️ {{ __('app.sessions.excuse_2h_rule') }}</p>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال عذر الغياب' : 'Submit Absence Excuse' }}
            </button>
        </form>
    </div>
</div>

{{-- 3. Modal: Submit Homework Exception Request --}}
<div id="homeworkExceptionModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4 border border-slate-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="font-bold text-lg text-slate-900">{{ __('app.portal.submit_exception') }}</h3>
            <button onclick="document.getElementById('homeworkExceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">&times;</button>
        </div>

        <div id="hwExceptionAlert" class="hidden p-3 rounded-xl text-xs font-semibold"></div>

        <form id="hwExceptionForm" action="{{ route('ajax.exception.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'نطاق الاستثناء' : 'Exception Scope' }}</label>
                <select name="scope" id="exceptionScopeSelect" class="input-mobile" onchange="document.getElementById('courseSelectGroup').style.display = this.value === 'global' ? 'none' : 'block'">
                    <option value="course">{{ app()->getLocale() === 'ar' ? 'كورس معين محدد' : 'Single Specific Course' }}</option>
                    <option value="global">{{ app()->getLocale() === 'ar' ? 'استثناء شامل لجميع الكورسات' : 'Global System Exception (All Enrolled Courses)' }}</option>
                </select>
            </div>

            <div id="courseSelectGroup" class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'اختر الكورس المستهدف' : 'Target Enrolled Course' }}</label>
                <select name="course_id" class="input-mobile">
                    @if(isset($enrollments) && count($enrollments) > 0)
                        @foreach($enrollments as $e)
                            <option value="{{ $e->course_id }}">{{ $e->course?->title ?: 'الكورس الدراسي' }}</option>
                        @endforeach
                    @else
                        <option value="1">Physics Electromagnetism & Circuits</option>
                    @endif
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'تفاصيل مشكلة الواجب أو سبب الاستثناء' : 'Homework Exception Details' }}</label>
                <textarea name="reason" required minlength="10" placeholder="{{ app()->getLocale() === 'ar' ? 'اشرح المشكلة التقنية أو سبب طلب استثناء الواجب...' : 'Describe technical issue or homework exception...' }}" class="input-mobile h-24"></textarea>
            </div>

            <button type="submit" class="btn-mobile-lg btn-lift text-white bg-teal-600 hover:bg-teal-700 shadow-md touch-press">
                {{ app()->getLocale() === 'ar' ? 'إرسال طلب استثناء الواجب' : 'Submit Homework Exception' }}
            </button>
        </form>
    </div>
</div>

<script>
let msqTimerInterval = null;

async function openMsqAssignmentModal(assignmentId) {
    const modal = document.getElementById('takeMsqModal');
    const titleEl = document.getElementById('msqModalTitle');
    const descEl = document.getElementById('msqModalDesc');
    const container = document.getElementById('msqQuestionsContainer');
    const assignIdInput = document.getElementById('msqAssignmentId');
    
    assignIdInput.value = assignmentId;
    modal.classList.remove('hidden');
    container.innerHTML = '<div class="text-center py-8 text-slate-500 font-mono text-xs">Loading assignment questions...</div>';

    try {
        const baseUrl = "{{ url('/ajax/assignments') }}";
        const res = await fetch(`${baseUrl}/${assignmentId}/details`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            if (window.Toast) window.Toast.error(data.message || 'Error loading assignment details');
            closeMsqModal();
            return;
        }

        const assign = data.assignment;
        titleEl.textContent = assign.title;
        descEl.textContent = assign.description || 'Complete all MSQ questions before the timer runs out.';

        // Setup Timer
        let durationSecs = (assign.duration_minutes || 30) * 60;
        const timerDisplay = document.getElementById('msqTimerDisplay');
        if (msqTimerInterval) clearInterval(msqTimerInterval);

        msqTimerInterval = setInterval(() => {
            if (durationSecs <= 0) {
                clearInterval(msqTimerInterval);
                timerDisplay.textContent = 'TIME EXPIRED';
                timerDisplay.className = 'font-bold text-red-400 animate-pulse';
                if (window.Toast) window.Toast.warning('Assignment time expired!', 'Deadline Alert');
                return;
            }
            durationSecs--;
            const mins = Math.floor(durationSecs / 60);
            const secs = durationSecs % 60;
            timerDisplay.textContent = `Time Remaining: ${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }, 1000);

        if (!assign.questions || assign.questions.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-slate-500 font-mono text-xs bg-slate-50 rounded-2xl">No questions configured for this assignment yet.</div>';
            return;
        }

        let html = '';
        assign.questions.forEach((q, idx) => {
            const inputType = q.is_multiple_choice ? 'checkbox' : 'radio';
            html += `
                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-slate-900">Q${idx + 1}. ${q.question_text || ''}</span>
                        <span class="text-[10px] font-mono font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md">${q.points || 1} Points</span>
                    </div>
                    ${q.image_path ? `<img src="${q.image_path}" class="max-h-48 rounded-xl border border-slate-200 my-2 object-contain">` : ''}
                    <div class="space-y-2 pt-1">
            `;

            (q.options || []).forEach(opt => {
                html += `
                    <label class="flex items-center gap-3 p-3 bg-white hover:bg-teal-50/50 rounded-xl border border-slate-200/90 cursor-pointer transition-colors text-xs font-semibold text-slate-800">
                        <input type="${inputType}" name="answers[${q.id}][]" value="${opt.id}" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <span>${opt.option_text || ''}</span>
                    </label>
                `;
            });

            html += `</div></div>`;
        });

        container.innerHTML = html;
    } catch (err) {
        if (window.Toast) window.Toast.error('Network error loading assignment');
        closeMsqModal();
    }
}

function closeMsqModal() {
    document.getElementById('takeMsqModal').classList.add('hidden');
    if (msqTimerInterval) clearInterval(msqTimerInterval);
}

document.addEventListener('DOMContentLoaded', function () {
    const excuseForm = document.getElementById('excuseForm');
    const excuseAlert = document.getElementById('excuseAlert');
    if (excuseForm) {
        excuseForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            excuseAlert.classList.add('hidden');
            const formData = new FormData(excuseForm);

            try {
                const res = await fetch(excuseForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                excuseAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                excuseAlert.textContent = data.message;
                excuseAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('excuseModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                excuseAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                excuseAlert.textContent = 'Network error. Please try again.';
                excuseAlert.classList.remove('hidden');
            }
        });
    }

    const hwForm = document.getElementById('hwExceptionForm');
    const hwAlert = document.getElementById('hwExceptionAlert');
    if (hwForm) {
        hwForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            hwAlert.classList.add('hidden');
            const formData = new FormData(hwForm);

            try {
                const res = await fetch(hwForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                hwAlert.className = `p-3 rounded-xl text-xs font-semibold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;
                hwAlert.textContent = data.message;
                hwAlert.classList.remove('hidden');

                if (data.success) {
                    setTimeout(() => document.getElementById('homeworkExceptionModal').classList.add('hidden'), 1500);
                }
            } catch (err) {
                hwAlert.className = 'p-3 rounded-xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200';
                hwAlert.textContent = 'Network error. Please try again.';
                hwAlert.classList.remove('hidden');
            }
        });
    }

    const msqForm = document.getElementById('msqAnswerForm');
    const submitBtn = document.getElementById('msqSubmitBtn');

    if (msqForm) {
        msqForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const formData = new FormData(msqForm);
                const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (window.Toast) window.Toast.error(data.message || 'Submission failed');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    return;
                }

                if (window.Toast) {
                    if (data.is_passed) {
                        window.Toast.success(`Score: ${data.percentage}% (PASSED ✓)`, 'Assignment Completed!');
                    } else {
                        window.Toast.error(`Score: ${data.percentage}% (FAILED ✕ - Passing: ${data.passing_score}%)`, 'Assignment Result');
                    }
                }

                closeMsqModal();
                setTimeout(() => window.location.reload(), 1200);
            } catch (err) {
                if (window.Toast) window.Toast.error('Network error submitting assignment');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    }
});

let testPushInterval = null;

async function trigger30SecTestPush() {
    const btn = document.getElementById('btn30sTestPush');
    const banner = document.getElementById('testPushTimerBanner');
    const countdownEl = document.getElementById('testPushCountdown');

    if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
    }

    try {
        const res = await fetch("{{ route('ajax.notifications.test-push') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            if (window.Toast) window.Toast.error(data.message || 'Failed to initialize test push');
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            return;
        }

        if (banner) banner.classList.remove('hidden');
        let remaining = 30;
        if (countdownEl) countdownEl.textContent = `Dispatched in: 30s`;

        if (testPushInterval) clearInterval(testPushInterval);

        testPushInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(testPushInterval);
                if (banner) banner.classList.add('hidden');
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-60', 'cursor-not-allowed');
                }

                // Trigger Web Push / Notification sound & Toast
                if (window.Toast && data.notification) {
                    window.Toast.success(data.notification.body, data.notification.title);
                }

                // Request Browser Notification Permission if granted
                if ('Notification' in window && Notification.permission === 'granted' && data.notification) {
                    new Notification(data.notification.title, {
                        body: data.notification.body,
                        icon: '/images/logo.png'
                    });
                } else if ('Notification' in window && Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }

                // Append new notification card to feed
                const feed = document.getElementById('notificationsFeedContainer');
                if (feed && data.notification) {
                    const card = document.createElement('div');
                    card.className = 'p-4 bg-teal-50 rounded-2xl border border-teal-200/90 space-y-1.5 shadow-md animate-bounce';
                    card.innerHTML = `
                        <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                            <span class="text-teal-800 bg-teal-100/90 px-2.5 py-0.5 rounded-md border border-teal-200">🔔 Real-Time FCM Alert</span>
                            <span class="text-slate-400 font-normal">Just now</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 leading-snug">${data.notification.title}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-mono">${data.notification.body}</p>
                    `;
                    feed.prepend(card);
                }
            } else {
                if (countdownEl) countdownEl.textContent = `Dispatched in: ${remaining}s`;
            }
        }, 1000);

    } catch (err) {
        if (window.Toast) window.Toast.error('Network error starting 30s test push');
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────
// Real-Time AJAX Notifications Pagination (No Page Refresh)
// ─────────────────────────────────────────────────────────────────────────
let notifCurrentPage = {{ $notifCurrentPage }};
let notifLastPage = {{ $notifLastPage }};

function updatePaginationControls(page, lastPage, total) {
    notifCurrentPage = page;
    notifLastPage = lastPage;

    const btnPrev = document.getElementById('btnNotifPrev');
    const btnNext = document.getElementById('btnNotifNext');
    const currText = document.getElementById('notifCurrentPageText');
    const lastText = document.getElementById('notifLastPageText');
    const totalAlerts = document.getElementById('notifTotalAlerts');
    const pagBar = document.getElementById('notificationsPaginationBar');

    if (currText) currText.textContent = page;
    if (lastText) lastText.textContent = lastPage;
    if (totalAlerts) totalAlerts.textContent = `${total} Alerts`;

    if (btnPrev) btnPrev.disabled = (page <= 1);
    if (btnNext) btnNext.disabled = (page >= lastPage);

    if (pagBar) {
        if (lastPage <= 1) {
            pagBar.classList.add('hidden');
        } else {
            pagBar.classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updatePaginationControls(notifCurrentPage, notifLastPage, {{ $totalAlertsCount }});
});

async function fetchNotificationsPage(page) {
    if (page < 1 || (notifLastPage && page > notifLastPage)) return;

    const container = document.getElementById('notificationsFeedContainer');
    if (container) container.classList.add('opacity-40');

    try {
        const res = await fetch(`{{ route('ajax.notifications.feed') }}?page=${page}&per_page=5`);
        const data = await res.json();

        if (!data.success) {
            if (container) container.classList.remove('opacity-40');
            return;
        }

        if (container) {
            container.innerHTML = '';
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(n => {
                    const card = document.createElement('div');
                    card.className = 'p-4 bg-slate-50/90 hover:bg-slate-100/90 rounded-2xl border border-slate-200/90 space-y-1.5 shadow-2xs hover:-translate-y-0.5 hover:shadow-md transition-all';
                    
                    let badgeHtml = '<span class="text-teal-800 bg-teal-100/90 px-2.5 py-0.5 rounded-md border border-teal-200">🔔 Real-Time FCM Alert</span>';
                    if (n.type === 'ASSIGNMENT_DEADLINE_REMINDER') {
                        badgeHtml = '<span class="text-amber-800 bg-amber-100/90 px-2.5 py-0.5 rounded-md border border-amber-200">⏰ Deadline 24h</span>';
                    } else if (n.type === 'ADMIN_APPROVAL_ALERT') {
                        badgeHtml = '<span class="text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-md border border-emerald-200">✅ Admin Approved</span>';
                    }

                    const timeStr = n.created_at ? formatTimeAgo(n.created_at) : 'Just now';

                    card.innerHTML = `
                        <div class="flex justify-between items-center text-[11px] font-mono font-bold">
                            ${badgeHtml}
                            <span class="text-slate-400 font-normal">${timeStr}</span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-900 leading-snug">${escapeHtml(n.title)}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-mono">${escapeHtml(n.body)}</p>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = `
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500 text-center font-mono space-y-1">
                        <div class="text-2xl">🔕</div>
                        <div>${@json(app()->getLocale() === 'ar' ? 'لا توجد إشعارات مسجلة حالياً.' : 'No notifications in feed yet.')}</div>
                    </div>
                `;
            }
            container.classList.remove('opacity-40');
        }

        if (data.pagination) {
            updatePaginationControls(data.pagination.current_page, data.pagination.last_page, data.pagination.total);
        }

    } catch (err) {
        if (container) container.classList.remove('opacity-40');
    }
}

function formatTimeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} minutes ago`;
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hours ago`;
    return `${Math.floor(diffHours / 24)} days ago`;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
}
</script>

{{-- Ultra-Premium Glassmorphic Enrolled Course Details Modal --}}
<div id="enrolledCourseModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto animate-fade-in">
    <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl border border-slate-200/90 flex flex-col my-auto relative">
        
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white p-6 sm:p-8 flex items-start justify-between relative overflow-hidden shrink-0">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-2 relative z-10">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="modalCourseSubject" class="bg-teal-600 text-white text-xs font-bold px-3 py-0.5 rounded-full shadow-xs"></span>
                    <span id="modalCourseGrade" class="bg-slate-800 text-slate-300 text-xs font-mono px-2.5 py-0.5 rounded-full border border-slate-700"></span>
                    <span class="bg-emerald-500/20 text-emerald-300 text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/40">✓ {{ app()->getLocale() === 'ar' ? 'مشترك بالنظام' : 'Enrolled' }}</span>
                </div>
                <h2 id="modalCourseTitle" class="font-heading font-black text-2xl sm:text-3xl text-white tracking-tight"></h2>
                <p id="modalCourseTeacher" class="text-xs font-mono text-teal-300 flex items-center gap-1.5"></p>
            </div>
            <button onclick="closeEnrolledCourseModal()" class="w-10 h-10 rounded-full bg-slate-800/80 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center font-bold text-lg transition-all cursor-pointer border border-slate-700 shrink-0 relative z-10">
                ✕
            </button>
        </div>

        {{-- Modal Scrollable Body --}}
        <div class="p-6 sm:p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-180px)] font-mono text-slate-800">
            {{-- Overview Card --}}
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500">
                    {{ app()->getLocale() === 'ar' ? 'نبذة عن الكورس والمحتوى' : 'Course Overview & Learning Objectives' }}
                </h4>
                <p id="modalCourseDesc" class="text-xs text-slate-700 leading-relaxed"></p>
            </div>

            {{-- Tabs / Section Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                    <span>📺</span> {{ app()->getLocale() === 'ar' ? 'منهج الكورس والحصص التفصيلية' : 'Full Curriculum & Session Modules' }}
                </h3>
                <span id="modalTotalSessionsBadge" class="text-xs font-bold bg-teal-100 text-teal-900 px-3 py-1 rounded-full border border-teal-200"></span>
            </div>

            {{-- Live Sessions Subsection --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-teal-800 flex items-center gap-1.5">
                        <span>🟢</span> {{ app()->getLocale() === 'ar' ? 'جدول الحصص والبث المباشر (Live Streams)' : 'Live Stream Schedule' }}
                    </h4>
                    <span id="liveSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                </div>
                <div id="modalLiveSessionsList" class="space-y-2.5"></div>
                {{-- Live Sessions Pagination Bar --}}
                <div id="modalLivePaginationBar" class="hidden flex items-center justify-between pt-2 text-xs font-mono text-slate-600 border-t border-slate-100">
                    <button id="btnLivePrev" onclick="changeModalLivePage(-1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                    </button>
                    <span id="modalLivePageText" class="font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-xl border border-teal-200">Page 1 of 1</span>
                    <button id="btnLiveNext" onclick="changeModalLivePage(1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                    </button>
                </div>
            </div>

            {{-- Recorded Course Sessions Subsection --}}
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-indigo-800 flex items-center gap-1.5">
                        <span>📹</span> {{ app()->getLocale() === 'ar' ? 'دروس الفيديو والواجبات المنهجية (Recorded Modules & MSQs)' : 'Recorded Modules & Assignments' }}
                    </h4>
                    <span id="recSessionsCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                </div>
                <div id="modalRecordedSessionsList" class="space-y-2.5"></div>
                {{-- Recorded Sessions Pagination Bar --}}
                <div id="modalRecPaginationBar" class="hidden flex items-center justify-between pt-2 text-xs font-mono text-slate-600 border-t border-slate-100">
                    <button id="btnRecPrev" onclick="changeModalRecPage(-1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>&larr;</span> <span>{{ app()->getLocale() === 'ar' ? 'السابق' : 'Prev' }}</span>
                    </button>
                    <span id="modalRecPageText" class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-200">Page 1 of 1</span>
                    <button id="btnRecNext" onclick="changeModalRecPage(1)" class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold transition-all disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1">
                        <span>{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}</span> <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs font-mono text-slate-500 shrink-0">
            <span>🎓 Elite Academy Certified Curriculum</span>
            <button onclick="closeEnrolledCourseModal()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold cursor-pointer transition-all">
                {{ app()->getLocale() === 'ar' ? 'إغلاق النافذة' : 'Close' }}
            </button>
        </div>
    </div>
</div>


<script>
window.enrolledCoursesData = @json($enrolledCoursesDataMap);

let currentModalCourseId = null;
let currentModalLivePage = 1;
let currentModalRecPage = 1;
const MODAL_LIVE_PER_PAGE = 3;
const MODAL_REC_PER_PAGE = 3;

function openEnrolledCourseModal(courseId) {
    const data = window.enrolledCoursesData[courseId];
    if (!data) return;

    currentModalCourseId = courseId;
    currentModalLivePage = 1;
    currentModalRecPage = 1;

    document.getElementById('modalCourseTitle').textContent = data.title;
    document.getElementById('modalCourseSubject').textContent = data.subject;
    document.getElementById('modalCourseGrade').textContent = data.grade;
    document.getElementById('modalCourseTeacher').textContent = '👨‍🏫 ' + (data.teacher || 'Dr. Instructor');
    document.getElementById('modalCourseDesc').textContent = data.description;

    const liveCount = data.live_sessions ? data.live_sessions.length : 0;
    const recCount = data.recorded_sessions ? data.recorded_sessions.length : 0;
    document.getElementById('modalTotalSessionsBadge').textContent = (liveCount + recCount) + ' Sessions Total';
    
    document.getElementById('liveSessionsCountBadge').textContent = liveCount + ' ' + @json(app()->getLocale() === 'ar' ? 'بث مباشر' : 'streams');
    document.getElementById('recSessionsCountBadge').textContent = recCount + ' ' + @json(app()->getLocale() === 'ar' ? 'دروس مسجلة' : 'modules');

    renderModalLiveSessions();
    renderModalRecordedSessions();

    const modal = document.getElementById('enrolledCourseModal');
    modal.classList.remove('hidden');
}

function renderModalLiveSessions() {
    const data = window.enrolledCoursesData[currentModalCourseId];
    if (!data) return;

    const liveContainer = document.getElementById('modalLiveSessionsList');
    const pagBar = document.getElementById('modalLivePaginationBar');
    liveContainer.innerHTML = '';

    const list = data.live_sessions || [];
    const total = list.length;

    if (total === 0) {
        liveContainer.innerHTML = `<div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-500 text-center font-mono">${@json(app()->getLocale() === 'ar' ? 'لا توجد جلسات بث مباشر مجدولة لهذا الكورس حالياً.' : 'No live streams currently scheduled for this course.')}</div>`;
        if (pagBar) pagBar.classList.add('hidden');
        return;
    }

    const lastPage = Math.ceil(total / MODAL_LIVE_PER_PAGE);
    if (currentModalLivePage < 1) currentModalLivePage = 1;
    if (currentModalLivePage > lastPage) currentModalLivePage = lastPage;

    const startIdx = (currentModalLivePage - 1) * MODAL_LIVE_PER_PAGE;
    const endIdx = startIdx + MODAL_LIVE_PER_PAGE;
    const pageItems = list.slice(startIdx, endIdx);

    pageItems.forEach(ls => {
        const card = document.createElement('div');
        card.className = 'p-3.5 bg-slate-50 hover:bg-slate-100/90 rounded-2xl border border-slate-200 space-y-2 transition-all';
        
        let btnHtml = `<span class="text-[11px] font-bold px-3 py-1 rounded-xl bg-slate-200 text-slate-700">${escapeHtml(ls.state_label)}</span>`;
        if (ls.can_join && ls.meeting_link) {
            btnHtml = `<a href="${escapeHtml(ls.meeting_link)}" target="_blank" class="btn-lift text-[11px] font-bold px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs">🟢 Join Stream</a>`;
        }

        card.innerHTML = `
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full ${ls.is_live ? 'bg-emerald-500 animate-ping' : 'bg-slate-400'}"></span>
                    <span class="font-bold text-xs text-slate-900">${escapeHtml(ls.title)}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500">📅 ${escapeHtml(ls.start_at)}</span>
                    ${btnHtml}
                </div>
            </div>
        `;
        liveContainer.appendChild(card);
    });

    if (pagBar) {
        if (lastPage > 1) {
            pagBar.classList.remove('hidden');
            document.getElementById('modalLivePageText').textContent = @json(app()->getLocale() === 'ar' ? 'صفحة' : 'Page') + ` ${currentModalLivePage} ` + @json(app()->getLocale() === 'ar' ? 'من' : 'of') + ` ${lastPage}`;
            document.getElementById('btnLivePrev').disabled = (currentModalLivePage <= 1);
            document.getElementById('btnLiveNext').disabled = (currentModalLivePage >= lastPage);
        } else {
            pagBar.classList.add('hidden');
        }
    }
}

function changeModalLivePage(delta) {
    currentModalLivePage += delta;
    renderModalLiveSessions();
}

function renderModalRecordedSessions() {
    const data = window.enrolledCoursesData[currentModalCourseId];
    if (!data) return;

    const recContainer = document.getElementById('modalRecordedSessionsList');
    const pagBar = document.getElementById('modalRecPaginationBar');
    recContainer.innerHTML = '';

    const list = data.recorded_sessions || [];
    const total = list.length;

    if (total === 0) {
        recContainer.innerHTML = `<div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-500 text-center font-mono">${@json(app()->getLocale() === 'ar' ? 'لا توجد دروس فيديو مسجلة منشورة لهذا الكورس حالياً.' : 'No recorded video lessons published for this course yet.')}</div>`;
        if (pagBar) pagBar.classList.add('hidden');
        return;
    }

    const lastPage = Math.ceil(total / MODAL_REC_PER_PAGE);
    if (currentModalRecPage < 1) currentModalRecPage = 1;
    if (currentModalRecPage > lastPage) currentModalRecPage = lastPage;

    const startIdx = (currentModalRecPage - 1) * MODAL_REC_PER_PAGE;
    const endIdx = startIdx + MODAL_REC_PER_PAGE;
    const pageItems = list.slice(startIdx, endIdx);

    pageItems.forEach(rs => {
        const card = document.createElement('div');
        card.className = 'p-4 bg-slate-50 hover:bg-slate-100/90 rounded-2xl border border-slate-200 space-y-2.5 transition-all';
        
        let assignHtml = '';
        if (rs.assignments && rs.assignments.length > 0) {
            assignHtml = `<div class="pt-2 border-t border-slate-200/60 flex flex-wrap items-center gap-2">`;
            rs.assignments.forEach(a => {
                assignHtml += `<a href="${escapeHtml(a.url)}" class="btn-lift inline-flex items-center gap-1 text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 px-2.5 py-1 rounded-lg">📝 ${escapeHtml(a.title)} (${a.points} pts) &rarr;</a>`;
            });
            assignHtml += `</div>`;
        }

        card.innerHTML = `
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Module ${rs.index}</span>
                        <span class="font-bold text-xs text-slate-900">${escapeHtml(rs.title)}</span>
                    </div>
                    ${rs.description ? `<p class="text-[11px] text-slate-600 line-clamp-1">${escapeHtml(rs.description)}</p>` : ''}
                </div>
                <span class="text-[11px] text-slate-500 font-bold bg-white px-2.5 py-1 rounded-lg border border-slate-200">⏱️ ${rs.duration} mins</span>
            </div>
            ${assignHtml}
        `;
        recContainer.appendChild(card);
    });

    if (pagBar) {
        if (lastPage > 1) {
            pagBar.classList.remove('hidden');
            document.getElementById('modalRecPageText').textContent = @json(app()->getLocale() === 'ar' ? 'صفحة' : 'Page') + ` ${currentModalRecPage} ` + @json(app()->getLocale() === 'ar' ? 'من' : 'of') + ` ${lastPage}`;
            document.getElementById('btnRecPrev').disabled = (currentModalRecPage <= 1);
            document.getElementById('btnRecNext').disabled = (currentModalRecPage >= lastPage);
        } else {
            pagBar.classList.add('hidden');
        }
    }
}

function changeModalRecPage(delta) {
    currentModalRecPage += delta;
    renderModalRecordedSessions();
}

function closeEnrolledCourseModal() {
    const modal = document.getElementById('enrolledCourseModal');
    modal.classList.add('hidden');
}

// ── Live Session Countdown Timers ──────────────────────────────────────────
function initSessionCountdowns() {
    const isAr = @json(app()->getLocale() === 'ar');

    function update() {
        const now = new Date().getTime();

        // Single live ticking countdown to session start time
        document.querySelectorAll('.session-countdown-pill').forEach(pill => {
            const startStr = pill.getAttribute('data-start-time');
            const textEl = pill.querySelector('.countdown-text');
            if (!startStr || !textEl) return;

            const startTime = new Date(startStr).getTime();
            const diff = startTime - now;

            if (diff <= 0) {
                textEl.textContent = isAr ? 'بدأت الحصة الآن 🔴' : 'Session Live Now 🔴';
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            let parts = [];
            if (d > 0) parts.push(d + (isAr ? 'ي ' : 'd '));
            if (h > 0 || d > 0) parts.push(String(h).padStart(2, '0') + (isAr ? 'س ' : 'h '));
            parts.push(String(m).padStart(2, '0') + (isAr ? 'د ' : 'm '));
            parts.push(String(s).padStart(2, '0') + (isAr ? 'ث' : 's'));

            textEl.textContent = (isAr ? 'تبدأ خلال: ' : 'Starts in: ') + parts.join('');
        });
    }

    update();
    setInterval(update, 1000);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSessionCountdowns);
} else {
    initSessionCountdowns();
}
</script>
@endsection

```

---

## File: `resources/views/pages/student-profile.blade.php`

```blade
@extends('layouts.app')

@section('content')
{{-- Ultra-Premium Dark Profile Hero Banner --}}
<section class="relative py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800/80 overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute left-10 -bottom-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('app.student_portal'), 'route' => 'student-portal'],
                ['label' => app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Student Profile'],
            ]
        ])

        {{-- Profile Header Card --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    @if($profile->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($profile->avatar))
                        <img src="{{ asset('storage/' . $profile->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-4 border-teal-500/40 shadow-xl shadow-teal-500/20">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-3xl sm:text-4xl flex items-center justify-center shadow-xl shadow-teal-500/20 border-4 border-teal-300/40">
                            {{ mb_substr($user->name ?? 'S', 0, 1) }}
                        </div>
                    @endif
                    <button onclick="document.getElementById('avatarInput').click()" class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-teal-500 hover:bg-teal-400 text-slate-950 flex items-center justify-center text-xs font-bold shadow-md cursor-pointer transition-transform hover:scale-110" title="{{ app()->getLocale() === 'ar' ? 'تغيير الصورة الشخصية' : 'Change Avatar' }}">
                        📷
                    </button>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block text-[11px] font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3 py-0.5 rounded-full border border-teal-700/60">
                            {{ app()->getLocale() === 'ar' ? 'حساب طالب معتمد' : 'Verified Student Account' }}
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-800/60">
                            ● Active Status
                        </span>
                    </div>
                    <h1 class="font-heading text-2xl sm:text-4xl font-black text-white tracking-tight">
                        {{ $user->name }}
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm font-mono flex flex-wrap items-center gap-3 pt-0.5">
                        <span>✉️ {{ $user->email }}</span>
                        <span>•</span>
                        <span>📱 {{ $user->phone ?: (app()->getLocale() === 'ar' ? 'غير مسجل' : 'Not Provided') }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 self-start md:self-auto">
                <a href="{{ route('student-portal') }}" class="btn-lift px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-2xl border border-slate-700 shadow-md flex items-center gap-2 transition-all">
                    <span>&larr;</span> {{ app()->getLocale() === 'ar' ? 'العودة للوحة التحكم' : 'Back to Dashboard' }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Main Profile Content Section --}}
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 md:space-y-12">

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="animate-fade-in-up p-5 bg-emerald-50 border border-emerald-200 rounded-3xl flex items-center justify-between text-emerald-950 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span class="font-bold text-xs sm:text-sm font-mono">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="animate-fade-in-up p-5 bg-rose-50 border border-rose-200 rounded-3xl space-y-2 text-rose-950 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                    <span>⚠️</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</span>
                </div>
                <ul class="list-disc list-inside text-xs font-mono text-rose-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            {{-- Main Profile Settings Form Column --}}
            <div class="lg:col-span-8 space-y-8 lg:space-y-10">

                {{-- 1. Personal & Academic Profile Form Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>👤</span> {{ app()->getLocale() === 'ar' ? 'البيانات الشخصية والأكاديمية' : 'Personal & Academic Details' }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ app()->getLocale() === 'ar' ? 'قم بتحديث اسمك، رقم الهاتف، المرحلة الدراسية، واسم المدرسة.' : 'Update your name, phone number, grade level, and school information.' }}
                        </p>
                    </div>

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Full Name --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'الاسم بالكامل' : 'Full Name' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'رقم الهاتف / الواتساب' : 'Phone Number' }}
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+20 100 000 0000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Email Address (Readonly) --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني (غير قابل للتعديل)' : 'Email Address (Readonly)' }}
                                </label>
                                <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-xs font-mono text-slate-500 cursor-not-allowed">
                            </div>

                            {{-- Grade Level Select --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'الصف / المرحلة الدراسية' : 'Grade Level' }}
                                </label>
                                <select name="grade_level_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المرحلة الدراسية --' : '-- Select Grade Level --' }}</option>
                                    @foreach($gradeLevels as $gl)
                                        <option value="{{ $gl->id }}" {{ old('grade_level_id', $profile->grade_level_id) == $gl->id ? 'selected' : '' }}>
                                            {{ $gl->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- School Name --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المدرسة / الأكاديمية' : 'School Name' }}
                                </label>
                                <input type="text" name="school_name" value="{{ old('school_name', $profile->school_name) }}" placeholder="e.g. STEM Cairo High School" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Date of Birth --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth' }}
                                </label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-2xl shadow-md shadow-teal-600/30 flex items-center gap-2 cursor-pointer">
                                <span>💾</span> {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Profile Details' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. Account Security & Password Change Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 md:p-9 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-2">
                    <div class="border-b border-slate-100 pb-4">
                        <h2 class="font-heading font-black text-xl sm:text-2xl text-slate-900 flex items-center gap-2">
                            <span>🔒</span> {{ app()->getLocale() === 'ar' ? 'أمان الحساب وكلمة المرور' : 'Account Security & Password' }}
                        </h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">
                            {{ app()->getLocale() === 'ar' ? 'قم بتغيير كلمة المرور الخاصة بك بحساب الطالب بانتظام لحماية بياناتك.' : 'Update your password regularly to maintain account security.' }}
                        </p>
                    </div>

                    <form action="{{ route('student.profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {{-- Current Password --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="current_password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- New Password --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>

                            {{-- Password Confirmation --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-mono font-bold text-slate-700 uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="submit" class="btn-lift px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold rounded-2xl shadow-md cursor-pointer flex items-center gap-2">
                                <span>🔑</span> {{ app()->getLocale() === 'ar' ? 'تحديث كلمة المرور' : 'Update Password' }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Sidebar Column: Active Package & Parent Link Status --}}
            <div class="lg:col-span-4 space-y-8 lg:space-y-10">

                {{-- Active Package Subscription Summary Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-6 animate-fade-in-up stagger-1">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2">
                            <span>💳</span> {{ app()->getLocale() === 'ar' ? 'باقة الحصص النشطة' : 'Active Package' }}
                        </h3>
                        @if($activePackage)
                            <span class="text-xs font-mono font-bold text-teal-800 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 shadow-2xs">
                                ● Active
                            </span>
                        @else
                            <span class="text-xs font-mono font-bold text-rose-800 bg-rose-50 px-3 py-1 rounded-full border border-rose-200 shadow-2xs">
                                ✕ No Active Package
                            </span>
                        @endif
                    </div>

                    @if($activePackage)
                        <div class="space-y-4">
                            <div class="p-5 bg-gradient-to-br from-teal-50/80 to-emerald-50/40 rounded-2xl border border-teal-200/80 space-y-3">
                                <h4 class="font-bold text-base text-slate-900">
                                    {{ $activePackage->packageTemplate?->name ?: ($activePackage->course?->title ?: 'Standard Monthly Package') }}
                                </h4>

                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-xs font-mono font-bold">
                                        <span class="text-slate-600">{{ app()->getLocale() === 'ar' ? 'الحصص المتبقية' : 'Sessions Remaining' }}:</span>
                                        <span class="text-teal-700 font-extrabold text-sm">{{ $activePackage->remaining_sessions }} / {{ $activePackage->total_sessions }}</span>
                                    </div>
                                    @php
                                        $percentRemaining = $activePackage->total_sessions > 0 ? round(($activePackage->remaining_sessions / $activePackage->total_sessions) * 100) : 0;
                                    @endphp
                                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentRemaining }}%"></div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-teal-200/60 flex items-center justify-between text-[11px] font-mono text-slate-600">
                                    <span>📅 {{ app()->getLocale() === 'ar' ? 'تاريخ التفعيل' : 'Activated' }}: {{ $activePackage->activated_at ? $activePackage->activated_at->format('Y-m-d') : 'Active' }}</span>
                                    <span>⏳ {{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'Expires' }}: {{ $activePackage->expires_at ? $activePackage->expires_at->format('Y-m-d') : 'No Expiry' }}</span>
                                </div>
                            </div>

                            @if($packageTransactions->count() > 0)
                                <div class="space-y-2">
                                    <h5 class="font-bold text-xs font-mono text-slate-700 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'آخر المعاملات والخصومات' : 'Recent Transactions' }}</h5>
                                    <div class="space-y-2">
                                        @foreach($packageTransactions as $tx)
                                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono flex items-center justify-between">
                                                <div class="space-y-0.5">
                                                    <span class="font-bold text-slate-900 block">{{ ucfirst($tx->type) }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $tx->created_at->diffForHumans() }}</span>
                                                </div>
                                                <span class="font-bold {{ $tx->session_change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                                    {{ $tx->session_change > 0 ? "+{$tx->session_change}" : $tx->session_change }} credits
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('courses') }}" class="btn-lift w-full py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-2xl font-bold text-xs shadow-md shadow-teal-600/30 text-center block">
                                🔄 {{ app()->getLocale() === 'ar' ? 'تجديد أو ترقية الباقة' : 'Renew / Upgrade Package' }}
                            </a>
                        </div>
                    @else
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                            <div class="text-3xl">💳</div>
                            <p class="text-xs font-mono text-slate-600">{{ app()->getLocale() === 'ar' ? 'لا توجد باقة حصص نشطة مرتبطة بحسابك حالياً.' : 'No active session package linked to your account.' }}</p>
                            <a href="{{ route('courses') }}" class="btn-lift px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-md shadow-rose-600/30 inline-block">
                                🛒 {{ app()->getLocale() === 'ar' ? 'تصفح الباقات والكورسات' : 'Browse Packages & Courses' }}
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Linked Guardian / Parent Info Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all space-y-5 animate-fade-in-up stagger-2">
                    <h3 class="font-heading font-black text-xl text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span>👨‍👩‍👦</span> {{ app()->getLocale() === 'ar' ? 'بيانات ولي الأمر المرتبط' : 'Linked Parent / Guardian' }}
                    </h3>

                    <div class="space-y-3">
                        @forelse($parents as $parent)
                            <div class="p-4 bg-slate-50/90 rounded-2xl border border-slate-200 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900">{{ $parent->name }}</span>
                                    <span class="text-[10px] font-mono font-bold bg-teal-100 text-teal-900 px-2 py-0.5 rounded-full">Linked</span>
                                </div>
                                <p class="text-xs font-mono text-slate-500">📱 {{ $parent->phone ?: $parent->email }}</p>
                            </div>
                        @empty
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-mono text-slate-500 text-center space-y-1">
                                <div>👨‍👩‍👦</div>
                                <div>{{ app()->getLocale() === 'ar' ? 'لم يتم ربط حساب ولي أمر بهذا الحساب بعد.' : 'No parent account linked yet.' }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatarImgs = document.querySelectorAll('.group img, .group div');
            avatarImgs.forEach(el => {
                if (el.tagName === 'IMG') {
                    el.src = e.target.result;
                }
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection

```

---

## File: `resources/views/pages/subject-details.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $name = $subject ? $subject->getLocalizedName() : __('Subject Details');
    $description = $subject ? ($subject->getLocalizedDescription() ?: __('Comprehensive curriculum covering core topics prepared for national curriculum standards.')) : __('Comprehensive curriculum covering core topics prepared for national curriculum standards.');
    $categoryName = $subject?->category ? $subject->category->getLocalizedName() : __('General Curriculum');

    $coursesCount = isset($activeCoursesCount) ? $activeCoursesCount : ($subject?->getActiveCoursesCount() ?? 0);
    $lessonsCount = isset($videoLessonsCount) ? $videoLessonsCount : ($subject?->getVideoLessonsCount() ?? 0);
    $studentsCount = isset($activeStudentsCount) ? $activeStudentsCount : ($subject?->getActiveStudentsCount() ?? 0);
    $rating = isset($ratingAvg) ? $ratingAvg : ($subject?->getRatingAvg() ?? 4.9);

    $image = $subject ? ($subject->image ?: 'images/course_ai.png') : 'images/course_ai.png';
@endphp

{{-- Hero Cover & Stats --}}
<section class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden">
    <img src="{{ media_url($image, 'images/course_ai.png') }}" alt="{{ $name }} Cover" class="absolute inset-0 w-full h-full object-cover opacity-30">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/80 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('Home'), 'route' => 'home'],
                ['label' => __('Subjects'), 'route' => 'subjects'],
                ['label' => $name],
            ]
        ])

        <div class="space-y-4 max-w-3xl">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-mono font-extrabold text-white bg-teal-600 px-3.5 py-1.5 rounded-full shadow-md">
                    {{ $categoryName }}
                </span>
                <span class="text-xs font-mono font-bold text-teal-300 bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                    {{ __('Term 1 & Term 2') }}
                </span>
            </div>

            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight">
                {{ $name }}
            </h1>
            <p class="text-slate-300 text-base sm:text-xl font-medium leading-relaxed">
                {{ $description }}
            </p>
        </div>

        {{-- Statistics Banner Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 max-w-4xl text-center">
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">{{ number_format($lessonsCount) }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Video Lessons') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-orange-400">{{ number_format($coursesCount) }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Active Courses') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-teal-400">{{ $studentsCount > 0 ? '+' . number_format($studentsCount) : '0' }}</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Active Students') }}</p>
            </div>
            <div>
                <p class="font-heading font-black text-3xl text-amber-400">{{ number_format($rating, 1) }} ★</p>
                <p class="text-xs font-mono text-slate-300 font-semibold">{{ __('Student Rating') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- About & Syllabus Units --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('About the Curriculum') }}</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ $description }}
                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Courses in') }} {{ $name }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if($subject && $subject->courses && $subject->courses->count() > 0)
                        @foreach($subject->courses as $course)
                            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-4 flex flex-col justify-between hover:shadow-xl transition-shadow duration-300">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-mono font-bold text-teal-600 uppercase bg-teal-50 px-3 py-1 rounded-full border border-teal-100">
                                            {{ $course->gradeLevel?->name ?: __('General') }}
                                        </span>
                                        @php
                                            $courseSessionsNum = $course->sessions ? $course->sessions->count() : ($course->sessions_count ?: 0);
                                        @endphp
                                        @if($courseSessionsNum > 0)
                                            <span class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                                                {{ $courseSessionsNum }} {{ __('Lessons') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="font-heading font-extrabold text-xl text-slate-900 leading-snug">{{ __($course->title) }}</h3>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">{{ __($course->description ?: 'Interactive curriculum with hands-on labs.') }}</p>
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        @if($course->teacher?->photo)
                                            <img src="{{ media_url($course->teacher->photo) }}" class="w-8 h-8 rounded-full object-cover shadow-sm border border-slate-200" alt="{{ $course->teacher->user?->name }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">
                                                {{ substr($course->teacher?->user?->name ?: 'F', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-xs font-bold text-slate-800">{{ $course->teacher?->user?->name ?: __('Faculty Advisor') }}</p>
                                            <p class="text-[10px] text-slate-500 font-mono">{{ $course->teacher?->title ?: __('Senior Instructor') }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('course-details', ['slug' => $course->slug]) }}" class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-teal-600/20">
                                        {{ __('View Details') }} &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-2 bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-4">
                            <p class="text-base font-semibold text-slate-700">{{ __('No individual courses listed yet for this subject.') }}</p>
                            <a href="{{ route('courses') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md">
                                {{ __('Browse Courses') }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Enroll in') }} {{ $name }}</h3>
                <p class="text-xs font-mono text-slate-500">{{ __('Access all video lectures, PDF revision books, and live mentor Q&A cohorts.') }}</p>

                <div class="space-y-3 pt-4 border-t border-slate-100 text-sm font-semibold text-slate-700">
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Full Term 1 & 2 Access') }}
                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Direct Mentor Q&A Sessions') }}
                    </div>
                    <div class="flex items-center gap-2 font-mono">
                        <span class="text-teal-600 font-bold">✓</span> {{ __('Ministry Exam Revision Sheets') }}
                    </div>
                    @if($coursesCount > 0)
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> {{ $coursesCount }} {{ __('Active Accredited Courses') }}
                        </div>
                    @endif
                    @if($lessonsCount > 0)
                        <div class="flex items-center gap-2 font-mono text-teal-700 font-bold">
                            <span>✓</span> {{ $lessonsCount }} {{ __('Video Lessons & Labs') }}
                        </div>
                    @endif
                </div>

                <a href="{{ route('courses') }}" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    {{ __('Explore Courses') }} &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/subjects.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Subjects Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-6">
            <div class="space-y-2">
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    School <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Subjects</span>
                </h1>
                <p class="text-slate-600 text-base font-medium">
                    Browse every subject and discover available teachers and courses.
                </p>
            </div>
        </div>

        {{-- Subjects Grid --}}
        <div id="subjects-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8 pt-2">
            @if(isset($subjects) && count($subjects) > 0)
                @foreach ($subjects as $s)
                    @php
                        $isModel = $s instanceof \App\Models\Subject;
                        $slug = $isModel ? $s->slug : 'subject-details';
                        $cardData = [
                            'image' => $isModel ? ($s->image ?: 'images/course_ai.png') : ($s['img'] ?? 'images/course_ai.png'),
                            'grade' => $isModel ? ($s->category?->name ?: 'General Curriculum') : ($s['grade'] ?? 'General Curriculum'),
                            'badgeColor' => 'bg-teal-600',
                            'name' => $isModel ? $s->name : ($s['name'] ?? 'Subject Name'),
                            'description' => $isModel ? ($s->description ?: 'Comprehensive subject curriculum.') : ($s['desc'] ?? 'Description'),
                            'teachers' => $isModel ? ($s->courses->count() . ' Courses') : ($s['teachers'] ?? '10 Teachers'),
                            'lessons' => 'Full Syllabus',
                            'route' => route('subject-details', ['slug' => $slug]),
                        ];
                    @endphp
                    @include('components.subject-card', $cardData)
                @endforeach
            @else
                <div class="col-span-4 text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <div class="text-4xl mb-3">📐</div>
                    <h3 class="font-bold text-lg text-slate-800">No Subjects Active Yet</h3>
                    <p class="text-xs text-slate-500 mt-1">Check back soon as new subjects are being added by administrators.</p>
                </div>
            @endif
        </div>

    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/teacher-portal.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $todayDateStr = \Carbon\Carbon::today()->format('l, F j, Y');
    $activeTabKey = in_array($activeTab ?? 'overview', ['overview', 'sessions', 'assignments', 'attendance', 'students', 'notifications']) ? ($activeTab ?? 'overview') : 'overview';
@endphp

<section class="py-10 md:py-16 bg-[#FAFAF9] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Header & Faculty Greeting Banner --}}
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-teal-950 rounded-3xl p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-3 relative z-10 max-w-2xl">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        👨‍🏫 {{ $teacherProfile->title ?: __('Faculty Instructor') }}
                    </span>
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-semibold bg-white/10 text-slate-200">
                        ⭐ {{ number_format($teacherProfile->rating_avg ?: 4.9, 1) }} {{ __('Rating') }}
                    </span>
                </div>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                    {{ __('Welcome back') }}, <span class="text-teal-400">{{ auth()->user()->name }}</span>
                </h1>
                <p class="text-slate-300 text-sm sm:text-base font-medium leading-relaxed">
                    {{ __('Manage your teaching sessions, track student attendance, grade assignments, and monitor academic performance.') }}
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-3 shrink-0">
                <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-teal-600/30 flex items-center gap-2 cursor-pointer">
                    <span>➕</span> {{ __('Schedule New Session') }}
                </button>
            </div>
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        {{-- Toast Alert Container --}}
        <div id="teacherToastAlert" class="hidden p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md"></div>

        {{-- KPI Statistics Grid (With Count-Up Counter Animations) --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            {{-- KPI 1: Today Sessions --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Today Sessions') }}</span>
                    <span class="text-lg">📅</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $todaySessionsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Scheduled today') }}</p>
            </div>

            {{-- KPI 2: Upcoming --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Upcoming') }}</span>
                    <span class="text-lg">⏳</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-blue-600 js-counter" data-target="{{ $upcomingSessionsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Future cohorts') }}</p>
            </div>

            {{-- KPI 3: Students --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Students') }}</span>
                    <span class="text-lg">🎓</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-slate-900 js-counter" data-target="{{ $assignedStudentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Enrolled learners') }}</p>
            </div>

            {{-- KPI 4: Pending Assignments --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Need Grading') }}</span>
                    <span class="text-lg">📝</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-orange-500 js-counter" data-target="{{ $pendingAssignmentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Submissions') }}</p>
            </div>

            {{-- KPI 5: Total Submissions --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Submissions') }}</span>
                    <span class="text-lg">📊</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-teal-600 js-counter" data-target="{{ $submittedAssignmentsCount }}">0</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Handled total') }}</p>
            </div>

            {{-- KPI 6: Attendance Rate --}}
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-md hover:shadow-lg transition-all space-y-1">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-xs font-mono font-bold uppercase">{{ __('Attendance Rate') }}</span>
                    <span class="text-lg">✅</span>
                </div>
                <p class="font-heading font-black text-2xl sm:text-3xl text-emerald-600"><span class="js-counter" data-target="{{ $attendanceRate }}">0</span>%</p>
                <p class="text-[11px] text-slate-500 font-semibold">{{ __('Historical sessions') }}</p>
            </div>
        </div>

        {{-- Teacher Navigation Tabs --}}
        <div class="bg-white p-2 rounded-3xl border border-slate-200/90 shadow-sm flex items-center gap-2 overflow-x-auto">
            <button type="button" onclick="switchTeacherTab('overview')" id="tab-btn-overview" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'overview' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📊 {{ __('Overview & Today') }}
            </button>
            <button type="button" onclick="switchTeacherTab('sessions')" id="tab-btn-sessions" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'sessions' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📅 {{ __('Sessions & Streams') }}
            </button>
            <button type="button" onclick="switchTeacherTab('assignments')" id="tab-btn-assignments" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'assignments' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
                📝 {{ __('Assignments & Submissions') }}
                @if($pendingAssignmentsCount > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-[10px] bg-orange-500 text-white rounded-full font-mono">{{ $pendingAssignmentsCount }}</span>
                @endif
            </button>
            <button type="button" onclick="switchTeacherTab('attendance')" id="tab-btn-attendance" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'attendance' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                📋 {{ __('Attendance Tracker') }}
            </button>
            <button type="button" onclick="switchTeacherTab('students')" id="tab-btn-students" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'students' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }}">
                🎓 {{ __('My Students') }} ({{ $assignedStudentsCount }})
            </button>
            <button type="button" onclick="switchTeacherTab('notifications')" id="tab-btn-notifications" class="teacher-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-extrabold transition-all whitespace-nowrap {{ $activeTabKey === 'notifications' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-100' }} relative">
                🔔 {{ __('Notifications') }}
                @if($unreadNotifCount > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-[10px] bg-red-500 text-white rounded-full font-mono">{{ $unreadNotifCount }}</span>
                @endif
            </button>
        </div>

        {{-- TAB 1: OVERVIEW & TODAY'S SESSIONS --}}
        <div id="teacher-tab-overview" class="teacher-tab-content {{ $activeTabKey === 'overview' ? '' : 'hidden' }} space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Today's Live Sessions Card --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="font-heading text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                                    <span>🔴</span> {{ __('Today\'s Teaching Sessions') }}
                                </h2>
                                <p class="text-xs font-mono text-slate-500 mt-1">{{ $todayDateStr }}</p>
                            </div>
                            <span class="px-3 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200">
                                {{ $todaySessions->count() }} {{ __('Sessions Today') }}
                            </span>
                        </div>

                        @if($todaySessions->count() > 0)
                            <div class="space-y-4">
                                @foreach($todaySessions as $session)
                                    @php
                                        $state = $session->evaluateState(auth()->user());
                                        $isLive = $state === \App\Enums\LiveSessionState::LIVE;
                                        $isStartingSoon = $state === \App\Enums\LiveSessionState::BEFORE_JOINABLE;
                                    @endphp
                                    <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-4 hover:border-teal-300 transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-xs font-mono font-bold text-teal-600 uppercase">{{ $session->subject?->name ?: __('General') }}</span>
                                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full {{ $isLive ? 'bg-red-500 text-white animate-pulse' : ($isStartingSoon ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700') }}">
                                                        {{ $state->label() }}
                                                    </span>
                                                </div>
                                                <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $session->title ?: __('Live Cohort Session') }}</h3>
                                            </div>
                                            <div class="text-left sm:text-right font-mono text-xs font-semibold text-slate-600">
                                                <p>⏰ {{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : 'Scheduled' }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $session->duration_minutes ?: 60 }} mins</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                            <div class="text-xs font-semibold text-slate-700 flex items-center gap-2">
                                                <span>👨‍🎓 {{ __('Student') }}: {{ $session->studentUser?->name ?: __('Enrolled Cohort') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if(!empty($session->meeting_link))
                                                    <a href="{{ $session->meeting_link }}" target="_blank" class="btn-lift px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm">
                                                        📺 {{ __('Launch Broadcast') }}
                                                    </a>
                                                @else
                                                    <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link) }}')" class="btn-lift px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-sm">
                                                        🔗 {{ __('Add Meeting URL') }}
                                                    </button>
                                                @endif
                                                <button type="button" onclick="openAttendanceModal({{ $session->id }}, '{{ addslashes($session->title) }}')" class="btn-lift px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm">
                                                    📋 {{ __('Mark Attendance') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-[#FAFAF9] rounded-2xl p-8 text-center space-y-3 border border-slate-200/80">
                                <div class="text-4xl">☕</div>
                                <h3 class="font-bold text-base text-slate-800">{{ __('No teaching sessions scheduled for today.') }}</h3>
                                <p class="text-xs text-slate-500">{{ __('You have no active live broadcasts for today. Prepare upcoming assignments or check your roster.') }}</p>
                                <button type="button" onclick="openCreateSessionModal()" class="btn-lift inline-block px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl mt-2">
                                    + {{ __('Schedule Session') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submissions Needing Review Sidebar Card --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="font-heading font-black text-lg text-slate-900 flex items-center gap-2">
                                <span>📝</span> {{ __('Pending Review') }}
                            </h3>
                            <span class="px-2.5 py-0.5 bg-orange-100 text-orange-700 font-mono text-xs font-bold rounded-full">
                                {{ $pendingSubmissions->count() }}
                            </span>
                        </div>

                        @if($pendingSubmissions->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingSubmissions->take(4) as $sub)
                                    <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200/80 space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-slate-900">{{ $sub->studentUser?->name ?: __('Student') }}</span>
                                            <span class="font-mono text-[10px] text-slate-500">{{ $sub->submitted_at ? $sub->submitted_at->diffForHumans() : '' }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 line-clamp-1 font-medium">{{ $sub->assignment?->title ?: __('Assignment') }}</p>
                                        <button type="button" onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->studentUser?->name) }}', '{{ addslashes($sub->assignment?->title) }}', '{{ $sub->score }}')" class="btn-lift block w-full text-center py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                                            ✏️ {{ __('Grade Submission') }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-500 space-y-2">
                                <span class="text-3xl">✨</span>
                                <p class="text-xs font-semibold">{{ __('All submissions have been reviewed!') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: SESSIONS & LIVE STREAMS --}}
        <div id="teacher-tab-sessions" class="teacher-tab-content {{ $activeTabKey === 'sessions' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Session & Live Stream Directory') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Manage meeting links, reschedule cohorts, or trigger cancellations.') }}</p>
                    </div>
                    <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                        + {{ __('Create New Session') }}
                    </button>
                </div>

                @if($allSessions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-3 px-4">{{ __('Session / Course') }}</th>
                                    <th class="py-3 px-4">{{ __('Scheduled Date') }}</th>
                                    <th class="py-3 px-4">{{ __('Meeting URL') }}</th>
                                    <th class="py-3 px-4">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 text-right rtl:text-left">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($allSessions as $session)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4">
                                            <p class="font-bold text-slate-900">{{ $session->title ?: __('Live Session') }}</p>
                                            <p class="text-xs text-slate-500 font-mono">{{ $session->course?->title ?: $session->subject?->name }}</p>
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs">
                                            <p class="font-bold text-slate-800">{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d') : '' }}</p>
                                            <p class="text-slate-500">{{ $session->effective_start_at ? $session->effective_start_at->format('h:i A') : '' }}</p>
                                        </td>
                                        <td class="py-4 px-4 max-w-xs truncate">
                                            @if($session->meeting_link)
                                                <a href="{{ $session->meeting_link }}" target="_blank" class="text-xs font-mono text-teal-600 hover:underline truncate block">
                                                    {{ $session->meeting_link }}
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 italic">{{ __('No link attached') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            @php
                                                $statusText = match((string) $session->status) {
                                                    'scheduled' => __('Scheduled'),
                                                    'starting_soon' => __('Starting Soon'),
                                                    'live' => __('Live Now'),
                                                    'completed' => __('Completed'),
                                                    'cancelled' => __('Cancelled'),
                                                    'cancelled_by_teacher' => __('Cancelled by Teacher'),
                                                    default => __('Scheduled'),
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-full {{ in_array($session->status, ['cancelled', 'cancelled_by_teacher']) ? 'bg-red-100 text-red-700' : ($session->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-teal-100 text-teal-700') }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right rtl:text-left space-x-1 rtl:space-x-reverse whitespace-nowrap">
                                            <button type="button" onclick="openMeetingLinkModal({{ $session->id }}, '{{ addslashes($session->meeting_link) }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-lg transition-colors">
                                                🔗 {{ __('Link') }}
                                            </button>
                                            <button type="button" onclick="openRescheduleModal({{ $session->id }}, '{{ $session->effective_start_at ? $session->effective_start_at->format('Y-m-d\TH:i') : '' }}')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-colors">
                                                🗓️ {{ __('Reschedule') }}
                                            </button>
                                            @if(!in_array($session->status, ['cancelled', 'cancelled_by_teacher']))
                                                <button type="button" onclick="confirmCancelSession({{ $session->id }})" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-lg transition-colors">
                                                    ❌ {{ __('Cancel') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        {{ $allSessions->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No sessions created yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 3: ASSIGNMENTS & SUBMISSIONS --}}
        <div id="teacher-tab-assignments" class="teacher-tab-content {{ $activeTabKey === 'assignments' ? '' : 'hidden' }} space-y-8">
            {{-- Assignments Header & Publish Action --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Assignments & Homework Manager') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Publish new course assignments and review student work.') }}</p>
                    </div>
                    <button type="button" onclick="openCreateAssignmentModal()" class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-extrabold rounded-xl shadow-md">
                        + {{ __('Publish New Assignment') }}
                    </button>
                </div>

                @if($assignments->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($assignments as $assignment)
                            <div class="bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-3 flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-mono font-bold text-teal-600 uppercase">{{ $assignment->course?->title ?: __('Course') }}</span>
                                        <span class="px-2 py-0.5 bg-teal-100 text-teal-800 text-[10px] font-mono font-bold rounded-full">
                                            {{ $assignment->submissions->count() }} {{ __('Submissions') }}
                                        </span>
                                    </div>
                                    <h3 class="font-heading font-extrabold text-base text-slate-900">{{ $assignment->title }}</h3>
                                    <p class="text-xs text-slate-600 line-clamp-2">{{ $assignment->description ?: __('Homework assignment for student revision.') }}</p>
                                </div>
                                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-mono text-slate-500">
                                    <span>{{ __('Due') }}: {{ $assignment->effective_due_at ? $assignment->effective_due_at->format('M d, H:i') : __('No deadline') }}</span>
                                    <span class="font-bold text-slate-800">{{ $assignment->passing_score ?: 70 }}% {{ __('Pass') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No assignments created yet.') }}</p>
                    </div>
                @endif
            </div>

            {{-- Submissions Table --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <h3 class="font-heading text-xl font-black text-slate-900">{{ __('All Student Submissions') }}</h3>

                @if($submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-mono font-bold text-slate-500 uppercase">
                                    <th class="py-3 px-4">{{ __('Student') }}</th>
                                    <th class="py-3 px-4">{{ __('Assignment') }}</th>
                                    <th class="py-3 px-4">{{ __('Submitted At') }}</th>
                                    <th class="py-3 px-4">{{ __('Grade / Score') }}</th>
                                    <th class="py-3 px-4 text-right rtl:text-left">{{ __('Review Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($submissions as $sub)
                                    @php
                                        $subVal = $sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->value : (is_object($sub->status) ? ($sub->status->value ?? '') : (string) $sub->status);
                                        $isReviewed = $subVal === 'reviewed';
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-4 px-4 font-bold text-slate-900">
                                            {{ $sub->studentUser?->name ?: __('Student') }}
                                        </td>
                                        <td class="py-4 px-4 text-xs font-medium text-slate-700">
                                            {{ $sub->assignment?->title ?: __('Assignment') }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                            {{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : 'Draft' }}
                                        </td>
                                        <td class="py-4 px-4 font-mono text-xs">
                                            @if($sub->score !== null)
                                                <span class="font-extrabold text-emerald-600">{{ number_format($sub->score, 1) }}%</span>
                                            @else
                                                <span class="text-orange-500 italic">{{ __('Pending Grade') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right rtl:text-left">
                                            <button type="button" onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($sub->studentUser?->name) }}', '{{ addslashes($sub->assignment?->title) }}', '{{ $sub->score }}', '{{ addslashes($sub->evaluation_notes) }}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs">
                                                🔍 {{ $isReviewed ? __('Review & Grade') : __('Review Submission') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No student submissions yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 4: ATTENDANCE TRACKER --}}
        <div id="teacher-tab-attendance" class="teacher-tab-content {{ $activeTabKey === 'attendance' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div>
                    <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Attendance & Student Check-In') }}</h2>
                    <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Select a session to record attendance for enrolled cohort learners.') }}</p>
                </div>

                @if($todaySessions->count() > 0 || $allSessions->count() > 0)
                    <div class="space-y-4">
                        <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">{{ __('Select Teaching Session to Mark Attendance') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($todaySessions->merge($allSessions->take(6))->unique('id') as $sess)
                                <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-sm text-slate-900">{{ $sess->title ?: __('Live Session') }}</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $sess->effective_start_at ? $sess->effective_start_at->format('Y-m-d h:i A') : '' }}</p>
                                    </div>
                                    <button type="button" onclick="openAttendanceModal({{ $sess->id }}, '{{ addslashes($sess->title) }}')" class="btn-lift px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">
                                        📋 {{ __('Mark Attendance') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No active sessions available for attendance tracking.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 5: MY STUDENTS ROSTER --}}
        <div id="teacher-tab-students" class="teacher-tab-content {{ $activeTabKey === 'students' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Assigned Student Roster') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Authorized learners enrolled in your active courses and cohorts.') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="text" id="studentSearchInput" onkeyup="filterTeacherStudents()" placeholder="🔍 {{ __('Search student by name, email, school...') }}" class="px-4 py-2 text-xs border border-slate-200 rounded-xl bg-white focus:outline-none focus:border-teal-600 w-64">
                        <span class="px-3.5 py-1 bg-teal-50 text-teal-700 font-mono text-xs font-bold rounded-full border border-teal-200 shrink-0">
                            {{ $assignedStudentsCount }} {{ __('Students') }}
                        </span>
                    </div>
                </div>

                @if($assignedStudents->count() > 0)
                    <div id="studentsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($assignedStudents as $st)
                            <div class="student-card bg-[#FAFAF9] rounded-2xl p-5 border border-slate-200/90 space-y-4 hover:border-teal-300 transition-colors" data-name="{{ strtolower($st->user?->name) }}" data-email="{{ strtolower($st->user?->email) }}" data-school="{{ strtolower($st->school_name) }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-base shadow-md shrink-0">
                                        {{ substr($st->user?->name ?: 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-heading font-extrabold text-sm text-slate-900 truncate">{{ $st->user?->name ?: __('Student') }}</h3>
                                        <p class="text-xs text-slate-500 font-mono truncate">{{ $st->gradeLevel?->name ?: __('High School') }}</p>
                                    </div>
                                </div>

                                <div class="pt-2 text-xs font-mono text-slate-600 space-y-1.5 border-t border-slate-200/60">
                                    <p class="truncate">📧 {{ $st->user?->email }}</p>
                                    <p class="truncate">🏫 {{ $st->school_name ?: __('Elite Academy') }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 text-center font-mono text-[11px]">
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[10px]">{{ __('Attendance') }}</span>
                                        <span class="font-bold text-emerald-600">{{ $st->attendance_rate }}%</span>
                                    </div>
                                    <div class="p-2 bg-white rounded-xl border border-slate-200/80">
                                        <span class="text-slate-400 block text-[10px]">{{ __('Avg Grade') }}</span>
                                        <span class="font-bold text-teal-600">{{ $st->avg_score !== null ? $st->avg_score . '%' : 'N/A' }}</span>
                                    </div>
                                </div>

                                <button type="button" onclick="openStudentDetailsModal({{ $st->user_id }})" class="btn-lift w-full text-center py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs">
                                    🎓 {{ __('Academic Profile') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('No students enrolled in your courses yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 6: NOTIFICATIONS CENTER --}}
        <div id="teacher-tab-notifications" class="teacher-tab-content {{ $activeTabKey === 'notifications' ? '' : 'hidden' }} space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="font-heading text-2xl font-black text-slate-900">{{ __('Notification Feed & Alerts') }}</h2>
                        <p class="text-xs font-mono text-slate-500 mt-1">{{ __('Real-time session updates, assignment submissions, and student alerts.') }}</p>
                    </div>
                </div>

                @if($userNotifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($userNotifications as $notif)
                            <div class="p-4 rounded-2xl border transition-colors flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-[#FAFAF9] border-slate-200/70' : 'bg-teal-50/80 border-teal-200 font-semibold' }}">
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-900">{{ $notif->title }}</h4>
                                    <p class="text-xs text-slate-600">{{ $notif->body }}</p>
                                    <p class="text-[10px] font-mono text-slate-400">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        {{ $userNotifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-[#FAFAF9] rounded-2xl border border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">{{ __('You\'re all caught up! No new notifications.') }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- MODAL 1: SCHEDULE NEW SESSION --}}
<div id="createSessionModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Schedule New Live Session') }}</h3>
            <button type="button" onclick="closeModal('createSessionModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="createSessionForm" action="{{ route('ajax.teacher.sessions.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}</label>
                <select name="course_id" required class="input-mobile bg-white">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->subject?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Session Title') }}</label>
                <input type="text" name="title" placeholder="e.g. Session 4: Electromagnetism & Ohm's Law" required class="input-mobile">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Scheduled Date & Time') }}</label>
                    <input type="datetime-local" name="scheduled_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Duration (Minutes)') }}</label>
                    <input type="number" name="duration_minutes" value="60" min="15" max="300" required class="input-mobile">
                </div>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast Link (Optional)') }}</label>
                <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..." class="input-mobile">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_free_demo" id="is_free_demo" value="1" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                <label for="is_free_demo" class="text-xs font-semibold text-slate-700">{{ __('Mark as Free Trial / Demo Session') }}</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createSessionModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Create Session') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: MEETING LINK EDITOR --}}
<div id="meetingLinkModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Update Live Stream Link') }}</h3>
            <button type="button" onclick="closeModal('meetingLinkModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="meetingLinkForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="linkSessionId" name="session_id">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Meeting Broadcast URL') }}</label>
                <input type="url" id="meetingUrlInput" name="meeting_link" placeholder="https://vimeo.com/... or Zoom link" required class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('meetingLinkModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save Meeting Link') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: RESCHEDULE SESSION --}}
<div id="rescheduleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Reschedule Teaching Session') }}</h3>
            <button type="button" onclick="closeModal('rescheduleModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="rescheduleForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="rescheduleSessionId">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('New Scheduled Date & Time') }}</label>
                <input type="datetime-local" id="rescheduleDateTime" name="scheduled_at" required class="input-mobile">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('rescheduleModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Confirm Reschedule') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: PUBLISH ASSIGNMENT --}}
<div id="createAssignmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Publish New Assignment') }}</h3>
            <button type="button" onclick="closeModal('createAssignmentModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="createAssignmentForm" action="{{ route('ajax.teacher.assignments.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Select Course') }}</label>
                <select name="course_id" required class="input-mobile bg-white">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Assignment Title') }}</label>
                <input type="text" name="title" placeholder="e.g. Kirchhoff's Laws Practice Worksheet" required class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Instructions / Description') }}</label>
                <textarea name="description" rows="3" placeholder="Solve all questions and upload work or text response..." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Submission Deadline') }}</label>
                    <input type="datetime-local" name="due_at" required class="input-mobile">
                </div>
                <div>
                    <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Passing Score (%)') }}</label>
                    <input type="number" name="passing_score" value="70" min="0" max="100" class="input-mobile">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('createAssignmentModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Publish Assignment') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 5: GRADE & REVIEW SUBMISSION --}}
<div id="gradeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-3xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Review & Grade Submission') }}</h3>
                <p id="gradeStudentName" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('gradeModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        {{-- Auto-Correction Read-Only Breakdown Section --}}
        <div class="space-y-3 border-b border-slate-100 pb-4">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>🔍</span> {{ __('Question Auto-Correction Review (Read-Only)') }}
                </h4>
                <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full font-bold">🔒 {{ __('Read-Only') }}</span>
            </div>
            <div id="submissionQuestionsContainer" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                <p class="text-xs text-slate-400 italic text-center py-4">{{ __('Loading question breakdown...') }}</p>
            </div>
        </div>

        <form id="gradeForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="gradeSubmissionId">
            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Score / Percentage (%)') }}</label>
                <input type="number" step="0.1" min="0" max="100" id="gradeScoreInput" name="score" required placeholder="e.g. 95.0" class="input-mobile">
            </div>

            <div>
                <label class="block text-xs font-mono font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ __('Teacher Evaluation Notes') }}</label>
                <textarea id="gradeEvaluationNotes" name="evaluation_notes" rows="3" placeholder="Great job! Exceptional work on formulas." class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:border-teal-600"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('gradeModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save & Send Grade') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 6: MARK ATTENDANCE --}}
<div id="attendanceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full shadow-2xl border border-slate-200 space-y-6 relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-heading font-black text-xl text-slate-900">{{ __('Mark Session Attendance') }}</h3>
                <p id="attendanceSessionTitle" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('attendanceModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <form id="attendanceForm" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" id="attendanceSessionId">

            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                @if($assignedStudents->count() > 0)
                    @foreach($assignedStudents as $idx => $st)
                        <div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
                            <span class="text-xs font-bold text-slate-900">{{ $st->user?->name }}</span>
                            <input type="hidden" name="attendance[{{ $idx }}][student_user_id]" value="{{ $st->user_id }}">
                            <select name="attendance[{{ $idx }}][status]" class="text-xs font-mono font-bold bg-white border border-slate-200 rounded-lg p-1.5 focus:outline-none focus:border-teal-600">
                                <option value="present">🟢 {{ __('Present') }}</option>
                                <option value="absent">🔴 {{ __('Absent') }}</option>
                                <option value="late">🟡 {{ __('Late') }}</option>
                                <option value="excused">🔵 {{ __('Excused') }}</option>
                            </select>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-slate-500 italic text-center py-4">{{ __('No enrolled students to mark attendance.') }}</p>
                @endif
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('attendanceModal')" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-lift px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md">
                    {{ __('Save Attendance') }} &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 7: STUDENT ACADEMIC PROFILE & ATTENDANCE / SUBMISSIONS --}}
<div id="studentDetailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 id="stModalName" class="font-heading font-black text-xl text-slate-900">Student Academic Profile</h3>
                <p id="stModalGrade" class="text-xs text-teal-600 font-bold font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('studentDetailsModal')" class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>

        <div class="space-y-5 text-xs font-mono">
            <div class="p-4 bg-[#FAFAF9] rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p><strong class="text-slate-900">Email:</strong> <span id="stModalEmail" class="text-slate-700"></span></p>
                    <p><strong class="text-slate-900">School:</strong> <span id="stModalSchool" class="text-slate-700"></span></p>
                </div>
            </div>

            {{-- Attendance History Section --}}
            <div class="space-y-2">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>📅</span> {{ __('Session Attendance Log') }}
                </h4>
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] text-slate-500 uppercase font-mono border-b border-slate-200">
                                <th class="py-2.5 px-3">{{ __('Session Title') }}</th>
                                <th class="py-2.5 px-3">{{ __('Date') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody id="stModalAttendanceTable">
                            <tr><td colspan="3" class="p-3 text-center text-slate-400 italic">Loading attendance...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Submissions History Section --}}
            <div class="space-y-2">
                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <span>📝</span> {{ __('Submitted Homework & Scores') }}
                </h4>
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] text-slate-500 uppercase font-mono border-b border-slate-200">
                                <th class="py-2.5 px-3">{{ __('Assignment') }}</th>
                                <th class="py-2.5 px-3">{{ __('Submitted At') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('Score') }}</th>
                            </tr>
                        </thead>
                        <tbody id="stModalSubmissionsTable">
                            <tr><td colspan="3" class="p-3 text-center text-slate-400 italic">Loading submissions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end border-t border-slate-100">
            <button type="button" onclick="closeModal('studentDetailsModal')" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm">
                Close &rarr;
            </button>
        </div>
    </div>
</div>

<script>
    const i18n = {
        loading: "{{ __('Loading...') }}",
        loadingAttendance: "{{ __('Loading attendance...') }}",
        loadingSubmissions: "{{ __('Loading submissions...') }}",
        noAttendanceRecords: "{{ __('No attendance records logged yet.') }}",
        noAssignmentsSubmitted: "{{ __('No assignments submitted yet.') }}",
        pendingGrade: "{{ __('Pending Grade') }}",
        draft: "{{ __('Draft') }}",
        studentProfile: "{{ __('Student Academic Profile') }}",
        loadingReview: "{{ __('Loading auto-correction review...') }}",
        correct: "{{ __('Correct') }}",
        incorrect: "{{ __('Incorrect') }}",
        studentCorrectPick: "{{ __('🟢 Student Correct Pick') }}",
        correctKey: "{{ __('✓ Correct Key') }}",
        studentWrongPick: "{{ __('❌ Student Wrong Pick') }}",
        explanation: "{{ __('💡 Explanation:') }}",
        question: "{{ __('Question') }}",
        noQuestionBreakdown: "{{ __('No question breakdown recorded for this submission.') }}",
        unableToLoadBreakdown: "{{ __('Unable to load question breakdown.') }}",
        confirmCancelSession: "{{ __('Are you sure you want to cancel this live session? Affected students will be notified immediately.') }}",
        failedCancelSession: "{{ __('Failed to cancel session') }}",
        validationError: "{{ __('Validation error') }}",
        networkError: "{{ __('Network connection error') }}",
    };

    function filterTeacherStudents() {
        const query = (document.getElementById('studentSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.student-card');
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const email = card.getAttribute('data-email') || '';
            const school = card.getAttribute('data-school') || '';
            if (name.includes(query) || email.includes(query) || school.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    async function openStudentDetailsModal(studentUserId) {
        document.getElementById('stModalName').textContent = i18n.loading;
        document.getElementById('stModalEmail').textContent = '';
        document.getElementById('stModalSchool').textContent = '';
        document.getElementById('stModalGrade').textContent = '';
        document.getElementById('stModalAttendanceTable').innerHTML = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.loadingAttendance}</td></tr>`;
        document.getElementById('stModalSubmissionsTable').innerHTML = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.loadingSubmissions}</td></tr>`;

        openModal('studentDetailsModal');

        try {
            const res = await fetch(`/ajax/teacher/students/${studentUserId}/details`);
            const data = await res.json();
            if (data.success && data.student) {
                document.getElementById('stModalName').textContent = data.student.name;
                document.getElementById('stModalEmail').textContent = data.student.email;
                document.getElementById('stModalSchool').textContent = data.student.school;
                document.getElementById('stModalGrade').textContent = data.student.grade;

                let attHtml = '';
                if (data.attendance && data.attendance.length > 0) {
                    data.attendance.forEach(a => {
                        let badge = 'bg-slate-100 text-slate-700';
                        if (a.status === 'present') badge = 'bg-emerald-100 text-emerald-800';
                        else if (a.status === 'absent') badge = 'bg-red-100 text-red-800';
                        else if (a.status === 'late') badge = 'bg-amber-100 text-amber-800';
                        else if (a.status === 'excused') badge = 'bg-blue-100 text-blue-800';

                        attHtml += `<tr class="border-b border-slate-100 text-xs">
                            <td class="py-2.5 px-3 font-bold text-slate-900">${a.title}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                            <td class="py-2.5 px-3 text-right"><span class="px-2 py-0.5 rounded-full font-mono text-[10px] font-bold ${badge}">${a.status.toUpperCase()}</span></td>
                        </tr>`;
                    });
                } else {
                    attHtml = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.noAttendanceRecords}</td></tr>`;
                }
                document.getElementById('stModalAttendanceTable').innerHTML = attHtml;

                let subHtml = '';
                if (data.submissions && data.submissions.length > 0) {
                    data.submissions.forEach(s => {
                        const scoreStr = s.score !== null ? `<span class="font-extrabold text-emerald-600">${parseFloat(s.score).toFixed(1)}%</span>` : `<span class="text-orange-500 italic">${i18n.pendingGrade}</span>`;
                        subHtml += `<tr class="border-b border-slate-100 text-xs">
                            <td class="py-2.5 px-3 font-bold text-slate-900">${s.assignment_title}</td>
                            <td class="py-2.5 px-3 font-mono text-slate-500">${s.submitted_at || i18n.draft}</td>
                            <td class="py-2.5 px-3 text-right font-mono">${scoreStr}</td>
                        </tr>`;
                    });
                } else {
                    subHtml = `<tr><td colspan="3" class="p-3 text-center text-slate-400 italic">${i18n.noAssignmentsSubmitted}</td></tr>`;
                }
                document.getElementById('stModalSubmissionsTable').innerHTML = subHtml;
            }
        } catch (err) {
            document.getElementById('stModalName').textContent = i18n.studentProfile;
        }
    }

    async function openGradeModal(submissionId, studentName, assignmentTitle, currentScore, evaluationNotes) {
        document.getElementById('gradeSubmissionId').value = submissionId;
        document.getElementById('gradeStudentName').textContent = `${studentName} — ${assignmentTitle}`;
        document.getElementById('gradeScoreInput').value = currentScore && currentScore !== 'null' ? currentScore : '';
        const notesEl = document.getElementById('gradeEvaluationNotes');
        if (notesEl) notesEl.value = evaluationNotes && evaluationNotes !== 'null' ? evaluationNotes : '';
        document.getElementById('gradeForm').action = `/ajax/teacher/submissions/${submissionId}/review`;

        const questionsContainer = document.getElementById('submissionQuestionsContainer');
        questionsContainer.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">${i18n.loadingReview}</p>`;

        openModal('gradeModal');

        try {
            const res = await fetch(`/ajax/teacher/submissions/${submissionId}/review-details`);
            const data = await res.json();
            if (data.success && data.questions && data.questions.length > 0) {
                let html = '';
                data.questions.forEach((q, idx) => {
                    const statusBadge = q.is_correct
                        ? `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold rounded-full">🟢 ${i18n.correct} (+${q.points_earned}/${q.points} pts)</span>`
                        : `<span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-bold rounded-full">🔴 ${i18n.incorrect} (0/${q.points} pts)</span>`;

                    let optsHtml = '';
                    q.options.forEach(opt => {
                        let optStyle = 'bg-white border-slate-200 text-slate-700';
                        let badge = '';

                        if (opt.is_correct && opt.is_selected) {
                            optStyle = 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold';
                            badge = `<span class="text-emerald-600 font-mono text-[10px]">${i18n.studentCorrectPick}</span>`;
                        } else if (opt.is_correct) {
                            optStyle = 'bg-teal-50 border-teal-300 text-teal-900 font-bold';
                            badge = `<span class="text-teal-600 font-mono text-[10px]">${i18n.correctKey}</span>`;
                        } else if (opt.is_selected) {
                            optStyle = 'bg-red-50 border-red-300 text-red-900 font-bold';
                            badge = `<span class="text-red-600 font-mono text-[10px]">${i18n.studentWrongPick}</span>`;
                        }

                        optsHtml += `<div class="p-2.5 rounded-xl border ${optStyle} text-xs flex items-center justify-between gap-2">
                            <span>${opt.option_text}</span>
                            ${badge}
                        </div>`;

                        if (opt.explanation && opt.is_correct) {
                            optsHtml += `<p class="text-[11px] text-slate-500 font-mono italic pl-2">${i18n.explanation} ${opt.explanation}</p>`;
                        }
                    });

                    html += `<div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900">Q${idx + 1}: ${q.question_text || i18n.question}</span>
                            ${statusBadge}
                        </div>
                        <div class="space-y-1.5 pt-1">
                            ${optsHtml}
                        </div>
                    </div>`;
                });
                questionsContainer.innerHTML = html;
            } else {
                questionsContainer.innerHTML = `<p class="text-xs text-slate-500 italic text-center py-4">${i18n.noQuestionBreakdown}</p>`;
            }
        } catch (err) {
            questionsContainer.innerHTML = `<p class="text-xs text-red-500 italic text-center py-4">${i18n.unableToLoadBreakdown}</p>`;
        }
    }

document.addEventListener('DOMContentLoaded', function () {
    // 0. Auto-switch tab from URL query param (e.g. ?tab=sessions)
    const urlParams = new URLSearchParams(window.location.search);
    const requestedTab = urlParams.get('tab');
    if (requestedTab) {
        switchTeacherTab(requestedTab);
    }

    // 1. Counter Animations
    const counters = document.querySelectorAll('.js-counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target') || '0', 10);
        if (target === 0) return;
        let count = 0;
        const step = Math.max(1, Math.ceil(target / 25));
        const timer = setInterval(() => {
            count += step;
            if (count >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = count.toLocaleString();
            }
        }, 30);
    });

    // 2. Form Submissions Handlers
    bindAjaxForm('createSessionForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createSessionModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('createAssignmentForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('createAssignmentModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('meetingLinkForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('meetingLinkModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('rescheduleForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('rescheduleModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('gradeForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('gradeModal');
        setTimeout(() => location.reload(), 1000);
    });

    bindAjaxForm('attendanceForm', function (data) {
        showTeacherToast(data.message, true);
        closeModal('attendanceModal');
        setTimeout(() => location.reload(), 1000);
    });
});

function switchTeacherTab(tabKey) {
    document.querySelectorAll('.teacher-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.teacher-tab-btn').forEach(btn => {
        btn.classList.remove('bg-teal-600', 'text-white', 'shadow-md');
        btn.classList.add('text-slate-700', 'hover:bg-slate-100');
    });

    const activeContent = document.getElementById('teacher-tab-' + tabKey);
    const activeBtn = document.getElementById('tab-btn-' + tabKey);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-100');
        activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-md');
    }
}

function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('hidden');
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('hidden');
}

function openCreateSessionModal() {
    openModal('createSessionModal');
}

function openCreateAssignmentModal() {
    openModal('createAssignmentModal');
}

function openMeetingLinkModal(sessionId, currentLink) {
    document.getElementById('linkSessionId').value = sessionId;
    document.getElementById('meetingUrlInput').value = currentLink || '';
    document.getElementById('meetingLinkForm').action = `/ajax/teacher/sessions/${sessionId}/link`;
    openModal('meetingLinkModal');
}

function openRescheduleModal(sessionId, currentDateTime) {
    document.getElementById('rescheduleSessionId').value = sessionId;
    document.getElementById('rescheduleDateTime').value = currentDateTime || '';
    document.getElementById('rescheduleForm').action = `/ajax/teacher/sessions/${sessionId}/reschedule`;
    openModal('rescheduleModal');
}

function openGradeModal(submissionId, studentName, assignmentTitle, currentScore) {
    document.getElementById('gradeSubmissionId').value = submissionId;
    document.getElementById('gradeStudentName').textContent = `${studentName} — ${assignmentTitle}`;
    document.getElementById('gradeScoreInput').value = currentScore && currentScore !== 'null' ? currentScore : '';
    document.getElementById('gradeForm').action = `/ajax/teacher/submissions/${submissionId}/review`;
    openModal('gradeModal');
}

function openAttendanceModal(sessionId, sessionTitle) {
    document.getElementById('attendanceSessionId').value = sessionId;
    document.getElementById('attendanceSessionTitle').textContent = sessionTitle;
    document.getElementById('attendanceForm').action = `/ajax/teacher/sessions/${sessionId}/attendance`;
    openModal('attendanceModal');
}

async function confirmCancelSession(sessionId) {
    if (!confirm('{{ __("Are you sure you want to cancel this live session? Affected students will be notified immediately.") }}')) {
        return;
    }

    try {
        const res = await fetch(`/ajax/teacher/sessions/${sessionId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        showTeacherToast(data.message, data.success);
        if (data.success) {
            setTimeout(() => location.reload(), 1000);
        }
    } catch (err) {
        showTeacherToast('Failed to cancel session', false);
    }
}

function bindAjaxForm(formId, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                onSuccess(data);
            } else {
                showTeacherToast(data.message || 'Validation error', false);
            }
        } catch (err) {
            showTeacherToast('Network connection error', false);
        }
    });
}

function showTeacherToast(message, isSuccess) {
    const toast = document.getElementById('teacherToastAlert');
    if (!toast) return;
    toast.className = `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
    toast.textContent = message;
    toast.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection

```

---

## File: `resources/views/pages/teacher-profile.blade.php`

```blade
@extends('layouts.app')

@section('content')
@php
    $name = $teacher->user->name ?? 'Dr. Ahmed Hassan';
    $title = $teacher->title ?? 'Senior Professor';
    $specialization = $teacher->specialization ?? 'Secondary Education';
    $bio = $teacher->bio ?: 'Expert instructor with extensive experience preparing secondary students for top academic achievements.';
    $rating = number_format($teacher->rating_avg ?: 4.9, 1) . ' ★';
    $studentsCount = number_format($teacher->students_count ?: 100) . '+';
    $yearsExp = ($teacher->years_experience ?: 5) . '+ Years';
    $photo = $teacher->photo_url;
    $courses = $teacher->courses ?: [];
@endphp

<section class="relative py-16 md:py-24 bg-white border-b border-slate-200/80 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('navbar.teachers'), 'route' => 'teachers'],
                ['label' => $name],
            ]
        ])

        {{-- Magazine Hero Card Layout --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 rounded-3xl p-8 lg:p-12 text-white shadow-2xl relative overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-5 relative">
                <div class="rounded-2xl overflow-hidden border-4 border-white/20 shadow-2xl h-[380px] bg-slate-950">
                    <img src="{{ media_url($photo, 'images/instructor_portrait.png') }}" alt="{{ $name }}" class="w-full h-full object-cover">
                </div>
                <span class="absolute bottom-4 left-4 bg-teal-500 text-slate-950 font-mono font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow-lg">
                    ✔ Faculty Member
                </span>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950/80 px-3.5 py-1.5 rounded-full border border-teal-800">
                        {{ $specialization }}
                    </span>
                    <h1 class="font-heading text-3xl sm:text-5xl font-black text-white tracking-tight">
                        {{ $name }}
                    </h1>
                    <p class="text-slate-300 text-base font-mono">
                        {{ $title }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-mono font-semibold text-slate-200">
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-teal-400 font-bold block text-sm">{{ $yearsExp }}</span>
                        Teaching Experience
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-amber-400 font-bold block text-sm">{{ $rating }}</span>
                        Student Evaluation
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl border border-white/10 col-span-2 sm:col-span-1">
                        <span class="text-teal-400 font-bold block text-sm">{{ $studentsCount }}</span>
                        Active Students
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Bio & Teaching Sections --}}
<section class="py-20 md:py-28 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-4">
                <h2 class="font-heading text-2xl font-black text-slate-900">Biography & Academic Background</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    {{ $bio }}
                </p>
            </div>

            <div class="space-y-6">
                <h2 class="font-heading text-2xl font-black text-slate-900">Courses Taught by {{ $name }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @forelse ($courses as $c)
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 card-lift">
                            <span class="text-xs font-mono font-extrabold text-teal-600 uppercase">{{ $c->subject->name ?? 'ACADEMIC COURSE' }}</span>
                            <h3 class="font-heading font-extrabold text-xl text-slate-900">{{ $c->title }}</h3>
                            <p class="text-xs font-mono text-slate-500">Accredited Course Curriculum</p>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-lg space-y-2 col-span-2">
                            <h3 class="font-heading font-extrabold text-base text-slate-800">Secondary Education Courses</h3>
                            <p class="text-xs font-mono text-slate-500">Curriculum modules available in student portal.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xl space-y-6 sticky top-28">
                <h3 class="font-heading font-black text-xl text-slate-900">Join {{ $name }}'s Cohort</h3>
                <p class="text-xs font-mono text-slate-500">Get direct access to weekly live Q&A webinars and revision worksheets.</p>

                <a href="{{ route('student-portal') }}" class="btn-lift block w-full text-center py-3.5 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-lg shadow-teal-600/20">
                    Enroll with Teacher &rarr;
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

```

---

## File: `resources/views/pages/teachers.blade.php`

```blade
@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Teacher Directory Header --}}
        <div class="space-y-2 border-b border-slate-200/80 pb-6">
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                {{ __('Meet Our') }} <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">{{ __('Expert Teachers') }}</span>
            </h1>
            <p class="text-slate-600 text-base font-medium">
                {{ __('Browse experienced teachers by subject and grade level.') }}
            </p>
        </div>

        {{-- Teacher Filters Toolbar --}}
        <form id="teachers-filter-form" method="GET" action="{{ route('teachers') }}" class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-lg space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Search Teacher') }}</label>
                    <div class="relative">
                        <input type="text" id="teacher-search-input" name="q" value="{{ $searchQuery ?? '' }}" placeholder="{{ __('Search teacher by name, title, or specialization...') }}" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 focus:outline-teal-600">
                        <span id="search-spinner" class="hidden absolute right-3 top-3 text-slate-400 text-xs animate-spin">⏳</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">{{ __('Subject Filter') }}</label>
                    <select id="teacher-subject-select" name="subject" class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-3.5 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value="">{{ __('All Subjects') }}</option>
                        @foreach ($subjects as $s)
                            <option value="{{ $s->slug }}" @selected(($selectedSubject ?? '') === $s->slug)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1 flex items-end">
                    <button type="submit" class="w-full h-11 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold font-mono transition-all card-lift">
                        {{ __('Filter Results') }}
                    </button>
                </div>
            </div>

            {{-- Subject Filter Chips --}}
            <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold pt-4 border-t border-slate-100">
                <span class="text-slate-400 mr-2 uppercase">{{ __('Subject Filters:') }}</span>
                <button type="button" data-subject=""
                   @class([
                       'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer',
                       'bg-teal-600 text-white border-teal-600 shadow-xs' => empty($selectedSubject),
                       'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! empty($selectedSubject),
                   ])>
                    {{ __('All') }}
                </button>
                @foreach ($subjects as $s)
                    @php $isActive = strtolower($selectedSubject ?? '') === strtolower($s->slug); @endphp
                    <button type="button" data-subject="{{ $s->slug }}"
                       @class([
                           'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer',
                           'bg-teal-600 text-white border-teal-600 shadow-xs' => $isActive,
                           'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' => ! $isActive,
                       ])>
                        {{ $s->name }}
                    </button>
                @endforeach
            </div>
        </form>

        {{-- Counter Info Row --}}
        <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-600 px-2 py-1 bg-white rounded-2xl border border-slate-200/80 shadow-2xs">
            <span id="faculty-counter">
                {{ __('Showing') }} <strong id="count-from" class="text-teal-600">{{ $teachers->firstItem() ?? 0 }}</strong>–<strong id="count-to" class="text-teal-600">{{ $teachers->lastItem() ?? 0 }}</strong> {{ __('of') }} <strong id="count-total" class="text-slate-900">{{ number_format($teachers->total()) }}</strong> {{ __('Teachers') }}
            </span>
            <span class="hidden sm:inline text-slate-400">{{ __('Faculty Directory • Accredited Tracks') }}</span>
        </div>

        {{-- Teachers Main Content & Loading State --}}
        <div class="relative min-h-[300px]">
            {{-- Loading Spinner Overlay --}}
            <div id="teachers-loading-overlay" class="hidden absolute inset-0 bg-white/70 backdrop-blur-xs z-10 flex items-center justify-center rounded-3xl transition-opacity">
                <div class="bg-slate-900 text-white px-6 py-3 rounded-2xl shadow-2xl font-mono text-xs font-bold flex items-center gap-3">
                    <span class="animate-spin text-teal-400">⏳</span> {{ __('Updating Teachers Directory...') }}
                </div>
            </div>

            {{-- Teachers Grid --}}
            <div id="teachers-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @include('partials.teachers-grid-items', ['teachers' => $teachers])
            </div>
        </div>

        {{-- Dynamic Pagination Container --}}
        <div id="pagination-container" class="pt-6">
            {!! $teachers->links('components.pagination') !!}
        </div>

    </div>
</section>

{{-- Real-time Dynamic AJAX JavaScript Engine --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('teachers-filter-form');
    const searchInput = document.getElementById('teacher-search-input');
    const subjectSelect = document.getElementById('teacher-subject-select');
    const subjectChips = document.querySelectorAll('.subject-chip');
    const gridContainer = document.getElementById('teachers-grid-container');
    const paginationContainer = document.getElementById('pagination-container');
    const loadingOverlay = document.getElementById('teachers-loading-overlay');
    const searchSpinner = document.getElementById('search-spinner');

    const countFrom = document.getElementById('count-from');
    const countTo = document.getElementById('count-to');
    const countTotal = document.getElementById('count-total');

    let debounceTimer = null;
    let activeSubject = '{{ $selectedSubject ?? "" }}';
    let currentPage = {{ $teachers->currentPage() }};

    function fetchTeachers(page = 1) {
        currentPage = page;
        const q = searchInput.value.trim();
        const subject = subjectSelect.value;

        // Build Query URL
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (subject) params.set('subject', subject);
        if (page > 1) params.set('page', page);

        const requestUrl = `{{ route('teachers') }}?${params.toString()}`;

        // Show UI Loading State
        loadingOverlay.classList.remove('hidden');
        if (searchSpinner) searchSpinner.classList.remove('hidden');

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update Grid & Pagination HTML
                gridContainer.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination_html;

                // Update Counter Stats
                if (countFrom) countFrom.textContent = data.from;
                if (countTo) countTo.textContent = data.to;
                if (countTotal) countTotal.textContent = data.total.toLocaleString();

                // Update Chip Highlight State
                updateSubjectChips(subject);

                // Push URL State
                history.pushState(null, '', requestUrl);
            }
        })
        .catch(error => {
            console.error('AJAX Teacher Fetch Error:', error);
        })
        .finally(() => {
            loadingOverlay.classList.add('hidden');
            if (searchSpinner) searchSpinner.classList.add('hidden');
        });
    }

    function updateSubjectChips(selectedSubject) {
        subjectChips.forEach(chip => {
            const chipSubject = chip.getAttribute('data-subject');
            const isActive = (chipSubject === selectedSubject) || (chipSubject === '' && !selectedSubject);

            if (isActive) {
                chip.className = 'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer bg-teal-600 text-white border-teal-600 shadow-xs';
            } else {
                chip.className = 'subject-chip px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border cursor-pointer bg-white text-slate-700 hover:bg-slate-100 border-slate-200';
            }
        });
    }

    // Event 1: Form Submit Guard
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchTeachers(1);
    });

    // Event 2: Debounced Real-time Search Typing (300ms)
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchTeachers(1);
        }, 300);
    });

    // Event 3: Subject Dropdown Change
    subjectSelect.addEventListener('change', function () {
        fetchTeachers(1);
    });

    // Event 4: Subject Filter Chips Click
    subjectChips.forEach(chip => {
        chip.addEventListener('click', function () {
            const selectedSubject = this.getAttribute('data-subject');
            subjectSelect.value = selectedSubject;
            fetchTeachers(1);
        });
    });

    // Event 5: Dynamic Pagination Page Links Click Delegation
    paginationContainer.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination-link');
        if (link) {
            e.preventDefault();
            const targetPage = link.getAttribute('data-page');
            if (targetPage) {
                fetchTeachers(parseInt(targetPage, 10));
                window.scrollTo({ top: gridContainer.offsetTop - 120, behavior: 'smooth' });
            }
        }
    });

    // Handle Browser Back/Forward Buttons
    window.addEventListener('popstate', function () {
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get('q') || '';
        subjectSelect.value = urlParams.get('subject') || '';
        const page = parseInt(urlParams.get('page') || '1', 10);
        fetchTeachers(page);
    });
});
</script>
@endsection

```

---

## File: `resources/views/partials/ambient.blade.php`

```blade
{{-- Ambient background mesh blobs (Strictly fixed background container to avoid padding gaps) --}}
<div class="fixed inset-0 pointer-events-none overflow-hidden -z-10" aria-hidden="true">
    <div class="absolute top-8 -left-24 w-[34rem] h-[34rem] bg-teal-400/15 rounded-full blur-3xl animate-pulse-glow"></div>
    <div class="absolute top-1/3 -right-24 w-[34rem] h-[34rem] bg-orange-400/15 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 left-1/3 w-[28rem] h-[28rem] bg-purple-400/15 rounded-full blur-3xl animate-float-reverse"></div>
    <div class="absolute top-28 left-[10%] w-4 h-4 border-2 border-teal-500/30 rounded-full animate-drift"></div>
    <div class="absolute top-96 right-[12%] w-6 h-6 border-2 border-orange-500/20 rounded-md rotate-12 animate-float"></div>
</div>

```

---

## File: `resources/views/partials/footer.blade.php`

```blade
@php
    $locale = app()->getLocale();

    $tagline = \App\Models\SiteSetting::get('footer_tagline_' . $locale, __('footer.tagline'));
    $quickTitle = \App\Models\SiteSetting::get('footer_quick_links_title_' . $locale, __('footer.quick_links'));
    $subjectsTitle = \App\Models\SiteSetting::get('footer_subjects_title_' . $locale, __('footer.subjects'));
    $contactTitle = \App\Models\SiteSetting::get('footer_contact_title_' . $locale, __('footer.contact'));
    
    $address = \App\Models\SiteSetting::get('contact_address_' . $locale, \App\Models\SiteSetting::get('contact_address_en', __('footer.address')));
    $phone = \App\Models\SiteSetting::get('contact_phone', '+20 100 000 0000');
    $email = \App\Models\SiteSetting::get('contact_email', 'info@eliteacademy.edu.eg');
    $hours = \App\Models\SiteSetting::get('footer_working_hours_' . $locale, __('footer.hours'));
    
    $rights = \App\Models\SiteSetting::get('footer_rights_' . $locale, __('footer.rights'));

    // Dynamic Quick Links Repeater
    $quickLinksRaw = \App\Models\SiteSetting::get('footer_quick_links');
    $quickLinks = $quickLinksRaw ? json_decode($quickLinksRaw, true) : [
        ['label_ar' => 'الرئيسية', 'label_en' => 'Home', 'url' => '/'],
        ['label_ar' => 'الأسئلة الشائعة', 'label_en' => 'FAQ & Help', 'url' => '/faq'],
        ['label_ar' => 'من نحن', 'label_en' => 'About Us', 'url' => '/about'],
        ['label_ar' => 'المعلمون', 'label_en' => 'Teachers', 'url' => '/teachers'],
        ['label_ar' => 'المدونة', 'label_en' => 'Blog', 'url' => '/blog'],
        ['label_ar' => 'خريطة الموقع', 'label_en' => 'Sitemap XML', 'url' => '/sitemap.xml'],
        ['label_ar' => 'بوابة الطلاب', 'label_en' => 'Student Portal', 'url' => '/student-portal'],
    ];

    // Dynamic Subjects Links Repeater
    $subjectsLinksRaw = \App\Models\SiteSetting::get('footer_subjects_links');
    $subjectsLinks = $subjectsLinksRaw ? json_decode($subjectsLinksRaw, true) : [
        ['label_ar' => 'البرمجة', 'label_en' => 'Programming', 'url' => '/subjects'],
        ['label_ar' => 'الذكاء الاصطناعي', 'label_en' => 'Artificial Intelligence', 'url' => '/subjects'],
        ['label_ar' => 'العلوم والفيزياء', 'label_en' => 'Science & Physics', 'url' => '/subjects'],
        ['label_ar' => 'إدارة الأعمال', 'label_en' => 'Business Administration', 'url' => '/subjects'],
        ['label_ar' => 'التصميم الإبداعي', 'label_en' => 'Creative Design', 'url' => '/subjects'],
        ['label_ar' => 'الرياضيات', 'label_en' => 'Mathematics', 'url' => '/subjects'],
    ];
@endphp

<footer class="bg-slate-950 text-slate-300 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 text-center rtl:sm:text-right ltr:sm:text-left">
            
            {{-- Col 1: Brand & Tagline --}}
            <div class="sm:col-span-2 space-y-4 flex flex-col items-center rtl:sm:items-start ltr:sm:items-start">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-20 sm:h-24 lg:h-28 w-auto max-h-28 object-contain mx-auto sm:mx-0">
                </a>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm text-center rtl:sm:text-right ltr:sm:text-left">
                    {{ $tagline }}
                </p>
            </div>

            {{-- Col 2: Dynamic Quick Links --}}
            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $quickTitle }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    @foreach($quickLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 3: Dynamic Subjects / Services Links --}}
            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $subjectsTitle }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    @foreach($subjectsLinks as $link)
                        @php
                            $label = ($locale === 'ar' ? ($link['label_ar'] ?? null) : null) ?: ($link['label_en'] ?? '');
                            $url = $link['url'] ?? '#';
                            if (str_starts_with($url, '/')) {
                                $url = url($url);
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="hover:text-teal-400 transition-colors link-underline">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 4: Dynamic Contact Information --}}
            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ $contactTitle }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">📍 <span>{{ $address }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">📞 <span>{{ $phone }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">✉️ <span>{{ $email }}</span></li>
                    <li class="flex items-center justify-center rtl:sm:justify-start ltr:sm:justify-start gap-2">🕒 <span>{{ $hours }}</span></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Footer: Social Links & Copyright Notice --}}
        <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-400">
            <div class="flex items-center gap-4">
                <a href="{{ \App\Models\SiteSetting::get('social_facebook', '#') }}" target="_blank" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Facebook">f</a>
                <a href="{{ \App\Models\SiteSetting::get('social_twitter', '#') }}" target="_blank" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Twitter">𝕏</a>
                <a href="{{ \App\Models\SiteSetting::get('social_instagram', '#') }}" target="_blank" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Instagram">ig</a>
                <a href="{{ \App\Models\SiteSetting::get('social_linkedin', '#') }}" target="_blank" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="LinkedIn">in</a>
                <a href="{{ \App\Models\SiteSetting::get('social_youtube', '#') }}" target="_blank" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="YouTube">yt</a>
            </div>
            <p>{{ $rights }}</p>
        </div>
    </div>
</footer>


```

---

## File: `resources/views/partials/inp-optimizer.blade.php`

```blade
{{-- Elite Academy — High-Performance Instant Motion & INP Engine --}}
<style>
    /* GPU Hardware Acceleration for Sub-16ms Response Times */
    button, a, summary, input, select, textarea, .btn-lift, .card-lift, .subject-chip {
        will-change: transform, opacity;
        transform: translateZ(0);
        -webkit-tap-highlight-color: transparent;
    }

    /* Fast Touch & Click Response */
    html {
        touch-action: manipulation;
    }

    /* Emergency Fallback: If animation JS is delayed, render visible after 200ms */
    @keyframes instantRevealFallback {
        to {
            opacity: 1 !important;
            transform: none !important;
        }
    }

    .sr, .sr-h, .sr-img, .sr-btn, .sr-card, .sr-sub, .sr-stat {
        animation: instantRevealFallback 1ms forwards 250ms;
    }
</style>

<script>
(function() {
    'use strict';

    // Non-blocking Event Scheduling for Sub-35ms INP
    const scheduleINPTask = (task) => {
        if ('requestAnimationFrame' in window) {
            requestAnimationFrame(() => {
                requestAnimationFrame(task);
            });
        } else {
            setTimeout(task, 0);
        }
    };

    // Instant Above-The-Fold Viewport Pre-Reveal Guard
    const preRevealViewportElements = () => {
        const elements = document.querySelectorAll('.sr, .sr-h, .sr-img, .sr-btn, .sr-card, .sr-sub, .sr-stat');
        const viewHeight = window.innerHeight * 1.25;

        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top <= viewHeight) {
                el.classList.add('revealed');
            }
        });
    };

    // Attach Passive & Non-blocking Listeners
    document.addEventListener('DOMContentLoaded', function() {
        preRevealViewportElements();

        const interactiveElements = document.querySelectorAll('button, a, summary, input, select, .subject-chip');

        interactiveElements.forEach(el => {
            el.addEventListener('pointerdown', function() {
                this.style.opacity = '0.92';
                scheduleINPTask(() => {
                    this.style.opacity = '';
                });
            }, { passive: true });
        });
    });

    window.addEventListener('load', preRevealViewportElements, { passive: true });
})();
</script>

```

---

## File: `resources/views/partials/navbar.blade.php`

```blade
@php
    $navLink = fn (string $key, string $route, string $label) => [
        'key' => $key,
        'route' => $route,
        'label' => $label,
        'active' => ($activeNav ?? null) === $key,
    ];
    $navItems = [
        $navLink('home', 'home', __('Home')),
        $navLink('subjects', 'subjects', __('Subjects')),
        $navLink('courses', 'courses', __('Courses')),
        $navLink('teachers', 'teachers', __('Teachers')),
        $navLink('blog', 'blog', __('Blog')),
        $navLink('faq', 'faq', __('FAQ')),
        $navLink('about', 'about', __('About')),
        $navLink('contact', 'contact', __('Contact')),
    ];
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $joinText = __('Join Us');
    $loginText = __('Log in');

    $authUser = auth()->user();
    $portalUrl = route('student-portal');
    $portalLabel = __('Student Portal');
    if ($authUser) {
        if ($authUser->isAdmin()) {
            $portalUrl = url('/admin');
            $portalLabel = __('Admin Panel');
        } elseif ($authUser->isTeacher()) {
            $portalUrl = route('teacher-portal');
            $portalLabel = __('Teacher Portal');

            // Teacher Private Workspace Navigation Items
            $navItems = [
                ['key' => 'portal', 'route' => 'teacher-portal', 'url' => route('teacher-portal'), 'label' => __('Dashboard'), 'active' => ($activeNav ?? null) === 'portal'],
                ['key' => 'sessions', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=sessions', 'label' => __('Sessions & Streams'), 'active' => false],
                ['key' => 'assignments', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=assignments', 'label' => __('Assignments'), 'active' => false],
                ['key' => 'attendance', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=attendance', 'label' => __('Attendance'), 'active' => false],
                ['key' => 'students', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=students', 'label' => __('My Students'), 'active' => false],
            ];
        } elseif ($authUser->isParent()) {
            $portalUrl = route('parent-portal');
            $portalLabel = __('Parent Portal');

            $currentTab = request()->query('tab', '');
            $navItems = [
                ['key' => 'portal', 'route' => 'parent-portal', 'url' => route('parent-portal'), 'label' => __('Dashboard'), 'active' => ($activeNav ?? null) === 'portal' && empty($currentTab)],
                ['key' => 'children', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-children', 'label' => __('My Children'), 'active' => $currentTab === 'children'],
                ['key' => 'sessions', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-sessions', 'label' => __('Upcoming Sessions'), 'active' => $currentTab === 'sessions'],
                ['key' => 'attendance', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-attendance', 'label' => __('Attendance & Absences'), 'active' => $currentTab === 'attendance'],
                ['key' => 'assignments', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-assignments', 'label' => __('Homework & Grades'), 'active' => $currentTab === 'assignments'],
            ];
        }
    }
@endphp

@if(\App\Models\SiteSetting::get('announcement_enabled') === '1')
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 text-white text-xs font-bold py-2 px-4 text-center border-b border-teal-500/30 flex items-center justify-center gap-2">
        <span>{{ \App\Models\SiteSetting::get('announcement_text', '🎉 Fall Cohort 2026 Registration is Now Open!') }}</span>
        <a href="{{ \App\Models\SiteSetting::get('announcement_link', '/courses') }}" class="underline font-extrabold hover:text-teal-300 focus-visible:outline-white">
            {{ app()->getLocale() === 'ar' ? 'التفاصيل والاشتراك ←' : 'Learn More →' }}
        </a>
    </div>
@endif

<header class="anim-nav sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[90px] lg:h-[100px] flex items-center justify-between gap-2 lg:gap-4">
        {{-- Logo (Significantly Increased Size) --}}
        <a href="{{ route('home') }}" class="flex items-center group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 rounded-xl transition-all duration-300 shrink-0 py-1" aria-label="Elite Academy Homepage">
            <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-20 sm:h-24 lg:h-26 w-auto max-h-22 object-contain transition-transform duration-300 group-hover:scale-105">
        </a>

        {{-- Desktop Navigation Links --}}
        <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 rtl:space-x-reverse text-xs lg:text-sm font-bold text-slate-800 shrink">
            @foreach ($navItems as $item)
                <a href="{{ $item['url'] ?? route($item['route']) }}"
                   @class([
                       'px-2 py-1 lg:px-3 lg:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600',
                       'text-teal-700 font-extrabold bg-teal-50/90 border border-teal-200/80 shadow-xs' => $item['active'],
                       'text-slate-800 font-bold hover:text-teal-600 hover:bg-slate-100/90' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        {{-- Desktop Right Controls --}}
        <div class="hidden md:flex items-center space-x-1.5 lg:space-x-2 rtl:space-x-reverse text-xs font-bold font-sans shrink-0">
            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}" class="px-2.5 py-1.5 rounded-xl text-slate-700 hover:text-slate-950 bg-slate-100/80 hover:bg-slate-200/80 uppercase border border-slate-200 transition-all font-sans font-bold shadow-xs whitespace-nowrap shrink-0" aria-label="Switch Language to {{ strtoupper($otherLocale) }}">
                🌐 {{ strtoupper($otherLocale) }}
            </a>

            @guest
                <a href="{{ route('login') }}" class="btn-lift px-3.5 py-2 rounded-xl text-slate-800 hover:bg-slate-100 border border-slate-200/90 transition-all font-sans font-bold text-xs whitespace-nowrap shrink-0">
                    {{ $loginText }}
                </a>
                <a href="{{ route('register') }}" class="btn-lift px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all font-sans font-extrabold text-xs whitespace-nowrap shrink-0">
                    ✨ {{ $joinText }}
                </a>
            @endguest

            @auth
                <a href="{{ $portalUrl }}" class="btn-lift px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md font-sans font-extrabold text-xs flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <span>📊</span> {{ $portalLabel }}
                </a>
                @if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent())
                    <a href="{{ route('student.profile') }}" class="btn-lift px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200/90 font-sans font-bold text-xs flex items-center gap-1 whitespace-nowrap shrink-0 hidden lg:flex">
                        <span>👤</span> {{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline shrink-0">
                    @csrf
                    <button type="submit" class="px-2.5 py-2 rounded-xl text-slate-600 hover:text-red-600 hover:bg-red-50 transition-colors font-sans font-bold text-xs whitespace-nowrap shrink-0">
                        {{ app()->getLocale() === 'ar' ? 'خروج' : 'Logout' }}
                    </button>
                </form>
            @endauth
        </div>

        {{-- Mobile Hamburger & Language Controls --}}
        <div class="flex items-center gap-2 md:hidden">
            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-sans font-bold text-slate-700 bg-slate-100 uppercase border border-slate-200" aria-label="Switch Language">
                {{ strtoupper($otherLocale) }}
            </a>

            <label for="mobile-drawer-toggle" class="p-2 text-slate-800 hover:bg-slate-100 rounded-xl cursor-pointer touch-press" aria-label="Toggle Navigation Menu">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>
    </div>
</header>

{{-- Mobile Drawer Navigation --}}
<input type="checkbox" id="mobile-drawer-toggle" class="peer hidden">

{{-- Drawer Backdrop --}}
<label for="mobile-drawer-toggle" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden peer-checked:flex transition-opacity duration-300 md:hidden"></label>

{{-- Drawer Content Panel --}}
<div class="fixed top-0 right-0 bottom-0 w-[300px] bg-white z-50 shadow-2xl flex flex-col justify-between p-6 transform translate-x-full peer-checked:translate-x-0 transition-transform duration-300 ease-in-out rtl:right-auto rtl:left-0 rtl:-translate-x-full rtl:peer-checked:translate-x-0 md:hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-14 sm:h-16 w-auto object-contain">
            <label for="mobile-drawer-toggle" class="p-2 text-slate-500 hover:text-slate-900 rounded-xl cursor-pointer font-bold text-lg">
                ✕
            </label>
        </div>

        <nav class="flex flex-col space-y-2">
            @foreach ($navItems as $item)
                <a href="{{ $item['url'] ?? route($item['route']) }}"
                   @class([
                       'px-4 py-3 rounded-2xl font-bold text-base transition-colors',
                       'bg-teal-50 text-teal-700 border border-teal-200/80 font-extrabold' => $item['active'],
                       'text-slate-800 hover:bg-slate-100/80' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>

    <div class="pt-6 border-t border-slate-100 space-y-3">
        @guest
            <a href="{{ route('login') }}" class="btn-mobile-lg text-slate-800 bg-slate-100 hover:bg-slate-200 touch-press text-center font-bold text-sm">{{ $loginText }}</a>
            <a href="{{ route('register') }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center font-extrabold text-sm">✨ {{ $joinText }}</a>
        @endguest
        @auth
            <a href="{{ $portalUrl }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center font-extrabold text-sm">📊 {{ auth()->user()->name }} ({{ $portalLabel }})</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-mobile-lg w-full text-red-600 bg-red-50 hover:bg-red-100 touch-press text-center font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log Out' }}</button>
            </form>
        @endauth
    </div>
</div>


```

---

## File: `resources/views/partials/teachers-grid-items.blade.php`

```blade
@forelse ($teachers as $t)
    @php
        $photo = $t->photo_url;
        $name = $t->user->name ?? 'Dr. Instructor';
        $title = $t->title ?? __('Senior Professor');
        $specialization = $t->specialization ?? __('Secondary Education');
        $rating = number_format($t->rating_avg ?: 4.9, 1) . ' ★';
        $studentsCount = number_format($t->students_count ?: 100) . ' ' . __('Students');
        $slug = $t->slug ?: $t->id;
    @endphp
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden flex flex-col justify-between hover:shadow-2xl transition-all duration-300 group card-lift">
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border-2 border-teal-500/20 group-hover:border-teal-500 transition-colors flex-shrink-0">
                    <img src="{{ media_url($photo, 'images/instructor_portrait.png') }}" alt="{{ $name }}" class="w-full h-full object-cover">
                </div>
                <div class="space-y-1">
                    <span class="inline-block text-[10px] font-mono font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-700 border border-teal-200">
                        {{ $specialization }}
                    </span>
                    <h3 class="font-heading font-black text-lg text-slate-900 group-hover:text-teal-600 transition-colors">
                        {{ $name }}
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">{{ $title }}</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                {{ $t->bio ?: __('Expert instructor with extensive experience preparing secondary students for top academic achievements.') }}
            </p>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs font-mono font-bold">
                <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                    <span class="text-slate-400 block text-[10px]">{{ __('Rating') }}</span>
                    <span class="text-amber-500 font-extrabold">{{ $rating }}</span>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl text-center">
                    <span class="text-slate-400 block text-[10px]">{{ __('Students') }}</span>
                    <span class="text-teal-600 font-extrabold">{{ $studentsCount }}</span>
                </div>
            </div>
        </div>

        <div class="p-6 pt-0">
            <a href="{{ route('teacher-profile', ['slug' => $slug]) }}" class="btn-lift w-full block text-center py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-teal-600/20 transition-all">
                {{ __('View Teacher Profile') }} &rarr;
            </a>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <div class="text-4xl mb-3">👨‍🏫</div>
        <h3 class="font-bold text-lg text-slate-800">{{ __('No Teachers Found') }}</h3>
        <p class="text-xs text-slate-500 mt-1 mb-4">{{ __('Try clearing filters or search term to see all faculty members.') }}</p>
        <a href="{{ route('teachers') }}" class="btn-lift inline-block px-5 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-bold">
            {{ __('View All Teachers') }}
        </a>
    </div>
@endforelse

```

---

