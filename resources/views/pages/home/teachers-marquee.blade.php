{{-- Featured Mentors Showcase Section --}}
<section class="py-16 md:py-24 bg-[#FAFAF9] relative overflow-hidden border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Featured Mentors Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
            <div class="space-y-3">
                <span class="anim-projects delay-1 inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80 animate-badge-pulse">
                    OUR FACULTY
                </span>
                <h2 class="anim-projects delay-2 font-heading text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Meet Our <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Featured Mentors.</span>
                </h2>
            </div>
            <span class="text-xs font-mono text-slate-500 font-medium">&larr; Swipe Teachers &rarr;</span>
        </div>

        {{-- Mentor Slider Carousel --}}
        @php
            $mentors = [
                ['name' => 'Dr. Ahmed Hassan', 'title' => 'Senior AI & Systems Researcher', 'dept' => 'Programming', 'badgeBg' => 'bg-teal-600', 'textColor' => 'group-hover:text-teal-300', 'meta' => '15+ Yrs Exp • 1,400+ Students • PhD - MIT', 'photo' => 'images/instructor_portrait.png'],
                ['name' => 'Sarah Mohamed', 'title' => 'Deep Learning Lead Architect', 'dept' => 'Artificial Intelligence', 'badgeBg' => 'bg-purple-600', 'textColor' => 'group-hover:text-purple-300', 'meta' => '12+ Yrs Exp • 1,100+ Students • MSc - Stanford', 'photo' => 'images/instructor_female.png'],
                ['name' => 'Omar Khaled', 'title' => 'Robotics & Autonomous Systems Specialist', 'dept' => 'Robotics', 'badgeBg' => 'bg-orange-600', 'textColor' => 'group-hover:text-orange-300', 'meta' => '10+ Yrs Exp • 950+ Students • PhD - Cambridge', 'photo' => 'images/instructor_male.png'],
                ['name' => 'Fatma Ali', 'title' => 'Tech Ventures & Product Director', 'dept' => 'Business', 'badgeBg' => 'bg-emerald-600', 'textColor' => 'group-hover:text-emerald-300', 'meta' => '14+ Yrs Exp • 1,250+ Students • MBA - Harvard', 'photo' => 'images/hero_student.png'],
                ['name' => 'Mohamed Adel', 'title' => 'Information Security & Ethics Lead', 'dept' => 'Cyber Security', 'badgeBg' => 'bg-rose-600', 'textColor' => 'group-hover:text-rose-300', 'meta' => '11+ Yrs Exp • 880+ Students • CISSP - Oxford', 'photo' => 'images/course_ai.png'],
                ['name' => 'Dr. Nour Ibrahim', 'title' => 'Applied Mathematics Chair', 'dept' => 'Mathematics', 'badgeBg' => 'bg-amber-600', 'textColor' => 'group-hover:text-amber-300', 'meta' => '16+ Yrs Exp • 1,600+ Students • PhD - ETH Zurich', 'photo' => 'images/academy_campus.png'],
            ];
        @endphp

        <div class="carousel-container no-scrollbar">
            @foreach ($mentors as $m)
                <div class="carousel-card-large-peek anim-projects delay-3 rounded-3xl overflow-hidden shadow-xl border border-slate-200/80 h-96 relative group card-lift flex-shrink-0 transition-all duration-300">
                    <img src="{{ asset($m['photo']) }}" alt="{{ $m['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent pointer-events-none"></div>

                    <span class="absolute top-5 left-5 text-xs font-mono font-extrabold text-white {{ $m['badgeBg'] }} px-3.5 py-1.5 rounded-full shadow-md">
                        {{ $m['dept'] }}
                    </span>

                    <div class="absolute bottom-6 left-6 right-6 text-white space-y-2">
                        <div>
                            <h3 class="font-heading font-extrabold text-2xl text-white {{ $m['textColor'] }} transition-colors">
                                {{ $m['name'] }}
                            </h3>
                            <p class="text-xs font-mono text-slate-300 font-semibold">{{ $m['title'] }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-white/20 text-xs font-medium text-slate-200">
                            <span class="text-[11px] font-mono">{{ $m['meta'] }}</span>
                            <a href="{{ route('teacher-profile') }}" class="text-xs font-extrabold text-teal-300 group-hover:text-teal-200 flex items-center gap-1">
                                <span>View Profile</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
