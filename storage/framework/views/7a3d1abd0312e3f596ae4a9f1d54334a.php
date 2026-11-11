<?php
    $credentials = config('demo.credentials', []);
    $accounts = $credentials['accounts'] ?? [];
    $password = $credentials['password'] ?? 'Demo@12345';
    $directLogin = $credentials['direct_login'] ?? [];
    $directEnabled = (bool) ($directLogin['enabled'] ?? true);
    $style = $directLogin['style'] ?? 'cards';
    $showTable = (bool) ($credentials['show_table'] ?? true);
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>

<div class="demo-credentials-wrapper">
    <style>
        /* Scoped Demo Credentials Architecture — Dual Theme & Zero SVG Ballooning */
        .demo-credentials-wrapper {
            width: 100% !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 0.85rem !important;
            font-family: 'Cairo', system-ui, sans-serif !important;
        }

        /* SVG Strict Safety Constraints */
        .demo-credentials-wrapper svg {
            display: inline-block !important;
            flex-shrink: 0 !important;
            box-sizing: content-box !important;
            vertical-align: middle !important;
        }
        .demo-credentials-wrapper .demo-role-icon-box svg {
            width: 1.15rem !important;
            height: 1.15rem !important;
            max-width: 1.15rem !important;
            max-height: 1.15rem !important;
            min-width: 1.15rem !important;
            min-height: 1.15rem !important;
        }
        .demo-credentials-wrapper svg.chevron-icon {
            width: 0.65rem !important;
            height: 0.65rem !important;
            max-width: 0.65rem !important;
            max-height: 0.65rem !important;
            min-width: 0.65rem !important;
            min-height: 0.65rem !important;
        }
        .demo-credentials-wrapper svg.copy-icon {
            width: 0.75rem !important;
            height: 0.75rem !important;
            max-width: 0.75rem !important;
            max-height: 0.75rem !important;
            min-width: 0.75rem !important;
            min-height: 0.75rem !important;
        }

        /* Top Header */
        .demo-cred-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            margin-bottom: 0.15rem !important;
        }
        .demo-cred-title {
            font-size: 0.75rem !important;
            font-weight: 800 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.45rem !important;
            color: #334155 !important;
        }
        html.dark .demo-cred-title {
            color: #E2E8F0 !important;
        }
        .demo-cred-pill {
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            padding: 0.15rem 0.5rem !important;
            border-radius: 9999px !important;
            background: #EFF6FF !important;
            color: #2563EB !important;
            border: 1px solid rgba(191, 219, 254, 0.8) !important;
            white-space: nowrap !important;
        }
        html.dark .demo-cred-pill {
            background: rgba(37, 99, 235, 0.2) !important;
            color: #93C5FD !important;
            border-color: rgba(37, 99, 235, 0.4) !important;
        }

        /* Pulse Indicator */
        .demo-pulse-ctn {
            position: relative !important;
            display: inline-flex !important;
            width: 0.5rem !important;
            height: 0.5rem !important;
        }
        .demo-pulse-ping {
            position: absolute !important;
            display: inline-flex !important;
            width: 100% !important;
            height: 100% !important;
            border-radius: 9999px !important;
            background-color: #60A5FA !important;
            opacity: 0.75 !important;
            animation: demo-ping 1.2s cubic-bezier(0, 0, 0.2, 1) infinite !important;
        }
        .demo-pulse-dot {
            position: relative !important;
            display: inline-flex !important;
            border-radius: 9999px !important;
            width: 0.5rem !important;
            height: 0.5rem !important;
            background-color: #2563EB !important;
        }
        @keyframes demo-ping {
            75%, 100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        /* Grid Layout (2 columns) */
        .demo-role-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.65rem !important;
            width: 100% !important;
        }
        @media (max-width: 480px) {
            .demo-role-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .demo-role-form {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Role Cards Button */
        .demo-role-btn {
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            width: 100% !important;
            min-height: 5.25rem !important;
            padding: 0.65rem 0.75rem !important;
            border-radius: 0.875rem !important;
            border: 1.5px solid transparent !important;
            text-align: start !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-sizing: border-box !important;
            background-color: transparent !important;
            outline: none !important;
        }
        .demo-role-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15) !important;
        }
        html[dir="rtl"] .demo-role-btn {
            text-align: right !important;
        }

        .demo-role-card-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            margin-bottom: 0.35rem !important;
        }
        .demo-role-icon-box {
            width: 1.85rem !important;
            height: 1.85rem !important;
            min-width: 1.85rem !important;
            min-height: 1.85rem !important;
            border-radius: 0.6rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
        html:not(.dark) .demo-role-icon-box {
            background-color: #FFFFFF !important;
        }
        html.dark .demo-role-icon-box {
            background-color: rgba(15, 23, 42, 0.85) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
        }

        .demo-role-badge {
            font-size: 0.65rem !important;
            font-weight: 800 !important;
            padding: 0.15rem 0.45rem !important;
            border-radius: 0.375rem !important;
            letter-spacing: 0.02em !important;
            white-space: nowrap !important;
        }

        .demo-role-card-body {
            width: 100% !important;
        }
        .demo-role-email {
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            margin: 0 !important;
            line-height: 1.25 !important;
        }
        html:not(.dark) .demo-role-email {
            color: #0F172A !important;
        }
        html.dark .demo-role-email {
            color: #F8FAFC !important;
        }

        .demo-role-action {
            font-size: 0.65rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
            margin-top: 0.25rem !important;
            line-height: 1 !important;
        }
        html:not(.dark) .demo-role-action {
            color: #64748B !important;
        }
        html.dark .demo-role-action {
            color: #94A3B8 !important;
        }
        html[dir="rtl"] .demo-role-action svg.chevron-icon {
            transform: rotate(180deg) !important;
        }

        /* --- Individual Role Theming --- */
        /* Admin (Rose) */
        .demo-role-btn.role-admin {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.08) 0%, rgba(225, 29, 72, 0.03) 100%) !important;
            border-color: rgba(244, 63, 94, 0.25) !important;
        }
        html.dark .demo-role-btn.role-admin {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.16) 0%, rgba(225, 29, 72, 0.06) 100%) !important;
            border-color: rgba(244, 63, 94, 0.4) !important;
        }
        .demo-role-btn.role-admin .demo-role-icon-box svg {
            color: #E11D48 !important;
        }
        html.dark .demo-role-btn.role-admin .demo-role-icon-box svg {
            color: #FB7185 !important;
        }
        .demo-role-btn.role-admin .demo-role-badge {
            background: #FFE4E6 !important;
            color: #9F1239 !important;
        }
        html.dark .demo-role-btn.role-admin .demo-role-badge {
            background: rgba(159, 18, 57, 0.5) !important;
            color: #FECDD3 !important;
        }

        /* Teacher (Teal) */
        .demo-role-btn.role-teacher {
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.08) 0%, rgba(15, 118, 110, 0.03) 100%) !important;
            border-color: rgba(13, 148, 136, 0.25) !important;
        }
        html.dark .demo-role-btn.role-teacher {
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.16) 0%, rgba(15, 118, 110, 0.06) 100%) !important;
            border-color: rgba(13, 148, 136, 0.4) !important;
        }
        .demo-role-btn.role-teacher .demo-role-icon-box svg {
            color: #0D9488 !important;
        }
        html.dark .demo-role-btn.role-teacher .demo-role-icon-box svg {
            color: #2DD4BF !important;
        }
        .demo-role-btn.role-teacher .demo-role-badge {
            background: #CCFBF1 !important;
            color: #115E59 !important;
        }
        html.dark .demo-role-btn.role-teacher .demo-role-badge {
            background: rgba(17, 94, 89, 0.5) !important;
            color: #99F6E4 !important;
        }

        /* Student (Blue) */
        .demo-role-btn.role-student {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(29, 78, 216, 0.03) 100%) !important;
            border-color: rgba(37, 99, 235, 0.25) !important;
        }
        html.dark .demo-role-btn.role-student {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.16) 0%, rgba(29, 78, 216, 0.06) 100%) !important;
            border-color: rgba(37, 99, 235, 0.4) !important;
        }
        .demo-role-btn.role-student .demo-role-icon-box svg {
            color: #2563EB !important;
        }
        html.dark .demo-role-btn.role-student .demo-role-icon-box svg {
            color: #60A5FA !important;
        }
        .demo-role-btn.role-student .demo-role-badge {
            background: #DBEAFE !important;
            color: #1E40AF !important;
        }
        html.dark .demo-role-btn.role-student .demo-role-badge {
            background: rgba(30, 64, 175, 0.5) !important;
            color: #BFDBFE !important;
        }

        /* Parent (Amber) */
        .demo-role-btn.role-parent {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.08) 0%, rgba(180, 83, 9, 0.03) 100%) !important;
            border-color: rgba(217, 119, 6, 0.25) !important;
        }
        html.dark .demo-role-btn.role-parent {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.16) 0%, rgba(180, 83, 9, 0.06) 100%) !important;
            border-color: rgba(217, 119, 6, 0.4) !important;
        }
        .demo-role-btn.role-parent .demo-role-icon-box svg {
            color: #D97706 !important;
        }
        html.dark .demo-role-btn.role-parent .demo-role-icon-box svg {
            color: #FBBF24 !important;
        }
        .demo-role-btn.role-parent .demo-role-badge {
            background: #FEF3C7 !important;
            color: #92400E !important;
        }
        html.dark .demo-role-btn.role-parent .demo-role-badge {
            background: rgba(146, 64, 14, 0.5) !important;
            color: #FDE68A !important;
        }

        /* Default Role */
        .demo-role-btn.role-default {
            background: linear-gradient(135deg, rgba(100, 116, 139, 0.08) 0%, rgba(71, 85, 105, 0.03) 100%) !important;
            border-color: rgba(100, 116, 139, 0.25) !important;
        }
        html.dark .demo-role-btn.role-default {
            background: linear-gradient(135deg, rgba(100, 116, 139, 0.16) 0%, rgba(71, 85, 105, 0.06) 100%) !important;
            border-color: rgba(100, 116, 139, 0.4) !important;
        }
        .demo-role-btn.role-default .demo-role-icon-box svg {
            color: #475569 !important;
        }
        html.dark .demo-role-btn.role-default .demo-role-icon-box svg {
            color: #94A3B8 !important;
        }
        .demo-role-btn.role-default .demo-role-badge {
            background: #F1F5F9 !important;
            color: #334155 !important;
        }
        html.dark .demo-role-btn.role-default .demo-role-badge {
            background: rgba(51, 65, 85, 0.5) !important;
            color: #CBD5E1 !important;
        }

        /* Buttons Style */
        .demo-btn-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }
        @media (min-width: 640px) {
            .demo-btn-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }
        .demo-quick-btn {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            border-radius: 0.75rem !important;
            background-color: #2563EB !important;
            color: #FFFFFF !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.35rem !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease !important;
        }
        .demo-quick-btn:hover {
            background-color: #1D4ED8 !important;
        }

        /* List Style */
        .demo-list-group {
            border-radius: 0.875rem !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            overflow: hidden !important;
            background-color: rgba(255, 255, 255, 0.5) !important;
            width: 100% !important;
        }
        html.dark .demo-list-group {
            border-color: rgba(51, 65, 85, 0.7) !important;
            background-color: rgba(15, 23, 42, 0.6) !important;
        }
        .demo-list-item-btn {
            width: 100% !important;
            padding: 0.55rem 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border: none !important;
            border-bottom: 1px solid rgba(241, 245, 249, 0.8) !important;
            background-color: transparent !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease !important;
        }
        html.dark .demo-list-item-btn {
            border-bottom-color: rgba(51, 65, 85, 0.4) !important;
        }
        .demo-list-item-btn:last-child {
            border-bottom: none !important;
        }
        .demo-list-item-btn:hover {
            background-color: rgba(37, 99, 235, 0.08) !important;
        }

        /* --- Manual Credentials Table --- */
        .demo-table-section {
            width: 100% !important;
            padding-top: 0.25rem !important;
        }
        .demo-table-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            margin-bottom: 0.4rem !important;
        }
        .demo-table-title {
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            color: #64748B !important;
        }
        html.dark .demo-table-title {
            color: #94A3B8 !important;
        }
        .demo-table-pwd-pill {
            font-size: 0.65rem !important;
            font-family: var(--font-family-mono, monospace) !important;
            padding: 0.15rem 0.5rem !important;
            border-radius: 0.375rem !important;
            background: #F1F5F9 !important;
            color: #475569 !important;
        }
        html.dark .demo-table-pwd-pill {
            background: rgba(30, 41, 59, 0.8) !important;
            color: #94A3B8 !important;
        }
        .demo-table-pwd-pill strong {
            color: #0F172A !important;
        }
        html.dark .demo-table-pwd-pill strong {
            color: #F8FAFC !important;
        }

        .demo-table-card {
            border-radius: 0.75rem !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            overflow: hidden !important;
            background: rgba(255, 255, 255, 0.6) !important;
            width: 100% !important;
        }
        html.dark .demo-table-card {
            border-color: rgba(51, 65, 85, 0.7) !important;
            background: rgba(15, 23, 42, 0.6) !important;
        }

        .demo-table-card table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 0.725rem !important;
        }
        .demo-table-card tr {
            border-bottom: 1px solid rgba(241, 245, 249, 0.9) !important;
            transition: background-color 0.15s ease !important;
        }
        html.dark .demo-table-card tr {
            border-bottom-color: rgba(51, 65, 85, 0.4) !important;
        }
        .demo-table-card tr:last-child {
            border-bottom: none !important;
        }
        .demo-table-card tr:hover {
            background-color: rgba(241, 245, 249, 0.5) !important;
        }
        html.dark .demo-table-card tr:hover {
            background-color: rgba(30, 41, 59, 0.4) !important;
        }

        .demo-table-card td {
            padding: 0.35rem 0.65rem !important;
            vertical-align: middle !important;
            box-sizing: border-box !important;
        }
        .demo-col-role {
            font-weight: 700 !important;
            width: 5rem !important;
            color: #334155 !important;
        }
        html.dark .demo-col-role {
            color: #CBD5E1 !important;
        }
        .demo-col-email {
            font-family: var(--font-family-mono, monospace) !important;
            font-size: 0.7rem !important;
            color: #64748B !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        html.dark .demo-col-email {
            color: #94A3B8 !important;
        }
        .demo-col-action {
            text-align: end !important;
            width: 4rem !important;
        }
        html[dir="rtl"] .demo-col-action {
            text-align: left !important;
        }

        .copy-cred-btn {
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            padding: 0.2rem 0.5rem !important;
            border-radius: 0.35rem !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
            transition: all 0.15s ease !important;
            background: #EFF6FF !important;
            color: #2563EB !important;
        }
        .copy-cred-btn:hover {
            background: #DBEAFE !important;
            color: #1D4ED8 !important;
        }
        html.dark .copy-cred-btn {
            background: rgba(37, 99, 235, 0.2) !important;
            color: #93C5FD !important;
        }
        html.dark .copy-cred-btn:hover {
            background: rgba(37, 99, 235, 0.35) !important;
            color: #BFDBFE !important;
        }

        /* Force font icons inside demo wrapper to retain icon fonts */
        .demo-credentials-wrapper .fa,
        .demo-credentials-wrapper .fas,
        .demo-credentials-wrapper .far,
        .demo-credentials-wrapper .fa-solid,
        .demo-credentials-wrapper i[class*="fa-"] {
            font-family: "Font Awesome 6 Free" !important;
            font-style: normal !important;
            font-weight: 900 !important;
        }
    </style>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($directEnabled && !empty($accounts)): ?>
        <div style="width: 100%; display: flex; flex-direction: column; gap: 0.5rem;">
            <div class="demo-cred-header">
                <span class="demo-cred-title">
                    <span class="demo-pulse-ctn">
                        <span class="demo-pulse-ping"></span>
                        <span class="demo-pulse-dot"></span>
                    </span>
                    <span><?php echo e($isRtl ? 'تسجيل الدخول السريع (نقرة واحدة)' : 'One-Click Instant Access'); ?></span>
                </span>
                <span class="demo-cred-pill">
                    <?php echo e($isRtl ? 'بدون كتابة كلمة مرور' : 'No typing required'); ?>

                </span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($style === 'cards'): ?>
                <div class="demo-role-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                            $email = $acc['email'] ?? '';
                            
                            $roleMeta = match($role) {
                                'admin' => [
                                    'class' => 'role-admin',
                                    'svg' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                                ],
                                'teacher' => [
                                    'class' => 'role-teacher',
                                    'svg' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v13H3zM8 21h8m-4-5v5m-4-10h8m-8 3h5"/></svg>',
                                ],
                                'student' => [
                                    'class' => 'role-student',
                                    'svg' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>',
                                ],
                                'parent' => [
                                    'class' => 'role-parent',
                                    'svg' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8zm14 10v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>',
                                ],
                                default => [
                                    'class' => 'role-default',
                                    'svg' => '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                                ],
                            };
                        ?>

                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="demo-role-form">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="demo-role-btn <?php echo e($roleMeta['class']); ?>" title="<?php echo e($label); ?> (<?php echo e($email); ?>)">
                                <div class="demo-role-card-header">
                                    <span class="demo-role-icon-box">
                                        <?php echo $roleMeta['svg']; ?>

                                    </span>
                                    <span class="demo-role-badge">
                                        <?php echo e($label); ?>

                                    </span>
                                </div>
                                <div class="demo-role-card-body">
                                    <p class="demo-role-email"><?php echo e($email); ?></p>
                                    <p class="demo-role-action">
                                        <span><?php echo e($isRtl ? 'دخول فوري' : 'Direct Sign-In'); ?></span>
                                        <svg class="chevron-icon" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </p>
                                </div>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php elseif($style === 'buttons'): ?>
                <div class="demo-btn-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                        ?>
                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="demo-role-form">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="demo-quick-btn">
                                <span><?php echo e($label); ?></span>
                                <svg class="chevron-icon" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php else: ?>
                
                <div class="demo-list-group">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $role = $acc['role'] ?? 'student';
                            $label = is_array($acc['label'] ?? null)
                                ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                : ($acc['label'] ?? ucfirst($role));
                            $email = $acc['email'] ?? '';
                        ?>
                        <form action="<?php echo e(route('demo.login', ['role' => $role])); ?>" method="POST" class="demo-role-form">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="demo-list-item-btn">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700;"><?php echo e($label); ?></span>
                                    <span style="font-size: 0.7rem; color: #94A3B8;"><?php echo e($email); ?></span>
                                </div>
                                <span style="font-size: 0.725rem; font-weight: 700; color: #2563EB; display: flex; align-items: center; gap: 0.25rem;">
                                    <span><?php echo e($isRtl ? 'دخول' : 'Sign in'); ?></span>
                                    <svg class="chevron-icon" width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </button>
                        </form>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTable && !empty($accounts)): ?>
        <div class="demo-table-section">
            <div class="demo-table-header">
                <span class="demo-table-title">
                    <?php echo e($isRtl ? 'بيانات الحسابات التجريبية (للملء اليدوي):' : 'Demo Accounts (Manual Fill):'); ?>

                </span>
                <span class="demo-table-pwd-pill">
                    <?php echo e($isRtl ? 'كلمة المرور موحدة: ' : 'Password: '); ?> <strong><?php echo e($password); ?></strong>
                </span>
            </div>

            <div class="demo-table-card">
                <table>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $role = $acc['role'] ?? 'student';
                                $label = is_array($acc['label'] ?? null)
                                    ? ($acc['label'][$locale] ?? $acc['label']['en'] ?? ucfirst($role))
                                    : ($acc['label'] ?? ucfirst($role));
                                $email = $acc['email'] ?? '';
                            ?>
                            <tr>
                                <td class="demo-col-role">
                                    <?php echo e($label); ?>

                                </td>
                                <td class="demo-col-email">
                                    <?php echo e($email); ?>

                                </td>
                                <td class="demo-col-action">
                                    <button 
                                        type="button" 
                                        class="copy-cred-btn"
                                        data-email="<?php echo e($email); ?>"
                                        data-pass="<?php echo e($password); ?>"
                                    >
                                        <svg class="copy-icon" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                        </svg>
                                        <span class="copy-text"><?php echo e($isRtl ? 'تعبئة' : 'Fill'); ?></span>
                                    </button>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyBtns = document.querySelectorAll('.copy-cred-btn');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const email = this.getAttribute('data-email');
            const pass = this.getAttribute('data-pass');

            // Find input fields across Filament Admin, Frontend login, or standard forms
            const emailInput = document.getElementById('login-email') 
                || document.querySelector('input[type="email"]')
                || document.getElementById('data.email')
                || document.querySelector('input[name="data.email"]')
                || document.querySelector('input[name="email"]');

            const passInput = document.getElementById('login-password') 
                || document.querySelector('input[type="password"]')
                || document.getElementById('data.password')
                || document.querySelector('input[name="data.password"]')
                || document.querySelector('input[name="password"]');

            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = pass;
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                emailInput.dispatchEvent(new Event('change', { bubbles: true }));
                passInput.dispatchEvent(new Event('input', { bubbles: true }));
                passInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(`${email}\n${pass}`).catch(() => {});
            }

            const copyText = this.querySelector('.copy-text');
            const orig = copyText ? copyText.textContent : '';
            if (copyText) {
                copyText.textContent = '✓';
                setTimeout(() => { copyText.textContent = orig; }, 1500);
            }
        });
    });
});
</script>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/demo/credentials.blade.php ENDPATH**/ ?>