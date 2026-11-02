<?php
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
?>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fontEn !== 'Cairo' || $fontAr !== 'Cairo'): ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo e(urlencode($fontEn)); ?>:wght@400;600;700;800&family=<?php echo e(urlencode($fontAr)); ?>:wght@400;600;700;800&display=swap" rel="stylesheet">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<style id="elite-global-theme-variables">
:root {
    --color-primary: <?php echo e($primaryColor); ?>;
    --color-secondary: <?php echo e($secondaryColor); ?>;
    --color-accent: <?php echo e($accentColor); ?>;
    
    --theme-btn-radius: <?php echo e($btnRadius); ?>;
    --theme-card-radius: <?php echo e($cardRadius); ?>;
    --theme-badge-radius: <?php echo e($badgeRadius); ?>;
    
    --font-family-english: "<?php echo e($fontEn); ?>", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-family-arabic: "<?php echo e($fontAr); ?>", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
<?php /**PATH C:\laragon\www\elite-academy\resources\views/partials/theme-styles.blade.php ENDPATH**/ ?>