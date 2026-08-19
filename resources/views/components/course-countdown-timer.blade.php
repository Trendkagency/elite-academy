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
