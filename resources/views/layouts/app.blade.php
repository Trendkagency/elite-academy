<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth h-full bg-[#FAFAF9] text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0F172A">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/png">
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

    {{-- Open Graph / Facebook Meta Tags --}}
    <meta property="og:site_name" content="Elite Academy | أكاديمية إيليت">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت - Leading Educational Platform' }}">
    <meta property="og:description" content="{{ $pageDescription ?? 'Join Egypt’s premier academic platform for live classes, accredited tracks, and expert mentors.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ media_url($ogImage ?? 'images/academy_campus.png') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_EG' : 'en_US' }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? 'Elite Academy | أكاديمية إيليت' }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? 'Egypt’s premier academic platform for live classes and accredited tracks.' }}">
    <meta name="twitter:image" content="{{ media_url($ogImage ?? 'images/academy_campus.png') }}">

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
                    "logo" => asset('images/logo.png'),
                    "image" => asset('images/academy_campus.png'),
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

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family-english: "Cairo", sans-serif;
            --font-family-arabic: "Cairo", sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-md: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            --font-weight-regular: 400;
            --font-weight-medium: 500;
            --font-weight-semibold: 600;
            --font-weight-bold: 700;
            --line-height-tight: 1.2;
            --line-height-normal: 1.5;
            --line-height-relaxed: 1.7;
        }
        html[lang="ar"], [dir="rtl"] {
            --font-sans: "Cairo", sans-serif;
            --font-heading: "Cairo", sans-serif;
        }
        html[lang="en"], [dir="ltr"] {
            --font-sans: "Cairo", sans-serif;
            --font-heading: "Cairo", sans-serif;
        }
        html, body, button, input, select, textarea, table, .font-sans, .font-heading {
            font-family: var(--font-sans) !important;
        }

        html, body {
            background-color: #FAFAF9 !important;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Smooth UI Keyframe Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Ken Burns Slow Hero Image Zoom */
        @keyframes kenBurns {
            0% { transform: scale(1); }
            50% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        .animate-ken-burns {
            animation: kenBurns 18s ease-in-out infinite alternate;
            will-change: transform;
        }

        /* Floating Animation */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(4deg); }
        }

        .animate-float {
            animation: floatSlow 6s ease-in-out infinite;
            will-change: transform;
        }

        /* Ambient Pulse Glow */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.08); }
        }

        .animate-pulse-glow {
            animation: pulseGlow 8s ease-in-out infinite;
            will-change: opacity, transform;
        }

        /* Badge Pulse */
        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(20, 184, 166, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(20, 184, 166, 0); }
        }

        .animate-badge-pulse {
            animation: badgePulse 3s infinite;
        }

        /* Drift Animation */
        @keyframes driftSlow {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(10px, -15px) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        .animate-drift {
            animation: driftSlow 14s linear infinite;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out forwards;
        }

        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.12s; }
        .stagger-3 { animation-delay: 0.19s; }
        .stagger-4 { animation-delay: 0.26s; }

        /* Smooth GPU-Accelerated Image Hover Scaling */
        img {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform-style: preserve-3d;
        }

        .img-hover-zoom,
        .group:hover img,
        .card-lift img,
        [class*="group-hover:scale"],
        [class*="hover:scale"] {
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform-style: preserve-3d;
        }

        /* Card Elevation & Hover Micro-interactions */
        .card-lift {
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: transform, box-shadow;
            backface-visibility: hidden;
        }

        .card-lift:hover {
            transform: translateY(-5px) !important;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        }

        .btn-lift {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-lift:hover {
            transform: translateY(-1.5px);
            filter: brightness(1.05);
        }
        .btn-lift:active {
            transform: translateY(0.5px) scale(0.98);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('dist/output.css') }}?v={{ time() }}">
    @stack('head')
    @include('partials.inp-optimizer')
</head>
<body class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] text-slate-900 selection:bg-teal-100 selection:text-teal-900 flex flex-col min-h-screen m-0 p-0">

    {{-- Scroll Progress Bar --}}
    @if(!request()->boolean('iframe'))
        <div id="scroll-progress" class="fixed top-0 left-0 right-0 h-1 bg-teal-500 z-[60]" style="width: 0%"></div>
    @endif

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.ambient')
        @include('partials.navbar')
    @endif

    <main class="flex-grow w-full bg-[#FAFAF9] min-h-[60vh]">
        @yield('content')
    </main>

    @if(!($minimalLayout ?? false) && !request()->boolean('iframe'))
        @include('partials.footer')
    @endif

    {{-- Back to Top Button --}}
    @if(!request()->boolean('iframe'))
        <button id="back-to-top" aria-label="Back to top">↑</button>
    @endif

    <script src="{{ asset('js/scroll-reveal.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}?v={{ time() }}"></script>
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
    @endif

    <!-- Firebase JS SDK (v9 Compat) for Real Native FCM Web Push -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

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
                                         '/images/logo.png';

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
            if (input && input.value) {
                navigator.clipboard.writeText(input.value).then(() => {
                    if (window.Toast) {
                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم نسخ رمز FCM للحافظة!' : 'FCM Token copied to clipboard!'));
                    }
                }).catch(() => {
                    input.select();
                    document.execCommand('copy');
                    if (window.Toast) {
                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم نسخ رمز FCM للحافظة!' : 'FCM Token copied to clipboard!'));
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

    @stack('scripts')
</body>
</html>
