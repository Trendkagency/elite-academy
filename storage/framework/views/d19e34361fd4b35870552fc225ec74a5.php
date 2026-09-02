<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>" class="scroll-smooth h-full bg-[#FAFAF9] text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#0D9488">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" href="<?php echo e(asset('images/logo_500.webp')); ?>" type="image/webp">
    <link rel="shortcut icon" href="<?php echo e(asset('images/logo_500.webp')); ?>" type="image/webp">
    <title><?php echo e($pageTitle ?? 'Elite Academy Portal'); ?></title>

    
    <script>
        (function() {
            try {
                localStorage.removeItem('theme');
                document.documentElement.classList.remove('dark');
            } catch (e) {}
        })();
    </script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-family-english: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-arabic: "Cairo", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-family-mono: "JetBrains Mono", monospace;
            --font-sans: var(--font-family-arabic);
            --font-heading: var(--font-family-arabic);
            --portal-sidebar-w: 280px;
        }
        html[lang="en"], [dir="ltr"] {
            --font-sans: var(--font-family-english);
            --font-heading: var(--font-family-english);
        }
        html, body, button, input, select, textarea, table, .font-sans, .font-heading {
            font-family: var(--font-sans) !important;
        }
        html, body {
            background-color: #FAFAF9;
            color: #0F172A;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .btn-lift {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-lift:hover {
            transform: translateY(-1.5px);
        }
        .btn-lift:active {
            transform: translateY(0.5px) scale(0.98);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: transform 0.25s ease, background-color 0.25s ease, border-color 0.25s ease;
        }

        /* ─── Robust Desktop & Mobile Sidebar Engine ─── */
        .portal-sidebar-wrapper {
            width: var(--portal-sidebar-w);
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .portal-main-canvas {
            transition: margin 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* LTR Desktop (>= 1024px) */
        @media (min-width: 1024px) {
            html[dir="ltr"] .portal-sidebar-wrapper {
                left: 0;
                right: auto;
                transform: translateX(0);
            }
            html[dir="ltr"] .portal-main-canvas {
                margin-left: var(--portal-sidebar-w) !important;
                margin-right: 0 !important;
            }
            /* Desktop Collapsed */
            html[dir="ltr"] body.sidebar-collapsed .portal-sidebar-wrapper {
                transform: translateX(-100%) !important;
            }
            html[dir="ltr"] body.sidebar-collapsed .portal-main-canvas {
                margin-left: 0 !important;
            }
        }

        /* RTL Desktop (>= 1024px) */
        @media (min-width: 1024px) {
            html[dir="rtl"] .portal-sidebar-wrapper {
                right: 0;
                left: auto;
                transform: translateX(0);
            }
            html[dir="rtl"] .portal-main-canvas {
                margin-right: var(--portal-sidebar-w) !important;
                margin-left: 0 !important;
            }
            /* Desktop Collapsed */
            html[dir="rtl"] body.sidebar-collapsed .portal-sidebar-wrapper {
                transform: translateX(100%) !important;
            }
            html[dir="rtl"] body.sidebar-collapsed .portal-main-canvas {
                margin-right: 0 !important;
            }
        }

        /* Mobile & Tablet (< 1024px) Off-Canvas */
        @media (max-width: 1023.98px) {
            .portal-main-canvas {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            html[dir="ltr"] .portal-sidebar-wrapper {
                left: 0;
                right: auto;
                transform: translateX(-100%);
            }
            html[dir="ltr"] .portal-sidebar-wrapper.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
            html[dir="rtl"] .portal-sidebar-wrapper {
                right: 0;
                left: auto;
                transform: translateX(100%);
            }
            html[dir="rtl"] .portal-sidebar-wrapper.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
        }

        /* ─── Buttons Visibility Rules ─── */
        .portal-navbar-toggle {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 1024px) {
            body:not(.sidebar-collapsed) .portal-navbar-toggle {
                display: none !important;
            }
        }

        .portal-sidebar-close-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        /* ─── Backdrop System ─── */
        #portalSidebarBackdrop {
            position: fixed;
            inset: 0;
            z-index: 45;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #portalSidebarBackdrop.active {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        @media (min-width: 1024px) {
            #portalSidebarBackdrop {
                display: none !important;
            }
        }
    </style>

    <link rel="stylesheet" href="<?php echo e(asset('dist/output.css')); ?>?v=<?php echo e(time()); ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="min-h-screen bg-[#FAFAF9] dark:bg-[#0B0F19] flex flex-col font-sans transition-colors duration-200 overflow-x-hidden">
    
    
    <?php if (isset($component)) { $__componentOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.portal-sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('portal-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50)): ?>
<?php $attributes = $__attributesOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50; ?>
<?php unset($__attributesOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50)): ?>
<?php $component = $__componentOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50; ?>
<?php unset($__componentOriginalc3b2f4ffdb73a0ce7fdaeca5b6486a50); ?>
<?php endif; ?>

    
    <div class="portal-main-canvas flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300">
        
        
        <?php if (isset($component)) { $__componentOriginalb4ac86ca19fcdcbf1ef1fc6085778540 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb4ac86ca19fcdcbf1ef1fc6085778540 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.portal-navbar','data' => ['title' => $pageTitle ?? __('Dashboard Panel')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('portal-navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle ?? __('Dashboard Panel'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb4ac86ca19fcdcbf1ef1fc6085778540)): ?>
<?php $attributes = $__attributesOriginalb4ac86ca19fcdcbf1ef1fc6085778540; ?>
<?php unset($__attributesOriginalb4ac86ca19fcdcbf1ef1fc6085778540); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb4ac86ca19fcdcbf1ef1fc6085778540)): ?>
<?php $component = $__componentOriginalb4ac86ca19fcdcbf1ef1fc6085778540; ?>
<?php unset($__componentOriginalb4ac86ca19fcdcbf1ef1fc6085778540); ?>
<?php endif; ?>

        
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-full">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        
        <footer class="py-6 px-6 border-t border-slate-200 dark:border-slate-800 text-center text-xs font-mono text-slate-500 dark:text-slate-400 bg-white/40 dark:bg-slate-900/40 backdrop-blur-xs">
            <p>&copy; <?php echo e(date('Y')); ?> Elite Academy. <?php echo e(__('All rights reserved.')); ?> • <?php echo e(__('Leading Accredited Academy')); ?></p>
        </footer>
    </div>

    
    <script>
        function togglePortalSidebar(action) {
            const isDesktop = window.innerWidth >= 1024;
            const sidebar = document.getElementById('portalSidebar');
            const backdrop = document.getElementById('portalSidebarBackdrop');

            if (isDesktop) {
                // Desktop collapse / expand
                if (action === false) {
                    document.body.classList.add('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '1');
                } else if (action === true) {
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', '0');
                } else {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('portal_sidebar_collapsed', isCollapsed ? '1' : '0');
                }
            } else {
                // Mobile off-canvas drawer
                if (action === true) {
                    if (sidebar) sidebar.classList.add('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.add('active');
                    document.body.classList.add('overflow-hidden');
                } else if (action === false) {
                    if (sidebar) sidebar.classList.remove('sidebar-mobile-open');
                    if (backdrop) backdrop.classList.remove('active');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    const isOpen = sidebar ? sidebar.classList.toggle('sidebar-mobile-open') : false;
                    if (backdrop) {
                        if (isOpen) {
                            backdrop.classList.add('active');
                            document.body.classList.add('overflow-hidden');
                        } else {
                            backdrop.classList.remove('active');
                            document.body.classList.remove('overflow-hidden');
                        }
                    }
                }
            }
        }

        // Restore desktop collapsed state on initial load
        (function() {
            try {
                if (window.innerWidth >= 1024 && localStorage.getItem('portal_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();

        // Global Toast Notification Helper
        window.Toast = {
            success: function(msg, title) {
                console.log('✅ SUCCESS:', title, msg);
                alert(`${title ? title + '\n' : ''}${msg}`);
            },
            error: function(msg, title) {
                console.error('❌ ERROR:', title, msg);
                alert(`⚠️ ${title ? title + '\n' : ''}${msg}`);
            },
            warning: function(msg, title) {
                console.warn('⚠️ WARNING:', title, msg);
                alert(`⚠️ ${title ? title + '\n' : ''}${msg}`);
            }
        };
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/layouts/portal-panel.blade.php ENDPATH**/ ?>