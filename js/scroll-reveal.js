/**
 * Elite Academy — Premium Motion & Micro-Interactions Engine
 * Apple / Stripe / Framer / Linear / Awwwards inspired animations.
 * GPU-accelerated (opacity + transform only). 60 FPS target.
 * Animations fire once per element — never on scroll-back.
 */

(function () {
  'use strict';

  /* ──────────────────────────────────────────────
     CONFIG & CONSTANTS
  ────────────────────────────────────────────── */
  const EASING    = 'cubic-bezier(0.22, 1, 0.36, 1)';
  const DURATION  = 900;    // ms — default
  const STAGGER   = 100;    // ms — between card children
  const THRESHOLD = 0.15;   // 15% visible before triggering

  /* ──────────────────────────────────────────────
     1. INJECT BASE REVEAL & MOTION CSS
  ────────────────────────────────────────────── */
  const style = document.createElement('style');
  style.textContent = `
    /* Scroll Reveal — hidden initial states */
    .sr, .sr-h, .sr-img, .sr-btn, .sr-card {
      will-change: opacity, transform;
    }
    .sr        { opacity: 0; transform: translateY(60px); }
    .sr-h      { opacity: 0; transform: translateY(40px); }
    .sr-sub    { opacity: 0; transform: translateY(30px); }
    .sr-img    { opacity: 0; transform: scale(0.94) translateY(28px); }
    .sr-btn    { opacity: 0; transform: scale(0.95); }
    .sr-card   { opacity: 0; transform: translateY(48px); }
    .sr-stat   { opacity: 0; transform: translateY(32px) scale(0.95); }

    /* Revealed — final state */
    .sr.revealed, .sr-h.revealed, .sr-sub.revealed,
    .sr-img.revealed, .sr-btn.revealed, .sr-card.revealed, .sr-stat.revealed {
      opacity: 1 !important;
      transform: none !important;
    }

    /* Word Stagger Containers */
    .word-wrap {
      display: inline-block;
      overflow: hidden;
      vertical-align: top;
      margin-right: 0.25em;
    }
    .word-wrap .word {
      display: inline-block;
      opacity: 0;
      transform: translateY(100%);
      transition: opacity 700ms cubic-bezier(0.22, 1, 0.36, 1), transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
    }
    .sr-h.revealed .word-wrap .word {
      opacity: 1;
      transform: translateY(0);
    }
  `;
  document.head.appendChild(style);

  /* ──────────────────────────────────────────────
     2. DYNAMIC UI CONTROLS (Progress Bar, Back-To-Top)
  ────────────────────────────────────────────── */
  let scrollProgressEl = null;
  let backToTopEl = null;

  function initUIControls() {
    // Scroll Progress Bar
    if (!document.getElementById('scroll-progress')) {
      scrollProgressEl = document.createElement('div');
      scrollProgressEl.id = 'scroll-progress';
      document.body.prepend(scrollProgressEl);
    } else {
      scrollProgressEl = document.getElementById('scroll-progress');
    }

    // Back to Top Button
    if (!document.getElementById('back-to-top')) {
      backToTopEl = document.createElement('button');
      backToTopEl.id = 'back-to-top';
      backToTopEl.setAttribute('aria-label', 'Back to Top');
      backToTopEl.innerHTML = '↑';
      document.body.appendChild(backToTopEl);

      backToTopEl.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    } else {
      backToTopEl = document.getElementById('back-to-top');
    }
  }

  /* ──────────────────────────────────────────────
     3. APPLY TRANSITIONS & STAGGER
  ────────────────────────────────────────────── */
  function applyTransitions() {
    document.querySelectorAll('.sr, .sr-h, .sr-sub, .sr-img, .sr-btn, .sr-card, .sr-stat').forEach(el => {
      const dur = el.dataset.srDuration || DURATION;
      const delay = el.style.getPropertyValue('--sr-delay') || el.dataset.srDelay || '0';
      el.style.transition = `opacity ${dur}ms ${EASING} ${delay}ms, transform ${dur}ms ${EASING} ${delay}ms`;
    });
  }

  function staggerGroup(parent) {
    const cards = parent.querySelectorAll('.sr-card, .sr-stat');
    cards.forEach((card, i) => {
      const delay = i * STAGGER;
      card.style.transitionDelay = `${delay}ms`;
      card.dataset.srDelay = delay;
    });
  }

  /* ──────────────────────────────────────────────
     4. INTERSECTION OBSERVER — REVEAL
  ────────────────────────────────────────────── */
  const revealed = new WeakSet();

  function reveal(el) {
    if (revealed.has(el)) return;
    revealed.add(el);
    el.classList.add('revealed');
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      reveal(el);
      observer.unobserve(el);
    });
  }, {
    threshold: THRESHOLD,
    rootMargin: '0px 0px -40px 0px'
  });

  function observeAll() {
    document.querySelectorAll('.sr, .sr-h, .sr-sub, .sr-img, .sr-btn, .sr-card, .sr-stat').forEach(el => {
      observer.observe(el);
    });
  }

  /* ──────────────────────────────────────────────
     5. NUMBER COUNTER ANIMATION
  ────────────────────────────────────────────── */
  function animateCounter(el) {
    const raw    = el.dataset.count || el.textContent;
    const suffix = raw.replace(/[\d,\.]/g, '').trim();
    const target = parseFloat(raw.replace(/[^\d\.]/g, ''));
    if (isNaN(target)) return;

    const isFloat   = raw.includes('.');
    const decimals  = isFloat ? (raw.split('.')[1]?.replace(/\D/g, '').length || 1) : 0;
    const duration  = 1800;
    const startTime = performance.now();

    function easeOut(t) { return 1 - Math.pow(1 - t, 3); }

    function tick(now) {
      const elapsed  = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const value    = easeOut(progress) * target;
      const display  = isFloat
        ? value.toFixed(decimals)
        : Math.floor(value).toLocaleString();
      el.textContent = display + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      animateCounter(entry.target);
      counterObserver.unobserve(entry.target);
    });
  }, { threshold: 0.5 });

  function observeCounters() {
    document.querySelectorAll('[data-count]').forEach(el => {
      counterObserver.observe(el);
    });
  }

  /* ──────────────────────────────────────────────
     6. SCROLL ENGINE (Progress Bar, Navbar Shrink, Back-To-Top)
  ────────────────────────────────────────────── */
  const headerEl = document.querySelector('header');

  function handleScroll() {
    const scrollY = window.scrollY;
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;

    // Scroll Progress Bar Width
    if (scrollProgressEl && maxScroll > 0) {
      const progress = Math.min((scrollY / maxScroll) * 100, 100);
      scrollProgressEl.style.width = `${progress}%`;
    }

    // Navbar Shrink & Glass Transparency Effect
    if (headerEl) {
      if (scrollY > 30) {
        headerEl.classList.add('is-scrolled');
      } else {
        headerEl.classList.remove('is-scrolled');
      }
    }

    // Back to Top Button Visibility
    if (backToTopEl) {
      if (scrollY > 500) {
        backToTopEl.classList.add('is-visible');
      } else {
        backToTopEl.classList.remove('is-visible');
      }
    }
  }

  /* ──────────────────────────────────────────────
     7. HERO AUTOPLAY SLIDER & MOUSE PARALLAX
  ────────────────────────────────────────────── */
  let heroAutoplayTimer = null;

  function initHeroAutoplay() {
    const slides = ['slide-1', 'slide-2', 'slide-3', 'slide-4'];
    let currentIdx = 0;

    function advanceSlide() {
      currentIdx = (currentIdx + 1) % slides.length;
      const radio = document.getElementById(slides[currentIdx]);
      if (radio) radio.checked = true;
    }

    // Advance slide every 6 seconds
    heroAutoplayTimer = setInterval(advanceSlide, 6000);

    // Pause autoplay on user interaction
    const heroSection = document.querySelector('section.relative.overflow-hidden') || document.querySelector('.hero-section');
    if (heroSection) {
      heroSection.addEventListener('pointerdown', () => {
        if (heroAutoplayTimer) clearInterval(heroAutoplayTimer);
      }, { passive: true });
    }

    initHeroSwipe();
    initHeroMouseParallax();
  }

  function initHeroMouseParallax() {
    // Only run desktop mouse parallax on non-touch devices
    if (window.matchMedia('(pointer: coarse)').matches) return;

    const heroSection = document.querySelector('section.relative.overflow-hidden');
    if (!heroSection) return;

    const floatingCards = heroSection.querySelectorAll('.glass-card, .animate-float, .animate-float-reverse');

    heroSection.addEventListener('mousemove', (e) => {
      const rect = heroSection.getBoundingClientRect();
      const mouseX = (e.clientX - rect.left) / rect.width - 0.5;
      const mouseY = (e.clientY - rect.top) / rect.height - 0.5;

      floatingCards.forEach((card, i) => {
        const depth = (i + 1) * 12;
        const moveX = mouseX * depth;
        const moveY = mouseY * depth;
        card.style.transform = `translate3d(${moveX}px, ${moveY}px, 0) rotate(${mouseX * 3}deg)`;
      });
    }, { passive: true });
  }

  function initHeroSwipe() {
    const heroSection = document.querySelector('section.relative.overflow-hidden') || document.querySelector('.hero-section');
    if (!heroSection) return;

    const slides = ['slide-1', 'slide-2', 'slide-3', 'slide-4'];
    let touchStartX = 0;

    heroSection.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    heroSection.addEventListener('touchend', (e) => {
      const touchEndX = e.changedTouches[0].screenX;
      const diff = touchEndX - touchStartX;
      if (Math.abs(diff) < 35) return;

      let currentIndex = slides.findIndex(id => {
        const radio = document.getElementById(id);
        return radio && radio.checked;
      });

      if (currentIndex === -1) currentIndex = 0;

      const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
      if ((diff < 0 && !isRtl) || (diff > 0 && isRtl)) {
        currentIndex = (currentIndex + 1) % slides.length;
      } else {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      }

      const nextRadio = document.getElementById(slides[currentIndex]);
      if (nextRadio) {
        nextRadio.checked = true;
      }
    }, { passive: true });
  }

  /* ──────────────────────────────────────────────
     8. PAGE FADE OUT / IN TRANSITIONS
  ────────────────────────────────────────────── */
  function initPageTransitions() {
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href]');
      if (!link) return;

      const href = link.getAttribute('href');
      // Only transition internal html page links
      if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:') || link.target === '_blank') return;

      // Prevent default and fade out before navigating
      e.preventDefault();
      document.body.classList.add('page-exit');

      setTimeout(() => {
        window.location.href = href;
      }, 250);
    });
  }

  /* ──────────────────────────────────────────────
     9. AUTO-LABEL ELEMENTS FOR MOTION
  ────────────────────────────────────────────── */
  function labelElements() {
    const rules = [
      { sel: 'section span.inline-block.font-mono.uppercase', cls: 'sr-h' },
      { sel: 'section h2', cls: 'sr-h' },
      { sel: 'section > div > div > p.text-slate-600, section > div > div > p.text-slate-500', cls: 'sr-sub' },
      { sel: '.card-lift, .teacher-card-glow, .glass-card:not(header *)', cls: 'sr-card' },
      { sel: 'section img:not(.hero-img)', cls: 'sr-img' },
      { sel: 'section a.btn-lift', cls: 'sr-btn' },
    ];

    rules.forEach(({ sel, cls }) => {
      document.querySelectorAll(sel).forEach(el => {
        if ([...el.classList].some(c => c.startsWith('sr')) ||
            el.closest('.hero-slide, [class*="z-10"][class*="hidden"]')) return;
        el.classList.add(cls);
      });
    });

    document.querySelectorAll('section').forEach(section => {
      const cards = [...section.querySelectorAll('.sr-card')];
      cards.forEach((card, i) => {
        card.style.transitionDelay = `${i * STAGGER}ms`;
      });
      const stats = [...section.querySelectorAll('.sr-stat')];
      stats.forEach((stat, i) => {
        stat.style.transitionDelay = `${i * STAGGER}ms`;
      });
    });
  }

  /* ──────────────────────────────────────────────
     BOOT MOTION ENGINE
  ────────────────────────────────────────────── */
  function boot() {
    initUIControls();
    labelElements();
    applyTransitions();
    initHeroAutoplay();
    initPageTransitions();

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        observeAll();
        observeCounters();
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

})();
