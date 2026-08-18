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
    <title>{{ $pageTitle ?? config('app.name') }}</title>
    @isset($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endisset
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Critical styles to eliminate white flash and apply Cairo font family */
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap');

        html, body, button, input, select, textarea, .font-sans, .font-heading {
            font-family: 'Cairo', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
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

        .animate-fade-in-up {
            animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out forwards;
        }

        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.12s; }
        .stagger-3 { animation-delay: 0.19s; }
        .stagger-4 { animation-delay: 0.26s; }

        /* Card Elevation & Hover Micro-interactions */
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
</head>
<body class="font-sans antialiased overflow-x-hidden bg-[#FAFAF9] text-slate-900 selection:bg-teal-100 selection:text-teal-900 flex flex-col min-h-screen m-0 p-0">

    {{-- Scroll Progress Bar --}}
    @unless(request()->boolean('iframe'))
        <div id="scroll-progress" class="fixed top-0 left-0 right-0 h-1 bg-teal-500 z-[60]" style="width: 0%"></div>
    @endunless

    @unless(($minimalLayout ?? false) || request()->boolean('iframe'))
        @include('partials.ambient')
        @include('partials.navbar')
    @endunless

    <main class="flex-grow w-full bg-[#FAFAF9] min-h-[60vh]">
        @yield('content')
    </main>

    @unless(($minimalLayout ?? false) || request()->boolean('iframe'))
        @include('partials.footer')
    @endunless

    {{-- Back to Top Button --}}
    @unless(request()->boolean('iframe'))
        <button id="back-to-top" aria-label="Back to top">↑</button>
    @endunless

    <script src="{{ asset('js/scroll-reveal.js') }}"></script>
    <script src="{{ asset('js/toast.js') }}?v={{ time() }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                if (window.Toast) window.Toast.success(@json(session('success')));
            @endif
            @if(session('error'))
                if (window.Toast) window.Toast.error(@json(session('error')));
            @endif
            @if(session('warning'))
                if (window.Toast) window.Toast.warning(@json(session('warning')));
            @endif
            @if(session('info'))
                if (window.Toast) window.Toast.info(@json(session('info')));
            @endif
        });
    </script>

    @auth
        @unless(request()->boolean('iframe'))
        {{-- FCM Push Notification Permission Popup Modal --}}
        <div id="fcm-permission-modal" class="hidden fixed bottom-6 right-6 left-6 sm:left-auto sm:max-w-md bg-slate-900/95 backdrop-blur-md text-white p-6 rounded-3xl shadow-2xl border border-slate-700/80 z-50 transition-all duration-300">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-400 border border-teal-500/40 flex items-center justify-center text-2xl shrink-0">
                    🔔
                </div>
                <div class="space-y-2 flex-1">
                    <h4 class="font-heading font-bold text-sm sm:text-base text-white">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل إشعارات الفصول والبث المباشر' : 'Enable Live Push Notifications' }}
                    </h4>
                    <p class="text-xs text-slate-300 leading-relaxed font-mono">
                        {{ app()->getLocale() === 'ar' ? 'اشترك في الإشعارات المباشرة ليصلك تنبيه قبل 24 ساعة من المواعيد وتذكيرات الواجبات وتنبيهات الإدارة.' : 'Stay updated with instant alerts for upcoming live sessions, 24h assignment deadlines, and admin approvals.' }}
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <button id="btn-enable-fcm" type="button" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-xl text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>✨</span> {{ app()->getLocale() === 'ar' ? 'السماح بالإشعارات الآن' : 'Allow Notifications Now' }}
                        </button>
                        <button id="btn-dismiss-fcm" type="button" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs transition-all cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'لاحقاً' : 'Later' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endunless

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

            const fcmTokenKey = 'elite_fcm_token_' + {{ auth()->id() }};
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
                    console.log('[FCM] Token synced silently for user:', {{ auth()->id() }});
                }
            })
            .catch(() => {});
        };

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('fcm-permission-modal');
            const btnEnable = document.getElementById('btn-enable-fcm');
            const btnDismiss = document.getElementById('btn-dismiss-fcm');

            // Initialize Native Firebase Messaging Web SDK
            let messaging = null;
            if (typeof firebase !== 'undefined') {
                try {
                    if (!firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                        console.log('[FCM] Firebase initialized.');
                        console.log('NotificationManager initialized (push mode).');
                    }
                    if (firebase.messaging.isSupported()) {
                        messaging = firebase.messaging();
                        
                        // Register Service Worker
                        if ('serviceWorker' in navigator) {
                            navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}').then((reg) => {
                                console.log('[FCM] Service Worker registered:', '{{ url('/') }}');

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

                        // Foreground Real-Time Firebase Message Listener
                        messaging.onMessage((payload) => {
                            console.log('[FCM] Message received:', payload);

                            const title = (payload.notification && payload.notification.title) ||
                                          (payload.data && payload.data.title) ||
                                          (payload.data && payload.data.heading) ||
                                          '🔔 Firebase Push Notification';

                            const body = (payload.notification && payload.notification.body) ||
                                         (payload.data && payload.data.body) ||
                                         (payload.data && payload.data.message) ||
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

                            // Dispatch custom DOM event for UI stream updates
                            window.dispatchEvent(new CustomEvent('fcm-realtime-message', { detail: { notification: { title, body, image: icon }, data: payload.data || {} } }));
                        });
                    }
                } catch (err) {
                    console.warn('Firebase Messaging init fallback:', err);
                }
            }

            function getSavedToken() {
                const fcmTokenKey = 'elite_fcm_token_' + {{ auth()->id() }};
                return localStorage.getItem(fcmTokenKey);
            }

            const savedToken = getSavedToken();
            if (savedToken) {
                sendFcmTokenToServer(savedToken);
            }

            // Show permission popup directly if permission is not decided yet
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted' && messaging) {
                            messaging.getToken().then((token) => {
                                if (token) sendFcmTokenToServer(token);
                            }).catch(() => {});
                        } else if (permission === 'default' && modal) {
                            modal.classList.remove('hidden');
                        }
                    }).catch(() => {
                        if (modal) modal.classList.remove('hidden');
                    });
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

            const fcmTokenKey = 'elite_fcm_token_' + {{ auth()->id() }};
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
                if (window.Toast) window.Toast.error('Browser does not support Web Push notifications');
                return;
            }

            Notification.requestPermission().then(async (permission) => {
                if (permission !== 'granted') {
                    if (window.Toast) window.Toast.warning('Notification permission was not granted');
                    return;
                }

                if (typeof firebase !== 'undefined') {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        if (firebase.messaging.isSupported()) {
                            const messaging = firebase.messaging();
                            const reg = await navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}');
                            await navigator.serviceWorker.ready;

                            const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                            const opts = { serviceWorkerRegistration: reg };
                            if (vapidKey) opts.vapidKey = vapidKey;

                            messaging.getToken(opts).then((token) => {
                                if (token) {
                                    sendFcmTokenToServer(token);
                                    if (window.Toast) {
                                        window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم جلب رمز FCM الحقيقي مباشرة من فايبربيس بنجاح!' : 'Live Firebase Token retrieved from Google successfully!'));
                                    }
                                } else {
                                    if (window.Toast) window.Toast.warning('No FCM registration token returned by Firebase');
                                }
                            }).catch(err => {
                                console.error('Firebase getToken error:', err);
                                const saved = localStorage.getItem('elite_fcm_token_' + {{ auth()->id() }});
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
    @endauth

    @stack('scripts')
</body>
</html>
