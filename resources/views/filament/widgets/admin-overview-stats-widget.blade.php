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

    $sysPrimaryHex = (function_exists('demo_enabled') && demo_enabled() && config('demo.branding.primary_color'))
        ? config('demo.branding.primary_color')
        : '#0D9488';
    $cleanPrimaryHex = ltrim((string) $sysPrimaryHex, '#');
    if (strlen($cleanPrimaryHex) === 3) {
        $cleanPrimaryHex = $cleanPrimaryHex[0] . $cleanPrimaryHex[0] . $cleanPrimaryHex[1] . $cleanPrimaryHex[1] . $cleanPrimaryHex[2] . $cleanPrimaryHex[2];
    }
    $sysPrimaryRgb = preg_match('/^[0-9a-fA-F]{6}$/', $cleanPrimaryHex)
        ? hexdec(substr($cleanPrimaryHex, 0, 2)) . ', ' . hexdec(substr($cleanPrimaryHex, 2, 2)) . ', ' . hexdec(substr($cleanPrimaryHex, 4, 2))
        : '13, 148, 136';

    // Pure presentation-layer ratios derived strictly from existing $overview['kpis'] (zero extra queries)
    $grossRaw = max(0.0, (float) ($kpis['collected_revenue']['raw_value'] ?? 0));
    $earnedRaw = max(0.0, (float) ($kpis['earned_deferred_revenue']['raw_value'] ?? 0));
    $realizationPct = $grossRaw > 0 ? min(100, (int) round(($earnedRaw / $grossRaw) * 100)) : 0;
    $deferredPct = $grossRaw > 0 ? max(0, 100 - $realizationPct) : 0;

    $remCredits = (int) ($kpis['active_credits_pool']['sub_metrics'][0]['value'] ?? 0);
    $capCredits = max(1, (int) ($kpis['active_credits_pool']['sub_metrics'][1]['value'] ?? 0));
    $creditsPoolPct = (int) ($kpis['active_credits_pool']['raw_value'] ?? 0) > 0
        ? min(100, (int) round(($remCredits / $capCredits) * 100))
        : 0;

    $deliveryStr = (string) ($kpis['session_delivery']['value'] ?? '0%');
    $deliveryPct = max(0, min(100, (int) round((float) rtrim($deliveryStr, '%'))));

    $coverageStr = (string) ($kpis['parents_coverage']['sub_metrics'][1]['value'] ?? '0%');
    $coveragePct = max(0, min(100, (int) round((float) rtrim($coverageStr, '%'))));

    $approvedStu = (int) ($kpis['students_overview']['raw_value'] ?? 0);
    $totalStu = max(1, (int) ($kpis['students_overview']['sub_metrics'][0]['value'] ?? 0));
    $approvedStuPct = (int) ($kpis['students_overview']['sub_metrics'][0]['value'] ?? 0) > 0
        ? min(100, (int) round(($approvedStu / $totalStu) * 100))
        : 0;

    $pendingIssuesCount = (int) ($kpis['pending_receivables']['raw_value'] ?? 0)
        + (int) ($kpis['collections_due']['raw_value'] ?? 0)
        + (int) ($kpis['open_exceptions']['raw_value'] ?? 0);

    $donutSlice1 = max(2, min(92, $realizationPct > 0 ? (int) round($realizationPct * 0.45) : 34));
    $donutSlice2 = max(2, min(95, $donutSlice1 + ($creditsPoolPct > 0 ? (int) round($creditsPoolPct * 0.35) : 33)));
    $donutSlice3 = max($donutSlice2 + 2, min(99, $donutSlice2 + ($coveragePct > 0 ? (int) round($coveragePct * 0.20) : 20)));
@endphp

<x-filament-widgets::widget>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="elite-admin-kpi-board" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
        <style>
            /* =====================================================================
               ELITE SMART GLASSMORPHISM EXECUTIVE BOARD — SYSTEM COLORS & DUAL THEME
               ===================================================================== */
            .elite-admin-kpi-board {
                --sys-primary: {{ $sysPrimaryHex }};
                --sys-primary-rgb: {{ $sysPrimaryRgb }};
                --sys-primary-dark: color-mix(in srgb, {{ $sysPrimaryHex }} 80%, #020617);
                --sys-primary-light: color-mix(in srgb, {{ $sysPrimaryHex }} 76%, #FFFFFF);
                --kpi-font: 'Cairo', 'Tajawal', 'Inter', system-ui, -apple-system, sans-serif;
                font-family: var(--kpi-font);
                display: flex;
                flex-direction: column;
                gap: 1.35rem;
                width: 100%;
                box-sizing: border-box;

                /* Light Mode Smart Glass Tokens */
                --kpi-blur: blur(22px) saturate(165%);
                --kpi-surface: rgba(255, 255, 255, 0.72);
                --kpi-surface-elevated: rgba(244, 248, 253, 0.78);
                --kpi-surface-sub: rgba(236, 243, 250, 0.82);
                --kpi-border: rgba(15, 23, 42, 0.08);
                --kpi-border-strong: rgba(var(--sys-primary-rgb), 0.36);
                --kpi-specular: inset 0 1px 0 rgba(255, 255, 255, 0.92);
                --kpi-text: #0F172A;
                --kpi-text-muted: #475569;
                --kpi-text-dim: #64748B;
                --kpi-shadow: 0 14px 34px -10px rgba(15, 23, 42, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.90);
                --kpi-shadow-hover: 0 22px 46px -12px rgba(var(--sys-primary-rgb), 0.20), inset 0 1px 0 rgba(255, 255, 255, 0.95);
                --kpi-pill-bg: rgba(241, 245, 249, 0.85);
                --kpi-pill-text: #334155;
                --kpi-modal-overlay: rgba(15, 23, 42, 0.55);
                --kpi-modal-bg: rgba(255, 255, 255, 0.90);
                --kpi-formula-bg: rgba(var(--sys-primary-rgb), 0.08);
                --kpi-formula-border: rgba(var(--sys-primary-rgb), 0.28);
                --kpi-formula-text: #0F766E;
                --kpi-table-head: rgba(241, 245, 249, 0.85);
                --kpi-table-hover: rgba(var(--sys-primary-rgb), 0.05);
            }

            html.dark .elite-admin-kpi-board,
            .dark .elite-admin-kpi-board,
            [data-theme="dark"] .elite-admin-kpi-board {
                --kpi-surface: rgba(13, 21, 41, 0.56);
                --kpi-surface-elevated: rgba(20, 32, 60, 0.62);
                --kpi-surface-sub: rgba(17, 27, 52, 0.74);
                --kpi-border: rgba(255, 255, 255, 0.10);
                --kpi-border-strong: rgba(var(--sys-primary-rgb), 0.52);
                --kpi-specular: inset 0 1px 0 rgba(255, 255, 255, 0.13);
                --kpi-text: #F8FAFC;
                --kpi-text-muted: #CBD5E1;
                --kpi-text-dim: #94A3B8;
                --kpi-shadow: 0 20px 46px -12px rgba(0, 0, 0, 0.58), inset 0 1px 0 rgba(255, 255, 255, 0.10);
                --kpi-shadow-hover: 0 26px 56px -12px rgba(0, 0, 0, 0.76), 0 0 0 1px rgba(var(--sys-primary-rgb), 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.16);
                --kpi-pill-bg: rgba(22, 34, 62, 0.78);
                --kpi-pill-text: #CBD5E1;
                --kpi-modal-overlay: rgba(2, 6, 23, 0.76);
                --kpi-modal-bg: rgba(10, 17, 36, 0.84);
                --kpi-formula-bg: rgba(var(--sys-primary-rgb), 0.14);
                --kpi-formula-border: rgba(var(--sys-primary-rgb), 0.38);
                --kpi-formula-text: #5EEAD4;
                --kpi-table-head: rgba(20, 32, 60, 0.82);
                --kpi-table-hover: rgba(var(--sys-primary-rgb), 0.10);
            }

            /* ── 1. SMART GLASS COMMAND TOOLBAR ── */
            .kpi-toolbar {
                position: relative;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem 1.25rem;
                border-radius: 1.25rem;
                background: var(--kpi-surface);
                backdrop-filter: var(--kpi-blur);
                -webkit-backdrop-filter: var(--kpi-blur);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-shadow);
                overflow: hidden;
            }

            .kpi-toolbar::after {
                content: '';
                position: absolute;
                top: 0;
                left: 8%;
                right: 8%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(var(--sys-primary-rgb), 0.65), transparent);
                pointer-events: none;
            }

            .kpi-toolbar-brand {
                display: flex;
                align-items: center;
                gap: 0.9rem;
                min-width: 240px;
            }

            .kpi-toolbar-icon {
                width: 2.85rem;
                height: 2.85rem;
                border-radius: 0.9rem;
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), #0284C7);
                color: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
                flex-shrink: 0;
                box-shadow: 0 8px 24px -4px rgba(var(--sys-primary-rgb), 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.35);
            }

            .kpi-toolbar-title {
                margin: 0;
                font-size: 1.02rem;
                font-weight: 800;
                color: var(--kpi-text);
                display: flex;
                align-items: center;
                gap: 0.55rem;
                flex-wrap: wrap;
            }

            .kpi-live-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.7rem;
                font-weight: 800;
                padding: 0.2rem 0.6rem;
                border-radius: 9999px;
                background: rgba(16, 185, 129, 0.14);
                color: #059669;
                border: 1px solid rgba(16, 185, 129, 0.35);
                backdrop-filter: blur(8px);
            }

            html.dark .kpi-live-chip,
            .dark .kpi-live-chip {
                color: #34D399;
                background: rgba(16, 185, 129, 0.18);
            }

            .kpi-live-dot {
                width: 0.45rem;
                height: 0.45rem;
                border-radius: 9999px;
                background: #10B981;
                box-shadow: 0 0 8px #10B981;
                animation: kpiPulse 1.8s infinite;
            }

            @keyframes kpiPulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.45; transform: scale(1.25); }
            }

            .kpi-toolbar-sub {
                margin: 0.2rem 0 0;
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
                padding: 0.3rem;
                border-radius: 0.85rem;
                background: var(--kpi-surface-sub);
                backdrop-filter: blur(14px);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-specular);
            }

            .kpi-seg-btn {
                border: 1px solid transparent;
                background: transparent;
                color: var(--kpi-text-muted);
                font-family: inherit;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.42rem 0.74rem;
                border-radius: 0.6rem;
                cursor: pointer;
                transition: all 0.18s ease;
                white-space: nowrap;
            }

            .kpi-seg-btn:hover {
                color: var(--kpi-text);
                background: var(--kpi-surface);
            }

            .kpi-seg-btn.is-active {
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), var(--sys-primary-dark));
                color: #FFFFFF;
                border-color: rgba(255, 255, 255, 0.22);
                box-shadow: 0 6px 18px -4px rgba(var(--sys-primary-rgb), 0.48), inset 0 1px 0 rgba(255, 255, 255, 0.28);
            }

            /* ── 2. SMART GLASS BENTO HERO ANALYTICS ROW (3 FEATURE PANELS) ── */
            .sg-bento-row {
                display: grid;
                grid-template-columns: 1.2fr 1fr 1.15fr;
                gap: 1rem;
                width: 100%;
            }

            @media (max-width: 1180px) {
                .sg-bento-row {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
                .sg-bento-card:last-child {
                    grid-column: span 2;
                }
            }

            @media (max-width: 720px) {
                .sg-bento-row {
                    grid-template-columns: minmax(0, 1fr);
                }
                .sg-bento-card:last-child {
                    grid-column: span 1;
                }
            }

            .sg-bento-card {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 0.9rem;
                padding: 1.15rem 1.2rem;
                border-radius: 1.25rem;
                background: var(--kpi-surface);
                backdrop-filter: var(--kpi-blur);
                -webkit-backdrop-filter: var(--kpi-blur);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-shadow);
                overflow: hidden;
            }

            .sg-bento-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 12%;
                right: 12%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(var(--sys-primary-rgb), 0.65), transparent);
                pointer-events: none;
            }

            .sg-bento-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
            }

            .sg-bento-title {
                margin: 0;
                font-size: 0.9rem;
                font-weight: 800;
                color: var(--kpi-text);
                display: flex;
                align-items: center;
                gap: 0.45rem;
            }

            .sg-bento-tag {
                font-size: 0.68rem;
                font-weight: 800;
                padding: 0.16rem 0.52rem;
                border-radius: 9999px;
                background: rgba(var(--sys-primary-rgb), 0.14);
                color: var(--sys-primary);
                border: 1px solid rgba(var(--sys-primary-rgb), 0.28);
            }

            html.dark .sg-bento-tag,
            .dark .sg-bento-tag {
                color: var(--sys-primary-light);
            }

            /* Treemap Bento Mosaic (matches "Report Distribution" block) */
            .sg-mosaic-grid {
                display: grid;
                grid-template-columns: 1.18fr 1fr;
                grid-template-rows: 1fr auto;
                gap: 0.55rem;
                min-height: 168px;
            }

            .sg-mosaic-tile {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 0.8rem 0.85rem;
                border-radius: 0.95rem;
                color: #FFFFFF;
                border: 1px solid rgba(255, 255, 255, 0.22);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 8px 20px -6px rgba(0, 0, 0, 0.3);
                cursor: pointer;
                text-align: start;
                transition: transform 0.18s ease, filter 0.18s ease;
                overflow: hidden;
            }

            .sg-mosaic-tile:hover {
                transform: translateY(-2px);
                filter: brightness(1.06);
            }

            .sg-mosaic-main {
                grid-row: span 2;
                background: linear-gradient(145deg, rgba(var(--sys-primary-rgb), 0.95), #0F766E);
            }

            .sg-mosaic-sky {
                background: linear-gradient(145deg, rgba(2, 132, 199, 0.92), #4F46E5);
            }

            .sg-mosaic-split {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.55rem;
            }

            .sg-mosaic-emerald {
                background: linear-gradient(145deg, rgba(16, 185, 129, 0.92), #059669);
            }

            .sg-mosaic-amber {
                background: linear-gradient(145deg, rgba(245, 158, 11, 0.94), #E11D48);
            }

            .sg-mosaic-lbl {
                font-size: 0.71rem;
                font-weight: 700;
                opacity: 0.92;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.35rem;
            }

            .sg-mosaic-pct {
                font-size: 1.4rem;
                font-weight: 900;
                line-height: 1.1;
                font-variant-numeric: tabular-nums;
                margin-top: 0.35rem;
            }

            .sg-mosaic-sub {
                font-size: 0.68rem;
                font-weight: 700;
                opacity: 0.9;
                margin-top: 0.15rem;
            }

            /* Equalizer Metric Panel (matches "Reports Generated / Viewed") */
            .sg-eq-header {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 0.75rem;
            }

            .sg-eq-big {
                margin: 0;
                font-size: 1.85rem;
                font-weight: 900;
                color: var(--kpi-text);
                line-height: 1.05;
                font-variant-numeric: tabular-nums;
            }

            .sg-eq-bars {
                display: flex;
                align-items: flex-end;
                gap: 4px;
                height: 44px;
                padding: 4px 6px;
                border-radius: 0.65rem;
                background: var(--kpi-surface-sub);
                border: 1px solid var(--kpi-border);
            }

            .sg-eq-bar {
                width: 6px;
                border-radius: 9999px;
                background: linear-gradient(180deg, rgba(var(--sys-primary-rgb), 1), rgba(16, 185, 129, 0.65));
                box-shadow: 0 0 8px rgba(var(--sys-primary-rgb), 0.35);
            }

            .sg-eq-rows {
                display: flex;
                flex-direction: column;
                gap: 0.42rem;
            }

            .sg-eq-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.42rem 0.65rem;
                border-radius: 0.65rem;
                background: var(--kpi-surface-elevated);
                border: 1px solid var(--kpi-border);
                font-size: 0.76rem;
                cursor: pointer;
                transition: border-color 0.16s ease, transform 0.16s ease;
            }

            .sg-eq-row:hover {
                border-color: var(--kpi-border-strong);
                transform: translateX(2px);
            }

            html[dir="rtl"] .sg-eq-row:hover {
                transform: translateX(-2px);
            }

            .sg-eq-row-lbl {
                color: var(--kpi-text-muted);
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .sg-eq-row-val {
                color: var(--kpi-text);
                font-weight: 800;
                font-variant-numeric: tabular-nums;
            }

            /* Donut Ring + Pulse Heatmap Panel (matches "Share & Distribution" + "Usage") */
            .sg-ring-wrap {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }

            .sg-donut {
                position: relative;
                width: 112px;
                height: 112px;
                border-radius: 9999px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: conic-gradient(
                    var(--sys-primary) 0% {{ $donutSlice1 }}%,
                    #10B981 {{ $donutSlice1 }}% {{ $donutSlice2 }}%,
                    #38BDF8 {{ $donutSlice2 }}% {{ $donutSlice3 }}%,
                    rgba(148, 163, 184, 0.25) {{ $donutSlice3 }}% 100%
                );
                box-shadow: 0 10px 26px -6px rgba(var(--sys-primary-rgb), 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.28);
            }

            .sg-donut-inner {
                width: 78px;
                height: 78px;
                border-radius: 9999px;
                background: var(--kpi-surface-sub);
                backdrop-filter: blur(12px);
                border: 1px solid var(--kpi-border);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
            }

            .sg-donut-val {
                font-size: 1.05rem;
                font-weight: 900;
                color: var(--kpi-text);
                line-height: 1.05;
                font-variant-numeric: tabular-nums;
            }

            .sg-donut-lbl {
                font-size: 0.62rem;
                font-weight: 700;
                color: var(--kpi-text-dim);
            }

            .sg-heat-strip {
                display: grid;
                grid-template-columns: repeat(12, minmax(0, 1fr));
                gap: 4px;
                padding-top: 0.4rem;
            }

            .sg-heat-cell {
                height: 14px;
                border-radius: 4px;
                border: 1px solid rgba(255, 255, 255, 0.14);
                cursor: pointer;
                transition: transform 0.15s ease;
            }

            .sg-heat-cell:hover {
                transform: scale(1.18);
            }

            /* ── 3. SECTION WRAPPER & 4-COLUMN GLASS KPI GRID ── */
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
                width: 2rem;
                height: 2rem;
                border-radius: 0.65rem;
                background: var(--kpi-surface);
                backdrop-filter: blur(12px);
                border: 1px solid var(--kpi-border);
                color: var(--sys-primary);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                box-shadow: var(--kpi-specular);
            }

            html.dark .kpi-section-icon,
            .dark .kpi-section-icon {
                color: var(--sys-primary-light);
            }

            .kpi-section-title {
                margin: 0;
                font-size: 0.96rem;
                font-weight: 800;
                color: var(--kpi-text);
                letter-spacing: -0.01em;
            }

            .kpi-section-meta {
                font-size: 0.73rem;
                font-weight: 700;
                color: var(--kpi-text-dim);
                background: var(--kpi-surface);
                backdrop-filter: blur(12px);
                padding: 0.22rem 0.65rem;
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

            /* ── SMART GLASS KPI CARD ── */
            .kpi-card {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 0.82rem;
                padding: 1.15rem;
                border-radius: 1.2rem;
                background: var(--kpi-surface);
                backdrop-filter: var(--kpi-blur);
                -webkit-backdrop-filter: var(--kpi-blur);
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
                background: var(--kpi-accent-grad, linear-gradient(90deg, var(--sys-primary), #14B8A6));
                opacity: 0.95;
            }

            .kpi-card::after {
                content: '';
                position: absolute;
                top: -36px;
                inset-inline-start: -36px;
                width: 110px;
                height: 110px;
                border-radius: 9999px;
                background: var(--kpi-icon-bg, rgba(var(--sys-primary-rgb), 0.14));
                filter: blur(26px);
                pointer-events: none;
                opacity: 0.75;
            }

            .kpi-card:hover {
                transform: translateY(-3px);
                box-shadow: var(--kpi-shadow-hover);
                border-color: var(--kpi-border-strong);
            }

            .kpi-accent-emerald { --kpi-accent-grad: linear-gradient(90deg, #059669, #10B981); --kpi-icon-bg: rgba(16, 185, 129, 0.18); --kpi-icon-fg: #059669; }
            .kpi-accent-teal    { --kpi-accent-grad: linear-gradient(90deg, var(--sys-primary), #14B8A6); --kpi-icon-bg: rgba(var(--sys-primary-rgb), 0.18); --kpi-icon-fg: var(--sys-primary); }
            .kpi-accent-amber   { --kpi-accent-grad: linear-gradient(90deg, #D97706, #F59E0B); --kpi-icon-bg: rgba(245, 158, 11, 0.18); --kpi-icon-fg: #D97706; }
            .kpi-accent-rose    { --kpi-accent-grad: linear-gradient(90deg, #E11D48, #F43F5E); --kpi-icon-bg: rgba(244, 63, 94, 0.18);  --kpi-icon-fg: #E11D48; }
            .kpi-accent-indigo  { --kpi-accent-grad: linear-gradient(90deg, #4F46E5, #6366F1); --kpi-icon-bg: rgba(99, 102, 241, 0.18); --kpi-icon-fg: #4F46E5; }
            .kpi-accent-sky     { --kpi-accent-grad: linear-gradient(90deg, #0284C7, #38BDF8); --kpi-icon-bg: rgba(56, 189, 248, 0.18); --kpi-icon-fg: #0284C7; }

            html.dark .kpi-accent-emerald, .dark .kpi-accent-emerald { --kpi-icon-fg: #34D399; }
            html.dark .kpi-accent-teal,    .dark .kpi-accent-teal    { --kpi-icon-fg: var(--sys-primary-light); }
            html.dark .kpi-accent-amber,   .dark .kpi-accent-amber   { --kpi-icon-fg: #FBBF24; }
            html.dark .kpi-accent-rose,    .dark .kpi-accent-rose    { --kpi-icon-fg: #FB7185; }
            html.dark .kpi-accent-indigo,  .dark .kpi-accent-indigo  { --kpi-icon-fg: #A5B4FC; }
            html.dark .kpi-accent-sky,     .dark .kpi-accent-sky     { --kpi-icon-fg: #38BDF8; }

            .kpi-card-top {
                position: relative;
                z-index: 1;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.65rem;
            }

            .kpi-icon-box {
                width: 2.55rem;
                height: 2.55rem;
                border-radius: 0.8rem;
                background: var(--kpi-icon-bg, rgba(var(--sys-primary-rgb), 0.16));
                border: 1px solid rgba(255, 255, 255, 0.16);
                color: var(--kpi-icon-fg, var(--sys-primary));
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                flex-shrink: 0;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
            }

            .kpi-top-right {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 0.35rem;
                min-width: 0;
            }

            .kpi-mini-eq {
                display: inline-flex;
                align-items: flex-end;
                gap: 2px;
                height: 12px;
                opacity: 0.8;
            }

            .kpi-mini-eq i {
                display: block;
                width: 2.5px;
                border-radius: 9999px;
                background: var(--kpi-icon-fg, var(--sys-primary));
            }

            .kpi-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.22rem 0.62rem;
                border-radius: 9999px;
                font-size: 0.7rem;
                font-weight: 700;
                line-height: 1.25;
                white-space: nowrap;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                backdrop-filter: blur(8px);
            }

            .kpi-badge-success, .kpi-badge-emerald { background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); }
            .kpi-badge-teal    { background: rgba(var(--sys-primary-rgb), 0.15); color: var(--sys-primary-dark); border: 1px solid rgba(var(--sys-primary-rgb), 0.3); }
            .kpi-badge-warning, .kpi-badge-amber { background: rgba(245, 158, 11, 0.16); color: #B45309; border: 1px solid rgba(245, 158, 11, 0.32); }
            .kpi-badge-danger, .kpi-badge-rose   { background: rgba(244, 63, 94, 0.15);  color: #BE123C; border: 1px solid rgba(244, 63, 94, 0.3); }
            .kpi-badge-indigo  { background: rgba(99, 102, 241, 0.15); color: #4338CA; border: 1px solid rgba(99, 102, 241, 0.3); }
            .kpi-badge-info, .kpi-badge-sky      { background: rgba(14, 165, 233, 0.15); color: #0369A1; border: 1px solid rgba(14, 165, 233, 0.3); }
            .kpi-badge-neutral { background: var(--kpi-pill-bg); color: var(--kpi-pill-text); border: 1px solid var(--kpi-border); }

            html.dark .kpi-badge-success, .dark .kpi-badge-success,
            html.dark .kpi-badge-emerald, .dark .kpi-badge-emerald { color: #34D399; }
            html.dark .kpi-badge-teal,    .dark .kpi-badge-teal    { color: var(--sys-primary-light); }
            html.dark .kpi-badge-warning, .dark .kpi-badge-warning,
            html.dark .kpi-badge-amber,   .dark .kpi-badge-amber   { color: #FBBF24; }
            html.dark .kpi-badge-danger,  .dark .kpi-badge-danger,
            html.dark .kpi-badge-rose,    .dark .kpi-badge-rose    { color: #FB7185; }
            html.dark .kpi-badge-indigo,  .dark .kpi-badge-indigo  { color: #A5B4FC; }
            html.dark .kpi-badge-info,    .dark .kpi-badge-info,
            html.dark .kpi-badge-sky,     .dark .kpi-badge-sky     { color: #38BDF8; }

            .kpi-card-body {
                position: relative;
                z-index: 1;
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
                font-weight: 900;
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
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.5rem;
                padding: 0.58rem;
                border-radius: 0.8rem;
                background: var(--kpi-surface-elevated);
                backdrop-filter: blur(10px);
                border: 1px solid var(--kpi-border);
                box-shadow: var(--kpi-specular);
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
                position: relative;
                z-index: 1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                padding-top: 0.48rem;
                border-top: 1px dashed var(--kpi-border);
            }

            .kpi-inspect-hint {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                font-size: 0.73rem;
                font-weight: 700;
                color: var(--kpi-icon-fg, var(--sys-primary));
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
                padding: 0.32rem 0.65rem;
                border-radius: 0.58rem;
                text-decoration: none;
                transition: all 0.16s ease;
                white-space: nowrap;
                backdrop-filter: blur(8px);
            }

            .kpi-tab-link:hover {
                color: #FFFFFF;
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), var(--sys-primary-dark));
                border-color: rgba(255, 255, 255, 0.25);
                box-shadow: 0 6px 14px -4px rgba(var(--sys-primary-rgb), 0.45);
            }

            /* ── SMART GLASS DRILL-DOWN MODAL ── */
            .kpi-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: var(--kpi-modal-overlay);
                backdrop-filter: blur(12px) saturate(140%);
                -webkit-backdrop-filter: blur(12px) saturate(140%);
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
                border-radius: 1.4rem;
                background: var(--kpi-modal-bg);
                backdrop-filter: blur(28px) saturate(170%);
                -webkit-backdrop-filter: blur(28px) saturate(170%);
                border: 1px solid var(--kpi-border-strong);
                box-shadow: 0 32px 72px -14px rgba(0, 0, 0, 0.65), var(--kpi-specular);
                overflow: hidden;
                color: var(--kpi-text);
            }

            .kpi-modal-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.2rem 1.45rem;
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
                padding: 0.2rem 0.68rem;
                border-radius: 0.58rem;
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), var(--sys-primary-dark));
                color: #FFFFFF;
                font-variant-numeric: tabular-nums;
                box-shadow: 0 6px 16px -4px rgba(var(--sys-primary-rgb), 0.42), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            }

            .kpi-modal-sub {
                margin: 0.22rem 0 0;
                font-size: 0.8rem;
                color: var(--kpi-text-dim);
            }

            .kpi-modal-close {
                width: 2.2rem;
                height: 2.2rem;
                border-radius: 0.65rem;
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
                background: #E11D48;
                border-color: #E11D48;
                color: #FFFFFF;
            }

            .kpi-modal-body {
                padding: 1.25rem 1.45rem;
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
                border-radius: 0.9rem;
                background: var(--kpi-formula-bg);
                border: 1px solid var(--kpi-formula-border);
                color: var(--kpi-formula-text);
                font-size: 0.82rem;
                font-weight: 600;
                line-height: 1.5;
                backdrop-filter: blur(10px);
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
                border-radius: 0.68rem;
                background: var(--kpi-surface-elevated);
                border: 1px solid var(--kpi-border);
                font-size: 0.78rem;
                box-shadow: var(--kpi-specular);
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
                border-radius: 0.95rem;
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
                color: var(--sys-primary);
                text-decoration: none;
                padding: 0.28rem 0.58rem;
                border-radius: 0.45rem;
                border: 1px solid rgba(var(--sys-primary-rgb), 0.32);
                background: rgba(var(--sys-primary-rgb), 0.10);
                white-space: nowrap;
            }

            html.dark .kpi-row-link,
            .dark .kpi-row-link {
                color: var(--sys-primary-light);
            }

            .kpi-row-link:hover {
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), var(--sys-primary-dark));
                color: #FFFFFF;
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
                padding: 0.95rem 1.45rem;
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
                border-radius: 0.68rem;
                border: 1px solid var(--kpi-border);
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
                padding: 0.55rem 1.15rem;
                border-radius: 0.68rem;
                border: 1px solid rgba(255, 255, 255, 0.22);
                background: linear-gradient(135deg, rgba(var(--sys-primary-rgb), 0.96), var(--sys-primary-dark));
                color: #FFFFFF;
                font-family: inherit;
                font-size: 0.8rem;
                font-weight: 800;
                text-decoration: none;
                box-shadow: 0 8px 20px -4px rgba(var(--sys-primary-rgb), 0.48), inset 0 1px 0 rgba(255, 255, 255, 0.28);
            }

            .kpi-btn-primary:hover {
                filter: brightness(1.06);
            }

            @media print {
                .elite-admin-kpi-board *,
                .kpi-toolbar,
                .sg-bento-card,
                .kpi-card {
                    backdrop-filter: none !important;
                    -webkit-backdrop-filter: none !important;
                    background: #FFFFFF !important;
                    color: #0F172A !important;
                    box-shadow: none !important;
                }
            }
        </style>

        {{-- 1. SMART GLASS COMMAND TOOLBAR (CATEGORY & TIMEFRAME CONTROLS) --}}
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
                            ? 'تصميم زجاجي ذكي بألوان النظام — انقر على أي مؤشر أو لوحة لفحص السجلات ومعادلة الاحتساب.'
                            : 'Smart Glass OS telemetry in your system colors — click any panel or KPI to inspect live records & formula.' }}
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

        {{-- 2. SMART GLASS BENTO HERO ANALYTICS ROW (ALL CATEGORY VIEW) --}}
        @if ($categoryFilter === 'all' && ! empty($kpis))
            <div class="sg-bento-row">
                {{-- Bento Tile Mosaic: Accounting & Credit Distribution --}}
                <div class="sg-bento-card">
                    <div class="sg-bento-head">
                        <h3 class="sg-bento-title">
                            <i class="fa-solid fa-layer-group" style="color: var(--sys-primary);"></i>
                            {{ $isAr ? 'توزيع المحفظة المالية والأرصدة' : 'Financial & Credit Mix' }}
                        </h3>
                        <span class="sg-bento-tag">{{ $timeframes[$timeframe] ?? '' }}</span>
                    </div>

                    <div class="sg-mosaic-grid">
                        <button type="button" wire:click="inspectKpi('earned_deferred_revenue')" class="sg-mosaic-tile sg-mosaic-main">
                            <div class="sg-mosaic-lbl">
                                <span>{{ $isAr ? 'إيراد محقق' : 'Earned Share' }}</span>
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <div class="sg-mosaic-pct">{{ $realizationPct }}%</div>
                                <div class="sg-mosaic-sub">{{ $kpis['earned_deferred_revenue']['value'] ?? '0' }}</div>
                            </div>
                        </button>

                        <button type="button" wire:click="inspectKpi('collected_revenue')" class="sg-mosaic-tile sg-mosaic-sky">
                            <div class="sg-mosaic-lbl">
                                <span>{{ $isAr ? 'المؤجل / المعتمد' : 'Deferred Pool' }}</span>
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div>
                                <div class="sg-mosaic-pct">{{ $deferredPct }}%</div>
                                <div class="sg-mosaic-sub">{{ $kpis['earned_deferred_revenue']['sub_metrics'][0]['value'] ?? '0' }}</div>
                            </div>
                        </button>

                        <div class="sg-mosaic-split">
                            <button type="button" wire:click="inspectKpi('active_credits_pool')" class="sg-mosaic-tile sg-mosaic-emerald">
                                <div class="sg-mosaic-lbl">
                                    <span>{{ $isAr ? 'رصيد متاح' : 'Pool Left' }}</span>
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <div>
                                    <div class="sg-mosaic-pct">{{ $creditsPoolPct }}%</div>
                                    <div class="sg-mosaic-sub">{{ $remCredits }} {{ $isAr ? 'حصة' : 'cr' }}</div>
                                </div>
                            </button>

                            <button type="button" wire:click="inspectKpi('collections_due')" class="sg-mosaic-tile sg-mosaic-amber">
                                <div class="sg-mosaic-lbl">
                                    <span>{{ $isAr ? 'معلق / متابعة' : 'Action Queue' }}</span>
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <div>
                                    <div class="sg-mosaic-pct">{{ $pendingIssuesCount }}</div>
                                    <div class="sg-mosaic-sub">{{ $isAr ? 'سجل متابعة' : 'Items Due' }}</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Equalizer Metric Panel: Live Operations Delivery --}}
                <div class="sg-bento-card">
                    <div class="sg-bento-head">
                        <h3 class="sg-bento-title">
                            <i class="fa-solid fa-signal" style="color: #10B981;"></i>
                            {{ $isAr ? 'مؤشر تسليم الحصص المباشرة' : 'Delivery Velocity & Queue' }}
                        </h3>
                        <span class="sg-bento-tag">{{ $kpis['todays_sessions']['badge_text'] ?? '' }}</span>
                    </div>

                    <div class="sg-eq-header">
                        <div>
                            <p class="sg-eq-big">{{ $kpis['session_delivery']['value'] ?? '0%' }}</p>
                            <span style="font-size: 0.74rem; color: var(--kpi-text-dim); font-weight: 700;">
                                {{ $kpis['session_delivery']['badge_text'] ?? '' }} · {{ $kpis['upcoming_sessions']['badge_text'] ?? '' }}
                            </span>
                        </div>
                        <div class="sg-eq-bars" aria-hidden="true">
                            @foreach ([34, 52, 44, 68, 60, 82, 74, max(28, $deliveryPct)] as $idx => $h)
                                <span class="sg-eq-bar" style="height: {{ min(100, $h) }}%; opacity: {{ 0.55 + ($idx * 0.05) }};"></span>
                            @endforeach
                        </div>
                    </div>

                    <div class="sg-eq-rows">
                        <div class="sg-eq-row" wire:click="inspectKpi('todays_sessions')">
                            <span class="sg-eq-row-lbl"><i class="fa-solid fa-tower-broadcast" style="color: var(--sys-primary);"></i> {{ $isAr ? 'حصص اليوم' : "Today's Scheduled" }}</span>
                            <span class="sg-eq-row-val">{{ $kpis['todays_sessions']['value'] ?? '0' }}</span>
                        </div>
                        <div class="sg-eq-row" wire:click="inspectKpi('upcoming_sessions')">
                            <span class="sg-eq-row-lbl"><i class="fa-solid fa-calendar-days" style="color: #6366F1;"></i> {{ $isAr ? 'الحصص القادمة' : 'Upcoming Pipeline' }}</span>
                            <span class="sg-eq-row-val">{{ $kpis['upcoming_sessions']['value'] ?? '0' }}</span>
                        </div>
                        <div class="sg-eq-row" wire:click="inspectKpi('open_exceptions')">
                            <span class="sg-eq-row-lbl"><i class="fa-solid fa-envelope-open-text" style="color: #F59E0B;"></i> {{ $isAr ? 'الأعذار المعلقة' : 'Open Exceptions' }}</span>
                            <span class="sg-eq-row-val">{{ $kpis['open_exceptions']['value'] ?? '0' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Donut Ring & 12-KPI Pulse Matrix --}}
                <div class="sg-bento-card">
                    <div class="sg-bento-head">
                        <h3 class="sg-bento-title">
                            <i class="fa-solid fa-chart-donut" style="color: #38BDF8;"></i>
                            {{ $isAr ? 'توازن التحصيل والتغطية الأكاديمية' : 'Realization & Coverage Pulse' }}
                        </h3>
                        <span class="sg-bento-tag">{{ $approvedStuPct }}% {{ $isAr ? 'اعتماد' : 'Approved' }}</span>
                    </div>

                    <div class="sg-ring-wrap">
                        <div class="sg-donut" role="img" aria-label="System Realization Breakdown">
                            <div class="sg-donut-inner">
                                <span class="sg-donut-val">{{ $realizationPct }}%</span>
                                <span class="sg-donut-lbl">{{ $isAr ? 'تحقق مالي' : 'Realized' }}</span>
                            </div>
                        </div>

                        <div class="sg-eq-rows" style="flex: 1; min-width: 0;">
                            <div class="sg-eq-row" wire:click="inspectKpi('collected_revenue')">
                                <span class="sg-eq-row-lbl"><span style="width:8px;height:8px;border-radius:9999px;background:var(--sys-primary);"></span> {{ $isAr ? 'إجمالي الإيراد' : 'Gross Booked' }}</span>
                                <span class="sg-eq-row-val">{{ $kpis['collected_revenue']['value'] ?? '0' }}</span>
                            </div>
                            <div class="sg-eq-row" wire:click="inspectKpi('students_overview')">
                                <span class="sg-eq-row-lbl"><span style="width:8px;height:8px;border-radius:9999px;background:#10B981;"></span> {{ $isAr ? 'الطلاب المعتمدون' : 'Active Students' }}</span>
                                <span class="sg-eq-row-val">{{ $approvedStu }} / {{ $kpis['students_overview']['sub_metrics'][0]['value'] ?? '0' }}</span>
                            </div>
                            <div class="sg-eq-row" wire:click="inspectKpi('parents_coverage')">
                                <span class="sg-eq-row-lbl"><span style="width:8px;height:8px;border-radius:9999px;background:#38BDF8;"></span> {{ $isAr ? 'تغطية أولياء الأمور' : 'Family Linked' }}</span>
                                <span class="sg-eq-row-val">{{ $coveragePct }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="sg-heat-strip" title="{{ $isAr ? 'نبض المؤشرات الاثني عشر — انقر للفحص' : '12-KPI live pulse — click any cell to inspect' }}">
                        @foreach ($kpis as $pulseKey => $pulseKpi)
                            @php
                                $cellBg = match ($pulseKpi['accent'] ?? 'teal') {
                                    'emerald' => 'linear-gradient(135deg, #10B981, #059669)',
                                    'amber' => 'linear-gradient(135deg, #F59E0B, #D97706)',
                                    'rose' => 'linear-gradient(135deg, #F43F5E, #E11D48)',
                                    'indigo' => 'linear-gradient(135deg, #6366F1, #4F46E5)',
                                    'sky' => 'linear-gradient(135deg, #38BDF8, #0284C7)',
                                    default => 'linear-gradient(135deg, var(--sys-primary), #0F766E)',
                                };
                            @endphp
                            <button
                                type="button"
                                wire:click="inspectKpi('{{ $pulseKey }}')"
                                class="sg-heat-cell"
                                style="background: {{ $cellBg }}; opacity: {{ ($pulseKpi['raw_value'] ?? 0) > 0 ? '0.95' : '0.38' }};"
                                title="{{ $pulseKpi['title'] }}: {{ $pulseKpi['value'] }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- 3. BALANCED 4-COLUMN SECTIONS (FINANCIALS, OPERATIONS, ACADEMIC) --}}
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
                                <div class="kpi-top-right">
                                    <span class="kpi-badge kpi-badge-{{ $kpi['badge_variant'] }}">
                                        {{ $kpi['badge_text'] }}
                                    </span>
                                    <span class="kpi-mini-eq" aria-hidden="true">
                                        <i style="height: 4px;"></i>
                                        <i style="height: 8px;"></i>
                                        <i style="height: 6px;"></i>
                                        <i style="height: 11px;"></i>
                                        <i style="height: 9px;"></i>
                                    </span>
                                </div>
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

        {{-- 4. INTERACTIVE SMART GLASS DRILL-DOWN MODAL --}}
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
