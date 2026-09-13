@extends('layouts.app')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-[#070A10] text-slate-900 dark:text-slate-100 transition-colors duration-300">

    {{-- ═══════════════════════════════════════════════════════════════
         1. HERO & STATS BANNER
         ═══════════════════════════════════════════════════════════════ --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-gradient-to-b from-teal-500/10 via-slate-50 to-slate-50 dark:from-teal-950/20 dark:via-[#070A10] dark:to-[#070A10] border-b border-slate-200/80 dark:border-slate-800/80">
        {{-- Background Glows --}}
        <div class="absolute top-10 left-1/4 w-[40rem] h-[24rem] bg-teal-500/10 dark:bg-teal-500/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-20 right-1/4 w-[35rem] h-[22rem] bg-indigo-500/10 dark:bg-indigo-500/15 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
                
                {{-- Left Text --}}
                <div class="space-y-5 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-mono font-bold tracking-wider uppercase bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-500/20 backdrop-blur-md shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                        <span>{{ $isAr ? 'تقييمات وآراء مجتمع إيليت' : 'COMMUNITY FEEDBACK & REVIEWS' }}</span>
                    </div>

                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.1]">
                        {{ $isAr ? 'تقييمات وتجارب' : 'What Students, Parents' }} 
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-teal-600 via-cyan-500 to-indigo-600 dark:from-teal-400 dark:via-cyan-300 dark:to-indigo-400">
                            {{ $isAr ? 'الطلاب وأولياء الأمور والمعلمين' : '& Teachers Say' }}
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                        {{ $isAr 
                            ? 'اكتشف تجارب حقيقية وتقييمات موثقة من الطلاب وأولياء الأمور ونخبة المعلمين عبر منصة إيليت الأكاديمية.' 
                            : 'Discover authentic experiences and verified reviews from students, parents, and expert instructors across Elite Academy.' }}
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <button
                            type="button"
                            onclick="openReviewModal()"
                            class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-heading font-extrabold text-sm sm:text-base shadow-xl shadow-teal-500/25 hover:shadow-teal-500/40 hover:scale-[1.02] active:scale-95 transition-all duration-300 cursor-pointer"
                        >
                            <i class="fa-solid fa-pen-nib text-base"></i>
                            <span>{{ $isAr ? 'أضف تقييمك ورأيك الآن' : 'Submit Your Review & Rating' }}</span>
                        </button>
                        
                        <a
                            href="#reviews-directory"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-white dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-400 font-heading font-bold text-sm sm:text-base border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-300"
                        >
                            <span>{{ $isAr ? 'تصفح كل التقييمات' : 'Browse All Reviews' }}</span>
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </a>
                    </div>
                </div>

                {{-- Right: Overall Rating Score Widget --}}
                <div class="w-full lg:w-[420px] bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl rounded-3xl p-7 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-2xl space-y-6">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                {{ $isAr ? 'التقييم الإجمالي العام' : 'OVERALL SATISFACTION' }}
                            </span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-5xl font-heading font-black text-slate-900 dark:text-white">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-base font-bold text-slate-400">/ 5.0</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center gap-1 text-amber-400 text-lg">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= round($avgRating) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-700' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mt-1 inline-block">
                                {{ $totalReviews }} {{ $isAr ? 'تقييم ومراجعة' : 'Verified Reviews' }}
                            </span>
                        </div>
                    </div>

                    {{-- Star Breakdown Bars --}}
                    <div class="space-y-2.5">
                        @foreach([5, 4, 3, 2, 1] as $star)
                            @php
                                $cnt = $ratingCounts[$star] ?? 0;
                                $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                            @endphp
                            <a href="{{ route('reviews', ['rating' => $star]) }}" class="flex items-center gap-3 text-xs font-mono group hover:text-teal-600 transition-colors">
                                <span class="w-12 font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                    <span>{{ $star }}</span>
                                    <i class="fa-solid fa-star text-amber-400 text-[10px]"></i>
                                </span>
                                <div class="flex-1 h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full group-hover:from-teal-500 group-hover:to-teal-400 transition-all duration-500" style="width: {{ $pct }}%;"></div>
                                </div>
                                <span class="w-10 text-right font-bold text-slate-400 group-hover:text-teal-600">{{ $pct }}%</span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Category Stats Pills --}}
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                        <div class="bg-teal-50/70 dark:bg-teal-950/40 p-2.5 rounded-2xl border border-teal-100 dark:border-teal-900/50">
                            <p class="text-lg font-heading font-black text-teal-600 dark:text-teal-400">{{ $roleCounts['student'] ?? 0 }}</p>
                            <p class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 uppercase">{{ $isAr ? 'طلاب' : 'Students' }}</p>
                        </div>
                        <div class="bg-purple-50/70 dark:bg-purple-950/40 p-2.5 rounded-2xl border border-purple-100 dark:border-purple-900/50">
                            <p class="text-lg font-heading font-black text-purple-600 dark:text-purple-400">{{ $roleCounts['parent'] ?? 0 }}</p>
                            <p class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 uppercase">{{ $isAr ? 'أولياء أمور' : 'Parents' }}</p>
                        </div>
                        <div class="bg-amber-50/70 dark:bg-amber-950/40 p-2.5 rounded-2xl border border-amber-100 dark:border-amber-900/50">
                            <p class="text-lg font-heading font-black text-amber-600 dark:text-amber-400">{{ $roleCounts['teacher'] ?? 0 }}</p>
                            <p class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 uppercase">{{ $isAr ? 'معلمون' : 'Teachers' }}</p>
                        </div>
                    </div>

                </div>{{-- /Score Widget --}}

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         2. REVIEWS DIRECTORY & REAL-TIME FILTERING
         ═══════════════════════════════════════════════════════════════ --}}
    <section id="reviews-directory" class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Filter Navigation Bar with Enhanced Spacing --}}
        <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-[28px] p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-[0_12px_36px_-10px_rgba(0,0,0,0.06)] dark:shadow-[0_12px_36px_-10px_rgba(0,0,0,0.4)] mb-16 sm:mb-20 flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            {{-- Real-Time Filter Tabs: All, Students, Parents, Teachers --}}
            <div id="realtime-filter-tabs" class="flex items-center gap-2.5 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
                <button
                    type="button"
                    data-tab-type="all"
                    onclick="filterReviewsRealtime('all')"
                    class="filter-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-heading font-extrabold transition-all duration-300 whitespace-nowrap flex items-center gap-2 cursor-pointer bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md scale-[1.02]"
                >
                    <i class="fa-solid fa-list-check text-xs"></i>
                    <span>{{ $isAr ? 'جميع التقييمات' : 'All Reviews' }}</span>
                    <span class="tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-white/20 dark:bg-slate-900/20 text-white dark:text-slate-900">{{ $totalReviews }}</span>
                </button>

                <button
                    type="button"
                    data-tab-type="student"
                    onclick="filterReviewsRealtime('student')"
                    class="filter-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-heading font-extrabold transition-all duration-300 whitespace-nowrap flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-teal-950/40 hover:text-teal-600 dark:hover:text-teal-400"
                >
                    <i class="fa-solid fa-user-graduate text-xs"></i>
                    <span>{{ $isAr ? 'تقييمات الطلاب' : 'Students' }}</span>
                    <span class="tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $roleCounts['student'] ?? 0 }}</span>
                </button>

                <button
                    type="button"
                    data-tab-type="parent"
                    onclick="filterReviewsRealtime('parent')"
                    class="filter-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-heading font-extrabold transition-all duration-300 whitespace-nowrap flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-purple-50 dark:hover:bg-purple-950/40 hover:text-purple-600 dark:hover:text-purple-400"
                >
                    <i class="fa-solid fa-hands-holding-child text-xs"></i>
                    <span>{{ $isAr ? 'تقييمات أولياء الأمور' : 'Parents' }}</span>
                    <span class="tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $roleCounts['parent'] ?? 0 }}</span>
                </button>

                <button
                    type="button"
                    data-tab-type="teacher"
                    onclick="filterReviewsRealtime('teacher')"
                    class="filter-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-heading font-extrabold transition-all duration-300 whitespace-nowrap flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 dark:hover:text-amber-400"
                >
                    <i class="fa-solid fa-chalkboard-user text-xs"></i>
                    <span>{{ $isAr ? 'تقييمات المعلمين' : 'Teachers' }}</span>
                    <span class="tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $roleCounts['teacher'] ?? 0 }}</span>
                </button>
            </div>

            {{-- Real-Time Search & Clear Filters --}}
            <div class="flex items-center gap-3">
                <div class="relative w-full sm:w-72">
                    <input
                        type="text"
                        id="realtime-search-input"
                        oninput="handleRealtimeSearch(this.value)"
                        placeholder="{{ $isAr ? 'ابحث في التقييمات فورياً...' : 'Instant search reviews...' }}"
                        class="w-full bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium transition-all"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                </div>

                <button
                    type="button"
                    id="realtime-clear-btn"
                    onclick="resetRealtimeFilters()"
                    class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900 hidden items-center justify-center text-xs font-mono font-bold hover:bg-rose-100 transition-colors shrink-0 shadow-xs cursor-pointer"
                    title="{{ $isAr ? 'إلغاء الفلاتر' : 'Clear Filters' }}"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        {{-- Reviews Cards Grid with Generous Spacing --}}
        <div id="reviews-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-9">
            @foreach($reviews as $review)
                @php
                    $badgeClass = $review->getBadgeClass();
                    $roleLabel = $review->getReviewerRoleLabel();
                    $roleIcon = match($review->reviewer_type) {
                        'parent'  => 'fa-hands-holding-child',
                        'teacher' => 'fa-chalkboard-user',
                        default   => 'fa-user-graduate',
                    };
                    $avatarUrl = $review->avatar ?: 'images/instructor_portrait.webp';
                    $searchText = mb_strtolower($review->name . ' ' . $review->content . ' ' . $review->course_name . ' ' . $roleLabel);
                @endphp

                <div
                    class="testim-card relative w-full min-h-[320px] bg-white dark:bg-slate-900/95 backdrop-blur-xl rounded-[28px] p-7 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-[0_12px_36px_-10px_rgba(0,0,0,0.07)] dark:shadow-[0_12px_36px_-10px_rgba(0,0,0,0.5)] hover:shadow-[0_24px_50px_-12px_rgba(20,184,166,0.22)] dark:hover:shadow-[0_24px_50px_-12px_rgba(20,184,166,0.3)] hover:border-teal-500/50 dark:hover:border-teal-400/50 flex flex-col justify-between group transition-all duration-300 hover:-translate-y-2"
                    data-reviewer-type="{{ $review->reviewer_type }}"
                    data-rating="{{ $review->rating }}"
                    data-search-text="{{ $searchText }}"
                >
                    {{-- Top Accent Gradient Bar --}}
                    <div class="absolute top-0 left-8 right-8 h-1 bg-gradient-to-r from-transparent via-teal-500/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full"></div>

                    {{-- Top Details: Rating Pill & Quote Icon & Role Badge --}}
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 dark:bg-amber-400/10 text-amber-600 dark:text-amber-400 rounded-full text-xs font-black font-mono border border-amber-500/20 shadow-xs">
                                <div class="flex items-center gap-0.5 text-[11px] text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-300 dark:text-slate-700' }}"></i>
                                    @endfor
                                </div>
                                <span class="ms-1">{{ number_format($review->rating, 1) }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 {{ $badgeClass }} text-[11px] font-mono font-extrabold px-3 py-1 rounded-full border shadow-2xs">
                                    <i class="fa-solid {{ $roleIcon }} text-[10px]"></i>
                                    <span>{{ $roleLabel }}</span>
                                </span>

                                <div class="w-9 h-9 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-teal-600 dark:text-teal-400 group-hover:bg-teal-500 group-hover:text-white transition-all duration-300 shadow-xs">
                                    <i class="fa-solid fa-quote-right text-xs"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Review Text --}}
                        <p class="font-sans font-normal text-slate-700 dark:text-slate-200 text-[15px] sm:text-[16px] leading-relaxed italic line-clamp-5 my-3 flex-1">
                            "{{ $review->content }}"
                        </p>
                    </div>

                    {{-- Footer: Author, Course & Verified Badge --}}
                    <div class="pt-5 mt-4 border-t border-slate-100 dark:border-slate-800/90 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="relative shrink-0">
                                <img
                                    src="{{ media_url($avatarUrl, 'images/instructor_portrait_128.webp') }}"
                                    alt="{{ $review->name }}"
                                    width="50" height="50"
                                    class="w-12 h-12 rounded-2xl object-cover shadow-md ring-2 ring-slate-200 dark:ring-slate-700 group-hover:ring-teal-500 transition-all duration-300"
                                    loading="lazy"
                                >
                                @if($review->is_verified)
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full flex items-center justify-center text-[8px] text-white" title="Verified Member">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="min-w-0 space-y-0.5">
                                <h3 class="font-heading font-extrabold text-[15px] text-slate-900 dark:text-white truncate group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                    {{ $review->name }}
                                </h3>
                                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 truncate font-semibold">
                                    {{ $review->course_name ?: __('Elite Academic Track') }}
                                </p>
                            </div>
                        </div>

                        <span class="text-[11px] font-mono text-slate-400 dark:text-slate-500 shrink-0 font-semibold">
                            {{ $review->created_at ? $review->created_at->diffForHumans() : __('Verified') }}
                        </span>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Real-Time Empty State --}}
        <div id="no-realtime-results" class="hidden text-center py-20 bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 mx-auto flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-comment-dots"></i>
            </div>
            <h3 class="font-heading text-xl font-extrabold text-slate-900 dark:text-white">
                {{ $isAr ? 'لم يتم العثور على تقييمات مطابقة' : 'No Reviews Found' }}
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                {{ $isAr ? 'جرب تغيير خيارات البحث أو الفلترة أو كن أول من يشارك رأيه.' : 'Try changing your search or filter criteria or be the first to share your experience.' }}
            </p>
            <div class="pt-2">
                <button
                    type="button"
                    onclick="resetRealtimeFilters()"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-teal-600 hover:bg-teal-500 text-white font-heading font-extrabold text-sm transition-all duration-200 cursor-pointer"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>{{ $isAr ? 'إعادة تعيين الفلاتر' : 'Reset All Filters' }}</span>
                </button>
            </div>
        </div>

        {{-- Pagination Links --}}
        @if($reviews->hasPages())
            <div class="mt-16 flex justify-center">
                {{ $reviews->links() }}
            </div>
        @endif

    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         3. SUBMIT REVIEW MODAL
         ═══════════════════════════════════════════════════════════════ --}}
    <div
        id="review-modal-backdrop"
        class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-md hidden items-center justify-center p-4 sm:p-6 overflow-y-auto transition-all duration-300"
        onclick="if(event.target === this) closeReviewModal()"
    >
        <div class="relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 my-8 animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <span class="inline-block text-[11px] font-mono font-extrabold uppercase tracking-wider text-teal-600 dark:text-teal-400">
                        {{ $isAr ? 'مشاركة التجربة' : 'SHARE YOUR FEEDBACK' }}
                    </span>
                    <h3 class="font-heading text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'أضف تقييمك ورأيك في المنصة' : 'Rate Your Elite Academy Experience' }}
                    </h3>
                </div>
                <button
                    type="button"
                    onclick="closeReviewModal()"
                    class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition-colors"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Review Form --}}
            <form id="review-submission-form" onsubmit="handleReviewSubmit(event)" class="space-y-5" enctype="multipart/form-data">
                @csrf

                {{-- Interactive Star Rating Selector --}}
                <div class="space-y-2 text-center py-2 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        {{ $isAr ? 'اختر تقييمك العام (1 - 5 نجوم)' : 'Select Your Overall Rating' }}
                    </label>
                    <div id="star-picker-container" class="flex items-center justify-center gap-2 text-2xl sm:text-3xl text-slate-300 dark:text-slate-700 cursor-pointer select-none">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="fa-solid fa-star star-btn transition-transform hover:scale-125 text-amber-400" data-val="{{ $s }}" onclick="setRatingValue({{ $s }})"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="review-rating-input" value="5">
                    <span id="rating-desc-text" class="text-xs font-mono font-bold text-amber-500">5 / 5 — {{ $isAr ? 'ممتاز جداً' : 'Excellent Experience' }}</span>
                </div>

                {{-- Reviewer Role Selection --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                        {{ $isAr ? 'أنت تمثل:' : 'You are submitting as:' }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="reviewer_type" value="student" class="peer sr-only" {{ $userRole === 'student' ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-2xl border border-slate-200 dark:border-slate-700 peer-checked:border-teal-500 peer-checked:bg-teal-50 dark:peer-checked:bg-teal-950/50 peer-checked:text-teal-600 dark:peer-checked:text-teal-400 transition-all font-heading font-extrabold text-xs">
                                <i class="fa-solid fa-user-graduate block text-sm mb-1"></i>
                                <span>{{ $isAr ? 'طالب' : 'Student' }}</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="reviewer_type" value="parent" class="peer sr-only" {{ $userRole === 'parent' ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-2xl border border-slate-200 dark:border-slate-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-950/50 peer-checked:text-purple-600 dark:peer-checked:text-purple-400 transition-all font-heading font-extrabold text-xs">
                                <i class="fa-solid fa-hands-holding-child block text-sm mb-1"></i>
                                <span>{{ $isAr ? 'ولي أمر' : 'Parent' }}</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="reviewer_type" value="teacher" class="peer sr-only" {{ $userRole === 'teacher' ? 'checked' : '' }}>
                            <div class="p-3 text-center rounded-2xl border border-slate-200 dark:border-slate-700 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-950/50 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all font-heading font-extrabold text-xs">
                                <i class="fa-solid fa-chalkboard-user block text-sm mb-1"></i>
                                <span>{{ $isAr ? 'معلم / مدرب' : 'Teacher' }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Full Name & Course --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ $isAr ? 'الاسم الكامل' : 'Your Full Name' }} <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            required
                            value="{{ $user?->name ?? '' }}"
                            placeholder="{{ $isAr ? 'مثال: أحمد مصطفى' : 'e.g. Alex Morgan' }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium"
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ $isAr ? 'المادة / المسار التعليمي' : 'Course / Track Name' }}
                        </label>
                        <input
                            type="text"
                            name="course_name"
                            placeholder="{{ $isAr ? 'مثال: كورس الذكاء الاصطناعي' : 'e.g. Physics & Robotics' }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-2.5 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium"
                        >
                    </div>
                </div>

                {{-- Review Content Textarea --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                        {{ $isAr ? 'نص التقييم والمراجعة' : 'Review Feedback' }} <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        name="content"
                        required
                        rows="4"
                        placeholder="{{ $isAr ? 'اكتب رأيك وتجربتك بالتفصيل وكيف ساعدتك المنصة...' : 'Write your detailed review, how the courses and teachers helped you succeed...' }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-xs sm:text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium leading-relaxed resize-none"
                    ></textarea>
                </div>

                {{-- Photo Upload --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                        {{ $isAr ? 'الصورة الشخصية (اختياري)' : 'Profile Photo (Optional)' }}
                    </label>
                    <input
                        type="file"
                        name="avatar"
                        accept="image/*"
                        class="w-full text-xs text-slate-500 dark:text-slate-400 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:file:bg-teal-950 dark:file:text-teal-400 cursor-pointer"
                    >
                </div>

                {{-- Alert Box --}}
                <div id="modal-alert-box" class="hidden p-3.5 rounded-2xl text-xs font-mono font-bold"></div>

                {{-- Submit Button --}}
                <div class="pt-2 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeReviewModal()"
                        class="px-5 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-heading font-bold text-xs hover:bg-slate-200 transition-colors"
                    >
                        {{ $isAr ? 'إلغاء' : 'Cancel' }}
                    </button>
                    
                    <button
                        type="submit"
                        id="submit-review-btn"
                        class="px-6 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-500 text-white font-heading font-extrabold text-xs sm:text-sm shadow-lg shadow-teal-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
                    >
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>{{ $isAr ? 'إرسال التقييم' : 'Publish Review' }}</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════
     JAVASCRIPT: REAL-TIME TABS & INSTANT FILTERING ENGINE
     ════════════════════════════════════════════════════════════════ --}}
<script>
let currentActiveTab = '{{ $currentType ?? "all" }}';
let currentSearchQuery = '{{ $searchQuery ?? "" }}';
let currentRatingVal = 5;

const ratingLabels = {
    5: '5 / 5 — {{ $isAr ? "ممتاز جداً" : "Excellent Experience" }}',
    4: '4 / 5 — {{ $isAr ? "جيد جداً" : "Very Good" }}',
    3: '3 / 5 — {{ $isAr ? "جيد" : "Good" }}',
    2: '2 / 5 — {{ $isAr ? "مقبول" : "Fair" }}',
    1: '1 / 5 — {{ $isAr ? "يحتاج تحسين" : "Needs Improvement" }}'
};

function applyRealtimeFiltering() {
    const cards = document.querySelectorAll('#reviews-cards-grid .testim-card');
    const emptyState = document.getElementById('no-realtime-results');
    const clearBtn = document.getElementById('realtime-clear-btn');
    let visibleCount = 0;

    if (clearBtn) {
        if (currentActiveTab !== 'all' || currentSearchQuery.length > 0) {
            clearBtn.classList.remove('hidden');
            clearBtn.classList.add('flex');
        } else {
            clearBtn.classList.add('hidden');
            clearBtn.classList.remove('flex');
        }
    }

    cards.forEach(card => {
        const cardType = card.getAttribute('data-reviewer-type');
        const cardSearchText = card.getAttribute('data-search-text') || '';

        const matchesType = (currentActiveTab === 'all' || cardType === currentActiveTab);
        const matchesSearch = !currentSearchQuery || cardSearchText.includes(currentSearchQuery);

        if (matchesType && matchesSearch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
}

function filterReviewsRealtime(type) {
    currentActiveTab = type;

    // Update tab styles instantly
    const tabs = document.querySelectorAll('#realtime-filter-tabs .filter-tab-btn');
    tabs.forEach(tab => {
        const tabType = tab.getAttribute('data-tab-type');
        const badge = tab.querySelector('.tab-badge');

        // Reset base classes
        tab.className = 'filter-tab-btn px-5 py-2.5 rounded-2xl text-xs sm:text-sm font-heading font-extrabold transition-all duration-300 whitespace-nowrap flex items-center gap-2 cursor-pointer ';

        if (tabType === type) {
            if (tabType === 'all') {
                tab.className += 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-md scale-[1.02]';
                if (badge) badge.className = 'tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-white/20 dark:bg-slate-900/20 text-white dark:text-slate-900';
            } else if (tabType === 'student') {
                tab.className += 'bg-gradient-to-r from-teal-600 to-teal-500 text-white shadow-lg shadow-teal-500/25 scale-[1.02]';
                if (badge) badge.className = 'tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-white/20 text-white';
            } else if (tabType === 'parent') {
                tab.className += 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-500/25 scale-[1.02]';
                if (badge) badge.className = 'tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-white/20 text-white';
            } else if (tabType === 'teacher') {
                tab.className += 'bg-gradient-to-r from-amber-600 to-amber-500 text-white shadow-lg shadow-amber-500/25 scale-[1.02]';
                if (badge) badge.className = 'tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-white/20 text-white';
            }
        } else {
            tab.className += 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700';
            if (badge) badge.className = 'tab-badge px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300';
        }
    });

    applyRealtimeFiltering();
}

function handleRealtimeSearch(query) {
    currentSearchQuery = (query || '').toLowerCase().trim();
    applyRealtimeFiltering();
}

function resetRealtimeFilters() {
    const input = document.getElementById('realtime-search-input');
    if (input) input.value = '';
    currentSearchQuery = '';
    filterReviewsRealtime('all');
}

function openReviewModal() {
    const modal = document.getElementById('review-modal-backdrop');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeReviewModal() {
    const modal = document.getElementById('review-modal-backdrop');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function setRatingValue(val) {
    currentRatingVal = val;
    document.getElementById('review-rating-input').value = val;
    document.getElementById('rating-desc-text').innerText = ratingLabels[val] || `${val} / 5`;

    const stars = document.querySelectorAll('#star-picker-container .star-btn');
    stars.forEach(star => {
        const starVal = parseInt(star.getAttribute('data-val'), 10);
        if (starVal <= val) {
            star.classList.remove('text-slate-300', 'dark:text-slate-700');
            star.classList.add('text-amber-400');
        } else {
            star.classList.remove('text-amber-400');
            star.classList.add('text-slate-300', 'dark:text-slate-700');
        }
    });
}

async function handleReviewSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('submit-review-btn');
    const alertBox = document.getElementById('modal-alert-box');

    alertBox.className = 'hidden';
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>{{ $isAr ? "جاري الإرسال..." : "Submitting..." }}</span>';

    try {
        const formData = new FormData(form);
        const res = await fetch("{{ route('ajax.reviews.submit') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();

        if (res.ok && data.success) {
            alertBox.className = 'p-3.5 rounded-2xl text-xs font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
            alertBox.innerText = data.message;
            alertBox.classList.remove('hidden');

            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } else {
            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : '{{ $isAr ? "حدث خطأ أثناء الإرسال" : "Submission failed" }}');
            alertBox.className = 'p-3.5 rounded-2xl text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
            alertBox.innerText = errorMsg;
            alertBox.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>{{ $isAr ? "إعادة المحاولة" : "Try Again" }}</span>';
        }
    } catch (err) {
        alertBox.className = 'p-3.5 rounded-2xl text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
        alertBox.innerText = '{{ $isAr ? "تعذر الاتصال بالخادم، يرجى المحاولة مرة أخرى." : "Network error, please try again." }}';
        alertBox.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>{{ $isAr ? "إعادة المحاولة" : "Try Again" }}</span>';
    }
}
</script>

@endsection
