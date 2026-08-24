<?php $__env->startSection('content'); ?>
<?php
    $siteJsonLd = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "WebSite",
                "@id" => url('/') . "/#website",
                "url" => url('/'),
                "name" => "Elite Academy LMS",
                "description" => "Leading K-12 Interactive Learning Management System & Online Tutoring Platform",
                "publisher" => [
                    "@id" => url('/') . "/#organization"
                ],
                "inLanguage" => app()->getLocale()
            ],
            [
                "@type" => "EducationalOrganization",
                "@id" => url('/') . "/#organization",
                "name" => "Elite Academy LMS",
                "url" => url('/'),
                "logo" => asset('images/logo.png'),
                "sameAs" => [
                    "https://facebook.com/eliteacademy",
                    "https://twitter.com/eliteacademy"
                ]
            ]
        ]
    ];
?>
<script type="application/ld+json">
<?php echo json_encode($siteJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>
    <?php
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
            'cta_section' => 'pages.home.cta-section',
        ];
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($layout) && count($layout) > 0): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $layout; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($sec['is_enabled'] ?? true) && isset($sectionsMap[$sec['key']])): ?>
                <?php echo $__env->make($sectionsMap[$sec['key']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <?php else: ?>
        
        <?php echo $__env->make('pages.home.hero-slider', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.stats-overlay', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.why-choose', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.about-preview', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.subjects-grid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.teachers-marquee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.testimonials', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.home.cta-section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\elite-academy\resources\views/pages/home.blade.php ENDPATH**/ ?>