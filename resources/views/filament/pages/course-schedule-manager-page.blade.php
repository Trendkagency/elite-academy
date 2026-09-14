<x-filament-panels::page>
    <div class="sch-wrapper">
        <style>
            /* ─────────────────────────────────────────────────────────────
               1. BASE & THEME VARIABLES (LIGHT & DARK MODE DUAL SUPPORT)
            ───────────────────────────────────────────────────────────── */
            :root {
                --sch-bg-surface: #FFFFFF;
                --sch-bg-surface-subtle: #F8FAFC;
                --sch-bg-surface-elevated: #FFFFFF;
                --sch-border: #E2E8F0;
                --sch-border-focus: #0D9488;
                --sch-text-primary: #0F172A;
                --sch-text-secondary: #475569;
                --sch-text-muted: #94A3B8;
                --sch-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
                --sch-shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.08);
            }

            html.dark, .dark, [data-theme="dark"], .fi-theme-dark {
                --sch-bg-surface: #0F172A;
                --sch-bg-surface-subtle: #1E293B;
                --sch-bg-surface-elevated: #162032;
                --sch-border: #334155;
                --sch-border-focus: #14B8A6;
                --sch-text-primary: #F8FAFC;
                --sch-text-secondary: #CBD5E1;
                --sch-text-muted: #64748B;
                --sch-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
                --sch-shadow-lg: 0 14px 35px -4px rgba(0, 0, 0, 0.5);
            }

            .sch-wrapper {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
                box-sizing: border-box;
                color: var(--sch-text-primary);
            }
            .sch-wrapper *, .sch-wrapper *::before, .sch-wrapper *::after {
                box-sizing: border-box;
            }

            /* ── Global SVG icon rules ── */
            .sch-wrapper svg {
                display: inline-block !important;
                vertical-align: middle;
                flex-shrink: 0;
                max-width: none !important;
                max-height: none !important;
            }

            /* ── Card Components ── */
            .sch-card {
                background: var(--sch-bg-surface);
                border: 1.5px solid var(--sch-border);
                border-radius: 1.25rem;
                padding: 1.25rem;
                box-shadow: var(--sch-shadow);
                transition: background-color 0.2s ease, border-color 0.2s ease;
            }

            /* ── Header & Title ── */
            .sch-topbar-header {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: 1.25rem;
            }
            .sch-title-box {
                display: flex;
                align-items: center;
                gap: 0.875rem;
            }
            .sch-title-icon {
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 1rem;
                background: linear-gradient(135deg, #0D9488, #059669);
                color: #FFFFFF !important;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 6px 16px rgba(13, 148, 136, 0.35);
                flex-shrink: 0;
            }
            .sch-title-icon svg {
                width: 1.75rem !important;
                height: 1.75rem !important;
                stroke: #FFFFFF !important;
                color: #FFFFFF !important;
                display: block !important;
            }
            .sch-title-text {
                font-size: 1.35rem;
                font-weight: 900;
                color: var(--sch-text-primary);
                line-height: 1.2;
                margin: 0;
            }
            .sch-subtitle {
                font-size: 0.8rem;
                color: var(--sch-text-secondary);
                margin: 0.25rem 0 0 0;
                font-weight: 500;
            }

            /* ── Header Buttons ── */
            .sch-top-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex-wrap: wrap;
            }
            .sch-btn-primary {
                background: linear-gradient(135deg, #0D9488, #059669);
                color: #FFFFFF !important;
                font-weight: 800;
                font-size: 0.85rem;
                padding: 0.7rem 1.25rem;
                border-radius: 0.875rem;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                border: none;
                transition: all 0.2s ease;
                box-shadow: 0 4px 14px rgba(13, 148, 136, 0.25);
                text-decoration: none;
            }
            .sch-btn-primary:hover {
                background: linear-gradient(135deg, #0f766e, #047857);
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35);
            }
            .sch-btn-primary:disabled, .sch-btn-primary[disabled] {
                opacity: 0.65 !important;
                cursor: not-allowed !important;
                pointer-events: none !important;
                transform: none !important;
                box-shadow: none !important;
            }
            .sch-btn-secondary {
                background: var(--sch-bg-surface-subtle);
                color: var(--sch-text-primary) !important;
                font-weight: 700;
                font-size: 0.85rem;
                padding: 0.7rem 1.25rem;
                border-radius: 0.875rem;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                border: 1.5px solid var(--sch-border);
                transition: all 0.2s ease;
                text-decoration: none;
            }
            .sch-btn-secondary:hover {
                border-color: var(--sch-border-focus);
                transform: translateY(-1px);
            }
            .sch-btn-secondary:disabled, .sch-btn-secondary[disabled] {
                opacity: 0.6 !important;
                cursor: not-allowed !important;
                pointer-events: none !important;
                transform: none !important;
            }

            /* ── Form Inputs & Selects ── */
            .sch-filters-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
                padding-top: 1rem;
                border-top: 1px solid var(--sch-border);
            }
            @media (max-width: 1024px) {
                .sch-filters-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 600px) {
                .sch-filters-grid {
                    grid-template-columns: 1fr;
                }
            }
            .sch-form-group {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }
            .sch-label {
                font-size: 0.725rem;
                font-weight: 800;
                color: var(--sch-text-secondary);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }
            .sch-label svg {
                width: 0.875rem;
                height: 0.875rem;
                flex-shrink: 0;
            }
            .sch-select, .sch-input {
                width: 100%;
                background: var(--sch-bg-surface-subtle);
                border: 1.5px solid var(--sch-border);
                border-radius: 0.875rem;
                padding: 0.65rem 0.875rem;
                font-size: 0.85rem;
                font-weight: 700;
                color: var(--sch-text-primary);
                outline: none;
                transition: all 0.2s ease;
            }
            .sch-select:focus, .sch-input:focus {
                border-color: var(--sch-border-focus);
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
            }

            /* ── Search Bar ── */
            .sch-search-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid var(--sch-border);
                flex-wrap: wrap;
            }
            .sch-search-wrap {
                position: relative;
                flex: 1;
                min-width: 260px;
            }
            .sch-search-wrap svg {
                position: absolute;
                inset-inline-start: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--sch-text-muted);
                width: 0.9rem;
                height: 0.9rem;
            }
            .sch-search-input {
                width: 100%;
                background: var(--sch-bg-surface-subtle);
                border: 1.5px solid var(--sch-border);
                border-radius: 0.875rem;
                padding: 0.65rem 1rem 0.65rem 2.5rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--sch-text-primary);
                outline: none;
                transition: all 0.2s ease;
            }
            html[dir="rtl"] .sch-search-input {
                padding: 0.65rem 2.5rem 0.65rem 1rem;
            }
            .sch-search-input:focus {
                border-color: var(--sch-border-focus);
                box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
            }
            .sch-btn-reset {
                background: #FEF2F2;
                color: #E11D48;
                border: 1px solid #FECDD3;
                padding: 0.6rem 1rem;
                border-radius: 0.75rem;
                font-size: 0.8rem;
                font-weight: 800;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                transition: all 0.2s ease;
            }
            html.dark .sch-btn-reset, .dark .sch-btn-reset {
                background: rgba(159, 18, 57, 0.25);
                border-color: rgba(225, 29, 72, 0.4);
                color: #FDA4AF;
            }

            /* ── KPI Metric Cards ── */
            .sch-kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
            }
            @media (max-width: 1024px) {
                .sch-kpi-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            @media (max-width: 480px) {
                .sch-kpi-grid {
                    grid-template-columns: 1fr;
                }
            }
            .sch-kpi-box {
                background: var(--sch-bg-surface);
                border: 1.5px solid var(--sch-border);
                border-radius: 1.25rem;
                padding: 1.25rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                box-shadow: var(--sch-shadow);
                transition: all 0.2s ease;
            }
            .sch-kpi-box:hover {
                transform: translateY(-2px);
                border-color: var(--sch-border-focus);
            }
            .sch-kpi-icon {
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .sch-kpi-icon svg {
                width: 1.6rem !important;
                height: 1.6rem !important;
                stroke: currentColor !important;
                display: block !important;
            }
            .sch-kpi-icon-teal {
                background: #CCFBF1;
                color: #0D9488;
                border: 1px solid #99F6E4;
            }
            .sch-kpi-icon-emerald {
                background: #D1FAE5;
                color: #059669;
                border: 1px solid #A7F3D0;
            }
            .sch-kpi-icon-blue {
                background: #DBEAFE;
                color: #2563EB;
                border: 1px solid #BFDBFE;
            }
            .sch-kpi-icon-purple {
                background: #F3E8FF;
                color: #9333EA;
                border: 1px solid #E9D5FF;
            }

            html.dark .sch-kpi-icon-teal, .dark .sch-kpi-icon-teal, [data-theme="dark"] .sch-kpi-icon-teal, .fi-theme-dark .sch-kpi-icon-teal {
                background: rgba(20, 184, 166, 0.22) !important;
                color: #2DD4BF !important;
                border-color: rgba(45, 212, 191, 0.35) !important;
            }
            html.dark .sch-kpi-icon-emerald, .dark .sch-kpi-icon-emerald, [data-theme="dark"] .sch-kpi-icon-emerald, .fi-theme-dark .sch-kpi-icon-emerald {
                background: rgba(16, 185, 129, 0.22) !important;
                color: #34D399 !important;
                border-color: rgba(52, 211, 153, 0.35) !important;
            }
            html.dark .sch-kpi-icon-blue, .dark .sch-kpi-icon-blue, [data-theme="dark"] .sch-kpi-icon-blue, .fi-theme-dark .sch-kpi-icon-blue {
                background: rgba(59, 130, 246, 0.22) !important;
                color: #60A5FA !important;
                border-color: rgba(96, 165, 250, 0.35) !important;
            }
            html.dark .sch-kpi-icon-purple, .dark .sch-kpi-icon-purple, [data-theme="dark"] .sch-kpi-icon-purple, .fi-theme-dark .sch-kpi-icon-purple {
                background: rgba(168, 85, 247, 0.22) !important;
                color: #C084FC !important;
                border-color: rgba(192, 132, 252, 0.35) !important;
            }
            .sch-kpi-num {
                font-size: 1.65rem;
                font-weight: 900;
                color: var(--sch-text-primary);
                line-height: 1.1;
                margin-top: 0.15rem;
            }
            .sch-kpi-tag {
                font-size: 0.725rem;
                font-weight: 800;
                color: var(--sch-text-secondary);
                text-transform: uppercase;
                letter-spacing: 0.04em;
                display: block;
            }

            /* ── Segmented Tabs ── */
            .sch-tabs-nav {
                display: inline-flex;
                background: var(--sch-bg-surface-subtle);
                padding: 0.35rem;
                border-radius: 1rem;
                gap: 0.5rem;
                border: 1.5px solid var(--sch-border);
                width: 100%;
                max-width: 34rem;
            }
            .sch-tab-btn {
                flex: 1;
                padding: 0.65rem 1.25rem;
                border-radius: 0.75rem;
                font-size: 0.825rem;
                font-weight: 800;
                color: var(--sch-text-secondary);
                border: none;
                background: transparent;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                transition: all 0.2s ease;
            }
            .sch-tab-btn svg {
                width: 1rem;
                height: 1rem;
            }
            .sch-tab-btn.active {
                background: linear-gradient(135deg, #0D9488, #059669);
                color: #FFFFFF !important;
                box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            }
            .sch-tab-badge {
                padding: 0.15rem 0.5rem;
                border-radius: 0.5rem;
                font-size: 0.7rem;
                font-weight: 900;
                background: rgba(0, 0, 0, 0.08);
                color: var(--sch-text-primary);
            }
            html.dark .sch-tab-badge, .dark .sch-tab-badge {
                background: rgba(255, 255, 255, 0.1);
                color: #F8FAFC;
            }
            .sch-tab-btn.active .sch-tab-badge {
                background: rgba(255, 255, 255, 0.25);
                color: #FFFFFF;
            }

            /* ── Desktop Table ── */
            .sch-table-wrapper {
                background: var(--sch-bg-surface);
                border: 1.5px solid var(--sch-border);
                border-radius: 1.25rem;
                overflow: hidden;
                box-shadow: var(--sch-shadow);
            }
            .sch-table {
                width: 100%;
                border-collapse: collapse;
                text-align: start;
                font-size: 0.85rem;
            }
            .sch-th {
                background: var(--sch-bg-surface-subtle);
                padding: 0.875rem 1rem;
                font-size: 0.725rem;
                font-weight: 900;
                color: var(--sch-text-secondary);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                border-bottom: 1.5px solid var(--sch-border);
                white-space: nowrap;
            }
            .sch-td {
                padding: 1rem;
                border-bottom: 1px solid var(--sch-border);
                vertical-align: middle;
                color: var(--sch-text-primary);
            }
            .sch-tr:hover td {
                background: var(--sch-bg-surface-subtle);
            }

            /* ── Mobile Feed Cards ── */
            .sch-mobile-feed {
                display: none;
                flex-direction: column;
                gap: 1rem;
            }
            .sch-mobile-card {
                background: var(--sch-bg-surface);
                border: 1.5px solid var(--sch-border);
                border-radius: 1.25rem;
                padding: 1rem;
                position: relative;
                overflow: hidden;
                box-shadow: var(--sch-shadow);
            }
            .sch-card-strip {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
            }
            .sch-mobile-actions {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-top: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid var(--sch-border);
            }
            @media (max-width: 768px) {
                .sch-desktop-table-container {
                    display: none !important;
                }
                .sch-mobile-feed {
                    display: flex !important;
                }
            }

            /* ── Badges ── */
            .sch-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 0.625rem;
                font-size: 0.75rem;
                font-weight: 800;
                line-height: 1.2;
            }
            .sch-badge-scheduled { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
            .sch-badge-completed { background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; }
            .sch-badge-cancelled { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }
            .sch-badge-active { background: #F0FDFA; color: #0F766E; border: 1px solid #99F6E4; }
            .sch-badge-cohort { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
            html.dark .sch-badge-scheduled, .dark .sch-badge-scheduled { background: rgba(180, 83, 9, 0.2); color: #FCD34D; border-color: rgba(245, 158, 11, 0.3); }
            html.dark .sch-badge-completed, .dark .sch-badge-completed { background: rgba(4, 120, 87, 0.2); color: #6EE7B7; border-color: rgba(16, 185, 129, 0.3); }
            html.dark .sch-badge-cancelled, .dark .sch-badge-cancelled { background: rgba(185, 28, 28, 0.2); color: #FCA5A5; border-color: rgba(239, 68, 68, 0.3); }
            html.dark .sch-badge-active, .dark .sch-badge-active { background: rgba(15, 118, 110, 0.2); color: #5EEAD4; border-color: rgba(20, 184, 166, 0.3); }
            html.dark .sch-badge-cohort, .dark .sch-badge-cohort { background: rgba(67, 56, 202, 0.2); color: #A5B4FC; border-color: rgba(99, 102, 241, 0.3); }

            /* ── Table Action Buttons ── */
            .sch-action-group {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
            }
            .sch-icon-btn {
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 0.625rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--sch-border);
                cursor: pointer;
                transition: all 0.15s ease;
                background: var(--sch-bg-surface-subtle);
            }
            .sch-icon-btn svg {
                width: 0.95rem;
                height: 0.95rem;
            }
            .sch-icon-btn:hover {
                transform: translateY(-2px);
                border-color: var(--sch-border-focus);
            }
            .sch-icon-btn-video { color: #0D9488; }
            .sch-icon-btn-time { color: #2563EB; }
            .sch-icon-btn-check { color: #059669; }
            .sch-icon-btn-cancel { color: #E11D48; }

            /* ── Student Multi-Select Picker ── */
            .sch-multi-picker {
                background: var(--sch-bg-surface-subtle);
                border: 1.5px solid var(--sch-border);
                border-radius: 1rem;
                padding: 0.75rem;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            .sch-multi-picker-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            .sch-multi-picker-actions {
                display: flex;
                align-items: center;
                gap: 0.35rem;
            }
            .sch-picker-btn {
                font-size: 0.72rem;
                font-weight: 700;
                padding: 0.25rem 0.55rem;
                border-radius: 0.5rem;
                border: 1px solid var(--sch-border);
                background: var(--sch-bg-surface);
                color: var(--sch-text-primary);
                cursor: pointer;
                transition: all 0.15s ease;
            }
            .sch-picker-btn:hover {
                border-color: var(--sch-border-focus);
                color: #0D9488;
            }
            .sch-multi-search-wrap {
                position: relative;
                width: 100%;
            }
            .sch-multi-search-wrap svg {
                position: absolute;
                inset-inline-start: 0.65rem;
                top: 50%;
                transform: translateY(-50%);
                width: 0.85rem;
                height: 0.85rem;
                color: var(--sch-text-muted);
            }
            .sch-multi-search-input {
                width: 100%;
                background: var(--sch-bg-surface);
                border: 1px solid var(--sch-border);
                border-radius: 0.65rem;
                padding: 0.45rem 0.75rem 0.45rem 1.85rem;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--sch-text-primary);
                outline: none;
                transition: all 0.15s ease;
            }
            html[dir="rtl"] .sch-multi-search-input {
                padding: 0.45rem 1.85rem 0.45rem 0.75rem;
            }
            .sch-multi-search-input:focus {
                border-color: var(--sch-border-focus);
                box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.15);
            }
            .sch-multi-list {
                max-height: 10rem;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
                padding-inline-end: 0.25rem;
            }
            .sch-multi-item {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                padding: 0.5rem 0.75rem;
                border-radius: 0.65rem;
                background: var(--sch-bg-surface);
                border: 1.5px solid var(--sch-border);
                cursor: pointer;
                transition: all 0.15s ease;
                user-select: none;
            }
            .sch-multi-item:hover {
                border-color: var(--sch-border-focus);
                background: rgba(13, 148, 136, 0.04);
            }
            .sch-multi-item.is-selected {
                border-color: #0D9488;
                background: rgba(13, 148, 136, 0.08);
            }
            html.dark .sch-multi-item.is-selected, .dark .sch-multi-item.is-selected {
                border-color: #14B8A6;
                background: rgba(20, 184, 166, 0.15);
            }
            .sch-multi-item input[type="checkbox"] {
                width: 1.05rem;
                height: 1.05rem;
                accent-color: #0D9488;
                cursor: pointer;
                flex-shrink: 0;
            }
            .sch-multi-item-name {
                font-size: 0.825rem;
                font-weight: 700;
                color: var(--sch-text-primary);
                flex: 1;
            }
            .sch-multi-item-code {
                font-size: 0.7rem;
                font-weight: 800;
                color: var(--sch-text-muted);
                background: var(--sch-bg-surface-subtle);
                padding: 0.15rem 0.45rem;
                border-radius: 0.35rem;
                border: 1px solid var(--sch-border);
            }
            .sch-chips-wrap {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
                margin-top: 0.25rem;
                max-height: 4.5rem;
                overflow-y: auto;
            }
            .sch-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.2rem 0.5rem;
                border-radius: 0.5rem;
                background: #CCFBF1;
                color: #0F766E;
                font-size: 0.725rem;
                font-weight: 700;
                border: 1px solid #99F6E4;
            }
            html.dark .sch-chip, .dark .sch-chip {
                background: rgba(20, 184, 166, 0.2);
                color: #5EEAD4;
                border-color: rgba(20, 184, 166, 0.35);
            }
            .sch-chip-remove {
                background: none;
                border: none;
                color: inherit;
                cursor: pointer;
                font-size: 0.85rem;
                font-weight: 900;
                line-height: 1;
                padding: 0;
            }

            /* ── Modals ── */
            .sch-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.75);
                backdrop-filter: blur(8px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                overflow-y: auto;
            }
            .sch-modal-dialog {
                background: var(--sch-bg-surface);
                border-radius: 1.5rem;
                width: 100%;
                max-width: 38rem;
                max-height: min(92vh, 52rem);
                display: flex;
                flex-direction: column;
                min-height: 0;
                overflow: hidden;
                box-shadow: var(--sch-shadow-lg);
                border: 1.5px solid var(--sch-border);
                animation: schModalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .sch-conflict-preview-scroll {
                scrollbar-width: thin;
                scrollbar-color: rgba(20, 184, 166, 0.4) transparent;
                -webkit-overflow-scrolling: touch;
            }
            .sch-conflict-preview-scroll::-webkit-scrollbar {
                width: 6px;
            }
            .sch-conflict-preview-scroll::-webkit-scrollbar-thumb {
                background: rgba(20, 184, 166, 0.4);
                border-radius: 6px;
            }
            .sch-conflict-preview-scroll::-webkit-scrollbar-track {
                background: transparent;
            }
            @keyframes schModalIn {
                from { opacity: 0; transform: scale(0.96) translateY(12px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
            @media (max-width: 640px) {
                .sch-modal-overlay {
                    align-items: flex-end;
                    padding: 0;
                }
                .sch-modal-dialog {
                    border-bottom-left-radius: 0;
                    border-bottom-right-radius: 0;
                    max-height: 90dvh;
                }
            }
        </style>

        {{-- ── 1. TOP BAR / HERO & FILTERS ── --}}
        <div class="sch-card">
            <div class="sch-topbar-header">
                <div class="sch-title-box">
                    <div class="sch-title-icon">
                        {{-- Calendar Days Icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-6.75-3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="sch-title-text">{{ __('Course & Student Schedule Manager') }}</h1>
                        <p class="sch-subtitle">{{ __('Manage one-on-one sessions, recurring schedules, and timetable sync between students and teachers.') }}</p>
                    </div>
                </div>

                <div class="sch-top-actions">
                    <button type="button" wire:click="openCreateSessionModal" class="sch-btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>{{ __('Schedule Live Session') }}</span>
                    </button>

                    <button type="button" wire:click="openRecurringModal" class="sch-btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 1rem; height: 1rem; color: #0D9488;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>{{ __('Generate Full Schedule') }}</span>
                    </button>
                </div>
            </div>

            {{-- ── Filters Grid ── --}}
            <div class="sch-filters-grid">
                {{-- Student --}}
                <div class="sch-form-group">
                    <label class="sch-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #0D9488;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        <span>{{ __('Filter by Student') }}</span>
                    </label>
                    <select wire:model.live="selectedStudentId" class="sch-select">
                        <option value="">{{ __('All Students') }}</option>
                        @foreach ($this->students as $stu)
                            <option value="{{ $stu->id }}">
                                {{ $stu->name }} ({{ $stu->studentProfile?->student_code ?? '#' . $stu->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher (Hidden if logged in as teacher) --}}
                @if (! $this->isTeacherOnly)
                    <div class="sch-form-group">
                        <label class="sch-label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #2563EB;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                            </svg>
                            <span>{{ __('Filter by Teacher') }}</span>
                        </label>
                        <select wire:model.live="selectedTeacherId" class="sch-select">
                            <option value="">{{ __('All Teachers') }}</option>
                            @foreach ($this->teachers as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->user?->name ?? 'Teacher #' . $t->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Course --}}
                <div class="sch-form-group">
                    <label class="sch-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #9333EA;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>{{ __('Filter by Course') }}</span>
                    </label>
                    <select wire:model.live="selectedCourseId" class="sch-select">
                        <option value="">{{ __('All Courses') }}</option>
                        @foreach ($this->courses as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->title }} ({{ $c->subject?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="sch-form-group">
                    <label class="sch-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="color: #D97706;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        <span>{{ __('Status') }}</span>
                    </label>
                    <select wire:model.live="selectedStatus" class="sch-select">
                        <option value="all">{{ __('All Statuses') }}</option>
                        <option value="scheduled">{{ __('Scheduled') }}</option>
                        <option value="link_visible">{{ __('Live Link Visible') }}</option>
                        <option value="in_progress">{{ __('In Progress') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>

            {{-- ── Search & Clear Bar ── --}}
            <div class="sch-search-bar">
                <div class="sch-search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="searchQuery"
                        placeholder="{{ __('Search session by title, student name, or course...') }}"
                        class="sch-search-input">
                </div>

                @if ($selectedStudentId || $selectedTeacherId || $selectedCourseId || $selectedStatus !== 'all' || $searchQuery)
                    <button type="button" wire:click="resetFilters" class="sch-btn-reset">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.9rem; height: 0.9rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>{{ __('Clear Filters') }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- ── 2. KPI METRICS CARDS ── --}}
        <div class="sch-kpi-grid">
            {{-- Total Sessions --}}
            <div class="sch-kpi-box">
                <div class="sch-kpi-icon sch-kpi-icon-teal">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div>
                    <span class="sch-kpi-tag">{{ __('Total Sessions') }}</span>
                    <div class="sch-kpi-num">{{ $this->metrics['total_sessions'] }}</div>
                </div>
            </div>

            {{-- Upcoming Live --}}
            <div class="sch-kpi-box">
                <div class="sch-kpi-icon sch-kpi-icon-emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-6.75-3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <div>
                    <span class="sch-kpi-tag">{{ __('Upcoming Live') }}</span>
                    <div class="sch-kpi-num" style="color: #059669;">{{ $this->metrics['upcoming'] }}</div>
                </div>
            </div>

            {{-- Recurring Templates --}}
            <div class="sch-kpi-box">
                <div class="sch-kpi-icon sch-kpi-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <div>
                    <span class="sch-kpi-tag">{{ __('Recurring Templates') }}</span>
                    <div class="sch-kpi-num" style="color: #2563EB;">{{ $this->metrics['recurring_rules'] }}</div>
                </div>
            </div>

            {{-- Active Enrollments --}}
            <div class="sch-kpi-box">
                <div class="sch-kpi-icon sch-kpi-icon-purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
                <div>
                    <span class="sch-kpi-tag">{{ __('Active Enrollments') }}</span>
                    <div class="sch-kpi-num" style="color: #9333EA;">{{ $this->metrics['active_enrollments'] }}</div>
                </div>
            </div>
        </div>

        {{-- ── 3. SUBTABS SELECTOR ── --}}
        <div>
            <div class="sch-tabs-nav">
                <button type="button" wire:click="setTab('sessions')"
                    class="sch-tab-btn {{ $activeTab === 'sessions' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                    <span>{{ __('Live Sessions & Timetable') }}</span>
                    <span class="sch-tab-badge">{{ $this->sessionsList->count() }}</span>
                </button>

                <button type="button" wire:click="setTab('recurring')"
                    class="sch-tab-btn {{ $activeTab === 'recurring' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>{{ __('Recurring Schedule Rules') }}</span>
                    <span class="sch-tab-badge">{{ $this->recurringSchedulesList->count() }}</span>
                </button>
            </div>
        </div>

        {{-- ── 4. TAB 1: SESSIONS (DESKTOP TABLE + MOBILE CARDS) ── --}}
        @if ($activeTab === 'sessions')
            {{-- A. MOBILE APP FEED (< 768px) --}}
            <div class="sch-mobile-feed">
                @forelse ($this->sessionsList as $session)
                    @php
                        $isCancelled = in_array($session->status, ['cancelled', 'cancelled_by_teacher']) || $session->lifecycle_state === 'cancelled';
                        $isCompleted = $session->status === 'completed' || $session->lifecycle_state === 'completed';
                        $stripBg = match (true) {
                            $isCompleted => '#10B981',
                            $isCancelled => '#E11D48',
                            in_array($session->status, ['in_progress', 'link_visible']) => '#0D9488',
                            $session->status === 'scheduled' => '#F59E0B',
                            default => '#64748B',
                        };
                    @endphp
                    <div class="sch-mobile-card">
                        <div class="sch-card-strip" style="background-color: {{ $stripBg }};"></div>

                        {{-- Date & Status Row --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; padding-top: 0.25rem;">
                            <div>
                                <span style="font-weight: 900; font-size: 0.8rem; color: var(--sch-text-primary); display: flex; align-items: center; gap: 0.35rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.95rem; height: 0.95rem; color: #0D9488;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                    </svg>
                                    <span>{{ $session->scheduled_at ? $session->scheduled_at->translatedFormat('D, d M Y - H:i') : '—' }}</span>
                                </span>
                                <span style="font-size: 0.7rem; font-weight: 700; color: #0D9488; display: block;">
                                    {{ $session->scheduled_at ? $session->scheduled_at->diffForHumans() : '' }}
                                </span>
                            </div>

                            <span class="sch-badge {{ $isCompleted ? 'sch-badge-completed' : ($isCancelled ? 'sch-badge-cancelled' : 'sch-badge-scheduled') }}">
                                {{ $isCompleted ? __('Completed') : ($isCancelled ? __('Cancelled') : __('Scheduled')) }}
                            </span>
                        </div>

                        {{-- Course / Title --}}
                        <div style="background: var(--sch-bg-surface-subtle); border: 1px solid var(--sch-border); padding: 0.65rem 0.875rem; border-radius: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="font-weight: 900; font-size: 0.875rem; color: var(--sch-text-primary);">
                                {{ $session->title ?: $session->course?->title }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--sch-text-secondary); margin-top: 0.15rem;">
                                {{ $session->course?->subject?->name }} &bull; {{ $session->course?->title }}
                            </div>
                        </div>

                        {{-- Student & Teacher Grid --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.775rem;">
                            <div style="padding: 0.5rem; border-radius: 0.625rem; background: var(--sch-bg-surface-subtle); border: 1px solid var(--sch-border);">
                                <span style="font-size: 0.65rem; font-weight: 800; color: var(--sch-text-muted); text-transform: uppercase; display: block;">{{ __('Assigned Student') }}</span>
                                @if ($session->studentUser)
                                    <strong style="color: var(--sch-text-primary); display: block;" class="truncate">{{ $session->studentUser->name }}</strong>
                                    <span style="font-size: 0.7rem; color: var(--sch-text-muted);">{{ $session->studentUser->studentProfile?->student_code ?? '' }}</span>
                                @else
                                    <span class="sch-badge sch-badge-cohort">{{ __('General Course Cohort') }}</span>
                                @endif
                            </div>

                            <div style="padding: 0.5rem; border-radius: 0.625rem; background: var(--sch-bg-surface-subtle); border: 1px solid var(--sch-border);">
                                <span style="font-size: 0.65rem; font-weight: 800; color: var(--sch-text-muted); text-transform: uppercase; display: block;">{{ __('Teacher') }}</span>
                                <strong style="color: var(--sch-text-primary); display: block;" class="truncate">{{ $session->course?->teacher?->user?->name ?? '—' }}</strong>
                                <span style="font-size: 0.7rem; color: var(--sch-text-secondary);">{{ $session->duration_minutes }} {{ __('mins') }}</span>
                            </div>
                        </div>

                        {{-- Mobile Quick Action Bar --}}
                        <div class="sch-mobile-actions">
                            <button type="button" wire:click="openLinkModal({{ $session->id }})"
                                style="flex: 1; padding: 0.5rem; border-radius: 0.625rem; background: var(--sch-bg-surface-subtle); border: 1px solid var(--sch-border); font-weight: 700; font-size: 0.75rem; color: var(--sch-text-primary); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.95rem; height: 0.95rem; color: #0D9488;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span>{{ __('Meeting Link') }}</span>
                            </button>

                            @if (! $isCancelled && ! $isCompleted)
                                <button type="button" wire:click="openReschedule({{ $session->id }})"
                                    style="flex: 1; padding: 0.5rem; border-radius: 0.625rem; background: rgba(37, 99, 235, 0.12); color: #2563EB; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(37, 99, 235, 0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.95rem; height: 0.95rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>{{ __('Reschedule') }}</span>
                                </button>

                                <button type="button" wire:click="markAttendance({{ $session->id }}, 'present')"
                                    class="sch-icon-btn sch-icon-btn-check" title="{{ __('Mark Present') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </button>

                                <button type="button" wire:click="openCancelModal({{ $session->id }})"
                                    class="sch-icon-btn sch-icon-btn-cancel" title="{{ __('Cancel Session') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif

                            <button type="button" wire:click="deleteSession({{ $session->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this session?') }}"
                                class="sch-icon-btn" style="color: #94A3B8;" title="{{ __('Delete Session') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="sch-card" style="text-align: center; padding: 2.5rem 1rem; color: var(--sch-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; display: block;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                        <p style="font-weight: 700; margin: 0;">{{ __('No sessions match the selected filters.') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- B. DESKTOP POLISHED TABLE VIEW (>= 768px) --}}
            <div class="sch-desktop-table-container sch-table-wrapper">
                <div style="overflow-x: auto;">
                    <table class="sch-table">
                        <thead>
                            <tr>
                                <th class="sch-th">{{ __('Date & Time') }}</th>
                                <th class="sch-th">{{ __('Course & Subject') }}</th>
                                <th class="sch-th">{{ __('Assigned Student') }}</th>
                                <th class="sch-th">{{ __('Teacher') }}</th>
                                <th class="sch-th">{{ __('Duration') }}</th>
                                <th class="sch-th">{{ __('Status') }}</th>
                                <th class="sch-th">{{ __('Attendance') }}</th>
                                <th class="sch-th" style="text-align: end;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->sessionsList as $session)
                                <tr class="sch-tr">
                                    <td class="sch-td" style="white-space: nowrap;">
                                        <div style="font-weight: 900; color: var(--sch-text-primary);">
                                            {{ $session->scheduled_at ? $session->scheduled_at->translatedFormat('d M Y, H:i') : '—' }}
                                        </div>
                                        <span style="font-size: 0.725rem; font-weight: 700; color: #0D9488; display: block; margin-top: 0.15rem;">
                                            {{ $session->scheduled_at ? $session->scheduled_at->diffForHumans() : '' }}
                                        </span>
                                    </td>
                                    <td class="sch-td">
                                        <div style="font-weight: 900; color: var(--sch-text-primary); max-width: 260px;" class="truncate">
                                            {{ $session->title ?: $session->course?->title }}
                                        </div>
                                        <span style="font-size: 0.75rem; color: var(--sch-text-secondary); display: block; margin-top: 0.15rem;">
                                            {{ $session->course?->subject?->name }} &bull; {{ $session->course?->title }}
                                        </span>
                                    </td>
                                    <td class="sch-td">
                                        @if ($session->studentUser)
                                            <div>
                                                <strong style="color: var(--sch-text-primary); display: block;">{{ $session->studentUser->name }}</strong>
                                                <span style="font-size: 0.7rem; color: var(--sch-text-muted);">{{ $session->studentUser->studentProfile?->student_code ?? $session->studentUser->email }}</span>
                                            </div>
                                        @else
                                            <span class="sch-badge sch-badge-cohort">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                                </svg>
                                                <span>{{ __('General Course Cohort') }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="sch-td" style="font-weight: 700; color: var(--sch-text-primary); white-space: nowrap;">
                                        {{ $session->course?->teacher?->user?->name ?? '—' }}
                                    </td>
                                    <td class="sch-td" style="font-weight: 700; color: var(--sch-text-secondary); white-space: nowrap;">
                                        {{ $session->duration_minutes }} {{ __('mins') }}
                                    </td>
                                    <td class="sch-td" style="white-space: nowrap;">
                                        @php
                                            $isCancelled = in_array($session->status, ['cancelled', 'cancelled_by_teacher']) || $session->lifecycle_state === 'cancelled';
                                            $isCompleted = $session->status === 'completed' || $session->lifecycle_state === 'completed';
                                        @endphp
                                        <span class="sch-badge {{ $isCompleted ? 'sch-badge-completed' : ($isCancelled ? 'sch-badge-cancelled' : 'sch-badge-scheduled') }}">
                                            {{ $isCompleted ? __('Completed') : ($isCancelled ? __('Cancelled') : __('Scheduled')) }}
                                        </span>
                                    </td>
                                    <td class="sch-td" style="white-space: nowrap;">
                                        @if ($session->attendance_status)
                                            <span class="sch-badge {{ $session->attendance_status === 'present' ? 'sch-badge-completed' : 'sch-badge-cancelled' }}">
                                                {{ $session->attendance_status === 'present' ? __('Present') : __('Absent') }}
                                            </span>
                                        @else
                                            <span style="color: var(--sch-text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td class="sch-td" style="text-align: end; white-space: nowrap;">
                                        <div class="sch-action-group">
                                            <button type="button" wire:click="openLinkModal({{ $session->id }})"
                                                title="{{ __('Update Meeting Link') }}"
                                                class="sch-icon-btn sch-icon-btn-video">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                            </button>

                                            @if (! $isCancelled && ! $isCompleted)
                                                <button type="button" wire:click="openReschedule({{ $session->id }})"
                                                    title="{{ __('Reschedule Session') }}"
                                                    class="sch-icon-btn sch-icon-btn-time">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </button>

                                                <button type="button" wire:click="markAttendance({{ $session->id }}, 'present')"
                                                    title="{{ __('Mark Present') }}"
                                                    class="sch-icon-btn sch-icon-btn-check">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </button>

                                                <button type="button" wire:click="openCancelModal({{ $session->id }})"
                                                    title="{{ __('Cancel Session') }}"
                                                    class="sch-icon-btn sch-icon-btn-cancel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif

                                            <button type="button" wire:click="deleteSession({{ $session->id }})"
                                                wire:confirm="{{ __('Are you sure you want to delete this session?') }}"
                                                title="{{ __('Delete Session') }}"
                                                class="sch-icon-btn" style="color: #94A3B8;">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--sch-text-muted);">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; display: block;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                                        </svg>
                                        <p style="font-weight: 700; margin: 0;">{{ __('No sessions match the selected filters.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── 5. TAB 2: RECURRING SCHEDULE RULES ── --}}
        @if ($activeTab === 'recurring')
            {{-- A. MOBILE APP FEED (< 768px) --}}
            <div class="sch-mobile-feed">
                @forelse ($this->recurringSchedulesList as $rule)
                    <div class="sch-mobile-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="font-size: 0.875rem; color: var(--sch-text-primary);">{{ $rule->title }}</strong>
                            <span class="sch-badge {{ $rule->status === 'active' ? 'sch-badge-active' : 'sch-badge-scheduled' }}">
                                {{ $rule->status === 'active' ? __('Active') : __('Inactive') }}
                            </span>
                        </div>

                        <div style="background: var(--sch-bg-surface-subtle); border: 1px solid var(--sch-border); padding: 0.65rem 0.875rem; border-radius: 0.75rem; font-size: 0.775rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            <div><strong style="color: var(--sch-text-muted);">{{ __('Course') }}:</strong> <span style="color: var(--sch-text-primary);">{{ $rule->course?->title }}</span></div>
                            <div><strong style="color: var(--sch-text-muted);">{{ __('Teacher') }}:</strong> <span style="color: var(--sch-text-primary);">{{ $rule->teacherProfile?->user?->name ?? '—' }}</span></div>
                            <div><strong style="color: var(--sch-text-muted);">{{ __('Student') }}:</strong> <span style="color: #0D9488; font-weight: 800;">{{ $rule->studentUser?->name ?? __('General Course Cohort') }}</span></div>
                            <div><strong style="color: var(--sch-text-muted);">{{ __('Time Window') }}:</strong> <span style="color: var(--sch-text-primary); font-family: monospace;">{{ $rule->start_time }} ({{ $rule->duration_minutes }}m)</span></div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--sch-border);">
                            <span style="font-weight: 800; font-size: 0.8rem; color: #0D9488; display: flex; align-items: center; gap: 0.35rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.95rem; height: 0.95rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                <span>{{ $rule->live_sessions_count }} {{ __('sessions') }}</span>
                            </span>

                            <button type="button" wire:click="deleteRecurringRule({{ $rule->id }})"
                                wire:confirm="{{ __('Are you sure you want to delete this recurring schedule rule and cancel its pending future sessions?') }}"
                                style="background: #FEF2F2; color: #E11D48; border: 1px solid #FECDD3; border-radius: 0.625rem; padding: 0.35rem 0.75rem; font-weight: 800; font-size: 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span>{{ __('Delete Rule') }}</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="sch-card" style="text-align: center; padding: 2.5rem 1rem; color: var(--sch-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; display: block;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <p style="font-weight: 700; margin: 0;">{{ __('No recurring schedule rules found.') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- B. DESKTOP POLISHED TABLE (>= 768px) --}}
            <div class="sch-desktop-table-container sch-table-wrapper">
                <div style="overflow-x: auto;">
                    <table class="sch-table">
                        <thead>
                            <tr>
                                <th class="sch-th">{{ __('Schedule Rule Title') }}</th>
                                <th class="sch-th">{{ __('Course') }}</th>
                                <th class="sch-th">{{ __('Teacher') }}</th>
                                <th class="sch-th">{{ __('Student') }}</th>
                                <th class="sch-th">{{ __('Pattern & Days') }}</th>
                                <th class="sch-th">{{ __('Time Window') }}</th>
                                <th class="sch-th">{{ __('Generated Sessions') }}</th>
                                <th class="sch-th">{{ __('Status') }}</th>
                                <th class="sch-th" style="text-align: end;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->recurringSchedulesList as $rule)
                                <tr class="sch-tr">
                                    <td class="sch-td">
                                        <div style="font-weight: 900; color: var(--sch-text-primary);">{{ $rule->title }}</div>
                                        <span style="font-size: 0.7rem; color: var(--sch-text-muted);">{{ $rule->start_date?->format('Y-m-d') }} &rarr; {{ $rule->end_date?->format('Y-m-d') }}</span>
                                    </td>
                                    <td class="sch-td" style="font-weight: 700; color: var(--sch-text-primary);">
                                        {{ $rule->course?->title }}
                                    </td>
                                    <td class="sch-td" style="font-weight: 700; color: var(--sch-text-primary);">
                                        {{ $rule->teacherProfile?->user?->name ?? '—' }}
                                    </td>
                                    <td class="sch-td" style="font-weight: 700;">
                                        {{ $rule->studentUser?->name ?? __('General Course Cohort') }}
                                    </td>
                                    <td class="sch-td">
                                        <span class="sch-badge sch-badge-active">{{ ucfirst($rule->recurrence_type) }}</span>
                                    </td>
                                    <td class="sch-td" style="font-weight: 700;">
                                        {{ $rule->start_time }} ({{ $rule->duration_minutes }}m)
                                    </td>
                                    <td class="sch-td" style="font-weight: 900; color: #0D9488;">
                                        {{ $rule->live_sessions_count }} {{ __('sessions') }}
                                    </td>
                                    <td class="sch-td">
                                        <span class="sch-badge {{ $rule->status === 'active' ? 'sch-badge-active' : 'sch-badge-scheduled' }}">
                                            {{ $rule->status === 'active' ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="sch-td" style="text-align: end;">
                                        <button type="button" wire:click="deleteRecurringRule({{ $rule->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this recurring schedule rule and cancel its pending future sessions?') }}"
                                            style="background: #FEF2F2; color: #E11D48; border: 1px solid #FECDD3; border-radius: 0.625rem; padding: 0.4rem 0.85rem; font-weight: 800; font-size: 0.75rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 0.85rem; height: 0.85rem;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            <span>{{ __('Delete Rule') }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 3rem 1rem; color: var(--sch-text-muted);">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem auto; display: block;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <p style="font-weight: 700; margin: 0;">{{ __('No recurring schedule rules found.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ── 6. MODAL: SCHEDULE SINGLE SESSION ── --}}
        @if ($showCreateSessionModal)
            <div class="sch-modal-overlay">
                <div class="sch-modal-dialog">
                    <div style="padding: 1.25rem; border-bottom: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-weight: 900; font-size: 1.15rem; color: var(--sch-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #0D9488;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-6.75-3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm-3.75-3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                            </svg>
                            <span>{{ __('Schedule Live Session') }}</span>
                        </h3>
                        <button type="button" wire:click="closeCreateSessionModal" style="background: none; border: none; font-size: 1.25rem; font-weight: 800; color: var(--sch-text-muted); cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit="createSingleSession" style="display: flex; flex-direction: column; overflow: hidden; margin: 0; flex: 1;">
                        <div style="padding: 1.25rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 1rem;">
                            <div class="sch-form-group">
                                <label class="sch-label">{{ __('Course') }} *</label>
                                <select wire:model.live="singleCourseId" required class="sch-select">
                                    <option value="">{{ __('Select Course...') }}</option>
                                    @foreach ($this->courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->subject?->name }})</option>
                                    @endforeach
                                </select>
                                @error('singleCourseId')
                                    <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="sch-form-group">
                                <label class="sch-label">
                                    <span>{{ __('Student') }} ({{ __('Optional') }})</span>
                                    @if ($singleCourseId && $this->singleModalStudents->count() > 0)
                                        <span class="sch-badge sch-badge-active" style="padding: 0.1rem 0.4rem; font-size: 0.65rem;">
                                            {{ $this->singleModalStudents->count() }} {{ __('Enrolled in Course') }}
                                        </span>
                                    @endif
                                </label>
                                <select wire:model="singleStudentId" class="sch-select">
                                    <option value="">{{ __('General Course Cohort') }}</option>
                                    @foreach ($this->singleModalStudents as $stu)
                                        <option value="{{ $stu->id }}">{{ $stu->name }} ({{ $stu->studentProfile?->student_code ?? '#' . $stu->id }})</option>
                                    @endforeach
                                </select>
                                @error('singleStudentId')
                                    <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="sch-form-group">
                                <label class="sch-label">{{ __('Session Title') }} *</label>
                                <input type="text" wire:model="singleTitle" required placeholder="{{ __('Session Title') }}" class="sch-input">
                                @error('singleTitle')
                                    <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Scheduled Date & Time') }} *</label>
                                    <input type="datetime-local" wire:model="singleScheduledAt" required class="sch-input">
                                    @error('singleScheduledAt')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Duration') }} ({{ __('mins') }}) *</label>
                                    <input type="number" wire:model="singleDuration" min="15" max="300" required class="sch-input">
                                    @error('singleDuration')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="sch-form-group">
                                <label class="sch-label">{{ __('Meeting Broadcast Link') }} ({{ __('Optional') }})</label>
                                <input type="url" wire:model="singleMeetingLink" placeholder="https://..." class="sch-input">
                                @error('singleMeetingLink')
                                    <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.5rem; padding-top: 0.25rem;">
                                <input type="checkbox" wire:model="singleIsFreeDemo" id="singleIsFreeDemo" style="width: 1.1rem; height: 1.1rem; accent-color: #0D9488;">
                                <label for="singleIsFreeDemo" style="font-size: 0.8rem; font-weight: 700; color: var(--sch-text-secondary); cursor: pointer;">{{ __('Mark as Free Trial Demo Session') }}</label>
                            </div>
                        </div>

                        <div style="padding: 1rem 1.25rem; background: var(--sch-bg-surface-subtle); border-top: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" wire:click="closeCreateSessionModal" wire:loading.attr="disabled" wire:target="createSingleSession" class="sch-btn-secondary">{{ __('Cancel') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="createSingleSession" class="sch-btn-primary" style="min-width: 10rem; justify-content: center;">
                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="createSingleSession"></i>
                                <span wire:loading.remove wire:target="createSingleSession">{{ __('Schedule Session') }} &rarr;</span>
                                <span wire:loading wire:target="createSingleSession">{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── 7. MODAL: GENERATE FULL RECURRING SCHEDULE ── --}}
        @if ($showRecurringModal)
            <div class="sch-modal-overlay">
                <div class="sch-modal-dialog" style="max-width: 44rem; max-height: min(92vh, 52rem);">
                    <div style="padding: 1.25rem; border-bottom: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
                        <div>
                            <h3 style="font-weight: 900; font-size: 1.15rem; color: var(--sch-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-arrows-rotate text-teal-500"></i>
                                <span>{{ __('Generate Full Recurring Schedule') }}</span>
                            </h3>
                            <p style="font-size: 0.75rem; color: var(--sch-text-muted); margin: 0.15rem 0 0 0;">{{ __('Automatically populate weekly/monthly schedules for student(s).') }}</p>
                        </div>
                        <button type="button" wire:click="closeRecurringModal" style="background: none; border: none; font-size: 1.25rem; font-weight: 800; color: var(--sch-text-muted); cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit="submitRecurringSchedule" style="display: flex; flex-direction: column; overflow: hidden; margin: 0; flex: 1 1 auto; min-height: 0;">
                        <div style="padding: 1.25rem; overflow-y: auto; overscroll-behavior: contain; flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; gap: 1rem;">
                            <div class="sch-form-group">
                                <label class="sch-label">{{ __('Schedule Title') }} *</label>
                                <input type="text" wire:model="recTitle" required placeholder="{{ __('Schedule Title') }}" class="sch-input">
                                @error('recTitle')
                                    <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Course') }} *</label>
                                    <select wire:model.live="recCourseId" required class="sch-select">
                                        <option value="">{{ __('Select Course...') }}</option>
                                        @foreach ($this->courses as $c)
                                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('recCourseId')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Recurrence Pattern') }} *</label>
                                    <select wire:model.live="recType" class="sch-select">
                                        <option value="weekly">{{ __('Weekly') }}</option>
                                        <option value="monthly">{{ __('Monthly') }}</option>
                                        <option value="multi_month">{{ __('Multi-Month') }}</option>
                                        <option value="yearly">{{ __('Full Academic Year') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Student Multi-Selector --}}
                            <div class="sch-form-group">
                                <div class="sch-multi-picker">
                                    <div class="sch-multi-picker-head">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <label class="sch-label" style="margin: 0;">{{ __('Target Students') }}</label>
                                            @if (count($recStudentIds) > 0)
                                                <span class="sch-badge sch-badge-active" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">
                                                    {{ count($recStudentIds) }} {{ __('students selected') }}
                                                </span>
                                            @elseif ($recCourseId)
                                                <span class="sch-badge sch-badge-cohort" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">
                                                    {{ $this->filteredRecStudents->count() }} {{ __('Enrolled in Course') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="sch-multi-picker-actions">
                                            <button type="button" wire:click="selectAllRecStudents" class="sch-picker-btn">
                                                {{ __('Select All') }}
                                            </button>
                                            <button type="button" wire:click="clearAllRecStudents" class="sch-picker-btn">
                                                {{ __('Clear Selection') }}
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Search inside students list --}}
                                    <div class="sch-multi-search-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                        <input type="text" wire:model.live.debounce.200ms="recStudentSearch"
                                            placeholder="{{ __('Search students by name or code...') }}"
                                            class="sch-multi-search-input">
                                    </div>

                                    {{-- Scrollable Checkbox List --}}
                                    <div class="sch-multi-list">
                                        @forelse ($this->filteredRecStudents as $stu)
                                            @php
                                                $isSelected = in_array($stu->id, $recStudentIds);
                                            @endphp
                                            <label class="sch-multi-item {{ $isSelected ? 'is-selected' : '' }}">
                                                <input type="checkbox" value="{{ $stu->id }}" wire:model.live="recStudentIds">
                                                <span class="sch-multi-item-name">{{ $stu->name }}</span>
                                                <span class="sch-multi-item-code">#{{ $stu->studentProfile?->student_code ?? $stu->id }}</span>
                                            </label>
                                        @empty
                                            <div style="padding: 0.75rem; text-align: center; color: var(--sch-text-muted); font-size: 0.775rem;">
                                                {{ __('No students match your search.') }}
                                            </div>
                                        @endforelse
                                    </div>

                                    <span style="font-size: 0.7rem; color: var(--sch-text-muted); margin-top: 0.15rem;">
                                        {{ __('Select multiple students, or leave empty for general cohort.') }}
                                    </span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Start Time') }} *</label>
                                    <input type="time" wire:model="recStartTime" required class="sch-input">
                                    @error('recStartTime')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="sch-form-group">
                                    <label class="sch-label">{{ __('Start Date') }} *</label>
                                    <input type="date" wire:model.live="recStartDate" required class="sch-input">
                                    @error('recStartDate')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="sch-form-group">
                                    <label class="sch-label">
                                        <span>{{ __('End Date') }} *</span>
                                    </label>
                                    <input type="date" wire:model="recEndDate" required class="sch-input">
                                    <span style="font-size: 0.65rem; color: #0D9488; font-weight: 700;">{{ __('Cycle End Date (Auto-Calculated)') }}</span>
                                    @error('recEndDate')
                                        <span style="color: #E11D48; font-size: 0.725rem; font-weight: 700;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="sch-form-group">
                                <label class="sch-label">{{ __('Meeting Link') }} ({{ __('Optional') }})</label>
                                <input type="url" wire:model="recMeetingLink" placeholder="https://..." class="sch-input">
                            </div>

                            {{-- Conflict Preview Button --}}
                            <div style="margin-top: 0.25rem;">
                                <button type="button" wire:click="previewRecurringSchedule" wire:loading.attr="disabled" class="sch-btn-secondary" style="width: 100%; justify-content: center; gap: 0.5rem; font-weight: 800; padding: 0.65rem 1rem;">
                                    <i class="fa-solid fa-magnifying-glass text-teal-500" wire:loading.remove wire:target="previewRecurringSchedule"></i>
                                    <i class="fa-solid fa-circle-notch fa-spin text-teal-500" wire:loading wire:target="previewRecurringSchedule"></i>
                                    <span>{{ __('Preview Generated Dates & Check Conflicts') }}</span>
                                </button>
                            </div>

                            @if ($recConflictWarning)
                                <div style="padding: 0.75rem 1rem; border-radius: 0.75rem; background: rgba(245, 158, 11, 0.15); border: 1.5px solid rgba(245, 158, 11, 0.4); color: #B45309; font-size: 0.8rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-500" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                                    <span>{{ $recConflictWarning }}</span>
                                </div>
                            @endif

                            @if (! empty($recPreviewList))
                                @php
                                    $conflictsCount = collect($recPreviewList)->where('has_conflict', true)->count();
                                @endphp
                                <div x-data x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'end' }); })" style="display: flex; flex-direction: column; gap: 0.5rem; flex-shrink: 0; margin-top: 0.25rem;">
                                    {{-- Preview Box Header --}}
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.25rem 0.2rem;">
                                        <span style="font-weight: 800; font-size: 0.85rem; color: var(--sch-text-primary); display: flex; align-items: center; gap: 0.4rem;">
                                            <i class="fa-solid fa-calendar-days text-teal-500"></i>
                                            <span>{{ __('Generated Schedule Dates Preview') }} ({{ count($recPreviewList) }})</span>
                                        </span>
                                        @if ($conflictsCount > 0)
                                            <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(225, 29, 72, 0.15); border: 1px solid rgba(225, 29, 72, 0.35); color: #E11D48; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                <span>{{ $conflictsCount }} {{ __('Conflicts Detected') }}</span>
                                            </span>
                                        @else
                                            <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #059669; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span>{{ __('All Dates Clear') }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Scrollable List of Preview Dates --}}
                                    <div class="sch-conflict-preview-scroll" style="background: var(--sch-bg-surface-subtle); border: 1.5px solid var(--sch-border); border-radius: 0.875rem; padding: 0.75rem; min-height: 180px; max-height: 300px; overflow-y: auto; overscroll-behavior: contain; display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach ($recPreviewList as $p)
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; border-radius: 0.6rem; transition: all 0.15s; background: {{ $p['has_conflict'] ? 'rgba(225, 29, 72, 0.1)' : 'var(--sch-bg-surface)' }}; border: 1px solid {{ $p['has_conflict'] ? 'rgba(225, 29, 72, 0.35)' : 'var(--sch-border)' }};">
                                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                                    <i class="{{ $p['has_conflict'] ? 'fa-solid fa-triangle-exclamation text-rose-500' : 'fa-solid fa-circle-check text-emerald-500' }}" style="font-size: 0.95rem;"></i>
                                                    <div>
                                                        <div style="font-weight: 800; font-size: 0.825rem; color: var(--sch-text-primary);">
                                                            {{ $p['date'] }} <span style="font-weight: 600; color: var(--sch-text-secondary);">({{ $p['day'] }})</span>
                                                        </div>
                                                        @if ($p['has_conflict'] && ! empty($p['conflict_reason']))
                                                            <div style="font-size: 0.72rem; color: #E11D48; font-weight: 700; margin-top: 0.1rem;">
                                                                {{ $p['conflict_reason'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <span style="color: var(--sch-text-secondary); font-family: ui-monospace, monospace; font-weight: 700; font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 0.15rem 0.45rem; border-radius: 0.35rem;">{{ $p['time'] }}</span>
                                                    <span style="font-weight: 900; font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 0.4rem; color: {{ $p['has_conflict'] ? '#E11D48' : '#059669' }}; background: {{ $p['has_conflict'] ? 'rgba(225, 29, 72, 0.15)' : 'rgba(16, 185, 129, 0.15)' }};">
                                                        {{ $p['has_conflict'] ? __('Conflict') : __('Available') }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="font-size: 0.7rem; color: var(--sch-text-muted); text-align: center;">
                                        <i class="fa-solid fa-arrows-up-down me-1"></i> {{ __('Scroll inside list to view all dates') }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div style="padding: 1rem 1.25rem; background: var(--sch-bg-surface-subtle); border-top: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" wire:click="closeRecurringModal" wire:loading.attr="disabled" wire:target="submitRecurringSchedule" class="sch-btn-secondary">{{ __('Cancel') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="submitRecurringSchedule" class="sch-btn-primary" style="min-width: 12rem; justify-content: center;">
                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="submitRecurringSchedule"></i>
                                <span wire:loading.remove wire:target="submitRecurringSchedule">{{ __('Generate Full Schedule') }} &rarr;</span>
                                <span wire:loading wire:target="submitRecurringSchedule">{{ __('Generating Sessions...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── 8. MODAL: RESCHEDULE SESSION ── --}}
        @if ($showRescheduleModal)
            <div class="sch-modal-overlay">
                <div class="sch-modal-dialog" style="max-width: 28rem;">
                    <div style="padding: 1.25rem; border-bottom: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-weight: 900; font-size: 1.15rem; color: var(--sch-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #2563EB;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>{{ __('Reschedule Session') }}</span>
                        </h3>
                        <button type="button" wire:click="$set('showRescheduleModal', false)" style="background: none; border: none; font-size: 1.25rem; font-weight: 800; color: var(--sch-text-muted); cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit="confirmReschedule" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; margin: 0;">
                        <div class="sch-form-group">
                            <label class="sch-label">{{ __('New Scheduled Date & Time') }} *</label>
                            <input type="datetime-local" wire:model="rescheduleNewDate" required class="sch-input">
                        </div>

                        <div class="sch-form-group">
                            <label class="sch-label">{{ __('Reason (Audit Log)') }}</label>
                            <input type="text" wire:model="rescheduleReason" placeholder="{{ __('Reason') }}" class="sch-input">
                        </div>

                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                            <button type="button" wire:click="$set('showRescheduleModal', false)" wire:loading.attr="disabled" wire:target="confirmReschedule" class="sch-btn-secondary">{{ __('Cancel') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="confirmReschedule" class="sch-btn-primary" style="background: #2563EB;">
                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="confirmReschedule"></i>
                                <span wire:loading.remove wire:target="confirmReschedule">{{ __('Confirm Reschedule') }}</span>
                                <span wire:loading wire:target="confirmReschedule">{{ __('Processing...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── 9. MODAL: CANCEL SESSION ── --}}
        @if ($showCancelModal)
            <div class="sch-modal-overlay">
                <div class="sch-modal-dialog" style="max-width: 28rem;">
                    <div style="padding: 1.25rem; border-bottom: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-weight: 900; font-size: 1.15rem; color: #E11D48; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #E11D48;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>{{ __('Cancel Session') }}</span>
                        </h3>
                        <button type="button" wire:click="$set('showCancelModal', false)" style="background: none; border: none; font-size: 1.25rem; font-weight: 800; color: var(--sch-text-muted); cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit="confirmCancel" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; margin: 0;">
                        <p style="font-size: 0.85rem; font-weight: 600; color: var(--sch-text-secondary); margin: 0;">
                            {{ __('Are you sure you want to cancel this session? The student and teacher will be alerted.') }}
                        </p>

                        <div class="sch-form-group">
                            <label class="sch-label">{{ __('Cancellation Reason') }} *</label>
                            <input type="text" wire:model="cancelReason" required placeholder="{{ __('Cancellation Reason') }}" class="sch-input">
                        </div>

                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                            <button type="button" wire:click="$set('showCancelModal', false)" wire:loading.attr="disabled" wire:target="confirmCancel" class="sch-btn-secondary">{{ __('Keep Session') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="confirmCancel" style="background: #E11D48; color: #FFFFFF; border: none; border-radius: 0.75rem; padding: 0.65rem 1.25rem; font-weight: 800; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="confirmCancel"></i>
                                <span wire:loading.remove wire:target="confirmCancel">{{ __('Cancel Session') }}</span>
                                <span wire:loading wire:target="confirmCancel">{{ __('Cancelling...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ── 10. MODAL: EDIT STREAM LINK ── --}}
        @if ($showLinkModal)
            <div class="sch-modal-overlay">
                <div class="sch-modal-dialog" style="max-width: 28rem;">
                    <div style="padding: 1.25rem; border-bottom: 1.5px solid var(--sch-border); display: flex; align-items: center; justify-content: space-between;">
                        <h3 style="font-weight: 900; font-size: 1.15rem; color: var(--sch-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1.25rem; height: 1.25rem; color: #0D9488;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <span>{{ __('Update Stream / Meeting URL') }}</span>
                        </h3>
                        <button type="button" wire:click="$set('showLinkModal', false)" style="background: none; border: none; font-size: 1.25rem; font-weight: 800; color: var(--sch-text-muted); cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit="saveMeetingLink" style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; margin: 0;">
                        <div class="sch-form-group">
                            <label class="sch-label">{{ __('Meeting Link') }} *</label>
                            <input type="url" wire:model="linkUrl" required placeholder="https://..." class="sch-input">
                        </div>

                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                            <button type="button" wire:click="$set('showLinkModal', false)" wire:loading.attr="disabled" wire:target="saveMeetingLink" class="sch-btn-secondary">{{ __('Cancel') }}</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="saveMeetingLink" class="sch-btn-primary">
                                <i class="fa-solid fa-circle-notch fa-spin" wire:loading wire:target="saveMeetingLink"></i>
                                <span wire:loading.remove wire:target="saveMeetingLink">{{ __('Save Link') }}</span>
                                <span wire:loading wire:target="saveMeetingLink">{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
