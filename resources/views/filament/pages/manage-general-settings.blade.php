<x-filament-panels::page>
    @php
        $isAr = app()->getLocale() === 'ar';
        $currentAppName = brand_name();
        $currentPrimary = brand_color('primary', '#0d9488');
        $currentSecondary = brand_color('secondary', '#6366f1');
        $currentCurrency = app_currency();
        $currentSymbol = currency_symbol();
        $currentSurfaceMode = \App\Models\SiteSetting::get('theme_surface_mode', 'dark-glass');
    @endphp

    {{-- ── 1. Compact Executive System Telemetry Ribbon (Only 42px High) ── --}}
    <div class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-teal-500/25 bg-white/70 dark:bg-slate-900/60 backdrop-blur-md shadow-xs flex-wrap">
        <div class="flex items-center gap-2.5 text-xs">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-700 dark:text-teal-300 font-bold border border-teal-500/20">
                <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                {{ $isAr ? 'المزامنة الحية مفعلة' : 'Live Sync Active' }}
            </span>

            <span class="text-slate-500 dark:text-slate-400 font-medium hidden sm:inline">
                {{ $isAr ? 'المنظومة النشطة:' : 'Active System:' }}
            </span>
            <strong class="text-slate-800 dark:text-white font-extrabold">{{ $currentAppName }}</strong>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3 text-xs flex-wrap">
            {{-- Primary Color Chip --}}
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                <span class="w-3 h-3 rounded-full border border-white/20 shadow-xs" style="background-color: {{ $currentPrimary }};"></span>
                <code class="font-mono text-[11px] font-bold text-teal-600 dark:text-teal-400">{{ $currentPrimary }}</code>
            </div>

            {{-- Currency Chip --}}
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 font-bold text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-coins text-[10px]"></i>
                <span>{{ $currentCurrency }} ({{ $currentSymbol }})</span>
            </div>

            {{-- Mode Chip --}}
            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium hidden md:inline-flex">
                <i class="fa-solid fa-wand-magic-sparkles text-teal-500 text-[10px]"></i>
                <span class="capitalize">{{ str_replace('-', ' ', (string) $currentSurfaceMode) }}</span>
            </div>
        </div>
    </div>

    {{-- ── 2. The Main Multi-Tab Settings Form ── --}}
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        {{-- ── 3. Static Bottom Action Card (Clean, Non-overlapping) ── --}}
        <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-teal-500/25 bg-white/80 dark:bg-slate-900/70 backdrop-blur-md shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i class="fa-solid fa-keyboard text-teal-500"></i>
                <span>{{ $isAr ? 'اضغط' : 'Press' }}</span>
                <kbd class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-[11px] text-teal-600 dark:text-teal-400 font-bold shadow-xs">Ctrl + S</kbd>
                <span>{{ $isAr ? 'للحفظ الفوري ومزامنة كافة الإعدادات' : 'to instantly save & sync full settings' }}</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    {{ $isAr ? 'معاينة الموقع الرئيسي' : 'Preview Site ↗' }}
                </a>

                <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle" color="primary">
                    {{ $isAr ? 'حفظ ومزامنة كافة الإعدادات' : 'Save & Sync Full System Settings' }}
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
