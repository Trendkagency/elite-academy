/**
 * Elite Academy — Continuous Bidirectional Scroll Motion Engine
 * Apple / Framer / Awwwards inspired scroll-triggered animations.
 * Smoothly animates elements on Scroll DOWN and Scroll UP.
 * GPU-accelerated (opacity + transform only). 60 FPS target.
 */

(function () {
  'use strict';

  const EASING    = 'cubic-bezier(0.16, 1, 0.3, 1)';
  const DURATION  = 400;    // ms — ultra-smooth liquid motion
  const STAGGER   = 50;     // ms — stagger delay
  const THRESHOLD = 0.12;   // Triggers when 12% of element is in view

  /* ──────────────────────────────────────────────
     1. INJECT BASE REVEAL & MOTION CSS
  ────────────────────────────────────────────── */
  const style = document.createElement('style');
  style.textContent = `
    .sr-pending {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 400ms cubic-bezier(0.16, 1, 0.3, 1), transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sr-pending.revealed {
      opacity: 1 !important;
      transform: translateY(0) !important;
    }
  `;
  document.head.appendChild(style);

  /* ──────────────────────────────────────────────
     2. DYNAMIC UI CONTROLS (Progress Bar, Back-To-Top)
  ────────────────────────────────────────────── */
  let scrollProgressEl = null;
  let backToTopEl = null;

  function initUIControls() {
    if (!document.getElementById('scroll-progress')) {
      scrollProgressEl = document.createElement('div');
      scrollProgressEl.id = 'scroll-progress';
      document.body.prepend(scrollProgressEl);
    } else {
      scrollProgressEl = document.getElementById('scroll-progress');
    }

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
     3. HIGH-PERFORMANCE ONE-SHOT INTERSECTION OBSERVER
  ────────────────────────────────────────────── */
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: THRESHOLD,
    rootMargin: '0px 0px 80px 0px'
  });

  function observeAll() {
    const vh = window.innerHeight;
    document.querySelectorAll('.sr, .sr-h, .sr-sub, .sr-img, .sr-btn, .sr-card, .sr-stat').forEach(el => {
      const rect = el.getBoundingClientRect();
      // If above-the-fold, keep immediately 100% visible
      if (rect.top <= vh * 1.1) {
        el.classList.add('revealed');
      } else {
        el.classList.add('sr-pending');
        observer.observe(el);
      }
    });
  }

  /* ──────────────────────────────────────────────
     4. NUMBER COUNTER ANIMATION
  ────────────────────────────────────────────── */
  function animateCounter(el) {
    const raw    = el.dataset.count || el.textContent;
    const suffix = raw.replace(/[\d,\.]/g, '').trim();
    const target = parseFloat(raw.replace(/[^\d\.]/g, ''));
    if (isNaN(target)) return;

    const isFloat   = raw.includes('.');
    const decimals  = isFloat ? (raw.split('.')[1]?.replace(/\D/g, '').length || 1) : 0;
    const duration  = 1600;
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
      if (entry.isIntersecting) {
        animateCounter(entry.target);
      }
    });
  }, { threshold: 0.2 });

  function observeCounters() {
    document.querySelectorAll('[data-count]').forEach(el => {
      counterObserver.observe(el);
    });
  }

  /* ──────────────────────────────────────────────
     5. SCROLL ENGINE
  ────────────────────────────────────────────── */
  const headerEl = document.querySelector('header');

  function handleScroll() {
    const scrollY = window.scrollY;
    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;

    if (scrollProgressEl && maxScroll > 0) {
      const progress = Math.min((scrollY / maxScroll) * 100, 100);
      scrollProgressEl.style.width = `${progress}%`;
    }

    if (headerEl) {
      if (scrollY > 30) {
        headerEl.classList.add('is-scrolled');
      } else {
        headerEl.classList.remove('is-scrolled');
      }
    }

    if (backToTopEl) {
      if (scrollY > 400) {
        backToTopEl.classList.add('is-visible');
      } else {
        backToTopEl.classList.remove('is-visible');
      }
    }
  }

  /* ──────────────────────────────────────────────
     6. AUTO-LABEL ELEMENTS FOR MOTION
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
    });
  }

  /* ──────────────────────────────────────────────
     BOOT MOTION ENGINE
  ────────────────────────────────────────────── */
  function boot() {
    initUIControls();
    labelElements();
    observeAll();
    observeCounters();

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

})();
