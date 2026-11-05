@php
    $loginCfg = config('demo.login', []);
    $branding = config('demo.branding', []);
    $guards = config('demo.guards', []);
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';

    $layout = $loginCfg['layout'] ?? 'split';
    $showLogo = (bool) ($loginCfg['show_logo'] ?? true);
    $logoHeight = $loginCfg['logo_height'] ?? '3.5rem';
    
    $heading = is_array($loginCfg['heading'] ?? null) 
        ? ($loginCfg['heading'][$locale] ?? $loginCfg['heading']['en'] ?? 'Demo Login')
        : ($loginCfg['heading'] ?? 'Demo Login');

    $subheading = is_array($loginCfg['subheading'] ?? null)
        ? ($loginCfg['subheading'][$locale] ?? $loginCfg['subheading']['en'] ?? 'Pick a role and sign in instantly.')
        : ($loginCfg['subheading'] ?? 'Pick a role and sign in instantly.');

    $bg = $loginCfg['background'] ?? [];
    $bgType = $bg['type'] ?? 'gradient';
    $bgImage = $bg['image'] ?? 'demo/login-bg.jpg';
    $bgGrad1 = $bg['gradient'][0] ?? '#0f172a';
    $bgGrad2 = $bg['gradient'][1] ?? '#2563eb';
    $bgColor = $bg['color'] ?? '#0f172a';
    $overlayOpacity = $bg['overlay_opacity'] ?? 0.45;

    $card = $loginCfg['card'] ?? [];
    $cardWidth = $card['width'] ?? '28rem';
    $cardRadius = $card['radius'] ?? '1rem';
    $cardBlur = (bool) ($card['blur'] ?? true);
    $cardShadow = $card['shadow'] ?? 'xl';

    $shadowClass = match($cardShadow) {
        'none' => 'shadow-none',
        'md' => 'shadow-md',
        default => 'shadow-2xl',
    };

    $sidePanel = $loginCfg['side_panel'] ?? [];
    $sideEnabled = ($layout === 'split') && (bool) ($sidePanel['enabled'] ?? true);
    $sideTitle = is_array($sidePanel['title'] ?? null)
        ? ($sidePanel['title'][$locale] ?? $sidePanel['title']['en'] ?? 'Everything you need to teach online')
        : ($sidePanel['title'] ?? 'Everything you need to teach online');
    $sideBullets = $sidePanel['bullets'] ?? [];

    $primaryColor = $branding['primary_color'] ?? '#2563eb';
    $accentColor = $branding['accent_color'] ?? '#f59e0b';
    $showBanner = (bool) ($guards['show_banner'] ?? true);
    $bannerText = $guards['banner_text'] ?? ($isRtl ? 'نسخة تجريبية — يتم إعادة تعيين البيانات تلقائيًا.' : 'Demo mode — data resets automatically.');
    $showManualForm = (bool) ($loginCfg['show_manual_form'] ?? true);
    $showCredentials = (bool) (config('demo.credentials.show_on_login', true));

    // Determine layout alignment classes
    $containerClass = match($layout) {
        'split' => 'lg:grid lg:grid-cols-12 min-h-screen',
        'card-left' => 'min-h-screen flex items-center justify-start px-4 sm:px-8 lg:px-16 py-12',
        'card-right' => 'min-h-screen flex items-center justify-end px-4 sm:px-8 lg:px-16 py-12',
        default => 'min-h-screen flex items-center justify-center px-4 py-12', // centered
    };

    // Background style inline
    $bgStyle = match($bgType) {
        'image' => "background-image: url('" . asset($bgImage) . "'); background-size: cover; background-position: center;",
        'color' => "background-color: {$bgColor};",
        default => "background: linear-gradient(135deg, {$bgGrad1} 0%, {$bgGrad2} 100%);",
    };
@endphp

<div 
    class="demo-login-root relative min-h-screen w-full overflow-x-hidden font-sans text-slate-800 dark:text-slate-100 transition-colors"
    style="--brand-primary: {{ $primaryColor }}; --brand-accent: {{ $accentColor }}; --card-max-width: {{ $cardWidth }}; --card-border-radius: {{ $cardRadius }}; {{ $bgStyle }}"
>
    {{-- Background Overlay if Image type --}}
    @if($bgType === 'image')
        <div class="absolute inset-0 bg-slate-950" style="opacity: {{ $overlayOpacity }};"></div>
    @endif

    {{-- Subtle Decorative Glow / Gradients for Depth --}}
    <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-amber-500/15 blur-3xl"></div>

    {{-- Top Demo Safety Guard Banner --}}
    @if($showBanner)
        <div class="relative z-30 w-full bg-amber-500/90 text-slate-950 backdrop-blur-md px-4 py-2 text-center text-xs font-bold shadow-xs flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-slate-950 animate-pulse shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $bannerText }}</span>
            <span class="hidden sm:inline text-[11px] opacity-75 font-normal">({{ brand('app_name', 'EDU') }})</span>
        </div>
    @endif

    {{-- Top Utility Bar (Language Selector & Home Link) --}}
    <div class="relative z-20 w-full px-6 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-white/80 hover:text-white transition-colors bg-white/10 hover:bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-full">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ $isRtl ? 'الرئيسية' : 'Home' }}</span>
        </a>

        <div class="flex items-center gap-2">
            @if($locale === 'ar')
                <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-full transition-all">
                    English
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="text-xs font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-full transition-all font-cairo">
                    العربية
                </a>
            @endif
        </div>
    </div>

    {{-- Main Container based on layout --}}
    <div class="relative z-10 w-full {{ $containerClass }}">
        {{-- Split Layout Side Hero Panel (When layout=split) --}}
        @if($sideEnabled)
            <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col justify-between p-12 xl:p-16 text-white relative">
                <div class="space-y-6 max-w-xl">
                    @if($showLogo)
                        <div class="mb-6">
                            <x-demo.brand-logo size="lg" theme="dark" />
                        </div>
                    @endif

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-amber-300">
                        <svg class="w-3.5 h-3.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>{{ brand('tagline') }}</span>
                    </div>

                    <h2 class="text-3xl xl:text-5xl font-black leading-tight tracking-tight drop-shadow-sm font-heading">
                        {{ $sideTitle }}
                    </h2>

                    <p class="text-sm xl:text-base text-slate-200/90 leading-relaxed max-w-lg">
                        {{ $isRtl 
                            ? 'منصة تعليمية ذكية متكاملة للمعلمين والطلاب وأولياء الأمور مع فصول افتراضية، تصحيح واجبات آلي، ومتابعة فورية للدرجات.' 
                            : 'An advanced full-stack LMS empowering educators, students, and parents with live interactive classrooms, automated assessments, and real-time academic analytics.' 
                        }}
                    </p>

                    @if(!empty($sideBullets))
                        <div class="pt-4 space-y-3">
                            @foreach($sideBullets as $bullet)
                                @php
                                    $bulletText = is_array($bullet) ? ($bullet[$locale] ?? $bullet['en'] ?? '') : $bullet;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-xl bg-blue-500/30 border border-blue-400/40 text-blue-200 flex items-center justify-center text-xs shadow-inner shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-white/95">{{ $bulletText }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Footer info in side panel --}}
                <div class="pt-8 border-t border-white/10 text-xs text-white/60 flex items-center justify-between">
                    <span>{{ brand('footer_text', '© ' . brand('app_name', 'EDU') . ' Demo') }}</span>
                    <span>{{ brand('support_email') }}</span>
                </div>
            </div>
        @endif

        {{-- Login Card Area --}}
        <div class="{{ $layout === 'split' ? 'lg:col-span-6 xl:col-span-5 flex items-center justify-center p-4 sm:p-8 lg:p-12' : 'w-full flex items-center justify-center' }}">
            <div 
                class="w-full relative transition-all duration-300 {{ $shadowClass }} {{ $cardBlur ? 'backdrop-blur-xl bg-white/90 dark:bg-slate-900/90' : 'bg-white dark:bg-slate-900' }} border border-white/30 dark:border-slate-800 p-6 sm:p-8 space-y-6"
                style="max-width: var(--card-max-width); border-radius: var(--card-border-radius);"
            >
                {{-- Card Header --}}
                <div class="text-center space-y-2">
                    @if($showLogo && !$sideEnabled)
                        <div class="flex justify-center mb-4">
                            <x-demo.brand-logo size="lg" theme="auto" />
                        </div>
                    @endif

                    <h1 class="font-heading font-black text-2xl text-slate-900 dark:text-white tracking-tight">
                        {{ $heading }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        {{ $subheading }}
                    </p>
                </div>

                {{-- Direct One-Click Credentials (cards/buttons/list) --}}
                @if($showCredentials)
                    <div class="border-b border-slate-200/80 dark:border-slate-800 pb-5">
                        <x-demo.credentials />
                    </div>
                @endif

                {{-- Manual Email / Password Form (if enabled in config) --}}
                @if($showManualForm)
                    <div class="space-y-4">
                        @if($showCredentials)
                            <div class="relative text-center">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                                </div>
                                <div class="relative flex justify-center text-[11px] uppercase">
                                    <span class="bg-white/95 dark:bg-slate-900/95 px-3 text-slate-400 font-bold tracking-wider">
                                        {{ $isRtl ? 'أو تسجيل الدخول العادي' : 'Or Manual Sign In' }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Slot containing manual form from page --}}
                        {{ $slot }}
                    </div>
                @endif

                {{-- Footer note --}}
                <div class="text-center pt-2">
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                        {{ brand('footer_text') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
