@extends('layouts.app')

@section('content')
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
