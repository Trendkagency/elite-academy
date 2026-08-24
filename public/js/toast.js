/**
 * Elite Academy — Premium Toast Notification System
 * Drop-down top-center smooth animation matching main design aesthetic.
 */

(function () {
    'use strict';

    // Inject Toast Keyframes & CSS styles dynamically
    const style = document.createElement('style');
    style.id = 'toast-styles';
    style.textContent = `
        @keyframes toastDropIn {
            0% {
                opacity: 0;
                transform: translate(-50%, -40px) scale(0.92);
            }
            65% {
                opacity: 1;
                transform: translate(-50%, 6px) scale(1.02);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, 0) scale(1);
            }
        }

        @keyframes toastFadeOut {
            0% {
                opacity: 1;
                transform: translate(-50%, 0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -24px) scale(0.95);
            }
        }

        @keyframes toastProgress {
            0% { width: 100%; }
            100% { width: 0%; }
        }

        .toast-container {
            position: fixed;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            width: calc(100% - 2rem);
            max-width: 440px;
            pointer-events: none;
        }

        .toast-card {
            pointer-events: auto;
            position: relative;
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            padding: 1rem 1.125rem;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 1.25rem;
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.9);
            overflow: hidden;
            animation: toastDropIn 0.42s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.25s ease;
            font-family: inherit;
        }

        .toast-card.toast-exiting {
            animation: toastFadeOut 0.3s cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        /* Toast Themes */
        .toast-card.toast-success {
            border-color: rgba(16, 185, 129, 0.3);
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,244,0.95) 100%);
        }
        .toast-card.toast-error {
            border-color: rgba(244, 63, 94, 0.3);
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(254,242,242,0.95) 100%);
        }
        .toast-card.toast-warning {
            border-color: rgba(245, 158, 11, 0.3);
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(254,252,232,0.95) 100%);
        }
        .toast-card.toast-info {
            border-color: rgba(13, 148, 136, 0.3);
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(240,253,250,0.95) 100%);
        }

        .toast-icon-badge {
            width: 2.25rem;
            height: 2.25rem;
            min-width: 2.25rem;
            border-radius: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            font-weight: 800;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .toast-success .toast-icon-badge {
            background-color: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }
        .toast-error .toast-icon-badge {
            background-color: #FEF2F2;
            color: #E11D48;
            border: 1px solid #FECDD3;
        }
        .toast-warning .toast-icon-badge {
            background-color: #FEFCE8;
            color: #D97706;
            border: 1px solid #FDE68A;
        }
        .toast-info .toast-icon-badge {
            background-color: #CCFBF1;
            color: #0D9488;
            border: 1px solid #99F6E4;
        }

        .toast-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.875rem;
            color: #0F172A;
            line-height: 1.25;
        }

        .toast-message {
            font-size: 0.785rem;
            color: #475569;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .toast-close-btn {
            background: transparent;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            line-height: 1;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .toast-close-btn:hover {
            color: #334155;
            background-color: rgba(226, 232, 240, 0.6);
        }

        .toast-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            border-radius: 0 0 1.25rem 1.25rem;
        }
        .toast-success .toast-progress-bar { background-color: #10B981; }
        .toast-error .toast-progress-bar { background-color: #F43F5E; }
        .toast-warning .toast-progress-bar { background-color: #F59E0B; }
        .toast-info .toast-progress-bar { background-color: #0D9488; }
    `;
    document.head.appendChild(style);

    function getOrCreateContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            container.setAttribute('role', 'status');
            document.body.appendChild(container);
        }
        return container;
    }

    const ToastEngine = {
        show: function (options) {
            const {
                type = 'info',
                title = '',
                message = '',
                duration = 4000
            } = options;

            const container = getOrCreateContainer();
            const card = document.createElement('div');
            card.className = `toast-card toast-${type}`;
            card.setAttribute('role', 'alert');

            let defaultTitle = '';
            let iconSymbol = 'ℹ️';

            switch (type) {
                case 'success':
                    defaultTitle = document.documentElement.lang === 'ar' ? 'نجاح' : 'Success';
                    iconSymbol = '✓';
                    break;
                case 'error':
                    defaultTitle = document.documentElement.lang === 'ar' ? 'خطأ' : 'Error';
                    iconSymbol = '✕';
                    break;
                case 'warning':
                    defaultTitle = document.documentElement.lang === 'ar' ? 'تنبيه' : 'Warning';
                    iconSymbol = '!';
                    break;
                case 'info':
                default:
                    defaultTitle = document.documentElement.lang === 'ar' ? 'معلومة' : 'Notice';
                    iconSymbol = 'ℹ';
                    break;
            }

            const displayTitle = title || defaultTitle;

            let messageHtml = '';
            if (Array.isArray(message)) {
                messageHtml = '<ul class="list-disc pl-4 space-y-0.5">' +
                    message.map(m => `<li>${m}</li>`).join('') +
                    '</ul>';
            } else {
                messageHtml = message;
            }

            card.innerHTML = `
                <div class="toast-icon-badge">${iconSymbol}</div>
                <div class="toast-content">
                    <div class="toast-title">${displayTitle}</div>
                    <div class="toast-message">${messageHtml}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Close Toast">&times;</button>
                <div class="toast-progress-bar" style="animation: toastProgress ${duration}ms linear forwards;"></div>
            `;

            const closeBtn = card.querySelector('.toast-close-btn');
            let timer;

            const dismiss = () => {
                clearTimeout(timer);
                card.classList.add('toast-exiting');
                card.addEventListener('animationend', () => {
                    if (card.parentNode) {
                        card.parentNode.removeChild(card);
                    }
                });
            };

            closeBtn.addEventListener('click', dismiss);

            if (duration > 0) {
                timer = setTimeout(dismiss, duration);
            }

            container.appendChild(card);
            return card;
        },

        success: function (message, title, duration) {
            return this.show({ type: 'success', message, title, duration });
        },

        error: function (message, title, duration) {
            return this.show({ type: 'error', message, title, duration });
        },

        warning: function (message, title, duration) {
            return this.show({ type: 'warning', message, title, duration });
        },

        info: function (message, title, duration) {
            return this.show({ type: 'info', message, title, duration });
        }
    };

    window.Toast = ToastEngine;
})();
