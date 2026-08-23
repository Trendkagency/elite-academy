<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'course' => null,
    'videoData' => null,
    'posterImage' => null,
    'title' => null,
]));

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

foreach (array_filter(([
    'course' => null,
    'videoData' => null,
    'posterImage' => null,
    'title' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $cId = $course ? $course->id : 1;
    $vData = $videoData ?: ($course ? $course->getVideoEmbedData() : ['type' => 'mp4', 'embed_url' => asset('videos/physics_demo.mp4')]);
    $poster = $posterImage ?: ($course && $course->image ? media_url($course->image, 'images/course_ai.png') : asset('images/course_ai.png'));
    $user = auth()->user();
    $userName = $user ? $user->name : 'Guest Student';
    $userPhone = $user ? ($user->phone ?: 'ID: ' . $user->id) : 'ID: Guest';
    $userIp = request()->ip();
?>

<div x-data="secureVideoPlayer({
        courseId: <?php echo e($cId); ?>,
        videoType: '<?php echo e($vData['type']); ?>',
        rawEmbedUrl: '<?php echo e($vData['embed_url']); ?>',
        userName: '<?php echo e(addslashes($userName)); ?>',
        userPhone: '<?php echo e(addslashes($userPhone)); ?>',
        userIp: '<?php echo e($userIp); ?>',
        tokenRoute: '<?php echo e(route('ajax.secure-video.token', $cId)); ?>'
    })"
    x-init="initPlayer()"
    @contextmenu.prevent
    class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-950 border border-teal-500/40 shadow-2xl select-none group">

    
    <div x-show="isBlurred"
         x-cloak
         class="absolute inset-0 z-50 bg-slate-950/95 backdrop-blur-3xl flex flex-col items-center justify-center gap-4 text-center p-6 transition-all duration-300">
        <div class="w-16 h-16 rounded-2xl bg-red-500/20 text-red-400 border border-red-500/40 flex items-center justify-center text-3xl shadow-xl animate-bounce">
            🔒
        </div>
        <div class="space-y-1 max-w-md">
            <h3 class="font-heading text-lg font-bold text-white">
                <?php echo e(app()->getLocale() === 'ar' ? 'محتوى محمي — تم رصد مغادرة التبويب' : 'Protected Content — Tab Switched'); ?>

            </h3>
            <p class="text-xs text-slate-400">
                <?php echo e(app()->getLocale() === 'ar' ? 'لحماية الملكية الفكرية، تم تعتيم الشاشة وإيقاف الشرح أثناء التواجد خارج تبويب الدرس.' : 'To protect intellectual property, video playback is obfuscated when navigating away from the lesson tab.'); ?>

            </p>
        </div>
        <button @click="resumePlayer()" class="mt-2 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 px-6 py-2.5 rounded-xl shadow-lg transition-all cursor-pointer">
            <?php echo e(app()->getLocale() === 'ar' ? 'العودة لمتابعة الشرح ▶' : 'Resume Playback ▶'); ?>

        </button>
    </div>

    
    <div x-ref="watermark"
         class="absolute z-40 pointer-events-none select-none px-3 py-1.5 rounded-lg bg-slate-900/70 backdrop-blur-md border border-teal-500/30 text-[10px] font-mono text-teal-300 shadow-xl transition-all duration-1000 flex items-center gap-2"
         :style="watermarkStyle">
        <span class="font-bold text-orange-400"><?php echo e($userName); ?></span>
        <span class="opacity-40">|</span>
        <span><?php echo e($userPhone); ?></span>
        <span class="opacity-40">|</span>
        <span>IP: <?php echo e($userIp); ?></span>
        <span class="opacity-40">|</span>
        <span x-text="currentTimeStr"></span>
    </div>

    
    <div class="w-full h-full relative group">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($vData['type'] === 'youtube' || $vData['type'] === 'vimeo'): ?>
            <iframe x-ref="iframePlayer"
                    :src="activeStreamUrl"
                    class="w-full h-full border-0 rounded-2xl"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
            </iframe>
        <?php else: ?>
            
            <video x-ref="nativePlayer"
                   :src="activeStreamUrl"
                   class="w-full h-full object-cover cursor-pointer"
                   playsinline
                   preload="metadata"
                   disablePictureInPicture
                   oncontextmenu="return false;"
                   poster="<?php echo e($poster); ?>"
                   @click="togglePlay()"
                   @timeupdate="onTimeUpdate()"
                   @loadedmetadata="onLoadedMetadata()"
                   @ended="isPlaying = false">
                Your browser does not support HTML5 secure video.
            </video>

            
            <div x-show="!isPlaying"
                 @click="togglePlay()"
                 class="absolute inset-0 z-20 flex items-center justify-center bg-slate-950/40 cursor-pointer group-hover:bg-slate-950/50 transition-all">
                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-orange-500 to-teal-500 text-white flex items-center justify-center text-3xl font-bold shadow-2xl group-hover:scale-110 transition-transform duration-300 ring-8 ring-white/10">
                    ▶
                </div>
            </div>

            
            <div class="absolute bottom-0 inset-x-0 z-30 bg-gradient-to-t from-slate-950 via-slate-900/90 to-transparent p-4 flex flex-col gap-2 transition-opacity duration-300 opacity-90 group-hover:opacity-100">
                
                <div class="relative w-full flex items-center">
                    <input type="range"
                           min="0"
                           :max="duration || 100"
                           :value="currentTime"
                           @input="seek($event.target.value)"
                           class="w-full h-1.5 bg-slate-700/80 rounded-lg appearance-none cursor-pointer accent-teal-400 focus:outline-none">
                </div>

                
                <div class="flex items-center justify-between text-xs font-mono text-white">
                    <div class="flex items-center gap-3">
                        <button @click="togglePlay()" class="p-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white shadow-md transition-all cursor-pointer font-bold">
                            <span x-text="isPlaying ? '⏸' : '▶'"></span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button @click="toggleMute()" class="text-slate-300 hover:text-white text-sm cursor-pointer">
                                <span x-text="isMuted ? '🔇' : '🔊'"></span>
                            </button>
                            <input type="range" min="0" max="1" step="0.1" :value="volume" @input="setVolume($event.target.value)" class="w-16 h-1 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-teal-400">
                        </div>

                        <span class="text-[11px] text-slate-300 font-bold" x-text="formatTime(currentTime) + ' / ' + formatTime(duration)"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-[10px] bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2 py-0.5 rounded-full font-bold">
                            🛡️ HD Protected Stream
                        </span>
                        <button @click="toggleFullscreen()" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition-all cursor-pointer text-sm">
                            ⛶
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function secureVideoPlayer(config) {
    return {
        courseId: config.courseId,
        videoType: config.videoType,
        rawEmbedUrl: config.rawEmbedUrl,
        userName: config.userName,
        userPhone: config.userPhone,
        userIp: config.userIp,
        activeStreamUrl: config.rawEmbedUrl || '<?php echo e(asset('videos/appropriate-sharing.mp4')); ?>',
        isBlurred: false,
        isPlaying: false,
        isMuted: false,
        currentTime: 0,
        duration: 0,
        volume: 1,
        watermarkStyle: 'top: 15%; left: 10%;',
        currentTimeStr: '',
        watermarkInterval: null,
        clockInterval: null,

        initPlayer() {
            this.updateClock();
            this.moveWatermark();

            // 1. Watermark repositioning every 4 seconds
            this.watermarkInterval = setInterval(() => {
                this.moveWatermark();
            }, 4000);

            // 2. Realtime timestamp ticker
            this.clockInterval = setInterval(() => {
                this.updateClock();
            }, 1000);

            // 3. Tab Visibility Protection (Only blur when switching away from tab)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.triggerSecurityBlur();
                } else {
                    this.resumePlayer();
                }
            });

            // 4. Keyboard & Shortcut Shield
            window.addEventListener('keydown', (e) => {
                if (
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j')) ||
                    (e.ctrlKey && (e.key === 'U' || e.key === 'u' || e.key === 'S' || e.key === 's')) ||
                    e.key === 'PrintScreen'
                ) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.key === 'PrintScreen') {
                        navigator.clipboard?.writeText('');
                    }
                    return false;
                }
            });
        },

        togglePlay() {
            const player = this.$refs.nativePlayer;
            if (!player) return;

            if (!player.src || player.src === window.location.href || player.src.endsWith('/null')) {
                player.src = this.activeStreamUrl || '<?php echo e(asset('videos/appropriate-sharing.mp4')); ?>';
                player.load();
            }

            if (player.paused) {
                const playPromise = player.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        this.isPlaying = true;
                    }).catch(err => {
                        console.log('Play error, attempting local fallback:', err);
                        player.src = '<?php echo e(asset('videos/appropriate-sharing.mp4')); ?>';
                        player.load();
                        player.play().then(() => {
                            this.isPlaying = true;
                        }).catch(e => console.log('Fallback playback failed:', e));
                    });
                }
            } else {
                player.pause();
                this.isPlaying = false;
            }
        },

        onTimeUpdate() {
            const player = this.$refs.nativePlayer;
            if (player) {
                this.currentTime = player.currentTime;
            }
        },

        onLoadedMetadata() {
            const player = this.$refs.nativePlayer;
            if (player) {
                this.duration = player.duration;
            }
        },

        seek(val) {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.currentTime = val;
                this.currentTime = val;
            }
        },

        toggleMute() {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.muted = !player.muted;
                this.isMuted = player.muted;
            }
        },

        setVolume(val) {
            const player = this.$refs.nativePlayer;
            if (player) {
                player.volume = val;
                this.volume = val;
                player.muted = (val == 0);
                this.isMuted = (val == 0);
            }
        },

        toggleFullscreen() {
            const el = this.$el;
            if (!document.fullscreenElement) {
                el.requestFullscreen?.() || el.webkitRequestFullscreen?.();
            } else {
                document.exitFullscreen?.() || document.webkitExitFullscreen?.();
            }
        },

        formatTime(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        },

        moveWatermark() {
            const top = Math.floor(Math.random() * 65) + 10;
            const left = Math.floor(Math.random() * 55) + 5;
            this.watermarkStyle = `top: ${top}%; left: ${left}%;`;
        },

        updateClock() {
            const now = new Date();
            this.currentTimeStr = now.toTimeString().split(' ')[0];
        },

        triggerSecurityBlur() {
            this.isBlurred = true;
            const native = this.$refs.nativePlayer;
            if (native && !native.paused) {
                native.pause();
                this.isPlaying = false;
            }
        },

        resumePlayer() {
            this.isBlurred = false;
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/secure-video-player.blade.php ENDPATH**/ ?>