<footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 text-center sm:text-left">
            <div class="sm:col-span-2 space-y-4 flex flex-col items-center sm:items-start">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logo.png') }}" alt="Elite Academy Logo" class="h-16 sm:h-20 w-auto object-contain mx-auto sm:mx-0">
                </a>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm text-center sm:text-left">
                    {{ __('footer.tagline') }}
                </p>
            </div>

            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ __('footer.quick_links') }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('home') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.nav_home') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.nav_about') }}</a></li>
                    <li><a href="{{ route('teachers') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.nav_instructors') }}</a></li>
                    <li><a href="{{ route('events') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.nav_events') }}</a></li>
                    <li><a href="{{ route('blog') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.nav_blog') }}</a></li>
                    <li><a href="{{ route('student-portal') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('navbar.portal') }}</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ __('footer.subjects') }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_programming') }}</a></li>
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_ai') }}</a></li>
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_science') }}</a></li>
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_business') }}</a></li>
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_design') }}</a></li>
                    <li><a href="{{ route('subject-details') }}" class="hover:text-teal-400 transition-colors link-underline">{{ __('footer.subject_math') }}</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h4 class="font-heading font-extrabold text-sm text-white uppercase tracking-wider">{{ __('footer.contact') }}</h4>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li class="flex items-center justify-center sm:justify-start gap-2">📍 <span>{{ __('footer.address') }}</span></li>
                    <li class="flex items-center justify-center sm:justify-start gap-2">📞 <span>{{ __('footer.phone') }}</span></li>
                    <li class="flex items-center justify-center sm:justify-start gap-2">✉️ <span>{{ __('footer.email') }}</span></li>
                    <li class="flex items-center justify-center sm:justify-start gap-2">🕒 <span>{{ __('footer.hours') }}</span></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-400">
            <div class="flex items-center gap-4">
                <a href="#" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Facebook">f</a>
                <a href="#" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Twitter">𝕏</a>
                <a href="#" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="Instagram">ig</a>
                <a href="#" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="LinkedIn">in</a>
                <a href="#" class="social-icon w-9 h-9 rounded-full bg-slate-800 hover:bg-teal-600 text-white flex items-center justify-center font-bold text-sm transition-all duration-300" aria-label="YouTube">yt</a>
            </div>
            <p>{{ __('footer.rights') }}</p>
        </div>
    </div>
</footer>
