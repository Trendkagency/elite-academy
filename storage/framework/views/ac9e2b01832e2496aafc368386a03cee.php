<?php
    $navLink = fn (string $key, string $route, string $label) => [
        'key' => $key,
        'route' => $route,
        'label' => $label,
        'active' => ($activeNav ?? null) === $key,
    ];
    $navItems = [
        $navLink('home', 'home', __('navbar.home') !== 'navbar.home' ? __('navbar.home') : 'Home'),
        $navLink('subjects', 'subjects', __('navbar.subjects') !== 'navbar.subjects' ? __('navbar.subjects') : 'Subjects'),
        $navLink('courses', 'courses', __('navbar.courses') !== 'navbar.courses' ? __('navbar.courses') : (app()->getLocale() === 'ar' ? 'المقررات' : 'Courses')),
        $navLink('teachers', 'teachers', __('navbar.teachers') !== 'navbar.teachers' ? __('navbar.teachers') : (app()->getLocale() === 'ar' ? 'المعلمون' : 'Teachers')),
        $navLink('blog', 'blog', __('navbar.blog') !== 'navbar.blog' ? __('navbar.blog') : 'Blog'),
        $navLink('about', 'about', __('navbar.about') !== 'navbar.about' ? __('navbar.about') : 'About'),
        $navLink('contact', 'contact', __('navbar.contact') !== 'navbar.contact' ? __('navbar.contact') : 'Contact'),
    ];
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $joinText = app()->getLocale() === 'ar' ? 'انضم إلينا' : 'Join Us';
    $loginText = app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Log in';

    $authUser = auth()->user();
    $portalUrl = route('student-portal');
    $portalLabel = __('navbar.portal');
    if ($authUser) {
        if ($authUser->isAdmin()) {
            $portalUrl = url('/admin');
            $portalLabel = __('navbar.admin_panel');
        } elseif ($authUser->isTeacher()) {
            $portalUrl = route('teachers');
            $portalLabel = app()->getLocale() === 'ar' ? 'بوابة المعلم' : 'Teacher Portal';
        } elseif ($authUser->isParent()) {
            $portalUrl = route('parent-portal');
            $portalLabel = __('navbar.parent_portal');
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Models\SiteSetting::get('announcement_enabled') === '1'): ?>
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 text-white text-xs font-semibold py-2 px-4 text-center border-b border-teal-500/30 flex items-center justify-center gap-2">
        <span><?php echo e(\App\Models\SiteSetting::get('announcement_text', '🎉 Fall Cohort 2026 Registration is Now Open!')); ?></span>
        <a href="<?php echo e(\App\Models\SiteSetting::get('announcement_link', '/courses')); ?>" class="underline font-bold hover:text-teal-300">
            <?php echo e(app()->getLocale() === 'ar' ? 'التفاصيل والاشتراك ←' : 'Learn More →'); ?>

        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<header class="anim-nav sticky z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 h-[64px] flex items-center justify-between gap-4">
        
        <a href="<?php echo e(route('home')); ?>" class="flex items-center group focus-visible:outline-teal-600 rounded-xl transition-all duration-300 flex-shrink-0">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Elite Academy Logo" class="h-20 sm:h-11 w-auto max-h-11 object-contain">
        </a>

        
        <nav class="hidden md:flex items-center space-x-1 lg:space-x-3 rtl:space-x-reverse text-sm font-semibold text-slate-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-3 py-1.5 rounded-xl transition-colors whitespace-nowrap',
                       'text-teal-600 font-bold bg-teal-50/80 border border-teal-100/50' => $item['active'],
                       'hover:text-teal-600 hover:bg-slate-100/80' => ! $item['active'],
                   ]); ?>"><?php echo e($item['label']); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>

        
        <div class="hidden md:flex items-center space-x-3 rtl:space-x-reverse text-xs font-bold font-mono">
            <a href="<?php echo e(route('lang.switch', ['locale' => $otherLocale])); ?>" class="px-2.5 py-1 rounded-lg text-slate-500 hover:text-slate-900 uppercase border border-slate-200/60 transition-colors">
                <?php echo e($otherLocale); ?>

            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>" class="btn-lift px-3.5 py-2 rounded-xl text-slate-700 hover:bg-slate-100 border border-slate-200/80 transition-all font-sans font-bold text-xs whitespace-nowrap">
                    <?php echo e($loginText); ?>

                </a>
                <a href="<?php echo e(route('register')); ?>" class="btn-lift px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all font-sans font-bold text-xs whitespace-nowrap">
                    <?php echo e($joinText); ?>

                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e($portalUrl); ?>" class="btn-lift px-3.5 py-2 rounded-xl bg-teal-600 text-white shadow-md font-sans font-bold text-xs flex items-center gap-1.5 whitespace-nowrap">
                    <span>📊</span> <?php echo e($portalLabel); ?>

                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent()): ?>
                    <a href="<?php echo e(route('student.profile')); ?>" class="btn-lift px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200/90 font-sans font-bold text-xs flex items-center gap-1.5 whitespace-nowrap">
                        <span>👤</span> <?php echo e(app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile'); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:text-red-600 transition-colors font-sans text-xs">
                        <?php echo e(app()->getLocale() === 'ar' ? 'خروج' : 'Logout'); ?>

                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="flex items-center gap-2 md:hidden">
            <a href="<?php echo e(route('lang.switch', ['locale' => $otherLocale])); ?>" class="px-2 py-1 rounded-lg text-xs font-mono font-bold text-slate-600 uppercase border border-slate-200">
                <?php echo e($otherLocale); ?>

            </a>

            <label for="mobile-drawer-toggle" class="p-2 text-slate-700 hover:bg-slate-100 rounded-xl cursor-pointer touch-press" aria-label="Toggle Navigation Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>
    </div>
</header>


<input type="checkbox" id="mobile-drawer-toggle" class="peer hidden">


<label for="mobile-drawer-toggle" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden peer-checked:flex transition-opacity duration-300 md:hidden"></label>


<div class="fixed top-0 right-0 bottom-0 w-[280px] bg-white z-50 shadow-2xl flex flex-col justify-between p-6 transform translate-x-full peer-checked:translate-x-0 transition-transform duration-300 ease-in-out rtl:right-auto rtl:left-0 rtl:-translate-x-full rtl:peer-checked:translate-x-0 md:hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Elite Academy Logo" class="h-8 w-auto">
            <label for="mobile-drawer-toggle" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg cursor-pointer">
                ✕
            </label>
        </div>

        <nav class="flex flex-col space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-4 py-2.5 rounded-xl font-bold text-sm transition-colors',
                       'bg-teal-50 text-teal-600 border border-teal-100' => $item['active'],
                       'text-slate-700 hover:bg-slate-50' => ! $item['active'],
                   ]); ?>"><?php echo e($item['label']); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>
    </div>

    <div class="pt-6 border-t border-slate-100 space-y-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <a href="<?php echo e(route('login')); ?>" class="btn-mobile-lg text-slate-800 bg-slate-100 hover:bg-slate-200 touch-press text-center"><?php echo e($loginText); ?></a>
            <a href="<?php echo e(route('register')); ?>" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center"><?php echo e($joinText); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e($portalUrl); ?>" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center">👤 <?php echo e(auth()->user()->name); ?> (<?php echo e($portalLabel); ?>)</a>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-mobile-lg w-full text-red-600 bg-red-50 hover:bg-red-100 touch-press text-center font-bold"><?php echo e(app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log Out'); ?></button>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/partials/navbar.blade.php ENDPATH**/ ?>