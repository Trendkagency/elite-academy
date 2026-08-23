<?php
    $navLink = fn (string $key, string $route, string $label) => [
        'key' => $key,
        'route' => $route,
        'label' => $label,
        'active' => ($activeNav ?? null) === $key,
    ];
    $navItems = [
        $navLink('home', 'home', __('Home')),
        $navLink('subjects', 'subjects', __('Subjects')),
        $navLink('courses', 'courses', __('Courses')),
        $navLink('teachers', 'teachers', __('Teachers')),
        $navLink('blog', 'blog', __('Blog')),
        $navLink('about', 'about', __('About')),
        $navLink('contact', 'contact', __('Contact')),
    ];
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $joinText = __('Join Us');
    $loginText = __('Log in');

    $authUser = auth()->user();
    $portalUrl = route('student-portal');
    $portalLabel = __('Student Portal');
    if ($authUser) {
        if ($authUser->isAdmin()) {
            $portalUrl = url('/admin');
            $portalLabel = __('Admin Panel');
        } elseif ($authUser->isTeacher()) {
            $portalUrl = route('teacher-portal');
            $portalLabel = __('Teacher Portal');

            // Teacher Private Workspace Navigation Items
            $navItems = [
                ['key' => 'portal', 'route' => 'teacher-portal', 'url' => route('teacher-portal'), 'label' => __('Dashboard'), 'active' => ($activeNav ?? null) === 'portal'],
                ['key' => 'sessions', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=sessions', 'label' => __('Sessions & Streams'), 'active' => false],
                ['key' => 'assignments', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=assignments', 'label' => __('Assignments'), 'active' => false],
                ['key' => 'attendance', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=attendance', 'label' => __('Attendance'), 'active' => false],
                ['key' => 'students', 'route' => 'teacher-portal', 'url' => route('teacher-portal') . '?tab=students', 'label' => __('My Students'), 'active' => false],
            ];
        } elseif ($authUser->isParent()) {
            $portalUrl = route('parent-portal');
            $portalLabel = __('Parent Portal');

            $currentTab = request()->query('tab', '');
            $navItems = [
                ['key' => 'portal', 'route' => 'parent-portal', 'url' => route('parent-portal'), 'label' => __('Dashboard'), 'active' => ($activeNav ?? null) === 'portal' && empty($currentTab)],
                ['key' => 'children', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-children', 'label' => __('My Children'), 'active' => $currentTab === 'children'],
                ['key' => 'sessions', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-sessions', 'label' => __('Upcoming Sessions'), 'active' => $currentTab === 'sessions'],
                ['key' => 'attendance', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-attendance', 'label' => __('Attendance & Absences'), 'active' => $currentTab === 'attendance'],
                ['key' => 'assignments', 'route' => 'parent-portal', 'url' => route('parent-portal') . '#section-assignments', 'label' => __('Homework & Grades'), 'active' => $currentTab === 'assignments'],
            ];
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Models\SiteSetting::get('announcement_enabled') === '1'): ?>
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 text-white text-xs font-bold py-2 px-4 text-center border-b border-teal-500/30 flex items-center justify-center gap-2">
        <span><?php echo e(\App\Models\SiteSetting::get('announcement_text', '🎉 Fall Cohort 2026 Registration is Now Open!')); ?></span>
        <a href="<?php echo e(\App\Models\SiteSetting::get('announcement_link', '/courses')); ?>" class="underline font-extrabold hover:text-teal-300 focus-visible:outline-white">
            <?php echo e(app()->getLocale() === 'ar' ? 'التفاصيل والاشتراك ←' : 'Learn More →'); ?>

        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<header class="anim-nav sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[90px] lg:h-[100px] flex items-center justify-between gap-2 lg:gap-4">
        
        <a href="<?php echo e(route('home')); ?>" class="flex items-center group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 rounded-xl transition-all duration-300 shrink-0 py-1" aria-label="Elite Academy Homepage">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Elite Academy Logo" class="h-20 sm:h-24 lg:h-26 w-auto max-h-22 object-contain transition-transform duration-300 group-hover:scale-105">
        </a>

        
        <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 rtl:space-x-reverse text-xs lg:text-sm font-bold text-slate-800 shrink">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e($item['url'] ?? route($item['route'])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-2 py-1 lg:px-3 lg:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600',
                       'text-teal-700 font-extrabold bg-teal-50/90 border border-teal-200/80 shadow-xs' => $item['active'],
                       'text-slate-800 font-bold hover:text-teal-600 hover:bg-slate-100/90' => ! $item['active'],
                   ]); ?>"><?php echo e($item['label']); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>

        
        <div class="hidden md:flex items-center space-x-1.5 lg:space-x-2 rtl:space-x-reverse text-xs font-bold font-sans shrink-0">
            <a href="<?php echo e(route('lang.switch', ['locale' => $otherLocale])); ?>" class="px-2.5 py-1.5 rounded-xl text-slate-700 hover:text-slate-950 bg-slate-100/80 hover:bg-slate-200/80 uppercase border border-slate-200 transition-all font-sans font-bold shadow-xs whitespace-nowrap shrink-0" aria-label="Switch Language to <?php echo e(strtoupper($otherLocale)); ?>">
                🌐 <?php echo e(strtoupper($otherLocale)); ?>

            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-lift px-3.5 py-2 rounded-xl text-slate-800 hover:bg-slate-100 border border-slate-200/90 transition-all font-sans font-bold text-xs whitespace-nowrap shrink-0">
                    <?php echo e($loginText); ?>

                </a>
                <a href="<?php echo e(route('register')); ?>" class="btn-lift px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all font-sans font-extrabold text-xs whitespace-nowrap shrink-0">
                    ✨ <?php echo e($joinText); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e($portalUrl); ?>" class="btn-lift px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md font-sans font-extrabold text-xs flex items-center gap-1.5 whitespace-nowrap shrink-0">
                    <span>📊</span> <?php echo e($portalLabel); ?>

                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent()): ?>
                    <a href="<?php echo e(route('student.profile')); ?>" class="btn-lift px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200/90 font-sans font-bold text-xs flex items-center gap-1 whitespace-nowrap shrink-0 hidden lg:flex">
                        <span>👤</span> <?php echo e(app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile'); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline shrink-0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-2.5 py-2 rounded-xl text-slate-600 hover:text-red-600 hover:bg-red-50 transition-colors font-sans font-bold text-xs whitespace-nowrap shrink-0">
                        <?php echo e(app()->getLocale() === 'ar' ? 'خروج' : 'Logout'); ?>

                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="flex items-center gap-2 md:hidden">
            <a href="<?php echo e(route('lang.switch', ['locale' => $otherLocale])); ?>" class="px-2.5 py-1.5 rounded-lg text-xs font-sans font-bold text-slate-700 bg-slate-100 uppercase border border-slate-200" aria-label="Switch Language">
                <?php echo e(strtoupper($otherLocale)); ?>

            </a>

            <label for="mobile-drawer-toggle" class="p-2 text-slate-800 hover:bg-slate-100 rounded-xl cursor-pointer touch-press" aria-label="Toggle Navigation Menu">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>
    </div>
</header>


<input type="checkbox" id="mobile-drawer-toggle" class="peer hidden">


<label for="mobile-drawer-toggle" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden peer-checked:flex transition-opacity duration-300 md:hidden"></label>


<div class="fixed top-0 right-0 bottom-0 w-[300px] bg-white z-50 shadow-2xl flex flex-col justify-between p-6 transform translate-x-full peer-checked:translate-x-0 transition-transform duration-300 ease-in-out rtl:right-auto rtl:left-0 rtl:-translate-x-full rtl:peer-checked:translate-x-0 md:hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Elite Academy Logo" class="h-14 sm:h-16 w-auto object-contain">
            <label for="mobile-drawer-toggle" class="p-2 text-slate-500 hover:text-slate-900 rounded-xl cursor-pointer font-bold text-lg">
                ✕
            </label>
        </div>

        <nav class="flex flex-col space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e($item['url'] ?? route($item['route'])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-4 py-3 rounded-2xl font-bold text-base transition-colors',
                       'bg-teal-50 text-teal-700 border border-teal-200/80 font-extrabold' => $item['active'],
                       'text-slate-800 hover:bg-slate-100/80' => ! $item['active'],
                   ]); ?>"><?php echo e($item['label']); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>
    </div>

    <div class="pt-6 border-t border-slate-100 space-y-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" class="btn-mobile-lg text-slate-800 bg-slate-100 hover:bg-slate-200 touch-press text-center font-bold text-sm"><?php echo e($loginText); ?></a>
            <a href="<?php echo e(route('register')); ?>" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center font-extrabold text-sm">✨ <?php echo e($joinText); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e($portalUrl); ?>" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center font-extrabold text-sm">📊 <?php echo e(auth()->user()->name); ?> (<?php echo e($portalLabel); ?>)</a>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-mobile-lg w-full text-red-600 bg-red-50 hover:bg-red-100 touch-press text-center font-bold text-sm"><?php echo e(app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log Out'); ?></button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php /**PATH C:\laragon\www\elite-academy\resources\views/partials/navbar.blade.php ENDPATH**/ ?>