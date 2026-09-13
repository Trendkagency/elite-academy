@php
    use App\Models\SiteSetting;

    $primaryColor    = SiteSetting::get('theme_primary_color', '#0d9488');
    $secondaryColor  = SiteSetting::get('theme_secondary_color', '#6366f1');
    $accentColor     = SiteSetting::get('theme_accent_color', '#f59e0b');

    $btnRadius = match(SiteSetting::get('theme_btn_radius', 'full')) {
        'none' => '0px',
        'sm'   => '4px',
        'md'   => '8px',
        'lg'   => '12px',
        'full' => '9999px',
        default => '9999px'
    };

    $cardRadius = match(SiteSetting::get('theme_card_radius', '2xl')) {
        'lg'  => '8px',
        'xl'  => '12px',
        '2xl' => '16px',
        '3xl' => '24px',
        default => '16px'
    };

    $badgeRadius = match(SiteSetting::get('theme_badge_radius', 'full')) {
        'sm'   => '4px',
        'md'   => '8px',
        'full' => '9999px',
        default => '9999px'
    };

    $fontEn = SiteSetting::get('theme_font_family_en', 'Cairo');
    $fontAr = SiteSetting::get('theme_font_family_ar', 'Cairo');
@endphp

{{-- Google Fonts for Selected Typography --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
@if($fontEn !== 'Cairo' || $fontAr !== 'Cairo')
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontEn) }}:wght@400;600;700;800&family={{ urlencode($fontAr) }}:wght@400;600;700;800&display=swap" rel="stylesheet">
@endif

<style id="elite-global-theme-variables">
:root {
    --color-primary: {{ $primaryColor }};
    --color-secondary: {{ $secondaryColor }};
    --color-accent: {{ $accentColor }};
    
    --theme-btn-radius: {{ $btnRadius }};
    --theme-card-radius: {{ $cardRadius }};
    --theme-badge-radius: {{ $badgeRadius }};
    
    --font-family-english: "{{ $fontEn }}", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-family-arabic: "{{ $fontAr }}", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* ── Global Theme Overrides ─────────────────────────────────────── */
.btn-lift, 
.btn-primary,
button[class*="rounded-full"],
a[class*="rounded-full"] {
    border-radius: var(--theme-btn-radius) !important;
}

.glass-card,
.card-lift,
.theme-card,
[class*="rounded-2xl"][class*="bg-"],
[class*="rounded-3xl"][class*="bg-"] {
    border-radius: var(--theme-card-radius) !important;
}

.hero-anim-badge,
.theme-badge,
[class*="rounded-full"][class*="border"] {
    border-radius: var(--theme-badge-radius) !important;
}
</style>
