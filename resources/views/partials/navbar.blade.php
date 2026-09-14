@php
    $navLink = fn (string $key, string $route, string $label) => [
        'key' => $key,
        'route' => $route,
        'label' => $label,
        'active' => ($activeNav ?? null) === $key,
    ];
    $primaryNavItems = [
        $navLink('home', 'home', __('Home')),
        $navLink('subjects', 'subjects', __('Subjects')),
        $navLink('courses', 'courses', __('Courses')),
        $navLink('teachers', 'teachers', __('Teachers')),
        $navLink('blog', 'blog', __('Blog')),
    ];
    $secondaryNavItems = [
        $navLink('reviews', 'reviews', __('Reviews & Ratings')),
        $navLink('faq', 'faq', __('FAQ')),
        $navLink('about', 'about', __('About')),
        $navLink('contact', 'contact', __('Contact')),
    ];
    $navItems = array_merge($primaryNavItems, $secondaryNavItems);
    $hasActiveSecondary = collect($secondaryNavItems)->contains('active', true);
    $isCustomPortalNav = false;
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
            $isCustomPortalNav = true;

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
            $isCustomPortalNav = true;

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
@endphp

@if(\App\Models\SiteSetting::get('announcement_enabled') === '1')
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 text-white text-xs font-bold py-2 px-4 text-center border-b border-teal-500/30 flex items-center justify-center gap-2">
        <span><i class="fa-solid fa-sparkles text-amber-400"></i> {{ __('Fall Cohort 2026 Registration is Now Open!') }}</span>
        <a href="{{ \App\Models\SiteSetting::get('announcement_link', '/courses') }}" class="underline font-extrabold hover:text-teal-300 focus-visible:outline-white" aria-label="Explore Fall 2026 Cohort Registration and Details">
            {{ __('Explore Cohort Details →') }}
        </a>
    </div>
@endif

<header class="anim-nav sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/90 dark:border-slate-800/90 shadow-xs transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[90px] lg:h-[100px] flex items-center justify-between gap-2 lg:gap-4">
        {{-- Logo (Significantly Increased Size) --}}
        <a href="{{ route('home') }}" class="flex items-center group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 rounded-xl transition-all duration-300 shrink-0 py-1" aria-label="Elite Academy Homepage">
            <img src="{{ asset('images/logo_500.webp') }}" alt="Elite Academy Logo" width="249" height="56" class="h-20 sm:h-24 lg:h-26 w-auto max-h-22 object-contain transition-transform duration-300 group-hover:scale-105" fetchpriority="high">
        </a>

        {{-- Desktop Navigation Links (lg+) --}}
        <nav aria-label="{{ app()->getLocale() === 'ar' ? 'التنقل الرئيسي' : 'Main Navigation' }}"
             class="hidden lg:flex items-center gap-0.5 xl:gap-1 flex-nowrap text-xs xl:text-sm font-bold text-slate-800 dark:text-slate-200 min-w-0">
            @if ($isCustomPortalNav)
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] ?? route($item['route']) }}"
                       @class([
                           'px-2 py-1.5 xl:px-3 xl:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600',
                           'text-teal-700 dark:text-teal-300 font-extrabold bg-teal-50/90 dark:bg-teal-950/60 border border-teal-200/80 dark:border-teal-700/80 shadow-xs' => $item['active'],
                           'text-slate-800 dark:text-slate-200 font-bold hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100/90 dark:hover:bg-slate-800/90' => ! $item['active'],
                       ])>{{ $item['label'] }}</a>
                @endforeach
            @else
                {{-- Primary 5 Links --}}
                @foreach ($primaryNavItems as $item)
                    <a href="{{ $item['url'] ?? route($item['route']) }}"
                       @class([
                           'px-2 py-1.5 xl:px-3 xl:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600',
                           'text-teal-700 dark:text-teal-300 font-extrabold bg-teal-50/90 dark:bg-teal-950/60 border border-teal-200/80 dark:border-teal-700/80 shadow-xs' => $item['active'],
                           'text-slate-800 dark:text-slate-200 font-bold hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100/90 dark:hover:bg-slate-800/90' => ! $item['active'],
                       ])>{{ $item['label'] }}</a>
                @endforeach

                {{-- Secondary Links Dropdown ("المزيد" / "More") --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" @click="open = !open"
                            @class([
                                'px-2 py-1.5 xl:px-3 xl:py-2 rounded-xl transition-all whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 flex items-center gap-1 cursor-pointer font-bold',
                                'text-teal-700 dark:text-teal-300 font-extrabold bg-teal-50/90 dark:bg-teal-950/60 border border-teal-200/80 dark:border-teal-700/80 shadow-xs' => $hasActiveSecondary,
                                'text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-slate-100/90 dark:hover:bg-slate-800/90' => ! $hasActiveSecondary,
                            ])>
                        <span>{{ __('More') }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute top-full end-0 mt-1 w-44 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200/90 dark:border-slate-800 p-1.5 z-50 space-y-0.5"
                         style="display: none;">
                        @foreach ($secondaryNavItems as $secItem)
                            <a href="{{ $secItem['url'] ?? route($secItem['route']) }}"
                               @class([
                                   'block px-3 py-2 rounded-xl text-xs font-bold transition-colors',
                                   'bg-teal-50 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 font-extrabold' => $secItem['active'],
                                   'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-teal-600 dark:hover:text-teal-400' => ! $secItem['active'],
                               ])>
                                {{ $secItem['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </nav>

        {{-- Desktop Right Controls (lg+) --}}
        <div class="hidden lg:flex items-center gap-1.5 xl:gap-2 text-xs font-bold font-sans shrink-0">
            {{-- Dark / Light Theme Switcher --}}
            <button type="button"
                    onclick="window.EliteTheme ? window.EliteTheme.toggle() : null"
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-700 dark:text-slate-200 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 transition-all cursor-pointer shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                    aria-label="{{ app()->getLocale() === 'ar' ? 'تبديل المظهر (فاتح / داكن)' : 'Toggle Theme (Light / Dark)' }}"
                    title="{{ app()->getLocale() === 'ar' ? 'تبديل المظهر' : 'Toggle Theme' }}">
                <i class="fa-solid fa-moon hidden dark:inline-block text-amber-300"></i>
                <i class="fa-solid fa-sun inline-block dark:hidden text-amber-500"></i>
            </button>

            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}"
               class="px-2.5 py-1.5 rounded-xl text-slate-700 dark:text-slate-200 hover:text-slate-950 dark:hover:text-white bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-200/80 dark:hover:bg-slate-700/80 uppercase border border-slate-200 dark:border-slate-700 transition-all font-sans font-bold shadow-xs whitespace-nowrap shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
               aria-label="Switch Language to {{ strtoupper($otherLocale) }}">
                <i class="fa-solid fa-globe"></i> {{ strtoupper($otherLocale) }}
            </a>

            @guest
                <a href="{{ route('login') }}" class="btn-lift px-3.5 py-2 rounded-xl text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/90 dark:border-slate-700 transition-all font-sans font-bold text-xs whitespace-nowrap shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    {{ $loginText }}
                </a>
                <a href="{{ route('register') }}" class="btn-lift px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all font-sans font-extrabold text-xs whitespace-nowrap shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> {{ $joinText }}
                </a>
            @endguest

            @auth
                <a href="{{ $portalUrl }}" class="btn-lift px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md font-sans font-extrabold text-xs flex items-center gap-1.5 whitespace-nowrap shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <span><i class="fa-solid fa-chart-column"></i></span> {{ $portalLabel }}
                </a>
                @if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent())
                    <a href="{{ route('student.profile') }}" class="btn-lift px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200/90 dark:border-slate-700 font-sans font-bold text-xs flex items-center gap-1 whitespace-nowrap shrink-0 hidden xl:flex focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        <span><i class="fa-solid fa-user"></i></span> {{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline shrink-0">
                    @csrf
                    <button type="submit" class="px-2.5 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors font-sans font-bold text-xs whitespace-nowrap shrink-0 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600">
                        {{ app()->getLocale() === 'ar' ? 'خروج' : 'Logout' }}
                    </button>
                </form>
            @endauth
        </div>

        {{-- Mobile Hamburger, Theme & Language Controls (below lg) --}}
        <div class="flex items-center gap-2 lg:hidden">
            <button type="button"
                    onclick="window.EliteTheme ? window.EliteTheme.toggle() : null"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                    aria-label="{{ app()->getLocale() === 'ar' ? 'تبديل المظهر' : 'Toggle Theme' }}">
                <i class="fa-solid fa-moon hidden dark:inline-block text-amber-300 text-xs"></i>
                <i class="fa-solid fa-sun inline-block dark:hidden text-amber-500 text-xs"></i>
            </button>

            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}"
               class="px-2.5 py-1.5 rounded-lg text-xs font-sans font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 uppercase border border-slate-200 dark:border-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
               aria-label="Switch Language to {{ strtoupper($otherLocale) }}">
                <i class="fa-solid fa-globe"></i> {{ strtoupper($otherLocale) }}
            </a>

            <label for="mobile-drawer-toggle"
                   class="p-2 text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl cursor-pointer touch-press focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                   aria-label="Toggle Navigation Menu" aria-controls="mobile-drawer-panel" role="button" tabindex="0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>
    </div>
</header>

{{-- Mobile Drawer Navigation --}}
<input type="checkbox" id="mobile-drawer-toggle" class="peer hidden" aria-hidden="true">

{{-- Drawer Backdrop --}}
<label for="mobile-drawer-toggle"
       class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden peer-checked:flex transition-opacity duration-300 lg:hidden cursor-pointer"
       aria-label="Close Mobile Navigation Menu" role="button"></label>

{{-- Drawer Content Panel (Scrollable with Momentum Scrolling) --}}
<div id="mobile-drawer-panel"
     class="fixed inset-y-0 h-full max-h-screen w-[310px] sm:w-[340px] max-w-[85vw] bg-white dark:bg-slate-900 z-50 shadow-2xl overflow-y-auto overscroll-contain transform transition-transform duration-300 ease-in-out lg:hidden border-slate-200 dark:border-slate-800 custom-scrollbar
            ltr:right-0 ltr:translate-x-full ltr:peer-checked:translate-x-0 ltr:border-l
            rtl:left-0 rtl:-translate-x-full rtl:peer-checked:translate-x-0 rtl:border-r"
     style="-webkit-overflow-scrolling: touch;">
    <div class="min-h-full flex flex-col justify-between p-5 sm:p-6 gap-6">
        <div class="space-y-4">
            {{-- Drawer Header with Logo and Close Button --}}
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <a href="{{ route('home') }}" class="focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 rounded-lg">
                    <img src="{{ asset('images/logo_500.webp') }}" alt="Elite Academy Logo" width="160" height="36" class="h-12 sm:h-14 w-auto object-contain" loading="lazy">
                </a>
                <label for="mobile-drawer-toggle"
                       class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors cursor-pointer font-bold text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600"
                       aria-label="Close Mobile Navigation Menu" role="button" tabindex="0">
                    <i class="fa-solid fa-xmark"></i>
                </label>
            </div>

            {{-- Navigation Links List --}}
            <nav aria-label="{{ app()->getLocale() === 'ar' ? 'تنقل القائمة الجانبية للهاتف' : 'Mobile Drawer Navigation' }}" class="flex flex-col space-y-1.5">
                @foreach ($navItems as $item)
                    <a href="{{ $item['url'] ?? route($item['route']) }}"
                       @class([
                           'flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600',
                           'bg-teal-50 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 border border-teal-200/80 dark:border-teal-700/80 font-extrabold shadow-xs' => $item['active'],
                           'text-slate-700 dark:text-slate-200 hover:bg-slate-100/90 dark:hover:bg-slate-800/90 hover:text-teal-600 dark:hover:text-teal-400' => ! $item['active'],
                       ])>
                        <span>{{ $item['label'] }}</span>
                        @if($item['active'])
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Bottom Auth and Portal Actions (Always reachable by scrolling) --}}
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2.5 shrink-0 pb-6">
            @guest
                <a href="{{ route('login') }}" class="btn-mobile-lg text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 touch-press text-center font-bold text-sm flex items-center justify-center gap-2 shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <i class="fa-solid fa-arrow-right-to-bracket text-teal-600 dark:text-teal-400"></i>
                    <span>{{ $loginText }}</span>
                </a>
                <a href="{{ route('register') }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/25 touch-press text-center font-extrabold text-sm flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i>
                    <span>{{ $joinText }}</span>
                </a>
            @endguest
            @auth
                <a href="{{ $portalUrl }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/25 touch-press text-center font-extrabold text-sm flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>{{ auth()->user()->name }} ({{ $portalLabel }})</span>
                </a>
                @if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent())
                    <a href="{{ route('student.profile') }}" class="btn-mobile-lg text-slate-800 dark:text-slate-100 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 touch-press text-center font-bold text-sm flex items-center justify-center gap-2 shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-600">
                        <i class="fa-solid fa-user text-teal-600 dark:text-teal-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}</span>
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-mobile-lg w-full text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/40 border border-red-200/60 dark:border-red-900/50 touch-press text-center font-bold text-sm cursor-pointer flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log Out' }}</span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('mobile-drawer-toggle');
        if (toggle) {
            toggle.addEventListener('change', function () {
                if (this.checked) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            });

            document.addEventListener('keydown', function (e) {
                if ((e.key === 'Escape' || e.key === 'Esc') && toggle.checked) {
                    toggle.checked = false;
                    document.body.classList.remove('overflow-hidden');
                }
            });
        }
    });
</script>

