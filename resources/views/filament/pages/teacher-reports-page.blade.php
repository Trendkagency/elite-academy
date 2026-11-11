<x-filament-panels::page>
    <div class="rpt-wrapper">
        {{-- Comprehensive Embedded Styles for Reporting Views (Dual Light & Dark Mode) --}}
        <style>
            .rpt-wrapper {
                /* Light Mode Design Tokens */
                --rpt-bg-surface: #FFFFFF;
                --rpt-bg-subtle: #F8FAFC;
                --rpt-bg-elevated: #FFFFFF;
                --rpt-bg-hover: rgba(241, 245, 249, 0.75);
                --rpt-border: #E2E8F0;
                --rpt-border-subtle: #F1F5F9;
                --rpt-text-primary: #0F172A;
                --rpt-text-secondary: #334155;
                --rpt-text-muted: #64748B;
                --rpt-text-dim: #94A3B8;

                --rpt-indigo: #4338CA;
                --rpt-indigo-bg: #EEF2FF;
                --rpt-indigo-border: rgba(99, 102, 241, 0.25);
                --rpt-teal: #0D9488;
                --rpt-teal-bg: #CCFBF1;
                --rpt-teal-border: rgba(13, 148, 136, 0.25);
                --rpt-rose: #E11D48;
                --rpt-rose-bg: #FFE4E6;
                --rpt-rose-border: rgba(225, 29, 72, 0.25);
                --rpt-purple: #9333EA;
                --rpt-purple-bg: #F3E8FF;
                --rpt-purple-border: rgba(147, 51, 234, 0.25);
                --rpt-sky: #0284C7;

                font-family: 'Cairo', sans-serif !important;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                color: var(--rpt-text-primary);
                animation: rptFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            html.dark .rpt-wrapper,
            .dark .rpt-wrapper,
            [data-theme="dark"] .rpt-wrapper {
                /* Dark Mode Design Tokens */
                --rpt-bg-surface: #0F172A;
                --rpt-bg-subtle: #162033;
                --rpt-bg-elevated: #0B1120;
                --rpt-bg-hover: rgba(30, 41, 59, 0.6);
                --rpt-border: rgba(51, 65, 85, 0.85);
                --rpt-border-subtle: rgba(30, 41, 59, 0.85);
                --rpt-text-primary: #F8FAFC;
                --rpt-text-secondary: #CBD5E1;
                --rpt-text-muted: #94A3B8;
                --rpt-text-dim: #64748B;

                --rpt-indigo: #818CF8;
                --rpt-indigo-bg: rgba(99, 102, 241, 0.16);
                --rpt-indigo-border: rgba(129, 140, 248, 0.3);
                --rpt-teal: #2DD4BF;
                --rpt-teal-bg: rgba(20, 184, 166, 0.16);
                --rpt-teal-border: rgba(45, 212, 191, 0.3);
                --rpt-rose: #FB7185;
                --rpt-rose-bg: rgba(244, 63, 94, 0.16);
                --rpt-rose-border: rgba(251, 113, 133, 0.3);
                --rpt-purple: #C084FC;
                --rpt-purple-bg: rgba(168, 85, 247, 0.16);
                --rpt-purple-border: rgba(192, 132, 252, 0.3);
                --rpt-sky: #38BDF8;
            }

            @keyframes rptFadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* --- Control Topbar --- */
            .rpt-topbar {
                background: var(--rpt-bg-surface);
                border: 1.5px solid var(--rpt-border);
                border-radius: 1.25rem;
                padding: 1rem 1.25rem;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                transition: all 0.2s ease;
            }
            html.dark .rpt-topbar {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .rpt-search-icon {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                inset-inline-start: 0.9rem;
                color: var(--rpt-text-muted);
                pointer-events: none;
            }

            .rpt-search-input {
                width: 100%;
                background: var(--rpt-bg-subtle);
                border: 1.5px solid var(--rpt-border);
                border-radius: 0.875rem;
                padding-block: 0.65rem;
                padding-inline-start: 2.5rem;
                padding-inline-end: 1rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--rpt-text-primary);
                outline: none;
                transition: all 0.2s ease;
                box-sizing: border-box;
            }
            .rpt-search-input::placeholder {
                color: var(--rpt-text-muted);
            }
            .rpt-search-input:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
            }

            .rpt-select {
                background: var(--rpt-bg-subtle);
                border: 1.5px solid var(--rpt-border);
                border-radius: 0.875rem;
                padding: 0.65rem 1rem;
                font-size: 0.85rem;
                font-weight: 700;
                color: var(--rpt-text-primary);
                cursor: pointer;
                outline: none;
                transition: all 0.2s ease;
                min-width: 260px;
            }
            .rpt-select:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
            }

            .rpt-btn {
                background: var(--rpt-bg-subtle);
                color: var(--rpt-text-secondary);
                border: 1px solid var(--rpt-border);
                border-radius: 0.875rem;
                padding: 0.65rem 1.25rem;
                font-size: 0.825rem;
                font-weight: 800;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .rpt-btn:hover {
                background: var(--rpt-bg-hover);
                color: var(--rpt-text-primary);
                transform: translateY(-1px);
            }

            /* --- Hero Teacher Banner Card --- */
            .rpt-hero-teacher {
                background: linear-gradient(135deg, #312E81 0%, #4338CA 50%, #1E1B4B 100%);
                color: #FFFFFF;
                border-radius: 1.5rem;
                padding: 2rem;
                position: relative;
                overflow: hidden;
                box-shadow: 0 10px 25px -5px rgba(49, 46, 129, 0.4);
                border: 1px solid rgba(129, 140, 248, 0.3);
            }
            .rpt-hero-teacher::before {
                content: '';
                position: absolute;
                top: -40px;
                right: -40px;
                width: 240px;
                height: 240px;
                background: radial-gradient(circle, rgba(165, 180, 252, 0.25) 0%, rgba(255,255,255,0) 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .rpt-avatar {
                width: 5rem !important;
                height: 5rem !important;
                min-width: 5rem !important;
                min-height: 5rem !important;
                max-width: 5rem !important;
                max-height: 5rem !important;
                border-radius: 1.25rem !important;
                background: rgba(255, 255, 255, 0.15) !important;
                backdrop-filter: blur(10px) !important;
                border: 2px solid rgba(255, 255, 255, 0.3) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 2.25rem !important;
                font-weight: 800 !important;
                color: #E0E7FF !important;
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1) !important;
                overflow: hidden !important;
                flex-shrink: 0 !important;
            }
            .rpt-avatar img {
                width: 100% !important;
                height: 100% !important;
                max-width: 5rem !important;
                max-height: 5rem !important;
                object-fit: cover !important;
                display: block !important;
                border-radius: inherit !important;
            }

            /* --- KPI Cards Grid --- */
            .rpt-grid-4 {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                gap: 1.25rem;
            }

            .rpt-stat-card {
                background: var(--rpt-bg-surface);
                border: 1.5px solid var(--rpt-border);
                border-radius: 1.25rem;
                padding: 1.25rem 1.5rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }
            html.dark .rpt-stat-card {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            }
            .rpt-stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
                border-color: var(--rpt-indigo-border);
            }
            html.dark .rpt-stat-card:hover {
                box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.4);
            }

            .rpt-stat-label {
                font-size: 0.75rem;
                font-weight: 800;
                color: var(--rpt-text-muted);
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            .rpt-stat-icon {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.875rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            .rpt-stat-icon-indigo { background: var(--rpt-indigo-bg); color: var(--rpt-indigo); border: 1px solid var(--rpt-indigo-border); }
            .rpt-stat-icon-teal   { background: var(--rpt-teal-bg);   color: var(--rpt-teal);   border: 1px solid var(--rpt-teal-border); }
            .rpt-stat-icon-rose   { background: var(--rpt-rose-bg);   color: var(--rpt-rose);   border: 1px solid var(--rpt-rose-border); }
            .rpt-stat-icon-purple { background: var(--rpt-purple-bg); color: var(--rpt-purple); border: 1px solid var(--rpt-purple-border); }

            .rpt-kpi-val {
                font-size: 1.85rem;
                font-weight: 900;
                line-height: 1;
            }
            .rpt-kpi-val-indigo { color: var(--rpt-indigo); }
            .rpt-kpi-val-teal   { color: var(--rpt-teal); }
            .rpt-kpi-val-rose   { color: var(--rpt-rose); }
            .rpt-kpi-val-purple { color: var(--rpt-purple); }

            .rpt-kpi-sub {
                font-size: 0.775rem;
                color: var(--rpt-text-muted);
                margin: 0.5rem 0 0 0;
                font-weight: 600;
            }
            .rpt-kpi-sub strong {
                color: var(--rpt-text-primary);
                font-weight: 800;
            }

            /* --- Content Box & Tabs --- */
            .rpt-main-box {
                background: var(--rpt-bg-surface);
                border: 1.5px solid var(--rpt-border);
                border-radius: 1.5rem;
                overflow: hidden;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            }
            html.dark .rpt-main-box {
                box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
            }

            .rpt-tabs-header {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.25rem 0;
                border-bottom: 1.5px solid var(--rpt-border);
                overflow-x: auto;
                scrollbar-width: none;
            }
            .rpt-tabs-header::-webkit-scrollbar {
                display: none;
            }

            .rpt-tab-btn {
                padding: 0.875rem 1.15rem;
                font-size: 0.875rem;
                font-weight: 800;
                border: none;
                background: transparent;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                white-space: nowrap;
                border-bottom: 3px solid transparent;
                color: var(--rpt-text-muted);
                transition: all 0.2s ease;
            }
            .rpt-tab-btn:hover {
                color: var(--rpt-indigo);
            }
            .rpt-tab-btn.active {
                color: var(--rpt-indigo);
                border-bottom-color: var(--rpt-indigo);
            }

            .rpt-tab-count {
                font-size: 0.75rem;
                font-weight: 800;
                padding: 0.15rem 0.6rem;
                border-radius: 9999px;
                background: var(--rpt-bg-subtle);
                color: var(--rpt-text-muted);
                border: 1px solid var(--rpt-border);
            }
            .rpt-tab-btn.active .rpt-tab-count {
                background: var(--rpt-indigo-bg);
                color: var(--rpt-indigo);
                border-color: var(--rpt-indigo-border);
            }

            /* --- Section Headings & Cards (100% Dual Theme) --- */
            .rpt-section-heading {
                font-size: 0.925rem;
                font-weight: 800;
                color: var(--rpt-text-primary);
                text-transform: uppercase;
                letter-spacing: 0.02em;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .rpt-courses-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
                gap: 1rem;
            }

            .rpt-course-card {
                background: var(--rpt-bg-subtle);
                border: 1.5px solid var(--rpt-border);
                border-radius: 1.15rem;
                padding: 1.25rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 0.85rem;
                transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            }
            .rpt-course-card:hover {
                transform: translateY(-2px);
                border-color: var(--rpt-indigo);
                box-shadow: 0 8px 20px -6px rgba(99, 102, 241, 0.15);
            }

            .rpt-course-title {
                font-size: 0.975rem;
                font-weight: 800;
                color: var(--rpt-text-primary);
                margin: 0;
                line-height: 1.35;
            }

            .rpt-course-metrics {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.65rem;
                text-align: center;
                padding-top: 0.75rem;
                border-top: 1px solid var(--rpt-border);
            }

            .rpt-course-metric-box {
                background: var(--rpt-bg-elevated);
                padding: 0.55rem 0.75rem;
                border-radius: 0.75rem;
                border: 1px solid var(--rpt-border);
            }

            .rpt-course-metric-label {
                color: var(--rpt-text-muted);
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            .rpt-course-metric-val {
                font-weight: 900;
                font-size: 1rem;
                margin-top: 0.15rem;
                color: var(--rpt-text-primary);
            }

            .rpt-note-card,
            .rpt-exception-card {
                background: var(--rpt-bg-subtle);
                border: 1.5px solid var(--rpt-border);
                border-radius: 1rem;
                padding: 1.15rem;
                transition: border-color 0.2s ease;
            }
            .rpt-note-card:hover,
            .rpt-exception-card:hover {
                border-color: var(--rpt-indigo-border);
            }

            .rpt-card-title {
                font-size: 0.875rem;
                font-weight: 800;
                color: var(--rpt-text-primary);
                margin: 0;
            }

            .rpt-card-body {
                font-size: 0.825rem;
                color: var(--rpt-text-secondary);
                margin: 0;
                line-height: 1.6;
            }

            .rpt-muted-text {
                color: var(--rpt-text-muted);
            }

            .rpt-strong-text {
                font-weight: 800;
                color: var(--rpt-text-primary);
            }

            .rpt-empty-card {
                padding: 3.5rem 1.5rem;
                text-align: center;
                background: var(--rpt-bg-surface);
                border-radius: 1.5rem;
                border: 1.5px solid var(--rpt-border);
            }

            /* --- Table Styles --- */
            .rpt-table-container {
                overflow-x: auto;
                border-radius: 1rem;
                border: 1px solid var(--rpt-border);
            }

            .rpt-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.825rem;
                text-align: left;
            }
            html[dir="rtl"] .rpt-table {
                text-align: right;
            }
            .rpt-table th {
                background: var(--rpt-bg-subtle);
                padding: 0.85rem 1rem;
                font-weight: 800;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: var(--rpt-text-muted);
                border-bottom: 1.5px solid var(--rpt-border);
            }
            .rpt-table td {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid var(--rpt-border-subtle);
                color: var(--rpt-text-secondary);
            }
            .rpt-table tr:hover td {
                background: var(--rpt-bg-hover);
            }

            /* --- Badges --- */
            .rpt-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 9999px;
                font-size: 0.725rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.025em;
            }
            .rpt-badge-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
            html.dark .rpt-badge-success { background: rgba(22, 101, 52, 0.35); color: #86efac; border-color: rgba(134, 239, 172, 0.3); }
            .rpt-badge-info { background: #E0F2FE; color: #075985; border: 1px solid #BAE6FD; }
            html.dark .rpt-badge-info { background: rgba(7, 89, 133, 0.35); color: #7dd3fc; border-color: rgba(125, 211, 252, 0.3); }
            .rpt-badge-warning { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
            html.dark .rpt-badge-warning { background: rgba(146, 64, 14, 0.35); color: #fcd34d; border-color: rgba(252, 211, 77, 0.3); }
            .rpt-badge-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
            html.dark .rpt-badge-danger { background: rgba(153, 27, 27, 0.35); color: #fca5a5; border-color: rgba(252, 165, 165, 0.3); }

            /* ================================================================
               EXECUTIVE PRINT STYLES — ZERO DARK MODE BLEED / CLEAN FACULTY DOSSIER
            ================================================================ */
            @media print {
                /* 1. Universal Environment Reset: 100% White Paper & Deep Contrast */
                *, *::before, *::after {
                    box-sizing: border-box !important;
                    text-shadow: none !important;
                    -webkit-filter: none !important;
                    filter: none !important;
                }

                @page {
                    size: A4 portrait;
                    margin: 12mm 14mm 16mm 14mm;
                }

                :root, html, html.dark,
                body, body.dark,
                .fi-body, .fi-panel, .fi-panel-admin, .fi-layout, .fi-main, .fi-main-ctn, .fi-page, .fi-page-content,
                .rpt-wrapper, .rpt-main-box {
                    background: #FFFFFF !important;
                    background-color: #FFFFFF !important;
                    color: #0F172A !important;
                    box-shadow: none !important;
                    border: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    font-family: 'Cairo', system-ui, -apple-system, sans-serif !important;
                    font-size: 9pt !important;
                    line-height: 1.4 !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }

                /* 2. Hide ALL Screen UI Chrome & Navigation */
                .no-print,
                header, aside, nav,
                .fi-sidebar, #fi-main-sidebar, .fi-topbar, .fi-breadcrumbs,
                .fi-header, .fi-pagination, .fi-modal, .fi-dropdown,
                .rpt-topbar,
                .rpt-tabs-header,
                .rpt-btn,
                .rpt-search-input,
                .rpt-select,
                [x-cloak] {
                    display: none !important;
                    height: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow: hidden !important;
                }

                /* 3. Executive Letterhead Header */
                .rpt-print-header {
                    display: flex !important;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 2.5pt solid #4338CA;
                    padding-bottom: 8pt;
                    margin-bottom: 12pt;
                }

                .rpt-print-brand {
                    display: flex;
                    align-items: center;
                    gap: 8pt;
                }

                .rpt-print-logo-box {
                    width: 32pt;
                    height: 32pt;
                    border-radius: 8pt;
                    background: #4338CA !important;
                    color: #FFFFFF !important;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16pt;
                    font-weight: 900;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .rpt-print-brand-title {
                    font-size: 16pt;
                    font-weight: 900;
                    color: #0F172A !important;
                    line-height: 1.1;
                    letter-spacing: -0.02em;
                }

                .rpt-print-brand-sub {
                    font-size: 7.5pt;
                    color: #64748B !important;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    margin-top: 2pt;
                }

                .rpt-print-meta {
                    text-align: right;
                    font-size: 7.5pt;
                    color: #334155 !important;
                    line-height: 1.45;
                }
                html[dir="rtl"] .rpt-print-meta {
                    text-align: left;
                }

                /* 4. Teacher Demographics & Profile Strip */
                .rpt-hero-teacher {
                    background: #F8FAFC !important;
                    background-color: #F8FAFC !important;
                    border: 1.5pt solid #CBD5E1 !important;
                    border-radius: 8pt !important;
                    padding: 10pt 12pt !important;
                    margin-bottom: 12pt !important;
                    box-shadow: none !important;
                    color: #0F172A !important;
                    display: flex !important;
                    flex-direction: row !important;
                    justify-content: space-between !important;
                    align-items: center !important;
                    page-break-inside: avoid;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .rpt-hero-teacher h2 {
                    color: #0F172A !important;
                    font-size: 13pt !important;
                    font-weight: 900 !important;
                }

                .rpt-hero-teacher span, .rpt-hero-teacher i {
                    color: #475569 !important;
                }

                .rpt-avatar {
                    width: 40pt !important;
                    height: 40pt !important;
                    min-width: 40pt !important;
                    border-radius: 8pt !important;
                    border: 1pt solid #CBD5E1 !important;
                    box-shadow: none !important;
                    background: #F1F5F9 !important;
                }

                .rpt-avatar img {
                    width: 40pt !important;
                    height: 40pt !important;
                    border-radius: 7pt !important;
                    object-fit: cover;
                }

                /* 5. Metric KPI Grid */
                .rpt-grid-4 {
                    display: grid !important;
                    grid-template-columns: repeat(4, 1fr) !important;
                    gap: 8pt !important;
                    margin-bottom: 14pt !important;
                    page-break-inside: avoid;
                }

                .rpt-stat-card, html.dark .rpt-stat-card {
                    background: #FFFFFF !important;
                    background-color: #FFFFFF !important;
                    border: 1pt solid #CBD5E1 !important;
                    border-radius: 6pt !important;
                    padding: 8pt 10pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                .rpt-stat-card p, html.dark .rpt-stat-card p {
                    color: #64748B !important;
                }

                /* 6. Tabs & Sections Unhiding */
                .rpt-main-box, html.dark .rpt-main-box {
                    border: none !important;
                    box-shadow: none !important;
                    background: transparent !important;
                    border-radius: 0 !important;
                }

                .rpt-print-section {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    margin-bottom: 14pt !important;
                    page-break-inside: auto;
                }

                .rpt-section-print-title {
                    display: flex !important;
                    align-items: center;
                    gap: 6pt;
                    font-size: 10.5pt !important;
                    font-weight: 900 !important;
                    color: #0F172A !important;
                    border-bottom: 1.5pt solid #4338CA !important;
                    border-left: none !important;
                    padding-bottom: 4pt !important;
                    margin: 14pt 0 8pt 0 !important;
                    page-break-after: avoid;
                }

                /* 7. Cards & Tables */
                .rpt-wrapper, html.dark .rpt-wrapper, .dark .rpt-wrapper {
                    --rpt-bg-surface: #FFFFFF !important;
                    --rpt-bg-subtle: #FFFFFF !important;
                    --rpt-bg-elevated: #F8FAFC !important;
                    --rpt-border: #CBD5E1 !important;
                    --rpt-text-primary: #0F172A !important;
                    --rpt-text-secondary: #334155 !important;
                    --rpt-text-muted: #64748B !important;
                }

                .rpt-course-card, .rpt-note-card, .rpt-exception-card,
                html.dark .rpt-course-card, html.dark .rpt-note-card, html.dark .rpt-exception-card {
                    background: #FFFFFF !important;
                    background-color: #FFFFFF !important;
                    border: 1pt solid #E2E8F0 !important;
                    border-radius: 6pt !important;
                    padding: 8pt 10pt !important;
                    margin-bottom: 6pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                .rpt-course-metric-box, html.dark .rpt-course-metric-box {
                    background: #F8FAFC !important;
                    border: 1pt solid #E2E8F0 !important;
                }

                .rpt-course-title, .rpt-card-title, .rpt-section-heading, .rpt-course-metric-val, .rpt-strong-text {
                    color: #0F172A !important;
                }

                .rpt-card-body {
                    color: #334155 !important;
                }

                .rpt-table-container, html.dark .rpt-table-container {
                    border: 1pt solid #CBD5E1 !important;
                    border-radius: 4pt !important;
                    overflow: visible !important;
                    margin-top: 6pt !important;
                }

                .rpt-table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                    font-size: 7.5pt !important;
                }

                .rpt-table thead, html.dark .rpt-table thead {
                    background: #F1F5F9 !important;
                    background-color: #F1F5F9 !important;
                    border-bottom: 1.5pt solid #CBD5E1 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .rpt-table th, html.dark .rpt-table th {
                    background: #F1F5F9 !important;
                    color: #1E293B !important;
                    padding: 5pt 6pt !important;
                    font-size: 7.2pt !important;
                    font-weight: 800 !important;
                    border-bottom: 1.5pt solid #CBD5E1 !important;
                    text-transform: uppercase !important;
                }

                .rpt-table td, html.dark .rpt-table td {
                    padding: 4pt 6pt !important;
                    border-bottom: 1pt solid #E2E8F0 !important;
                    color: #0F172A !important;
                    font-weight: 600 !important;
                }

                .rpt-table tbody tr {
                    page-break-inside: avoid;
                }

                .rpt-table tbody tr:nth-child(even) {
                    background: #F8FAFC !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* 8. Status Badges */
                .rpt-badge, .rpt-badge-success, .rpt-badge-info, .rpt-badge-warning, .rpt-badge-danger {
                    border: 1pt solid #CBD5E1 !important;
                    padding: 1.5pt 5pt !important;
                    border-radius: 3pt !important;
                    font-size: 6.8pt !important;
                    font-weight: 800 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .rpt-badge-success { background: #DCFCE7 !important; color: #166534 !important; border-color: #86EFAC !important; }
                .rpt-badge-info    { background: #E0F2FE !important; color: #075985 !important; border-color: #7DD3FC !important; }
                .rpt-badge-warning { background: #FEF3C7 !important; color: #92400E !important; border-color: #FCD34D !important; }
                .rpt-badge-danger  { background: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important; }

                /* 9. Sign-off Section */
                .rpt-print-signatures {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 15pt;
                    margin-top: 18pt;
                    padding-top: 14pt;
                    border-top: 1pt solid #CBD5E1;
                    page-break-inside: avoid;
                }

                .rpt-signature-box {
                    text-align: center;
                    font-size: 7.2pt;
                    color: #475569;
                }

                .rpt-signature-line {
                    margin-top: 24pt;
                    border-bottom: 1pt solid #0F172A;
                }

                /* 10. Running Print Footer */
                .rpt-print-footer {
                    display: block !important;
                    text-align: center;
                    font-size: 7pt;
                    color: #94A3B8 !important;
                    margin-top: 14pt;
                    padding-top: 6pt;
                    border-top: 0.5pt solid #E2E8F0;
                    page-break-before: avoid;
                }
            }
        </style>

        {{-- Top Control Bar: Search & Quick Picker --}}
        <div class="rpt-topbar no-print">
            <div style="flex: 1; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <div style="position: relative; flex: 1; min-width: 240px;">
                    <span class="rpt-search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input 
                        wire:model.live.debounce.300ms="searchQuery"
                        type="text" 
                        placeholder="{{ __('Search teachers by name, specialization, email, or phone...') }}"
                        class="rpt-search-input"
                    />
                </div>
                <div>
                    <select 
                        wire:change="selectTeacher($event.target.value)"
                        class="rpt-select"
                    >
                        @forelse($this->teachersList as $tch)
                            <option value="{{ $tch->id }}" {{ $this->selectedTeacherId === $tch->id ? 'selected' : '' }}>
                                {{ $tch->user?->name ?? __('Teacher') }} ({{ $tch->title ?: ($tch->specialization ?: __('Instructor')) }})
                            </option>
                        @empty
                            <option disabled>{{ __('No teachers found') }}</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button onclick="elitePrintReport()" type="button" class="rpt-btn">
                    <i class="fa-solid fa-print"></i>
                    <span>{{ __('Print Report') }}</span>
                </button>
            </div>
        </div>

        @if($this->selectedTeacher)
            @php
                $teacher = $this->selectedTeacher;
                $user = $teacher->user;
                $metrics = $this->teacherMetrics;
            @endphp

            {{-- Print-only Executive Faculty Dossier Header --}}
            <div class="rpt-print-header" style="display: none;">
                <div class="rpt-print-brand">
                    <div class="rpt-print-logo-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <div class="rpt-print-brand-title">{{ brand('app_name', 'EDU') }}</div>
                        <div class="rpt-print-brand-sub">{{ __('Official Faculty Instructor Comprehensive Dossier & Performance Record') }}</div>
                    </div>
                </div>
                <div class="rpt-print-meta">
                    <div><strong>{{ __('Faculty Member') }}:</strong> {{ $user?->name }}</div>
                    <div><strong>{{ __('Faculty ID') }}:</strong> #FAC-{{ str_pad($teacher->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div><strong>{{ __('Specialization') }}:</strong> {{ $teacher->specialization ?? $teacher->title ?? '-' }}</div>
                    <div><strong>{{ __('Report Generated') }}:</strong> {{ now()->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            {{-- Teacher Profile Banner Card --}}
            <div class="rpt-hero-teacher">
                <div style="position: relative; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div class="rpt-avatar" style="width: 5rem; height: 5rem; min-width: 5rem; min-height: 5rem; max-width: 5rem; max-height: 5rem; border-radius: 1.25rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.25); border: 2px solid rgba(255,255,255,0.35);">
                            <img src="{{ $teacher->photo_url }}" alt="{{ $user?->name }}" style="width: 5rem; height: 5rem; max-width: 5rem; max-height: 5rem; object-fit: cover; display: block; border-radius: inherit;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user?->name ?? 'Teacher') }}&background=4F46E5&color=fff&size=256&bold=true';" />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: #FFFFFF;">
                                    {{ $user?->name ?? __('Teacher Name') }}
                                </h2>
                                <span style="background: rgba(255, 255, 255, 0.2); padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(255, 255, 255, 0.3); color: #FFFFFF;">
                                    <i class="fa-solid fa-graduation-cap" style="margin-inline-end: 0.25rem;"></i>
                                    {{ $teacher->title ?: __('Faculty Instructor') }}
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; font-size: 0.8rem; color: #E0E7FF;">
                                @if($user?->email)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-envelope" style="color: #A5B4FC;"></i>
                                        {{ $user->email }}
                                    </span>
                                @endif
                                @if($user?->phone)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-phone" style="color: #A5B4FC;"></i>
                                        {{ $user->phone }}
                                    </span>
                                @endif
                                @if($teacher->specialization)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-atom" style="color: #A5B4FC;"></i>
                                        {{ $teacher->specialization }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Rating & Experience Badges --}}
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                        <div style="background: rgba(251, 191, 36, 0.25); border: 1px solid rgba(251, 191, 36, 0.4); color: #FEF08A; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-star" style="color: #FBBF24;"></i>
                            <span>{{ number_format($metrics['average_rating'], 1) }} / 5.0</span>
                            <span style="color: #FEF9C3; font-size: 0.75rem; opacity: 0.85;">({{ $metrics['reviews_count'] }} {{ __('reviews') }})</span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-business-time" style="color: #A5B4FC;"></i>
                            <span>{{ $teacher->years_experience ?: 5 }}+ {{ __('Years Teaching Experience') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric KPI Overview Cards --}}
            <div class="rpt-grid-4">
                {{-- Courses Taught --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="rpt-stat-label">{{ __('Courses Taught') }}</span>
                        <div class="rpt-stat-icon rpt-stat-icon-indigo">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-val rpt-kpi-val-indigo">
                        {{ $metrics['courses_count'] }}
                    </div>
                    <p class="rpt-kpi-sub">
                        <i class="fa-solid fa-layer-group" style="color: var(--rpt-indigo);"></i>
                        {{ __('Active published courses') }}
                    </p>
                </div>

                {{-- Enrolled Students --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="rpt-stat-label">{{ __('Enrolled Students') }}</span>
                        <div class="rpt-stat-icon rpt-stat-icon-teal">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-val rpt-kpi-val-teal">
                        {{ $metrics['total_students_enrolled'] }}
                    </div>
                    <p class="rpt-kpi-sub">
                        {{ __('Unique active learners') }}
                    </p>
                </div>

                {{-- Live Sessions --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="rpt-stat-label">{{ __('Live Sessions') }}</span>
                        <div class="rpt-stat-icon rpt-stat-icon-rose">
                            <i class="fa-solid fa-video"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span class="rpt-kpi-val rpt-kpi-val-rose">
                            {{ $metrics['completed_sessions_count'] }}
                        </span>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--rpt-text-muted);">/ {{ $metrics['live_sessions_count'] }} {{ __('Total') }}</span>
                    </div>
                    <p class="rpt-kpi-sub">
                        {{ __('Taught') }}: <strong>{{ $metrics['total_hours_taught'] }}</strong> {{ __('hours') }}
                    </p>
                </div>

                {{-- Assignments & Grading --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span class="rpt-stat-label">{{ __('Assignments & Grading') }}</span>
                        <div class="rpt-stat-icon rpt-stat-icon-purple">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span class="rpt-kpi-val rpt-kpi-val-purple">
                            {{ $metrics['graded_submissions_count'] }}
                        </span>
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--rpt-text-muted);">{{ __('Graded') }}</span>
                    </div>
                    <p class="rpt-kpi-sub">
                        {{ __('Created assignments') }}: <strong>{{ $metrics['assignments_count'] }}</strong>
                    </p>
                </div>
            </div>

            {{-- Main Tabbed Detail Box --}}
            <div class="rpt-main-box">
                {{-- Tabs Navigation --}}
                <div class="rpt-tabs-header no-print">
                    @php
                        $tabs = [
                            'overview'    => ['icon' => 'fa-book-open',         'label' => __('Assigned Courses'),            'count' => $this->courses->count()],
                            'sessions'    => ['icon' => 'fa-video',             'label' => __('Conducted & Live Sessions'),    'count' => $this->liveSessions->count()],
                            'students'    => ['icon' => 'fa-user-group',        'label' => __('Enrolled Students Roster'),    'count' => $this->enrolledStudents->count()],
                            'assignments' => ['icon' => 'fa-file-signature',    'label' => __('Assignments & Grading'),       'count' => $this->assignments->count()],
                            'notes'       => ['icon' => 'fa-clipboard-user',    'label' => __('Authored Educational Notes'),  'count' => $this->notes->count()],
                            'exceptions'  => ['icon' => 'fa-envelope-open-text','label' => __('Student Exception Requests'),   'count' => $this->exceptions->count()],
                        ];
                    @endphp

                    @foreach($tabs as $tabKey => $tab)
                        <button 
                            wire:click="setTab('{{ $tabKey }}')"
                            type="button" 
                            class="rpt-tab-btn {{ $activeTab === $tabKey ? 'active' : '' }}"
                        >
                            <i class="fa-solid {{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                            <span class="rpt-tab-count">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Tab Content Display --}}
                <div style="padding: 1.5rem;">

                    {{-- 1. Assigned Courses Tab --}}
                    <div class="rpt-print-section" id="section-overview" style="{{ $activeTab !== 'overview' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-book-bookmark"></i> {{ __('Assigned Courses & Curriculum Delivery') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-book-bookmark" style="color: var(--rpt-indigo);"></i>
                                <span>{{ __('Assigned Courses & Curriculum Delivery') }}</span>
                            </h3>

                            <div class="rpt-courses-grid">
                                @forelse($this->courses as $c)
                                    <div class="rpt-course-card">
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                                            <div>
                                                <h4 class="rpt-course-title">{{ $c->title }}</h4>
                                                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.45rem;">
                                                    <span class="rpt-badge rpt-badge-info">{{ $c->subject?->name ?? __('General') }}</span>
                                                    <span style="font-size: 0.75rem; font-weight: 600;" class="rpt-muted-text">{{ $c->gradeLevel?->name ?? __('All Grades') }}</span>
                                                </div>
                                            </div>
                                            <span class="rpt-badge {{ $c->is_active ? 'rpt-badge-success' : 'rpt-badge-danger' }}">
                                                {{ $c->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </div>

                                        <div class="rpt-course-metrics">
                                            <div class="rpt-course-metric-box">
                                                <div class="rpt-course-metric-label">{{ __('Sessions') }}</div>
                                                <div class="rpt-course-metric-val" style="color: var(--rpt-teal);">{{ $c->sessions_count ?: $c->sessions->count() }}</div>
                                            </div>
                                            <div class="rpt-course-metric-box">
                                                <div class="rpt-course-metric-label">{{ __('Students') }}</div>
                                                <div class="rpt-course-metric-val">{{ $c->enrollments->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div style="grid-column: 1 / -1; padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                        <i class="fa-solid fa-book-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                        <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No courses currently assigned to this teacher.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>{{-- end section-overview --}}

                    {{-- 2. Live Sessions Tab --}}
                    <div class="rpt-print-section" id="section-sessions" style="{{ $activeTab !== 'sessions' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-video"></i> {{ __('Conducted & Scheduled Live Sessions') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-video" style="color: var(--rpt-rose);"></i>
                                <span>{{ __('Conducted & Scheduled Live Sessions') }}</span>
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Session Title') }}</th>
                                            <th>{{ __('Course / Subject') }}</th>
                                            <th>{{ __('Target Student') }}</th>
                                            <th>{{ __('Scheduled Date & Time') }}</th>
                                            <th>{{ __('Duration & Platform') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->liveSessions as $sess)
                                            <tr>
                                                <td class="rpt-strong-text">
                                                    {{ $sess->title }}
                                                </td>
                                                <td>{{ $sess->course?->title ?? $sess->subject?->name ?? '-' }}</td>
                                                <td>
                                                    @if($sess->studentUser)
                                                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                                                            <img src="{{ $sess->studentUser->avatar_url }}" alt="{{ $sess->studentUser->name }}" style="width: 2rem; height: 2rem; min-width: 2rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid var(--rpt-teal);" />
                                                            <div>
                                                                <div class="rpt-strong-text" style="line-height: 1.2;">{{ $sess->studentUser->name }}</div>
                                                                <div style="font-size: 0.7rem;" class="rpt-muted-text">{{ $sess->studentUser->email }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="rpt-badge rpt-badge-info">{{ __('Cohort Session') }}</span>
                                                    @endif
                                                </td>
                                                <td style="font-family: monospace;">
                                                    {{ $sess->scheduled_at ? $sess->scheduled_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td>
                                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                                        <i class="fa-solid fa-clock" style="color: var(--rpt-text-muted);"></i>
                                                        {{ $sess->duration_minutes }}m ({{ __(ucfirst($sess->meeting_platform ?: 'online')) }})
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $sess->status === 'completed' ? 'rpt-badge-success' : ($sess->status === 'scheduled' ? 'rpt-badge-info' : 'rpt-badge-danger') }}">
                                                        {{ __(ucfirst($sess->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                                    {{ __('No live sessions recorded for this instructor.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-sessions --}}

                    {{-- 3. Enrolled Students Roster Tab --}}
                    <div class="rpt-print-section" id="section-students" style="{{ $activeTab !== 'students' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-user-group"></i> {{ __('Enrolled Students Roster') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-user-group" style="color: var(--rpt-teal);"></i>
                                <span>{{ __('Enrolled Students Roster') }}</span>
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Student') }}</th>
                                            <th>{{ __('Contact Info') }}</th>
                                            <th>{{ __('Grade Level') }}</th>
                                            <th>{{ __('Enrolled Course') }}</th>
                                            <th>{{ __('Enrollment Date') }}</th>
                                            <th>{{ __('Progress') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->enrolledStudents as $enr)
                                            <tr>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <img src="{{ $enr->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0D9488&color=fff' }}" alt="{{ $enr->studentUser?->name }}" style="width: 2.35rem; height: 2.35rem; min-width: 2.35rem; border-radius: 9999px; object-fit: cover; border: 2px solid var(--rpt-teal); flex-shrink: 0;" />
                                                        <div>
                                                            <div class="rpt-strong-text" style="line-height: 1.2;">
                                                                {{ $enr->studentUser?->name ?? __('Student') }}
                                                            </div>
                                                            <div style="font-size: 0.7rem;" class="rpt-muted-text">
                                                                {{ $enr->studentUser?->studentProfile?->school_name ?: brand('app_name', 'EDU') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="font-size: 0.75rem;">
                                                    <div>{{ $enr->studentUser?->email }}</div>
                                                    <div style="font-family: monospace;" class="rpt-muted-text">{{ $enr->studentUser?->phone }}</div>
                                                </td>
                                                <td>
                                                    {{ $enr->studentUser?->studentProfile?->gradeLevel?->name ?? '-' }}
                                                </td>
                                                <td style="font-weight: 700; color: var(--rpt-indigo);">
                                                    {{ $enr->course?->title ?? '-' }}
                                                </td>
                                                <td style="font-family: monospace;">
                                                    {{ $enr->enrolled_at ? $enr->enrolled_at->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>
                                                    <span style="font-weight: 800; color: var(--rpt-teal);">{{ $enr->progress_percent }}%</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                                    {{ __('No students currently enrolled in this teacher\'s courses.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-students --}}

                    {{-- 4. Assignments & Grading Tab --}}
                    <div class="rpt-print-section" id="section-assignments" style="{{ $activeTab !== 'assignments' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-file-pen"></i> {{ __('Assignments Created & Grading Log') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-file-pen" style="color: var(--rpt-purple);"></i>
                                <span>{{ __('Assignments Created & Grading Log') }}</span>
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Assignment Title') }}</th>
                                            <th>{{ __('Course') }}</th>
                                            <th>{{ __('Passing Grade') }}</th>
                                            <th>{{ __('Submissions Received') }}</th>
                                            <th>{{ __('Graded Submissions') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->assignments as $a)
                                            @php
                                                $gradedCount = $a->submissions->whereNotNull('grade')->count();
                                                $totalSubs = $a->submissions->count();
                                            @endphp
                                            <tr>
                                                <td class="rpt-strong-text">
                                                    {{ $a->title }}
                                                </td>
                                                <td>{{ $a->course?->title ?? '-' }}</td>
                                                <td style="font-family: monospace; font-weight: 800;">
                                                    {{ $a->passing_grade ?: 60 }}%
                                                </td>
                                                <td style="font-weight: 700; color: var(--rpt-purple);">
                                                    {{ $totalSubs }} {{ __('Submissions') }}
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $gradedCount === $totalSubs && $totalSubs > 0 ? 'rpt-badge-success' : 'rpt-badge-warning' }}">
                                                        {{ $gradedCount }} / {{ $totalSubs }} {{ __('Graded') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $a->status === 'published' ? 'rpt-badge-success' : 'rpt-badge-info' }}">
                                                        {{ __(ucfirst($a->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                                    {{ __('No assignments created by this teacher.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-assignments --}}

                    {{-- 5. Educational Notes Authored Tab --}}
                    <div class="rpt-print-section" id="section-notes" style="{{ $activeTab !== 'notes' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-clipboard-user"></i> {{ __('Educational Notes Authored for Students') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-clipboard-user" style="color: var(--rpt-sky);"></i>
                                <span>{{ __('Educational Notes Authored for Students') }}</span>
                            </h3>

                            @forelse($this->notes as $note)
                                <div class="rpt-note-card" style="display: flex; flex-direction: column; gap: 0.65rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <img src="{{ $note->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0284C7&color=fff' }}" alt="{{ $note->studentUser?->name }}" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid var(--rpt-sky); flex-shrink: 0;" />
                                            <div>
                                                <h4 class="rpt-card-title">
                                                    {{ __('For Student') }}: {{ $note->studentUser?->name ?? __('Student') }}
                                                </h4>
                                                <div style="font-size: 0.7rem;" class="rpt-muted-text">{{ $note->studentUser?->email }}</div>
                                            </div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-family: monospace;" class="rpt-muted-text">{{ $note->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p class="rpt-card-body" style="padding-inline-start: 3rem;">
                                        {{ $note->note }}
                                    </p>
                                </div>
                            @empty
                                <div style="padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No educational notes authored by this teacher.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>{{-- end section-notes --}}

                    {{-- 6. Exception Requests Handled Tab --}}
                    <div class="rpt-print-section" id="section-exceptions" style="{{ $activeTab !== 'exceptions' ? 'display:none;' : '' }}">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-envelope-open-text"></i> {{ __('Student Absence & Exception Requests Handled') }}</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 class="rpt-section-heading no-print">
                                <i class="fa-solid fa-envelope-open-text" style="color: var(--rpt-purple);"></i>
                                <span>{{ __('Student Absence & Exception Requests Handled') }}</span>
                            </h3>

                            @forelse($this->exceptions as $exc)
                                <div class="rpt-exception-card" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="{{ $exc->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=9333EA&color=fff' }}" alt="{{ $exc->studentUser?->name }}" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid var(--rpt-purple); flex-shrink: 0;" />
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <h4 class="rpt-card-title">
                                                    {{ $exc->studentUser?->name ?? __('Student') }}
                                                </h4>
                                                <span class="rpt-badge rpt-badge-info">{{ $exc->liveSession?->title ?? $exc->course?->title ?? __('Session Request') }}</span>
                                                <span class="rpt-badge {{ $exc->status === 'approved' ? 'rpt-badge-success' : ($exc->status === 'rejected' ? 'rpt-badge-danger' : 'rpt-badge-warning') }}">
                                                    {{ __(ucfirst($exc->status)) }}
                                                </span>
                                            </div>
                                            <p class="rpt-card-body" style="font-size: 0.775rem; color: var(--rpt-text-muted);">
                                                {{ __('Reason') }}: {{ $exc->reason ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span style="font-size: 0.75rem; font-family: monospace;" class="rpt-muted-text">{{ $exc->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @empty
                                <div style="padding: 2.5rem; text-align: center;" class="rpt-muted-text">
                                    <i class="fa-solid fa-envelope-circle-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No exception requests for this teacher\'s classes.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>{{-- end section-exceptions --}}

                    {{-- Formal Institutional Sign-off Section (Print Only) --}}
                    <div class="rpt-print-signatures" style="display: none;">
                        <div class="rpt-signature-box">
                            <div>{{ __('Faculty Department Head') }}</div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;">{{ __('Department Chair Signature') }}</div>
                        </div>
                        <div class="rpt-signature-box">
                            <div>{{ __('Dean of Faculty & Academic Affairs') }}</div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;">{{ __('Academic Affairs Signature') }}</div>
                        </div>
                        <div class="rpt-signature-box">
                            <div>{{ __('Official Institution Seal & Approval') }}</div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;">{{ brand('app_name', 'EDU') }} {{ __('Administration') }}</div>
                        </div>
                    </div>

                    {{-- Running Print Footer --}}
                    <div class="rpt-print-footer" style="display: none;">
                        {{ brand('app_name', 'EDU') }} &mdash; {{ __('Official Confidential Faculty Performance Dossier') }} &mdash; {{ __('Issue Date') }}: {{ now()->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
        @else
            <div class="rpt-empty-card">
                <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; color: var(--rpt-text-muted); margin-bottom: 0.75rem; display: block;"></i>
                <h3 class="rpt-card-title" style="font-size: 1.05rem;">{{ __('No teacher selected') }}</h3>
                <p class="rpt-muted-text" style="font-size: 0.825rem; margin: 0.35rem 0 0 0;">{{ __('Please pick a teacher from the dropdown above to view their comprehensive faculty report.') }}</p>
            </div>
        @endif
    </div>

    {{-- Professional Print JavaScript --}}
    <script>
    function elitePrintReport() {
        const sections = document.querySelectorAll('.rpt-print-section');
        const origDisplays = [];
        sections.forEach(function(sec) {
            origDisplays.push(sec.style.display);
            sec.style.display = 'block';
        });

        const printHeaders = document.querySelectorAll('.rpt-print-header, .rpt-print-footer, .rpt-section-print-title, .rpt-print-signatures');
        printHeaders.forEach(function(el) {
            el.style.display = el.classList.contains('rpt-print-header') ? 'flex' : (el.classList.contains('rpt-print-signatures') ? 'grid' : 'block');
        });

        window.print();

        setTimeout(function() {
            sections.forEach(function(sec, i) {
                sec.style.display = origDisplays[i];
            });
            printHeaders.forEach(function(el) {
                el.style.display = 'none';
            });
        }, 1200);
    }
    </script>
</x-filament-panels::page>
