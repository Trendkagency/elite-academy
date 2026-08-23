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
