{{-- CTA Section --}}
<section class="py-20 md:py-28 bg-gradient-to-br from-teal-950 via-slate-900 to-teal-900 text-white relative overflow-hidden">
    {{-- Ambient Blurs --}}
    <div class="absolute -top-12 left-1/4 w-[30rem] h-[30rem] bg-teal-500/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
    <div class="absolute -bottom-12 right-1/4 w-[30rem] h-[30rem] bg-orange-500/15 rounded-full blur-3xl pointer-events-none animate-float"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 sm:space-y-8 relative z-10">

        <span class="anim-cta delay-1 sr-h inline-block text-xs sm:text-sm font-mono font-extrabold uppercase tracking-widest text-teal-300 bg-teal-900/80 px-4 py-2 rounded-full border border-teal-500/30 animate-badge-pulse">
            🚀 Ready To Start Learning?
        </span>

        <h2 class="anim-cta delay-2 sr-h font-heading font-black text-3xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-tight">
            Join Egypt's Leading <span class="text-teal-400 underline decoration-orange-500 underline-offset-8">Educational Platform</span>
        </h2>

        <p class="anim-cta delay-3 sr-sub text-slate-300 text-base sm:text-lg font-medium max-w-2xl mx-auto leading-relaxed">
            Learn from the best instructors, master future-ready skills, and achieve accredited credentials.
        </p>

        <div class="anim-cta delay-4 sr-btn flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
            <a href="{{ route('subjects') }}" class="btn-lift btn-mobile-lg sm:w-auto text-base font-extrabold text-white bg-teal-600 hover:bg-teal-500 px-8 py-4 rounded-2xl shadow-xl shadow-teal-600/30 touch-press">
                Explore Subjects →
            </a>
            <a href="{{ route('student-portal') }}" class="btn-lift btn-mobile-lg sm:w-auto text-base font-extrabold text-slate-200 bg-slate-800/80 hover:bg-slate-700 px-8 py-4 rounded-2xl border border-slate-700/80 touch-press">
                Student Portal
            </a>
        </div>

    </div>
</section>

{{-- Compact Stats Strip --}}
<section class="bg-slate-950 py-8 border-y border-slate-800 text-slate-300">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-4 text-center divide-x divide-slate-800/80">

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-teal-400" data-count="150+">150+</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">Teachers</p>
            </div>

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-orange-400" data-count="250+">250+</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">Courses</p>
            </div>

            <div class="space-y-1">
                <p class="font-heading font-black text-xl sm:text-3xl text-teal-400" data-count="25K+">25K+</p>
                <p class="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-slate-400">Students</p>
            </div>

        </div>
    </div>
</section>
