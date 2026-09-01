<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth h-full bg-[#FAFAF9] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0F172A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    <link rel="shortcut icon" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    <title>{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform in Egypt' }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'Elite Academy empowers Egyptian students with accredited academic tracks in Programming, Artificial Intelligence, Science, and Business led by top educators.' }}">
    <meta name="keywords" content="Elite Academy, أكاديمية إيليت, منصة تعليمية, الثانوية العامة, برمجة, ذكاء اصطناعي, مصر, كورس, دروس مباشرة">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="Elite Academy Team">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Multilingual Hreflang Alternates --}}
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    {{-- Ensure Pure Light Mode Across All Pages --}}
    <script>
        (function() {
            try {
                localStorage.removeItem('theme');
                document.documentElement.classList.remove('dark');
            } catch (e) {}
        })();
    </script>

    {{-- Open Graph / Facebook Meta Tags --}}
    <meta property="og:site_name" content="Elite Academy | أكاديمية إيليت">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform' }}">
    <meta property="og:description" content="{{ $pageDescription ?? 'Join Egypt’s premier academic platform for live classes, accredited tracks, and expert mentors.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ media_url($ogImage ?? 'images/academy_campus.webp') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_EG' : 'en_US' }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت' }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? 'Egypt’s premier academic platform for live classes and accredited tracks.' }}">
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
        <link rel="preload" as="image" href="{{ asset('images/hero_student.webp') }}" type="image/webp" fetchpriority="high">
    @endif
    <link rel="preload" as="image" href="{{ asset('images/logo_500.webp') }}" type="image/webp">
    @stack('head_preloads')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">

    <style>
        :root {
            --font-family-english: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-arabic: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-sans: var(--font-family-arabic);
            --font-heading: var(--font-family-arabic);
        }
        html[lang="en"], [dir="ltr"] {
            --font-sans: var(--font-family-english);
            --font-heading: var(--font-family-english);
        }
        html, body, button, input, select, textarea, table, .font-sans, .font-heading {
            font-family: var(--font-sans) !important;
        }
        html, body {
            background-color: #FAFAF9;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        html.dark, html.dark body {
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
    </style>
    <link rel="preload" as="style" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
    @include('partials.inp-optimizer')
</head>
<body class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 selection:bg-teal-500/20 selection:text-teal-400 flex flex-col min-h-screen m-0 p-0 transition-colors duration-200">

    {{-- Accessible Skip to Content Link --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-teal-600 focus:text-white focus:font-bold focus:rounded-xl focus:shadow-2xl focus:ring-2 focus:ring-white">
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
        <button id="back-to-top" aria-label="{{ app()->getLocale() === 'ar' ? 'الرجوع إلى أعلى الصفحة' : 'Back to top' }}">↑</button>
    @endif

    <script>
        function announceA11y(message) {
            const announcer = document.getElementById('a11y-announcer');
            if (announcer) {
                announcer.textContent = '';
                setTimeout(() => { announcer.textContent = message; }, 50);
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script defer src="{{ asset('js/scroll-reveal.js') }}"></script>
    <script defer src="{{ asset('js/toast.js') }}?v={{ time() }}"></script>
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

    @if(auth()->check() && !request()->boolean('iframe'))
        {{-- FCM Push Notification Permission Popup Modal --}}
        <div id="fcm-permission-modal" class="hidden fixed bottom-6 right-6 left-6 sm:left-auto sm:max-w-md bg-slate-900/95 backdrop-blur-md text-white p-6 rounded-3xl shadow-2xl border border-slate-700/80 z-50 transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/40 flex items-center justify-center text-2xl shrink-0">
                    🔔
                </div>
                <div class="space-y-2 flex-1">
                    <h4 class="font-heading font-bold text-sm sm:text-base text-white">
                        {{ __('Enable Live Push Notifications') }}
                    </h4>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('Stay updated with instant alerts for upcoming live sessions, 24h assignment deadlines, and admin approvals.') }}
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <button id="btn-enable-fcm" type="button" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-xl text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>✨</span> {{ __('Allow Notifications Now') }}
                        </button>
                        <button id="btn-dismiss-fcm" type="button" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs transition-all cursor-pointer">
                            {{ __('Later') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firebase JS SDK (v9 Compat) for Real Native FCM Web Push (Authenticated Users Only) -->
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js" defer></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js" defer></script>

        <script>
            window.firebaseConfig = {
                apiKey: "{{ config('fcm.web_config.api_key') }}",
                authDomain: "{{ config('fcm.web_config.auth_domain', 'elite-academy-67a15.firebaseapp.com') }}",
                projectId: "{{ config('fcm.v1.project_id', 'elite-academy-67a15') }}",
                storageBucket: "{{ config('fcm.web_config.storage_bucket', 'elite-academy-67a15.firebasestorage.app') }}",
                messagingSenderId: "{{ config('fcm.web_config.messaging_sender_id', '53377882422') }}",
                appId: "{{ config('fcm.web_config.app_id') }}"
            };

            window.sendFcmTokenToServer = function(token) {
                const shortToken = token.length > 25 ? token.substring(0, 22) + '...' : token;
                console.log('[FCM] Token obtained:', shortToken);

                const tokenInputs = document.querySelectorAll('#userFcmTokenInput');
                tokenInputs.forEach(input => {
                    input.value = token;
                });

                const currentUserId = "{{ auth()->id() ?? 0 }}";
                const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
                localStorage.setItem(fcmTokenKey, token);

                fetch('{{ route('ajax.notifications.token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: token,
                        device_type: 'web_browser'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log('[FCM] Token saved to server.');
                    }
                })
                .catch(() => {});
            };

            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('fcm-permission-modal');
                const btnEnable = document.getElementById('btn-enable-fcm');
                const btnDismiss = document.getElementById('btn-dismiss-fcm');

                let messaging = null;
                if (typeof firebase !== 'undefined') {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        if (firebase.messaging.isSupported()) {
                            messaging = firebase.messaging();
                            
                            if ('serviceWorker' in navigator) {
                                navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}').then((reg) => {
                                    if (messaging && Notification.permission === 'granted') {
                                        const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                                        const tokenOpts = { serviceWorkerRegistration: reg };
                                        if (vapidKey) tokenOpts.vapidKey = vapidKey;

                                        messaging.getToken(tokenOpts).then((token) => {
                                            if (token) {
                                                sendFcmTokenToServer(token);
                                            }
                                        }).catch((err) => {
                                            const saved = getSavedToken();
                                            if (saved) sendFcmTokenToServer(saved);
                                        });
                                    }
                                }).catch(() => {});
                            }

                            messaging.onMessage((payload) => {
                                const title = (payload.notification && payload.notification.title) ||
                                              (payload.data && payload.data.title) ||
                                              '🔔 Firebase Push Notification';

                                const body = (payload.notification && payload.notification.body) ||
                                             (payload.data && payload.data.body) ||
                                             '';

                                const icon = (payload.notification && payload.notification.image) ||
                                             (payload.data && payload.data.icon) ||
                                             '/images/logo_500.webp';

                                if (window.Toast) {
                                    window.Toast.info(body, title);
                                }

                                if ('Notification' in window && Notification.permission === 'granted') {
                                    try {
                                        new Notification(title, { body: body, icon: icon });
                                    } catch (e) {}
                                }

                                window.dispatchEvent(new CustomEvent('fcm-realtime-message', { detail: { notification: { title, body, image: icon }, data: payload.data || {} } }));
                            });
                        }
                    } catch (err) {
                        console.warn('Firebase Messaging init fallback:', err);
                    }
                }

                function getSavedToken() {
                    const currentUserId = "{{ auth()->id() ?? 0 }}";
                    const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
                    return localStorage.getItem(fcmTokenKey);
                }

                const savedToken = getSavedToken();
                if (savedToken) {
                    sendFcmTokenToServer(savedToken);
                }

                if ('Notification' in window) {
                    if (Notification.permission === 'default') {
                        if (modal) modal.classList.remove('hidden');
                    } else if (Notification.permission === 'granted') {
                        const saved = getSavedToken();
                        if (!saved && messaging) {
                            window.requestLiveFirebaseToken();
                        }
                    }
                }

                if (btnEnable) {
                    btnEnable.addEventListener('click', function () {
                        if (modal) modal.classList.add('hidden');
                        window.requestLiveFirebaseToken();
                    });
                }

                if (btnDismiss) {
                    btnDismiss.addEventListener('click', function () {
                        if (modal) modal.classList.add('hidden');
                    });
                }
            });

            window.copyFcmTokenToClipboard = function() {
                const input = document.getElementById('userFcmTokenInput');
                if (input && input.value && navigator.clipboard) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        if (window.Toast) {
                            window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم نسخ رمز FCM للحافظة!' : 'FCM Token copied to clipboard!'));
                        }
                    }).catch(() => {
                        if (window.Toast) {
                            window.Toast.error(@json(app()->getLocale() === 'ar' ? 'فشل نسخ الرمز' : 'Failed to copy token'));
                        }
                    });
                }
            };

            window.registerCustomFcmToken = function() {
                const input = document.getElementById('userFcmTokenInput');
                const token = input ? input.value.trim() : '';

                if (!token) {
                    if (window.Toast) window.Toast.error(@json(app()->getLocale() === 'ar' ? 'يرجى إدخال رمز FCM أولاً!' : 'Please enter an FCM token string first!'));
                    return;
                }

                const currentUserId = "{{ auth()->id() ?? 0 }}";
                const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
                localStorage.setItem(fcmTokenKey, token);

                fetch('{{ route('ajax.notifications.token') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: token,
                        device_type: 'web_browser'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && window.Toast) {
                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم تسجيل وتحديث رمز FCM في النظام بنجاح!' : 'FCM Token registered and updated cleanly!'));
                    }
                })
                .catch(() => {
                    if (window.Toast) window.Toast.error('Failed to update FCM token');
                });
            };

            window.requestLiveFirebaseToken = function() {
                if (!('Notification' in window) || !('PushManager' in window) || !('serviceWorker' in navigator)) {
                    if (window.Toast) window.Toast.error(document.documentElement.lang === 'ar' ? 'المتصفح لا يدعم إشعارات المتصفح الفورية' : 'Browser does not support Web Push notifications');
                    return;
                }

                Notification.requestPermission().then(async (permission) => {
                    if (permission !== 'granted') {
                        if (window.Toast) window.Toast.warning(document.documentElement.lang === 'ar' ? 'لم يتم منح إذن الإشعارات' : 'Notification permission was not granted');
                        return;
                    }

                    if (typeof firebase !== 'undefined') {
                        try {
                            if (!firebase.apps.length) {
                                firebase.initializeApp(firebaseConfig);
                            }
                            if (firebase.messaging.isSupported()) {
                                const messaging = firebase.messaging();
                                const reg = await navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}', { scope: '/' });
                                await navigator.serviceWorker.ready;

                                const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                                const opts = { serviceWorkerRegistration: reg };
                                if (vapidKey) opts.vapidKey = vapidKey;

                                messaging.getToken(opts).then((token) => {
                                    if (token) {
                                        sendFcmTokenToServer(token);
                                        
                                        fetch("{{ route('ajax.notifications.test-push') }}", {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        }).catch(() => {});

                                        if (window.Toast) {
                                            window.Toast.success(document.documentElement.lang === 'ar' ? 'تم تفعيل وتحديث إشعارات الفايبربيس بنجاح! 🔔' : 'Live Firebase Push Notifications Enabled Successfully! 🔔');
                                        }
                                    } else {
                                        if (window.Toast) window.Toast.warning('No FCM registration token returned by Firebase');
                                    }
                                }).catch(err => {
                                    console.error('Firebase getToken error:', err);
                                    const currentUserId = "{{ auth()->id() ?? 0 }}";
                                    const saved = localStorage.getItem('elite_fcm_token_' + currentUserId);
                                    if (saved) sendFcmTokenToServer(saved);
                                    if (window.Toast) window.Toast.error('Firebase token request: ' + (err ? err.message : 'Push manager not ready'));
                                });
                            }
                        } catch (e) {
                            console.error('Firebase error:', e);
                        }
                    }
                });
            };
        </script>
    @endif

    @stack('scripts')
</body>
</html>
