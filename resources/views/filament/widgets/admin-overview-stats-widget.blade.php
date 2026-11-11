@php
    $isAr = app()->getLocale() === 'ar';
    $sections = $overview['sections'] ?? [];
    $kpis = $overview['kpis'] ?? [];
    $visibleSections = $categoryFilter === 'all'
        ? $sections
        : array_filter($sections, fn (array $sec, string $key) => $key === $categoryFilter, ARRAY_FILTER_USE_BOTH);

    $timeframes = [
        'all' => $isAr ? 'كل الفترات' : 'All Time',
        'today' => $isAr ? 'اليوم' : 'Today',
        'month' => $isAr ? 'هذا الشهر' : 'This Month',
        'year' => $isAr ? 'هذا العام' : 'This Year',
    ];

    $categories = [
        'all' => $isAr ? 'الكل (12)' : 'All (12)',
        'financials' => $isAr ? 'المالية والمحاسبة (4)' : 'Financials (4)',
        'operations' => $isAr ? 'العمليات المباشرة (4)' : 'Operations (4)',
        'academic' => $isAr ? 'المجتمع الأكاديمي (4)' : 'Academic (4)',
    ];
@endphp

<x-filament-widgets::widget>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="elite-admin-kpi-board" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
        <style>
            /* =====================================================================
               ELITE EXECUTIVE KPI BOARD — DUAL THEME (LIGHT & DARK MODE)
               Self-contained CSS custom properties; zero uncompiled Tailwind reliance
            ===================================================================== */
            .elite-admin-kpi-board {
                --kpi-font: 'Tajawal', 'Cairo', 'Inter', system-ui, -apple-system, sans-serif;
                font-family: var(--kpi-font);
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
                box-sizing: border-box;

                /* Light Mode Tokens */
                --kpi-surface: #ffffff;
                --kpi-surface-elevated: #f8fafc;
                --kpi-surface-sub: #f1f5f9;
                --kpi-border: #e2e8f0;
                --kpi-border-strong: #cbd5e1;
                --kpi-text: #0f172a;
                --kpi-text-muted: #475569;
                --kpi-text-dim: #64748b;
                --kpi-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.06);
                --kpi-shadow-hover: 0 14px 28px -8px rgba(15, 23, 42, 0.12);
                --kpi-pill-bg: #f1f5f9;
                --kpi-pill-text: #334155;
                --kpi-modal-overlay: rgba(15, 23, 42, 0.62);
                --kpi-formula-bg: #f0fdfa;
                --kpi-formula-border: #99f6e4;
                --kpi-formula-text: #115e59;
                --kpi-table-head: #f8fafc;
                --kpi-table-hover: #f8fafc;
            }

            html.dark .elite-admin-kpi-board,
            .dark .elite-admin-kpi-board,
            [data-theme="dark"] .elite-admin-kpi-board {
                --kpi-surface: #0f172a;
                --kpi-surface-elevated: #16223a;
                --kpi-surface-sub: #1e293b;
                --kpi-border: rgba(255, 255, 255, 0.09);
                --kpi-border-strong: rgba(255, 255, 255, 0.18);
                --kpi-text: #f8fafc;
                --kpi-text-muted: #cbd5e1;
                --kpi-text-dim: #94a3b8;
                --kpi-shadow: 0 8px 28px -6px rgba(0, 0, 0, 0.45);
                --kpi-shadow-hover: 0 16px 36px -8px rgba(0, 0, 0, 0.65);
                --kpi-pill-bg: rgba(30, 41, 59, 0.85);
                --kpi-pill-text: #cbd5e1;
                --kpi-modal-overlay: rgba(2, 6, 23, 0.78);
                --kpi-formula-bg: rgba(13, 148, 136, 0.14);
                --kpi-formula-border: rgba(45, 212, 191, 0.35);
                --kpi-formula-text: #5eead4;
                --kpi-table-head: #141f36;
                --kpi-table-hover: rgba(30, 41, 59, 0.65);
            }

            /* ── EXECUTIVE CONTROL BAR ── */
            .kpi-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.25rem;
                border-radius: 1rem;
                background: var(--kpi-surface);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-shadow);
            }

            .kpi-toolbar-brand {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                min-width: 240px;
            }

            .kpi-toolbar-icon {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.85rem;
                background: linear-gradient(135deg, #0d9488, #0284c7);
                color: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
                flex-shrink: 0;
                box-shadow: 0 6px 18px -4px rgba(13, 148, 136, 0.45);
            }

            .kpi-toolbar-title {
                margin: 0;
                font-size: 1rem;
                font-weight: 800;
                color: var(--kpi-text);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                flex-wrap: wrap;
            }

            .kpi-live-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.7rem;
                font-weight: 700;
                padding: 0.18rem 0.55rem;
                border-radius: 9999px;
                background: rgba(16, 185, 129, 0.14);
                color: #059669;
                border: 1px solid rgba(16, 185, 129, 0.3);
            }

            html.dark .kpi-live-chip,
            .dark .kpi-live-chip {
                color: #34d399;
                background: rgba(16, 185, 129, 0.18);
            }

            .kpi-live-dot {
                width: 0.45rem;
                height: 0.45rem;
                border-radius: 9999px;
                background: #10b981;
                animation: kpiPulse 1.8s infinite;
            }

            @keyframes kpiPulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.45; transform: scale(1.25); }
            }

            .kpi-toolbar-sub {
                margin: 0.18rem 0 0;
                font-size: 0.78rem;
                color: var(--kpi-text-dim);
            }

            .kpi-toolbar-controls {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.75rem;
            }

            .kpi-seg-group {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.25rem;
                padding: 0.28rem;
                border-radius: 0.75rem;
                background: var(--kpi-surface-sub);
                border: 1px solid var(--kpi-border);
            }

            .kpi-seg-btn {
                border: 0;
                background: transparent;
                color: var(--kpi-text-muted);
                font-family: inherit;
                font-size: 0.76rem;
                font-weight: 700;
                padding: 0.42rem 0.72rem;
                border-radius: 0.55rem;
                cursor: pointer;
                transition: all 0.18s ease;
                white-space: nowrap;
            }

            .kpi-seg-btn:hover {
                color: var(--kpi-text);
                background: var(--kpi-surface);
            }

            .kpi-seg-btn.is-active {
                background: linear-gradient(135deg, #0d9488, #059669);
                color: #ffffff;
                box-shadow: 0 4px 12px -2px rgba(13, 148, 136, 0.4);
            }

            /* ── SECTION WRAPPER & 4-COLUMN GRID ── */
            .kpi-section {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
            }

            .kpi-section-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 0 0.25rem;
            }

            .kpi-section-title-wrap {
                display: flex;
                align-items: center;
                gap: 0.6rem;
            }

            .kpi-section-icon {
                width: 1.95rem;
                height: 1.95rem;
                border-radius: 0.6rem;
                background: var(--kpi-surface-sub);
                border: 1px solid var(--kpi-border);
                color: #0d9488;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
            }

            html.dark .kpi-section-icon,
            .dark .kpi-section-icon {
                color: #2dd4bf;
            }

            .kpi-section-title {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 800;
                color: var(--kpi-text);
                letter-spacing: -0.01em;
            }

            .kpi-section-meta {
                font-size: 0.74rem;
                font-weight: 700;
                color: var(--kpi-text-dim);
                background: var(--kpi-pill-bg);
                padding: 0.2rem 0.6rem;
                border-radius: 9999px;
                border: 1px solid var(--kpi-border);
            }

            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 1rem;
                width: 100%;
            }

            @media (max-width: 1279px) {
                .kpi-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 639px) {
                .kpi-grid {
                    grid-template-columns: minmax(0, 1fr);
                }
            }

            /* ── EXECUTIVE KPI CARD ── */
            .kpi-card {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 0.85rem;
                padding: 1.15rem;
                border-radius: 1rem;
                background: var(--kpi-surface);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-shadow);
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                overflow: hidden;
                text-align: start;
                box-sizing: border-box;
            }

            .kpi-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: var(--kpi-accent-grad, linear-gradient(90deg, #0d9488, #14b8a6));
                opacity: 0.92;
            }

            .kpi-card:hover {
                transform: translateY(-2px);
                box-shadow: var(--kpi-shadow-hover);
                border-color: var(--kpi-border-strong);
            }

            .kpi-accent-emerald { --kpi-accent-grad: linear-gradient(90deg, #059669, #10b981); --kpi-icon-bg: rgba(16, 185, 129, 0.14); --kpi-icon-fg: #059669; }
            .kpi-accent-teal    { --kpi-accent-grad: linear-gradient(90deg, #0d9488, #14b8a6); --kpi-icon-bg: rgba(20, 184, 166, 0.14); --kpi-icon-fg: #0d9488; }
            .kpi-accent-amber   { --kpi-accent-grad: linear-gradient(90deg, #d97706, #f59e0b); --kpi-icon-bg: rgba(245, 158, 11, 0.15); --kpi-icon-fg: #d97706; }
            .kpi-accent-rose    { --kpi-accent-grad: linear-gradient(90deg, #e11d48, #f43f5e); --kpi-icon-bg: rgba(244, 63, 94, 0.14);  --kpi-icon-fg: #e11d48; }
            .kpi-accent-indigo  { --kpi-accent-grad: linear-gradient(90deg, #4f46e5, #6366f1); --kpi-icon-bg: rgba(99, 102, 241, 0.14); --kpi-icon-fg: #4f46e5; }
            .kpi-accent-sky     { --kpi-accent-grad: linear-gradient(90deg, #0284c7, #38bdf8); --kpi-icon-bg: rgba(56, 189, 248, 0.14); --kpi-icon-fg: #0284c7; }

            html.dark .kpi-accent-emerald, .dark .kpi-accent-emerald { --kpi-icon-fg: #34d399; }
            html.dark .kpi-accent-teal,    .dark .kpi-accent-teal    { --kpi-icon-fg: #2dd4bf; }
            html.dark .kpi-accent-amber,   .dark .kpi-accent-amber   { --kpi-icon-fg: #fbbf24; }
            html.dark .kpi-accent-rose,    .dark .kpi-accent-rose    { --kpi-icon-fg: #fb7185; }
            html.dark .kpi-accent-indigo,  .dark .kpi-accent-indigo  { --kpi-icon-fg: #a5b4fc; }
            html.dark .kpi-accent-sky,     .dark .kpi-accent-sky     { --kpi-icon-fg: #38bdf8; }

            .kpi-card-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.65rem;
            }

            .kpi-icon-box {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.75rem;
                background: var(--kpi-icon-bg, rgba(13, 148, 136, 0.14));
                color: var(--kpi-icon-fg, #0d9488);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                flex-shrink: 0;
            }

            .kpi-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.22rem 0.62rem;
                border-radius: 9999px;
                font-size: 0.71rem;
                font-weight: 700;
                line-height: 1.25;
                white-space: nowrap;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kpi-badge-success, .kpi-badge-emerald { background: rgba(16, 185, 129, 0.14); color: #047857; border: 1px solid rgba(16, 185, 129, 0.28); }
            .kpi-badge-teal    { background: rgba(20, 184, 166, 0.14); color: #0f766e; border: 1px solid rgba(20, 184, 166, 0.28); }
            .kpi-badge-warning, .kpi-badge-amber { background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3); }
            .kpi-badge-danger, .kpi-badge-rose   { background: rgba(244, 63, 94, 0.14);  color: #be123c; border: 1px solid rgba(244, 63, 94, 0.28); }
            .kpi-badge-indigo  { background: rgba(99, 102, 241, 0.14); color: #4338ca; border: 1px solid rgba(99, 102, 241, 0.28); }
            .kpi-badge-info, .kpi-badge-sky      { background: rgba(14, 165, 233, 0.14); color: #0369a1; border: 1px solid rgba(14, 165, 233, 0.28); }
            .kpi-badge-neutral { background: var(--kpi-pill-bg); color: var(--kpi-pill-text); border: 1px solid var(--kpi-border); }

            html.dark .kpi-badge-success, .dark .kpi-badge-success,
            html.dark .kpi-badge-emerald, .dark .kpi-badge-emerald { color: #34d399; }
            html.dark .kpi-badge-teal,    .dark .kpi-badge-teal    { color: #2dd4bf; }
            html.dark .kpi-badge-warning, .dark .kpi-badge-warning,
            html.dark .kpi-badge-amber,   .dark .kpi-badge-amber   { color: #fbbf24; }
            html.dark .kpi-badge-danger,  .dark .kpi-badge-danger,
            html.dark .kpi-badge-rose,    .dark .kpi-badge-rose    { color: #fb7185; }
            html.dark .kpi-badge-indigo,  .dark .kpi-badge-indigo  { color: #a5b4fc; }
            html.dark .kpi-badge-info,    .dark .kpi-badge-info,
            html.dark .kpi-badge-sky,     .dark .kpi-badge-sky     { color: #38bdf8; }

            .kpi-card-body {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .kpi-card-title {
                margin: 0;
                font-size: 0.84rem;
                font-weight: 700;
                color: var(--kpi-text-muted);
                line-height: 1.35;
            }

            .kpi-card-value {
                margin: 0;
                font-size: 1.65rem;
                font-weight: 800;
                color: var(--kpi-text);
                line-height: 1.15;
                font-variant-numeric: tabular-nums;
                letter-spacing: -0.02em;
            }

            .kpi-card-sub {
                margin: 0;
                font-size: 0.74rem;
                color: var(--kpi-text-dim);
                line-height: 1.4;
                min-height: 2.1rem;
            }

            .kpi-submetrics {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.5rem;
                padding: 0.55rem;
                border-radius: 0.75rem;
                background: var(--kpi-surface-elevated);
                border: 1px solid var(--kpi-border);
            }

            .kpi-submetric-item {
                display: flex;
                flex-direction: column;
                gap: 0.12rem;
                min-width: 0;
            }

            .kpi-submetric-label {
                font-size: 0.68rem;
                font-weight: 600;
                color: var(--kpi-text-dim);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kpi-submetric-val {
                font-size: 0.82rem;
                font-weight: 800;
                color: var(--kpi-text);
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .kpi-card-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                padding-top: 0.45rem;
                border-top: 1px dashed var(--kpi-border);
            }

            .kpi-inspect-hint {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.73rem;
                font-weight: 700;
                color: var(--kpi-icon-fg, #0d9488);
            }

            .kpi-tab-link {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.72rem;
                font-weight: 700;
                color: var(--kpi-text-muted);
                background: var(--kpi-surface-sub);
                border: 1px solid var(--kpi-border);
                padding: 0.32rem 0.62rem;
                border-radius: 0.55rem;
                text-decoration: none;
                transition: all 0.16s ease;
                white-space: nowrap;
            }

            .kpi-tab-link:hover {
                color: #ffffff;
                background: #0d9488;
                border-color: #0d9488;
            }

            /* ── DRILL-DOWN MODAL ── */
            .kpi-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: var(--kpi-modal-overlay);
                backdrop-filter: blur(5px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
                box-sizing: border-box;
            }

            .kpi-modal-dialog {
                width: 100%;
                max-width: 960px;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
                border-radius: 1.25rem;
                background: var(--kpi-surface);
                border: 1px solid var(--kpi-border-strong);
                box-shadow: 0 24px 64px -12px rgba(0, 0, 0, 0.55);
                overflow: hidden;
                color: var(--kpi-text);
            }

            .kpi-modal-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.15rem 1.4rem;
                background: var(--kpi-surface-elevated);
                border-bottom: 1px solid var(--kpi-border);
            }

            .kpi-modal-head-left {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                min-width: 0;
            }

            .kpi-modal-title {
                margin: 0;
                font-size: 1.08rem;
                font-weight: 800;
                color: var(--kpi-text);
                display: flex;
                align-items: center;
                gap: 0.55rem;
                flex-wrap: wrap;
            }

            .kpi-modal-headline-val {
                font-size: 0.95rem;
                font-weight: 800;
                padding: 0.18rem 0.65rem;
                border-radius: 0.55rem;
                background: linear-gradient(135deg, #0d9488, #059669);
                color: #ffffff;
                font-variant-numeric: tabular-nums;
            }

            .kpi-modal-sub {
                margin: 0.22rem 0 0;
                font-size: 0.8rem;
                color: var(--kpi-text-dim);
            }

            .kpi-modal-close {
                width: 2.15rem;
                height: 2.15rem;
                border-radius: 0.6rem;
                border: 1px solid var(--kpi-border);
                background: var(--kpi-surface);
                color: var(--kpi-text-muted);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                flex-shrink: 0;
                transition: all 0.16s ease;
            }

            .kpi-modal-close:hover {
                background: #e11d48;
                border-color: #e11d48;
                color: #ffffff;
            }

            .kpi-modal-body {
                padding: 1.25rem 1.4rem;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .kpi-formula-box {
                display: flex;
                align-items: flex-start;
                gap: 0.65rem;
                padding: 0.85rem 1rem;
                border-radius: 0.85rem;
                background: var(--kpi-formula-bg);
                border: 1px solid var(--kpi-formula-border);
                color: var(--kpi-formula-text);
                font-size: 0.82rem;
                font-weight: 600;
                line-height: 1.5;
            }

            .kpi-pills-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.6rem;
            }

            .kpi-stat-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.42rem 0.78rem;
                border-radius: 0.65rem;
                background: var(--kpi-surface-elevated);
                border: 1px solid var(--kpi-border);
                font-size: 0.78rem;
            }

            .kpi-stat-pill-lbl {
                color: var(--kpi-text-dim);
                font-weight: 600;
            }

            .kpi-stat-pill-val {
                color: var(--kpi-text);
                font-weight: 800;
                font-variant-numeric: tabular-nums;
            }

            .kpi-table-wrap {
                border: 1px solid var(--kpi-border);
                border-radius: 0.9rem;
                overflow-x: auto;
                background: var(--kpi-surface);
            }

            .kpi-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.82rem;
                text-align: start;
            }

            .kpi-table thead th {
                background: var(--kpi-table-head);
                color: var(--kpi-text-dim);
                font-size: 0.72rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                padding: 0.72rem 0.9rem;
                border-bottom: 1px solid var(--kpi-border);
                white-space: nowrap;
            }

            .kpi-table tbody tr {
                border-bottom: 1px solid var(--kpi-border);
                transition: background 0.15s ease;
            }

            .kpi-table tbody tr:last-child {
                border-bottom: 0;
            }

            .kpi-table tbody tr:hover {
                background: var(--kpi-table-hover);
            }

            .kpi-table td {
                padding: 0.72rem 0.9rem;
                color: var(--kpi-text);
                vertical-align: middle;
            }

            .kpi-row-primary {
                font-weight: 700;
                color: var(--kpi-text);
            }

            .kpi-row-secondary {
                color: var(--kpi-text-muted);
                font-size: 0.78rem;
            }

            .kpi-row-metric {
                font-weight: 800;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
            }

            .kpi-row-meta {
                font-size: 0.75rem;
                color: var(--kpi-text-dim);
            }

            .kpi-row-link {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                font-size: 0.74rem;
                font-weight: 700;
                color: #0d9488;
                text-decoration: none;
                padding: 0.28rem 0.58rem;
                border-radius: 0.45rem;
                border: 1px solid rgba(13, 148, 136, 0.3);
                background: rgba(13, 148, 136, 0.08);
                white-space: nowrap;
            }

            html.dark .kpi-row-link,
            .dark .kpi-row-link {
                color: #2dd4bf;
            }

            .kpi-row-link:hover {
                background: #0d9488;
                color: #ffffff;
            }

            .kpi-empty-state {
                padding: 2rem 1rem;
                text-align: center;
                color: var(--kpi-text-dim);
                font-size: 0.86rem;
                font-weight: 600;
            }

            .kpi-modal-foot {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 0.95rem 1.4rem;
                background: var(--kpi-surface-elevated);
                border-top: 1px solid var(--kpi-border);
            }

            .kpi-modal-foot-links {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.5rem;
            }

            .kpi-modal-foot-actions {
                display: flex;
                align-items: center;
                gap: 0.6rem;
            }

            .kpi-btn-secondary {
                padding: 0.5rem 0.95rem;
                border-radius: 0.65rem;
                border: 1px solid var(--kpi-border-strong);
                background: var(--kpi-surface);
                color: var(--kpi-text);
                font-family: inherit;
                font-size: 0.8rem;
                font-weight: 700;
                cursor: pointer;
                text-decoration: none;
            }

            .kpi-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.54rem 1.1rem;
                border-radius: 0.65rem;
                border: 0;
                background: linear-gradient(135deg, #0d9488, #059669);
                color: #ffffff;
                font-family: inherit;
                font-size: 0.8rem;
                font-weight: 800;
                text-decoration: none;
                box-shadow: 0 6px 18px -4px rgba(13, 148, 136, 0.45);
            }

            .kpi-btn-primary:hover {
                filter: brightness(1.06);
            }
        </style>

        {{-- 1. EXECUTIVE TOOLBAR (CATEGORY & TIMEFRAME CONTROLS) --}}
        <div class="kpi-toolbar">
            <div class="kpi-toolbar-brand">
                <span class="kpi-toolbar-icon">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                <div>
                    <h2 class="kpi-toolbar-title">
                        <span>{{ $isAr ? 'مركز القيادة المالية والأكاديمية' : 'Executive Financial & Operations Telemetry' }}</span>
                        <span class="kpi-live-chip">
                            <span class="kpi-live-dot"></span>
                            {{ $isAr ? 'بيانات حية ومباشرة' : 'Live Reconciled' }}
                        </span>
                    </h2>
                    <p class="kpi-toolbar-sub">
                        {{ $isAr
                            ? 'انقر على أي مؤشر لفحص السجلات الحقيقية ومعادلة الاحتساب، أو افتح الجدول المصفّى مباشرة.'
                            : 'Click any KPI to inspect live records & accounting formula, or jump straight to its filtered table.' }}
                    </p>
                </div>
            </div>

            <div class="kpi-toolbar-controls">
                <div class="kpi-seg-group" role="tablist" aria-label="{{ $isAr ? 'تصنيف المؤشرات' : 'KPI Category Filter' }}">
                    @foreach ($categories as $catKey => $catLabel)
                        <button
                            type="button"
                            wire:click="setCategoryFilter('{{ $catKey }}')"
                            class="kpi-seg-btn {{ $categoryFilter === $catKey ? 'is-active' : '' }}"
                        >
                            {{ $catLabel }}
                        </button>
                    @endforeach
                </div>

                <div class="kpi-seg-group" role="tablist" aria-label="{{ $isAr ? 'النطاق الزمني' : 'Timeframe Selector' }}">
                    @foreach ($timeframes as $tfKey => $tfLabel)
                        <button
                            type="button"
                            wire:click="setTimeframe('{{ $tfKey }}')"
                            class="kpi-seg-btn {{ $timeframe === $tfKey ? 'is-active' : '' }}"
                        >
                            {{ $tfLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 2. BALANCED 4-COLUMN SECTIONS (FINANCIALS, OPERATIONS, ACADEMIC) --}}
        @foreach ($visibleSections as $sectionKey => $section)
            <section class="kpi-section" wire:key="kpi-section-{{ $sectionKey }}">
                <div class="kpi-section-header">
                    <div class="kpi-section-title-wrap">
                        <span class="kpi-section-icon">
                            <i class="fa-solid {{ $section['icon'] }}"></i>
                        </span>
                        <h3 class="kpi-section-title">{{ $section['title'] }}</h3>
                    </div>
                    <span class="kpi-section-meta">
                        {{ count($section['keys']) }} {{ $isAr ? 'مؤشرات' : 'KPIs' }} · {{ $timeframes[$timeframe] ?? '' }}
                    </span>
                </div>

                <div class="kpi-grid">
                    @foreach ($section['keys'] as $kpiKey)
                        @php $kpi = $kpis[$kpiKey] ?? null; @endphp
                        @continue(! $kpi)

                        <article
                            wire:key="kpi-card-{{ $kpi['key'] }}"
                            wire:click="inspectKpi('{{ $kpi['key'] }}')"
                            role="button"
                            tabindex="0"
                            onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); $wire.inspectKpi('{{ $kpi['key'] }}'); }"
                            class="kpi-card kpi-accent-{{ $kpi['accent'] }}"
                        >
                            <div class="kpi-card-top">
                                <span class="kpi-icon-box">
                                    <i class="fa-solid {{ $kpi['icon'] }}"></i>
                                </span>
                                <span class="kpi-badge kpi-badge-{{ $kpi['badge_variant'] }}">
                                    {{ $kpi['badge_text'] }}
                                </span>
                            </div>

                            <div class="kpi-card-body">
                                <h4 class="kpi-card-title">{{ $kpi['title'] }}</h4>
                                <p class="kpi-card-value">{{ $kpi['value'] }}</p>
                                <p class="kpi-card-sub">{{ $kpi['subtitle'] }}</p>
                            </div>

                            <div class="kpi-submetrics">
                                @foreach ($kpi['sub_metrics'] as $sub)
                                    <div class="kpi-submetric-item">
                                        <span class="kpi-submetric-label">{{ $sub['label'] }}</span>
                                        <span class="kpi-submetric-val">{{ $sub['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="kpi-card-actions">
                                <span class="kpi-inspect-hint">
                                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                                    {{ $isAr ? 'فحص التفاصيل' : 'Inspect Live' }}
                                </span>

                                <a
                                    href="{{ $kpi['target_url'] }}"
                                    onclick="event.stopPropagation()"
                                    class="kpi-tab-link"
                                    title="{{ $kpi['target_label'] }}"
                                >
                                    <span>{{ $isAr ? 'الجدول' : 'Table' }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- 3. INTERACTIVE KPI DRILL-DOWN MODAL --}}
        @if ($showDetailModal && ! empty($inspectedDetail))
            <div
                class="kpi-modal-backdrop"
                x-data
                x-on:keydown.escape.window="$wire.closeDetailModal()"
                wire:click.self="closeDetailModal"
                role="dialog"
                aria-modal="true"
                aria-label="{{ $inspectedDetail['title'] }}"
            >
                <div class="kpi-modal-dialog kpi-accent-{{ $inspectedDetail['accent'] ?? 'teal' }}">
                    <div class="kpi-modal-head">
                        <div class="kpi-modal-head-left">
                            <span class="kpi-icon-box">
                                <i class="fa-solid {{ $inspectedDetail['icon'] ?? 'fa-chart-bar' }}"></i>
                            </span>
                            <div>
                                <h3 class="kpi-modal-title">
                                    <span>{{ $inspectedDetail['title'] }}</span>
                                    <span class="kpi-modal-headline-val">{{ $inspectedDetail['headline_value'] }}</span>
                                    <span class="kpi-badge kpi-badge-{{ $inspectedDetail['badge_variant'] ?? 'neutral' }}">
                                        {{ $inspectedDetail['badge_text'] }}
                                    </span>
                                </h3>
                                <p class="kpi-modal-sub">
                                    {{ $inspectedDetail['subtitle'] }} ·
                                    <strong>{{ $inspectedDetail['timeframe_label'] ?? '' }}</strong>
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeDetailModal"
                            class="kpi-modal-close"
                            aria-label="{{ $isAr ? 'إغلاق' : 'Close' }}"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="kpi-modal-body">
                        @if (! empty($inspectedDetail['logic_rule']))
                            <div class="kpi-formula-box">
                                <i class="fa-solid fa-calculator" style="margin-top: 2px;"></i>
                                <span>{{ $inspectedDetail['logic_rule'] }}</span>
                            </div>
                        @endif

                        @if (! empty($inspectedDetail['summary_pills']))
                            <div class="kpi-pills-row">
                                @foreach ($inspectedDetail['summary_pills'] as $pill)
                                    <div class="kpi-stat-pill">
                                        <span class="kpi-stat-pill-lbl">{{ $pill['label'] }}:</span>
                                        <span class="kpi-stat-pill-val">{{ $pill['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="kpi-table-wrap">
                            @if (! empty($inspectedDetail['rows']))
                                <table class="kpi-table">
                                    <thead>
                                        <tr>
                                            <th>{{ $isAr ? 'السجل / المستفيد' : 'Record / Party' }}</th>
                                            <th>{{ $isAr ? 'التفاصيل' : 'Context & Plan' }}</th>
                                            <th>{{ $isAr ? 'المقياس الفعلي' : 'Metric' }}</th>
                                            <th>{{ $isAr ? 'ملاحظات / تاريخ' : 'Audit Meta' }}</th>
                                            <th>{{ $isAr ? 'الحالة الحقيقية' : 'Real Status' }}</th>
                                            <th>{{ $isAr ? 'إجراء' : 'Action' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inspectedDetail['rows'] as $row)
                                            <tr>
                                                <td class="kpi-row-primary">{{ $row['primary'] }}</td>
                                                <td class="kpi-row-secondary">{{ $row['secondary'] }}</td>
                                                <td class="kpi-row-metric">{{ $row['metric'] }}</td>
                                                <td class="kpi-row-meta">{{ $row['meta'] }}</td>
                                                <td>
                                                    <span class="kpi-badge kpi-badge-{{ $row['status_color'] ?? 'neutral' }}">
                                                        {{ $row['status_label'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if (! empty($row['url']))
                                                        <a href="{{ $row['url'] }}" class="kpi-row-link">
                                                            <span>{{ $isAr ? 'فتح' : 'Open' }}</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                        </a>
                                                    @else
                                                        <span class="kpi-row-meta">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="kpi-empty-state">
                                    <i class="fa-regular fa-folder-open" style="font-size: 1.5rem; margin-bottom: 0.4rem; display: block;"></i>
                                    {{ $isAr ? 'لا توجد سجلات مطابقة لهذا الفلتر في الفترة المحددة.' : 'No matching records found for this filter & timeframe.' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="kpi-modal-foot">
                        <div class="kpi-modal-foot-links">
                            @foreach ($inspectedDetail['secondary_links'] ?? [] as $secLink)
                                <a href="{{ $secLink['url'] }}" class="kpi-tab-link">
                                    <span>{{ $secLink['label'] }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            @endforeach
                        </div>

                        <div class="kpi-modal-foot-actions">
                            <button type="button" wire:click="closeDetailModal" class="kpi-btn-secondary">
                                {{ $isAr ? 'إغلاق' : 'Close' }}
                            </button>
                            <a href="{{ $inspectedDetail['target_url'] }}" class="kpi-btn-primary">
                                <span>{{ $inspectedDetail['target_label'] }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
