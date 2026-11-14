<x-filament-panels::page>
    @php
        $isAr = app()->getLocale() === 'ar';
        $currentLightLogo = brand_logo('light');
        $currentDarkLogo = brand_logo('dark');
        $currentFavicon = brand_logo('favicon');
        $currentAppName = brand('app_name', 'Elite Academy');
        $currentTagline = brand('tagline', 'Leading LMS Platform');
        $currentPrimary = brand_color('primary', '#0d9488');
        $currentSecondary = brand_color('secondary', '#6366f1');
        $currentCurrency = app_currency();
        $currentSymbol = currency_symbol();
    @endphp

    <style>
        .sg-settings-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(var(--sys-primary-rgb, 13, 148, 136), 0.12) 0%, rgba(15, 23, 42, 0.6) 100%);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(var(--sys-primary-rgb, 13, 148, 136), 0.25);
            border-radius: 20px;
            padding: 24px 28px;
            box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .sg-settings-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(var(--sys-primary-rgb, 13, 148, 136), 0.22) 0%, transparent 70%);
            pointer-events: none;
            filter: blur(40px);
        }

        .sg-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .sg-preview-card {
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .sg-preview-card:hover {
            transform: translateY(-2px);
        }

        .sg-preview-card.light-surface {
            background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid rgba(203, 213, 225, 0.8);
            box-shadow: 0 8px 24px -8px rgba(15, 23, 42, 0.08);
            color: #0f172a;
        }

        .sg-preview-card.dark-surface {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.85) 0%, rgba(2, 6, 23, 0.95) 100%);
            border: 1px solid rgba(var(--sys-primary-rgb, 13, 148, 136), 0.3);
            box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.5);
            color: #f8fafc;
        }

        .sg-browser-tab-mock {
            background: #1e293b;
            border-radius: 8px 8px 0 0;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--sys-primary, #0d9488);
            width: fit-content;
            max-width: 100%;
        }

        .sg-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .sg-sticky-actions {
            position: sticky;
            bottom: 16px;
            z-index: 30;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(var(--sys-primary-rgb, 13, 148, 136), 0.35);
            border-radius: 16px;
            padding: 12px 20px;
            box-shadow: 0 12px 32px -8px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Enforce high-contrast text and crisp icons */
        .fi-tabs-item-active {
            color: var(--sys-primary, #0d9488) !important;
        }
    </style>

    <div class="space-y-6">
        {{-- ── 1. Smart Glass Hero Header & System Telemetry ── --}}
        <div class="sg-settings-hero">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="sg-pill-badge bg-teal-500/15 text-teal-400 border border-teal-500/30">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                            {{ $isAr ? 'محرك التهيئة ومزامنة المنظومة الشاملة' : 'System Synchronization & Visual Engine' }}
                        </span>
                        <span class="sg-pill-badge bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                            <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                            {{ $isAr ? 'مزامنة فورية 100%' : '100% Live Sync' }}
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <i class="fa-solid fa-sliders text-teal-400"></i>
                        {{ $isAr ? 'مركز إعدادات النظام والهوية البصرية' : 'System Settings & Visual Identity Hub' }}
                    </h1>

                    <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                        {{ $isAr
                            ? 'إدارة مركزية لكافة أصول العلامة التجارية، الشعارات الرسمية للوضع الفاتح والداكن، أيقونة المتصفح، العملة النشطة، وألوان النظام مع تطبيق وانعكاس فوري في كامل أجزاء المنظومة.'
                            : 'Central management for brand assets, light and dark logos, favicon, currency, and system colors with instant live synchronization across the entire application.'
                        }}
                    </p>
                </div>

                {{-- Quick Telemetry Stat Badges --}}
                <div class="flex flex-wrap md:flex-col gap-2 shrink-0">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs text-slate-200">
                        <span class="text-slate-400">{{ $isAr ? 'الاسم النشط:' : 'Brand:' }}</span>
                        <strong class="text-teal-300 font-bold">{{ $currentAppName }}</strong>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs text-slate-200">
                        <span class="text-slate-400">{{ $isAr ? 'اللون الأساسي:' : 'Primary:' }}</span>
                        <span class="w-3.5 h-3.5 rounded-full border border-white/20 shadow-xs" style="background-color: {{ $currentPrimary }};"></span>
                        <code class="text-teal-300 font-mono text-[11px]">{{ $currentPrimary }}</code>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/60 border border-slate-700/60 text-xs text-slate-200">
                        <span class="text-slate-400">{{ $isAr ? 'العملة الحالية:' : 'Currency:' }}</span>
                        <strong class="text-amber-300 font-bold">{{ $currentCurrency }} ({{ $currentSymbol }})</strong>
                    </div>
                </div>
            </div>

            {{-- ── Real-Time Interactive Visual Preview Grid ── --}}
            <div class="sg-preview-grid">
                {{-- Light Mode Preview --}}
                <div class="sg-preview-card light-surface">
                    <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                        <span><i class="fa-solid fa-sun text-amber-500 me-1"></i> {{ $isAr ? 'معاينة الوضع الفاتح' : 'Light Surface Preview' }}</span>
                        <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-bold">Light</span>
                    </div>
                    <div class="flex items-center gap-3 py-2 px-3 bg-white/80 rounded-xl border border-slate-200/80 min-h-[56px]">
                        <img src="{{ $currentLightLogo }}" alt="Light Logo" class="h-9 w-auto max-w-[140px] object-contain">
                        <div class="border-s border-slate-200 ps-2.5">
                            <div class="font-extrabold text-sm text-slate-900 leading-tight">{{ $currentAppName }}</div>
                            <div class="text-[10px] text-slate-500 leading-none mt-0.5 truncate max-w-[130px]">{{ $currentTagline }}</div>
                        </div>
                    </div>
                </div>

                {{-- Dark Mode Preview --}}
                <div class="sg-preview-card dark-surface">
                    <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                        <span><i class="fa-solid fa-moon text-indigo-400 me-1"></i> {{ $isAr ? 'معاينة الوضع الزجاجي والداكن' : 'Dark & Glass Surface Preview' }}</span>
                        <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-teal-950 text-teal-300 border border-teal-800 font-bold">Glass OS</span>
                    </div>
                    <div class="flex items-center gap-3 py-2 px-3 bg-slate-950/70 rounded-xl border border-teal-500/25 min-h-[56px] shadow-inner">
                        <img src="{{ $currentDarkLogo }}" alt="Dark Logo" class="h-9 w-auto max-w-[140px] object-contain">
                        <div class="border-s border-slate-800 ps-2.5">
                            <div class="font-extrabold text-sm text-white leading-tight">{{ $currentAppName }}</div>
                            <div class="text-[10px] text-slate-400 leading-none mt-0.5 truncate max-w-[130px]">{{ $currentTagline }}</div>
                        </div>
                    </div>
                </div>

                {{-- Browser Tab Favicon Preview --}}
                <div class="sg-preview-card dark-surface justify-between">
                    <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                        <span><i class="fa-brands fa-chrome text-teal-400 me-1"></i> {{ $isAr ? 'أيقونة وعنوان المتصفح' : 'Browser Tab Favicon' }}</span>
                        <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-bold">Favicon</span>
                    </div>
                    <div class="pt-1">
                        <div class="sg-browser-tab-mock">
                            <img src="{{ $currentFavicon }}" alt="Favicon" class="w-4 h-4 object-contain rounded-xs shrink-0">
                            <span class="text-xs font-medium text-slate-200 truncate max-w-[180px]">{{ $currentAppName }} — LMS</span>
                            <span class="text-slate-500 text-[10px] ms-1">×</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. The Main Multi-Tab Settings Form ── --}}
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            {{-- ── 3. Sticky Action Dock ── --}}
            <div class="sg-sticky-actions flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-xs text-slate-300">
                    <i class="fa-solid fa-keyboard text-teal-400"></i>
                    <span>{{ $isAr ? 'اضغط' : 'Press' }}</span>
                    <kbd class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 font-mono text-[11px] text-teal-300">Ctrl + S</kbd>
                    <span>{{ $isAr ? 'للحفظ الفوري من أي مكان في الصفحة' : 'to instantly save from anywhere' }}</span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <a href="{{ route('home') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-600/60 text-slate-200 text-xs font-bold transition-all">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        {{ $isAr ? 'معاينة الموقع الرئيسي' : 'Preview Landing' }}
                    </a>

                    <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle" color="primary">
                        {{ $isAr ? 'حفظ ومزامنة كافة إعدادات المنظومة' : 'Save & Sync Full System Settings' }}
                    </x-filament::button>
                </div>
            </div>
        </form>
    </div>
</x-filament-panels::page>
