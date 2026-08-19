@php
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
@endphp

@if(\App\Models\SiteSetting::get('announcement_enabled') === '1')
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-teal-950 text-white text-xs font-semibold py-2 px-4 text-center border-b border-teal-500/30 flex items-center justify-center gap-2">
        <span>{{ \App\Models\SiteSetting::get('announcement_text', '🎉 Fall Cohort 2026 Registration is Now Open!') }}</span>
        <a href="{{ \App\Models\SiteSetting::get('announcement_link', '/courses') }}" class="underline font-bold hover:text-teal-300">
            {{ app()->getLocale() === 'ar' ? 'التفاصيل والاشتراك ←' : 'Learn More →' }}
        </a>
    </div>
@endif

<header class="anim-nav sticky z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 h-[64px] flex items-center justify-between gap-4">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center group focus-visible:outline-teal-600 rounded-xl transition-all duration-300 flex-shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-20 sm:h-11 w-auto max-h-11 object-contain">
        </a>

        {{-- Desktop Navigation Links --}}
        <nav class="hidden md:flex items-center space-x-1 lg:space-x-3 rtl:space-x-reverse text-sm font-semibold text-slate-700">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'px-3 py-1.5 rounded-xl transition-colors whitespace-nowrap',
                       'text-teal-600 font-bold bg-teal-50/80 border border-teal-100/50' => $item['active'],
                       'hover:text-teal-600 hover:bg-slate-100/80' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        {{-- Desktop Right Controls --}}
        <div class="hidden md:flex items-center space-x-3 rtl:space-x-reverse text-xs font-bold font-mono">
            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}" class="px-2.5 py-1 rounded-lg text-slate-500 hover:text-slate-900 uppercase border border-slate-200/60 transition-colors">
                {{ $otherLocale }}
            </a>

            @guest
                <a href="{{ route('login') }}" class="btn-lift px-3.5 py-2 rounded-xl text-slate-700 hover:bg-slate-100 border border-slate-200/80 transition-all font-sans font-bold text-xs whitespace-nowrap">
                    {{ $loginText }}
                </a>
                <a href="{{ route('register') }}" class="btn-lift px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white shadow-md shadow-teal-600/20 transition-all font-sans font-bold text-xs whitespace-nowrap">
                    {{ $joinText }}
                </a>
            @endguest

            @auth
                <a href="{{ $portalUrl }}" class="btn-lift px-3.5 py-2 rounded-xl bg-teal-600 text-white shadow-md font-sans font-bold text-xs flex items-center gap-1.5 whitespace-nowrap">
                    <span>📊</span> {{ $portalLabel }}
                </a>
                @if(! $authUser->isAdmin() && ! $authUser->isTeacher() && ! $authUser->isParent())
                    <a href="{{ route('student.profile') }}" class="btn-lift px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200/90 font-sans font-bold text-xs flex items-center gap-1.5 whitespace-nowrap">
                        <span>👤</span> {{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:text-red-600 transition-colors font-sans text-xs">
                        {{ app()->getLocale() === 'ar' ? 'خروج' : 'Logout' }}
                    </button>
                </form>
            @endauth
        </div>

        {{-- Mobile Hamburger Button --}}
        <div class="flex items-center gap-2 md:hidden">
            <a href="{{ route('lang.switch', ['locale' => $otherLocale]) }}" class="px-2 py-1 rounded-lg text-xs font-mono font-bold text-slate-600 uppercase border border-slate-200">
                {{ $otherLocale }}
            </a>

            <label for="mobile-drawer-toggle" class="p-2 text-slate-700 hover:bg-slate-100 rounded-xl cursor-pointer touch-press" aria-label="Toggle Navigation Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>
    </div>
</header>

{{-- Mobile Drawer Navigation --}}
<input type="checkbox" id="mobile-drawer-toggle" class="peer hidden">

{{-- Drawer Backdrop --}}
<label for="mobile-drawer-toggle" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden peer-checked:flex transition-opacity duration-300 md:hidden"></label>

{{-- Drawer Content Panel --}}
<div class="fixed top-0 right-0 bottom-0 w-[280px] bg-white z-50 shadow-2xl flex flex-col justify-between p-6 transform translate-x-full peer-checked:translate-x-0 transition-transform duration-300 ease-in-out rtl:right-auto rtl:left-0 rtl:-translate-x-full rtl:peer-checked:translate-x-0 md:hidden">
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-8 w-auto">
            <label for="mobile-drawer-toggle" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg cursor-pointer">
                ✕
            </label>
        </div>

        <nav class="flex flex-col space-y-2">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'px-4 py-2.5 rounded-xl font-bold text-sm transition-colors',
                       'bg-teal-50 text-teal-600 border border-teal-100' => $item['active'],
                       'text-slate-700 hover:bg-slate-50' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>

    <div class="pt-6 border-t border-slate-100 space-y-3">
        @guest
            <a href="{{ route('login') }}" class="btn-mobile-lg text-slate-800 bg-slate-100 hover:bg-slate-200 touch-press text-center">{{ $loginText }}</a>
            <a href="{{ route('register') }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center">{{ $joinText }}</a>
        @endguest
        @auth
            <a href="{{ $portalUrl }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center">👤 {{ auth()->user()->name }} ({{ $portalLabel }})</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-mobile-lg w-full text-red-600 bg-red-50 hover:bg-red-100 touch-press text-center font-bold">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log Out' }}</button>
            </form>
        @endauth
    </div>
</div>
