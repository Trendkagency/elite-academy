@php
    $navLink = fn (string $key, string $route, string $label) => [
        'key' => $key,
        'route' => $route,
        'label' => $label,
        'active' => ($activeNav ?? null) === $key,
    ];
    $navItems = [
        $navLink('home', 'home', __('navbar.home')),
        $navLink('subjects', 'subjects', __('navbar.subjects')),
        $navLink('teachers', 'teachers', __('navbar.instructors')),
        $navLink('blog', 'blog', __('navbar.blog')),
        $navLink('about', 'about', __('navbar.about')),
        $navLink('contact', 'contact', __('navbar.contact')),
    ];
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
@endphp

<header class="anim-nav sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[70px] lg:h-[80px] flex items-center justify-between gap-4">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center group focus-visible:outline-teal-600 rounded-xl py-1 transition-all duration-300">
            <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-[42px] md:h-[50px] lg:h-[60px] w-auto object-contain transition-transform duration-250 ease-out group-hover:scale-[1.03]">
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hidden md:flex items-center gap-7 font-medium text-sm text-slate-600">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   @class([
                       'py-1 transition-colors',
                       'text-teal-600 font-bold border-b-2 border-teal-600' => $item['active'],
                       'link-underline hover:text-teal-600' => ! $item['active'],
                   ])>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        {{-- Desktop Action CTA Buttons --}}
        <div class="hidden md:flex items-center gap-4">
            <a href="{{ route('lang.switch', $otherLocale) }}"
               class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-slate-700 hover:text-teal-600 px-3.5 py-2 rounded-xl border border-slate-200/90 bg-white/80 hover:bg-white shadow-2xs transition-all min-h-[44px]">
                <span>🌐</span>
                <span>{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}</span>
            </a>
            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-teal-600 transition-colors px-3.5 py-2 min-h-[44px] inline-flex items-center">{{ __('navbar.login') }}</a>
            <a href="{{ route('student-portal') }}" class="btn-lift text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 active:bg-teal-800 px-5 py-2.5 rounded-xl shadow-md shadow-teal-600/20 min-h-[44px] inline-flex items-center">{{ __('navbar.portal') }}</a>
        </div>

        {{-- Mobile Hamburger Toggle Button (Hidden on Desktop, Visible on Mobile Only) --}}
        <label for="nav-toggle" class="md:hidden touch-target flex items-center justify-center p-2.5 text-slate-700 hover:text-teal-600 bg-slate-100/80 hover:bg-slate-200/80 rounded-2xl cursor-pointer focus-within:ring-2 focus-within:ring-teal-600 touch-press border border-slate-200/60" aria-label="{{ __('navbar.menu') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </label>
    </div>
</header>

{{-- Mobile Drawer Overlay --}}
<input type="checkbox" id="nav-toggle" class="peer hidden">
<div class="fixed inset-0 z-50 drawer-backdrop opacity-0 pointer-events-none peer-checked:opacity-100 peer-checked:pointer-events-auto transition-opacity duration-300 md:hidden drawer-overlay">
    <div class="fixed inset-y-0 drawer-content w-[88vw] max-w-md bg-white/95 backdrop-blur-2xl p-6 sm:p-8 shadow-2xl flex flex-col justify-between rounded-t-[2.5rem] md:rounded-none z-50">
        <div>
            <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-16 sm:h-20 w-auto object-contain">
                </a>
                <label for="nav-toggle" class="inline-flex items-center justify-center p-2.5 rounded-full bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200 cursor-pointer touch-press" aria-label="{{ __('navbar.close') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </label>
            </div>

            <nav class="flex flex-col gap-2 py-6 font-semibold text-slate-800">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @class([
                           'min-h-[48px] text-[18px] py-3 px-4 rounded-2xl flex items-center justify-between touch-press',
                           'bg-teal-50/80 text-teal-700 font-bold border border-teal-100/50' => $item['active'],
                           'hover:bg-slate-50 text-slate-700' => ! $item['active'],
                       ])>
                        <span>{{ $item['label'] }}</span>
                        <span @class(['text-sm', 'text-teal-500' => $item['active'], 'text-slate-400' => ! $item['active']])>→</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex flex-col gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('lang.switch', $otherLocale) }}" class="btn-mobile-lg text-sm font-mono font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200/80 touch-press text-center">
                <span class="mr-2">🌐</span>
                <span>{{ $otherLocale === 'ar' ? 'العربية' : 'English' }}</span>
            </a>
            <a href="{{ route('login') }}" class="btn-mobile-lg text-slate-800 bg-slate-100 hover:bg-slate-200 touch-press text-center">{{ __('navbar.login') }}</a>
            <a href="{{ route('student-portal') }}" class="btn-mobile-lg text-white bg-teal-600 hover:bg-teal-700 shadow-lg shadow-teal-600/25 touch-press text-center">{{ __('navbar.portal') }}</a>
        </div>
    </div>
</div>
