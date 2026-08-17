@props([
    'targetDate' => '2026-09-01T10:00:00',
    'title' => 'Next Live Cohort Starts In',
    'subtitle' => 'Limited seats available for upcoming accredited term',
])

<div class="bg-gradient-to-br from-slate-900 via-slate-950 to-teal-950 text-white rounded-3xl p-6 sm:p-8 border border-teal-500/30 shadow-2xl space-y-6">
    <div class="flex items-center justify-between border-b border-teal-500/20 pb-4">
        <div class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
            <span class="font-mono text-xs font-bold uppercase tracking-widest text-rose-400">Live Timer Ticker</span>
        </div>
        <span class="text-xs font-mono bg-teal-900/80 text-teal-300 px-3 py-1 rounded-full border border-teal-700/50 font-bold">
            Fall Cohort 2026
        </span>
    </div>

    <div class="space-y-1">
        <h3 class="font-heading font-black text-xl sm:text-2xl text-white">{{ $title }}</h3>
        <p class="text-xs font-mono text-slate-300 leading-relaxed">{{ $subtitle }}</p>
    </div>

    {{-- Countdown Display Grid --}}
    <div id="countdown-widget" class="grid grid-cols-4 gap-3 text-center" data-target="{{ $targetDate }}">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/15 shadow-sm space-y-1">
            <span id="timer-days" class="font-mono font-black text-2xl sm:text-4xl text-teal-400">00</span>
            <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-300 font-extrabold">Days</span>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/15 shadow-sm space-y-1">
            <span id="timer-hours" class="font-mono font-black text-2xl sm:text-4xl text-orange-400">00</span>
            <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-300 font-extrabold">Hours</span>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/15 shadow-sm space-y-1">
            <span id="timer-mins" class="font-mono font-black text-2xl sm:text-4xl text-teal-400">00</span>
            <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-300 font-extrabold">Mins</span>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-4 border border-white/15 shadow-sm space-y-1">
            <span id="timer-secs" class="font-mono font-black text-2xl sm:text-4xl text-amber-400 animate-pulse">00</span>
            <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-300 font-extrabold">Secs</span>
        </div>
    </div>
</div>

<script>
(function() {
    function initCountdown() {
        const widget = document.getElementById('countdown-widget');
        if (!widget) return;

        const targetStr = widget.getAttribute('data-target') || '2026-09-01T10:00:00';
        const targetTime = new Date(targetStr).getTime();

        const daysEl = document.getElementById('timer-days');
        const hoursEl = document.getElementById('timer-hours');
        const minsEl = document.getElementById('timer-mins');
        const secsEl = document.getElementById('timer-secs');

        function update() {
            const now = new Date().getTime();
            const diff = targetTime - now;

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minsEl) minsEl.textContent = '00';
                if (secsEl) secsEl.textContent = '00';
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
