<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    class="scroll-smooth h-full bg-[#FAFAF9] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0F172A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    <link rel="shortcut icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    <title>{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform in Egypt' }}</title>
    <meta name="description"
        content="{{ $pageDescription ?? 'Elite Academy empowers Egyptian students with accredited academic tracks in Programming, Artificial Intelligence, Science, and Business led by top educators.' }}">
    <meta name="keywords"
        content="Elite Academy, أكاديمية إيليت, منصة تعليمية, الثانوية العامة, برمجة, ذكاء اصطناعي, مصر, كورس, دروس مباشرة">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="Elite Academy Team">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Multilingual Hreflang Alternates --}}
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    {{-- Ensure Pure Light Mode Across All Pages --}}
    <script>
        (function () {
            try {
                localStorage.removeItem('theme');
                document.documentElement.classList.remove('dark');
            } catch (e) { }
        })();
    </script>

    {{-- Open Graph / Facebook Meta Tags --}}
    <meta property="og:site_name" content="Elite Academy | أكاديمية إيليت">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform' }}">
    <meta property="og:description"
        content="{{ $pageDescription ?? 'Join Egypt’s premier academic platform for live classes, accredited tracks, and expert mentors.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ media_url($ogImage ?? 'images/academy_campus.webp') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_EG' : 'en_US' }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت' }}">
    <meta name="twitter:description"
        content="{{ $pageDescription ?? 'Egypt’s premier academic platform for live classes and accredited tracks.' }}">
    <meta name="twitter:image" content="{{ media_url($ogImage ?? 'images/academy_campus.webp') }}">

    {{-- Master JSON-LD Schema.org Structured Data Graph --}}
    @php
        $globalAppJsonLd = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "WebSite",
                    "@id" => url('/') . "/#website",
                    "url" => url('/'),
                    "name" => "Elite Academy LMS",
                    "description" => "Egypt premier accredited K-12 interactive tutoring and learning platform.",
                    "publisher" => [
                        "@id" => url('/') . "/#organization"
                    ],
                    "inLanguage" => app()->getLocale(),
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => url('/courses') . "?search={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "EducationalOrganization",
                    "@id" => url('/') . "/#organization",
                    "name" => "Elite Academy LMS",
                    "alternateName" => "أكاديمية إيليت التعليمية",
                    "url" => url('/'),
                    "logo" => asset('images/logo_500.webp'),
                    "image" => asset('images/academy_campus.webp'),
                    "description" => "Ministry-accredited interactive educational platform providing live classes, auto-graded assignments, and verified tutoring in Egypt.",
                    "telephone" => "+201000000000",
                    "email" => "support@elite-academy.com",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "Academic Center Tower, New Cairo",
                        "addressLocality" => "Cairo",
                        "addressCountry" => "EG"
                    ],
                    "aggregateRating" => [
                        "@type" => "AggregateRating",
                        "ratingValue" => "4.9",
                        "reviewCount" => "1280",
                        "bestRating" => "5",
                        "worstRating" => "1"
                    ],
                    "sameAs" => [
                        "https://facebook.com/eliteacademy",
                        "https://twitter.com/eliteacademy",
                        "https://instagram.com/eliteacademy"
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($globalAppJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @if(request()->routeIs('home') || request()->is('/'))
        <link rel="preload" as="image" href="{{ asset('images/hero_student.webp') }}" type="image/webp"
            fetchpriority="high">
    @endif
    <link rel="preload" as="image" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    @stack('head_preloads')

    {{-- Performance: Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Google Fonts --}}
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">

    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --font-family-english: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-arabic: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-sans: var(--font-family-arabic);
            --font-heading: var(--font-family-arabic);
        }

        html[lang="en"],
        [dir="ltr"] {
            --font-sans: var(--font-family-english);
            --font-heading: var(--font-family-english);
        }

        html,
        body,
        button,
        input,
        select,
        textarea,
        table,
        .font-sans,
        .font-heading {
            font-family: var(--font-sans) !important;
        }

        html,
        body {
            background-color: #FAFAF9;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        html.dark,
        html.dark body {
            background-color: #0B0F19 !important;
            color: #F1F5F9 !important;
        }

        /* Essential Micro-Interactions without Forced Layer Proliferation */
        .card-lift {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-lift:hover {
            transform: translateY(-4px);
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

        html.dark .glass-card {
            background: rgba(15, 23, 42, 0.92) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #F1F5F9;
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
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* ─── Universal Elite Modal System ─── */
        .elite-modal {
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.25s ease;
            opacity: 0;
            pointer-events: none;
        }
        .elite-modal.active {
            opacity: 1;
            pointer-events: auto;
        }
        .elite-modal.hidden {
            display: none !important;
        }
        .elite-modal-dialog {
            transition: transform 0.26s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease;
            transform: scale(0.95) translateY(10px);
            opacity: 0;
            will-change: transform, opacity;
        }
        .elite-modal.active .elite-modal-dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
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
    </style>
    <script src="{{ asset('js/toast.js') }}?v={{ time() }}"></script>
    <link rel="preload" as="style" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
    @include('partials.inp-optimizer')
</head>

<body
    class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 selection:bg-teal-500/20 selection:text-teal-400 flex flex-col min-h-screen m-0 p-0 transition-colors duration-200">

    {{-- Accessible Skip to Content Link --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-600 focus:text-white focus:font-bold focus:rounded-xl focus:shadow-2xl focus:ring-2 focus:ring-white">
        {{ app()->getLocale() === 'ar' ? 'التخطي إلى المحتوى الرئيسي' : 'Skip to main content' }}
    </a>

    {{-- Screen Reader Live Announcement Region --}}
    <div id="a11y-announcer" class="sr-only" aria-live="polite" aria-atomic="true"></div>

    {{-- Scroll Progress Bar --}}
    @if(!request()->boolean('iframe'))
        <div id="scroll-progress" class="fixed top-0 left-0 right-0 h-1 bg-teal-500 z-[60]" style="width: 0%"></div>
    @endif

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.ambient')
        @include('partials.navbar')
    @endif

    <main id="main-content" class="flex-grow w-full bg-[#FAFAF9] dark:bg-[#0B0F19] min-h-[60vh]" tabindex="-1">
        @yield('content')
    </main>

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.footer')
    @endif

    {{-- Back to Top Button --}}
    @if(!request()->boolean('iframe'))
        <button id="back-to-top"
            aria-label="{{ app()->getLocale() === 'ar' ? 'الرجوع إلى أعلى الصفحة' : 'Back to top' }}">↑</button>
    @endif

    <script>
        function announceA11y(message) {
            const announcer = document.getElementById('a11y-announcer');
            if (announcer) {
                announcer.textContent = '';
                setTimeout(() => { announcer.textContent = message; }, 50);
            }
        }

        // ─── Universal High-Performance Modal System ───
        window.openModal = function (id) {
            const modal = typeof id === 'string' ? document.getElementById(id) : id;
            if (!modal) return;

            modal.classList.add('elite-modal');
            const dialog = modal.querySelector('.elite-modal-dialog') || modal.firstElementChild;
            if (dialog && !dialog.classList.contains('elite-modal-dialog')) {
                dialog.classList.add('elite-modal-dialog');
            }

            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modal.classList.add('active');
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
            setTimeout(() => {
                modal.classList.add('hidden');
                const remaining = document.querySelectorAll('.elite-modal.active, [id$="Modal"]:not(.hidden)');
                if (remaining.length === 0) {
                    document.body.classList.remove('overflow-hidden');
                }
            }, 200);
        };

        // Universal Backdrop Click & ESC Key Handling
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const openModalEl = e.target.closest('.elite-modal, [id$="Modal"]');
                if (openModalEl && !openModalEl.classList.contains('hidden')) {
                    const dialog = openModalEl.querySelector('.elite-modal-dialog') || openModalEl.firstElementChild;
                    if (dialog && !dialog.contains(e.target)) {
                        window.closeModal(openModalEl.id);
                    }
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    const openModals = document.querySelectorAll('.elite-modal.active, [id$="Modal"]:not(.hidden)');
                    if (openModals.length > 0) {
                        const topModal = openModals[openModals.length - 1];
                        window.closeModal(topModal.id);
                    }
                }
            });
        });
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script defer src="{{ asset('js/scroll-reveal.js') }}"></script>
    @php
        $flashToasts = array_filter([
            'success' => session('success'),
            'error' => session('error'),
            'warning' => session('warning'),
            'info' => session('info'),
        ]);
    @endphp
    @if(!empty($flashToasts))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toasts = @json($flashToasts);
                if (window.Toast) {
                    Object.keys(toasts).forEach(type => {
                        if (toasts[type] && typeof window.Toast[type] === 'function') {
                            window.Toast[type](toasts[type]);
                        }
                    });
                }
            });
        </script>
    @endif

    @include('partials.realtime-notifications')

    @stack('scripts')
</body>

</html>