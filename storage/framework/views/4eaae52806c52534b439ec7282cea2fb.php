<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

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
    .fi-body {
        background-color: #F8FAFC !important;
    }

    .fi-main-ctn {
        background-color: #F8FAFC !important;
    }

    /* --- Sidebar: Sleek Dark Slate Navy Theme --- */
    aside.fi-sidebar {
        background: linear-gradient(180deg, #0F172A 0%, #020617 100%) !important;
        border-right: 1px solid rgba(30, 41, 59, 0.8) !important;
        border-left: 1px solid rgba(30, 41, 59, 0.8) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12) !important;
    }

    .fi-sidebar-header {
        border-bottom: 1px solid rgba(30, 41, 59, 0.8) !important;
        padding: 1.25rem 1rem !important;
    }

    .fi-sidebar-header a img {
        filter: drop-shadow(0 2px 8px rgba(13, 148, 136, 0.3));
    }

    .fi-sidebar-group-label {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.7rem !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        color: #94A3B8 !important;
        margin-top: 0.75rem !important;
        margin-bottom: 0.25rem !important;
    }

    .fi-sidebar-item-btn {
        border-radius: 0.875rem !important;
        padding: 0.65rem 0.875rem !important;
        font-weight: 700 !important;
        font-size: 0.825rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        color: #CBD5E1 !important;
    }

    .fi-sidebar-item-btn:hover {
        background-color: rgba(15, 118, 110, 0.25) !important;
        color: #5EEAD4 !important;
        transform: translateX(-2px);
    }
    html[dir="rtl"] .fi-sidebar-item-btn:hover {
        transform: translateX(2px) !important;
    }

    .fi-sidebar-item-btn.fi-active,
    .fi-sidebar-item-btn[aria-current="page"] {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.9) 0%, rgba(15, 118, 110, 0.95) 100%) !important;
        color: #FFFFFF !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
    }

    .fi-sidebar-item-btn.fi-active .fi-sidebar-item-icon,
    .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon {
        color: #FFFFFF !important;
    }

    .fi-sidebar-item-icon {
        color: #2DD4BF !important;
        transition: transform 0.2s ease !important;
    }

    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
        transform: scale(1.15) !important;
    }

    .fi-sidebar-footer {
        border-top: 1px solid rgba(30, 41, 59, 0.8) !important;
        background: rgba(2, 6, 23, 0.6) !important;
    }

    /* --- Topbar / Header: Frosted Glass Effect --- */
    header.fi-topbar {
        background: rgba(255, 255, 255, 0.92) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    }

    .fi-topbar-nav {
        padding: 0.5rem 1.5rem !important;
    }

    .fi-global-search-input-ctn input {
        border-radius: 1rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background-color: #F1F5F9 !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }

    .fi-global-search-input-ctn input:focus {
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

    .fi-ta-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    .fi-ta-header-cell {
        background-color: #F8FAFC !important;
        padding: 0.875rem 1rem !important;
        font-family: 'Cairo', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
        color: #475569 !important;
        border-bottom: 1.5px solid #E2E8F0 !important;
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
    }

    /* --- Form Sections & Cards --- */
    .fi-section {
        border-radius: 1.5rem !important;
        border: 1.5px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03) !important;
        overflow: hidden !important;
    }

    .fi-section-header {
        border-bottom: 1px solid #F1F5F9 !important;
        padding: 1.25rem 1.5rem !important;
    }

    .fi-section-header-heading {
        font-family: 'Cairo', sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.15rem !important;
        color: #0F172A !important;
    }

    /* --- Inputs, Selects & Form Controls --- */
    input.fi-input, select.fi-select-input, textarea.fi-input {
        border-radius: 0.875rem !important;
        border: 1.5px solid #CBD5E1 !important;
        background-color: #FFFFFF !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        transition: all 0.2s ease !important;
        padding: 0.65rem 0.875rem !important;
    }

    input.fi-input:focus, select.fi-select-input:focus, textarea.fi-input:focus {
        border-color: #0D9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15) !important;
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

    html.dark header.fi-topbar {
        background: rgba(15, 23, 42, 0.88) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.7) !important;
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

    html.dark .fi-ta-header-cell {
        background-color: #1E293B !important;
        color: #94A3B8 !important;
        border-bottom-color: rgba(51, 65, 85, 0.8) !important;
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
    html.dark textarea.fi-input {
        background-color: #1E293B !important;
        border-color: rgba(71, 85, 105, 0.9) !important;
        color: #F8FAFC !important;
    }

    html.dark input.fi-input:focus, 
    html.dark select.fi-select-input:focus, 
    html.dark textarea.fi-input:focus {
        border-color: #14B8A6 !important;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2) !important;
    }

    html.dark .fi-fo-field-wrp-label label {
        color: #CBD5E1 !important;
    }

    html.dark .fi-modal-window {
        background: #0F172A !important;
        border-color: rgba(51, 65, 85, 0.8) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
    }

    html.dark .fi-modal-header {
        border-bottom-color: rgba(30, 41, 59, 0.8) !important;
    }

    html.dark .fi-modal-heading {
        color: #F8FAFC !important;
    }
</style>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/hooks/head-styles.blade.php ENDPATH**/ ?>