<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['session', 'user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['session', 'user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>

<div id="inSystemMeetingContainer" class="relative w-full max-w-6xl mx-auto bg-slate-950 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden text-white font-sans transition-all duration-300">
    
    
    <div class="px-6 py-4 bg-slate-900/90 border-b border-slate-800/80 flex flex-wrap items-center justify-between gap-4 backdrop-blur-md relative z-30">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                🎓
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span id="meetingStatusBadge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span id="meetingStatusText"><?php echo e($isRtl ? 'جاري الاتصال...' : 'CONNECTING...'); ?></span>
                    </span>
                    <span class="text-xs font-mono text-slate-400">
                        ID: <?php echo e('SES-' . str_pad((string) $session->id, 5, '0', STR_PAD_LEFT)); ?>

                    </span>
                </div>
                <h3 class="font-heading font-bold text-base sm:text-lg text-white tracking-tight mt-0.5">
                    <?php echo e($session->title ?: ($isRtl ? 'حصة البث المباشر التفاعلية' : 'Interactive Live Stream Session')); ?>

                </h3>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs font-mono">
            <div class="hidden sm:flex flex-col items-end text-slate-300">
                <span>👨‍🏫 <?php echo e($session->teacherProfile?->user?->name ?: 'Dr. Instructor'); ?></span>
                <span class="text-[11px] text-slate-400">📚 <?php echo e($session->subject?->name ?: 'Physics'); ?></span>
            </div>
            <div class="px-3.5 py-1.5 bg-slate-800/80 rounded-xl border border-slate-700/70 text-teal-300 font-bold flex items-center gap-2">
                <span>⏱️</span>
                <span id="meetingDurationTimer">00:00:00</span>
            </div>
            <button onclick="leaveMeetingSession()" class="btn-lift px-4 py-2 bg-rose-600/80 hover:bg-rose-600 text-white rounded-xl font-bold cursor-pointer transition-all flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                <span>🚪</span> <?php echo e($isRtl ? 'مغادرة الحصة' : 'Leave Session'); ?>

            </button>
        </div>
    </div>

    
    <div class="relative w-full aspect-video min-h-[420px] max-h-[680px] bg-slate-950 flex items-center justify-center overflow-hidden select-none">
        
        
        <div id="dynamicWatermark" class="absolute inset-0 pointer-events-none z-30 overflow-hidden flex items-center justify-center opacity-70 select-none">
            <div id="watermarkContent" class="text-teal-300 font-mono font-black text-xs sm:text-sm tracking-widest uppercase transition-all duration-1000 transform -rotate-12 bg-slate-950/80 px-4 py-2 rounded-xl border border-teal-500/50 shadow-2xl backdrop-blur-md">
                🛡️ <?php echo e($user->name); ?> • <?php echo e('STU-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)); ?> • <?php echo e('SES-' . str_pad((string) $session->id, 5, '0', STR_PAD_LEFT)); ?> • IP: <?php echo e(request()->ip()); ?> • <span id="watermarkClock"></span>
            </div>
        </div>

        
        <div id="screenRecordSecurityOverlay" class="hidden absolute inset-0 z-50 bg-slate-950/95 backdrop-blur-3xl flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-3xl shadow-xl animate-bounce">
                🔒
            </div>
            <div class="space-y-1 max-w-md">
                <h4 id="securityBlurTitle" class="font-heading font-bold text-lg text-white">
                    <?php echo e($isRtl ? 'محتوى محمي — تم رصد محاولة تسجيل أو تصوير الشاشة' : 'Protected Content — Screen Recording/Capture Detected'); ?>

                </h4>
                <p id="securityBlurMsg" class="text-xs font-mono text-slate-400">
                    <?php echo e($isRtl ? 'لحماية الملكية الفكرية، تم تعتيم الشاشة وحظر العرض فوراً. يتم تسجيل بيانات الطالب والعنوان الرقمي (IP) لدى الإدارة.' : 'To protect IP rights, playback is obfuscated. Security events are audited with your identity.'); ?>

                </p>
            </div>
            <button onclick="resumeMeetingPlayback()" class="mt-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-500 px-6 py-2.5 rounded-xl shadow-lg transition-all cursor-pointer font-mono">
                <?php echo e($isRtl ? 'العودة لمتابعة الحصة المباشرة ▶' : 'Resume Session Playback ▶'); ?>

            </button>
        </div>

        
        <div id="meetingStateOverlay" class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div id="meetingSpinner" class="w-14 h-14 border-4 border-teal-500/30 border-t-teal-400 rounded-full animate-spin"></div>
            <div class="space-y-1">
                <h4 id="overlayTitle" class="font-heading font-extrabold text-xl text-white">
                    <?php echo e($isRtl ? 'جاري تجهيز غرفة الاجتماع الأكاديمي المباشر...' : 'Preparing Secure In-System Live Session...'); ?>

                </h4>
                <p id="overlayMessage" class="text-xs font-mono text-slate-400 max-w-md">
                    <?php echo e($isRtl ? 'يتم التحقق من هوية الطالب، رصيد الحصص، والواجبات المطلوبة...' : 'Verifying enrollment, timing rules, and security tokens...'); ?>

                </p>
            </div>
            <button id="btnRetryMeeting" onclick="initializeInSystemMeeting()" class="hidden px-5 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold font-mono transition-all shadow-lg">
                🔄 <?php echo e($isRtl ? 'إعادة المحاولة' : 'Retry Connection'); ?>

            </button>
        </div>

        
        <iframe id="embeddedMeetingFrame" src="about:blank" class="w-full h-full border-0 relative z-10 hidden" allow="camera *; microphone *; display-capture *; autoplay *; encrypted-media *; fullscreen *" allowfullscreen></iframe>

        
        <div id="externalMeetingLauncher" class="hidden w-full h-full flex flex-col items-center justify-center p-6 text-center space-y-5 bg-slate-950 relative z-10">
            <div class="w-16 h-16 rounded-3xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-3xl shadow-xl shadow-teal-500/10">
                📹
            </div>
            <div class="space-y-1.5 max-w-lg">
                <h4 id="externalPlatformTitle" class="font-heading font-extrabold text-xl sm:text-2xl text-white">
                    <?php echo e($isRtl ? 'غرفة البث المباشر — Google Meet' : 'Interactive Live Stream Room'); ?>

                </h4>
                <p class="text-xs font-mono text-slate-400 leading-relaxed">
                    <?php echo e($isRtl ? 'تنبيه الأمان والسرية: يتم تسجيل الحضور والغياب ومدة التواجد تلقائياً في خلفية هذا التبويب. يرجى إبقاء هذه الصفحة مفتوحة.' : 'Attendance & security duration are recorded automatically. Keep this tab open.'); ?>

                </p>
            </div>
            <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <a id="btnExternalLaunch" href="#" target="_blank" rel="noopener noreferrer" class="btn-lift px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 rounded-2xl font-heading font-black text-sm shadow-xl shadow-emerald-500/20 flex items-center gap-2">
                    <span>🟢</span> <span id="externalLaunchBtnText"><?php echo e($isRtl ? 'انضم للبث عبر Google Meet 🚀' : 'Join via Google Meet 🚀'); ?></span>
                </a>
            </div>
        </div>

        
        <div id="zoomMeetingContainer" class="w-full h-full relative z-10 hidden"></div>
    </div>

    
    <div id="securityAlertBanner" class="hidden px-6 py-3 bg-amber-950/80 border-t border-amber-800/80 text-amber-200 text-xs font-mono flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span>⚠️</span>
            <span id="securityAlertText"><?php echo e($isRtl ? 'تنبيه: يرجى إبقاء نافذة الحصة نشطة ومفتوحة دائماً للحفاظ على الحضور.' : 'Warning: Please keep the session tab active to record attendance.'); ?></span>
        </div>
        <button onclick="document.getElementById('securityAlertBanner').classList.add('hidden')" class="text-amber-400 hover:text-white font-bold cursor-pointer">✕</button>
    </div>
</div>

<script>
    let meetingSessionId = <?php echo e($session->id); ?>;
    let meetingAccessToken = null;
    let meetingTokenExpiresAt = null;
    let heartbeatInterval = null;
    let watermarkInterval = null;
    let durationTimerInterval = null;
    let sessionSecondsCount = 0;

    document.addEventListener('DOMContentLoaded', function () {
        initializeInSystemMeeting();

        // Anti-Tamper & Security Detection Event Listeners
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // 1. Intercept Screen Capture & Screen Recording Shortcuts
        window.addEventListener('keydown', function (e) {
            if (
                e.key === 'PrintScreen' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'S' || e.key === 's')) ||
                (e.metaKey && e.shiftKey && (e.key === '4' || e.key === '5' || e.key === '3')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S' || e.key === 'p' || e.key === 'P')) ||
                e.key === 'F12'
            ) {
                e.preventDefault();
                e.stopPropagation();
                if (navigator.clipboard) navigator.clipboard.writeText('');
                logSecurityEvent('SCREEN_CAPTURE_KEY_BLOCKED', { key: e.key });
                triggerSecurityBlur('<?php echo e($isRtl ? "تم رصد محاولة تصوير أو تسجيل الشاشة! المحتوى محمي بحقوق الملكية الفكرية." : "Screen capture/recording attempt blocked! Content is IP protected."); ?>');
                return false;
            }
        });

        // 2. Intercept Display Capture API (getDisplayMedia)
        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            const origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
            navigator.mediaDevices.getDisplayMedia = function() {
                logSecurityEvent('DISPLAY_MEDIA_REQUESTED', {});
                triggerSecurityBlur('<?php echo e($isRtl ? "تم رصد محاولة تسجيل/مشاركة الشاشة عبر المتصفح! تم حجب البث لحماية المحتوى." : "Display screen recording request detected! Stream obfuscated."); ?>');
                return origGetDisplayMedia.apply(this, arguments);
            };
        }

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                logSecurityEvent('TAB_HIDDEN', { path: window.location.pathname });
                showSecurityAlert('<?php echo e($isRtl ? "تنبيه: يرجى إبقاء تبويب الحصة نشطاً ومفتوحاً للحفاظ على تسجيل الحضور." : "Warning: Please keep session tab active to record attendance."); ?>');
            } else {
                logSecurityEvent('TAB_VISIBLE', {});
            }
        });

        window.addEventListener('blur', function () {
            logSecurityEvent('WINDOW_BLURRED', {});
        });

        window.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) {
                logSecurityEvent('FULLSCREEN_EXIT', {});
            }
        });
    });

    function triggerSecurityBlur(msg) {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        const msgEl = document.getElementById('securityBlurMsg');
        if (overlay) {
            if (msgEl && msg) msgEl.innerText = msg;
            overlay.classList.remove('hidden');
        }
    }

    function resumeMeetingPlayback() {
        const overlay = document.getElementById('screenRecordSecurityOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    function initializeInSystemMeeting() {
        const overlay = document.getElementById('meetingStateOverlay');
        const spinner = document.getElementById('meetingSpinner');
        const overlayTitle = document.getElementById('overlayTitle');
        const overlayMessage = document.getElementById('overlayMessage');
        const retryBtn = document.getElementById('btnRetryMeeting');
        const frame = document.getElementById('embeddedMeetingFrame');
        const statusBadge = document.getElementById('meetingStatusBadge');
        const statusText = document.getElementById('meetingStatusText');

        overlay.classList.remove('hidden');
        spinner.classList.remove('hidden');
        retryBtn.classList.add('hidden');
        overlayTitle.innerText = '<?php echo e($isRtl ? "جاري الاتصال بالغرفة المباشرة..." : "Connecting to Live Session..."); ?>';

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                meetingAccessToken = res.body.access_token;
                meetingTokenExpiresAt = res.body.expires_at;

                statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                statusText.innerText = '<?php echo e($isRtl ? "مباشر LIVE" : "LIVE SESSION"); ?>';

                const supportsEmbed = res.body.supports_embedding ?? true;
                const streamUrl = res.body.stream_url || res.body.join_url;
                const provider = res.body.provider || '';

                const isExternalMeeting = (!supportsEmbed) || (streamUrl && (streamUrl.includes('meet.google.com') || streamUrl.includes('teams.microsoft.com')));
                const embeddableUrl = getEmbeddableUrl(streamUrl);

                if (isExternalMeeting && streamUrl) {
                    const launcher = document.getElementById('externalMeetingLauncher');
                    const launchBtn = document.getElementById('btnExternalLaunch');
                    const launchBtnText = document.getElementById('externalLaunchBtnText');
                    const platformTitle = document.getElementById('externalPlatformTitle');

                    if (launchBtn) launchBtn.href = streamUrl;

                    if (platformTitle) {
                        platformTitle.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '<?php echo e($isRtl ? "غرفة البث المباشر التفاعلي — Google Meet" : "Interactive Live Stream — Google Meet"); ?>'
                            : '<?php echo e($isRtl ? "غرفة البث المباشر التفاعلي — Microsoft Teams" : "Interactive Live Stream — Microsoft Teams"); ?>';
                    }

                    if (launchBtnText) {
                        launchBtnText.innerText = (provider === 'google_meet' || (streamUrl && streamUrl.includes('meet.google.com')))
                            ? '<?php echo e($isRtl ? "انضم للبث عبر Google Meet 🚀" : "Join via Google Meet 🚀"); ?>'
                            : '<?php echo e($isRtl ? "انضم للبث عبر Microsoft Teams 🚀" : "Join via Microsoft Teams 🚀"); ?>';
                    }

                    if (launcher) launcher.classList.remove('hidden');
                } else if (embeddableUrl) {
                    frame.src = embeddableUrl;
                    frame.classList.remove('hidden');
                }

                overlay.classList.add('hidden');

                startWatermarkAnimation();
                startHeartbeatLoop();
                startDurationTimer();
            } else {
                spinner.classList.add('hidden');
                retryBtn.classList.remove('hidden');
                statusBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40';
                statusText.innerText = '<?php echo e($isRtl ? "مغلق / غير متاح" : "UNAVAILABLE"); ?>';

                overlayTitle.innerText = res.body.message || '<?php echo e($isRtl ? "تعذر الوصول للحصة المباشرة" : "Unable to Join Live Session"); ?>';
                overlayMessage.innerText = res.body.reason_code ? `Error Code: ${res.body.reason_code}` : '<?php echo e($isRtl ? "تأكد من شروط الموعد، الباقة الفعالة، وتسليم الواجبات." : "Please verify session timing, active package, and prerequisite homework."); ?>';
            }
        })
        .catch(err => {
            spinner.classList.add('hidden');
            retryBtn.classList.remove('hidden');
            overlayTitle.innerText = '<?php echo e($isRtl ? "خطأ في الاتصال بالخادم" : "Server Connection Failure"); ?>';
            overlayMessage.innerText = err.message;
        });
    }

    function startHeartbeatLoop() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        heartbeatInterval = setInterval(function () {
            if (!meetingAccessToken) return;

            fetch(`/ajax/sessions/${meetingSessionId}/meeting/heartbeat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    access_token: meetingAccessToken,
                    expires_at: meetingTokenExpiresAt
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.formatted_duration) {
                    document.getElementById('meetingDurationTimer').innerText = data.formatted_duration;
                } else if (data.session_ended) {
                    alert('<?php echo e($isRtl ? "انتهت هذه الجلسة المباشرة." : "This live session has ended."); ?>');
                    leaveMeetingSession();
                }
            })
            .catch(console.error);
        }, 30000);
    }

    function startDurationTimer() {
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        durationTimerInterval = setInterval(function () {
            sessionSecondsCount++;
            const hrs = String(Math.floor(sessionSecondsCount / 3600)).padStart(2, '0');
            const mins = String(Math.floor((sessionSecondsCount % 3600) / 60)).padStart(2, '0');
            const secs = String(sessionSecondsCount % 60).padStart(2, '0');
            document.getElementById('meetingDurationTimer').innerText = `${hrs}:${mins}:${secs}`;
        }, 1000);
    }

    function startWatermarkAnimation() {
        const watermark = document.getElementById('watermarkContent');
        const clock = document.getElementById('watermarkClock');

        function updateClock() {
            const now = new Date();
            clock.innerText = now.toLocaleTimeString();
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Move watermark position periodically across container
        if (watermarkInterval) clearInterval(watermarkInterval);
        watermarkInterval = setInterval(function () {
            const topPct = Math.floor(Math.random() * 60) + 20;
            const leftPct = Math.floor(Math.random() * 60) + 20;
            watermark.style.top = topPct + '%';
            watermark.style.left = leftPct + '%';
        }, 8000);
    }

    function logSecurityEvent(eventType, metadata) {
        fetch(`/ajax/sessions/${meetingSessionId}/meeting/security-event`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                event_type: eventType,
                metadata: metadata
            })
        }).catch(console.error);
    }

    function getEmbeddableUrl(rawUrl) {
        if (!rawUrl) return '';

        // 1. YouTube conversion
        const ytMatch = rawUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
        if (ytMatch && ytMatch[1]) {
            return `https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0&modestbranding=1&enablejsapi=1`;
        }

        // 2. Vimeo conversion
        const vimeoMatch = rawUrl.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/\d+\/video\/|video\/|)(\d+)/);
        if (vimeoMatch && vimeoMatch[1]) {
            return `https://player.vimeo.com/video/${vimeoMatch[1]}?autoplay=1`;
        }

        // 3. Zoom Web Client URL conversion (In-page Web Client player)
        if (rawUrl.includes('zoom.us/j/')) {
            const parts = rawUrl.split('zoom.us/j/');
            if (parts[1]) {
                const meetingParts = parts[1].split('?');
                const meetingId = meetingParts[0];
                const query = meetingParts[1] ? '&' + meetingParts[1] : '';
                return `https://zoom.us/wc/${meetingId}/join?prefer=1${query}`;
            }
        }

        return rawUrl;
    }

    function showSecurityAlert(msg) {
        const banner = document.getElementById('securityAlertBanner');
        const text = document.getElementById('securityAlertText');
        if (banner && text) {
            text.innerText = msg;
            banner.classList.remove('hidden');
        }
    }

    function leaveMeetingSession() {
        if (heartbeatInterval) clearInterval(heartbeatInterval);
        if (durationTimerInterval) clearInterval(durationTimerInterval);
        if (watermarkInterval) clearInterval(watermarkInterval);

        fetch(`/ajax/sessions/${meetingSessionId}/meeting/leave`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        }).finally(() => {
            window.location.href = '<?php echo e(route("student-portal")); ?>';
        });
    }
</script>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/meeting-container.blade.php ENDPATH**/ ?>