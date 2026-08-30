<style>
    /* Fast Touch & Click Response without forced compositing layer explosion */
    html {
        touch-action: manipulation;
    }
    button, a, summary, input, select, textarea {
        -webkit-tap-highlight-color: transparent;
    }

    /* Fallback: Ensure elements stay visible */
    .sr.revealed, .sr-h.revealed, .sr-img.revealed, .sr-btn.revealed, .sr-card.revealed, .sr-sub.revealed, .sr-stat.revealed {
        opacity: 1 !important;
        transform: none !important;
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
<?php /**PATH C:\laragon\www\elite-academy\resources\views/partials/inp-optimizer.blade.php ENDPATH**/ ?>