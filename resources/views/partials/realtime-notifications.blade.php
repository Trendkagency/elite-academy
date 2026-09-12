@if(auth()->check() && !request()->boolean('iframe'))
    {{-- ========================================================================= --}}
    {{-- ELITE ACADEMY — REAL-TIME NOTIFICATION ENGINE & SOUND SYNTHESIZER          --}}
    {{-- ========================================================================= --}}

    {{-- Floating Notification Quick Access Bell (Bottom-Left / Bottom-Right) --}}
    <div id="fcm-floating-bell-wrapper" class="fixed bottom-6 start-6 z-40">
        <button id="fcm-floating-bell-btn" type="button"
            title="{{ __('Notification Settings') }}"
            class="group relative flex items-center justify-center w-12 h-12 rounded-full bg-slate-900/90 text-teal-400 border border-teal-500/30 shadow-xl backdrop-blur-md hover:bg-slate-800 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
            <i class="fa-solid fa-bell text-lg group-hover:rotate-12 transition-transform"></i>
            <span id="fcm-status-indicator" class="absolute -top-1 -end-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-500 border-2 border-slate-900"></span>
            </span>
        </button>
    </div>

    {{-- Interactive Permission Alert Modal Card --}}
    <div id="fcm-permission-modal"
        class="fixed bottom-6 start-6 end-6 sm:end-auto sm:max-w-md z-50 transform transition-all duration-500 ease-out opacity-0 translate-y-8 pointer-events-none">
        <div class="relative overflow-hidden bg-slate-950/95 backdrop-blur-xl text-white p-6 rounded-3xl shadow-2xl border border-teal-500/30 ring-1 ring-white/10">
            {{-- Decorative background glow --}}
            <div class="absolute -top-16 -end-16 w-36 h-36 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -start-16 w-36 h-36 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Close Button --}}
            <button id="btn-close-fcm-modal" type="button"
                class="absolute top-4 end-4 text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800/80 transition-colors cursor-pointer"
                aria-label="{{ __('Close') }}">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="flex items-start gap-4">
                {{-- Animated Bell Icon --}}
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500/20 to-emerald-500/10 text-teal-400 border border-teal-500/30 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-bell animate-bounce" style="animation-duration: 2.5s;"></i>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2">
                        <h4 class="font-heading font-bold text-base text-white">
                            {{ __('Enable Push Notifications?') }}
                        </h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30">
                            {{ __('Live Alerts') }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('Get real-time browser alerts for live session reminders, 24h assignment deadlines, and course announcements even when this tab is in the background.') }}
                    </p>

                    {{-- Features mini-list --}}
                    <div class="grid grid-cols-2 gap-1.5 py-1 text-[11px] text-slate-300 font-medium">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-teal-400 text-[10px]"></i>
                            <span>{{ __('Live Sessions') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-teal-400 text-[10px]"></i>
                            <span>{{ __('Assignment Deadlines') }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons: YES / NO --}}
                    <div class="flex items-center gap-2.5 pt-2">
                        {{-- YES Button --}}
                        <button id="btn-enable-fcm" type="button"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-teal-400 to-emerald-500 hover:from-teal-300 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs shadow-lg shadow-teal-500/20 hover:shadow-teal-500/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span id="btn-enable-icon"><i class="fa-solid fa-check"></i></span>
                            <span id="btn-enable-text">{{ __('Yes, Allow Notifications') }}</span>
                        </button>

                        {{-- NO Button --}}
                        <button id="btn-dismiss-fcm" type="button"
                            class="px-4 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold rounded-xl text-xs border border-slate-700/60 transition-all cursor-pointer">
                            {{ __('No, Later') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Help Modal if Permission was Blocked in Browser Settings --}}
    <div id="fcm-blocked-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="relative bg-slate-900 text-white p-6 rounded-3xl max-w-md w-full border border-rose-500/30 shadow-2xl">
            <div class="flex items-center gap-3 text-rose-400 mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-bell-slash"></i>
                </div>
                <h4 class="font-bold text-base text-white">{{ __('Notifications Are Blocked') }}</h4>
            </div>
            <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                {{ __('Your browser has notifications blocked for this site. To receive alerts, please allow notifications in your browser settings:') }}
            </p>
            <div class="bg-slate-950/60 rounded-xl p-3 border border-slate-800 text-xs text-slate-300 space-y-2 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold text-[11px]">1</span>
                    <span>{{ __('Click the lock / tune icon (🔒) in your browser address bar.') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold text-[11px]">2</span>
                    <span>{{ __('Find "Notifications" and switch it to "Allow".') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold text-[11px]">3</span>
                    <span>{{ __('Refresh the page to activate.') }}</span>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('fcm-blocked-modal').classList.add('hidden')"
                class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs transition-colors cursor-pointer">
                {{ __('Understood') }}
            </button>
        </div>
    </div>

    <!-- Firebase JS SDK (v9 Compat) for Web Push Notifications -->
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

        // ─────────────────────────────────────────────────────────────────────────
        // 1. Crystal-Clear Web Audio API Notification Chime (Zero External Files)
        // ─────────────────────────────────────────────────────────────────────────
        window.playNotificationChime = function () {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;

                // Primary Tone: D5 (587.33 Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.2, now + 0.04);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.35);

                // Harmonious Second Tone: A5 (880 Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.12);
                gain2.gain.setValueAtTime(0, now + 0.12);
                gain2.gain.linearRampToValueAtTime(0.25, now + 0.16);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.12);
                osc2.stop(now + 0.55);
            } catch (err) {
                console.debug('[Audio] Chime playback note:', err);
            }
        };

        // ─────────────────────────────────────────────────────────────────────────
        // 2. Real-Time Notification Poller & Dispatch Engine
        // ─────────────────────────────────────────────────────────────────────────
        (function () {
            const currentUserId = "{{ auth()->id() ?? 0 }}";
            const fcmTokenKey = 'elite_fcm_token_' + currentUserId;
            const dismissedKey = 'fcm_prompt_dismissed_until_' + currentUserId;

            const modal = document.getElementById('fcm-permission-modal');
            const btnEnable = document.getElementById('btn-enable-fcm');
            const btnDismiss = document.getElementById('btn-dismiss-fcm');
            const btnClose = document.getElementById('btn-close-fcm-modal');
            const floatingBellBtn = document.getElementById('fcm-floating-bell-btn');
            const statusIndicator = document.getElementById('fcm-status-indicator');
            const blockedModal = document.getElementById('fcm-blocked-modal');

            let messaging = null;
            let latestNotificationId = 0;
            let isPollingActive = false;

            // Global Real-Time Poller
            window.pollNotifications = async function (forceImmediate = false) {
                if (isPollingActive && !forceImmediate) return;
                isPollingActive = true;

                try {
                    const checkUrl = '{{ route('ajax.notifications.check') }}?since_id=' + latestNotificationId;
                    const res = await fetch(checkUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) {
                        isPollingActive = false;
                        return;
                    }

                    const data = await res.json();
                    if (data && data.success) {
                        const previousLatest = latestNotificationId;
                        latestNotificationId = Math.max(latestNotificationId, data.latest_id || 0);

                        // Broadcast latest state to Navbar Bell and any open listeners
                        window.dispatchEvent(new CustomEvent('notifications-updated', {
                            detail: {
                                unread_count: data.unread_count,
                                recent_notifications: data.recent_notifications,
                                latest_id: data.latest_id
                            }
                        }));

                        // If new notifications arrived (and this wasn't just the initial page load scan)
                        if (previousLatest > 0 && data.has_new && data.new_notifications && data.new_notifications.length > 0) {
                            window.playNotificationChime();

                            data.new_notifications.forEach(n => {
                                // Show in-app Toast
                                if (window.Toast) {
                                    window.Toast.info(n.body, n.title);
                                }

                                // Native desktop push if permitted
                                if ('Notification' in window && Notification.permission === 'granted') {
                                    try {
                                        new Notification(n.title, {
                                            body: n.body,
                                            icon: '/images/logo_500.webp'
                                        });
                                    } catch (e) {}
                                }

                                // Dispatch event for UI feeds (Student portal, navbar, etc.)
                                window.dispatchEvent(new CustomEvent('new-notification-received', {
                                    detail: n
                                }));
                            });
                        }
                    }
                } catch (e) {
                    console.debug('[Notifications] Poll check note:', e);
                } finally {
                    isPollingActive = false;
                }
            };

            // Global Instant Test Push Trigger
            window.triggerTestPush = async function (buttonEl) {
                if (buttonEl) {
                    buttonEl.disabled = true;
                    buttonEl.classList.add('opacity-60', 'cursor-not-allowed');
                }

                if (window.Toast) {
                    window.Toast.info(@json(app()->getLocale() === 'ar' ? 'جارٍ إرسال الإشعار وتجربة البث المباشر...' : 'Dispatching real-time test notification...'));
                }

                try {
                    const res = await fetch('{{ route('ajax.notifications.test-push') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    if (data.success) {
                        // Immediately poll backend to receive and display notification in real time!
                        setTimeout(() => {
                            window.pollNotifications(true);
                        }, 250);
                    } else {
                        if (window.Toast) {
                            window.Toast.error(data.message || 'Error triggering test push');
                        }
                    }
                } catch (err) {
                    if (window.Toast) {
                        window.Toast.error('Network error triggering test push');
                    }
                } finally {
                    if (buttonEl) {
                        setTimeout(() => {
                            buttonEl.disabled = false;
                            buttonEl.classList.remove('opacity-60', 'cursor-not-allowed');
                        }, 1800);
                    }
                }
            };

            // Global Mark as Read Helpers
            window.markNotificationAsRead = async function (id) {
                try {
                    const res = await fetch(`{{ url('/ajax/notifications') }}/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.pollNotifications(true);
                    }
                } catch (e) {}
            };

            window.markAllNotificationsAsRead = async function () {
                try {
                    const res = await fetch('{{ route('ajax.notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (window.Toast) {
                            window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم تمييز جميع الإشعارات كمقروءة' : 'All notifications marked as read'));
                        }
                        window.pollNotifications(true);
                    }
                } catch (e) {}
            };

            // Initialize Firebase Messaging safely
            function initFirebase() {
                if (typeof firebase !== 'undefined' && firebase.messaging && firebase.messaging.isSupported()) {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(window.firebaseConfig);
                        }
                        messaging = firebase.messaging();
                        attachForegroundListener();
                        return true;
                    } catch (e) {
                        console.warn('[FCM] Init error:', e);
                    }
                }
                return false;
            }

            // Handle FCM Foreground Messages
            function attachForegroundListener() {
                if (!messaging) return;
                messaging.onMessage((payload) => {
                    const title = (payload.notification && payload.notification.title) ||
                        (payload.data && payload.data.title) ||
                        '{{ __("New Notification") }}';

                    const body = (payload.notification && payload.notification.body) ||
                        (payload.data && payload.data.body) ||
                        '';

                    const icon = (payload.notification && payload.notification.image) ||
                        (payload.data && payload.data.icon) ||
                        '/images/logo_500.webp';

                    window.playNotificationChime();

                    if (window.Toast) {
                        window.Toast.info(body, title);
                    }

                    if ('Notification' in window && Notification.permission === 'granted') {
                        try {
                            if ('serviceWorker' in navigator) {
                                navigator.serviceWorker.ready.then((reg) => {
                                    reg.showNotification(title, {
                                        body: body,
                                        icon: icon,
                                        badge: icon,
                                        data: payload.data || { url: '/student-portal' }
                                    });
                                }).catch(() => {
                                    new Notification(title, { body: body, icon: icon });
                                });
                            } else {
                                new Notification(title, { body: body, icon: icon });
                            }
                        } catch (e) { }
                    }

                    window.dispatchEvent(new CustomEvent('fcm-realtime-message', {
                        detail: {
                            notification: { title, body, image: icon },
                            data: payload.data || {}
                        }
                    }));

                    // Immediately poll backend to sync feed
                    window.pollNotifications(true);
                });
            }

            // Send Token to Laravel Backend
            window.sendFcmTokenToServer = function (token) {
                if (!token) return;
                localStorage.setItem(fcmTokenKey, token);

                const tokenInputs = document.querySelectorAll('#userFcmTokenInput');
                tokenInputs.forEach(input => { input.value = token; });

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
                            updateIndicatorState('granted');
                        }
                    })
                    .catch(() => { });
            };

            // Request Live Firebase Token
            window.requestLiveFirebaseToken = async function () {
                if (!('Notification' in window) || !('PushManager' in window) || !('serviceWorker' in navigator)) {
                    if (window.Toast) window.Toast.error(@json(app()->getLocale() === 'ar' ? 'المتصفح لا يدعم إشعارات المتصفح الفورية' : 'Browser does not support Web Push notifications'));
                    return;
                }

                if (btnEnable) {
                    btnEnable.disabled = true;
                    const iconSpan = document.getElementById('btn-enable-icon');
                    const textSpan = document.getElementById('btn-enable-text');
                    if (iconSpan) iconSpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                    if (textSpan) textSpan.innerText = @json(app()->getLocale() === 'ar' ? 'جارٍ التفعيل...' : 'Enabling...');
                }

                try {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        initFirebase();
                        if (messaging) {
                            const reg = await navigator.serviceWorker.register('{{ url('/firebase-messaging-sw.js') }}', { scope: '/' });
                            await navigator.serviceWorker.ready;

                            const vapidKey = "{{ config('fcm.web_config.vapid_key') }}";
                            const opts = { serviceWorkerRegistration: reg };
                            if (vapidKey) opts.vapidKey = vapidKey;

                            const token = await messaging.getToken(opts);
                            if (token) {
                                sendFcmTokenToServer(token);
                                hideModal();
                                updateIndicatorState('granted');

                                if (window.Toast) {
                                    window.Toast.success(@json(app()->getLocale() === 'ar' ? 'تم تفعيل إشعارات المتصفح بنجاح! 🔔' : 'Push notifications enabled successfully! 🔔'));
                                }
                            }
                        }
                    } else if (permission === 'denied') {
                        hideModal();
                        updateIndicatorState('denied');
                        if (window.Toast) {
                            window.Toast.warning(@json(app()->getLocale() === 'ar' ? 'تم حظر الإشعارات في المتصفح' : 'Notifications were blocked in your browser'));
                        }
                    } else {
                        hideModal();
                    }
                } catch (err) {
                    console.error('[FCM] Permission/Token error:', err);
                    if (window.Toast) window.Toast.error(err ? err.message : 'Error enabling notifications');
                } finally {
                    if (btnEnable) {
                        btnEnable.disabled = false;
                        const iconSpan = document.getElementById('btn-enable-icon');
                        const textSpan = document.getElementById('btn-enable-text');
                        if (iconSpan) iconSpan.innerHTML = '<i class="fa-solid fa-check"></i>';
                        if (textSpan) textSpan.innerText = @json(app()->getLocale() === 'ar' ? 'نعم، تفعيل الإشعارات' : 'Yes, Allow Notifications');
                    }
                }
            };

            // UI Helpers
            function showModal() {
                if (!modal) return;
                modal.classList.remove('opacity-0', 'translate-y-8', 'pointer-events-none');
                modal.classList.add('opacity-100', 'translate-y-0');
            }

            function hideModal() {
                if (!modal) return;
                modal.classList.remove('opacity-100', 'translate-y-0');
                modal.classList.add('opacity-0', 'translate-y-8', 'pointer-events-none');
            }

            function dismissPrompt(days = 2) {
                hideModal();
                const expiry = Date.now() + (days * 24 * 60 * 60 * 1000);
                localStorage.setItem(dismissedKey, expiry.toString());
            }

            function updateIndicatorState(permission) {
                if (!statusIndicator) return;
                if (permission === 'granted') {
                    statusIndicator.innerHTML = '<span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-slate-900"></span>';
                } else if (permission === 'denied') {
                    statusIndicator.innerHTML = '<span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-slate-900"></span>';
                } else {
                    statusIndicator.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-500 border-2 border-slate-900"></span>';
                }
            }

            // Document Ready Setup
            document.addEventListener('DOMContentLoaded', function () {
                initFirebase();

                const savedToken = localStorage.getItem(fcmTokenKey);
                if (savedToken) {
                    sendFcmTokenToServer(savedToken);
                }

                if ('Notification' in window) {
                    updateIndicatorState(Notification.permission);

                    if (Notification.permission === 'default') {
                        const dismissedUntil = localStorage.getItem(dismissedKey);
                        const now = Date.now();
                        if (!dismissedUntil || now > parseInt(dismissedUntil, 10)) {
                            setTimeout(showModal, 1500);
                        }
                    } else if (Notification.permission === 'granted') {
                        if (!savedToken && messaging) {
                            window.requestLiveFirebaseToken();
                        }
                    }
                }

                // Button Event Listeners
                if (btnEnable) {
                    btnEnable.addEventListener('click', window.requestLiveFirebaseToken);
                }

                if (btnDismiss) {
                    btnDismiss.addEventListener('click', function () {
                        dismissPrompt(2);
                    });
                }

                if (btnClose) {
                    btnClose.addEventListener('click', function () {
                        dismissPrompt(1);
                    });
                }

                if (floatingBellBtn) {
                    floatingBellBtn.addEventListener('click', function () {
                        if ('Notification' in window) {
                            if (Notification.permission === 'granted') {
                                if (window.Toast) {
                                    window.Toast.info(@json(app()->getLocale() === 'ar' ? 'الإشعارات مفعلة بالفعل على هذا الجهاز 🔔' : 'Push notifications are already active on this device 🔔'));
                                }
                            } else if (Notification.permission === 'denied') {
                                if (blockedModal) blockedModal.classList.remove('hidden');
                            } else {
                                showModal();
                            }
                        }
                    });
                }

                // Initial poll to load current unread counts and latest ID
                setTimeout(() => {
                    window.pollNotifications(true);
                }, 800);

                // Polling loop every 5.5 seconds for true real-time delivery
                setInterval(() => {
                    window.pollNotifications(false);
                }, 5500);

                // Immediate poll on tab focus
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        window.pollNotifications(true);
                    }
                });
            });
        })();
    </script>
@endif
