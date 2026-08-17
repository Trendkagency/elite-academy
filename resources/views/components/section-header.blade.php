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
