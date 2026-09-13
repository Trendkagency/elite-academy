
        window.firebaseConfig = {
            apiKey: "AIzaSyCmAS3q2VNvCbKhrfKtC8hn163GX7116Ns",
            authDomain: "elite-academy-67a15.firebaseapp.com",
            projectId: "elite-academy-67a15",
            storageBucket: "elite-academy-67a15.firebasestorage.app",
            messagingSenderId: "53377882422",
            appId: "1:53377882422:web:dddcb2f63b4fcc089f7b97"
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
            const currentUserId = "9";
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
                    const checkUrl = 'http://localhost/elite-academy/public/ajax/notifications/check?since_id=' + latestNotificationId;
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
                    window.Toast.info("Dispatching real-time test notification...");
                }

                try {
                    const res = await fetch('http://localhost/elite-academy/public/ajax/notifications/test-push', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y',
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
                    const res = await fetch(`http://localhost/elite-academy/public/ajax/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y',
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
                    const res = await fetch('http://localhost/elite-academy/public/ajax/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (window.Toast) {
                            window.Toast.success("All notifications marked as read");
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
                        'New Notification';

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

                fetch('http://localhost/elite-academy/public/ajax/notifications/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
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
            window.requestLiveFirebaseToken = async function (isUserInitiated = true) {
                if (!('Notification' in window) || !('PushManager' in window) || !('serviceWorker' in navigator)) {
                    if (isUserInitiated && window.Toast) {
                        window.Toast.error("Browser does not support Web Push notifications");
                    }
                    return;
                }

                if (btnEnable && isUserInitiated) {
                    btnEnable.disabled = true;
                    const iconSpan = document.getElementById('btn-enable-icon');
                    const textSpan = document.getElementById('btn-enable-text');
                    if (iconSpan) iconSpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                    if (textSpan) textSpan.innerText = "Enabling...";
                }

                try {
                    const permission = (isUserInitiated && Notification.permission !== 'granted')
                        ? await Notification.requestPermission()
                        : Notification.permission;

                    if (permission === 'granted') {
                        initFirebase();
                        if (messaging) {
                            const appBasePath = (() => {
                                const match = window.location.pathname.match(/^(\/[^\/]+\/public)/);
                                return match ? (match[1] + '/') : '/elite-academy/public/';
                            })();
                            const swUrl = (() => {
                                const match = window.location.pathname.match(/^(\/[^\/]+\/public)/);
                                return match ? (window.location.origin + match[1] + '/firebase-messaging-sw.js') : 'http://localhost/elite-academy/public/firebase-messaging-sw.js';
                            })();
                            let reg;
                            try {
                                reg = await navigator.serviceWorker.register(swUrl, { scope: appBasePath });
                            } catch (scopeErr) {
                                console.warn('[FCM] Scope register fallback to default:', scopeErr);
                                reg = await navigator.serviceWorker.register(swUrl);
                            }
                            await navigator.serviceWorker.ready;

                            const vapidKey = "BM-m60kE0EKBkhRfnv-Lq4GH2X2NRZ15ir9QcSPJO_xKOHavHtTrTTbIvDShwxNcrgaMKLYU02fz0jgc7KfwZPA";
                            const opts = { serviceWorkerRegistration: reg };
                            if (vapidKey) opts.vapidKey = vapidKey;

                            const token = await messaging.getToken(opts);
                            if (token) {
                                sendFcmTokenToServer(token);
                                hideModal();
                                updateIndicatorState('granted');

                                if (isUserInitiated && window.Toast) {
                                    window.Toast.success("Push notifications enabled successfully! \ud83d\udd14");
                                }
                            }
                        }
                    } else if (permission === 'denied') {
                        hideModal();
                        updateIndicatorState('denied');
                        if (isUserInitiated && window.Toast) {
                            window.Toast.warning("Notifications were blocked in your browser");
                        }
                    } else {
                        hideModal();
                    }
                } catch (err) {
                    console.warn('[FCM] Permission/Token error:', err);
                    if (isUserInitiated && window.Toast) {
                        window.Toast.error(err ? err.message : 'Error enabling notifications');
                    }
                } finally {
                    if (btnEnable) {
                        btnEnable.disabled = false;
                        const iconSpan = document.getElementById('btn-enable-icon');
                        const textSpan = document.getElementById('btn-enable-text');
                        if (iconSpan) iconSpan.innerHTML = '<i class="fa-solid fa-check"></i>';
                        if (textSpan) textSpan.innerText = "Yes, Allow Notifications";
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
                            window.requestLiveFirebaseToken(false);
                        }
                    }
                }

                // Button Event Listeners
                if (btnEnable) {
                    btnEnable.addEventListener('click', function () {
                        window.requestLiveFirebaseToken(true);
                    });
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
                                    window.Toast.info("Push notifications are already active on this device \ud83d\udd14");
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
    