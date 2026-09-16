{{-- SECTION 3: ENROLLED COURSES & CURRICULUM ROADMAP (Full Table & Detailed Explorer) --}}
<section id="sectionPane_courses" class="student-section-pane space-y-6 {{ $activeTab === 'courses' ? '' : 'hidden' }}">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <h2 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fa-solid fa-book-open"></i>
                    </span>
                    <span>{{ $isAr ? 'الكورسات والمناهج التفصيلية المشترك بها' : 'My Enrolled Courses & Detailed Curriculum' }}</span>
                </h2>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'استكشف قائمة المقررات الدراسية، نسب الإنجاز، الدروس المسجلة، وحصص البث المباشر لكل كورس بالتفصيل.' : 'Inspect curriculum progress, teacher profiles, lecture modules, and direct session roadmaps.' }}
                </p>
            </div>

            <span class="text-xs font-mono font-extrabold bg-teal-100 dark:bg-teal-950 text-teal-900 dark:text-teal-200 px-3.5 py-1.5 rounded-full border border-teal-200 dark:border-teal-800 self-start sm:self-auto shadow-2xs">
                {{ count($enrollmentCards) }} {{ $isAr ? 'كورسات مفعلة' : 'Active Courses' }}
            </span>
        </div>

        @if(count($enrollmentCards) > 0)
            {{-- Comprehensive Full Information Table for Courses --}}
            <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                <table class="w-full text-start text-xs elite-sortable-table" data-page-size="6">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                        <tr>
                            <th class="py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الكورس والمقرر' : 'Course Title' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المادة' : 'Subject' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'المعلم المشرف' : 'Instructor' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="number">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الدروس والبث' : 'Modules & Streams' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors" data-sort-type="number">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'نسبة الإنجاز' : 'Progress' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="py-3.5 px-4 text-end" data-no-sort="true">{{ $isAr ? 'عرض التفاصيل والمنهج' : 'Curriculum Explorer' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono">
                        @foreach($enrollmentCards as $card)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                {{-- Course Title & Description --}}
                                <td class="py-3.5 px-4 allow-wrap">
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $card['course']->title }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1">
                                            {{ $card['course']->description ?: ($isAr ? 'مقرر دراسي تفاعلي شامل للمرحلة الثانوية.' : 'Interactive curriculum with assessments.') }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Subject --}}
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-600 text-white shadow-xs">
                                        {{ $card['subject'] }}
                                    </span>
                                </td>

                                {{-- Instructor --}}
                                <td class="py-3.5 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-teal-100 dark:bg-teal-900 flex items-center justify-center text-teal-800 dark:text-teal-300 font-bold text-[10px]">
                                            <i class="fa-solid fa-chalkboard-user"></i>
                                        </div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card['teacher'] }}</span>
                                    </div>
                                </td>

                                {{-- Modules & Streams Count --}}
                                <td class="py-3.5 px-3 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="space-y-0.5">
                                        <span><i class="fa-solid fa-video text-teal-600"></i> <strong>{{ $card['recCount'] }}</strong> {{ $isAr ? 'دروس' : 'Modules' }}</span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span><i class="fa-solid fa-circle text-emerald-500 text-[8px]"></i> <strong>{{ $card['liveCount'] }}</strong> {{ $isAr ? 'بث' : 'Live' }}</span>
                                    </div>
                                </td>

                                {{-- Progress Bar --}}
                                <td class="py-3.5 px-3 whitespace-nowrap min-w-[140px]">
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-[11px] font-bold">
                                            <span class="text-slate-500">{{ $isAr ? 'مكتمل' : 'Done' }}</span>
                                            <span class="text-teal-600 dark:text-teal-400 font-extrabold">{{ $card['progressPct'] }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full" style="width: {{ max(8, $card['progressPct']) }}%;"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-end whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="openEnrolledCourseModal({{ $card['course']->id }})"
                                            class="btn-lift px-3.5 py-1.5 bg-slate-900 dark:bg-slate-800 hover:bg-teal-600 text-white rounded-xl font-bold text-xs shadow-xs inline-flex items-center gap-1.5 cursor-pointer">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <span>{{ $isAr ? 'التفاصيل والحصص' : 'Syllabus & Streams' }}</span>
                                        </button>
                                        <a href="{{ route('course-details', ['slug' => $card['course']->slug]) }}"
                                            class="btn-lift px-2.5 py-1.5 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950 dark:hover:bg-teal-900 text-teal-800 dark:text-teal-300 rounded-xl font-bold text-xs border border-teal-200 dark:border-teal-800"
                                            title="{{ $isAr ? 'صفحة الكورس' : 'Course Details' }}">
                                            <span>▶</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                <div class="text-4xl text-teal-600"><i class="fa-solid fa-book-open"></i></div>
                <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base">
                    {{ $isAr ? 'لم تقم بالتسجيل في أي كورس بعد' : 'No Enrolled Courses Found' }}
                </h3>
                <p class="text-xs font-mono text-slate-500 max-w-md mx-auto">
                    {{ $isAr ? 'تصفح قائمة الكورسات والمناهج المتاحة في الأكاديمية وقم بالتسجيل فوراً لبدء التعلم.' : 'Browse the catalog to enroll in courses and unlock interactive sessions.' }}
                </p>
                <div class="pt-2">
                    <a href="{{ route('courses') }}" class="btn-lift inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold font-mono rounded-xl shadow-md">
                        <i class="fa-solid fa-rocket"></i> {{ $isAr ? 'تصفح الكورسات المتاحة الآن' : 'Browse Courses Catalog' }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
