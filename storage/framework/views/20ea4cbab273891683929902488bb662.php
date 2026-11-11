<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="rpt-wrapper">
        
        <style>
            .rpt-wrapper {
                font-family: 'Cairo', sans-serif !important;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                animation: rptFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes rptFadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* --- Control Topbar --- */
            .rpt-topbar {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
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
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .rpt-search-input {
                width: 100%;
                background: #F8FAFC;
                border: 1.5px solid #CBD5E1;
                border-radius: 0.875rem;
                padding: 0.65rem 1rem 0.65rem 2.5rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: #0F172A;
                outline: none;
                transition: all 0.2s ease;
            }
            html[dir="rtl"] .rpt-search-input {
                padding: 0.65rem 2.5rem 0.65rem 1rem;
            }
            html.dark .rpt-search-input {
                background: #1E293B;
                border-color: #334155;
                color: #F8FAFC;
            }
            .rpt-search-input:focus {
                border-color: #0D9488;
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18);
            }

            .rpt-select {
                background: #F8FAFC;
                border: 1.5px solid #CBD5E1;
                border-radius: 0.875rem;
                padding: 0.65rem 1rem;
                font-size: 0.85rem;
                font-weight: 700;
                color: #0F172A;
                cursor: pointer;
                outline: none;
                transition: all 0.2s ease;
                min-width: 260px;
            }
            html.dark .rpt-select {
                background: #1E293B;
                border-color: #334155;
                color: #F8FAFC;
            }
            .rpt-select:focus {
                border-color: #0D9488;
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18);
            }

            .rpt-btn {
                background: #F1F5F9;
                color: #334155;
                border: 1px solid #CBD5E1;
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
            html.dark .rpt-btn {
                background: #1E293B;
                color: #E2E8F0;
                border-color: #334155;
            }
            .rpt-btn:hover {
                transform: translateY(-1px);
                background: #E2E8F0;
            }
            html.dark .rpt-btn:hover {
                background: #334155;
            }

            /* --- Banner Hero Card --- */
            .rpt-hero-banner {
                background: linear-gradient(135deg, #0D9488 0%, #0F766E 50%, #0F172A 100%);
                color: #FFFFFF;
                border-radius: 1.75rem;
                padding: 1.75rem 2rem;
                position: relative;
                overflow: hidden;
                box-shadow: 0 16px 36px -8px rgba(13, 148, 136, 0.35);
                border: 1px solid rgba(255, 255, 255, 0.15);
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1.5rem;
            }
            .rpt-avatar {
                width: 4.5rem;
                height: 4.5rem;
                border-radius: 1.25rem;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1.5px solid rgba(255, 255, 255, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                color: #99F6E4;
                box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.2);
                flex-shrink: 0;
            }
            .rpt-hero-title {
                font-size: 1.65rem;
                font-weight: 900;
                color: #FFFFFF;
                line-height: 1.2;
            }
            .rpt-hero-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 1rem;
                margin-top: 0.5rem;
                font-size: 0.8rem;
                color: rgba(204, 251, 241, 0.9);
            }
            .rpt-hero-badge {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(6px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 0.4rem 0.85rem;
                border-radius: 0.75rem;
                font-size: 0.775rem;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                color: #FFFFFF;
            }

            /* --- Metric KPI Grid --- */
            .rpt-grid-4 {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                gap: 1.25rem;
            }
            .rpt-kpi-card {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.35rem;
                padding: 1.25rem 1.35rem;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
                transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            }
            html.dark .rpt-kpi-card {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            .rpt-kpi-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 12px 28px -6px rgba(13, 148, 136, 0.18);
                border-color: #0D9488;
            }
            .rpt-kpi-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }
            .rpt-kpi-label {
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #64748B;
            }
            html.dark .rpt-kpi-label {
                color: #94A3B8;
            }
            .rpt-kpi-icon {
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
            }
            .rpt-kpi-value {
                font-size: 1.85rem;
                font-weight: 900;
                color: #0F172A;
                line-height: 1.1;
            }
            html.dark .rpt-kpi-value {
                color: #F8FAFC;
            }
            .rpt-kpi-desc {
                font-size: 0.75rem;
                font-weight: 600;
                color: #64748B;
                margin-top: 0.35rem;
            }
            html.dark .rpt-kpi-desc {
                color: #94A3B8;
            }

            /* --- Tabbed Content Section --- */
            .rpt-tabs-container {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.5rem;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.03);
                overflow: hidden;
            }
            html.dark .rpt-tabs-container {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 6px 24px rgba(0, 0, 0, 0.3);
            }
            .rpt-tabs-nav {
                border-bottom: 1.5px solid #E2E8F0;
                padding: 0.75rem 1.25rem 0 1.25rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                overflow-x: auto;
            }
            html.dark .rpt-tabs-nav {
                border-bottom-color: rgba(51, 65, 85, 0.8);
            }
            .rpt-tab-btn {
                padding: 0.75rem 1rem;
                font-size: 0.85rem;
                font-weight: 800;
                color: #64748B;
                border-bottom: 3px solid transparent;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                white-space: nowrap;
                transition: all 0.2s ease;
                background: none;
                border-top: none;
                border-left: none;
                border-right: none;
            }
            html.dark .rpt-tab-btn {
                color: #94A3B8;
            }
            .rpt-tab-btn:hover {
                color: #0D9488;
            }
            .rpt-tab-btn.active {
                color: #0D9488;
                border-bottom-color: #0D9488;
            }
            html.dark .rpt-tab-btn.active {
                color: #2DD4BF;
                border-bottom-color: #2DD4BF;
            }
            .rpt-tab-badge {
                padding: 0.2rem 0.5rem;
                border-radius: 0.5rem;
                font-size: 0.7rem;
                font-weight: 800;
                background: #F1F5F9;
                color: #475569;
            }
            .rpt-tab-btn.active .rpt-tab-badge {
                background: rgba(13, 148, 136, 0.15);
                color: #0D9488;
            }
            html.dark .rpt-tab-btn.active .rpt-tab-badge {
                background: rgba(45, 212, 191, 0.2);
                color: #2DD4BF;
            }

            .rpt-tab-body {
                padding: 1.5rem;
            }

            /* --- Table Styles --- */
            .rpt-table-responsive {
                width: 100%;
                overflow-x: auto;
                border-radius: 1rem;
                border: 1px solid #E2E8F0;
            }
            html.dark .rpt-table-responsive {
                border-color: rgba(51, 65, 85, 0.8);
            }
            .rpt-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                font-size: 0.825rem;
            }
            html[dir="rtl"] .rpt-table {
                text-align: right;
            }
            .rpt-table thead {
                background: #F8FAFC;
                border-bottom: 1.5px solid #E2E8F0;
            }
            html.dark .rpt-table thead {
                background: #1E293B;
                border-bottom-color: rgba(51, 65, 85, 0.8);
            }
            .rpt-table th {
                padding: 0.85rem 1rem;
                font-size: 0.725rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #475569;
            }
            html.dark .rpt-table th {
                color: #94A3B8;
            }
            .rpt-table td {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid #F1F5F9;
                color: #1E293B;
                font-weight: 600;
            }
            html.dark .rpt-table td {
                border-bottom-color: rgba(51, 65, 85, 0.5);
                color: #E2E8F0;
            }
            .rpt-table tbody tr:hover {
                background: rgba(241, 245, 249, 0.6);
            }
            html.dark .rpt-table tbody tr:hover {
                background: rgba(30, 41, 59, 0.5);
            }

            /* --- Status Badges --- */
            .rpt-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 0.625rem;
                font-size: 0.725rem;
                font-weight: 800;
                text-transform: uppercase;
            }
            .rpt-pill-success {
                background: #DCFCE7;
                color: #15803D;
                border: 1px solid #86EFAC;
            }
            html.dark .rpt-pill-success {
                background: rgba(22, 101, 52, 0.4);
                color: #86EFAC;
                border-color: rgba(134, 239, 172, 0.3);
            }
            .rpt-pill-danger {
                background: #FFE4E6;
                color: #BE123C;
                border: 1px solid #FDA4AF;
            }
            html.dark .rpt-pill-danger {
                background: rgba(159, 18, 57, 0.4);
                color: #FDA4AF;
                border-color: rgba(253, 164, 175, 0.3);
            }
            .rpt-pill-warning {
                background: #FEF3C7;
                color: #B45309;
                border: 1px solid #FDE68A;
            }
            html.dark .rpt-pill-warning {
                background: rgba(180, 83, 9, 0.35);
                color: #FDE68A;
                border-color: rgba(253, 230, 138, 0.3);
            }
            .rpt-pill-info {
                background: #CCFBF1;
                color: #0F766E;
                border: 1px solid #99F6E4;
            }
            html.dark .rpt-pill-info {
                background: rgba(15, 118, 110, 0.4);
                color: #99F6E4;
                border-color: rgba(153, 246, 228, 0.3);
            }

            .rpt-pill-neutral {
                background: #F1F5F9;
                color: #334155;
                border: 1px solid #CBD5E1;
            }
            html.dark .rpt-pill-neutral {
                background: #1E293B;
                color: #CBD5E1;
                border: 1px solid #334155;
            }

            /* --- Progress Bar --- */
            .rpt-progress-bg {
                background: #E2E8F0;
                border-radius: 9999px;
                height: 0.5rem;
                overflow: hidden;
                width: 100%;
            }
            html.dark .rpt-progress-bg {
                background: #334155;
            }
            .rpt-progress-fill {
                background: #0D9488;
                height: 100%;
                border-radius: 9999px;
                transition: width 0.5s ease;
            }

            /* --- Card Item --- */
            .rpt-item-card {
                background: #F8FAFC;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.15rem;
                padding: 1.15rem 1.35rem;
                margin-bottom: 0.85rem;
                transition: all 0.2s ease;
            }
            html.dark .rpt-item-card {
                background: #162033;
                border-color: rgba(51, 65, 85, 0.85);
            }
            .rpt-item-card:hover {
                border-color: #0D9488;
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
            }
            .rpt-item-card h4,
            .rpt-item-card strong {
                color: #0F172A;
            }
            html.dark .rpt-item-card h4,
            html.dark .rpt-item-card strong {
                color: #F8FAFC;
            }
            .rpt-item-meta {
                color: #64748B;
            }
            html.dark .rpt-item-meta {
                color: #94A3B8;
            }
            .rpt-note-body {
                font-size: 0.8rem;
                color: #475569;
                margin: 0;
                padding-inline-start: 2.25rem;
                line-height: 1.6;
            }
            html.dark .rpt-note-body {
                color: #CBD5E1;
            }
            .rpt-empty-card {
                text-align: center;
                padding: 4rem 2rem;
                background: #FFFFFF;
                border-radius: 1.5rem;
                border: 1.5px solid #E2E8F0;
            }
            html.dark .rpt-empty-card {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
            }
            .rpt-empty-card h3 {
                font-size: 1.15rem;
                font-weight: 800;
                color: #0F172A;
                margin: 0 0 0.35rem 0;
            }
            html.dark .rpt-empty-card h3 {
                color: #F8FAFC;
            }
            .rpt-empty-card p {
                font-size: 0.85rem;
                color: #64748B;
                margin: 0;
            }
            html.dark .rpt-empty-card p {
                color: #94A3B8;
            }

            /* ================================================================
               EXECUTIVE PRINT STYLES — ZERO DARK MODE BLEED / CLEAN PAPER TRANSCRIPT
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
                .rpt-wrapper, .rpt-tabs-container, .rpt-tab-body {
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
                .rpt-tabs-nav,
                .rpt-btn,
                .rpt-search-input,
                .rpt-select,
                .rpt-section-screen-title,
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
                    border-bottom: 2.5pt solid #0D9488;
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
                    background: #0D9488 !important;
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

                /* 4. Student Demographics & Profile Strip */
                .rpt-hero-banner {
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

                .rpt-hero-title {
                    font-size: 13pt !important;
                    font-weight: 900 !important;
                    color: #0F172A !important;
                    margin: 0 !important;
                }

                .rpt-hero-meta {
                    display: flex !important;
                    flex-wrap: wrap !important;
                    gap: 8pt !important;
                    margin-top: 4pt !important;
                    font-size: 7.5pt !important;
                    color: #475569 !important;
                }

                .rpt-hero-meta span, .rpt-hero-meta i {
                    color: #475569 !important;
                }

                .rpt-hero-badge {
                    background: #FFFFFF !important;
                    border: 1pt solid #CBD5E1 !important;
                    color: #0F172A !important;
                    font-size: 7.5pt !important;
                    padding: 2.5pt 6pt !important;
                    border-radius: 4pt !important;
                    font-weight: 700 !important;
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

                .rpt-kpi-card, html.dark .rpt-kpi-card {
                    background: #FFFFFF !important;
                    background-color: #FFFFFF !important;
                    border: 1pt solid #CBD5E1 !important;
                    border-radius: 6pt !important;
                    padding: 8pt 10pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                .rpt-kpi-label, html.dark .rpt-kpi-label {
                    font-size: 7pt !important;
                    font-weight: 800 !important;
                    color: #64748B !important;
                    text-transform: uppercase !important;
                }

                .rpt-kpi-value, html.dark .rpt-kpi-value {
                    font-size: 14pt !important;
                    font-weight: 900 !important;
                    color: #0F172A !important;
                    margin: 2pt 0 !important;
                }

                .rpt-kpi-desc, html.dark .rpt-kpi-desc {
                    font-size: 7pt !important;
                    color: #64748B !important;
                }

                .rpt-kpi-icon {
                    display: none !important;
                }

                /* 6. Tabs & Sections Unhiding */
                .rpt-tabs-container, html.dark .rpt-tabs-container {
                    border: none !important;
                    box-shadow: none !important;
                    background: transparent !important;
                    border-radius: 0 !important;
                }

                .rpt-tab-body {
                    padding: 0 !important;
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
                    border-bottom: 1.5pt solid #0D9488 !important;
                    border-left: none !important;
                    padding-bottom: 4pt !important;
                    margin: 14pt 0 8pt 0 !important;
                    page-break-after: avoid;
                }

                /* 7. Item Cards & Tables */
                .rpt-item-card, html.dark .rpt-item-card {
                    background: #FFFFFF !important;
                    background-color: #FFFFFF !important;
                    border: 1pt solid #E2E8F0 !important;
                    border-radius: 6pt !important;
                    padding: 8pt 10pt !important;
                    margin-bottom: 6pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                .rpt-item-card h4 {
                    color: #0F172A !important;
                    font-size: 9pt !important;
                    font-weight: 800 !important;
                }

                .rpt-table-responsive, html.dark .rpt-table-responsive {
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

                /* 8. Status Pills & Progress Bars */
                .rpt-pill, .rpt-badge {
                    padding: 1.5pt 5pt !important;
                    border-radius: 3pt !important;
                    font-size: 6.8pt !important;
                    font-weight: 800 !important;
                    border: 1pt solid #CBD5E1 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .rpt-pill-success { background: #DCFCE7 !important; color: #166534 !important; border-color: #86EFAC !important; }
                .rpt-pill-warning { background: #FEF3C7 !important; color: #92400E !important; border-color: #FCD34D !important; }
                .rpt-pill-danger  { background: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important; }
                .rpt-pill-info    { background: #E0F2FE !important; color: #075985 !important; border-color: #7DD3FC !important; }

                .rpt-progress-bg, html.dark .rpt-progress-bg {
                    background: #E2E8F0 !important;
                    height: 4pt !important;
                    border-radius: 9999px !important;
                    overflow: hidden !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .rpt-progress-fill {
                    background: #0D9488 !important;
                    height: 100% !important;
                    border-radius: 9999px !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* 9. Formal Institutional Sign-off Section */
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

                .rpt-print-page-break {
                    display: block !important;
                    page-break-before: always !important;
                    height: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            }
        </style>

        
        <div class="rpt-topbar no-print">
            <div style="flex: 1; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; min-width: 300px;">
                <div style="position: relative; flex: 1; min-width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; top: 50%; transform: translateY(-50%); inset-inline-start: 0.9rem; color: #94A3B8; font-size: 0.85rem; pointer-events: none;"></i>
                    <input 
                        wire:model.live.debounce.300ms="searchQuery"
                        type="text" 
                        placeholder="<?php echo e(__('Search students by name, email, or phone...')); ?>"
                        class="rpt-search-input"
                    />
                </div>
                <div>
                    <select 
                        wire:change="selectStudent($event.target.value)"
                        class="rpt-select"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->studentsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($stu->id); ?>" <?php echo e($this->selectedStudentId === $stu->id ? 'selected' : ''); ?>>
                                <?php echo e($stu->name); ?> (<?php echo e($stu->studentProfile?->gradeLevel?->name ?? __('General Student')); ?>)
                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <option disabled><?php echo e(__('No students found')); ?></option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <button onclick="elitePrintReport()" type="button" class="rpt-btn">
                    <i class="fa-solid fa-print"></i>
                    <span><?php echo e(__('Print Report')); ?></span>
                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedStudent): ?>
            <?php
                $student = $this->selectedStudent;
                $metrics = $this->studentMetrics;
            ?>

            
            <div class="rpt-print-header" style="display: none;">
                <div class="rpt-print-brand">
                    <div class="rpt-print-logo-box">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="rpt-print-brand-title"><?php echo e(brand('app_name', 'EDU')); ?></div>
                        <div class="rpt-print-brand-sub"><?php echo e(__('Official Academic Transcript & Student Progress Dossier')); ?></div>
                    </div>
                </div>
                <div class="rpt-print-meta">
                    <div><strong><?php echo e(__('Student Name')); ?>:</strong> <?php echo e($student->name); ?></div>
                    <div><strong><?php echo e(__('Reference ID')); ?>:</strong> #STU-<?php echo e(str_pad($student->id, 5, '0', STR_PAD_LEFT)); ?></div>
                    <div><strong><?php echo e(__('Grade Level')); ?>:</strong> <?php echo e($student->studentProfile?->gradeLevel?->name ?? '-'); ?></div>
                    <div><strong><?php echo e(__('Issue Date')); ?>:</strong> <?php echo e(now()->format('Y-m-d H:i')); ?></div>
                </div>
            </div>

            
            <div class="rpt-hero-banner">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div class="rpt-avatar" style="width: 4.5rem; height: 4.5rem; min-width: 4.5rem; min-height: 4.5rem; max-width: 4.5rem; max-height: 4.5rem; border-radius: 1.25rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.25); border: 2px solid rgba(255,255,255,0.35);">
                        <img src="<?php echo e($student->avatar_url); ?>" alt="<?php echo e($student->name); ?>" style="width: 4.5rem; height: 4.5rem; max-width: 4.5rem; max-height: 4.5rem; object-fit: cover; display: block; border-radius: inherit;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($student->name)); ?>&background=0D9488&color=fff&size=256&bold=true';" />
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                            <h2 class="rpt-hero-title"><?php echo e($student->name); ?></h2>
                            <span class="rpt-pill <?php echo e($student->status?->value === 'approved' || $student->status === 'approved' ? 'rpt-pill-success' : 'rpt-pill-warning'); ?>">
                                <i class="fa-solid <?php echo e($student->status?->value === 'approved' || $student->status === 'approved' ? 'fa-circle-check' : 'fa-clock'); ?>"></i>
                                <?php echo e(__(ucfirst($student->status?->value ?? (string) $student->status))); ?>

                            </span>
                        </div>
                        <div class="rpt-hero-meta">
                            <span><i class="fa-solid fa-envelope" style="color: #5EEAD4; margin-right: 0.35rem;"></i> <?php echo e($student->email); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->phone): ?>
                                <span><i class="fa-solid fa-phone" style="color: #5EEAD4; margin-right: 0.35rem;"></i> <?php echo e($student->phone); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><i class="fa-solid fa-school" style="color: #5EEAD4; margin-right: 0.35rem;"></i> <?php echo e($student->studentProfile?->school_name ?: (brand('app_name', 'EDU') . ' ' . __('School'))); ?></span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                    <div class="rpt-hero-badge">
                        <i class="fa-solid fa-layer-group" style="color: #5EEAD4;"></i>
                        <span><?php echo e($student->studentProfile?->gradeLevel?->name ?? __('Academic Stage')); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->parents->isNotEmpty()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $student->parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="rpt-hero-badge" style="background: rgba(15, 23, 42, 0.45);">
                                <i class="fa-solid fa-users" style="color: #5EEAD4;"></i>
                                <span><?php echo e(__('Parent')); ?>: <strong><?php echo e($p->name); ?></strong> (<?php echo e($p->phone ?: $p->email); ?>)</span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="rpt-grid-4">
                
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-header">
                        <span class="rpt-kpi-label"><?php echo e(__('Enrolled Courses')); ?></span>
                        <div class="rpt-kpi-icon" style="background: rgba(13, 148, 136, 0.15); color: #0D9488;">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-value"><?php echo e($metrics['enrolled_courses_count']); ?></div>
                    <div class="rpt-kpi-desc">
                        <i class="fa-solid fa-check-double" style="color: #0D9488;"></i> <?php echo e(__('Active study curriculum')); ?>

                    </div>
                </div>

                
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-header">
                        <span class="rpt-kpi-label"><?php echo e(__('Package Balance')); ?></span>
                        <div class="rpt-kpi-icon" style="background: rgba(99, 102, 241, 0.15); color: #6366F1;">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-value" style="color: #6366F1;">
                        <?php echo e($metrics['remaining_sessions_balance']); ?> <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">/ <?php echo e($metrics['total_sessions_balance']); ?> <?php echo e(__('Sessions')); ?></span>
                    </div>
                    <div class="rpt-kpi-desc">
                        <?php echo e(__('Used')); ?>: <strong><?php echo e($metrics['used_sessions_balance']); ?></strong> <?php echo e(__('sessions')); ?>

                    </div>
                </div>

                
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-header">
                        <span class="rpt-kpi-label"><?php echo e(__('Attendance Rate')); ?></span>
                        <div class="rpt-kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-value" style="color: #10B981;">
                        <?php echo e($metrics['attendance_rate']); ?>% <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">(<?php echo e($metrics['attended_sessions']); ?>/<?php echo e($metrics['total_live_sessions']); ?>)</span>
                    </div>
                    <div class="rpt-progress-bg" style="margin-top: 0.5rem;">
                        <div class="rpt-progress-fill" style="background: #10B981; width: <?php echo e($metrics['attendance_rate']); ?>%;"></div>
                    </div>
                </div>

                
                <div class="rpt-kpi-card">
                    <div class="rpt-kpi-header">
                        <span class="rpt-kpi-label"><?php echo e(__('Avg Grade / Score')); ?></span>
                        <div class="rpt-kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #F59E0B;">
                            <i class="fa-solid fa-award"></i>
                        </div>
                    </div>
                    <div class="rpt-kpi-value" style="color: #F59E0B;">
                        <?php echo e($metrics['average_grade']); ?>% <span style="font-size: 0.85rem; font-weight: 600; color: #94A3B8;">(<?php echo e($metrics['graded_submissions']); ?>/<?php echo e($metrics['total_submissions']); ?>)</span>
                    </div>
                    <div class="rpt-kpi-desc">
                        <?php echo e(__('Completed assignments')); ?>: <strong><?php echo e($metrics['total_submissions']); ?></strong>
                    </div>
                </div>
            </div>

            
            <div class="rpt-tabs-container">
                
                <div class="rpt-tabs-nav no-print">
                    <?php
                        $tabs = [
                            'overview'    => ['icon' => 'fa-chart-pie',          'label' => __('Enrolled Courses & Progress'), 'count' => $this->enrollments->count()],
                            'packages'    => ['icon' => 'fa-cubes',              'label' => __('Packages & Transactions'),     'count' => $this->packages->count()],
                            'sessions'    => ['icon' => 'fa-video',              'label' => __('Live Sessions & Attendance'),  'count' => $this->liveSessions->count()],
                            'assignments' => ['icon' => 'fa-file-signature',     'label' => __('Assignments & Exams'),        'count' => $this->submissions->count()],
                            'notes'       => ['icon' => 'fa-clipboard-user',     'label' => __('Educational Notes'),           'count' => $this->notes->count()],
                            'exceptions'  => ['icon' => 'fa-envelope-open-text', 'label' => __('Exception Requests'),          'count' => $this->exceptions->count()],
                        ];
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button 
                            wire:click="setTab('<?php echo e($tabKey); ?>')"
                            type="button" 
                            class="rpt-tab-btn <?php echo e($activeTab === $tabKey ? 'active' : ''); ?>"
                        >
                            <i class="fa-solid <?php echo e($tab['icon']); ?>"></i>
                            <span><?php echo e($tab['label']); ?></span>
                            <span class="rpt-tab-badge"><?php echo e($tab['count']); ?></span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>



                
                <div class="rpt-tab-body">
                    
                    <div class="rpt-print-section" id="section-overview" style="<?php echo e($activeTab !== 'overview' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-graduation-cap"></i> <?php echo e(__('Enrolled Courses & Academic Progress')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #0D9488; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span><?php echo e(__('Enrolled Courses & Academic Progress')); ?></span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $course = $enr->course;
                                    $completedSessions = $enr->sessionProgress->where('status', \App\Enums\SessionProgressStatus::COMPLETED)->count();
                                    $totalCourseSessions = $course?->sessions_count ?: 8;
                                    $progressPercent = $enr->progress_percent ?: ($totalCourseSessions > 0 ? round(($completedSessions / $totalCourseSessions) * 100) : 0);
                                ?>
                                <div class="rpt-item-card" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                                    <div style="flex: 1; min-width: 250px;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0;"><?php echo e($course?->title ?: __('Course Name')); ?></h4>
                                            <span class="rpt-pill rpt-pill-info"><?php echo e($course?->subject?->name ?? __('Subject')); ?></span>
                                        </div>
                                        <div style="font-size: 0.775rem; color: #64748B; display: flex; flex-wrap: wrap; gap: 1rem;">
                                            <span><i class="fa-solid fa-chalkboard-user" style="color: #0D9488;"></i> <?php echo e(__('Teacher')); ?>: <strong><?php echo e($course?->teacher?->user?->name ?? __('Faculty Teacher')); ?></strong></span>
                                            <span><i class="fa-solid fa-calendar-check" style="color: #0D9488;"></i> <?php echo e(__('Enrolled')); ?>: <?php echo e($enr->enrolled_at ? $enr->enrolled_at->format('Y-m-d') : __('Active')); ?></span>
                                            <span><i class="fa-solid fa-circle-play" style="color: #0D9488;"></i> <?php echo e($completedSessions); ?> / <?php echo e($totalCourseSessions); ?> <?php echo e(__('Sessions Completed')); ?></span>
                                        </div>
                                    </div>

                                    <div style="width: 200px;">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.775rem; font-weight: 800; margin-bottom: 0.25rem;">
                                            <span><?php echo e(__('Progress')); ?></span>
                                            <span style="color: #0D9488;"><?php echo e($progressPercent); ?>%</span>
                                        </div>
                                        <div class="rpt-progress-bg">
                                            <div class="rpt-progress-fill" style="width: <?php echo e($progressPercent); ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                    <p style="font-weight: 700;"><?php echo e(__('No course enrollments found for this student.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="rpt-print-section" id="section-packages" style="<?php echo e($activeTab !== 'packages' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-credit-card"></i> <?php echo e(__('Purchased Packages & Session Ledger')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #6366F1; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-credit-card"></i>
                                <span><?php echo e(__('Purchased Packages & Session Ledger')); ?></span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rpt-item-card">
                                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.75rem;">
                                        <div>
                                            <h4 style="font-size: 1rem; font-weight: 800; margin: 0;"><?php echo e($pkg->packageTemplate?->name ?? __('Custom Package')); ?></h4>
                                            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.2rem;">
                                                <?php echo e(__('Activated')); ?>: <?php echo e($pkg->activated_at ? $pkg->activated_at->format('Y-m-d') : '-'); ?> |
                                                <?php echo e(__('Expires')); ?>: <?php echo e($pkg->expires_at ? $pkg->expires_at->format('Y-m-d') : __('No Expiration')); ?>

                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <span class="rpt-pill rpt-pill-success"><?php echo e($pkg->remaining_sessions); ?> <?php echo e(__('Remaining')); ?></span>
                                            <span class="rpt-pill rpt-pill-neutral"><?php echo e($pkg->used_sessions); ?> / <?php echo e($pkg->total_sessions); ?> <?php echo e(__('Used')); ?></span>
                                        </div>
                                    </div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pkg->transactions->isNotEmpty()): ?>
                                        <div class="rpt-table-responsive">
                                            <table class="rpt-table">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo e(__('Date')); ?></th>
                                                        <th><?php echo e(__('Transaction Type')); ?></th>
                                                        <th><?php echo e(__('Sessions Delta')); ?></th>
                                                        <th><?php echo e(__('Balance After')); ?></th>
                                                        <th><?php echo e(__('Reason / Description')); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pkg->transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <tr>
                                                            <td style="font-family: monospace;"><?php echo e($trx->created_at->format('Y-m-d H:i')); ?></td>
                                                            <td><?php echo e(__(ucwords(str_replace('_', ' ', $trx->type)))); ?></td>
                                                            <td style="font-weight: 800; color: <?php echo e($trx->sessions_delta > 0 ? '#10B981' : '#E11D48'); ?>;">
                                                                <?php echo e($trx->sessions_delta > 0 ? '+' : ''); ?><?php echo e($trx->sessions_delta); ?>

                                                            </td>
                                                            <td style="font-weight: 800;"><?php echo e($trx->balance_after); ?></td>
                                                            <td style="color: #64748B;"><?php echo e($trx->reason ?: '-'); ?></td>
                                                        </tr>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                    <i class="fa-solid fa-credit-card" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                    <p style="font-weight: 700;"><?php echo e(__('No packages found for this student.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="rpt-print-section" id="section-sessions" style="<?php echo e($activeTab !== 'sessions' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-video"></i> <?php echo e(__('Live Sessions & Attendance Log')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #E11D48; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-video"></i>
                                <span><?php echo e(__('Live Sessions & Attendance Log')); ?></span>
                            </div>

                            <div class="rpt-table-responsive">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Session Title & Course')); ?></th>
                                            <th><?php echo e(__('Teacher')); ?></th>
                                            <th><?php echo e(__('Scheduled Date & Time')); ?></th>
                                            <th><?php echo e(__('Platform & Duration')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('Attendance')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->liveSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <tr>
                                                <td>
                                                    <div style="font-weight: 800;"><?php echo e($sess->title); ?></div>
                                                    <div style="font-size: 0.725rem; color: #94A3B8;"><?php echo e($sess->course?->title ?? __('Live Session')); ?></div>
                                                </td>
                                                <td><?php echo e($sess->teacherProfile?->user?->name ?? __('Teacher')); ?></td>
                                                <td style="font-family: monospace;"><?php echo e($sess->scheduled_at ? $sess->scheduled_at->format('Y-m-d H:i') : '-'); ?></td>
                                                <td>
                                                    <i class="fa-solid fa-desktop" style="color: #94A3B8; margin-right: 0.25rem;"></i>
                                                    <?php echo e(__(ucfirst($sess->meeting_platform ?: 'online'))); ?> (<?php echo e($sess->duration_minutes); ?>m)
                                                </td>
                                                <td>
                                                    <span class="rpt-pill <?php echo e($sess->status === 'completed' ? 'rpt-pill-success' : ($sess->status === 'scheduled' ? 'rpt-pill-info' : 'rpt-pill-danger')); ?>">
                                                        <?php echo e(__(ucfirst($sess->status))); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($sess->attendance_status, ['present', 'attended'], true) || $sess->status === 'completed'): ?>
                                                        <span class="rpt-pill rpt-pill-success"><i class="fa-solid fa-circle-check"></i> <?php echo e(__('Present')); ?></span>
                                                    <?php elseif($sess->attendance_status === 'absent'): ?>
                                                        <span class="rpt-pill rpt-pill-danger"><i class="fa-solid fa-circle-xmark"></i> <?php echo e(__('Absent')); ?></span>
                                                    <?php else: ?>
                                                        <span style="color: #94A3B8; font-size: 0.75rem;"><?php echo e(__('Pending')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <tr>
                                                <td colspan="6" style="text-align: center; padding: 2rem; color: #94A3B8;">
                                                    <?php echo e(__('No live sessions found for this student.')); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-assignments" style="<?php echo e($activeTab !== 'assignments' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-file-pen"></i> <?php echo e(__('Assignments, Quizzes & Homework Submissions')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #F59E0B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-file-pen"></i>
                                <span><?php echo e(__('Assignments, Quizzes & Homework Submissions')); ?></span>
                            </div>

                            <div class="rpt-table-responsive">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Assignment')); ?></th>
                                            <th><?php echo e(__('Course')); ?></th>
                                            <th><?php echo e(__('Submission Date')); ?></th>
                                            <th><?php echo e(__('Grade / Score')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('Teacher Feedback')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <tr>
                                                <td style="font-weight: 800;"><?php echo e($sub->assignment?->title ?? __('Assignment')); ?></td>
                                                <td><?php echo e($sub->assignment?->course?->title ?? '-'); ?></td>
                                                <td style="font-family: monospace;"><?php echo e($sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '-'); ?></td>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sub->grade !== null): ?>
                                                        <span style="font-size: 0.95rem; font-weight: 900; color: <?php echo e($sub->grade >= ($sub->assignment?->passing_grade ?? 60) ? '#10B981' : '#E11D48'); ?>;">
                                                            <?php echo e($sub->grade); ?>%
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #94A3B8;"><?php echo e(__('Ungraded')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="rpt-pill <?php echo e($sub->status?->value === 'completed' || $sub->status === 'completed' ? 'rpt-pill-success' : 'rpt-pill-warning'); ?>">
                                                        <?php echo e(__(ucfirst($sub->status?->value ?? (string) $sub->status))); ?>

                                                    </span>
                                                </td>
                                                <td style="color: #64748B;"><?php echo e($sub->teacher_notes ?: '-'); ?></td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <tr>
                                                <td colspan="6" style="text-align: center; padding: 2rem; color: #94A3B8;">
                                                    <?php echo e(__('No assignment submissions found for this student.')); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-notes" style="<?php echo e($activeTab !== 'notes' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-clipboard-user"></i> <?php echo e(__('Educational & Behavioral Notes Log')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #0284C7; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-clipboard-user"></i>
                                <span><?php echo e(__('Educational & Behavioral Notes Log')); ?></span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rpt-item-card" style="margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="width: 1.75rem; height: 1.75rem; border-radius: 0.5rem; background: rgba(2, 132, 199, 0.15); color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                                <i class="fa-solid fa-comment-dots"></i>
                                            </span>
                                            <strong style="font-size: 0.85rem;"><?php echo e($note->teacherProfile?->user?->name ?? ($note->author?->name ?? __('Teacher / Counselor'))); ?></strong>
                                        </div>
                                        <span style="font-size: 0.75rem; color: #94A3B8; font-family: monospace;"><?php echo e($note->created_at->format('Y-m-d H:i')); ?></span>
                                    </div>
                                    <p class="rpt-note-body"><?php echo e($note->note); ?></p>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                    <p style="font-weight: 700;"><?php echo e(__('No educational notes recorded for this student.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-exceptions" style="<?php echo e($activeTab !== 'exceptions' ? 'display:none;' : ''); ?>">
                        <div class="rpt-section-print-title" style="display: none;"><i class="fa-solid fa-envelope-open-text"></i> <?php echo e(__('Absence & Session Exception Requests')); ?></div>
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #9333EA; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-envelope-open-text"></i>
                                <span><?php echo e(__('Absence & Session Exception Requests')); ?></span>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rpt-item-card" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <h4 style="font-size: 0.9rem; font-weight: 800; margin: 0;"><?php echo e($exc->liveSession?->title ?? $exc->course?->title ?? __('General Session Request')); ?></h4>
                                            <span class="rpt-pill <?php echo e($exc->status === 'approved' ? 'rpt-pill-success' : ($exc->status === 'rejected' ? 'rpt-pill-danger' : 'rpt-pill-warning')); ?>">
                                                <?php echo e(__(ucfirst($exc->status))); ?>

                                            </span>
                                        </div>
                                        <p class="rpt-item-meta" style="font-size: 0.75rem; margin: 0.25rem 0 0 0;"><?php echo e(__('Reason')); ?>: <?php echo e($exc->reason ?: '-'); ?></p>
                                    </div>
                                    <span style="font-size: 0.75rem; color: #94A3B8; font-family: monospace;"><?php echo e($exc->created_at->format('Y-m-d H:i')); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="text-align: center; padding: 2.5rem; color: #94A3B8;">
                                    <i class="fa-solid fa-envelope-circle-check" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i>
                                    <p style="font-weight: 700;"><?php echo e(__('No exception requests filed by this student.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="rpt-print-signatures" style="display: none;">
                        <div class="rpt-signature-box">
                            <div><?php echo e(__('Academic Advisor / Counselor')); ?></div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;"><?php echo e(__('Advisor Signature & Verification')); ?></div>
                        </div>
                        <div class="rpt-signature-box">
                            <div><?php echo e(__('Head of Academic Affairs')); ?></div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;"><?php echo e(__('Registrar Signature')); ?></div>
                        </div>
                        <div class="rpt-signature-box">
                            <div><?php echo e(__('Official Institution Seal & Approval')); ?></div>
                            <div class="rpt-signature-line"></div>
                            <div style="margin-top: 4pt; font-weight: 700;"><?php echo e(brand('app_name', 'EDU')); ?> <?php echo e(__('Administration')); ?></div>
                        </div>
                    </div>

                    
                    <div class="rpt-print-footer" style="display: none;">
                        <?php echo e(brand('app_name', 'EDU')); ?> &mdash; <?php echo e(__('Official Confidential Academic Progress Dossier')); ?> &mdash; <?php echo e(__('Issue Date')); ?>: <?php echo e(now()->format('Y-m-d H:i')); ?>

                    </div>

                </div>
            </div>
        <?php else: ?>
            <div class="rpt-empty-card">
                <i class="fa-solid fa-user-slash" style="font-size: 3rem; color: #94A3B8; margin-bottom: 0.75rem;"></i>
                <h3><?php echo e(__('No student selected')); ?></h3>
                <p><?php echo e(__('Please pick a student from the dropdown above to view their comprehensive academic report.')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <script>
    function elitePrintReport() {
        // Unhide all print sections and headers
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/pages/student-reports-page.blade.php ENDPATH**/ ?>