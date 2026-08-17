@extends('layouts.app')

@section('content')
    {{-- Section 01: Hero Slider (4 Fullscreen Slides) --}}
    @include('pages.home.hero-slider')

    {{-- Section 01.1: Overlapping Glass Statistics Bar --}}
    @include('pages.home.stats-overlay')

    {{-- Section 01.5: Why Choose Elite Section --}}
    @include('pages.home.why-choose')

    {{-- Section 02: About Elite Academy Preview --}}
    @include('pages.home.about-preview')

    {{-- Section 03: Subjects Showcase Grid --}}
    @include('pages.home.subjects-grid')

    {{-- Section 04: Featured Faculty Mentors --}}
    @include('pages.home.teachers-marquee')

    {{-- Section 05: Student & Parent Testimonials --}}
    @include('pages.home.testimonials')

    {{-- Section 06: Call To Action & Stats Strip --}}
    @include('pages.home.cta-section')
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
