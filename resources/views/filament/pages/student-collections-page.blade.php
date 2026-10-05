<x-filament-panels::page>
    <div class="col-page-container">
        <style>
            /* ─────────────────────────────────────────────────────────────
               COLLECTIONS & RENEWALS DASHBOARD: LIGHT & DARK MODE THEME
            ───────────────────────────────────────────────────────────── */
            :root {
                --col-bg-page: #f8fafc;
                --col-card-bg: #ffffff;
                --col-card-elevated: #ffffff;
                --col-card-border: #e2e8f0;
                --col-text-main: #0f172a;
                --col-text-muted: #64748b;
                --col-text-dim: #94a3b8;
                --col-input-bg: #f8fafc;
                --col-input-border: #cbd5e1;
                --col-table-header: #f1f5f9;
                --col-table-row-hover: rgba(241, 245, 249, 0.7);
                --col-table-border: #e2e8f0;
                --col-pill-bg: #f1f5f9;
                --col-pill-border: #e2e8f0;
                --col-pill-text: #475569;
                --col-primary-grad: linear-gradient(135deg, #0d9488, #0f766e);
                --col-primary-shadow: 0 4px 14px rgba(13, 148, 136, 0.25);
            }

            html.dark, .dark, [data-theme="dark"], .fi-theme-dark {
                --col-bg-page: #0b132b;
                --col-card-bg: #141e33;
                --col-card-elevated: #1a2744;
                --col-card-border: #243452;
                --col-text-main: #f8fafc;
                --col-text-muted: #94a3b8;
                --col-text-dim: #64748b;
                --col-input-bg: #0b132b;
                --col-input-border: #334155;
                --col-table-header: #0f182c;
                --col-table-row-hover: rgba(26, 39, 68, 0.5);
                --col-table-border: #1e2c47;
                --col-pill-bg: #19253d;
                --col-pill-border: #273859;
                --col-pill-text: #cbd5e1;
                --col-primary-grad: linear-gradient(135deg, #0d9488, #059669);
                --col-primary-shadow: 0 4px 18px rgba(13, 148, 136, 0.4);
            }

            .col-page-container {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
                color: var(--col-text-main);
                box-sizing: border-box;
            }

            /* ── BULLETPROOF SVG & ICON SIZING (NEVER BLOWS UP) ── */
            .col-page-container svg {
                display: inline-block !important;
                vertical-align: middle;
                max-width: 1.5rem !important;
                max-height: 1.5rem !important;
                width: 1.25rem !important;
                height: 1.25rem !important;
                flex-shrink: 0;
            }

            .col-icon-box {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.875rem;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 1.25rem;
            }
            .col-icon-box svg {
                width: 1.35rem !important;
                height: 1.35rem !important;
                max-width: 1.35rem !important;
                max-height: 1.35rem !important;
            }

            /* ── STAT CARDS ── */
            .col-stat-card {
                background: var(--col-card-bg);
                border: 1px solid var(--col-card-border);
                border-radius: 1.25rem;
                padding: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
                position: relative;
                overflow: hidden;
            }
            .col-stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            }
            .col-stat-card::before {
                content: '';
                position: absolute;
                inset-inline-start: 0;
                top: 0;
                bottom: 0;
                width: 4px;
            }
            .col-stat-red::before { background: #ef4444; }
            .col-stat-amber::before { background: #f59e0b; }
            .col-stat-purple::before { background: #a855f7; }
            .col-stat-emerald::before { background: #10b981; }

            /* ── MAIN SURFACE CONTAINER ── */
            .col-surface-card {
                background: var(--col-card-bg);
                border: 1px solid var(--col-card-border);
                border-radius: 1.25rem;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
                overflow: hidden;
            }

            /* ── TABS ── */
            .col-tab-btn {
                padding: 0.65rem 1.25rem;
                border-radius: 0.875rem;
                font-size: 0.875rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.2s ease;
                cursor: pointer;
                border: 1px solid transparent;
            }
            .col-tab-btn.active {
                background: var(--col-primary-grad);
                color: #ffffff;
                box-shadow: var(--col-primary-shadow);
            }
            .col-tab-btn.inactive {
                background: var(--col-pill-bg);
                border-color: var(--col-pill-border);
                color: var(--col-pill-text);
            }
            .col-tab-btn.inactive:hover {
                filter: brightness(1.05);
            }

            /* ── BADGE PILLS ── */
            .col-badge-count {
                padding: 0.15rem 0.55rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 800;
            }
            .col-tab-btn.active .col-badge-count {
                background: rgba(255, 255, 255, 0.25);
                color: #ffffff;
            }
            .col-tab-btn.inactive .col-badge-count {
                background: rgba(0, 0, 0, 0.08);
                color: var(--col-text-main);
            }
            html.dark .col-tab-btn.inactive .col-badge-count {
                background: rgba(255, 255, 255, 0.1);
                color: #f8fafc;
            }

            /* ── SUB-FILTER PILLS ── */
            .col-filter-pill {
                padding: 0.35rem 0.85rem;
                border-radius: 0.65rem;
                font-size: 0.775rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.15s ease;
                border: 1px solid var(--col-pill-border);
                background: var(--col-pill-bg);
                color: var(--col-pill-text);
                white-space: nowrap;
            }
            .col-filter-pill:hover {
                filter: brightness(1.08);
            }
            .col-filter-pill.active {
                border-color: #0d9488;
                background: rgba(13, 148, 136, 0.15);
                color: #0d9488;
                font-weight: 800;
            }
            html.dark .col-filter-pill.active {
                color: #2dd4bf;
                border-color: #14b8a6;
                background: rgba(20, 184, 166, 0.2);
            }

            /* ── TABLE STYLING ── */
            .col-table-wrapper {
                width: 100%;
                overflow-x: auto;
                border-radius: 0.875rem;
                border: 1px solid var(--col-table-border);
            }
            .col-table {
                width: 100%;
                border-collapse: collapse;
                text-align: start;
                font-size: 0.8125rem;
            }
            .col-table thead {
                background: var(--col-table-header);
                color: var(--col-text-muted);
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.02em;
            }
            .col-table th {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid var(--col-table-border);
                white-space: nowrap;
            }
            .col-table td {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid var(--col-table-border);
                vertical-align: middle;
            }
            .col-table tbody tr {
                background: var(--col-card-bg);
                transition: background-color 0.15s ease;
            }
            .col-table tbody tr:hover {
                background: var(--col-table-row-hover);
            }
            .col-table tbody tr:last-child td {
                border-bottom: none;
            }

            /* ── ACTION BUTTONS ── */
            .col-action-btn {
                padding: 0.4rem 0.75rem;
                border-radius: 0.65rem;
                font-size: 0.75rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                cursor: pointer;
                transition: all 0.15s ease;
                white-space: nowrap;
                border: 1px solid transparent;
                text-decoration: none !important;
            }
            .col-btn-whatsapp {
                background: #25D366;
                color: #ffffff !important;
            }
            .col-btn-whatsapp:hover {
                background: #1eb857;
                transform: translateY(-1px);
            }
            .col-btn-reminder {
                background: #3b82f6;
                color: #ffffff !important;
            }
            .col-btn-reminder:hover {
                background: #2563eb;
                transform: translateY(-1px);
            }
            .col-btn-renew {
                background: var(--col-primary-grad);
                color: #ffffff !important;
                box-shadow: 0 2px 8px rgba(13, 148, 136, 0.3);
            }
            .col-btn-renew:hover {
                filter: brightness(1.1);
                transform: translateY(-1px);
            }
            .col-btn-credits {
                background: #059669;
                color: #ffffff !important;
            }
            .col-btn-credits:hover {
                background: #047857;
                transform: translateY(-1px);
            }

            /* ── MODAL DIALOGS ── */
            .col-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 99999;
                background: rgba(11, 19, 43, 0.75);
                backdrop-filter: blur(6px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .col-modal-card {
                background: var(--col-card-elevated);
                border: 1px solid var(--col-card-border);
                border-radius: 1.25rem;
                width: 100%;
                max-width: 28rem;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
                padding: 1.5rem;
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
                color: var(--col-text-main);
                box-sizing: border-box;
            }
            .col-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 1px solid var(--col-table-border);
                padding-bottom: 0.85rem;
            }
            .col-modal-title {
                font-size: 1.05rem;
                font-weight: 800;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: var(--col-text-main);
            }
            .col-input {
                width: 100%;
                border-radius: 0.75rem;
                border: 1px solid var(--col-input-border);
                background: var(--col-input-bg);
                color: var(--col-text-main);
                padding: 0.65rem 0.85rem;
                font-size: 0.85rem;
                outline: none;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
                box-sizing: border-box;
            }
            .col-input:focus {
                border-color: #0d9488;
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
            }
        </style>

        {{-- ── 1. TOP EXECUTIVE KPI STAT CARDS ──────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Stat 1: Exhausted Packages (0 Sessions) --}}
            <div class="col-stat-card col-stat-red">
                <div class="space-y-1">
                    <p class="text-xs font-semibold" style="color: var(--col-text-muted);">
                        {{ __('باقات منتهية تماماً (0 حصة)') }}
                    </p>
                    <p class="text-3xl font-extrabold text-red-500">
                        {{ $this->stats['exhausted_packages'] }}
                    </p>
                    <p class="text-[11px] font-bold text-red-500 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ __('مستحق السداد فوراً') }}</span>
                    </p>
                </div>
                <div class="col-icon-box bg-red-500/10 text-red-500 border border-red-500/20">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                </div>
            </div>

            {{-- Stat 2: Low Balance (1-3 Sessions) --}}
            <div class="col-stat-card col-stat-amber">
                <div class="space-y-1">
                    <p class="text-xs font-semibold" style="color: var(--col-text-muted);">
                        {{ __('باقات أوشكت على النفاد (1-3 حصص)') }}
                    </p>
                    <p class="text-3xl font-extrabold text-amber-500">
                        {{ $this->stats['low_balance_packages'] }}
                    </p>
                    <p class="text-[11px] font-bold text-amber-500 flex items-center gap-1">
                        <i class="fa-solid fa-bell"></i>
                        <span>{{ __('تحتاج تذكير مبكر بالسداد') }}</span>
                    </p>
                </div>
                <div class="col-icon-box bg-amber-500/10 text-amber-500 border border-amber-500/20">
                    <i class="fa-solid fa-ticket text-xl"></i>
                </div>
            </div>

            {{-- Stat 3: Expiring Within 7 Days --}}
            <div class="col-stat-card col-stat-purple">
                <div class="space-y-1">
                    <p class="text-xs font-semibold" style="color: var(--col-text-muted);">
                        {{ __('صلاحيات تنتهي خلال 7 أيام') }}
                    </p>
                    <p class="text-3xl font-extrabold text-purple-500">
                        {{ $this->stats['expiring_in_7_days'] }}
                    </p>
                    <p class="text-[11px] font-bold text-purple-500 flex items-center gap-1">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <span>{{ __('تجديد الاشتراكات الزمنية') }}</span>
                    </p>
                </div>
                <div class="col-icon-box bg-purple-500/10 text-purple-500 border border-purple-500/20">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
            </div>

            {{-- Stat 4: Recurring Schedules Ending in 14 Days --}}
            <div class="col-stat-card col-stat-emerald">
                <div class="space-y-1">
                    <p class="text-xs font-semibold" style="color: var(--col-text-muted);">
                        {{ __('جداول دورية تنتهي قريباً (14 يوماً)') }}
                    </p>
                    <p class="text-3xl font-extrabold text-emerald-500">
                        {{ $this->stats['cycles_ending_soon'] }}
                    </p>
                    <p class="text-[11px] font-bold text-emerald-500 flex items-center gap-1">
                        <i class="fa-solid fa-rotate"></i>
                        <span>{{ __('دورات شهرية تحتاج تمديداً وتجديداً') }}</span>
                    </p>
                </div>
                <div class="col-icon-box bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                    <i class="fa-solid fa-calendar-days text-xl"></i>
                </div>
            </div>
        </div>

        {{-- ── 2. MAIN SECTION: TABS & DATA TABLES ──────────────────────── --}}
        <div class="col-surface-card">
            
            {{-- Card Header: Primary Tab Switchers & Search Bar --}}
            <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4" style="border-bottom: 1px solid var(--col-table-border);">
                
                {{-- Dual Tabs --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                    <button type="button" wire:click="setTab('packages')"
                        class="col-tab-btn {{ $activeTab === 'packages' ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-receipt text-sm"></i>
                        <span>{{ __('مستحقات الباقات التعليمية') }}</span>
                        <span class="col-badge-count">
                            {{ $this->stats['exhausted_packages'] + $this->stats['low_balance_packages'] }}
                        </span>
                    </button>

                    <button type="button" wire:click="setTab('schedules')"
                        class="col-tab-btn {{ $activeTab === 'schedules' ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                        <span>{{ __('جداول شهرية أوشكت على الانتهاء') }}</span>
                        <span class="col-badge-count">
                            {{ $this->stats['cycles_ending_soon'] }}
                        </span>
                    </button>
                </div>

                {{-- Search Box --}}
                <div class="relative w-full md:w-80">
                    <input type="text" wire:model.live.debounce.300ms="searchQuery"
                        placeholder="{{ __('بحث باسم الطالب، الهاتف، المقرر...') }}"
                        class="col-input ps-9 pe-4 py-2">
                    <div class="absolute inset-y-0 start-0 ps-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    @if (!empty($searchQuery))
                        <button type="button" wire:click="$set('searchQuery', '')" class="absolute inset-y-0 end-0 pe-3 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- ═════════════════════════════════════════════════════════════ --}}
            {{-- TAB 1: EXPIRING / EXHAUSTED PACKAGES                           --}}
            {{-- ═════════════════════════════════════════════════════════════ --}}
            @if ($activeTab === 'packages')
                <div class="p-4 sm:p-5 space-y-4">
                    
                    {{-- Sub Filter Pills --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                        <span class="font-bold me-1" style="color: var(--col-text-muted);">
                            <i class="fa-solid fa-filter me-1"></i>{{ __('تصفية:') }}
                        </span>
                        
                        <button type="button" wire:click="setPackageFilter('all')"
                            class="col-filter-pill {{ $packageFilter === 'all' ? 'active' : '' }}">
                            {{ __('جميع المستحقات') }}
                        </button>
                        
                        <button type="button" wire:click="setPackageFilter('exhausted')"
                            class="col-filter-pill {{ $packageFilter === 'exhausted' ? 'active' : '' }}">
                            <span class="inline-block w-2 h-2 rounded-full bg-red-500 me-1"></span>
                            {{ __('رصيد منتهٍ (0 حصة)') }} ({{ $this->stats['exhausted_packages'] }})
                        </button>
                        
                        <button type="button" wire:click="setPackageFilter('low')"
                            class="col-filter-pill {{ $packageFilter === 'low' ? 'active' : '' }}">
                            <span class="inline-block w-2 h-2 rounded-full bg-amber-500 me-1"></span>
                            {{ __('رصيد منخفض (1-3 حصص)') }} ({{ $this->stats['low_balance_packages'] }})
                        </button>
                        
                        <button type="button" wire:click="setPackageFilter('expiring')"
                            class="col-filter-pill {{ $packageFilter === 'expiring' ? 'active' : '' }}">
                            <span class="inline-block w-2 h-2 rounded-full bg-purple-500 me-1"></span>
                            {{ __('تنتهي الصلاحية خلال أسبوع') }} ({{ $this->stats['expiring_in_7_days'] }})
                        </button>
                    </div>

                    {{-- Data Table --}}
                    <div class="col-table-wrapper">
                        <table class="col-table">
                            <thead>
                                <tr>
                                    <th>{{ __('الطالب') }}</th>
                                    <th>{{ __('ولي الأمر للتواصل') }}</th>
                                    <th>{{ __('الباقة الحالية') }}</th>
                                    <th class="text-center">{{ __('الرصيد المتبقي') }}</th>
                                    <th>{{ __('صلاحية الباقة') }}</th>
                                    <th class="text-center">{{ __('حالة التحصيل') }}</th>
                                    <th class="text-center">{{ __('إجراءات المتابعة والتحصيل') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->expiringPackages as $item)
                                    <tr>
                                        {{-- Student Info --}}
                                        <td>
                                            <div class="font-bold text-sm" style="color: var(--col-text-main);">
                                                {{ $item->student?->name ?? '—' }}
                                            </div>
                                            <div class="text-[11px]" style="color: var(--col-text-muted);">
                                                {{ $item->student?->studentProfile?->gradeLevel?->name ?? $item->student?->email }}
                                            </div>
                                            @if ($item->student?->phone)
                                                <div class="text-[11px] font-mono flex items-center gap-1.5 mt-0.5" style="color: var(--col-text-dim);">
                                                    <span>{{ $item->student->phone }}</span>
                                                    @if ($item->studentWhatsAppUrl)
                                                        <a href="{{ $item->studentWhatsAppUrl }}" target="_blank" title="{{ __('محادثة واتساب مع الطالب') }}"
                                                            class="text-emerald-500 hover:text-emerald-400">
                                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Parent Info --}}
                                        <td>
                                            @if ($item->parent)
                                                <div class="font-semibold text-sm" style="color: var(--col-text-main);">
                                                    {{ $item->parent->name }}
                                                </div>
                                                <div class="text-[11px] font-mono flex items-center gap-1.5 mt-0.5" style="color: var(--col-text-dim);">
                                                    <span>{{ $item->parent->phone ?? '—' }}</span>
                                                    @if ($item->parentWhatsAppUrl)
                                                        <a href="{{ $item->parentWhatsAppUrl }}" target="_blank"
                                                            class="col-action-btn col-btn-whatsapp py-0.5 px-2 text-[10px]"
                                                            title="{{ __('فتح واتساب ولي الأمر برسالة تذكير بالسداد') }}">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                            <span>{{ __('واتساب ولي الأمر') }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs italic" style="color: var(--col-text-dim);">{{ __('غير مسجل') }}</span>
                                            @endif
                                        </td>

                                        {{-- Package Name --}}
                                        <td>
                                            <div class="font-bold text-sm" style="color: var(--col-text-main);">
                                                {{ $item->pkgName }}
                                            </div>
                                            <div class="text-[11px]" style="color: var(--col-text-muted);">
                                                {{ __('سعة:') }} {{ $item->total }} {{ __('حصص') }} • {{ __('مستخدم:') }} {{ $item->used }}
                                            </div>
                                        </td>

                                        {{-- Remaining Sessions --}}
                                        <td class="text-center">
                                            @if ($item->remaining <= 0)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-red-500/15 text-red-500 border border-red-500/30">
                                                    0 {{ __('حصص متبقية') }}
                                                </span>
                                            @elseif ($item->remaining <= 3)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-500/15 text-amber-500 border border-amber-500/30">
                                                    {{ $item->remaining }} {{ __('حصص متبقية') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-500 border border-emerald-500/30">
                                                    {{ $item->remaining }} {{ __('حصص متبقية') }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Expiry Date --}}
                                        <td>
                                            @if ($item->expiryFormatted)
                                                <div class="text-xs font-mono font-bold" style="color: var(--col-text-main);">
                                                    {{ $item->expiryFormatted }}
                                                </div>
                                                <div class="text-[10px] {{ str_contains($item->expiryCountdown ?? '', 'منتهية') ? 'text-red-500 font-bold' : 'text-gray-400' }}">
                                                    {{ $item->expiryCountdown }}
                                                </div>
                                            @else
                                                <span class="text-xs" style="color: var(--col-text-dim);">{{ __('بدون تاريخ انتهاء') }}</span>
                                            @endif
                                        </td>

                                        {{-- Urgency Badge --}}
                                        <td class="text-center">
                                            @if ($item->urgency === 'danger')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-red-500/10 text-red-500 border border-red-500/25">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                    {{ $item->urgencyLabel }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-amber-500/10 text-amber-500 border border-amber-500/25">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $item->urgencyLabel }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                {{-- WhatsApp Reminder button --}}
                                                @if ($item->parentWhatsAppUrl || $item->studentWhatsAppUrl)
                                                    <a href="{{ $item->parentWhatsAppUrl ?: $item->studentWhatsAppUrl }}" target="_blank"
                                                        class="col-action-btn col-btn-whatsapp"
                                                        title="{{ __('إرسال رسالة تذكير بالسداد عبر الواتساب') }}">
                                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                                        <span>{{ __('واتساب') }}</span>
                                                    </a>
                                                @endif

                                                {{-- Push notification reminder --}}
                                                <button type="button" wire:click="sendPaymentReminder({{ $item->record->id }})"
                                                    class="col-action-btn col-btn-reminder"
                                                    title="{{ __('إرسال إشعار فوري لحظي داخل النظام وتطبيق الجوال') }}">
                                                    <i class="fa-solid fa-bell text-xs"></i>
                                                    <span>{{ __('إشعار فوري') }}</span>
                                                </button>

                                                {{-- Renew package button --}}
                                                <button type="button" wire:click="openRenewPackageModal({{ $item->record->id }})"
                                                    class="col-action-btn col-btn-renew"
                                                    title="{{ __('تجديد الباقة واحتساب دورة حصص جديدة') }}">
                                                    <i class="fa-solid fa-rotate text-xs"></i>
                                                    <span>{{ __('تجديد الباقة') }}</span>
                                                </button>

                                                {{-- Quick credit injection --}}
                                                <button type="button" wire:click="openAddCreditsModal({{ $item->record->id }})"
                                                    class="col-action-btn col-btn-credits"
                                                    title="{{ __('إضافة حصص إضافية سريعة كرصيد') }}">
                                                    <i class="fa-solid fa-plus text-xs"></i>
                                                    <span>{{ __('شحن سريع') }}</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl border border-emerald-500/20">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                                <p class="font-extrabold text-base" style="color: var(--col-text-main);">
                                                    {{ __('لا توجد مستحقات باقات معلقة حالياً!') }}
                                                </p>
                                                <p class="text-xs" style="color: var(--col-text-muted);">
                                                    {{ __('جميع الطلاب المشتركين لديهم رصيد كافٍ من الحصص وصلاحياتهم سارية.') }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ═════════════════════════════════════════════════════════════ --}}
            {{-- TAB 2: EXPIRING RECURRING SCHEDULE CYCLES                     --}}
            {{-- ═════════════════════════════════════════════════════════════ --}}
            @if ($activeTab === 'schedules')
                <div class="p-4 sm:p-5 space-y-4">
                    
                    {{-- Notice banner explaining the feature --}}
                    <div class="p-4 rounded-xl flex items-center gap-3" style="background: rgba(13, 148, 136, 0.1); border: 1px solid rgba(13, 148, 136, 0.25); color: var(--col-text-main);">
                        <div class="w-9 h-9 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0 text-lg">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="text-xs leading-relaxed">
                            <span class="font-extrabold text-teal-400">{{ __('متابعة الجداول الشهرية المنتهية:') }}</span>
                            <span>{{ __('هنا يظهر الطلاب الذين لديهم جدول حصص دوري (مثلاً لشهر كامل سينتهي قريباً)، لتتمكن الإدارة من تذكيرهم بالسداد وتمديد الجدول وتوليد حصص الشهر القادم بنقرة واحدة.') }}</span>
                        </div>
                    </div>

                    {{-- Sub Filter Pills --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                        <span class="font-bold me-1" style="color: var(--col-text-muted);">
                            <i class="fa-solid fa-filter me-1"></i>{{ __('تصفية:') }}
                        </span>
                        
                        <button type="button" wire:click="setScheduleFilter('all')"
                            class="col-filter-pill {{ $scheduleFilter === 'all' ? 'active' : '' }}">
                            {{ __('جميع الجداول القريبة') }}
                        </button>
                        
                        <button type="button" wire:click="setScheduleFilter('ending_soon')"
                            class="col-filter-pill {{ $scheduleFilter === 'ending_soon' ? 'active' : '' }}">
                            <span class="inline-block w-2 h-2 rounded-full bg-amber-500 me-1"></span>
                            {{ __('تنتهي خلال 14 يوماً') }}
                        </button>
                        
                        <button type="button" wire:click="setScheduleFilter('ended')"
                            class="col-filter-pill {{ $scheduleFilter === 'ended' ? 'active' : '' }}">
                            <span class="inline-block w-2 h-2 rounded-full bg-red-500 me-1"></span>
                            {{ __('الدورة انتهت بالفعل') }}
                        </button>
                    </div>

                    {{-- Data Table --}}
                    <div class="col-table-wrapper">
                        <table class="col-table">
                            <thead>
                                <tr>
                                    <th>{{ __('الطالب المعني') }}</th>
                                    <th>{{ __('المقرر / المادة / العنوان') }}</th>
                                    <th>{{ __('المعلم') }}</th>
                                    <th>{{ __('نمط التكرار والموعد') }}</th>
                                    <th class="text-center">{{ __('تاريخ نهاية الدورة') }}</th>
                                    <th class="text-center">{{ __('الحصص المتبقية') }}</th>
                                    <th class="text-center">{{ __('الإجراءات والتمديد') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->endingSchedules as $schItem)
                                    <tr>
                                        {{-- Student Info --}}
                                        <td>
                                            @if ($schItem->student)
                                                <div class="font-bold text-sm" style="color: var(--col-text-main);">
                                                    {{ $schItem->student->name }}
                                                </div>
                                                <div class="text-[11px]" style="color: var(--col-text-muted);">
                                                    {{ $schItem->student->studentProfile?->gradeLevel?->name ?? $schItem->student->email }}
                                                </div>
                                                @if ($schItem->student->phone)
                                                    <div class="text-[11px] font-mono flex items-center gap-1 mt-0.5" style="color: var(--col-text-dim);">
                                                        <span>{{ $schItem->student->phone }}</span>
                                                        @if ($schItem->studentWhatsAppUrl)
                                                            <a href="{{ $schItem->studentWhatsAppUrl }}" target="_blank" title="{{ __('واتساب الطالب') }}"
                                                                class="text-emerald-500 hover:text-emerald-400">
                                                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold" style="background: var(--col-pill-bg); color: var(--col-text-muted);">
                                                    {{ __('جدول جماعي / عام') }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Course / Subject / Title --}}
                                        <td>
                                            <div class="font-bold text-sm" style="color: var(--col-text-main);">
                                                {{ $schItem->record->title ?: $schItem->courseTitle }}
                                            </div>
                                            <div class="text-[11px] font-bold text-teal-500 mt-0.5">
                                                {{ $schItem->courseTitle }} • {{ $schItem->subjectName }}
                                            </div>
                                        </td>

                                        {{-- Teacher --}}
                                        <td>
                                            <div class="font-semibold text-sm" style="color: var(--col-text-main);">
                                                {{ $schItem->teacher?->name ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Recurrence Pattern --}}
                                        <td>
                                            <div class="text-xs font-bold" style="color: var(--col-text-main);">
                                                {{ $schItem->daysFormatted }}
                                            </div>
                                            <div class="text-[11px]" style="color: var(--col-text-muted);">
                                                {{ __('الساعة:') }} {{ $schItem->startTime }}
                                            </div>
                                        </td>

                                        {{-- End Date & Countdown --}}
                                        <td class="text-center">
                                            <div class="font-mono font-bold text-xs" style="color: var(--col-text-main);">
                                                {{ $schItem->endDateFormatted }}
                                            </div>
                                            @if ($schItem->statusColor === 'danger')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-red-500/10 text-red-500 border border-red-500/25 mt-1">
                                                    {{ $schItem->statusBadge }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-500/10 text-amber-500 border border-amber-500/25 mt-1">
                                                    {{ $schItem->statusBadge }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Remaining Sessions --}}
                                        <td class="text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-500/10 text-blue-400 border border-blue-500/25">
                                                {{ $schItem->remainingSessions }} {{ __('حصص متبقية') }}
                                            </span>
                                            <div class="text-[10px] mt-0.5" style="color: var(--col-text-dim);">
                                                {{ __('إجمالي المولدة:') }} {{ $schItem->totalSessions }}
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td>
                                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                                {{-- WhatsApp Reminder button --}}
                                                @if ($schItem->parentWhatsAppUrl || $schItem->studentWhatsAppUrl)
                                                    <a href="{{ $schItem->parentWhatsAppUrl ?: $schItem->studentWhatsAppUrl }}" target="_blank"
                                                        class="col-action-btn col-btn-whatsapp"
                                                        title="{{ __('إرسال رسالة تذكير لتأكيد تجديد الجدول للشهر القادم عبر الواتساب') }}">
                                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                                        <span>{{ __('واتساب التجديد') }}</span>
                                                    </a>
                                                @endif

                                                {{-- Extend Cycle Modal Button --}}
                                                <button type="button" wire:click="openExtendScheduleModal({{ $schItem->record->id }})"
                                                    class="col-action-btn col-btn-renew"
                                                    title="{{ __('تمديد الدورة وتوليد حصص الشهر القادم بنقرة واحدة') }}">
                                                    <i class="fa-solid fa-calendar-plus text-xs"></i>
                                                    <span>{{ __('تمديد الدورة للشهر الجديد') }}</span>
                                                </button>

                                                {{-- View in schedules manager --}}
                                                <a href="/admin/course-schedules" target="_blank"
                                                    class="col-action-btn py-1 px-2.5 text-xs font-semibold"
                                                    style="background: var(--col-pill-bg); border-color: var(--col-pill-border); color: var(--col-text-muted);"
                                                    title="{{ __('عرض في إدارة الجداول') }}">
                                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="flex flex-col items-center justify-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-2xl border border-emerald-500/20">
                                                    <i class="fa-solid fa-calendar-check"></i>
                                                </div>
                                                <p class="font-extrabold text-base" style="color: var(--col-text-main);">
                                                    {{ __('لا توجد جداول دورية قريبة من الانتهاء حالياً') }}
                                                </p>
                                                <p class="text-xs" style="color: var(--col-text-muted);">
                                                    {{ __('جميع الدورات الشهرية والجداول الدورية جارية بنجاح ومواعيدها ممتدة.') }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── MODAL 1: RENEW STUDENT PACKAGE ───────────────────────────── --}}
        @if ($showRenewModal)
            <div class="col-modal-backdrop">
                <div class="col-modal-card">
                    <div class="col-modal-header">
                        <div class="col-modal-title">
                            <i class="fa-solid fa-rotate text-teal-500"></i>
                            <span>{{ __('تجديد باقة الطالب وإعادة التفعيل') }}</span>
                        </div>
                        <button type="button" wire:click="$set('showRenewModal', false)" class="text-gray-400 hover:text-white text-xl font-bold cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-3.5 text-xs sm:text-sm">
                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('اختر خطة الباقة (اختياري)') }}
                            </label>
                            <select wire:model="newTemplateId" class="col-input">
                                <option value="">{{ __('— بدون قالب (مخصص) —') }}</option>
                                @foreach (\App\Models\PackageTemplate::where('is_active', true)->get() as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->sessions_count }} حصة)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('عدد الحصص الجديد للباقة') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1" wire:model="newTotalSessions" class="col-input font-bold text-lg text-center">
                            <p class="text-[11px] mt-1" style="color: var(--col-text-dim);">
                                {{ __('سيتم تعيين هذا العدد كرصيد متبقٍ جديد وإعادة تفعيل حالة الباقة وتصفير الاستخدام.') }}
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('تاريخ الانتهاء الجديد (اختياري)') }}
                            </label>
                            <input type="datetime-local" wire:model="newExpiresAt" class="col-input">
                        </div>

                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('ملاحظة / سبب التجديد') }}
                            </label>
                            <input type="text" wire:model="renewReason" class="col-input">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3" style="border-top: 1px solid var(--col-table-border);">
                        <button type="button" wire:click="$set('showRenewModal', false)"
                            class="col-tab-btn inactive text-xs">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitRenewPackage"
                            class="col-tab-btn active text-xs">
                            <i class="fa-solid fa-check me-1"></i>
                            {{ __('تأكيد التجديد وتفعيل الرصيد') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── MODAL 2: QUICK ADD CREDITS ───────────────────────────────── --}}
        @if ($showAddCreditsModal)
            <div class="col-modal-backdrop">
                <div class="col-modal-card" style="max-width: 24rem;">
                    <div class="col-modal-header">
                        <div class="col-modal-title">
                            <i class="fa-solid fa-plus-circle text-emerald-500"></i>
                            <span>{{ __('إضافة رصيد حصص سريع') }}</span>
                        </div>
                        <button type="button" wire:click="$set('showAddCreditsModal', false)" class="text-gray-400 hover:text-white text-xl font-bold cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-3.5 text-xs sm:text-sm">
                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('عدد الحصص الإضافية') }}
                            </label>
                            <input type="number" min="1" max="50" wire:model="creditsToAdd" class="col-input font-black text-2xl text-center">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" wire:click="$set('creditsToAdd', 2)"
                                class="col-filter-pill text-center font-bold {{ $creditsToAdd == 2 ? 'active' : '' }}">
                                +2 حصص
                            </button>
                            <button type="button" wire:click="$set('creditsToAdd', 4)"
                                class="col-filter-pill text-center font-bold {{ $creditsToAdd == 4 ? 'active' : '' }}">
                                +4 حصص
                            </button>
                            <button type="button" wire:click="$set('creditsToAdd', 8)"
                                class="col-filter-pill text-center font-bold {{ $creditsToAdd == 8 ? 'active' : '' }}">
                                +8 حصص
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3" style="border-top: 1px solid var(--col-table-border);">
                        <button type="button" wire:click="$set('showAddCreditsModal', false)"
                            class="col-tab-btn inactive text-xs">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitAddCredits"
                            class="col-action-btn col-btn-credits px-4 py-2 text-xs">
                            <i class="fa-solid fa-check me-1"></i>
                            {{ __('إضافة الرصيد فوراً') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── MODAL 3: EXTEND RECURRING SCHEDULE CYCLE ─────────────────── --}}
        @if ($showExtendScheduleModal)
            <div class="col-modal-backdrop">
                <div class="col-modal-card">
                    <div class="col-modal-header">
                        <div class="col-modal-title">
                            <i class="fa-solid fa-calendar-plus text-teal-500"></i>
                            <span>{{ __('تمديد الدورة وتجديد الجدول الشهري') }}</span>
                        </div>
                        <button type="button" wire:click="$set('showExtendScheduleModal', false)" class="text-gray-400 hover:text-white text-xl font-bold cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-3.5 text-xs sm:text-sm">
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-400 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-base shrink-0"></i>
                            <span>{{ __('سيقوم النظام تلقائياً بتوليد الحصص المباشرة المتكررة للفترة الممتدة وحفظها في جدول مواعيد الطالب والمعلم.') }}</span>
                        </div>

                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('تاريخ نهاية الدورة الجديد') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="newScheduleEndDate" class="col-input font-bold text-base">
                            <p class="text-[11px] mt-1" style="color: var(--col-text-dim);">
                                {{ __('حدد تاريخ نهاية الدورة الشهرية الجديدة (مثال: بعد شهر كامل من الدورة السابقة).') }}
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold mb-1" style="color: var(--col-text-muted);">
                                {{ __('ملاحظة التمديد') }}
                            </label>
                            <input type="text" wire:model="extendReason" class="col-input">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3" style="border-top: 1px solid var(--col-table-border);">
                        <button type="button" wire:click="$set('showExtendScheduleModal', false)"
                            class="col-tab-btn inactive text-xs">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitExtendSchedule"
                            class="col-tab-btn active text-xs">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i>
                            {{ __('تأكيد التمديد وتوليد الحصص') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
