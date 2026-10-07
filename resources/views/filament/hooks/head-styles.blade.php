<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* -------------------------------------------------------------
       ELITE ACADEMY — LUXURY ADMIN PANEL DESIGN SYSTEM
       ------------------------------------------------------------- */
    :root {
        --font-family: "Cairo", sans-serif !important;
        --default-font-family: "Cairo", sans-serif !important;
        --font-family-english: "Cairo", sans-serif;
        --font-family-arabic: "Cairo", sans-serif;
        --font-family-mono: "JetBrains Mono", monospace;
        --primary-teal: #0D9488;
        --primary-teal-dark: #0F766E;
        --primary-teal-light: #14B8A6;
        --accent-emerald: #10B981;
        --bg-slate-light: #F8FAFC;
    }

    html, body, button, input, select, textarea, table, th, td, label,
    .fi-body, .fi-panel, .fi-modal, .fi-dropdown, .fi-input, .fi-btn,
    .fi-sidebar, .fi-header, .fi-badge, .fi-avatar, .fi-breadcrumbs,
    .fi-section, .fi-widget, .fi-table {
        font-family: 'Cairo', sans-serif !important;
    }

    /* --- Background & Overall Canvas --- */
    html:not(.dark) .fi-body,
    html:not(.dark) .fi-main-ctn {
        background-color: #F8FAFC !important;
    }

    /* --- Sidebar: Universal Layout & Geometry --- */
    .fi-sidebar,
    #fi-main-sidebar,
    div.fi-sidebar {
        transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease !important;
    }

    .fi-sidebar-header {
        padding: 1.25rem 1rem !important;
        transition: background-color 0.25s ease, border-color 0.25s ease !important;
    }

    .fi-sidebar-header a img {
        filter: drop-shadow(0 2px 8px rgba(13, 148, 136, 0.3));
    }

    .fi-sidebar-group-label {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        margin-top: 0.75rem !important;
        margin-bottom: 0.25rem !important;
        transition: color 0.2s ease !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 0.875rem !important;
        padding: 0.65rem 0.875rem !important;
        font-weight: 700 !important;
        font-size: 0.825rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .fi-sidebar-item-btn:hover {
        transform: translateX(-2px);
    }
    html[dir="rtl"] .fi-sidebar-item-btn:hover {
        transform: translateX(2px) !important;
    }

    .fi-sidebar-item-icon {
        transition: transform 0.2s ease, color 0.2s ease !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
        transform: scale(1.12) !important;
    }

    /* --- ACTIVE SIDEBAR ITEM: 100% PURE WHITE TEXT & ICON IN BOTH LIGHT AND DARK MODES --- */
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar-item.fi-active > a,
    .fi-sidebar-item.fi-active > button,
    .fi-sidebar-item-btn.fi-active,
    .fi-sidebar-item-btn[aria-current="page"] {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
        color: #FFFFFF !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn *,
    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon,
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon svg,
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon i,
    .fi-sidebar-item-btn.fi-active *,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-icon,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-icon svg,
    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-icon i,
    .fi-sidebar-item-btn[aria-current="page"] *,
    .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label,
    .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon,
    .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon svg,
    .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon i {
        color: #FFFFFF !important;
        fill: #FFFFFF !important;
        stroke: #FFFFFF !important;
        font-weight: 800 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn:hover,
    .fi-sidebar-item-btn.fi-active:hover,
    .fi-sidebar-item-btn[aria-current="page"]:hover {
        background: linear-gradient(135deg, #0F766E 0%, #115E59 100%) !important;
        color: #FFFFFF !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn:hover *,
    .fi-sidebar-item-btn.fi-active:hover *,
    .fi-sidebar-item-btn[aria-current="page"]:hover * {
        color: #FFFFFF !important;
        fill: #FFFFFF !important;
    }

    /* ══════════════════════════════════════════════════════════════════════════
       LIGHT MODE: SIDEBAR & TOPBAR THEME (Crisp White Canvas, Dark Slate Text)
       ══════════════════════════════════════════════════════════════════════════ */
    html:not(.dark) .fi-sidebar,
    html:not(.dark) #fi-main-sidebar,
    html:not(.dark) div.fi-sidebar {
        background-color: #FFFFFF !important;
        border-right: 1px solid #E2E8F0 !important;
        border-left: 1px solid #E2E8F0 !important;
        box-shadow: 2px 0 16px rgba(0, 0, 0, 0.04) !important;
    }

    html:not(.dark) .fi-sidebar-header {
        border-bottom: 1px solid #F1F5F9 !important;
        background-color: #FFFFFF !important;
    }

    html:not(.dark) .fi-sidebar-group-label {
        color: #64748B !important;
    }

    html:not(.dark) .fi-sidebar-group-collapse-btn {
        color: #94A3B8 !important;
    }
    html:not(.dark) .fi-sidebar-group-collapse-btn:hover {
        color: #0F172A !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) {
        color: #334155 !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-label,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) .fi-sidebar-item-label {
        color: #334155 !important;
        font-weight: 700 !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-icon,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) .fi-sidebar-item-icon {
        color: #0D9488 !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover {
        background-color: rgba(13, 148, 136, 0.08) !important;
        color: #0F766E !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover .fi-sidebar-item-label {
        color: #0F766E !important;
    }

    html:not(.dark) .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover .fi-sidebar-item-icon,
    html:not(.dark) .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover .fi-sidebar-item-icon {
        color: #0D9488 !important;
    }

    html:not(.dark) .fi-sidebar-footer {
        border-top: 1px solid #F1F5F9 !important;
        background-color: #F8FAFC !important;
    }

    /* --- Light Mode: Topbar / Header --- */
    html:not(.dark) header.fi-topbar {
        background: rgba(255, 255, 255, 0.94) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
    }

    html:not(.dark) .fi-topbar-nav {
        padding: 0.5rem 1.5rem !important;
    }

    html:not(.dark) .fi-topbar .fi-icon-btn {
        color: #475569 !important;
    }
    html:not(.dark) .fi-topbar .fi-icon-btn:hover {
        color: #0D9488 !important;
        background-color: #F1F5F9 !important;
    }

    html:not(.dark) .fi-global-search-input-ctn input {
        border-radius: 1rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background-color: #F1F5F9 !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        transition: all 0.2s ease !important;
    }

    html:not(.dark) .fi-global-search-input-ctn input:focus {
        background-color: #FFFFFF !important;
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15) !important;
    }

    /* --- Page Headers & Breadcrumbs --- */
    .fi-header-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.65rem !important;
        color: #0F172A !important;
        letter-spacing: -0.02em !important;
    }

    .fi-header-subheading {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #64748B !important;
    }

    .fi-breadcrumbs-item-label {
        font-weight: 700 !important;
        font-size: 0.8rem !important;
    }

    /* --- Stats & Metric Widgets --- */
    .fi-wi-stats-overview-stat {
        border-radius: 1.5rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow: hidden !important;
    }

    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 20px 30px -10px rgba(13, 148, 136, 0.12), 0 10px 10px -5px rgba(0, 0, 0, 0.03) !important;
        border-color: #99F6E4 !important;
    }

    .fi-wi-stats-overview-stat-value {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 2rem !important;
        color: #0F172A !important;
    }

    .fi-wi-stats-overview-stat-label {
        font-weight: 800 !important;
        font-size: 0.85rem !important;
        color: #475569 !important;
    }

    .fi-wi-stats-overview-stat-description {
        font-weight: 600 !important;
        font-size: 0.775rem !important;
        color: #64748B !important;
    }

    /* --- Filament Tables --- */
    .fi-ta-ctn {
        border-radius: 1.5rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03) !important;
        overflow: hidden !important;
    }

    .fi-ta-header {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid #E2E8F0 !important;
    }

    .fi-ta-header-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.25rem !important;
        color: #0F172A !important;
    }

    .fi-ta-content-ctn,
    .fi-ta-content {
        overflow-x: auto !important;
        max-width: 100% !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(148, 163, 184, 0.4) transparent !important;
    }

    .fi-ta-content-ctn::-webkit-scrollbar,
    .fi-ta-content::-webkit-scrollbar {
        height: 7px !important;
    }

    .fi-ta-content-ctn::-webkit-scrollbar-track,
    .fi-ta-content::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-ta-content-ctn::-webkit-scrollbar-thumb,
    .fi-ta-content::-webkit-scrollbar-thumb {
        background-color: rgba(148, 163, 184, 0.4) !important;
        border-radius: 9999px !important;
    }

    .fi-ta-content-ctn::-webkit-scrollbar-thumb:hover,
    .fi-ta-content::-webkit-scrollbar-thumb:hover {
        background-color: rgba(148, 163, 184, 0.7) !important;
    }

    .fi-ta-table {
        width: 100% !important;
        min-width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        table-layout: auto !important;
    }

    .fi-ta-table thead,
    .fi-ta-table thead tr {
        background-color: #F8FAFC !important;
    }

    .fi-ta-table thead th,
    .fi-ta-header-cell,
    .fi-ta-actions-header-cell,
    .fi-ta-empty-header-cell,
    .fi-ta-selection-cell {
        background-color: #F8FAFC !important;
        padding: 0.875rem 1rem !important;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
        color: #475569 !important;
        border-bottom: 1.5px solid #E2E8F0 !important;
        vertical-align: middle !important;
    }

    /* Actions Column Header & Complete Header Styling */
    .fi-ta-actions-header-cell {
        text-align: end !important;
        white-space: nowrap !important;
        width: 1% !important;
    }

    .fi-ta-actions-header-cell::after {
        content: "{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'ACTIONS' }}";
        display: inline-block;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
        color: #475569 !important;
    }

    .fi-ta-row {
        transition: background-color 0.15s ease !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(241, 245, 249, 0.6) !important;
    }

    .fi-ta-cell {
        padding: 0.875rem 1rem !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #1E293B !important;
        border-bottom: 1px solid #F1F5F9 !important;
        vertical-align: middle !important;
    }

    /* Column Responsive Protection - Prevent primary text/title columns from collapsing into 1-word columns */
    .fi-ta-table th.fi-ta-header-cell-title,
    .fi-ta-table td.fi-ta-cell-title,
    .fi-ta-table th[class*="header-cell-title"],
    .fi-ta-table td[class*="cell-title"],
    .fi-ta-table th[class*="header-cell-name"],
    .fi-ta-table td[class*="cell-name"],
    .fi-ta-table td[class*="cell-message"],
    .fi-ta-table th[class*="header-cell-message"],
    .fi-ta-table th.fi-wrapped,
    .fi-ta-table td.fi-wrapped,
    .fi-ta-table th:has(.fi-wrapped),
    .fi-ta-table td:has(.fi-wrapped) {
        min-width: 250px !important;
    }

    /* Badges inside cells should be tidy and not monopolize table width */
    .fi-ta-cell .fi-badge {
        max-width: 280px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    /* Ensure date and scheduled time columns don't break awkwardly */
    .fi-ta-cell[class*="scheduled-at"],
    .fi-ta-header-cell[class*="scheduled-at"],
    .fi-ta-cell[class*="created-at"],
    .fi-ta-header-cell[class*="created-at"],
    .fi-ta-cell[class*="date"],
    .fi-ta-header-cell[class*="date"] {
        white-space: nowrap !important;
        min-width: 155px !important;
    }

    /* Actions Column Content - Tight, elegant, no dead space */
    .fi-ta-actions-cell {
        width: 1% !important;
        white-space: nowrap !important;
        text-align: end !important;
        padding: 0.875rem 1rem !important;
    }

    .fi-ta-actions-cell > .fi-ta-actions,
    .fi-ta-actions-cell .fi-ta-actions {
        display: inline-flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 0.5rem !important;
        white-space: nowrap !important;
    }

    .fi-ta-actions .fi-btn {
        white-space: nowrap !important;
        transition: all 0.2s ease !important;
    }

    [dir="rtl"] .fi-ta-actions-header-cell,
    [dir="rtl"] .fi-ta-actions-cell {
        text-align: left !important;
    }

    [dir="rtl"] .fi-ta-actions-cell > .fi-ta-actions,
    [dir="rtl"] .fi-ta-actions-cell .fi-ta-actions {
        justify-content: flex-start !important;
    }

    [dir="ltr"] .fi-ta-actions-header-cell,
    [dir="ltr"] .fi-ta-actions-cell {
        text-align: right !important;
    }

    [dir="ltr"] .fi-ta-actions-cell > .fi-ta-actions,
    [dir="ltr"] .fi-ta-actions-cell .fi-ta-actions {
        justify-content: flex-end !important;
    }

    /* --- Form Sections & Cards --- */
    .fi-section {
        border-radius: 1.5rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03) !important;
        overflow: visible !important;
    }

    .fi-section-header {
        border-bottom: 1px solid #F1F5F9 !important;
        padding: 1.25rem 1.5rem !important;
        border-top-left-radius: inherit !important;
        border-top-right-radius: inherit !important;
    }

    .fi-section-header-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.15rem !important;
        color: #0F172A !important;
    }

    /* --- Inputs, Selects & Form Controls --- */
    input.fi-input, select.fi-select-input, textarea.fi-input, .fi-select-input-btn {
        border-radius: 0.875rem !important;
        border: 1.5px solid #CBD5E1 !important;
        background-color: #FFFFFF !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        transition: all 0.2s ease !important;
        padding: 0.65rem 0.875rem !important;
    }

    input.fi-input:focus, select.fi-select-input:focus, textarea.fi-input:focus, .fi-select-input-btn:focus, .fi-select-input-btn[aria-expanded="true"] {
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18) !important;
        outline: none !important;
    }

    .fi-fo-field-wrp-label label {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.8rem !important;
        color: #334155 !important;
        margin-bottom: 0.35rem !important;
    }

    /* --- Buttons: Luxury Action Buttons --- */
    .fi-btn {
        border-radius: 0.875rem !important;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.825rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
    }

    .fi-btn:hover {
        transform: translateY(-1px) !important;
    }

    .fi-btn-primary {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3) !important;
        border: none !important;
    }

    .fi-btn-primary:hover {
        background: linear-gradient(135deg, #0F766E 0%, #115E59 100%) !important;
        box-shadow: 0 6px 18px rgba(13, 148, 136, 0.4) !important;
    }

    /* --- Status Badges --- */
    .fi-badge {
        border-radius: 0.625rem !important;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.725rem !important;
        padding: 0.25rem 0.65rem !important;
        border: 1px solid transparent !important;
    }

    /* --- Modals & Dialogs --- */
    .fi-modal-window {
        border-radius: 1.75rem !important;
        border: 1.5px solid #E2E8F0 !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        overflow: hidden !important;
    }

    .fi-modal-header {
        border-bottom: 1px solid #F1F5F9 !important;
        padding: 1.5rem !important;
    }

    .fi-modal-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.35rem !important;
        color: #0F172A !important;
    }

    /* --- Pagination --- */
    .fi-pagination-item-btn {
        border-radius: 0.625rem !important;
        font-weight: 800 !important;
        font-size: 0.775rem !important;
    }

    /* --- Dark Mode Custom Overrides --- */
    html.dark .fi-body,
    html.dark .fi-main-ctn {
        background-color: #0B1120 !important;
    }

    /* ══════════════════════════════════════════════════════════════════════════
       DARK MODE: SIDEBAR & TOPBAR THEME (Deep Navy Canvas, Crisp Mint Text)
       ══════════════════════════════════════════════════════════════════════════ */
    html.dark .fi-sidebar,
    html.dark #fi-main-sidebar,
    html.dark div.fi-sidebar {
        background: linear-gradient(180deg, #0F172A 0%, #020617 100%) !important;
        border-right: 1px solid rgba(51, 65, 85, 0.6) !important;
        border-left: 1px solid rgba(51, 65, 85, 0.6) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.35) !important;
    }

    html.dark .fi-sidebar-header {
        border-bottom: 1px solid rgba(51, 65, 85, 0.6) !important;
        background: rgba(15, 23, 42, 0.7) !important;
    }

    html.dark .fi-sidebar-group-label {
        color: #94A3B8 !important;
    }

    html.dark .fi-sidebar-group-collapse-btn {
        color: #64748B !important;
    }
    html.dark .fi-sidebar-group-collapse-btn:hover {
        color: #E2E8F0 !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) {
        color: #CBD5E1 !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-label,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) .fi-sidebar-item-label {
        color: #CBD5E1 !important;
        font-weight: 700 !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-icon,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]) .fi-sidebar-item-icon {
        color: #2DD4BF !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover {
        background-color: rgba(15, 118, 110, 0.25) !important;
        color: #5EEAD4 !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover .fi-sidebar-item-label {
        color: #5EEAD4 !important;
    }

    html.dark .fi-sidebar-item:not(.fi-active) > .fi-sidebar-item-btn:hover .fi-sidebar-item-icon,
    html.dark .fi-sidebar-item-btn:not(.fi-active):not([aria-current="page"]):hover .fi-sidebar-item-icon {
        color: #5EEAD4 !important;
    }

    html.dark .fi-sidebar-footer {
        border-top: 1px solid rgba(51, 65, 85, 0.6) !important;
        background: rgba(2, 6, 23, 0.7) !important;
    }

    /* --- Dark Mode: Topbar / Header --- */
    html.dark header.fi-topbar {
        background: rgba(15, 23, 42, 0.92) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.7) !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    }

    html.dark .fi-topbar-nav {
        padding: 0.5rem 1.5rem !important;
    }

    html.dark .fi-topbar .fi-icon-btn {
        color: #94A3B8 !important;
    }
    html.dark .fi-topbar .fi-icon-btn:hover {
        color: #2DD4BF !important;
        background-color: rgba(51, 65, 85, 0.5) !important;
    }

    /* --- Dropdowns & Flyouts (Dual Mode) --- */
    html:not(.dark) .fi-dropdown-panel {
        background-color: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
        border-radius: 1rem !important;
    }
    html:not(.dark) .fi-dropdown-list-item-btn {
        color: #334155 !important;
        font-weight: 700 !important;
    }
    html:not(.dark) .fi-dropdown-list-item-btn:hover {
        background-color: #F1F5F9 !important;
        color: #0F766E !important;
    }

    html.dark .fi-dropdown-panel {
        background-color: #0F172A !important;
        border: 1px solid rgba(51, 65, 85, 0.8) !important;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5) !important;
        border-radius: 1rem !important;
    }
    html.dark .fi-dropdown-list-item-btn {
        color: #CBD5E1 !important;
        font-weight: 700 !important;
    }
    html.dark .fi-dropdown-list-item-btn:hover {
        background-color: rgba(30, 41, 59, 0.8) !important;
        color: #5EEAD4 !important;
    }

    /* --- Breadcrumbs (Dual Mode) --- */
    html:not(.dark) .fi-breadcrumbs-item-label {
        color: #64748B !important;
        font-weight: 700 !important;
    }
    html:not(.dark) .fi-breadcrumbs-item-label:hover {
        color: #0D9488 !important;
    }
    html.dark .fi-breadcrumbs-item-label {
        color: #94A3B8 !important;
        font-weight: 700 !important;
    }
    html.dark .fi-breadcrumbs-item-label:hover {
        color: #2DD4BF !important;
    }

    html.dark .fi-global-search-input-ctn input {
        background-color: #1E293B !important;
        border-color: rgba(51, 65, 85, 0.9) !important;
        color: #F8FAFC !important;
    }

    html.dark .fi-global-search-input-ctn input:focus {
        background-color: #0F172A !important;
        border-color: #14B8A6 !important;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2) !important;
    }

    html.dark .fi-header-heading {
        color: #F8FAFC !important;
    }

    html.dark .fi-header-subheading {
        color: #94A3B8 !important;
    }

    html.dark .fi-wi-stats-overview-stat {
        background: #0F172A !important;
        border-color: rgba(51, 65, 85, 0.7) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
    }

    html.dark .fi-wi-stats-overview-stat:hover {
        border-color: #14B8A6 !important;
        box-shadow: 0 20px 30px -10px rgba(13, 148, 136, 0.25) !important;
    }

    html.dark .fi-wi-stats-overview-stat-value {
        color: #F8FAFC !important;
    }

    html.dark .fi-wi-stats-overview-stat-label {
        color: #94A3B8 !important;
    }

    html.dark .fi-ta-ctn {
        background: #0F172A !important;
        border-color: rgba(51, 65, 85, 0.7) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
    }

    html.dark .fi-ta-table thead,
    html.dark .fi-ta-table thead tr,
    html.dark .fi-ta-table thead th,
    html.dark .fi-ta-header-cell,
    html.dark .fi-ta-actions-header-cell,
    html.dark .fi-ta-empty-header-cell,
    html.dark .fi-ta-selection-cell {
        background-color: #1E293B !important;
        color: #94A3B8 !important;
        border-bottom: 1.5px solid rgba(51, 65, 85, 0.8) !important;
    }

    html.dark .fi-ta-actions-header-cell::after {
        color: #94A3B8 !important;
    }

    html.dark .fi-ta-header-heading {
        color: #F8FAFC !important;
    }

    html.dark .fi-ta-row:hover {
        background-color: rgba(30, 41, 59, 0.6) !important;
    }

    html.dark .fi-ta-cell {
        color: #E2E8F0 !important;
        border-bottom-color: rgba(30, 41, 59, 0.8) !important;
    }

    html.dark .fi-ta-content-ctn,
    html.dark .fi-ta-content {
        scrollbar-color: rgba(100, 116, 139, 0.5) transparent !important;
    }

    html.dark .fi-ta-content-ctn::-webkit-scrollbar-thumb,
    html.dark .fi-ta-content::-webkit-scrollbar-thumb {
        background-color: rgba(100, 116, 139, 0.4) !important;
    }

    html.dark .fi-ta-content-ctn::-webkit-scrollbar-thumb:hover,
    html.dark .fi-ta-content::-webkit-scrollbar-thumb:hover {
        background-color: rgba(100, 116, 139, 0.7) !important;
    }

    html.dark .fi-section {
        background: #0F172A !important;
        border-color: rgba(51, 65, 85, 0.7) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
    }

    html.dark .fi-section-header {
        border-bottom-color: rgba(30, 41, 59, 0.8) !important;
    }

    html.dark .fi-section-header-heading {
        color: #F8FAFC !important;
    }

    html.dark input.fi-input, 
    html.dark select.fi-select-input, 
    html.dark textarea.fi-input,
    html.dark .fi-select-input-btn {
        background-color: #1E293B !important;
        border-color: rgba(71, 85, 105, 0.9) !important;
        color: #F8FAFC !important;
    }

    html.dark input.fi-input:focus, 
    html.dark select.fi-select-input:focus, 
    html.dark textarea.fi-input:focus,
    html.dark .fi-select-input-btn:focus,
    html.dark .fi-select-input-btn[aria-expanded="true"] {
        border-color: #14B8A6 !important;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.22) !important;
    }

    html.dark .fi-fo-field-wrp-label label {
        color: #CBD5E1 !important;
    }

    html.dark .fi-modal-window {
        background: #0B132B !important;
        border-color: rgba(51, 65, 85, 0.7) !important;
        box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(20, 184, 166, 0.15) !important;
    }

    html.dark .fi-modal-header {
        border-bottom-color: rgba(30, 41, 59, 0.8) !important;
        background: rgba(11, 19, 43, 0.98) !important;
    }

    html.dark .fi-modal-heading {
        color: #F8FAFC !important;
    }

    /* -------------------------------------------------------------
       RESPONSIVE & LUXURY FILAMENT MODALS (UNIVERSAL FIX)
       ------------------------------------------------------------- */
    .fi-modal-window-ctn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1.25rem 1rem !important;
        min-height: 100% !important;
        overflow-y: auto !important;
    }

    .fi-modal-window {
        display: flex !important;
        flex-direction: column !important;
        max-height: min(calc(100vh - 3rem), calc(100dvh - 3rem)) !important;
        overflow: hidden !important;
        border-radius: 1.5rem !important;
        margin: auto !important;
        position: relative !important;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35) !important;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease !important;
    }

    /* Top Accent Line on Modals */
    .fi-modal-window::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0D9488 0%, #10B981 50%, #14B8A6 100%);
        z-index: 50;
    }

    /* Modal Width Classes Enhancement */
    .fi-modal-window.fi-width-2xl,
    .fi-modal-window.max-w-2xl {
        width: 100% !important;
        max-width: 48rem !important;
    }

    .fi-modal-window.fi-width-xl,
    .fi-modal-window.max-w-xl {
        width: 100% !important;
        max-width: 40rem !important;
    }

    .fi-modal-window.fi-width-lg,
    .fi-modal-window.max-w-lg {
        width: 100% !important;
        max-width: 34rem !important;
    }

    .fi-modal-window.fi-width-md,
    .fi-modal-window.max-w-md {
        width: 100% !important;
        max-width: 29rem !important;
    }

    .fi-modal-header {
        flex-shrink: 0 !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 30 !important;
        padding: 1.35rem 1.75rem !important;
        border-bottom: 1.5px solid rgba(226, 232, 240, 0.9) !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
    }

    .fi-modal-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.285rem !important;
        color: #0F172A !important;
        letter-spacing: -0.01em !important;
        line-height: 1.4 !important;
    }

    .fi-modal-description {
        font-family: 'Cairo', sans-serif !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #64748B !important;
        margin-top: 0.35rem !important;
        line-height: 1.55 !important;
    }

    html.dark .fi-modal-description {
        color: #94A3B8 !important;
    }

    .fi-modal-close-btn {
        border-radius: 9999px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .fi-modal-close-btn:hover {
        transform: rotate(90deg) scale(1.1) !important;
        background-color: rgba(239, 68, 68, 0.12) !important;
        color: #EF4444 !important;
    }

    .fi-modal-content {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        max-height: min(calc(100vh - 13.5rem), calc(100dvh - 13.5rem)) !important;
        padding: 1.75rem !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(13, 148, 136, 0.4) transparent !important;
        -webkit-overflow-scrolling: touch !important;
    }

    /* Suppress modal content scrollbar when dropdown is actively expanded */
    .fi-modal-content:has(.fi-select-input-btn[aria-expanded="true"]) {
        overflow-y: hidden !important;
    }

    .fi-modal-content::-webkit-scrollbar {
        width: 6px !important;
    }

    .fi-modal-content::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-modal-content::-webkit-scrollbar-thumb {
        background-color: rgba(13, 148, 136, 0.4) !important;
        border-radius: 9999px !important;
    }

    .fi-modal-content::-webkit-scrollbar-thumb:hover {
        background-color: rgba(13, 148, 136, 0.7) !important;
    }

    .fi-modal-footer {
        flex-shrink: 0 !important;
        position: sticky !important;
        bottom: 0 !important;
        z-index: 30 !important;
        padding: 1.15rem 1.75rem !important;
        border-top: 1.5px solid rgba(226, 232, 240, 0.9) !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
    }

    html.dark .fi-modal-footer {
        border-top: 1.5px solid rgba(30, 41, 59, 0.8) !important;
        background: rgba(11, 19, 43, 0.98) !important;
    }

    .fi-modal-footer-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.75rem !important;
        flex-wrap: wrap !important;
    }

    [dir="rtl"] .fi-modal-footer-actions {
        justify-content: flex-start !important;
    }

    /* Mobile Responsiveness for Modals */
    @media (max-width: 640px) {
        .fi-modal-window-ctn {
            padding: 0.35rem !important;
        }

        .fi-modal-window {
            max-height: calc(100dvh - 0.75rem) !important;
            width: calc(100vw - 0.75rem) !important;
            max-width: calc(100vw - 0.75rem) !important;
            border-radius: 1.15rem !important;
            margin: 0.35rem auto !important;
        }

        .fi-modal-header {
            padding: 1rem 1.15rem !important;
        }

        .fi-modal-heading {
            font-size: 1.1rem !important;
        }

        .fi-modal-content {
            padding: 1.15rem 1rem !important;
            max-height: calc(100dvh - 9rem) !important;
        }

        .fi-modal-footer {
            padding: 0.85rem 1rem !important;
        }

        .fi-modal-footer-actions {
            width: 100% !important;
            flex-direction: column-reverse !important;
            gap: 0.5rem !important;
        }

        .fi-modal-footer-actions > button,
        .fi-modal-footer-actions > .fi-btn {
            width: 100% !important;
            justify-content: center !important;
        }
    }

    /* --- Modern "See More..." / Simple Pagination Styling --- */
    .fi-pagination {
        padding: 0.75rem 1.25rem !important;
        align-items: center !important;
        gap: 0.75rem !important;
    }

    .fi-pagination.fi-simple {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }

    .fi-pagination-next-btn,
    .fi-pagination-previous-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-weight: 700 !important;
        font-family: 'Cairo', sans-serif !important;
        padding: 0.5rem 1.25rem !important;
        border-radius: 0.625rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .fi-pagination-next-btn {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(13, 148, 136, 0.3) !important;
    }

    .fi-pagination-next-btn:hover {
        background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%) !important;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3) !important;
        transform: translateY(-1px) !important;
        color: #FFFFFF !important;
    }

    .fi-pagination-next-btn:active {
        transform: translateY(0) !important;
    }

    .fi-pagination-previous-btn {
        background: rgba(241, 245, 249, 0.8) !important;
        color: #475569 !important;
        border: 1px solid rgba(203, 213, 225, 0.8) !important;
    }

    html.dark .fi-pagination-previous-btn {
        background: rgba(30, 41, 59, 0.8) !important;
        color: #CBD5E1 !important;
        border: 1px solid rgba(51, 65, 85, 0.8) !important;
    }

    .fi-pagination-previous-btn:hover {
        background: rgba(226, 232, 240, 0.9) !important;
        color: #0F172A !important;
    }

    html.dark .fi-pagination-previous-btn:hover {
        background: rgba(51, 65, 85, 0.9) !important;
        color: #FFFFFF !important;
    }

    /* -------------------------------------------------------------
       ELITE ACADEMY — RECURRING SCHEDULES & FORM UI (DARK & LIGHT)
       ------------------------------------------------------------- */

    /* --- Form Sections Modern Geometry --- */
    .fi-section {
        border-radius: 1.25rem !important;
        transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease !important;
    }

    html:not(.dark) .fi-section {
        background-color: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04) !important;
    }

    html.dark .fi-section {
        background-color: rgba(15, 23, 42, 0.72) !important;
        backdrop-filter: blur(14px) !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        box-shadow: 0 4px 24px -2px rgba(0, 0, 0, 0.25) !important;
    }

    .fi-section-header {
        padding: 1.1rem 1.4rem !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
    }

    html:not(.dark) .fi-section-header {
        border-bottom-color: #F1F5F9 !important;
    }

    .fi-section-header-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 1.05rem !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
    }

    .fi-section-header-description {
        font-family: 'Cairo', sans-serif !important;
        font-size: 0.775rem !important;
        color: #94A3B8 !important;
        margin-top: 0.2rem !important;
        line-height: 1.5 !important;
    }

    html:not(.dark) .fi-section-header-description {
        color: #64748B !important;
    }

    /* --- Recurring Days Picker (Never wrap Arabic text, pill card styling) --- */
    .ec-recurring-days-picker {
        padding: 0.35rem 0 0.85rem 0 !important;
    }

    .ec-recurring-days-picker .fi-fo-checkbox-list {
        gap: 0.75rem !important;
    }

    .ec-recurring-days-picker .fi-fo-checkbox-list-option {
        padding: 0.65rem 0.95rem !important;
        border-radius: 0.875rem !important;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        white-space: nowrap !important;
    }

    .ec-recurring-days-picker label,
    .ec-recurring-days-picker span {
        white-space: nowrap !important;
        word-break: keep-all !important;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.875rem !important;
    }

    /* Days Picker: Light Mode */
    html:not(.dark) .ec-recurring-days-picker .fi-fo-checkbox-list-option {
        background-color: #F8FAFC !important;
        border: 1.5px solid #E2E8F0 !important;
        color: #334155 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    html:not(.dark) .ec-recurring-days-picker .fi-fo-checkbox-list-option:hover {
        background-color: #F1F5F9 !important;
        border-color: #0D9488 !important;
        color: #0F766E !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 10px rgba(13, 148, 136, 0.12) !important;
    }

    html:not(.dark) .ec-recurring-days-picker .fi-fo-checkbox-list-option:has(input:checked) {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.12) 0%, rgba(16, 185, 129, 0.18) 100%) !important;
        border: 1.5px solid #0D9488 !important;
        color: #0F766E !important;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.18) !important;
    }

    /* Days Picker: Dark Mode */
    html.dark .ec-recurring-days-picker .fi-fo-checkbox-list-option {
        background-color: rgba(30, 41, 59, 0.6) !important;
        border: 1.5px solid rgba(148, 163, 184, 0.16) !important;
        color: #CBD5E1 !important;
    }

    html.dark .ec-recurring-days-picker .fi-fo-checkbox-list-option:hover {
        background-color: rgba(30, 41, 59, 0.9) !important;
        border-color: #14B8A6 !important;
        color: #2DD4BF !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2) !important;
    }

    html.dark .ec-recurring-days-picker .fi-fo-checkbox-list-option:has(input:checked) {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.28) 0%, rgba(15, 118, 110, 0.4) 100%) !important;
        border: 1.5px solid #14B8A6 !important;
        color: #2DD4BF !important;
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.3) !important;
    }

    /* --- Day Schedule Cards (.ec-day-schedule-card) --- */
    .ec-day-schedule-card {
        border-radius: 1rem !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .ec-day-schedule-card:hover {
        transform: translateY(-2px) !important;
    }

    html:not(.dark) .ec-day-schedule-card {
        background-color: #FFFFFF !important;
        border: 1.5px solid #E2E8F0 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04) !important;
    }

    html:not(.dark) .ec-day-schedule-card:hover {
        border-color: #0D9488 !important;
        box-shadow: 0 8px 24px rgba(13, 148, 136, 0.12) !important;
    }

    html.dark .ec-day-schedule-card {
        background-color: rgba(15, 23, 42, 0.65) !important;
        border: 1.5px solid rgba(148, 163, 184, 0.18) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2) !important;
    }

    html.dark .ec-day-schedule-card:hover {
        border-color: rgba(20, 184, 166, 0.5) !important;
        box-shadow: 0 8px 24px rgba(13, 148, 136, 0.25) !important;
    }

    /* Top accent bar on each active day schedule card */
    .ec-day-schedule-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: linear-gradient(90deg, #0D9488, #10B981, #14B8A6);
        opacity: 0.9;
    }

    /* Ensure TimePicker and input labels have ample room and no truncation */
    .ec-day-schedule-card .fi-input-wrp input {
        min-width: 0 !important;
        font-weight: 700 !important;
    }

    /* -------------------------------------------------------------
       FILAMENT LUXURY SELECT & SEARCHABLE DROPDOWNS (DUAL MODE)
       ------------------------------------------------------------- */
    /* Form Sections and wrappers must allow dropdowns to pop cleanly without clipping */
    .fi-section,
    .fi-section-content-ctn,
    .fi-section-content,
    .fi-fo-field-wrp,
    .fi-fo-select-wrp,
    .fi-fo-select {
        overflow: visible !important;
    }

    /* ELIMINATE DOUBLE BORDERS: Remove outer wrapper border/ring so the select trigger is the only styled container */
    .fi-fo-select > .fi-input-wrp,
    .fi-fo-select-wrp > .fi-input-wrp {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
        ring: none !important;
        --tw-ring-color: transparent !important;
        --tw-ring-shadow: none !important;
        --tw-shadow: none !important;
    }

    /* CRITICAL STACKING CONTEXT ELEVATION: When any select is open, elevate its parent section and field */
    .fi-section:has(.fi-select-input-btn[aria-expanded="true"]),
    .fi-section:has(.fi-dropdown-panel:not([style*="display: none"])),
    .fi-section.fi-select-open-elevated {
        position: relative !important;
        z-index: 9999 !important;
    }

    .fi-fo-field-wrp:has(.fi-select-input-btn[aria-expanded="true"]),
    .fi-fo-field-wrp:has(.fi-dropdown-panel:not([style*="display: none"])),
    .fi-fo-field-wrp.fi-select-open-elevated {
        position: relative !important;
        z-index: 10000 !important;
    }

    .fi-fo-select-wrp:has(.fi-select-input-btn[aria-expanded="true"]),
    .fi-fo-select:has(.fi-select-input-btn[aria-expanded="true"]) {
        position: relative !important;
        z-index: 10001 !important;
    }

    .fi-select-input-ctn:has(.fi-select-input-btn[aria-expanded="true"]) {
        position: relative !important;
        z-index: 10002 !important;
    }

    /* Ensure form actions (bottom submit buttons) stay under any open dropdown */
    .fi-form-actions,
    .fi-sc-actions,
    .fi-ac {
        position: relative !important;
        z-index: 10 !important;
    }

    /* Select Trigger Button Customization - Sleek Single Unified Border */
    .fi-select-input-btn {
        min-height: 2.85rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        font-family: 'Cairo', sans-serif !important;
        cursor: pointer !important;
        border-radius: 0.75rem !important;
        padding: 0.55rem 0.95rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    html:not(.dark) .fi-select-input-btn {
        background-color: #FFFFFF !important;
        border: 1.5px solid #CBD5E1 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
        color: #0F172A !important;
    }

    html:not(.dark) .fi-select-input-btn:hover {
        border-color: #0D9488 !important;
        box-shadow: 0 3px 10px rgba(13, 148, 136, 0.1) !important;
    }

    html:not(.dark) .fi-select-input-btn:focus,
    html:not(.dark) .fi-select-input-btn[aria-expanded="true"] {
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.18) !important;
        outline: none !important;
    }

    html.dark .fi-select-input-btn {
        background-color: #0F172A !important;
        border: 1.5px solid #334155 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4) !important;
        color: #F8FAFC !important;
    }

    html.dark .fi-select-input-btn:hover {
        border-color: #14B8A6 !important;
        box-shadow: 0 4px 14px rgba(20, 184, 166, 0.15) !important;
    }

    html.dark .fi-select-input-btn:focus,
    html.dark .fi-select-input-btn[aria-expanded="true"] {
        border-color: #2DD4BF !important;
        box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.25) !important;
        outline: none !important;
    }

    .fi-select-input-value-ctn {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        line-height: 1.45 !important;
    }

    html:not(.dark) .fi-select-input-value-ctn {
        color: #0F172A !important;
    }

    html.dark .fi-select-input-value-ctn {
        color: #F1F5F9 !important;
    }

    .fi-select-input-placeholder {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 500 !important;
        font-size: 0.85rem !important;
    }

    html:not(.dark) .fi-select-input-placeholder {
        color: #94A3B8 !important;
    }

    html.dark .fi-select-input-placeholder {
        color: #64748B !important;
    }

    /* Floating Dropdown Panel in Select - 100% Solid Opaque Elevated Card */
    .fi-select-input-ctn .fi-dropdown-panel {
        z-index: 999999 !important;
        border-radius: 1rem !important;
        padding: 0.45rem !important;
        max-height: min(24rem, 55vh) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(13, 148, 136, 0.5) transparent !important;
        transition: opacity 0.18s ease, transform 0.18s ease !important;
        box-sizing: border-box !important;
    }

    html:not(.dark) .fi-select-input-ctn .fi-dropdown-panel {
        background-color: #FFFFFF !important;
        border: 1.5px solid #E2E8F0 !important;
        box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(13, 148, 136, 0.15) !important;
    }

    html.dark .fi-select-input-ctn .fi-dropdown-panel {
        background-color: #0F172A !important;
        border: 1.5px solid #334155 !important;
        box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.95), 0 0 0 1px rgba(20, 184, 166, 0.3) !important;
    }

    /* Search Input Container in Dropdown - Clean & Integrated (NO Double Borders) */
    .fi-select-input-search-ctn {
        padding: 0.5rem 0.4rem 0.65rem 0.4rem !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 50 !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }

    html:not(.dark) .fi-select-input-search-ctn {
        background-color: #FFFFFF !important;
        border-bottom: 1px solid #E2E8F0 !important;
    }

    html.dark .fi-select-input-search-ctn {
        background-color: #0F172A !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.8) !important;
    }

    .fi-select-input-search-ctn input.fi-input {
        border-radius: 0.65rem !important;
        font-family: 'Cairo', sans-serif !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        padding: 0.5rem 0.875rem !important;
        height: 2.5rem !important;
        width: 100% !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    html:not(.dark) .fi-select-input-search-ctn input.fi-input {
        background-color: #F8FAFC !important;
        border: 1px solid #CBD5E1 !important;
        color: #0F172A !important;
    }

    html:not(.dark) .fi-select-input-search-ctn input.fi-input::placeholder {
        color: #94A3B8 !important;
    }

    html:not(.dark) .fi-select-input-search-ctn input.fi-input:focus {
        background-color: #FFFFFF !important;
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.15) !important;
        outline: none !important;
    }

    html.dark .fi-select-input-search-ctn input.fi-input {
        background-color: #1E293B !important;
        border: 1px solid #475569 !important;
        color: #F8FAFC !important;
    }

    html.dark .fi-select-input-search-ctn input.fi-input::placeholder {
        color: #94A3B8 !important;
    }

    html.dark .fi-select-input-search-ctn input.fi-input:focus {
        background-color: #0F172A !important;
        border-color: #2DD4BF !important;
        box-shadow: 0 0 0 2px rgba(45, 212, 191, 0.2) !important;
        outline: none !important;
    }

    /* Options List & Card Items */
    .fi-select-input-options-ctn,
    .fi-dropdown-list {
        padding: 0.35rem 0.15rem !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0.25rem !important;
    }

    .fi-select-input-option {
        border-radius: 0.65rem !important;
        padding: 0.65rem 0.875rem !important;
        margin: 0 !important;
        font-family: 'Cairo', sans-serif !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        line-height: 1.5 !important;
        cursor: pointer !important;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        border: 1px solid transparent !important;
    }

    html:not(.dark) .fi-select-input-option {
        color: #1E293B !important;
        background: transparent !important;
    }

    html:not(.dark) .fi-select-input-option:hover {
        background-color: rgba(13, 148, 136, 0.08) !important;
        color: #0F766E !important;
        transform: translateX(-2px);
    }

    html[dir="rtl"]:not(.dark) .fi-select-input-option:hover {
        transform: translateX(2px) !important;
    }

    html:not(.dark) .fi-select-input-option.fi-selected {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        box-shadow: 0 3px 10px rgba(13, 148, 136, 0.25) !important;
    }

    html.dark .fi-select-input-option {
        color: #E2E8F0 !important;
        background: transparent !important;
    }

    html.dark .fi-select-input-option:hover {
        background-color: rgba(20, 184, 166, 0.14) !important;
        color: #5EEAD4 !important;
        transform: translateX(-2px);
    }

    html[dir="rtl"].dark .fi-select-input-option:hover {
        transform: translateX(2px) !important;
    }

    html.dark .fi-select-input-option.fi-selected {
        background: linear-gradient(135deg, #0F766E 0%, #115E59 100%) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
    }

    /* Loading / Empty Message State */
    .fi-select-input-message {
        padding: 1.25rem 1rem !important;
        text-align: center !important;
        font-family: 'Cairo', sans-serif !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
    }

    html:not(.dark) .fi-select-input-message {
        color: #64748B !important;
    }

    html.dark .fi-select-input-message {
        color: #94A3B8 !important;
    }

    /* Option scrollbars */
    .fi-select-input-ctn .fi-dropdown-panel::-webkit-scrollbar {
        width: 5px !important;
        height: 5px !important;
    }

    .fi-select-input-ctn .fi-dropdown-panel::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-select-input-ctn .fi-dropdown-panel::-webkit-scrollbar-thumb {
        background-color: rgba(13, 148, 136, 0.45) !important;
        border-radius: 9999px !important;
    }

    .fi-select-input-ctn .fi-dropdown-panel::-webkit-scrollbar-thumb:hover {
        background-color: rgba(13, 148, 136, 0.75) !important;
    }



    /* FileUpload Component Dropzone Enhancement */
    .fi-fo-file-upload .filepond--panel-root {
        border-radius: 1rem !important;
        transition: all 0.25s ease !important;
    }

    html:not(.dark) .fi-fo-file-upload .filepond--panel-root {
        background-color: #F8FAFC !important;
        border: 2px dashed #CBD5E1 !important;
    }

    html:not(.dark) .fi-fo-file-upload:hover .filepond--panel-root {
        border-color: #0D9488 !important;
        background-color: #F0FDFA !important;
    }

    html.dark .fi-fo-file-upload .filepond--panel-root {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border: 2px dashed rgba(51, 65, 85, 0.9) !important;
    }

    html.dark .fi-fo-file-upload:hover .filepond--panel-root {
        border-color: #14B8A6 !important;
        background-color: rgba(13, 148, 136, 0.08) !important;
    }

    /* -------------------------------------------------------------
       LUXURY SIDEBAR SUB-MENU & STUDENT PACKAGE STATUS STYLING
       ------------------------------------------------------------- */
    /* Nested Sub-Menu items under Parent Resources in Sidebar */
    .fi-sidebar-sub-group-items {
        margin-top: 0.25rem !important;
        margin-bottom: 0.35rem !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0.2rem !important;
    }

    html[dir="rtl"] .fi-sidebar-sub-group-items {
        padding-right: 1.15rem !important;
        border-right: 2px solid rgba(13, 148, 136, 0.25) !important;
        margin-right: 1.25rem !important;
    }

    html:not([dir="rtl"]) .fi-sidebar-sub-group-items {
        padding-left: 1.15rem !important;
        border-left: 2px solid rgba(13, 148, 136, 0.25) !important;
        margin-left: 1.25rem !important;
    }

    .fi-sidebar-sub-group-items .fi-sidebar-item-btn {
        border-radius: 0.65rem !important;
        padding: 0.45rem 0.75rem !important;
        font-size: 0.825rem !important;
        font-weight: 600 !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    html:not(.dark) .fi-sidebar-sub-group-items .fi-sidebar-item-btn:hover {
        background-color: rgba(13, 148, 136, 0.08) !important;
        color: #0F766E !important;
    }

    html.dark .fi-sidebar-sub-group-items .fi-sidebar-item-btn:hover {
        background-color: rgba(20, 184, 166, 0.12) !important;
        color: #5EEAD4 !important;
    }

    /* Active Sub-menu Item */
    .fi-sidebar-sub-group-items .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
        background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25) !important;
    }

    /* Sub-menu Danger Badge Glow / Pulse */
    .fi-sidebar-item-badge-ctn .fi-badge-color-danger,
    .fi-sidebar-sub-group-items .fi-badge-color-danger {
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.35) !important;
        font-weight: 800 !important;
        animation: pulseDangerBadge 2.5s infinite ease-in-out !important;
    }

    @keyframes pulseDangerBadge {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
</style>

<script>
    (function () {
        // Automatically inject .fi-fixed-positioning-context into modal windows
        // so Floating UI in select.js uses strategy: 'fixed' without triggering modal scrollbars
        function setupModalFixedContext() {
            const modals = document.querySelectorAll('.fi-modal-window, .fi-modal-content, .fi-modal');
            modals.forEach(function (el) {
                if (!el.classList.contains('fi-fixed-positioning-context')) {
                    el.classList.add('fi-fixed-positioning-context');
                }
            });
        }

        setupModalFixedContext();

        // Stacking context elevation manager: Ensures open select dropdowns are NEVER overlapped by bottom action buttons
        function syncSelectStackingContext() {
            const openButtons = document.querySelectorAll('.fi-select-input-btn[aria-expanded="true"]');
            
            // First remove elevated class from all previously elevated sections/fields
            document.querySelectorAll('.fi-select-open-elevated').forEach(function (el) {
                el.classList.remove('fi-select-open-elevated');
            });

            // Elevate ancestor containers for all currently open selects
            openButtons.forEach(function (btn) {
                const section = btn.closest('.fi-section');
                if (section) section.classList.add('fi-select-open-elevated');

                const fieldWrp = btn.closest('.fi-fo-field-wrp');
                if (fieldWrp) fieldWrp.classList.add('fi-select-open-elevated');
            });
        }

        // Observe DOM mutations to monitor aria-expanded toggles and modal openings
        const observer = new MutationObserver(function (mutations) {
            setupModalFixedContext();
            syncSelectStackingContext();
        });

        if (document.body) {
            observer.observe(document.body, { 
                childList: true, 
                subtree: true, 
                attributes: true, 
                attributeFilter: ['aria-expanded', 'style', 'class'] 
            });
        }

        // Also sync on click and focus events
        document.addEventListener('click', function () {
            setTimeout(syncSelectStackingContext, 20);
        });

        // Universal Arabic Normalization for instant client-side select search filtering
        function normalizeAr(str) {
            if (!str) return '';
            return String(str)
                .toLowerCase()
                .replace(/[أإآٱ]/g, 'ا')
                .replace(/[ة]/g, 'ه')
                .replace(/[ى]/g, 'ي')
                .replace(/[\u064B-\u065F\u0670]/g, '')
                .replace(/[,\-–—]/g, ' ')
                .trim();
        }

        // Attach instant typing filter on all search inputs in Filament Select dropdowns
        document.addEventListener('input', function (e) {
            if (!e.target || !e.target.matches('.fi-select-input-search-ctn input.fi-input')) {
                return;
            }

            const input = e.target;
            const query = normalizeAr(input.value);
            const dropdown = input.closest('.fi-dropdown-panel');
            if (!dropdown) return;

            const options = dropdown.querySelectorAll('.fi-select-input-option');
            if (!options.length) return;

            let matchCount = 0;
            options.forEach(function (opt) {
                if (!query) {
                    opt.style.display = '';
                    matchCount++;
                    return;
                }

                const optText = normalizeAr(opt.textContent);
                if (optText.includes(query)) {
                    opt.style.display = '';
                    matchCount++;
                } else {
                    opt.style.display = 'none';
                }
            });
        }, true);
    })();
</script>
