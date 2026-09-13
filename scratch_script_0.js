
        (function() {
            try {
                localStorage.removeItem('theme');
                document.documentElement.classList.remove('dark');
            } catch (e) {}
        })();

        // Universal Modal Controller (Early Init in Head)
        window.openModal = function (id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) {
                console.warn('[Modal] Element not found:', id);
                return;
            }

            modal.classList.add('elite-modal');
            const dialog = modal.querySelector('.elite-modal-dialog') || modal.firstElementChild;
            if (dialog && !dialog.classList.contains('elite-modal-dialog')) {
                dialog.classList.add('elite-modal-dialog');
            }

            modal.classList.remove('hidden');
            modal.style.setProperty('display', 'flex', 'important');
            modal.style.setProperty('opacity', '1', 'important');
            modal.style.setProperty('pointer-events', 'auto', 'important');
            modal.style.setProperty('visibility', 'visible', 'important');
            modal.classList.add('active');

            if (dialog) {
                dialog.style.setProperty('opacity', '1', 'important');
                dialog.style.setProperty('transform', 'scale(1) translateY(0)', 'important');
            }

            document.body.classList.add('overflow-hidden');

            const focusTarget = modal.querySelector('[autofocus], input:not([type="hidden"]), select, textarea, button:not([aria-label="Close"])');
            if (focusTarget) {
                setTimeout(() => focusTarget.focus(), 60);
            }
        };

        window.closeModal = function (id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) return;

            modal.classList.remove('active');
            modal.style.setProperty('opacity', '0', 'important');
            modal.style.setProperty('pointer-events', 'none', 'important');

            const dialog = modal.querySelector('.elite-modal-dialog') || modal.firstElementChild;
            if (dialog) {
                dialog.style.setProperty('opacity', '0', 'important');
                dialog.style.setProperty('transform', 'scale(0.95) translateY(10px)', 'important');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.setProperty('display', 'none', 'important');
                const remaining = document.querySelectorAll('.elite-modal.active:not(.hidden)');
                if (remaining.length === 0) {
                    document.body.classList.remove('overflow-hidden');
                }
            }, 200);
        };
    