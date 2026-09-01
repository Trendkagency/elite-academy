<?php
    $user = auth()->user();
    $role = $user ? $user->getRoleName() : 'student';
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $otherLocale = $isAr ? 'en' : 'ar';
    $otherLocaleName = $isAr ? 'English (EN)' : 'العربية (AR)';
?>

<!-- Master Portal Navbar -->
<header class="sticky top-0 z-30 h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs transition-colors">
    
    <!-- Left: Hamburger Toggle (Mobile) + Breadcrumb / Portal Title -->
    <div class="flex items-center gap-4">
        <button type="button" onclick="togglePortalSidebar(true)" class="portal-navbar-toggle p-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-teal-50 dark:hover:bg-teal-950/50 hover:text-teal-600 transition-colors shadow-xs cursor-pointer" aria-label="Open Sidebar">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="hidden sm:flex flex-col">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-mono">
                <a href="<?php echo e(route('home')); ?>" class="hover:text-teal-600 dark:hover:text-teal-400 transition-colors"><?php echo e(__('navbar.home')); ?></a>
                <span>/</span>
                <span class="text-teal-600 dark:text-teal-400 font-bold">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'teacher'): ?>
                        <?php echo e(__('app.faculty_portal', ['default' => 'Teacher Portal'])); ?>

                    <?php elseif($role === 'parent'): ?>
                        <?php echo e(__('app.parent_portal', ['default' => 'Parent Portal'])); ?>

                    <?php else: ?>
                        <?php echo e(__('app.student_portal', ['default' => 'Student Portal'])); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>
            <h2 id="portalNavTitle" class="font-heading font-black text-slate-900 dark:text-white text-lg tracking-tight">
                <?php echo e($title ?? __('Dashboard Panel')); ?>

            </h2>
        </div>
    </div>

    <!-- Right: Quick Actions, Theme, Language, Notifications, User Menu -->
    <div class="flex items-center gap-2 sm:gap-3">
        
        <!-- Role Quick Action Buttons -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'student'): ?>
            <div class="hidden md:flex items-center gap-2">
                <button onclick="document.getElementById('excuseModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span>📄</span> <?php echo e(__('app.portal.submit_excuse')); ?>

                </button>
                <button onclick="document.getElementById('homeworkExceptionModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span>📋</span> <?php echo e(__('app.portal.submit_exception')); ?>

                </button>
            </div>
        <?php elseif($role === 'teacher'): ?>
            <div class="hidden md:flex items-center gap-2">
                <button type="button" onclick="openCreateSessionModal()" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span>➕</span> <?php echo e(__('Schedule New Session')); ?>

                </button>
            </div>
        <?php elseif($role === 'parent'): ?>
            <div class="hidden md:flex items-center gap-2">
                <button type="button" onclick="window.print()" class="btn-lift px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span>🖨️</span> <?php echo e(__('Print Academic Report')); ?>

                </button>
                <button type="button" onclick="document.getElementById('linkChildModal')?.classList.remove('hidden')" class="btn-lift px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer flex items-center gap-1.5 transition-all">
                    <span>➕</span> <?php echo e(__('Link Child')); ?>

                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Language Switcher -->
        <a href="<?php echo e(route('lang.switch', $otherLocale)); ?>" class="px-3 py-2 rounded-xl text-xs font-mono font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 transition-colors shadow-xs" title="Switch Language">
            🌐 <?php echo e($isAr ? 'EN' : 'عربي'); ?>

        </a>

        <!-- Dark / Light Mode Toggle Button -->
        <button type="button" onclick="toggleTheme()" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/60 hover:text-teal-600 transition-colors shadow-xs" title="Toggle Dark/Light Mode">
            <span class="dark:hidden text-sm">🌙</span>
            <span class="hidden dark:inline text-sm">☀️</span>
        </button>

        <!-- User Profile Pill & Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button @click="open = !open" type="button" class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 hover:border-teal-500/50 transition-all shadow-xs cursor-pointer">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-slate-950 font-heading font-black text-xs flex items-center justify-center shadow-xs">
                    <?php echo e(mb_substr($user->name ?? 'U', 0, 1)); ?>

                </div>
                <div class="hidden sm:block text-<?php echo e($isAr ? 'right' : 'left'); ?> leading-tight">
                    <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate max-w-[120px]"><?php echo e($user->name ?? 'User'); ?></p>
                    <span class="text-[10px] font-mono font-bold text-teal-600 dark:text-teal-400 capitalize"><?php echo e($role); ?></span>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display: none;"
                 class="absolute <?php echo e($isAr ? 'left-0' : 'right-0'); ?> mt-2 w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl py-2 z-50">
                
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-black text-slate-900 dark:text-white"><?php echo e($user->name); ?></p>
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 truncate"><?php echo e($user->email); ?></p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user && $user->isAdmin()): ?>
                    <a href="/admin" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition-colors">
                        <span>⚡</span> <?php echo e(__('Admin Panel (Filament)')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 transition-colors">
                    <span>🏠</span> <?php echo e(__('navbar.home')); ?>

                </a>

                <div class="border-t border-slate-100 dark:border-slate-800 mt-1 pt-1">
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full text-<?php echo e($isAr ? 'right' : 'left'); ?> flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer">
                            <span>🚪</span> <?php echo e(__('navbar.logout')); ?>

                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/components/portal-navbar.blade.php ENDPATH**/ ?>