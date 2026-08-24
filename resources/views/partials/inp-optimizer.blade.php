{{-- Elite Academy — High-Performance Instant Motion & INP Engine --}}
<style>
    /* GPU Hardware Acceleration for Sub-16ms Response Times */
    button, a, summary, input, select, textarea, .btn-lift, .card-lift, .subject-chip {
        will-change: transform, opacity;
        transform: translateZ(0);
        -webkit-tap-highlight-color: transparent;
    }

    /* Fast Touch & Click Response */
    html {
        touch-action: manipulation;
    }

    /* Emergency Fallback: If animation JS is delayed, render visible after 200ms */
    @keyframes instantRevealFallback {
        to {
            opacity: 1 !important;
            transform: none !important;
        }
    }

    .sr, .sr-h, .sr-img, .sr-btn, .sr-card, .sr-sub, .sr-stat {
        animation: instantRevealFallback 1ms forwards 250ms;
    }
</style>

<script>
(function() {
    'use strict';

    // Non-blocking Event Scheduling for Sub-35ms INP
    const scheduleINPTask = (task) => {
        if ('requestAnimationFrame' in window) {
            requestAnimationFrame(() => {
                requestAnimationFrame(task);
            });
        } else {
            setTimeout(task, 0);
        }
    };

    // Instant Above-The-Fold Viewport Pre-Reveal Guard
    const preRevealViewportElements = () => {
        const elements = document.querySelectorAll('.sr, .sr-h, .sr-img, .sr-btn, .sr-card, .sr-sub, .sr-stat');
        const viewHeight = window.innerHeight * 1.25;

        elements.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top <= viewHeight) {
                el.classList.add('revealed');
            }
        });
    };

    // Attach Passive & Non-blocking Listeners
    document.addEventListener('DOMContentLoaded', function() {
        preRevealViewportElements();

        const interactiveElements = document.querySelectorAll('button, a, summary, input, select, .subject-chip');

        interactiveElements.forEach(el => {
            el.addEventListener('pointerdown', function() {
                this.style.opacity = '0.92';
                scheduleINPTask(() => {
                    this.style.opacity = '';
                });
            }, { passive: true });
        });
    });

    window.addEventListener('load', preRevealViewportElements, { passive: true });
})();
</script>
