<x-filament-panels::page>
    {{-- External Icon Library fallback to ensure 100% reliable icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="elite-collections-dashboard" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <style>
            /* ─────────────────────────────────────────────────────────────
               ELITE COLLECTIONS & RENEWALS: ULTRA-PREMIUM RESPONSIVE DESIGN
               Self-contained CSS with zero reliance on missing utility classes
            ───────────────────────────────────────────────────────────── */
            .elite-collections-dashboard {
                --ec-font-sans: 'Tajawal', 'Cairo', 'Inter', system-ui, -apple-system, sans-serif;
                font-family: var(--ec-font-sans);
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
                width: 100%;
                box-sizing: border-box;

                /* Light Theme Variables */
                --ec-bg-surface: #ffffff;
                --ec-bg-surface-elevated: #ffffff;
                --ec-bg-card-subtle: #f8fafc;
                --ec-border-subtle: #e2e8f0;
                --ec-border-highlight: #cbd5e1;
                --ec-text-main: #0f172a;
                --ec-text-muted: #475569;
                --ec-text-dim: #94a3b8;
                --ec-table-head-bg: #f8fafc;
                --ec-table-row-hover: #f1f5f9;
                --ec-table-border: #e2e8f0;
                --ec-input-bg: #f8fafc;
                --ec-input-border: #cbd5e1;

                /* Brand Accent Gradients */
                --ec-teal-grad: linear-gradient(135deg, #0d9488, #059669);
                --ec-teal-glow: 0 8px 24px -6px rgba(13, 148, 136, 0.35);
                --ec-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            }

            /* Dark Theme Overrides */
            html.dark .elite-collections-dashboard,
            .dark .elite-collections-dashboard,
            [data-theme="dark"] .elite-collections-dashboard {
                --ec-bg-surface: #0f172a;
                --ec-bg-surface-elevated: #1e293b;
                --ec-bg-card-subtle: #141f36;
                --ec-border-subtle: rgba(255, 255, 255, 0.08);
                --ec-border-highlight: rgba(255, 255, 255, 0.16);
                --ec-text-main: #f8fafc;
                --ec-text-muted: #94a3b8;
                --ec-text-dim: #64748b;
                --ec-table-head-bg: #141f36;
                --ec-table-row-hover: rgba(30, 41, 59, 0.7);
                --ec-table-border: rgba(255, 255, 255, 0.06);
                --ec-input-bg: #141f36;
                --ec-input-border: rgba(255, 255, 255, 0.12);
                --ec-card-shadow: 0 8px 32px -4px rgba(0, 0, 0, 0.35);
            }

            /* ── 1. HEADER EXECUTIVE BANNER ── */
            .ec-header-card {
                position: relative;
                overflow: hidden;
                padding: 1.35rem 1.5rem;
                border-radius: 1.25rem;
                background: var(--ec-bg-surface);
                border: 1px solid var(--ec-border-subtle);
                box-shadow: var(--ec-card-shadow);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.25rem;
                flex-wrap: wrap;
                box-sizing: border-box;
            }
            .ec-header-main {
                display: flex;
                align-items: center;
                gap: 1rem;
                flex: 1;
                min-width: 260px;
            }
            .ec-header-icon {
                width: 3.25rem !important;
                height: 3.25rem !important;
                min-width: 3.25rem !important;
                max-width: 3.25rem !important;
                border-radius: 1rem;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                background: var(--ec-teal-grad);
                box-shadow: var(--ec-teal-glow);
                color: #ffffff;
                font-size: 1.35rem;
                flex-shrink: 0;
            }
            .ec-header-text {
                display: flex;
                flex-direction: column;
                gap: 0.3rem;
            }
            .ec-header-title-row {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                flex-wrap: wrap;
            }
            .ec-header-title {
                font-size: 1.35rem;
                font-weight: 900;
                letter-spacing: -0.02em;
                color: var(--ec-text-main);
                margin: 0;
                line-height: 1.2;
            }
            .ec-live-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.2rem 0.65rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 800;
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.3);
                white-space: nowrap;
            }
            .ec-live-dot {
                width: 0.5rem;
                height: 0.5rem;
                border-radius: 50%;
                background: #10b981;
                animation: ecPulse 1.5s infinite;
            }
            @keyframes ecPulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.4; transform: scale(0.85); }
            }
            .ec-header-desc {
                font-size: 0.825rem;
                color: var(--ec-text-muted);
                margin: 0;
                line-height: 1.45;
            }
            .ec-header-actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            @media (max-width: 768px) {
                .ec-header-card {
                    flex-direction: column;
                    align-items: stretch;
                    padding: 1.15rem;
                    gap: 1rem;
                }
                .ec-header-main {
                    align-items: flex-start;
                }
                .ec-header-title {
                    font-size: 1.15rem;
                }
                .ec-header-actions {
                    width: 100%;
                    justify-content: flex-start;
                }
            }

            /* ── 2. BULLETPROOF 4-COLUMN KPI GRID ── */
            .ec-kpi-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 1rem !important;
                width: 100% !important;
                box-sizing: border-box;
            }
            @media (max-width: 1200px) {
                .ec-kpi-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
            }
            @media (max-width: 640px) {
                .ec-kpi-grid {
                    grid-template-columns: 1fr !important;
                    gap: 0.75rem !important;
                }
            }

            /* ── KPI CARD ── */
            .ec-kpi-card {
                background: var(--ec-bg-surface);
                border: 1px solid var(--ec-border-subtle);
                border-radius: 1.25rem;
                padding: 1.15rem 1.25rem;
                position: relative;
                overflow: hidden;
                box-shadow: var(--ec-card-shadow);
                transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                min-height: 135px;
                cursor: pointer;
                user-select: none;
                box-sizing: border-box;
            }
            .ec-kpi-card:hover {
                transform: translateY(-3px);
                border-color: var(--ec-border-highlight);
                box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.12);
            }
            .ec-kpi-card.ec-kpi-active {
                transform: translateY(-4px);
            }
            .ec-card-red.ec-kpi-active {
                border-color: #ef4444 !important;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5), 0 16px 32px -8px rgba(239, 68, 68, 0.3) !important;
            }
            .ec-card-amber.ec-kpi-active {
                border-color: #f59e0b !important;
                box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.5), 0 16px 32px -8px rgba(245, 158, 11, 0.3) !important;
            }
            .ec-card-purple.ec-kpi-active {
                border-color: #a855f7 !important;
                box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.5), 0 16px 32px -8px rgba(168, 85, 247, 0.3) !important;
            }
            .ec-card-emerald.ec-kpi-active {
                border-color: #10b981 !important;
                box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.5), 0 16px 32px -8px rgba(16, 185, 129, 0.3) !important;
            }

            .ec-kpi-card::before {
                content: '';
                position: absolute;
                inset-inline-start: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                border-radius: 4px;
            }
            .ec-card-red::before { background: linear-gradient(180deg, #ef4444, #dc2626); }
            .ec-card-amber::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
            .ec-card-purple::before { background: linear-gradient(180deg, #a855f7, #7e22ce); }
            .ec-card-emerald::before { background: linear-gradient(180deg, #10b981, #059669); }

            .ec-card-glow {
                position: absolute;
                top: -30px;
                inset-inline-end: -30px;
                width: 90px;
                height: 90px;
                border-radius: 50%;
                filter: blur(28px);
                opacity: 0.18;
                pointer-events: none;
            }
            .ec-card-red .ec-card-glow { background: #ef4444; }
            .ec-card-amber .ec-card-glow { background: #f59e0b; }
            .ec-card-purple .ec-card-glow { background: #a855f7; }
            .ec-card-emerald .ec-card-glow { background: #10b981; }

            .ec-kpi-top {
                display: flex !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
                gap: 0.5rem;
            }
            .ec-kpi-info {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
            }
            .ec-kpi-title-row {
                display: flex;
                align-items: center;
                gap: 0.4rem;
                flex-wrap: wrap;
            }
            .ec-kpi-label {
                font-size: 0.775rem;
                font-weight: 700;
                color: var(--ec-text-muted);
            }
            .ec-kpi-active-pill {
                font-size: 0.65rem;
                font-weight: 800;
                padding: 0.1rem 0.45rem;
                border-radius: 9999px;
                white-space: nowrap;
            }
            .ec-kpi-value {
                font-size: 2.15rem;
                font-weight: 900;
                letter-spacing: -0.03em;
                line-height: 1.1;
                margin-top: 0.15rem;
            }
            .ec-kpi-icon {
                width: 2.75rem !important;
                height: 2.75rem !important;
                min-width: 2.75rem !important;
                border-radius: 0.85rem;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 1.15rem;
                flex-shrink: 0;
                transition: transform 0.2s ease;
            }
            .ec-kpi-card:hover .ec-kpi-icon {
                transform: scale(1.06) rotate(3deg);
            }
            .ec-kpi-footer {
                margin-top: 0.75rem;
                padding-top: 0.6rem;
                border-top: 1px solid rgba(148, 163, 184, 0.15);
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                font-size: 0.725rem;
                font-weight: 700;
            }

            /* ── 3. DYNAMIC KPI DETAILS LOGIC BANNER ── */
            .ec-kpi-banner {
                background: var(--ec-bg-surface);
                border: 1px solid var(--ec-border-subtle);
                border-radius: 1.25rem;
                padding: 1.25rem;
                position: relative;
                overflow: hidden;
                box-shadow: var(--ec-card-shadow);
                display: flex;
                flex-direction: column;
                gap: 1rem;
                animation: ecFadeInUp 0.22s ease-out;
            }
            .ec-kpi-banner.banner-red {
                border-color: rgba(239, 68, 68, 0.35);
                background: linear-gradient(135deg, var(--ec-bg-surface) 0%, rgba(239, 68, 68, 0.05) 100%);
            }
            .ec-kpi-banner.banner-amber {
                border-color: rgba(245, 158, 11, 0.35);
                background: linear-gradient(135deg, var(--ec-bg-surface) 0%, rgba(245, 158, 11, 0.05) 100%);
            }
            .ec-kpi-banner.banner-purple {
                border-color: rgba(168, 85, 247, 0.35);
                background: linear-gradient(135deg, var(--ec-bg-surface) 0%, rgba(168, 85, 247, 0.05) 100%);
            }
            .ec-kpi-banner.banner-emerald {
                border-color: rgba(16, 185, 129, 0.35);
                background: linear-gradient(135deg, var(--ec-bg-surface) 0%, rgba(16, 185, 129, 0.05) 100%);
            }

            .ec-banner-header {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 0.75rem;
                flex-wrap: wrap;
                padding-bottom: 0.85rem;
                border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            }
            .ec-banner-lead {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                flex: 1;
                min-width: 260px;
            }
            .ec-banner-icon {
                width: 2.75rem !important;
                height: 2.75rem !important;
                min-width: 2.75rem !important;
                border-radius: 0.85rem;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            .ec-banner-titles {
                display: flex;
                flex-direction: column;
                gap: 0.2rem;
            }
            .ec-banner-title-line {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            .ec-banner-title {
                font-size: 1.05rem;
                font-weight: 900;
                color: var(--ec-text-main);
                margin: 0;
            }
            .ec-banner-desc {
                font-size: 0.775rem;
                color: var(--ec-text-muted);
                margin: 0;
            }
            .ec-banner-actions {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                flex-wrap: wrap;
            }
            .ec-btn-clear-kpi {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.55rem 1.1rem;
                border-radius: 0.85rem;
                background: rgba(239, 68, 68, 0.14);
                border: 1px solid rgba(239, 68, 68, 0.38);
                color: #fca5a5;
                font-size: 0.8rem;
                font-weight: 800;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .ec-btn-clear-kpi:hover {
                background: rgba(239, 68, 68, 0.26);
                border-color: rgba(239, 68, 68, 0.65);
                color: #ffffff;
                transform: translateY(-1px);
            }
            .ec-btn-kpi-info {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.55rem 0.95rem;
                border-radius: 0.85rem;
                background: rgba(148, 163, 184, 0.1);
                border: 1px solid rgba(148, 163, 184, 0.22);
                color: #cbd5e1;
                font-size: 0.775rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .ec-btn-kpi-info:hover {
                background: rgba(148, 163, 184, 0.2);
                border-color: rgba(148, 163, 184, 0.4);
                color: #ffffff;
            }
            .ec-filter-active-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 800;
                background: rgba(16, 185, 129, 0.14);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.32);
            }
            .ec-count-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.725rem;
                font-weight: 800;
                background: rgba(148, 163, 184, 0.14);
                color: #e2e8f0;
                border: 1px solid rgba(148, 163, 184, 0.28);
            }
            .ec-guide-grid {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 1rem !important;
            }
            @media (max-width: 960px) {
                .ec-guide-grid {
                    grid-template-columns: 1fr !important;
                }
            }
            .ec-guide-card {
                background: rgba(15, 23, 42, 0.55);
                border: 1px solid rgba(148, 163, 184, 0.18);
                border-radius: 1rem;
                padding: 1.15rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 0.85rem;
                transition: all 0.22s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            }
            .ec-guide-card:hover {
                border-color: rgba(148, 163, 184, 0.35);
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }
            html:not(.dark) .ec-guide-card {
                background: #ffffff;
                border-color: #e2e8f0;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            }
            html:not(.dark) .ec-guide-card:hover {
                border-color: #cbd5e1;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
            }
            .ec-guide-card-top {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .ec-step-badge {
                width: 1.85rem;
                height: 1.85rem;
                min-width: 1.85rem;
                border-radius: 9999px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 900;
                font-size: 0.85rem;
                flex-shrink: 0;
            }
            .ec-step-1 {
                background: rgba(56, 189, 248, 0.18);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.45);
            }
            .ec-step-2 {
                background: rgba(245, 158, 11, 0.18);
                color: #f59e0b;
                border: 1px solid rgba(245, 158, 11, 0.45);
            }
            .ec-step-3 {
                background: rgba(16, 185, 129, 0.18);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.45);
            }
            .ec-guide-card-title {
                display: flex;
                flex-direction: column;
                gap: 0.1rem;
            }
            .ec-guide-card-title span {
                font-weight: 800;
                font-size: 0.875rem;
                color: var(--ec-text-main);
            }
            .ec-guide-card-title small {
                font-size: 0.7rem;
                color: var(--ec-text-muted);
                font-weight: 600;
            }
            .ec-guide-card-body {
                color: var(--ec-text-muted);
                line-height: 1.6;
                font-size: 0.785rem;
                margin: 0;
                font-weight: 500;
            }
            .ec-guide-card-footer {
                padding-top: 0.65rem;
                border-top: 1px dashed rgba(148, 163, 184, 0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .ec-guide-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 0.5rem;
                font-size: 0.7rem;
                font-weight: 700;
            }
            .ec-pill-info {
                background: rgba(56, 189, 248, 0.12);
                color: #38bdf8;
                border: 1px solid rgba(56, 189, 248, 0.28);
            }
            .ec-pill-target {
                background: rgba(245, 158, 11, 0.12);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.28);
            }
            .ec-action-guide-tags {
                display: flex;
                align-items: center;
                gap: 0.4rem;
                flex-wrap: wrap;
            }
            .ec-hint-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.25rem 0.55rem;
                border-radius: 0.45rem;
                font-size: 0.675rem;
                font-weight: 800;
            }
            .ec-hint-whatsapp {
                background: rgba(34, 197, 94, 0.15);
                color: #4ade80;
                border: 1px solid rgba(34, 197, 94, 0.35);
            }
            .ec-hint-notify {
                background: rgba(168, 85, 247, 0.15);
                color: #c084fc;
                border: 1px solid rgba(168, 85, 247, 0.35);
            }
            .ec-hint-renew {
                background: rgba(20, 184, 166, 0.15);
                color: #2dd4bf;
                border: 1px solid rgba(20, 184, 166, 0.35);
            }
            .ec-banner-bottom-bar {
                padding: 0.75rem 1.1rem;
                border-radius: 0.85rem;
                background: rgba(15, 23, 42, 0.45);
                border: 1px solid rgba(148, 163, 184, 0.16);
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.75rem;
            }
            html:not(.dark) .ec-banner-bottom-bar {
                background: #f1f5f9;
                border-color: #cbd5e1;
            }
            .ec-pulse-arrow {
                width: 1.5rem;
                height: 1.5rem;
                border-radius: 9999px;
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* ── PROMPT BOX (WHEN NO KPI IS SELECTED) ── */
            .ec-prompt-box {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                flex-wrap: wrap !important;
                gap: 0.75rem;
                padding: 0.85rem 1.25rem;
                border-radius: 1rem;
                border: 1px dashed var(--ec-border-highlight);
                background: var(--ec-bg-card-subtle);
                color: var(--ec-text-muted);
                font-size: 0.8rem;
            }

            /* ── 4. MAIN WORKBENCH SURFACE ── */
            .ec-main-surface {
                background: var(--ec-bg-surface);
                border: 1px solid var(--ec-border-subtle);
                border-radius: 1.25rem;
                box-shadow: var(--ec-card-shadow);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            .ec-toolbar {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 1rem;
                flex-wrap: wrap;
                padding: 1rem 1.25rem;
                border-bottom: 1px solid var(--ec-table-border);
            }
            @media (max-width: 768px) {
                .ec-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 0.85rem;
                }
            }

            /* ── SEGMENTED TAB SWITCHER ── */
            .ec-segmented-nav {
                background: var(--ec-bg-card-subtle);
                border: 1px solid var(--ec-border-subtle);
                border-radius: 0.85rem;
                padding: 0.3rem;
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            @media (max-width: 640px) {
                .ec-segmented-nav {
                    width: 100%;
                    display: flex;
                }
                .ec-tab-btn {
                    flex: 1;
                    justify-content: center;
                    padding: 0.6rem 0.75rem !important;
                    font-size: 0.775rem !important;
                }
            }
            .ec-tab-btn {
                padding: 0.6rem 1.15rem;
                border-radius: 0.7rem;
                font-size: 0.825rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                transition: all 0.2s ease;
                cursor: pointer;
                border: 1px solid transparent;
                color: var(--ec-text-muted);
                background: transparent;
                text-decoration: none;
                white-space: nowrap;
            }
            .ec-tab-btn.active {
                background: var(--ec-teal-grad);
                color: #ffffff !important;
                box-shadow: var(--ec-teal-glow);
            }
            .ec-tab-btn:not(.active):hover {
                color: var(--ec-text-main);
                background: rgba(255, 255, 255, 0.05);
            }

            .ec-badge-count {
                padding: 0.15rem 0.55rem;
                border-radius: 9999px;
                font-size: 0.725rem;
                font-weight: 800;
            }
            .ec-tab-btn.active .ec-badge-count {
                background: rgba(255, 255, 255, 0.25);
                color: #ffffff;
            }
            .ec-tab-btn:not(.active) .ec-badge-count {
                background: rgba(0, 0, 0, 0.06);
                color: var(--ec-text-muted);
            }
            html.dark .ec-tab-btn:not(.active) .ec-badge-count {
                background: rgba(255, 255, 255, 0.1);
                color: #cbd5e1;
            }

            /* ── BULLETPROOF SEARCH BOX ── */
            .ec-search-box {
                position: relative !important;
                display: flex !important;
                align-items: center !important;
                width: 100%;
                max-width: 22rem;
            }
            @media (max-width: 768px) {
                .ec-search-box {
                    max-width: 100% !important;
                }
            }
            .ec-search-input {
                width: 100% !important;
                border-radius: 0.75rem !important;
                border: 1px solid var(--ec-input-border) !important;
                background: var(--ec-input-bg) !important;
                color: var(--ec-text-main) !important;
                padding: 0.65rem 2.4rem 0.65rem 2.4rem !important;
                font-size: 0.85rem !important;
                font-family: inherit !important;
                outline: none !important;
                transition: all 0.2s ease !important;
                box-sizing: border-box !important;
            }
            .ec-search-input:focus {
                border-color: #0d9488 !important;
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2) !important;
                background: var(--ec-bg-surface) !important;
            }
            .ec-search-icon {
                position: absolute !important;
                inset-inline-start: 0.85rem !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                pointer-events: none !important;
                color: var(--ec-text-dim) !important;
                font-size: 0.85rem !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 2 !important;
            }
            .ec-search-clear {
                position: absolute !important;
                inset-inline-end: 0.85rem !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                cursor: pointer !important;
                color: var(--ec-text-dim) !important;
                background: transparent !important;
                border: none !important;
                font-size: 0.85rem !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 2 !important;
            }
            .ec-search-clear:hover {
                color: #ef4444 !important;
            }

            /* ── SUB-FILTERS CHIP BAR ── */
            .ec-filters-bar {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                overflow-x: auto !important;
                padding: 0.75rem 1.25rem !important;
                border-bottom: 1px solid var(--ec-table-border) !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .ec-filters-label {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.775rem;
                font-weight: 800;
                color: var(--ec-text-dim);
                margin-inline-end: 0.25rem;
                white-space: nowrap;
            }
            .ec-filter-chip {
                padding: 0.4rem 0.85rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s ease;
                border: 1px solid var(--ec-border-subtle);
                background: var(--ec-bg-card-subtle);
                color: var(--ec-text-muted);
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .ec-filter-chip:hover {
                border-color: var(--ec-border-highlight);
                color: var(--ec-text-main);
                transform: translateY(-1px);
            }
            .ec-filter-chip.active {
                border-color: #0d9488;
                background: rgba(13, 148, 136, 0.12);
                color: #0d9488;
            }
            html.dark .ec-filter-chip.active {
                background: rgba(20, 184, 166, 0.2);
                color: #2dd4bf;
                border-color: #14b8a6;
            }

            /* ── CONTENT BODY & RESPONSIVE DATA TABLE ── */
            .ec-content-body {
                padding: 1.25rem;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            @media (max-width: 640px) {
                .ec-content-body {
                    padding: 0.85rem;
                }
            }
            .ec-table-container {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border-radius: 0.85rem;
                border: 1px solid var(--ec-table-border);
            }
            .ec-table {
                width: 100%;
                min-width: 900px;
                border-collapse: separate;
                border-spacing: 0;
                text-align: start;
                font-size: 0.8125rem;
            }
            .ec-table thead th {
                background: var(--ec-table-head-bg);
                color: var(--ec-text-muted);
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                padding: 0.9rem 1.15rem;
                border-bottom: 1px solid var(--ec-table-border);
                white-space: nowrap;
            }
            .ec-table tbody td {
                padding: 0.95rem 1.15rem;
                border-bottom: 1px solid var(--ec-table-border);
                vertical-align: middle;
                background: var(--ec-bg-surface);
                transition: background-color 0.15s ease;
            }
            .ec-table tbody tr:hover td {
                background: var(--ec-table-row-hover);
            }
            .ec-table tbody tr:last-child td {
                border-bottom: none;
            }

            /* ── STUDENT CARDLET ── */
            .ec-avatar-circle {
                width: 2.35rem;
                height: 2.35rem;
                border-radius: 50%;
                background: linear-gradient(135deg, #0d9488, #2563eb);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 0.9rem;
                flex-shrink: 0;
                box-shadow: 0 2px 8px rgba(13, 148, 136, 0.25);
            }

            /* ── PARENT CARDLET ── */
            .ec-parent-card {
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(148, 163, 184, 0.16);
                border-radius: 0.75rem;
                padding: 0.5rem 0.75rem;
                min-width: 145px;
                max-width: 195px;
                box-sizing: border-box;
                transition: all 0.2s ease;
            }
            .ec-parent-card:hover {
                border-color: rgba(148, 163, 184, 0.32);
            }
            html:not(.dark) .ec-parent-card {
                background: #f8fafc;
                border-color: #e2e8f0;
            }

            .ec-btn-whatsapp-compact {
                background: #25D366;
                color: #ffffff !important;
                padding: 0.2rem 0.55rem;
                border-radius: 0.5rem;
                font-size: 0.675rem;
                font-weight: 800;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                transition: all 0.18s ease;
                text-decoration: none !important;
                line-height: 1;
                flex-shrink: 0;
                box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);
            }
            .ec-btn-whatsapp-compact:hover {
                background: #1eb857;
                transform: translateY(-1px);
                box-shadow: 0 4px 10px rgba(37, 211, 102, 0.4);
            }

            /* ── REMAINING SESSIONS PILLS ── */
            .ec-balance-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.35rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 800;
                white-space: nowrap;
                line-height: 1.2;
            }
            .ec-balance-zero {
                background: rgba(239, 68, 68, 0.15);
                color: #f87171;
                border: 1px solid rgba(239, 68, 68, 0.35);
                box-shadow: 0 0 10px rgba(239, 68, 68, 0.12);
            }
            .ec-balance-low {
                background: rgba(245, 158, 11, 0.15);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.35);
            }
            .ec-balance-good {
                background: rgba(16, 185, 129, 0.15);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.35);
            }

            /* ── URGENCY STATUS BADGES ── */
            .ec-urgency-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.35rem 0.7rem;
                border-radius: 0.6rem;
                font-size: 0.725rem;
                font-weight: 800;
                white-space: nowrap;
                line-height: 1.2;
            }
            .ec-urgency-danger {
                background: rgba(239, 68, 68, 0.14);
                color: #f87171;
                border: 1px solid rgba(239, 68, 68, 0.32);
            }
            .ec-urgency-warning {
                background: rgba(245, 158, 11, 0.14);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.32);
            }
            .ec-urgency-info {
                background: rgba(168, 85, 247, 0.14);
                color: #c084fc;
                border: 1px solid rgba(168, 85, 247, 0.32);
            }

            /* ── COMPACT 2X2 ACTION GRID & BUTTONS ── */
            .ec-action-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 0.35rem !important;
                width: 100% !important;
                max-width: 13.5rem !important;
                margin: 0 auto !important;
            }
            .ec-btn-action {
                padding: 0.4rem 0.55rem !important;
                border-radius: 0.6rem !important;
                font-size: 0.725rem !important;
                font-weight: 700 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.3rem !important;
                border: 1px solid transparent !important;
                cursor: pointer !important;
                transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
                text-decoration: none !important;
                line-height: 1 !important;
                white-space: nowrap !important;
                box-sizing: border-box !important;
            }
            .ec-btn-action:hover {
                transform: translateY(-1px) !important;
            }
            .ec-btn-act-whatsapp {
                background: rgba(37, 211, 102, 0.16) !important;
                color: #4ade80 !important;
                border-color: rgba(37, 211, 102, 0.35) !important;
            }
            .ec-btn-act-whatsapp:hover {
                background: #25d366 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 10px rgba(37, 211, 102, 0.35) !important;
            }
            .ec-btn-act-notify {
                background: rgba(59, 130, 246, 0.16) !important;
                color: #60a5fa !important;
                border-color: rgba(59, 130, 246, 0.35) !important;
            }
            .ec-btn-act-notify:hover {
                background: #3b82f6 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 10px rgba(59, 130, 246, 0.35) !important;
            }
            .ec-btn-act-renew {
                background: rgba(20, 184, 166, 0.16) !important;
                color: #2dd4bf !important;
                border-color: rgba(20, 184, 166, 0.35) !important;
            }
            .ec-btn-act-renew:hover {
                background: #0d9488 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 10px rgba(13, 148, 136, 0.35) !important;
            }
            .ec-btn-act-credit {
                background: rgba(16, 185, 129, 0.16) !important;
                color: #34d399 !important;
                border-color: rgba(16, 185, 129, 0.35) !important;
            }
            .ec-btn-act-credit:hover {
                background: #059669 !important;
                color: #ffffff !important;
                box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35) !important;
            }

            /* Legacy .ec-btn fallback */
            .ec-btn {
                padding: 0.45rem 0.75rem;
                border-radius: 0.65rem;
                font-size: 0.75rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                cursor: pointer;
                transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
                white-space: nowrap;
                border: 1px solid transparent;
                text-decoration: none !important;
                line-height: 1;
            }
            .ec-btn:hover {
                transform: translateY(-1.5px);
            }
            .ec-btn-whatsapp {
                background: #25D366;
                color: #ffffff !important;
                box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
            }
            .ec-btn-whatsapp:hover {
                background: #1eb857;
                box-shadow: 0 4px 12px rgba(37, 211, 102, 0.45);
            }
            .ec-btn-reminder {
                background: #3b82f6;
                color: #ffffff !important;
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            }
            .ec-btn-reminder:hover {
                background: #2563eb;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.45);
            }
            .ec-btn-renew {
                background: var(--ec-teal-grad);
                color: #ffffff !important;
                box-shadow: var(--ec-teal-glow);
            }
            .ec-btn-renew:hover {
                filter: brightness(1.1);
            }
            .ec-btn-credits {
                background: #059669;
                color: #ffffff !important;
                box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
            }
            .ec-btn-credits:hover {
                background: #047857;
                box-shadow: 0 4px 12px rgba(5, 150, 105, 0.45);
            }

            /* ── TABLE ROW ANIMATION ── */
            @keyframes ecRowFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(4px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .ec-table tbody tr {
                animation: ecRowFadeIn 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            }

            /* ── SEARCH LIVE RESULTS PILL ── */
            .ec-search-results-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.3rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.725rem;
                font-weight: 800;
                background: rgba(13, 148, 136, 0.15);
                color: #2dd4bf;
                border: 1px solid rgba(13, 148, 136, 0.3);
                white-space: nowrap;
                animation: ecRowFadeIn 0.2s ease-out;
            }

            /* ── PROGRESS BAR ── */
            .ec-progress-track {
                width: 100%;
                height: 5px;
                border-radius: 9999px;
                background: rgba(0, 0, 0, 0.12);
                overflow: hidden;
            }
            html.dark .ec-progress-track {
                background: rgba(255, 255, 255, 0.12);
            }
            .ec-progress-fill {
                height: 100%;
                border-radius: 9999px;
                transition: width 0.3s ease;
            }

            /* ── MODALS ── */
            .ec-modal-overlay {
                position: fixed;
                inset: 0;
                z-index: 99999;
                background: rgba(11, 19, 43, 0.78);
                backdrop-filter: blur(8px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                box-sizing: border-box;
            }
            .ec-modal-panel {
                background: var(--ec-bg-surface-elevated);
                border: 1px solid var(--ec-border-highlight);
                border-radius: 1.5rem;
                width: 100%;
                max-width: 30rem;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                padding: 1.75rem;
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
                color: var(--ec-text-main);
                box-sizing: border-box;
                animation: ecFadeInUp 0.2s ease-out;
            }
            .ec-modal-input {
                width: 100%;
                border-radius: 0.75rem;
                padding: 0.65rem 0.85rem;
                font-size: 0.875rem;
                font-weight: 600;
                outline: none;
                transition: all 0.2s ease;
                background: rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(148, 163, 184, 0.25);
                color: var(--ec-text-main);
                box-sizing: border-box;
            }
            html:not(.dark) .ec-modal-input {
                background: #ffffff;
                border-color: #cbd5e1;
                color: #0f172a;
            }
            .ec-modal-input:focus {
                border-color: #14b8a6;
                box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
            }
            .ec-modal-btn-confirm {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.65rem 1.25rem;
                border-radius: 0.75rem;
                font-size: 0.825rem;
                font-weight: 800;
                color: #ffffff;
                background: linear-gradient(135deg, #0d9488, #059669);
                border: 1px solid rgba(20, 184, 166, 0.4);
                box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .ec-modal-btn-confirm:hover:not(:disabled) {
                transform: translateY(-1px);
                box-shadow: 0 6px 16px rgba(13, 148, 136, 0.45);
                background: linear-gradient(135deg, #0f766e, #047857);
            }
            .ec-modal-btn-confirm:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none;
            }
            .ec-modal-btn-cancel {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.65rem 1rem;
                border-radius: 0.75rem;
                font-size: 0.825rem;
                font-weight: 700;
                color: var(--ec-text-muted);
                background: rgba(148, 163, 184, 0.1);
                border: 1px solid rgba(148, 163, 184, 0.2);
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .ec-modal-btn-cancel:hover {
                background: rgba(148, 163, 184, 0.18);
                color: var(--ec-text-main);
            }
            @keyframes ecFadeInUp {
                from { opacity: 0; transform: translateY(12px) scale(0.98); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
        </style>

        {{-- ── 1. PAGE EXECUTIVE HEADER BANNER ──────────────────────────── --}}
        <div class="ec-header-card">
            {{-- Background ambient glow --}}
            <div class="absolute -top-12 -end-12 w-48 h-48 rounded-full pointer-events-none opacity-20" style="background: radial-gradient(circle, #0d9488, transparent 70%); filter: blur(35px);"></div>
            <div class="absolute -bottom-10 start-1/3 w-40 h-40 rounded-full pointer-events-none opacity-15" style="background: radial-gradient(circle, #3b82f6, transparent 70%); filter: blur(30px);"></div>

            <div class="ec-header-main">
                <div class="ec-header-icon">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div class="ec-header-text">
                    <div class="ec-header-title-row">
                        <h1 class="ec-header-title">
                            {{ __('متابعة التحصيلات وتجديد الاشتراكات') }}
                        </h1>
                        <span class="ec-live-badge">
                            <span class="ec-live-dot"></span>
                            {{ __('مراقبة مباشرة 24/7') }}
                        </span>
                    </div>
                    <p class="ec-header-desc">
                        {{ __('نظام الإدارة الاستباقية للباقات الموشكة على الانتهاء، تذكيرات الواتساب، وتجديد الدورات الشهرية بنقرة واحدة.') }}
                    </p>
                </div>
            </div>

            <div class="ec-header-actions">
                <button type="button" wire:click="$refresh" class="ec-filter-chip" title="{{ __('تحديث البيانات') }}">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    <span>{{ __('تحديث لحظي') }}</span>
                </button>
                <a href="/admin/student-packages" class="ec-filter-chip" title="{{ __('سجل الباقات الشامل') }}">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>{{ __('إدارة الباقات') }}</span>
                </a>
            </div>
        </div>

        {{-- ── 2. EXECUTIVE 4-COLUMN KPI CARDS (BULLETPROOF GRID) ────────── --}}
        <div class="ec-kpi-grid">
            
            {{-- KPI 1: Exhausted Packages --}}
            <div class="ec-kpi-card ec-card-red {{ $this->activeKpi === 'exhausted' ? 'ec-kpi-active' : '' }}" 
                wire:click="selectKpi('exhausted')"
                role="button"
                tabindex="0"
                title="{{ __('انقر لتصفية الباقات المنتهية وعرض تفاصيل المنطق الحسابي') }}">
                <div class="ec-card-glow"></div>
                <div class="ec-kpi-top">
                    <div class="ec-kpi-info">
                        <div class="ec-kpi-title-row">
                            <span class="ec-kpi-label">
                                {{ __('باقات منتهية (0 حصة)') }}
                            </span>
                            @if ($this->activeKpi === 'exhausted')
                                <span class="ec-kpi-active-pill bg-red-500/20 text-red-400 border border-red-500/30">
                                    {{ __('نشط') }}
                                </span>
                            @endif
                        </div>
                        <div class="ec-kpi-value text-red-500">
                            {{ $this->stats['exhausted_packages'] }}
                        </div>
                    </div>
                    <div class="ec-kpi-icon bg-red-500/15 text-red-500 border border-red-500/25">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                </div>
                <div class="ec-kpi-footer text-red-500">
                    <span class="flex items-center gap-1">
                        @if ($this->activeKpi === 'exhausted')
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('معروضة في الجدول بالتفصيل') }}</span>
                        @else
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ __('مستحق السداد فوراً') }}</span>
                        @endif
                    </span>
                    <span class="text-[10px] text-red-400/80 font-bold flex items-center gap-1">
                        {{ $this->activeKpi === 'exhausted' ? __('إلغاء التصفية') : __('انقر للتفاصيل') }}
                        <i class="fa-solid {{ $this->activeKpi === 'exhausted' ? 'fa-xmark' : 'fa-arrow-down' }} text-[9px]"></i>
                    </span>
                </div>
            </div>

            {{-- KPI 2: Low Balance (1-3 Sessions) --}}
            <div class="ec-kpi-card ec-card-amber {{ $this->activeKpi === 'low' ? 'ec-kpi-active' : '' }}" 
                wire:click="selectKpi('low')"
                role="button"
                tabindex="0"
                title="{{ __('انقر لتصفية الباقات منخفضة الرصيد وعرض تفاصيل المنطق الحسابي') }}">
                <div class="ec-card-glow"></div>
                <div class="ec-kpi-top">
                    <div class="ec-kpi-info">
                        <div class="ec-kpi-title-row">
                            <span class="ec-kpi-label">
                                {{ __('رصيد منخفض (1-3 حصص)') }}
                            </span>
                            @if ($this->activeKpi === 'low')
                                <span class="ec-kpi-active-pill bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                    {{ __('نشط') }}
                                </span>
                            @endif
                        </div>
                        <div class="ec-kpi-value text-amber-500">
                            {{ $this->stats['low_balance_packages'] }}
                        </div>
                    </div>
                    <div class="ec-kpi-icon bg-amber-500/15 text-amber-500 border border-amber-500/25">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                </div>
                <div class="ec-kpi-footer text-amber-500">
                    <span class="flex items-center gap-1">
                        @if ($this->activeKpi === 'low')
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('معروضة في الجدول بالتفصيل') }}</span>
                        @else
                            <i class="fa-solid fa-hourglass-half"></i>
                            <span>{{ __('تذكير مبكر بالسداد') }}</span>
                        @endif
                    </span>
                    <span class="text-[10px] text-amber-400/80 font-bold flex items-center gap-1">
                        {{ $this->activeKpi === 'low' ? __('إلغاء التصفية') : __('انقر للتفاصيل') }}
                        <i class="fa-solid {{ $this->activeKpi === 'low' ? 'fa-xmark' : 'fa-arrow-down' }} text-[9px]"></i>
                    </span>
                </div>
            </div>

            {{-- KPI 3: Expiring Within 7 Days --}}
            <div class="ec-kpi-card ec-card-purple {{ $this->activeKpi === 'expiring' ? 'ec-kpi-active' : '' }}" 
                wire:click="selectKpi('expiring')"
                role="button"
                tabindex="0"
                title="{{ __('انقر لتصفية الباقات التي تنتهي صلاحيتها خلال 7 أيام وعرض تفاصيل المنطق') }}">
                <div class="ec-card-glow"></div>
                <div class="ec-kpi-top">
                    <div class="ec-kpi-info">
                        <div class="ec-kpi-title-row">
                            <span class="ec-kpi-label">
                                {{ __('صلاحيات تنتهي (7 أيام)') }}
                            </span>
                            @if ($this->activeKpi === 'expiring')
                                <span class="ec-kpi-active-pill bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                    {{ __('نشط') }}
                                </span>
                            @endif
                        </div>
                        <div class="ec-kpi-value text-purple-500">
                            {{ $this->stats['expiring_in_7_days'] }}
                        </div>
                    </div>
                    <div class="ec-kpi-icon bg-purple-500/15 text-purple-500 border border-purple-500/25">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="ec-kpi-footer text-purple-500">
                    <span class="flex items-center gap-1">
                        @if ($this->activeKpi === 'expiring')
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('معروضة في الجدول بالتفصيل') }}</span>
                        @else
                            <i class="fa-solid fa-calendar-xmark"></i>
                            <span>{{ __('تجديد الاشتراكات الزمنية') }}</span>
                        @endif
                    </span>
                    <span class="text-[10px] text-purple-400/80 font-bold flex items-center gap-1">
                        {{ $this->activeKpi === 'expiring' ? __('إلغاء التصفية') : __('انقر للتفاصيل') }}
                        <i class="fa-solid {{ $this->activeKpi === 'expiring' ? 'fa-xmark' : 'fa-arrow-down' }} text-[9px]"></i>
                    </span>
                </div>
            </div>

            {{-- KPI 4: Recurring Schedules Ending Soon (14 Days) --}}
            <div class="ec-kpi-card ec-card-emerald {{ $this->activeKpi === 'ending_soon' ? 'ec-kpi-active' : '' }}" 
                wire:click="selectKpi('ending_soon')"
                role="button"
                tabindex="0"
                title="{{ __('انقر لتصفية الجداول الشهرية التي تنتهي خلال 14 يوماً وعرض تفاصيل المنطق') }}">
                <div class="ec-card-glow"></div>
                <div class="ec-kpi-top">
                    <div class="ec-kpi-info">
                        <div class="ec-kpi-title-row">
                            <span class="ec-kpi-label">
                                {{ __('جداول تنتهي قريباً (14 يوماً)') }}
                            </span>
                            @if ($this->activeKpi === 'ending_soon')
                                <span class="ec-kpi-active-pill bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    {{ __('نشط') }}
                                </span>
                            @endif
                        </div>
                        <div class="ec-kpi-value text-emerald-500">
                            {{ $this->stats['cycles_ending_soon'] }}
                        </div>
                    </div>
                    <div class="ec-kpi-icon bg-emerald-500/15 text-emerald-500 border border-emerald-500/25">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                </div>
                <div class="ec-kpi-footer text-emerald-500">
                    <span class="flex items-center gap-1">
                        @if ($this->activeKpi === 'ending_soon')
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('معروضة في الجدول بالتفصيل') }}</span>
                        @else
                            <i class="fa-solid fa-rotate"></i>
                            <span>{{ __('تمديد الدورة الشهرية') }}</span>
                        @endif
                    </span>
                    <span class="text-[10px] text-emerald-400/80 font-bold flex items-center gap-1">
                        {{ $this->activeKpi === 'ending_soon' ? __('إلغاء التصفية') : __('انقر للتفاصيل') }}
                        <i class="fa-solid {{ $this->activeKpi === 'ending_soon' ? 'fa-xmark' : 'fa-arrow-down' }} text-[9px]"></i>
                    </span>
                </div>
            </div>
        </div>

        {{-- ── 2.5 DYNAMIC KPI INSIGHT & DETAILS LOGIC BANNER ────────────── --}}
        @if ($this->activeKpi)
            @php
                $activeMeta = $this->getKpiMetadata($this->activeKpi);
            @endphp
            @if ($activeMeta)
                <div class="ec-kpi-banner banner-{{ $activeMeta['color'] }}">
                    {{-- Header Row: Executive Titles & Clear Actions --}}
                    <div class="ec-banner-header">
                        <div class="ec-banner-lead">
                            <div class="ec-banner-icon {{ $activeMeta['badge_class'] }}">
                                <i class="fa-solid {{ $activeMeta['icon'] }}"></i>
                            </div>
                            <div class="ec-banner-titles">
                                <div class="ec-banner-title-line">
                                    <h2 class="ec-banner-title">
                                        {{ $activeMeta['user_friendly_title'] ?? $activeMeta['title'] }}
                                    </h2>
                                    <span class="ec-filter-active-pill">
                                        <i class="fa-solid fa-filter text-[10px]"></i>
                                        <span>{{ __('تصفية نشطة بالجدول') }}</span>
                                    </span>
                                    <span class="ec-count-pill">
                                        <i class="fa-solid fa-users text-[10px]"></i>
                                        <span>{{ $activeMeta['count'] }} {{ __('حالات مسجلة') }}</span>
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black {{ $activeMeta['badge_class'] }}">
                                        {{ $activeMeta['urgency'] }}
                                    </span>
                                </div>
                                <p class="ec-banner-desc">
                                    {{ $activeMeta['subtitle'] }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons: Prominent Clear Filter + Info Modal --}}
                        <div class="ec-banner-actions">
                            <button type="button" wire:click="clearKpiFilter"
                                class="ec-btn-clear-kpi"
                                title="{{ __('إلغاء التصفية والعودة لعرض كافة السجلات') }}">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                                <span>{{ __('إلغاء التصفية (عرض جميع الطلاب)') }}</span>
                            </button>
                            <button type="button" wire:click="inspectKpi('{{ $this->activeKpi }}')"
                                class="ec-btn-kpi-info"
                                title="{{ __('استعراض التفاصيل والقواعد البرمجية للمشرف') }}">
                                <i class="fa-solid fa-circle-question"></i>
                                <span>{{ __('تفاصيل للمشرفين') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- 3-Step Humanized Operational Guidance Grid --}}
                    <div class="ec-guide-grid">
                        {{-- Step 1: Who are these students? --}}
                        <div class="ec-guide-card">
                            <div>
                                <div class="ec-guide-card-top">
                                    <div class="ec-step-badge ec-step-1">
                                        <span>1</span>
                                    </div>
                                    <div class="ec-guide-card-title">
                                        <span>{{ __('من هم هؤلاء الطلاب؟') }}</span>
                                        <small>{{ __('فهم التصنيف وحالة الحساب') }}</small>
                                    </div>
                                </div>
                                <p class="ec-guide-card-body mt-3">
                                    {{ $activeMeta['who_are_they'] ?? $activeMeta['business_rule'] }}
                                </p>
                            </div>
                            <div class="ec-guide-card-footer">
                                <div class="ec-guide-pill ec-pill-info">
                                    <i class="fa-solid fa-circle-info text-[10px]"></i>
                                    <span>{{ $activeMeta['criterion_pill'] ?? __('معيار التصنيف المطبق') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Why follow up now? --}}
                        <div class="ec-guide-card">
                            <div>
                                <div class="ec-guide-card-top">
                                    <div class="ec-step-badge ec-step-2">
                                        <span>2</span>
                                    </div>
                                    <div class="ec-guide-card-title">
                                        <span>{{ __('الهدف وأهمية المتابعة') }}</span>
                                        <small>{{ __('الأثر التعليمي والتشغيلي') }}</small>
                                    </div>
                                </div>
                                <p class="ec-guide-card-body mt-3">
                                    {{ $activeMeta['why_it_matters'] ?? $activeMeta['operational_impact'] }}
                                </p>
                            </div>
                            <div class="ec-guide-card-footer">
                                <div class="ec-guide-pill ec-pill-target">
                                    <i class="fa-solid fa-bullseye text-[10px]"></i>
                                    <span>{{ $activeMeta['impact_pill'] ?? __('الحفاظ على استمرارية الدروس') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3: What to do right now? --}}
                        <div class="ec-guide-card">
                            <div>
                                <div class="ec-guide-card-top">
                                    <div class="ec-step-badge ec-step-3">
                                        <span>3</span>
                                    </div>
                                    <div class="ec-guide-card-title">
                                        <span>{{ __('الإجراء المطلوب منك الآن') }}</span>
                                        <small>{{ __('تنفيذ فوري بنقرة واحدة من الجدول') }}</small>
                                    </div>
                                </div>
                                <p class="ec-guide-card-body mt-3">
                                    {{ $activeMeta['how_to_act'] ?? $activeMeta['action_label'] }}
                                </p>
                            </div>
                            <div class="ec-guide-card-footer">
                                <div class="ec-action-guide-tags">
                                    <span class="ec-hint-chip ec-hint-whatsapp" title="{{ __('إرسال رسالة واتساب جاهزة') }}">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>{{ __('واتساب') }}</span>
                                    </span>
                                    <span class="ec-hint-chip ec-hint-notify" title="{{ __('إرسال إشعار فوري للتطبيق') }}">
                                        <i class="fa-solid fa-bell"></i>
                                        <span>{{ __('إشعار فوري') }}</span>
                                    </span>
                                    @if ($this->activeKpi === 'ending_soon')
                                        <span class="ec-hint-chip ec-hint-renew" title="{{ __('تمديد الدورة للشهر الجديد') }}">
                                            <i class="fa-solid fa-rotate"></i>
                                            <span>{{ __('تمديد الدورة') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Directional Helper Strip --}}
                    <div class="ec-banner-bottom-bar">
                        <div class="flex items-center gap-2.5">
                            <span class="ec-pulse-arrow">
                                <i class="fa-solid fa-arrow-down text-xs"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-200">
                                {{ __('الجدول أدناه تم تصفيته تلقائياً ليعرض فقط الطلاب المشمولين في هذا القسم.') }}
                            </span>
                            <span class="text-xs text-slate-400 hidden lg:inline">
                                {{ __('اضغط على زر (واتساب) أو (إشعار) بجانب اسم الطالب بالأسفل للمتابعة الفورية.') }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-circle-dot text-[8px] text-emerald-400 animate-pulse"></i>
                            <span>{{ __('تحديث فوري مع كل إجراء') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="ec-prompt-box">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <span>{{ __('انقر فوق أي مؤشر أداء (KPI) أعلاه لتطبيق التصفية الفورية واستعراض المنطق الحسابي والإجراءات الموصى بها.') }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 shrink-0 hidden sm:flex">
                    <i class="fa-solid fa-computer-mouse text-xs"></i>
                    <span>{{ __('تصفية بنقرة واحدة') }}</span>
                </div>
            </div>
        @endif

        {{-- ── 3. MAIN WORKBENCH: TABS, FILTERS & DATA TABLES ────────────── --}}
        <div class="ec-main-surface">
            
            {{-- Toolbar: Segmented Tabs & Search Input --}}
            <div class="ec-toolbar">
                
                {{-- Segmented Tab Switcher --}}
                <div class="ec-segmented-nav">
                    <button type="button" wire:click="setTab('packages')"
                        class="ec-tab-btn {{ $activeTab === 'packages' ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt text-sm"></i>
                        <span>{{ __('مستحقات الباقات التعليمية') }}</span>
                        <span class="ec-badge-count">
                            {{ $this->stats['exhausted_packages'] + $this->stats['low_balance_packages'] }}
                        </span>
                    </button>

                    <button type="button" wire:click="setTab('schedules')"
                        class="ec-tab-btn {{ $activeTab === 'schedules' ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                        <span>{{ __('جداول شهرية أوشكت على الانتهاء') }}</span>
                        <span class="ec-badge-count">
                            {{ $this->stats['cycles_ending_soon'] }}
                        </span>
                    </button>
                </div>

                {{-- Bulletproof Search Box with Live Animation --}}
                <div class="ec-search-box">
                    <span class="ec-search-icon" wire:loading.remove wire:target="searchQuery">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <span class="ec-search-icon text-teal-400" wire:loading wire:target="searchQuery">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.250ms="searchQuery"
                        placeholder="{{ __('بحث باسم الطالب، الهاتف، المقرر، الباقة، ولي الأمر...') }}"
                        class="ec-search-input">
                    @if (!empty($searchQuery))
                        <button type="button" wire:click="$set('searchQuery', '')" class="ec-search-clear" title="{{ __('مسح البحث') }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- ═════════════════════════════════════════════════════════════ --}}
            {{-- TAB 1: EXPIRING / EXHAUSTED PACKAGES                           --}}
            {{-- ═════════════════════════════════════════════════════════════ --}}
            @if ($activeTab === 'packages')
                <div>
                    {{-- Sub Filter Chips Bar --}}
                    <div class="ec-filters-bar flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2 overflow-x-auto">
                            <span class="ec-filters-label">
                                <i class="fa-solid fa-filter"></i>
                                <span>{{ __('تصفية:') }}</span>
                            </span>
                            
                            <button type="button" wire:click="setPackageFilter('all')"
                                class="ec-filter-chip {{ $packageFilter === 'all' ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check text-xs"></i>
                                <span>{{ __('جميع المستحقات') }}</span>
                            </button>
                            
                            <button type="button" wire:click="setPackageFilter('exhausted')"
                                class="ec-filter-chip {{ $packageFilter === 'exhausted' ? 'active' : '' }}">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span>{{ __('رصيد منتهٍ (0 حصة)') }}</span>
                                <span class="font-bold">({{ $this->stats['exhausted_packages'] }})</span>
                            </button>
                            
                            <button type="button" wire:click="setPackageFilter('low')"
                                class="ec-filter-chip {{ $packageFilter === 'low' ? 'active' : '' }}">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>{{ __('رصيد منخفض (1-3 حصص)') }}</span>
                                <span class="font-bold">({{ $this->stats['low_balance_packages'] }})</span>
                            </button>
                            
                            <button type="button" wire:click="setPackageFilter('expiring')"
                                class="ec-filter-chip {{ $packageFilter === 'expiring' ? 'active' : '' }}">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <span>{{ __('تنتهي خلال أسبوع') }}</span>
                                <span class="font-bold">({{ $this->stats['expiring_in_7_days'] }})</span>
                            </button>
                        </div>

                        {{-- Live Search Result Counter Pill --}}
                        @if (!empty($searchQuery))
                            <div class="ec-search-results-pill">
                                <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                <span>{{ count($this->expiringPackages) }} {{ __('نتائج مطابقة للبحث') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Data Table Body --}}
                    <div class="ec-content-body">
                        <div class="ec-table-container">
                            <table class="ec-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('الطالب') }}</th>
                                        <th>{{ __('ولي الأمر للتواصل') }}</th>
                                        <th>{{ __('الباقة الحالية') }}</th>
                                        <th style="text-align: center;">{{ __('الرصيد المتبقي') }}</th>
                                        <th>{{ __('صلاحية الباقة') }}</th>
                                        <th style="text-align: center;">{{ __('حالة التحصيل') }}</th>
                                        <th style="text-align: center;">{{ __('إجراءات المتابعة والتحصيل') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->expiringPackages as $item)
                                        @php
                                            $initial = mb_substr($item->student?->name ?? 'ط', 0, 1);
                                            $percentRemaining = $item->total > 0 ? max(0, min(100, round(($item->remaining / $item->total) * 100))) : 0;
                                            $progressColor = $item->remaining <= 0 ? '#ef4444' : ($item->remaining <= 3 ? '#f59e0b' : '#10b981');
                                        @endphp
                                        <tr>
                                            {{-- 1. Student Info --}}
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="ec-avatar-circle">
                                                        {{ $initial }}
                                                    </div>
                                                    <div>
                                                        <div class="font-extrabold text-sm" style="color: var(--ec-text-main);">
                                                            {{ $item->student?->name ?? '—' }}
                                                        </div>
                                                        <div class="text-[11px] font-semibold mt-0.5" style="color: var(--ec-text-muted);">
                                                            {{ $item->student?->studentProfile?->gradeLevel?->name ?? $item->student?->email }}
                                                        </div>
                                                        @if ($item->student?->phone)
                                                            <div class="text-[11px] font-mono flex items-center gap-1.5 mt-0.5" style="color: var(--ec-text-dim);">
                                                                <i class="fa-solid fa-phone text-[9px] opacity-70"></i>
                                                                <span>{{ $item->student->phone }}</span>
                                                                @if ($item->studentWhatsAppUrl)
                                                                    <a href="{{ $item->studentWhatsAppUrl }}" target="_blank" title="{{ __('محادثة واتساب الطالب') }}"
                                                                        class="text-emerald-400 hover:text-emerald-300">
                                                                        <i class="fa-brands fa-whatsapp text-xs"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- 2. Parent Info (Modern Cardlet) --}}
                                            <td>
                                                @if ($item->parent)
                                                    <div class="ec-parent-card">
                                                        <div class="flex items-center justify-between gap-1.5">
                                                            <span class="font-extrabold text-xs truncate" style="color: var(--ec-text-main);" title="{{ $item->parent->name }}">
                                                                {{ $item->parent->name }}
                                                            </span>
                                                            @if ($item->parentWhatsAppUrl)
                                                                <a href="{{ $item->parentWhatsAppUrl }}" target="_blank"
                                                                    class="ec-btn-whatsapp-compact"
                                                                    title="{{ __('فتح واتساب ولي الأمر برسالة تذكير') }}">
                                                                    <i class="fa-brands fa-whatsapp"></i>
                                                                    <span>{{ __('واتساب') }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div class="text-[11px] font-mono flex items-center gap-1 mt-1" style="color: var(--ec-text-dim);">
                                                            <i class="fa-solid fa-phone text-[9px] opacity-70"></i>
                                                            <span>{{ $item->parent->phone ?? '—' }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-xs italic" style="color: var(--ec-text-dim);">{{ __('غير مسجل') }}</span>
                                                @endif
                                            </td>

                                            {{-- 3. Package Name & Progress --}}
                                            <td style="min-width: 185px;">
                                                <div class="font-black text-xs sm:text-sm whitespace-nowrap overflow-hidden text-ellipsis" style="color: var(--ec-text-main);" title="{{ $item->pkgName }}">
                                                    {{ $item->pkgName }}
                                                </div>
                                                <div class="flex items-center justify-between text-[11px] font-bold mt-1 mb-1.5" style="color: var(--ec-text-muted);">
                                                    <span>{{ __('الإجمالي:') }} <strong class="text-slate-200">{{ $item->total }}</strong></span>
                                                    <span>{{ __('المستخدم:') }} <strong class="text-slate-200">{{ $item->used }}</strong></span>
                                                </div>
                                                {{-- Visual Mini Progress Bar --}}
                                                <div class="ec-progress-track">
                                                    <div class="ec-progress-fill" style="width: {{ $percentRemaining }}%; background: {{ $progressColor }};"></div>
                                                </div>
                                            </td>

                                            {{-- 4. Remaining Sessions Badge --}}
                                            <td style="text-align: center; white-space: nowrap;">
                                                @if ($item->remaining <= 0)
                                                    <span class="ec-balance-pill ec-balance-zero">
                                                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                                                        <span>{{ __('0 حصص (منتهية)') }}</span>
                                                    </span>
                                                @elseif ($item->remaining <= 3)
                                                    <span class="ec-balance-pill ec-balance-low">
                                                        <i class="fa-solid fa-bell text-xs"></i>
                                                        <span>{{ $item->remaining }} {{ __('حصص متبقية') }}</span>
                                                    </span>
                                                @else
                                                    <span class="ec-balance-pill ec-balance-good">
                                                        <i class="fa-solid fa-circle-check text-xs"></i>
                                                        <span>{{ $item->remaining }} {{ __('حصص متبقية') }}</span>
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- 5. Expiry Date --}}
                                            <td style="white-space: nowrap;">
                                                @if ($item->expiryFormatted)
                                                    <div class="text-xs font-mono font-bold" style="color: var(--ec-text-main);">
                                                        {{ $item->expiryFormatted }}
                                                    </div>
                                                    <div class="text-[10px] mt-0.5 font-bold {{ str_contains($item->expiryCountdown ?? '', 'منتهية') ? 'text-red-400 font-extrabold' : 'text-slate-400' }}">
                                                        {{ $item->expiryCountdown }}
                                                    </div>
                                                @else
                                                    <span class="text-xs" style="color: var(--ec-text-dim);">{{ __('بدون تاريخ انتهاء') }}</span>
                                                @endif
                                            </td>

                                            {{-- 6. Urgency Badge --}}
                                            <td style="text-align: center; white-space: nowrap;">
                                                @if ($item->remaining <= 0)
                                                    <span class="ec-urgency-badge ec-urgency-danger">
                                                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                        <span>{{ __('مستحق السداد فوراً') }}</span>
                                                    </span>
                                                @elseif ($item->remaining <= 3)
                                                    <span class="ec-urgency-badge ec-urgency-warning">
                                                        <i class="fa-solid fa-hourglass-half text-xs"></i>
                                                        <span>{{ __('رصيد أوشك على النفاد') }}</span>
                                                    </span>
                                                @elseif ($item->record->expires_at && $item->record->expires_at->isPast())
                                                    <span class="ec-urgency-badge ec-urgency-danger">
                                                        <i class="fa-solid fa-calendar-xmark text-xs"></i>
                                                        <span>{{ __('صلاحية منتهية') }}</span>
                                                    </span>
                                                @else
                                                    <span class="ec-urgency-badge ec-urgency-info">
                                                        <i class="fa-solid fa-clock text-xs"></i>
                                                        <span>{{ __('تنتهي الصلاحية قريباً') }}</span>
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- 7. Action Suite (Compact 2x2 Grid) --}}
                                            <td style="text-align: center; white-space: nowrap;">
                                                <div class="ec-action-grid">
                                                    {{-- WhatsApp Reminder button --}}
                                                    @if ($item->parentWhatsAppUrl || $item->studentWhatsAppUrl)
                                                        <a href="{{ $item->parentWhatsAppUrl ?: $item->studentWhatsAppUrl }}" target="_blank"
                                                            class="ec-btn-action ec-btn-act-whatsapp"
                                                            title="{{ __('إرسال رسالة تذكير بالسداد عبر الواتساب') }}">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                            <span>{{ __('واتساب') }}</span>
                                                        </a>
                                                    @endif

                                                    {{-- Push notification reminder --}}
                                                    <button type="button" wire:click="sendPaymentReminder({{ $item->record->id }})"
                                                        class="ec-btn-action ec-btn-act-notify"
                                                        title="{{ __('إرسال إشعار فوري لحظي داخل النظام وتطبيق الجوال') }}">
                                                        <i class="fa-solid fa-bell"></i>
                                                        <span>{{ __('إشعار فوري') }}</span>
                                                    </button>

                                                    {{-- Renew package button --}}
                                                    <button type="button" wire:click="openRenewPackageModal({{ $item->record->id }})"
                                                        class="ec-btn-action ec-btn-act-renew"
                                                        title="{{ __('تجديد الباقة واحتساب دورة حصص جديدة') }}">
                                                        <i class="fa-solid fa-rotate"></i>
                                                        <span>{{ __('تجديد الباقة') }}</span>
                                                    </button>

                                                    {{-- Quick credit injection --}}
                                                    <button type="button" wire:click="openAddCreditsModal({{ $item->record->id }})"
                                                        class="ec-btn-action ec-btn-act-credit"
                                                        title="{{ __('إضافة حصص إضافية سريعة كرصيد') }}">
                                                        <i class="fa-solid fa-plus"></i>
                                                        <span>{{ __('شحن سريع') }}</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-14 text-center">
                                                <div class="flex flex-col items-center justify-center gap-3">
                                                    <div class="w-16 h-16 rounded-3xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl border border-teal-500/25">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </div>
                                                    <p class="font-black text-base" style="color: var(--ec-text-main);">
                                                        @if (!empty($searchQuery))
                                                            {{ __('لا توجد نتائج مطابقة لبحثك:') }} "{{ $searchQuery }}"
                                                        @else
                                                            {{ __('لا توجد مستحقات باقات معلقة حالياً!') }}
                                                        @endif
                                                    </p>
                                                    <p class="text-xs" style="color: var(--ec-text-muted);">
                                                        @if (!empty($searchQuery))
                                                            {{ __('جرب البحث بكلمات أخرى أو اضغط على إلغاء البحث لاستعراض جميع السجلات.') }}
                                                        @else
                                                            {{ __('جميع الطلاب المشتركين لديهم رصيد كافٍ من الحصص وصلاحياتهم سارية.') }}
                                                        @endif
                                                    </p>
                                                    @if (!empty($searchQuery))
                                                        <button type="button" wire:click="$set('searchQuery', '')" class="ec-filter-chip active mt-2">
                                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                                            <span>{{ __('إلغاء البحث وعرض الكل') }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═════════════════════════════════════════════════════════════ --}}
            {{-- TAB 2: EXPIRING RECURRING SCHEDULE CYCLES                     --}}
            {{-- ═════════════════════════════════════════════════════════════ --}}
            @if ($activeTab === 'schedules')
                <div>
                    {{-- Notice banner explaining the feature --}}
                    <div style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--ec-table-border); background: rgba(13, 148, 136, 0.06); display: flex; align-items: center; gap: 0.75rem;">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0 text-sm shadow-sm">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="text-xs leading-relaxed" style="color: var(--ec-text-main);">
                            <span class="font-black text-teal-400">{{ __('متابعة الجداول الشهرية المنتهية:') }}</span>
                            <span class="font-medium">{{ __('هنا يظهر الطلاب الذين لديهم جدول حصص دوري (مثلاً لشهر كامل سينتهي قريباً)، لتتمكن الإدارة من تذكيرهم بالسداد وتمديد الجدول وتوليد حصص الشهر القادم بنقرة واحدة.') }}</span>
                        </div>
                    </div>

                    {{-- Sub Filter Chips Bar --}}
                    <div class="ec-filters-bar flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2 overflow-x-auto">
                            <span class="ec-filters-label">
                                <i class="fa-solid fa-filter"></i>
                                <span>{{ __('تصفية:') }}</span>
                            </span>
                            
                            <button type="button" wire:click="setScheduleFilter('all')"
                                class="ec-filter-chip {{ $scheduleFilter === 'all' ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check text-xs"></i>
                                <span>{{ __('جميع الجداول القريبة') }}</span>
                            </button>
                            
                            <button type="button" wire:click="setScheduleFilter('ending_soon')"
                                class="ec-filter-chip {{ $scheduleFilter === 'ending_soon' ? 'active' : '' }}">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>{{ __('تنتهي خلال 14 يوماً') }}</span>
                            </button>
                            
                            <button type="button" wire:click="setScheduleFilter('ended')"
                                class="ec-filter-chip {{ $scheduleFilter === 'ended' ? 'active' : '' }}">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span>{{ __('الدورة انتهت بالفعل') }}</span>
                            </button>
                        </div>

                        {{-- Live Search Result Counter Pill --}}
                        @if (!empty($searchQuery))
                            <div class="ec-search-results-pill">
                                <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                <span>{{ count($this->endingSchedules) }} {{ __('نتائج مطابقة للبحث') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Data Table Body --}}
                    <div class="ec-content-body">
                        <div class="ec-table-container">
                            <table class="ec-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('الطالب المعني') }}</th>
                                        <th>{{ __('المقرر / المادة / العنوان') }}</th>
                                        <th>{{ __('المعلم') }}</th>
                                        <th>{{ __('نمط التكرار والموعد') }}</th>
                                        <th style="text-align: center;">{{ __('تاريخ نهاية الدورة') }}</th>
                                        <th style="text-align: center;">{{ __('الحصص المتبقية') }}</th>
                                        <th style="text-align: center;">{{ __('الإجراءات والتمديد') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->endingSchedules as $schItem)
                                        @php
                                            $initialSch = mb_substr($schItem->student?->name ?? 'ج', 0, 1);
                                        @endphp
                                        <tr class="ec-table-row">
                                            {{-- Student Info --}}
                                            <td>
                                                @if ($schItem->student)
                                                    <div class="flex items-center gap-3">
                                                        <div class="ec-avatar-circle" style="background: linear-gradient(135deg, #a855f7, #3b82f6);">
                                                            {{ $initialSch }}
                                                        </div>
                                                        <div>
                                                            <div class="font-black text-sm" style="color: var(--ec-text-main);">
                                                                {{ $schItem->student->name }}
                                                            </div>
                                                            <div class="text-[11px] font-medium" style="color: var(--ec-text-muted);">
                                                                {{ $schItem->student->studentProfile?->gradeLevel?->name ?? $schItem->student->email }}
                                                            </div>
                                                            @if ($schItem->student->phone)
                                                                <div class="text-[11px] font-mono flex items-center gap-1.5 mt-0.5" style="color: var(--ec-text-dim);">
                                                                    <span>{{ $schItem->student->phone }}</span>
                                                                    @if ($schItem->studentWhatsAppUrl)
                                                                        <a href="{{ $schItem->studentWhatsAppUrl }}" target="_blank" title="{{ __('واتساب الطالب') }}"
                                                                            class="text-emerald-500 hover:text-emerald-400 transition-colors">
                                                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold" style="background: var(--ec-bg-card-subtle); color: var(--ec-text-muted);">
                                                        <i class="fa-solid fa-users text-xs"></i>
                                                        {{ __('جدول جماعي / عام') }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Course / Subject / Title --}}
                                            <td>
                                                <div class="font-black text-sm" style="color: var(--ec-text-main);">
                                                    {{ $schItem->record->title ?: $schItem->courseTitle }}
                                                </div>
                                                <div class="text-[11px] font-bold text-teal-500 mt-0.5">
                                                    {{ $schItem->courseTitle }} • {{ $schItem->subjectName }}
                                                </div>
                                            </td>

                                            {{-- Teacher --}}
                                            <td>
                                                <div class="font-bold text-sm" style="color: var(--ec-text-main);">
                                                    {{ $schItem->teacher?->name ?? '—' }}
                                                </div>
                                            </td>

                                            {{-- Recurrence Pattern --}}
                                            <td>
                                                <div class="text-xs font-bold" style="color: var(--ec-text-main);">
                                                    {{ $schItem->daysFormatted }}
                                                </div>
                                                <div class="text-[11px] mt-0.5" style="color: var(--ec-text-muted);">
                                                    <i class="fa-regular fa-clock text-[10px] text-teal-400"></i>
                                                    {{ __('الساعة:') }} {{ $schItem->startTime }}
                                                </div>
                                            </td>

                                            {{-- End Date & Countdown --}}
                                            <td style="text-align: center; white-space: nowrap;">
                                                <div class="font-mono font-bold text-xs" style="color: var(--ec-text-main);">
                                                    {{ $schItem->endDateFormatted }}
                                                </div>
                                                <div class="mt-1">
                                                    @if ($schItem->statusColor === 'danger')
                                                        <span class="ec-urgency-badge ec-urgency-danger">
                                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                                            <span>{{ $schItem->statusBadge }}</span>
                                                        </span>
                                                    @else
                                                        <span class="ec-urgency-badge ec-urgency-warning">
                                                            <i class="fa-solid fa-hourglass-half"></i>
                                                            <span>{{ $schItem->statusBadge }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Remaining Sessions --}}
                                            <td style="text-align: center; white-space: nowrap;">
                                                <span class="ec-balance-pill {{ $schItem->remainingSessions <= 0 ? 'ec-balance-zero' : ($schItem->remainingSessions <= 3 ? 'ec-balance-low' : 'ec-balance-good') }}">
                                                    {{ $schItem->remainingSessions }} {{ __('حصص') }}
                                                </span>
                                                <div class="text-[10px] mt-1 font-medium" style="color: var(--ec-text-dim);">
                                                    {{ __('من إجمالي:') }} {{ $schItem->totalSessions }}
                                                </div>
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <div class="ec-action-grid">
                                                    {{-- WhatsApp Reminder button --}}
                                                    @if ($schItem->parentWhatsAppUrl || $schItem->studentWhatsAppUrl)
                                                        <a href="{{ $schItem->parentWhatsAppUrl ?: $schItem->studentWhatsAppUrl }}" target="_blank"
                                                            class="ec-btn-action ec-btn-act-whatsapp"
                                                            title="{{ __('إرسال تذكير بالسداد وتجديد الدورة عبر الواتساب') }}">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                            <span>{{ __('واتساب') }}</span>
                                                        </a>
                                                    @endif

                                                    {{-- Push notification reminder --}}
                                                    <button type="button" wire:click="sendScheduleRenewalReminder({{ $schItem->record->id }})"
                                                        class="ec-btn-action ec-btn-act-notify"
                                                        title="{{ __('إرسال إشعار فوري داخل النظام وتطبيق الجوال') }}">
                                                        <i class="fa-solid fa-bell"></i>
                                                        <span>{{ __('إشعار فوري') }}</span>
                                                    </button>

                                                    {{-- Extend Cycle button --}}
                                                    <button type="button" wire:click="openExtendScheduleModal({{ $schItem->record->id }})"
                                                        class="ec-btn-action ec-btn-act-renew {{ ($schItem->parentWhatsAppUrl || $schItem->studentWhatsAppUrl) ? 'col-span-2' : '' }}"
                                                        title="{{ __('تمديد دورة الجدول لشهر إضافي وتوليد الحصص آلياً') }}">
                                                        <i class="fa-solid fa-calendar-plus"></i>
                                                        <span>{{ __('تمديد الدورة') }}</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-14 text-center">
                                                <div class="flex flex-col items-center justify-center gap-3">
                                                    <div class="w-16 h-16 rounded-3xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-3xl border border-teal-500/25">
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                    </div>
                                                    <p class="font-black text-base" style="color: var(--ec-text-main);">
                                                        {{ __('لا توجد جداول دورية قريبة من الانتهاء حالياً!') }}
                                                    </p>
                                                    <p class="text-xs" style="color: var(--ec-text-muted);">
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
                </div>
            @endif

        </div>

        {{-- ── MODAL 1: RENEW STUDENT PACKAGE ───────────────────────────── --}}
        @if ($showRenewModal)
            <div class="ec-modal-overlay" wire:click.self="$set('showRenewModal', false)">
                <div class="ec-modal-panel">
                    <div class="flex items-center justify-between pb-3.5 border-b" style="border-color: var(--ec-border-subtle);">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-base" style="color: var(--ec-text-main);">
                                    {{ __('تجديد اشتراك الباقة التعليمية') }}
                                </h3>
                                <p class="text-[11px] font-medium" style="color: var(--ec-text-muted);">
                                    {{ __('إعادة ضبط رصيد الحصص وتفعيل دورة اشتراك جديدة للطالب') }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showRenewModal', false)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm">
                        <div class="p-3.5 rounded-xl bg-teal-500/10 border border-teal-500/25 text-teal-400 text-xs flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-info text-base shrink-0"></i>
                            <span class="font-medium leading-relaxed">{{ __('سيتم إعادة تعيين رصيد الحصص وتفعيل الباقة وتحديث تاريخ الصلاحية وسجل حركات الاشتراك.') }}</span>
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('اختر خطة/نموذج باقة (اختياري لملء الإعدادات)') }}
                            </label>
                            <select wire:model.live="newTemplateId" class="ec-modal-input">
                                <option value="">{{ __('— تخصيص يدوي مباشر —') }}</option>
                                @foreach (\App\Models\PackageTemplate::where('is_active', true)->get() as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->sessions_count ?? $tpl->total_sessions }} حصة)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('عدد الحصص الكلي للباقة الجديدة') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1" max="500" wire:model="newTotalSessions" class="ec-modal-input font-bold text-sm">
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('تاريخ الانتهاء الجديد (اختياري)') }}
                            </label>
                            <input type="datetime-local" wire:model="newExpiresAt" class="ec-modal-input">
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('ملاحظة / سبب التجديد') }}
                            </label>
                            <input type="text" wire:model="renewReason" placeholder="{{ __('سبب التجديد...') }}" class="ec-modal-input">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t" style="border-color: var(--ec-border-subtle);">
                        <button type="button" wire:click="$set('showRenewModal', false)"
                            class="ec-modal-btn-cancel">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitRenewPackage" wire:loading.attr="disabled"
                            class="ec-modal-btn-confirm">
                            <span wire:loading.remove wire:target="submitRenewPackage" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>{{ __('تأكيد التجديد وتفعيل الرصيد') }}</span>
                            </span>
                            <span wire:loading wire:target="submitRenewPackage" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري التجديد...') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── MODAL 2: QUICK ADD CREDITS ───────────────────────────────── --}}
        @if ($showAddCreditsModal)
            <div class="ec-modal-overlay" wire:click.self="$set('showAddCreditsModal', false)">
                <div class="ec-modal-panel" style="max-width: 25rem;">
                    <div class="flex items-center justify-between pb-3.5 border-b" style="border-color: var(--ec-border-subtle);">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-plus-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-base" style="color: var(--ec-text-main);">
                                    {{ __('إضافة رصيد حصص سريع') }}
                                </h3>
                                <p class="text-[11px] font-medium" style="color: var(--ec-text-muted);">
                                    {{ __('إيداع حصص إضافية برصيد الطالب فوراً') }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showAddCreditsModal', false)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="space-y-4 text-xs sm:text-sm">
                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('عدد الحصص الإضافية') }}
                            </label>
                            <input type="number" min="1" max="50" wire:model="creditsToAdd" class="ec-modal-input font-black text-2xl text-center py-2.5">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" wire:click="$set('creditsToAdd', 2)"
                                class="ec-filter-chip justify-center font-black {{ $creditsToAdd == 2 ? 'active' : '' }}">
                                +2 حصص
                            </button>
                            <button type="button" wire:click="$set('creditsToAdd', 4)"
                                class="ec-filter-chip justify-center font-black {{ $creditsToAdd == 4 ? 'active' : '' }}">
                                +4 حصص
                            </button>
                            <button type="button" wire:click="$set('creditsToAdd', 8)"
                                class="ec-filter-chip justify-center font-black {{ $creditsToAdd == 8 ? 'active' : '' }}">
                                +8 حصص
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t" style="border-color: var(--ec-border-subtle);">
                        <button type="button" wire:click="$set('showAddCreditsModal', false)"
                            class="ec-modal-btn-cancel">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitAddCredits" wire:loading.attr="disabled"
                            class="ec-modal-btn-confirm">
                            <span wire:loading.remove wire:target="submitAddCredits" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>{{ __('إضافة الرصيد فوراً') }}</span>
                            </span>
                            <span wire:loading wire:target="submitAddCredits" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري الإضافة...') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── MODAL 3: EXTEND RECURRING SCHEDULE CYCLE ─────────────────── --}}
        @if ($showExtendScheduleModal)
            <div class="ec-modal-overlay" wire:click.self="$set('showExtendScheduleModal', false)">
                <div class="ec-modal-panel" style="max-width: 32rem;">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between pb-3.5 border-b" style="border-color: var(--ec-border-subtle);">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-400 border border-teal-500/30 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-base" style="color: var(--ec-text-main);">
                                    {{ __('تمديد الدورة وتجديد الجدول الشهري') }}
                                </h3>
                                <p class="text-[11px] font-medium" style="color: var(--ec-text-muted);">
                                    {{ __('تجديد الدورة التلقائي وتوليد حصص الشهر الجديد في تقويم الطالب والمعلم') }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showExtendScheduleModal', false)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    {{-- Target Schedule Summary Cardlet --}}
                    @if ($extendingScheduleStudentName || $extendingScheduleTitle)
                        <div class="p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs" style="background: var(--ec-bg-surface); border-color: var(--ec-border-subtle);">
                            <div class="space-y-1">
                                <div class="font-extrabold text-sm" style="color: var(--ec-text-main);">
                                    <i class="fa-solid fa-user-graduate text-teal-400 me-1.5"></i>
                                    <span>{{ $extendingScheduleStudentName }}</span>
                                </div>
                                <div class="text-[11px] font-medium" style="color: var(--ec-text-muted);">
                                    <span>{{ $extendingScheduleTitle }}</span>
                                </div>
                            </div>
                            <div class="text-end shrink-0">
                                <span class="text-[10px] block" style="color: var(--ec-text-dim);">{{ __('نهاية الدورة الحالية:') }}</span>
                                <span class="font-mono font-black text-amber-400 text-xs">
                                    {{ $extendingScheduleCurrentEndDate ?: '—' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Form Fields --}}
                    <div class="space-y-4 text-xs sm:text-sm">
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-400 text-xs flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-info text-base shrink-0"></i>
                            <span class="font-medium leading-relaxed">
                                {{ __('سيقوم النظام تلقائياً بتوليد الحصص المباشرة المتكررة للفترة الممتدة وحفظها في جدول مواعيد الطالب والمعلم.') }}
                            </span>
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('تاريخ نهاية الدورة الجديد') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="newScheduleEndDate"
                                min="{{ $extendingScheduleCurrentEndDate ? \Carbon\Carbon::parse($extendingScheduleCurrentEndDate)->addDay()->format('Y-m-d') : date('Y-m-d') }}"
                                class="ec-modal-input font-bold text-sm">
                            <p class="text-[11px] mt-1 font-medium" style="color: var(--ec-text-dim);">
                                {{ __('يجب أن يكون التاريخ بعد نهاية الدورة الحالية (تم ضبطه تلقائياً لشهر إضافي).') }}
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold mb-1.5 text-xs" style="color: var(--ec-text-main);">
                                {{ __('ملاحظة التمديد') }}
                            </label>
                            <input type="text" wire:model="extendReason"
                                placeholder="{{ __('سبب أو ملاحظة التمديد...') }}"
                                class="ec-modal-input">
                        </div>
                    </div>

                    {{-- Modal Footer with Loading State --}}
                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t" style="border-color: var(--ec-border-subtle);">
                        <button type="button" wire:click="$set('showExtendScheduleModal', false)"
                            class="ec-modal-btn-cancel">
                            {{ __('إلغاء') }}
                        </button>
                        <button type="button" wire:click="submitExtendSchedule" wire:loading.attr="disabled"
                            class="ec-modal-btn-confirm">
                            <span wire:loading.remove wire:target="submitExtendSchedule" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>{{ __('تأكيد التمديد وتوليد الحصص') }}</span>
                            </span>
                            <span wire:loading wire:target="submitExtendSchedule" class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                                <span>{{ __('جاري التمديد والتوليد...') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── MODAL 4: DETAILED KPI ALGORITHMIC LOGIC MODAL ─────────────── --}}
        @if ($showKpiDetailModal && $inspectedKpi)
            @php
                $modalMeta = $this->getKpiMetadata($inspectedKpi);
            @endphp
            @if ($modalMeta)
                <div class="ec-modal-overlay" wire:click.self="closeKpiModal">
                    <div class="ec-modal-panel" style="max-width: 36rem;">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--ec-border-subtle);">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $modalMeta['badge_class'] }}">
                                    <i class="fa-solid {{ $modalMeta['icon'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-base" style="color: var(--ec-text-main);">
                                        {{ $modalMeta['title'] }}
                                    </h3>
                                    <span class="text-xs text-slate-400">
                                        {{ __('المعمارية البرمجية ومنطق إدارة دورة حياة الاشتراكات') }}
                                    </span>
                                </div>
                            </div>
                            <button type="button" wire:click="closeKpiModal" class="text-slate-400 hover:text-white text-xl font-bold cursor-pointer">
                                &times;
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="space-y-4 text-xs sm:text-sm">
                            {{-- Status Badge Row --}}
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/40 border border-slate-700/50">
                                <div>
                                    <span class="text-xs text-slate-400 block">{{ __('مستوى الأولوية والخطورة:') }}</span>
                                    <span class="font-extrabold text-sm {{ $modalMeta['badge_class'] }} px-2 py-0.5 rounded-full inline-block mt-0.5">
                                        {{ $modalMeta['urgency'] }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="text-xs text-slate-400 block">{{ __('إجمالي الحالات المتأثرة:') }}</span>
                                    <span class="text-lg font-black text-white">
                                        {{ $modalMeta['count'] }} {{ __('سجل') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Detailed Rules --}}
                            <div class="space-y-2.5">
                                <details class="p-3 rounded-xl bg-slate-800/30 border border-slate-700/40 group cursor-pointer">
                                    <summary class="font-bold text-teal-400 flex items-center justify-between list-none">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-code"></i>
                                            <span>{{ __('كود وقاعدة الاستعلام البرمجي (للمشرف الفني / المطور)') }}</span>
                                        </div>
                                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180"></i>
                                    </summary>
                                    <div class="font-mono text-xs p-2 rounded bg-black/40 text-teal-300 border border-teal-500/20 overflow-x-auto mt-2">
                                        <code>{{ $modalMeta['sql_condition'] }}</code>
                                    </div>
                                </details>

                                <div class="p-3 rounded-xl bg-slate-800/30 border border-slate-700/40">
                                    <div class="font-bold text-slate-200 flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-clipboard-check text-blue-400"></i>
                                        <span>{{ __('القاعدة التشغيلية والمالية (Business Rule)') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        {{ $modalMeta['business_rule'] }}
                                    </p>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-800/30 border border-slate-700/40">
                                    <div class="font-bold text-slate-200 flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-shield-virus text-amber-400"></i>
                                        <span>{{ __('الأثر التشغيلي على النظام التعليمي') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        {{ $modalMeta['operational_impact'] }}
                                    </p>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-800/30 border border-slate-700/40">
                                    <div class="font-bold text-slate-200 flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-wand-magic-sparkles text-emerald-400"></i>
                                        <span>{{ __('إجراءات المعالجة السريعة المتاحة للمشرف') }}</span>
                                    </div>
                                    <ul class="text-xs text-slate-400 list-disc list-inside space-y-1 mt-1">
                                        <li>{{ __('إرسال رسالة تذكير مخصصة وفورية عبر الواتساب متضمنة تفاصيل الحصص المتبقية.') }}</li>
                                        <li>{{ __('تجديد الباقة أو إضافة رصيد حصص استثنائي بنقرة زر واحدة دون مغادرة الصفحة.') }}</li>
                                        <li>{{ __('تمديد الدورة الشهرية للجداول الدورية مع التوليد الآلي لكافة الحصص.') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-between pt-3 border-t" style="border-color: var(--ec-border-subtle);">
                            <button type="button" wire:click="closeKpiModal" class="ec-filter-chip">
                                {{ __('إغلاق') }}
                            </button>
                            <button type="button" wire:click="closeKpiModal" class="ec-btn ec-btn-renew px-5 py-2.5 text-xs">
                                <i class="fa-solid fa-table-list me-1"></i>
                                {{ __('متابعة واستعراض السجلات في الجدول') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

    </div>
</x-filament-panels::page>
