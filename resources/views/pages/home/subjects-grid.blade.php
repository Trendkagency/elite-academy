{{-- Subjects Showcase Grid Section --}}
<section class="py-20 md:py-28 bg-white border-y border-slate-200/80 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

        {{-- 2-Column Section Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="space-y-3 max-w-xl">
                <span class="anim-subject delay-1 sr-h inline-block text-xs font-mono uppercase tracking-widest text-teal-600 font-extrabold bg-teal-50 px-3.5 py-1.5 rounded-full border border-teal-200/80">
                    {{ __('OUR SUBJECTS') }}
                </span>
                <h2 class="anim-subject delay-2 sr-h font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ \App\Models\SiteSetting::getLocalized('subjects_title', __('Explore Specialized Subjects & Programs')) }}
                </h2>
            </div>

            <div class="hidden md:block w-px h-16 bg-slate-200/80 mx-2 flex-shrink-0"></div>

            <div class="max-w-md my-auto">
                <p class="text-slate-600 text-sm sm:text-base font-medium leading-relaxed">
                    {{ \App\Models\SiteSetting::getLocalized('subjects_subtitle', __('Cutting-edge curriculum designed by industry experts and academic researchers.')) }}
                </p>
            </div>
        </div>

        {{-- Dynamic Subjects 4-Column Grid --}}
        @php
            $dbSubjects = \App\Models\Subject::where('is_active', true)
                ->with(['category', 'courses'])
                ->orderBy('sort_order')
                ->take(8)
                ->get();
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8">
            @foreach ($dbSubjects as $sub)
                @php
                    $isModel = is_object($sub) && $sub instanceof \App\Models\Subject;
                    $subjectName = $isModel ? $sub->getLocalizedName() : (is_array($sub) ? ($sub['name'] ?? __('Subject')) : (string) $sub);
                    
                    $categoryName = __('General');
                    if ($isModel && $sub->category && is_object($sub->category)) {
                        $categoryName = method_exists($sub->category, 'getLocalizedName') ? $sub->category->getLocalizedName() : ($sub->category->name ?? __('General'));
                    } elseif (is_array($sub) && isset($sub['category'])) {
                        $categoryName = is_array($sub['category']) ? ($sub['category']['name'] ?? __('General')) : (string) $sub['category'];
                    }

                    $coursesCount = 0;
                    if ($isModel && $sub->courses) {
                        $coursesCount = $sub->courses->count();
                    } elseif (is_array($sub) && isset($sub['courses_count'])) {
                        $coursesCount = (int) $sub['courses_count'];
                    }

                    $slug = $isModel ? $sub->slug : (is_array($sub) ? ($sub['slug'] ?? null) : null);
                    $subjectUrl = $slug ? route('subject-details', ['slug' => $slug]) : route('subjects');
                    
                    $rawImage = $isModel ? $sub->image : (is_array($sub) ? ($sub['image'] ?? null) : null);
                    $image = media_url($rawImage, 'images/hero_student.webp');
                @endphp
                <div class="anim-subject delay-3 sr-card aspect-[4/5] md:aspect-auto md:h-[520px] rounded-[24px] bg-slate-950 text-white shadow-lg hover:shadow-2xl card-lift flex flex-col justify-between overflow-hidden group transition-all duration-300 relative active:scale-[0.98]">
                    <div class="absolute inset-0 md:relative md:h-[338px] overflow-hidden bg-slate-950">
                        <img src="{{ $image }}" alt="{{ $subjectName }}" width="400" height="338" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent pointer-events-none"></div>
                    </div>

                    <div class="absolute bottom-0 inset-x-0 p-3.5 sm:p-4 text-white z-10 flex flex-col justify-end space-y-1 md:relative md:p-6 md:flex-1 md:bg-slate-950 md:space-y-3">
                        <div class="space-y-1">
                            <span class="text-[9px] sm:text-[10px] font-mono font-extrabold uppercase tracking-widest text-teal-300 bg-slate-950/70 md:bg-transparent backdrop-blur-xs md:backdrop-blur-none px-2 py-0.5 md:p-0 rounded-full md:rounded-none border border-white/10 md:border-none inline-block w-max">
                                {{ $categoryName }}
                            </span>
                            <h3 class="font-heading font-extrabold text-sm sm:text-base md:text-2xl text-white group-hover:text-teal-300 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ $subjectUrl }}">{{ $subjectName }}</a>
                            </h3>
                        </div>

                        <div class="hidden md:flex items-center justify-between pt-3 border-t border-slate-800 text-xs text-slate-300 font-medium">
                            <span>📚 {{ $coursesCount }} {{ __('Courses') }}</span>
                            <a href="{{ $subjectUrl }}" class="text-xs font-extrabold text-teal-300 group-hover:text-teal-200 flex items-center gap-1" aria-label="View curriculum and details for {{ $subjectName }}">
                                <span>{{ __('View Details') }}</span>
                                <span class="group-hover:translate-x-1.5 transition-transform duration-300">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
