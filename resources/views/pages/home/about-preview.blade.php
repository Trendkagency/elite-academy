@use('App\Models\SiteSetting')
{{-- About Elite Academy Preview Section --}}
<section class="py-16 md:py-24 lg:py-32 bg-[#FAFAF9] relative overflow-hidden">
    <div class="absolute top-1/2 -left-24 w-[36rem] h-[36rem] bg-teal-400/12 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>
    <div class="absolute bottom-10 right-0 w-[32rem] h-[32rem] bg-orange-400/10 rounded-full blur-3xl pointer-events-none -z-10 animate-float"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- MOBILE-ONLY ABOUT LAYOUT (<768px) --}}
        <div class="block md:hidden space-y-6 anim-about delay-1">
            <div class="space-y-2">
                <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION') }}
                </span>
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery') }}
                </h2>
                <p class="text-slate-600 text-sm font-medium leading-relaxed line-clamp-2">
                    {{ SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.') }}
                </p>
            </div>

            <div class="flex flex-row items-center gap-4">
                <div class="w-[40%] shrink-0">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/5] border border-slate-200 shadow-md">
                        <img src="{{ media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.png') }}" alt="Elite Academy Campus" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-2 left-2 text-[9px] font-mono font-bold text-white bg-slate-900/80 px-2 py-0.5 rounded-md backdrop-blur-xs">{{ __('Campus') }}</span>
                    </div>
                </div>

                <div class="w-[60%] space-y-3.5">
                    @foreach ([
                        ['icon' => '🎓', 'title' => __('Expert Mentors'), 'desc' => __('PhD faculty guidance.')],
                        ['icon' => '💻', 'title' => __('Practical Learning'), 'desc' => __('Hands-on lab projects.')],
                        ['icon' => '🌍', 'title' => __('Global Certificates'), 'desc' => __('Accredited diplomas.')],
                        ['icon' => '🚀', 'title' => __('Career Support'), 'desc' => __('Job readiness tracks.')],
                    ] as $feat)
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-base font-bold shrink-0">
                                {{ $feat['icon'] }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading font-extrabold text-xs text-slate-900 leading-tight">{{ $feat['title'] }}</h3>
                                <p class="text-[10px] text-slate-500 font-medium leading-tight truncate">{{ $feat['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <a href="{{ route('about') }}" class="btn-lift w-full inline-flex items-center justify-center gap-2 text-sm font-extrabold text-white bg-teal-600 hover:bg-teal-700 min-h-[52px] rounded-2xl shadow-md touch-press">
                    <span>{{ __('Learn More') }}</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        {{-- DESKTOP / TABLET ABOUT LAYOUT (>=768px) --}}
        <div class="hidden md:grid md:grid-cols-12 md:gap-10 lg:gap-16 md:items-center">
            <div class="md:col-span-6 flex flex-col space-y-6 lg:space-y-8">
                <div class="space-y-3 sm:space-y-4">
                    <span class="anim-about delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                        {{ SiteSetting::getLocalized('about_badge', 'REDEFINING EDUCATION') }}
                    </span>
                    <h2 class="anim-about delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-slate-900 tracking-tight leading-[1.15]">
                        {{ SiteSetting::getLocalized('about_title', 'Where Passion Meets Academic Mastery') }}
                    </h2>
                    <p class="anim-about delay-3 sr-img text-slate-600 text-sm sm:text-base font-medium leading-relaxed">
                        {{ SiteSetting::getLocalized('about_content', 'Elite Academy bridges secondary education and real-world innovation through interactive live streams, structured MCQs, and expert teacher mentorship.') }}
                    </p>
                </div>

                <div class="anim-about delay-4 sr grid grid-cols-2 gap-4 border-t border-slate-200/80 pt-6">
                    @foreach ([
                        ['icon' => '🎓', 'title' => __('Expert Mentors')],
                        ['icon' => '💻', 'title' => __('Practical Learning')],
                        ['icon' => '🌍', 'title' => __('Global Certificates')],
                        ['icon' => '🚀', 'title' => __('Career Support')],
                    ] as $item)
                        <div class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-slate-900 group-hover:text-teal-600 group-hover:scale-110 flex items-center justify-center font-extrabold text-xl transition-all duration-300 shadow-xs flex-shrink-0">
                                {{ $item['icon'] }}
                            </div>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-teal-600 transition-colors leading-tight">
                                {{ $item['title'] }}
                            </h3>
                        </div>
                    @endforeach
                </div>

                <div class="anim-about delay-5 pt-1">
                    <a href="{{ route('about') }}" class="btn-lift group inline-flex items-center justify-center gap-2.5 text-sm sm:text-base font-extrabold text-white bg-teal-600 hover:bg-teal-700 active:bg-teal-800 px-8 py-4 rounded-2xl shadow-lg shadow-teal-600/20 touch-press">
                        <span>{{ __('Learn More') }}</span>
                        <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="md:col-span-6 relative anim-about delay-2">
                <div class="relative max-w-lg lg:max-w-none mx-auto">
                    <div class="relative rounded-[36px] p-3 bg-white border border-slate-200/90 shadow-2xl shadow-slate-900/15 group overflow-hidden card-lift">
                        <div class="relative rounded-[26px] overflow-hidden">
                            <img src="{{ media_url(\App\Models\SiteSetting::get('about_image'), 'images/academy_campus.png') }}" alt="Elite Academy Campus Photography" class="w-full h-80 sm:h-[400px] lg:h-[440px] object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-slate-950/10 to-transparent pointer-events-none"></div>
                            <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold uppercase tracking-wider text-white bg-slate-950/70 px-3 py-1 rounded-full backdrop-blur-xs border border-white/20">
                                🏫 Campus Life
                            </span>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -right-4 lg:-bottom-8 lg:-right-6 w-40 sm:w-48 h-28 sm:h-32 rounded-2xl overflow-hidden border-2 border-orange-500 shadow-xl z-20 card-lift group">
                        <img src="{{ asset('images/course_ai.png') }}" alt="AI Neural Research" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent pointer-events-none"></div>
                        <span class="absolute bottom-2 left-2.5 text-[9px] font-mono font-bold text-white bg-orange-600/90 px-2 py-0.5 rounded-full backdrop-blur-xs shadow-xs">
                            🧠 AI Research
                        </span>
                    </div>

                    <div class="hidden lg:block absolute top-1/3 -right-6 w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-xl z-30 animate-float group">
                        <img src="{{ asset('images/instructor_portrait.png') }}" alt="Faculty Mentor Session" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>

                    <div class="absolute bottom-6 left-6 z-30 glass-card bg-white/92 backdrop-blur-md p-4 rounded-2xl border border-white/80 shadow-2xl shadow-slate-950/15 space-y-2 animate-float max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-100/90 text-teal-700 flex items-center justify-center font-extrabold text-sm icon-rotate flex-shrink-0 shadow-xs">
                                ⭐
                            </div>
                            <div>
                                <p class="text-[9px] font-mono uppercase tracking-wider text-slate-500 font-bold">Trusted by</p>
                                <p class="text-xs font-extrabold text-slate-900">25,000+ Active Students</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[11px]">
                            <div>
                                <p class="text-amber-500 font-extrabold">★★★★★ 4.9</p>
                                <p class="text-[9px] text-slate-500 font-medium">Verified Rating</p>
                            </div>
                            <div>
                                <p class="text-teal-600 font-extrabold">120+ Courses</p>
                                <p class="text-[9px] text-slate-500 font-medium">Accredited Tracks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
