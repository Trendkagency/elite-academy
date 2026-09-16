<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="notranslate scroll-smooth h-full" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="google" content="notranslate">
    <meta name="theme-color" content="#0D9488">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    <link rel="shortcut icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">

    {{-- Defensive protection against buggy browser extensions calling removeChild on mismatched parents --}}
    <script>
        (function() {
            var origRemoveChild = Node.prototype.removeChild;
            Node.prototype.removeChild = function(child) {
                if (!child || child.parentNode !== this) {
                    if (child && child.parentNode) {
                        return child.parentNode.removeChild(child);
                    }
                    return child;
                }
                return origRemoveChild.call(this, child);
            };
        })();
    </script>

    {{-- Web App Manifest & Mobile PWA / iOS Web Push Support --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ @filemtime(public_path('manifest.json')) ?: '2' }}">
    <meta name="mobile-web-app-capable" content="yes">
    @if(preg_match('/iPhone|iPad|iPod|Macintosh.*AppleWebKit/i', request()->userAgent() ?? ''))
    <meta name="apple-mobile-web-app-capable" content="yes">
    @endif
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Elite Academy">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <title>{{ $pageTitle ?? 'Elite Academy Portal' }}</title>

    {{-- Enforce Pure Light Mode & Universal Modal Controller --}}
    <script>
        // ── Theme Bootstrap: read stored preference BEFORE body paints to avoid FOUC ──
        (function () {
            try {
                var stored = localStorage.getItem('elite_theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();

        window.EliteTheme = {
            get: function () {
                return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            },
            set: function (theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    try { localStorage.setItem('elite_theme', 'dark'); } catch (e) {}
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    try { localStorage.setItem('elite_theme', 'light'); } catch (e) {}
                }
                window.dispatchEvent(new CustomEvent('elite-theme-changed', { detail: { theme: theme } }));
            },
            toggle: function () {
                this.set(this.get() === 'dark' ? 'light' : 'dark');
            }
        };

        window.togglePortalTheme = function() {
            window.EliteTheme.toggle();
        };

        // Universal Modal Controller (Early Init in Head)
        window.openModal = function (id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) {
                console.warn('[Modal] Element not found:', id);
                return;
            }

            // Move modal directly to document.body so it is never trapped inside .portal-main-canvas or below the sidebar
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
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

            document.documentElement.classList.add('overflow-hidden');
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
                    document.documentElement.classList.remove('overflow-hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            }, 200);
        };
    </script>

    {{-- Google Fonts: Cairo (AR) & Plus Jakarta Sans / Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --font-family-english: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-arabic: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-sans: var(--font-family-arabic);
            --font-heading: var(--font-family-arabic);
            --portal-sidebar-w: 280px;

            /* Light mode tokens */
            --surface-bg:     #FAFAF9;
            --surface-card:   #FFFFFF;
            --surface-border: #E2E8F0;
            --surface-subtle: #F8FAFC;
            --text-primary:   #0F172A;
            --text-secondary: #475569;
            --text-muted:     #94A3B8;
        }
        html[lang="en"], [dir="ltr"] {
            --font-sans: var(--font-family-english);
            --font-heading: var(--font-family-english);
        }
        html.dark {
            --surface-bg:     #0B0F19;
            --surface-card:   #0F172A;
            --surface-border: #1E293B;
            --surface-subtle: #162032;
            --text-primary:   #F8FAFC;
            --text-secondary: #CBD5E1;
            --text-muted:     #94A3B8;
        }
        html, body, button, input, select, textarea, table, .font-sans, .font-heading {
            font-family: var(--font-sans) !important;
        }
        html, body {
            background-color: var(--surface-bg);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
        }

        /* ── Mobile input font-size fix (prevents iOS Safari auto-zoom) ── */
        .input-mobile {
            width: 100%;
            background: var(--surface-subtle);
            border: 1.5px solid var(--surface-border);
            border-radius: 0.75rem;
            padding: 0.625rem 0.875rem;
            font-size: 1rem; /* 16px on mobile */
            font-family: var(--font-sans);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }
        @media (min-width: 640px) { .input-mobile { font-size: 0.8125rem; } }
        .input-mobile:focus {
            border-color: #0D9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.15);
            background: var(--surface-card);
        }
        html.dark .input-mobile {
            background-color: #1E293B;
            border-color: #334155;
            color: #F8FAFC;
        }
        html.dark .input-mobile:focus {
            border-color: #14B8A6;
            background: #1E293B;
        }

        .btn-lift {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-lift:hover {
            transform: translateY(-1.5px);
        }
        .btn-lift:active {
            transform: translateY(0.5px) scale(0.98);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: transform 0.25s ease, background-color 0.25s ease, border-color 0.25s ease;
        }

        /* ─── Ultra-Responsive Mobile-First Table System ─── */
        .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            position: relative;
            scrollbar-width: thin;
        }
        .table-responsive table {
            width: 100% !important;
            min-width: 860px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            text-align: start !important;
        }
        .table-responsive th {
            white-space: nowrap !important;
            font-family: 'Cairo', sans-serif !important;
            font-weight: 800 !important;
            letter-spacing: 0.02em !important;
            vertical-align: middle !important;
        }
        .table-responsive td {
            font-family: 'Cairo', sans-serif !important;
            vertical-align: middle !important;
            white-space: nowrap;
        }
        .table-responsive td.allow-wrap,
        .table-responsive th.allow-wrap {
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            line-height: 1.55 !important;
        }
        .table-responsive .col-title {
            min-width: 260px !important;
            max-width: 380px !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.55 !important;
        }
        .table-responsive .col-course {
            min-width: 200px !important;
            max-width: 300px !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.45 !important;
        }
        .table-responsive .col-date {
            min-width: 140px !important;
            white-space: nowrap !important;
        }
        .table-responsive .col-badge {
            min-width: 110px !important;
            white-space: nowrap !important;
        }
        .table-responsive .col-action {
            min-width: 140px !important;
            white-space: nowrap !important;
        }
        .table-responsive::-webkit-scrollbar {
            height: 7px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 9999px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: rgba(13, 148, 136, 0.4);
            border-radius: 9999px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: rgba(13, 148, 136, 0.7);
        }
        html.dark .table-responsive::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        /* ─── Ultra-Smooth Portal Table Hover System (Prevents White Flashes in Light & Dark Modes) ─── */
        .portal-main-canvas table tbody tr {
            transition: background-color 0.18s cubic-bezier(0.16, 1, 0.3, 1), color 0.18s ease;
        }

        /* Light mode table row hover: gentle, elegant slate tint */
        html:not(.dark) .portal-main-canvas table tbody tr:hover,
        html:not(.dark) .portal-main-canvas table tbody tr.hover\:bg-slate-50\/80:hover,
        html:not(.dark) .portal-main-canvas table tbody tr.hover\:bg-slate-50:hover,
        html:not(.dark) .portal-main-canvas table tbody tr.hover\:bg-slate-100:hover {
            background-color: #F1F5F9 !important; /* Soft slate-100, no harsh white flash */
        }

        /* Dark mode table row hover: deep slate-800, perfectly dark */
        html.dark .portal-main-canvas table tbody tr:hover,
        html.dark .portal-main-canvas table tbody tr.hover\:bg-slate-50\/80:hover,
        html.dark .portal-main-canvas table tbody tr.hover\:bg-slate-50:hover,
        html.dark .portal-main-canvas table tbody tr.hover\:bg-slate-100:hover,
        html.dark .portal-main-canvas table tbody tr.dark\:hover\:bg-slate-800\/50:hover,
        html.dark .portal-main-canvas table tbody tr.dark\:hover\:bg-slate-800:hover {
            background-color: #1E293B !important; /* Slate 800 */
        }

        /* Prevent white hover flashes across all dark mode interactive table elements */
        html.dark [class*="hover:bg-slate-50"]:hover,
        html.dark [class*="hover:bg-white"]:hover,
        html.dark [class*="hover:bg-slate-100"]:hover,
        html.dark [class*="hover:bg-gray-50"]:hover,
        html.dark [class*="hover:bg-gray-100"]:hover {
            background-color: #1E293B !important;
        }

        /* Table header sortable hover */
        .sortable-header {
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        html:not(.dark) .sortable-header:hover {
            color: #0D9488 !important;
            background-color: #F8FAFC !important;
        }
        html.dark .sortable-header:hover {
            color: #2DD4BF !important;
            background-color: #162032 !important;
        }

        /* Active Highlighted Rows in Dark Mode (Live sessions, In Progress) - Soft Eye-Friendly Ambient Tint */
        html.dark .portal-main-canvas table tbody tr.bg-emerald-50\/40,
        html.dark .portal-main-canvas table tbody tr.is-live-row {
            background-color: rgba(16, 185, 129, 0.06) !important;
        }
        html.dark .portal-main-canvas table tbody tr.bg-emerald-50\/40:hover,
        html.dark .portal-main-canvas table tbody tr.is-live-row:hover {
            background-color: rgba(16, 185, 129, 0.12) !important;
        }
        html.dark .portal-main-canvas table tbody tr.bg-amber-50\/40 {
            background-color: rgba(245, 158, 11, 0.06) !important;
        }
        html.dark .portal-main-canvas table tbody tr.bg-amber-50\/40:hover {
            background-color: rgba(245, 158, 11, 0.12) !important;
        }

        /* ── Complete Viewport Scroll-Locking when Modal is Active ── */
        html.overflow-hidden,
        body.overflow-hidden {
            overflow: hidden !important;
            height: 100% !important;
            overscroll-behavior: none !important;
        }

        /* ── Elite Ultra-Responsive Mobile-First Modal System ── */
        .elite-modal {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.24s ease;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            inset: 0 !important;
            width: 100% !important;
            width: 100vw !important;
            height: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            z-index: 99999 !important;
            background: rgba(15, 23, 42, 0.72) !important;
            backdrop-filter: blur(10px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(10px) saturate(180%) !important;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            -webkit-overflow-scrolling: touch;
            box-sizing: border-box;
            overscroll-behavior: contain;
        }
        @media (max-width: 640px) {
            .elite-modal {
                padding: 0.5rem;
                padding-bottom: env(safe-area-inset-bottom, 0.75rem);
            }
        }
        .elite-modal.active {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .elite-modal.hidden {
            display: none !important;
        }
        .elite-modal-dialog {
            transition: transform 0.26s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease;
            transform: scale(0.95) translateY(10px);
            opacity: 0;
            will-change: transform, opacity;
            max-height: calc(100dvh - 2rem);
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            width: 100%;
            margin: auto;
            box-sizing: border-box;
            overscroll-behavior: contain;
        }
        @media (max-width: 640px) {
            .elite-modal-dialog {
                max-height: calc(100dvh - 1.25rem);
                max-height: calc(100vh - 1.25rem);
                border-radius: 24px;
            }
        }
        .elite-modal.active .elite-modal-dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        html.dark .elite-modal-dialog {
            background-color: #0F172A !important;
            border-color: #1E293B !important;
            color: #F8FAFC !important;
        }
        html.dark .toast-card {
            background: #1E293B !important;
            color: #F8FAFC !important;
            border-color: #334155 !important;
        }
        html.dark .toast-card.toast-danger, html.dark .toast-card.toast-error {
            background: #3B1219 !important;
            border-color: #881337 !important;
            color: #FECDD3 !important;
        }
        html.dark .toast-card.toast-success {
            background: #052E16 !important;
            border-color: #14532D !important;
            color: #BBF7D0 !important;
        }
        html.dark .toast-card.toast-warning {
            background: #3B2404 !important;
            border-color: #78350F !important;
            color: #FDE68A !important;
        }
        html.dark .toast-card.toast-info {
            background: #042F2E !important;
            border-color: #115E59 !important;
            color: #99F6E4 !important;
        }

        /* Sleek custom scrollbars for modals and panels */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(13, 148, 136, 0.7);
        }

        /* ─── Global Top-Center Toast Notification System ─── */
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
            from { opacity: 0; transform: translateY(-16px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .toast-card.toast-exiting {
            animation: toastSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
        }
        @keyframes toastSlideUp {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-12px) scale(0.95); }
        }
        .toast-card.toast-danger, .toast-card.toast-error {
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
        .toast-danger .toast-icon-badge, .toast-error .toast-icon-badge {
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
        .toast-danger .toast-progress-bar, .toast-error .toast-progress-bar { background: #e11d48; }
        .toast-success .toast-progress-bar { background: #16a34a; }
        .toast-warning .toast-progress-bar { background: #d97706; }
        .toast-info .toast-progress-bar { background: #0d9488; }
        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* ─── Robust Desktop & Mobile Sidebar Engine ─── */
        .portal-sidebar-wrapper {
            width: var(--portal-sidebar-w);
            min-width: var(--portal-sidebar-w);
            overflow-x: hidden;
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .portal-main-canvas {
            transition: margin 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* LTR Desktop (>= 1024px) */
        @media (min-width: 1024px) {
            html[dir="ltr"] .portal-sidebar-wrapper {
                left: 0;
                right: auto;
                transform: translateX(0);
            }
            html[dir="ltr"] .portal-main-canvas {
                margin-left: var(--portal-sidebar-w) !important;
                margin-right: 0 !important;
            }
            /* Desktop Collapsed */
            html[dir="ltr"] body.sidebar-collapsed .portal-sidebar-wrapper {
                transform: translateX(-100%) !important;
            }
            html[dir="ltr"] body.sidebar-collapsed .portal-main-canvas {
                margin-left: 0 !important;
            }
        }

        /* RTL Desktop (>= 1024px) */
        @media (min-width: 1024px) {
            html[dir="rtl"] .portal-sidebar-wrapper {
                right: 0;
                left: auto;
                transform: translateX(0);
            }
            html[dir="rtl"] .portal-main-canvas {
                margin-right: var(--portal-sidebar-w) !important;
                margin-left: 0 !important;
            }
            /* Desktop Collapsed */
            html[dir="rtl"] body.sidebar-collapsed .portal-sidebar-wrapper {
                transform: translateX(100%) !important;
            }
            html[dir="rtl"] body.sidebar-collapsed .portal-main-canvas {
                margin-right: 0 !important;
            }
        }

        /* Mobile & Tablet (< 1024px) Off-Canvas */
        @media (max-width: 1023.98px) {
            .portal-main-canvas {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            html[dir="ltr"] .portal-sidebar-wrapper {
                left: 0;
                right: auto;
                transform: translateX(-100%);
            }
            html[dir="ltr"] .portal-sidebar-wrapper.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
            html[dir="rtl"] .portal-sidebar-wrapper {
                right: 0;
                left: auto;
                transform: translateX(100%);
            }
            html[dir="rtl"] .portal-sidebar-wrapper.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
        }

        /* ─── Buttons Visibility Rules ─── */
        .portal-navbar-toggle {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 1024px) {
            body:not(.sidebar-collapsed) .portal-navbar-toggle {
                display: none !important;
            }
        }

        .portal-sidebar-close-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        /* ─── Backdrop System ─── */
        #portalSidebarBackdrop {
            position: fixed;
            inset: 0;
            z-index: 45;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #portalSidebarBackdrop.active {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        @media (min-width: 1024px) {
            #portalSidebarBackdrop {
                display: none !important;
            }
        }

        /* ── Dark mode card/surface overrides ── */
        html.dark .bg-white { background-color: #0F172A !important; }
        html.dark .bg-\[\#FAFAF9\] { background-color: #0B0F19 !important; }
        html.dark .bg-slate-50 { background-color: #162032 !important; }
        html.dark .bg-white\/40 { background-color: rgba(15,23,42,0.4) !important; }
        html.dark .border-slate-200\/90 { border-color: rgba(30,41,59,0.9) !important; }
        html.dark .border-slate-200 { border-color: #1E293B !important; }
        html.dark .border-slate-100 { border-color: #1E293B !important; }
        html.dark .text-slate-900 { color: #F8FAFC !important; }
        html.dark .text-slate-800 { color: #E2E8F0 !important; }
        html.dark .text-slate-700 { color: #CBD5E1 !important; }
        html.dark .text-slate-600 { color: #94A3B8 !important; }
        html.dark .text-slate-500 { color: #94A3B8 !important; }
        html.dark .shadow-xl { box-shadow: 0 20px 40px rgba(0,0,0,0.4) !important; }

        /* Dark mode: keep text readable on portal cards/chips */
        html.dark .glass-card {
            background: rgba(15, 23, 42, 0.96) !important;
            border-color: #1E293B !important;
            color: #E2E8F0;
        }
        html.dark .session-card-item {
            background: #162032 !important;
            border-color: #334155 !important;
            color: #E2E8F0 !important;
        }
        html.dark .session-card-item h3,
        html.dark .session-card-item strong {
            color: #F8FAFC !important;
        }
        html.dark .bg-teal-50,
        html.dark .bg-teal-50\/90,
        html.dark .bg-teal-100 {
            background-color: rgba(19, 78, 74, 0.55) !important;
        }
        html.dark .text-teal-900,
        html.dark .text-teal-800,
        html.dark .text-teal-700 {
            color: #99F6E4 !important;
        }
        html.dark .bg-indigo-50 {
            background-color: rgba(49, 46, 129, 0.45) !important;
        }
        html.dark .text-indigo-900,
        html.dark .text-indigo-800 {
            color: #C7D2FE !important;
        }
        html.dark .bg-emerald-50,
        html.dark .bg-emerald-50\/70 {
            background-color: rgba(6, 78, 59, 0.55) !important;
        }
        html.dark .text-emerald-800 {
            color: #A7F3D0 !important;
        }
        html.dark .bg-amber-50,
        html.dark .bg-amber-100,
        html.dark .bg-amber-100\/90,
        html.dark .bg-amber-500\/10 {
            background-color: rgba(120, 53, 15, 0.45) !important;
        }
        html.dark .text-amber-950,
        html.dark .text-amber-900,
        html.dark .text-amber-800 {
            color: #FDE68A !important;
        }
        html.dark .bg-rose-50,
        html.dark .bg-rose-50\/90 {
            background-color: rgba(136, 19, 55, 0.4) !important;
        }
        html.dark .text-rose-950,
        html.dark .text-rose-900,
        html.dark .text-rose-800 {
            color: #FECDD3 !important;
        }
        html.dark .bg-blue-100,
        html.dark .bg-blue-100\/90 {
            background-color: rgba(30, 64, 175, 0.4) !important;
        }
        html.dark .text-blue-900 {
            color: #BFDBFE !important;
        }
        html.dark .bg-slate-100,
        html.dark .bg-slate-100\/90,
        html.dark .bg-slate-100\/80 {
            background-color: #1E293B !important;
        }
        html.dark .bg-slate-200,
        html.dark .bg-slate-200\/80 {
            background-color: #334155 !important;
        }
        html.dark .session-tab-btn.bg-white,
        html.dark .session-tab-btn.text-teal-900 {
            background-color: #134E4A !important;
            color: #99F6E4 !important;
            border-color: #115E59 !important;
        }
        html.dark .tab-count-badge.bg-teal-100 {
            background-color: rgba(19, 78, 74, 0.8) !important;
            color: #99F6E4 !important;
        }
        html.dark .tab-count-badge.bg-slate-200 {
            background-color: #334155 !important;
            color: #E2E8F0 !important;
        }
        html.dark #liveSessions .bg-slate-100\/90 {
            background-color: #1E293B !important;
        }
        html.dark .portal-main-canvas a.bg-white,
        html.dark .portal-main-canvas button.bg-white {
            background-color: #1E293B !important;
            color: #E2E8F0 !important;
            border-color: #334155 !important;
        }

        /* ── Responsive Tab Bar Scroll ── */
        .teacher-tab-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            scroll-snap-type: x proximity;
        }
        .teacher-tab-scroll::-webkit-scrollbar { display: none; }

        /* ── Elite Custom Select Component ── */
        .elite-select-wrapper {
            position: relative;
            width: 100%;
        }
        .elite-select-trigger {
            cursor: pointer;
            user-select: none;
        }
        .elite-select-menu {
            transition: opacity 0.15s cubic-bezier(0.16, 1, 0.3, 1), transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .elite-select-menu.hidden {
            display: none !important;
        }
        html.dark .elite-select-trigger {
            background-color: #0F172A !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }
        html.dark .elite-select-trigger:hover {
            border-color: #14B8A6 !important;
        }
        html.dark .elite-select-menu {
            background-color: rgba(15, 23, 42, 0.98) !important;
            border-color: #1E293B !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7) !important;
        }
        html.dark .elite-select-option {
            color: #E2E8F0 !important;
        }
        html.dark .elite-select-option:hover {
            background-color: rgba(30, 41, 59, 0.8) !important;
            color: #FFFFFF !important;
        }
        html.dark .elite-select-option.is-selected {
            background-color: rgba(19, 78, 74, 0.6) !important;
            color: #5EEAD4 !important;
            border-color: rgba(20, 184, 166, 0.4) !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/portal-theme.css') }}?v={{ @filemtime(public_path('css/portal-theme.css')) ?: time() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col font-sans transition-colors duration-200 overflow-x-hidden"
      style="background-color:var(--surface-bg,#FAFAF9);color:var(--text-primary,#0F172A);">
    
    {{-- Unified Portal Sidebar Component --}}
    <x-portal-sidebar />

    {{-- Main App Canvas Container (Shifted with CSS margin on desktop) --}}
    <div class="portal-main-canvas flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300">
        
        {{-- Unified Portal Top Navbar --}}
        <x-portal-navbar :title="$pageTitle ?? __('Dashboard Panel')" />

        {{-- Page Main Dynamic Body --}}
        <main class="flex-1 p-3 sm:p-5 lg:p-8 space-y-6 max-w-full overflow-x-hidden pb-safe">
            @yield('content')
        </main>

        {{-- Minimal Portal Footer --}}
        <footer class="py-5 px-6 border-t text-center text-xs font-mono pb-safe"
                style="border-color:var(--surface-border,#E2E8F0);color:var(--text-muted,#94A3B8);background:var(--surface-card,#fff);">
            <p>&copy; {{ date('Y') }} Elite Academy. {{ __('All rights reserved.') }} &bull; {{ __('Leading Accredited Academy') }}</p>
        </footer>
    </div>

    {{-- Core Global Portal UI Scripts --}}
    <script>
        function togglePortalSidebar(action) {
            const isDesktop = window.innerWidth >= 1024;
            const sidebar = document.getElementById('portalSidebar');
            const backdrop = document.getElementById('portalSidebarBackdrop');

            if (isDesktop) {
                // Desktop collapse / expand
                if (action === false) {
                    document.body.classList.add('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '1');
                } else if (action === true) {
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '0');
                } else {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', isCollapsed ? '1' : '0');
                }
            } else {
                // Mobile off-canvas drawer
                if (action === true) {
                    if (sidebar) sidebar.classList.add('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.add('active');
                    document.body.classList.add('overflow-hidden');
                } else if (action === false) {
                    if (sidebar) sidebar.classList.remove('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    const isOpen = sidebar ? sidebar.classList.toggle('sidebar-mobile-open') : false;
                    if (backdrop) {
                        if (isOpen) {
                            backdrop.classList.add('active');
                            document.body.classList.add('overflow-hidden');
                        } else {
                            backdrop.classList.remove('active');
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                }
            }
        }

        // Restore desktop collapsed state on initial load
        (function() {
            try {
                if (window.innerWidth >= 1024 && localStorage.getItem('portal_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();

        // ── Theme Toggle ─────────────────────────────────────────────────────
        window.togglePortalTheme = function () {
            if (window.EliteTheme) {
                window.EliteTheme.toggle();
            } else {
                var isDark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('elite_theme', isDark ? 'dark' : 'light'); } catch(e) {}
            }
            var isNowDark = document.documentElement.classList.contains('dark');
            document.body.style.backgroundColor = isNowDark ? 'var(--surface-bg,#0B0F19)' : 'var(--surface-bg,#FAFAF9)';
            document.body.style.color = isNowDark ? 'var(--text-primary,#F8FAFC)' : 'var(--text-primary,#0F172A)';
            // Sync all toggle icons
            document.querySelectorAll('.theme-toggle-icon').forEach(function(el) {
                el.className = 'theme-toggle-icon ' + (isNowDark ? 'fa-solid fa-sun text-amber-400' : 'fa-solid fa-moon text-slate-700');
            });
        };

        // Universal Backdrop Click & ESC Key Handling
        document.addEventListener('DOMContentLoaded', function () {
            // Sync theme icons on first load
            var isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('.theme-toggle-icon').forEach(function(el) {
                el.className = 'theme-toggle-icon ' + (isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon');
            });

            // Clicking directly on the modal backdrop overlay closes the modal
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('elite-modal')) {
                    window.closeModal(e.target.id);
                }
            });

            // ESC key closes topmost open modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    const openModals = document.querySelectorAll('.elite-modal.active:not(.hidden)');
                    if (openModals.length > 0) {
                        const topModal = openModals[openModals.length - 1];
                        if (topModal && topModal.id) {
                            window.closeModal(topModal.id);
                        }
                    }
                    // Also close open custom selects
                    document.querySelectorAll('.elite-select-menu:not(.hidden)').forEach(m => m.classList.add('hidden'));
                    document.querySelectorAll('.elite-select-trigger .fa-chevron-down.rotate-180').forEach(i => i.classList.remove('rotate-180'));
                    document.querySelectorAll('.elite-select-trigger.ring-2').forEach(t => t.classList.remove('ring-2', 'ring-teal-500/25', 'border-teal-500'));
                }
            });

            // Global click outside for custom select
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.elite-select-wrapper')) {
                    document.querySelectorAll('.elite-select-menu:not(.hidden)').forEach(m => m.classList.add('hidden'));
                    document.querySelectorAll('.elite-select-trigger .fa-chevron-down.rotate-180').forEach(i => i.classList.remove('rotate-180'));
                    document.querySelectorAll('.elite-select-trigger.ring-2').forEach(t => t.classList.remove('ring-2', 'ring-teal-500/25', 'border-teal-500'));
                }
            });

            // Auto-initialize Elite Custom Selects
            if (typeof window.initEliteCustomSelects === 'function') {
                window.initEliteCustomSelects();
            }
        });

        // ════════════════════════════════════════════════════════════════════════
        // UNIVERSAL ELITE CUSTOM SELECT ENGINE
        // ════════════════════════════════════════════════════════════════════════
        class EliteCustomSelect {
            constructor(selectElement, options = {}) {
                this.select = typeof selectElement === 'string' ? document.getElementById(selectElement) : selectElement;
                if (!this.select || this.select._eliteCustomSelect) return;
                this.select._eliteCustomSelect = this;
                this.options = options;
                this.isOpen = false;
                this.build();
            }

            build() {
                // Ensure native select is sr-only
                this.select.classList.add('sr-only');
                this.select.setAttribute('tabindex', '-1');
                this.select.setAttribute('aria-hidden', 'true');

                // Wrapper
                this.wrapper = document.createElement('div');
                this.wrapper.className = 'elite-select-wrapper relative w-full min-w-0';
                this.select.parentNode.insertBefore(this.wrapper, this.select);
                this.wrapper.appendChild(this.select);

                // Trigger button
                this.trigger = document.createElement('button');
                this.trigger.type = 'button';
                this.trigger.className = 'elite-select-trigger w-full min-w-0 overflow-hidden flex items-center justify-between gap-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-teal-500/80 dark:hover:border-teal-500 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-xs transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500/25';
                
                this.labelSpan = document.createElement('span');
                this.labelSpan.className = 'elite-select-label truncate text-start flex-1 min-w-0';
                
                this.icon = document.createElement('i');
                this.icon.className = 'fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 shrink-0';

                this.trigger.appendChild(this.labelSpan);
                this.trigger.appendChild(this.icon);
                this.wrapper.appendChild(this.trigger);

                // Dropdown menu
                this.menu = document.createElement('div');
                this.menu.className = 'elite-select-menu hidden absolute top-[calc(100%+6px)] start-0 w-full min-w-[210px] max-h-64 overflow-y-auto custom-scrollbar bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-1.5 z-50 transition-all duration-150 transform origin-top';
                this.wrapper.appendChild(this.menu);

                // Populate options
                this.renderOptions();

                // Sync initial label
                this.syncLabel();

                // Trigger click
                this.trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggle();
                });

                // Listen to external change events on native select
                this.select.addEventListener('change', () => {
                    this.syncLabel();
                    this.syncSelectedOption();
                });
            }

            renderOptions() {
                this.menu.innerHTML = '';
                const options = Array.from(this.select.options);
                const currentVal = this.select.value;

                options.forEach(opt => {
                    const isSelected = opt.value === currentVal || (!currentVal && !opt.value);
                    const item = document.createElement('div');
                    item.className = 'elite-select-option px-3 py-2 text-xs rounded-xl cursor-pointer transition-all flex items-center justify-between gap-2 ' +
                        (isSelected
                            ? 'is-selected bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 font-bold border border-teal-200/80 dark:border-teal-800/60'
                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100/90 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white font-medium');
                    item.setAttribute('data-value', opt.value);

                    const textSpan = document.createElement('span');
                    textSpan.className = 'text-start whitespace-normal break-words';
                    textSpan.textContent = opt.textContent;
                    item.appendChild(textSpan);

                    if (isSelected) {
                        const checkIcon = document.createElement('i');
                        checkIcon.className = 'fa-solid fa-check text-xs text-teal-600 dark:text-teal-400 shrink-0';
                        item.appendChild(checkIcon);
                    }

                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.selectValue(opt.value);
                    });

                    this.menu.appendChild(item);
                });
            }

            selectValue(val) {
                if (this.select.value !== val) {
                    this.select.value = val;
                    this.select.dispatchEvent(new Event('change', { bubbles: true }));
                    this.select.dispatchEvent(new Event('input', { bubbles: true }));
                }
                this.syncLabel();
                this.syncSelectedOption();
                this.close();
            }

            syncLabel() {
                const selectedOpt = this.select.options[this.select.selectedIndex];
                const label = selectedOpt ? selectedOpt.textContent.trim() : '';
                this.labelSpan.textContent = label;
                this.trigger.title = label;
            }

            syncSelectedOption() {
                const currentVal = this.select.value;
                this.menu.querySelectorAll('.elite-select-option').forEach(item => {
                    const isSelected = item.getAttribute('data-value') === currentVal;
                    if (isSelected) {
                        item.className = 'elite-select-option is-selected px-3 py-2 text-xs rounded-xl cursor-pointer transition-all flex items-center justify-between gap-2 bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 font-bold border border-teal-200/80 dark:border-teal-800/60';
                        if (!item.querySelector('.fa-check')) {
                            const check = document.createElement('i');
                            check.className = 'fa-solid fa-check text-xs text-teal-600 dark:text-teal-400 shrink-0';
                            item.appendChild(check);
                        }
                    } else {
                        item.className = 'elite-select-option px-3 py-2 text-xs rounded-xl cursor-pointer transition-all flex items-center justify-between gap-2 text-slate-700 dark:text-slate-300 hover:bg-slate-100/90 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white font-medium';
                        const existingCheck = item.querySelector('.fa-check');
                        if (existingCheck) existingCheck.remove();
                    }
                });
            }

            refresh() {
                this.renderOptions();
                this.syncLabel();
            }

            open() {
                document.querySelectorAll('.elite-select-menu:not(.hidden)').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.elite-select-trigger .fa-chevron-down.rotate-180').forEach(i => i.classList.remove('rotate-180'));
                document.querySelectorAll('.elite-select-trigger.ring-2').forEach(t => t.classList.remove('ring-2', 'ring-teal-500/25', 'border-teal-500'));

                this.menu.classList.remove('hidden');
                this.icon.classList.add('rotate-180');
                this.trigger.classList.add('ring-2', 'ring-teal-500/25', 'border-teal-500');
                this.isOpen = true;
            }

            close() {
                this.menu.classList.add('hidden');
                this.icon.classList.remove('rotate-180');
                this.trigger.classList.remove('ring-2', 'ring-teal-500/25', 'border-teal-500');
                this.isOpen = false;
            }

            toggle() {
                if (this.isOpen) {
                    this.close();
                } else {
                    this.open();
                }
            }
        }
        window.EliteCustomSelect = EliteCustomSelect;

        window.initEliteCustomSelects = function(root = document) {
            root.querySelectorAll('.elite-custom-select').forEach(el => {
                new EliteCustomSelect(el);
            });
        };
    </script>

    <script src="{{ asset('js/toast.js') }}"></script>

    @include('partials.realtime-notifications')

    @stack('scripts')
</body>
</html>
