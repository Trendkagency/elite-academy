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
