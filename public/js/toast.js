(function () {
    "use strict";

    // Inject Toast CSS styles dynamically if not already injected
    function injectStyles() {
        if (document.getElementById("elite-toast-styles")) return;
        const style = document.createElement("style");
        style.id = "elite-toast-styles";
        style.textContent = `
            #toast-container {
                position: fixed;
                top: 1.25rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 999999;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.625rem;
                pointer-events: none;
                width: calc(100% - 2rem);
                max-width: 440px;
            }

            .toast-card {
                pointer-events: auto;
                position: relative;
                overflow: hidden;
                width: 100%;
                display: flex;
                align-items: flex-start;
                gap: 0.875rem;
                padding: 0.875rem 1rem;
                border-radius: 1rem;
                background: #ffffff;
                color: #0f172a;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
                border: 1px solid #e2e8f0;
                backdrop-filter: blur(12px);
                animation: toastSlideDown 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                font-family: inherit;
                box-sizing: border-box;
            }

            @keyframes toastSlideDown {
                from {
                    opacity: 0;
                    transform: translateY(-16px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .toast-card.toast-exiting {
                animation: toastSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
            }

            @keyframes toastSlideUp {
                from {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                to {
                    opacity: 0;
                    transform: translateY(-12px) scale(0.95);
                }
            }

            .toast-card.toast-danger,
            .toast-card.toast-error {
                background: #fff5f5;
                border-color: #fecdd3;
                color: #9f1239;
            }

            .toast-card.toast-success {
                background: #f0fdf4;
                border-color: #bbf7d0;
                color: #14532d;
            }

            .toast-card.toast-warning {
                background: #fffbeb;
                border-color: #fde68a;
                color: #78350f;
            }

            .toast-card.toast-info {
                background: #f0fdfa;
                border-color: #99f6e4;
                color: #115e59;
            }

            .toast-icon-badge {
                flex-shrink: 0;
                width: 2rem;
                height: 2rem;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.95rem;
                font-weight: 800;
            }

            .toast-danger .toast-icon-badge,
            .toast-error .toast-icon-badge {
                background: #fee2e2;
                color: #e11d48;
                border: 1px solid #fca5a5;
            }

            .toast-success .toast-icon-badge {
                background: #dcfce7;
                color: #16a34a;
                border: 1px solid #86efac;
            }

            .toast-warning .toast-icon-badge {
                background: #fef3c7;
                color: #d97706;
                border: 1px solid #fcd34d;
            }

            .toast-info .toast-icon-badge {
                background: #ccfbf1;
                color: #0d9488;
                border: 1px solid #5eead4;
            }

            .toast-content {
                flex: 1;
                min-width: 0;
                text-align: start;
            }

            .toast-title {
                font-size: 0.85rem;
                font-weight: 800;
                line-height: 1.25;
                margin-bottom: 0.2rem;
            }

            .toast-message {
                font-size: 0.78rem;
                line-height: 1.4;
                opacity: 0.92;
                word-break: break-word;
            }

            .toast-close-btn {
                flex-shrink: 0;
                background: transparent;
                border: none;
                cursor: pointer;
                font-size: 1.2rem;
                line-height: 1;
                color: inherit;
                opacity: 0.5;
                padding: 0.2rem;
                border-radius: 0.375rem;
                transition: opacity 0.15s ease, transform 0.15s ease;
            }

            .toast-close-btn:hover {
                opacity: 1;
                transform: scale(1.1);
            }

            .toast-progress-bar {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                opacity: 0.65;
            }

            .toast-danger .toast-progress-bar,
            .toast-error .toast-progress-bar {
                background: #e11d48;
            }

            .toast-success .toast-progress-bar {
                background: #16a34a;
            }

            .toast-warning .toast-progress-bar {
                background: #d97706;
            }

            .toast-info .toast-progress-bar {
                background: #0d9488;
            }

            @keyframes toastProgress {
                from { width: 100%; }
                to { width: 0%; }
            }
        `;
        document.head.appendChild(style);
    }

    let container = null;
    function getContainer() {
        injectStyles();
        if (!container) {
            container = document.getElementById("toast-container");
            if (!container) {
                container = document.createElement("div");
                container.id = "toast-container";
                container.setAttribute("aria-live", "polite");
                container.setAttribute("aria-atomic", "true");
                container.setAttribute("role", "status");
                document.body.appendChild(container);
            }
        }
        return container;
    }

    const Toast = {
        show: function (options) {
            let { type = "info", title = "", message = "", duration = 4500 } = options;

            // Map aliases
            if (type === "danger") type = "error";

            const parent = getContainer();
            if (!parent) return null;

            const card = document.createElement("div");
            card.className = `toast-card toast-${type}`;
            card.setAttribute("role", "alert");

            const isAr = document.documentElement.lang === "ar" || document.dir === "rtl";

            let defaultTitle = title;
            if (!defaultTitle) {
                if (isAr) {
                    defaultTitle = {
                        success: "تم بنجاح",
                        error: "فشل / تنبيه خطأ",
                        warning: "تنبيه هام",
                        info: "معلومة"
                    }[type] || "إشعار";
                } else {
                    defaultTitle = {
                        success: "Success",
                        error: "Failed / Error",
                        warning: "Warning",
                        info: "Notice"
                    }[type] || "Notice";
                }
            }

            const iconMap = {
                success: "✓",
                error: "✕",
                warning: "!",
                info: "ℹ"
            };
            const icon = iconMap[type] || "ℹ";

            let msgHtml = message;
            if (Array.isArray(message)) {
                msgHtml = `<ul style="margin:0; padding-inline-start: 1rem; list-style-type: disc;">${message.map(m => `<li>${m}</li>`).join("")}</ul>`;
            }

            card.innerHTML = `
                <div class="toast-icon-badge">${icon}</div>
                <div class="toast-content">
                    <div class="toast-title">${defaultTitle}</div>
                    <div class="toast-message">${msgHtml}</div>
                </div>
                <button type="button" class="toast-close-btn" aria-label="Close">&times;</button>
                <div class="toast-progress-bar" style="animation: toastProgress ${duration}ms linear forwards;"></div>
            `;

            const closeBtn = card.querySelector(".toast-close-btn");
            let timerId = null;

            const dismiss = () => {
                if (timerId) clearTimeout(timerId);
                card.classList.add("toast-exiting");
                card.addEventListener("animationend", () => {
                    if (card.parentNode) {
                        card.parentNode.removeChild(card);
                    }
                }, { once: true });
            };

            if (closeBtn) {
                closeBtn.addEventListener("click", dismiss, { passive: true });
            }

            if (duration > 0) {
                timerId = setTimeout(dismiss, duration);
            }

            // Insert at top of container for latest on top
            if (parent.firstChild) {
                parent.insertBefore(card, parent.firstChild);
            } else {
                parent.appendChild(card);
            }

            return card;
        },

        success: function (msg, title, duration) {
            return this.show({ type: "success", message: msg, title: title, duration: duration });
        },

        error: function (msg, title, duration) {
            return this.show({ type: "error", message: msg, title: title, duration: duration });
        },

        danger: function (msg, title, duration) {
            return this.show({ type: "error", message: msg, title: title, duration: duration });
        },

        warning: function (msg, title, duration) {
            return this.show({ type: "warning", message: msg, title: title, duration: duration });
        },

        info: function (msg, title, duration) {
            return this.show({ type: "info", message: msg, title: title, duration: duration });
        }
    };

    window.Toast = Toast;
})();
